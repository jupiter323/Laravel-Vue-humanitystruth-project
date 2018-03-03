<?php

Route::group(['middleware' => 'web', 'prefix' => 'securedrop', 'namespace' => 'Modules\SecureDrop\Http\Controllers'], function()
{
    Route::get('/', 'SecureDropController@index');
});
