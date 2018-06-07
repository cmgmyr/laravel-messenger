@extends('layouts.master')

@section('content')
    <div class="col-md-6">
        <h1>{{ $thread->subject }}</h1>
        @each($viewSpace.'.partials.messages', $thread->messages, 'message')

        @include($viewSpace.'.partials.form-message')
    </div>
@stop
