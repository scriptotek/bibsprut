<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'token', 'token_type', 'created_at'];

    /**
     * Get the user that owns the token.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
