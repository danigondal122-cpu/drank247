<!DOCTYPE html>
<html>

<head></head>

<body>
   <style>
      /* @font-face {
         font-family: 'Lato-Italic';
         src: url('http://fonts.googleapis.com/css?family=Lato:400,700') format('truetype');
         font-weight: normal;
         font-style: normal;
      } */

      body {
         font-family: 'Lato', sans-serif;
      }

      footer {
         position: fixed;
         bottom: 0px;
         left: 0px;
         right: 0px;
         height: 50px;
         font-size: 30px;

         /** Extra personal styles **/
         text-align: center;
         line-height: 35px;
      }
   </style>
   <table id="example2" width="50%" cellspacing="" style="width:100%;">
      <tr>

         <td style="font-size:30px;vertical-align:bottom;text-align:left;margin-top:10px;;">
            <h3>Reporting</h3>
         </td>
         <td align="right"><img src="{{$image_url}}" width="400"></td>
      </tr>

   </table>

   <table style="padding-top: 40px;">
      <tr>
         <td>Period: {{ isset($start_from_date) ? $start_from_date:'' }} until {{ isset($end_to_date) ? $end_to_date:'' }} included:</td>
      </tr>
   </table>
   <table style="padding-top: 20px;">
      <tr>
         <td style="width: 40%;">Total</td>
         <td>{{$total}} orders with a total amount of € {{$total_order_amount}}</td>
      </tr>
      <tr>
         <td>Paid online *</td>
         <td>{{$online_total_order}} orders with a total amount of € {{$online_pay_total}}
         </td>
      </tr>
   </table>

   <!-- <table id="example2" width="50%" cellspacing="4" style="width:100%;margin-top:15px;">
    <tr>
       <td>Total : 10 orders with a total amount of € 377.75</td>
    </tr>

</table> -->


   <table id="example2" width="50%" cellspacing="0" cellpadding="10" style="border:1px solid #c4c4c4;width:100%;margin-top:20px;">
      <tr style="">
         <td style="border-top:1px solid #c4c4c4;background-color:#444444;color:#fff;"></td>
         <td style="border-top:1px solid #c4c4c4;background-color:#444444;color:#fff;"><b>Date</b></td>
         <td style="border-top:1px solid #c4c4c4;background-color:#444444;color:#fff;"><b>#</b></td>
         <td style="border-top:1px solid #c4c4c4;background-color:#444444;color:#fff;"><b>Delivery Person</b></td>
         <td style="border-top:1px solid #c4c4c4;background-color:#444444;color:#fff;"><b>Price</b></td>
         <td style="border-top:1px solid #c4c4c4;background-color:#444444;color:#fff;"><b>Order Payment</b></td>

      </tr>

      @foreach($order as $value)
      <?php //$total_order_amount += $value->order_final_with_discount;
      ?>
      <tr>
         <td style="border-top:1px solid #c4c4c4;"><img width="30" height="30" src="{{ $value->channel_image ? asset('images/channel/'.$value->channel_image) : url('images/channel/drank.png') }}"></img></td>
         <td style="border-top:1px solid #c4c4c4;">{{$value->new_order_date}}</td>
         <td style="border-top:1px solid #c4c4c4;">{{$value->order_channel_order_id}}</td>
         <td style="border-top:1px solid #c4c4c4;"> {{$value->dp_name}}</td>
         <td style="border-top:1px solid #c4c4c4;">€ {{str_replace('.',',',number_format($value->order_final_with_discount,2))}}</td>
         <td style="border-top:1px solid #c4c4c4;"> {{$value->order_payment_status ? 'ONLINE':'CASH/PIN'}}</td>


      </tr>
      @endforeach



   </table>



</body>

</html>
<?php // exit();
?>
