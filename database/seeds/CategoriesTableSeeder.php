<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('categories')->insertGetId([
            'parent'   => 0,
            'priority' => 0,
            'status'   => 'publish',
            'type'     => 'default',
        ]);
        foreach (['vi'] as $lang) {
            DB::table('category_languages')->insert([
                'title' => 'Uncategorized',
                'slug' => 'uncategorized',
                'language' => $lang,
                'category_id' => $id,
            ]);
        }

        factory(App\Category::class, 10)->create()->each(function ($c) {
            foreach (['vi'] as $lang) {
                $c->languages()->save(factory(App\CategoryLanguage::class)->make(['language'=>$lang]));
            }
        });
    }
}
