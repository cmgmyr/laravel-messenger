<?php namespace Cmgmyr\Messenger\tests;

use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentThreadTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Eloquent::unguard();
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
        $this->assertEquals($newMessage->body, $thread->latestMessage()->body);
    }

    /** @test */
    public function it_should_return_all_threads()
    {
        $threadCount = rand(5, 20);

        foreach (range(1, $threadCount) as $index) {
            $this->faktory->create('thread', ['id' => ($index + 1)]);
        }

        $threads = Thread::getAllLatest();

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

        $threads = Thread::forUser($userId);
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

        $threads = Thread::forUserWithNewMessages($userId);
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
}
