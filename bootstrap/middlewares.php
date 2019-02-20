<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Exceptions;

/*
 *-------------------------------------------------------------------------
 * Authentication Middleware
 *-------------------------------------------------------------------------
 */
$hasNotLogin = function (Request $request, Application $app) {
    try {
        $auth = $app['controller.login']->checkAndRenew($request);
        return redirectResponse(
            $app['url_generator']->generate(
                'home.index',
                $request->query->all()
            )
        );
    } catch (\Exception $e) {
        if (
            $e instanceof Exceptions\UnauthorizedException or 
            $e instanceof Exceptions\ForbiddenException
        ) {
            $request->cookies->remove('session');
            return null;
        }
    }
};

$hasLogin = function (Request $request, Application $app) {
    $session = $request->cookies->get('session');
    
    $doAction = function ($request, $exception) use ($app) {
        if ($request->isXmlHttpRequest()) {
            throw $exception;  
        } 

        return redirectResponse(
            $app['url_generator']->generate(
                'login.index',
                $request->query->all()
            )
        );
    };

    if (empty($session)) {
        return $doAction($request, new Exceptions\UnauthorizedException(
            $app['translator']->trans('unauthorized')
        ));
    }

    try {
        $auth = $app['controller.login']->checkAndRenew($request);
    } catch (Exceptions\ForbiddenException $e)  {
        return $doAction($request, new Exceptions\ForbiddenException(
            $app['translator']->trans('forbidden')
        ));
    }

    $session = json_encode(array_get($auth, 'session', ''));
    $request->cookies->set('session', encrypt($session));
    
    // Appending actor info to headers
    $request->headers->set('type', 'web');
    $request->headers->set('actor_id', array_get($auth, 'actor_id', ''));
    $request->headers->set('actor_fullname', array_get($auth, 'actor_fullname', ''));
    $request->headers->set('actor_username', array_get($auth, 'actor_username', ''));
    $request->headers->set('actor_tier', array_get($auth, 'actor_tier', ''));
    $request->headers->set('actor_roles', json_encode(array_get($auth, 'actor_roles', [])));
};

$hasAccessToken = function  (Request $request, Application $app) {
    $authorization = $request->headers->get('Authorization');
    $accessToken = array_get(explode(' ', $authorization), 1, '');
    
    $clientId = $request->query->get('client_id');

    $actor = $app['repository.oauth']->validateToken($accessToken, $clientId);
    
    $userId = array_get($actor, 'user_id', '');
    $auth = $app['repository.login']->getActorData($userId);
    
    $request->headers->set('type', 'api');
    $request->headers->set('actor_id', array_get($auth, 'actor_id', ''));
    $request->headers->set('actor_fullname', array_get($auth, 'actor_fullname', ''));
    $request->headers->set('actor_username', array_get($auth, 'actor_username', ''));
    $request->headers->set('actor_tier', array_get($auth, 'actor_tier', ''));
    $request->headers->set('actor_roles', json_encode(array_get($auth, 'actor_roles', [])));
};