<?php
/*
 *-------------------------------------------------------------------------
 * All Libraries
 *-------------------------------------------------------------------------
 */
// Token generator service
$app['library.token'] = new App\Semlohe\Libraries\TokenGeneratorService();

// Fractal service
$app['fractal.manager'] = new League\Fractal\Manager();
$app['library.fractal'] = new App\Semlohe\Libraries\FractalService(
    $app['fractal.manager']
);
