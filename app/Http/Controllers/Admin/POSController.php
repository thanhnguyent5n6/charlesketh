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

use DateTime;
use DNS1D;
use Carbon\Carbon;

class POSController extends Controller
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

        if( $keyword = $request->keyword ){
        	$items->where(function($query) use ($keyword){
        		$query->where('code','like','%'.$keyword.'%')
        			->orWhere('name','like','%'.$keyword.'%')
        			->orWhere('phone','like','%'.$keyword.'%');
        	});
        }
        if( $request->from_at ){
            $items->where('created_at', '>=', $request->from_at);
        }if( $request->to_at ){
            $items->where('created_at', '<=', $request->to_at);
        }if( $request->status ){
            $items->where('status_id', $request->status);
        }
        if ( !Auth::user()->hasRole('admin') && Auth::user()->groups()->first()->id != 7 ) {
        	$items->where('user_id', Auth::id());
        }
        $this->_data['total']['count'] = $items->count();
        $this->_data['total']['qty'] = $items->sum('order_qty');
        $this->_data['total']['price'] = $items->sum('order_price');

        $this->_data['items'] = $items->paginate(25);

        return view('admin.pos.index',$this->_data);
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

		        		$table = '<table class="table table-bordered table-condensed print-productlist" style="border:1px solid black">
				        <thead>
				            <tr class="text-uppercase">
				                <th width="7%" align="center" style="border:1px solid black !important"> M?? SP </th>
				                <th width="15%" align="center" style="border:1px solid black !important"> T??n SP </th>
				                <th width="8%" align="center" style="border:1px solid black !important"> Gi?? b??n</th>
	                            <th width="8%" align="center" style="border:1px solid black !important"> Gi?? k?? </th>
				                <th width="6%" align="center" style="border:1px solid black !important"> SL </th>
				                <th width="10%" align="center" style="border:1px solid black !important"> Th??nh ti???n </th>
				            </tr>
				        </thead>
				        <tbody>';
				        if($products = $item->details()->get()){
		                    $total = 0;
			                foreach($products as $key => $val){
		                        $total += $val->product_price*$val->product_qty;
	                            $table .= '<tr>
	                                <td align="center">'.$val->product_code.'</td>
	                                <td style="font-size: 20px; font-weight: 600">'.$val->product_title.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price,'').'</td>
	                                <td align="center">'.get_currency_vn($val->product_price_second,'').'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($val->product_price*$val->product_qty,'').'</td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
			            }

			            $table .= '<tr>
					                <td align="right" colspan="30">
					                    <span class="pull-left text-uppercase">
					                        S??? l?????ng: <span class="font-red-mint font-md bold">'.$item->order_qty.'</span>
					                    </span>
					                    <span class="pull-right text-uppercase">
					                        T???ng: <span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
					                    </span>
					                </td>
					            </tr>
					        </tbody>
					    </table>';

			            $data .= '<div class="container">
							<div class="row">
								<div class="col-xs-5"><h2 class="print-title">PHI???U ????? XU???T</h2></div>
								<div class="col-xs-4">
									<div>Ng??y: '.$item->created_at->format('d/m/Y').'</div>
									<div>NVKD: '.@$user->name.'</div>
									<div>M?? ????n H??ng: '.$item->code.'</div>
								</div>
								<div class="col-xs-3">
									<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
								</div>
							</div>
							<hr />
							<div class="row">
								<div class="col-xs-12"><b>TH??NG TIN ?????T H??NG</b></div>
								<div class="col-xs-7">
									Ng?????i ?????t h??ng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-5">
									S??T: <b>'.$item->phone.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									?????a ch???: <b>'.$item->address.' - '. @$district[$item->province_id][$item->district_id]['name'] .' - '. @$province[$item->province_id]['name'] .'</b><br>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-5">
									H??nh th???c thanh to??n: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
								</div>
								<div class="col-xs-7">
									<div class="row">
										<div class="col-xs-6">
											Ng??y thanh to??n:
										</div>
										<div class="col-xs-6">
											Ng??y xu???t h??a ????n:
										</div>
									</div>
								</div>
							</div>
							<br/>
							<div class="row">
								<div class="col-xs-12"><b>TH??NG TIN NH???N H??NG</b></div>
								<div class="col-xs-7">
									Ng?????i nh???n h??ng: <b>'.$item->delivery['name'].'</b>
								</div>
								<div class="col-xs-5">
									S??T: <b>'.$item->delivery['phone'].'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-7">
									?????a ch???: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b><br>
								</div>
								<div class="col-xs-5">
									Ng??y giao h??ng: <b>'.$item->delivery_time.'</b>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-12">
									Y??u c???u thu c???c: <b>'.get_currency_vn($item->deposit_note,'').'</b><br>
								</div>
							</div>
							<br/>
							<div class="row">
								<div class="col-xs-12"><b>CHI TI???T ????N H??NG</b></div>
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>

							<div class="row">
		                        <div class="col-xs-12">
		                            <b>*Ghi ch??:</b> '.$item->note.'
		                        </div>
								<div class="col-xs-12">
									<b> Th??ng tin xu???t h??a ????n GTGT:</b> (Li??n 2 l?? c??n c??? xu???t h??a ????n. Tr?????ng h???p xu???t h??a ????n ngay, VP gi??? l???i li??n 2)
								</div>
								<div class="col-xs-12">
									<b>T??n t??? ch???c, c?? nh??n:</b> '.@$item->invoice['company'].'
								</div>
								<div class="col-xs-12">
									<b>?????a ch???:</b> '.@$item->invoice['address'].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<b>M?? s??? thu???:</b>  '.@$item->invoice['tax_code'].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									* B???o l??nh thanh to??n to??n b??? gi?? tr??? h??ng h??a: Ng?????i b???o l??nh k?? v??o m???c b???o l??nh thanh to??n
								</div>
							</div>
							<div class="row mt-2">
								<div class="col-xs-3">
									<center> <b>Duy???t</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>V??n ph??ng</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Ng?????i ????? xu???t <br>'.@$user->name.'</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>B???o l??nh thanh to??n</b> </center>
								</div>
							</div><br><br><br><br>
							<div class="row mt-2">
								<div class="col-xs-3">
									<center> <b>Ki???m so??t 1 k?? t??n</b> </center><br><br><br>
									<center> <b>.........................</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Ki???m so??t 2 k?? t??n</b> </center><br><br><br>
									<center> <b>.........................</b> </center>
								</div>
								<div class="col-xs-3">
									<center> <b>Ki???m so??t 3 k?? t??n</b> </center><br><br><br>
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
		        					<b>Th??ng tin thi???t b???: V???t T??, linh ph??? ki???n & d???ch v???:</b>
		        				</div>
		        				<div class="col-xs-12">
									<table class="table table-bordered table-condensed" style="border:1px solid black !important">
										<thead>
											<tr>
												<th scope="col" style="width: 5%; text-align: center;border:1px solid black !important">STT</th>
												<th scope="col" style="width: 45%; text-align: center;border:1px solid black !important">D???ch v???</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">M?? h??ng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">S??? l?????ng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">????n gi??</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Th??nh ti???n</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="border:1px solid black !important">1</th>
												<td style="border:1px solid black !important">???ng ?????ng 1.0HP: 120K/m??t - 1.5HP: 130K/m??t - 2.0HP: 160K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">2</th>
												<td style="border:1px solid black !important">???ng ?????ng 2.5HP: 160K/m??t - 3.0HP: 180K/m??t - 3.5HP 4.0HP: 250K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">3</th>
												<td style="border:1px solid black !important">???ng ?????ng 4.5HP 6.0HP: 290K/m??t - 10.0HP: 350K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">4</th>
												<td style="border:1px solid black !important">D??y 1.0HP : 6.000??/m -  1.5HP : 7.000??/m - 2.0HP : 10.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">5</th>
												<td style="border:1px solid black !important">D??y 2.5HP : 12.000??/m - 3.0HP : 15.000??/m - 3.5HP - 4.0HP : 20.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">6</th>
												<td style="border:1px solid black !important"> D??y 4.5HP - 6.0HP : 25.000??/m - 10.0HP : 45.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">7</th>
												<td style="border:1px solid black !important">* Ph?? thi c??ng ???ng ??m: 50K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">8</th>
												<td style="border:1px solid black !important">Ru???t g??: 10.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">9</th>
												<td style="border:1px solid black !important">???ng n?????c b??nh minh: 25.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">10</th>
												<td style="border:1px solid black !important">C???p Ke: 1HP - 90.000??/c???p - 1.5HP - 100.000??/c???p - 1HP - 110.000??/c???p</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<td colspan="5" style="text-align: right;border:1px solid black !important">(??TV: 1.000??) - T???ng C???ng</td>
												<td style="border:1px solid black !important"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<div>
										<p>
											* Th???c thi???n xong l??c: ........Gi???.........ng??y........./........./..........
											<span> H??nh th???c d???ch v???: ..................................................</span> 
										</p>
									</div>
									<div>
										<p>
											* H??nh th???c thanh to??n: .....................................
											<span><b>T???ng s??? ti???n ph???i thu:</b> .............................................................</span>
										</p>
									</div>
									<div class="row">
										<div class="col-xs-3 text-center">
											Kh??ch h??ng
										</div>
										<div class="col-xs-3 text-center">
											NV K??? Thu???t
										</div>
										<div class="col-xs-3 text-center">
											Gi??m s??t
										</div>
										<div class="col-xs-3 text-center">
											Ng?????i l???p phi???u
										</div>
									</div>
								</div>
		        			</div>
							</div>';
		        		} else {
		        			$construction = '';
		        		}
			        	$table = '<table class="table table-bordered table-condensed print-productlist" style="border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> T??n h??ng h??a </th>
					                <th width="7%" style="text-align: center !important; border:1px solid black !important"> M?? h??ng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> S??? l?????ng </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> ????n gi??</th>
					                <th width="10%" style="text-align: center !important; border:1px solid black !important"> Th??nh ti???n </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0;
			                foreach($products as $key => $val){
		                        $price = $val->product_price_second > 0 ? $val->product_price_virtual > 0 ? ($val->product_price_second+$val->product_price_virtual) : $val->product_price_second : ($val->product_price+$val->product_price_virtual);
	                            $total += $price*$val->product_qty;
	                            $total_virtual += $val->product_price_virtual*$val->product_qty;
	                            $table .= '<tr>
	                            	<td align="center">'.($key+1).'</td>
	                            	<td style="font-size: 14px !important; font-weight: 600">'.$val->product_title.'</td>
	                                <td align="center">'.$val->product_code.'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($price,'').'</td>
	                                <td align="center">'.get_currency_vn($price*$val->product_qty,'').'</td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
						}
						
						if($item->coupon_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Gi???m gi??:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->coupon_amount,'').'</span>
					                </td>
					            </tr>';
					    }
					    
					    if($item->shipping > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										Ph?? v???n chuy???n:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($item->shipping,'').'</span>
								</td>
							</tr>';
						}

						$table .= '<tr>
				                <td align="right" colspan="5">
				                    <span class="pull-right text-uppercase bold text-center">
				                        T???ng s??? ti???n:
				                    </span>
				                </td>
				                <td>
				                	<span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
				                </td>
				            </tr>';

						if($item->received_amount > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										???? thanh to??n:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($item->received_amount,'').'</span>
								</td>
							</tr>';
						}						
				        if($item->received_amount > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										C??n l???i:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($total-$item->received_amount,'').'</span>
								</td>
							</tr>';
						}
				        if($total_virtual > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        C???c l???n 1:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($total_virtual,'').'</span>
					                </td>
					            </tr>';
					    }
					    if($item->deposit_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        ???? thanh to??n:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->deposit_amount,'').'</span>
					                </td>
					            </tr>';
					        $table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Ph???i Thu:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($total - $item->deposit_amount,'').'</span>
					                </td>
					            </tr>';

					    } else if($total_virtual > 0){
					    	$table .= '<tr>
				                <td align="right" colspan="5">
				                    <span class="pull-right text-uppercase bold text-center">
				                        Ph???i Thu:
				                    </span>
				                </td>
				                <td>
				                	<span class="font-red-mint font-md bold">'.get_currency_vn($total - $total_virtual - $item->received_amount,'').'</span>
				                </td>
				            </tr>';
					    }

					    $table .= '</tbody>
					    </table>';

			            $data .= '<div class="container" style="padding-left: 60px">
									<div class="row" style="border-bottom:1px solid black; padding-bottom: 10px">
										<div class="col-xs-9">
											<div><b style="font-size: 17px">C??NG TY TNHH ??I???N M??Y GI?? S???</b></div>
											<div>M?? s??? thu???: <b>0315626048</b></div>
											<div>Si??u Th???: F1/7D H????ng L??? 80, V??nh L???c A, H B??nh Ch??nh, TP H??? Ch?? Minh</div>
											<div>Hotline: <b style="color: #f00">0828 100 100 - 0828 100 200 - 028 7300 8010</b></div>
										</div>
										<div class="col-xs-3">
											<img src="/public/images/logo-2.svg" alt="logo">
										</div>
									</div>
									<div class="row">
										<div class="col-xs-9">
											<h2><b>PHI???U B??N H??NG</b></h2>
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
									<div class="row" style="font-size:15px">
										<div class="col-xs-7">Kho h??ng: V??nh L???c</div>
										<div class="col-xs-5">M?? ????n h??ng: '.$item->code.'</div>
									</div>

									<br>

									<div class="row" style="font-size:15px">
										<div class="col-xs-7">
											T??n kh??ch h??ng: <b>'.$item->name.'</b>
										</div>
										<div class="col-xs-5">
											??i???n tho???i: <b>'.$item->phone.'</b>
										</div>
									</div>

									<div class="row" style="font-size:15px">
										<div class="col-xs-12">
											?????a ch??? giao h??ng: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
											<br>
										</div>
										<div class="col-xs-12">Gi??? giao h??ng: <b>'.$item->delivery_time.'</b></div>
									</div>
									<br>

									<div class="row" style="font-size:15px">
										<div class="col-xs-12">
											Th??ng tin XH??: <b>'.@$item->invoice['company'].' - '.@$item->invoice['tax_code'].'</b>
										</div>
										<div class="col-xs-12">
											Email nh???n H??: <b>'.$item->email.'</b>
										</div>
									</div>
									<br>

									<div class="row" style="font-size:15px">
										<div class="col-xs-12">
											H??nh Th???c Thanh to??n: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
										</div>
									</div>
									<br>
									
									<div class="row">
										<div class="col-xs-12">
											<b>TH??NG TIN H??NG H??A</b>
										</div>
									</div>
							
									<div class="row">
										<div class="col-xs-12">
											'.$table.'
										</div>
									</div>
							
									<div class="row">
				                        <div class="col-xs-12">
				                            <b>*Ghi ch??:</b> '.$item->note.'
				                        </div>
									</div>
							
									<div class="row">
										<div class="col-xs-12">
											T???ng s??? ti???n (Vi???t b???ng ch???):............................................................................................................................................................................................
										</div>
									</div>
									<br>
								
									<div class="row ">
										<div class="col-xs-3" style="width: 20%">
											<center> <b>Gi??m ?????c</b> </center><br><br><br>
										</div>
										<div class="col-xs-3" style="width: 15%">
											<center> <b>Nh??n vi??n giao h??ng</b> </center>
										</div>
										<div class="col-xs-3" style="width: 15%">
											<center> <b>Th??? kho</b> </center>
										</div>
										<div class="col-xs-3" style="width: 20%">
											<center> <b>Ng?????i l???p phi???u<br><br> '.@$user->name.' </b></center>
										</div>
										<div class="col-xs-3" style="width: 30%">
											<center> <b>K?? X??c Nh???n Thanh To??n</b> </center><br>
											<center> <b>..................................vn??</b> </center>
											<center> <b>K?? T??n</b> </center><br><br><br>
											<center> <b>H??? v?? t??n:.................................</b> </center>
										</div>
									</div>
							'.$construction.'
						</div>';
					} elseif( $request->loai == 3 ) {
						$table = '<table class="table table-bordered table-condensed print-productlist" style="border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> T??n h??ng h??a </th>
					                <th width="7%" style="text-align: center !important; border:1px solid black !important"> M?? h??ng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> S??? l?????ng </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> ????n gi??</th>
					                <th width="10%" style="text-align: center !important; border:1px solid black !important"> Th??nh ti???n </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0;
			                foreach($products as $key => $val){
		                        $price = $val->product_price_second > 0 ? $val->product_price_virtual > 0 ? ($val->product_price_second+$val->product_price_virtual) : $val->product_price_second : ($val->product_price+$val->product_price_virtual);
	                            $total += $price*$val->product_qty;
	                            $total_virtual += $val->product_price_virtual*$val->product_qty;
	                            $table .= '<tr>
	                            	<td align="center">'.($key+1).'</td>
	                            	<td style="font-size: 20px; font-weight: 600">'.$val->product_title.'</td>
	                                <td align="center">'.$val->product_code.'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($price,'').'</td>
	                                <td align="center">'.get_currency_vn($price*$val->product_qty,'').'</td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
						}
						
						if($item->coupon_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Gi???m gi??:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->coupon_amount,'').'</span>
					                </td>
					            </tr>';
					    }
					    if($item->shipping > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										Ph?? v???n chuy???n:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($item->shipping,'').'</span>
								</td>
							</tr>';
						}
						if($item->received_amount > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										???? thanh to??n:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($item->received_amount,'').'</span>
								</td>
							</tr>';
						}

						$table .= '<tr>
				                <td align="right" colspan="5">
				                    <span class="pull-right text-uppercase bold text-center">
				                        T???ng s??? ti???n:
				                    </span>
				                </td>
				                <td>
				                	<span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
				                </td>
				            </tr>';

				        if($item->received_amount > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										C??n l???i:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($total-$item->received_amount,'').'</span>
								</td>
							</tr>';
						}

				        if($total_virtual > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        C???c l???n 1:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($total_virtual,'').'</span>
					                </td>
					            </tr>';
					    }

					    if($item->deposit_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        ???? thu c???c:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->deposit_amount,'').'</span>
					                </td>
					            </tr>';
					        $table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Ph???i Thu:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($total - $item->deposit_amount,'').'</span>
					                </td>
					            </tr>';
					    } else if($total_virtual > 0){
					    	$table .= '<tr>
				                <td align="right" colspan="5">
				                    <span class="pull-right text-uppercase bold text-center">
				                        Ph???i Thu:
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
							<div class="row" style="border-bottom:1px solid black">
								<div class="col-xs-3">
									<img src="/public/images/logo.jpg" alt="logo">
								</div>
								<div class="col-xs-7">
									<div><b>C??NG TY TNHH ??I???N M??Y GI?? S???</b></div>
									<div>M?? s??? thu???: <b>0315626048</b></div>
									<div>Sieu th???: F1/7D H????ng L??? 80, V??nh L???c A, H B??nh Ch??nh, TP.HCM</div>
									<div><b>Website: https://dienmaygiasi.vn</b></div>
								</div>
								<div class="col-xs-2">
									<div class="text-center">Hotline: </br><p style="font-size: 16px"><b> 0828 100 100 - 028 7300 8010</b></p></div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9">
									<h2 style="text-align: left;font-weight:bold">PHI???U THU</h2><br>
									<p>Ng??y: ......../......../........... </p>
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
								<div class="col-xs-6">
									T??n kh??ch h??ng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-6">
									??i???n tho???i: <b>'.$item->phone.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									
									?????a ch???: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>

								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b>S??? ti???n Thu C???c:</b> <span class="font-red-mint font-md bold">'.get_currency_vn($item->deposit_amount,'').'</span>
		                        </div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									T???ng s??? ti???n (Vi???t b???ng ch???):................................................................................................................................................................
								</div><br>
							</div>

							<br>
							<div class="row">
								<div class="col-xs-12">
									<b>THANH TO??N C???C CHO S???N PH???M</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>
							
							<div class="row">
		                        <div class="col-xs-12">
		                            <b>*Ghi ch??:</b> '.$item->note.'
		                        </div>
							</div><br>
							<div class="row mt-2">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Ng?????i N???p Ti??n</b> </center>
									<center> (K?? ghi r?? h??? t??n) </center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>Ng?????i L???p Phi???u</b> </center>
									<center><br><br> '.@$user->name.'  </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Th??? qu???</b> </center>
									<center> (K?? ghi r?? h??? t??n) </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>K??? to??n</b></center>
									<center> (K?? ghi r?? h??? t??n) </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Gi??m ?????c</b> </center>
									<center> (K?? ghi r?? h??? t??n) </center>
								</div>
							</div><br><br><br>
							<div>
								???? nh???n ????? s??? ti???n (Vi???t b???ng ch???): ...........................................................................................................................................
							</div>
						</div>';
					}elseif( $request->loai == 4 ){
						if( strpos($item->status,'construction') !== false ){
		        			$construction = '<br/>
		        			<div class="row" style="font-size:13px">
								<div class="col-xs-12">
		        					<b>Th??ng tin thi???t b???: V???t T??, linh ph??? ki???n & d???ch v???:</b>
		        				</div>
		        				<div class="col-xs-12">
									<table class="table table-bordered table-condensed" style="border:1px solid black !important">
										<thead>
											<tr>
												<th scope="col" style="width: 5%; text-align: center;border:1px solid black !important">STT</th>
												<th scope="col" style="width: 45%; text-align: center;border:1px solid black !important">D???ch v???</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">M?? h??ng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">S??? l?????ng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">????n gi??</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Th??nh ti???n</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="border:1px solid black !important">1</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">2</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">3</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">4</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">5</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">6</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">7</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">8</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">9</th>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<td colspan="5" style="text-align: right;border:1px solid black !important">(??TV: 1.000??) - T???ng C???ng</td>
												<td style="border:1px solid black !important"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<div>
										<p>
											* Th???c thi???n xong l??c: ........Gi???.........ng??y........./........./..........
											<span> H??nh th???c d???ch v???: ..................................................</span> 
										</p>
									</div>
									<div>
										<p>
											* H??nh th???c thanh to??n: .....................................
											<span><b>T???ng s??? ti???n ph???i thu:</b> .............................................................</span>
										</p>
									</div>
									<div class="row">
										<div class="col-xs-3 text-center">
											Kh??ch h??ng
										</div>
										<div class="col-xs-3 text-center">
											NV K??? Thu???t
										</div>
										<div class="col-xs-3 text-center">
											Gi??m s??t
										</div>
										<div class="col-xs-3 text-center">
											Ng?????i l???p phi???u
										</div>
									</div>
								</div>
		        			</div>
							</div>';
		        		} else {
		        			$construction = '';
		        		}
			        	$table = '<table class="table table-bordered table-condensed print-productlist" style="border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> T??n h??ng h??a </th>
					                <th width="7%" style="text-align: center !important; border:1px solid black !important"> M?? h??ng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> S??? l?????ng </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> ????n gi??</th>
					                <th width="10%" style="text-align: center !important; border:1px solid black !important"> Th??nh ti???n </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0;
			                foreach($products as $key => $val){
		                        $price = $val->product_price_second > 0 ? $val->product_price_virtual > 0 ? ($val->product_price_second+$val->product_price_virtual) : $val->product_price_second : ($val->product_price+$val->product_price_virtual);
	                            $total += $price*$val->product_qty;
	                            $total_virtual += $val->product_price_virtual*$val->product_qty;
	                            $table .= '<tr>
	                            	<td align="center">'.($key+1).'</td>
	                            	<td style="font-size: 20px; font-weight: 600">'.$val->product_title.'</td>
	                                <td align="center">'.$val->product_code.'</td>
	                                <td align="center">'.$val->product_qty.'</td>
	                                <td align="center">'.get_currency_vn($price,'').'</td>
	                                <td align="center">'.get_currency_vn($price*$val->product_qty,'').'</td>
	                            </tr>';
			                }
		                    $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
						}
						
						if($item->coupon_amount > 0){
					    	$table .= '<tr>
					                <td align="right" colspan="5">
					                    <span class="pull-right text-uppercase bold text-center">
					                        Gi???m gi??:
					                    </span>
					                </td>
					                <td>
					                	<span class="font-red-mint font-md bold">'.get_currency_vn($item->coupon_amount,'').'</span>
					                </td>
					            </tr>';
					    }
					    
					    if($item->shipping > 0){
			            	$table .= '<tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										Ph?? v???n chuy???n:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($item->shipping,'').'</span>
								</td>
							</tr>';
						}

						$table .= '<tr>
				                <td align="right" colspan="5">
				                    <span class="pull-right text-uppercase bold text-center">
				                        T???ng s??? ti???n:
				                    </span>
				                </td>
				                <td>
				                	<span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
				                </td>
				            </tr><tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										???? thanh to??n:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
								</td>
							</tr><tr>
								<td align="right" colspan="5">
									<span class="pull-right text-uppercase bold text-center">
										C??n l???i:
									</span>
								</td>
								<td>
									<span class="font-red-mint font-md bold">'.get_currency_vn($total-$total,'').'</span>
								</td>
							</tr>';

					    $table .= '</tbody>
					    </table>';

			            $data .= '<div class="container">
							<div class="row" style="border-bottom:1px solid black">
								<div class="col-xs-9">
									<div><b>C??NG TY TNHH ??I???N M??Y GI?? S???</b></div>
									<div>M?? s??? thu???: <b>0315626048</b></div>
									<div>Si??u Th???: F1/7D H????ng L??? 80, V??nh L???c A, H B??nh Ch??nh, TP H??? Ch?? Minh</div>
									<div>Hotline: <b style="color: #f00">0828 100 100 - 0828 100 200 - 028 7300 8010</b></div>
								</div>
								<div class="col-xs-3">
									<img src="/public/images/logo-2.svg" alt="logo">
								</div>
							</div class="row">
								<div class="col-xs-9">
									<h2> <b>PHI???U B??N H??NG </b></h2>
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
								<div class="col-xs-4">M?? kho: V??nh L???c</div>
								<div class="col-xs-4">Gi??? giao h??ng: <b>'.$item->delivery_time.'</b></div>
								<div class="col-xs-4">M?? ????n h??ng: '.$item->code.'</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									T??n kh??ch h??ng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-8">
									??i???n tho???i: <b>'.$item->phone.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Thanh to??n: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									?????a ch??? giao h??ng: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-12">
									<b>TH??NG TIN H??NG H??A</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b>*Ghi ch??:</b> '.$item->note.'
		                        </div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									T???ng s??? ti???n (Vi???t b???ng ch???):............................................................................................................................................................................................
								</div>
							</div><br>
							<div class="row ">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Gi??m ?????c</b> </center><br><br><br>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Nh??n vi??n giao h??ng</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Th??? kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Ng?????i l???p phi???u<br><br> '.@$user->name.' </b></center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>K?? X??c Nh???n Thanh To??n</b> </center><br>
									<center> <b>..................................vn??</b> </center>
									<center> <b>K?? T??n</b> </center><br><br><br>
									<center> <b>H??? v?? t??n:.................................</b> </center>
								</div>
							</div>
							
							'.$construction.'
						</div>';
					} elseif( $request->loai == 5 ) {
						if( strpos($item->status,'construction') !== false ){
		        			$construction = '<br/>
		        			<div class="row" style="font-size:13px">
								<div class="col-xs-12">
		        					<b>Th??ng tin thi???t b???: V???t T??, linh ph??? ki???n & d???ch v???:</b>
		        				</div>
		        				<div class="col-xs-12">
									<table class="table table-bordered table-condensed" style="border:1px solid black !important">
										<thead>
											<tr>
												<th scope="col" style="width: 5%; text-align: center;border:1px solid black !important">STT</th>
												<th scope="col" style="width: 45%; text-align: center;border:1px solid black !important">D???ch v???</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">M?? h??ng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">S??? l?????ng</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">????n gi??</th>
												<th scope="col" style="width: 10%; text-align: center;border:1px solid black !important">Th??nh ti???n</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="border:1px solid black !important">1</th>
												<td style="border:1px solid black !important">???ng ?????ng 1.0HP: 120K/m??t - 1.5HP: 130K/m??t - 2.0HP: 160K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">2</th>
												<td style="border:1px solid black !important">???ng ?????ng 2.5HP: 160K/m??t - 3.0HP: 180K/m??t - 3.5HP 4.0HP: 250K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">3</th>
												<td style="border:1px solid black !important">???ng ?????ng 4.5HP 6.0HP: 290K/m??t - 10.0HP: 350K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">4</th>
												<td style="border:1px solid black !important">D??y 1.0HP : 6.000??/m -  1.5HP : 7.000??/m - 2.0HP : 10.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">5</th>
												<td style="border:1px solid black !important">D??y 2.5HP : 12.000??/m - 3.0HP : 15.000??/m - 3.5HP - 4.0HP : 20.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">6</th>
												<td style="border:1px solid black !important"> D??y 4.5HP - 6.0HP : 25.000??/m - 10.0HP : 45.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">7</th>
												<td style="border:1px solid black !important">* Ph?? thi c??ng ???ng ??m: 50K/m??t</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">8</th>
												<td style="border:1px solid black !important">Ru???t g??: 10.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">9</th>
												<td style="border:1px solid black !important">???ng n?????c b??nh minh: 25.000??/m</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<th scope="row" style="border:1px solid black !important">10</th>
												<td style="border:1px solid black !important">C???p Ke ????? d??n n??ng: 90.000??/c???p</td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
												<td style="border:1px solid black !important"></td>
											</tr>
											<tr>
												<td colspan="5" style="text-align: right;border:1px solid black !important">(??TV: 1.000??) - T???ng C???ng</td>
												<td style="border:1px solid black !important"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<div>
										<p>
											* Th???c thi???n xong l??c: ........Gi???.........ng??y........./........./..........
											<span> H??nh th???c d???ch v???: ..................................................</span> 
										</p>
									</div>
									<div>
										<p>
											* H??nh th???c thanh to??n: .....................................
											<span><b>T???ng s??? ti???n ph???i thu:</b> .............................................................</span>
										</p>
									</div>
									<div class="row">
										<div class="col-xs-3 text-center">
											Kh??ch h??ng
										</div>
										<div class="col-xs-3 text-center">
											NV K??? Thu???t
										</div>
										<div class="col-xs-3 text-center">
											Gi??m s??t
										</div>
										<div class="col-xs-3 text-center">
											Ng?????i l???p phi???u
										</div>
									</div>
								</div>
		        			</div>
							</div>';
		        		} else {
		        			$construction = '';
		        		}

			        	$table = '<table class="table table-bordered table-condensed print-productlist" style="border:1px solid black">
					        <thead>
					            <tr class="text-uppercase">
					            	<th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
					                <th width="15%" style="text-align: center !important; border:1px solid black !important"> T??n h??ng h??a </th>
					                <th width="7%" style="text-align: center !important; border:1px solid black !important"> M?? h??ng </th>
					                <th width="6%" style="text-align: center !important; border:1px solid black !important"> S??? l?????ng </th>
					                <th width="8%" style="text-align: center !important; border:1px solid black !important"> ????n gi??</th>
					                <th width="10%" style="text-align: center !important; border:1px solid black !important"> Th??nh ti???n </th>
					            </tr>
					        </thead>
					        <tbody>';
					    
			            if($products = $item->details()->get()){
		                    $total = 0; $total_virtual = 0;
			                foreach($products as $key => $val){
		                        $price = $val->product_price_second > 0 ? $val->product_price_virtual > 0 ? ($val->product_price_second+$val->product_price_virtual) : $val->product_price_second : ($val->product_price+$val->product_price_virtual);
	                            $total += $price*$val->product_qty;
	                            $total_virtual += $val->product_price_virtual*$val->product_qty;
	                            $table .= '<tr>
	                            	<td align="center">'.($key+1).'</td>
	                            	<td style="font-size: 20px; font-weight: 600">'.$val->product_title.'</td>
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
									<div><b>C??NG TY TNHH ??I???N M??Y GI?? S???</b></div>
									<div>M?? s??? thu???: <b>0315626048</b></div>
									<div>Tr??? s???: F1/7D H????ng L??? 80, V??nh L???c A, H B??nh Ch??nh, TP H??? Ch?? Minh</div>
									<div>Hotline: <b style="color: #f00">0828 100 100 - 028 7300 8010</b></div>
								</div>
								<div class="col-xs-3">
									<img src="/public/images/logo-2.svg" alt="logo">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9">
									<h2 style="text-align: left;font-weight:bold">PHI???U XU???T KHO</h2>
									<small style="text-center">Ng??y xu???t: '.Carbon::now().'</small>
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
								<div class="col-xs-4">M?? kho:</div>
								<div class="col-xs-4">Gi??? giao h??ng: <b>'.$item->delivery_time.'</b></div>
								<div class="col-xs-4">M?? ????n h??ng: '.$item->code.'</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									T??n kh??ch h??ng: <b>'.$item->name.'</b>
								</div>
								<div class="col-xs-8">
									??i???n tho???i: <b>'.$item->phone.'</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									T??n ng?????i nh???n: <b>'.$item->delivery['name'].'</b>
								</div>
								<div class="col-xs-4">
									??i???n tho???i: <b>'.$item->delivery['phone'].'</b>
								</div>
								<div class="col-xs-4">
									Thanh to??n: '.@$this->_data['siteconfig'][$request->type]['site']['payment'][$item->payment_id].'
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									?????a ch??? giao h??ng: <b>'.$item->delivery['address'].' - '. @$district[$item->delivery['province_id']][$item->delivery['district_id']]['name'] .' - '. @$province[$item->delivery['province_id']]['name'] .'</b>
									<br>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-7">
									Thay ?????i ?????a ch??? giao h??ng:................................................................
								</div>
								<div class="col-xs-5">
									Thay ?????i gi??? giao h??ng:.......................................................
								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12">
									<b>TH??NG TIN H??NG H??A</b>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									'.$table.'
								</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12">
		                            <b style="font-size:18px">*Ghi ch??: '.$item->note.' </b> 
		                        </div>
							</div>
							<br>
							<div class="row mt-2">
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Gi??m ?????c</b> </center><br><br><br>
									<center> <b>K??? To??n Kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Nh??n vi??n giao h??ng</b> </center>
								</div>
								<div class="col-xs-3" style="width: 15%">
									<center> <b>Th??? kho</b> </center>
								</div>
								<div class="col-xs-3" style="width: 20%">
									<center> <b>Ng?????i l???p phi???u<br><br> '.@$user->name.' </b></center>
								</div>
								<div class="col-xs-3" style="width: 30%">
									<center> <b>K?? X??c Nh???n Thanh To??n</b> </center><br>
									<center> <b>..................................vn??</b> </center>
									<center> <b>K?? T??n</b> </center><br><br><br>
									<center> <b>H??? v?? t??n:.................................</b> </center>
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

    public function customer(Request $request){
    	if($request->ajax() && $request->type){
    		$item = Order::where('type',$request->type)->where('phone',$request->phone)->firstOrFail();
    		return response()->json(['data'=>$item]);
    	}
    }
    
    public function create(){
    	$this->_data['sales'] = Member::where('type','saler')->get();
        return view('admin.pos.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            ], [
            'products.required' => 'Vui l??ng ch???n s???n ph???m',
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
                    
                    $sumPrice       += $products[$id]['price']*$products[$id]['qty'];
                    $sumQty         += $products[$id]['qty'];
                    $dataInsert[]   = new OrderDetail($product);
                    unset($products[$id]);
                }
            }

            $order->birthday      =    $request->birthday ? $request->birthday.' 00:00:00' : null;
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
            $order->deposit_amount  =    (int)str_replace('.', '', $request->deposit_amount);
            $order->received_amount  =    (int)str_replace('.', '', $request->received_amount);
            $order->installation_fees  =    (int)str_replace('.', '', $request->installation_fees);
            $order->order_price   =    ($order->subtotal + $order->shipping + $order->enhancement)-$order->coupon_amount;
            $order->saler_id      =    $request->saler_id ? $request->saler_id.'' : null;
			$order->user_id       =    Auth::id();
			$order->status         = ($request->status) ? implode(',',$request->status) : '';
            $order->priority      =    (int)str_replace('.', '', $request->priority);
            $order->type          =    $this->_data['type'];
            $order->created_at    =    new DateTime();
            $order->updated_at    =    new DateTime();
            $order->save();
            $order->code          =    update_code($order->id,'DH');
            $order->save();
            $order->details()->saveMany($dataInsert);

            $customer = Customer::firstOrCreate(
            	['phone'=>$order->phone],
            	['name'=>$order->name, 'email'=>$order->email, 'address'=>$order->address, 'birthday'=>$order->birthday, 'type'=>$order->type]
            );
            $basePrice = 500000;
            if($order->order_price <= $basePrice){
            	$customer->point += 1;
            } else {
            	$customer->point += round(($order->order_price-$basePrice)/1000000,1);
            }
            $customer->save();
            
            return redirect()->route('admin.pos.index',['type'=>$this->_data['type']])->with('success','Th??m d??? li???u <b>'.$order->code.'</b> th??nh c??ng');
        }
        
    }

    public function show($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 7  ) {
            $this->_data['item'] = Order::find($id);
        } else {
            $this->_data['item'] = Order::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            $this->_data['saler'] = Member::find($this->_data['item']->saler_id);
            $products = [];
            if($this->_data['products'] !== null){
                foreach($this->_data['products'] as $key => $val){
                    $products[$key]['id']       =  $val->product_id;
                    $products[$key]['code']     =  $val->product_code;
                    $products[$key]['price']    =  $val->product_price;
                    $products[$key]['price_second']    =  $val->product_price_second;
                    $products[$key]['price_virtual']    =  $val->product_price_virtual;
                    $products[$key]['qty']      =  $val->product_qty;
                    $products[$key]['title']    =  $val->product_title;
                }
                $this->_data['products'] = $products;
            }
            return view('admin.pos.show',$this->_data);
        }
        return redirect()->route('admin.pos.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function edit($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 7  ) {
            $this->_data['item'] = Order::find($id);
        } else {
            $this->_data['item'] = Order::where('user_id',Auth::id())->find($id);
        }

        if ( Auth::user()->hasRole('admin') && $this->_data['item'] !== null) {
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
                    $products[$key]['qty']      =  $val->product_qty;
                    $products[$key]['title']    =  $val->product_title;
                }
                $this->_data['products'] = $products;
            }
            return view('admin.pos.edit',$this->_data);
        }
        return redirect()->route('admin.pos.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
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
    			return redirect( $request->redirects_to )->with('success','C???p nh???t d??? li???u <b>'.$order->name.'</b> th??nh c??ng');
    		}
    		return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng t???n t???i');
    	}

        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            ], [
            'products.required' => 'Vui l??ng ch???n s???n ph???m',
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
                        
                        $sumPrice       += $products[$id]['price']*$products[$id]['qty'];
                        $sumQty         += $products[$id]['qty'];
                        $dataInsert[]   = new OrderDetail($product);
                        unset($products[$id]);
                    }
                }

                $order->birthday      =    $request->birthday ? $request->birthday.( strlen($request->birthday) < 15 ? ' 00:00:00' : '') : null;
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
				$order->status         = ($request->status) ? implode(',',$request->status) : '';
                $order->priority      =    (int)str_replace('.', '', $request->priority);
                $order->type          =    $this->_data['type'];
                $order->updated_at    =    new DateTime();
                $order->save();
                OrderDetail::whereIn('id',$order->details()->pluck('id')->toArray())->delete();
                $order->details()->saveMany($dataInsert);
                return redirect( $request->redirects_to )->with('success','C???p nh???t d??? li???u <b>'.$order->name.'</b> th??nh c??ng');
            }
            return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng t???n t???i');
        }
    }

    public function delete($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 7  ) {
            $order = Order::find($id);
        } else {
            $order = Order::where('user_id',Auth::id())->find($id);
        }
        $deleted = $order->name;
        if ($order !== null) {
            if( $order->delete() ){
                OrderDetail::whereIn('id',$order->details()->pluck('id')->toArray())->delete();
                return redirect()->route('admin.pos.index',['type'=>$this->_data['type']])->with('success', 'X??a d??? li???u <b>'.$deleted.'</b> th??nh c??ng');
            }else{
                return redirect()->route('admin.pos.index',['type'=>$this->_data['type']])->with('danger', 'X??a d??? li???u b??? l???i');
            }
        }
        return redirect()->route('admin.pos.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }
}
