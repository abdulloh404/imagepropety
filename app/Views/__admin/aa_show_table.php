<style>
	.header-btn {
		justify-content: space-between;
	}

	@media (max-width: 575.98px) {
		.header-btn {
			flex-direction: column;
		}
	}
</style>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="main-content horizontal-content">

	<div class="container">
		<div class="row row-sm">
			<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 grid-margin">
				<div class="card">

					<div class="card-body">

						<div class="card-header pb-0">
							<div class="d-flex header-btn">

								<h1 class="content-title mb-0 my-auto"><?php echo $title ?></h1>

								

								<div class="main-header-right">
									<?php echo $addButton; ?>
									
								
								</div>


							</div>
						</div>
						<div class="load-data-table mt-3">

							<?php echo $datastable ?>

						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
