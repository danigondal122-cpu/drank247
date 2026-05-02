

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
		@stack('exstraStyle')
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
												<td height="73" style="background: #FFFFFF; padding-left: 20px;">
													<a target="_blank" href="">
														<img src="{{ asset('img/247-Drank-Logo.png') }}" style="width: 170px; height: 70px;">
													</a>
												</td>
											</tr>
											<tr>
												<td height="{{ isset($data['title']) ? '21' : '2' }}" style="background:#e91362;font-size:20px; font-weight:bold; color:#ffffff; font-family:Open Sans; padding:0px 20px;">
													@isset($data['title'])
														<strong>{{ $data['title'] }}</strong>
													@endisset
												</td>
											</tr>
											<tr><td height="10"></td></tr>
											@yield('content')
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>