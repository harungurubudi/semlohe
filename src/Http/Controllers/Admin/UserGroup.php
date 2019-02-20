<?php
namespace App\Semlohe\Http\Controllers\Admin;

use App\Semlohe\Repositories\Admin\UserGroup as Repository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Http\Controllers\Controller;
use Symfony\Component\Routing\Generator\UrlGenerator;

class UserGroup extends Controller
{
    /** @var Twig Environment $twig */
    private $twig;

    /** @var Repository $repository */
    protected $repository;

    /** @var UrlGenerator $url */
    protected $url;

    /**
     * @param Repository $repository
     * @param $twig
     */
    public function __construct(
        Repository $repository,
        $twig,
        UrlGenerator $url
    ) {
        $this->repository = $repository;
        $this->twig = $twig;
        $this->url = $url;
    }

    /**
     * Retrieve article list
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_group_read');

        $filters = $this->getFilters($request);
        $sorting = $this->getSorting($request);
        $page = $request->query->get('page', 1);
        
        $result = $this->repository->getCollection($filters, $sorting, $page, $actor);
        
        $breadcrumb = [
            'Dashboard' => $this->url->generate('dashboard.index'),
            'User Group' => ''
        ];

        $sortLink = [
            'name' => $this->getSortLink('user_group.index','name', $filters, $sorting, $page),
            'tier' => $this->getSortLink('user_group.index','tier', $filters, $sorting, $page),
        ];

        $response = $this->twig->render(
            'admin/user_group/index.html',
            array_merge($result, [
                'title' => 'User Group List', 
                'actor' => $actor,
                'breadcrumb' => $breadcrumb,
                'filters' => $filters,
                'sortlink' => $sortLink
            ])
        );

        return webResponse($response);
    }

    /**
     * Get all filters
     *
     * @param  Request $request
     * @return
     */
    protected function getFilters(Request $request)
    {
        $filters = [];

        $filters['keyword'] = $request->query->get('keyword', '');
        
        return $filters;
    }

    /**
     * Get sortings
     *
     * @param  Request $request
     * @return
     */
    protected function getSorting(Request $request)
    {
        $sort = [];
        $sortBy = $request->query->get('sort_by');
        $sort['sort_by'] = empty($sortBy) ? 'tier' : $sortBy;

        $order = $request->query->get('order');
        $sort['order'] = empty($order) ? 'ASC' : $order;

        return $sort;
    }

    /**
     * Get a single item data
     *
     * @param $id
     * @param Request $request
     * @param array $error
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request, $errors = [])
    {
        $success = $request->query->get('success', '0');

        $actor = $this->getActorInfo($request, 'user_group_write');
        $result = $this->repository->getById($id, $actor);
        $formParams = [
            'title' => 'Edit User Group',
            'success' => $success
        ];

        $response = $this->generateForm($result, $formParams, $actor, $id);

        return webResponse($response);
    }

    /**
     * Post Edit
     *
     * @param $id
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postEdit($id, Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_group_write');
        $role = json_encode($request->request->get('role', []));

        $data = [
            'name' => $request->request->get('name', ''),
            'tier' => (int) $request->request->get('tier', ''),
            'role' => $role,
        ];

        try {
            $result = $this->repository->update($data, $id, $actor);
        } catch(Exceptions\BadRequestException $e) {  
            $response = $this->generateForm(
                ['data' => $data],
                ['title' => 'Edit User Group'],  
                $actor, 
                $id, 
                array_get($e->getMeta(), 'errors', [])
            );
            return webResponse($response);
            
        }

        return redirectResponse(
            $this->url->generate('user_group.edit', [
                'id' => $id,
                'success' => '1'
            ])
        );
    }

    /**
     * Get a single item data
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_group_write');

        $data = [
            'data' => [
                'name' => '',
                'tier' => $actor['tier'],
                'role' => []
            ]
        ];

        $response = $this->generateForm(
            $data, 
            ['title' => 'Create New User Group'],
            $actor
        );
        return webResponse($response);
    }

    /**
     * Post Create
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postCreate(Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_group_write');
        $role = json_encode($request->request->get('role', []));

        $data = [
            'name' => $request->request->get('name', ''),
            'tier' => (int) $request->request->get('tier', ''),
            'role' => $role,
        ];

        try {
            $result = $this->repository->insert($data, $actor);
        } catch(Exceptions\BadRequestException $e) {  
            $response = $this->generateForm(
                ['data' => $data], 
                ['title' => 'Create New User Group'], 
                $actor, 
                null, 
                array_get($e->getMeta(), 'errors', [])
            );
            return webResponse($response);
            
        }

        $id = array_get($result, 'data.id');
        return redirectResponse(
            $this->url->generate('user_group.edit', [
                'id' => $id,
                'success' => '1'
            ])
        );
    }

    /**
     * Generate form
     * 
     * @param array $data
     * @param array $params
     * @param array $actor
     * @param array $breadcrumb
     * @param array $target
     * @param array $errors
     * @return string
     */
    private function generateForm($data, $params, $actor, $id = null, $errors = [])
    {
        if ($id !== null) {
            $meta = [
                'breadcrumb' => [
                    'Dashboard' => $this->url->generate('dashboard.index'),
                    'User Group' => $this->url->generate('user_group.index'),
                    $id => ''
                ],
                'target' => $this->url->generate('user_group.edit', [
                    'id' => $id
                ])
            ];
        } else {
            $meta = [
                'breadcrumb' => [
                    'Dashboard' => $this->url->generate('dashboard.index'),
                    'User Group' => $this->url->generate('user_group.index'),
                    'Create New' => ''
                ],
                'target' => $this->url->generate('user_group.create')
            ];
        }

        return $this->twig->render(
            'admin/user_group/form.html',
            array_merge($data, $params, $meta, [
                'actor' => $actor,
                'errors' => $errors
            ])
        );
    }

    /**
     * Delete user group item
     *
     * @param $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function delete($id, Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_group_write');
        $result = $this->repository->delete($id, $actor);

        return jsonResponse($result);
    }

    /**
     * Check if slug is available
     *
     * @param $id
     * @param  Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function toggleStatus($id, Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_group_write');
        $status = $request->query->get('value');
        $result = $this->repository->toggleStatus($id, $status, $actor);

        return jsonResponse($result);
    }
}
