<?php

namespace App\Http\Controllers\Front;

use App\AcceptEstimate;
use App\Company;
use App\Contract;
use App\ContractSign;
use App\Estimate;
use App\EstimateItem;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Front\FrontBaseController;
use App\Http\Requests\Admin\Contract\SignRequest;
use App\Http\Requests\EstimateAcceptRequest;
use App\Product;
use App\Invoice;
use App\InvoiceItems;
use App\Notifications\ContractSigned;
use App\Notifications\NewInvoice;
use App\Notifications\NewNotice;
use App\ProjectMilestone;
use App\Scopes\CompanyScope;
use App\Setting;
use App\UniversalSearch;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstimateAcceptEmail;

use App\ShortLink;



class ShortLinkController extends FrontBaseController
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shortenLink($code)
    {
        
        $find = ShortLink::where('code', $code)->first();
        if (!$find) {
            abort(404);
        }
        return redirect($find->link);
        
        // scrupt for update old products
        
//        $query = Product::withoutGlobalScope(CompanyScope::class)->where('url', '!=', '');
//        $products = $query->get();
//        if($products) {
//            foreach ($products as $product) {
//                
//                $shortLink = new ShortLink();
//                $shortLink->link = $product->url;
//                $shortLink->code = str_random(6);
//                $shortLink->product_id = $product->id;
//                $shortLink->save();
//                
//                $product->short_code = $shortLink->code;
//                $product->save();
//                
//            }
//        }
//        
//        echo 'Done';
        
        
    }
}
