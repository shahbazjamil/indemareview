<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
        .mail-header{
            box-sizing: border-box;
            padding: 20px 0 5px 0;
            text-align: center;
        }
        .mail-header-link{
            box-sizing: border-box;
            color: #bbbfc3;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
            display: flex;
        }
        .mail-header-image{
            object-fit: cover;
            width: auto;
            height: 65px;
        }

        /* Body */
        .mail-body{
            box-sizing: border-box;
            background-color: #ffffff;
            border-bottom: 1px solid #edeff2;
            border-top: 1px solid #edeff2;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .mail-body-table{
            box-sizing: border-box;
            background-color: #ffffff;
            margin: 0 auto;
            padding: 0;
        }
        .mail-body-table-td{
            box-sizing: border-box;
            padding: 35px;
        }
        .mail-body-table-td p{
            box-sizing: border-box;
            color: #3d4852;
            font-size: 16px;
            line-height: 1.5em;
            margin-top: 0;
            text-align: left;
        }

        /* Footer */
        .mail-footer-table{
            box-sizing: border-box;
            margin: 0 auto;
            padding: 0;
            text-align: center;
            width: 570px;
        }
        .mail-footer-table-td{
            padding: 35px;
        }
        .mail-footer-table-td p{
            box-sizing: border-box;
            line-height: 1.5em;
            margin-top: 0;
            color: #aeaeae;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0">
                <tr style="background: #ffffff;">
                    <td class="header mail-header">
                        <a href="{{ !empty($company) ? $company['website'] : config('app.url') }}" class="mail-header-link">
                            <img src="{{ !empty($company) ? $message->embed($company['gmail_logo_url']) : config('app.logo') }}" class="mail-header-image" alt="">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td class="body mail-body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body mail-body-table" align="center" width="570" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-cell mail-body-table-td">
                                    {!! $email_template['body'] !!}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr style="height: 100px;background: #ffffff;">
                    <td>
                        <table class="footer mail-footer-table" align="center" width="570" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-cell mail-footer-table-td" align="center">
                                    <p> &copy; {{ date('Y') }} {{ !empty($company) ? $company['company_name'] : config('app.name') }}.  All rights reserved.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>