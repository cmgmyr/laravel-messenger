<?php namespace Cmgmyr\Messenger\Traits;

trait Messagable
{

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('Cmgmyr\Messenger\Models\Message');
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function threads()
    {
        return $this->belongsToMany('Cmgmyr\Messenger\Models\Thread', 'participants');
    }

    /**
     * Returns the new messages count for user
     *
     * @return int
     */
    public function newMessagesCount()
    {
        return count($this->threadsWithNewMessages());
    }

    /**
     * Returns all threads with new messages
     *
     * @return array
     */
    public function threadsWithNewMessages()
    {
        $threadsWithNewMessages = [];
        $participants = \Cmgmyr\Messenger\Models\Participant::where('user_id', $this->id)->lists('last_read', 'thread_id');

        if ($participants)
        {
            $threads = \Cmgmyr\Messenger\Models\Thread::whereIn('id', array_keys($participants))->get();

            foreach ($threads as $thread)
            {
                if ($thread->updated_at > $participants[$thread->id])
                {
                    $threadsWithNewMessages[] = $thread->id;
                }
            }
        }

        return $threadsWithNewMessages;
    }
}