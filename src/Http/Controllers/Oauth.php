<?php
namespace App\Semlohe\Http\Controllers;

use App\Semlohe\Repositories\Oauth as Repository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;

class Oauth extends Controller
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
     * Authorization page
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function authorize(Request $request)
    {
        $actor = $this->getActorInfo($request);
        $result = $this->repository->authorize(
            $this->getAuthCredential($request),
            $actor
        );

        $data = array_get($result, 'data', '');
        $redirectUrl = array_get($result, 'meta.redirect_uri', '') . '?' . http_build_query($data);
        return redirectResponse($redirectUrl);
    }

    /**
     * Get token controller
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getToken(Request $request)
    {
        $grantType = $request->query->get('grant_type');
        $clientId = $request->query->get('client_id');
        $clientSecret = $request->query->get('client_secret');
        $params = $request->request->all();

        $result = $this->repository->getToken(
            $grantType,
            $clientId,
            $clientSecret,
            $params
        );

        return jsonResponse($result);
    }

    /**
     * Refresh token controller
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function refreshToken(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $accessToken = $data['access_token'];
        $refreshToken = $data['refresh_token'];
        $clientId = $data['client_id'];
        
        $result = $this->repository->refreshToken(
            $accessToken,
            $refreshToken,
            $clientId
        );

        return jsonResponse($result);
    }
}