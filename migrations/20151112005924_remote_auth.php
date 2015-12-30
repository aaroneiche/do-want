<?php

use Phinx\Migration\AbstractMigration;

class RemoteAuth extends AbstractMigration
{
    //Creates the user_auth_providers table.

    public function change()
    {

        $table = $this->table('user_auth_providers');
        
        $table->addColumn('user_id', 'integer')
        ->addColumn('provider', 'string', array('limit'=>60))
        ->addColumn('social_id', 'string', array('limit'=>60))
        ->create();
    }
}
