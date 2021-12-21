<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CallBackRequest;
use App\Mail\ContactInformation;
use App\Mail\OrderConfirmation;
use App\Register;
use App\Order;
use App\OrderDetail;

use Cache;
use DateTime;


class AjaxController extends Controller
{

	public function index(Request $request){
        
        switch($request->act){
            
            case 'filters':
                $data = self::filters($request);
                break;

            case 'products':
                $data = self::products($request);
                break;
            case 'cleaning-service':
                $data = self::cleaningService($request);
                break;
            case 'call-back-request':
                $data = self::callBackRequest($request);
                break;
            case 'newsletter':
                $data = self::newsletter($request);
                break;
            case 'contact':
                $data = self::contact($request);
                break;
            case 'comment'; case 'reply':
                $data = self::comment($request);
                break;
        }
        return response()->json($data);
    }

    public function filters($request){
        $sortBy = $request->order_by ? $request->order_by : null;
        $products = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->where('B.language','vi')
            ->where(function($query) use ($request){
                if($request->supplier){
                    $query->whereIn('A.supplier_id', $request->supplier);
                }
            })
            ->where(function($query) use ($request){
                if($request->price){
                    switch ($request->price) {
                        case 1:
                            $query->whereBetween('A.wholesale_price', [1,2000000]);
                            break;
                        case 2:
                            $query->whereBetween('A.wholesale_price', [2000000,4000000]);
                            break;
                        case 3:
                            $query->whereBetween('A.wholesale_price', [4000000,7000000]);
                            break;
                        case 4:
                            $query->whereBetween('A.wholesale_price', [7000000,12000000]);
                            break;
                        case 5:
                            $query->whereBetween('A.wholesale_price', [12000000,25000000]);
                            break;
                        case 6:
                            $query->whereBetween('A.wholesale_price', [25000000,50000000]);
                            break;
                        case 7:
                            $query->whereBetween('A.wholesale_price', [50000000,75000000]);
                            break;
                        case 8:
                            $query->where('A.wholesale_price', '>=', 75000000);
                            break;
                    }
                    
                }
                if($request->filter){
                    $whereRaws = [];
                    foreach ($request->filter as $vals) {
                        $whereRaw = [];
                        foreach ($vals as $val) {
                            if(is_numeric($val)){
                                $whereRaw[] = 'FIND_IN_SET(\''.$val.'\',A.filters)';
                            }else{
                                foreach (explode(',', $val) as $child) {
                                    $whereRaw[] = 'FIND_IN_SET(\''.$child.'\',A.filters)';
                                }
                            }
                            
                        }
                        $whereRaws[] = implode(' OR ',$whereRaw);
                        
                    }
                    $query->WhereRaw(implode(' AND ',$whereRaws));
                }
            })
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.category_id',$request->category_id)
            ->where('A.type','san-pham')
            ->when($sortBy, function ($query, $sortBy) {
                switch ($sortBy) {
                    case 1:
                        return $query->orderBy('B.title','asc');
                        break;
                    case 2:
                        return $query->orderBy('B.title','desc');
                        break;
                    case 3:
                        return $query->orderBy('A.wholesale_price','asc');
                        break;
                    case 4:
                        return $query->orderBy('A.wholesale_price','desc');
                        break;
                    case 5:
                        return $query->orderBy('A.viewed','desc');
                        break;
                    case 6:
                        return $query->orderBy('A.id','desc');
                        break;
                }
            }, function ($query) {
                return $query->orderBy('A.wholesale_price','asc')->orderBy('A.priority','asc')->orderBy('A.id','desc');
            })
            ->get();

        $data['products'] = '';
        if( $products->count() > 0 ){
            foreach($products as $val){
                $data['products'] .= get_template_product($val,'san-pham',4,'p-0');
            }
        }else{
            $data['products'] .= '<div class="col text-center text-danger font-weight-bold py-4">Không tìm thấy sản phẩm</div>';
        }
        $data['products'] .= '';

        // if( $products->count() > 0 ){
        //     $data['products'] .= $products->withPath('/san-pham/elenora-sporer')->links('frontend.default.blocks.paginate');
        // }

        return $data;
    }
    
    public function products($request){

        if($request->category_id){
            $data['products'] = '<div class="tab-pane fade show active" id="tab-'.$request->category_id.'"><div class="row m-0">';
            $whereRaw = 'FIND_IN_SET(\'publish\',A.status) AND A.category_id='.$request->category_id;
        }else{
            $data['products'] = '<div class="tab-pane fade show active" id="tab-0"><div class="row m-0">';
            $whereRaw = 'FIND_IN_SET(\'publish\',A.status) AND FIND_IN_SET(\'index\',A.status)';
        }

        $products = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->whereRaw($whereRaw)
            ->where('B.language','vi')
            ->where('A.type','san-pham')
            ->orderBy('A.wholesale_price','asc')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->limit(12)
            ->get();
            
        if( $products->count() > 0 ){
            foreach($products as $val){
                $data['products'] .= get_template_product($val,'san-pham',4,'p-0');
            }
        }else{
            $data['products'] .= '<div class="col text-center text-danger font-weight-bold py-4">Không tìm thấy sản phẩm</div>';
        }
        $data['products'] .= '</div></div>';

        return $data;
    }

    public function cleaningService($request){
        $valid = Validator::make($request->all(), [
            'name'  =>  'required',
            'phone' => [
                'required',
                'digits:10',
                // Rule::unique('registers')->where(function ($query) use($request) {
                //     $query->where('phone', $request->phone)->where('type', $request->type);
                // })
            ],
        ], [
            'name.required' => __('validation.required', ['attribute'=>__('site.name')]),
            'phone.required' =>  'Vui lòng nhập Số điện thoại',
            'phone.digits' =>  'Số điện thoại phải có :digits chữ số',
            // 'phone.unique' =>  'Số điện thoại đã được đăng ký, vui lòng sử dụng số khác',
        ]);

        $data['type'] = 'danger';
        $data['icon'] = 'warning';

        if ($valid->fails()) {
            $data['message'] = $valid->errors()->first();
            return $data;
        } else {

            $client_ip = $request->getClientIp();
            if(Cache::has($client_ip.'_cleaningService')){
                $data['message'] = __('site.contact_wait');
                return $data;
            }else{
                Cache::add($client_ip.'_cleaningService',$request->phone,10);
            }

            $data_insert['name'] = $request->name;
            $data_insert['phone'] = $request->phone;
            $data_insert['address'] = implode(',', $request->address);
            $data_insert['type'] = $request->type;
            $data_insert['status'] = '';
            $data_insert['created_at'] = new DateTime();
            $data_insert['updated_at'] = new DateTime();
            $contact = Register::create($data_insert);
            if($contact){
                $data['type'] = 'success';
                $data['icon'] = 'check';
                $data['message'] = 'Đăng ký thành công! Chúng tôi sẽ gọi lại cho bạn trong thời gian sớm nhất.';
            }else{
                $data['message'] = __('site.sign_up_fail');
            }
        }
        return $data;
    }
    
    public function callBackRequest($request){
        $valid = Validator::make($request->all(), [
            'phone' => [
                'required',
                'digits:10',
                // Rule::unique('registers')->where(function ($query) use($request) {
                //     $query->where('phone', $request->phone)->where('type', $request->type);
                // })
            ],
        ], [
            'phone.required' =>  'Vui lòng nhập Số điện thoại',
            'phone.digits' =>  'Số điện thoại phải có :digits chữ số',
            // 'phone.unique' =>  'Số điện thoại đã được đăng ký, vui lòng sử dụng số khác',
        ]);

        $data['type'] = 'danger';
        $data['icon'] = 'warning';

        if ($valid->fails()) {
            $data['message'] = $valid->errors()->first();
            return $data;
        } else {

            $client_ip = $request->getClientIp();
            if(Cache::has($client_ip.'_callBackRequest_'.str_slug($request->title) )){
                $data['message'] = __('site.contact_wait');
                return $data;
            }else{
                Cache::add($client_ip.'_callBackRequest_'.str_slug($request->title),$request->phone,10);
            }

            $data_insert['title'] = $request->title;
            $data_insert['name'] = $request->name;
            $data_insert['phone'] = $request->phone;
            $data_insert['type'] = $request->type;
            $data_insert['status'] = '';
            $data_insert['created_at'] = new DateTime();
            $data_insert['updated_at'] = new DateTime();
            $contact = Register::create($data_insert);
            if($contact){
                $data['type'] = 'success';
                $data['icon'] = 'check';
                $data['close_modal'] = 'true';
                $data['message'] = 'Đăng ký thành công! Chúng tôi sẽ gọi lại cho bạn trong thời gian sớm nhất.';
                if(@config('settings.email_username') !='') Mail::to(config('settings.email_to'))->send(new CallBackRequest($contact));
            }else{
                $data['message'] = __('site.sign_up_fail');
            }
        }
        return $data;
    }
    
    public function newsletter($request){
        $valid = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('registers')->where(function ($query) use($request) {
                    $query->where('email', $request->email)->where('type', $request->type);
                })
            ],
        ], [
            'email.required' => __('validation.required', ['attribute'=>'Email']),
            'email.email' => __('validation.email', ['attribute'=>'Email']),
            'email.unique' => __('validation.unique', ['attribute'=>'Email']),
        ]);

        $data['type'] = 'danger';
        $data['icon'] = 'warning';

        if ($valid->fails()) {
            $data['message'] = $valid->errors()->first();
            return $data;
        } else {

            $data_insert['title'] = "Đăng ký nhận bản tin";
            $data_insert['name'] = $request->name ? $request->name : null;
            $data_insert['email'] = $request->email;
            $data_insert['gender'] = (int)$request->gender;
            $data_insert['type'] = $request->type;
            $data_insert['created_at'] = new DateTime();
            $data_insert['updated_at'] = new DateTime();

            if(DB::table('registers')->insert($data_insert)){
                $data['type'] = 'success';
                $data['icon'] = 'check';
                $data['message'] = __('site.sign_up_success');
            }else{
                $data['message'] = __('site.sign_up_fail');
            }
        }
    	return $data;
    }

    public function contact($request){
        $valid = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ], [
            'name.required' => __('validation.required', ['attribute'=>__('site.name')]),
            'email.required' => __('validation.required', ['attribute'=>'Email']),
            'email.email' => __('validation.email', ['attribute'=>'Email']),
            'subject.required' => __('validation.required', ['attribute'=>__('site.subject')]),
            'message.required' => __('validation.required', ['attribute'=>__('site.message')]),
        ]);

        $data['type'] = 'danger';
        $data['icon'] = 'warning';

        if ($valid->fails()) {
            $data['message'] = $valid->errors()->first();
            return $data;
        } else {

            $client_ip = $request->getClientIp();
            if(Cache::has($client_ip.'_contact')){
                $data['message'] = __('site.contact_wait');
                return $data;
            }else{
                Cache::add($client_ip.'_contact',$request->email,10);
            }

            $data_insert['title'] = $request->subject;
            $data_insert['name'] = $request->name;
            $data_insert['email'] = $request->email;
            $data_insert['description'] = $request->message;
            $data_insert['type'] = $request->type;
            $data_insert['created_at'] = new DateTime();
            $data_insert['updated_at'] = new DateTime();
            $contact = Register::create($data_insert);
            if($contact){
                $data['type'] = 'success';
                $data['icon'] = 'check';
                $data['message'] = __('site.contact_success');
                if(@config('settings.email_username') !='') Mail::to(config('settings.email_to'))->send(new ContactInformation($contact));
            }else{
                $data['message'] = __('site.contact_fail');
            }
        }
        return $data;
    }

    public function comment($request){
        $valid = Validator::make($request->all(), [
            'score' => 'required|integer|between:1,5',
            'description' => 'required',
            
        ], [
            'score.required' => 'Yêu cầu nhập vào điểm số',
            'score.between' => 'Vui lòng chỉ nhập từ :min tới :max khi chấm điểm',
            'description.required' => __('validation.required', ['attribute'=>__('site.content')]),
        ]);

        $data['type'] = 'danger';
        $data['icon'] = 'warning';

        if ($valid->fails()) {
            $data['message'] = $valid->errors()->first();
            return $data;
        } else {

            $client_ip = $request->getClientIp();
            $table = @$request->category_id ? 'category' : @$request->product_id ? 'product' : 'post' ;
            $id = @$request->category_id ? @$request->category_id : (@$request->product_id ? @$request->product_id : @$request->post_id) ;
            if($request->act == 'comment'){
                if(Cache::has($client_ip.'_comment_'.$table.'_'.$id)){
                    $data['message'] = __('site.comment_wait', ['attribute'=>( $table == 'category' ? __('site.category') : $table == 'product' ? __('site.product') : __('site.post') )] );
                    return $data;
                }else{
                    Cache::add($client_ip.'_comment_'.$table.'_'.$id,$id,10);
                }
            }elseif($request->act == 'reply'){
                $data['remove_element'] = 'true';
                if(Cache::has($client_ip.'_reply_'.$table.'_'.(int)$request->parent)){
                    $data['message'] = __('site.reply_wait');
                    return $data;
                }else{
                    Cache::add($client_ip.'_reply_'.$table.'_'.(int)$request->parent,(int)$request->parent,10);
                }
            }

            $data_insert['parent'] = (int)$request->parent;
            // $data_insert['category_id'] = ($request->category_id) ? $request->category_id : null ;
            $data_insert['product_id'] = ($request->product_id) ? $request->product_id : null ;
            $data_insert['post_id'] = ($request->post_id) ? $request->post_id : null ;
            $data_insert['member_id'] = auth()->guard('member')->check() ? auth()->guard('member')->id() : null ;
            $data_insert['name'] = $request->name;
            $data_insert['email'] = $request->email;
            $data_insert['title'] = $request->title;
            $data_insert['description'] = $request->description;
            $data_insert['score'] = $request->score;
            $data_insert['status'] = '';
            $data_insert['type'] = $request->type;
            $data_insert['created_at'] = new DateTime();
            $data_insert['updated_at'] = new DateTime();

            if(DB::table('comments')->insert($data_insert)){
                $data['type'] = 'success';
                $data['icon'] = 'check';
                $data['message'] = __('site.comment_success');
            }else{
                $data['message'] = __('site.comment_fail');
            }
        }
        return $data;
    }
}
