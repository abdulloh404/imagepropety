<?php

if ($id = 1) {
	$links = "loadDataSeach/loadSeach";
} else {
	$links = "loadSeach";
}
?>

<style>
	.mobile-search {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 9999;
		width: 100vw;
		height: 100vh;
		margin: 0 auto;
		padding-top: 15vh;
		background-color: rgba(0, 0, 0, .9);
	}

	.closebtn-search {
		position: absolute;
		top: 30px;
		right: 0;
		padding: 22px 22px 0 0;
		font-size: 40px;
		cursor: pointer;
		z-index: 11;
		color: #818181 !important;
		transition: 0.3s;
	}

	.ubermenu-responsive-toggle {
		z-index: 8 !important;
	}

	.ui-menu {
		z-index: 9999;
	}

	ul.nav li.dropdownCus:hover ul.dropdownCus-menu {
		display: block;
	}

	.border-respon {
		border-bottom: 2px solid;
		margin: 0;
	}

	@media screen and (max-width: 992px) {
		.border-respon {
			border: none;
			margin: 0;
		}
	}
</style>

<link rel="stylesheet" href="front/asset/css/jquery-ui.css">

<div class="container mb-2">
	<div class="headerLogo" style="">
		<div class="">
			<div class="" style="display: flex;justify-content: center;">

				<h1 class="respon-icon" style="visibility: hidden;">สวพ.FM91</h1>
			</div>
		</div>
		<div style="position: relative; display: flex; justify-content: center;">
			<a href="<?php echo front_link(1)?>">
				<img style="width: 80%;" class="respon-icon" src="<?php base_url() ?>front/assets/img/FM91transparentlogo.png">
			</a>
		</div>
		<div class="respon-icon">
			<ul class="socialicons d-flex justify-content-end respon-icon" style="margin: 0;">
				<li style="list-style: none;"><?php echo getSocialIcons() ?></li>
			</ul>

			<form method="get" action="<?php echo front_link(344, 'loadSeach') ?>" style="width: 100% grid-template-columns: 50%; display: grid; justify-content: end;" enctype="multipart/form-data">
				<nav class="navbar navbar-expand-lg navbar-light justify-content-end">




					<input type="submit" style="display: none;">
					<div class="form-group has-search collapse navbar-collapse" id="" style="position: relative;">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search myglass myglasscustom" viewBox="0 0 16 16">
							<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
						</svg>

						<div style="position: relative;">
							<i class="fas fa-search" style="position: absolute; top: 11px; left: 13px;"></i>
							<input data-link="<?php echo front_link($id, $links, array('pageId' => $id), false) ?>" type="text" class="form-control" name="model_hotkey" placeholder="ค้นหา..." style="background-color: #E5E5E5;">
						</div>

					</div>


				</nav>
			</form>

		</div>
	</div>
</div>

<?php echo getTopBanner() ?>

<div class="container bg-mobile" style=" margin-top:-30px;">
	<div class="row border-respon">
		<div class="col-xl-2 col-lg-2 col-md-12 col-12">

			<div class="d-flex justify-content-between">
				<div class="" style="background-color: #FFCC00;">
					<img class="logo-mobile" src="<?php base_url() ?>front/assets/img/FM91transparentlogo.png">
				</div>
				<div class="nav-item d-flex">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search myglass myglasscustom control-mobile-top-head" viewBox="0 0 16 16" style="">
						<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
					</svg>
					<div class="search-form mobile-search" style="display: none;">

						<form method="get" action="<?php echo front_link(344, 'loadSeach') ?>" style="width: 100%" enctype="multipart/form-data">
							<a href="javascript:void(0)" class="closebtn-search visible-xs" onclick="closeSearch()">×</a>

							<input type="submit" style="display: none;">
							<div class="form-group has-search navbar-collapse" id="" style="position: relative; margin: 20px;">
								<input data-link="<?php echo front_link($id, $links, array('pageId' => $id), false) ?>" type="text" class="form-control  hide" name="model_hotkey" placeholder="ค้นหา..." style="padding: 10px;">
							</div>
						</form>
					</div>

					<button class="navbar-toggler text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
						<i class="fas fa-bars"></i>
					</button>

				</div>
			</div>
		</div>
		<div class="col-xl-8 col-lg-8 col-md-12 col-12" style="padding: 0;">
			<nav class="navbar navbar-expand-lg navbar-light" style="position: relative; font-weight: bold;">
				<?php echo front_menus($params) ?>
			</nav>
		</div>
		<div class="col-xl-2 col-lg-2 col-md-12 col-12"></div>
	</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
	
	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
	<script src="admin/assets/plugins/jquery/jquery-ui.js"></script>


	<script src="<?php echo base_url('front/asset/js/search.js') ?>"></script>


	<script>
		$(function() {
			$('.closebtn-search').click(function() {
				$('.mobile-search').hide();
			});


			$('.control-mobile-top-head').click(function() {
				me = $(this);

				parents = me.parents('.mobile-top-head');

				$('.mobile-search').show();
				$('.ui-menu').css('z-index', '9999');

			});

			$('.control-navbar-toggler').click(function() {
				$('.navbar-toggler').trigger('click', function() {


				});

			});

		});
	</script>


</div>