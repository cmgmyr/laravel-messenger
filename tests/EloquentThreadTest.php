<?php namespace Cmgmyr\Messenger\Tests;

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
    public function it_creates_a_new_thread()
    {
        $thread = $this->faktory->build('thread');
        $this->assertEquals('Sample thread', $thread->subject);

        $thread = $this->faktory->build('thread', ['subject' => 'Second sample thread']);
        $this->assertEquals('Second sample thread', $thread->subject);
    }

    /** @test */
    public function it_can_retrieve_the_latest_message()
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
}
