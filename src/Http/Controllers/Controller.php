<?php
namespace App\Semlohe\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Exceptions;

abstract class Controller
{
    /**
     * Return credential parameter from request cookies
     *
     * @param Request $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        $session = $request->cookies->get('session', '');
        return json_decode(decrypt($session), true)['data'];
    }
    
    /**
     * Get actor information from request
     * 
     * @param Request $request
     * @param string $neededRole
     * @return array
     */
    protected function getActorInfo(Request $request, $neededRole = '')
    {
        $roles = json_decode($request->headers->get('actor_roles', []), true);
        
        if ($neededRole !== '' && !in_array($neededRole, $roles)) {
            throw new Exceptions\ForbiddenException('You don\'t have permission to access this request');
        }

        return [
            'id' => $request->headers->get('actor_id', ''),
            'fullname' => $request->headers->get('actor_fullname', ''),
            'username' => $request->headers->get('actor_username', ''),
            'tier' => $request->headers->get('actor_tier', ''),
            'roles' => $roles,
            'url' => $request->getMethod() . ' ' . $request->getUri(),
            'user_agent' => $request->headers->get('User-Agent'),
            'ip' => $request->getClientIp()
        ];
    }

    /**
     * Generate index sort link params
     *
     * @param string $routeName
     * @param string $name
     * @param array $filters
     * @param array $sorting
     * @param int $page
     * @return array
     */
    protected function getSortLink($routeName, $name, $filters, $sorting, $page)
    {
        $order = array_get($sorting, 'sort_by', '') === $name && array_get($sorting, 'order', '') === 'ASC' ?
            'DESC' : 'ASC';
        
        $params = array_merge([
                'sort_by' => $name,
                'order' => $order
            ], 
            ['page' => $page],
            $filters
        );

        return $this->url->generate($routeName, $params);
    }

    /**
     * Get oauth2 authorization params
     *
     * @param Request $request
     * @return array
     */
    protected function getAuthCredential(Request $request)
    {
        $clientId = $request->query->get('client_id', '');
        $scope = $request->query->get('scope', '');
        $responseType = $request->query->get('response_type', '');
        $redirectUrl = $request->query->get('redirect_url', '');

        $result = [];
        if ($clientId !== '') $result['client_id'] = $clientId;
        if ($scope !== '') $result['scope'] = $scope;
        if ($responseType !== '') $result['response_type'] = $responseType;
        if ($redirectUrl !== '') $result['redirect_url'] = $redirectUrl;
        return $result;
    }
}
