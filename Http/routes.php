<?php

/**
 * Auth Routes
 */
Route::group(['prefix' => 'auth', 'namespace' => 'Modules\Users\Http\Controllers'], function()
{
    Route::get('/login', ['as' => 'login', 'uses' => 'AuthController@login']);
    Route::post('/login', ['as' => 'login-post', 'uses' => 'AuthController@postLogin']);
    Route::get('/logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
});

Route::group(['prefix' => 'password', 'namespace' => 'Modules\Users\Http\Controllers'], function(){
    Route::get('reset/{token}', ['as' => 'reset-password-get', 'uses' => 'RecoverPasswordController@getReset']);
    Route::post('reset', ['as' => 'reset-password', 'uses' => 'RecoverPasswordController@postReset']);
});
/** Module Routes **/
Route::group(['namespace' => 'Modules\Users\Http\Controllers', 'middleware' => ['auth']], function()
{
    Route::group(['middleware' => ['acl:user-create,user-edit,user-delete,user-activate']], function(){
        Route::resource('users', 'UsersController', ['only' => ['index', 'show', 'create', 'edit', 'store', 'update']]);
    });
    Route::post('users/{id}/avatar', 'UsersController@updateAvatar');

    Route::group(['middleware' => ['acl:create-role,edit-role,delete-role,admin-permissions']], function(){
        Route::resource('roles', 'RolesController');
        Route::get('roles/{id}/permissions', ['as' => 'roles.permissions', 'uses' => 'RolesController@permissions']);
        Route::put('roles/{id}/update-permissions', ['as' => 'roles.permissions.update', 'uses' => 'RolesController@permissionsUpdate']);
    });

    Route::group(['prefix' => 'config', 'middleware' => ['acl:user-configuration']], function(){
        Route::get('users', ['as' => 'users.config', 'uses' => 'ConfigController@index']);
        Route::get('users/create-field', ['as' => 'users.config.create', 'uses' => 'ConfigController@createField']);
        Route::get('users/edit-field/{id}', ['as' => 'users.config.edit', 'uses' => 'ConfigController@editField']);
    });

    Route::get('u/{uuid}', ['as' => 'user.profile', 'uses' => 'ProfileController@show']);
    Route::get('me/edit', ['as' => 'me.edit', 'uses' => 'ProfileController@edit']);
    Route::put('me/edit', ['as' => 'me.update', 'uses' => 'ProfileController@update']);
});

/** Module API Routes **/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'Modules\Users\Http\Controllers'], function ($api) {
    $api->group(['middleware' => ['api.auth'], 'providers' => ['inSession']], function($api) {
        $api->group(['prefix' => 'users'], function($api) {
            $api->post('find-users', 'UsersController@find');
            $api->delete('/{id}', 'UsersController@destroy');
            $api->post('/forgot-password', 'UsersController@forgotPassword');
        });
    });
});