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

		<!--  Custom Scroll bar-->
		<link href="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet"/>

		<!--  Sidebar css -->
		<link href="admin/assets/plugins/sidebar/sidebar.css" rel="stylesheet">

		<!--- Internal Morris css-->
		<link href="admin/assets/plugins/morris.js/morris.css" rel="stylesheet">

		<!--- Style css --->
		<link href="admin/assets/css/style.css" rel="stylesheet">
		<link href="admin/assets/css/boxed.css" rel="stylesheet">
		<link href="admin/assets/css/dark-boxed.css" rel="stylesheet">

		<!--- Dark-mode css --->
		<link href="admin/assets/css/style-dark.css" rel="stylesheet">

		<!---Skinmodes css-->
		<link href="admin/assets/css/skin-modes.css" rel="stylesheet" />

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
			<!-- main-content opened -->
			<div class="main-content horizontal-content">

				<!-- container opened -->
				<div class="container">

					<!--Row-->
					<div class="row row-sm">
						<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 grid-margin">
							<div class="card">
								<div class="card-header pb-0">
									<div class="d-flex justify-content-between">
										<h1 class="content-title mb-0 my-auto">จัดการสมาชิก</h1>
										
										<div class="main-header-left ">
											<div class="main-header-center  ms-4">
												<input class="form-control" placeholder="ค้นหา..." type="search"><button class="btn"><i class="fe fe-search"></i></button>
											</div>
										</div>

										<div class="col-sm-6 col-md-3 mg-t-10 mg-md-t-0">
											<button class="btn btn-success">เพิ่มสมาชิก</button>
										</div>

									</div>
									
								</div>
								<div class="card-body">
									<div class="table-responsive border-top userlist-table">
										<table class="table card-table table-striped table-vcenter text-nowrap mb-0">
											<thead>
												<tr>
													<th class="wd-lg-20p"><span>ลำดับ</span></th>
													<th class="wd-lg-20p"><span>ผู้ใช้งาน</span></th>
													<th class="wd-lg-20p"><span>ชื่อ-นามสกุล</span></th>
													<th class="wd-lg-20p"><span>อีเมล</span></th>
													<th class="wd-lg-20p">จัดการผู้ใช้</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>
														1
													</td>
													<td>สมปอง</td>
													<td>
														นายสมปอง มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														2
													</td>
													<td>มีนา</td>
													<td>
														สาวมีนา มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														3
													</td>
													<td>สมปอง</td>
													<td>
														นายสมปอง มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														4
													</td>
													<td>มีนา</td>
													<td>
														สาวมีนา มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														5
													</td>
													<td>สมปอง</td>
													<td>
														นายสมปอง มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														6
													</td>
													<td>สมปอง</td>
													<td>
														สาวมีนา มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														7
													</td>
													<td>สมปอง</td>
													<td>
														นายสมปอง มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														8
													</td>
													<td>สมปอง</td>
													<td>
														สาวมีนา มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														9
													</td>
													<td>สมปอง</td>
													<td>
														นายสมปอง มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														8
													</td>
													<td>สมมีนา</td>
													<td>
														สาวมีนา มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														9
													</td>
													<td>สมปอง</td>
													<td>
														นายสมปอง มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>

												<tr>
													<td>
														10
													</td>
													<td>สมมีนา</td>
													<td>
														สาวมีนา มีสุข
													</td>
													<td>
														<a href="#">mila@kunis.com</a>
													</td>
													<td>
														<a href="edituser.html" class="btn btn-sm btn-primary" style="background-color: #FDD400;">
															<i class="las la-pen"></i>
														</a>
														<a href="#" class="btn btn-sm btn-danger">
															<i class="las la-trash"></i>
														</a>
													</td>
												</tr>


											</tbody>
										</table>
									</div>
									<ul class="pagination mt-4 mb-0 float-end flex-wrap">
										<li class="page-item page-prev disabled">
											<a class="page-link" href="#" tabindex="-1">Prev</a>
										</li>
										<li class="page-item active"><a class="page-link" href="#">1</a></li>
										<li class="page-item"><a class="page-link" href="#">2</a></li>
										<li class="page-item"><a class="page-link" href="#">3</a></li>
										<li class="page-item"><a class="page-link" href="#">4</a></li>
										<li class="page-item"><a class="page-link" href="#">5</a></li>
										<li class="page-item page-next">
											<a class="page-link" href="#">Next</a>
										</li>
									</ul>
								</div>
							</div>
						</div><!-- COL END -->
					</div>
					<!-- row closed  -->
				</div>
				<!-- Container closed -->
			</div>
			<!-- main-content closed -->
			<!-- Container closed -->




			<!-- Footer opened -->
			<?php echo view( 'admin/component/footer', $params ) ?>
			<!-- Footer closed -->

		</div>
		<!-- End Page -->

		<!-- Back-to-top -->
		<a href="#top" id="back-to-top"><i class="fas fa-angle-double-up"></i></a>

		<!-- JQuery min js -->
		<script src="admin/assets/plugins/jquery/jquery.min.js"></script>

		<!-- Bootstrap Bundle js -->
		<script src="admin/assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="admin/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

		<!-- Ionicons js -->
		<script src="admin/assets/plugins/ionicons/ionicons.js"></script>

		<!-- Moment js -->
		<script src="admin/assets/plugins/moment/moment.js"></script>

		<!--Internal Sparkline js -->
		<script src="admin/assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

		<!-- Moment js -->
		<script src="admin/assets/plugins/raphael/raphael.min.js"></script>

		<!-- Internal Piety js -->
		<script src="admin/assets/plugins/peity/jquery.peity.min.js"></script>

		<!-- Rating js-->
		<script src="admin/assets/plugins/rating/jquery.rating-stars.js"></script>
		<script src="admin/assets/plugins/rating/jquery.barrating.js"></script>

		<!-- P-scroll js -->
		<script src="admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="admin/assets/plugins/perfect-scrollbar/p-scroll.js"></script>

		<!-- Sidemenu js-->
		<script src="admin/assets/plugins/sidebar/sidebar.js"></script>
		<script src="admin/assets/plugins/sidebar/sidebar-custom.js"></script>

		<!-- Eva-icons js -->
		<script src="admin/assets/js/eva-icons.min.js"></script>

		<!--Internal Apexchart js-->
		<script src="admin/assets/js/apexcharts.js"></script>

		<!-- Horizontalmenu js-->
		<script src="admin/assets/plugins/horizontal-menu/horizontal-menu-2/horizontal-menu.js"></script>

		<!-- Sticky js -->
		<script src="admin/assets/js/sticky.js"></script>

		<!-- Internal Map -->
		<script src="admin/assets/plugins/jqvmap/jquery.vmap.min.js"></script>
		<script src="admin/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

		<!-- Internal Chart js -->
		<script src="admin/assets/plugins/chart.js/Chart.bundle.min.js"></script>

		<!--Internal  index js -->
		<script src="admin/assets/js/index.js"></script>
		<script src="admin/assets/js/jquery.vmap.sampledata.js"></script>

		<!-- custom js -->
		<script src="admin/assets/js/custom.js"></script>
		<script src="admin/assets/js/jquery.vmap.sampledata.js"></script>

	</body>
</html>