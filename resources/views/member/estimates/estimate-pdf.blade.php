<html>
	<head>
            <title>@lang('app.estimate') #{{ (is_null($estimate->estimate_number)) ? $estimate->id : $estimate->estimate_number }}</title>
		<style>
				@import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');
					.item-summary{
						font-size: 10px
					}
					.body{
						font-size: 12px;font-family: 'Lato', sans-serif;
					}tr {
			page-break-inside: avoid !important;
			page-break-after: auto !important;
			}

			@media print { .tr {
			page-break-inside: avoid !important;
			-webkit-column-break-inside: avoid;
			break-inside: avoid;
			-webkit-region-break-inside: avoid;
			}}
		</style>
	</head>
	<body>
		<table cellpadding="0" cellspacing="0" align="center" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;border-bottom:1px solid #808080">
			<tr>
				<td>
					<table cellpadding="10" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed">
						<tr>
							<td style="width:83%">
								<h2>{{$company->company_name}}</h2>
								<span style="display:block">{!! nl2br($company->address) !!}</span>
								<span style="display:block">{{$company->website}}</span>
								<span style="display:block">{{$company->company_phone}}</span>

							</td>
							<td style="width:17%;text-align:center">
								<img src="{!! $company->logo_url !!}" alt="" style="max-width:100%" />
							</td>
						</tr>
						<tr>
							<td style="width:83%">
							</td>
							<td style="width:17%;text-align:center"{{ $estimate->valid_till->format($company->date_format) }}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-top:1px solid #808080">
					<table cellpadding="10" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%">
						<tr>
							<td valign="top">
								<table cellpadding="0" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%">
									<tr>
										<td valign="top">
											<h3>CLIENT</h3>
                                                                                        @if(!is_null($estimate->client_id))
                                                                                            <span style="display:block">{{$estimate->clientdetails->name}}</span>
                                                                                            <span style="display:block">{!! nl2br($estimate->clientdetails->shipping_address) !!}</span>
                                                                                            <span style="display:block">{{$estimate->clientdetails->mobile}}</span>
                                                                                        @endif
										</td>
										<td valign="top">
											<h3>SHIP TO</h3>
                                                                                        @if($estimate->show_shipping_address == 'yes')
											<span style="display:block">{!! nl2br($estimate->shipping_address) !!}</span>
                                                                                        @endif
											
										</td>
									</tr>
								</table>	
							</td>
							<td align="right" valign="top">
								<h3>ESTIMATE</h3>
								<span style="display:block">{{ (is_null($estimate->estimate_number)) ? '#'.$estimate->id : $estimate->estimate_number }}</span>
                                                                <span style="display:block">@lang("modules.estimates.validTill"): {{ $estimate->valid_till->format($global->date_format) }}</span>
                                                                <span style="display:block">@lang('app.status'): {{ ucwords($estimate->status) }}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>	
                <table cellpadding="10" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed">
                        <tr>
                                <th valign="top" align="left" style="border-bottom:1px solid #808080;width:40%">
                                        @lang("modules.invoices.item")	
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        @lang("modules.invoices.qty")
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        Sale Price ({!! htmlentities($estimate->currency->currency_code)  !!})
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        Shipping Price ({!! htmlentities($estimate->currency->currency_code)  !!})
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        @lang("modules.invoices.price") ({!! htmlentities($estimate->currency->currency_code)  !!})
                                </th>
                        </tr>
                            @foreach($estimate->items as $item)
                           @if($item->type == 'item')
                                    <tr>
                                            <td valign="top">
                                                    <table cellpadding="0" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed;padding:0">
                                                            <tr>
                                                                <td style="width:25%">
                                                                    
                                                                    <?php 
                                                                    $picture = '';
                                                                    if($item->picture != '') { 
//                                                                        $pictures = json_decode($item->product->picture);
//                                                                        if(isset($pictures[0])) {
//                                                                            $picture =  asset('user-uploads/products/'.$item->product->id.'/'.$pictures[0].'');
//                                                                        }
                                                                        $picture =  asset('user-uploads/products/'.$item->product_id.'/'.$item->picture.'');
                                                                        $extension = pathinfo($picture, PATHINFO_EXTENSION);
                                                                        // webp images convert into jpeg
                                                                        if($extension == 'webp') {
                                                                                $directory = "user-uploads/products/$item->product_id";
                                                                                $org_imageFilePath = "$directory/".$item->picture;
                                                                                $imageFilePath = "$directory/".str_replace('.webp', '.jpeg', $item->picture);
                                                                                $im = imagecreatefromwebp($org_imageFilePath);
                                                                                // Convert it to a jpeg file with 100% quality
                                                                                imagejpeg($im, $imageFilePath, 100);
                                                                                $picture = str_replace('.webp', '.jpeg', $picture); 
                                                                        }
                                                                        
                                                                        
                                                                    } else if ($item->invoice_item_type == 'product') {
                                                                        
                                                                        if($item->product_url == 'https://app.indema.co/img/img-dummy.jpg'){
                                                                            $picture = asset('img/default-product.png');
                                                                        } else {
                                                                            $picture = $item->product_url;
                                                                        }

                                                                    }
                                                                    ?>
                                                                    <?php if($picture != '') { ?>
                                                                    <img src="{{ $picture }}" width="50" style="margin-right:20px" alt=""/>
                                                                    <?php  } else {?>
                                                                    &nbsp;
                                                                    <?php } ?>
                                                                    
                                                                    
                                                                    
                                                                </td>
                                                                <td style="width:75%">
                                                                            <h4 style="margin:0 0 15px;">{{ ucfirst($item->item_name) }}</h4>
                                                                            @if(!is_null($item->product_id))
                                                                            <?php if(isset($item->product)) { ?>
                                                                            <span class="item-summary" style="display:block">Product Number: {{ $item->product->product_number }}</span>
                                                                            <span class="item-summary" style="display:block">Materials: {{ $item->product->materials }}</span>
                                                                            <span class="item-summary" style="display:block">Dimensions: {{ $item->product->dimensions }}</span>
                                                                            <span class="item-summary" style="display:block">Finish/Color: {{ $item->product->finish_color }}</span>
                                                                            <?php } ?>
                                                                            @endif
                                                                            @if(!is_null($item->item_summary))
                                                                                    <span class="item-summary" style="display:block">{!! nl2br($item->item_summary)!!}</span>
                                                                            @endif
                                                                    </td>
                                                            </tr>
                                                    </table>
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ $item->quantity }}
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ number_format((float)$item->sale_price, 2, '.', '') }}
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ number_format((float)$item->shipping_price, 2, '.', '') }}
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ number_format((float)$item->amount, 2, '.', '') }}
                                            </td>
                                    </tr>
                            @endif
                            @endforeach
                            
                @if(!is_null($estimate->note))
                    <tr>
                            <td colspan="5"><b>Notes: </b>{!! nl2br($estimate->note) !!}</td>
                    </tr>
                @endif
                            
                </table>

					
                <table cellpadding="0" cellspacing="0" align="center" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%; table-layout:fixed">
			<tr>
				<td style="border-top:1px solid #808080;border-bottom:1px solid #808080">
					<table cellpadding="10" cellspacing="0" width="250" align="right" >
						<tr>
							<td align="right"><b>SUBTOTAL</b></td>
							<td align="right">{{ number_format((float)$estimate->sub_total, 2, '.', '') }}</td>
						</tr>
                                                 @if($discount != 0 && $discount != '')
                                                    <tr>
                                                        <td align="right"><b>DISCOUNT</b></td>
                                                        <td align="right">-{{ number_format((float)$discount, 2, '.', '') }}</td>
                                                    </tr>
                                                @endif
						@foreach($taxes as $key=>$tax)
                                                    <tr>
                                                        <td align="right"><b>{{ strtoupper($key) }}</b></td>
                                                        <td align="right">{{ number_format((float)$tax, 2, '.', '') }}</td>
                                                   </tr>
                                                 @endforeach
                                                 @if($individual_tax > 0)
                                                   <tr>
                                                        <td align="right"><b>{{ $individual_tax_name }}</b></td>
                                                        <td align="right">{{ number_format((float)$individual_tax, 2, '.', '') }}</td>
                                                   </tr>
                                                @endif
						<tr style="font-size:18px">
							<td align="right"><b>TOTAL</b></td>
							<td align="right">{{ number_format((float)$estimate->total, 2, '.', '') }}</td>
						</tr>
                                                <tr>
							<td align="right"><b>Deposit Request</b></td>
							<td align="right">{{ number_format((float)$estimate->deposit_req, 2, '.', '') }}</td>
						</tr>
                                                
                                                
					</table>
				</td>
			</tr>
		</table>
            
             @if($invoiceSetting->hide_signature_pdf == 'no')
            
           <table cellpadding="0" cellspacing="0" align="center" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;margin-top:200px">
                        <tr>
                                <th valign="top" align="left" style="border-bottom:1px solid #808080;width:40%">
                                       CUSTOMER SIGNATURE
                                    @if($estimate->sign)
                                            <img src="{{ $estimate->sign->signature }}" style="width: 150px;margin-top: 20px;">
                                    @endif
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        
                                    @if($estimate->sign)
                                    <div style="margin-top: 40px;">DATE &nbsp; {{ $estimate->sign->created_at->format($company->date_format) }}</div>
                                    @else
                                        DATE
                                    @endif
                                </th>
                        </tr>
                        <tr>
                            <td valign="top" style="text-align:center"></td>
                            <td valign="top" style="text-align:center"></td>
                        </tr>
                </table>
             
              @endif
               
            
	</body>
</html>