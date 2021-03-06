<?php

/**
 * Auth Routes
 */
Route::group(['middleware' => 'web', 'prefix' => 'auth', 'namespace' => 'Modules\Users\Http\Controllers'], function()
{
    Route::get('/login', ['as' => 'login', 'uses' => 'AuthController@login']);
    Route::post('/login', ['as' => 'login-post', 'uses' => 'AuthController@postLogin']);
    Route::get('/logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
});

Route::group(['middleware' => 'web', 'prefix' => 'password', 'namespace' => 'Modules\Users\Http\Controllers'], function(){
    Route::get('reset/{token}', ['as' => 'password.reset', 'uses' => 'RecoverPasswordController@getReset']);
    Route::post('reset', ['as' => 'reset-password', 'uses' => 'RecoverPasswordController@postReset']);
});
/** Module Routes **/
Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Users\Http\Controllers'], function() {
    Route::resource('users', 'UsersController', ['only' => ['index', 'show', 'create', 'edit', 'store', 'update']]);
    Route::get('users/{id}/avatar', 'UsersController@getAvatar');
    Route::post('users/{id}/avatar', 'UsersController@updateAvatar');
    Route::group(['middleware' => ['acl:Crear roles,Editar roles,Eliminar roles,Listar roles,Asignación de permisos a roles']], function(){
        Route::resource('roles', 'RolesController');
        Route::get('roles/{id}/permissions', ['as' => 'roles.permissions', 'uses' => 'RolesController@permissions']);
        Route::put('roles/{id}/update-permissions', ['as' => 'roles.permissions.update', 'uses' => 'RolesController@permissionsUpdate']);
    });
    Route::group(['prefix' => 'config', 'middleware' => ['acl:Editar campos de perfil']], function(){
        Route::get('/', ['as' => 'users.config.menu', 'uses' => 'ConfigController@config']);
        Route::get('users', ['as' => 'users.config', 'uses' => 'ConfigController@index']);
        Route::get('users/create-field', ['as' => 'users.config.create', 'uses' => 'ConfigController@createField']);
        Route::get('users/edit-field/{id}', ['as' => 'users.config.edit', 'uses' => 'ConfigController@editField']);
    });
    Route::get('u/{uuid}', ['as' => 'user.profile', 'uses' => 'ProfileController@show']);
    Route::get('me/edit', ['as' => 'me.edit', 'uses' => 'ProfileController@edit']);
    Route::put('me/edit', ['as' => 'me.update', 'uses' => 'ProfileController@update']);
});

Route::group(['middleware' => ['api'], 'prefix' => 'api', 'namespace' => 'Modules\Users\Http\Controllers'], function(){
    Route::post('/users/forgot-password', 'UsersController@forgotPassword');
    Route::post('users/find-users', 'UsersController@find');
    Route::delete('users/{id}', 'UsersController@destroy');
});