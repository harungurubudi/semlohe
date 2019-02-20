<?php
namespace App\Semlohe\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WhoopsServiceProvider implements ServiceProviderInterface
{
    /**
     * Register whoops service
     *
     * @param Application $app
     */
    public function register(Container $app)
    {
        $app['whoops'] = function ($app) {
            $whoops =  new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            return $whoops;
        };
    }

    /**
     * @see Silex\ServiceProviderInterface::boot
     */
    public function boot(Application $app)
    {
    }
}
