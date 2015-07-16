<?php namespace Cmgmyr\Messenger\Domain\Thread;

interface ThreadRepository
{
    /**
     * Persists a given thread
     *
     * @param Thread $thread
     * @return Thread
     */
    public function save(Thread $thread);
}
