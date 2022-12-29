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
            margin-top: 60px;
            margin-bottom: 60px;
        }
    </style>
</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->
    <br />
    <div class="container mainbody">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-md-8 load-form">

                <div class="card">                    
                    <div class="card-body">
                        <h1 style="text-align: center; margin: 20px">CREATE NEW PASSWORD</h1>
                        <hr style="border: 3px black solid;"><br>
                        <form action="" method="post" id="form_login" class="formSubmit">
                            <?php echo $secret ?>
                            <h3>ตั้งค่ารหัสผ่านใหม่</h3>
                            <br />
                            <input type="hidden" name="memberPass" value="1"></input>
                            <div id="email_input">
                                <label for="otp" class="form-label">รหัส OTP <span class="text-danger" data-name="otp">*</span></label>
                                <input type="text" name="otp" class="form-control" autocomplete="off">
                                <br/>
                                <label for="password" class="form-label">รหัสผ่านใหม่ <span class="text-danger" data-name="newPassword">*</span></label>
                                <input type="password" name="newPassword" class="form-control" autocomplete="off">
                                <br/>
                                <label for="password" class="form-label">ยืนยันรหัสผ่านใหม่* <span class="text-danger" data-name="comfirmPassword">*</span></label>
                                <input type="password" name="comfirmPassword" class="form-control" autocomplete="off">
                            </div>

                            <br />
                            <button type="submit" class="btn btn-warning Reset_submit" style="width: 100%;">Submit</button>
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
        $(function() {


            $('.Reset_submit').click(function() {

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
                            // $('.load-form').html(data.html);
                        });





                    }
                });
            });
        });
    </script>



<?php echo view('front/components/footer') ?></body>

</html>