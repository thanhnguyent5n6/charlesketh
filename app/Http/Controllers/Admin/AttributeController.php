<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Attribute;
use App\AttributeLanguage;

use DateTime;

class AttributeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request)
    {
        $this->_data['type']             = (isset($request->type) && $request->type !='') ? $request->type : 'default';
        $this->_data['siteconfig']       = config('siteconfig.attribute');
        $this->_data['languages']        = config('siteconfig.languages');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['pageTitle']        = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $this->_data['items'] = DB::table('attributes as A')
            ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
            ->select('A.*','B.title')
            ->where('A.type',$this->_data['type'])
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);
        return view('admin.attributes.index',$this->_data);
    }
    
    public function create(){
    	return view('admin.attributes.create',$this->_data);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
        ], [
            'dataL.vi.title.required'    => 'Vui lòng nhập Tên Thuộc Tính',
        ]);

        if ($valid->fails()) {
            if($request->ajax()){
                return response()->json(['type'=>'danger', 'icon'=>'warning', 'message'=>$valid->errors()->first()]);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $attribute  = new Attribute;
            if($request->data){
                foreach($request->data as $field => $value){
                    $attribute->$field = $value;
                }
            }
            $attribute->regular_price  = floatval(str_replace('.', '', $request->regular_price));
            $attribute->sale_price     = floatval(str_replace('.', '', $request->sale_price));
            $attribute->priority   = (int)str_replace('.', '', $request->priority);
            $attribute->status     = ($request->status) ? implode(',',$request->status) : '';
            $attribute->type       = $this->_data['type'];
            $attribute->created_at = new DateTime();
            $attribute->updated_at = new DateTime();
            $attribute->save();

            $dataL = [];
            $dataInsert = [];
            foreach($this->_data['languages'] as $lang => $val){
                if($request->dataL[$lang]){
                    foreach($request->dataL[$lang] as $fieldL => $valueL){
                        $dataL[$fieldL] = $valueL;
                    }
                }
                if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == ''){
                    $dataL['slug']       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                }else{
                    $dataL['slug']       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                }
                $dataL['language']   = $lang;
                $dataInsert[]        = new AttributeLanguage($dataL);
            }
            $attribute->languages()->saveMany($dataInsert);
            if($request->ajax()){
                $items = get_attributes($this->_data['type']);
                $arrIDs = explode(',',$request->ids);
                $newData = '';
                if($items){
                    foreach($items as $item){
                        $newData .= '<option value="'.$item->id.'" '.( ( $item->id == $attribute->id || in_array($item->id,$arrIDs) ) ? 'selected' : '' ).'> '.$item->title.' </option>';
                    }
                }
                return response()->json(['type'=>'success', 'icon'=>'check', 'message'=>'Thêm dữ liệu <b>'.$attribute->languages[0]->title.'</b> thành công', 'newData'=>$newData]);
            }
            return redirect()->route('admin.attribute.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$attribute->languages[0]->title.'</b> thành công');
        }
    }

    public function edit($id){
        $this->_data['item'] = Attribute::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.attributes.edit',$this->_data);
        }
        return redirect()->route('admin.attribute.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){

        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
        ], [
            'dataL.vi.title.required'    => 'Vui lòng nhập Tên Thuộc Tính',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else { 
            $attribute = Attribute::find($id);
            if ($attribute !== null) {
                if($request->data){
                	foreach($request->data as $field => $value){
                        $attribute->$field = $value;
                    }
                }
                $attribute->regular_price  = floatval(str_replace('.', '', $request->regular_price));
                $attribute->sale_price     = floatval(str_replace('.', '', $request->sale_price));
                $attribute->priority   = (int)str_replace('.', '', $request->priority);
                $attribute->status     = ($request->status) ? implode(',',$request->status) : '';
                $attribute->type       = $this->_data['type'];
                $attribute->updated_at = new DateTime();
                $attribute->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $attributeLang = AttributeLanguage::find($attribute->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $attributeLang->$fieldL = $valueL;
                        }
                    }
                    if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == '' ){
                        $attributeLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                    }else{
                        $attributeLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                    }
                    $attributeLang->language   = $lang;
                    $attributeLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$attribute->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
        
    }

    public function delete($id){
    	$attribute = Attribute::find($id);
        $deleted = $attribute->languages[0]->title; 
        if ($attribute !== null) {
            if( $attribute->delete() ){
                return redirect()->route('admin.attribute.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.attribute.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.attribute.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }



}
