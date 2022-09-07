<html>
    <head>
	<link href="https://app.indema.co/pdf-fonts/style.css" rel="stylesheet" />
        <style>            
            *{box-sizing:border-box;}
            body{font-family: 'MyriadPro-Regular', helvetica;font-size:13px;}
            h2{font-size:24px;font-weight:400;letter-spacing:5.0;}
            h3{font-size:18px;font-weight:400}
            h5{font-size:12px;margin:0;font-weight:400}
            ul{padding-left:15px;margin:0 0 15px}
            .w-100{width:100%;}.m-0{margin:0 !important;}
            .mt-3{margin-top:1.5rem !important;}.mt-4{margin-top:2rem !important;}
            .mt-2{margin-top:1.0rem}.mb-2{margin-bottom:1.0rem}
            .p-0{padding:0 !important;}.pb-2{padding-bottom:1rem !important;}.pb-1{padding-bottom:0.5rem !important;}
			.pr-0{padding-right:0 !important;}.pl-0{padding-left:0 !important;}
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
			img{max-width:277px;max-height:281px;}
			p{margin:0 0 15px}
			textarea{min-height:60px;margin-bottom:10px}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="border-bottom text-left pb-1">FF&amp;E SPEC SHEET</h2>
                    <img src="{{ $image }}" alt="" />
                </div>
            </div><!--end of row-->
            <div class="row mt-4">
                <div class="col-12 border-bottom mb-2 p-0">
				<table style="width:100%">
				<tr>
				<td><h3 class="m-0">{{ $product->name }}</h3></td>
				<td><h3 class="text-right m-0">$ {{$product->itemObj->totalEstimatedCost ?? "00.00"}}</h3></td>
				</tr>
				</table>
                </div>
                <div class="col pl-0">
                    <table style="width:100%" valign="top">
                        <tr>
                            <td>
                                <p>{{ $product->itemObj->description ?? "" }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Vendor Number </td>
                        </tr>
                        <tr>
                            <td> {{$workroomVendor ?? ""}}</td>
                        </tr>
                    </table>
                </div>
                <div class="col f-right pr-0">
                    <table style="width:100%" valign="top">
                        <tr>
                        <td>
						<span>Material</span>
                            <ul>
                                @foreach($attrs as $key => $attr)
                                <li>{{ $key }} {{ $attr }}</li>
                                @endforeach
                            </ul>
                        </td>
                        </tr>
                        <tr>
                            <td>Unit</td>
                        </tr>
                        <tr>
                            <td>{{ $unitVal ?? "" }} {{ $unitName ?? "" }}</td>
                        </tr>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div><!--end of row-->
            <div class="row mt-4">
                <div class="col-12 border-bottom pb-2" style="padding-left:0;padding-right:0;">
                    <h5>Notes:</h5>
                    <textarea cols="10" rows="4" class="border w-100"></textarea>
                </div><!--end of col-12-->
            </div><!--end of row-->
        </div><!--end of container-->
    </body>
</html>