<?php

namespace App\Http\Controllers\Parsing;

use App\ClientVendorDetails;
use App\Events\ParsingCallEvent;
use App\Http\Controllers\Controller;
use App\Product;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Project;
use App\SalescategoryType;
use App\ProductProject;
use App\ProductCodeType;
use App\CodeType;
use App\ShortLink;


class ParsingController extends Controller
{
    /**
     * Iframe parsing
     *
     * @param $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form($uuid)
    {
        if (!User::query()->where('uuid', $uuid)->count())
            abort(403);
       
        $user = User::where('uuid', $uuid)->first();
        
        $projects = Project::where('company_id', $user->company_id)->get();
        $salescategories = SalescategoryType::where('company_id', $user->company_id)->get();
        $codetypes = CodeType::where('company_id', $user->company_id)->get();
        
        return view('parsing.form', compact('uuid','projects', 'salescategories', 'codetypes'));
    }

    /**
     * Set data in iframe
     *
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function setData(Request $request, $uuid)
    {
        Log::info(time());
        event(new ParsingCallEvent($request->all(), $uuid));

        return response()->json(['success' => true]);
    }

    /**
     * Link for append in browser
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function link(Request $request)
    {
        $user = Auth::user();
        if (!$user['uuid']) {
            $user['uuid'] = Str::random(15);
            $user->save();
        }
        $uuid = $user['uuid'];

        return view('parsing.parsing-link', compact('uuid'));
    }

    /**
     * Save Product
     *
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveProduct(Request $request, $uuid)
    {
        try {
            $user = User::query()->where('uuid', $uuid)->firstOrFail();

            $product = new Product();
            if (isset($request['vendor']) && $request['vendor'] !=='') {
                $product->vendor_id = $this->getVendorId($request['vendor'], $user->company_id);
            }
            $product['company_id'] = $user->company_id;
            $product['name'] = $request['name'];
            $product['vendor_description'] = $request['vendor_description'] ?? '';
            $product['link'] = $request['link'] ?? '';
            $product['cost_per_unit'] = $request['cost_per_unit'] ?? 0;
            $product['msrp'] = $request['msrp'] ?? 0;
            $product['default_markup'] = $request['default_markup'] ?? 0;
            $product['markup_per'] = $request['default_markup'] ?? 0;
            $product['product_number'] = $request['product_number'] ?? '';
            
            $product['finish_color'] = $request['finish_color'] ?? '';
            $product['materials'] = $request['materials'] ?? '';
            $product['dimensions'] = $request['dimensions'] ?? '';
            $product['manufacturer'] = $request['manufacturer'] ?? '';
            $product['url'] = $request['url'] ?? '';
            $product['quantity'] = $request['quantity'] ?? 1;
            
            
            $product['item'] = json_encode(array(
            'description' => $request['client_description']?$request['client_description']:'',
            'locationCode' => '',
            'quantity' => '',
            'salesCategory' => $request['category']?$request['category']:'',
            'clientDeposit' => '',
            'depositRequested' => '',
            'unit' => '',
            'totalEstimatedCost' => '',
            'totalSalesPrice' => '',
            'unitBudget' => '',
        ));
            
            
            $product->save();
            
            if (!empty($product->url)) {
                //$shortLink = ShortLink::where('product_id', $products->id)->first();
                //if(!$shortLink) {
                $shortLink = new ShortLink();
                //}
                $shortLink->link = $product->url;
                $shortLink->code = str_random(6);
                $shortLink->product_id = $product->id;
                $shortLink->save();
                
                $product->short_code = $shortLink->code;
                $product->save();
            }

            /// delete and create new
            ProductProject::where('product_id', $product->id)->delete();
            $project_ids = [];
            if(isset($request['project']) && !empty($request['project'])) {
                $project_ids[] = $request['project'];
            }
            
            if(count($project_ids) > 0) {
                foreach ($project_ids as $project_id) {
                    $productProject = new ProductProject();
                    $productProject->product_id = $product->id;
                    $productProject->project_id = $project_id;
                    $productProject->save();
                }
            }
            
            // delete and create new
            ProductCodeType::where('product_id', $product->id)->delete();
            $code_type_ids = [];
            
            if(isset($request['location']) && !empty($request['location'])) {
                $code_type_ids[] = $request['location'];
            }
            
            if(count($code_type_ids) > 0) {
                foreach ($code_type_ids as $code_type_id) {
                    $productProject = new ProductCodeType();
                    $productProject->product_id = $product->id;
                    $productProject->code_type_id = $code_type_id;
                    $productProject->save();
                }
            }
            
//            if($uuid == '5DDGnno4r2UMSEA') {
//                echo $request['image'];
//            }
            
            if ($request['image']) {
                $this->uploadImage($request['image'], $product);
            }
            
            if (isset($request['image1']) && !empty($request['image1'])) {
                $this->uploadImage($request['image1'], $product);
            }
            
             if (isset($request['image2']) && !empty($request['image2'])) {
                $this->uploadImage($request['image2'], $product);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'error' => $exception->getMessage()]);
        }
    }

    /**
     * * Get vendor id ,create new if not has
     *
     * @param $vendor
     * @param $company_id
     * @return mixed
     */
    private function getVendorId($vendor, $company_id)
    {
        $vendorData = ClientVendorDetails::query()->where(['company_name' => trim($vendor), 'company_id' => $company_id])->firstOrCreate(['vendor_name' => trim($vendor), 'company_name' => trim($vendor), 'company_address' => trim($vendor), 'company_id' => $company_id, 'status' => 'active', 'added_by_parser' => 1]);
        return $vendorData->id;
    }

     /*
     * @param $image
     * @param $product
     */
    private function uploadImage($image, $product)
    {
        $directory = "user-uploads/products/" . $product['id'];
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }

        $fileName = time().mt_rand(). ".jpg";
        $imageFilePath = "$directory/$fileName";

        File::put(public_path($imageFilePath), file_get_contents($image));

        $pictureArr = array();
        if ($product->picture != null)
            $pictureArr = json_decode($product->picture);
        array_push($pictureArr, $fileName);

        $product->picture = json_encode($pictureArr);
        $product->save();
    }
}
