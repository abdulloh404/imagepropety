<div class="main-header nav nav-item hor-header">


	<div class="container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="<?php echo base_url() ?>">
				<img src="admin/assets/img/brand/logo-white.png" class="desktop-dark">
				<img src="admin/assets/img/logo.jpg" style="border-radius: 99px;" class="desktop-logo">
				<img src="admin/assets/img/logo.jpg" style="border-radius: 99px;" class="desktop-logo-1">
				<img src="admin/assets/img/brand/favicon-white.png" class="desktop-logo-dark">
			</a>
			<!-- <div class="main-header-center  ms-4">
				<input class="form-control" placeholder="Search for anything..." type="search"><button class="btn"><i class="fe fe-search"></i></button>
			</div> -->
		</div><!-- search -->
		<div class="main-header-right">
			<div class="dropdown nav nav-item nav-link">
				<a class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
					สวัสดีคุณ : <?php echo $_SESSION['u']->first_name ?>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
					<li><a class="dropdown-item" href="<?php echo front_link(349,'formProduct/'.@$_SESSION['u']->user_id) ?>">จัดการบัญชี</a></li>
					<li><a class="dropdown-item" href="<?php echo base_url('logout') ?>">ออกจากระบบ</a></li>					
				</ul>
			</div>

			<li class="dropdown main-profile-menu nav nav-item nav-link">

			</li>

			</ul>

		</div>
	</div>


</div>



<div class="sticky">
	<div class="horizontal-main hor-menu clearfix side-header">
		<div class="horizontal-mainwrapper  clearfix">
			<!--Nav-->
			<nav class="horizontalMenu clearfix">


				<ul class="horizontalMenu-list top-menu">
					<?php echo getAdminMenu( NULL, false, 'aaaaaaaaaaaaaaa', $params  );?>
				</ul>
			</nav>
			<!--Nav-->
			
		</div>
	</div>
</div>



<style>
	.top-menu {
		display: flex !important;
		justify-content: center;
	}

	@media (max-width: 798px) {
		.top-menu {
			display: block !important;
			justify-content: start;
		}
	}


	@media (max-width: 450px) {
		.top-menu {
			display: block !important;
			justify-content: start;
		}

		.dropdown-item {
			padding: 8px 15px;
			font-size: 9px;
		}
	}

	@media screen and (max-width: 375) {
		.dropdown-item {
			padding: 8px 15px;
			font-size: 9px;
		}
	}
</style>