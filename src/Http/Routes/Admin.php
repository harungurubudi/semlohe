<?php
$admin = $app['controllers_factory'];

// Admin user group route
$admin->patch('/user-group/toggle-status/{id}', 'controller.admin.user_group:toggleStatus')->bind('user_group.switch_status');
$admin->post('/user-group/create', 'controller.admin.user_group:postCreate');
$admin->get('/user-group/create', 'controller.admin.user_group:create')->bind('user_group.create');
$admin->delete('/user-group/{id}', 'controller.admin.user_group:delete');
$admin->post('/user-group/{id}', 'controller.admin.user_group:postEdit');
$admin->get('/user-group/{id}', 'controller.admin.user_group:edit')->bind('user_group.edit');
$admin->get('/user-group', 'controller.admin.user_group:index')->bind('user_group.index');

// Admin user route
$admin->patch('/user/toggle-status/{id}', 'controller.admin.user:toggleStatus')->bind('user.switch_status');
$admin->post('/user/create', 'controller.admin.user:postCreate');
$admin->get('/user/create', 'controller.admin.user:create')->bind('user.create');
$admin->delete('/user/{id}', 'controller.admin.user:delete');
$admin->post('/user/{id}', 'controller.admin.user:postEdit');
$admin->get('/user/{id}', 'controller.admin.user:edit')->bind('user.edit');
$admin->get('/user', 'controller.admin.user:index')->bind('user.index');

$admin->post('/upload', 'controller.admin.upload:postImageUpload');

$admin->before($hasLogin);

$app->mount('/admin', $admin);
$app->get('/admin', 'controller.admin.dashboard:index')
    ->before($hasLogin)
    ->bind('dashboard.index');
