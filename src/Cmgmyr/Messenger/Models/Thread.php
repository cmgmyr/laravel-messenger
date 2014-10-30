<?php namespace Cmgmyr\Messenger\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Thread extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threads';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['subject'];

    /**
     * Messages relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('Cmgmyr\Messenger\Models\Message');
    }

    /**
     * Returns the latest message from a thread
     *
     * @return \Cmgmyr\Messenger\Models\Message
     */
    public function latestMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Participants relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('Cmgmyr\Messenger\Models\Participant');
    }

    /**
     * Returns an array of user ids that are associated with the thread
     *
     * @return array
     */
    public function participantsUserIds()
    {
        return $this->participants->lists('user_id');
    }

    /**
     * Returns threads that the user is associated with
     *
     * @param $query
     * @param null $user
     * @return mixed
     */
    public function scopeForUser($query, $user = null)
    {
        $user = $user ?: \Auth::user()->id;

        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $user)
            ->select('threads.*');
    }

    /**
     * Returns threads with new messages that the user is associated with
     *
     * @param $query
     * @param null $user
     * @return mixed
     */
    public function scopeWithNewMessages($query, $user = null)
    {
        $user = $user ?: \Auth::user()->id;

        return $query->join('participants', 'thread.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $user)
            ->where('threads.updated_at', '>', \DB::raw('participants.last_read'))
            ->select('threads.*');
    }

    /**
     * Returns a string of thread participants names
     *
     * @param null $user
     * @return string
     */
    public function participantsString($user = null)
    {
        $user = $user ?: \Auth::user()->id;

        $participantNames = \DB::table('users')
            ->join('participants', 'users.id', '=', 'participants.user_id')
            ->where('users.id', '!=', $user)
            ->where('participants.thread_id', $this->id)
            ->select(\DB::raw("concat(users.first_name, ' ', users.last_name) as name"))
            ->lists('users.name');

        return implode(', ', $participantNames);
    }

    /**
     * Adds users to this thread
     *
     * @param array $participants list of all participants
     * @return void
     */
    public function addParticipants(array $participants)
    {
        if(count($participants))
        {
            foreach ($participants as $user_id)
            {
                Participant::create([
                    'user_id' => $user_id,
                    'thread_id' => $this->id,
                ]);
            }
        }
    }

}