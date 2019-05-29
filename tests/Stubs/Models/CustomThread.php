<?php

namespace Cmgmyr\Messenger\Tests\Stubs\Models;

use Cmgmyr\Messenger\Models\Thread;

class CustomThread extends Thread
{
    protected $table = 'custom_threads';
}
