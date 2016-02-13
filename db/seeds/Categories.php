<?php

use Phinx\Seed\AbstractSeed;

class Categories extends AbstractSeed
{
    /**
     * Categories seed method
     *
     */
    public function run()
    {
        $data = array(
            array( 'category' => "Books"),
            array( 'category' => "Music"),
            array( 'category' => "Video Games"),
            array( 'category' => "Clothing"),
            array( 'category' => "Movies/DVD"),
            array( 'category' => "Gift Certificates"),
            array( 'category' => "Hobbies"),
            array( 'category' => "Household"),
            array( 'category' => "Electronics"),
            array( 'category' => "Ornaments/Figurines"),
            array( 'category' => "Automotive"),
            array( 'category' => "Toys"),
            array( 'category' => "Jewelery"),
            array( 'category' => "Computer"),
            array( 'category' => "Games"),
            array( 'category' => "Tools"),
            array( 'category' => "Kitchen"),
            array( 'category' => "Bathroom"),
            array( 'category' => "Bedroom"),
            array( 'category' => "Shoes"),
            array( 'category' => "Gardening")
        );
        $categories = $this->table('categories');
        $categories->insert($data)
              ->save();
    }
}
