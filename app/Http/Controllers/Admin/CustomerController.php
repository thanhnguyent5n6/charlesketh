<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Customer;

use DateTime;
use Carbon\Carbon;

class CustomerController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.customer');
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $this->_data['items'] = Customer::where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.customers.index',$this->_data);
    }

    public function create(){
        return view('admin.customers.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.phone' => 'required|unique:customers,phone',
        ], [
            'data.name.required' => 'Vui lòng nhập Họ Tên',
            'data.phone.required' => 'Vui lòng nhập Số Điện Thoại',
            'data.phone.unique' => 'Số Điện Thoại này đã có, vui lòng chọn số khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $customer  = new Customer;

            if($request->data){
                foreach($request->data as $field => $value){
                    $customer->$field = $value;
                }
            }
            $customer->birthday       = $request->birthday ? $request->birthday.'' : null;
            $customer->province_id    = (int)$customer->province_id;
            $customer->district_id    = (int)$customer->district_id;
            // $customer->point          = (float)str_replace('.', '', $request->point);
            $customer->priority       = (int)str_replace('.', '', $request->priority);
            $customer->status         = ($request->status) ? implode(',',$request->status) : '';
            $customer->type           = $this->_data['type'];;
            $customer->created_at     = new DateTime();
            $customer->updated_at     = new DateTime();
            $customer->save();

            return redirect()->route('admin.customer.index')->with('success', 'Thêm người dùng <b>'. $customer->name .'</b> thành công');
        }
    }

    public function edit($id){
        $this->_data['item'] = Customer::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.customers.edit',$this->_data);
        }
        return redirect()->route('admin.customer.index')->with('danger', 'Không tìm thấy người dùng này');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.phone' => 'required|unique:customers,phone,'.$id,
        ], [
            'data.name.required' => 'Vui lòng nhập Họ Tên',
            'data.phone.required' => 'Vui lòng nhập Số Điện Thoại',
            'data.phone.unique' => 'Số Điện Thoại này đã có, vui lòng chọn số khác',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $customer = Customer::find($id);
            if ($customer !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $customer->$field = $value;
                    }
                }
                $customer->birthday       = $request->birthday ? $request->birthday.( strlen($request->birthday) < 15 ? '' : '') : null;
                $customer->province_id    = (int)$customer->province_id;
                $customer->district_id    = (int)$customer->district_id;
                // $customer->point          = (float)str_replace('.', '', $request->point);
                $customer->priority       = (int)str_replace('.', '', $request->priority);
                $customer->status         = ($request->status) ? implode(',',$request->status) : '';
                $customer->updated_at     = new DateTime();
                $customer->save();
                
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$customer->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Không tìm thấy người dùng này');
        }
    }

    public function delete($id){
        $customer = Customer::find($id);
        if ($customer !== null) {
            $customer->delete();
            return redirect()->route('admin.customer.index')->with('success', 'Xóa người dùng <b>'. $customer->name .'</b> thành công');
        }
        return redirect()->route('admin.customer.index')->with('danger', 'Không tìm thấy người dùng này');
    }

}
