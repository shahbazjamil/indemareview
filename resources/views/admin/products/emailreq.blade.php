<html>

<head>
</head>

<body>
  <h2>
<!--    {{  $data->Subject }}-->
  </h2>
<br>

<p>Hello! Please find below information for a product that I need to request a quote for. Please reply if you need any information I may have missed. Thank you</p>
<p> <b>Product Name:</b> {!! $ProductName !!}</p>
<p> <b>Product Number:</b> {!! $ProductNumber !!}</p>
<p> <b>Vendor:</b> {!! $Vendor !!}</p>
<p> <b>URL:</b> {!! $URL !!}</p>
<p> <b>Dimensions:</b> {!! $Dimensions !!}</p>
<p> <b>Finish/Color:</b> {!! $FinishColor !!}</p>
<br>

<b>Additional Information:</b>
<p>{!! $bodyMessage !!}</p>

</body>

</html>