<?php
namespace App\Semlohe\DataSources;

use App\Semlohe\Models\Client as Model;

class Client
{
    /** @var Model */
    protected $model;

    public function __construct(Model $model) 
    {
        $this->model = $model;
    }

    /**
     * Get single user by it's id
     *
     * @param string $id
     * @param Array $actor
     * @return Mixed
     */
    public function getById($id, array $actor = [])
    {
        $tier = array_get($actor, 'tier', 0);
        
        $this->model->lowerOrEqualTier($tier);
        return parent::getById($id, $actor);
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
        $tier = array_get($actor, 'tier', 0);

        $this->model = $this->filterByGroupId($filters);
        $this->model = $this->filterByKeyword($filters);
        $this->model = $this->model->lowerOrEqualTier($tier);        
        return parent::getCollection($filters, $sorting, $page, $actor);
    }   

    /**
     * Check is id is available
     *
     * @param  String $id
     * @param  integer $id
     * @return Mixed / Array
     */
    public function checkIsIdAvailable($id)
    {
        $condition = ['id' => $id];
        $response = $this->model->where($condition);
        
        return $response->count() > 0 ? false : true;
    }

    /**
     * Bind database column on it's input column
     *
     * @param $column
     * @return
     */
    protected function bindFilterColumn($column)
    {
        $columns = [
            'fullname' => 'fullname',
            'username' => 'username',
            'user_group_id' => 'user_group_id',
            'tier' => 'tier'
        ];

        return array_get($columns, $column, 'username');
    }

    /**
     * Filter model by group_id
     *
     * @param $filters
     * @return
     */
    protected function filterByGroupId($filters)
    {
        $groupId = array_get($filters, 'user_group_id', '');

        if (!empty($groupId)) {
            return $this->model->where('user_group_id', $groupId);
        }

        return $this->model;
    }

    /**
     * Filter model by keyword
     *
     * @param $filters
     * @return
     */
    protected function filterByKeyword($filters)
    {
        $keyword = array_get($filters, 'keyword', 0);

        if (!empty($keyword)) {
            return $this->model
                ->where(function ($query) use ($keyword) {
                    $query
                        ->where('fullname', 'like', '%' . $keyword . '%')
                        ->orWhere('username', 'like', '%' . $keyword . '%');
                });
        }

        return $this->model;
    }
}
