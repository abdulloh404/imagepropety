
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
<div class="main-content horizontal-content">

	<!-- container opened -->
	<div class="container">

		<!--Row-->
		<div class="row row-sm">
			<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 grid-margin">
				<div class="card">
				
					<div class="card-body">
					
						<div class="card-header pb-0 pt-5">
							<div class="d-flex header-btn">
								<h1 class="content-title mb-0 my-auto">จัดการบทความ</h1>

								<div class="main-header-left ">
									<div class="main-header-center  ms-4">
										<input class="form-control" placeholder="ค้นหา..." type="search"><button class="btn"><i class="fe fe-search"></i></button>
									</div>
								</div>
								
								<button class="btn btn-secondary p-2">ลบที่เลือก</button>

								<div class="btn-group dropdown">
									<button type="button" class="btn btn-primary">14 Aug 2019</button>
									<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuDate" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate" x-placement="bottom-end">
										<a class="dropdown-item" href="#">2015</a>
										<a class="dropdown-item" href="#">2016</a>
										<a class="dropdown-item" href="#">2017</a>
										<a class="dropdown-item" href="#">2018</a>
									</div>
																											
								</div>

							</div>
						</div>									
						<div class="load-data-table">
					
							<?php echo $datastable ?>
							

						</div>
						
					</div>
				</div>
			</div><!-- COL END -->
		</div>
		<!-- row closed  -->
	</div>
	<!-- Container closed -->
</div>


