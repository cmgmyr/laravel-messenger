<?php

namespace Cmgmyr\Messenger\Models;

use App\User;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Message extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['thread'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['thread_id', 'user_id', 'body'];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'body' => 'required',
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Models::table('messages');

        parent::__construct($attributes);
    }

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Models::classname(Thread::class), 'thread_id', 'id');
    }

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Models::classname(User::class), 'user_id');
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(Models::classname(Participant::class), 'thread_id', 'thread_id');
    }

    /**
     * Recipients of this message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients()
    {
        return $this->participants()->where('user_id', '!=', $this->user_id);
    }
}
