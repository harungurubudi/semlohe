<?php
namespace App\Semlohe\Libraries;

use \Firebase\JWT\JWT;

class TokenGeneratorService
{
    /** @var string $key */
    private $key = '';

    /** @var string $algorithm */
    private $algorithm = 'HS256';

    /**
     * Secret key setter
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Algorithm setter
     *
     * @param string $algorithm - Supported options is : HS256, HS512, HS384, RS256
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Generate new token
     *
     * @param $params
     * @param $appendedKey
     * @return string
     */
    public function generate($params, $appendedKey = '')
    {
        return JWT::encode(
            $params,
            $this->key . $appendedKey,
            $this->algorithm
        );
    }

    /**
     * Check if token is valid
     *
     * @param string $token
     * @return boolean
     */
    public function decode($token, $appendedKey = '')
    {
        $result = JWT::decode(
            $token,
            $this->key . $appendedKey,
            [$this->algorithm]
        );

        return (array) $result;
    }
}
