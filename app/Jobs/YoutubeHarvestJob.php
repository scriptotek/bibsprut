<?php

namespace App\Jobs;

use App\Exceptions\ScrapeException;
use App\GoogleAccount;
use App\Harvest;
use App\VortexEvent;
use App\YoutubePlaylist;
use Carbon\Carbon;
use Google_Service_YouTube;
use PulkitJalan\Google\Client as GoogleClient;

class YoutubeHarvestJob extends Job
{
    protected $force;

    public function __construct($force)
    {
        $this->force = $force;
    }

    public function vortexUrlFromText($text)
    {
        $pattern = '/(https?:\/\/[^\s]+.uio.no\/[^\s]+)/';
        if (!preg_match($pattern, $text, $matches)) {
            return null;
        }
        return $matches[1];
    }

    public function getPlaylistVideos($playlistId)
    {
        $items = \Youtube::getPlaylistItemsByPlaylistId($playlistId);
        if ($items['results'] == false) {
            \Log::error('Could not get playlist ' . $playlistId);
            return [];
        }
        return $items['results'];
    }

    public function normalizeDateTime($date)
    {
        return Carbon::parse($date)->setTimezone('Europe/Oslo');
    }

    public function normalizeDate($date)
    {
        return Carbon::parse($date);
    }

    public function storeVideo($data, GoogleAccount $account)
    {
        $recording = \App\YoutubeVideo::where(['youtube_id' => $data->id])->withTrashed()->first() ?: new \App\YoutubeVideo(['youtube_id' => $data->id]);

        $recording->account_id = $account->id;

        $creating = $recording->isDirty();
        $meta = $recording->youtube_meta ?: [];

        if ($creating) {
            $meta['tags'] = [];
        }

        $meta['title'] = $data->snippet->title;
        $meta['description'] = $data->snippet->description;

        if (!empty($meta['description'])) {
            $vortexLink = $this->vortexUrlFromText($meta['description']);
            if (empty($vortexLink)) {
                $recording->vortex_event_id = null;
            } else {
                try {
                    $vortexEvent = VortexEvent::where(['url' => $vortexLink])->withTrashed()->first() ?: new VortexEvent(['url' => $vortexLink]);
                    $vortexEvent->scrape();
                    $vortexEvent->save();
                    $recording->vortex_event_id = $vortexEvent->id;
                } catch (ScrapeException $exception) {
                    \Log::warning('Failed to scrape URL: ' . $vortexLink);
                }
            }
        }

        if (isset($data->snippet->thumbnails->standard)) {
            $meta['thumbnail'] = $data->snippet->thumbnails->standard->url;
        } elseif (isset($data->snippet->thumbnails->high)) {
            $meta['thumbnail'] = $data->snippet->thumbnails->high->url;
        } elseif (isset($data->snippet->thumbnails->default)) {
            $meta['thumbnail'] = $data->snippet->thumbnails->default->url;
        }

        // If a video, not broadcast
        if (!isset($data->status->lifeCycleStatus)) {
            $meta['tags'] = isset($data->snippet->tags) ? $data->snippet->tags : [];

            // Ref: <https://laracasts.com/discuss/channels/eloquent/strange-results-of-isdirty-with-casted-boolean>
            $meta['published_at'] = $this->normalizeDateTime($data->snippet->publishedAt);
            $meta['license'] = $data->status->license;
        }

        $meta['is_public'] = ($data->status->privacyStatus == 'public');

        if (isset($data->snippet->scheduledStartTime)) {
            $recording->start_time = $this->normalizeDateTime($data->snippet->scheduledStartTime);
        }
        if (isset($data->snippet->scheduledEndTime)) {
            $recording->end_time = $this->normalizeDateTime($data->snippet->scheduledEndTime);
        }
        if (isset($data->status->lifeCycleStatus)) {
            $meta['broadcast_status'] = $data->status->lifeCycleStatus;
        }

        if (isset($data->statistics) && isset($data->statistics->viewCount)) {
            $meta['views'] = $data->statistics->viewCount;
        }

        if (isset($data->recordingDetails) && isset($data->recordingDetails->recordingDate)) {
            //var_dump($data->recordingDetails);
            if (is_null($recording->start_time)) {
                $recording->start_time = $this->normalizeDate($data->recordingDetails->recordingDate);
            }
        }

        if (isset($data->contentDetails) && isset($data->contentDetails->duration)) {
            $meta['duration'] = $data->contentDetails->duration;
        }

        if ($creating) {
            \Log::info('Adding YouTube video: ' . $data->id);
        } else if ($recording->isDirty()) {
            //var_dump($recording->getDirty());
            \Log::info('Updating YouTube video: ' . $data->id);
        }

        $recording->youtube_meta = $meta;

        $recording->save();
    }

    public function harvestPlaylistVideos($playlistId)
    {
        $video_ids = [];
        $playlist = YoutubePlaylist::where('youtube_id', '=', $playlistId)->firstOrFail();
        foreach ($this->getPlaylistVideos($playlistId) as $response) {
            $videoId = $response->snippet->resourceId->videoId;
            $recording = \App\YoutubeVideo::where('youtube_id', '=', $videoId)->first();
            if (!is_null($recording)) {
                $video_ids[$recording->id] = ['playlist_position' => $response->snippet->position];
            }
        }

        $playlist->videos()->sync($video_ids);
    }

    public function harvestLiveBroadcasts(Google_Service_YouTube $youtube, GoogleAccount $account)
    {
        $videos = $youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,status', [
            'broadcastStatus' => 'upcoming',
            'maxResults' => 50,
        ]);

        foreach ($videos->items as $broadcast) {
            \Log::debug("Got YouTube broadcast: {$broadcast->snippet->title}\n");
            $this->storeVideo($broadcast, $account);
        }
    }

    public function harvestCompletedLiveBroadcasts(Google_Service_YouTube $youtube, GoogleAccount $account)
    {
        $params = [
            'broadcastStatus' => 'completed',
            'maxResults' => 50,
        ];
        do {
            $response = $youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,status', $params);
            // $videos = array_merge($videos, $response->items);

            foreach ($response->items as $broadcast) {
                \Log::debug("Got YouTube broadcast: {$broadcast->snippet->title}\n");
                $this->storeVideo($broadcast, $account);
            }

            if ($response->nextPageToken) {
                $params['pageToken'] = $response->nextPageToken;
            }
        } while ($response->nextPageToken);
    }

    public function harvestVideosFromIds($ids, Google_Service_YouTube $youtube, GoogleAccount $account)
    {
        // list buckets example
        $chunks = array_chunk($ids, 50);
        $videos = [];
        foreach ($chunks as $ids) {
            $response = $youtube->videos->listVideos('id,snippet,contentDetails,fileDetails,recordingDetails,status,statistics', [
                'id' => implode(',', $ids),
                'maxResults' => 50,
            ]);
            $videos = array_merge($videos, $response->items);
        }

        foreach ($videos as $video) {
            \Log::debug("Got YouTube video: {$video->snippet->title}\n");
            $this->storeVideo($video, $account);
        }
    }

    protected function search($params, Google_Service_YouTube $youtube)
    {
        $params['maxResults'] = 50;
        $videos = [];

        do {
            $response = $youtube->search->listSearch('id', $params);
            $videos = array_merge($videos, $response->items);
            if ($response->nextPageToken) {
                $params['pageToken'] = $response->nextPageToken;
            }
        } while ($response->nextPageToken);

        return $videos;
    }

    protected function playlists($params, Google_Service_YouTube $youtube)
    {
        $params['maxResults'] = 50;
        $playlists = [];

        do {
            $response = $youtube->playlists->listPlaylists('id,snippet,status', $params);
            $playlists = array_merge($playlists, $response->items);
            if ($response->nextPageToken) {
                $params['pageToken'] = $response->nextPageToken;
            }
        } while ($response->nextPageToken);

        return $playlists;
    }

    /*
     * Harvest all videos, both private and public
     */
    public function harvestVideos(Google_Service_YouTube $youtube, GoogleAccount $account)
    {
        $videos = $this->search([
            'forMine' => true,
            'type' => 'video'
        ], $youtube);

        $ids = [];
        foreach ($videos as $video) {
            $ids[] = $video->id->videoId;
        }
        $this->harvestVideosFromIds($ids, $youtube, $account);
    }

    /*
     * Harvest all videos, both private and public
     */
    public function harvestPlaylists(Google_Service_YouTube $youtube, GoogleAccount $account)
    {
        $items = $this->playlists([
            'mine' => true
        ], $youtube);

        $ids = [];
        foreach ($items as $response) {

            $id = $response->id;
            \Log::debug("Got YouTube playlist: {$response->snippet->title}\n");

            $playlist = YoutubePlaylist::firstOrNew(['youtube_id' => $id]);

            $playlist->is_public = ($response->status->privacyStatus == 'public');
            $playlist->title = $response->snippet->title;
            $playlist->description = $response->snippet->description;

            $playlist->save();

            $this->harvestPlaylistVideos($id);
            $ids[] = $id;
        }

        // TODO: Delete any playlists in DB with youtube_id NOT IN $ids
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $running = Harvest::first();
        if (!is_null($running)) {
            if ($this->force) {
                $running->delete();
            } else {
                \Log::error('Another harvest is running or did not exit normally. Use -f to override.');
                return;
            }
        }

        $harvest = Harvest::create();
        try {
            $accounts = GoogleAccount::get();
            if (!count($accounts)) {
                \Log::error('No accounts configured');
            }
            foreach ($accounts as $account) {
                \Log::debug("Harvesting YouTube data for account {$account->userinfo['name']}\n");
                $client = $account->getClient();
                $youtube = $client->make('YouTube');

                $this->harvestCompletedLiveBroadcasts($youtube, $account);
                $this->harvestLiveBroadcasts($youtube, $account);
                $this->harvestVideos($youtube, $account);
                $this->harvestPlaylists($youtube, $account);
            }
        } finally {
            $harvest->delete();
        }
    }
}
