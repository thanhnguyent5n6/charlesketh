<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;
use App\Permission;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$user = User::create([
    		'name' => 'Võ Quốc Hải',
            'email' => 'quochainina@gmail.com',
	        'username' => '',
            'password' => bcrypt('123456'),
	        'type' => 'admin',
	        'remember_token' => str_random(10),
    	]);

        // Create roles
        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        // $product = Role::create([
        //     'name' => 'san-pham',
        //     'display_name' => 'Sản phẩm',
        // ]);

        // $sale = Role::create([
        //     'name' => 'sales',
        //     'display_name' => 'Bán hàng',
        // ]);

        // $wms = Role::create([
        //     'name' => 'wms',
        //     'display_name' => 'Kho hàng'
        // ]);

        // $news = Role::create([
        //     'name' => 'tin-tuc',
        //     'display_name' => 'Tin tức',
        // ]);

        // $services = Role::create([
        //     'name' => 'dich-vu',
        //     'display_name' => 'Dịch vụ',
        // ]);

        // $page = Role::create([
        //     'name' => 'pages',
        //     'display_name' => 'Trang tĩnh',
        // ]);

        // $photo = Role::create([
        //     'name' => 'photos',
        //     'display_name' => 'Hình ảnh'
        // ]);

        // $link = Role::create([
        //     'name' => 'links',
        //     'display_name' => 'Liên kết'
        // ]);

        // $register = Role::create([
        //     'name' => 'registers',
        //     'display_name' => 'Đăng ký'
        // ]);

        // Assign Users with roles
        $user->roles()->attach($admin->id);
    }
}
