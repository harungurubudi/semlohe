<?php
namespace App\Semlohe\DataSources;

use App\Semlohe\Models\User as Model;

class User extends AbstractDataSource implements DataSourceInterface
{
    /** @var Model */
    protected $model;

    public function __construct(Model $model) 
    {
        $this->model = $model;
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
        $tier = array_get($actor, 'tier', 0);
        $this->model->lowerOrEqualTier($tier);
        return parent::update($data, $id, $actor);
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
        $tier = array_get($actor, 'tier', 0);
        $this->model->lowerOrEqualTier($tier);
        return parent::delete($id, $actor);
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
        $tier = array_get($actor, 'tier', 0);
        $this->model->lowerOrEqualTier($tier);

        return parent::toggleStatus($id, $status, $actor);
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
     * Check is username is available
     *
     * @param  String $username
     * @param  integer $id
     * @return Mixed / Array
     */
    public function checkIsUsernameAvailable($username, $id = 0)
    {
        $condition = ['username' => $username];

        $response = $this->model->where($condition);

        if (!empty($id)) {
            $response->where('id', '<>', $id);
        }
        
        return $response->count() > 0 ? false : true;
    }

    /**
     * Get single user by it's username
     *
     * @param string $username
     * @param Array $actor 
     * @return Mixed
     */
    public function getByUsername($username, array $actor = [])
    {
        $tier = array_get($actor, 'tier', 0);
        $condition = ['username' => $username];
        return $this->model
            ->where($condition)
            ->lowerOrEqualTier($tier)
            ->first();
    }

    /**
     * Get single user by it's email
     *
     * @param string $email
     * @param Array $actor 
     * @return Mixed
     */
    public function getByEmail($email, array $actor = [])
    {
        $tier = array_get($actor, 'tier', 0);
        $condition = ['email' => $email];
        return $this->model
            ->where($condition)
            ->lowerOrEqualTier($tier)
            ->first();
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
