<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUserGroupMigration extends AbstractMigration
{
    public function up()
    {
        $userGroup = $this->table('user_group', [
            'id' => false,
            'primary_key' => 'id'
        ]);
        $userGroup
            ->addColumn('id', 'string', ['length' => 48])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true, 'default' => '0000-00-00 00:00:00'])
            ->addColumn('created_by', 'string', ['length' => 255])
            ->addColumn('updated_by', 'string', ['length' => 255])
            ->addColumn('deleted_by', 'string', ['length' => 255])
            ->addColumn('name', 'string', ['length' => 255])
            ->addColumn('role', 'text')
            ->addColumn('tier', 'integer')
            ->addColumn('status', 'enum', ['values' => ['0', '1'], 'default' => '1'])
            ->addColumn('deleted', 'enum', ['values' => ['0', '1'], 'default' => '0'])
            ->addIndex(['id'], ['unique' => true])
            ->create();
    }

    public function down()
    {

    }
}
