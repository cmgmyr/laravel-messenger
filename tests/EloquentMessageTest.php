<?php

namespace Cmgmyr\Messenger\Tests;

class EloquentMessageTest extends TestCase
{
    /** @test */
    public function it_should_get_the_recipients_of_a_message(): void
    {
        $message = $this->messageFactory();
        $thread = $this->threadFactory();

        $thread->messages()->saveMany([$message]);

        $user_1 = $this->participantFactory();
        $user_2 = $this->participantFactory(['user_id' => 2]);
        $user_3 = $this->participantFactory(['user_id' => 3]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $this->assertEquals(2, $message->recipients()->count());
    }
}
