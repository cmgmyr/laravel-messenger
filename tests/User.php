<?php

namespace Cmgmyr\Messenger\Tests;

use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    use Messagable;

    protected $table = 'users';

    protected $guarded = [];
}
