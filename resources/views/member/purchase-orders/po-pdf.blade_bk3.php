<!doctype html>
<html lang="en">
  <head>
    
    <title>Purchase Order # {{ $po->purchase_order_number }}</title>
    <style>
        .item-summary{
            font-size: 10px
        }
        .body{
            font-size: 12px
        }
    </style>
  </head>
  <body>
	<table cellpadding="0" cellspacing="0" width="700" style="font-family:Arial, Helvetica, sans-serif;font-size:14px" align="center">
		<tr>
                    <td valign="middle"><a href="javascript:voide(0)"><img src="{{ $company->logo_url }}" alt="" width="225" /></a></td>
			<td valign="middle" align="right">
					<table cellpadding="10" cellspacing="0">
						<tr>
							<td>PO Number:</td><td align="right">#{{ $po->purchase_order_number }}</td>
						</tr>
						<tr>
							<td>PO Date:</td><td align="right">{{ $po->purchase_order_date->format($company->date_format) }}</td>
						</tr>
						<tr>
							<td>Company:</td><td align="right">{{ $po->company }}</td>
						</tr>
					</table>
			</td>
		</tr>		
		<tr>
			<td colspan="2" style="font-size:40px;letter-spacing:5px">PURCHASE ORDER</td>
		</tr>		
		<tr>
			<td colspan="2" height="100"></td>
		</tr>		
		<tr>
			<td colspan="2" style="font-size:14px;letter-spacing:1px">
				<table cellpadding="10" cellspacing="0" style="width:700px" width="700">
					<tr>
						<th align="center">Item<hr /></th>
						<th align="center">Price<hr /></th>
						<th align="center">QTY<hr /></th>
						<th align="center">Total<hr /></th>
					</tr>
                                         @foreach($po->items as $key => $item)
                                            @if($item->type == 'item')
                                                <tr>
                                                        <td align="center">{{ ucfirst($item->item_name) }}
                                                        @if(!is_null($item->item_summary))
                                                                <p class="item-summary">{{ $item->item_summary }}</p>
                                                        @endif
                                                        </td>
                                                        <td align="center">{{ number_format((float)$item->unit_price, 2, '.', '') }}</td>
                                                        <td align="center">{{ $item->quantity }}</td>
                                                        <td align="center">{{ number_format((float)$item->amount, 2, '.', '') }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        
					<tr>
						<td align="right" colspan="3">Subtotal<hr /></td>
						<td align="center">{{ number_format((float)$po->product_subtotal, 2, '.', '') }}<hr /></td>
					</tr>
                                        @if($discount != 0 && $discount != '')
                                        <tr>
						<td align="right" colspan="3">Discount<hr /></td>
						<td align="center">-{{ number_format((float)$discount, 2, '.', '') }}<hr /></td>
					</tr>
                                        @endif
                                        @foreach($taxes as $key=>$tax)
					<tr>
						<td align="right" colspan="3">{{ strtoupper($key) }}<hr /></td>
						<td align="center">{{ number_format((float)$tax, 2, '.', '') }}<hr /></td>
					</tr>
                                        @endforeach
					<tr>
						<td align="right" colspan="3">Total</td>
						<td align="center">{{ number_format((float)$po->total_amount, 2, '.', '') }}</td>
					</tr>
				</table>
			</td>
		</tr>			
		<tr>
			<td colspan="2" height="100"></td>
		</tr>
                @if(!is_null($po->memo_order))
                    <tr>
                            <td colspan="2"><b>Notes: </b>{!! nl2br($po->memo_order) !!}</td>
                    </tr>
                @endif		
		<tr>
			<td colspan="2" height="50"></td>
		</tr>
	</table>
  </body>
</html>