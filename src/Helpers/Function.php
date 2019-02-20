<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

if (!function_exists('generateId')) {
    function generateId() {
        $uuid4 = Uuid::uuid4();
        return $uuid4->toString();
    } 
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}


if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }
            $array = $array[$segment];
        }
        return $array;
    }
}

if (!function_exists('object_get')) {
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object  $object
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return value($default);
            }
            $object = $object->{$segment};
        }
        return $object;
    }
}

if (!function_exists('slugify')) {

    /**
     * Convert test into dash separated slug
     *
     * @param string $text
     * @return string
     */
    function slugify($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

if (!function_exists('isValidSlug')) {

    /**
     * Make sure slug is only contain alphanumeric and dash
     *
     * @param $text
     * @return boolean
     */
    function isValidSlug($text)
    {
        return preg_match('/^[a-z][-a-z0-9]*$/', $text);
    }
}


if (!function_exists('getUploadPath')) {

    /**
     * Return upload path
     *
     * @param $path
     * @return boolean
     */
    function getUploadPath($path)
    {
        return __DIR__ . '/../../public/images/' . $path;
    }
}

if (!function_exists('getImage')) {

    /**
     * Return upload path
     *
     * @param $path
     * @return boolean
     */
    function getImage($path, $imageFile, $dimension = '')
    {
        $dimensionPath = $dimension === '' ? '' : $dimension . '/';
        $file = __DIR__ . '/../../public/images/' . $path . '/' . $dimensionPath . $imageFile;
        if (file_exists($file)) {
            return config_get('base.url') . '/images/' . $path . '/' . $dimensionPath . $imageFile;
        } else {
            return config_get('base.url') . '/images/not-found/' . $path . '/' . $dimension . '.jpg';  
        }
    }
}

if (!function_exists('generateRandomString')) {
    /**
     * Generate random string
     *
     * @param integer $length
     * @return string
     */
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}