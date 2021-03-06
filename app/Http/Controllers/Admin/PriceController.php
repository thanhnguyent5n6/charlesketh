<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

use App\Price;
use App\PriceDetail;
use App\User;

use Excel;
use DateTime;
use Carbon\Carbon;
use DNS1D;

class PriceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){

        $items = Price::orderBy('priority','asc')->orderBy('id','desc');

        if ( !Auth::user()->hasRole('admin') && Auth::user()->groups()->first()->id != 5 && Auth::user()->groups()->first()->id != 11 && Auth::user()->groups()->first()->id != 14 ) {
        	$items->where('user_id', Auth::id());
        } elseif( $request->user_id ){
			$items->where('user_id', $request->user_id);
        }

        $this->_data['items'] = $items->with('user','details')->paginate(25);

        return view('admin.prices.index',$this->_data);
    }

    public function ajax(Request $request){
        if($request->ajax()){
            $data['items'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
                ->select('A.*','B.title')
                ->whereRaw("(A.code LIKE '%".$request->q."%' OR B.title LIKE '%".$request->q."%') AND FIND_IN_SET('publish',A.status)")
                ->where('B.language', 'vi')
                ->where('A.type',$request->t)
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get();
            return response()->json($data);
        }
    }

    public function export(Request $request){
        $fileExt = $request->extension;
        $item = Price::where('id',$request->id)->with('details')->firstOrFail()->toArray();
		$filename = $item['code'];

        return Excel::create($filename, function($excel) use ($item, $request) {
            $excel->sheet('B???ng B??o Gi??', function($sheet) use ($item, $request) {
            	$sheet->loadView('excel.price')->with(['data'=>$item]);
            });
        })->download($fileExt);
	}

    public function print(Request $request){
    	if($request->ajax() && $request->id){
        	$arrID = explode(',',$request->id);
    		$items = Price::whereIn('id',$arrID)->get();
	        if ($items !== null) {
	        	$data = '';
	        	foreach($items as $k => $item){
		        	$user = User::find($item->user_id);
		        	if($k > 0){
		        		$data .= '<div style="page-break-after: always;"></div>';
		        	}
		        	if( strpos($item->status,'printed') === false ){
	        			$item->status .= ',printed';
	        			$item->save();
	        		}

	        		$table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
			        <thead>
			            <tr class="text-uppercase">
			                <th width="7%" align="center" style="border:1px solid black !important"> STT </th>
			                <th width="15%" align="center" style="border:1px solid black !important"> T??N H??NG </th>
                            <th width="10%" align="center" style="border:1px solid black !important"> MODEL </th>
                            <th width="6%" align="center" style="border:1px solid black !important"> SL </th>
			                <th width="8%" align="center" style="border:1px solid black !important"> ????N GI?? </th>
			                <th width="15%" align="center" style="border:1px solid black !important"> TH??NH TI???N</th>
			            </tr>
			        </thead>
			        <tbody>';
			        if($products = $item->details()->get()){
	                    $total = 0;
		                foreach($products as $key => $val){
	                        $total += $val->product_price*$val->product_qty;
                            $table .= '<tr style="font-size:11px">
                                <td align="center"><b>'.($key+1).'</b></td>
                                <td><b>'.$val->product_title.'</b></td>
                                <td align="center"><b>'.$val->product_code.'</b></td>
                                <td align="center">'.$val->product_qty.'</td>
                                <td align="center">'.get_currency_vn($val->product_price,'').'</td>
                                <td align="center">'.get_currency_vn($val->product_price*$val->product_qty,'').'</td>
                            </tr>';
		                }
		            }
		            $table .= '<tr>
				                <td align="right" colspan="30">
				                    <span class="pull-right text-uppercase">
				                         <span class="font-red-mint font-md bold" style="font-weight: 600; font-size: 18px">T???ng: '.get_currency_vn($total,'').'</span>
				                    </span>
				                </td>
				            </tr>
				        </tbody>
				    </table>';

		            $data .= '<div class="container">
		            	<div class="row" style="border-bottom: 1px solid black; padding-bottom: 10px">
							<div class="col-xs-3">
								<img src="/public/images/logo.jpg" alt="logo">
							</div>
							<div class="col-xs-9">
								<div><b style="font-size: 17px">C??NG TY TNHH ??I???N M??Y GI?? S???</b></div>
                                <div>M?? s??? thu???: <b>0315626048</b></div>
                                <div>Si??u Th???: F1/7D H????ng L??? 80, V??nh L???c A, H B??nh Ch??nh, TP H??? Ch?? Minh</div>
                                <div>Hotline: <b style="color: #f00">0828 100 100</b></div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 text-center"><h2 class="print-title"><b>B???NG B??O GI??</b></h2><small style="text-center"><b>Gi??? In: '.Carbon::now().'</b></small></div>
						</div>
                        <div class="row" style="font-size: 16px">
                            <div class="col-xs-12"><b>I/ TH??NG TIN KH??CH H??NG</b></div>
                            <div class="col-xs-12"><b>Ng?????i nh???n</b>: '.$item->name.'</div>
                            <div class="col-xs-12"><b>??i???n tho???i</b> : '.$item->phone.'</div>
                            <div class="col-xs-12"><b>?????a ch???</b>    : '.$item->address.'</div>
                            <div class="col-xs-12"><b>Email</b>       : '.$item->email.'</div>
                        </div>
                        <br>
						<div class="row">
							<div class="col-xs-12" style="font-size: 16px"><b>II/ N???I DUNG B??O GI?? S???N PH???M</b></div>
							<div class="col-xs-12">
								'.$table.'
							</div>
						</div>
						<div class="row mt-2" style="font-size: 15px">
							<div class="col-xs-12">
								<p><b>1. Th??ng Tin Ng??n H??ng</b></p>
                                <ul>
                                    <li>Ng??n H??ng Sacombank t??i kho???n s???: 060218302151 ??? PGD B?? Qu???o, T??n B??nh</li>
                                    <li>Ng??n h??ng Vietcombank (VCB) t??i kho???n s???: 0441000797349 ??? PGD Etown</li>
                                </ul>
                                <p><b>2. Gi?? tr??n ???? bao g???m VAT</b></p>
                                <p><b>3. B??o gi?? c?? hi???u l???c trong th???i h???n 7 ng??y k??? t??? ng??y l???p b??o gi??</b></p>
                                <p><b>4. '.$item->note.'.</b></p>
							</div>
                            <div class="col-xs-6">
                            </div>
                            <div class="col-xs-6 text-center">
                                <h4><b>C??NG TY TNHH ??I???N M??Y GI?? S???</b></h4>
                            </div>
						</div>			
					</div>';
				}
	            return response()->json(['data'=>$data]);
	        }
    	}
    }
    
    public function create(){
        return view('admin.prices.create');
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
            $price  = new Price;

            if($request->data){
                foreach($request->data as $field => $value){
                    $price->$field = $value;
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
                    
                    $sumPrice       += $products[$id]['price']*$products[$id]['qty'];
                    $sumQty         += $products[$id]['qty'];
                    $dataInsert[]   = new PriceDetail($product);
                    unset($products[$id]);
                }
            }


            $price->code          =    time();
            $price->user_id       =    Auth::id();
            $price->priority      =    (int)str_replace('.', '', $request->priority);
            $price->status        = 	($request->status) ? implode(',',$request->status) : '';
            $price->created_at    =    new DateTime();
            $price->updated_at    =    new DateTime();
            $price->save();
            $price->code          =    update_code($price->id,'PBG');
            $price->save();
			$price->details()->saveMany($dataInsert);

            return redirect()->route('admin.price.index')->with('success','Th??m d??? li???u <b>'.$price->code.'</b> th??nh c??ng');
        }
        
    }

    public function show($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5 || Auth::user()->groups()->first()->id == 11 || Auth::user()->groups()->first()->id == 14 ) {
            $this->_data['item'] = Price::find($id);
        } else {
            $this->_data['item'] = Price::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            $products = [];
            if($this->_data['products'] !== null){
                foreach($this->_data['products'] as $key => $val){
                    $products[$key]['id']       =  $val->product_id;
                    $products[$key]['code']     =  $val->product_code;
                    $products[$key]['price']    =  $val->product_price;
                    $products[$key]['qty']      =  $val->product_qty;
                    $products[$key]['title']    =  $val->product_title;
                }
                $this->_data['products'] = $products;
            }
            return view('admin.prices.show',$this->_data);
        }
        return redirect()->route('admin.price.index')->with('danger', 'D??? li???u kh??ng t???n t???i');
	}

    public function edit($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $this->_data['item'] = Price::find($id);
        } else {
            $this->_data['item'] = Price::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            $products = [];
            if($this->_data['products'] !== null){
                foreach($this->_data['products'] as $key => $val){
                    $products[$key]['id']       =  $val->product_id;
                    $products[$key]['code']     =  $val->product_code;
                    $products[$key]['price']    =  $val->product_price;
                    $products[$key]['qty']      =  $val->product_qty;
                    $products[$key]['title']    =  $val->product_title;
                }
                $this->_data['products'] = $products;
            }
            return view('admin.prices.edit',$this->_data);
        }
        return redirect()->route('admin.price.index')->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function update(Request $request, $id){

        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            ], [
            'products.required' => 'Vui l??ng ch???n s???n ph???m',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $price = Price::find($id);

            if ($price !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $price->$field = $value;
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
                        
                        $sumPrice       += $products[$id]['price']*$products[$id]['qty'];
                        $sumQty         += $products[$id]['qty'];
                        $dataInsert[]   = new PriceDetail($product);
                        unset($products[$id]);
                    }
                }
                $price->priority      =    (int)str_replace('.', '', $request->priority);
                $price->updated_at    =    new DateTime();

                $price->save();
                PriceDetail::whereIn('id',$price->details()->pluck('id')->toArray())->delete();
                $price->details()->saveMany($dataInsert);

                return redirect( $request->redirects_to )->with('success','C???p nh???t d??? li???u <b>'.$price->name.'</b> th??nh c??ng');
            }
            return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng t???n t???i');
        }
    }

    public function delete($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $price = Price::find($id);
        } else {
            $price = Price::where('user_id',Auth::id())->find($id);
        }
        $deleted = $price->name;
        if ($price !== null) {
            if( $price->delete() ){
                PriceDetail::whereIn('id',$price->details()->pluck('id')->toArray())->delete();
                return redirect()->route('admin.price.index')->with('success', 'X??a d??? li???u <b>'.$deleted.'</b> th??nh c??ng');
            }else{
                return redirect()->route('admin.price.index')->with('danger', 'X??a d??? li???u b??? l???i');
            }
        }
        return redirect()->route('admin.price.index')->with('danger', 'D??? li???u kh??ng t???n t???i');
    }
}
