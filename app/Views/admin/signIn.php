<!DOCTYPE html>
<html lang="en">

<head>
	<base href="<?php echo base_url() ?>" />

	<meta charset="UTF-8">
	<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
	<meta name="Author" content="Spruko Technologies Private Limited">
	<meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4" />

	<title>ImageProperty</title>

    <link rel="icon" type="image/x-icon" href="page/admin-assets/img/logo.png">

	<!-- Icons css -->
	<link href="admin/assets/css/icons.css" rel="stylesheet">

	<!-- Bootstrap css -->
	<link href="admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!--  Right-sidemenu css -->
	<link href="admin/assets/plugins/sidebar/sidebar.css" rel="stylesheet">

	<!--  Custom Scroll bar-->
	<link href="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" />

	<!--- Style css --->
	<link href="admin/assets/css/style.css" rel="stylesheet">
	<link href="admin/assets/css/boxed.css" rel="stylesheet">
	<link href="admin/assets/css/dark-boxed.css" rel="stylesheet">

	<!--- Dark-mode css --->
	<link href="admin/assets/css/style-dark.css" rel="stylesheet">

	<!---Skinmodes css-->
	<link href="admin/assets/css/skin-modes.css" rel="stylesheet" />

	<!--- Animations css-->
	<link href="admin/assets/css/animate.css" rel="stylesheet">
	<style>
		.text-warning {
			color: #FDD400 !important;
		}

		.btn-warning {
			background-color: #FDD400 !important;
		}
	</style>

<style>
        body {
            background-color: #FFFFFF;
        }

        .signin-form {
            background: #FFFFFF;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.25);
            border-radius: 5px;
            padding: 60px 10px;
            margin-top: 50px;
        }

        .signin-form>h1 {
            font-weight: 600;
            line-height: 36px;
            color: #0967A8;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-input {
            display: flex;
            flex-direction: column;
        }

        .form-input>label {
            font-weight: 500;
            line-height: 15px;
            color: rgba(0, 0, 0, 0.73);
            margin-left: 15px;
            margin-top: 20px;
        }

        .form-input>input {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.7);
            border-radius: 5px;
            width: 100%;
            height: 45px;
            padding-left: 20px;
            margin-top: 5px;
        }

        .form-checkRemember {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5px;
        }

        .button-group {
            display: flex;
            margin-top: 50px;
        }

        .button-group>button[type=submit] {
            background: #007299;
            color: #ffffff;
            border: 2px solid #007299;
            border-radius: 5px;
            width: 50%;
            padding: 10px 15px;
            margin-right: 5px;
        }

        .button-group>button[type=submit]:hover {
            background: #ffffff;
            color: #007299;
            transition: 0.5s;
        }

        .button-group>button[type=reset] {
            background: #ffffff;
            color: #007299;
            border: 2px solid #007299;
            border-radius: 5px;
            width: 50%;
            padding: 10px 15px;
            margin-left: 5px;
        }

        .button-group>button[type=reset]:hover {
            background: #007299;
            color: #ffffff;
            transition: 0.5s;
        }

        .signup-link {
            text-decoration: none;
            color: #007299;
        }

        .signup-link:hover {
            color: #000000;
        }
    </style>

	<script src="admin/assets/plugins/jquery/jquery.min.js"></script>
</head>

<body class="error-page1 main-body bg-light text-dark">

	<!-- Loader -->
	<div id="global-loader">
		<img src="admin/assets/img/loader.svg" class="loader-img" alt="Loader">
	</div>
	<!-- /Loader -->

	<!-- Page -->
	<div class="page">

	<div class="container">
            <div class="row mt-4 mb-4">
                <div class="col-xl-4 col-lg-3 col-md-12 col-12"></div>
                <div class="col-xl-4 col-lg-6 col-md-12 col-12">
                    <form method="post" action="">
                        <?php echo $secret ?>
                        <div class="signin-form">
                            <h1>เข้าสู่ระบบ</h1>
                            <div class="form-input">
                                <label for="">อีเมล</label>
                                <input type="email" name="email">
                            </div>
                            <div class="form-input">
                                <label for="">รหัสผ่าน</label>
                                <input type="password" name="password">
                            </div>
                            <div class="form-checkRemember">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="">
                                    <label class="form-check-label" for="">
                                        จดจำฉันไว้ในระบบ
                                    </label>
                                </div>
                            </div>
                            <div class="button-group">
                                <!-- <button type="submit"><a href="<?php //echo front_link(14) ?>">เข้าสู่ระบบ</a></button> -->
                                <button type="submit" class="Login_submit">เข้าสู่ระบบ</button>
                                <button type="reset">เคลียร์</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xl-4 col-lg-3 col-md-12 col-12"></div>
            </div>
        </div>
		<script type="text/javascript" src="page/assets/js/jquery.form.js"></script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script>
			$(function() {


				$('.Login_submit').click(function() {

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

							if (data.token_val)
								$('[name="<?php echo get_token('name') ?>"]').val(data.token_val);

							if (data.success == 0) {


								Swal.fire({
									title: data.message,
									text: '',
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
		<div class="main-footer ht-40" style="position: fixed; bottom: 0; width: 100%; text-align: center;">
			<div class="container-fluid pd-t-0-f ht-100p" style="display: flex; justify-content: center">
				<p style="margin-top: 20px;">© <span class="main-color">FM91BKK</span> website -All Rights Reserve 2017</p>
			</div>
		</div>
	</div>
	<!-- End Page -->
	<!-- 


 -->




	<!-- JQuery min js -->


	<!-- Bootstrap Bundle js -->
	<script src="admin/assets/plugins/bootstrap/js/popper.min.js"></script>
	<script src="admin/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- Ionicons js -->
	<script src="admin/assets/plugins/ionicons/ionicons.js"></script>

	<!-- Moment js -->
	<script src="admin/assets/plugins/moment/moment.js"></script>

	<!-- P-scroll js -->
	<script src="admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

	<!-- eva-icons js -->
	<script src="admin/assets/js/eva-icons.min.js"></script>

	<!-- Rating js-->
	<script src="admin/assets/plugins/rating/jquery.rating-stars.js"></script>
	<script src="admin/assets/plugins/rating/jquery.barrating.js"></script>

	<!-- Custom Scroll bar Js-->
	<script src="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

	<!-- custom js -->
	<script src="admin/assets/js/custom.js"></script>

</body>

</html>