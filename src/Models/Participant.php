<?php

namespace Cmgmyr\Messenger\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Eloquent
{
    use SoftDeletes;

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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['last_read' => 'datetime'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Models::table('participants');

        parent::__construct($attributes);
    }

    /**
     * Thread relationship.
     *
     * @return BelongsTo
     *
     * @codeCoverageIgnore
     */
    public function thread()
    {
        return $this->belongsTo(Models::classname(Thread::class), 'thread_id', 'id');
    }

    /**
     * User relationship.
     *
     * @return BelongsTo
     *
     * @codeCoverageIgnore
     */
    public function user()
    {
        return $this->belongsTo(Models::user(), 'user_id');
    }
}
