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

    public function getThread($id)
    {
        return $this->repo->getById($id);
    }

    public function getThreadsForUser($userId)
    {
        return $this->repo->getForUser($userId);
    }
}
