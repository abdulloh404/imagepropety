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

	<!-- Title -->
	<title>สวพ.FM91</title>

	<!-- Favicon -->
	<link rel="icon" href="admin/assets/img/brand/favicon.png" type="image/x-icon" />

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

		<div class="container-fluid">
			<div class="row no-gutter">
				<!-- The image half -->
				<div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-warning-transparent">
					<div class="row wd-100p mx-auto text-center">
						<div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
							<img src="admin/assets/img/website-g0736a7036_1280.png" style="border-radius: 20px;" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
						</div>
					</div>
				</div>
				<!-- The content half -->
				<div class="col-md-6 col-lg-6 col-xl-5 bg-white">
					<div class="login d-flex align-items-center py-2">
						<!-- Demo content-->
						<div class="container p-0">
							<div class="row">
								<div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
									<div class="card-sigin">
										<div class="card-sigin">
											<div class="main-signup-header">
												
												<div class="text-center">
														<img src="page/assets/img/logofooter.jpg" alt="" style="border-radius: 10px;">
													
														<h5 class="fw-semibold mt-4 mb-2">Please sign in to continue.</h5>
													</div>
												<form method="post" action="">
													<?php echo $secret ?>
													<div class="form-group">
														<label>Email</label>
														<input class="form-control" placeholder="Enter your email" type="email" name="email">
													</div>
													<div class="form-group">
														<label>Password</label>
														<input class="form-control" placeholder="Enter your password" type="password" name="password">
													</div>
													<div class="row">
														<div class="col-6"><button class="Login_submit btn btn-warning btn-block">Sign In</button></div>
														<div class="col-6"><input type="reset" class="btn btn-danger btn-block" value="Reset"></input></div>
													</div>

												</form>
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
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><!-- End -->
					</div>
				</div><!-- End -->
			</div>
		</div>
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