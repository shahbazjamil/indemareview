<!doctype html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Purchase Order # {{ $po->purchase_order_number }}</title>
        <style>
            body{font-family:Arial;font-size:14px;padding:0 !important;margin:0}
            *{box-sizing:border-box}
            table{background:#FFF}
            h1{text-transform:uppercase;font-size:36px;letter-spacing:5px}
            th{background:#000;color:#FFF;text-transform:uppercase;text-align:left;font-weight:normal;padding:5px 15px;font-family:arial}
            span{display:block;width:100%;}
            .header{border-bottom:7px solid #000;width:100%;}.header img{width:200px;}
            .c-name, .vendor, .comments, .total{margin:20px 40px; line-height:24px;width:720px;}
            .vendor, .comments, .total{margin-top:0;border-collapse:collapse;border:1px solid #000;}.vendor td, .comments td, .total td{border:1px solid #000;}
            .comments{width:300px;float:left;}.totald{margin-left:0;width:300px;float:left;}.totald tr td:first-child{width:100px;color:#FFF;padding:0;}
            .no-transform{text-transform:none;}.totald tr td{vertical-align:top;border:1px solid #000}
			.vendor.table td:last-child{background:#F2F3F2;}
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" border="0" align="center" style="width:1000px">
            <tr>
                <td>
                    <table class="header" align="center">
                        <tr>
                            <td width="230px"><img src="{{ $company->logo_url }}" alt="" /></td>
                            <td><h1>Purchase Order</h1></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="c-name">
                        <tr>
                            <td>
                                <b>{{ $company->company_name }}</b>
                                <span>{{ $company->address }}</span>
                                <span>{{ $company->company_phone }}</span> 
                            </td>
                            <td align="right" valign="top">
                                <span>DATE : {{ $po->purchase_order_date->format($global->date_format) }}</span>
                                <span>PO# : {{ $po->purchase_order_number }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="vendor" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Vendor Information</th>
                            <th>Ship to</th>
                        </tr>
                        <tr>
                            <td>
                                <b>{{ ucwords($po->vendor->vendor_name) }}</b>
                                <span>{{ $po->address }}</span>
                                <span>{{ $po->contact }}</span>
                            </td>
                            <td valign="top">
                                <span>{{ $po->company }}</span>
                                <span>{{ $po->shipping_address }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="vendor table" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        @foreach($po->items as $key => $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->item_summary }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit_price }}</td>
                                <td>{{ number_format((float)$item->amount, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
				<div class="comments">
					<table class="" cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<th class="no-transform">Memo to be displayed on the purchase order</th>
							</tr>
							<tr>
								<td style="height:70px">
                                                                    {{ $po->memo_order }}
								</td>
							</tr>
						</table>
				</div>
				<div class="totald">
					<table class="" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <th width="100">Sub Total</th>
                            <td width="100">{{ number_format((float)$po->product_subtotal , 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <th width="100">Discount</th>
                            <td width="100">{{ $po->discount }}</td>
                        </tr>
                        <tr>
                            <th width="100">Tax</th>
                            <td width="100">{{ number_format((float)$po->sales_tax, 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <th width="100">Grand Total</th>
                            <td width="100" style="background:#F2F3F2">{{ number_format((float)$po->total_amount, 2, '.', '') }}</td>
                        </tr>
                    </table>
				</div>
                    
                </td>
            </tr>
        </table>
    </body>
</html>