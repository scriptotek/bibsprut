<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    public $archiveBaseUrl = 'http://ub-prod01-imgs.uio.no/arkiv/filmFraUreal/';

	/**
     * Get the presentations from this event.
     */
    public function presentations()
    {
        return $this->hasMany('App\Presentation')
            ->orderBy('start_time', 'asc');
    }

    /**
     * Get the organizers of this event.
     */
    public function organizers()
    {
        return $this->hasMany('App\Organizer');
    }

    /**
     * Get the resources for this event.
     */
    public function resources()
    {
        return $this->hasMany('App\EventResource');
    }

    /**
     * Get the YouTube playlist for this event, if any.
     */
    public function youtubePlaylist()
    {
        return $this->belongsTo('App\YoutubePlaylist');
    }

    public function teaserAsHtml()
    {
        $parser = new \cebe\markdown\GithubMarkdown();
        return $parser->parse($this->intro);
    }

	public function descriptionAsHtml()
    {
        $parser = new \cebe\markdown\GithubMarkdown();
        return $parser->parse($this->description);
    }

    public function sha1()
    {
        return substr(sha1($this->uuid), 0, 7);
    }

    public function archiveLink()
    {
        return $this->archiveBaseUrl . $this->start_date . '-' . $this->sha1();
    }
}
