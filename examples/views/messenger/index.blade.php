@extends('layouts.master')

@section('content')
    @include($viewSpace.'.partials.flash')

    @each($viewSpace.'.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
@stop
