<?php

namespace Cmgmyr\Messenger\Tests;

class EloquentMessageTest extends TestCase
{
    /** @test */
    public function it_should_get_the_recipients_of_a_message()
    {
        $message = $this->faktory->build('message');
        $thread = $this->faktory->create('thread');

        $thread->messages()->saveMany([$message]);

        $user_1 = $this->faktory->build('participant');
        $user_2 = $this->faktory->build('participant', ['user_id' => 2]);
        $user_3 = $this->faktory->build('participant', ['user_id' => 3]);

        $thread->participants()->saveMany([$user_1, $user_2, $user_3]);

        $this->assertEquals(2, $message->recipients()->count());
    }
}
