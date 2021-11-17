<?php

namespace Cmgmyr\Messenger\Tests;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Models;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Tests\Stubs\Models\CustomMessage;
use Cmgmyr\Messenger\Tests\Stubs\Models\CustomParticipant;
use Cmgmyr\Messenger\Tests\Stubs\Models\CustomThread;

class CustomModelsTest extends TestCase
{
    /** @test */
    public function it_can_use_custom_message_model(): void
    {
        $this->setMessageCustomModel();
        $this->assertEquals(CustomMessage::class, get_class(Models::message()));
        $this->unsetMessageCustomModel();
    }

    /** @test */
    public function it_can_use_custom_participant_model(): void
    {
        $this->setParticipantCustomModel();
        $this->assertEquals(CustomParticipant::class, get_class(Models::participant()));
        $this->unsetParticipantCustomModel();
    }

    /** @test */
    public function it_can_use_custom_thread_model(): void
    {
        $this->setThreadCustomModel();
        $this->assertEquals(CustomThread::class, get_class(Models::thread()));
        $this->unsetThreadCustomModel();
    }

    /** @test */
    public function it_can_use_custom_table(): void
    {
        $this->setMessageCustomModel();
        $this->setMessageCustomTable();

        $this->assertEquals('custom_messages', Models::table('messages'));

        $this->unsetMessageCustomModel();
        $this->unsetMessageCustomTable();
    }

    /** @test */
    public function it_should_return_custom_name_when_not_available(): void
    {
        $modelName = 'ModelNotFound';

        $this->assertEquals('ModelNotFound', Models::classname($modelName));
    }

    /** :TODO: test */
    public function it_can_get_custom_model_table_property(): void
    {
        $this->setMessageCustomModel();

        $this->assertEquals('custom_messages', Models::message()->getTable());

        $this->unsetMessageCustomModel();
    }

    protected function setMessageCustomModel(): void
    {
        Models::setMessageModel(CustomMessage::class);
    }

    protected function setParticipantCustomModel(): void
    {
        Models::setParticipantModel(CustomParticipant::class);
    }

    protected function setThreadCustomModel(): void
    {
        Models::setThreadModel(CustomThread::class);
    }

    protected function unsetMessageCustomModel(): void
    {
        Models::setMessageModel(Message::class);
    }

    protected function unsetParticipantCustomModel(): void
    {
        Models::setParticipantModel(Participant::class);
    }

    protected function unsetThreadCustomModel(): void
    {
        Models::setThreadModel(Thread::class);
    }

    protected function setMessageCustomTable(): void
    {
        Models::setTables([
            'messages' => 'custom_messages',
        ]);
    }

    protected function unsetMessageCustomTable(): void
    {
        Models::setTables([
            'messages' => 'messages',
        ]);
    }
}
