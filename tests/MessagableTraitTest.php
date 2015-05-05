<?php namespace Cmgmyr\Messenger\tests;

use Carbon\Carbon;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Database\Eloquent\Model as Eloquent;

class MessagableTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Eloquent::unguard();
    }

    /** @test */
    public function it_should_get_all_threads_with_new_messages()
    {
        $user = User::create(
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'notify' => 'y'
            ]
        );

        $thread = $this->faktory->create('thread');
        $user_1 = $this->faktory->build('participant', ['user_id' => $user->id, 'last_read' => Carbon::yesterday()]);
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $thread->participants()->saveMany([$user_1, $user_2]);

        $message_1 = $this->faktory->build('message', ['user_id' => 2]);
        $thread->messages()->saveMany([$message_1]);

        $thread2 = $this->faktory->create('thread');
        $user_1b = $this->faktory->build('participant', ['user_id' => 3, 'last_read' => Carbon::yesterday()]);
        $user_2b = $this->faktory->build('participant', ['user_id' => 2]);
        $thread2->participants()->saveMany([$user_1b, $user_2b]);

        $message_1b = $this->faktory->build('message', ['user_id' => 2]);
        $thread2->messages()->saveMany([$message_1b]);

        $threads = $user->threadsWithNewMessages();
        $this->assertEquals(1, $threads[0]);

        $this->assertEquals(1, $user->newMessagesCount());
    }
}

class User extends Eloquent
{
    use Messagable;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'notify'];
}
