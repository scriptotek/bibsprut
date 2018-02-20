<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VideoEntityRelation extends Pivot
{
    // Add data from the pivot model
    // protected $appends = ['entityRelation'];

    /**
     * Get the videos for the playlist.
     */
    public function entityRelation()
    {
        return $this->belongsTo('App\EntityRelation');
    }

    // public function toArray()
    // {
    //     return [
    //         'entity_id' => $this->entity_id,
    //         'entity_label' => $this->entity_label,
    //         'entity_type' => $this->entity_type,
    //         'entity_relationship_id' => $this->pivot->entity_relationship_id,
    //     ];
    // }
}
