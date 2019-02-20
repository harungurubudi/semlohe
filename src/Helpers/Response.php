<?php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

if (!function_exists('redirectResponse')) {
    /**
     * Generate response in json format
     *
     * @param string $url
     * @param array $headers
     * @param array $cookies
     * @return Symfony\Component\HttpFoundation\Response
     */
    function redirectResponse($url, array $headers = [], array $cookies = [])
    {
        $code = array_get($headers, 'code', 302);

        $response = new RedirectResponse($url, $code);
        if (is_array($headers) && $headers !== []) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
        }
        if (is_array($cookies) && $cookies !== []) {
            foreach ($cookies as $key => $value) {
                $cookie = new Cookie(
                    $key,
                    encrypt($value),
                    time() + (2 * 365 * 24 * 60 * 60),
                    null,
                    false,
                    false
                );
                $response->headers->setCookie($cookie);
            }
        }
        return $response;
    }
}


if (!function_exists('webResponse')) {
    /**
     * Generate response in json format
     *
     * @param mixed $body
     * @param array $headers
     * @param array $cookies
     * @return Symfony\Component\HttpFoundation\Response
     */
    function webResponse($body, array $headers = [], array $cookies = [])
    {
        $code = array_get($headers, 'code', 200);
        $response = new Response($body, $code);
        if (is_array($headers) && $headers !== []) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
        }
        if (is_array($cookies) && $cookies !== []) {
            foreach ($cookies as $key => $value) {
                $cookie = new Cookie(
                    $key,
                    encrypt($value),
                    time() + ( 2 * 365 * 24 * 60 * 60),
                    null,
                    false,
                    false
                );
                $response->headers->setCookie($cookie);
            }
        }
        return $response;
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * Generate response in json format
     *
     * @param string $body
     * @param array $headers
     * @param array $cookie
     * @return Symfony\Component\HttpFoundation\Response
     */
    function jsonResponse($body, array $headers = [], array $cookies = [])
    {
        $code = array_get($body, 'meta.code', 200);
        $response = new Response(json_encode($body), $code);
        $response->headers->set('Content-Type', 'application/vnd.api+json');
        if (is_array($headers) && $headers !== []) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
        }
        if (is_array($cookies) && $cookies !== []) {
            foreach ($cookies as $key => $value) {
                $cookie = new Cookie(
                    $key,
                    encrypt($value),
                    time() + ( 2 * 365 * 24 * 60 * 60),
                    null,
                    false,
                    false
                );
                $response->headers->setCookie($cookie);
            }
        }
        return $response;
    }
}

if (!function_exists('exceptionResponse')) {
    /**
     * Get exception response.
     *
     * @param \Exception $e
     * @param int $code
     * @param string $message
     * @return Symfony\Component\HttpFoundation\Response
     */
    function exceptionResponse(\Exception $e, $code = 500, $message = 'Internal server error')
    {
        if (config_get('env.debug')) {
            throw $e;
        } else {
            return jsonResponse([
                'meta' => [
                    'code' => $code,
                    'message' => $message
                ]
            ]);
        }
    }
}
