<?php
namespace App\Semlohe\Repositories\Admin;

use App\Semlohe\Exceptions;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\Generator\UrlGenerator;

class AbstractHierarchyRepository extends AbstractCrudRepository
{
    /**
     * Request collection meta to database
     *
     * @param string $parentId
     * @param array $actor
     * @return array
     */
    protected function requestHierarchyCollection(
        $parentId,
        array $actor = []
    ) {
        $response = $this->datasource->getCategories(
            $parentId,
            $actor
        );

        return $this->transformHierarchyCollection($response);
    }

    /**
     * Tranform hierarchy collection and request it's chidlren
     *
     * @param $source
     * @param array $actor
     * @return array
     */
    private function transformHierarchyCollection($source, $actor = [])
    {
        if ($source->count() > 0) {
            $result = $this->fractal->collection($source, $this->transformer)['data']; 

            foreach ($result as $key => $value) {
                $children = $this->requestHierarchyCollection($value['id'], $actor);

                if ($children !== null) {
                    $result[$key]['children'] = $children;
                }
            }

            return $result;
        }
    }

    /**
     * Request collection meta to database
     *
     * @param string $parentId
     * @param array $actor
     * @return array
     */
    protected function requestListCollection(
        $parentId,
        array $actor = [],
        $result = [],
        $level = 0
    ) {
        $response = $this->datasource->getCategories(
            $parentId,
            $actor
        );

        return $this->transformListCollection($response, $actor, $result, $level);
    }

    /**
     * Tranform hierarchy collection and request it's chidlren
     *
     * @param $source
     * @param array $actor
     * @return array
     */
    private function transformListCollection($source, $actor = [], $result = [], $level = 0)
    {
        $temp = [];
        if ($source->count() > 0) {
            $this->transformer->setLevel($level);
            $result = $this->fractal->collection($source, $this->transformer)['data']; 
            
            foreach ($result as $key => $value) {
                $temp[] = $value;
                $children = $this->requestListCollection($value['id'], $actor, $result, $level + 1);
                
                if ($children !== null) {
                    $temp = array_merge($temp, $children);
                }
            }
        }

        return $temp;
    }

    /**
     * Get max level from all response
     *
     * @param array $responses
     * @return void
     */
    protected function getMaxLevel($responses)
    {
        $maxLevel = 0;

        foreach ($responses as $response) {
            $level = array_get($response, 'level', 0);
            if ($level > $maxLevel) {
                $maxLevel = $level; 
            }
        }

        return $maxLevel;
    }
}