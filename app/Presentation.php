<?php

namespace App;

use Carbon\Carbon;
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
     * Get the recording from the presentation.
     */
    public function recording()
    {
        return $this->hasOne('App\Recording');
    }

    /**
     * Get the slides from the presentation.
     */
    // public function slides()
    // {
    //     return $this->hasOne('App\Slides');
    // }

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

    public function getStartDateTime()
    {
        return new Carbon($this->event->start_date . ' ' . $this->start_time, 'Europe/Oslo');
    }
}
