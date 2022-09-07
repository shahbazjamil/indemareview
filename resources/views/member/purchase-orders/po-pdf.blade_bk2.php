<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Purchase Order # {{ $po->purchase_order_number }}</title>
    <style>
        /* Please don't remove this code it is useful in case of add new language in dompdf */

        /* @font-face {
            font-family: Hind;
            font-style: normal;
            font-weight: normal;
            src: url({{ asset('fonts/hind-regular.ttf') }}) format('truetype');
        } */

         /* For hindi language  */

        /* * {
           font-family: Hind, DejaVu Sans, sans-serif;
        } */

         /* For japanese language */

        @php
            $font = '';
            if($company->locale == 'ja') {
                $font = 'ipag';
            } else if($company->locale == 'hi') {
                $font = 'hindi';
            } else {
                $font = 'noto-sans';
            }
        @endphp

        * {
            font-family: {{$font}}, DejaVu Sans , sans-serif;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 100%;
            height: auto;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: Verdana, Arial, Helvetica, sans-serif;
        }

        h2 {
            font-weight:normal;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 11px;
        }

        #logo img {
            height: 55px;
            margin-bottom: 15px;
        }

        #company {

        }

        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name, div.name {
            font-size: 1.2em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {

        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;table-layout;fixed;
        }

        table th,
        table td {
            padding: 5px 10px 7px 10px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: left;
        }

        table td.desc h3, table td.qty h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0 0;
        }

        table .no {
            color: #555;
            font-size: 1.6em;
            background: #DDD;
            width: 30px;text-align:center;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
        }


        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total
        {
            font-size: 1em;
            text-align: center;
        }

        table td.unit{
            width: 100px;
        }

        table td.desc{
            
        }

        table td.qty{
            width: 80px;
        }

        .status {
            margin-top: 15px;
            padding: 1px 8px 5px;
            font-size: 1.3em;
            width: 80px;
            color: #fff;
            float: right;
            text-align: center;
            display: inline-block;
        }

        .status.unpaid {
            background-color: #E7505A;
        }
        .status.paid {
            background-color: #26C281;
        }
        .status.cancelled {
            background-color: #95A5A6;
        }
        .status.error {
            background-color: #F4D03F;
        }

        table tr.tax .desc {
            text-align: right;
            color: #1BA39C;
        }
        table tr.discount .desc {
            text-align: right;
            color: #E43A45;
        }
        table tr.subtotal .desc {
            text-align: right;
            color: #1d0707;
        }
        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 10px 10px 20px 10px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-bottom: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        table.billing td {
            background-color: #fff;
        }

        table td div#invoiced_to {
            text-align: right;
        }

        #notes{
            color: #767676;
            font-size: 11px;
        }

        .item-summary{
            font-size: 12px
        }

        .mb-3{
            margin-bottom: 1rem;
        }
        .logo {
            text-align: left;
        }
        .logo img {
            max-width: 150px;
        }
		th.unit{width:100px}
		table.vendor{border-collapse:collapse;}table.vendor, table.vendor td{border:1px solid #FFF;}
        table.vendor th{
			color: #555;
			text-align:left;
			background: #DDD;border:1px solid #FFF;width:50%;
		}   
    </style>
</head>
<body>
<header class="clearfix">

    <table cellpadding="0" cellspacing="0" class="billing">
        <tr>
            <td>
                <div id="company">
                    <div class="logo">
                        <img src="{{ $company->logo_url }}" alt="home" class="dark-logo" />
                    </div>
                    <h3 class="name">{{ ucwords($company->company_name) }}</h3>
                    <div>{!! nl2br($company->address) !!}</div>
                    <div>Contact# :{{ $company->company_phone }}</div>
                </div>
            </td>
            <td>
                <div id="invoiced_to">
                        <div class="">
                            <div><b>DATE :</b>{{ $po->purchase_order_date->format($company->date_format) }}</div>
                        </div>
                       <div class="name">
                            <small>PO# :</small><span class="bold">{{ $po->purchase_order_number }}</span>
                        </div>
                </div>
            </td>
        </tr>
    </table>
	<table class="vendor" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Vendor Information</th>
                            <th>Ship to</th>
                        </tr>
                        <tr>
                            <td><b>{{ ucwords($po->vendor->vendor_name) }} ( {{ ucwords($po->vendor->company_name) }} )</b></td>
                            <td>{{ $po->company }}</td>
                        </tr>
                        <tr>
                            <td>{!! nl2br($po->address) !!}</td>
                            <td>{!! nl2br($po->shipping_address) !!}</td>
                        </tr>
                       
                        <tr>
                            <td>{{ $po->contact }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
</header>
<main>

    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="desc">@lang("modules.invoices.item")</th>
            <th class="qty">@lang("modules.invoices.qty")</th>
            <th class="qty">@lang("modules.invoices.unitPrice") </th>
            <th class="unit">@lang("modules.invoices.price") </th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 0; ?>
        @foreach($po->items as $key => $item)
            @if($item->type == 'item')
            <tr style="page-break-inside: avoid;">
                <td class="no">{{ ++$count }}</td>
                <td class="desc"><h3>{{ ucfirst($item->item_name) }}</h3>
                    @if(!is_null($item->item_summary))
                        <p class="item-summary">{{ $item->item_summary }}</p>
                    @endif
                </td>
                <td class="qty"><h3>{{ $item->quantity }}</h3></td>
                <td class="qty"><h3>{{ number_format((float)$item->unit_price, 2, '.', '') }}</h3></td>
                <td class="unit">{{ number_format((float)$item->amount, 2, '.', '') }}</td>
            </tr>
            @endif
        @endforeach
        <tr style="page-break-inside: avoid;" class="subtotal">
            <td class="no">&nbsp;</td>
            <td class="qty">&nbsp;</td>
            <td class="qty">&nbsp;</td>
            <td class="desc">@lang("modules.invoices.subTotal")</td>
            <td class="unit">{{ number_format((float)$po->product_subtotal, 2, '.', '') }}</td>
        </tr>
        @if($discount != 0 && $discount != '')
        <tr style="page-break-inside: avoid;" class="discount">
            <td class="no">&nbsp;</td>
            <td class="qty">&nbsp;</td>
            <td class="qty">&nbsp;</td>
            <td class="desc">@lang("modules.invoices.discount")</td>
            <td class="unit">-{{ number_format((float)$discount, 2, '.', '') }}</td>
        </tr>
        @endif
        @foreach($taxes as $key=>$tax)
            <tr style="page-break-inside: avoid;" class="tax">
                <td class="no">&nbsp;</td>
                <td class="qty">&nbsp;</td>
                <td class="qty">&nbsp;</td>
                <td class="desc">{{ strtoupper($key) }}</td>
                <td class="unit">{{ number_format((float)$tax, 2, '.', '') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr dontbreak="true">
                <td colspan="4">@lang("modules.invoices.total")</td>
                <td style="text-align: center">{{ number_format((float)$po->total_amount, 2, '.', '') }}</td>
            </tr>
            
        </tfoot>
    </table>
    <p>&nbsp;</p>
    <hr>
    <p id="notes">
        @if(!is_null($po->memo_order))
            {!! nl2br($po->memo_order) !!}<br>
        @endif
    </p>

</main>
</body>
</html>