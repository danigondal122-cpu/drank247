
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
        }.table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}.table {
    width: 100%;
 
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
                                <td height="21" style="background:#e91362;font-size:20px; font-weight:bold; color:#ffffff; font-family:Open Sans; padding:0px 20px;"><strong>Order Confirmation</strong></td>
                            </tr>
                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td height="30" style="font-size:16px; color:#4d4d4d; font-family:Open Sans; padding:10px 20px;font-weight:bold">Dear {{$name}}, </td>
                            </tr>
                            
                            <tr>
                                <td height="10" style="padding:0px 20px;">Your Order Has Been Confirmed</td>
                            </tr>
                           
                            <tr>
                                <td height="30" style="padding:15px 20px;">
                                   <table class="table" width="100%" style="border:1px solid #f2f2f2;" cellpadding="4" cellspacing="0">
                                       <tbody>
                                        <tr>
                                            <td style="padding:15px 20px;"><b>Order No:</b></td>
                                            <td><b>{{$order->order_id}}</b></td>
                                            <td style="text-align:right;"><b>Scan QR code</b></td>
                                        </tr>  
                                        <tr> 
                                            <td style="padding:0px 20px; vertical-align:top"><b>Address:</b></td>
                                            <td style="vertical-align:top;">{{$order->address_address}}</td>
                                            <td style="text-align:right;vertical-align:top;">  <a target="_blank" >
                                                <img src="{{asset('uploads/qrcode/'.$scan)}}" height="100">
                                                </a></td>
                                        </tr>
                                       </tbody>
                                   </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="30" style="padding:10px 20px;">  
                                   
                                          <table class="table" width="100%" cellpadding="4" cellspacing="0">
                                            <thead>
                                            <tr style="background-color:#f2f2f2;">
                                              <th  style="border:1px solid #eee;">Image</th>
                                              <th  style="border:1px solid #eee;">Product</th>
                                              <th  style="border:1px solid #eee;">Qty</th>
                                              <th  style="border:1px solid #eee;">Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                   @foreach($orderdetail as $value)
                                               
                                            <tr>
                                              <td  style="border:1px solid #eee;text-align:center;">
                                                <img src="{{$value->image}}" alt="Product Image" class="img-size-50" width="50px;">
                                               </td>
                                              <td  style="border:1px solid #eee;">{{$value->product_name}}</td>
                                              <td style="text-align:center;border:1px solid #eee;">{{$value->od_qty}}</td>
                                              <td  style="border:1px solid #eee;text-align:right">&euro; {{number_format($value->od_vattotal,2)}}</td>
                                            </tr>
                                            @endforeach
                                     
                                            </tbody>
                                          </table>
                                          <table class="table" width="100%" style="text-align:right;margin-top:10px;">

                                              <tbody>
                                                  <tr>
                                                        <td><b>Total:</b></td>
                                                        <td><b>&euro; {{number_format($order->order_price,2)}}</b></td>
                                                  </tr>
                                                  <tr>
                                                        <td><b>Delivery Charge:</b></td>
                                                        <td><b>&euro; {{number_format($order->order_deliverycharge,2)}}</b></td>
                                                  </tr>
                                                  {{-- <tr>
                                                        <td><b>Final Charge:</b></td>
                                                        <td><b>&euro; {{number_format($order->order_finalamount,2)}}</b></td>
                                                  </tr> --}}
                                                  @if($order->order_promocode!="")
                                                  <tr>
                                                        <td><b>Discount:</b></td>
                                                        <td><b>&euro; {{number_format($order->order_discount,2)}}</b></td>
                                                   </tr>
                                                   @endif
                                                   <tr>
                                                        <td><b>Final Charge:</b></td>
                                                        <td><b>&euro; {{number_format($order->order_final_with_discount,2)}}</b></td>
                                                   </tr>
                                              </tbody>
                                          </table>
                                </td> 
                            </tr>
                            <tr>
                          
                            <tr>
                                <td height="20"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</body>
</html>