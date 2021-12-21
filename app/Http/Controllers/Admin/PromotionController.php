<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Promotion;

use DateTime;

class PromotionController extends Controller
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
        $this->_data['items'] = Promotion::orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.promotions.index',$this->_data);
    }
    
    public function create(){
        $this->_data['categories'] = $this->getCategories('san-pham');
        return view('admin.promotions.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'coupon_amount' =>  'required',
        ], [
            'coupon_amount.required'  => 'Vui lòng nhập số tiền khuyến mãi',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $promotion  = new Promotion;

            if($request->data){
                foreach($request->data as $field => $value){
                    $promotion->$field = $value;
                }
            }
            if( !isset($request->data['slug']) || $request->data['slug'] == ''){
                $promotion->slug       = str_slug($request->data['title']);
            } else {
                $promotion->slug       = str_slug($request->data['slug']);
            }
            $promotion->coupon_amount          = floatval(str_replace('.', '', $request->coupon_amount));
            $promotion->category_id         = ($request->category_id) ? implode(',',$request->category_id) : '';
            $promotion->product_id         = ($request->product_id) ? implode(',',$request->product_id) : '';
            $promotion->product_limit         = ($request->product_limit) ? implode(',',$request->product_limit) : '';

            $promotion->priority       = (int)str_replace('.', '', $request->priority);
            $promotion->status         = ($request->status) ? implode(',',$request->status) : '';
            $promotion->created_at     = new DateTime();
            $promotion->updated_at     = new DateTime();
            $promotion->save();
            
            return redirect()->route('admin.promotion.index')->with('success','Thêm dữ liệu <b>'.$promotion->code.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Promotion::find($id);
        $this->_data['products'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->select('A.id','B.title')
            ->whereIn('A.id', ($this->_data['item']->product_id ? explode(',',$this->_data['item']->product_id) : []) )
            ->where('B.language', 'vi')
            ->where('A.type','san-pham')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();

        $this->_data['limits'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->select('A.id','B.title')
            ->whereIn('A.id', ($this->_data['item']->product_limit ? explode(',',$this->_data['item']->product_limit) : []) )
            ->where('B.language', 'vi')
            ->where('A.type','san-pham')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
        $this->_data['categories'] = $this->getCategories('san-pham');
        if ($this->_data['item'] !== null) {
            return view('admin.promotions.edit',$this->_data);
        }
        return redirect()->route('admin.promotion.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'coupon_amount' =>  'required',
        ], [
            'coupon_amount.required'  => 'Vui lòng nhập số tiền khuyến mãi',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $promotion = Promotion::find($id);
            if ($promotion !== null) {
                
                if($request->data){
                    foreach($request->data as $field => $value){
                        $promotion->$field = $value;
                    }
                }
                if( !isset($request->data['slug']) || $request->data['slug'] == ''){
                    $promotion->slug       = str_slug($request->data['title']);
                } else {
                    $promotion->slug       = str_slug($request->data['slug']);
                }
                $promotion->coupon_amount          = floatval(str_replace('.', '', $request->coupon_amount));
                $promotion->category_id         = ($request->category_id) ? implode(',',$request->category_id) : '';
                $promotion->product_id         = ($request->product_id) ? implode(',',$request->product_id) : '';
                $promotion->product_limit         = ($request->product_limit) ? implode(',',$request->product_limit) : '';

                $promotion->priority       = (int)str_replace('.', '', $request->priority);
                $promotion->status         = ($request->status) ? implode(',',$request->status) : '';
                $promotion->created_at     = new DateTime();
                $promotion->updated_at     = new DateTime();
                $promotion->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$promotion->code.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $promotion = Promotion::find($id);
        $deleted = $promotion->display_name;
        if ($promotion !== null) {
            if( $promotion->delete() ){
                return redirect()->route('admin.promotion.index')->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.promotion.index')->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.promotion.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function getCategories($type){
        return DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.id', 'A.parent', 'A.filters', 'B.title')
            ->whereRaw('(A.type = \''.$type.'\')')
            ->where('B.language', 'vi')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }
}
