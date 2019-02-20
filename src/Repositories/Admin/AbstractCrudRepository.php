<?php
namespace App\Semlohe\Repositories\Admin;

use App\Semlohe\Repositories\AbstractRepository;
use App\Semlohe\DataSources\DataSourceInterface as DataSource;
use App\Semlohe\Exceptions;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\Generator\UrlGenerator;

class AbstractCrudRepository extends AbstractRepository
{
    /**
     * Save insert data
     *
     * @param array $data
     * @param array $actor
     * @return array
     */
    public function insert(array $data, array $actor = [])
    {
        $this->validate($data);
        $response = $this->datasource->insert($data, $actor);
        
        return $this->responseMeta(
            ['data' => $response], 
            201, 
            $this->translator->trans('created')
        );
    }

    /**
     * Save update data
     *
     * @param array $data
     * @param string $id
     * @param array $actor
     * @return array
     */
    public function update(array $data, $id, array $actor = [])
    {
        $this->validate($data, $id);
        $response = $this->datasource->update($data, $id, $actor);

        return $this->responseMeta(
            ['data' => $response], 
            200, 
            $this->translator->trans('updated')
        );
    }

    /**
     * Retrieve single page item
     *
     * @param string $id
     * @param array $actor
     * @return mixed | array
     */
    public function getById($id, array $actor = [])
    {
        $response = $this->datasource
            ->getById($id, $actor);
        
        if (empty($response)) {
            throw new Exceptions\NotFoundException();
        }

        return $this->transformItem($response);
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
        $response = $this->datasource->getCollection(
            $filters, 
            $sorting, 
            $page, 
            $actor
        );

        return $this->transformCollection($response, $page, $filters, $sorting);
    }

    /**
     * Delete page
     *
     * @param string $id
     * @param array $actor
     * @return mixed / array
     */
    public function delete($id, array $actor = [])
    {
        $this->datasource->delete($id, $actor);

        return $this->responseMeta(
            [], 
            200, 
            $this->translator->trans('deleted')
        );
    }

    /**
     * Toggle page status
     *
     * @param integer $id
     * @param integer $status '1' | '0'
     * @param array $actor 
     * @return mixed / array
     */
    public function toggleStatus($id, $status, array $actor = [])
    {
        $this->datasource->toggleStatus($id, $status, $actor);

        return $this->responseMeta(
            [], 
            200, 
            $this->translator->trans('status_updated')
        );
    }
}