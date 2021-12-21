<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Coupon;

use DateTime;

class CouponController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $this->_data['items'] = Coupon::orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.coupons.index',$this->_data);
    }
    
    public function create(){
        return view('admin.coupons.create');
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code',
            'coupon_amount' =>  'required|numeric|min:1',
            'number_of_uses'	=>	'required|numeric|min:1',
        ], [
            'code.required'           => 'Vui lòng nhập Mã coupon',
            'code.unique'             => 'Mã coupon đã tồn tại, vui lòng nhập mã khác',
            'coupon_amount.required'  => 'Vui lòng nhập số tiền coupon',
            'coupon_amount.min'      => 'Số tiền coupon phải lớn hơn hoặc bằng :min',
            'number_of_uses.required' => 'Vui lòng nhập số lần sử dụng',
            'number_of_uses.min'      => 'Số lần sử dụng phải lớn hơn hoặc bằng :min',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $coupon  = new Coupon;

            if($request->data){
                foreach($request->data as $field => $value){
                    $coupon->$field = $value;
                }
            }
            $coupon->code                   = strtoupper($request->code);
            $coupon->coupon_amount          = floatval(str_replace('.', '', $request->coupon_amount));
            $coupon->number_of_uses         = (int)str_replace('.', '', $request->number_of_uses);
            $coupon->min_restriction_amount = floatval(str_replace('.', '', $request->min_restriction_amount));
            $coupon->max_restriction_amount = floatval(str_replace('.', '', $request->max_restriction_amount));

            $coupon->priority       = (int)str_replace('.', '', $request->priority);
            $coupon->status         = ($request->status) ? implode(',',$request->status) : '';
            $coupon->created_at     = new DateTime();
            $coupon->updated_at     = new DateTime();
            $coupon->save();
            
            return redirect()->route('admin.coupon.index')->with('success','Thêm dữ liệu <b>'.$coupon->code.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Coupon::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.coupons.edit',$this->_data);
        }
        return redirect()->route('admin.coupon.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code,'.$id,
            'coupon_amount' =>  'required|numeric|min:1',
            'number_of_uses'    =>  'required|numeric|min:1',
        ], [
            'code.required'           => 'Vui lòng nhập Mã coupon',
            'code.unique'             => 'Mã coupon đã tồn tại, vui lòng nhập mã khác',
            'coupon_amount.required'  => 'Vui lòng nhập số tiền coupon',
            'coupon_amount.min'      => 'Số tiền coupon phải lớn hơn hoặc bằng :min',
            'number_of_uses.required' => 'Vui lòng nhập số lần sử dụng',
            'number_of_uses.min'      => 'Số lần sử dụng phải lớn hơn hoặc bằng :min',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $coupon = Coupon::find($id);
            if ($coupon !== null) {
                
                if($request->data){
                    foreach($request->data as $field => $value){
                        $coupon->$field = $value;
                    }
                }
                $coupon->coupon_amount          = floatval(str_replace('.', '', $request->coupon_amount));
                $coupon->number_of_uses         = (int)str_replace('.', '', $request->number_of_uses);
                $coupon->min_restriction_amount = floatval(str_replace('.', '', $request->min_restriction_amount));
                $coupon->max_restriction_amount = floatval(str_replace('.', '', $request->max_restriction_amount));

                $coupon->priority       = (int)str_replace('.', '', $request->priority);
                $coupon->status         = ($request->status) ? implode(',',$request->status) : '';
                $coupon->created_at     = new DateTime();
                $coupon->updated_at     = new DateTime();
                $coupon->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$coupon->code.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $coupon = Coupon::find($id);
        $deleted = $coupon->display_name;
        if ($coupon !== null) {
            if( $coupon->delete() ){
                return redirect()->route('admin.coupon.index')->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.coupon.index')->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.coupon.index')->with('danger', 'Dữ liệu không tồn tại');
    }
}
