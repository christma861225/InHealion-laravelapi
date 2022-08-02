<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;

// $router-group(['prefix' => ''], function() use($router) {
//     $router->get('/', function() { return "123"; });
// });
$router->get('foo', function () {
    return 'Hello World';
});

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('login', 'UserController@login');
    $router->put('refresh', 'UserController@refresh');
    $router->post('reset-password', 'UserController@resetPassword');
    $router->post('forget-password', 'UserController@forgetPassword');

    $router->post('logout', ['middleware' => 'auth', 'uses' => 'UserController@logout']);
    $router->put('/{id}', ['middleware' => 'auth', 'uses' => 'UserController@update']);

    // $router->get('/{id}/profile', ['middleware' => 'auth', 'uses' => 'UserProfileController@getByUserId']);
    $router->get('/{id}/profile', 'UserProfileController@getByUserId');
});

$router->group(['prefix' => 'folders'], function () use ($router) {
    $router->delete('/{id}', ['middleware' => 'auth', 'uses' => 'FolderController@delete']);
    $router->get('/{id}/h/{hash}', 'FolderController@get');
    $router->get('/{id}', 'FolderController@get');
    $router->get('/{id}/download', ['middleware' => 'auth', 'uses' => 'FolderController@download']);
    $router->get('/{id}/download-link', ['middleware' => 'auth', 'uses' => 'FolderController@prepareDownload']);
    $router->get('/{id}/download/{hash}', 'FolderController@import');

    $router->post('/{folderId}/programs', ['middleware' => 'auth', 'uses' => 'ProgramController@create']);
    $router->get('/{folderId}/programs', ['middleware' => 'auth', 'uses' => 'ProgramController@getAll']);
});

$router->group(['prefix' => 'programs'], function () use ($router) {
    $router->get('/{id}/download', ['middleware' => 'auth', 'uses' => 'ProgramController@download']);
    $router->delete('/{id}', ['middleware' => 'auth', 'uses' => 'ProgramController@delete']);
    $router->delete('/', ['middleware' => 'auth', 'uses' => 'ProgramController@deleteAll']);
});

$router->group(['prefix' => 'firmware'], function () use ($router) {
    $router->get('/', 'FirmwareController@getAll');
    $router->get('/latest', 'FirmwareController@getLatest');
    $router->get('/{version}/download', 'FirmwareController@download');

    $router->post('/', ['middleware' => 'auth', 'uses' => 'FirmwareController@create']);
    $router->put('/{id}', ['middleware' => 'auth', 'uses' => 'FirmwareController@update']);
    $router->delete('/{version}', ['middleware' => 'auth', 'uses' => 'FirmwareController@delete']);
});

$router->group(['prefix' => 'software'], function () use ($router) {
    $router->get('/', 'SoftwareController@getAll');
    $router->get('/latest', 'SoftwareController@getLatest');
    $router->get('/{version}/download', 'SoftwareController@download');

    $router->post('/', ['middleware' => 'auth', 'uses' => 'SoftwareController@create']);
    $router->put('/{id}', ['middleware' => 'auth', 'uses' => 'SoftwareController@update']);
    $router->delete('/{version}', ['middleware' => 'auth', 'uses' => 'SoftwareController@delete']);
});

$router->group(['prefix' => 'profiles'], function () use ($router) {
    
    // $router->post('/', ['uses' => 'UserProfileController@test']);
    $router->post('/', ['uses' => 'UserProfileController@create']);
    $router->put('/{id}', ['uses' => 'UserProfileController@update']);
    $router->delete('/{id}', ['uses' => 'UserProfileController@delete']);
    $router->delete('/', ['uses' => 'UserProfileController@deleteAll']);
    $router->get('/', ['uses' => 'UserProfileController@getAll']);
    $router->get('/{id}', ['uses' => 'UserProfileController@get']);

    $router->get('/{userProfileId}/folders', ['uses' => 'FolderController@getAllByUserProfileId']);
    $router->post('/{userProfileId}/folders', ['uses' => 'FolderController@create']);
});

$router->group(['prefix' => 'scans'], function () use ($router) {
    $router->get('/', 'ScanController@index');

    $router->post('/', 'ScanController@create');
    $router->put('/{id}', 'ScanController@update');
    $router->delete('/{id}', 'ScanController@delete');
    $router->get('/{id}', ['uses' => 'ScanController@get']);
});

$router->group(['prefix' => 'sessionImage'], function () use ($router) {
    $router->get('/', 'SessionImageController@index');
    $router->get('/{id}', 'SessionImageController@getByUserId');

    $router->post('/', 'SessionImageController@create');
    $router->put('/{id}', 'SessionImageController@update');
    $router->delete('/{id}', 'SessionImageController@delete');
});

$router->group(['prefix' => 'sessionText'], function () use ($router) {
    $router->get('/', 'SessionTextController@index');

    $router->post('/', 'SessionTextController@create');
    $router->put('/{id}', 'SessionTextController@update');
    $router->delete('/{id}', 'SessionTextController@delete');
});
