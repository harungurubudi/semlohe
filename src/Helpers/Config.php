<?php
use Noodlehaus\Config;
use Dotenv\Dotenv;

if (!function_exists('config_env')) {
    
    /**
     * Getting current environment
     *
     * @return string
     */
    function config_env()
    {
        $dotenv = new Dotenv(__DIR__ . '/../../');
        $dotenv->load();
        return getenv('ENV');
    }
}

if (!function_exists('config_get')) {
    
    /**
     * Getting config value by it's key
     *
     * @param $key
     * @param string $default
     * @return mixed
     */
    function config_get($key, $default = '')
    {
        $conf = new Config([
            __DIR__ . '/../../config/env/' . config_env() . '.json'
        ]);
        
        return $conf->get($key, $default);
    }
}

if (!function_exists('map_get')) {
    
    /**
     * Getting config value by it's key
     *
     * @param $key
     * @param string $default
     * @return mixed
     */
    function map_get($key, $default = '')
    {
        $conf = new Config([
            __DIR__ . '/../../config/maps.json'
        ]);
        
        return $conf->get($key, $default);
    }
}

if (!function_exists('assets_get')) {
    
    /**
     * Getting assets with base assets config
     *
     * @param $assets
     * @param string $default
     * @return mixed
     */
    function assets_get($assets)
    {
        return config_get('base.assets') . '/' . $assets;
    }
}