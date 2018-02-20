<?php

namespace App;

use Carbon\Carbon;
use cebe\markdown\GithubMarkdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YoutubeVideo extends Model
{

    use SoftDeletes, HasStartEndTime;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_time', 'end_time', 'deleted_at'];

    public function upcoming()
    {
        $now = Carbon::now();
        $bs = $this->yt('broadcast_status');
        if (is_null($bs)) {
            return false;
        }
        if ($bs == 'complete') {
            return false;
        }
        if (!is_null($this->end_time) && $this->end_time < $now) {
            return false;
        }
        if (!is_null($this->start_time) && $this->start_time < $now) {
            return false;
        }

        return true;
    }

    public static function events($includePrivate=false, $includeMissingStartTime=false)
    {
        $events = [];

        if ($includeMissingStartTime) {
            $recordingsMissingStartTime = static::whereNull('start_time')
                    ->get();

            if (count($recordingsMissingStartTime)) {
                $events['missing_starttime'] = [
                    'recordings' => $recordingsMissingStartTime,
                    'vortexEvent' => null,
                    'playlist' => null,
                    'title' => null,
                    'publicVideos' => count($recordingsMissingStartTime),
                ];
            }
        }

        $recordings = static::leftJoin('youtube_playlist_videos', 'youtube_playlist_videos.youtube_video_id', '=', 'youtube_videos.id')
                    // ->leftJoin('tag_youtube_video', 'tag_youtube_video.youtube_video_id', '=', 'youtube_videos.id')
                    //->leftJoin('tags', 'tag_youtube_video.youtube_video_id', '=', 'tags.id')
                    ->select('youtube_videos.*', 'youtube_playlist_videos.playlist_position')
                    ->orderBy('youtube_videos.start_time', 'desc')
                    ->orderBy('youtube_playlist_videos.playlist_position', 'asc')
                    ->whereNotNull('youtube_videos.start_time')
                    ->paginate(25); // TODO: Actually utilize this!
                    //->get();

        // $recordings = static::with('playlists')
        //     ->orderBy('start_time', 'desc')
        //     ->orderBy('youtube_playlist_videos.playlist_position', 'asc')
        //     ->whereNotNull('start_time')
        //     ->get();

        foreach ($recordings as $rec) {
            if (!$includePrivate && !$rec->yt('is_public')) {
                continue;
            }
            if ($rec->vortexEvent) {
                $gid = $rec->vortexEvent->url;
            } else {
                $gid = $rec->id;
            }
            if (!isset($events[$gid])) {
                $events[$gid] = [
                    'recordings' => [],
                    'publicVideos' => 0,
                    'playlist' => null,
                    'title' => null,
                    'vortexEvent' => $rec->vortexEvent,
                ];
                if ($rec->vortexEvent) {
                    $events[$gid]['title'] = $rec->vortexEvent->title;
                } else {
                    $events[$gid]['title'] = $rec->yt('title');
                }
            }
            foreach ($rec->playlists as $playlist) {
                $events[$gid]['playlist'] = $playlist;
            }
            $events[$gid]['recordings'][] = $rec;
            if ($rec->yt('is_public')) {
                $events[$gid]['publicVideos']++;
            }
        }

        return $events;
    }

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['youtube_id', 'youtube_meta', 'duration'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'youtube_meta' => 'array',
    ];

    /**
     * Get the vortex event the video is from.
     */
    public function vortexEvent()
    {
        return $this->belongsTo('App\VortexEvent');
    }


    /**
     * Get the vortex event the video is from.
     */
    public function account()
    {
        return $this->belongsTo('App\GoogleAccount');
    }

    /**
     * Get the playlists the video are part of.
     */
    public function playlists()
    {
        return $this->belongsToMany('App\YoutubePlaylist', 'youtube_playlist_videos')
            ->withPivot('playlist_position');
    }

    /**
     * Get the tags the video have attached.
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag')
            ->withPivot('tag_role_id')
            ->using('App\VideoTag');
    }

    /**
     * Get the presentation the video is from.
     */
    public function presentation()
    {
        return $this->belongsTo('App\Presentation');
    }

    public function youtubeDescriptionAsHtml($options=[])
    {
        $txt = $this->yt('description');
        if (array_get($options, 'withoutLinks')) {
            $txt = preg_replace('/https?:\/\/[^\s]+/', '', $txt);
        }

        if (array_get($options, 'firstParagraphOnly')) {
            $txt = explode("\n\n", $txt)[0];
        }

        $parser = new GithubMarkdown();
        return $parser->parse($txt);
    }

    public function youtubeLink($method='watch')
    {
        $host = 'https://www.youtube.com';
        switch ($method) {
            case 'embed':
                return $host . '/embed/' . $this->youtube_id;

            case 'edit':
                return $host . '/edit?video_id=' . $this->youtube_id;

            default:
                return $host .'/watch?v=' . $this->youtube_id;
        }
    }

    public function yt($key)
    {
        return array_get($this->youtube_meta, $key);
    }

    // public function duration()
    // {
        
    // }
}
