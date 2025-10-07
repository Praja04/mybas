<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EMAIL</title>
</head>
<body style="padding: 0;margin:0; font-family: Arial, Helvetica, sans-serif">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;background-color:#F9F9F9;" id="bodyTable">
    <tbody><tr>
        <td align="center" valign="top" style="padding-right:10px;padding-left:10px;" id="bodyCell">
            <!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" style="width:600px;" width="600"><tr><td align="center" valign="top"><![endif]-->


            <!-- Email Wrapper Webview Close //-->

            <!-- Email Wrapper Header Open //-->
            <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperWebview">
                <tbody><tr>
                    <td align="center" valign="top">
                        <!-- Content Table Open // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr>
                                <td align="center" valign="middle" style="padding-top: 40px; padding-bottom: 10px;" class="emailLogo">
                                    <!-- Logo and Link // -->
                                    <p>My BAS Online Notification</p>
                                </td>
                            </tr>
                            </tbody></table>
                        <!-- Content Table Close // -->
                    </td>
                </tr>
                </tbody></table>
            <!-- Email Wrapper Header Close //-->

            <!-- Email Wrapper Body Open // -->
            <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
                <tbody><tr>
                    <td align="center" valign="top">

                        <!-- Table Card Open // -->
                        <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

                            <tbody><tr>
                                <!-- Header Top Border // -->
                                <td height="3" style="background-color: rgb(201, 0, 27); font-size: 1px; line-height: 3px;" class="topBorder">&nbsp;</td>
                            </tr>


                            <tr>
                                <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                                    <!-- Hero Image // -->
                                    <a href="#" target="_blank" style="text-decoration:none;">

                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" valign="top" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;" class="mainTitle">
                                    <!-- Main Title Text // -->
                                    <h2 class="text" style="color:#000000; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
                                        {{ $data['title'] }}
                                    </h2>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" valign="top" style="padding-bottom: 30px; padding-left: 20px; padding-right: 20px;" class="subTitle">
                                    <!-- Sub Title Text // -->
                                    <h4 class="text" style="color:#999999; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
                                        {{ $data['app'] }}
                                    </h4>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">

                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription" style="">
                                        <tbody><tr>
                                            <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                                <!-- Description Text// -->
                                                <p class="text" style="color:#666666; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                                                  
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    <h3>Data Karyawan Masuk Hari Libur Baru Diapprove Hari Ini :</h3>
                                    <table border="3" cellpadding="0" cellspacing="0" width="100%" class="tableDescription" style="">
                                        <thead>
                                            <tr>
                                                <th>SHIFT 1</th>
                                                <th>SHIFT 2</th>
                                                <th>SHIFT 3</th>
                                            </tr>
                                        </thead>
                                    <tbody>
                                        <tr>
                                        <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                            <br>
                                             {{ $data['data_count']['data_tambahan_shift_1'] }} Orang
                                        </td>
                                        <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                            <br>
                                          {{ $data['data_count']['data_tambahan_shift_2'] }} Orang
                                        </td>
                                        <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                            <br>
                                          {{ (int)$data['data_count']['data_tambahan_shift_3'] }} Orang
                                        </td>
                                    </tr>
                                    </tbody></table>
                                    {{-- {{Carbon\Carbon::parse($tanggal)->format('d-M-Y')}}:</h3> --}}
                                    <h3>Jumlah Total Karyawan Masuk Hari Libur Untuk Tanggal {{$tanggal}} :</h3>
                                        <table border="3" cellpadding="0" cellspacing="0" width="100%" class="tableDescription" style="">
                                            <thead>
                                                <tr>
                                                    <th>SHIFT 1</th>
                                                    <th>SHIFT 2</th>
                                                    <th>SHIFT 3</th>
                                                </tr>
                                            </thead>
                                        <tbody>
                                            <tr>
                                            <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                                <br>
                                                 {{ $data['data_count']['data_all_shift_1'] }} Orang
                                            </td>
                                            <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                                <br>
                                              {{ $data['data_count']['data_all_shift_2'] }} Orang
                                            </td>
                                            <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                                <br>
                                              {{ (int)$data['data_count']['data_all_shift_3'] }} Orang
                                            </td>
                                        </tr>
                                        </tbody></table>
                                        

                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButton">
                                        <tbody><tr>
                                            <td align="center" valign="top" style="padding-top:20px;padding-bottom:20px;">

                                                <!-- Button Table // -->
                                                <table align="center" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody><tr>
                                                        <td align="center" class="ctaButton" style="background-color: rgb(252, 53, 63); padding: 12px 35px; border-radius: 50px;">
                                                            <!-- Button Link // -->
                                                            <a class="text" href="http://172.21.5.105/" style="color:#FFFFFF; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block" onMouseOver="this.style.text='#000'" onMouseOut="this.style.color='#FFF'">
                                                                Periksa Sekarang
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    </tbody></table>

                                            </td>
                                        </tr>
                                        </tbody></table>

                                </td>
                            </tr>

                            <tr>
                                <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                            </tr>

                            <tr><td align="center" valign="middle" style="padding-bottom:40px" class="emailRegards">
                                    <!-- Image and Link // -->
                                    <i style="color: #999">
                                        PT. Bumi Alam Segar Team
                                    </i>
                                </td>
                            </tr>
                            </tbody></table>
                        <!-- Table Card Close// -->



                    </td>
                </tr>
                </tbody></table>
            <!-- Email Wrapper Body Close // -->

            <!-- Email Wrapper Footer Open // -->
            <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperFooter">
                <tbody><tr>
                    <td align="center" valign="top">
                        <!-- Content Table Open// -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="footer">
                            <tbody>

                            <tr>
                                <td align="center" valign="top" style="padding-top:10px;padding-bottom:5px;padding-left:10px;padding-right:10px;" class="brandInfo">
                                    <!-- Brand Information // -->
                                    <p class="text" style="color:#777777; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:12px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:20px; text-transform:none; text-align:center; padding:0; margin:0;">© PT. Bumi Alam Segar 2022
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" valign="top" style="padding-top:0px;padding-bottom:20px;padding-left:10px;padding-right:10px;" class="footerLinks">
                                    <!-- Use Full Links (Privacy Policy)// -->
                                    <p class="text" style="color:#777777; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:12px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:20px; text-transform:none; text-align:center; padding:0; margin:0;">
                                        <a href="#" style="color:#777777;text-decoration:underline;" target="_blank"> BAS APP </a>&nbsp;|&nbsp;<a href="#" style="color:#777777;text-decoration:underline;" target="_blank"> ITE - 4003 </a>&nbsp;
                                    </p>
                                </td>
                            </tr>


                            <!-- Space -->
                            <tr>
                                <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                            </tr>
                            </tbody></table>
                        <!-- Content Table Close// -->
                    </td>
                </tr>

                <!-- Space -->
                <tr>
                    <td height="40" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>
                </tbody></table>
            <!-- Email Wrapper Footer Close // -->

            <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
        </td>
    </tr>
    </tbody></table>
</body>
</html>
