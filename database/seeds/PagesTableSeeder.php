<?php

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (['Giới thiệu','Tuyển dụng','Liên hệ','Footer'] as $name) {
    		$id = DB::table('pages')->insertGetId([
	    		'type' => str_slug($name)
	    	]);
	    	foreach (['vi','en'] as $lang) {
	    		DB::table('page_languages')->insert([
	    			'title' => $name,
	    			'slug' => str_slug($name),
	    			'language' => $lang,
		    		'page_id' => $id,
		    	]);
            }
        }
    }
}
