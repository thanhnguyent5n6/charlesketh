<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\CategoryLanguage;
use App\Functions\Facades\Menu;
use DateTime;
use Excel;

class CategoryController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.category');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['path'] = $this->_data['siteconfig']['path'];
        $this->_data['thumbs'] = $this->_data['siteconfig'][$this->_data['type']]['thumbs'];
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $this->_data['items'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.*','B.title')
            ->where('A.priority','>',0)
            ->where('A.type',$this->_data['type'])
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
        return view('admin.categories.index',$this->_data);
    }
    
    public function create(){
        $this->_data['filters'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.id', 'B.title')
            ->where('B.language', $this->_data['default_language'])
            ->where('A.parent',0)
            ->where('A.type','filter')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
        $this->_data['suppliers'] = DB::table('suppliers')
            ->where('type','default')
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
        $this->_data['categories'] = $this->getCategory($this->_data['type']);
    	return view('admin.categories.create',$this->_data);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
            'image' => 'image|max:2048',
        ], [
            'dataL.vi.title.required'    => 'Vui l??ng nh???p T??n Danh M???c',
            'image.image' => 'Kh??ng ????ng chu???n h??nh ???nh cho ph??p',
            'image.max' => 'Dung l?????ng v?????t qu?? gi???i h???n cho ph??p l?? :max KB',
        ]);

        if ($valid->fails()) {
            if($request->ajax()){
                return response()->json(['type'=>'danger', 'icon'=>'warning', 'message'=>$valid->errors()->first()]);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $category  = new Category;
            if($request->data){
                foreach($request->data as $field => $value){
                    $category->$field = $value;
                }
            }
            if($request->hasFile('image')){
                $category->image = save_image($this->_data['path'],$request->file('image'),$this->_data['thumbs']);
            }
            $category->filters    = ($request->filters) ? implode(',',$request->filters) : '';
            $category->suppliers  = ($request->suppliers) ? implode(',',$request->suppliers) : '';
            $category->priority   = (int)str_replace('.', '', $request->priority);
            $category->status     = ($request->status) ? implode(',',$request->status) : '';
            $category->type       = $this->_data['type'];
            $category->created_at = new DateTime();
            $category->updated_at = new DateTime();
            $category->save();

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
                $dataInsert[]        = new CategoryLanguage($dataL);
            }
            $category->languages()->saveMany($dataInsert);
            if($request->ajax()){
                Menu::setMenu($this->getCategory($this->_data['type']));
                $newData = Menu::getMenuSelect(0,0,'',$category->id);
                return response()->json(['type'=>'success', 'icon'=>'check', 'message'=>'Th??m d??? li???u <b>'.$category->languages[0]->title.'</b> th??nh c??ng', 'newData'=>$newData]);
            }
            return redirect()->route('admin.category.index',['type'=>$this->_data['type']])->with('success','Th??m d??? li???u <b>'.$category->languages[0]->title.'</b> th??nh c??ng');
        }
    }

    public function edit($id){
        $this->_data['item'] = Category::find($id);

        $this->_data['filters'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.id', 'B.title')
            ->where('B.language', $this->_data['default_language'])
            ->where('A.parent',0)
            ->where('A.type','filter')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();

        $this->_data['suppliers'] = DB::table('suppliers')
            ->where('type','default')
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();

        $this->_data['categories'] = $this->getCategory($this->_data['type']);
        if ($this->_data['item'] !== null) {
            return view('admin.categories.edit',$this->_data);
        }
        return redirect()->route('admin.category.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
            'image' => 'image|max:2048',
        ], [
            'dataL.vi.title.required'    => 'Vui l??ng nh???p T??n Danh M???c',
            'image.image' => 'Kh??ng ????ng chu???n h??nh ???nh cho ph??p',
            'image.max' => 'Dung l?????ng v?????t qu?? gi???i h???n cho ph??p l?? :max KB',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else { 
            $category = Category::find($id);
            if ($category !== null) {
                if($request->data){
                	foreach($request->data as $field => $value){
                        $category->$field = $value;
                    }
                }
                if($request->hasFile('image')){
                    delete_image($this->_data['path'].'/'.$category->image,$this->_data['thumbs']);
                    $category->image = save_image($this->_data['path'],$request->file('image'),$this->_data['thumbs']);
                }
                $category->filters    = ($request->filters) ? implode(',',$request->filters) : '';
                $category->suppliers  = ($request->suppliers) ? implode(',',$request->suppliers) : '';
                $category->priority   = (int)str_replace('.', '', $request->priority);
                $category->status     = ($request->status) ? implode(',',$request->status) : '';
                $category->type       = $this->_data['type'];
                $category->updated_at = new DateTime();
                $category->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $categoryLang = CategoryLanguage::find($category->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $categoryLang->$fieldL = $valueL;
                        }
                    }
                    if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == '' ){
                        $categoryLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                    }else{
                        $categoryLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                    }
                    $categoryLang->language   = $lang;
                    $categoryLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','C???p nh???t d??? li???u <b>'.$category->languages[0]->title.'</b> th??nh c??ng');
            }
            return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng t???n t???i');
        }
        
    }

    public function delete($id){
    	$category = Category::find($id);

        \App\Product::whereIn('id',$category->products()->pluck('id')->toArray())->update(['category_id' => 1]);
        \App\Post::whereIn('id',$category->posts()->pluck('id')->toArray())->update(['category_id' => 1]);

        $deleted = $category->languages[0]->title;
        if ($category !== null) {
            delete_image($this->_data['path'].'/'.$category->image,$this->_data['thumbs']);
            if( $category->delete() ){
                Category::whereIn('id',$category->children()->pluck('id')->toArray())->update(['parent' => 0]);
                return redirect()->route('admin.category.index',['type'=>$this->_data['type']])->with('success', 'X??a d??? li???u <b>'.$deleted.'</b> th??nh c??ng');
            }else{
                return redirect()->route('admin.category.index',['type'=>$this->_data['type']])->with('danger', 'X??a d??? li???u b??? l???i');
            }
        }
        return redirect()->route('admin.category.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function getCategory($type){
        return DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.id', 'A.parent', 'B.title')
            ->where('A.type',$type)
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function export(Request $request){
        $filename = 'Danh-Sach-Bo-Loc-'.date('dmY');
        $fileExt = $request->extension;
        $whereRaw = "A.type='".$this->_data['type']."' AND A.parent!=0";
        $data = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.*','B.title')
            ->whereRaw($whereRaw)
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.id','asc')
            ->get()->toArray();

        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('B??? l???c', function($sheet) use ($data) {
                $sheet->loadView('excel.categories')->with(['data'=>$data]);
            });
        })->download($fileExt);
    }
}
