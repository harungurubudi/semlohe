<?php
namespace App\Semlohe\DataSources;

use App\Semlohe\Library\Paginator;

abstract class AbstractDataSource
{
    /** @var $order */
    private $order = [];

    /** @var $perPage */
    private $perPage = 15;

    /** @var $urlPattern */
    private $urlPattern = '';

    /**
     * Do data insertion
     * 
     * @param array $data
     * @param array $actor
     * @return array
     */
    public function insert(array $data, array $actor = [])
    {
        $now = time();
        $id = generateId();
        $username = array_get($actor, 'username', '');

        $data = array_merge([
            'id' => $id,
            'created_at' => date('Y-m-d H:i:s', $now),
            'updated_at' => date('Y-m-d H:i:s', $now),
            'created_by' => $username,
            'updated_by' => $username,
        ], $data);
        $this->model->insert($data);
        return $this->getById($id, $actor);
    }

    /**
     * Update user by it's id
     * 
     * @param array $data
     * @param string $id 
     * @param array $actor
     * @return Mixed
     */
    public function update(array $data, $id, array $actor = [])
    {
        $now = time();
        $username = array_get($actor, 'username', '');

        $data = array_merge([
            'updated_by' => $username,
            'updated_at' => date('Y-m-d H:i:s', $now),
        ], $data);
        
        $this->model
            ->notDeleted()
            ->where('id', '=', $id)
            ->update($data);
        
        return $this->getById($id, $actor);
    }

    /**
     * Delete single user by it's id 
     * 
     * @param string $id 
     * @param array $actor
     * @return boolean
     */
    public function delete($id, array $actor = [])
    {
        $now = time();
        $username = array_get($actor, 'username', '');
        
        $data = [
            'deleted_by' => $username,
            'deleted_at' => date('Y-m-d H:i:s', $now),
            'deleted' => '1'
        ];

        return $this->model
            ->where('id', '=', $id)
            ->update($data);
    }

    /**
     * Toggle status
     *
     * @param  integer $id
     * @param  integer $status '1' | '0''
     * @param array $actor
     * @return boolean
     */
    public function toggleStatus($id, $status, array $actor = [])
    {
        $condition = ['id' => $id];

        $this->model
            ->notDeleted()
            ->where($condition)
            ->update(['status' => $status]);

        return $this->getById($id, $actor);
    }

    /**
     * Get single user by it's id
     *
     * @param string $id
     * @param integer $actor
     * @return Mixed
     */
    public function getById($id, array $actor = [])
    {
        $condition = ['id' => $id];
        return $this->model
            ->notDeleted()
            ->where($condition)
            ->first();
    }

    /**
     * Retrieve user collection
     *
     * @param Array $filters
     * @param Array $sorting
     * @param integer $page
     * @param integer $actor
     * @return Mixed / Array
     */
    public function getCollection(array $filters = [], array $sorting = [], $page = 1, array $actor = [])
    {
        $sortOrder = array_get($sorting, 'order', 'ASC');
        $sortColumn = $this->bindFilterColumn(
            array_get($sorting, 'sort_by', '')
        );

        $result = $this->model
            ->notDeleted()
            ->orderBy($sortColumn, $sortOrder);

        if ($page === 'all') {
            return $result->get();
        }

        return $result->paginate($this->getPerPage(), ['*'], 'user', $page);
    }   

    /**
     * Set result order
     *
     * @param $order
     * @param $sort
     */
    public function setOrder($column, $sort = 'asc')
    {
        $this->order = [
            'column' => $column,
            'sort'   => $sort
        ];

        return $this;
    }

    /**
     * Return result order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set result order
     *
     * @param $order
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Return result perPage
     * @return string]
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set result urlPattern
     *
     * @param $url
     * @param $sort
     */
    public function setUrlPattern($url)
    {
        $this->urlPattern = $url;

        return $this;
    }

    /**
     * Return result urlPattern
     *
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * Create transaction
     *
     * @param  function $callback
     * @return mixed
     */
    protected function transaction($callback)
    {
        return app('db')->transaction($callback);
    }
}
