<?php
namespace App\Semlohe\Http\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;
use App\Semlohe\Http\Controllers\Controller;

class Dashboard extends Controller
{
    /** @var Twig Environment $twig */
    private $twig;

    /**
     * @param $twig
     * @param Save $save
     */
    public function __construct(
        $twig
    ) {
        $this->twig = $twig;
    }

    /**
     * Retrieve article list
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $actor = $this->getActorInfo($request, 'admin_dashboard');

        $breadcrumb = [
            'Dashboard' => ''
        ];
        
        $response = $this->twig->render(
            'admin/dashboard.html',
            [
                'title' => 'Dashboard',
                'actor' => $actor,
                'breadcrumb' => $breadcrumb,
            ]
        );

        return webResponse($response);
    }
}