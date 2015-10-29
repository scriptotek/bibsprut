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

    public function harvestVideos()
    {
        foreach ($this->getUploads() as $response) {
            $id = $response->contentDetails->videoId;
            $response = \Youtube::getVideoInfo($id);

            $video = \App\YoutubeVideo::firstOrCreate(['youtube_id' => $id]);

            $video->license = $response->status->license;
            $video->is_public = ($response->status->privacyStatus == 'public');

            $video->published_at = $response->snippet->publishedAt;
            $video->title = $response->snippet->title;
            $video->description = $response->snippet->description;
            $video->tags = isset($response->snippet->tags) ? $response->snippet->tags : [];
            if (isset($response->snippet->thumbnails->standard)) {
                $video->thumbnail = $response->snippet->thumbnails->standard->url;
            }

            $video->save();
        }
    }

    public function harvestPlaylists()
    {
        $ids = [];
        foreach ($this->getPlaylists() as $response) {

            $id = $response->id;

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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->harvestLiveBroadcasts();
        $this->harvestVideos();
        $this->harvestPlaylists();
    }
}
