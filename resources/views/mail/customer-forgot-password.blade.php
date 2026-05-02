@extends('layouts.mail')

@section('content')
	<tr>
		<td height="30" style="font-size:16px; color:#4d4d4d; font-family:Open Sans; padding:10px 20px;font-weight:bold">Dear {{ $data['customer_name'] }},</td>
	</tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;">You have requested to reset your password for below email</td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"><b> Email: </b>{{ $data['customer_email'] }}</td></tr>
	<tr><td height="10"></td></tr>
	<tr><td height="10"></td></tr>

	<tr><td height="20" style="padding:0px 20px;"> Please visit below link to generate your new password.</td></tr>
	<tr><td height="10"></td></tr>

	<tr>
		<td height="20" style="padding:0px 20px;">
			<b><a href="{{ $data['reset_url'] }}">{{ $data['reset_url'] }}</a></b>
		</td>
	</tr>

	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td style="font-size:14px; color:#000000; font-family:Open Sans; padding:0px 20px;">&nbsp;</td></tr>
	<tr><td height="20"></td></tr>
@endsection