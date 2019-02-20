<?php
namespace App\Semlohe\Repositories;

use App\Semlohe\Repositories\Admin\User;
use App\Semlohe\Repositories\Admin\UserGroup;
use App\Semlohe\Libraries\TokenGeneratorService;
use App\Semlohe\Exceptions;

class Login extends AbstractRepository
{
    /** @var User $user */
    private $user;

    /** @var UserGroup $userGroup */
    private $userGroup;

    /** @var TokenGeneratorService $token */
    private $token;

    public function __construct(
        User $user,
        UserGroup $userGroup,
        TokenGeneratorService $token
    ) {
        $this->user = $user;
        $this->userGroup = $userGroup;
        $this->token = $token; 
    }

    /**
     * Do Login
     * 
     * @param array $credential
     * @return array
     */
    public function login(array $credential)
    {
        $user = $this->validateLogin($credential);
        $this->token->setKey($user->jwt_key);
        return $this->responseMeta([
            'data' => $this->createSession($user->id)
        ]);
    }

    /**
     * Validate all input
     *
     * @param array $data
     * @param $id
     * @return array
     */
    protected function validateLogin(array $credential, $id = 0)
    {
        $errors = [];
        
        $username = array_get($credential, 'username', '');
        $password = array_get($credential, 'password', '');
        
        if (empty($username)) {
            $errors['username'][] = 'empty_username';
        }

        try {
            $result = $this->user->getByUsername(array_get($credential, 'username'), []);
            $this->token->setKey($result['data']['jwt_key']); 

            if (!hash_bcrypt_verify($password, $result['data']['password'])) {
                $errors['password'][] = 'invalid_password';
            }
            
        } catch (Exceptions\NotFoundException $e) {
            $errors['username'][] = 'invalid_username';
        }

        $password = array_get($credential, 'password', '');
        if (empty($password)) {
            $errors['password'][] = 'empty_password';
        }

        if ($errors !== []) {
            $exception = new Exceptions\BadRequestException('input_error');
            $exception->setMeta([
                'errors' => $errors
            ]);
            throw $exception;
        }
        return $result['data'];
    }

    /**
     * Validate token
     *
     * @param array $credential
     * @throws App\Semlohe\Exceptions\ForbiddenException
     * @throws App\Semlohe\Exceptions\NotFoundException
     * @return array|boolean
     */
    public function check($credential)
    {
        $userInfo = array_get($credential, 'user', '');
        $actor = $this->getActorData($userInfo['id']);
        $this->token->setKey($actor['jwt_key']);    

        $accessToken = array_get($credential, 'access_token', '');
        $refreshToken = array_get($credential, 'refresh_token', '');
        
        try {
            $result = $this->token->decode(
                $accessToken,
                config_get('token_payload.login_access_token')
            );    
        } catch (\Exception $e) {
            if ($e instanceof \Firebase\JWT\ExpiredException) {
                try {
                    $result = $this->token->decode(
                        $refreshToken,
                        config_get('token_payload.login_refresh_token')
                    );
                    $credential = $this->createSession($userInfo['id']);
                } catch (\Exception $e) {
                    $this->throwUnauthorizedException('expired_token');
                }
            } else {
                $this->throwUnauthorizedException('invalid_token');
            }
        }

        return array_merge($actor, [
            'session' => $credential
        ]);
    }

    /**
     * Get actor data by it's user id
     *
     * @param string $userId
     * @return array
     */
    public function getActorData($userId)
    {
        try {
            $user = $this->user->getById($userId, [], true)['data'];
            $userGroup = $this->userGroup->getById($user['user_group_id'])['data'];
        } catch (Exceptions\NotFoundException $e) {
            $this->throwUnauthorizedException('invalid_token');
        }
        
        if (!$user) {
            $this->throwUnauthorizedException('invalid_token');
        }

        return [
            'actor_id' => $user['id'],
            'actor_fullname' => $user['fullname'],
            'actor_username' => $user['username'],
            'jwt_key' => $user['jwt_key'],
            'actor_tier' => $userGroup['tier'],
            'actor_roles' => json_decode($userGroup['role'], true),
        ];
    }

    /**
     * Generate jwt
     *
     * @param array $userId
     * @return array
     */
    private function createSession($userId)
    {
        $now = strtotime('now');
        $baseUrl = config_get('base.url');

        $accessToken = $this->generateToken(
            $now,
            'Authentication access token',
            0,
            7200,
            config_get('token_payload.login_access_token')
        );

        $refreshToken = $this->generateToken(
            $now,
            'Authentication access token',
            3600,
            0,
            config_get('token_payload.login_refresh_token')
        );

        return [
            'user' => ['id' => $userId],
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
    }

    /**
     * Generate token
     *
     * @param integer $now - current epoch
     * @param string $sub - token subject
     * @param integer $nbf - not before, token will not be valid before this
     * @param integer $exp = expired, token expiraton time
     * @param string $payload - additional information of jwt
     * @return string
     */
    protected function generateToken($now, $sub, $nbf, $exp = 0, $payload = '')
    {
        $baseUrl = config_get('base.url');
        
        $expirationConfig = [];
        if ($exp > 0) {
            $expirationConfig = [
                'exp' => $now + $exp
            ];
        }

        $configurator = array_merge([
            'iss' => $baseUrl,
            'sub' => $sub,
            'aud' => $baseUrl,
            'iat' => $now,
            'nbf' => $now + $nbf,
            'jti' => uniqid(mt_rand(), true),
        ], $expirationConfig);

        return $this->token->generate($configurator, $payload);
    }

    /**
     * Throw to new unauthorized exception
     *
     * @param string $message - message code (check language resource for detail)
     * @throws Exceptions\UnauthorizedException
     * @return
     */
    private function throwUnauthorizedException($message)
    {
        throw new Exceptions\UnauthorizedException($message);
    }
}
