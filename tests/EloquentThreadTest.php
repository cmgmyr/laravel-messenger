<?php

namespace Cmgmyr\Messenger\Tests;

use Cmgmyr\Messenger\Models\Models;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use ReflectionClass;

class EloquentThreadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Eloquent::unguard();
    }

    /**
     * Activate private/protected methods for testing.
     *
     * @param $name
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass(Thread::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /** @test */
    public function search_specific_thread_by_subject()
    {
        $this->faktory->create('thread', ['id' => 1, 'subject' => 'first subject']);
        $this->faktory->create('thread', ['id' => 2, 'subject' => 'second subject']);

        $threads = Thread::getBySubject('first subject');

        $this->assertEquals(1, $threads->count());
        $this->assertEquals(1, $threads->first()->id);
        $this->assertEquals('first subject', $threads->first()->subject);
    }

    /** @test */
    public function search_threads_by_subject()
    {
        $this->faktory->create('thread', ['id' => 1, 'subject' => 'first subject']);
        $this->faktory->create('thread', ['id' => 2, 'subject' => 'second subject']);

        $threads = Thread::getBySubject('%subject');

        $this->assertEquals(2, $threads->count());

        $this->assertEquals(1, $threads->first()->id);
        $this->assertEquals('first subject', $threads->first()->subject);

        $this->assertEquals(2, $threads->last()->id);
        $this->assertEquals('second subject', $threads->last()->subject);
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
            'created_at' => Carbon::yesterday(),
        ]);

        $newMessage = $this->faktory->build('message', [
            'created_at' => Carbon::now(),
            'body' => 'This is the most recent message',
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
        $participantIds = $thread->participantsUserIds();
        $this->assertCount(0, $participantIds);

        $user_1 = $this->faktory->build('participant');
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $user_3 = $this->faktory->build('participant', ['user_id' => 3]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $participantIds = $thread->participantsUserIds();
        $this->assertCount(3, $participantIds);
        $this->assertEquals(2, $participantIds[1]);

        $participantIds = $thread->participantsUserIds(999);
        $this->assertCount(4, $participantIds);
        $this->assertEquals(999, end($participantIds));

        $this->assertIsArray($participantIds);
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
    public function it_should_get_all_user_entities_for_a_thread()
    {
        $thread = $this->faktory->create('thread');
        $user_1 = $this->faktory->build('participant');
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $thread->participants()->saveMany([$user_1, $user_2]);

        $threadUserIds = $thread->users()->get()->pluck('id')->values()->flip();
        $this->assertArrayHasKey(1, $threadUserIds);
        $this->assertArrayHasKey(2, $threadUserIds);
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
    public function it_should_get_all_threads_shared_by_specified_users()
    {
        $userId = 1;
        $userId2 = 2;

        $thread = $this->faktory->create('thread');
        $thread2 = $this->faktory->create('thread');

        $this->faktory->create('participant', ['user_id' => $userId, 'thread_id' => $thread->id]);
        $this->faktory->create('participant', ['user_id' => $userId2, 'thread_id' => $thread->id]);
        $this->faktory->create('participant', ['user_id' => $userId, 'thread_id' => $thread2->id]);

        $threads = Thread::between([$userId, $userId2])->get();
        $this->assertCount(1, $threads);
    }

    /** @test **/
    public function it_should_get_thread_between_participants(): void
    {
        $participant_1 = 1;
        $participant_2 = 2;
        $participant_3 = 3;
        $participant_4 = 4;

        $thread_1 = $this->faktory->create('thread');
        $this->faktory->create('participant', ['user_id' => $participant_1, 'thread_id' => $thread_1->id]);
        $this->faktory->create('participant', ['user_id' => $participant_2, 'thread_id' => $thread_1->id]);
        $this->faktory->create('participant', ['user_id' => $participant_3, 'thread_id' => $thread_1->id]);
        $this->faktory->create('participant', ['user_id' => $participant_4, 'thread_id' => $thread_1->id]);


        $thread_2 = $this->faktory->create('thread');
        $this->faktory->create('participant', ['user_id' => $participant_1, 'thread_id' => $thread_2->id]);
        $this->faktory->create('participant', ['user_id' => $participant_2, 'thread_id' => $thread_2->id]);

        $thread_3 = $this->faktory->create('thread');
        $this->faktory->create('participant', ['user_id' => $participant_1, 'thread_id' => $thread_3->id]);
        $this->faktory->create('participant', ['user_id' => $participant_2, 'thread_id' => $thread_3->id]);
        $this->faktory->create('participant', ['user_id' => $participant_3, 'thread_id' => $thread_3->id]);

        $threads_1 = Thread::between([$participant_1, $participant_2, $participant_3]);
        $threads_2 = Thread::between([$participant_1, $participant_2]);
        $this->assertCount(2, $threads_1->get());
        $this->assertCount(3, $threads_2->get());
    }

    /** @test **/
    public function it_should_get_thread_between_only_participants(): void
    {
        $participant_1 = $this->faktory->build('participant', ['user_id' => 1]);
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $participant_3 = $this->faktory->build('participant', ['user_id' => 3]);
        $participant_4 = $this->faktory->build('participant', ['user_id' => 4]);

        $thread_1 = $this->faktory->create('thread');
        $thread_1->participants()->saveMany([$participant_1, $participant_2, $participant_3, $participant_4]);

        $thread_2 = $this->faktory->create('thread');
        $thread_2->participants()->saveMany([$participant_1, $participant_2]);

        $thread_3 = $this->faktory->create('thread');
        $thread_3->participants()->saveMany([$participant_1, $participant_2, $participant_3]);

        $threads_1 = Thread::betweenOnly([$participant_1->id, $participant_2->id, $participant_3->id]);
        $threads_2 = Thread::betweenOnly([$participant_1->id, $participant_2->id]);
        $this->assertCount(1, $threads_1->get());
        $this->assertCount(1, $threads_2->get());
    }

    /** @test */
    public function it_should_add_a_participant_to_a_thread()
    {
        $participant = 1;

        $thread = $this->faktory->create('thread');

        $thread->addParticipant($participant);

        $this->assertEquals(1, $thread->participants()->count());
    }

    /** @test */
    public function it_should_add_participants_to_a_thread_with_array()
    {
        $participants = [1, 2, 3];

        $thread = $this->faktory->create('thread');

        $thread->addParticipant($participants);

        $this->assertEquals(3, $thread->participants()->count());
    }

    /** @test */
    public function it_should_add_participants_to_a_thread_with_arguments()
    {
        $thread = $this->faktory->create('thread');

        $thread->addParticipant(1, 2);

        $this->assertEquals(2, $thread->participants()->count());
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

        $this->assertInstanceOf(Participant::class, $newParticipant);
    }

    /** @test */
    public function it_should_throw_an_exception_when_participant_is_not_found()
    {
        try {
            $thread = $this->faktory->create('thread');

            $thread->getParticipantFromUser(99);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('ModelNotFoundException was not called.');
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
        $tableName = Models::table('users');

        $columns = ['name'];
        $select = $method->invokeArgs($thread, [$columns]);
        $this->assertEquals('(' . Eloquent::getConnectionResolver()->getTablePrefix() . $tableName . '.name) as name', $select);

        $columns = ['name', 'email'];
        $select = $method->invokeArgs($thread, [$columns]);
        $this->assertEquals('(' . Eloquent::getConnectionResolver()->getTablePrefix() . $tableName . ".name || ' ' || " . Eloquent::getConnectionResolver()->getTablePrefix() . $tableName . '.email) as name', $select);
    }

    /** @test */
    public function it_should_get_participants_string()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $participant_3 = $this->faktory->build('participant', ['user_id' => 3]);
        $participant_4 = $this->faktory->build('participant', ['user_id' => 4]);

        $thread->participants()->saveMany([$participant_1, $participant_2, $participant_3, $participant_4]);

        $string = $thread->participantsString();
        $this->assertEquals('Chris Gmyr, Adam Wathan, Taylor Otwell, Abdullah Al-Faqeir', $string);

        $string = $thread->participantsString(1);
        $this->assertEquals('Adam Wathan, Taylor Otwell, Abdullah Al-Faqeir', $string);

        $string = $thread->participantsString(1, ['email']);
        $this->assertEquals('adam@test.com, taylor@test.com, abdullah@test.com', $string);
    }

    /** @test */
    public function it_should_check_users_and_participants()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);

        $thread->participants()->saveMany([$participant_1, $participant_2]);

        $this->assertTrue($thread->hasParticipant(1));
        $this->assertTrue($thread->hasParticipant(2));
        $this->assertFalse($thread->hasParticipant(3));
    }

    /** @test */
    public function it_should_remove_a_single_participant()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);

        $thread->participants()->saveMany([$participant_1, $participant_2]);

        $thread->removeParticipant(2);

        $this->assertEquals(1, $thread->participants()->count());
    }

    /** @test */
    public function it_should_remove_a_group_of_participants_with_array()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);

        $thread->participants()->saveMany([$participant_1, $participant_2]);

        $thread->removeParticipant([1, 2]);

        $this->assertEquals(0, $thread->participants()->count());
    }

    /** @test */
    public function it_should_remove_a_group_of_participants_with_arguments()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);

        $thread->participants()->saveMany([$participant_1, $participant_2]);

        $thread->removeParticipant(1, 2);

        $this->assertEquals(0, $thread->participants()->count());
    }

    /** @test */
    public function it_should_get_all_unread_messages_for_user()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);

        $message_1 = $this->faktory->build('message', [
            'created_at' => Carbon::now(),
            'body' => 'Message 1',
            'user_id' => $participant_1->user_id,
        ]);

        $thread->participants()->saveMany([$participant_1, $participant_2]);
        $thread->messages()->saveMany([$message_1]);

        $thread->markAsRead($participant_2->user_id);

        // Simulate delay after last read
        sleep(1);

        $message_2 = $this->faktory->build('message', [
            'created_at' => Carbon::now(),
            'body' => 'Message 2',
            'user_id' => $participant_1->user_id,
        ]);

        $thread->messages()->saveMany([$message_2]);

        $this->assertCount(0, $thread->userUnreadMessages($participant_1->user_id));

        $secondParticipantUnreadMessages = $thread->userUnreadMessages($participant_2->user_id);
        $this->assertCount(1, $secondParticipantUnreadMessages);
        $this->assertEquals('Message 2', $secondParticipantUnreadMessages->first()->body);
    }

    /** @test */
    public function it_should_get_all_unread_messages_for_user_when_dates_not_set()
    {
        $thread = $this->faktory->create('thread');

        $participant_1 = $this->faktory->build('participant');
        $participant_2 = $this->faktory->build('participant', ['user_id' => 2]);

        $message_1 = $this->faktory->build('message', [
//            'created_at' => Carbon::now(),
            'body' => 'Message 1',
            'user_id' => $participant_1->user_id,
        ]);

        $thread->participants()->saveMany([$participant_1, $participant_2]);
        $thread->messages()->saveMany([$message_1]);

        $thread->markAsRead($participant_2->user_id);

        // Simulate delay after last read
        sleep(1);

        $message_2 = $this->faktory->build('message', [
//            'created_at' => Carbon::now(),
            'body' => 'Message 2',
            'user_id' => $participant_1->user_id,
        ]);

        $thread->messages()->saveMany([$message_2]);

        $this->assertCount(0, $thread->userUnreadMessages($participant_1->user_id));

        $secondParticipantUnreadMessages = $thread->userUnreadMessages($participant_2->user_id);
        $this->assertCount(1, $secondParticipantUnreadMessages);
        $this->assertEquals('Message 2', $secondParticipantUnreadMessages->first()->body);
    }

    /** @test */
    public function it_should_return_empty_collection_when_user_not_participant()
    {
        $thread = $this->faktory->create('thread');

        $this->assertEquals(0, $thread->userUnreadMessagesCount(1));
    }

    /** @test */
    public function it_should_get_the_creator_of_a_thread()
    {
        $thread = $this->faktory->create('thread');

        $user_1 = $this->faktory->build('participant');
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $user_3 = $this->faktory->build('participant', ['user_id' => 3]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $message_1 = $this->faktory->build('message', ['created_at' => Carbon::yesterday()]);
        $message_2 = $this->faktory->build('message', ['user_id' => 2]);
        $message_3 = $this->faktory->build('message', ['user_id' => 3]);

        $thread->messages()->saveMany([$message_1, $message_2, $message_3]);

        $this->assertEquals('Chris Gmyr', $thread->creator()->name);
    }

    /**
     * @test
     *
     * TODO: Need to get real creator of the thread without messages in future versions.
     */
    public function it_should_get_the_null_creator_of_a_thread_without_messages()
    {
        $thread = $this->faktory->create('thread');

        $user_1 = $this->faktory->build('participant');
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $user_3 = $this->faktory->build('participant', ['user_id' => 3]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $this->assertFalse($thread->creator()->exists);
        $this->assertNull($thread->creator()->name);
    }
}
