<?php
require_once __DIR__ . "/Routes/Admin.php";
require_once __DIR__ . "/Routes/API.php";

// Login 
$app->post('/login', "controller.login:login")->before($hasNotLogin);
$app->get('/login', "controller.login:index")->bind('login.index')->before($hasNotLogin);
$app->get('/logout', "controller.login:logout")->bind('login.logout')->before($hasLogin);

// Forgot Password
$app->get('/forgot-password/password-changed', "controller.forgot_password:passwordChanged")->bind('forgot_password.password_changed')->before($hasNotLogin);
$app->get('/forgot-password/email-sent', "controller.forgot_password:emailSent")->bind('forgot_password.info_sent')->before($hasNotLogin);
$app->post('/forgot-password/reset', "controller.forgot_password:changePasswordUpdate")->before($hasNotLogin);
$app->get('/forgot-password/reset', "controller.forgot_password:changePasswordIndex")->bind('forgot_password.change')->before($hasNotLogin);
$app->post('/forgot-password', "controller.forgot_password:requestEmail")->before($hasNotLogin);
$app->get('/forgot-password', "controller.forgot_password:index")->bind('forgot_password.index')->before($hasNotLogin);

// Oauth
$app->get('/authorize', "controller.oauth:authorize")->bind('oauth.authorize')->before($hasLogin);

$app->get('/', "controller.home:index")->bind('home.index');
