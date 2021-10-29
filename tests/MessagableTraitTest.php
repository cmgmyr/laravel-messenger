<?php

namespace Cmgmyr\Messenger\Tests;

use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Support\Carbon;

class MessagableTraitTest extends TestCase
{
    /** @test */
    public function it_should_get_all_threads_with_new_messages()
    {
        $user = $this->addUser();

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
        $this->assertEquals(1, $threads->first()->id);

        $this->assertEquals(1, $user->newThreadsCount());
    }

    /** @test */
    public function it_get_all_incoming_messages_count_for_user()
    {
        $user = $this->addUser();

        $thread_1 = $this->faktory->create('thread');
        $participant_11 = $this->faktory->build('participant', ['user_id' => $user->id, 'last_read' => Carbon::now()->subDays(5)]);
        $participant_12 = $this->faktory->build('participant', ['user_id' => 2]);
        $thread_1->participants()->saveMany([$participant_11, $participant_12]);

        $thread_2 = $this->faktory->create('thread');
        $participant_21 = $this->faktory->build('participant', ['user_id' => 3, 'last_read' => Carbon::now()->subDays(5)]);
        $participant_22 = $this->faktory->build('participant', ['user_id' => 2]);
        $thread_2->participants()->saveMany([$participant_21, $participant_22]);

        for ($i = 0; $i < 10; $i++) {
            $thread_1->messages()->saveMany([$this->faktory->build('message', ['user_id' => 2, 'created_at' => Carbon::now()->subDays(1)])]);
        }

        for ($i = 0; $i < 5; $i++) {
            $thread_1->messages()->saveMany([$this->faktory->build('message', ['user_id' => 2, 'created_at' => Carbon::now()->subDays(10)])]);
        }

        $thread_2->messages()->saveMany([$this->faktory->build('message', ['user_id' => 2])]);

        $this->assertEquals(10, $user->unreadMessagesCount());
    }

    /** @test */
    public function it_should_get_participant_threads()
    {
        $user = $this->addUser();

        $thread = $this->faktory->create('thread');
        $user_1 = $this->faktory->build('participant', ['user_id' => $user->id]);
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $thread->participants()->saveMany([$user_1, $user_2]);

        $firstThread = $user->threads->first();
        $this->assertInstanceOf(Thread::class, $firstThread);
    }
}
