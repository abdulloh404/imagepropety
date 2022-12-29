<!DOCTYPE html>
<html lang="en">

<head>
	<base href="<?php echo base_url() ?>" />
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>สวพ.FM91</title>

	<!-- bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

	<!-- favicon -->
	<link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

	<!-- font -->
	<link rel="stylesheet" href="front/assets/style/font.css">

	<!-- Internal Select2 css -->
	<link href="admin/assets/plugins/select2/css/select2.min.css" rel="stylesheet">

	<link rel="stylesheet" href="front/assets/style/style.css">
	<link rel="stylesheet" href="front/asset/css/jquery-ui.css">
	<link href="admin/assets/css/skin-modes.css" rel="stylesheet" />
	<!-- <link href="admin/assets/css/style.css" rel="stylesheet"> -->
	<!-- <link href="admin/assets/css/style-dark.css" rel="stylesheet"> -->
	<!-- <link href="admin/assets/css/boxed.css" rel="stylesheet"> -->
	<!-- <link href="admin/assets/css/dark-boxed.css" rel="stylesheet">
	<link href="admin/assets/css/animate.css" rel="stylesheet"> -->

	<script src="https://kit.fontawesome.com/c5c4fee514.js" crossorigin="anonymous"></script>

	<link rel="stylesheet" href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
	<style>
		.ckbox {
			font-weight: normal;
			position: relative;
			display: block;
			line-height: 1;
			margin-bottom: 0;
		}

		.ckbox span {
			padding-left: 15px;
		}

		.ckbox span:empty {
			float: left;
		}

		.ckbox span:before,
		.ckbox span:after {
			line-height: 18px;
			position: absolute;
		}

		.ckbox span:before {
			content: '';
			width: 16px;
			height: 16px;
			background-color: #fff;
			border: 1px solid #949eb7;
			top: 1px;
			left: 0;
		}

		.ckbox span:after {
			top: 1px;
			left: 0;
			width: 16px;
			height: 16px;
			content: '';
			background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3E%3C/svg%3E");
			background-size: 65%;
			background-repeat: no-repeat;
			background-position: 55% 45%;
			background-color: #0162e8;
			line-height: 17px;
			display: none;
		}

		.ckbox span:empty {
			padding-left: 0;
			width: 3px;
		}

		.ckbox input[type='checkbox'] {
			opacity: 0;
			margin: 0;
		}

		.ckbox input[type='checkbox']:checked+span:after {
			display: block;
		}

		.ckbox input[type='checkbox'][disabled]+span {
			opacity: .75;
		}

		.ckbox input[type='checkbox'][disabled]+span:before,
		.ckbox input[type='checkbox'][disabled]+span:after {
			opacity: .75;
		}

		.ckbox-inline {
			display: inline-block;
		}

		.latest-tasks .check-box .ckbox span:before {
			content: '';
			width: 16px;
			height: 16px;
			background-color: rgba(190, 206, 255, 0.05);
			border: 1px solid #d9e2ff;
			top: 1px;
			left: 0;
			border-radius: 2px;
		}

		.latest-tasks .check-box .ckbox span:after {
			border-radius: 2px;
		}

		.form-input input {
			display: none;

		}

		.form-input label {
			display: block;
			width: 100%;


			text-align: center;
			background: #1172c2;

			color: #fff;
			font-size: 15px;

			text-transform: Uppercase;
			font-weight: 600;
			border-radius: 5px;
			cursor: pointer;
		}

		.mg-b-10 {
			margin-bottom: 10px;
		}
	</style>

</head>

<body>
	<!--navbar-->
	<?php echo view('front/components/navbar') ?>
	<!--navbar-->

	<div class="main-content horizontal-content mt-5">

		<!-- container opened -->
		<div class="container">

			<!-- breadcrumb -->
			<div class="breadcrumb-header justify-content-between">
			</div>
			<!-- breadcrumb -->

			<!-- row -->
			<div class="row row-sm">
				<!-- Col -->
				<!-- Col -->
				<div class="col-lg-12">
					<div class="card">
						
						<?php echo $userData ?>

					</div>
				</div>
				<!-- /Col -->
			</div>
			<!-- row closed -->
		</div>
		<!-- Container closed -->
	</div>
	<!-- main-content closed -->


	<!--footer-->
	<?php echo view('front/components/footer') ?>
	<!--footer-->

	<!-- bootstrap bundle -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>



	<script src="admin/assets/plugins/jquery/jquery.min.js"></script>
	<script src="admin/assets/plugins/jquery/jquery-ui.js"></script>
	<!-- Internal Select2.min js -->
	<script src="admin/assets/plugins/select2/js/select2.min.js"></script>
	<script src="admin/assets/js/select2.js"></script>

	<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
	<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
	<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url('page/assets/js/jquery.form.js') ?>"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		function showPreview(event) {
			if (event.target.files.length > 0) {
				var src = URL.createObjectURL(event.target.files[0]);
				var preview = document.getElementById("file-ip-1-preview");
				preview.src = src;
				preview.style.display = "block";
			}
		}
		$.Thailand({
			$district: $(".district"), // input ของตำบล
			$amphoe: $(".sub_district"), // input ของอำเภอ
			$province: $(".province"), // input ของจังหวัด
			$zipcode: $(".postcode") // input ของรหัสไปรษณีย์
		});

		$(function() {

			$('.save').click(function() {

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

</body>

</html>