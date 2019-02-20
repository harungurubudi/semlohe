<?php
namespace App\Semlohe\DataSources;

use App\Semlohe\DataSources;
use App\Semlohe\Models\UserGroup as Model;

class UserGroup extends AbstractDataSource implements DataSourceInterface
{
    /** @var Model */
    protected $model;

    public function __construct(Model $model) 
    {
        $this->model = $model;
    }

    /**
     * Retrieve user collection
     *
     * @param integer $tier
     * @param Array $filters
     * @param Array $sorting
     * @param integer $page
     * @return Mixed / Array
     */
    public function getCollection(array $filters = [], array $sorting = [], $page = 1, array $actor = [])
    {
        $page = (int) $page;
        $tier = array_get($actor, 'tier', 0);
        $this->model = $this->filterByKeyword($filters);
        $this->model->lowerOrEqualTier($tier);
        
        return parent::getCollection($filters, $sorting, $page, $actor);
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
            'name' => 'name',
            'tier' => 'tier'
        ];

        return array_get($columns, $column, 'tier');
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
                        ->where('name', 'like', '%' . $keyword . '%');
                });
        }

        return $this->model;
    }
}
