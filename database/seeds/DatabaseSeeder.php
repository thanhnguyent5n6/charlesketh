<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        // $this->call(PagesTableSeeder::class);

        $id = DB::table('seos')->insertGetId([
            'link' => url('/'),
            'priority' => 0,
            'status'   => 'publish',
        ]);
        foreach (['vi','en'] as $lang) {
            DB::table('seo_languages')->insert([
                'title' => 'Trang chủ',
                'slug' => 'trang-chu',
                'language' => $lang,
                'seo_id' => $id,
            ]);
        }
    }
}
