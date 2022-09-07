<!doctype html>
<html lang="en">
  <head>
    
    <title>Specification</title>
  </head>
  <body>
	<table cellpadding="0" cellspacing="0" width="700" style="font-family:Arial, Helvetica, sans-serif;font-size:14px;width:700px" align="center">
		<tr>
                    <td><a href="#"><img src="{{ $company->logo_url }}" width="225" alt="" /></a></td>
			<td style="font-size:30px">SPECIFICATION</td>
		</tr>
		<tr>
			<td colspan="2">
				<table>
				<tr><td style="width:55%;letter-spacing:1px">
					<table cellpadding="10" cellspacing="0" style="width:100%">
						<tr>
							<td>Product Name:</td>
							<td align="right">{{ $product->name }}</td>
						</tr>
						<tr>
							<td>Location Code:</td>
							<td align="right">{{ $locationCode }}</td>
						</tr>
						<tr>
							<td>Sales Category:</td>
							<td align="right">{{ $salesCategory }}</td>
						</tr>
						<tr>
							<td>Product Number:</td>
							<td align="right">{{ $product->product_number }}</td>
						</tr>
						<tr>
							<td>Finish:</td>
							<td align="right">{{ $product->finish_color }}</td>
						</tr>
						<tr>
							<td>Material:</td>
							<td align="right">{{ $product->materials }}</td>
						</tr>
						<tr>
							<td>Dimensions:</td>
							<td align="right">{{ $product->dimensions }}</td>
						</tr>
					</table>
				</td>
				<td style="width:45%;float:left;text-align:center"><img src="{{ $image }}" height="300px" alt="" /></td>
				</tr></table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="letter-spacing:1px;font-size:14px;line-height:30px">
				<b>Description: </b>{{ $product->itemObj->description ?? "" }}			
			</td>
		</tr>
		<tr>
			<td colspan="2" height="30"></td>
		</tr>
		@if(!is_null($product->notes))
                    <tr>
                            <td colspan="2" style="letter-spacing:1px;font-size:14px;line-height:30px">
                                    <b>Notes: </b>{!! nl2br($product->notes) !!}
                            </td>
                    </tr>	
                    
                @endif	
		<tr>
			<td colspan="2" height="50"></td>
		</tr>
	</table>
  </body>
</html>