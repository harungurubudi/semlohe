<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUserMigration extends AbstractMigration
{
    public function up()
    {
        $userGroup = $this->table('user', [
            'id' => false,
            'primary_key' => 'id'
        ]);
        $userGroup
            ->addColumn('id', 'string', ['length' => 48])
            ->addColumn('jwt_key', 'string', ['length' => 255])
            ->addColumn('user_group_id', 'string', ['length' => 48])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('suspended_until', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('created_by', 'string', ['length' => 255])
            ->addColumn('updated_by', 'string', ['length' => 255])
            ->addColumn('deleted_by', 'string', ['length' => 255])
            ->addColumn('suspended_by', 'string', ['length' => 255])
            ->addColumn('fullname', 'string', ['length' => 255])
            ->addColumn('username', 'string', ['length' => 255])
            ->addColumn('email', 'string', ['length' => 255])
            ->addColumn('password', 'string', ['length' => 255])
            ->addColumn('phone', 'string', ['length' => 255])
            ->addColumn('description', 'text')
            ->addColumn('status', 'enum', ['values' => ['0', '1'], 'default' => '1'])
            ->addColumn('deleted', 'enum', ['values' => ['0', '1'], 'default' => '0'])
            ->addIndex(['id'], ['unique' => true])
            ->addIndex(['user_group_id'], ['name' => 'user_non_unique_index'])
            ->create();
    }

    public function down()
    {

    }
}
