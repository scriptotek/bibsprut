<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['entity_label', 'entity_data', 'entity_type'];

    /**
     * Get the videos for the playlist.
     */
    public function videos()
    {
        return $this->belongsToMany('App\YoutubeVideo')
            ->withPivot('entity_relationship_id')
            ->using('App\VideoEntityRelation');
    }

    public function simpleRepresentation()
    {
        $x = [
            'id' => $this->id,
            'entity_label' => $this->entity_label,
        ];
        if (isset($this->pivot)) {
            $x['entity_relationship_id'] = $this->pivot->entity_relationship_id;
        }
        return $x;
    }
}
