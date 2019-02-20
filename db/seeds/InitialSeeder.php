<?php

use Phinx\Seed\AbstractSeed;

class InitialSeeder extends AbstractSeed
{
    /** @var array $sharedValue */
    private $sharedValue = [];

    public function run()
    {
        $user = $this->importJson('user.json');
        $this->seedUserGroup($user);
        $this->seedUser($user);
        
        $client = $this->importJson('client.json');
        $this->seedClient($client);
    }

    /**
     * Seed client data
     *
     * @param array $params
     */
    private function seedClient($params)
    {
        $data = [];
        foreach ($params['client'] as $value) {
            $data[] = [
                'id' => array_get($value, 'id', ''),
                'secret' => array_get($value, 'secret', ''),
                'jwt_key' => array_get($value, 'jwt_key', ''),
                'scope' => array_get($value, 'scope', ''),
                'grant_types' => array_get($value, 'grant_types', ''),
                'redirect_uri' => array_get($value, 'redirect_uri', ''),
                'name' => array_get($value, 'name', ''),
                'address' => array_get($value, 'address', ''),
                'organization' => array_get($value, 'organization', ''),
                'description' => array_get($value, 'description', ''),
                'autoapprove' => array_get($value, 'autoapprove', ''),
            ];
        }
        $client = $this->table('client');
        $client->insert($data)->save();
    } 

    /**
     * Seed user group data
     * 
     * @param array $params 
     */
    private function seedUserGroup($params)
    {
        $data = [];
        foreach ($params['user_group'] as $value) {
            $id = generateId();
            $name = array_get($value, 'name', '');
            $now = date('YYYY-mm-dd H:i:s');
            $data[] = [
                'id' => $id,
                'name' => $name,
                'role' => array_get($value, 'role', '[]'),
                'tier' => array_get($value, 'tier', ''),
                'status' => '1'
            ];

            $this->sharedValue['user_group'][$name] = $id;
        }
        $userGroup = $this->table('user_group');
        $userGroup->insert($data)->save();
    }

    /**
     * Seed user data
     * 
     * @param array $params 
     */
    private function seedUser($params)
    {
        $data = [];
        foreach ($params['user'] as $key => $value) {
            $id = generateId();
            $userGroup = array_get($value, 'user_group', '');
            $username = array_get($value, 'username', '');
            $now = date('YYYY-mm-dd H:i:s');
            $data[] = [
                'id' => $id,
                'jwt_key' => generateRandomString(32),
                'user_group_id' => array_get($this->sharedValue['user_group'], $userGroup, ''),
                'fullname' => array_get($value, 'name', ''),
                'username' => $username,
                'email' => array_get($value, 'email', ''),
                'password' => hash_bcrypt(array_get($value, 'password', '')),
                'phone' => array_get($value, 'phone', ''),
                'status' => '1'
            ];

            $this->sharedValue['username'][$key] = $username;
        }
        $user = $this->table('user');
        $user->insert($data)->save();
    }

    /**
     * Import json file and return as associative array
     * 
     * @param string $file
     * @return array
     */
    private function importJson($file)
    {
        $resource = file_get_contents(dirname(__FILE__) . '/../data/' . $file);
        return json_decode($resource, true);
    }
}
