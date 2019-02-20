<?php
namespace App\Semlohe\Views\Detail;

use Symfony\Component\HttpFoundation\Response;
use App\Semlohe\Views;

class General extends Views\ViewAbstract implements Views\ViewInterface
{
    /** @var Twig Environment $twig */
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    /**
     * Generate detail view
     *
     * @param $data
     * @return string
     */
    public function render($data, $template)
    {
        $data = array_get($data, 'data', []);
        
        return $this->twig->render(
            $template,
            array_merge($data, [
                'view' => $view
            ])
        );
    }
}
