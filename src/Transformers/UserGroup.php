<?php
namespace App\Semlohe\Transformers;

class UserGroup extends AbstractTransformer
{
    /** @var string $type */
    private $type = 'collection';
    
    /**
     * Type attribute setter
     * 
     * @param string $type
     * @return UserGroup $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Type attribute getter
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Do tranformation
     *
     * @param $userGroup
     * @return
     */
    public function transform($userGroup)
    {
        return $this->userGroup($userGroup);
    }

    /**
     * Admin brief object definition

     * @param Admin $userGroup
     * @return Array
     */
    protected function userGroup($userGroup)
    {
        $result = [
            'id' => object_get($userGroup, 'id', ''),
            'name' => object_get($userGroup, 'name', ''),
            'tier' => object_get($userGroup, 'tier', ''),
            'status' => object_get($userGroup, 'status', '')
        ];

        if ($this->getType() === 'item') {
            $result = $this->appendFullData($result, $userGroup);
        }

        return $result;
    }

    /**
     * Append full data for type item
     * 
     * @param $result
     * @param $userGroup
     * @return array
     */
    private function appendFullData($result, $userGroup)
    {
        return array_merge($result, [
            'role' => object_get($userGroup, 'role', '')
        ]);
    }

}
