<?php namespace Cmgmyr\Messenger\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Config;

class Participant extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'participants';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['thread_id', 'user_id', 'last_read'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'last_read'];

    /**
     * Thread relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo('Cmgmyr\Messenger\Models\Thread');
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Config::get('messenger::user_model'));
    }

    /**
     * Finds the participant that is the current user
     *
     * @param $query
     * @param null $user
     * @return mixed
     */
    public function scopeMe($query, $user = null)
    {
        $user = $user ?: \Auth::user()->id;

        return $query->where('user_id', '=', $user);
    }

    /**
     * Finds the participant that is not the current user
     *
     * @param $query
     * @param null $user
     * @return mixed
     */
    public function scopeNotMe($query, $user = null)
    {
        $user = $user ?: \Auth::user()->id;

        return $query->where('user_id', '!=', $user);
    }

}