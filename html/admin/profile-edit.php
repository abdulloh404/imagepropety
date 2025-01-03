
<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
		<meta name="Author" content="Spruko Technologies Private Limited">
		<meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>

		<!-- Title -->
		<title>สวพ.FM91</title>

		<!-- Favicon -->
		<link rel="icon" href="./assets/img/brand/favicon.png" type="image/x-icon"/>

		<!-- Icons css -->
		<link href="./assets/css/icons.css" rel="stylesheet">

		<!-- Bootstrap css -->
		<link href="./assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Internal Select2 css -->
		<link href="./assets/plugins/select2/css/select2.min.css" rel="stylesheet">

		<!--  Right-sidemenu css -->
		<link href="./assets/plugins/sidebar/sidebar.css" rel="stylesheet">

		<!--  Custom Scroll bar-->
		<link href="./assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet"/>

		<!--- Style css-->
		<link href="./assets/css/style.css" rel="stylesheet">
		<link href="./assets/css/style-dark.css" rel="stylesheet">
		<link href="./assets/css/boxed.css" rel="stylesheet">
		<link href="./assets/css/dark-boxed.css" rel="stylesheet">

		<!---Skinmodes css-->
		<link href="./assets/css/skin-modes.css" rel="stylesheet" />

		<!--- Animations css-->
		<link href="./assets/css/animate.css" rel="stylesheet">

	</head>

	<body class="main-body">

		<!-- Loader -->
		<div id="global-loader">
			<img src="./assets/img/loader.svg" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->

		<!-- Page -->
		<div class="page">
			<!-- เมนู -->
						<!-- เมนู -->
			<!-- main-header opened -->
			<div class="main-header nav nav-item hor-header">
				<div class="container">
					<div class="main-header-left ">
						<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
						<a class="header-brand" href="index.html">
							<img src="./assets/img/brand/logo-white.png" class="desktop-dark">
							<img src="./assets/img/logo.jpg" style="border-radius: 99px;" class="desktop-logo">
							<img src="./assets/img/logo.jpg" style="border-radius: 99px;" class="desktop-logo-1">
							<img src="./assets/img/brand/favicon-white.png" class="desktop-logo-dark">
						</a>
						<div class="main-header-center  ms-4">
							<input class="form-control" placeholder="Search for anything..." type="search"><button class="btn"><i class="fe fe-search"></i></button>
						</div>
					</div><!-- search -->
					<div class="main-header-right">
						<ul class="nav nav-item  navbar-nav-right ms-auto">
							<li class="nav-link" id="bs-example-navbar-collapse-1">
								<form class="navbar-form" role="search">
									<div class="input-group">
										<input type="text" class="form-control" placeholder="Search">
										<span class="input-group-btn">
											<button type="reset" class="btn btn-default">
												<i class="fas fa-times"></i>
											</button>
											<button type="submit" class="btn btn-default nav-link resp-btn">
												<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
											</button>
										</span>
									</div>
								</form>
							</li>
							<li class="dropdown nav-item main-header-message ">
								<button class="btn btn-light btn-block" style="background-color: #fff;margin-right: 0px;padding-right: 0px;">สวัสดีคุณ :</button>
							</li>
							<li class="dropdown main-profile-menu nav nav-item nav-link">
								<p style="padding-top: 15px;">Admin</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- /main-header -->



			<!--Horizontal-main -->
			<div class="sticky">
				<div class="horizontal-main hor-menu clearfix side-header">
					<div class="horizontal-mainwrapper container clearfix">
						<!--Nav-->
						<nav class="horizontalMenu clearfix">
							<ul class="horizontalMenu-list">
								<!-- <li aria-haspopup="true"><a href="index.html" class=""><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>แดชบอร์ด</a></li>
								<li aria-haspopup="true"><a href="addnew.html" class=""><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" class="side-menu__icon" viewBox="0 0 24 24" ><g><rect fill="none"/></g><g><g/><g><path d="M21,5c-1.11-0.35-2.33-0.5-3.5-0.5c-1.95,0-4.05,0.4-5.5,1.5c-1.45-1.1-3.55-1.5-5.5-1.5S2.45,4.9,1,6v14.65 c0,0.25,0.25,0.5,0.5,0.5c0.1,0,0.15-0.05,0.25-0.05C3.1,20.45,5.05,20,6.5,20c1.95,0,4.05,0.4,5.5,1.5c1.35-0.85,3.8-1.5,5.5-1.5 c1.65,0,3.35,0.3,4.75,1.05c0.1,0.05,0.15,0.05,0.25,0.05c0.25,0,0.5-0.25,0.5-0.5V6C22.4,5.55,21.75,5.25,21,5z M3,18.5V7 c1.1-0.35,2.3-0.5,3.5-0.5c1.34,0,3.13,0.41,4.5,0.99v11.5C9.63,18.41,7.84,18,6.5,18C5.3,18,4.1,18.15,3,18.5z M21,18.5 c-1.1-0.35-2.3-0.5-3.5-0.5c-1.34,0-3.13,0.41-4.5,0.99V7.49c1.37-0.59,3.16-0.99,4.5-0.99c1.2,0,2.4,0.15,3.5,0.5V18.5z"/><path d="M11,7.49C9.63,6.91,7.84,6.5,6.5,6.5C5.3,6.5,4.1,6.65,3,7v11.5C4.1,18.15,5.3,18,6.5,18 c1.34,0,3.13,0.41,4.5,0.99V7.49z" opacity=".3"/></g><g><path d="M17.5,10.5c0.88,0,1.73,0.09,2.5,0.26V9.24C19.21,9.09,18.36,9,17.5,9c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,10.69,16.18,10.5,17.5,10.5z"/><path d="M17.5,13.16c0.88,0,1.73,0.09,2.5,0.26V11.9c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,13.36,16.18,13.16,17.5,13.16z"/><path d="M17.5,15.83c0.88,0,1.73,0.09,2.5,0.26v-1.52c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,16.02,16.18,15.83,17.5,15.83z"/></g></g></svg>เพิ่มข่าว</a></li>
								<li aria-haspopup="true"><a href="editnew.html" class=""><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M6.26 9L12 13.47 17.74 9 12 4.53z" opacity=".3"/><path d="M19.37 12.8l-7.38 5.74-7.37-5.73L3 14.07l9 7 9-7zM12 2L3 9l1.63 1.27L12 16l7.36-5.73L21 9l-9-7zm0 11.47L6.26 9 12 4.53 17.74 9 12 13.47z"/></svg>จัดการข่าว</a></li>
								<li aria-haspopup="true"><a href="userlist.html" class=""><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>จัดการผู้ใช้</a></li>
								 -->
								
								<li aria-haspopup="true"><a href="index.html" >แดชบอร์ด</a></li>
								<li aria-haspopup="true"><a href="addnew.html" >เพิ่มข่าว</a></li>
								<li aria-haspopup="true"><a href="editnew.html" >จัดการข่าว</a></li>
								<li aria-haspopup="true"><a href="userlist.html" >จัดการผู้ใช้</a></li>	
								<li aria-haspopup="true"><a href="memberlist.html" >จัดการสมาชิก</a></li>
								<li aria-haspopup="true"><a href="managetemplates.html">จัดการเทมเพลต</a></li>	
								<li aria-haspopup="true"><a href="lostitems.html">ของหายได้คืน</a></li>	
								<li aria-haspopup="true"><a href="publicrelations.html">ประชาสัมพันธ์</a></li>	
								<li aria-haspopup="true"><a href="editvideo.html">วีดีโอ</a></li>
								<li aria-haspopup="true"><a href="radiostation.html">สถานีวิทยุในเครือบริษัท</a></li>
								<li aria-haspopup="true"><a href="#" style="border: 1px solid #fff;padding: 7px 12px;margin: 7px 5px 5px 5px;border-radius: 30px;background: #FDD400;color: #fff;">ออกจากระบบ</a></li>
							</ul>
						</nav>
						<!--Nav-->
					</div>
				</div>
			</div>
			<!--Horizontal-main -->
			<!-- เมนู -->





			<!-- main-content opened -->
			<div class="main-content horizontal-content">

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
								<div class="card-body">
									<div class="form-group mt-3 mb-3">
											<div class="row">
												<div class="col-md-3">
														<img alt="Responsive image" class="img-thumbnail wd-100p wd-sm-200" src="./assets/img/photos/1.jpg">
														<button class="btn btn-secondary mt-1" style="padding: 5px 20px;">แก้ไขรูป</button>
												</div>
												<div class="col-md-9">
													<h4>สวัสดีคุณ:</h4>
													<h3 class="mb-4 main-content-label mt-2 mb-2">Personal Information</h3>
													<h4 class="mb-2 mt-5 main-content-label">ข้อมูลส่วนตัว</h4>								
												</div>
											</div>
									</div>
									<form class="form-horizontal">
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">ชื่อ (ภาษาไทย)<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">นามสกุล (ภาษาไทย)<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">ชื่อ (ภาษาอังกฤษ)<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">นามสกุล (ภาษาอังกฤษ)<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">เบอร์โทรศัพท์<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="081-123-4567">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">อีเมล<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="email@email.com">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">วัน/เดือน/ปีเกิด<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="20/01/1999">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">เพศ<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<select class="form-control select2">
														<option>เลือกเพศ</option>
														<option>ชาย</option>
														<option>หญิง</option>
														<option>อื่น ๆ</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">สถานภาพ<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<select class="form-control select2">
														<option>เลือกสถานภาพ</option>
														<option>โสด</option>
														<option>สมรส</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">ระดับการศึกษา<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<select class="form-control select2">
														<option>เลือกระดับการศึกษา</option>
														<option>ต่ำกว่าปริญญาตรี</option>
														<option>ปริญญาตรี</option>
														<option>ปริญญาโท</option>
														<option>สูงกว่าปริญญาโท</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">ที่อยู่<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<textarea class="form-control" name="example-textarea-input" rows="3"  placeholder=""></textarea>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">แขวง/ตำบล<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">เขต/อำเภอ<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">จังหวัด<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">รหัสไปรษณีย์<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">อาชีพ<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<select class="form-control select2">
														<option>เลือกอาชีพ</option>
														<option>ข้าราชการ / รัฐวิสาหกิจ / สถาบันการศึกษา หรือหน่วยงานภายใต้สังกัดรัฐ</option>
														<option>เอ็นจีโอ กิจการเพื่อสังคม หรือองค์กรระหว่างประเทศ</option>
														<option>อาชีพอิสระ</option>
														<option>พนักงานบริษัท</option>
														<option>เจ้าของกิจการ</option>
														<option>อาชีพเฉพาะทาง (เช่น แพทย์/ทันตแพทย์/เภสัชกร/ผู้พิพากษา/ทนายความ/วิศวกร/สถาปนิก)</option>
														<option>นักเรียน/นักศึกษา</option>
														<option>อื่นๆ</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">รายได้<span class="text-danger">*</span></label>
												</div>
												<div class="col-md-9">
													<select class="form-control select2">
														<option>เลือกระดับรายได้</option>
														<option>น้อยกว่า 14,999 บาท</option>
														<option>15,000 - 29,999 บาท</option>
														<option>30,000 - 49,999 บาท</option>
														<option>50,000 - 69,999 บาท</option>
														<option>70,000 - 199,999 บาท</option>
														<option>200,000 - 299,999 บาท</option>
														<option>300,000 - 499,999 บาท</option>
														<option>มากกว่า 500,000 บาท</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">สถานที่ทำงาน / หน่วยงาน / สังกัด</label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>
										<div class="form-group ">
											<div class="row">
												<div class="col-md-3">
													<label class="form-label">กิจกรรมยามว่าง</label>
												</div>
												<div class="col-md-9">
													<input type="text" class="form-control"  placeholder="">
												</div>
											</div>
										</div>



										<div class="mb-4 main-content-label">ความสนใจด้านการลงทุน<span class="text-danger">*</span></div>
										<div class="form-group mb-0">
											<div class="row">
												<div class="col-md-6">
													<div class="custom-controls-stacked">
														<label class="ckbox mg-b-10"><input type="checkbox"><span>เงินฝาก</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>พันธบัตรรัฐบาล</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>หุ้น (Single Stock)</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>ทองคํา เพชร</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>ประกันชีวิต</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>อื่นๆ</span></label>
													</div>
												</div>
												<div class="col-md-6">
													<div class="custom-controls-stacked">
														<label class="ckbox mg-b-10"><input type="checkbox"><span>หุ้นกู้ (bond)</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>กองทุนรวม</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>อสังหาริมทรัพย์ เช่น บ้าน คอนโดมิเนียม ที่ดิน</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>คริปโตเคอร์เรนซี</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>สลากออมสิน,สลาก ธ.ก.ส.</span></label>
														<label class="ckbox mg-b-10"><input type="checkbox"><span>ไม่สนใจ</span></label>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
								<div class="card-footer">
									<button type="submit" class="btn btn-warning waves-effect waves-light">Update Profile</button>
								</div>
							</div>
						</div>
						<!-- /Col -->
					</div>
					<!-- row closed -->
				</div>
				<!-- Container closed -->
			</div>
			<!-- main-content closed -->


			<!-- Footer opened -->
			<div class="main-footer ht-40">
				<div class="container-fluid pd-t-0-f ht-100p">
					<span>Copyright © 2021 <a href="#">Valex</a>. Designed by <a href="https://www.spruko.com/">Spruko</a> All rights reserved.</span>
				</div>
			</div>
			<!-- Footer closed -->



		</div>
		<!-- End Page -->

		<!-- Back-to-top -->
		<a href="#top" id="back-to-top"><i class="fas fa-angle-double-up"></i></a>

		<!-- JQuery min js -->
		<script src="./assets/plugins/jquery/jquery.min.js"></script>

		<!-- Bootstrap Bundle js -->
		<script src="./assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="./assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

		<!--Internal  Chart.bundle js -->
		<script src="./assets/plugins/chart.js/Chart.bundle.min.js"></script>

		<!-- Ionicons js -->
		<script src="./assets/plugins/ionicons/ionicons.js"></script>

		<!-- Moment js -->
		<script src="./assets/plugins/moment/moment.js"></script>

		<!-- P-scroll js -->
		<script src="./assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="./assets/plugins/perfect-scrollbar/p-scroll.js"></script>

		<!-- Rating js-->
		<script src="./assets/plugins/rating/jquery.rating-stars.js"></script>
		<script src="./assets/plugins/rating/jquery.barrating.js"></script>

		<!-- Internal Select2.min js -->
		<script src="./assets/plugins/select2/js/select2.min.js"></script>
		<script src="./assets/js/select2.js"></script>

		<!-- Custom Scroll bar Js-->
		<script src="./assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

		<!-- Horizontalmenu js-->
		<script src="./assets/plugins/horizontal-menu/horizontal-menu-2/horizontal-menu.js"></script>

		<!-- Sticky js -->
		<script src="./assets/js/sticky.js"></script>

		<!-- Right-sidebar js -->
		<script src="./assets/plugins/sidebar/sidebar.js"></script>
		<script src="./assets/plugins/sidebar/sidebar-custom.js"></script>

		<!-- eva-icons js -->
		<script src="./assets/js/eva-icons.min.js"></script>

		<!-- custom js -->
		<script src="./assets/js/custom.js"></script>

	</body>
</html>