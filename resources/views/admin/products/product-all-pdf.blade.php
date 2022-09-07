<html>
    <head>
        <style>
            .item-summary{
                font-size: 10px
            }
            body{
                font-size: 12px;font-family: 'Lato';line-height:1.5;
            }tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }div.divFooter {
    position: fixed;
    bottom: 10px;
  }
		.divFooter span{padding:0 10px;font-size: 10px;}.divFooter span + span{border-left:1px solid #ccc;}	
			span b{font-weight:700;font-size: 11px;font-family: 'Lato';line-height:1.5;}
			h2{font-weight:700;font-size: 18px;font-family: 'Lato',;line-height:1.5}
                        h4{font-weight:700;font-size: 14px;font-family: 'Lato',;line-height:0.5}
            @media print { .tr {
                               page-break-inside: avoid !important;
                               -webkit-column-break-inside: avoid;
                               break-inside: avoid;
                               -webkit-region-break-inside: avoid;
                           }}
            </style>
            <title>indema-products</title>
			<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
        </head>
        <body>
            <table cellpadding="0" cellspacing="0" align="center" style=" width:100%;border-bottom:3px solid #808080">
            <tr>
                <td>
                    <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed">
                        <tr>
                            <td style="width:83%">
                                <h2 style="">Specifications</h2>
                                
                                @if($project_name_search != '' && !is_null($project_name_search))
                                    <h4 style=""> {{$project_name_search}} 
                                        @if($location_search != '' && !is_null($location_search))
                                        <span style="font-size: 12px;">({{$location_search}})</span>
                                        @endif
                                    </h4>
                                @endif
                                
<!--                                <h1>The POINTE 3 bedrm</h1>-->
                            </td>
                            <td style="width:17%;text-align:center">
                                <img src="{!! $company->logo_url !!}" alt="" style="max-width:100%" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
            @if($invoiceSetting->hide_product_footer == 'no')
                <div class="divFooter" style="color:#666">
                        <table cellpadding="10" cellspacing="0" style=" width:100%;text-align:center">
                                <tr>
                                        <td>
                                        <span>{!! $company->company_phone !!}</span>
                                        <span>{!! $company->website !!}</span>					
                                        @if($invoiceSetting->hide_company_address == 'no')
                                                <span>{!! nl2br($company->address) !!}</span>
                                        @endif
                                        </td>
                                </tr>
                        </table>

                </div>
             @endif
            
        @forelse($products as $product)
            <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed;border-bottom:1px solid #eee; ">
                <tr>
                    <td valign="top" style="width:18%">
                        <img src="{{ $product['image'] }}" style="width:100px" />
                    </td>
                    <td valign="top" style="text-align:left;">
                        @if($product['spec_number'] != '' && !is_null($product['spec_number']))
                            <span style="display:block"><b style="">SPEC #:</b> {{ $product['spec_number'] }}</span> 
                        @endif
                        @if($product['name'] != '' && !is_null($product['name']))
                            <span style="display:block"><b>NAME:</b> {{ $product['name'] }}</span> 
                        @endif
                        @if($product['project'] != '' && !is_null($product['project']))
                            <span style="display:block"><b>PROJECT:</b> {{ $product['project'] }}</span>  
                        @endif
                        @if($product['location'] != '' && !is_null($product['location']))
                            <span style="display:block"><b>LOCATION:</b> {{ $product['location'] }}</span> 
                        @endif
                        @if($product['category'] != '' && !is_null($product['category']))
                            <span style="display:block"><b>CATEGORY:</b> {{ $product['category'] }}</span> 
                        @endif
                        @if($product['vendor'] != '' && !is_null($product['vendor']))
                            <span style="display:block"><b>VENDOR:</b> {{ $product['vendor'] }}</span>  
                        @endif
                        @if($product['manufacturer'] != '' && !is_null($product['manufacturer']))
                            <span style="display:block"><b>MANUFACTURER:</b> {{ $product['manufacturer'] }}</span>
                        @endif   
                        @if($product['short_code'] != '' && !is_null($product['short_code']))
                            <span style="display:block"><b>URL:</b> {{route('shorten.link', $product['short_code'])}}</span>
                        @endif 
                         @if($product['finish_color'] != '' && !is_null($product['finish_color']))
                            <span style="display:block"><b>FINISH/COLOR:</b> {{ $product['finish_color'] }}</span>
                        @endif 
                        
                        
                        @if($product['acknowledgement'] != '' && !is_null($product['acknowledgement']))
                            <span style="display:block"><b>ACKNOWLEDGEMENT:</b> {{ $product['acknowledgement'] }}</span>
                        @endif 
                         @if($product['est_ship_date'] != '' && !is_null($product['est_ship_date']))
                            <span style="display:block"><b>EST. SHIP DATE:</b> {{ $product['est_ship_date'] }}</span>
                        @endif 
                         @if($product['act_ship_date'] != '' && !is_null($product['act_ship_date']))
                            <span style="display:block"><b>ACT. SHIP DATE:</b> {{ $product['act_ship_date'] }}</span>
                        @endif 
                        
                    </td>
                    <td valign="top" style="text-align:left;"> 
                        @if($product['product_number'] != '' && !is_null($product['product_number']))
                            <span style="display:block"><b style="">PRODUCT NUMBER:</b> {{ $product['product_number'] }}</span> 
                        @endif
                        @if($product['dimensions'] != '' && !is_null($product['dimensions']))
                            <span style="display:block"><b>DIMENSIONS:</b> {{ $product['dimensions'] }}</span> 
                        @endif
                        @if($product['materials'] != '' && !is_null($product['materials']))
                            <span style="display:block"><b>MATERIAL:</b> {{ $product['materials'] }}</span> 
                        @endif
                        @if($product['lead_time'] != '' && !is_null($product['lead_time']))
                            <span style="display:block"><b>LEAD TIME:</b> {{ $product['lead_time'] }}</span> 
                        @endif
                        @if($product['cost_per_unit'] != '' && !is_null($product['cost_per_unit']) && $product['cost_per_unit'] !='$')
                            <span style="display:block"><b>COST PER UNIT:</b> {{ $product['cost_per_unit'] }}</span> 
                        @endif
                        @if($product['default_markup'] != '' && !is_null($product['default_markup']) && $product['default_markup'] != '$')
                            <span style="display:block"><b>MARKUP:</b> {{ $product['default_markup'] }}</span> 
                        @endif
                        @if($product['sales_tax'] != '' && !is_null($product['sales_tax']) && $product['sales_tax'] != '$')
                            <span style="display:block"><b>SALES TAX:</b> {{ $product['sales_tax'] }}</span> 
                        @endif
                        @if($product['freight'] != '' && !is_null($product['freight']) && $product['freight'] != '$')
                            <span style="display:block"><b>FREIGHT:</b> {{ $product['freight'] }}</span> 
                        @endif
                        @if($product['total'] != '' && !is_null($product['total']) && $product['total'] != '$')
                            <span style="display:block"><b>TOTAL:</b> {{ $product['total'] }}</span> 
                        @endif
                        @if($product['quantity'] != '' && !is_null($product['quantity']))
                            <span style="display:block"><b>QTY:</b> {{ $product['quantity'] }}</span> 
                        @endif
                        
                        @if($product['est_receive'] != '' && !is_null($product['est_receive']))
                            <span style="display:block"><b>EST. RECEIVE:</b> {{ $product['est_receive'] }}</span>
                        @endif 
                         @if($product['act_receive'] != '' && !is_null($product['act_receive']))
                            <span style="display:block"><b>ACT. RECEIVE:</b> {{ $product['act_receive'] }}</span>
                        @endif 
                         @if($product['receive_by'] != '' && !is_null($product['receive_by']))
                            <span style="display:block"><b>RECEIVE BY:</b> {{ $product['receive_by'] }}</span>
                        @endif 
                        @if($product['est_install'] != '' && !is_null($product['est_install']))
                            <span style="display:block"><b>EST. INSTALL:</b> {{ $product['est_install'] }}</span>
                        @endif
                        @if($product['act_install'] != '' && !is_null($product['act_install']))
                            <span style="display:block"><b>ACT. INSTALL:</b> {{ $product['act_install'] }}</span>
                        @endif 
                    </td>
                </tr>
                @if($product['notes'] != '' && !is_null($product['notes']))
                    <tr>
                        <td valign="top" style="text-align:left;" colspan="3"><b>NOTES: </b>{!! nl2br($product['notes']) !!}</td>
                    </tr>
                @endif
            </table>
        @empty
        <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed;border-bottom:1px solid #eee; ">
            <tr>
                <td>No Products</td>
            </tr>
        </table>
        @endforelse
        <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed;border-bottom:1px solid #eee; ">
            <tr>
                <td valign="top" style="text-align:right;">
                    <span style="display:block"><b>Total Sale price:</b> {{ $grand_total_sale }}</span>
                </td>
                <td valign="top" style="text-align:left;">
                    <span style="display:block"><b>Total Unit price:</b> {{ $grand_cost_per_unit }}</span>
                </td>
            </tr>
            
    </body>
</html>