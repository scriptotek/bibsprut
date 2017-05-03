<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['completed_at'];

    public function complete()
    {
        $this->completed_at = Carbon::now();
        $this->save();
    }
}
