<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\ProductSetting;
use Illuminate\Http\Request;

class ProductSettingsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.productSettings';
        $this->pageIcon = 'ti-settings';
        $this->middleware(function ($request, $next) {
            if(!in_array('messages',$this->user->modules)){
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(){
        if(!ProductSetting::first()) {
            $prodSetting = new ProductSetting();
            $prodSetting->company_id = user()->company_id;
            $prodSetting->save();
        }
        
        $this->productSettings = ProductSetting::first();
        
        return view('admin.product-settings.index', $this->data);
    }

    public function update(Request $request, $id){
        
        $prodSetting = ProductSetting::first();
        
        
        if($request->show_spec_number){ $prodSetting->show_spec_number = 'yes';}else{$prodSetting->show_spec_number = 'no';}
        if($request->show_project_name){ $prodSetting->show_project_name = 'yes';}else{$prodSetting->show_project_name = 'no';}
        if($request->show_location){ $prodSetting->show_location = 'yes';}else{$prodSetting->show_location = 'no';}
        if($request->show_category){ $prodSetting->show_category = 'yes';}else{$prodSetting->show_category = 'no';}
        if($request->show_vendor){ $prodSetting->show_vendor = 'yes';}else{$prodSetting->show_vendor = 'no';}
        if($request->show_manufacturer){ $prodSetting->show_manufacturer = 'yes';}else{$prodSetting->show_manufacturer = 'no';}
        if($request->show_notes){ $prodSetting->show_notes = 'yes';}else{$prodSetting->show_notes = 'no';}
        if($request->show_url){ $prodSetting->show_url = 'yes';}else{$prodSetting->show_url = 'no';}
        if($request->show_dimensions){ $prodSetting->show_dimensions = 'yes';}else{$prodSetting->show_dimensions = 'no';}
        if($request->show_materials){ $prodSetting->show_materials = 'yes';}else{$prodSetting->show_materials = 'no';}
        if($request->show_quantity){ $prodSetting->show_quantity = 'yes';}else{$prodSetting->show_quantity = 'no';}
        if($request->show_cost_per_unit){ $prodSetting->show_cost_per_unit = 'yes';}else{$prodSetting->show_cost_per_unit = 'no';}
        if($request->show_markup_fix){ $prodSetting->show_markup_fix = 'yes';}else{$prodSetting->show_markup_fix = 'no';}
        if($request->show_markup_per){ $prodSetting->show_markup_per = 'yes';}else{$prodSetting->show_markup_per = 'no';}
        if($request->show_freight){ $prodSetting->show_freight = 'yes';}else{$prodSetting->show_freight = 'no';}
        if($request->show_total_sale){ $prodSetting->show_total_sale = 'yes';}else{$prodSetting->show_total_sale = 'no';}
        if($request->show_msrp){ $prodSetting->show_msrp = 'yes';}else{$prodSetting->show_msrp = 'no';}
        if($request->show_acknowledgement){ $prodSetting->show_project_name = 'yes';}else{$prodSetting->show_project_name = 'no';}
        if($request->show_project_name){ $prodSetting->show_acknowledgement = 'yes';}else{$prodSetting->show_acknowledgement = 'no';}
        if($request->show_est_ship_date){ $prodSetting->show_est_ship_date = 'yes';}else{$prodSetting->show_est_ship_date = 'no';}
        if($request->show_act_ship_date){ $prodSetting->show_act_ship_date = 'yes';}else{$prodSetting->show_act_ship_date = 'no';}
        if($request->show_est_receive_date){ $prodSetting->show_est_receive_date = 'yes';}else{$prodSetting->show_est_receive_date = 'no';}
        if($request->show_act_receive_date){ $prodSetting->show_act_receive_date = 'yes';}else{$prodSetting->show_act_receive_date = 'no';}
        if($request->show_received_by){ $prodSetting->show_received_by = 'yes';}else{$prodSetting->show_received_by = 'no';}
        if($request->show_est_install_date){ $prodSetting->show_est_install_date = 'yes';}else{$prodSetting->show_est_install_date = 'no';}
        if($request->show_act_install_date){ $prodSetting->show_act_install_date = 'yes';}else{$prodSetting->show_act_install_date = 'no';}
        if($request->show_product_number){ $prodSetting->show_product_number = 'yes';}else{$prodSetting->show_product_number = 'no';}
        if($request->show_finish_color){ $prodSetting->show_finish_color = 'yes';}else{$prodSetting->show_finish_color = 'no';}
        
        
        $prodSetting->save();

        return Reply::success(__('Product Settings Updated'));
    }
}
