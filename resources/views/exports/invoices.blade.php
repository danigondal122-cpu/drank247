
<!DOCTYPE html>
<html>
<head></head>
<body>
{{-- {{dd($franchise)}} --}}
<table style="font-size:20px;">
   <thead>
      <tr>
         <th colspan="5" style="text-align:center">{{$franchise}}</th>
     </tr>
      <tr style="font-size:20px;">
         <th>Order No</th>
         <th>Delivery Person</th>
         <th>Start Time</th>
         <th>End Time</th>
         <th>Total Time</th>
      </tr>
      {{-- {{dd($invoices)}} --}}
         @foreach($invoices as $value)
         <tr>
            <td>{{ $value['id']}}</td>
            <td>{{ $value['dp_name'] }}</td>
            <td>{{ $value['od_start_time'] }}</td>
            <td>{{ $value['od_end_time'] }}</td>
            <td>{{ $value['TotalOrderTime'] }}</td>
         </tr>
         @endforeach
         <tr>
            <th colspan="4">Total Hours</th>
            <th colspan="4">{{$TotalHours}}</th>
         </tr>

   </thead>
   <tbody>

   </tbody>
</table>
</body>
</html>


