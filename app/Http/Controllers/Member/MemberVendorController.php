<?php

namespace App\Http\Controllers\Member;

use App\ClientVendorDetails;
use App\DataTables\Member\VendorsDataTable;
use App\Helper\Reply;
use App\Mail\VendorEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use Illuminate\Support\Facades\File;


class MemberVendorController extends MemberBaseController
{
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Vendors';
        $this->pageIcon = 'icon-people';
        $this->middleware(function ($request, $next) {
            if (!in_array('vendor', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(VendorsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/vendor'));
        
        //$vendors = DB::table('client_vendor_details')->select('id', 'vendor_name', 'company_name', 'vendor_email', 'status', 'created_at')->get();
        $this->__set('totalVendors', ClientVendorDetails::all()->count());
        
        $this->totalRecords = $this->totalVendors;
        
        return $dataTable->render('member.vendor.index', $this->data);
    }

    public function create()
    {
        $this->clients = User::allClients();
        return view('member.vendor.create', $this->data);
    }

    public function sendEmail(Request $request)
    {
        
        $objDemo = new \stdClass();
        $objDemo->Message = $request->messageText;
        $objDemo->Subject = $request->subject;
        $objDemo->FromEmail = $this->user->email;
        $objDemo->FromName = $this->user->name;
        // $data = ['message' => $request->messageText, 'subject'=> $request->subject];
        // Mail::to($request->send_Email_To)->send(new VendorEmail($data));
        Mail::to($request->send_Email_To)->send(new VendorEmail($objDemo));
        return redirect(route('member.vendor.index'));
    }

    public function update(UpdateVendorRequest $request, $id)
    {
        
        $vendor = ClientVendorDetails::find($id);

        $vendor->company_name = $request->company_name;
        $vendor->vendor_rep_name = $request->vendor_rep_name ? $request->vendor_rep_name:null;
        $vendor->rep_email = $request->rep_email ? $request->rep_email : null;
        $vendor->rep_phone = $request->rep_phone ? $request->rep_phone: null;
        $vendor->company_website = $request->company_website ? $request->company_website : null;
        $vendor->company_address = $request->company_address;
        $vendor->vendor_name = $request->vendor_name ? $request->vendor_name : null;
        $vendor->vendor_email = $request->rep_email ? $request->rep_email: null;
        $vendor->vendor_mobile = $request->rep_phone ? $request->rep_phone: null;
        $vendor->status = $request->status;
        
        $vendor->vendor_skype = $request->skype ? $request->skype: null;
        $vendor->vendor_linkedIn = $request->linkedin ? $request->linkedin : null;
        $vendor->vendor_twitter = $request->twitter ? $request->twitter : null;
        $vendor->vendor_facebook = $request->facebook ? $request->facebook : null;
        
        $vendor->vendor_gst_number = $request->gst_number;
        $vendor->vendor_shipping_address = $request->shipping_address;
        $vendor->vendor_note = $request->note;
        $vendor->vendor_category = $request->vendor_category?$request->vendor_category:null;
        $vendor->vendor_markup = $request->vendor_markup?$request->vendor_markup:0;
        
        $vendor->url = $request->url ? $request->url : null;
        $vendor->user = $request->user ? $request->user : null;
        $vendor->password = $request->password ? $request->password : null;
        
        $vendor->tags = json_encode(array());
        if($request->tags) {
            $vendor->tags =   json_encode(array_values(array_unique($request->tags)));
        }


        $vendor->save();
        
        return Reply::redirect(route('member.vendor.index'));
    }

    public function store(StoreVendorRequest $request) //StoreClientVendorRequest //Request
    {
       
        

        
        


        $vendor = new ClientVendorDetails();
        $vendor->company_name = $request->company_name;
        $vendor->vendor_rep_name = $request->vendor_rep_name;
        $vendor->rep_email = $request->rep_email;
        $vendor->company_website = $request->company_website ? $request->company_website : null;
        $vendor->company_address = $request->company_address;
        $vendor->vendor_name = $request->vendor_name;
        $vendor->vendor_email = $request->rep_email;
        $vendor->vendor_mobile = $request->rep_phone;
        $vendor->rep_phone = $request->rep_phone;

        $vendor->vendor_number = $request->vendor_number;
        $vendor->status = $request->status;
        
        $vendor->vendor_skype = $request->skype ? $request->skype : null;
        $vendor->vendor_linkedIn = $request->linkedin ? $request->linkedin: null;
        $vendor->vendor_twitter = $request->twitter ? $request->twitter: null;
        $vendor->vendor_facebook = $request->facebook ? $request->facebook: null;
        
        $vendor->vendor_gst_number = $request->gst_number;
        $vendor->vendor_shipping_address = $request->shipping_address;
        $vendor->vendor_note = $request->note;
        $vendor->vendor_category = $request->vendor_category?$request->vendor_category:null;
        $vendor->vendor_markup = $request->vendor_markup?$request->vendor_markup:0;
        
        $vendor->url = $request->url ? $request->url : null;
        $vendor->user = $request->user ? $request->user : null;
        $vendor->password = $request->password ? $request->password : null;
        
        $vendor->tags = json_encode(array());
        if($request->tags) {
            $vendor->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $vendor->save();
        
        $this->mixPanelTrackEvent('vendor_created', array('page_path' => '/member/vendor'));

        return Reply::redirect(route('member.vendor.index'), 'vendor created successfully');
    }

    public function edit($id)
    {
        $vendorDetail = DB::table('client_vendor_details')->get()->where('id', $id)->first();
        
        $tags = $vendorDetail->tags ? json_decode($vendorDetail->tags) : array();
        $vendorDetail->tags = $tags;
        
        if($tags) {
            $vendorDetail->tags = array_values(array_unique($tags));
        }
        
        return view('member.vendor.edit', $this->data)->with('vendorDetail', $vendorDetail);
    }
    
    
     public function createVendor()
    {
        return view('member.vendor.create-vendor-modal', $this->data);
    }
    
    public function storeVendor(StoreVendorRequest $request) {

        $existing_user = ClientVendorDetails::select('id', 'vendor_name')->where('vendor_name', $request->input('vendor_name'))->first();
        
        // already user can't added again 
        if ($existing_user) {
            return Reply::error('Provided Vendor name is already registered. Try with different name.');
        }
        
        $vendor_id = 0;
        if (!$existing_user) {
            $vendor = new ClientVendorDetails();
            $vendor->company_name = $request->company_name;
            $vendor->vendor_name = $request->vendor_name;
            $vendor->save();
            $vendor_id = $vendor->id;
        }  
        
        $clients = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        $select = '<option value="">Select Vendor</option>';
        foreach ($clients as $row) {
            $selected = '';
            if($row->id == $vendor_id) {
                $selected = 'selected=""';
            }
            $select .= '<option '.$selected.' value="' . $row->id . '">' . ucwords($row->company_name) . '</option>';
        }
        return Reply::successWithData('Vendor Added Successfully', ['optionData' => $select]);
    }

    public function showVendor($id)
    {
        $vendorDetail = DB::table('client_vendor_details')->get()->where('id', $id)->first();
        //return response()->json($vendorDetail, 200);
        return view('member.vendor.detail', $this->data)->with('vendorDetail', $vendorDetail);
    }

    public function export($followUp, $vendor)
    {
        $currentDate = Carbon::today()->format('Y-m-d');
        $lead = Lead::select('leads.id', 'client_name', 'website', 'client_email', 'company_name', 'lead_status.type as statusName', 'leads.created_at', 'lead_sources.type as source', \DB::raw("(select next_follow_up_date from lead_follow_up where lead_id = leads.id and leads.next_follow_up  = 'yes' and DATE(next_follow_up_date) >= {$currentDate} ORDER BY next_follow_up_date asc limit 1) as next_follow_up_date"))
            ->leftJoin('lead_status', 'lead_status.id', 'leads.status_id')
            ->leftJoin('lead_sources', 'lead_sources.id', 'leads.source_id');
        if ($followUp != 'all' && $followUp != '') {
            $lead = $lead->leftJoin('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->where('leads.next_follow_up', 'yes')
                ->where('lead_follow_up.next_follow_up_date', '<', $currentDate);
        }
        if ($vendor != 'all' && $vendor != '') {
            if ($vendor == 'lead') {
                $lead = $lead->whereNull('client_id');
            } else {
                $lead = $lead->whereNotNull('client_id');
            }
        }

        $lead = $lead->GroupBy('leads.id')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Client Name', 'Website', 'Email', 'Company Name', 'Status', 'Created On', 'Source', 'Next Follow Up Date'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($lead as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('leads', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Leads');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('leads file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold' => true
                    ));
                });
            });
        })->download('xlsx');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        $vendor_count = DB::table('client_vendor_details')->where('id', $id)->count();
        if ($vendor_count > 0) {
            DB::table('client_vendor_details')->where('id', '=', $id)->delete();
        }

        DB::commit();
        return Reply::success('vendor deleted successfully.');
    }
    
    public function downloadTemplate()
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=vendor-smaple-template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $records = array();
        $records[] = array('company_name' => 'xyz', 'company_website'=>'https://www.google.com.pk/', 'company_address'=>'xyz', 'rep_name'=>'abc', 'rep_email'=>'abcvendor@gmail.com', 'rep_phone_number'=>'123', 'vendor_number'=> '123', 'vendor_status' => 'active', 'skype'=>'', 'linkedin'=>'', 'twitter'=>'', 'fcebook'=>'', 'gst_number'=>'123', 'shipping_address'=>'', 'note'=>'');
        $records[] = array('company_name' => 'xyz', 'company_website'=>'https://www.google.com.pk/', 'company_address'=>'xyz', 'rep_name'=>'abc', 'rep_email'=>'abcvendor2@gmail.com', 'rep_phone_number'=>'123', 'vendor_number'=> '456', 'vendor_status' => 'inactive', 'skype'=>'', 'linkedin'=>'', 'twitter'=>'', 'fcebook'=>'', 'gst_number'=>'123', 'shipping_address'=>'', 'note'=>'');
        
        $columns = array('Company Name', 'Company Website', 'Company Address', 'Rep Name', 'Rep Email', 'Rep Phone Number', 'Vendor Number', 'Vendor Status', 'Skype', 'LinkedIn', 'Twitter', 'Fcebook' , 'GST Number', 'Shipping Address', 'Note');

        $callback = function() use ($records, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($records as $record) {
                fputcsv($file, array($record['company_name'], $record['company_website'], $record['company_address'], $record['rep_name'], $record['rep_email'], $record['rep_phone_number'], $record['vendor_number'], $record['vendor_status'], $record['skype'], $record['linkedin'], $record['twitter'], $record['fcebook'], $record['gst_number'], $record['shipping_address'], $record['note']));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    
    public function import(Request $request) {
        
         $directory = "user-uploads/import-csv/".company()->id;
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
        $file = $request->file('csv_file');
        if($file) {
        // File Details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");
        // 2MB in Bytes
        $maxFileSize = 2097152;
        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {
            // Check file size
            if ($fileSize <= $maxFileSize) {
                
                $fileName = time().".csv";
                // Upload file
                $file->move(public_path($directory), $fileName);
                // Import CSV to Database
                $filepath = public_path($directory . "/" . $fileName);
                // Reading file
                $file = fopen($filepath, "r");
                $importData_arr = array();
                $i = 0;
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);
                    // Skip first row (Remove below comment if you want to skip the first row)
                   if($i == 0){$i++; continue;} 
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);
                
//                // Insert to MySQL database
                foreach ($importData_arr as $importData) {
                    // $importData[3] name
                    // $importData[4] email
//                    if(!empty($importData[0]) && !empty($importData[1]) && !empty($importData[2]) && !empty($importData[3]) && !empty($importData[4])  && !empty($importData[5]) && !empty($importData[6])) {
//                        $this->addImported($importData);
//
//                    }
                    
                    // mandotry only name field
                    if(!empty($importData[0])) {
                        $this->addImported($importData);

                    }
                   
                    
                }
                
                \Session::put('success', 'Import Successful.');
                return redirect(route('member.vendor.index'));
            } else {
                \Session::put('error', 'File too large. File must be less than 2MB.');
                return redirect(route('member.vendor.index'));
            }
        } else {
            \Session::put('error', 'Invalid File Extension.');
            return redirect(route('member.vendor.index'));
        }
        } else {
            \Session::put('error', 'Select File.');
            return redirect(route('member.vendor.index'));
        }
    }
    
    public function addImported($req){
        
        $vendor = new ClientVendorDetails();
        $vendor->company_name = isset($req[0]) ? $req[0] : '';
        $vendor->company_website = isset($req[1]) ? $req[1] : '';
        $vendor->company_address = isset($req[2]) ? $req[2] : '';
        $vendor->vendor_rep_name = isset($req[3]) ? $req[3] : '';
        $vendor->rep_email = isset($req[4]) ? $req[4] : '';
        $vendor->vendor_name = isset($req[3]) ? $req[3] : '';
        $vendor->vendor_email = isset($req[4]) ? $req[4] : '';
        $vendor->vendor_mobile = isset($req[5]) ? $req[5] : '';
        $vendor->vendor_number = isset($req[5]) ? $req[5] : '';
        $vendor->status = isset($req[6]) ? $req[6] : 'inactive';
        $vendor->vendor_skype = isset($req[7]) ? $req[7] : '';
        $vendor->vendor_linkedIn = isset($req[8]) ? $req[8] : '';
        $vendor->vendor_twitter = isset($req[9]) ? $req[9] : '';
        $vendor->vendor_facebook = isset($req[10]) ? $req[10] : '';
        $vendor->vendor_gst_number = isset($req[11]) ? $req[11] : '';
        $vendor->vendor_shipping_address = isset($req[12]) ? $req[12] : '';
        $vendor->vendor_note = isset($req[13]) ? $req[13] : '';
        $vendor->save();
        
    }
}
