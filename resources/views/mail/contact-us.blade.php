@extends('layouts.mail')

@section('content')
	<tr>
		<td height="30" style="font-size:16px; color:#4d4d4d; font-family:Open Sans; padding:10px 20px;font-weight:bold">Dear {{ $data['admin_name'] }},</td>
	</tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"> You have new Inquiry from Customer {{ $data['name'] }}</td></tr>
	<tr><td height="10"></td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Name: </b>{{ $data['name'] }}</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Email: </b>{{ $data['email'] }}</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Contact no.: </b>{{ $data['contact_no'] }}</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Subject : </b>{{ $data['subject'] }}</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Message: </b>{{ $data['message'] }}</td></tr>
	<tr><td height="10"></td></tr>
	<tr><td height="10"></td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Thanks, </b></td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> {{ $data['name'] }} </b></td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td height="20"></td></tr>
@endsection