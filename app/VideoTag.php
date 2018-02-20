<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VideoTag extends Pivot
{
    // Add data from the pivot model
    // protected $appends = ['tagRole'];

    /**
     * Get the videos for the playlist.
     */
    public function tagRole()
    {
        return $this->belongsTo('App\TagRole');
    }

    // public function toArray()
    // {
    //     return [
    //         'tag_id' => $this->tag_id,
    //         'tag_name' => $this->tag_name,
    //         'tag_type' => $this->tag_type,
    //         'tag_role_id' => $this->pivot->tag_role_id,
    //     ];
    // }
}
