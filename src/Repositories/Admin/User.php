<?php
namespace App\Semlohe\Repositories\Admin;

use App\Semlohe\DataSources\User as DataSource;
use App\Semlohe\Transformers\User as Transformer;
use App\Semlohe\Exceptions;
use App\Semlohe\Libraries\FractalService;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\Generator\UrlGenerator;

class User extends AbstractCrudRepository
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
    protected $urlBind = 'user.index';

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
     * Check if username is available
     */
    public function checkIfUsernameIsAvailable($username, $id = '')
    {
        $result = $this->datasource->checkIsUsernameAvailable($username, $id);
        return $this->responseMeta([
            'data' => $result
        ]);
    }

    /**
     * Save update data
     *
     * @param array $data
     * @param string $id
     * @param array $actor
     * @return array
     */
    public function changePassword(array $data, $id)
    {
        $response = $this->datasource->update($data, $id);

        return $this->responseMeta(
            [], 
            200, 
            $this->translator->trans('password_changed')
        );
    }

    /**
     * Retrieve single page item by username
     *
     * @param string $username
     * @param array $actor 
     * @return mixed | array
     */
    public function getByUsername($username, $actor = [])
    {
        $response = $this->datasource
            ->getByUsername($username, $actor);
        
        if (empty($response)) {
            throw new Exceptions\NotFoundException('not_found');
        }

        return $this->responseMeta([
            'data' => $response
        ]);
    }

    /**
     * Retrieve single page item by email
     *
     * @param string $email
     * @param array $actor 
     * @return mixed | array
     */
    public function getByEmail($email, $actor = [])
    {
        $response = $this->datasource
            ->getByEmail($email, $actor);
        
        if (empty($response)) {
            throw new Exceptions\NotFoundException('not_found');
        }

        return $this->responseMeta([
            'data' => $response
        ]);
    }

    /**
     * Retrieve single page item
     *
     * @param string $id
     * @param array $actor
     * @param boolean $showSensitiveData
     * @return mixed | array
     */
    public function getById(
        $id, 
        array $actor = [], 
        $showSensitiveData = false
    ) {
        $this->transformer->setShowSensitivedata($showSensitiveData);
        return parent::getById($id, $actor);
    }

    /**
     * Retrieve page object collection
     *
     * @param array $filters - string 'type', string 'keyword'
     * @param array $sorting
     *     - string 'column' - name (default)  | title | type | sequence,
     *     - string 'order' - ASC (default) | DESC
     * @param integer $page
     * @param array $actor
     * @return mixed | array
     */
    public function getCollection(
        array $filters = [], 
        array $sorting = [], 
        $page = 1, 
        array $actor = []
    ) {
        return parent::getCollection(
            $filters, 
            $sorting, 
            $page, 
            $actor
        );
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
        
        $username = array_get($data, 'username', '');
        if (empty($username)) {
            $errors['username'][] = $this->translator->trans('empty_username');
        }

        if (!$this->datasource->checkIsUsernameAvailable($username, $id)) {
            $errors['username'][] = $this->translator->trans('username_not_available');
        }

        if (empty(array_get($data, 'user_group_id', ''))) {
            $errors['group_id'][] = $this->translator->trans('empty_group_id');
        }

        if (empty(array_get($data, 'fullname', ''))) {
            $errors['fullname'][] = $this->translator->trans('empty_name');
        }

        if (empty(array_get($data, 'email', ''))) {
            $errors['email'][] = $this->translator->trans('empty_email');
        }

        if (empty(array_get($data, 'phone', ''))) {
            $errors['phone'][] = $this->translator->trans('empty_phone');
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