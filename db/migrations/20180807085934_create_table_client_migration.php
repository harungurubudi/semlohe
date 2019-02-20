<?php

use Phinx\Migration\AbstractMigration;

class CreateTableClientMigration extends AbstractMigration
{
    public function up()
    {
        $userGroup = $this->table('client', [
            'id' => false,
            'primary_key' => 'id'
        ]);
        $userGroup
            ->addColumn('id', 'string', ['length' => 48])
            ->addColumn('secret', 'string', ['length' => 255])
            ->addColumn('jwt_key', 'string', ['length' => 255])
            ->addColumn('scope', 'text')
            ->addColumn('grant_types', 'string', ['length' => 255])
            ->addColumn('redirect_uri', 'text')
            ->addColumn('name', 'string', ['length' => 255])
            ->addColumn('email', 'string', ['length' => 255])
            ->addColumn('address', 'text')
            ->addColumn('organization', 'string', ['length' => 255])
            ->addColumn('description', 'text')
            ->addColumn('autoapprove', 'enum', ['values' => ['0', '1'], 'default' => '1'])
            ->addIndex(['id'], ['unique' => true])
            ->create();
    }

    public function down()
    {

    }
}
