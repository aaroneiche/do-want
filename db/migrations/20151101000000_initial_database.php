<?php

/*
The Initial migration handles existing DoWant installations, as well as creating new 
tables for new installations.
*/

use Phinx\Migration\AbstractMigration;

class InitialDatabase extends AbstractMigration
{
    public function change()
    {

        //Allocations table
        if(!$this->hasTable('allocs')){
            $table = $this->table('allocs');
            $table
                ->addColumn('itemid', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('userid', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('bought', 'integer', array('default' => '0', 'limit' => 1))
                ->addColumn('quantity', 'integer', array('default' => '0', 'limit' => 1))
                ->create();            
        }

        // Migration for table categories
        if(!$this->hasTable('categories')){
            $table = $this->table('categories', array('id'=> 'categoryid'));
            $table
                ->addColumn('category', 'string', array('null' => true, 'limit' => 50))
                ->create();            
        }


        // Migration for table events
        if(!$this->hasTable('events')){
            $table = $this->table('events', array('id'=> 'eventid'));
            $table
                ->addColumn('userid', 'integer', array('null' => true, 'limit' => 11))
                ->addColumn('description', 'string', array('default' => '', 'limit' => 100))
                ->addColumn('eventdate', 'date', array('default' => '0000-00-00'))
                ->addColumn('recurring', 'integer', array('default' => '0', 'limit' => 1))
                ->create();            
        }


        // Migration for table families
        if(!$this->hasTable('families')){
            $table = $this->table('families', array('id'=> 'familyid'));
            $table
                ->addColumn('familyname', 'string', array('default' => '', 'limit' => 255))
                ->create();    
        }
        

        // Migration for table itemimages
        if(!$this->hasTable('itemimages')){
            $table = $this->table('itemimages', array('id'=> 'imageid'));
            $table
                ->addColumn('itemid', 'integer', array('limit' => 11))
                ->addColumn('filename', 'string', array('default' => '', 'limit' => 255))
                ->create();            
        }


        // Migration for table items
        if(!$this->hasTable('items')){
            $table = $this->table('items',array('id'=> 'itemid'));
            $table
                ->addColumn('userid', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('description', 'string', array('default' => '', 'limit' => 255))
                ->addColumn('price', 'decimal', array('precision' => 9, 'scale' => 2, 'null' => true))
                ->addColumn('source', 'string', array('default' => '', 'limit' => 255))
                ->addColumn('ranking', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('url', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('category', 'integer', array('null' => true, 'limit' => 11))
                ->addColumn('comment', 'text', array('null' => true))
                ->addColumn('quantity', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('image_filename', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('addedByUserId', 'integer', array('null' => true, 'limit' => 11))
                ->addColumn('visibleToOwner', 'integer', array('null' => true, 'limit' => 11))
                ->addColumn('received', 'integer', array('null' => true, 'default' => '0', 'limit' => 1))
                ->create();            
        }


        // Migration for table itemsources
        if(!$this->hasTable('itemsources')){
            $table = $this->table('itemsources', array('id'=> 'sourceid'));
            $table
                ->addColumn('itemid', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('source', 'text', array())
                ->addColumn('sourceurl', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('sourceprice', 'decimal', array('precision' => 9, 'scale' => 2, 'null' => true))
                ->addColumn('sourcecomments', 'text', array('null' => true))
                ->addColumn('addedByUserId', 'integer', array('limit' => 11))
                ->addColumn('visibleToOwner', 'integer', array('null' => true, 'limit' => 11))
                ->create();            
        }


        // Migration for table memberships
        if(!$this->hasTable('memberships')){
            $table = $this->table('memberships');
            $table
                ->addColumn('userid', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('familyid', 'integer', array('default' => '0', 'limit' => 11))
                ->create();
        }


        // Migration for table messages
        if(!$this->hasTable('messages')){
            $table = $this->table('messages', array('id'=> 'messageid'));
            $table
            ->addColumn('sender', 'integer', array('default' => '0', 'limit' => 11))
            ->addColumn('recipient', 'integer', array('default' => '0', 'limit' => 11))
            ->addColumn('message', 'string', array('default' => '', 'limit' => 255))
            ->addColumn('isread', 'integer', array('default' => '0', 'limit' => 1))
            ->addColumn('created', 'date', array('default' => '0000-00-00'))
            ->create();
        }


        // Migration for table ranks
        if(!$this->hasTable('ranks')){
            $table = $this->table('ranks');
            $table
                ->addColumn('ranking', 'integer', array('limit' => 11))
                ->addColumn('title', 'string', array('default' => '', 'limit' => 50))
                ->addColumn('rendered', 'string', array('default' => '', 'limit' => 255))
                ->addColumn('rankorder', 'integer', array('default' => '0', 'limit' => 11))
                ->create();
        }

        // Migration for table shoppers
        if(!$this->hasTable('shoppers')){
            $table = $this->table('shoppers');
            $table
                ->addColumn('shopper', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('mayshopfor', 'integer', array('default' => '0', 'limit' => 11))
                ->addColumn('pending', 'integer', array('default' => '0', 'limit' => 1))
                ->create();
        }

        // Migration for table users
        if(!$this->hasTable('users')){
            $table = $this->table('users', array('id'=> 'userid'));
            $table
                ->addColumn('username', 'string', array('default' => '', 'limit' => 20))
                ->addColumn('password', 'string', array('default' => '', 'limit' => 50))
                ->addColumn('fullname', 'string', array('default' => '', 'limit' => 50))
                ->addColumn('email', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('approved', 'integer', array('default' => '0', 'limit' => 1))
                ->addColumn('admin', 'integer', array('default' => '0', 'limit' => 1))
                ->addColumn('comment', 'text', array('null' => true))
                ->addColumn('email_msgs', 'integer', array('default' => '0', 'limit' => 1))
                ->addColumn('list_stamp', 'datetime', array('null' => true))
                ->addColumn('initialfamilyid', 'integer', array('null' => true, 'limit' => 11))
                ->create();
        }

    }
}

?>