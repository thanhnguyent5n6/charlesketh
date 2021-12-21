<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Order;
use App\Product;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function index(Request $request){
    	$this->_data['order_count'] = Order::count();
    	$this->_data['order_sum'] = Order::sum('subtotal');
    	$this->_data['product_count'] = Product::count();
    	return view('admin.layouts.dashboard',$this->_data);
    }
}
