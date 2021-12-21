<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login/facebook', 'Socialite\FacebookController@redirectToProvider')->name('login.facebook');
Route::get('/login/facebook/callback', 'Socialite\FacebookController@handleProviderCallback')->name('login.facebook.callback');

Route::get('/login/google', 'Socialite\GoogleController@redirectToProvider')->name('login.google');
Route::get('/login/google/callback', 'Socialite\GoogleController@handleProviderCallback')->name('login.google.callback');

Route::get('/noimage/{width}x{height}', function(Intervention\Image\Facades\Image $image, $w, $h){
	if(!file_exists(public_path('images/noimage/'.$w.'x'.$h.'.jpg'))){
		$image::canvas($w,$h,'#ccc')
		->text($w.'x'.$h, $w/2, $h/2, function($font) use($w) {
		    $font->file( public_path('fonts/Roboto-Regular.ttf') );
		    $font->size($w/8);
		    $font->color('#333');
		    $font->align('center');
		    $font->valign('center');
		    // $font->angle(45);
		})->save( public_path('images/noimage/'.$w.'x'.$h.'.jpg') );
	}
	return response()->file( public_path('images/noimage/'.$w.'x'.$h.'.jpg') );
});

Route::get('/download/{file}', function($file){
	return response()->download(public_path($file));
})->where('file','.*')->name('download.file');


// Route::get('/uploads/{img}', function(Intervention\Image\Facades\Image $image, $img){
// 	if(file_exists(public_path('uploads/'.$img))){
// 		$img = $image::make(public_path('uploads/'.$img));
// 		if(file_exists(public_path('uploads/photos/'.config('settings.watermark'))))
// 			$img->insert(public_path('uploads/photos/'.config('settings.watermark')), 'bottom-right', 10, 10);
//         return $img->response();
// 	}
// })->where('img','.*');

Route::get('/thumbnail/{width}x{height}x{zc}/{file}', function(Intervention\Image\Facades\Image $image, $w, $h, $zc, $file){
	return $image::make( public_path($file) )
		->fit($w, $h, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        })->response();
})->where('file','.*');


Route::group(['prefix'=>'admin', 'as'=> 'admin.', 'namespace'=>'Admin'], function(){
	Auth::routes();
	Route::group(['middleware' => ['auth', 'checkUsers:admin']], function(){
		Route::get('/', 'DashboardController@index')->name('dashboard.index');

		Route::group(['middleware' => ['checkRoles:admin']], function(){
			// Users
			Route::get('/users', 'UserController@index')->name('user.index');
			Route::get('/users/create', 'UserController@create')->name('user.create');
			Route::post('/users', 'UserController@store')->name('user.store');
			Route::get('/users/{id}', 'UserController@edit')->where('id','[0-9]+')->name('user.edit');
			Route::put('/users/{id}', 'UserController@update')->name('user.update');
			Route::delete('/users/{id}', 'UserController@delete')->name('user.delete');

			Route::get('/roles', 'RoleController@index')->name('role.index');
			Route::get('/roles/create', 'RoleController@create')->name('role.create');
			Route::post('/roles', 'RoleController@store')->name('role.store');
			Route::get('/roles/{id}', 'RoleController@edit')->where('id','[0-9]+')->name('role.edit');
			Route::put('/roles/{id}', 'RoleController@update')->name('role.update');
			Route::delete('/roles/{id}', 'RoleController@delete')->name('role.delete');

			Route::get('/permissions', 'PermissionController@index')->name('permission.index');
			Route::get('/permissions/create', 'PermissionController@create')->name('permission.create');
			Route::post('/permissions', 'PermissionController@store')->name('permission.store');
			Route::get('/permissions/{id}', 'PermissionController@edit')->where('id','[0-9]+')->name('permission.edit');
			Route::put('/permissions/{id}', 'PermissionController@update')->name('permission.update');
			Route::delete('/permissions/{id}', 'PermissionController@delete')->name('permission.delete');

			Route::get('/groups', 'GroupController@index')->name('group.index');
			Route::get('/groups/create', 'GroupController@create')->name('group.create');
			Route::post('/groups', 'GroupController@store')->name('group.store');
			Route::get('/groups/{id}', 'GroupController@edit')->where('id','[0-9]+')->name('group.edit');
			Route::put('/groups/{id}', 'GroupController@update')->name('group.update');
			Route::delete('/groups/{id}', 'GroupController@delete')->name('group.delete');

			// Members
			Route::get('/members', 'MemberController@index')->name('member.index');
			Route::get('/members/create', 'MemberController@create')->name('member.create');
			Route::post('/members', 'MemberController@store')->name('member.store');
			Route::get('/members/{id}', 'MemberController@edit')->where('id','[0-9]+')->name('member.edit');
			Route::put('/members/{id}', 'MemberController@update')->name('member.update');
			Route::delete('/members/{id}', 'MemberController@delete')->name('member.delete');

			// Searchs
			Route::get('/searchs', 'SearchController@index')->name('search.index');
			Route::get('/searchs/create', 'SearchController@create')->name('search.create');
			Route::post('/searchs', 'SearchController@store')->name('search.store');
			Route::get('/searchs/{id}', 'SearchController@edit')->where('id','[0-9]+')->name('search.edit');
			Route::put('/searchs/{id}', 'SearchController@update')->name('search.update');
			Route::delete('/searchs/{id}', 'SearchController@delete')->name('search.delete');

			Route::get('/settings', 'SettingController@index')->name('setting.index');
        	Route::post('/settings', 'SettingController@store')->name('setting.store');

        	// Categories
			Route::get('/categories', 'CategoryController@index')->name('category.index');
			Route::get('/categories/create', 'CategoryController@create')->name('category.create');
			Route::post('/categories', 'CategoryController@store')->name('category.store');
			Route::get('/categories/{id}', 'CategoryController@edit')->where('id','[0-9]+')->name('category.edit');
			Route::put('/categories/{id}', 'CategoryController@update')->name('category.update');
			Route::delete('/categories/{id}', 'CategoryController@delete')->name('category.delete');
			Route::get('/categories/export', 'CategoryController@export')->name('category.export');

			// Attributes
			Route::get('/attributes', 'AttributeController@index')->name('attribute.index');
			Route::get('/attributes/create', 'AttributeController@create')->name('attribute.create');
			Route::post('/attributes', 'AttributeController@store')->name('attribute.store');
			Route::get('/attributes/{id}', 'AttributeController@edit')->where('id','[0-9]+')->name('attribute.edit');
			Route::put('/attributes/{id}', 'AttributeController@update')->name('attribute.update');
			Route::delete('/attributes/{id}', 'AttributeController@delete')->name('attribute.delete');

			// Suppliers
	        Route::get('/suppliers', 'SupplierController@index')->name('supplier.index');
	        Route::get('/suppliers/create', 'SupplierController@create')->name('supplier.create');
	        Route::post('/suppliers', 'SupplierController@store')->name('supplier.store');
	        Route::get('/suppliers/{id}', 'SupplierController@edit')->where('id','[0-9]+')->name('supplier.edit');
	        Route::put('/suppliers/{id}', 'SupplierController@update')->name('supplier.update');
	        Route::delete('/suppliers/{id}', 'SupplierController@delete')->name('supplier.delete');
		});

		Route::group(['middleware' => ['checkRoles:san-pham']], function(){
			// Products
			Route::get('/products', 'ProductController@index')->name('product.index');
			Route::get('/products/create', 'ProductController@create')->name('product.create');
			Route::post('/products', 'ProductController@store')->name('product.store');
			Route::get('/products/{id}', 'ProductController@edit')->where('id','[0-9]+')->name('product.edit');
			Route::put('/products/{id}', 'ProductController@update')->name('product.update');
			Route::delete('/products/{id}', 'ProductController@delete')->name('product.delete');

			Route::get('/products/ajax', 'ProductController@ajax')->name('product.ajax');
			Route::get('/products/export', 'ProductController@export')->name('product.export');
			Route::post('/products/import-new', 'ProductController@importNew')->name('product.import-new');
			Route::post('/products/import', 'ProductController@import')->name('product.import');
			Route::get('/products/mask/{id}', 'ProductController@mask')->name('product.mask');
			Route::get('/products/mask-small/{id}', 'ProductController@maskSmall')->name('product.mask-small');
			Route::get('/products/mask-circle/{id}', 'ProductController@maskCircle')->name('product.mask-circle');

			Route::get('/products/price', 'ProductController@price')->name('product.price');

		});
		Route::group(['middleware' => ['checkRoles:coupons']], function(){
			// Coupons
	        Route::get('/coupons', 'CouponController@index')->name('coupon.index');
	        Route::get('/coupons/create', 'CouponController@create')->name('coupon.create');
	        Route::post('/coupons', 'CouponController@store')->name('coupon.store');
	        Route::get('/coupons/{id}', 'CouponController@edit')->where('id','[0-9]+')->name('coupon.edit');
	        Route::put('/coupons/{id}', 'CouponController@update')->name('coupon.update');
	        Route::delete('/coupons/{id}', 'CouponController@delete')->name('coupon.delete');
	    });
	    Route::group(['middleware' => ['checkRoles:promotions']], function(){
			// Promotions
	        Route::get('/promotions', 'PromotionController@index')->name('promotion.index');
	        Route::get('/promotions/create', 'PromotionController@create')->name('promotion.create');
	        Route::post('/promotions', 'PromotionController@store')->name('promotion.store');
	        Route::get('/promotions/{id}', 'PromotionController@edit')->where('id','[0-9]+')->name('promotion.edit');
	        Route::put('/promotions/{id}', 'PromotionController@update')->name('promotion.update');
	        Route::delete('/promotions/{id}', 'PromotionController@delete')->name('promotion.delete');
	    });

	    Route::group(['middleware' => ['checkRoles:seos']], function(){
	    	// Seos
			Route::get('/seos', 'SeoController@index')->name('seo.index');
			Route::get('/seos/create', 'SeoController@create')->name('seo.create');
			Route::post('/seos', 'SeoController@store')->name('seo.store');
			Route::get('/seos/{id}', 'SeoController@edit')->where('id','[0-9]+')->name('seo.edit');
			Route::put('/seos/{id}', 'SeoController@update')->name('seo.update');
			Route::delete('/seos/{id}', 'SeoController@delete')->name('seo.delete');
		});

		Route::group(['middleware' => ['checkRoles:tin-tuc,dich-vu']], function(){
			// Posts
			Route::get('/posts', 'PostController@index')->name('post.index');
			Route::get('/posts/create', 'PostController@create')->name('post.create');
			Route::post('/posts', 'PostController@store')->name('post.store');
			Route::get('/posts/{id}', 'PostController@edit')->where('id','[0-9]+')->name('post.edit');
			Route::put('/posts/{id}', 'PostController@update')->name('post.update');
			Route::delete('/posts/{id}', 'PostController@delete')->name('post.delete');
		});
		
		Route::group(['middleware' => ['checkRoles:page']], function(){
			// Pages
			Route::get('/pages', 'PageController@index')->name('page.index');
			Route::get('/pages/create', 'PageController@create')->name('page.create');
			Route::post('/pages', 'PageController@store')->name('page.store');
			Route::get('/pages/{id}', 'PageController@edit')->where('id','[0-9]+')->name('page.edit');
			Route::put('/pages/{id}', 'PageController@update')->name('page.update');
			Route::delete('/pages/{id}', 'PageController@delete')->name('page.delete');
		});

		Route::group(['middleware' => ['checkRoles:photo']], function(){
	        // Images
	        Route::get('/photos', 'PhotoController@index')->name('photo.index');
	        Route::get('/photos/create', 'PhotoController@create')->name('photo.create');
	        Route::post('/photos', 'PhotoController@store')->name('photo.store');
	        Route::get('/photos/{id}', 'PhotoController@edit')->where('id','[0-9]+')->name('photo.edit');
	        Route::put('/photos/{id}', 'PhotoController@update')->name('photo.update');
	        Route::delete('/photos/{id}', 'PhotoController@delete')->name('photo.delete');
	    });

	    Route::group(['middleware' => ['checkRoles:link']], function(){
	        // Links
	        Route::get('/links', 'LinkController@index')->name('link.index');
	        Route::get('/links/create', 'LinkController@create')->name('link.create');
	        Route::post('/links', 'LinkController@store')->name('link.store');
	        Route::get('/links/{id}', 'LinkController@edit')->where('id','[0-9]+')->name('link.edit');
	        Route::put('/links/{id}', 'LinkController@update')->name('link.update');
	        Route::delete('/links/{id}', 'LinkController@delete')->name('link.delete');
	    });

	    Route::group(['middleware' => ['checkRoles:register']], function(){
	        // Registers
	        Route::get('/registers', 'RegisterController@index')->name('register.index');
	        Route::get('/registers/create', 'RegisterController@create')->name('register.create');
	        Route::post('/registers', 'RegisterController@store')->name('register.store');
	        Route::get('/registers/{id}', 'RegisterController@edit')->where('id','[0-9]+')->name('register.edit');
	        Route::put('/registers/{id}', 'RegisterController@update')->name('register.update');
	        Route::delete('/registers/{id}', 'RegisterController@delete')->name('register.delete');


	        Route::get('/registers/export', 'RegisterController@export')->name('register.export');
	    });

		Route::group(['middleware' => ['checkRoles:comment']], function(){
	        // Comments
	        Route::get('/comments', 'CommentController@index')->name('comment.index');
	        Route::post('/comments/ajax', 'CommentController@ajax')->name('comment.ajax');
	        Route::get('/comments/create', 'CommentController@create')->name('comment.create');
	        Route::post('/comments', 'CommentController@store')->name('comment.store');
	        Route::get('/comments/{id}', 'CommentController@edit')->where('id','[0-9]+')->name('comment.edit');
	        Route::put('/comments/{id}', 'CommentController@update')->name('comment.update');
	        Route::delete('/comments/{id}', 'CommentController@delete')->name('comment.delete');
		});

		Route::group(['middleware' => ['checkRoles:online,retail,wholesale,shop']], function(){
	        // Orders
	        Route::get('/orders', 'OrderController@index')->name('order.index');
	        Route::get('/orders/create', 'OrderController@create')->name('order.create');
	        Route::post('/orders', 'OrderController@store')->name('order.store');
	        Route::get('/orders/{id}', 'OrderController@edit')->where('id','[0-9]+')->name('order.edit');

	        Route::get('/orders/{id}/show', 'OrderController@show')->where('id','[0-9]+')->name('order.show');

	        Route::put('/orders/{id}', 'OrderController@update')->name('order.update');
	        Route::delete('/orders/{id}', 'OrderController@delete')->name('order.delete');
	        
	        Route::get('/orders/ajax', 'OrderController@ajax')->name('order.ajax');
	        Route::get('/orders/customer', 'OrderController@customer')->name('order.customer');
	        Route::get('/orders/print', 'OrderController@print')->name('order.print');
	        Route::get('/orders/export', 'OrderController@export')->name('order.export');
	        Route::get('/orders/export-customer', 'OrderController@exportCustomer')->name('order.export-customer');


	        Route::get('/customers', 'CustomerController@index')->name('customer.index');
	        Route::get('/customers/create', 'CustomerController@create')->name('customer.create');
	        Route::post('/customers', 'CustomerController@store')->name('customer.store');
	        Route::get('/customers/{id}', 'CustomerController@edit')->where('id','[0-9]+')->name('customer.edit');
	        Route::put('/customers/{id}', 'CustomerController@update')->name('customer.update');
	        Route::delete('/customers/{id}', 'CustomerController@delete')->name('customer.delete');

	        Route::get('/pos', 'POSController@index')->name('pos.index');
	        Route::get('/pos/create', 'POSController@create')->name('pos.create');
	        Route::post('/pos', 'POSController@store')->name('pos.store');
	        Route::get('/pos/{id}', 'POSController@edit')->where('id','[0-9]+')->name('pos.edit');

	        Route::get('/pos/{id}/show', 'POSController@show')->where('id','[0-9]+')->name('pos.show');
	        Route::put('/pos/{id}', 'POSController@update')->name('pos.update');
	        Route::delete('/pos/{id}', 'POSController@delete')->name('pos.delete');

	        Route::get('/pos/ajax', 'POSController@ajax')->name('pos.ajax');
	        Route::get('/pos/customer', 'POSController@customer')->name('pos.customer');
	        Route::get('/pos/print', 'POSController@print')->name('pos.print');


        });

		Route::group(['middleware' => ['checkRoles:warranty']], function(){
	        // Warranties
	        Route::get('/warranties', 'WarrantyController@index')->name('warranty.index');
	        Route::get('/warranties/create', 'WarrantyController@create')->name('warranty.create');
	        Route::post('/warranties', 'WarrantyController@store')->name('warranty.store');
	        Route::get('/warranties/{id}', 'WarrantyController@edit')->where('id','[0-9]+')->name('warranty.edit');
	        Route::put('/warranties/{id}', 'WarrantyController@update')->name('warranty.update');
	        Route::delete('/warranties/{id}', 'WarrantyController@delete')->name('warranty.delete');

	        Route::get('/warranties/ajax', 'WarrantyController@ajax')->name('warranty.ajax');
        });

		Route::group(['middleware' => ['checkRoles:delivery']], function(){
	        // Deliveries
	        Route::get('/deliveries', 'DeliveryController@index')->name('delivery.index');
	        Route::get('/deliveries/create', 'DeliveryController@create')->name('delivery.create');
	        Route::post('/deliveries', 'DeliveryController@store')->name('delivery.store');
	        Route::get('/deliveries/{id}', 'DeliveryController@edit')->where('id','[0-9]+')->name('delivery.edit');
	        Route::put('/deliveries/{id}', 'DeliveryController@update')->name('delivery.update');
	        Route::delete('/deliveries/{id}', 'DeliveryController@delete')->name('delivery.delete');

	        Route::get('/deliveries/print', 'DeliveryController@print')->name('delivery.print');
        });

        Route::group(['middleware' => ['checkRoles:prices']], function(){
	        // Prices
	        Route::get('/prices', 'PriceController@index')->name('price.index');
	        Route::get('/prices/create', 'PriceController@create')->name('price.create');
	        Route::post('/prices', 'PriceController@store')->name('price.store');
	        Route::get('/prices/{id}', 'PriceController@edit')->where('id','[0-9]+')->name('price.edit');
	        Route::put('/prices/{id}', 'PriceController@update')->name('price.update');
	        Route::delete('/prices/{id}', 'PriceController@delete')->name('price.delete');

	        Route::get('/prices/ajax', 'PriceController@ajax')->name('price.ajax');
	        Route::get('/prices/print', 'PriceController@print')->name('price.print');
			Route::get('/prices/export', 'PriceController@export')->name('price.export');
        });

		Route::group(['middleware' => ['checkRoles:wms']], function(){
	        // WMS
	        Route::get('/wms_stores', 'WMS_StoreController@index')->name('wms_store.index');
	        Route::get('/wms_stores/create', 'WMS_StoreController@create')->name('wms_store.create');
	        Route::post('/wms_stores', 'WMS_StoreController@store')->name('wms_store.store');
	        Route::get('/wms_stores/{id}', 'WMS_StoreController@edit')->where('id','[0-9]+')->name('wms_store.edit');
	        Route::put('/wms_stores/{id}', 'WMS_StoreController@update')->name('wms_store.update');
	        Route::delete('/wms_stores/{id}', 'WMS_StoreController@delete')->name('wms_store.delete');

	        Route::get('/wms_stores/inventory', 'WMS_StoreController@inventory')->name('wms_store.inventory');

	        Route::get('/wms_imports', 'WMS_ImportController@index')->name('wms_import.index');
	        Route::get('/wms_imports/ajax', 'WMS_ImportController@ajax')->name('wms_import.ajax');
	        Route::get('/wms_imports/create', 'WMS_ImportController@create')->name('wms_import.create');
	        Route::post('/wms_imports', 'WMS_ImportController@store')->name('wms_import.store');
	        Route::get('/wms_imports/{id}', 'WMS_ImportController@edit')->where('id','[0-9]+')->name('wms_import.edit');
	        Route::put('/wms_imports/{id}', 'WMS_ImportController@update')->name('wms_import.update');
	        Route::delete('/wms_imports/{id}', 'WMS_ImportController@delete')->name('wms_import.delete');
	        Route::get('/wms_imports/print', 'WMS_ImportController@print')->name('wms_import.print');

	        Route::get('/wms_exports', 'WMS_ExportController@index')->name('wms_export.index');
	        Route::get('/wms_exports/create', 'WMS_ExportController@create')->name('wms_export.create');
	        Route::post('/wms_exports', 'WMS_ExportController@store')->name('wms_export.store');
	        Route::get('/wms_exports/{id}', 'WMS_ExportController@edit')->where('id','[0-9]+')->name('wms_export.edit');
	        Route::put('/wms_exports/{id}', 'WMS_ExportController@update')->name('wms_export.update');
	        Route::delete('/wms_exports/{id}', 'WMS_ExportController@delete')->name('wms_export.delete');

	        Route::get('/wms_transfers', 'WMS_TransferController@index')->name('wms_transfer.index');
	        Route::get('/wms_transfers/ajax', 'WMS_TransferController@ajax')->name('wms_transfer.ajax');
	        Route::get('/wms_transfers/create', 'WMS_TransferController@create')->name('wms_transfer.create');
	        Route::post('/wms_transfers', 'WMS_TransferController@store')->name('wms_transfer.store');
	        Route::get('/wms_transfers/{id}', 'WMS_TransferController@edit')->where('id','[0-9]+')->name('wms_transfer.edit');
	        Route::put('/wms_transfers/{id}', 'WMS_TransferController@update')->name('wms_transfer.update');
	        Route::delete('/wms_transfers/{id}', 'WMS_TransferController@delete')->name('wms_transfer.delete');

	    });

		Route::get('/profile', 'UserController@profile')->name('user.profile');
		Route::put('/profile/{id}', 'UserController@updateProfile')->where('id','[0-9]+')->name('user.update_profile');
		Route::post('/ajax', 'AjaxController@index');
		Route::get('/barcode/{code}', function(Milon\Barcode\Facades\DNS1DFacade $dns1d, $code){
			echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($code, "C128",3,33,array(1,1,1), true) . '" alt="barcode"   />';
		});
	});
});

Route::group(['as'=>'frontend.', 'namespace'=>'Frontend', 'middleware'=>'checkMaintenance'], function(){
	Auth::routes();
	Route::group(['middleware' => 'auth:member'], function(){
    	Route::get('/member', 'MemberController@index')->name('member.index');
    	Route::get('/member/profile', 'MemberController@profile')->name('member.profile');
    	Route::put('/member/profile', 'MemberController@profile')->name('member.profile');
    	Route::get('/member/orders', 'MemberController@orders')->name('member.order');
    	Route::get('/member/orders/{id}', 'MemberController@orderDetail')->where('id','[0-9]+')->name('member.order_detail');
    });

	Route::get('/lang={locale}', function($locale){
		session(['lang' => $locale]);
		return redirect()->back();
	})->name('home.lang');
	
	Route::get('/' , 'HomeController@index')->name('home.index');
	Route::post('/ajax', 'AjaxController@index');

	Route::get('/may-lanh-{supplier}-{filter}' , 'HomeController@static')->name('home.static')->where(['supplier' => '[a-z0-9]+', 'filter' => '[a-z0-9]+']);

	Route::get('/may-giat-bi-chay-nuoc-ra-ngoai-va-nguyen-nhan-do-dau', function(){
		return view('frontend.default.ads',['lang' => 'vi']);
	});

	Route::get('/may-giat-keu-to-nguyen-nhan-do-dau-va-cach-khac-phuc', function(){
		return view('frontend.default.ads2',['lang' => 'vi']);
	});
	
	Route::get('/so-sanh-san-pham', 'HomeController@compare')->name('home.compare');
	Route::post('/so-sanh-san-pham', 'UrlParserController@search')->name('urlparser.search');

	Route::get('/lay-gia-san-pham', 'HomeController@laygia')->name('home.laygia');
	Route::post('/lay-gia-san-pham', 'UrlParserController@getPrice')->name('urlparser.getprice');

	Route::get('/lien-he', 'HomeController@contact')->name('home.contact');
	Route::get('/tin-tuc' , 'HomeController@archive')->name('home.archive')->defaults('type','tin-tuc');
	Route::get('/thong-tin-can-biet' , 'HomeController@archive')->name('home.archive')->defaults('type','thong-tin-can-biet');
	Route::get('/ho-tro-khach-hang' , 'HomeController@archive')->name('home.archive')->defaults('type','ho-tro-khach-hang');
	Route::get('/kiem-tra-don-hang', 'CartController@tracking')->name('cart.tracking');

	Route::get('/thanh-toan', 'CartController@checkOut')->name('cart.checkout');
	Route::post('/thanh-toan', 'CartController@placeOrder')->name('cart.placeorder');
	Route::get('/thankyou', 'CartController@thankYou')->name('cart.thankyou');

	Route::get('/gio-hang', 'CartController@index')->name('cart.index');
	Route::get('/gio-hang/load', 'CartController@miniCart')->name('cart.mini_cart');
	Route::get('/gio-hang/delete-all', 'CartController@deleteAll')->name('cart.delete_all');
	Route::post('/gio-hang/add', 'CartController@addToCart')->name('cart.add');
	Route::post('/gio-hang/delete', 'CartController@deleteCart')->name('cart.delete');
	Route::post('/gio-hang/update', 'CartController@updateCart')->name('cart.update');
	Route::post('/gio-hang/coupon', 'CartController@coupon')->name('cart.coupon');

	Route::get('/viewed', 'HomeController@viewed')->name('home.viewed');
	Route::get('/wishlist', 'WishListController@index')->name('wishlist.index');
	Route::post('/wishlist/add', 'WishListController@addToWishList')->name('wishlist.add');
	Route::post('/wishlist/delete', 'WishListController@deleteWishList')->name('wishlist.delete');

	Route::get('/tim-kiem/{slug}' , 'HomeController@search')->name('home.search');

	Route::get('/coupon' , 'HomeController@coupon')->name('home.coupon');

	Route::get('/khuyen-mai' , 'HomeController@promotion')->name('home.promotion');
	Route::get('/hang-san-xuat/{slug}' , 'HomeController@supplier')->name('home.supplier');
	Route::get('/san-pham-khuyen-mai' , 'HomeController@sales')->name('home.sales');
	Route::get('/san-pham-da-xem' , 'HomeController@viewed')->name('home.viewed');
	Route::get('/ve-sinh-may-lanh' , 'HomeController@vesinhmaylanh')->name('home.vesinhmaylanh');

	Route::get('/sitemap.xml' , 'HomeController@sitemapIndex')->name('home.sitemap.index');
	Route::get('/{slug}-sitemap.xml' , 'HomeController@sitemap')->name('home.sitemap');

	Route::get('/{slug}' , 'HomeController@product')->name('home.product');

	Route::get('/{type}/{slug}.html' , 'HomeController@page')->name('home.page');
	Route::get('/{type}/{slug}' , 'HomeController@category')->name('home.category');
	Route::get('/{type}' , 'HomeController@archive')->name('home.archive');

});