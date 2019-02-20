<?php
namespace App\Semlohe\Http\Controllers\API;

use App\Semlohe\Repositories\Admin\User as Repository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Http\Controllers\Controller;

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
        Repository $repository
    ) {
        $this->repository = $repository;
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

        return jsonResponse($result);
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
    public function detail($id, Request $request, $errors = [])
    {
        $actor = $this->getActorInfo($request, 'user_read');
        $result = $this->repository->getById($id, $actor);

        return jsonResponse($result);
    }

    /**
     * Get a actor item data
     *
     * @param Request $request
     * @param array $error
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function self(Request $request, $errors = [])
    {
        $actor = $this->getActorInfo($request);
        $id = array_get($actor, 'id', '');  
        $result = $this->repository->getById($id, $actor);

        return jsonResponse($result);
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
