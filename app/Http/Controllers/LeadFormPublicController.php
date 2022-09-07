<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\LeadForm;
use App\Company;
use App\Http\Requests\LeadSetting\StoreLeadDataPublic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\LeadPublicEmail;

class LeadFormPublicController extends BaseController
{
    

    public function __construct()
    {
        //parent::__construct();
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    
    
    /**
     * @return array
     */
    public function getFormData(Request $request, $id)
    {
        $leadForm = LeadForm::where('company_id', $id)->get();
        
        //$form_html ='<link href="https://app.indema.co/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">';
        $form_html ='<link href="https://app.indema.co/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">';
//        $form_html .='<style>*{box-sizing:border-box;}
//        form{max-width:600px;margin:0 auto;font: 400 13.3333px Arial;padding:0 15px;font-family:Arial;}
//        form .form-group{margin:0 -15px;display:flex;align-items:center;}
//        form .form-group>*{margin-bottom:10px}form .form-group>label{padding:0 15px;width:100px}
//        .form-control {display: block;width: 100%;height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem;font-size: 1rem;font-weight: 400;line-height: 1.5;color: #495057;background-color: #fff;background-clip: padding-box;border: 1px solid #ced4da;border-radius: .25rem;transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;} .submit{background:#007bff;border:none;height:35px;width:auto;padding:0 30px;margin:0 auto;display:block;line-height:37px;border-radius:5px;color:#FFF}</style>';
//        
         $form_html .='<style>*{box-sizing:border-box;}
        form{max-width:600px;margin:0 auto;font: 400 13.3333px Arial;padding:0 15px;font-family:Arial;}
        form .form-group{margin:0 -15px;display:flex;align-items:center;flex-wrap:wrap;}
        form .form-group>*{margin-bottom:10px}form .form-group>label{padding:0 15px;width:80px}
        .form-control {display: block;width:calc(100% - 80px);height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem;font-size: 1rem;font-weight: 400;line-height: 1.5;color: #495057;background-color: #fff;background-clip: padding-box;border: 1px solid #ced4da;border-radius: .25rem;transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;} 
		.submit{cursor:pointer;background:#007bff;border:none;height:35px;width:auto;padding:0 30px;margin:0 auto;display:block;line-height:37px;border-radius:5px;color:#FFF}
		.help-block{display: block;width: 100%;padding-left: 80px;font-size: 11px;color: red;}
		iframe{display:block;margin: 0 auto}</style>';
        

        $form_html .='<form id="lead-container" action="#" method="POST">';
        $form_html .='<input type="hidden" name="company_id" id="company_id" value="'.$id.'">';
        $form_html .='<input type="hidden" name="_token" id="_token" value="'.csrf_token().'">';
        $form_html .='<div class="form-group"><label>Name</label><input  type="text" name="lead_name" id="lead_name" value="" class="form-control"></div>';
        $form_html .='<div class="form-group"><label>Email</label><input  type="text" name="lead_email" id="lead_email" value="" class="form-control"></div>';
        $form_html .='<div class="form-group"><label>Phone</label><input  type="text" name="lead_phone" id="lead_phone" value="" class="form-control"></div>';
        $form_html .='<div class="form-group"><label>Message</label><input  type="text" name="lead_message" id="lead_message" value="" class="form-control"></div>';
        
        
        if($leadForm) {
            foreach ($leadForm as $form) {
                $label_name = ucfirst($form->field_name);
                $label_id = $form->id;
                $label_field_name = str_replace(' ', '_', strtolower($form->field_name));
                $form_html .='<div class="form-group"><label>'.$label_name.'</label><input type="text" name="lead_extra['.$label_field_name.']" id="lead_extra-'.$label_id.'" value="" class="form-control"></div>';
            }
        }
        
        $form_html .='<div class="form-group"><button type="button" class="submit lead-attendance">Submit</button></div>';
        
        $form_html .='</form>';
        
        $form_html .='<script src="https://app.indema.co/plugins/bower_components/jquery/dist/jquery.min.js"></script>';
         $form_html .='<script src="https://app.indema.co/plugins/bower_components/toast-master/js/jquery.toast.js"></script>';
        $form_html .='<script src="https://app.indema.co/plugins/froiden-helper/helper.js"></script>';
        $form_html .='<script>$("#lead-container").on("click", ".lead-attendance", function () {
        $.easyAjax({
            url: "'.route('leadpublic.store').'",
            type: "POST",
            container: "#lead-container",
            data: $("#lead-container").serialize(),
            success: function (response) {
                if(response.status == "success"){
                }
            }
        })
        });</script>';
        
        echo $form_html;
        exit;
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeadDataPublic $request)
    {
        $company_id = $request->company_id?$request->company_id:'';
        $lead_name = $request->lead_name?$request->lead_name:'';
        $lead_email = $request->lead_email?$request->lead_email:'';
        $lead_phone = $request->company_id?$request->lead_phone:'';
        $lead_message = $request->lead_message?$request->lead_message:'';
        $lead_extra = $request->lead_extra?$request->lead_extra: array();
        $lead_extra = json_encode($lead_extra);
        $company = Company::find($company_id);
        
       if(!empty($company_id) && !empty($lead_name) && !empty($lead_email) && !empty($lead_phone) && $company){
           try {
                DB::table('leads')->insert([
                    'company_id' =>  $company_id,
                    'client_name'=> $lead_name,
                    'client_email'=> $lead_email,
                    'mobile'=> $lead_phone,
                    'note'=> $lead_message,
                    'lead_extra' => $lead_extra,
                    'created_at'=>Carbon::now(),
                ]);
                
                $objDemo = new \stdClass();
                
                $objDemo->companyName = $company->company_name;
                $objDemo->LeadName = $lead_name;
                $objDemo->LeadEmail = $lead_email;
                $objDemo->LeadPhone = $lead_phone;
                
                $objDemo->Subject = "New Lead has been added - $lead_name";
                $objDemo->FromEmail = $company->company_email;
                $objDemo->FromName = $company->company_name;
                $to = $company->company_email;
                //$to = 'shahbazjamil@gmail.com';
                
                Mail::to($to)->send(new LeadPublicEmail($objDemo));
                
                return Reply::success(__('messages.addSuccess'));
            } catch (\Throwable $th) {
                return Reply::error('Sorry, required fields are missing. Please review the highlighted red area(s) below to proceed.');
            }
           
       } else {
           return Reply::error('Sorry, required fields are missing. Please review the highlighted red area(s) below to proceed.');
       }
    }

}
