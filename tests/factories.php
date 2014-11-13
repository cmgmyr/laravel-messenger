<?php

$faktory->define(['thread', 'Cmgmyr\Messenger\Models\Thread'], function ($f) {
    $f->subject = "Sample thread";
});

$faktory->define(['message', 'Cmgmyr\Messenger\Models\Message'], function ($f) {
    $f->user_id = 1;
    $f->thread_id = 1;
    $f->body = "A message";
});

$faktory->define(['participant', 'Cmgmyr\Messenger\Models\Participant'], function ($f) {
    $f->user_id = 1;
    $f->thread_id = 1;
});
