<?php

use Phinx\Seed\AbstractSeed;

class Ranks extends AbstractSeed
{
    /**
     * Ranks seed method
     *
     */
    public function run()
    {
        $data = array(

            array(
                'ranking' => 1,
                'title' => "1 - Wouldn't mind it",
                'rendered' => '<img src="images/star_on.gif" alt="*"><img src="images/star_off.gif" alt=""><img src="images/star_off.gif" alt=""><img src="images/star_off.gif" alt=""><img src="images/star_off.gif" alt="">',
                'rankorder' => 1
            ),
            array(
                'ranking' => 2,
                'title' => "2 - Would be nice to have",
                'rendered' => '<img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_off.gif" alt=""><img src="images/star_off.gif" alt=""><img src="images/star_off.gif" alt="">',
                'rankorder' => 2
            ),
            array(
                'ranking' => 3,
                'title' => "3 - Would make me happy",
                'rendered' => '<img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_off.gif" alt=""><img src="images/star_off.gif" alt="">',
                'rankorder' => 3
            ),
            array(
                'ranking' => 4,
                'title' => "4 - I would really, really like this",
                'rendered' => '<img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_off.gif" alt="">',
                'rankorder' => 4
            ),
            array(
                'ranking' => 5,
                'title' => "5 - I'd love to get this",
                'rendered' => '<img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*"><img src="images/star_on.gif" alt="*">',
                'rankorder' => 5
            )
        );
        $ranks = $this->table('ranks');
        $ranks->insert($data)
              ->save();
    }
}
