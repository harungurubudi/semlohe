<?php 
use Silex\Application;

date_default_timezone_set('Asia/Jakarta');

$app = new Application(); 

include __DIR__ . '/providers.php';
include __DIR__ . '/exceptions.php';
include __DIR__ . '/libraries.php';
include __DIR__ . '/models.php';
include __DIR__ . '/datasources.php';
include __DIR__ . '/repositories.php';
include __DIR__ . '/controllers.php';
include __DIR__ . '/middlewares.php';
include __DIR__ . '/../src/Http/routes.php';

