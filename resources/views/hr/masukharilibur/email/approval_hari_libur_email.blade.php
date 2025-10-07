<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EMAIL</title>
</head>

<body style="padding: 0; margin: 0; font-family: Arial, Helvetica, sans-serif">
    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="table-layout: fixed; background-color: #F9F9F9;" id="bodyTable">
        <tbody>
            <tr>
                <td align="center" valign="top" style="padding: 20px;" id="bodyCell">
                    <!-- Email Wrapper Body Open // -->
                    <table border="0" cellpadding="0" cellspacing="0" style="max-width: 600px;" width="100%"
                        class="wrapperBody">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Table Card Open // -->
                                    <table border="0" cellpadding="0" cellspacing="0"
                                        style="background-color: #FFFFFF; border-color: #E5E5E5; border-style: solid; border-width: 0 1px 1px 1px; border-radius: 10px;"
                                        width="100%" class="tableCard">
                                        <tbody>
                                            <tr>
                                                <!-- Header Top Border // -->
                                                <td height="3"
                                                    style="background-color: rgb(201, 0, 27); font-size: 1px; line-height: 3px;"
                                                    class="topBorder">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"
                                                    style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;"
                                                    class="mainTitle">
                                                    <!-- Main Title Text // -->
                                                    <h2 class="text"
                                                        style="color: #000000; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 28px; font-weight: 500; font-style: normal; letter-spacing: normal; line-height: 36px; text-transform: none; text-align: center; padding: 0; margin: 0">
                                                        Email Reminder
                                                    </h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"
                                                    style="padding-left: 20px; padding-right: 20px;"
                                                    class="containtTable ui-sortable">
                                                    <!-- Description Text // -->
                                                    <p class="text"
                                                        style="color: #666666; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; font-style: normal; letter-spacing: normal; line-height: 22px; text-transform: none; text-align: center; padding: 0; margin: 0">
                                                        Anda memiliki notifikasi upload data baru dari
                                                        {{ $data->created_by }}. Silakan cek akun Anda
                                                        untuk detailnya.
                                                    </p>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                                        class="tableButton">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" valign="top"
                                                                    style="padding-top: 20px; padding-bottom: 20px;">
                                                                    <!-- Button Table // -->
                                                                    <table align="center" border="0" cellpadding="0"
                                                                        cellspacing="0">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" class="ctaButton"
                                                                                    style="background-color: rgb(252, 53, 63); padding: 12px 35px; border-radius: 50px;">
                                                                                    <!-- Button Link // -->
                                                                                    <a class="text"
                                                                                        href="http://172.21.5.105/masukharilibur/approver"
                                                                                        style="color: #FFFFFF; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 600; font-style: normal; letter-spacing: 1px; line-height: 20px; text-transform: uppercase; text-decoration: none; display: block"
                                                                                        onMouseOver="this.style.color='#000'"
                                                                                        onMouseOut="this.style.color='#FFF'">
                                                                                        Cek Akun
                                                                                    </a>
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
                                            <tr>
                                                <td height="20" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- Table Card Close // -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Email Wrapper Body Close // -->
                </td>
            </tr>
        </tbody>
    </table>
    <p style="font-size: 12px; color: #999; text-align: center; margin-top: 20px;">&copy; PT. Bumi Alam Segar Team</p>
</body>

</html>
