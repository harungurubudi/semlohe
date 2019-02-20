<?php
namespace App\Semlohe\Http\Controllers\Admin;

use App\Semlohe\Repositories\Admin\Upload as Repository;
use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Http\Controllers\Controller;

class Upload extends Controller
{
    /** @var Repository $repository */
    protected $repository;

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
     * Do post upload
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function postImageUpload(Request $request)
    {
        $file = $request->files->get('file');
        
        $result = $this->repository->move(
            'content', 
            $file
        );

        return jsonResponse($result);
    }
}