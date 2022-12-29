<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.2.min.js"></script>
    

    <link href="page/assets/css/icons.css" rel="stylesheet">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
        }

        p {
            font-weight: 400;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .container {
            width: 650px;
            padding-right: var(--bs-gutter-x, .75rem);
            padding-left: var(--bs-gutter-x, .75rem);
            margin-right: auto;
            margin-left: auto;
        }

        /* .m-2 {
            margin: .5rem !important;
        } */

        /* table,th,td{
            border: 1px solid black;
        } */

        .txtHead {
            font-size: 20px;
        }

        .txt {
            font-size: 15px;
        }

        .colright {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .colleft {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .controll-main {
            background-color: #E7E7E7;
            border-radius: 5px;
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 40px;
            border: 2px solid #afafaf;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(var(--bs-gutter-y) * -1);
            margin-right: calc(var(--bs-gutter-x) * -.5);
            margin-left: calc(var(--bs-gutter-x) * -.5);
        }

        .col-md-6 {
            flex: 0 0 auto;
            width: 50%;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .flex-container {
            display: flex;
            justify-content: space-around;
        }

        a {
            text-decoration: none;
            /* font-size: 16px; */
            color: #555555;
        }

        h3 {
            font-size: 25px;
            color: #333333;
        }

        @media only screen and (max-width: 575.98px) {
            .flex-container {
                display: flex;
                flex-direction: column;
                align-items: start;
                padding: 20px;
            }
        }

        li {
            list-style-type: none;
        }

        /* th,td{
            border: 1px solid black;
        } */
        .tdStyle {
            text-align: center;
        }


        .colorinput-color {
            border-radius: 99px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        /* table td th {
            border: 1px solid black;
        } */
    </style>
</head>

<body>

    <div class="container" style="text-align: center; margin-top: 20px;">
        <div><img src="<?php echo base_url('front/assets/img/logo.jpg')?>" alt="logo" style="border-radius: 5px"></div>
    </div>

    <?php echo view($viewName); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://cdn.socket.io/4.5.0/socket.io.js"></script>
    <br/>
    <!-- <footer class="container">
        <table style="width: 100%;">
            <tr>
                <th colspan="4" style="text-align: left;">FOLLOW US</th>
            </tr>
            <tr>
                <td class="tdStyle"><a href="https://www.facebook.com/GoldCityFoottechThailand/">
                        <img src="https://goldcity.digital/page/assets/img/icon/facebook-square.png" width="28px" height="28px" alt="">
                    </a>
                </td>
                <td class="tdStyle"><a href="https://tiktok.com/"><img src="https://goldcity.digital/page/assets/img/icon/tiktok.png" width="28px" height="28px" alt=""></a>
                </td>
                <td class="tdStyle"><a href="https://www.instagram.com/">
                        <img src="https://goldcity.digital/page/assets/img/icon/instagram-brands.png" width="28px" height="28px" alt="">
                    </a>
                </td>
                <td class="tdStyle"><a href="https://youtube.com/">
                        <img src="https://goldcity.digital/page/assets/img/icon/youtube-brands.png" width="28px" height="28px" alt="">
                    </a>
                </td>
            </tr>
        </table>

        <div class="container" style="margin-top: 40px;">
            <table style="width: 100%;">
                <tr>
                    <th style="text-align: left;">CUSTOMER SERVICE</th>
                    <th style="text-align: left;">SHOP AT GOLD CITY</th>
                    <th style="text-align: left;">ABOUT US</th>
                    <th style="text-align: left;">LEGAL</th>
                </tr>
                <tr>
                    <td><a href="<?php echo front_link(12) ?>">
                            <p style="font-size: 12px;">Contact Us</p>
                        </a>
                    </td>
                    <td><a href="<?php echo front_link(11) ?>">
                            <p style="font-size: 12px;">Click & Collect</p>
                        </a>
                    </td>
                    <td><a href="<?php echo front_link(1) ?>">
                            <p style="font-size: 12px;">Who we are</p>
                        </a>
                    </td>
                    <td><a href="<?php echo front_link(25) ?>">
                            <p style="font-size: 12px;">Privacy & Cookies Policy</p>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="<?php echo front_link(28) ?>">
                            <p style="font-size: 12px;">Return & Exchange</p>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo front_link(30) ?>">
                            <p style="font-size: 12px;">Store Locations</p>
                        </a>
                    </td>

                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <a href="<?php echo front_link(21) ?>">
                            <p style="font-size: 12px;">Membership</p>
                        </a>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </footer> -->
</body>

</html>