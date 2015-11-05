<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventResource extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['original_url', 'original_filename', 'filetype', 'mime', 'width', 'height', 'role', 'license', 'attribution'];
}
