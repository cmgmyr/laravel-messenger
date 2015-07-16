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
     * Returns all threads
     * $messenger->getAllThreads();
     *
     * @return Thread
     */
    public function getAllThreads()
    {
        // @todo: Implement getAllThreads() method.
    }

    /**
     * Returns all threads that a user is participant in.
     * $messenger->getAllThreadsForUser(1)
     *
     * @param $userId
     * @return Thread
     */
    public function getAllThreadsForUser($userId)
    {
        // @todo: Implement getAllThreadsForUser() method.
    }

    /**
     * Returns all new threads that a user is participant in.
     * $messenger->getNewThreadsForUser(1)
     *
     * @param $userId
     * @return Thread
     */
    public function getNewThreadsForUser($userId)
    {
        // @todo: Implement getNewThreadsForUser() method.
    }

    /**
     * Returns thread that matches a given id
     * $messenger->getThread(1);
     *
     * @param $id
     * @return Thread
     */
    public function getThread($id)
    {
        return $this->threadService->getThread($id);
    }

    /**
     * Returns the full base model. This will be helpful for making
     * any sort of custom queries.
     *
     * @return Thread
     */
    public function getThreadModel()
    {
        // @todo: Implement getThreadModel() method.
    }
}
