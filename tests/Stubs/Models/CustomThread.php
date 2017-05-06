<?php

namespace Cmgmyr\Messenger\Test\Stubs\Models;

use Cmgmyr\Messenger\Models\Message;

class CustomThread extends Message
{
    protected $table = 'custom_threads';
}
