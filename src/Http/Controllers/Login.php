<?php
namespace App\Semlohe\Http\Controllers;

use App\Semlohe\Repositories\Login as Repository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;

class Login extends Controller
{
    /** @var Twig Environment $twig */
    private $twig;

    /** @var Repository $repository */
    private $repository;

    /** @var Symfony\Component\Routing\Generator\UrlGenerator */
    private $urlGenerator;

    /**
     * @param Repository $repository
     * @param $twig
     * @param $urlGenerator 
     */
    public function __construct(
        Repository $repository,
        $twig,
        $urlGenerator
    ) {
        $this->repository = $repository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
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
        $target = $this->urlGenerator->generate('login.index', $authCredential);
        
        $data = [
            'errors' => $errors,
            'credential' => $credentials,
            'target' => $target
        ];

        $response = $this->twig->render(
            'admin/login/index.html',
            $data
        );

        return webResponse($response);
    }

    /**
     * Post login request
     * 
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function login(Request $request)
    {
        $authCredential = $this->getAuthCredential($request);
        $credential = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
        ];
        
        try {
            $result = $this->repository->login($credential);
        } catch(Exceptions\BadRequestException $e) {
            return $this->index(
                $request,
                $credential,
                array_get($e->getMeta(), 'errors', [])
            );
        }

        $code = array_get($result, 'code', 200);
        $message = array_get($result, 'message', '');
        
        $responseType = array_get($authCredential, 'response_type', '');
        $clientId = array_get($authCredential, 'client_id', '');
        
        if ($responseType === 'code' && $clientId !== '') {
            $url = $this->urlGenerator->generate('oauth.authorize', $authCredential);
        } else {
            $url = $this->urlGenerator->generate('dashboard.index');
        }
        
        $cookies = [
            'session' => json_encode($result)
        ];

        return redirectResponse($url, [], $cookies)->send();
    }

    /**
     * Check / validate session
     *
     * @param  Request $request
     * @throws App\Semlohe\Exceptions\ForbiddenException
     * @return boolean
     */
    public function checkAndRenew(Request $request)
    {
        $credentials = $this->getCredentials($request);
        return $this->repository->check($credentials);
    }

    /**
     * Logout request
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\RedirectResponse 
     */
    public function logout(Request $request) {
        $url = $this->urlGenerator->generate('login.index');
        $result = redirectResponse($url);
        $result->headers->clearCookie('session');
        return $result;
    }
}
