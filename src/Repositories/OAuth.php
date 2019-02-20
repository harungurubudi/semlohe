<?php
namespace App\Semlohe\Repositories;

use App\Semlohe\Repositories\Admin\User;
use App\Semlohe\DataSources\Client;
use App\Semlohe\Exceptions;
use App\Semlohe\Libraries\TokenGeneratorService;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\Generator\UrlGenerator;

class Oauth extends AbstractRepository
{
    /** @var User $user */
    private $user;

    /** @var DataSource $dataSource */
    private $dataSource;

    /** @var Translator $translator */
    private $translator;

    /** @var TokenGeneratorService $token */
    private $token;

    public function __construct(
        Client $client,
        User $user,
        TokenGeneratorService $token,
        Translator $translator
    ) {
        $this->client = $client;
        $this->user = $user;
        $this->token = $token;
        $this->translator = $translator;
    }

    /**
     * Generate authorization key for oauth
     *
     * @param $credentials
     * @param $actor
     * @return array
     */
    public function authorize($credentials, $actor)
    {
        $clientId = array_get($credentials, 'client_id', ''); 
        $scope = array_get($credentials, 'scope', ''); 
        $state = array_get($credentials, 'state', ''); 
        $redirectUrl = array_get($credentials, 'redirect_url', '');

        $client = $this->defineJWTClient($clientId);

        $now = strtotime('now');
        $redirectUrl = $redirectUrl === '' ? $client->redirect_uri : $redirectUrl;

        $baseUrl = config_get('base.url');
        $authorizationKey = $this->token->generate([
            'iss' => $baseUrl,
            'sub' => 'Oauth2 authorization key',
            'aud' => $baseUrl,
            'exp' => $now + 7200,
            'user_id' => array_get($actor, 'id', '')
        ]);

        return $this->responseMeta([
            'data' => [
                'auth_code' => $authorizationKey,
                'state' => $state
            ],
            'meta' => [
                'redirect_uri' => $redirectUrl,
            ]
        ], 200, $this->translator->trans('authorization_succeeded'));
    }

    /**
     * Exchange Authorization key to token
     *
     * @param $grantType
     * @param $clientId
     * @param $clientSecret
     * @param $params
     * @return
     */
    public function getToken($grantType, $clientId, $clientSecret, $params)
    {
        $client = $this->defineJWTClient($clientId);

        switch ($grantType) {
            case 'authorization_code':
                $this->checkClientSecret($clientSecret, $client);
                $userId = $this->validateGetTokenByAuthorization($client, $params);
                $hasRefresh = true;
                break;
            case 'refresh_token':
                $this->checkClientSecret($clientSecret, $client);
                $userId = $this->validateGetTokenByRefreshToken($params);
                $hasRefresh = true;
                break;
            case 'password_credential':
                $userId = $this->validateGetTokenByPasswordCredential($params);
                $hasRefresh = false;
                break;
            default:
                throw new Exceptions\ForbiddenException(
                    $this->translator->trans('invalid_grant_type')
                );
                break;
        }

        return $this->generateOauthToken($userId, $hasRefresh);
    }

    /**
     * Check client secret credential
     *
     * @param string $secret
     * @param App\Semlohe\DataSources\Client $client
     * @return void
     */
    private function checkClientSecret($secret, $client)
    {
        if ($secret !== $client->secret) {
            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_client_secret')
            );
        }
    }

    /**
     * Validate token generation params by authorization
     *
     * @param array $params
     * @param $client
     * @return void
     */
    private function validateGetTokenByAuthorization($client, $params)
    {
        if (array_get($params, 'redirect_uri', '') !== $client->redirect_uri) {
            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_redirect_uri')
            );
        }
        try {
            $auth = $this->token->decode(
                array_get($params, 'auth_code', ''), 
                config_get('token_payload.auth_code')
            );
            $userId = array_get($auth, 'user_id', '');
            
        } catch (\Exception $e) {
            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_auth_code')
            );
        }

        return $userId;
    }

    /**
     * Validate token generation params by refresh token
     *
     * @param array $params
     * @return void
     */
    private function validateGetTokenByRefreshToken($params)
    {
        try {
            $decodedRefreshToken = $this->token->decode(
                array_get($params, 'refresh_token', ''), 
                config_get('token_payload.oauth_refresh_token')
            );

            $userId = array_get($decodedRefreshToken, 'user_id', '');
        } catch (\UnexpectedValueException $e) {
            if ($e instanceOf \Firebase\JWT\BeforeValidException) {
                throw new Exceptions\ForbiddenException(
                    $this->translator->trans('before_valid_refresh_token')
                );
            }

            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_refresh_token')
            );
        }

        return $userId;
    }

    /**
     * Validate token generation params by password credential
     *
     * @param array $params
     * @return void
     */
    private function validateGetTokenByPasswordCredential($params)
    {
        try {
            $username = array_get($params, 'username', '');
            $password = array_get($params, 'password', '');

            if (empty($username)) {
                throw new Exceptions\ForbiddenException(
                    $this->translator->trans('empty_username')
                );
            }
    
            try {
                $result = $this->user->getByUsername(array_get($params, 'username'), []);
                
                if (!hash_bcrypt_verify($password, $result['data']['password'])) {
                    throw new Exceptions\ForbiddenException(
                        $this->translator->trans('invalid_password')
                    );
                }
                
            } catch (Exceptions\NotFoundException $e) {
                throw new Exceptions\ForbiddenException(
                    $this->translator->trans('invalid_username')
                );
            }
    
            $password = array_get($params, 'password', '');
            if (empty($password)) {
                throw new Exceptions\ForbiddenException(
                    $this->translator->trans('empty_password')
                );
            }
            
            $userId = $result['data']['id'];
            
        } catch (\UnexpectedValueException $e) {
            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_password_credential')
            );
        }

        return $userId;
    }

    /**
     * Generate token for oauth
     *
     * @param $key
     * @return
     */
    private function generateOauthToken($userId, $canRefresh = true)
    {
        $now = strtotime('now');
        $baseUrl = config_get('base.url');

        $result = [
            'access_token' => $this->token->generate([
                'iss' => $baseUrl,
                'sub' => 'Oauth2 access token',
                'aud' => $baseUrl,
                'iat' => $now,
                'nbf' => $now,
                'jti' => uniqid(mt_rand(), true),
                'exp' => $now + 7200,
                'user_id' => $userId
            ], config_get('token_payload.oauth_access_token'))
        ];

        if ($canRefresh) {
            $result['refresh_token'] = $this->token->generate([
                'iss' => $baseUrl,
                'sub' => 'Oauth2 refresh token',
                'aud' => $baseUrl,
                'iat' => $now,
                'nbf' => $now + 3600,
                'jti' => uniqid(mt_rand(), true),
                'user_id' => $userId
            ], config_get('token_payload.oauth_refresh_token'));
        }

        return $this->responseMeta([
            'data' => $result
        ], 201, $this->translator->trans('oauth_token_created'));
    }

    /**
     * Validate access token and return response if it is valid
     *
     * @throws Exceptions\ForbiddenException
     * @param string $accessToken
     * @return array
     */
    public function validateToken($accessToken, $clientId)
    {
        $client = $this->defineJWTClient($clientId);

        try {
            $decodedToken = $this->token->decode(
                $accessToken, 
                config_get('token_payload.oauth_access_token')
            );
        } catch (\Exception $e) {
            if ($e instanceOf \Firebase\JWT\ExpiredException) {
                throw new Exceptions\ForbiddenException(
                    $this->translator->trans('expired_access_token')
                );
            }
            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_access_token')
            );
        }

        return $this->responseMeta(
            ['user_id' => array_get($decodedToken, 'user_id', '')],
            200,
            $this->translator->trans('access_token_valid')
        );
    }

    /**
     * Define client id and set jwt key
     *
     * @param $clientId
     * @return Storykota\AuthServer\Models\Client
     */
    private function defineJWTClient($clientId)
    {
        $client = $this->client->getById($clientId);
        if (empty($client)) {
            throw new Exceptions\ForbiddenException(
                $this->translator->trans('invalid_client_id')
            );
        }

        $this->token->setKey($client->jwt_key);
        return $client;
    }
}