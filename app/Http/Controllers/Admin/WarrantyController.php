<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Warranty;
use App\Order;
use App\OrderDetail;

use DateTime;
use Carbon\Carbon;

class WarrantyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request)
    {
        $this->_data['type'] = (isset($request->type) && $request->type !='') ? $request->type : 'default';
        $this->_data['siteconfig'] = config('siteconfig.warranty');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){

        $this->_data['oldInput'] = $request->all();

        $items = Warranty::where('type',$this->_data['type'])
            ->orderBy('priority','asc')
            ->orderBy('id','desc');

        if( $keyword = $request->keyword ){
        	$items->where(function($query) use ($keyword){
        		$query->where('order_code',$keyword)
        			->orWhere('warranty_code','like','%'.$keyword.'%');
        	});
        }
        if( $request->from_at ){
            $items->where('created_at', '>=', $request->from_at);
        }if( $request->to_at ){
            $items->where('created_at', '<=', $request->to_at);
        }

        $this->_data['items'] = $items->paginate(25);

        return view('admin.warranties.index',$this->_data);
    }

    public function ajax(Request $request){
        if($request->ajax()){
            $items = Order::whereRaw("(code LIKE '%".$request->q."%' OR phone LIKE '%".$request->q."%' OR name LIKE '%".$request->q."%') AND FIND_IN_SET('publish',status)")->get();
            $data['items'] = [];
            if ( $items !== null ) {
            	foreach($items as $key => $item){
            		$data['items'][$key]['id'] = $item->id;
            		$data['items'][$key]['title'] = $item->code;
            		$data['items'][$key]['name'] = $item->name;
            		$data['items'][$key]['email'] = $item->email;
            		$data['items'][$key]['phone'] = $item->phone;
            		$data['items'][$key]['code'] = $item->code;
            		$products = $item->details()->get();
	                if($products !== null){
	                    foreach($products as $k => $val){
	                        $data['items'][$key]['products'][$k]['id']       =  $val->product_id;
	                        $data['items'][$key]['products'][$k]['code']     =  $val->product_code;
	                        $data['items'][$key]['products'][$k]['price']    =  $val->product_price;
	                        $data['items'][$key]['products'][$k]['price_second']    =  $val->product_price_second;
	                        $data['items'][$key]['products'][$k]['qty']      =  $val->product_qty;
	                        $data['items'][$key]['products'][$k]['title']    =  $val->product_title;
	                    }
	                }
            	}
            }
            return response()->json($data);
        }
    }

    public function create(){
        return view('admin.warranties.create',$this->_data);
    }

    public function store(Request $request){
    	$valid = Validator::make($request->all(), [
            'data.order_code'          => 'required',
            // 'warranty_code'          => 'required',
            ], [
            'data.order_code.required' => 'Vui lòng chọn đơn hàng',
            // 'warranty_code.required' => 'Vui lòng quét mã bảo hành',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $warranty  = new Warranty;

            if($request->data){
                foreach($request->data as $field => $value){
                    $warranty->$field = $value;
                }
            }
            $warranty->code = time();
            $warranty->warranty_code =    ($request->warranty_code) ? implode(',',$request->warranty_code) : '';
            $warranty->priority      =    (int)str_replace('.', '', $request->priority);
            $warranty->status        =    ($request->status) ? implode(',',$request->status) : '';
            $warranty->type          =    $this->_data['type'];
            $warranty->user_id       =    Auth::id();
            $warranty->created_at    =    new DateTime();
            $warranty->updated_at    =    new DateTime();
            $warranty->save();
            $warranty->code          =    update_code($warranty->id,'BH');
            $warranty->save();
            return redirect()->route('admin.warranty.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$warranty->code.'</b> thành công');

        }
    }

    public function edit($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $this->_data['item'] = Warranty::find($id);
        } else {
            $this->_data['item'] = Warranty::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            $order = Order::where('code',$this->_data['item']->order_code)->first();
            if($order !== null){

                $this->_data['order']['name'] = $order->name;
                $this->_data['order']['email'] = $order->email;
                $this->_data['order']['phone'] = $order->phone;
                $this->_data['order']['code'] = $order->code;

                $this->_data['products'] = $order->details()->get();
                $products = [];
                if($this->_data['products'] !== null){
                    foreach($this->_data['products'] as $key => $val){
                        $products[$key]['id']       =  $val->product_id;
                        $products[$key]['code']     =  $val->product_code;
                        $products[$key]['price']    =  $val->product_price;
                        $products[$key]['price_second']    =  $val->product_price_second;
                        $products[$key]['qty']      =  $val->product_qty;
                        $products[$key]['title']    =  $val->product_title;
                    }
                    $this->_data['products'] = $products;
                }
            }
            return view('admin.warranties.edit',$this->_data);
        }
        return redirect()->route('admin.warranty.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'data.order_code'          => 'required',
            // 'warranty_code'          => 'required',
            ], [
            'data.order_code.required' => 'Vui lòng chọn đơn hàng',
            // 'warranty_code.required' => 'Vui lòng quét mã bảo hành',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $warranty  = Warranty::find($id);
            if ($warranty !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $warranty->$field = $value;
                    }
                }
                $warranty->warranty_code =    ($request->warranty_code) ? implode(',',$request->warranty_code) : '';
                $warranty->priority      =    (int)str_replace('.', '', $request->priority);
                $warranty->status        =    ($request->status) ? implode(',',$request->status) : '';
                $warranty->type          =    $this->_data['type'];
                $warranty->updated_at    =    new DateTime();
                $warranty->save();
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$warranty->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $warranty = Warranty::find($id);
        } else {
            $warranty = Warranty::where('user_id',Auth::id())->find($id);
        }
        $deleted = $warranty->name;
        if ($warranty !== null) {
            if( $warranty->delete() ){
                return redirect()->route('admin.warranty.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.warranty.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.warranty.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

}
