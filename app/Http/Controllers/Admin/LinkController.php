<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Link;
use App\LinkLanguage;

use DateTime;

class LinkController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.link');
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
        $this->_data['items'] = DB::table('links as A')
            ->leftjoin('link_languages as B', 'A.id','=','B.link_id')
            ->select('A.*','B.title')
            ->where('A.type',$this->_data['type'])
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        return view('admin.links.index',$this->_data);
    }
    
    public function create(){
        return view('admin.links.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'image'            => 'image|max:2048'
        ], [
            'image.image'               => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max'                 => 'Dung lượng vượt quá giới hạn cho phép là :max KB'
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $link  = new Link;

            if($request->data){
                foreach($request->data as $field => $value){
                    $link->$field = $value;
                }
            }

            if($request->hasFile('image')){
                $link->image = save_image( $this->_data['path'],$request->file('image'),$this->_data['thumbs'] );
            }
            
            $link->priority       = (int)str_replace('.', '', $request->priority);
            $link->status         = ($request->status) ? implode(',',$request->status) : '';
            $link->type           = $this->_data['type'];
            $link->created_at     = new DateTime();
            $link->updated_at     = new DateTime();
            $link->save();

            $dataL = [];
            $dataInsert = [];
            foreach($this->_data['languages'] as $lang => $val){
                if($request->dataL[$lang]){
                    foreach($request->dataL[$lang] as $fieldL => $valueL){
                        $dataL[$fieldL] = $valueL;
                    }
                }
                $dataL['language']   = $lang;
                $dataInsert[]        = new LinkLanguage($dataL);
            }
            $link->languages()->saveMany($dataInsert);

            return redirect()->route('admin.link.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$link->languages[0]->title.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Link::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.links.edit',$this->_data);
        }
        return redirect()->route('admin.link.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'image' => 'image|max:2048',
        ], [
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $link = Link::find($id);
            if ($link !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $link->$field = $value;
                    }
                }

                if($request->hasFile('image')){
                    delete_image( $this->_data['path'].'/'.$link->image, $this->_data['thumbs'] );
                    $link->image = save_image( $this->_data['path'], $request->file('image'), $this->_data['thumbs'] );
                }

                
                $link->priority       = (int)str_replace('.', '', $request->priority);
                $link->status         = ($request->status) ? implode(',',$request->status) : '';
                $link->type           = $this->_data['type'];
                $link->updated_at     = new DateTime();
                $link->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $linkLang = LinkLanguage::find($link->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $linkLang->$fieldL = $valueL;
                        }
                    }
                    $linkLang->language   = $lang;
                    $linkLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$link->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $link = Link::find($id);
        $deleted = $link->languages[0]->title;
        if ($link !== null) {
            delete_image($this->_data['path'].'/'.$link->image,$this->_data['thumbs']);
            if( $link->delete() ){
                return redirect()->route('admin.link.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.link.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.link.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }
}
