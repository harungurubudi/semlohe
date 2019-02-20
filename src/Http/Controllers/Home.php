<?php
namespace App\Semlohe\Http\Controllers;

use App\Semlohe\Exceptions;
use Symfony\Component\HttpFoundation\Request;

class Home extends Controller
{
    /** @var Twig Environment $twig */
    private $twig;

    /** @var Symfony\Component\Routing\Generator\UrlGenerator */
    private $urlGenerator;

    /**
     * @param Repository $repository
     * @param $twig
     */
    public function __construct(
        $twig,
        $urlGenerator
    ) {
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
        $data = [
            'errors' => $errors,
            'credential' => $credentials
        ];

        $response = $this->twig->render(
            'web/home/index.html',
            $data
        );

        return webResponse($response);
    }
}