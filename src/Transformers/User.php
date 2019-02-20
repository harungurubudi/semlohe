<?php
namespace App\Semlohe\Transformers;

class User extends AbstractTransformer
{
    /** @var string $type */
    private $type = 'collection';

    /** @var boolean $showSensitiveData */
    private $showSensitiveData = false;
    
    /**
     * Type attribute setter
     * 
     * @param string $type
     * @return User $this
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
     * ShowSensitiveData attribute setter
     * 
     * @param string $showSensitiveData
     * @return User $this
     */
    public function setShowSensitiveData($showSensitiveData)
    {
        $this->showSensitiveData = $showSensitiveData;
        return $this;
    }

    /**
     * ShowSensitiveData attribute getter
     * 
     * @return string
     */
    public function getShowSensitiveData()
    {
        return $this->type;
    }

    /**
     * Do tranformation
     *
     * @param $user
     * @return
     */
    public function transform($user)
    {
        return $this->user($user);
    }

    /**
     * Admin brief object definition

     * @param Admin $user
     * @return Array
     */
    protected function user($user)
    {
        $result = [
            'id' => object_get($user, 'id', ''),
            'fullname' => object_get($user, 'fullname', ''),
            'username' => object_get($user, 'username', ''),
            'status' => object_get($user, 'status', '')
        ];

        $result = $this->appendGroupData($result, $user);

        if ($this->getType() === 'item') {
            $result = $this->appendFullData($result, $user);
        }

        if ($this->showSensitiveData) {
            $result = $this->appendSensitiveData($result, $user);
        }

        return $result;
    }

    /**
     * Append group data from relation
     * 
     * @param $result
     * @param $user
     * @return array
     */
    private function appendGroupData($result, $user) {
        $userGroup = $user->userGroup()->first();
        return array_merge($result, [
            'user_group_id' => object_get($userGroup, 'id', ''),
            'user_group_name' => object_get($userGroup, 'name', ''),
            'user_group_tier' => object_get($userGroup, 'tier', ''),
        ]);
    }

    /**
     * Append full data for type item
     * 
     * @param $result
     * @param $user
     * @return array
     */
    private function appendFullData($result, $user)
    {
        return array_merge($result, [
            'email' => object_get($user, 'email', ''),
            'phone' => object_get($user, 'phone', ''),
        ]);
    }

    /**
     * Append sensitive data if toggle is set to true
     *
     * @param $result
     * @param $user
     * @return void
     */
    private function appendSensitiveData($result, $user)
    {
        return array_merge($result, [
            'jwt_key' => object_get($user, 'jwt_key', ''),
        ]);
        
        return $result;
    }
}
