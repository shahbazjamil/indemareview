<html>
    <head>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');
            .item-summary{
                font-size: 10px
            }
            body{
                font-size: 14px;font-family: 'Lato', sans-serif;line-height:1.5;
            }tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }div.divFooter {
    position: fixed;
    bottom: 10px;
  }
		.divFooter span{padding:0 10px;font-size: 10px;}.divFooter span + span{border-left:1px solid #ccc;}	b{font-weight:700;font-size: 14px;font-family: 'Lato';line-height:1.5;}
			h1{font-weight:400;font-size: 30px;font-family: 'Lato',;line-height:1.5}
			h2{font-weight:700;font-size: 18px;font-family: 'Lato',;line-height:1.5}
			h2 b{font-size:18px;}
            @media print { .tr {
                               page-break-inside: avoid !important;
                               -webkit-column-break-inside: avoid;
                               break-inside: avoid;
                               -webkit-region-break-inside: avoid;
                           }}
            </style>
			<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
        </head>
        <body>
            <table cellpadding="0" cellspacing="0" align="center" style=" width:100%;border-bottom:3px solid #808080">
            <tr>
                <td>
                    <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed">
                        <tr>
                            <td style="width:83%">
                                <h1>{{ $product->name }}</h1>
                            </td>
                            <td style="width:17%;text-align:center">
                                <img src="{!! $company->logo_url !!}" alt="" style="max-width:100%" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed;">
            <tr>
                <td valign="top" style="width:50%">
                    <span style="display:block;width:100%;text-align:center;"><img src="{!! $image !!}"  style="max-width:80%;display:block;max-height:250px" /></span>
					 @if($image_two !='')
                         <span style="display:block;width:100%;text-align:center;margin-top:20px;"><img src="{!! $image_two !!}"  style="max-width:80%;display:block;max-height:250px" /></span>
            @endif
            @if($image_three !='')
                         <span style="display:block;width:100%;text-align:center;margin-top:20px;"><img src="{!! $image_three !!}"  style="max-width:80%;display:block;max-height:250px" /></span>
            @endif
                </td>
                <td valign="top" style="text-align:left;">
                    <h2>SPECIFICATIONS</h2>
                    <table cellpadding="5" cellspacing="0" style="margin-bottom:15px">
                        @if($product->spec_number != '' && !is_null($product->spec_number))
                            <tr>
                                <td><b style="margin:0;">Spec #</b></td><td>{{ $product->spec_number }}</td>
                            </tr>
                        @endif
                        @if($product->dimensions != '' && !is_null($product->dimensions))
                            <tr>
                                <td><b style="margin:0;">Dimensions</b></td><td>{{ $product->dimensions }}</td>
                            </tr>
                        @endif
                        @if($product->materials != '' && !is_null($product->materials))
                            <tr>
                                <td><b style="margin:0;">Material</b></td><td>{{ $product->materials }}</td>
                            </tr>
                        @endif
<!--                        @if($product->url != '' && !is_null($product->url))
                            <tr>
                                <td><b style="margin:0;">URL</b></td><td> <a href="{{$product->url}}" >{!! nl2br($product->url) !!}</a></td>
                            </tr>
                        @endif-->
                        @if($locationCode != '' && !is_null($locationCode))
                            <tr>
                                <td><b style="margin:0;">Location Code</b></td><td>{{ $locationCode }}</td>
                            </tr>
                        @endif
                        @if($salesCategory != '' && !is_null($salesCategory))
                            <tr>
                                <td><b style="margin:0;">Sales Category</b></td><td>{{ $salesCategory }}</td>
                            </tr>
                         @endif
                         @if($product->finish_color != '' && !is_null($product->finish_color))
                            <tr>
                                <td><b style="margin:0;">Finish/Color</b></td><td>{{ $product->finish_color }}</td>
                            </tr>
                         @endif
                    </table>
                    <h2>PRICING</h2>
                    <table cellpadding="5" cellspacing="0" style="margin-bottom:15px">
                       @if($product->total_sale != '' && !is_null($product->total_sale))
                            <tr>
                                <td><b style="margin:0;">Sale Price</b></td><td>{{$currency_symbol}}{{ $product->total_sale }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
           
            
        </table>
            <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed;">
                @if($product->itemObj->description != '' && !is_null($product->itemObj->description))
                    <tr>
                        <td valign="top" style="text-align:left;">
                            <b>Description : </b>{!! nl2br($product->itemObj->description) !!}
                        </td>
                    </tr>	
                @endif
            </table>
            <table cellpadding="10" cellspacing="0" style=" width:100%;table-layout:fixed;">
                @if($product->notes != '' && !is_null($product->notes))
                    <tr>
                        <td valign="top" style="text-align:left;">
                            <b>Notes: </b>{!! nl2br($product->notes) !!}
                        </td>
                    </tr>	
                @endif
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
    </body>
</html>