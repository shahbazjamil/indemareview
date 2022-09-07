<!doctype html>
<html lang="en">
  <head>
    
    <title>@lang('app.invoice')</title>
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
                    <td valign="middle"><a href="javascript:voide(0)"><img src="{!! $invoiceSetting->logo_url !!}" width="225" alt="" /></a></td>
			<td valign="middle" align="right">
					<table cellpadding="10" cellspacing="0">
						<tr>
							<td>Invoice Number:</td><td align="right">{{ $invoice->invoice_number }}</td>
						</tr>
						<tr>
							<td>Issue Date:</td><td align="right">{{ $invoice->issue_date->format($company->date_format) }}</td>
						</tr>
                                                 @if($invoice->status == 'unpaid')
                                                <tr>
							<td>Due Date:</td><td align="right">{{ $invoice->due_date->format($company->date_format) }}</td>
						</tr>
                                                @endif
                                                <tr>
							<td>Status:</td><td align="right">{{ ucwords($invoice->status) }}</td>
						</tr>
					</table>
			</td>
		</tr>		
		<tr>
			<td colspan="2" style="font-size:60px;letter-spacing:5px;line-height:60px">Invoice</td>
		</tr>		
		<tr>
			<td colspan="2" height="100"></td>
		</tr>		
		<tr>
			<td colspan="2" style="font-size:13px;letter-spacing:1px">
				<table cellpadding="5" cellspacing="0" style="width:700px" width="700">
					<tr>
						<th align="center">Picture<hr/></th>
						<th align="center">Item<hr /></th>
						<th align="center">Price<hr /></th>
						<th align="center">Shipping<hr /></th>
						<th align="center">QTY<hr /></th>
						<th align="center">Total<hr /></th>
					</tr>
					 @foreach($invoice->items as $item)
                                            @if($item->type == 'item')
                                            <tr>
                                                    <td align="center">
                                                         @if($item->picture != '')
                                                         <img src="{{ asset('user-uploads/products/'.$item->product_id.'/'.$item->picture.'') }}" width="50" alt=""/>
                                                         @else
                                                         <img src="{{ asset('img/img-dummy.jpg') }}" width="50" alt=""/>
                                                         @endif
                                                    </td>
                                                    <td align="center">{{ ucfirst($item->item_name) }}
                                                    @if(!is_null($item->item_summary))
                                                            <p class="item-summary">{{ $item->item_summary }}</p>
                                                    @endif
                                                    </td>
                                                    <td align="center">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$item->sale_price, 2, '.', '') }}</td>
                                                    <td align="center">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$item->shipping_price, 2, '.', '') }}</td>
                                                    <td align="center">{{ $item->quantity }}</td>
                                                    <td align="center">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$item->amount, 2, '.', '') }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
					<tr>
						<td align="right" colspan="5">Subtotal<hr /></td>
						<td align="center">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->sub_total, 2, '.', '') }}<hr /></td>
					</tr>
                                        @if($discount != 0 && $discount != '')
                                        <tr>
						<td align="right" colspan="5">Discount<hr /></td>
						<td align="center">-{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$discount, 2, '.', '') }}<hr /></td>
					</tr>
                                         @endif
                                         @foreach($taxes as $key=>$tax)
                                         <tr>
						<td align="right" colspan="5">{{ strtoupper($key) }}<hr /></td>
						<td align="center">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$tax, 2, '.', '') }}<hr /></td>
					</tr>
                                         @endforeach
			
					<tr>
						<td align="right" colspan="5">Total</td>
						<td align="center">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
					</tr>
				</table>
			</td>
		</tr>			
		<tr>
			<td colspan="2" height="100"></td>
		</tr>
                @if(!is_null($invoice->note))
                <tr>
			<td colspan="2"><b>Notes: </b> {!! nl2br($invoice->note) !!}</td>
		</tr>
                @endif
                @if($invoice->status == 'unpaid')
                <tr>
			<td colspan="2" style="word-break: break-all;"><p style="width:700px"><b>Invoice Terms: </b> {!! nl2br($invoiceSetting->invoice_terms) !!}</p></td>
		</tr>
                @endif
					
		<tr>
			<td colspan="2" height="50"></td>
		</tr>
	</table>
  </body>
</html>