<?php
$api = $app['controllers_factory'];

// Api user route
$api->get('/user/self', 'controller.api.user:self');
$api->get('/user/{id}', 'controller.api.user:detail');
$api->get('/user', 'controller.api.user:index');

$api->before($hasAccessToken);

$app->mount('/api', $api);

// Get token
$app->post('/api/token', "controller.oauth:getToken")->bind('oauth.get_token');
