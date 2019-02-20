<?php 
/*
 *-------------------------------------------------------------------------
 * List of Controllers
 *-------------------------------------------------------------------------
 */
$app['controller.admin.user_group'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Admin\UserGroup(
        $app['repository.admin.user_group'],
        $app['twig'],
        $app['url_generator']
    );
};

$app['controller.admin.user'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Admin\User(
        $app['repository.admin.user'],
        $app['repository.admin.user_group'],
        $app['twig'],
        $app['url_generator']
    );
};

$app['controller.admin.upload'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Admin\Upload(
        $app['repository.admin.upload']
    );
};

$app['controller.admin.dashboard'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Admin\Dashboard(
        $app['twig']
    );
};

$app['controller.api.user'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\API\User(
        $app['repository.admin.user']
    );
};

$app['controller.login'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Login(
        $app['repository.login'],
        $app['twig'],
        $app['url_generator']
    );
};

$app['controller.forgot_password'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\ForgotPassword(
        $app['repository.forgot_password'],
        $app['twig'],
        $app['url_generator'],
        $app['mailer'],
        $app['translator']
    );
};

$app['controller.oauth'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Oauth(
        $app['repository.oauth'],
        $app['twig'],
        $app['url_generator']
    );
};

$app['controller.home'] = function() use ($app) {
    return new App\Semlohe\Http\Controllers\Home(
        $app['twig'],
        $app['url_generator']
    );
};
