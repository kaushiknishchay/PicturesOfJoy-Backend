<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {

    // Collection Resource Urls
    $router->get('/getCollection[/{colId}]', 'AdminController@getCollection');
    $router->post('/createCollection', 'AdminController@createCollection');
    $router->post('/updateCollection/{colId}/{colKey}', 'AdminController@updateCollection');
    $router->delete('/deleteCollection/{colId}', 'AdminController@deleteCollection');

    // Album Resource Urls
    $router->get('/getAlbum[/{albumId}]', 'AdminController@getAlbum');
    $router->post('/createAlbum', 'AdminController@createAlbum');
    $router->post('/updateAlbum/{albumId}/{albumKey}', 'AdminController@updateAlbum');
    $router->delete('/deleteAlbum/{albumKey}', 'AdminController@deleteAlbum');

    $router->post('/login', 'UserController@login');
    $router->get('/logout', 'UserController@signout');

    $router->get('/', 'PhotoController@index');
    $router->get('/getAllCollection[/{colKey}]', 'PhotoController@getCollectionsList');
    $router->get('/getAlbumInfo/{albumKey}', 'PhotoController@getAlbumPhotos');

    $router->get('/neelCreate', 'UserController@setupNeelForm');
    $router->post('/neelCreate', 'UserController@setupNeel');
});

