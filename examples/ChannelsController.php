<?php

namespace App\Http\Controllers;

use App\User;
use App\Channel;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class ChannelsController extends MessagesController
{

    protected $viewSpace    = 'channels';
    protected $routeSpace   = 'channels';

    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        // All threads, ignore deleted/archived participants
//         $threads = Thread::getAllLatest()->get();

        // All threads that user is participating in
//         $threads = Thread::forUser(Auth::id())->latest('updated_at')->get();

        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages(Auth::id())->latest('updated_at')->get();

        $threads = Thread::publicThread(Channel::first())->latest('updated_at')->get();

        return view( $this->viewSpace . '.index', compact('threads'));
    }

}
