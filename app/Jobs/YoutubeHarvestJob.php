<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class YoutubeHarvestJob extends Job implements SelfHandling
{

    protected $channelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->channelId = 'UCynaukrA8wxvJOcyFQJN3Jg';
    }

    public function getPlaylists()
    {
        return \Youtube::getPlaylistsByChannelId($this->channelId);
    }

    public function getPlaylistVideos($playlistId)
    {
        return \Youtube::getPlaylistItemsByPlaylistId($playlistId)['results'];
    }

    public function getUploads()
    {
        $channel = \Youtube::getChannelById($this->channelId);
        $playlistId = $channel->contentDetails->relatedPlaylists->uploads;
        return $this->getPlaylistVideos($playlistId);
    }

    public function normalizeDateTime($date)
    {
        return preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2}).*/', '\1-\2-\3 \4:\5:\6', $date);
    }

    public function normalizeDate($date)
    {
        return preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2}).*/', '\1-\2-\3', $date);
    }

    public function storeVideo($data)
    {
        $video = \App\YoutubeVideo::firstOrNew(['youtube_id' => $data->id]);

        $creating = $video->isDirty();

        $video->title = $data->snippet->title;
        $video->description = $data->snippet->description;
        $video->tags = isset($data->snippet->tags) ? $data->snippet->tags : [];

        if (isset($data->snippet->thumbnails->standard)) {
            $video->thumbnail = $data->snippet->thumbnails->standard->url;
        }

        // Ref: <https://laracasts.com/discuss/channels/eloquent/strange-results-of-isdirty-with-casted-boolean>
        $video->published_at = $this->normalizeDateTime($data->snippet->publishedAt);
        $video->is_public = ($data->status->privacyStatus == 'public') ? '1' : '0';

        $video->license = $data->status->license;

        if (isset($data->snippet->scheduledStartTime)) {
            $video->recorded_at = $this->normalizeDate($data->snippet->scheduledStartTime);
        }
        if (isset($data->status->lifeCycleStatus)) {
            $video->broadcast_status = $data->status->lifeCycleStatus;
        }

        if (isset($data->statistics) && isset($data->statistics->viewCount)) {
            $video->views = $data->statistics->viewCount;
        }

        if (isset($data->recordingDetails) && isset($data->recordingDetails->recordingDate)) {
            //var_dump($data->recordingDetails);
            $video->recorded_at = $this->normalizeDate($data->recordingDetails->recordingDate);
        }

        if (isset($data->contentDetails) && isset($data->contentDetails->duration)) {
            $video->duration = $data->contentDetails->duration;
        }

        if ($creating) {
            \Log::info('Adding YouTube video: ' . $data->id);
        } else if ($video->isDirty()) {
            \Log::info('Updating YouTube video: ' . $data->id);
        }

        $video->save();
    }

    public function harvestPlaylists()
    {
        $ids = [];
        foreach ($this->getPlaylists() as $response) {

            $id = $response->id;

            echo "- " . $response->snippet->title . "\n";

            $playlist = \App\YoutubePlaylist::firstOrCreate(['youtube_id' => $id]);

            $playlist->is_public = ($response->status->privacyStatus == 'public');
            $playlist->title = $response->snippet->title;
            $playlist->description = $response->snippet->description;

            $playlist->save();

            $this->harvestPlaylistVideos($id);
            $ids[] = $id;
        }

        // TODO: Delete any playlists in DB with youtube_id NOT IN $ids
    }

    public function harvestPlaylistVideos($playlistId)
    {
        $video_ids = [];
        $playlist = \App\YoutubePlaylist::where('youtube_id', '=', $playlistId)->firstOrFail();
        foreach ($this->getPlaylistVideos($playlistId) as $response) {
            $videoId = $response->snippet->resourceId->videoId;
            $video = \App\YoutubeVideo::where('youtube_id', '=', $videoId)->first();
            if (!is_null($video)) {
                $video_ids[$video->id] = ['playlist_position' => $response->snippet->position];
            }
        }

        $playlist->videos()->sync($video_ids);
    }

    public function harvestLiveBroadcasts()
    {
        $client = app('google.api.client');
        $client->setAccessType('offline');
        if (\Storage::disk('local')->exists('google_access_token.json')) {
            $token = \Storage::disk('local')->get('google_access_token.json');
            $client->setAccessToken($token);
        }

        $youtube = $client->make('youTube');

        // list buckets example
//         $videos = $youtube->search->listSearch('snippet', [
// //            'onBehalfOfContentOwner' => 'UCynaukrA8wxvJOcyFQJN3Jg',
// //            'forContentOwner' => true,
// //            'forMine' => true,
//             'type' => 'video',
//             'channelId' => 'UCynaukrA8wxvJOcyFQJN3Jg',
//             'maxResults' => 50,
//             'order' => 'date',
//             'eventType' => 'upcoming',
//         ]);

        $videos = $youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,status', [
            'broadcastStatus' => 'upcoming',
            'maxResults' => 50,
        ]);

        foreach ($videos->items as $broadcast) {
            echo "- " . $broadcast->snippet->title . "\n";
            $this->storeVideo($broadcast);
        }


        // $client = new \Google_Client();
        // $credentials = $client->loadServiceAccountJson(base_path('google_credentials.json'), [
        //     'https://www.googleapis.com/auth/youtube.readonly',
        //     'https://www.googleapis.com/auth/youtube'
        // ]);
        // $client->setAssertionCredentials($credentials);
        // if ($client->getAuth()->isAccessTokenExpired()) {
        //     $client->getAuth()->refreshTokenWithAssertion();
        // }

        // echo "OK";


        // $client->setApplicationName("bibsprut");
        // $service = new \Google_Service_YouTube($client);

        // $part = 'snippet'; # ['id', 'snippet', 'contentDetails', 'status'];


        // $results = $service->liveBroadcasts->listLiveBroadcasts($part, ['mine' => 'true']);

        // var_dump($results);


        // die;
    }

    public function harvestCompletedLiveBroadcasts()
    {
        $client = app('google.api.client');
        $client->setAccessType('offline');
        if (\Storage::disk('local')->exists('google_access_token.json')) {
            $token = \Storage::disk('local')->get('google_access_token.json');
            $client->setAccessToken($token);
        }

        $youtube = $client->make('youTube');

        // list buckets example
//         $videos = $youtube->search->listSearch('snippet', [
// //            'onBehalfOfContentOwner' => 'UCynaukrA8wxvJOcyFQJN3Jg',
// //            'forContentOwner' => true,
// //            'forMine' => true,
//             'type' => 'video',
//             'channelId' => 'UCynaukrA8wxvJOcyFQJN3Jg',
//             'maxResults' => 50,
//             'order' => 'date',
//             'eventType' => 'upcoming',
//         ]);

        $videos = $youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,status', [
            'broadcastStatus' => 'completed',
            'maxResults' => 50,
        ]);

        foreach ($videos->items as $broadcast) {
            echo "- " . $broadcast->snippet->title . "\n";
            $this->storeVideo($broadcast);
        }


        // $client = new \Google_Client();
        // $credentials = $client->loadServiceAccountJson(base_path('google_credentials.json'), [
        //     'https://www.googleapis.com/auth/youtube.readonly',
        //     'https://www.googleapis.com/auth/youtube'
        // ]);
        // $client->setAssertionCredentials($credentials);
        // if ($client->getAuth()->isAccessTokenExpired()) {
        //     $client->getAuth()->refreshTokenWithAssertion();
        // }

        // echo "OK";


        // $client->setApplicationName("bibsprut");
        // $service = new \Google_Service_YouTube($client);

        // $part = 'snippet'; # ['id', 'snippet', 'contentDetails', 'status'];


        // $results = $service->liveBroadcasts->listLiveBroadcasts($part, ['mine' => 'true']);

        // var_dump($results);


        // die;
    }

    public function harvestVideosFromIds($ids)
    {
        print ":: $ids\n";
        $client = app('google.api.client');
        $client->setAccessType('offline');
        if (\Storage::disk('local')->exists('google_access_token.json')) {
            $token = \Storage::disk('local')->get('google_access_token.json');
            $client->setAccessToken($token);
        }

        $youtube = $client->make('youTube');

        // list buckets example
        $videos = $youtube->videos->listVideos('id,snippet,contentDetails,fileDetails,recordingDetails,status,statistics', [
           'id' => $ids,
           'maxResults' => 50,
        ]);

        foreach ($videos->items as $video) {
            echo "- " . $video->snippet->title . "\n";
            $this->storeVideo($video);
        }
    }

    public function harvestVideos()
    {
        $ids = [];
        foreach ($this->getUploads() as $response) {
            $ids[] = $response->contentDetails->videoId;
            // $response = \Youtube::getVideoInfo($id,
            //     ['id', 'snippet', 'contentDetails', 'statistics', 'status', 'recordingDetails', 'fileDetails']
            // );
            // $this->storeVideo($response);
        }
        $this->harvestVideosFromIds(implode(',', $ids));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$this->harvestCompletedLiveBroadcasts();
        //$this->harvestLiveBroadcasts();
        $this->harvestVideos();
        // $this->harvestPlaylists();
    }
}
