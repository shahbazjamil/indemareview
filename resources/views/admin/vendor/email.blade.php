<html>

<head>
</head>

<body>
  <h2>
    {{  $data->Subject }}
  </h2>
<br>
<p>{!! $bodyMessage !!}</p>
<?php if(isset($invoiceID) && $invoiceID!='') {
    echo 'Please click the link to payment. ';
 echo '<a href="' . route("front.invoice", [md5($invoiceID)]) . '" target="_blank" data-toggle="tooltip" ><i class="fa fa-link"></i> ' . __('modules.payments.paymentLink') . '</a>';
} ?>
<?php if(isset($estimateID) && $estimateID!='') {
    echo 'Please click the link to view estimate. ';
 echo '<a href="' . route("front.estimate.show", md5($estimateID)) . '" target="_blank" data-toggle="tooltip" ><i class="fa fa-link"></i> Estimate Link</a>';
} ?>
</body>
</html>