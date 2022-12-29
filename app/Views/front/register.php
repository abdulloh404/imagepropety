<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo titleWeb()?></title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">
    <style>
        .nav-login-tabs {
            display: flex;
            /* border-bottom: 1px solid #D5D5D5; */
        }

        .nav-login-item {
            width: 50%;
            display: inline-block;
            text-align: center;
            border-left: 1px solid transparent;
            border-right: 1px solid transparent;
            border-top: 1px solid transparent;
            border-bottom: 1px solid #D5D5D5;
            font-size: 22px;
            font-weight: bold;
            color: #999999;
            padding: 20px;
            cursor: pointer;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .login_contain {
            border-left: 1px solid #D5D5D5;
            border-right: 1px solid #D5D5D5;
            border-bottom: 1px solid #D5D5D5;
            padding: 30px 0;
        }

        .login_contain_wrapper {
            width: 75%;
            margin: 0 auto;
        }

        #membership .loginContainer {
            width: 100%;
            max-width: 720px;
            margin: 50px auto 0;
        }

        .active {
            border-left: 1px solid #D5D5D5;
            border-right: 1px solid #D5D5D5;
            border-top: 1px solid #D5D5D5;
            border-bottom: 1px solid #FFF;
            color: #000000;
            border-radius: 5px 5px 0 0;
        }

        .forgot {
            margin-top: 15px;
        }

        .mainbody {
            margin-top: 100px;
            margin-bottom: 100px;
        }
    </style>
</head>

<body>

    <?php
    if (isset($_REQUEST['fmaction'])) {
        $display = 1;
    } else {
        $display = 0;
    }
    ?>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->
    <br />
    <div class="container mainbody">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-md-8 load-form">


                <div class="nav-login-tabs">
                    <div class="nav-login-item active" id="tab-login" onclick="openTab('Sigin')">LOGIN</div>
                    <div class="nav-login-item" id="tab-register" onclick="openTab('Signup')">REGISTER</div>
                </div>
                <div class="login_contain">
                    <div class="login_contain_wrapper">
                        <form action="<?php echo front_link($id, 'signin', array(), false) ?>" method="post" id="form_login" class="formSubmit">
                            <?php echo $secret ?>
                            <div id="email_input">
                                <label for="email" class="form-label">Email <span class="text-danger" data-name="email">*</span></label>
                                <input type="email" name="email" class="form-control" autocomplete="off">

                            </div>
                            <div id="password_input">
                                <label for="password" class="form-label">Password <span class="text-danger" data-name="password">*</span></label>
                                <input type="password" name="password" class="form-control">

                            </div>
                            <div class="forgot">
                                <a href="<?php echo front_link(14) ?>">Forgot Password</a>
                            </div>




                            <br />
                            <button type="submit" class="btn btn-warning SignIn_submit" style="width: 100%;">SignIn</button>
                        </form>


                        <form action="<?php echo front_link($id, 'register') ?>" method="get" id="form_register" class="formSubmit" style="display: none;">
                            <?php echo $secret ?>
                            <div id="email_input">
                                <label for="email" class="form-label">Email <span class="text-danger" data-name="emailRegis">*</span></label>
                                <input type="email" name="emailRegis" class="form-control" autocomplete="off">

                            </div>
                            <div id="password_input">
                                <label for="password" class="form-label">Password <span class="text-danger" data-name="passwordRegis">*</span></label>
                                <input type="password" name="passwordRegis" class="form-control">

                            </div>
                            <div id="password_input">
                                <label for="register_repassword" class="form-label">Confirm password <span class="text-danger" data-name="repasswordRegis">*</span></label>
                                <input type="password" name="repasswordRegis" class="form-control">
                            </div>
                            <br />
                            <button type="submit" class="btn btn-warning Signup_submit" style="width: 100%;">Register</button>
                        </form>

                    </div>



                </div>

                <br>


            </div>
            <div class="col-2"></div>
        </div>
        <div class="mb-1">
        </div>

    </div>





    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('page/assets/js/jquery.form.js') ?>"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* #footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        } */
    </style>
    <!--footer-->
    <div id="footer">
        

    </div>


    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>


    <script>
        var regisVal = '<?php echo $display ?>';
        

        if (regisVal == 1) {
            $('.nav-login-item').removeClass('active')
            $('#form_login').hide();
            $('#form_register').show();
            $('#tab-register').addClass('active')
        } else {
            $("#tab-login").click(function() {
                $('.nav-login-item').removeClass('active')
                $('#form_login').show();
                $('#form_register').hide();
                $(this).addClass('active')
            })

            $("#tab-register").click(function() {
                $('.nav-login-item').removeClass('active')
                $('#form_login').hide();
                $('#form_register').show();
                $(this).addClass('active')
            })
        }


        $(function() {


            $('.Signup_submit').click(function() {

                myForm = $(this).parents('form');


                var completed = '0%';

                $(myForm).ajaxForm({

                    beforeSubmit: function(data, form, options) {

                        var data = {};

                        options["url"] = myForm.attr('action');
                    },
                    complete: function(response) {

                        protect = 0;

                        data = $.parseJSON(response.responseText);

                        //	$('.text-danger').html('<i class="fa fa-check" style="color: green;"></i>');

                        if (data.success == 0) {


                            Swal.fire({
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            }).then(function() {

                                for (x in data.field) {

                                    $('.text-danger[data-name="' + x + '"]').html(data.field[x]);
                                }

                            });

                            return false;
                        }

                        Swal.fire({
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(function() {
                            $('.load-form').html(data.html);
                        });





                    }
                });
            });

            $('.SignIn_submit').click(function() {

                myForm = $(this).parents('form');


                var completed = '0%';

                $(myForm).ajaxForm({

                    beforeSubmit: function(data, form, options) {

                        var data = {};

                        options["url"] = myForm.attr('action');
                    },
                    complete: function(response) {

                        protect = 0;

                        data = $.parseJSON(response.responseText);

                        //	$('.text-danger').html('<i class="fa fa-check" style="color: green;"></i>');

                        if (data.success == 0) {


                            Swal.fire({
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            }).then(function() {

                                for (x in data.field) {

                                    $('.text-danger[data-name="' + x + '"]').html(data.field[x]);
                                }

                            });

                            return false;
                        }

                        Swal.fire({
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(function() {
                            window.location = data.redirect;
                        });





                    }
                });
            });

        });
    </script>

<?php echo view('front/components/footer') ?></body>

</html>