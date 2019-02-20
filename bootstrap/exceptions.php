<?php
use Symfony\Component\HttpFoundation\Request;

function getRequestType(Request $request) {
    $basePath = $request->getPathInfo();
    if (substr($basePath, 0, 4) === '/api') {
        return 'api';
    }
    return 'web';
}

/*
 *-------------------------------------------------------------------------
 * Not found routes exception handler
 *-------------------------------------------------------------------------
 */
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app->error(function (NotFoundHttpException $e, Request $request, $code) use ($app) {
    $message = $app['translator']->trans('not_found');
    return exceptionResponse($e, 404, $message);
});

/*
 *-------------------------------------------------------------------------
 * App exception handler
 *-------------------------------------------------------------------------
 */
use App\Semlohe\Exceptions\UnauthorizedException;

$app->error(function (UnauthorizedException $e, Request $request, $code) use ($app) {
    if (getRequestType($request) === 'api') {
        return exceptionResponse($e, $code, 'Unauthorized');
    }
    return redirectResponse($app['url_generator']->generate('login.index'));
});

/*
 *-------------------------------------------------------------------------
 * App exception handler
 *-------------------------------------------------------------------------
 */
use App\Semlohe\Exceptions\HttpException;

$app->error(function (HttpException $e, Request $request, $code) use ($app) {
    if (getRequestType($request) === 'api') {
        return exceptionResponse($e, $code, $e->getMessage());
    }
    return exceptionResponse($e, $code, $e->getMessage());
});

/*
 *-------------------------------------------------------------------------
 * Eloquent query exception
 *-------------------------------------------------------------------------
 */
use Illuminate\Database\QueryException;

$app->error(function (QueryException $e, Request $request, $code) use ($app) {
    $message = $app['translator']->trans('internal_server_error');
    return exceptionResponse($e, 500, $message);
});

/*
 *-------------------------------------------------------------------------
 * Guzzle connect exception
 *-------------------------------------------------------------------------
 */
use GuzzleHttp\Exception\ConnectException;

$app->error(function (ConnectException $e, Request $request, $code) use ($app) {
    $message = $app['translator']->trans('internal_server_error');
    return exceptionResponse($e, 500, $message);
});

