<?php

namespace Cmgmyr\Messenger\Test\Models;

use Cmgmyr\Messenger\Models\Message;

class CustomMessage extends Message
{
    protected $table = 'custom_messages';
}
