<?php 
/*
 *-------------------------------------------------------------------------
 * All Datasources
 *-------------------------------------------------------------------------
 */
$app['datasource.user_group'] = new App\Semlohe\DataSources\UserGroup(
    $app['model.user_group']
);

$app['datasource.user'] = new App\Semlohe\DataSources\User(
    $app['model.user']
);

$app['datasource.client'] = new App\Semlohe\DataSources\Client(
    $app['model.client']
);