@extends('layouts.master')

@section('content')

    @include( $viewSpace.'.partials.flash' )

    @if( empty( $threads ) || count( $threads ) == 0 )

        @include( $viewSpace.'.partials.no-threads' )

    @else

        @foreach($threads as $thread)

            @include(
                $viewSpace.'.partials.thread',
                [
                    'thread'     => $thread,
                    'routeSpace' => $routeSpace,
                    'viewSpace'  => $viewSpace
                ]
            )

        @endforeach

    @endif

@stop
