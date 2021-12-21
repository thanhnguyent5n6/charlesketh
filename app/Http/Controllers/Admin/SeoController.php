<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Seo;
use App\SeoLanguage;

use DateTime;

class SeoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request)
    {
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['pageTitle'] = "Seo Page";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $this->_data['items'] = DB::table('seos as A')
            ->leftjoin('seo_languages as B', 'A.id','=','B.seo_id')
            ->select('A.*','B.title')
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        return view('admin.seos.index',$this->_data);
    }
    
    public function create(){
        return view('admin.seos.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'dataL.vi.title'   => 'required',
            'data.link'   => 'required',
        ], [
            'dataL.vi.title.required'   => 'Vui lòng nhập Tên',
            'data.link.required'   => 'Vui lòng nhập link seo',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $seo  = new Seo;

            if($request->data){
                foreach($request->data as $field => $value){
                    $seo->$field = $value;
                }
            }

            $seo->priority       = (int)str_replace('.', '', $request->priority);
            $seo->status         = ($request->status) ? implode(',',$request->status) : '';
            $seo->created_at     = new DateTime();
            $seo->updated_at     = new DateTime();
            $seo->save();

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
                $dataInsert[]        = new SeoLanguage($dataL);
            }
            $seo->languages()->saveMany($dataInsert);

            return redirect()->route('admin.seo.index')->with('success','Thêm dữ liệu <b>'.$seo->languages[0]->title.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Seo::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.seos.edit',$this->_data);
        }
        return redirect()->route('admin.seo.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'dataL.vi.title'   => 'required',
            'data.link'   => 'required',
        ], [
            'dataL.vi.title.required'   => 'Vui lòng nhập Tên',
            'data.link.required'   => 'Vui lòng nhập link seo',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $seo = Seo::find($id);
            if ($seo !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $seo->$field = $value;
                    }
                }

                $seo->priority       = (int)str_replace('.', '', $request->priority);
                $seo->status         = ($request->status) ? implode(',',$request->status) : '';
                $seo->updated_at     = new DateTime();
                $seo->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $seoLang = SeoLanguage::find($seo->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $seoLang->$fieldL = $valueL;
                        }
                    }
                    $seoLang->language   = $lang;
                    $seoLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$seo->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $seo = Seo::find($id);
        $deleted = $seo->languages[0]->title;
        if ($seo !== null) {
            if( $seo->delete() ){
                return redirect()->route('admin.seo.index')->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.seo.index')->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.seo.index')->with('danger', 'Dữ liệu không tồn tại');
    }

}
