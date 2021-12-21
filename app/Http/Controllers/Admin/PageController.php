<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Page;
use App\PageLanguage;

use DateTime;

class PageController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.page');
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

    public function index(){
        $page = Page::where('type', $this->_data['type'])->first();
        if ($page === null) {
            $page  = new Page;
            $page->type           = $this->_data['type'];
            $page->created_at     = new DateTime();
            $page->updated_at     = new DateTime();
            $page->save();

            $dataL = [];
            $dataInsert = [];
            foreach($this->_data['languages'] as $lang => $val){
                $dataL['title'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
                $dataL['slug'] = str_slug($this->_data['siteconfig'][$this->_data['type']]['page-title']);
                $dataL['meta_seo']['title'] = null;
                $dataL['meta_seo']['keywords'] = null;
                $dataL['meta_seo']['description'] = null;
                $dataL['language']   = $lang;
                $dataInsert[]        = new PageLanguage($dataL);
            }
            $page->languages()->saveMany($dataInsert);
        }
        return redirect()->route('admin.page.edit',['id' => $page->id, 'type' => $this->_data['type']]);
    }
    
    public function create(){
        return view('admin.pages.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'dataL.vi.title'   => 'required',
            'image'            => 'image|max:2048'
        ], [
            'dataL.vi.title.required'   => 'Vui lòng nhập Tên Bài Viết',
            'image.image'               => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max'                 => 'Dung lượng vượt quá giới hạn cho phép là :max KB'
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $page  = new Page;

            if($request->data){
                foreach($request->data as $field => $value){
                    $page->$field = $value;
                }
            }

            if($request->hasFile('image')){
                $page->image = save_image( $this->_data['path'],$request->file('image'),$this->_data['thumbs'] );
            }
            
            $page->priority       = (int)str_replace('.', '', $request->priority);
            $page->status         = ($request->status) ? implode(',',$request->status) : '';
            $page->type           = $this->_data['type'];
            $page->created_at     = new DateTime();
            $page->updated_at     = new DateTime();
            $page->save();

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
                $dataInsert[]        = new PageLanguage($dataL);
            }
            $page->languages()->saveMany($dataInsert);

            return redirect()->route('admin.page.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$page->languages[0]->title.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Page::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.pages.edit',$this->_data);
        }
        return redirect()->route('admin.page.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
            'image' => 'image|max:2048'
        ], [
            'dataL.vi.title.required'    => 'Vui lòng nhập Tên Bài Viết',
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB'
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $page = Page::find($id);
            if ($page !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $page->$field = $value;
                    }
                }

                if($request->hasFile('image')){
                    delete_image( $this->_data['path'].'/'.$page->image, $this->_data['thumbs'] );
                    $page->image = save_image( $this->_data['path'], $request->file('image'), $this->_data['thumbs'] );
                }

                $page->priority       = (int)str_replace('.', '', $request->priority);
                $page->status         = ($request->status) ? implode(',',$request->status) : '';
                $page->type           = $this->_data['type'];
                $page->updated_at     = new DateTime();
                $page->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $pageLang = PageLanguage::find($page->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $pageLang->$fieldL = $valueL;
                        }
                    }
                    if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == '' ){
                        $pageLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                    }else{
                        $pageLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                    }
                    $pageLang->language   = $lang;
                    $pageLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$page->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $page = Page::find($id);
        $deleted = $page->languages[0]->title;
        if ($page !== null) {
            delete_image($this->_data['path'].'/'.$page->image,$this->_data['thumbs']);
            if( $page->delete() ){
                return redirect()->route('admin.page.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.page.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.page.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

}
