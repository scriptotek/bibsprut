<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{

    /**
     * Get the event the presentation belongs to.
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    /**
     * Get the persons from the presentation.
     */
    public function persons()
    {
        return $this->belongsToMany('App\Person', 'presentation_persons')
            ->withPivot('role');
    }

    /**
     * Get the video from the presentation.
     */
    public function video()
    {
        return $this->hasOne('App\YoutubeVideo');
    }

    /**
     * Get the start time without seconds
     *
     * @param  string  $value
     * @return string
     */
    public function getStartTimeAttribute($value)
    {
        return preg_replace('/:00$/', '', $value);
    }

    /**
     * Get the end time without seconds
     *
     * @param  string  $value
     * @return string
     */
    public function getEndTimeAttribute($value)
    {
        return preg_replace('/:00$/', '', $value);
    }
}
