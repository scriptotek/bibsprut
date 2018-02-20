<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagRole extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label', 'description'];
}
