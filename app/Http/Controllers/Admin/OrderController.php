<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

use App\Order;
use App\OrderDetail;
use App\User;
use App\Member;
use App\Customer;

use Excel;
use DateTime;
use DNS1D;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderAlert;

class OrderController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.order');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){

        $this->_data['oldInput'] = $request->all();

        $items = Order::where('type',$this->_data['type'])
            ->orderBy('priority','asc')
            ->orderBy('id','desc');

        if( $request->status ){
        	if( $request->status == 'construction' ){
        		$items->whereRaw("FIND_IN_SET('".$request->status."',status)");
        	}else{
            	$items->where('status_id', $request->status);
            }
        }

        if( $keyword = $request->keyword ){

        	$productIds = OrderDetail::where('product_code',$keyword)->pluck('order_id')->toArray();
        	if( count($productIds) > 0){
        		$items->whereIn('id',$productIds);
        	}else{
        		$items->where(function($query) use ($keyword){
	        		$query->where('code','like','%'.$keyword.'%')
	        			->orWhere('name','like','%'.$keyword.'%')
	        			->orWhere('phone','like','%'.$keyword.'%')
	        			->orWhere('address','like','%'.$keyword.'%');
	        	});
        	}
        }
        if( $request->from_at ){
            $items->whereDate('created_at', '>=', $request->from_at);
        }if( $request->to_at ){
            $items->whereDate('created_at', '<=', $request->to_at);
        }
        if( $request->delivery_time_from ){
            $items->whereDate('delivery_time', '>=', $request->delivery_time_from);
        }if( $request->delivery_time_to ){
            $items->whereDate('delivery_time', '<=', $request->delivery_time_to);
        }
        if ( !Auth::user()->hasRole('admin') && Auth::user()->groups()->first()->id != 5 ) {
        	$items->where('user_id', Auth::id());
        }
        $this->_data['total']['count'] = $items->count();
        $this->_data['total']['qty'] = $items->sum('order_qty');
        $this->_data['total']['price'] = $items->sum('order_price');

        $this->_data['items'] = $items->paginate(25);

        return view('admin.orders.index',$this->_data);
    }

    public function ajax(Request $request){
        if($request->ajax()){
        	$promotions = config('promotions');
	        $data['items'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
                ->select('A.*','B.title')
                ->whereRaw("(A.code LIKE '%".$request->q."%' OR B.title LIKE '%".$request->q."%') AND FIND_IN_SET('publish',A.status)")
                ->where('B.language', $this->_data['default_language'])
                ->where('A.type',$request->t)
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get()->transform(function($item) use($promotions){
                	if( count( $promotions ) > 0 ){
			            foreach( $promotions as $promotion ){
			                if( $promotion['coupon_amount'] <= 0) continue;
			                if( in_array($item->id, explode(',', ($promotion['product_limit'] ? $promotion['product_limit'] : '') ) ) ){
			                    break;
			                }
			                if( in_array($item->id, explode(',', ($promotion['product_id'] ? $promotion['product_id'] : '') ) ) || in_array($item->category_id, explode(',', ($promotion['category_id'] ? $promotion['category_id'] : '') ) ) ){
			                    if($promotion['change_conditions_type'] == 'discount_from_total_cart'){
			                        $item->sale_price = $item->wholesale_price - $promotion['coupon_amount'];
			                    }elseif($promotion['change_conditions_type'] == 'percentage_discount_from_total_cart' && $promotion['coupon_amount'] < 100){
			                        $item->sale_price = $item->wholesale_price - (($item->wholesale_price * $promotion['coupon_amount'])/100);
			                    }
			                    break;
			                }
			            }
			        }
			        return $item;
                });
            return response()->json($data);
        }
    }

    public function export(Request $request){
        
		// $filename = 'DS-Don-Hang-'.date('dmY');
  //       $fileExt = $request->extension;

  //       $items = Order::where('type',$this->_data['type'])
  //           ->orderBy('priority','asc')
  //           ->orderBy('id','desc');

  //       if( $request->status ){
  //       	if( $request->status == 'construction' ){
  //       		$items->whereRaw("FIND_IN_SET('".$request->status."',status)");
  //       	}else{
  //           	$items->where('status_id', $request->status);
  //           }
  //       }

  //       if( $keyword = $request->keyword ){
  //       	$items->where(function($query) use ($keyword){
  //       		$query->where('code','like','%'.$keyword.'%')
  //       			->orWhere('name','like','%'.$keyword.'%')
  //       			->orWhere('phone','like','%'.$keyword.'%');
  //       	});
  //       }
  //       if( $request->from_at ){
  //           $items->whereDate('created_at', '>=', $request->from_at);
  //       }if( $request->to_at ){
  //           $items->whereDate('created_at', '<=', $request->to_at);
  //       }
  //       if ( !Auth::user()->hasRole('admin') && Auth::user()->groups()->first()->id != 5 ) {
  //       	$items->where('user_id', Auth::id());
  //       }
  //       $items->with('details');
  //       $data = $items->get()->toArray();

  //       return Excel::create($filename, function($excel) use ($data) {
  //           $excel->sheet($this->_data['pageTitle'], function($sheet) use ($data) {
  //           	if( $this->_data['type'] == 'shop' ){
  //           		$sheet->loadView('excel.order_shop')->with(['data'=>$data]);
		//     	} else {
		//     		$sheet->loadView('excel.orders')->with(['data'=>$data]);
		//     	}
            	
  //           });
  //       })->download($fileExt);

    	$filename = 'DS-Don-Hang-'.date('dmY');
        $fileExt = $request->extension;

		$year = $subyear = Carbon::now()->year;
		$month = Carbon::now()->month;
        $day = Carbon::now()->day;
        $subday = Carbon::now()->subDay(1)->day;
        $submonth = Carbon::now()->subDay(1)->month;
		$hour = 17; $minute = 00; $second = 00; $tz = 'Asia/Ho_Chi_Minh';
		
		// Edit
		// $day = '10';
		// $month = '01';
		// $year = '2021';

		// $subday = '09';
		// $submonth = '01';
		// $subyear = '2021';

        $items = Order::where('type',$this->_data['type'])
			// ->whereBetween('created_at', [Carbon::create($subyear, $submonth, $subday, $hour, '00', '00', $tz), Carbon::create($year, $month, $day, $hour, '00', '00', $tz)])
			// ->whereDate('created_at', Carbon::today()->subDay(1))
			->whereDate('created_at', Carbon::today())
			->where('status_id', '!=', 8)
            ->orderBy('priority','asc')
            ->orderBy('id','desc');

   //      $items = Order::where('type',$this->_data['type'])
   //      	->where('created_at','>=','2020-10-01 00:00:00')
   //      	->where('created_at','<','2020-10-20 00:00:00')
			// ->where('status_id', '=', 8)
   //          ->orderBy('priority','asc')
   //          ->orderBy('id','desc');

        if ( !Auth::user()->hasRole('admin') && Auth::user()->groups()->first()->id != 5 ) {
        	$items->where('user_id', Auth::id());
        }
        $items->with('details');
        $data = $items->get()->toArray();

        return Excel::create($filename, function($excel) use ($data, $request) {
            $excel->sheet($this->_data['pageTitle'], function($sheet) use ($data, $request) {
            	if( $this->_data['type'] == 'shop' ){
            		$sheet->loadView('excel.order_shop')->with(['data'=>$data]);
		    	} else {
		    		$sheet->loadView('excel.orders')->with(['data'=>$data,'from_at'=>$request->from_at,'to_at'=>$request->to_at]);
		    	}
            	
            });
        })->download($fileExt);
	}
	
	public function exportCustomer(Request $request){
        $filename = 'Danh-Sach-Khach-Hang-'.date('dmY');
        $fileExt = $request->extension;

        $items = Order::where('type',$this->_data['type'])
            ->orderBy('priority','asc')
            ->orderBy('id','desc');

        if( $request->status ){
        	if( $request->status == 'construction' ){
        		$items->whereRaw("FIND_IN_SET('".$request->status."',status)");
        	}else{
            	$items->where('status_id', $request->status);
            }
        }
        if ( !Auth::user()->hasRole('admin') && Auth::user()->groups()->first()->id != 5 ) {
        	$items->where('user_id', Auth::id());
        }
        $data = $items->select(DB::raw('count(*) as order_count, name, phone, address'))->groupBy('phone')->having('order_count', '>=', 5)->get()->toArray();

        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('Khách hàng', function($sheet) use ($data) {
            	$sheet->loadView('excel.customers')->with(['data'=>$data]);
            });
        })->download($fileExt);
    }

    public function customer(Request $request){
    	if($request->ajax() && $request->type){
    		$item = Order::where('type',$request->type)->where('phone',$request->phone)->firstOrFail();
    		return response()->json(['data'=>$item]);
    	}
    }

    public function print(Request $request){
    	if($request->ajax() && $request->id){
    		$province = json_decode(str_replace('var province = ','',file_get_contents(public_path().'/jsons/province.js')),true);
        	$district = json_decode(str_replace('var district = ','',file_get_contents(public_path().'/jsons/district.js')),true);
        	$arrID = explode(',',$request->id);
    		$items = Order::whereIn('id',$arrID)->get();
	        if ($items !== null) {
	        	$data = '';
	        	foreach($items as $k => $item){
		        	$user = User::find($item->user_id);
		        	if($k > 0){
		        		$data .= '<div style="page-break-after: always;"></div>';
		        	}
		        	if( $request->loai == 1 ){
		        		if( strpos($item->status,'printed') === false ){
		        			$item->status .= ',printed';
		        			$item->save();
		        		}

		        		$table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
				        <thead>
				            <tr class="text-uppercase">
				                <th width="7%" align="center" style="border:1px solid black !important"> Mã SP </th>
				                <th width="15%" align="center" style="border:1px solid black !important"> Tên SP </th>
				                <th width="8%" align="center" style="border:1px solid black !important"> Giá bán</th>
	                            <th width="8%" align="center" style="border:1px solid black !important"> Giá kê </th>
				                <th width="6%" align="center" style="border:1px solid black !important"> SL </th>
				                <th width="10%" align="center" style="border:1px solid black !important"> Thành tiền </th>
				            </tr>
				        </thead>
				        <tbody>';
				        if($products = $item->details()->get()){
		                    $total = 0;
			                foreach($products as $key => $val){
		                        $total += $val->product_price*$val->product_qty;
	                            $table .= '<tr style="font-size:11px">
	                                <td align="center">'.$val->product_code.'</td>
	                                <td>'.$val->product_title.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price,'').'</td>
	                                <td align="center">'.get_currency_vn($val->product_price_second,'').'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price*$val->product_qty,'').'</td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
			            }
			            if($item->shipping > 0){
			            	$table .= '<tr>
								<td align="right" colspan="30">
									<span class="pull-right text-uppercase bold text-center">
										Phí vận chuyển: <span class="font-red-mint font-md bold">'.get_currency_vn($item->shipping,'').'</span>
									</span>
								</td>
							</tr>';
						}
			            $table .= '<tr>
			            			<td align="right" colspan="30">					                  
					                    Giảm giá: <span class="font-red-mint font-md bold">'.get_currency_vn($item->coupon_amount,'').'</span>
					                </td>
					                </tr>
					                <tr>
					                <td align="right" colspan="30">
					                    <span class="pull-left text-uppercase">
					                        Số lượng: <span class="font-red-mint font-md bold">'.$item->order_qty.'</span>
					                    </span>
					                    <span class="pull-right text-uppercase">
					                        Tổng: <span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
					                    </span>
					                </td>
					            </tr>
					        </tbody>
					    </table>';

			            $data .= '<div class="container">
							<div class="row">
								<div class="col-xs-5"><h2 class="print-title">PHIẾU ĐỀ XUẤT</h2><small style="text-center">Giờ In: '.Carbon::now().'</small></div>
								<div class="col-xs-4">
									<div>Ngày: '.$item->created_at->format('d/m/Y').'</div>
									<div>NVKD: '.@$user->name.'</div>
									<div>Mã Đơn Hàng: '.$item->code.'</div>
								</div>
								<div class="col-xs-3">
									<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
								</div>
							</div>
							<hr />
							<div class="row">
								<div class="col-xs-12"><b>THÔNG TIN ĐẶT HÀNG</b></div>
								<div class="col-xs-5">
									Người đặt hàng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-7">
									SĐT: <b>'.$item->phone.'</b> - 
									Email nhận HĐ: <b>'.$item->email.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-7">
									Địa chỉ: <b>'.$item->address.' - '. @$district[$item->province_id][$item->district_id]['name'] .' - '. @$province[$item->province_id]['name'] .'</b><br>
								</div>
								<div class="col-xs-5">
									Ngày xuất hóa đơn:
								</div>
							</div>
							<br/>
							<div class="row">
								<div class="col-xs-12"><b>THÔNG TIN NHẬN HÀNG</b></div>
								<div class="col-xs-7">
									Người nhận hàng: <b>'.$item->delivery['name'].'</b>
								</div>
								<div class="col-xs-5">
									SĐT: <b>'.$item->delivery['phone'].'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Địa chỉ: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b><br>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-7">
									Yêu cầu thu cọc: <b>'.get_currency_vn($item->deposit_note,'').'</b> ('.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].')<br>
								</div>
								<div class="col-xs-5">
									Ngày thanh toán:
								</div>
							</div>
							<br/>
							<div class="row">
								<div class="col-xs-12"><b>CHI TIẾT ĐƠN HÀNG</b></div>
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>

							<div class="row">
		                        <div class="col-xs-12 mb-2">
		                            <b style="font-size:20px">*Ghi chú: '.$item->note.' <br>
									Ngày giao hàng: <b>'.$item->delivery_time.'</b>									
		                            </b>
		                        </div>
								<div class="col-xs-12">
									<b> Thông tin xuất hóa đơn GTGT:</b> (Liên 2 là căn cứ xuất hóa đơn. Trường hợp xuất hóa đơn ngay, VP giữ lại liên 2)
								</div>
								<div class="col-xs-12">
									<b>Tên tổ chức, cá nhân:</b> '.@$item->invoice['company'].'
								</div>
								<div class="col-xs-12">
									<b>Địa chỉ:</b> '.@$item->invoice['address'].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<b>Mã số thuế:</b>  '.@$item->invoice['tax_code'].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									* Bảo lãnh thanh toán toàn bộ giá trị hàng hóa: Người bảo lãnh ký vào mục bảo lãnh thanh toán
								</div>
							</div>
							<div class="row mt-2">
								<div class="col-xs-3">
									<center> <b>Duyệt</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Văn phòng</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Người đề xuất <br>'.@$user->name.'</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Bảo lãnh thanh toán</b> </center>
								</div>
							</div><br><br><br><br>
							<div class="row mt-2">
								<div class="col-xs-3">
									<center> <b>Kiểm soát 1 ký tên</b> </center><br><br><br>
									<center> <b>.........................</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Kiểm soát 2 ký tên</b> </center><br><br><br>
									<center> <b>.........................</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Kiểm soát 3 ký tên</b> </center><br><br><br>
									<center> <b>.........................</b> </center>
								</div>
								<div class="col-xs-3">
								</div>
							</div>
						</div>';
		        	} elseif( $request->loai == 2 ){

		        		if( strpos($item->status,'construction') !== false ){
		        			$construction = '<br/>
		        			<div class="row" style="font-size:13px">
								<div class="col-xs-12">
		        					<b>Thông tin thiết bị: Vật Tư, linh phụ kiện & dịch vụ:</b>
		        				</div>
		        				<div class="col-xs-12">
									<table class="table table-bordered table-condensed" style="font-size:11px;border:1px solid black !important">
										<thead>
											<tr>
												<th scope="col" style="width: 5%; text-align: center;border:1px solid black !important">STT</th>
												<th scope="col" style="width: 45%; text-align: center;border:1px solid black !important">Dịch vụ</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Mã hàng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Số lượng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Đơn giá</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Thành tiền</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="border:1px solid black !important">1</th>
												<td style="border:1px solid black !important">Ống đồng 1.0HP: 120K/mét - 1.5HP: 130K/mét - 2.0HP: 160K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">2</th>
												<td style="border:1px solid black !important">Ống đồng 2.5HP: 160K/mét - 3.0HP: 180K/mét - 3.5HP 4.0HP: 250K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">3</th>
												<td style="border:1px solid black !important">Ống đồng 4.5HP 6.0HP: 290K/mét - 10.0HP: 350K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">4</th>
												<td style="border:1px solid black !important">Dây 1.0HP : 6.000đ/m -  1.5HP : 7.000đ/m - 2.0HP : 10.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">5</th>
												<td style="border:1px solid black !important">Dây 2.5HP : 12.000đ/m - 3.0HP : 15.000đ/m - 3.5HP - 4.0HP : 20.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">6</th>
												<td style="border:1px solid black !important"> Dây 4.5HP - 6.0HP : 25.000đ/m - 10.0HP : 45.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">7</th>
												<td style="border:1px solid black !important">* Phí thi công ống âm: 50K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">8</th>
												<td style="border:1px solid black !important">Ruột gà: 10.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">9</th>
												<td style="border:1px solid black !important">Ống nước bình minh: 25.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">10</th>
												<td style="border:1px solid black !important">Cặp Ke: 1HP - 90.000đ/cặp - 1.5HP - 100.000đ/cặp - 1HP - 110.000đ/cặp</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<td colspan="5" style="text-align: right;border:1px solid black !important">(ĐTV: 1.000đ) - Tổng Cộng</td>
												<td style="border:1px solid black !important"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<div>
										<p>
											* Thực thiện xong lúc: ........Giờ.........ngày........./........./..........
											<span> Hình thức dịch vụ: ..................................................</span> 
										</p>
									</div>
									<div>
										<p>
											* Hình thức thanh toán: .....................................
											<span><b>Tổng số tiền phải thu:</b> .............................................................</span>
										</p>
									</div>
									<div class="row">
										<div class="col-xs-3 text-center">
											Khách hàng
										</div>
										<div class="col-xs-3 text-center">
											NV Kỹ Thuật
										</div>
										<div class="col-xs-3 text-center">
											Giám sát
										</div>
										<div class="col-xs-3 text-center">
											Người lập phiếu
										</div>
									</div>
								</div>
		        			</div>
							</div>';
		        		} else {
		        			$construction = '';
		        		}

			        	$table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> Tên hàng hóa </th>
					                <th width="7%" style="font-weight: bold !important; text-align: center !important; border:1px solid black !important"> Mã hàng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> Số lượng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> Giảm giá </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> Đơn giá</th>
					                <th width="10%" style="font-weight: bold !important; text-align: center !important; border:1px solid black !important"> Thành tiền </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0; $total_discount = 0;
			                foreach($products as $key => $val){
			                	if( $val->product_price_virtual == 0 && ($val->product_price == $val->product_price_second) ){
			                		$price = $val->product_price;
			                		$tiencoc = 0;
			                	}elseif( $val->product_price_virtual == 0 && ($val->product_price_second > $val->product_price) ){
			                		$price = $val->product_price_second;
			                		$tiencoc = 0;
			                	}elseif( $val->product_price_virtual > 0 && ( ($val->product_price + $val->product_price_virtual) > $val->product_price_second) ) {
			                		$price = $val->product_price + $val->product_price_virtual;
			                		$tiencoc = $price - $val->product_price_second;
			                	}elseif( $val->product_price_virtual > 0 && ( ($val->product_price + $val->product_price_virtual) < $val->product_price_second) ){
			                		$price = $val->product_price_second;
			                		$tiencoc = 0;
			                	}else {
			                		$price = $val->product_price;
			                		$tiencoc = 0;
			                	}
	                            $total += ($price-$val->product_price_discount)*$val->product_qty;
	                            $total_virtual += $tiencoc*$val->product_qty;
	                            $table .= '<tr style="font-size:11px">
	                            	<td align="center">'.($key+1).'</td>
	                            	<td>'.$val->product_title.'</td>
	                                <td align="center"><b style="font-size: 15px">'.$val->product_code.'</b></td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price_discount,'').'</td>
	                                <td align="center">'.get_currency_vn($price,'').'</td>
	                                <td align="center"><b>'.get_currency_vn($price*$val->product_qty,'').'</b></td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
						}
						
						if($item->coupon_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Giảm giá:
					                    </span>
					                </td>
					                <td>
					                	<b>'.get_currency_vn($item->coupon_amount,'').'</b>
					                </td>
					            </tr>';
					    }
					    if($item->shipping > 0){
			            	$table .= '<tr>
								<td align="right" colspan="6">
									<span class="pull-right text-uppercase bold text-center">
										Phí vận chuyển + Phí dịch vụ:
									</span>
								</td>
								<td>
									<b>'.get_currency_vn($item->shipping,'').'</b>
								</td>
							</tr>';
						}

						$table .= '<tr>
				                <td align="right" colspan="6">
				                    <span class="pull-right text-uppercase bold text-center">
				                        Tổng số tiền:
				                    </span>
				                </td>
				                <td>
				                	<b>'.get_currency_vn($total,'').'</b>
				                </td>
				            </tr>';

				        if($total_virtual > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Cọc lần 1:
					                    </span>
					                </td>
					                <td>
					                	<b>'.get_currency_vn($total_virtual,'').'</b>
					                </td>
					            </tr>';
					    }

					    if($item->deposit_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Đã thu cọc:
					                    </span>
					                </td>
					                <td>
					                	<b>'.get_currency_vn($item->deposit_amount,'').'</b>
					                </td>
					            </tr>';
					        $table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Phải Thu:
					                    </span>
					                </td>
					                <td>
					                	<b>'.get_currency_vn($total - $item->deposit_amount - $total_virtual,'').'</b>
					                </td>
					            </tr>';
					    } else if($total_virtual > 0){
					    	$table .= '<tr>
				                <td align="right" colspan="6">
				                    <span class="pull-right text-uppercase bold text-center">
				                        Phải Thu:
				                    </span>
				                </td>
				                <td>
				                	<b>'.get_currency_vn($total - $total_virtual,'').'</b>
				                </td>
				            </tr>';
					    }

					    $table .= '</tbody>
					    </table>';

			            $data .= '<div class="container">
							<div class="row" style="border-bottom:1px solid black; padding-bottom: 5px">	
								<div class="col-xs-9">
									<div><b>CÔNG TY TNHH ĐIỆN MÁY GIÁ SỈ</b></div>
									<div>Mã số thuế: <b>0315626048</b></div>
									<div>Siêu Thị: F1/7D Hương Lộ 80, Vĩnh Lộc A, H Bình Chánh, TP Hồ Chí Minh</div>
									<div>Hotline: <b style="color: #f00">0828 100 100 - 0828 100 200 - 028 7300 8010</b></div>
								</div>
								<div class="col-xs-3">
									<img src="/public/images/logo-2.svg" alt="logo">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9">
									<h2 style="text-align: left;font-weight:bold">PHIẾU XUẤT KHO</h2>
									<small>Giờ In: '.Carbon::now().'</small>
								</div>
								<div class="col-xs-3">
									<br/>
									<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
									<br>
									<p style="font-size: 16px">
										'.$item->code.'										
									</p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">Mã kho: Vĩnh Lộc</div>
								<div class="col-xs-4">Giờ giao hàng: <b>'.$item->delivery_time.'</b></div>
								<div class="col-xs-4">Mã đơn hàng: '.$item->code.'</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									Tên khách hàng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-8">
									Điện thoại: <b>'.$item->phone.'</b>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6">
									Thông tin XHĐ: <b>'.@$item->invoice['company'].' - '.@$item->invoice['tax_code'].'</b>
								</div>
								<div class="col-xs-6">
									Email nhận hóa đơn: <b>'.$item->email.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									Tên người nhận: <b>'.$item->delivery['name'].'</b>
								</div>
								<div class="col-xs-4">
									Điện thoại: <b>'.$item->delivery['phone'].'</b>
								</div>
								<div class="col-xs-4">
									Thanh toán: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Địa chỉ giao hàng: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-3">
									Mã số thuế: <b>'.@$item->invoice['tax_code'].'</b>
								</div>
								<div class="col-xs-5">
									Thay đổi địa chỉ giao hàng:................................................................
								</div>
								<div class="col-xs-4">
									Thay đổi giờ giao hàng:.......................................................
								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12">
									<b>THÔNG TIN HÀNG HÓA</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b style="font-size:18px">*Ghi chú: '.$item->note.' <br>
		                           	Ngày giao hàng: <b>'.$item->delivery_time.'</b>									
		                            </b> 
		                        </div>
							</div>
							<br>
							<div class="row mt-2">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Giám đốc</b> </center><br><br><br>
									<center> <b>Kế Toán Kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Nhân viên giao hàng</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Thủ kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Người lập phiếu<br><br> '.@$user->name.' </b></center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>Ký Xác Nhận Thanh Toán</b> </center><br>
									<center> <b>..................................vnđ</b> </center>
									<center> <b>Ký Tên</b> </center><br><br><br>
									<center> <b>Họ và tên:.................................</b> </center>
								</div>
							</div>
							
							'.$construction.'
						</div>';
					} elseif( $request->loai == 3 ) {

						$table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
				        <thead>
				            <tr class="text-uppercase">
				                <th width="7%" align="center" style="border:1px solid black !important"> Mã SP </th>
				                <th width="15%" align="center" style="border:1px solid black !important"> Tên SP </th>
				                <th width="8%" align="center" style="border:1px solid black !important"> Giá bán</th>
	                            <th width="8%" align="center" style="border:1px solid black !important"> Giá kê </th>
				                <th width="6%" align="center" style="border:1px solid black !important"> SL </th>
				                <th width="10%" align="center" style="border:1px solid black !important"> Thành tiền </th>
				            </tr>
				        </thead>
				        <tbody>';
				        if($products = $item->details()->get()){
		                    $total = 0;
			                foreach($products as $key => $val){
		                        $total += $val->product_price*$val->product_qty;
	                            $table .= '<tr style="font-size:11px">
	                                <td align="center">'.$val->product_code.'</td>
	                                <td>'.$val->product_title.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price,'').'</td>
	                                <td align="center">'.get_currency_vn($val->product_price_second,'').'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price*$val->product_qty,'').'</td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
			            }
			            $table .= '</tbody></table>';

						$data .= '<div class="container">
							<div class="row" style="border-bottom:1px solid black; padding-bottom: 5px">
								
								<div class="col-xs-9">
									<div><b>CÔNG TY TNHH ĐIỆN MÁY GIÁ SỈ</b></div>
									<div>Mã số thuế: <b>0315626048</b></div>
									<div>Trụ sở: F1/7D Hương Lộ 80, Vĩnh Lộc A, H Bình Chánh, TP Hồ Chí Minh</div>
									<div>Hotline: <b style="color: #f00">0828 100 100 - 028 7300 8010</b></div>
								</div>
								<div class="col-xs-3">
									<img src="/public/images/logo-2.svg" alt="logo">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9">
									<h2 style="text-align: left;font-weight:bold">PHIẾU THU</h2><br>
									<small style="text-center">Ngày xuất: '.Carbon::now().'</small>
									<p>Ngày: ......../......../...........</p>
									
								</div>
								<div class="col-xs-3">
									<br/>
									<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
									<br>
									<p style="font-size: 16px">
										'.$item->code.'										
									</p>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-12">
									Tên khách hàng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-12">
									Điện thoại: <b>'.$item->phone.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Đơn vị:....................................................................................................................................................................................<br>
									Địa chỉ: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>

								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Lý do nộp
									'.$table.'
								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b>Số tiền Thu Cọc:</b> <span class="font-red-mint font-md bold">'.get_currency_vn($item->deposit_note,'').'</span>
		                        </div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Tổng số tiền (Viết bằng chữ):................................................................................................................................................................
								</div><br>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b>*Ghi chú:</b> '.$item->note.'
		                        </div>
							</div><br>
							<div class="row mt-2">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Người Nộp Tiên</b> </center>
									<center> (Ký ghi rõ họ tên) </center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>Người Lập Phiếu</b> </center>
									<center><br><br> '.@$user->name.'  </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Thủ quỹ</b> </center>
									<center> (Ký ghi rõ họ tên) </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Kế toán</b></center>
									<center> (Ký ghi rõ họ tên) </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Giám Đốc</b> </center>
									<center> (Ký ghi rõ họ tên) </center>
								</div>
							</div><br><br><br>
							<div>
								Đã nhận đủ số tiền (Viết bằng chữ): ...........................................................................................................................................
							</div>
						</div>';
					} elseif( $request->loai == 4) {
						if( strpos($item->status,'construction') !== false ){
		        			$construction = '<br/>
		        			<div class="row" style="font-size:13px">
								<div class="col-xs-12">
		        					<b>Thông tin thiết bị: Vật Tư, linh phụ kiện & dịch vụ:</b>
		        				</div>
		        				<div class="col-xs-12">
									<table class="table table-bordered table-condensed" style="font-size:11px;border:1px solid black !important">
										<thead>
											<tr>
												<th scope="col" style="width: 5%; text-align: center;border:1px solid black !important">STT</th>
												<th scope="col" style="width: 45%; text-align: center;border:1px solid black !important">Dịch vụ</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Mã hàng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Số lượng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Đơn giá</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Thành tiền</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="border:1px solid black !important">1</th>
												<td style="border:1px solid black !important">Ống đồng 1.0HP: 120K/mét - 1.5HP: 130K/mét - 2.0HP: 160K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">2</th>
												<td style="border:1px solid black !important">Ống đồng 2.5HP: 160K/mét - 3.0HP: 180K/mét - 3.5HP 4.0HP: 250K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">3</th>
												<td style="border:1px solid black !important">Ống đồng 4.5HP 6.0HP: 290K/mét - 10.0HP: 350K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">4</th>
												<td style="border:1px solid black !important">Dây 1.0HP : 6.000đ/m -  1.5HP : 7.000đ/m - 2.0HP : 10.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">5</th>
												<td style="border:1px solid black !important">Dây 2.5HP : 12.000đ/m - 3.0HP : 15.000đ/m - 3.5HP - 4.0HP : 20.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">6</th>
												<td style="border:1px solid black !important"> Dây 4.5HP - 6.0HP : 25.000đ/m - 10.0HP : 45.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">7</th>
												<td style="border:1px solid black !important">* Phí thi công ống âm: 50K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">8</th>
												<td style="border:1px solid black !important">Ruột gà: 10.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">9</th>
												<td style="border:1px solid black !important">Ống nước bình minh: 25.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">10</th>
												<td style="border:1px solid black !important">Cặp Ke đỡ dàn nóng: 90.000đ/cặp</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<td colspan="5" style="text-align: right;border:1px solid black !important">(ĐTV: 1.000đ) - Tổng Cộng</td>
												<td style="border:1px solid black !important"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<div>
										<p>
											* Thực thiện xong lúc: ........Giờ.........ngày........./........./..........
											<span> Hình thức dịch vụ: ..................................................</span> 
										</p>
									</div>
									<div>
										<p>
											* Hình thức thanh toán: .....................................
											<span><b>Tổng số tiền phải thu:</b> .............................................................</span>
										</p>
									</div>
									<div class="row">
										<div class="col-xs-3 text-center">
											Khách hàng
										</div>
										<div class="col-xs-3 text-center">
											NV Kỹ Thuật
										</div>
										<div class="col-xs-3 text-center">
											Giám sát
										</div>
										<div class="col-xs-3 text-center">
											Người lập phiếu
										</div>
									</div>
								</div>
		        			</div>
							</div>';
		        		} else {
		        			$construction = '';
		        		}

			        	$table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> Tên hàng hóa </th>
					                <th width="7%" style="text-align: center !important; border:1px solid black !important"> Mã hàng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> Số lượng </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> Đơn giá</th>
					                <th width="10%" style="text-align: center !important; border:1px solid black !important"> Thành tiền </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0;
			                foreach($products as $key => $val){
		                        $price = $val->product_price_second > 0 ? $val->product_price_virtual > 0 ? ($val->product_price_second+$val->product_price_virtual) : $val->product_price_second : ($val->product_price+$val->product_price_virtual);
	                            $total += $price*$val->product_qty;
	                            $total_virtual += $val->product_price_virtual*$val->product_qty;
	                            $table .= '<tr style="font-size:11px">
	                            	<td align="center">'.($key+1).'</td>
	                            	<td>'.$val->product_title.'</td>
	                                <td align="center">'.$val->product_code.'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center"></td>
	                                <td align="center"></td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
						}

					    $table .= '</tbody>
					    </table>';

			            $data .= '<div class="container">
							<div class="row" style="border-bottom:1px solid black; padding-bottom: 5px">
								
								<div class="col-xs-9">
									<div><b>CÔNG TY TNHH ĐIỆN MÁY GIÁ SỈ</b></div>
									<div>Mã số thuế: <b>0315626048</b></div>
									<div>Trụ sở: F1/7D Hương Lộ 80, Vĩnh Lộc A, H Bình Chánh, TP Hồ Chí Minh</div>
									<div>Hotline: <b style="color: #f00">0828 100 100 - 0828 100 200 - 028 7300 8010</b></div>
								</div>
								<div class="col-xs-3">
									<img src="/public/images/logo-2.svg" alt="logo">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9">
									<h2 style="text-align: left;font-weight:bold">PHIẾU XUẤT KHO</h2>
									<small style="text-center">Ngày xuất: '.Carbon::now().'</small>
								</div>
								<div class="col-xs-3">
									<br/>
									<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
									<br>
									<p style="font-size: 16px">
										'.$item->code.'										
									</p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">Mã kho:</div>
								<div class="col-xs-4">Giờ giao hàng: <b>'.$item->delivery_time.'</b></div>
								<div class="col-xs-4">Mã đơn hàng: '.$item->code.'</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									Tên khách hàng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-8">
									Điện thoại: <b>'.$item->phone.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									Tên người nhận: <b>'.$item->delivery['name'].'</b>
								</div>
								<div class="col-xs-4">
									Điện thoại: <b>'.$item->delivery['phone'].'</b>
								</div>
								<div class="col-xs-4">
									Thanh toán: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Địa chỉ giao hàng: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-7">
									Thay đổi địa chỉ giao hàng:................................................................
								</div>
								<div class="col-xs-5">
									Thay đổi giờ giao hàng:.......................................................
								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12">
									<b>THÔNG TIN HÀNG HÓA</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b style="font-size:18px">*Ghi chú: '.$item->note.' </b> 
		                        </div>
							</div>
							<br>
							<div class="row mt-2">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Giám đốc</b> </center><br><br><br>
									<center> <b>Kế Toán Kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Nhân viên giao hàng</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Thủ kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Người lập phiếu<br><br> '.@$user->name.' </b></center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>Ký Xác Nhận Thanh Toán</b> </center><br>
									<center> <b>..................................vnđ</b> </center>
									<center> <b>Ký Tên</b> </center><br><br><br>
									<center> <b>Họ và tên:.................................</b> </center>
								</div>
							</div>
							
							'.$construction.'
						</div>';
					}elseif( $request->loai == 5 ){
						if( strpos($item->status,'construction') !== false ){
		        			$construction = '<br/>
		        			<div class="row" style="font-size:13px">
								<div class="col-xs-12">
		        					<b>Thông tin thiết bị: Vật Tư, linh phụ kiện & dịch vụ:</b>
		        				</div>
		        				<div class="col-xs-12">
									<table class="table table-bordered table-condensed" style="font-size:11px;border:1px solid black !important">
										<thead>
											<tr>
												<th scope="col" style="width: 5%; text-align: center;border:1px solid black !important">STT</th>
												<th scope="col" style="width: 45%; text-align: center;border:1px solid black !important">Dịch vụ</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Mã hàng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Số lượng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Đơn giá</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Thành tiền</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="border:1px solid black !important">1</th>
												<td style="border:1px solid black !important">Ống đồng 1.0HP: 120K/mét - 1.5HP: 130K/mét - 2.0HP: 160K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">2</th>
												<td style="border:1px solid black !important">Ống đồng 2.5HP: 160K/mét - 3.0HP: 180K/mét - 3.5HP 4.0HP: 250K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">3</th>
												<td style="border:1px solid black !important">Ống đồng 4.5HP 6.0HP: 290K/mét - 10.0HP: 350K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">4</th>
												<td style="border:1px solid black !important">Dây 1.0HP : 6.000đ/m -  1.5HP : 7.000đ/m - 2.0HP : 10.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">5</th>
												<td style="border:1px solid black !important">Dây 2.5HP : 12.000đ/m - 3.0HP : 15.000đ/m - 3.5HP - 4.0HP : 20.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">6</th>
												<td style="border:1px solid black !important"> Dây 4.5HP - 6.0HP : 25.000đ/m - 10.0HP : 45.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">7</th>
												<td style="border:1px solid black !important">* Phí thi công ống âm: 50K/mét</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">8</th>
												<td style="border:1px solid black !important">Ruột gà: 10.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">9</th>
												<td style="border:1px solid black !important">Ống nước bình minh: 25.000đ/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">10</th>
												<td style="border:1px solid black !important">Cặp Ke: 1HP - 90.000đ/cặp - 1.5HP - 100.000đ/cặp - 1HP - 110.000đ/cặp</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<td colspan="5" style="text-align: right;border:1px solid black !important">(ĐTV: 1.000đ) - Tổng Cộng</td>
												<td style="border:1px solid black !important"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<div>
										<p>
											* Thực thiện xong lúc: ........Giờ.........ngày........./........./..........
											<span> Hình thức dịch vụ: ..................................................</span> 
										</p>
									</div>
									<div>
										<p>
											* Hình thức thanh toán: .....................................
											<span><b>Tổng số tiền phải thu:</b> .............................................................</span>
										</p>
									</div>
									<div class="row">
										<div class="col-xs-3 text-center">
											Khách hàng
										</div>
										<div class="col-xs-3 text-center">
											NV Kỹ Thuật
										</div>
										<div class="col-xs-3 text-center">
											Giám sát
										</div>
										<div class="col-xs-3 text-center">
											Người lập phiếu
										</div>
									</div>
								</div>
		        			</div>
							</div>';
		        		} else {
		        			$construction = '';
		        		}

			        	$table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> Tên hàng hóa </th>
					                <th width="7%" style="text-align: center !important; border:1px solid black !important"> Mã hàng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> Số lượng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> Giảm giá </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> Đơn giá</th>
					                <th width="10%" style="font-weight: bold !important; text-align: center !important; border:1px solid black !important"> Thành tiền </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0; $total_discount = 0;
			                foreach($products as $key => $val){
			                	if( $val->product_price_virtual == 0 && ($val->product_price == $val->product_price_second) ){
			                		$price = $val->product_price;
			                		$tiencoc = 0;
			                	}elseif( $val->product_price_virtual == 0 && ($val->product_price_second > $val->product_price) ){
			                		$price = $val->product_price_second;
			                		$tiencoc = 0;
			                	}elseif( $val->product_price_virtual > 0 && ( ($val->product_price + $val->product_price_virtual) > $val->product_price_second) ) {
			                		$price = $val->product_price + $val->product_price_virtual;
			                		$tiencoc = $price - $val->product_price_second;
			                	}elseif( $val->product_price_virtual > 0 && ( ($val->product_price + $val->product_price_virtual) < $val->product_price_second) ){
			                		$price = $val->product_price_second;
			                		$tiencoc = 0;
			                	}else {
			                		$price = $val->product_price;
			                		$tiencoc = 0;
			                	}
	                            $total += ($price-$val->product_price_discount)*$val->product_qty;
	                            $total_virtual += $tiencoc*$val->product_qty;
	                            $table .= '<tr style="font-size:11px">
	                            	<td align="center">'.($key+1).'</td>
	                            	<td>'.$val->product_title.'</td>
	                                <td align="center">'.$val->product_code.'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price_discount,'').'</td>
	                                <td align="center">'.get_currency_vn($price,'').'</td>
	                                <td align="center"><b>'.get_currency_vn($price*$val->product_qty,'').'</b></td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
						}
						
						if($item->coupon_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Giảm giá:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->coupon_amount,'').'</span>
					                </td>
					            </tr>';
					    }
					    if($item->shipping > 0){
			            	$table .= '<tr>
								<td align="right" colspan="6">
									<span class="pull-right text-uppercase bold text-center">
										Phí vận chuyển + Phí dịch vụ:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($item->shipping,'').'</span>
								</td>
							</tr>';
						}

						$table .= '<tr>
				                <td align="right" colspan="6">
				                    <span class="pull-right text-uppercase bold text-center">
				                        Tổng số tiền:
				                    </span>
				                </td>
				                <td>
				                	<span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
				                </td>
				            </tr>';

				        if($total_virtual > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Cọc lần 1:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($total_virtual,'').'</span>
					                </td>
					            </tr>';
					    }

					    if($item->deposit_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Đã thu cọc:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->deposit_amount,'').'</span>
					                </td>
					            </tr>';
					        $table .= '<tr>
					                <td align="right" colspan="6">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Phải Thu:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($total - $item->deposit_amount - $total_virtual,'').'</span>
					                </td>
					            </tr>';
					    } else if($total_virtual > 0){
					    	$table .= '<tr>
				                <td align="right" colspan="6">
				                    <span class="pull-right text-uppercase bold text-center">
				                        Phải Thu:
				                    </span>
				                </td>
				                <td>
				                	<span class="font-red-mint font-md bold">'.get_currency_vn($total - $total_virtual,'').'</span>
				                </td>
				            </tr>';
					    }

					    $table .= '</tbody>
					    </table>';

			            $data .= '<div class="container">
							<div class="row" style="border-bottom:1px solid black; padding-bottom: 5px">	
								<div class="col-xs-9">
									<div><b>CÔNG TY TNHH ĐIỆN MÁY GIÁ SỈ</b></div>
									<div>Mã số thuế: <b>0315626048</b></div>
									<div>Siêu Thị: F1/7D Hương Lộ 80, Vĩnh Lộc A, H Bình Chánh, TP Hồ Chí Minh</div>
									<div>Hotline: <b style="color: #f00">0828 100 100 - 0828 100 200 - 028 7300 8010</b></div>
								</div>
								<div class="col-xs-3">
									<img src="/public/images/logo-2.svg" alt="logo">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9">
									<h2 style="text-align: left;font-weight:bold">PHIẾU XUẤT KHO</h2>
									<small>Giờ In: '.Carbon::now().'</small>
								</div>
								<div class="col-xs-3">
									<br/>
									<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
									<br>
									<p style="font-size: 16px">
										'.$item->code.'										
									</p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">Mã kho: Vĩnh Lộc</div>
								<div class="col-xs-4">Giờ giao hàng: <b>'.$item->delivery_time.'</b></div>
								<div class="col-xs-4">Mã đơn hàng: '.$item->code.'</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									Tên khách hàng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-8">
									Điện thoại: <b>'.$item->phone.'</b>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6">
									Thông tin XHĐ: <b>'.@$item->invoice['company'].' - '.@$item->invoice['tax_code'].'</b>
								</div>
								<div class="col-xs-6">
									Email nhận hóa đơn: <b>'.$item->email.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									Tên người nhận: <b>'.$item->delivery['name'].'</b>
								</div>
								<div class="col-xs-4">
									Điện thoại: <b>'.$item->delivery['phone'].'</b>
								</div>
								<div class="col-xs-4">
									Thanh toán: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Địa chỉ giao hàng: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-3">
									Mã số thuế: <b>'.@$item->invoice['tax_code'].'</b>
								</div>
								<div class="col-xs-5">
									Thay đổi địa chỉ giao hàng:................................................................
								</div>
								<div class="col-xs-4">
									Thay đổi giờ giao hàng:.......................................................
								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12">
									<b>THÔNG TIN HÀNG HÓA</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b style="font-size:18px">*Ghi chú: '.$item->note.' <br>
		                           	Ngày giao hàng: <b>'.$item->delivery_time.'</b>									
		                            </b> 
		                        </div>
							</div>
							<br>
							<div class="row mt-2">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Giám đốc</b> </center><br><br><br>
									<center> <b>Kế Toán Kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Nhân viên giao hàng</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Thủ kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Người lập phiếu<br><br> '.@$user->name.' </b></center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>Ký Xác Nhận Thanh Toán</b> </center><br>
									<center> <b>..................................vnđ</b> </center>
									<center> <b>Ký Tên</b> </center><br><br><br>
									<center> <b>Họ và tên:.................................</b> </center>
								</div>
							</div>
							
							'.$construction.'
						</div>';
					}
				}
	            return response()->json(['data'=>$data]);
	        }
    	}
    }
    
    public function create(){
    	$this->_data['sales'] = Member::where('type','saler')->get();
        return view('admin.orders.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            ], [
            'products.required' => 'Vui lòng chọn sản phẩm',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $order  = new Order;

            if($request->data){
                foreach($request->data as $field => $value){
                    $order->$field = $value;
                }
            }

            $inputProduct = $request->products;
            $products = [];
            $product  = [];
            $sumPrice = 0;
            $sumQty   = 0;
            $dataInsert = [];
            foreach($inputProduct as $key => $value){
                $id    = (int)$value['id'];
                if( !isset($products[$id]) ){
                    $products[$id]['title']  =  $value['title'];
                    $products[$id]['code']   =  strtoupper($value['code']);
                    $products[$id]['price']  =  (int)$value['price'];
                    $products[$id]['price_second']  =  (int)$value['price_second'];
                    $products[$id]['price_virtual']  =  (int)$value['price_virtual'];
                    $products[$id]['price_discount']  =  (int)$value['price_discount'];
                    @$products[$id]['qty']  +=  (int)$value['qty'];
                }else{
                    @$products[$id]['qty']  +=  (int)$value['qty'];
                    unset($inputProduct[$key]);
                }
            }
            array_values($inputProduct);
            foreach($inputProduct as $key => $value){
                $id    = (int)$value['id'];
                if( isset($products[$id]) ){
                    $product['product_id']    =   $id;
                    $product['product_code']  =   $products[$id]['code'];
                    $product['product_title'] =   $products[$id]['title'];
                    $product['product_qty']   =   $products[$id]['qty'];
                    $product['product_price'] =   $products[$id]['price'];
                    $product['product_price_second'] =   $products[$id]['price_second'];
                    $product['product_price_virtual'] =   $products[$id]['price_virtual'];
                    $product['product_price_discount'] =   $products[$id]['price_discount'];
                    
                    $sumPrice       += ($products[$id]['price']-$product['product_price_discount'])*$products[$id]['qty'];
                    $sumQty         += $products[$id]['qty'];
                    $dataInsert[]   = new OrderDetail($product);
                    unset($products[$id]);
                }
            }

            $order->birthday      =    $request->birthday ? $request->birthday.'' : null;
            $order->delivery_time =    $request->delivery_time ? $request->delivery_time : null;

            $order->province_id    = (int)$order->province_id;
            $order->district_id    = (int)$order->district_id;

            $order->code          =    time();
            $order->order_qty     =    (int)$sumQty;
            $order->subtotal      =    floatval($sumPrice);
            $order->coupon_amount =    floatval($request->coupon_amount);
            $order->enhancement   =    floatval($request->enhancement);
            $order->shipping      =    (int)str_replace('.', '', $request->shipping);
			$order->deposit_note  =    (int)str_replace('.', '', $request->deposit_note);
			$order->installation_fees  =    (int)str_replace('.', '', $request->installation_fees);
            $order->order_price   =    ($order->subtotal + $order->shipping + $order->enhancement)-$order->coupon_amount;
            $order->saler_id      =    $request->saler_id ? $request->saler_id.'' : null;
            $order->user_id       =    Auth::id();
            $order->priority      =    (int)str_replace('.', '', $request->priority);
            $order->type          =    $this->_data['type'];
            $order->status        = ($request->status) ? implode(',',$request->status) : '';
            $order->created_at    =    new DateTime();
            $order->updated_at    =    new DateTime();
            $order->save();
            $order->code          =    update_code($order->id,'DH');
            $order->save();
			$order->details()->saveMany($dataInsert);
			
			// if($this->_data['type'] === 'wholesale') Mail::to('baohanhvtp@gmail.com')->send(new OrderAlert($order));

            return redirect()->route('admin.order.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$order->code.'</b> thành công');
        }
        
    }

    public function show($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $this->_data['item'] = Order::find($id);
        } else {
            $this->_data['item'] = Order::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            $products = [];
            if($this->_data['products'] !== null){
                foreach($this->_data['products'] as $key => $val){
                    $products[$key]['id']       =  $val->product_id;
                    $products[$key]['code']     =  $val->product_code;
                    $products[$key]['price']    =  $val->product_price;
                    $products[$key]['price_second']    =  $val->product_price_second;
                    $products[$key]['price_virtual']    =  $val->product_price_virtual;
                    $products[$key]['price_discount']    =  $val->product_price_discount;
                    $products[$key]['qty']      =  $val->product_qty;
                    $products[$key]['title']    =  $val->product_title;
                }
                $this->_data['products'] = $products;
            }
            return view('admin.orders.show',$this->_data);
        }
        return redirect()->route('admin.order.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function edit($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $this->_data['item'] = Order::find($id);
        } else {
            $this->_data['item'] = Order::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            $this->_data['sales'] = Member::where('type','saler')->get();
            $products = [];
            if($this->_data['products'] !== null){
                foreach($this->_data['products'] as $key => $val){
                    $products[$key]['id']       =  $val->product_id;
                    $products[$key]['code']     =  $val->product_code;
                    $products[$key]['price']    =  $val->product_price;
                    $products[$key]['price_second']    =  $val->product_price_second;
                    $products[$key]['price_virtual']    =  $val->product_price_virtual;
                    $products[$key]['price_discount']    =  $val->product_price_discount;
                    $products[$key]['qty']      =  $val->product_qty;
                    $products[$key]['title']    =  $val->product_title;
                }
                $this->_data['products'] = $products;
            }
            return view('admin.orders.edit',$this->_data);
        }
        return redirect()->route('admin.order.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
    	// dd($request->input('products'));
    	if( $request->update_form_list && ($request->received_amount || $request->deposit_amount || $request->installation_fees) ){
    		$order = Order::find($id);
    		if ($order !== null) {
    			if( $request->received_amount ) $order->received_amount = floatval(str_replace('.', '', $request->received_amount));
    			if( $request->deposit_amount ) $order->deposit_amount = floatval(str_replace('.', '', $request->deposit_amount));
    			if( $request->installation_fees ) $order->installation_fees = floatval(str_replace('.', '', $request->installation_fees));
    			$order->save();
    			return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$order->name.'</b> thành công');
    		}
    		return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
    	}

        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            ], [
            'products.required' => 'Vui lòng chọn sản phẩm',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $order = Order::find($id);

            if ($order !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $order->$field = $value;
                    }
                }
                
                $inputProduct = $request->products;
                $products = [];
                $product  = [];
                $sumPrice = 0;
                $sumQty   = 0;
                $dataInsert = [];
                foreach($inputProduct as $key => $value){
                    $id    = (int)$value['id'];
                    if( !isset($products[$id]) ){
                        $products[$id]['title']  =  $value['title'];
                        $products[$id]['code']   =  strtoupper($value['code']);
                        $products[$id]['price']  =  (int)$value['price'];
                        $products[$id]['price_second']  =  (int)$value['price_second'];
                        $products[$id]['price_virtual']  =  (int)$value['price_virtual'];
                        $products[$id]['price_discount']  =  (int)$value['price_discount'];
                        @$products[$id]['qty']  +=  (int)$value['qty'];
                    }else{
                        @$products[$id]['qty']  +=  (int)$value['qty'];
                        unset($inputProduct[$key]);
                    }
                }
                array_values($inputProduct);
                foreach($inputProduct as $key => $value){
                    $id    = (int)$value['id'];
                    if( isset($products[$id]) ){
                        $product['product_id']    =   $id;
                        $product['product_code']  =   $products[$id]['code'];
                        $product['product_title'] =   $products[$id]['title'];
                        $product['product_qty']   =   $products[$id]['qty'];
                        $product['product_price'] =   $products[$id]['price'];
                        $product['product_price_second'] =   $products[$id]['price_second'];
                        $product['product_price_virtual'] =   $products[$id]['price_virtual'];
                        $product['product_price_discount'] =   $products[$id]['price_discount'];
                        
                        $sumPrice       += ($products[$id]['price']-$product['product_price_discount'])*$products[$id]['qty'];
                        $sumQty         += $products[$id]['qty'];
                        $dataInsert[]   = new OrderDetail($product);
                        unset($products[$id]);
                    }
                }

                $order->birthday      =    $request->birthday ? $request->birthday.( strlen($request->birthday) < 15 ? '' : '') : null;
                $order->delivery_time =    $request->delivery_time ? $request->delivery_time : null;

                $order->province_id   =    (int)$order->province_id;
                $order->district_id   =    (int)$order->district_id;

                $order->order_qty     =    (int)$sumQty;
                $order->subtotal      =    floatval($sumPrice);
                $order->coupon_amount =    floatval($request->coupon_amount);
                $order->enhancement   =    floatval($request->enhancement);
                $order->shipping      =    (int)str_replace('.', '', $request->shipping);
                $order->deposit_note  =    (int)str_replace('.', '', $request->deposit_note);
                $order->deposit_amount  =    (int)str_replace('.', '', $request->deposit_amount);
                $order->installation_fees  =    (int)str_replace('.', '', $request->installation_fees);
                $order->order_price   =    ($order->subtotal + $order->shipping + $order->enhancement)-$order->coupon_amount;
                $order->saler_id      =    $request->saler_id ? $request->saler_id.'' : null;
                $order->priority      =    (int)str_replace('.', '', $request->priority);
                $order->type          =    $this->_data['type'];
                $order->updated_at    =    new DateTime();
                $order->save();
                OrderDetail::whereIn('id',$order->details()->pluck('id')->toArray())->delete();
                $order->details()->saveMany($dataInsert);
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$order->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $order = Order::find($id);
        } else {
            $order = Order::where('user_id',Auth::id())->find($id);
        }
        $deleted = $order->name;
        if ($order !== null) {
            if( $order->delete() ){
                OrderDetail::whereIn('id',$order->details()->pluck('id')->toArray())->delete();
                return redirect()->route('admin.order.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.order.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.order.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }
}
