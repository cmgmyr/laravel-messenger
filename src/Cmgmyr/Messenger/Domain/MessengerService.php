<?php namespace Cmgmyr\Messenger\Domain;

use Cmgmyr\Messenger\Domain\Thread\Thread;
use Cmgmyr\Messenger\Domain\Thread\ThreadService;

class MessengerService
{
    /**
     * @var ThreadService
     */
    protected $threadService;

    public function __construct(ThreadService $threadService)
    {
        $this->threadService = $threadService;
    }

    /**
     * Returns thread that matches a given id
     *
     * @param $id
     * @return Thread
     */
    public function getThread($id)
    {
        return $this->threadService->getThread($id);
    }
}
