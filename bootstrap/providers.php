<?php 
/*
 *-------------------------------------------------------------------------
 * Silex Built in Services
 *-------------------------------------------------------------------------
 */
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\RoutingServiceProvider());

/*
 *-------------------------------------------------------------------------
 * Debugger Service
 *-------------------------------------------------------------------------
 */
use App\Semlohe\Providers\WhoopsServiceProvider;

$app['debug'] = config_get('env.debug');
$app['enable_profiler'] = config_get('env.enable_profiler');

if ($app['debug']) {
    $app->register(new WhoopsServiceProvider());
}

/*
 *-------------------------------------------------------------------------
 * Logger Service
 *-------------------------------------------------------------------------
 */
use Silex\Provider\MonologServiceProvider;

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile'   => __DIR__ . '/../log/' . config_env() . '.log',
    'monolog.name'      => 'app',
    'monolog.level'     => 'WARNING'
));

/*
 *-------------------------------------------------------------------------
 * Swiftmailer Service
 *-------------------------------------------------------------------------
 */
use Silex\Provider\SwiftmailerServiceProvider;
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.options'   => config_get('mail.smtp')
));

/*
 *-------------------------------------------------------------------------
 * Translation Service
 *-------------------------------------------------------------------------
 */
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\JsonFileLoader;

$app->register(new TranslationServiceProvider(), [
    'locale' => config_get('default.locale'),
    'locale_fallbacks' => array('en_us')
]);

$app['translator'] = $app->extend('translator', function($translator, $app) {
    $translator->addLoader('json', new JsonFileLoader());
    $translator->addResource('json', __DIR__.'/../resources/lang/en_us.json', 'en_us');
    $translator->addResource('json', __DIR__.'/../resources/lang/id_id.json', 'id_id');
    return $translator;
});

/*
 *-------------------------------------------------------------------------
 * Database Service
 *-------------------------------------------------------------------------
 */
use \JG\Silex\Provider\CapsuleServiceProvider;

$app->register(
    new CapsuleServiceProvider(),
    [
        'capsule.connections' => [
            'default' => config_get('db')
        ],
        'capsule.options' => [
            'setAsGlobal'    => true,
            'bootEloquent'   => true,
            'enableQueryLog' => true,
        ],
    ]
);

// $app['capsule']->connection()->listen(function($query) {
//     var_dump($query->sql);
// });

/*
 *-------------------------------------------------------------------------
 * Template Service
 *-------------------------------------------------------------------------
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../src/Templates',
    'twig.options'    => array(
        // 'cache' => __DIR__ . '/../cache',
        'cache' => false, // Due server permission issues, view cache is disabled
    ),
));

$app['twig']->addFilter(
    new Twig_SimpleFilter('config', function($key) {
        return config_get($key);
    })
);

$app['twig']->addFilter(
    new Twig_SimpleFilter('map', function($key) {
        return map_get($key);
    })
);

$app['twig']->addFilter(
    new Twig_SimpleFilter('assets', function($name) {
        return assets_get($name);
    })
);

$app['twig']->addFilter(
    new Twig_SimpleFilter('image', function($file, $path, $dimension) {
        return getImage($path, $file, $dimension);
    })
);

$app['twig']->addFilter(
    new Twig_SimpleFilter('trans', function($key) use ($app) {
        return $app['translator']->trans($key);
    })
);