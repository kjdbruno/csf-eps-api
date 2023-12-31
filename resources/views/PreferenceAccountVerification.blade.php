<!DOCTYPE html>
<html>
<head>
    <title>Account Verification</title>
</head>
<body>
    <div style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
        <!-- HIDDEN PREHEADER TEXT -->
        <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;"> We're thrilled to have you here! Get ready to dive into your new account. </div>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <!-- LOGO -->
            <tr>
                <td bgcolor="#224488" align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#224488" align="center" style="padding: 0px 10px 0px 10px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                                <h3 style="font-size: 24px; font-weight: 400; margin: 2;">Welcome back to ePS.</h3>
                                <h5 style="font-size: 24px; font-weight: 400; margin: 0;">The Community Engagement Platform</h5>
                                <h5 style="font-size: 24px; font-weight: 400; margin: 0;">City of San Fernando, La Union</h5>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                <p style="margin: 0;">Hello {{$name}},</p>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                <p style="margin: 0;">Your account has been verified. Please check the details below:</p>
                                <p style="margin: 15px 0px 0px 0px;"><span style='font-weight: bold;'>NAME: </span><span>{{ $name }}<span></p>
                                <p style="margin: 0px 0px 0px 0px;"><span style='font-weight: bold;'>EMPLOYEE ID: </span><span>{{ $employeeID }}<span></p>
                                <p style="margin: 0px 0px 0px 0px;"><span style='font-weight: bold;'>ROLE: </span><span>{{ $role }}<span></p>
                                <p style="margin: 0px 0px 0px 0px;"><span style='font-weight: bold;'>OFFICE: </span><span>{{ $office }}<span></p>
                                <p style="margin: 0px 0px 0px 0px;"><span style='font-weight: bold;'>POSITION: </span><span>{{ $position }}<span></p>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 0px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                <p style="margin: 0;">Got a question? You can reply to this email or contact us at the City's Facebook Page.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td bgcolor="#FFFFFF" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <img src="https://www.sanfernandocity.gov.ph/csflu_website/wp-content/uploads/2020/04/San-Fernando-Seal_1-500x500.jpg" width="125" height="120" style="display: block; border: 0px;" />
                                <h2 style="font-size: 20px; font-weight: 400; color: #111111; margin: 0;">City Government of San Fernando La Union</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </div>
</body>
</html>
