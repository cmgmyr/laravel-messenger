<?php namespace Cmgmyr\Messenger\Domain\Thread;

class ThreadService
{
    /**
     * @var ThreadRepository
     */
    protected $repo;

    public function __construct(ThreadRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Returns a thread with a given id
     *
     * @param $id
     * @return mixed
     */
    public function getThread($id)
    {
        return $this->repo->getById($id);
    }

    /**
     * Returns a collection of threads for a given user
     *
     * @param $userId
     * @return Thread
     */
    public function getThreadsForUser($userId)
    {
        return $this->repo->getForUser($userId);
    }
}
