<html>
	<head>
            <title>Purchase Order # {{ $po->purchase_order_number }}</title>
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
							<td style="width:17%;text-align:center"{{ $po->purchase_order_date->format($company->date_format) }}</td>
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
											<h3>VENDOR</h3>
                                                                                        @if(!is_null($po->vendor_id))
											<span style="display:block">{{$po->vendor->name}}</span>
                                                                                        <span style="display:block">{{$po->vendor->company_name}}</span>
											<span style="display:block">{!! nl2br($po->vendor->company_address) !!}</span>
											<span style="display:block">{{$po->vendor->mobile}}</span>
                                                                                        @endif
										</td>
										<td valign="top">
											<h3>SHIP TO</h3>
											<span style="display:block">{!! nl2br($po->shipping_address) !!}</span>
											
										</td>
									</tr>
								</table>	
							</td>
							<td align="right" valign="top">
								<h3>PURCHASE ORDER</h3>
								PO-{{ $po->purchase_order_number }}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>	
                <table cellpadding="10" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed">
                        <tr>
                                <th valign="top" align="left" style="border-bottom:1px solid #808080;width:60%">
                                        ITEM	
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        UNIT PRICE
                                </th>
                                 <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        QTY
                                </th>
                                <th valign="top" style="border-bottom:1px solid #808080;text-align:center">
                                        TOTAL
                                </th>
                        </tr>
                            @foreach($po->items as $key => $item)
                            @if($item->type == 'item')
                                    <tr>
                                            <td valign="top">
                                                    <table cellpadding="0" cellspacing="0" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;table-layout:fixed;padding:0">
                                                            <tr>
                                                                <td style="width:100%">
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
                                                                                    <span class="item-summary" style="display:block">Vendor Description: {!! $item->item_summary !!}</span>
                                                                            @endif
                                                                    </td>
                                                            </tr>
                                                    </table>
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ number_format((float)$item->unit_price, 2, '.', '') }}
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ $item->quantity }}
                                            </td>
                                            <td valign="top" style="text-align:center">
                                                    {{ number_format((float)$item->amount, 2, '.', '') }}
                                            </td>
                                    </tr>
                            @endif
                            @endforeach
                            
                @if(!is_null($po->memo_order))
                    <tr>
                            <td colspan="4"><b>Notes: </b>{!! nl2br($po->memo_order) !!}</td>
                    </tr>
                @endif
                            
                </table>

					
                <table cellpadding="0" cellspacing="0" align="center" style="font-family: 'Lato', sans-serif;font-size:12px;width:100%;">
			<tr>
				<td style="border-top:1px solid #808080;border-bottom:1px solid #808080">
					<table cellpadding="10" cellspacing="0" width="250" align="right">
						<tr>
							<td align="right"><b>SUBTOTAL</b></td>
							<td align="right">{{ number_format((float)$po->product_subtotal, 2, '.', '') }}</td>
						</tr>
                                                 @if($discount != 0 && $discount != '')
                                                    <tr>
                                                        <td align="right"><b>>DISCOUNT</b></td>
                                                        <td align="right">-{{ number_format((float)$discount, 2, '.', '') }}</td>
                                                    </tr>
                                                @endif
						@foreach($taxes as $key=>$tax)
                                                    <tr>
                                                        <td align="right"><b>{{ strtoupper($key) }}</b></td>
                                                        <td align="right">{{ number_format((float)$tax, 2, '.', '') }}</td>
                                                   </tr>
                                                 @endforeach
						<tr style="font-size:18px">
							<td align="right"><b>TOTAL</b></td>
							<td align="right">{{ number_format((float)$po->total_amount, 2, '.', '') }}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>