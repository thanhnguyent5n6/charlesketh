<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Setting;

use DateTime;

class SettingController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(){
    	$this->_data['pageTitle'] = 'Cấu hình website';
    	$this->_data['siteconfig'] = config('siteconfig');
    	$this->_data['default_language'] = config('siteconfig.general.language');
    	$this->_data['languages'] = config('siteconfig.languages');
    	$this->_data['path'] = 'uploads/photos';
    }

    public function index(){

    	$items = Setting::all();
    	if( $items !== null ){
    		foreach( $items as $k => $v ){
                
    			$this->_data['item'][$v['name']] = is_array(json_decode($v['value'],true)) ? json_decode($v['value'],true) : $v['value'];
    		}
    	}

    	return view('admin.settings.index', $this->_data);
    }

    public function store(Request $request){
        if($request->hasFile('files')){
            foreach( $request->file('files') as $field => $file ){
                $setting = Setting::where('name',$field)->first();
                if($setting !== null){
                    delete_image($this->_data['path'].'/'.$setting->value, null);
                }
                $fileName = save_image( $this->_data['path'],$file, null );
                Setting::updateOrCreate(
                    ['name' => $field ],
                    ['value' => $fileName ]
                );
            }
        }
        if($request->data){
            foreach($request->data as $field => $value){
                Setting::updateOrCreate(
                	['name' => $field ],
                	['value' => is_array($value) ? json_encode($value) : $value ]
	            );
            }
        }
        return redirect()->route('admin.setting.index')->with('success','Lưu cấu hình website thành công');
    }
}
