<table width="624" style="max-width:624px;" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<!doctype html>
			<html>
            
			<head>
				<meta charset="utf-8">
				<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet"> </head>

			<body style="margin:0px; padding:0px; font-family: Lato, sans-serif;">
				<table width="624" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
					<tr style="border-bottom: 5px solid #fd890c;">
						<td width="312" height="90" colspan="2" style="padding-left:20px;padding-right:0px;padding-top:0px; padding-bottom:0px;background:#fcf3e8; -moz-border-top-left-radius: 10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;-moz-border-top-right-radius: 10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; text-align:center;">
                        <img src="{{url('/')}}/images/trafikLogo.png" width="80" height="70" alt="Trafiksol" style="display:inline-block;">                        
						</td>
					</tr>
					<tr style="background:#f7f7f7;">
						<td colspan="2" style="padding-left:15px;padding-right:15px;padding-top:25px; padding-bottom:25px; font-size:14px; line-height:24px; color:#242424;">
							<br>
							<br> @if(isset($name)) Dear, {{$name}} @endif
							<br> Below are the notification send my admin
							<br>
							<br> <b>Message: </b>{{$message1}}
							<br>							
					</tr>					
				</table>
			</body>

			</html>
		</td>
	</tr>
</table>
