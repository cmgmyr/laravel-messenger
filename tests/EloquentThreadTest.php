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
} 