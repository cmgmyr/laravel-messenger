<?php

namespace Cmgmyr\Messenger\Test;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Models;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Test\Stubs\Models\CustomMessage;
use Cmgmyr\Messenger\Test\Stubs\Models\CustomParticipant;
use Cmgmyr\Messenger\Test\Stubs\Models\CustomThread;

class CustomModelsTest extends TestCase
{
    /** @test */
    public function it_can_use_custom_message_model()
    {
        $this->setMessageCustomModel();
        $this->assertEquals(CustomMessage::class, get_class(Models::message()));
        $this->unsetMessageCustomModel();
    }

    /** @test */
    public function it_can_use_custom_participant_model()
    {
        $this->setParticipantCustomModel();
        $this->assertEquals(CustomParticipant::class, get_class(Models::participant()));
        $this->unsetParticipantCustomModel();
    }

    /** @test */
    public function it_can_use_custom_thread_model()
    {
        $this->setThreadCustomModel();
        $this->assertEquals(CustomThread::class, get_class(Models::thread()));
        $this->unsetThreadCustomModel();
    }

    /** @test */
    public function it_can_use_custom_table()
    {
        $this->setMessageCustomModel();
        $this->setMessageCustomTable();

        $this->assertEquals('custom_messages', Models::table('messages'));

        $this->unsetMessageCustomModel();
        $this->unsetMessageCustomTable();
    }

    /** @test */
    public function it_should_return_custom_name_when_not_available()
    {
        $modelName = 'ModelNotFound';

        $this->assertEquals('ModelNotFound', Models::classname($modelName));
    }

    /** :TODO: test */
    public function it_can_get_custom_model_table_property()
    {
        $this->setMessageCustomModel();

        $this->assertEquals('custom_messages', Models::message()->getTable());

        $this->unsetMessageCustomModel();
    }

    protected function setMessageCustomModel()
    {
        Models::setMessageModel(CustomMessage::class);
    }

    protected function setParticipantCustomModel()
    {
        Models::setParticipantModel(CustomParticipant::class);
    }

    protected function setThreadCustomModel()
    {
        Models::setThreadModel(CustomThread::class);
    }

    protected function unsetMessageCustomModel()
    {
        Models::setMessageModel(Message::class);
    }

    protected function unsetParticipantCustomModel()
    {
        Models::setParticipantModel(Participant::class);
    }

    protected function unsetThreadCustomModel()
    {
        Models::setThreadModel(Thread::class);
    }

    protected function setMessageCustomTable()
    {
        Models::setTables([
            'messages' => 'custom_messages',
        ]);
    }

    protected function unsetMessageCustomTable()
    {
        Models::setTables([
            'messages' => 'messages',
        ]);
    }
}
