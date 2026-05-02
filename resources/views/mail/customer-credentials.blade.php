@extends('layouts.mail')

@section('content')
	<tr>
		<td height="30" style="font-size:16px; color:#4d4d4d; font-family:Open Sans; padding:10px 20px;font-weight:bold">Dear {{ $data['name'] }}</td>
	</tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;">Thanks for registration with 247 Drank system.</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;">Please use below credentials to login in.</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Email: </b>{{ $data['email'] }}</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Password: </b>{{ $data['password'] }}</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td height="20"></td></tr>
@endsection