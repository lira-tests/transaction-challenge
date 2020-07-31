<?php

$router->get('/', function () use ($router) {
    return [
        'application' => config('app.name'),
        'version' => $router->app->version(),
    ];
});

$router->group(['prefix' => 'v1'], function ($router) {
    $router->group(['prefix' => 'users'], function ($router) {
        $router->get('/', 'UserController@index');
    });
    $router->group(['prefix' => 'transactions'], function ($router) {
        $router->get('/', 'TransactionController@index');
        $router->post('/', 'TransactionController@create');
    });
});
