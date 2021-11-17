<?php

namespace Cmgmyr\Messenger\Tests;

use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Support\Carbon;

class MessagableTraitTest extends TestCase
{
    /** @test */
    public function it_should_get_all_threads_with_new_messages(): void
    {
        $user = $this->userFactory();

        $thread = $this->threadFactory();
        $user_1 = $this->participantFactory(['user_id' => $user->id, 'last_read' => Carbon::yesterday()]);
        $user_2 = $this->participantFactory(['user_id' => 2]);
        $thread->participants()->saveMany([$user_1, $user_2]);

        $message_1 = $this->messageFactory(['user_id' => 2]);
        $thread->messages()->saveMany([$message_1]);

        $thread2 = $this->threadFactory();
        $user_1b = $this->participantFactory(['user_id' => 3, 'last_read' => Carbon::yesterday()]);
        $user_2b = $this->participantFactory(['user_id' => 2]);
        $thread2->participants()->saveMany([$user_1b, $user_2b]);

        $message_1b = $this->messageFactory(['user_id' => 2]);
        $thread2->messages()->saveMany([$message_1b]);

        $threads = $user->threadsWithNewMessages();
        $this->assertEquals(1, $threads->first()->id);

        $this->assertEquals(1, $user->newThreadsCount());
    }

    /** @test */
    public function it_get_all_incoming_messages_count_for_user(): void
    {
        $user = $this->userFactory();

        $thread_1 = $this->threadFactory();
        $participant_11 = $this->participantFactory(['user_id' => $user->id, 'last_read' => Carbon::now()->subDays(5)]);
        $participant_12 = $this->participantFactory(['user_id' => 2]);
        $thread_1->participants()->saveMany([$participant_11, $participant_12]);

        $thread_2 = $this->threadFactory();
        $participant_21 = $this->participantFactory(['user_id' => 3, 'last_read' => Carbon::now()->subDays(5)]);
        $participant_22 = $this->participantFactory(['user_id' => 2]);
        $thread_2->participants()->saveMany([$participant_21, $participant_22]);

        for ($i = 0; $i < 10; $i++) {
            $thread_1->messages()->saveMany([$this->messageFactory(['user_id' => 2, 'created_at' => Carbon::now()->subDays(1)])]);
        }

        for ($i = 0; $i < 5; $i++) {
            $thread_1->messages()->saveMany([$this->messageFactory(['user_id' => 2, 'created_at' => Carbon::now()->subDays(10)])]);
        }

        $thread_2->messages()->saveMany([$this->messageFactory(['user_id' => 2])]);

        $this->assertEquals(10, $user->unreadMessagesCount());
    }

    /** @test */
    public function it_should_get_participant_threads(): void
    {
        $user = $this->userFactory();

        $thread = $this->threadFactory();
        $user_1 = $this->participantFactory(['user_id' => $user->id]);
        $user_2 = $this->participantFactory(['user_id' => 2]);
        $thread->participants()->saveMany([$user_1, $user_2]);

        $firstThread = $user->threads->first();
        $this->assertInstanceOf(Thread::class, $firstThread);
    }
}
