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


</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <div class="container mt-5">
        <div class="container">
                <h4>สถานีวิทยุพิทักษ์สันติราษฎร์ สวพ. FM91<br>กองตำรวจสื่อสาร สำนักงานตำรวจแห่งชาติ</h4>
                <hr>
                <p>82 ซอยข้างกรมพัฒนาที่ดิน ถนนพหลโยธิน แขวงลาดยาว เขตจตุจักร, 10900 กรุงเทพฯ</p>
                <hr>
                <p>1644 โทรฟรีทั่วประเทศไทย ตลอด 24 ชั่วโมง</p>
        </div>
        <div class="container mb-3">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3874.015762487486!2d100.57026431462224!3d13.838091990291648!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29ce2fa624429%3A0x5f1ac473547c4e1e!2z4Liq4Lin4LieLkZNOTE!5e0!3m2!1sth!2sth!4v1652239455629!5m2!1sth!2sth" width="100%" position="center" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

            <!-- <div class="col-md-2"></div>
            <div class="col-md-8">
                <h4 style="color: #555555;"><u>แบบฟอร์มติดต่อ</u></h4>
                <form method="POST" action="">
                    <?php //echo $secret 
                    ?>
                    <div class="row mt-3">
                        <div class="col-md-3 col-12">
                            <label for="name" class="form-label">ชื่อ</label>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="text" class="form-control" name="name" placeholder="">
                            <span class="text-danger" data-name="name">*</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3 col-12">
                            <label for="email" class="form-label">อีเมล์</label>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="email" class="form-control" name="email" placeholder="">
                            <span class="text-danger" data-name="email">*</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3 col-12">
                            <label for="content" class="form-label">ข้อความ</label>
                        </div>
                        <div class="col-md-9 col-12" style="text-align: center;">
                            
                            <textarea class="form-control mb-3" name="content" rows="5"></textarea>
                            
                            <button type="submit" class="btn btn-warning mt-3 contact_submit" style="padding: 8px 40px;">ส่งถึงเรา</button>
                            <!-- <a href="" style="padding: 5px 40px;border: 1px solid #FDD400;background-color: #FDD400;border-radius: 5px;">ส่งถึงเรา</a> -->
        </div>
    </div>
   

    <style>
        .mainbody {
            padding: 40px;
            border: 1px solid;
            border-radius: 10px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        h4 {
            color: #333333;
        }
    </style>


    <!--footer-->
    
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('page/assets/js/jquery.form.js') ?>"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {


            $('.contact_submit').click(function() {

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
                            location.reload();
                        });

                    }
                });
            });
        });
    </script>


<?php echo view('front/components/footer') ?></body>

</html>