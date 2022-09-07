<?php

namespace Modules\RestAPI\Http\Controllers;

//namespace Modules\RestAPI\Http\Requests;
use Froiden\RestAPI\ApiResponse;
use Froiden\RestAPI\Exceptions\ApiException;
use Modules\RestAPI\Entities\User;
use Modules\RestAPI\Entities\Product;

use Modules\RestAPI\Http\Requests\MoodBoard\MoodRequest;

class MoodBoardController extends ApiBaseController
{

    public function allUsers(MoodRequest $request)
    {
        $mood_board_key = $request->get('mood_board_key');
        
        if(isset($mood_board_key) && $mood_board_key == 'cPPLjwMwFsytuCnKvTm0hMJOEC') {
            $query = User::select('id', 'company_id', 'name', 'email', 'password');
            // Modify query
            //$query->where('holidays.date', '>=', $startDate);

            $holidays = $query->get()->toArray();

            $results = [
                'users' => $holidays
            ];
            
            return ApiResponse::make(null, $results);
        }
        
        $exception = new ApiException('Wrong key provided', null, 403, 403, 2001);
        return ApiResponse::exception($exception);
       
    }
    
    public function productsByCompnayID(MoodRequest $request)
    {
        $mood_board_key = $request->get('mood_board_key');
        $compnay_id = $request->get('compnay_id');
        
        if(isset($mood_board_key) && $mood_board_key == 'cPPLjwMwFsytuCnKvTm0hMJOEC') {
            if(isset($compnay_id) && !empty($compnay_id)) {
                
                $query = Product::select('id', 'name' ,'cost_per_unit', 'vendor_description', 'taxable' ,'picture');
                // Modify query
                //$query->where('holidays.date', '>=', $startDate);

                $holidays = $query->get()->toArray();

                $results = [
                    'products' => $holidays
                ];

                return ApiResponse::make(null, $results);
                
            }
            
            $exception = new ApiException('Invalid company ID provided', null, 403, 403, 2001);
            return ApiResponse::exception($exception);
            
        }
        
        $exception = new ApiException('Wrong key provided', null, 403, 403, 2001);
        return ApiResponse::exception($exception);
       
    }
    
}
