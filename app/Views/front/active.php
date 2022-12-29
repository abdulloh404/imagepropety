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

</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <?php
    $request = service('request');
    $result = $request->getVar();
    $user_code = $result['code'];
    $encrypter = \Config\Services::encrypter();
    $ids_users = $encrypter->decrypt($result['code']);
        //var_dump($ids_users);exit;

    ?>

    <div class="container ">
        <!-- Content -->
        <div class="row bg-row" style="height: 700px;">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card"  style="margin-top: 100px;">
                    <div class="card-body" style="margin: 10px;">
                        <!-- <h2 style="text-align: center; color: black;">GOLD CITY</h2> -->
                        <div style="text-align: center; margin-top:20px;">
                            <img src="<?php echo base_url('page/assets/img/logofooter.jpg') ?>" style="width: 150px;">
                        </div>
                        <form method="POST" action="<?php echo front_link($id,'activateUser',array(),false)?>">
                            <?php echo $secret; ?>
                            <input type="hidden" class="form-control" name="activate_code" value="<?php echo $ids_users ?>"><br>
                            <h3 class="goldcity-header" style="text-align: center;">ขอขอบคุณที่สมัครสมาชิก FM91 </h3>
                            <h4 class="goldcity-header" style="text-align: center;">คลิกปุ่มด้านล่างนี้เพื่อเริ่มต้นการใช้งาน</h4>
                            <button type="submit" class="btn btn-warning btn-block Active_submit mt-2" style="padding: 10px; width: 100%;">Activate</button>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
        <!--End Content -->
    </div>

    <!--footer-->
    
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!--Map-->   
    <script src="front/assets/js/map.js"></script>
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo base_url('page/assets/js/jquery.form.js') ?>"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		$(function() {


			$('.Active_submit').click(function() {

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
								title: data.message,
								text: '',
								icon: 'error',
								confirmButtonText: 'ตกลง'
							}).then(function() {
								
							});

							
						}

						Swal.fire({
							title: data.message,
							text: '',
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