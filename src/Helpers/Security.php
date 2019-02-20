<?php
use Defuse\Crypto;

if (!function_exists('encrypt')) {

    /**
     * Encrypt string
     *
     * @param $text
     * @return $text
     */
    function encrypt($text)
    {
        $keyString = config_get('security.encryption_key');
        $key = Crypto\Key::loadFromAsciiSafeString($keyString);
        return Crypto\Crypto::encrypt($text, $key);
    }
}

if (!function_exists('decrypt')) {

    /**
     * Decrypt string
     *
     * @param $text
     * @return $text
     */
    function decrypt($text)
    {
        $keyString = config_get('security.encryption_key');
        $key = Crypto\Key::loadFromAsciiSafeString($keyString);
        return Crypto\Crypto::decrypt($text, $key);
    }
}

if (!function_exists('hash_bcrypt')) 
{    
    /**
     * Hash text with bcrypt
     *
     * @param string $text
     * @return string
     */
    function hash_bcrypt($text)
    {
        return password_hash($text, PASSWORD_BCRYPT, [
            'cost' => 10
        ]);
    }
}

if (!function_exists('hash_bcrypt_verify')) 
{    
    /**
     * Verify hash and text with bcrypt
     *
     * @param string $text
     * @param string $hash
     * @return boolean
     */
    function hash_bcrypt_verify($text, $hash)
    {
        return password_verify($text, $hash);
    }
}
