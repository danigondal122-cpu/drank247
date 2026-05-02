
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        .ExternalClass {
            display: block !important;
        }
        body {
            font-family: "Open Sans";
            color: #333333;
            font-size: 14px;
        }
    </style>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" bgcolor="#ffffff" style="font-family:Open Sans">
<table style="width: 100%;" align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td>
            <table style="width: 600px;" align="center" border="0"cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td width="600" style="border:1px solid #71CAEA;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="73" style="background:#FFFFFF; padding-left:20px;">
                                    <a target="_blank" href="">
                                        <img src="{{asset('img/247-Drank-Logo.png')}}" style="width:170px;height:70px;">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td height="21" style="background:#e91362;font-size:20px; font-weight:bold; color:#ffffff; font-family:Open Sans; padding:0px 20px;"><strong>New Registration</strong></td>
                            </tr>
                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td height="30" style="font-size:16px; color:#4d4d4d; font-family:Open Sans; padding:10px 20px;font-weight:bold">Dear  {{$adminname}}</td>
                            </tr>
                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td height="20" style="padding:0px 20px;"></td>
                            </tr>
                            <tr>
                                <td height="10"></td>
                            </tr>
                             <tr><td height="20" style="padding:0px 20px;">You have new Inquiry from Customer {{$name}}</td></tr>
                             <tr>
                             <td height="10"></td>
                             </tr>
                             <tr>
                                <td height="10"></td>
                            </tr>
                            <tr><td height="20" style="padding:0px 20px;"><b> </td></tr>
                         
                            <tr>
                                <td height="20" style="padding:0px 20px;">Name:{{$name}}</td>
                            </tr>
                            <tr>
                                <td height="20" style="padding:0px 20px;">Email:{{$email}} </td>
                            </tr>
                            <tr>
                                <td height="20" style="padding:0px 20px;">Contact No:{{$contact_no}} </td>
                            </tr>
                            <tr>
                                <td height="20" style="padding:0px 20px;">Subject:{{$subject}}  </td>
                            </tr>
                            <tr>
                                <td height="20" style="padding:0px 20px;">Messages:{{$messagefrom}}  </td>
                            </tr>

                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            {{dd('sfsd')}}
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</body>
</html>