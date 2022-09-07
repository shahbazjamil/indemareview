<?php

namespace App\Http\Controllers\Member;

use App\ClientDetails;
use App\Currency;
use App\Estimate;
use App\EstimateItem;
use App\Helper\Reply;
use App\Http\Controllers\Member\MemberBaseController;
use App\Http\Requests\StoreEstimate;
use App\Notifications\NewEstimate;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Tax;
use App\Product;
use App\InvoiceSetting;
use App\Project;
use App\ClientVendorDetails;
use App\SalescategoryType;
use App\CodeType;
use App\LineItemGroup;

class MemberEstimatesController extends MemberBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.estimates';
        $this->pageIcon = 'ti-file';
        $this->middleware(function ($request, $next) {
            if (!in_array('estimates', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        if (!$this->user->can('view_estimates')) {
            abort(403);
        }
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/estimates'));
        
        $this->totalRecords = Estimate::count();
        
        return view('member.estimates.index', $this->data);
    }

    public function create()
    {
        if (!$this->user->can('add_estimates')) {
            abort(403);
        }
        
        $default_project_id = 0;
        if(isset($request->project_id) && $request->project_id!='') {
            $default_project_id = $request->project_id;
        }
        if(session()->get('project_id')) {
            $default_project_id = session()->get('project_id');
        }
        
        $review_products = [];
        if(session()->get('review_products')) {
            $review_products = session()->get('review_products');
        }
        
        session()->forget('project_id');
        session()->forget('review_products');
        
        $this->default_project_id = $default_project_id;
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        
        $lastEstimate = Estimate::count();
        do {
            $lastEstimate += 1;
            $exists = false;
            $estimate = Estimate::where('estimate_number', $lastEstimate)->first();
            if($estimate) {
                $exists = true;
            }
        } while ($exists);
        $this->lastEstimate = $lastEstimate;
        
        //$this->lastEstimate = Estimate::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastEstimate) < $this->invoiceSetting->estimate_digit) {
            for ($i = 0; $i < $this->invoiceSetting->estimate_digit - strlen($this->lastEstimate); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        $this->review_products = $review_products;
        $this->projects = Project::all();
        
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();

        return view('member.estimates.create', $this->data);
    }

    public function store(StoreEstimate $request)
    {
        //        dd($request->all());
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $productImage = $request->file('product_img');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');
        $group = request()->input('groups');
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $project_id = $request->input('project_id')?$request->input('project_id'):null;
        $invoice_item_type = $request->input('invoice_item_type');

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }
        
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }
        
        //        dd([round($request->sub_total,2), round($request->total, 2)]);
        $estimate = new Estimate();
        $estimate->client_id = $request->client_id;
        //$lastEstimate = Estimate::count();
        $estimate->estimate_number = $request->estimate_number;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->note = $request->note;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->status = 'waiting';
       
        $estimate->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $estimate->shipping_total = round($request->shipping_total, 2);
        
        $estimate->project_id = $project_id;
        
        $estimate->total_tax = round($request->total_tax, 2);
        
        $estimate->card_processing_value = $request->card_processing_value ? round($request->card_processing_value, 2) : 0;
        $estimate->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        
        $estimate->deposit_request = round($request->deposit_request, 2);
        $estimate->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $estimate->deposit_req = round($request->deposit_req, 2);
        
        $estimate->combine_line_items = $combine_line_items;
        
        //$estimate->tags = json_encode($request->tags);
        
        $estimate->tags = json_encode(array());
        if($request->tags) {
            $estimate->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $estimate->save();
        
        $this->invoiceSetting = InvoiceSetting::first();
        $status = 'pending';
        if($this->invoiceSetting->line_item_approval == 'no') {
            $status = 'approved';
        }


        foreach ($items as $key => $item) :
            if (!is_null($item)) {

                $fileName = null;
                $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                if ($existKey){
                    $file = $productImage[$key]->getClientOriginalName();
                    $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $fileName = time().mt_rand().".".$extension;
                }
                $estimateItem = EstimateItem::create([
                        'estimate_id' => $estimate->id,
                        'item_name' => $item,
                        'item_summary' => $itemsSummary[$key],
                        'type' => 'item',
                        'quantity' => $quantity[$key],
                        'unit_price' => round($cost_per_item[$key], 2),
                        'amount' => round($amount[$key], 2),
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => $group[$key]?$group[$key]:null,
                        'picture' => $pictures[$key],
                        'product_id' => $product_ids[$key],
                        'markup' => $markups[$key]?$markups[$key]:'0.00',
                        'markup_fix' => $markup_fix[$key]?$markup_fix[$key]:'0.00',
                        'invoice_item_type' => $invoice_item_type[$key]?$invoice_item_type[$key]:'product',
                        'sale_price' => $sale_prices[$key]?$sale_prices[$key]:'0.00',
                        'shipping_price' => $shipping_prices[$key]?$shipping_prices[$key]:'0.00',
                        'status' => $status,
                        'product_image' => $fileName,
                    ]);

                if ($existKey) {
                    $directory = "user-uploads/estimates/products/$estimateItem->id";
                    if (!File::exists(public_path($directory))) {
                        $result = File::makeDirectory(public_path($directory), 0775, true);
                    }
                    $imageFilePath = "$directory/$fileName";

                    File::move($productImage[$key], public_path($imageFilePath));
                    $estimateItem->save();
                }

            }
        endforeach;

        $this->logSearchEntry($estimate->id, 'Estimate #' . $estimate->id, 'admin.estimates.edit', 'estimate');

        return Reply::redirect(route('member.estimates.index'), __('messages.estimateCreated'));
    }

    public function edit($id)
    {
        if (!$this->user->can('edit_estimates')) {
            abort(403);
        }
        $this->estimate = Estimate::findOrFail($id);
        $this->invoiceSetting = InvoiceSetting::first();

        $tags = $this->estimate->tags ? json_decode($this->estimate->tags) : array();
        $this->estimate->tags = $tags;
        
        if($tags) {
            $this->estimate->tags = array_values(array_unique($tags));
        }
        
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        $this->projects = Project::all();
        
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        return view('member.estimates.edit', $this->data);
    }

    public function update(StoreEstimate $request, $id)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $productImage = $request->file('product_img');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');
        $group = request()->input('groups');
        
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
       
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $project_id = $request->input('project_id')?$request->input('project_id'):null;
        $invoice_item_type = $request->input('invoice_item_type');

        $old_items = $request->input('old_items') ?? [];

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }
        
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }


        $estimate = Estimate::findOrFail($id);
        $estimate->client_id = $request->client_id;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->status = $request->status;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->note = $request->note;
        $estimate->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $estimate->shipping_total = round($request->shipping_total, 2);
        $estimate->project_id = $project_id;
        
        $estimate->total_tax = round($request->total_tax, 2);
        
        $estimate->card_processing_value = $request->card_processing_value ? round($request->card_processing_value, 2) : 0;
        $estimate->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        
        $estimate->deposit_request = round($request->deposit_request, 2);
        $estimate->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $estimate->deposit_req = round($request->deposit_req, 2);
        $estimate->deposit_req = $combine_line_items;
        
        //$estimate->tags = json_encode($request->tags);
        
        $estimate->tags = json_encode(array());
        if($request->tags) {
            $estimate->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $estimate->save();
        
        $this->invoiceSetting = InvoiceSetting::first();
        $status = 'pending';
        if($this->invoiceSetting->line_item_approval == 'no') {
            $status = 'approved';
        }

        $estimateItemQuery = EstimateItem::query()->where('estimate_id', $estimate->id)->where('product_image','!=',null);
        $oldEstimateItemImg = $estimateItemQuery->pluck('product_image','id')->toArray();

        // delete and create new
        EstimateItem::where('estimate_id', $estimate->id)->delete();

        foreach ($items as $key => $item) :

            $fileName = null;
            $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
            if ($existKey){
                $file = $productImage[$key]->getClientOriginalName();
                $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                $fileName = time().mt_rand().".".$extension;
            }
            $fname = isset($old_items[$key]) && !empty($old_items[$key]) ? $old_items[$key] : 'N/A';
            $oldPath = public_path('user-uploads/estimates/products/'.$fname);
            if(file_exists($oldPath)){
                $fileName = $oldEstimateItemImg[$old_items[$key]];
            }

            $estimateItem = EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                    'group_id' => $group[$key]?$group[$key]:null,
                     'picture' => $pictures[$key],
                    'product_id' => $product_ids[$key],
                    'markup' => $markups[$key]?$markups[$key]:'0.00',
                     'markup_fix' => $markup_fix[$key]?$markup_fix[$key]:'0.00',
                    'invoice_item_type' => $invoice_item_type[$key]?$invoice_item_type[$key]:'product',
                    'sale_price' => $sale_prices[$key]?$sale_prices[$key]:'0.00',
                    'shipping_price' => $shipping_prices[$key]?$shipping_prices[$key]:'0.00',
                    'status' => $status,
                    'product_image'=> $fileName,
                ]);

            if(file_exists($oldPath)){
                $newPath = public_path('user-uploads/estimates/products/'.$estimateItem->id);
                File::moveDirectory($oldPath,$newPath);
            }

            if ($existKey) {
                $directory = "user-uploads/estimates/products/$estimateItem->id";
                if (!File::exists(public_path($directory))) {
                    $result = File::makeDirectory(public_path($directory), 0775, true);
                }
                $imageFilePath = "$directory/$fileName";

                File::move($productImage[$key], public_path($imageFilePath));
                $estimateItem->save();
            }

        endforeach;

        return Reply::redirect(route('member.estimates.index'), __('messages.estimateUpdated'));
    }

    public function data(Request $request)
    {
        $invoices = Estimate::join('users', 'estimates.client_id', '=', 'users.id')
            ->join('currencies', 'currencies.id', '=', 'estimates.currency_id')
            ->select('estimates.id', 'estimates.client_id', 'users.name', 'estimates.total', 'currencies.currency_symbol', 'estimates.status', 'estimates.valid_till', 'estimates.estimate_number', 'estimates.send_status' , 'estimates.tags');


        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->format('Y-m-d');
            $invoices = $invoices->where(DB::raw('DATE(estimates.`valid_till`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->format('Y-m-d');
            $invoices = $invoices->where(DB::raw('DATE(estimates.`valid_till`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $invoices = $invoices->where('estimates.status', '=', $request->status);
        }

        $invoices = $invoices->orderBy('estimates.id', 'desc')->get();

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">';
                if ($this->user->can('view_estimates') && $row->status != 'draft') {
                    $action .= '<li><a href="' . route("member.estimates.download", $row->id) . '" ><i class="fa fa-download"></i> '.__('app.download').'</a></li>';
                }

                if (!$row->send_status && $row->status != 'draft') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-estimate-id="' . $row->id . '" class="sendButton"><i class="fa fa-send"></i> ' . __('app.send') . '</a></li>';
                }

                if ($this->user->can('edit_estimates') && ($row->status == 'waiting' || $row->status == 'draft')) {
                    $action .= '<li><a href="' . route("member.estimates.edit", $row->id) . '" ><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                }

                if ($this->user->can('delete_estimates')) {
                    $action .= '<li><a class="sa-params" href="javascript:;" data-estimate-id="' . $row->id . '"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }

                if ($this->user->can('add_invoices') && $row->status != 'draft') {
                    $action .= '<li><a href="' . route("member.all-invoices.convert-estimate", $row->id) . '" ><i class="ti-receipt"></i> ' . __('app.create') . ' ' . __('app.invoice') . '</a></li>';
                }
                $action .= '</ul></div>';

                return $action;
            })
            ->editColumn('name', function ($row) {
                return '<a href="' . route('member.clients.projects', $row->client_id) . '">' . ucwords($row->name) . '</a>';
            })
            ->editColumn('status', function ($row) {
                $status = '';
                if ($row->status == 'waiting') {
                    $status .= '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                } else if ($row->status == 'draft') {
                    $status .= '<label class="label label-primary">' . strtoupper($row->status) . '</label>';
                } else if ($row->status == 'declined') {
                    $status .= '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                } else {
                    $status .= '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }

                if (!$row->send_status && $row->status != 'draft') {
                    $status .= '<br><br><label class="label label-inverse">' . strtoupper(__('modules.invoices.notSent')) . '</label>';
                }
                return $status;
            })
            ->editColumn('total', function ($row) {
                return currency_position($row->total, $row->currency_symbol);
            })
            ->editColumn(
                'valid_till',
                function ($row) {
                    return Carbon::parse($row->valid_till)->format($this->global->date_format);
                }
            )
            ->editColumn('tags', function ($row) {
                $tags = '';
                if($row->tags) {
                    $tags = $row->tags ? json_decode($row->tags) : array();
                    if($tags) {
                        $tags = implode(', ', $tags);
                    }
                    
                }
                return $tags;
            })
            ->rawColumns(['name', 'action', 'status'])
            ->removeColumn('currency_symbol')
            ->removeColumn('client_id')
            ->make(true);
    }

    public function destroy($id)
    {
        Estimate::destroy($id);
        return Reply::success(__('messages.estimateDeleted'));
    }


    public function download($id)
    {

        $this->estimate = Estimate::findOrFail($id);
        $this->company = company();
        $this->invoiceSetting = InvoiceSetting::first();
        
        if ($this->estimate->discount > 0) {
            if ($this->estimate->discount_type == 'percent') {
                $this->discount = (($this->estimate->discount / 100) * $this->estimate->sub_total);
            } else {
                $this->discount = $this->estimate->discount;
            }
        } else {
            $this->discount = 0;
        }
        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $this->estimate->id)
            ->get();

        foreach ($items as $item) {
            if ($this->estimate->discount > 0 && $this->estimate->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->estimate->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }
        
        $this->individual_tax = $this->estimate->total - ($this->estimate->sub_total + $this->estimate->total_tax);
        
        $individual_tax_name = '';
       
        if($this->estimate->tax_on_total) {
            foreach (json_decode($this->estimate->tax_on_total) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
                if ($this->tax) {
                    if($individual_tax_name == '') {
                         $individual_tax_name = $this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    } else {
                        $individual_tax_name .= ' ,'.$this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    }
                }
            }
        }
        
        $this->individual_tax_name = $individual_tax_name;
        

        $this->taxes = $taxList;

        $this->settings = $this->global;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('member.estimates.estimate-pdf', $this->data);
        $filename = 'estimate-' . $this->estimate->id;
        //        return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }

    public function sendEstimate($id)
    {
        $estimate = Estimate::findOrFail($id);
        $estimate->client->notify(new NewEstimate($estimate));
        
        $estimate->send_status = 1;
        $estimate->save();
        return Reply::success(__('messages.updateSuccess'));
    }
}
