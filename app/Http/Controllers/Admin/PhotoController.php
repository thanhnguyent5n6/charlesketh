<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Photo;
use App\PhotoLanguage;

use DateTime;

class PhotoController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.photo');
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
        $this->_data['items'] = DB::table('photos as A')
            ->leftjoin('photo_languages as B', 'A.id','=','B.photo_id')
            ->select('A.*','B.title')
            ->where('A.type',$this->_data['type'])
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        // $this->_data['items'] = Photo::where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc')->with([
        //         'languages' => function($query){
        //             $query->select('photo_id','title')->where('language', $this->_data['default_language'] );
        //         }
        //     ])->paginate(25);
        return view('admin.photos.index',$this->_data);
    }
    
    public function create(){
        return view('admin.photos.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'image'            => 'image|max:2048'
        ], [
            'image.image'               => 'Kh??ng ????ng chu???n h??nh ???nh cho ph??p',
            'image.max'                 => 'Dung l?????ng v?????t qu?? gi???i h???n cho ph??p l?? :max KB'
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $photo  = new Photo;

            if($request->data){
                foreach($request->data as $field => $value){
                    $photo->$field = $value;
                }
            }

            if($request->hasFile('image')){
                $photo->image = save_image( $this->_data['path'],$request->file('image'),$this->_data['thumbs'] );
            }
            
            $photo->priority       = (int)str_replace('.', '', $request->priority);
            $photo->status         = ($request->status) ? implode(',',$request->status) : '';
            $photo->type           = $this->_data['type'];
            $photo->created_at     = new DateTime();
            $photo->updated_at     = new DateTime();
            $photo->save();

            $dataL = [];
            $dataInsert = [];
            foreach($this->_data['languages'] as $lang => $val){
                if($request->dataL[$lang]){
                    foreach($request->dataL[$lang] as $fieldL => $valueL){
                        $dataL[$fieldL] = $valueL;
                    }
                }
                $dataL['language']   = $lang;
                $dataInsert[]        = new PhotoLanguage($dataL);
            }
            $photo->languages()->saveMany($dataInsert);

            return redirect()->route('admin.photo.index',['type'=>$this->_data['type']])->with('success','Th??m d??? li???u <b>'.$photo->languages[0]->title.'</b> th??nh c??ng');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Photo::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.photos.edit',$this->_data);
        }
        return redirect()->route('admin.photo.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'image' => 'image|max:2048',
        ], [
            'image.image' => 'Kh??ng ????ng chu???n h??nh ???nh cho ph??p',
            'image.max' => 'Dung l?????ng v?????t qu?? gi???i h???n cho ph??p l?? :max KB',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $photo = Photo::find($id);
            if ($photo !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $photo->$field = $value;
                    }
                }

                if($request->hasFile('image')){
                    delete_image( $this->_data['path'].'/'.$photo->image, $this->_data['thumbs'] );
                    $photo->image = save_image( $this->_data['path'], $request->file('image'), $this->_data['thumbs'] );
                }

                
                $photo->priority       = (int)str_replace('.', '', $request->priority);
                $photo->status         = ($request->status) ? implode(',',$request->status) : '';
                $photo->type           = $this->_data['type'];
                $photo->updated_at     = new DateTime();
                $photo->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $photoLang = PhotoLanguage::find($photo->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $photoLang->$fieldL = $valueL;
                        }
                    }
                    $photoLang->language   = $lang;
                    $photoLang->save();
                    $i++;
                }
                return redirect( $request->redirects_to )->with('success','C???p nh???t d??? li???u <b>'.$photo->languages[0]->title.'</b> th??nh c??ng');
            }
            return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng t???n t???i');
        }
    }

    public function delete($id){
        $photo = Photo::find($id);
        $deleted = $photo->languages[0]->title;
        if ($photo !== null) {
            delete_image($this->_data['path'].'/'.$photo->image,$this->_data['thumbs']);
            if( $photo->delete() ){
                return redirect()->route('admin.photo.index',['type'=>$this->_data['type']])->with('success', 'X??a d??? li???u <b>'.$deleted.'</b> th??nh c??ng');
            }else{
                return redirect()->route('admin.photo.index',['type'=>$this->_data['type']])->with('danger', 'X??a d??? li???u b??? l???i');
            }
        }
        return redirect()->route('admin.photo.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }
}
