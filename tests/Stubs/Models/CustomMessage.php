<?php

namespace Cmgmyr\Messenger\Tests\Stubs\Models;

use Cmgmyr\Messenger\Models\Message;

class CustomMessage extends Message
{
    protected $table = 'custom_messages';
}
