<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

	/**
     * Get the presentations from this event.
     */
    public function presentations()
    {
        return $this->hasMany('App\Presentation');
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

	public function descriptionAsHtml()
    {
        $parser = new \cebe\markdown\GithubMarkdown();
        return $parser->parse($this->description);
    }
}
