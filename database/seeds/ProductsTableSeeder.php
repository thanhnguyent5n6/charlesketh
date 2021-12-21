<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Product::class, 100)->create()->each(function ($p) {
            foreach (['vi','en'] as $lang) {
                $p->languages()->save(factory(App\ProductLanguage::class)->make(['language'=>$lang]));
            }
	    });
    }
}
