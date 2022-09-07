<!doctype html>
<html lang="en">
  <head>
    
    <title>Invoice</title>
  </head>
  <body>
	<table cellpadding="0" cellspacing="0" width="700" style="font-family:Arial, Helvetica, sans-serif;font-size:14px" align="center">
		<tr>
			<td valign="middle"><a href="#"><img src="{{ asset('user-uploads/products/logo.jpg') }}" alt="" /></a></td>
			<td valign="middle" align="right">
					<table cellpadding="10" cellspacing="0">
						<tr>
							<td>Invoice Number:</td><td align="right">#123456</td>
						</tr>
						<tr>
							<td>Invoice Date:</td><td align="right">May 5 2025</td>
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
						<th align="center">Picture1 <hr/></th>
						<th align="center">Item<hr /></th>
						<th align="center">Price<hr /></th>
						<th align="center">Shipping<hr /></th>
						<th align="center">QTY<hr /></th>
						<th align="center">Total<hr /></th>
					</tr>
					<tr>
						<td align="center"><img src="{{ asset('user-uploads/products/invoice-img.jpg') }}" width="50" alt=""/></td>
						<td align="center">ITEM 1 - LESLEY SOFA</td>
						<td align="center">$10</td>
						<td align="center">$10</td>
						<td align="center">4</td>
						<td align="center">$40</td>
					</tr>
					<tr>
						<td align="center"><img src="{{ asset('user-uploads/products/invoice-img.jpg') }}" width="50" alt=""/></td>
						<td align="center">ITEM 1 - LESLEY SOFA</td>
						<td align="center">$15</td>
						<td align="center">$15</td>
						<td align="center">2</td>
						<td align="center">$30</td>
					</tr>
					<tr>
						<td align="center"><img src="{{ asset('user-uploads/products/invoice-img.jpg') }}" width="50" alt=""/></td>
						<td align="center">ITEM 1 - LESLEY SOFA</td>
						<td align="center">$20</td>
						<td align="center">$20</td>
						<td align="center">1</td>
						<td align="center">$20</td>
					</tr>
					<tr>
						<td align="center"><img src="{{ asset('user-uploads/products/invoice-img.jpg') }}" width="50" alt=""/></td>
						<td align="center">ITEM 1 - LESLEY SOFA</td>
						<td align="center">$15</td>
						<td align="center">$15</td>
						<td align="center">1</td>
						<td align="center">$15</td>
					</tr>
					<tr>
						<td align="right" colspan="5">Subtotal<hr /></td>
						<td align="center">$105<hr /></td>
					</tr>
					<tr>
						<td align="right" colspan="5">Tax<hr /></td>
						<td align="center">$5<hr /></td>
					</tr>
					<tr>
						<td align="right" colspan="5">Total</td>
						<td align="center">$100</td>
					</tr>
				</table>
			</td>
		</tr>			
		<tr>
			<td colspan="2" height="100"></td>
		</tr>	
		<tr>
			<td colspan="2"><b>Notes: </b>Proposition 65 Warning: Products known to the state of California are known to have exposure to cancer.</td>
		</tr>			
		<tr>
			<td colspan="2" height="50"></td>
		</tr>
	</table>
  </body>
</html>