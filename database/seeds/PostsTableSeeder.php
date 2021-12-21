<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Post::class, 20)->create()->each(function ($p) {
            foreach (['vi','en'] as $lang) {
                $p->languages()->save(factory(App\PostLanguage::class)->make(['language'=>$lang]));
            }
	    });
    }
}
