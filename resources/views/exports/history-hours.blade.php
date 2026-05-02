
<!DOCTYPE html>
<html>
<head></head>
<body>
{{-- {{dd($franchise)}} --}}
<table style="font-size:20px;">
   <thead>
      <tr>
         <th colspan="3" style="text-align:center">{{$franchise}}</th>
     </tr>
     <tr>
      <th colspan="3" style="text-align:center">Delivery Person: {{$deliveryperson}}</th>
     </tr>
      <tr style="font-size:12px;background-color:#f2f2f2;text-align:center;">
         <th><b>Date</b></th>
         <th><b>Time</b></th>
         <th><b>Odo Meter</b></th>
      </tr>
      {{-- {{dd($invoices)}} --}}
         @foreach($invoices as $value)
         <tr style="text-align:center;">
            <td>{{ $value['Date']}}</td>
            <td>{{ $value['TotalOrderTime'] }}</td>
            <td>{{ $value['OdoMeter'] }}</td>
         </tr>
         @endforeach
         <tr style="background-color:#f2f2f2;text-align:center;">
            <th style="font-size:12px;"><b>Total Hours</b></th>
            <th style="font-size:12px;"><b>{{$TotalHours}}</b></th>
            <th></th>
         </tr>
     
   </thead>
   <tbody>

   </tbody>
</table>
</body>
</html>


     