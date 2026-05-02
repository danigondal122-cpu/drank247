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
                                 <td height="30" style="font-size:16px; color:#4d4d4d; font-family:Open Sans; padding:10px 20px;font-weight:bold">Dear Admin,</td>
                              </tr>
                              <tr>
                                 <td height="10"></td>
                              </tr>
                              <tr>
                                 <td height="20" style="padding:0px 20px;">Stock Order</td>
                              </tr>
                              <tr>
                                 <td height="30" style="padding:10px 20px;">
                                    <table class="table" width="100%" style="border:1px solid #f2f2f2;" cellpadding="4" cellspacing="0">
                                       <tbody>
                                          <tr style="background-color:#f2f2f2;">
                                             <td><b>Order No: {{$order_id}}</b></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <td height="30" style="padding:10px 20px;">
                                 <table class="table" width="100%" style="border:1px solid #f2f2f2;" cellpadding="4" cellspacing="0">
                                    <tbody>
                                       <tr>
                                          <td><b>Franchise Name: </b></td>
                                          <td>{{$franchise->franchises_name}}</td>
                                       </tr>
                                       <tr>
                                          <td><b>Email: </b></td>
                                          <td>{{$franchise->franchises_email}}</td>
                                       </tr>
                                       <tr>
                                          <td><b>Address:</b></td>
                                          <td>{{$franchise->company_name }} <br/>{{$franchise->house_no_street .'-'.$franchise->block_no}}<br/>{{$franchise->residence}}<br/>{{$franchise->landmark}}<br/>{{$franchise->post_code}}</td>
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
                                             <th  style="border:1px solid #eee;">Product</th>
                                             <th  style="border:1px solid #eee;">Qty</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          @foreach($orderdetail as $value)
                                          <tr>
                                             <td  style="border:1px solid #eee;">{{$value->product_name}}</td>
                                             <td style="text-align:center;border:1px solid #eee;">{{$value->qty}}</td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <tr>
                              <tr>
                                 <td height="20"></td>
                              </tr>
                           </table>
                           {{-- {{dd('sas')}} --}}
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
   </body>
</html>