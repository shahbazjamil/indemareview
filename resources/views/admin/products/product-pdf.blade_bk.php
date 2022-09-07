<html>
    <head>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <style>
            *{box-sizing:border-box;}
            body{font-family: 'Roboto', sans-serif;font-size:14px;}
            h2{font-size:30px;font-weight:500}
            h3{font-size:18px;font-weight:500}
            h5{font-size:14px;margin:0;font-weight:500}
            ul{padding-left:15px;}
            .w-100{width:100%;}.m-0{margin:0 !important;}
            .mt-3{margin-top:1.5rem !important;}.mt-4{margin-top:2rem !important;}
            .mt-2{margin-top:1.0rem}.mb-2{margin-bottom:1.0rem}
            .p-0{padding:0 !important;}.pb-2{padding-bottom:1rem !important;}
            .text-center{text-align:center}
            .text-left{text-align:left}.text-right{text-align:right}
            .border{border:1px solid #000;}
            .border-bottom{border-bottom:1px solid #000;}
            .container{max-width:1170px;padding:0 15px;width:100%;margin:0 auto}
            .row{width:100%}
            .col{width:45%;float:left;padding:0 15px}
            .col-12{width:100%;padding:0 15px}
            .f-left{float:left}.f-right{float:right}
            .d-block{display:block;}.clearfix{clear:both;display:block}
            ul{list-style-type: none;}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="border-bottom text-left">FF&amp;E SPEC SHEET</h2>
                    <img src="{{ $image }}" alt="" />
                </div>
            </div><!--end of row-->
            <div class="row mt-4">
                <div class="col-12 border-bottom mb-2">
                    <h3 class="f-left">{{ $product->name }}</h3>
                    <span class="f-right">$ {{ $product->itemObj->totalEstimatedCost ?? "00.00" }}</span>
                    <div class="clearfix"></div>
                </div>
                <div class="col">
                        <table style="width:100%">
                                <tr>
                                        <td><h5>Project Name</h5></td><td class="text-right">{{ ucfirst($projectName) ?? " " }}</td>
                                </tr>
                                <tr>
                                        <td><h5>Location Code</h5></td><td class="text-right">{{ ucfirst($locationCode) ?? " " }}</td>
                                </tr>
                                <tr>
                                        <td><h5>Quantity</h5></td><td class="text-right">{{ $product->itemObj->quantity ?? "00" }}</td>
                                </tr>
                                <tr>
                                        <td><h5>Sales Price</h5></td><td class="text-right">$ {{ $product->itemObj->totalSalesPrice ?? "00" }}</td>
                                </tr>
                        </table>
                </div>
                <div class="col f-right">
                    <table style="width:100%">
                        <tr>
                            <td><h5>Manufacturer</h5></td><td class="text-right">{{ ucfirst($manufacturer) ?? " " }}</td>
                        </tr>
                        <tr>
                            <td><h5>Sales Category</h5></td><td class="text-right">{{ ucfirst($salesCategory) ?? "" }}</td>
                        </tr>
                        <tr>
                            <td><h5>Deposit amount</h5></td><td class="text-right">$ {{ $product->itemObj->clientDeposit ?? "00" }}</td>
                        </tr>
                         <tr>
                            <td><h5>Estimated Price</h5></td><td class="text-right">$ {{ $product->itemObj->totalEstimatedCost ?? "00" }}</td>
                        </tr>
                       
                    </table>
                </div>
                <div class="clearfix"></div>
            </div><!--end of row-->
            <div class="row mt-4">
                <div class="col-12 border-bottom pb-2">
                    <h5>Notes:</h5>
                    <textarea cols="10" rows="4" class="border w-100"></textarea>
                </div><!--end of col-12-->
            </div><!--end of row-->
        </div><!--end of container-->
    </body>
</html>