<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\ClientVendorDetails;


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Vendor;


class QuickbooksOnlineModel extends Model
{
    public function add_vendor($qb,$data)
    {   

        $billing_addr                               = '';
        $billing_city                               = '';
        $billing_countrySubDivisionCode             = '';
        $billing_postal_code                        = '';
        $shipping_addr                              = '';
        $shipping_city                              = '';
        $shipping_countrySubDivisionCode            = '';
        $shipping_postal_code                       = '';

        if(!empty($data['company_address'])){
            $address = preg_split('/\r\n|\r|\n/', $data['company_address']); 
            $billing_addr = isset($address[0]) ? $address[0] : '';
            if(isset($address[1])){
                $address2 =  explode(',',trim($address[1]));
                $billing_city = isset($address2[0]) ? $address2[0] : '';
            }
            if(isset($address2[1])){
                $address3 =  explode(' ',trim($address2[1]));
            } 
            $billing_countrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
            $billing_postal_code = isset($address3[1]) ? $address3[1] : '';
        }

    	$theResourceObj = Vendor::create([
    		"BillAddr" => [
                "Line1"=> $billing_addr,
                "City"=> $billing_city,
                "CountrySubDivisionCode"=>$billing_countrySubDivisionCode,
                "PostalCode"=> $billing_postal_code,
            ],
            "ShipAddr" => [
                "Line1"=>  $shipping_addr,
                "City"=>  $shipping_city,
                "CountrySubDivisionCode"=>  $shipping_countrySubDivisionCode,
                "PostalCode"=>  $shipping_postal_code,
            ],
    		"GivenName"=> $data['GivenName'],
    		"FamilyName"=> $data['FamilyName'],
    		"CompanyName"=> $data['CompanyName'],
    		"DisplayName"=> $data['DisplayName'],
    		"PrintOnCheckName"=> $data['PrintOnCheckName'],
    		"PrimaryPhone"=> [
    			"FreeFormNumber"=> $data['phone']
    		],
    		"Mobile"=> [
    			"FreeFormNumber"=> $data['mobile'],
    		],
    		"WebAddr" => [
    			"URI" => $data['website'],
    		],
    		"PrimaryEmailAddr"=> [
    			"Address"=> $data['email']
    		]
    	]);

    	$response = $qb->Add($theResourceObj);
        $error = $qb->getLastError();
        if ($error) {
        	return $error->getResponseBody();
        }else{
        	return $response->Id;
        }
    
    }


    public function update_vendor($qb,$data)
    {
       

        $billing_addr                               = '';
        $billing_city                               = '';
        $billing_countrySubDivisionCode             = '';
        $billing_postal_code                        = '';
        $shipping_addr                              = '';
        $shipping_city                              = '';
        $shipping_countrySubDivisionCode            = '';
        $shipping_postal_code                       = '';

        $vendor = ClientVendorDetails::find($data['vendor_id']);
        if(!empty($data['company_address'])){
            $address = preg_split('/\r\n|\r|\n/', $data['company_address']); 
            $billing_addr = isset($address[0]) ? $address[0] : '';
            if(isset($address[1])){
                $address2 =  explode(',',trim($address[1]));
                $billing_city = isset($address2[0]) ? $address2[0] : '';
            }
            if(isset($address2[1])){
                $address3 =  explode(' ',trim($address2[1]));
            } 
            $billing_countrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
            $billing_postal_code = isset($address3[1]) ? $address3[1] : '';
        }


        if(!empty($vendor->qbo_id)){
            $entities = $qb->Query("SELECT * FROM Vendor where Id='".$vendor->qbo_id."'");
            $error = $qb->getLastError();
            if ($error) {
                return $error->getResponseBody();
            }
            $theVendor = reset($entities);
            $theResourceObj = Vendor::update($theVendor,[
                "BillAddr" => [
                    "Line1"=> $billing_addr,
                    "City"=> $billing_city,
                    "CountrySubDivisionCode"=>$billing_countrySubDivisionCode,
                    "PostalCode"=> $billing_postal_code,
                ],
                "ShipAddr" => [
                    "Line1"=>  $shipping_addr,
                    "City"=>  $shipping_city,
                    "CountrySubDivisionCode"=>  $shipping_countrySubDivisionCode,
                    "PostalCode"=>  $shipping_postal_code,
                ],
                "GivenName"=> $data['GivenName'],
                "FamilyName"=> $data['FamilyName'],
                "CompanyName"=> $data['CompanyName'],
                "DisplayName"=> $data['DisplayName'],
                "PrintOnCheckName"=> $data['PrintOnCheckName'],
                "PrimaryPhone"=> [
                    "FreeFormNumber"=> $data['phone']
                ],
                "Mobile"=> [
                    "FreeFormNumber"=> $data['mobile'],
                ],
                "WebAddr" => [
                    "URI" => $data['website'],
                ],
                "PrimaryEmailAddr"=> [
                    "Address"=> $data['email']
                ]
            ]);

            $response = $qb->update($theResourceObj);
            $error = $qb->getLastError();
            if ($error) {
                return $error->getResponseBody();
            }else{
                return $response->Id;
            }
        }else{
            return $this->add_vendor($qb,$data);
        }


    }
}
