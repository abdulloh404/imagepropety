<!DOCTYPE html>
<html lang="en">
	<head><base href="<?php echo base_url() ?>" />

		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
		<meta name="Author" content="Spruko Technologies Private Limited">
		<meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>

		<!-- Title -->
		<title>สวพ.FM91</title>

		<!-- Favicon -->
		<link rel="icon" href="admin/assets/img/brand/favicon.png" type="image/x-icon"/>

		<!-- Icons css -->
		<link href="admin/assets/css/icons.css" rel="stylesheet">

		<!-- Bootstrap css -->
		<link href="admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Internal Select2 css -->
		<link href="admin/assets/plugins/select2/css/select2.min.css" rel="stylesheet">

		<!---Internal Fileupload css-->
		<link href="admin/assets/plugins/fileuploads/css/fileupload.css" rel="stylesheet" type="text/css"/>

		<!---Internal Fancy uploader css-->
		<link href="admin/assets/plugins/fancyuploder/fancy_fileupload.css" rel="stylesheet" />
		
		<!--Internal  Datetimepicker-slider css -->
		<link href="admin/assets/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css" rel="stylesheet">
		<link href="admin/assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.css" rel="stylesheet">
		<link href="admin/assets/plugins/pickerjs/picker.min.css" rel="stylesheet">

		<!-- Internal Spectrum-colorpicker css -->
		<link href="admin/assets/plugins/spectrum-colorpicker/spectrum.css" rel="stylesheet">

		<!--  Right-sidemenu css -->
		<link href="admin/assets/plugins/sidebar/sidebar.css" rel="stylesheet">

		<!--  Custom Scroll bar-->
		<link href="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet"/>

		<!--- Style css-->
		<link href="admin/assets/css/style.css" rel="stylesheet">
		<link href="admin/assets/css/style-dark.css" rel="stylesheet">
		<link href="admin/assets/css/boxed.css" rel="stylesheet">
		<link href="admin/assets/css/dark-boxed.css" rel="stylesheet">

		<!---Skinmodes css-->
		<link href="admin/assets/css/skin-modes.css" rel="stylesheet" />

		<!--- Animations css-->
		<link href="admin/assets/css/animate.css" rel="stylesheet">
	</head>

	<body class="main-body">

		<!-- Loader -->
		<div id="global-loader">
			<img src="admin/assets/img/loader.svg" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->

		<!-- Page -->
		<div class="page">

			<!-- เมนู -->
			<!-- main-header opened -->
			<?php echo view( 'admin/component/top_bar', $params ) ?>
			<!-- /main-header -->



			<!--Horizontal-main -->
			
			<!--Horizontal-main -->
			<!-- เมนู -->

			<div class="jumps-prevent" style="padding-top: 53.2969px;"></div>
			<div class="main-content horizontal-content">
				<!-- content -->
				<div class="container">
					<div class="card">
						<div class="card-body">
								<div class="card  box-shadow-0">
									<div class="card-header">
										<h2>สถานีวิทยุในเครือบริษัท</h2>
										<p class="mb-2">กรุณากรอกข้อมูลให้ครบถ้วน และตรวจสอบความถูกต้อง</p>
									</div>
									<div class="card-body pt-0">
										<form class="form-horizontal">
											<div class="form-group">
												<label>ชื่อสถานีวิทยุ</label>
												<input type="text" class="form-control" name="form[headernew]" id="" placeholder="หัวเรื่องข่าว">
											</div>
											<div class="form-group">
												<label>อัพโหลดรูปภาพหน้าปก</label>
													<div>
														<input id="demo" type="file" name="form[imagenew]" accept=".jpg, .png, image/jpeg, image/png, html, zip, css,js" multiple>
													</div>
											</div>

											<div class="form-group">
												<label>ลิ้งสถานีวิทยุ</label>
												<source src="https://ia800905.us.archive.org/19/items/FREE_background_music_dhalius/backsound.mp3" type="audio/mp3">
													<div>
														<input id="demo" type="text" name="form[imageradio]" accept=".jpg, .png, image/jpeg, image/png, html, zip, css,js" multiple>
													</div>
											</div>
											
											
											<div class="form-group mb-0 mt-3 justify-content-end">
												<div>
													<button type="submit" class="btn btn-primary" style="background-color: #FDD400;">เพิ่มข้อมูล</button>
													<button type="submit" class="btn btn-secondary">เคลียร์ข้อมูล</button>
												</div>
											</div>
										</form>
									</div>
								</div>
						</div>
					</div>
				</div>
			<!-- end contect -->
		</div>



			<!-- Footer opened -->
			<?php echo view( 'admin/component/footer', $params ) ?>
			<!-- Footer closed -->


		</div>
		<!-- End Page -->

		<!-- Back-to-top -->
		<a href="#top" id="back-to-top"><i class="fas fa-angle-double-up"></i></a>

		<!-- JQuery min js -->
		<script src="admin/assets/plugins/jquery/jquery.min.js"></script>

		<!--Internal  Datepicker js -->
		<script src="admin/assets/plugins/jquery-ui/ui/widgets/datepicker.js"></script>

		<!-- Bootstrap Bundle js -->
		<script src="admin/assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="admin/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

		<!-- Ionicons js -->
		<script src="admin/assets/plugins/ionicons/ionicons.js"></script>

		<!-- Moment js -->
		<script src="admin/assets/plugins/moment/moment.js"></script>

		<!--Internal  jquery.maskedinput js -->
		<script src="admin/assets/plugins/jquery.maskedinput/jquery.maskedinput.js"></script>

		<!--Internal  spectrum-colorpicker js -->
		<script src="admin/assets/plugins/spectrum-colorpicker/spectrum.js"></script>

		<!-- Internal Select2.min js -->
		<script src="admin/assets/plugins/select2/js/select2.min.js"></script>

		<!--Internal Ion.rangeSlider.min js -->
		<script src="admin/assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>

		<!--Internal  jquery-simple-datetimepicker js -->
		<script src="admin/assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js"></script>

		<!-- Ionicons js -->
		<script src="admin/assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js"></script>

		<!--Internal  pickerjs js -->
		<script src="admin/assets/plugins/pickerjs/picker.min.js"></script>

		<!-- Rating js-->
		<script src="admin/assets/plugins/rating/jquery.rating-stars.js"></script>
		<script src="admin/assets/plugins/rating/jquery.barrating.js"></script>

		<!-- P-scroll js -->
		<script src="admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="admin/assets/plugins/perfect-scrollbar/p-scroll.js"></script>

		<!-- Custom Scroll bar Js-->
		<script src="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

		<!-- Horizontalmenu js-->
		<script src="admin/assets/plugins/horizontal-menu/horizontal-menu-2/horizontal-menu.js"></script>

		<!-- Sticky js -->
		<script src="admin/assets/js/sticky.js"></script>

		<!-- Right-sidebar js -->
		<script src="admin/assets/plugins/sidebar/sidebar.js"></script>
		<script src="admin/assets/plugins/sidebar/sidebar-custom.js"></script>

		<!-- eva-icons js -->
		<script src="admin/assets/js/eva-icons.min.js"></script>

		<!-- custom js -->
		<script src="admin/assets/js/custom.js"></script>

		<!-- Internal form-elements js -->
		<script src="admin/assets/js/form-elements.js"></script>

		
		<!--Internal Fileuploads js-->
		<script src="admin/assets/plugins/fileuploads/js/fileupload.js"></script>
		<script src="admin/assets/plugins/fileuploads/js/file-upload.js"></script>

		<!--Internal Fancy uploader js-->
		<script src="admin/assets/plugins/fancyuploder/jquery.ui.widget.js"></script>
        <script src="admin/assets/plugins/fancyuploder/jquery.fileupload.js"></script>
        <script src="admin/assets/plugins/fancyuploder/jquery.iframe-transport.js"></script>
        <script src="admin/assets/plugins/fancyuploder/jquery.fancy-fileupload.js"></script>
        <script src="admin/assets/plugins/fancyuploder/fancy-uploader.js"></script>

		
	</body>
</html>