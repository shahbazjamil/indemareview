<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>indema-products</title><link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
<style>body{font-family: 'Lato', sans-serif;}</style>
  </head>
  <body>
	<div class="table-container" style="width:700px;margin:0 auto">
	<img src="{{ $company->logo_url }}" width="210px" height="66px" /><br /><br />
        @forelse($products as $product)
        
		<table style="width:700px;border-collapse:collapse;font-family:'Lato', sans-serif;font-size:11px;" cellspacing="0" cellpadding="5">			
				<tr>
					<th width="20" style="font-size:10px;width:20px;border-bottom:1px solid #000">NUM</th>
					<th style="font-size:10px;border-bottom:1px solid #000">NAME</th>
					<th style="font-size:10px;width:50px;border-bottom:1px solid #000">IMAGE</th>
					<th style="font-size:10px;border-bottom:1px solid #000">PROJECT</th>
					<th style="font-size:10px;border-bottom:1px solid #000">LOCATION</th>
					<th style="font-size:10px;border-bottom:1px solid #000">CATEGORY</th>
					<th style="font-size:10px;border-bottom:1px solid #000">VENDOR</th>
					<th style="font-size:10px;border-bottom:1px solid #000">MANUFACTURER</th>
					<th style="font-size:10px;border-bottom:1px solid #000">NOTES</th>
					<th style="font-size:10px;border-bottom:1px solid #000">URL</th>
				</tr>
				<tr>
					<td valign="top" style="border-right:1px solid #000">{{ $product['sq_num'] }}</td>
					<td valign="top">{{ $product['name'] }}</td>
					<td valign="top"><img src="{{ $product['image'] }}" alt="product" width="50" height="50"></td>
					<td valign="top">{{ $product['project'] }}</td>
					<td valign="top">{{ $product['location'] }}</td>
					<td valign="top">{{ $product['category'] }}</td>
					<td valign="top">{{ $product['vendor'] }}</td>
					<td valign="top">{{ $product['manufacturer'] }}</td>
					<td valign="top">{{ $product['notes'] }}</td>
					<td valign="top">{{ $product['url'] }}</td>
				</tr>
				<tr>
					<td style="border-right:1px solid #000"></td>
					<td valign="top"><b>Dimensions</b><br />{{ $product['dimensions'] }}</td>
					<td></td>
					<td valign="top"><b>Material</b><br />{{ $product['materials'] }}</td>
					<td valign="top"><b>Lead Time</b><br />{{ $product['lead_time'] }}</td>
					<td valign="top"><b>Cost Per Unit</b><br />{{ $product['cost_per_unit'] }}</td>
					<td valign="top"><b>Markup</b><br />{{ $product['default_markup'] }}</td>
					<td valign="top"><b>Sales Tax</b><br />{{ $product['sales_tax'] }}</td>
					<td valign="top"><b>Freight</b><br />{{ $product['freight'] }}</td>
					<td valign="top"><b>Total</b><br />{{ $product['total'] }}</td>
				</tr>
				<tr>
					<td style="border-right:1px solid #000"></td>
					<td valign="top"><b>Acknowledgement</b><br />{{ $product['acknowledgement'] }}</td>
					<td></td>
					<td valign="top"><b>Est. Ship Date</b><br />{{ $product['est_ship_date'] }}</td>
					<td valign="top"><b>Act. Ship Date</b><br />{{ $product['act_ship_date']}}</td>
					<td valign="top"><b>Est. Receive</b><br />{{ $product['est_receive'] }}</td>
					<td valign="top"><b>Act. Receive</b><br />{{ $product['act_receive'] }}</td>
					<td valign="top"><b>Receive By</b><br />{{ $product['receive_by'] }}</td>
					<td valign="top"><b>Est. Install </b><br />{{ $product['est_install'] }}</td>
					<td valign="top"><b>Act. Install</b><br />{{ $product['act_install'] }}</td>
				</tr>
		</table>
            <br /><br /><br />
        @empty
        
        <table style="width:700px;border-collapse:collapse;font-family:arial;font-size:11px;" cellspacing="0" cellpadding="5">			
				<tr>
					<th width="20" style="font-size:10px;width:20px;border-bottom:1px solid #000">NUM</th>
					<th style="font-size:10px;border-bottom:1px solid #000">NAME</th>
					<th style="font-size:10px;width:50px;border-bottom:1px solid #000">IMAGE</th>
					<th style="font-size:10px;border-bottom:1px solid #000">PROJECT</th>
					<th style="font-size:10px;border-bottom:1px solid #000">LOCATION</th>
					<th style="font-size:10px;border-bottom:1px solid #000">CATEGORY</th>
					<th style="font-size:10px;border-bottom:1px solid #000">VENDOR</th>
					<th style="font-size:10px;border-bottom:1px solid #000">MANUFACTURER</th>
					<th style="font-size:10px;border-bottom:1px solid #000">NOTES</th>
					<th style="font-size:10px;border-bottom:1px solid #000">URL</th>
				</tr>
                                <tr><td colspan="10">No Products</td></tr>
        </table>
         <br /><br /><br />
        @endforelse
		
	</div><!--end of table-container-->
  </body>
</html>