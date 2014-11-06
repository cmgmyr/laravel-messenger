<?php namespace Cmgmyr\Messenger\Tests;

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
    public function it_sets_a_thread_subject()
    {
        $thread = new Thread;
        $thread->subject = 'Test Thread';

        $this->assertEquals('Test Thread', $thread->subject);
    }

    /** @test */
    public function it_can_retrieve_the_latest_message()
    {
        $old_message = $this->faktory->build('message', [
            'created_at' => new \DateTime('5 days ago')
        ]);

        $new_message = $this->faktory->build('message', [
            'created_at' => new \DateTime,
            'body' => 'This is the most recent message'
        ]);

        $thread = $this->faktory->create('thread');
        $thread->messages()->saveMany([$old_message, $new_message]);
        $this->assertEquals($new_message->body, $thread->latestMessage()->body);
    }
}
