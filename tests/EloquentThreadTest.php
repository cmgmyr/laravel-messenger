<?php namespace Cmgmyr\Messenger\tests;

use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model as Eloquent;
use ReflectionClass;

class EloquentThreadTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Eloquent::unguard();
    }

    /**
     * Activate private/protected methods for testing
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Cmgmyr\Messenger\Models\Thread');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /** @test */
    public function it_should_create_a_new_thread()
    {
        $thread = $this->faktory->build('thread');
        $this->assertEquals('Sample thread', $thread->subject);

        $thread = $this->faktory->build('thread', ['subject' => 'Second sample thread']);
        $this->assertEquals('Second sample thread', $thread->subject);
    }

    /** @test */
    public function it_should_return_the_latest_message()
    {
        $oldMessage = $this->faktory->build('message', [
            'created_at' => Carbon::yesterday()
        ]);

        $newMessage = $this->faktory->build('message', [
            'created_at' => Carbon::now(),
            'body' => 'This is the most recent message'
        ]);

        $thread = $this->faktory->create('thread');
        $thread->messages()->saveMany([$oldMessage, $newMessage]);
        $this->assertEquals($newMessage->body, $thread->latestMessage->body);
    }

    /** @test */
    public function it_should_return_all_threads()
    {
        $threadCount = rand(5, 20);

        foreach (range(1, $threadCount) as $index) {
            $this->faktory->create('thread', ['id' => ($index + 1)]);
        }

        $threads = Thread::getAllLatest()->get();

        $this->assertCount($threadCount, $threads);
    }

    /** @test */
    public function it_should_get_all_thread_participants()
    {
        $thread = $this->faktory->create('thread');
        $participants = $thread->participantsUserIds();
        $this->assertCount(0, $participants);

        $user_1 = $this->faktory->build('participant');
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $user_3 = $this->faktory->build('participant', ['user_id' => 3]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $participants = $thread->participantsUserIds();
        $this->assertCount(3, $participants);

        $this->assertInternalType('array', $participants);
    }

    /** @test */
    public function it_should_get_all_threads_for_a_user()
    {
        $userId = 1;

        $participant_1 = $this->faktory->create('participant', ['user_id' => $userId]);
        $thread = $this->faktory->create('thread');
        $thread->participants()->saveMany([$participant_1]);

        $thread2 = $this->faktory->create('thread', ['subject' => 'Second Thread']);
        $participant_2 = $this->faktory->create('participant', ['user_id' => $userId, 'thread_id' => $thread2->id]);
        $thread2->participants()->saveMany([$participant_2]);

        $threads = Thread::forUser($userId)->get();
        $this->assertCount(2, $threads);
    }

    /** @test */
    public function it_should_get_all_threads_for_a_user_with_new_messages()
    {
        $userId = 1;

        $participant_1 = $this->faktory->create('participant', ['user_id' => $userId, 'last_read' => Carbon::now()]);
        $thread = $this->faktory->create('thread', ['updated_at' => Carbon::yesterday()]);
        $thread->participants()->saveMany([$participant_1]);

        $thread2 = $this->faktory->create('thread', ['subject' => 'Second Thread', 'updated_at' => Carbon::now()]);
        $participant_2 = $this->faktory->create('participant', ['user_id' => $userId, 'thread_id' => $thread2->id, 'last_read' => Carbon::yesterday()]);
        $thread2->participants()->saveMany([$participant_2]);

        $threads = Thread::forUserWithNewMessages($userId)->get();
        $this->assertCount(1, $threads);
    }

    /** @test */
    public function it_should_add_participants_to_a_thread()
    {
        $participants = [1, 2, 3];

        $thread = $this->faktory->create('thread');

        $thread->addParticipants($participants);

        $this->assertEquals(3, $thread->participants()->count());
    }

    /** @test */
    public function it_should_mark_the_participant_as_read()
    {
        $userId = 1;
        $last_read = Carbon::yesterday();

        $participant = $this->faktory->create('participant', ['user_id' => $userId, 'last_read' => $last_read]);
        $thread = $this->faktory->create('thread');
        $thread->participants()->saveMany([$participant]);

        $thread->markAsRead($userId);

        $this->assertNotEquals($thread->getParticipantFromUser($userId)->last_read, $last_read);
    }

    /** @test */
    public function it_should_see_if_thread_is_unread_by_user()
    {
        $userId = 1;

        $participant_1 = $this->faktory->create('participant', ['user_id' => $userId, 'last_read' => Carbon::now()]);
        $thread = $this->faktory->create('thread', ['updated_at' => Carbon::yesterday()]);
        $thread->participants()->saveMany([$participant_1]);

        $this->assertFalse($thread->isUnread($userId));

        $thread2 = $this->faktory->create('thread', ['subject' => 'Second Thread', 'updated_at' => Carbon::now()]);
        $participant_2 = $this->faktory->create('participant', ['user_id' => $userId, 'thread_id' => $thread2->id, 'last_read' => Carbon::yesterday()]);
        $thread2->participants()->saveMany([$participant_2]);

        $this->assertTrue($thread2->isUnread($userId));
    }

    /** @test */
    public function it_should_get_a_participant_from_userid()
    {
        $userId = 1;

        $participant = $this->faktory->create('participant', ['user_id' => $userId]);
        $thread = $this->faktory->create('thread');
        $thread->participants()->saveMany([$participant]);

        $newParticipant = $thread->getParticipantFromUser($userId);

        $this->assertInstanceOf('\Cmgmyr\Messenger\Models\Participant', $newParticipant);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function it_should_throw_an_exception_when_participant_is_not_found()
    {
        $thread = $this->faktory->create('thread');

        $thread->getParticipantFromUser(99);
    }

    /** @test */
    public function it_should_activate_all_deleted_participants()
    {
        $deleted_at = Carbon::yesterday();
        $thread = $this->faktory->create('thread');

        $user_1 = $this->faktory->build('participant', ['deleted_at' => $deleted_at]);
        $user_2 = $this->faktory->build('participant', ['user_id' => 2, 'deleted_at' => $deleted_at]);
        $user_3 = $this->faktory->build('participant', ['user_id' => 3, 'deleted_at' => $deleted_at]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $participants = $thread->participants();
        $this->assertEquals(0, $participants->count());

        $thread->activateAllParticipants();

        $participants = $thread->participants();
        $this->assertEquals(3, $participants->count());
    }

    /** @test */
    public function it_should_generate_participant_select_string()
    {
        $method = self::getMethod('createSelectString');
        $thread = new Thread();

        $columns = ['name'];
        $select = $method->invokeArgs($thread, [$columns]);
        $this->assertEquals("(" . Eloquent::getConnectionResolver()->getTablePrefix() . "users.name) as name", $select);

        $columns = ['name', 'email'];
        $select = $method->invokeArgs($thread, [$columns]);
        $this->assertEquals("(" . Eloquent::getConnectionResolver()->getTablePrefix() . "users.name || ' ' || " . Eloquent::getConnectionResolver()->getTablePrefix() . "users.email) as name", $select);
    }

    /** @test */
    public function it_should_get_participants_string()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $participant_3 = $this->faktory->build('participant', ['user_id' => 3]);

        $thread->participants()->saveMany([$participant_1, $participant_2, $participant_3]);

        $string = $thread->participantsString();
        $this->assertEquals("Chris Gmyr, Adam Wathan, Taylor Otwell", $string);

        $string = $thread->participantsString(1);
        $this->assertEquals("Adam Wathan, Taylor Otwell", $string);

        $string = $thread->participantsString(1, ['email']);
        $this->assertEquals("adam@test.com, taylor@test.com", $string);
    }
}
