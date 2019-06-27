@include('emails.includes.header')
<table border="0" cellpadding="0" cellspacing="0" style="margin:0; padding:30px; border-radius: 30px;" width="100%"
       bgcolor="#ffffff">
    <tr>
        <td align="center">
            <span style="color: #333333; font: 14px Arial, sans-serif; line-height: 18px; -webkit-text-size-adjust:none; display: block;margin:0 0 30px;font-weight: bold;">THANKS FOR <br>YOUR REGISTRATION</span>
        </td>
    </tr>
    <tr>
        <td>
            <span style="color: #333333; font: 14px Arial, sans-serif; line-height: 18px; -webkit-text-size-adjust:none; display: block;margin: 0 0 20px;">Dear {{ $name }}
                </span>
            <span style="color: #333333; font: 14px Arial, sans-serif; line-height: 18px; -webkit-text-size-adjust:none; display: block;margin: 0 0 20px;">Thank you for your registration. <br>Your account has been succesfully created.</span>
            <span style="color: #333333; font: 14px Arial, sans-serif; line-height: 18px; -webkit-text-size-adjust:none; display: block;margin: 0 0 20px;"><strong>Please follow the link for confirm your email: <a href="{{ route('register_confirm', ['token' => $token]) }}">click here</a></strong>.</span>
        </td>
    </tr>
    <tr>
        <td>
            <span style="color: #333333; font: 14px Arial, sans-serif; line-height: 18px; -webkit-text-size-adjust:none; display: block;margin: 0 0 20px;">Thank you!</span>
            <span style="color: #333333; font: 14px Arial, sans-serif; line-height: 18px; -webkit-text-size-adjust:none; display: block;margin: 0;">Always in your heart, <br>hydra creator</span>
        </td>
    </tr>
</table>
@include('emails.includes.footer')