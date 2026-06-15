<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>       

        <div class="mb-4 text-sm text-gray-600">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tbody>
            <tr>
                <td align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td width="100%" cellpadding="0" cellspacing="0">
                                    <table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';background-color:#ffffff;border-color:#e8e5ef;border-radius:2px;border-width:1px;margin:0 auto;padding:0;width:570px">
                                        <tbody>
                                            <tr>
                                                <td>
												
                                                    <h1>Hello!</h1>
                                                    <h3>{{ $details['title'] }}</h3>
                                                    <p>{{ $details['body'] }}</p>
													<p><a href="<?php echo URL::to('/')?>">Click here</p>
                                                    <p>Regards,<br>
                                                        Classified
                                                    </p>                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                        <tbody>
                                            <tr>
                                                <td align="center">
                                                    <p>© 2021 Classified. All rights reserved.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
</table>          
        </div>
    </x-jet-authentication-card>
</x-guest-layout>

