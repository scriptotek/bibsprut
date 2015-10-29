<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['youtube_id', 'title', 'description', 'tags', 'thumbnail', 'published_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * Get the playlists the video are part of.
     */
    public function playlists()
    {
        return $this->belongsToMany('App\YoutubePlaylist', 'youtube_playlist_videos')
            ->withPivot('playlist_position');
    }

    /**
     * Get the presentation the video is from.
     */
    public function presentation()
    {
        return $this->belongsTo('App\Presentation');
    }

    public function descriptionAsHtml()
    {
        $parser = new \cebe\markdown\GithubMarkdown();
        return $parser->parse($this->description);
    }
}
