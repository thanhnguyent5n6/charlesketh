<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Delivery;
use App\Member;
use App\Order;
use App\OrderDetail;

use DNS1D;
use DateTime;
use Carbon\Carbon;

class DeliveryController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.delivery');
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

        $items = Delivery::where('type',$this->_data['type'])
            ->orderBy('priority','asc')
            ->orderBy('id','desc');

        if( $keyword = $request->keyword ){
        	$items->where(function($query) use ($keyword){
        		$query->where('code',$keyword)
        			->orWhere('order_code','like','%'.$keyword.'%');
        	});
        }
        if( $request->from_at ){
            $items->where('created_at', '>=', $request->from_at);
        }if( $request->to_at ){
            $items->where('created_at', '<=', $request->to_at);
        }

        $this->_data['items'] = $items->paginate(25);

        return view('admin.deliveries.index',$this->_data);
    }

        public function print(Request $request){
        if($request->ajax() && $request->id){
            // $province = json_decode(str_replace('var province = ','',file_get_contents(public_path().'/jsons/province.js')),true);
            // $district = json_decode(str_replace('var district = ','',file_get_contents(public_path().'/jsons/district.js')),true);
            $arrID = explode(',',$request->id);
            $items = Delivery::whereIn('id',$arrID)->get();
            if ($items !== null) {
                $data = '';
                foreach($items as $k => $item){
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
                                <th width="1%" style="text-align: center !important; border:1px solid black !important"> STT </th>
                                <th width="35%" style="text-align: center !important; border:1px solid black !important"> M?? ????n h??ng </th>
                                <th width="10%" style="text-align: center !important; border:1px solid black !important"> Th??nh ti???n </th>
                            </tr>
                        </thead>
                        <tbody>';
                    $total = 0;
                    if( $orders = explode(',',$item->order_code) ){
                        foreach($orders as $key => $val){
                            $order = Order::where('code',$val)->firstOrFail();
                            $total += $order->order_price;
                            $table .= '<tr style="font-size:11px">
                                <td align="center">'.($key+1).'</td>
                                <td>'.$val.'</td>
                                <td align="center">'.get_currency_vn($order->order_price,'').'</td>
                            </tr>';
                        }
                    }

                    $table .= '<tr>
                                <td align="right" colspan="2">
                                    <span class="pull-right text-uppercase bold text-center">
                                        T???ng s??? ti???n:
                                    </span>
                                </td>
                                <td align="center">
                                    <span class="font-red-mint font-md bold">'.get_currency_vn($total,'').'</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>';

                    $data .= '<div class="container">
                        <div class="row" style="border-bottom:1px solid black">
                            <div class="col-xs-3">
                                <img src="/public/images/logo-vtp-in3.png" style="width:90px;display:block;margin: 0px auto;">
                            </div>
                            <div class="col-xs-9">
                                <div><b>C??NG TY C??? PH???N ??I???N M??Y <span style="color: #f00">V???N TH???NH PH??T</span></b></div>
                                <div>M?? s??? thu???: <b>0312629851</b></div>
                                <div>Tr??? s???: 253 Ph???m ????ng Gi???ng, Ph?????ng B??nh H??ng H??a, Qu???n B??nh T??n, TP.HCM</div>
                                <div>VPGD: 636 - 638, Tr?????ng Chinh,Ph?????ng 15, Qu???n T??n B??nh, TP.HCM</div>
                                <div class="row">
                                    <div class="col-xs-7">??i???n tho???i: <b style="color: #f00">(028) 62.600.800</b> - Fax: <b style="color: #f00">(028) 62.699.800</b></div>
                                    <div class="col-xs-5">Hotline: <b style="color: #f00">0901.100.100</b></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-9">
                                <h2 style="text-align: left;font-weight:bold">PHI???U GIAO H??NG</h2>
                            </div>
                            <div class="col-xs-3">
                                <br/>
                                <img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4"><i>M?? kho:</i></div>
                            <div class="col-xs-4"><i>Gi??? giao h??ng:</i></div>
                            <div class="col-xs-4">M?? giao h??ng: '.$item->code.'</div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-xs-12">
                                <b>Th??ng tin ????n h??ng</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                '.$table.'
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-xs-6">
                                <center> <b>Nh??n vi??n giao h??ng</b> </center>
                            </div>
                            <div class="col-xs-6">
                                <center> <b>K??? to??n</b> </center>
                            </div>
                        </div>
                        <br><br><br>
                        <div class="row">
                            <div class="col-xs-12">
                                <b>*Ghi ch??:</b> '.$item->note.'
                            </div>
                        </div>
                        
                    </div>';
                }
                return response()->json(['data'=>$data]);
            }
        }
    }

    public function create(){
        $this->_data['members'] = Member::where('type','dirver')->whereRaw("FIND_IN_SET('publish',status)")->get();
        return view('admin.deliveries.create',$this->_data);
    }

    public function store(Request $request){
    	$valid = Validator::make($request->all(), [
            'order_code'          => 'required',
            ], [
            'order_code.required' => 'Vui l??ng ch???n ????n h??ng',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $delivery  = new Delivery;

            if($request->data){
                foreach($request->data as $field => $value){
                    $delivery->$field = $value;
                }
            }
            $delivery->code = time();
            $delivery->order_code    =    ($request->order_code) ? implode(',',$request->order_code) : '';
            $delivery->priority      =    (int)str_replace('.', '', $request->priority);
            $delivery->status        =    ($request->status) ? implode(',',$request->status) : '';
            $delivery->type          =    $this->_data['type'];
            $delivery->user_id       =    Auth::id();
            $delivery->member_id     =    $request->member_id;
            $delivery->created_at    =    new DateTime();
            $delivery->updated_at    =    new DateTime();
            $delivery->save();
            $delivery->code          =    update_code($delivery->id,'PGH');
            $delivery->save();
            return redirect()->route('admin.delivery.index',['type'=>$this->_data['type']])->with('success','Th??m d??? li???u <b>'.$delivery->code.'</b> th??nh c??ng');

        }
    }

    public function edit($id){
        $this->_data['members'] = Member::where('type','dirver')->whereRaw("FIND_IN_SET('publish',status)")->get();
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $this->_data['item'] = Delivery::find($id);
        } else {
            $this->_data['item'] = Delivery::where('user_id',Auth::id())->find($id);
        }

        if ($this->_data['item'] !== null) {
            return view('admin.deliveries.edit',$this->_data);
        }
        return redirect()->route('admin.delivery.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'order_code'          => 'required',
            ], [
            'order_code.required' => 'Vui l??ng ch???n ????n h??ng',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $delivery  = Delivery::find($id);
            if ($delivery !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $delivery->$field = $value;
                    }
                }
                $delivery->order_code    =    ($request->order_code) ? implode(',',$request->order_code) : '';
                $delivery->priority      =    (int)str_replace('.', '', $request->priority);
                $delivery->status        =    ($request->status) ? implode(',',$request->status) : '';
                $delivery->type          =    $this->_data['type'];
                $delivery->member_id     =    $request->member_id;
                $delivery->updated_at    =    new DateTime();
                $delivery->save();
                return redirect( $request->redirects_to )->with('success','C???p nh???t d??? li???u <b>'.$delivery->name.'</b> th??nh c??ng');
            }
            return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng t???n t???i');
        }
    }

    public function delete($id){
        if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  ) {
            $delivery = Delivery::find($id);
        } else {
            $delivery = Delivery::where('user_id',Auth::id())->find($id);
        }
        $deleted = $delivery->name;
        if ($delivery !== null) {
            if( $delivery->delete() ){
                return redirect()->route('admin.delivery.index',['type'=>$this->_data['type']])->with('success', 'X??a d??? li???u <b>'.$deleted.'</b> th??nh c??ng');
            }else{
                return redirect()->route('admin.delivery.index',['type'=>$this->_data['type']])->with('danger', 'X??a d??? li???u b??? l???i');
            }
        }
        return redirect()->route('admin.delivery.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

}
