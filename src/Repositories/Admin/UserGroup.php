<?php
namespace App\Semlohe\Repositories\Admin;

use App\Semlohe\DataSources\UserGroup as DataSource;
use App\Semlohe\Transformers\UserGroup as Transformer;
use App\Semlohe\Exceptions;
use App\Semlohe\Libraries\FractalService;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\Generator\UrlGenerator;

class UserGroup extends AbstractCrudRepository
{
     /** @var UrlGenerator $url */
     protected $url;

     /** @var FractalService */
     protected $fractal;
 
     /** @var Transformer */
     protected $transformer;
     
     /** @var DataSource */
     protected $datasource;
 
     /** @var Translator $translator */
     protected $translator;

    /** @var string $urlBind */
    protected $urlBind = 'user_group.index';

    public function __construct(
        DataSource $datasource,
        FractalService $fractal,
        Translator $translator,
        UrlGenerator $url
    ) {
        $this->datasource = $datasource;
        $this->fractal = $fractal;
        $this->translator = $translator;
        $this->url = $url;
        $this->transformer = new Transformer();
    }

    /**
     * Validate all input
     *
     * @param array $data
     * @param $id
     * @return array
     */
    protected function validate(array $data, $id = 0)
    {
        $errors = [];
        
        $name = array_get($data, 'name', '');
        if (empty($name)) {
            $errors['name'][] = $this->translator->trans('empty_name');
        }

        $role = array_get($data, 'role', '');
        if (empty($role)) {
            $errors['role'][] = $this->translator->trans('empty_role');
        }

        $tier = array_get($data, 'tier', '');
        if (empty($tier)) {
            $errors['tier'][] = $this->translator->trans('empty_tier');
        }

        if ($errors !== []) {
            $exception = new Exceptions\BadRequestException('input_error');
            $exception->setMeta([
                'errors' => $errors
            ]);
            throw $exception;
        }
    }
}