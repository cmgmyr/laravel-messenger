<?php namespace Cmgmyr\Messenger\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Thread extends Eloquent
{
    use SoftDeletes;

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
    protected $fillable = ['subject', 'threadable_id', 'threadable_type'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * "Users" table name to use for manual queries
     *
     * @var string|null
     */
    private $usersTable = null;

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
    public function getLatestMessageAttribute()
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
     * Participants relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participant_users()
    {
        //return $this->participants()->join('users', 'participants.user_id', '=', 'users.id');

        return $this->hasManyThrough('Cmgmyr\Messenger\Models\Participant','App\User', 'id', 'user_id');
    }



    /**
     * Returns the user object that created the thread
     *
     * @return mixed
     */
    public function creator()
    {
        return $this->messages()->oldest()->first()->user;
    }

    /**
     * Returns all of the latest threads by updated_at date
     *
     * @return mixed
     */
    public static function getAllLatest()
    {
        return self::latest('updated_at');
    }

    /**
     * Returns an array of user ids that are associated with the thread
     *
     * @param null $userId
     * @return array
     */
    public function participantsUserIds($userId = null)
    {
        $users = $this->participants()->withTrashed()->lists('user_id');

        if ($userId) {
            $users[] = $userId;
        }

        return $users;
    }

    /**
     * Returns threads that the user is associated with
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUser($query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->where('participants.deleted_at', null)
            ->select('threads.*');
    }

    /**
     * Returns threads with new messages that the user is associated with
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUserWithNewMessages($query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->whereNull('participants.deleted_at')
            ->where(function ($query) {
                $query->where('threads.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . 'participants.last_read'))
                    ->orWhereNull('participants.last_read');
            })
            ->select('threads.*');
    }

    /**
     * Returns threads between given user ids
     *
     * @param $query
     * @param $participants
     * @return mixed
     */
    public function scopeBetween($query, array $participants)
    {
        $query->whereHas('participants', function ($query) use ($participants) {
            $query->whereIn('user_id', $participants)
                    ->groupBy('thread_id')
                    ->havingRaw('COUNT(thread_id)='.count($participants));
        });
    }

    /**
     * Adds users to this thread
     *
     * @param array $participants list of all participants
     * @return void
     */
    public function addParticipants(array $participants)
    {
        if (count($participants)) {
            foreach ($participants as $user_id) {
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
     * @param integer $userId
     */
    public function markAsRead($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            $participant->last_read = new Carbon;
            $participant->save();
        } catch (ModelNotFoundException $e) {
            // do nothing
        }
    }

    /**
     * See if the current thread is unread by the user
     *
     * @param integer $userId
     * @return bool
     */
    public function isUnread($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            if ($this->updated_at > $participant->last_read) {
                return true;
            }
        } catch (ModelNotFoundException $e) {
            // do nothing
        }

        return false;
    }

    /**
     * Finds the participant record from a user id
     *
     * @param $userId
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getParticipantFromUser($userId)
    {
        return $this->participants()->where('user_id', $userId)->firstOrFail();
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

    /**
     * Generates a string of participant information
     *
     * @param null $userId
     * @param array $columns
     * @return string
     */
    public function participantsString($userId=null, $columns=['name'])
    {
        $selectString = $this->createSelectString($columns);

        $participantNames = $this->getConnection()->table($this->getUsersTable())
            ->join('participants', $this->getUsersTable() . '.id', '=', 'participants.user_id')
            ->where('participants.thread_id', $this->id)
            ->select($this->getConnection()->raw($selectString));

        if ($userId !== null) {
            $participantNames->where($this->getUsersTable() . '.id', '!=', $userId);
        }

        $userNames = $participantNames->lists($this->getUsersTable() . '.name');

        return implode(', ', $userNames);
    }

    /**
     * Checks to see if a user is a current participant of the thread
     *
     * @param $userId
     * @return bool
     */
    public function hasParticipant($userId)
    {
        $participants = $this->participants()->where('user_id', '=', $userId);
        if ($participants->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Generates a select string used in participantsString()
     *
     * @param $columns
     * @return string
     */
    protected function createSelectString($columns)
    {
        $dbDriver = $this->getConnection()->getDriverName();

        switch ($dbDriver) {
            case 'pgsql':
            case 'sqlite':
                $columnString = implode(" || ' ' || " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".", $columns);
                $selectString = "(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ") as name";
                break;
            case 'sqlsrv':
                $columnString = implode(" + ' ' + " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".", $columns);
                $selectString = "(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ") as name";
                break;
            default:
                $columnString = implode(", ' ', " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".", $columns);
                $selectString = "concat(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ") as name";
        }

        return $selectString;
    }

    /**
     * Sets the "users" table name
     *
     * @param $tableName
     */
    public function setUsersTable($tableName)
    {
        $this->usersTable = $tableName;
    }

    /**
     * Returns the "users" table name to use in manual queries
     *
     * @return string
     */
    private function getUsersTable()
    {
        if ($this->usersTable !== null) {
            return $this->usersTable;
        }

        $userModel = Config::get('messenger.user_model');
        return $this->usersTable = (new $userModel)->getTable();
    }


    //http://stackoverflow.com/questions/27866020/laravel-returning-the-namespaced-owner-of-a-polymorphic-relation/27909753#27909753
    /**
     * Get all of the owning threadable models.
     */
    public function threadable()
    {
        return $this->morphTo();
    }



    protected $types = [
        'newsfeed_thread' => 'App\NewsfeedThread',
        'thread' => 'App\Thread',
        'opp_push' => 'App\OppPush',
        'organization' => 'App\Organization'
    ];

    public function getThreadableTypeAttribute($type) {
        // transform to lower case
        $type = strtolower($type);

        // to make sure this returns value from the array
        return array_get($this->types, $type, $type);

        // which is always safe, because new 'class'
        // will work just the same as new 'Class'
    }


}
