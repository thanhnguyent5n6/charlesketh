<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostLanguage;
use App\Category;
use App\Attribute;
use App\MediaLibrary;

use DateTime;

class PostController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.post');
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

    public function index(Request $request){
        $whereRaw = "A.type='".$this->_data['type']."'";
        $whereRaw .= $request->category_id ? " and A.category_id=".$request->category_id : "";
        $whereRaw .= $request->title ? " and B.title like '%".$request->title."%'" : "";
        $whereRaw .= $request->created_at ? " and A.created_at like '%".$request->created_at."%'" : "";
        $whereRaw .= $request->status ? " and FIND_IN_SET('".$request->status."',A.status)" : "";

        $this->_data['oldInput'] = $request->all();
        $this->_data['categories'] = $this->getCategories($this->_data['type']);
        $this->_data['items'] = DB::table('posts as A')
            ->leftjoin('post_languages as B', 'A.id','=','B.post_id')
            ->leftjoin('category_languages as C', 'A.category_id','=','C.category_id')
            ->select('A.*','B.title','C.title as category')
            ->whereRaw($whereRaw)
            ->where('B.language', $this->_data['default_language'])
            ->where('C.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);
        return view('admin.posts.index',$this->_data);
    }
    
    public function create(){
        $this->_data['categories'] = $this->getCategories($this->_data['type']);
        $this->_data['tags'] = $this->getAttributes('post_tags');
        return view('admin.posts.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'dataL.vi.title'   => 'required',
            'image'            => 'image|max:2048',
            'data.category_id' => 'exists:categories,id'
        ], [
            'dataL.vi.title.required'   => 'Vui lòng nhập Tên Bài Viết',
            'image.image'               => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max'                 => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
            'data.category_id.exists'   => 'Vui lòng chọn Danh mục',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $post  = new Post;

            if($request->data){
                foreach($request->data as $field => $value){
                    $post->$field = $value;
                }
            }

            if($request->hasFile('image')){
                $post->image = save_image( $this->_data['path'],$request->file('image'),$this->_data['thumbs'] );
            }

            if($request->hasFile('files')){
                $fileuploader = json_decode($request->input('fileuploader-list-files'),true);
                foreach($request->file('files') as $file){
                    $fileName  = $file->getClientOriginalName();

                    if( false !== $key = array_search($fileName, $request->attachment['name']) ){
                        $fileMime  = $file->getClientMimeType();
                        $fileSize      = $file->getClientSize();
                        $imageName = save_image( $this->_data['path'],$file, null);

                        if( isset($fileuploader[$key]['editor']) ){
                            $newImg  = Image::make( public_path($this->_data['path'].'/'.$imageName) );
                            if( @$fileuploader[$key]['editor']['rotation'] ){
                                $rotation = -(int)$fileuploader[$key]['editor']['rotation'];
                                $newImg->rotate($rotation);
                            }
                            if( @$fileuploader[$key]['editor']['crop'] ){
                                $width  = round($fileuploader[$key]['editor']['crop']['width']);
                                $height = round($fileuploader[$key]['editor']['crop']['height']);
                                $left   = round($fileuploader[$key]['editor']['crop']['left']);
                                $top    = round($fileuploader[$key]['editor']['crop']['top']);
                                $newImg->crop($width,$height,$left,$top);
                            }
                            $newImg->save( public_path($this->_data['path'].'/'.$imageName) );
                        }

                        $media = MediaLibrary::create([
                            'image' => $imageName,
                            'alt'   => $request->attachment['alt'][$key],
                            'editor' => isset($fileuploader[$key]['editor']) ? $fileuploader[$key]['editor'] : '',
                            'mime_type' => $fileMime,
                            'type' => $this->_data['type'],
                            'size' => $fileSize,
                            'priority'   => $request->attachment['priority'][$key],
                        ]);
                        $media_list_id[] = $media->id;
                        unset($fileuploader[$key]);
                    }
                }
                $post->attachments = implode(',',$media_list_id);
            }
            
            $post->priority       = (int)str_replace('.', '', $request->priority);
            $post->status         = ($request->status) ? implode(',',$request->status) : '';
            $post->user_id        = Auth::id();
            $post->type           = $this->_data['type'];
            $post->created_at     = new DateTime();
            $post->updated_at     = new DateTime();
            $post->save();

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
                $dataL['attributes'] = $request->input('attributes.'.$lang);
                $dataL['language']   = $lang;
                $dataInsert[]        = new PostLanguage($dataL);
            }
            $post->languages()->saveMany($dataInsert);

            $attrs = [];
            // Add Tags
            if ($request->has('tags') && is_array($request->tags) && count($request->tags) > 0) {
                foreach ($request->tags as $tag) {
                    $attrs[$tag] = ['type' => 'post_tags'];
                }
            }
            $post->attribute()->sync($attrs);

            return redirect()->route('admin.post.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$post->languages[0]->title.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Post::find($id);
        if ($this->_data['item'] !== null) {
            $this->_data['categories'] = $this->getCategories($this->_data['type']);
            $this->_data['tags'] = $this->getAttributes('post_tags');
            $this->_data['images'] = $this->getMediaLibrary($this->_data['item']['attachments']);
            return view('admin.posts.edit',$this->_data);
        }
        return redirect()->route('admin.post.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
            'image' => 'image|max:2048',
            'data.category_id' => 'exists:categories,id'
        ], [
            'dataL.vi.title.required'    => 'Vui lòng nhập Tên Bài Viết',
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
            'data.category_id.exists' => 'Vui lòng chọn Danh mục',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $post = Post::find($id);
            if ($post !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $post->$field = $value;
                    }
                }

                if($request->hasFile('image')){
                    delete_image( $this->_data['path'].'/'.$post->image, $this->_data['thumbs'] );
                    $post->image = save_image( $this->_data['path'], $request->file('image'), $this->_data['thumbs'] );
                }

                $fileuploader = json_decode($request->input('fileuploader-list-files'),true);
                $media_list_id = [];
                
                if($request->media){
                    foreach($request->media['id'] as $key => $media_id){
                        $media = MediaLibrary::find($media_id);
                        if( isset($fileuploader[$key]['editor']) ){
                            
                            $newImg = Image::make( public_path($this->_data['path'].'/'. $media->image) );
                            if( @$fileuploader[$key]['editor']['rotation'] ){
                                $rotation = -(int)$fileuploader[$key]['editor']['rotation'];
                                $newImg->rotate($rotation);
                            }
                            if( @$fileuploader[$key]['editor']['crop'] ){
                                $width = round($fileuploader[$key]['editor']['crop']['width']);
                                $height = round($fileuploader[$key]['editor']['crop']['height']);
                                $left = round($fileuploader[$key]['editor']['crop']['left']);
                                $top = round($fileuploader[$key]['editor']['crop']['top']);
                                $newImg->crop($width,$height,$left,$top);
                            }
                            $newImg->save( public_path($this->_data['path'].'/'. $media->image) );
                            $media->editor = $fileuploader[$key]['editor'];
                        }
                        $media->priority = $request->media['priority'][$key];
                        $media->save();
                        $media_list_id[] = $media_id;
                        unset($fileuploader[$key]);
                    }
                    $fileuploader = array_values($fileuploader);
                }

                if($request->hasFile('files')){
                    
                    foreach($request->file('files') as $file){

                        $fileName  = $file->getClientOriginalName();
                        if( false !== $key = array_search($fileName, $request->attachment['name']) ){

                            $fileMime  = $file->getClientMimeType();
                            $fileSize      = $file->getClientSize();
                            
                            $imageName = save_image( $this->_data['path'],$file, null);

                            if( isset($fileuploader[$key]['editor']) ){
                                $newImg  = Image::make( public_path($this->_data['path'].'/'.$imageName) );
                                if( @$fileuploader[$key]['editor']['rotation'] ){
                                    $rotation = -(int)$fileuploader[$key]['editor']['rotation'];
                                    $newImg->rotate($rotation);
                                }
                                if( @$fileuploader[$key]['editor']['crop'] ){
                                    $width  = round($fileuploader[$key]['editor']['crop']['width']);
                                    $height = round($fileuploader[$key]['editor']['crop']['height']);
                                    $left   = round($fileuploader[$key]['editor']['crop']['left']);
                                    $top    = round($fileuploader[$key]['editor']['crop']['top']);
                                    $newImg->crop($width,$height,$left,$top);
                                }
                                $newImg->save( public_path($this->_data['path'].'/'.$imageName) );
                            }

                            $media = MediaLibrary::create([
                                'image' => $imageName,
                                'alt'   => $request->attachment['alt'][$key],
                                'editor' => isset($fileuploader[$key]['editor']) ? $fileuploader[$key]['editor'] : '',
                                'mime_type' => $fileMime,
                                'type' => $this->_data['type'],
                                'size' => $fileSize,
                                'priority'   => $request->attachment['priority'][$key],
                            ]);
                            $media_list_id[] = $media->id;
                            unset($fileuploader[$key]);
                        }
                    }
                    
                }
                $post->attachments    = implode(',',$media_list_id);
                $post->priority       = (int)str_replace('.', '', $request->priority);
                $post->status         = ($request->status) ? implode(',',$request->status) : '';
                $post->type           = $this->_data['type'];
                $post->updated_at     = new DateTime();
                $post->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $postLang = PostLanguage::find($post->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $postLang->$fieldL = $valueL;
                        }
                    }
                    if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == '' ){
                        $postLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                    }else{
                        $postLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                    }
                    $postLang->attributes = $request->input('attributes.'.$lang);
                    $postLang->language   = $lang;
                    $postLang->save();
                    $i++;
                }
                $attrs = [];
                // Add Tags
                if ($request->has('tags') && is_array($request->tags) && count($request->tags) > 0) {
                    foreach ($request->tags as $tag) {
                        $attrs[$tag] = ['type' => 'post_tags'];
                    }
                }
                $post->attribute()->sync($attrs);
                
            
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$post->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $post = Post::find($id);
        $deleted = $post->languages[0]->title;
        if ($post !== null) {
            delete_image($this->_data['path'].'/'.$post->image,$this->_data['thumbs']);
            if( $post->attachments ){
                $arrID = explode(',',$post->attachments);
                $medias = MediaLibrary::select('*')->whereIn('id',$arrID)->get();
                if( $medias !== null ){
                    foreach( $medias as $media ){
                        delete_image($this->_data['path'].'/'.$media->image,$this->_data['thumbs']);
                    }
                    MediaLibrary::destroy($arrID);
                }
            }
            if( $post->delete() ){
                return redirect()->route('admin.post.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.post.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.post.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function getCategories($type){
        return DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.id', 'A.parent', 'B.title')
            ->whereRaw('(A.type = \''.$type.'\' or A.type = \'default\')')
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getAttributes($type){
        return DB::table('attributes as A')
            ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
            ->select('A.*','B.title')
            ->where('A.type',$type)
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getMediaLibrary($attachments){
        $arrID = explode(',',$attachments);
        return MediaLibrary::select('*')->whereIn('id',$arrID)->orderBy('priority','asc')->orderBy('id','desc')->get();
    }
}
