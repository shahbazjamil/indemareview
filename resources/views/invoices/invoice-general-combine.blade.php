<html>
	<head>
		<style>
				@import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');
					.item-summary{
						font-size: 10px
					}
					body{
						font-size: 12px;font-family: 'Lato', sans-serif;line-height:1.5;
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
                                                                
                                                                @if($invoiceSetting->show_vat == 'yes' && !is_null($invoiceSetting->vat_number))
                                                                 <span style="display:block"><b>VAT number #: </b>{{ $invoiceSetting->vat_number }}</span>
                                                                 @endif
                                                                
                                                               
							</td>
							<td style="width:17%;text-align:center">
								<img src="{!! $company->logo_url !!}" alt="" style="max-width:100%" />
							</td>
						</tr>
						<tr>
							<td style="width:83%"><span>@lang('app.status'):</span> 
                                                        @if($invoice->status == 'paid')
                                                            <strong>PAID IN FULL</strong>
                                                        @else
                                                            <strong>{{ ucwords($invoice->status) }}</strong>
                                                        @endif
                                                        </td>
							<td style="width:17%;text-align:center">{{ $invoice->due_date->format($company->date_format) }}</td>
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
                                                                                        @if(!is_null($invoice->client_id))
                                                                                            <span style="display:block">{{$invoice->clientdetails->name}}</span>
<!--                                                                                            <span style="display:block">{{$invoice->clientdetails->company_name}}</span>-->
                                                                                            <span style="display:block">{!! nl2br($invoice->clientdetails->shipping_address) !!}</span>
                                                                                            <span style="display:block">{{$invoice->clientdetails->mobile}}</span>
                                                                                        @endif
											
										</td>
										<td valign="top">
                                                                                       
										</td>
									</tr>
								</table>	
							</td>
							<td align="right" valign="top">
								<h3>INVOICE</h3>
								{{$invoice->invoice_number}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>	
					<table cellpadding="10" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed">
						<tr>
							<th valign="top" align="left" style="border-bottom:1px solid #808080;width:40%">
								ITEM	
							</th>
                                                        @if($invoiceSetting->show_vat == 'yes' && !is_null($invoiceSetting->vat_number))
                                                            <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                                                   PRICE (Excl. VAT)
                                                            </th>
                                                        @else
                                                            <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                                                    PRICE
                                                            </th>
                                                        @endif
                                                       
                                                        <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
								SHIPPING
							</th>
							<th valign="top" style="border-bottom:1px solid #808080;text-align:center">
								QTY
							</th>
							
							<th valign="top" style="border-bottom:1px solid #808080;text-align:center">
								TOTAL
							</th>
						</tr>
                                                @foreach($groupItems as $item)
                                                        <tr>
                                                                <td valign="top">
                                                                        <table cellpadding="0" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed;padding:0">
                                                                                <tr>
                                                                                        <td style="width:25%">
                                                                                            @if($item['product_url'] != '')
                                                                                                @if($item['product_url'] == 'https://app.indema.co/img/default-product.png')
                                                                                                    &nbsp;
                                                                                                 @else
                                                                                                 <img src="{{ $item['product_url'] }}" width="50" style="margin-right:20px" alt="{{$item['item_name']}}"/>
                                                                                                 @endif
                                                                                             
                                                                                             @else
                                                                                                &nbsp;
                                                                                             @endif
                                                                                        </td>
                                                                                        <td style="width:75%">
                                                                                            @if($item['group_name'] != '')
                                                                                                <h5 style="margin:0 0 15px;">{{ ucfirst($item['group_name']) }}</h5><br/>	
                                                                                            @else
                                                                                                <h5 style="margin:0 0 15px;">{{ ucfirst($item['item_name']) }}</h5><br/>	
                                                                                                @if(!is_null($item['item_summary']))
                                                                                                    <span style="display:block">{!! nl2br($item['item_summary'])!!}</span>
                                                                                                @endif
                                                                                             @endif
                                                                                                
                                                                                        </td>
                                                                                </tr>
                                                                        </table>
                                                                </td>
                                                                
                                                                <td valign="top" style="text-align:center">
                                                                        {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$item['sale_price'], 2, '.', '') }}
                                                                </td>
                                                                <td valign="top" style="text-align:center">
                                                                       {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$item['shipping_price'], 2, '.', '') }}
                                                                </td>
                                                                <td valign="top" style="text-align:center">
                                                                        {{ $item['quantity'] }}
                                                                </td>
                                                                <td valign="top" style="text-align:center">
                                                                        {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$item['amount'], 2, '.', '') }}
                                                                </td>
                                                        </tr>
                                                   
                                                @endforeach
					</table>
					
					
                    <table cellpadding="0" cellspacing="0" align="center" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;">
			<tr>
				<td style="border-top:1px solid #808080;border-bottom:1px solid #808080">
                                    
                                    <table cellpadding="10" cellspacing="0" width="150" align="left">
                                        <tr>
                                            <td>
                                                
                                            @if($invoice->status == 'unpaid')
                                                {!! nl2br($invoiceSetting->invoice_terms) !!}
                                            @else
                                                &nbsp;
                                            @endif
                                            
                                            </td>     
                                        </tr>
                                    </table>
                                    
                                    <table cellpadding="5" cellspacing="0" width="210" align="right">
                                            <tr>
                                                @if($invoiceSetting->show_vat == 'yes' && !is_null($invoiceSetting->vat_number))
                                                    <td align="right"><b>SUBTOTAL</b> (Excl. VAT)</td>
                                                @else
                                                    <td align="right"><b>SUBTOTAL</b></td>
                                                @endif
                                                    
                                                    <td align="right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->sub_total, 2, '.', '') }}</td>
                                            </tr>
                                            @if($discount != 0 && $discount != '')
                                                <tr>
                                                    <td align="right"><b>>DISCOUNT</b></td>
                                                    <td align="right">-{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$discount, 2, '.', '') }}</td>
                                                </tr>
                                            @endif

                                            @foreach($taxes as $key=>$tax)
                                                <tr>
                                                    <td align="right"><b>{{ strtoupper($key) }}</b></td>
                                                    <td align="right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$tax, 2, '.', '') }}</td>
                                               </tr>
                                             @endforeach
                                             @if($individual_tax > 0)
                                                   <tr>
                                                        <td align="right"><b>{{ $individual_tax_name }}</b></td>
                                                        <td align="right">{{ number_format((float)$individual_tax, 2, '.', '') }}</td>
                                                   </tr>
                                                @endif
                                            @if($invoice->deposit_req)
                                                <tr>
                                                    <td align="right"><b>DEPOSIT REQUEST</b></td>
                                                    <td align="right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->deposit_req, 2, '.', '') }}</td>
                                                </tr>
                                            @endif

                                            <tr>
                                                    <td align="right"><b>TOTAL</b></td>
                                                    <td align="right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                                            </tr>
                                            <tr>
                                                    <td align="right"><b>@lang('modules.invoices.amountPaid')</b></td>
                                                    <td align="right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->amountPaid(), 2, '.', '') }}</td>
                                            </tr>
                                            <tr style="font-size:18px">
                                                    <td align="right"><b>@lang('modules.invoices.amountDue')</b></td>
                                                    <td align="right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->amountDue(), 2, '.', '') }}</td>
                                            </tr>
                                    </table>
				</td>
			</tr>
		</table>
            
           @if($invoiceSetting->hide_signature_pdf == 'no')
            <table cellpadding="0" cellspacing="0" align="center" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;margin-top:250px">
                         <tr>
                                 <th valign="top" align="left" style="border-bottom:1px solid #808080;width:40%">
                                        CUSTOMER SIGNATURE
                                 </th>
                                 <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                         DATE
                                 </th>
                         </tr>
                         <tr>
                             <td valign="top" style="text-align:center"></td>
                             <td valign="top" style="text-align:center"></td>
                         </tr>
                 </table>
            @endif
            
    <p>&nbsp;</p>
    <hr>
    
    <p id="notes">
        @if(!is_null($invoice->note))
            {!! nl2br($invoice->note) !!}<br>
        @endif
        
        
        
    </p>
    
	</body>
</html>