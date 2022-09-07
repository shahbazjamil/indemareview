<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 Route::group(['middleware' => ['auth']], function () {
    // Admin routes
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['role:admin']], function () {
        Route::get('zoom-meeting/table', 'AdminZoomMeetingController@tableView')->name('zoom-meeting.table-view');
        Route::get('zoom-meeting/start-meeting/{id}', 'AdminZoomMeetingController@startMeeting')->name('zoom-meeting.startMeeting');
        Route::post('zoom-meeting/cancel-meeting', 'AdminZoomMeetingController@cancelMeeting')->name('zoom-meeting.cancelMeeting');
        Route::post('zoom-meeting/end-meeting', 'AdminZoomMeetingController@endMeeting')->name('zoom-meeting.endMeeting');
        Route::post('zoom-meeting/updateOccurrence/{id}', 'AdminZoomMeetingController@updateOccurrence')->name('zoom-meeting.updateOccurrence');
        Route::resource('zoom-meeting', 'AdminZoomMeetingController');

        Route::resource('zoom-setting', 'ZoomMeetingSettingController');
    });
    // Employee routes
    Route::group(['prefix' => 'member', 'as' => 'member.', 'middleware' => ['role:employee']], function () {
        Route::get('zoom-meeting/start-meeting/{id}', 'EmployeeZoomMeetingController@startMeeting')->name('zoom-meeting.startMeeting');
        Route::post('zoom-meeting/cancel-meeting', 'EmployeeZoomMeetingController@cancelMeeting')->name('zoom-meeting.cancelMeeting');
        Route::post('zoom-meeting/end-meeting', 'EmployeeZoomMeetingController@endMeeting')->name('zoom-meeting.endMeeting');
        Route::post('zoom-meeting/updateOccurrence/{id}', 'EmployeeZoomMeetingController@updateOccurrence')->name('zoom-meeting.updateOccurrence');
        Route::resource('zoom-meeting', 'EmployeeZoomMeetingController');
    });
    // Client routes
    Route::group(['prefix' => 'client', 'as' => 'client.', 'middleware' => ['role:client']], function () {
        Route::get('zoom-meeting/start-meeting/{id}', 'ClientZoomMeetingController@startMeeting')->name('zoom-meeting.startMeeting');
        Route::resource('zoom-meeting', 'ClientZoomMeetingController');
    });
});

Route::post('zoom-webhook', 'ZoomWebhookController@index')->name('zoom-webhook');



