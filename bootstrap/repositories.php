<?php 
/*
 *-------------------------------------------------------------------------
 * List of Repositories
 *-------------------------------------------------------------------------
 */
$app['repository.admin.user_group'] = new App\Semlohe\Repositories\Admin\UserGroup(
    $app['datasource.user_group'],
    $app['library.fractal'],
    $app['translator'],
    $app['url_generator']
);

$app['repository.admin.user'] = new App\Semlohe\Repositories\Admin\User(
    $app['datasource.user'],
    $app['library.fractal'],
    $app['translator'],
    $app['url_generator']
);

$app['repository.admin.upload'] = new App\Semlohe\Repositories\Admin\Upload(
    $app['translator']
);

$app['repository.login'] = new App\Semlohe\Repositories\Login(
    $app['repository.admin.user'],
    $app['repository.admin.user_group'],
    $app['library.token']
);

$app['repository.forgot_password'] = new App\Semlohe\Repositories\ForgotPassword(
    $app['repository.admin.user'],
    $app['library.token'],
    $app['translator']
);

$app['repository.oauth'] = new App\Semlohe\Repositories\OAuth(
    $app['datasource.client'],
    $app['repository.admin.user'],
    $app['library.token'],
    $app['translator']
);