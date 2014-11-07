<?php namespace Cmgmyr\Messenger\Models;

use Carbon\Carbon;
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
     * Returns all of the latest threads by updated_at date
     *
     * @return mixed
     */
    public static function getAllLatest()
    {
        return self::latest('updated_at')->get();
    }

    /**
     * Returns an array of user ids that are associated with the thread
     *
     * @return array
     */
    public function participantsUserIds()
    {
        $users = $this->participants()->withTrashed()->lists('user_id');
        $users[] = \Auth::user()->id;

        return $users;
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
            ->where('participants.deleted_at', null)
            ->select('threads.*')
            ->latest('updated_at')
            ->get();
    }

    /**
     * Returns threads with new messages that the user is associated with
     *
     * @param $query
     * @param null $user
     * @return mixed
     */
    public function scopeForUserWithNewMessages($query, $user = null)
    {
        $user = $user ?: \Auth::user()->id;

        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $user)
            ->where('participants.deleted_at', null)
            ->where('threads.updated_at', '>', \DB::raw('participants.last_read'))
            ->select('threads.*')
            ->latest('updated_at')
            ->get();
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
                Participant::firstOrCreate([
                    'user_id' => $user_id,
                    'thread_id' => $this->id,
                ]);
            }
        }
    }

    /**
     * Mark a thread as read for a user
     *
     * @param null|integer $userId
     */
    public function markAsRead($userId = null)
    {
        $participant = $this->getParticipantFromUser($userId);

        if ($participant) {
            $participant->last_read = new Carbon;
            $participant->save();
        }
    }

    /**
     * See if the current thread is unread by the user
     *
     * @param null|integer $userId
     * @return bool
     */
    public function isUnread($userId = null)
    {
        $participant = $this->getParticipantFromUser($userId);

        if ($participant && ($this->updated_at > $participant->last_read)) {
            return true;
        }

        return false;
    }

    /**
     * Finds the participant record from a user id
     *
     * @param $userId
     * @return mixed
     */
    public function getParticipantFromUser($userId)
    {
        $userId = $userId ?: \Auth::user()->id;

        return $this->participants()->where('user_id', $userId)->first();
    }

    /**
     * Restores all participants within a thread that has a new message
     */
    public function activateAllParticipants()
    {
        $participants = $this->participants()->withTrashed()->get();
        foreach ($participants as $participant) {
            $participant->restore();
        }
    }

}