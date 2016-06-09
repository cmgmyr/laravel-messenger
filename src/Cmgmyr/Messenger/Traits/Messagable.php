<?php

namespace Cmgmyr\Messenger\Traits;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Models;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;

trait Messagable
{
    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Models::classname(Message::class));
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(Models::classname(Participant::class));
    }

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function threads()
    {
        return $this->belongsToMany(
            Models::classname(Thread::class),
            Models::table('participants'),
            'user_id',
            'thread_id'
        );
    }

    /**
     * Unread threads as a relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function unreadThreads()
    {
        return $this->threads()
            ->whereRaw('(' . Models::table('threads') . '.updated_at > '
                    . Models::table('participants') . '.last_read'
                    . ' OR ' . Models::table('participants') . '.last_read IS NULL)');
    }

    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function newThreadsCount()
    {
        return $this->unreadThreads()->count();
    }

    /**
     * Returns the id of all threads with new messages.
     *
     * @return array
     */
    public function threadsWithNewMessages()
    {
        $threads = $this->unreadThreads()->lists(Models::table('threads') . '.id');

        //Always return array (L5.0 vs L5.1 lists behaviour change)
        return is_array($threads) ? $threads : $threads->all();
    }
}
