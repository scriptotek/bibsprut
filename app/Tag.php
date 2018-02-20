<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
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
    protected $fillable = ['tag_name', 'tag_data', 'tag_type'];

    /**
     * Get the videos for the playlist.
     */
    public function videos()
    {
        return $this->belongsToMany('App\YoutubeVideo')
            ->withPivot('tag_role_id')
            ->using('App\VideoTag');
    }

    public function simpleRepresentation()
    {
        $x = [
            'id' => $this->id,
            'tag_name' => $this->tag_name,
        ];
        if (isset($this->pivot)) {
            $x['tag_role_id'] = $this->pivot->tag_role_id;
        }
        return $x;
    }
}
