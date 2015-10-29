<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public function presentations()
    {
        return $this->belongsToMany('App\Presentation', 'presentation_persons')
        	->withPivot('role');
    }

}
