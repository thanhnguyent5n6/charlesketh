<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Search;
use App\SearchLanguage;

use DateTime;

class SearchController extends Controller
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
        $this->_data['siteconfig']       = config('siteconfig.search');
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
        $this->_data['items'] = DB::table('searchs as A')
            ->leftjoin('search_languages as B', 'A.id','=','B.search_id')
            ->select('A.*','B.title')
            ->where('A.type',$this->_data['type'])
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);
        return view('admin.searchs.index',$this->_data);
    }
    
    public function create(){
    	return view('admin.searchs.create',$this->_data);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
        ], [
            'dataL.vi.title.required'    => 'Vui lòng nhập Tên Thuộc Tính',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $search  = new Search;
            if($request->data){
                foreach($request->data as $field => $value){
                    $search->$field = $value;
                }
            }
            $search->priority   = (int)str_replace('.', '', $request->priority);
            $search->status     = ($request->status) ? implode(',',$request->status) : '';
            $search->type       = $this->_data['type'];
            $search->created_at = new DateTime();
            $search->updated_at = new DateTime();
            $search->save();

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
                $dataInsert[]        = new SearchLanguage($dataL);
            }
            $search->languages()->saveMany($dataInsert);
            return redirect()->route('admin.search.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$search->languages[0]->title.'</b> thành công');
        }
    }

    public function edit($id){
        $this->_data['item'] = Search::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.searchs.edit',$this->_data);
        }
        return redirect()->route('admin.search.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
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
            $search = Search::find($id);
            if ($search !== null) {
                if($request->data){
                	foreach($request->data as $field => $value){
                        $search->$field = $value;
                    }
                }
                
                $search->priority   = (int)str_replace('.', '', $request->priority);
                $search->status     = ($request->status) ? implode(',',$request->status) : '';
                $search->type       = $this->_data['type'];
                $search->updated_at = new DateTime();
                $search->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $searchLang = SearchLanguage::find($search->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $searchLang->$fieldL = $valueL;
                        }
                    }
                    if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == '' ){
                        $searchLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                    }else{
                        $searchLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                    }
                    $searchLang->language   = $lang;
                    $searchLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$search->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
        
    }

    public function delete($id){
    	$search = Search::find($id);
        $deleted = $search->languages[0]->title; 
        if ($search !== null) {
            if( $search->delete() ){
                return redirect()->route('admin.search.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.search.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.search.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }



}
