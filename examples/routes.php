<?php

Route::group(['prefix' => 'channels'], function () {
    Route::get('/', ['as' => 'channels', 'uses' => 'ChannelsController@index']);
    Route::get('create', ['as' => 'channels.create', 'uses' => 'ChannelsController@create']);
    Route::post('/', ['as' => 'channels.store', 'uses' => 'ChannelsController@store']);
    Route::get('{id}', ['as' => 'channels.show', 'uses' => 'ChannelsController@show']);
    Route::put('{id}', ['as' => 'channels.update', 'uses' => 'ChannelsController@update']);
});

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
    Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
    Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
    Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
    Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
});
