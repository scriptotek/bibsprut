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

	public function descriptionAsHtml()
    {
        $parser = new \cebe\markdown\GithubMarkdown();
        return $parser->parse($this->description);
    }
}
