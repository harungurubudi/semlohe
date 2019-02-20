<?php
namespace App\Semlohe\Repositories;

use App\Semlohe\Repositories\Admin\User;
use App\Semlohe\Libraries\TokenGeneratorService;
use Symfony\Component\Translation\Translator;
use App\Semlohe\Exceptions;

class ForgotPassword extends AbstractRepository
{
    /** @var User $user */
    private $user;

    /** @var Translator $translator */
    private $translator;

    /** @var TokenGeneratorService $token */
    private $token;

    public function __construct(
        User $user,
        TokenGeneratorService $token,
        Translator $translator
    ) {
        $this->user = $user;
        $this->token = $token;
        $this->translator = $translator;
    }

    /**
     * Request forgot password email
     * 
     * @param array $credential
     * @return array
     */
    public function requestEmail(array $credential)
    {
        $errors = [];
        $email = array_get($credential, 'email', '');
        
        if (empty($email)) {
            $errors['email'][] = 'empty_email';
        }
        
        try {
            $user = $this->user->getByEmail($email);
        } catch (Exceptions\NotFoundException $e) {
            $errors['email'][] = 'email_not_found';
        }

        if ($errors !== []) {
            $exception = new Exceptions\BadRequestException('input_error');
            $exception->setMeta([
                'errors' => $errors
            ]);
            throw $exception;
        }

        $this->token->setKey($user['data']['jwt_key']);

        $data = [
            'user' => $user['data'],
            'token' => $this->generateToken(
                array_get($user, 'data.id', '')
            ),
        ];

        return $this->responseMeta(
            ['data' => $data], 
            200,
            $this->translator->trans('email_reset_password_sent') 
        );
    }

    /**
     * Generate token 
     *
     * @param $key
     * @return
     */
    private function generateToken($userId)
    {
        $now = strtotime('now');
        $baseUrl = config_get('base.url');

        return $this->token->generate([
            'iss' => $baseUrl,
            'sub' => 'Oauth2 access token',
            'aud' => $baseUrl,
            'iat' => $now,
            'nbf' => $now,
            'jti' => uniqid(mt_rand(), true),
            'exp' => $now + 7200
        ], config_get('token_payload.change_password'));
    }

    /**
     * Get user information by token
     *
     * @param $userId
     * @param string $token
     * @return void
     */
    public function getUserByToken($userId, $token)
    {
        $user = $this->user->getById($userId, [], true);
        $this->token->setKey($user['data']['jwt_key']);

        try {
            $decoded = $this->token->decode(
                $token, 
                config_get('token_payload.change_password')
            );
        } catch (\Exception $e) {
            throw new Exceptions\BadRequestException(
                $this->translator->trans('invalid_access_token')
            );
        }

        $result = $user = $this->user->getById($userId);
        
        return $this->responseMeta(
            $result, 
            200,
            $this->translator->trans('email_reset_password_sent') 
        );
    }

    /**
     * Change password
     * 
     * @param array $credential
     * @return array
     */
    public function changePassword($userId, $token, array $credential)
    {
        $errors = [];
        $password = array_get($credential, 'password', '');
        $passwordRepeat = array_get($credential, 'password_repeat', '');
        
        if (empty($password)) {
            $errors['password'][] = 'empty_password';
        }

        if (strlen($password) < 8) {
            $errors['password'][] = 'password_too_short';
        }

        if ($password !== $passwordRepeat) {
            $errors['password_repeat'][] = 'both_password_different';
        }

        if ($errors !== []) {
            $exception = new Exceptions\BadRequestException('input_error');
            $exception->setMeta([
                'errors' => $errors
            ]);
            throw $exception;
        }

        return $this->user->changePassword([
            'password' => hash_bcrypt($password)
        ], $userId);
    }
}
