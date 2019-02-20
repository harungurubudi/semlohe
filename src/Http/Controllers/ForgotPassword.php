<?php
namespace App\Semlohe\Http\Controllers;

use App\Semlohe\Repositories\ForgotPassword as Repository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;

class ForgotPassword extends Controller
{
    /** @var Twig Environment $twig */
    private $twig;

    /** @var Repository $repository */
    private $repository;

    /** @var Symfony\Component\Routing\Generator\UrlGenerator */
    private $urlGenerator;

    /** @var \Swift_Mailer $mailer*/
    private $mailer;

    /** @var Translator $translator */
    private $translator;

    /**
     * @param Repository $repository
     * @param $twig
     * @param $urlGenerator 
     */
    public function __construct(
        Repository $repository,
        $twig,
        $urlGenerator,
        \Swift_Mailer $mailer,
        Translator $translator
    ) {
        $this->repository = $repository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * Login index page
     *
     * @param Request $request
     * @param array $credentials
     * @param array $errors
     * @return string
     */
    public function index(Request $request, $credentials = [], $errors = [])
    {
        $authCredential = $this->getAuthCredential($request);
        $target = $this->urlGenerator->generate('forgot_password.index', $authCredential);
        
        $data = [
            'errors' => $errors,
            'credential' => $credentials,
            'target' => $target
        ];

        $response = $this->twig->render(
            'admin/forgot_password/index.html',
            $data
        );

        return webResponse($response);
    }

    /**
     * Request forgot password email 
     *
     * @param Request $request
     * @param array $errors
     * @return string
     */
    public function requestEmail(Request $request, $errors = [])
    {
        $credential = [
            'email' => $request->request->get('email'),
        ];

        try {
            $result = $this->repository->requestEmail($credential);
        } catch(Exceptions\BadRequestException $e) {
            return $this->index(
                $request,
                $credential,
                array_get($e->getMeta(), 'errors', [])
            );
        }

        $data = array_get($result, 'data', []);
        $this->sendResetPasswordEmail($data);
        $url = $this->urlGenerator->generate('forgot_password.info_sent');
        return redirectResponse($url)->send();
    }

    /**
     * Send reset password email confirmation
     *
     * @param string $email
     * @return void
     */
    private function sendResetPasswordEmail($data) 
    {
        $mailBody = $this->twig->render(
            'email/en_us/forgot_password/confirm.html',
            $data
        );
        
        $message = (new \Swift_Message('Reset Password Instruction'))
            ->setFrom([config_get('mail.from_email') => config_get('mail.from')])
            ->setTo([array_get($data, 'user.email', '') =>  array_get($data, 'user.name', '')])
            ->setBody($mailBody, 'text/html');

        $this->mailer->send($message);
    }

    /**
     * Email sent information
     *
     * @param Request $request
     * @return void
     */
    public function emailSent(Request $request)
    {
        $data['title'] = $this->translator->trans('forgot_password_info_title');
        $data['body'] = $this->translator->trans('forgot_password_info_body');
        $response = $this->twig->render(
            'admin/forgot_password/info.html',
            $data
        );

        return webResponse($response);
    }

    /**
     * Change password index
     *
     * @param Request $request
     * @param array $errors
     * @return void
     */
    public function changePasswordIndex(Request $request, $errors = [])
    {
        $uid = $request->query->get('uid', '');
        $token = $request->query->get('token', '');
        $user = $this->repository->getUserByToken($uid, $token);
        $data = [
            'errors' => $errors,
            'user' => $user['data']
        ];
        $response = $this->twig->render(
            'admin/forgot_password/change.html',
            $data
        );

        return webResponse($response);
    }

    /**
     * Do change password
     *
     * @param Request $request
     * @return void
     */
    public function changePasswordUpdate(Request $request)
    {
        $uid = $request->query->get('uid', '');
        $token = $request->query->get('token', '');

        $params = [
            'password' => $request->request->get('password', ''),
            'password_repeat' => $request->request->get('password_repeat', ''),
        ];

        try {
            $result = $this->repository->changePassword($uid, $token, $params);
        } catch(Exceptions\BadRequestException $e) {
            return $this->changePasswordIndex(
                $request,
                array_get($e->getMeta(), 'errors', [])
            );
        }
        $url = $this->urlGenerator->generate('forgot_password.password_changed');
        return redirectResponse($url)->send();
    }

    /**
     * Password changed information
     *
     * @param Request $request
     * @return void
     */
    public function passwordChanged(Request $request)
    {
        $data['title'] = $this->translator->trans('forgot_password_changed_title');
        $data['body'] = $this->translator->trans('forgot_password_changed_body');
        $response = $this->twig->render(
            'admin/forgot_password/info.html',
            $data
        );

        return webResponse($response);
    }
}
