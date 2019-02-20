<?php
namespace App\Semlohe\Http\Controllers\Admin;

use App\Semlohe\Repositories\Admin\User as Repository;
use App\Semlohe\Repositories\Admin\UserGroup as UserGroupRepository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Http\Controllers\Controller;
use Symfony\Component\Routing\Generator\UrlGenerator;

class User extends Controller
{
    /** @var Twig Environment $twig */
    private $twig;

    /** @var Repository $repository */
    protected $repository;

    /** @var UserGroupRepository $userGroup */
    protected $userGroup;
    
    /** @var UrlGenerator $url */
    protected $url;

    /**
     * @param Repository $repository
     * @param $twig
     */
    public function __construct(
        Repository $repository,
        UserGroupRepository $userGroup,
        $twig,
        UrlGenerator $url
    ) {
        $this->repository = $repository;
        $this->userGroup = $userGroup;
        $this->twig = $twig;
        $this->url = $url;
    }

    /**
     * Retrieve article list
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $actor = $this->getActorInfo($request, 'user_read');

        $filters = $this->getFilters($request);
        $sorting = $this->getSorting($request);
        $page = $request->query->get('page', 1);

        $result = $this->repository->getCollection($filters, $sorting, $page, $actor);

        $breadcrumb = [
            'Dashboard' => $this->url->generate('dashboard.index'),
            'User' => ''
        ];

        $sortLink = [
            'fullname' => $this->getSortLink('user.index', 'fullname', $filters, $sorting, $page),
            'username' => $this->getSortLink('user.index', 'username', $filters, $sorting, $page),
            'user_group_id' => $this->getSortLink('user.index', 'user_group_id', $filters, $sorting, $page)

        ];

        $response = $this->twig->render(
            'admin/user/index.html',
            array_merge($result, [
                'title' => 'User List',
                'actor' => $actor,
                'breadcrumb' => $breadcrumb,
                'filters' => $filters,
                'sortlink' => $sortLink,
                'user_group' => $this->userGroup->getCollection([], [], 0) 
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
        $filters['user_group_id'] = $request->query->get('user_group_id', '');
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
        $sort['sort_by'] = empty($sortBy) ? 'username' : $sortBy;

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

        $actor = $this->getActorInfo($request, 'user_write');
        $result = $this->repository->getById($id, $actor);

        $formParams = [
            'title' => 'Edit user',
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
        $actor = $this->getActorInfo($request, 'user_write');
        
        $data = [
            'user_group_id' => $request->request->get('user_group_id', ''),
            'fullname' => $request->request->get('fullname', ''),
            'username' => $request->request->get('username', ''),
            'email' => $request->request->get('email', ''),
            'phone' => $request->request->get('phone', ''),
        ];

        try {
            $result = $this->repository->update($data, $id, $actor);
        } catch(Exceptions\BadRequestException $e) {  
            $response = $this->generateForm(
                ['title' => 'Edit User'], 
                ['data' => $data],
                $actor, 
                $id, 
                array_get($e->getMeta(), 'errors', [])
            );
            return webResponse($response);
        }

        return redirectResponse(
            $this->url->generate('user.edit', [
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
        $actor = $this->getActorInfo($request, 'user_write');

        $data = [
            'data' => [
                'user_group_id' => '',
                'fullname' => '',
                'username' => '',
                'email' => '',
                'phone' => '',
            ]
        ];

        $response = $this->generateForm(
            $data, 
            ['title' => 'Create New User'], 
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
        $actor = $this->getActorInfo($request, 'user_write');
        $data = [
            'user_group_id' => $request->request->get('user_group_id', ''),
            'fullname' => $request->request->get('fullname', ''),
            'username' => $request->request->get('username', ''),
            'email' => $request->request->get('email', ''),
            'phone' => $request->request->get('phone', ''),
        ];

        try {
            $result = $this->repository->insert($data, $actor);
        } catch(Exceptions\BadRequestException $e) {  
            $response = $this->generateForm(
                ['data' => $data], 
                ['title' => 'Create New User'], 
                $actor, 
                null, 
                array_get($e->getMeta(), 'errors', [])
            );
            return webResponse($response);
            
        }

        $id = array_get($result, 'data.id');
        return redirectResponse(
            $this->url->generate('user.edit', [
                'id' => $id,
                'success' => '1'
            ])
        );
    }

    /**
     * Generate form
     * 
     * @param array $data
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
                    'User' => $this->url->generate('user.index'),
                    $id => ''
                ],
                'target' => $this->url->generate('user.edit', [
                    'id' => $id
                ])
            ];
        } else {
            $meta = [
                'breadcrumb' => [
                    'Dashboard' => $this->url->generate('dashboard.index'),
                    'User' => $this->url->generate('user.index'),
                    'Create New' => ''
                ],
                'target' => $this->url->generate('user.create')
            ];
        }

        return $this->twig->render(
            'admin/user/form.html',
            array_merge($data, $params, $meta, [
                'actor' => $actor,
                'errors' => $errors,
                'user_group' => $this->userGroup->getCollection([], [], 'all') 
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
        $actor = $this->getActorInfo($request, 'user_write');
        $result = $this->repository->delete($id, $actor);

        return jsonResponse($result);
    }

    /**
     * Check if slug is available
     *
     * @param  Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function checkIsSlugAvailable(Request $request)
    {
        $slug = $request->query->get('slug');
        $lang = $request->query->get('lang');
        $id = $request->query->get('id');

        $result = $this->save
            ->returnIsSlugAvailable($slug, $lang, $id);

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
        $actor = $this->getActorInfo($request, 'user_write');
        $status = $request->query->get('value');
        $result = $this->repository->toggleStatus($id, $status, $actor);

        return jsonResponse($result);
    }
}
