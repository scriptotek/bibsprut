<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubePlaylist extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['youtube_id', 'title'];

    /**
     * Get the videos for the playlist.
     */
    public function videos()
    {
        return $this->belongsToMany('App\YoutubeVideo', 'youtube_playlist_videos')
            ->withPivot('playlist_position');
    }

}
