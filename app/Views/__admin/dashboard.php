<div class="main-content horizontal-content">



	<!-- กราฟ -->
	<div class="container">
		<div class="col-xl-12">
			<div>
				<div class="card">
					<div class="card-body">
						<!-- กราฟ 1 -->
						<h4>ภาพรวมสถิติการเข้าดูบทความ</h4>
						<canvas id="myChart" style="width:100%;max-width:100%"></canvas>
					</div>
				</div>
			</div>

		</div>
	</div>

	<!-- จบ กราฟ -->

	<!-- บทความล่าสุด -->
	<!-- container opened -->
	<div class="container">
		<div class="col-xl-12">
			<div class="card">

				<div class="card-body">
					<div class="card-header pb-4 pt-5">
						<div class="d-flex justify-content-between">
							<h4>ข่าวล่าสุด</h4>
							<div class="main-header-left ">
								
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<?php echo $datastable ?>


					</div><!-- bd -->
					<!-- ดูเพิ่ม -->
					<div class="form-group mb-0 mt-3 justify-content-end pt-5">
						<div>
							<a href="<?php echo front_link( 4 )?>" class="btn btn-primary" style="background-color: #FDD400;">ข่าวทั้งหมด</a>
						</div>
					</div>
				</div><!-- bd -->
			</div><!-- bd -->
		</div>
	</div>




	<!-- กราฟ -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
	<!-- กราฟ 2 -->
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


	<script>
		// กราฟ 1
		// var xValues = ["Italy", "France", "Spain", "USA","France", "Argentina", "Argentina", "Italy", "Argentina", "Italy"];
		// var xValues = ;
		// var yValues = [55, 49, 44, 24, 65, 35, 49, 44, 24, 45];
		// var barColors = ["red", "green","blue","orange","brown","red", "green","blue","orange","brown"];

		// new Chart("myChart", {
		// 	type: "bar",
		// 	data: {
		// 		labels: xValues,
		// 		datasets: [{
		// 			backgroundColor: barColors,
		// 			data: yValues
		// 		}]
		// 	},
		// 	options: {
		// 		legend: {
		// 			display: false
		// 		},
		// 		title: {
		// 			display: true,
		// 			text: ""
		// 		}
		// 	}
		// });

		// กราฟ 2
		google.charts.load('current', {
			'packages': ['corechart']
		});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Contry', 'Mhl'],
				['Italy', 54.8],
				['France', 48.6],
				['Spain', 44.4],
				['USA', 23.9],
				['Argentina', 14.5]
			]);

			var options = {
				title: '',
				is3D: true
			};

			var chart = new google.visualization.PieChart(document.getElementById('myChart2'));
			chart.draw(data, options);
		}

		$.ajax({
			url: "<?php echo front_link($id, 'dashboard') ?>",
			type: 'GET',
			success: function(res, req) {
				data = JSON.parse(res);				

				var xValues = data.name;
				// var xValues = ["Italy", "France", "Spain", "USA", "France", "Argentina", "Argentina", "Italy", "Argentina", "Italy"];

				var yValues = data.count;
				// var yValues = [55, 49, 44, 24, 65, 35, 49, 44, 24, 45];
				var barColors = ["red", "green", "blue", "orange", "brown", "red", "green", "blue", "orange", "brown"];

				new Chart("myChart", {
					type: "bar",
					data: {
						labels: xValues,
						datasets: [{
							backgroundColor: barColors,
							data: yValues
						}]
					},
					options: {
						tooltips:{
							callbacks:{
								label: function(item, everything){
								let population = item.yLabel;
								population = population.toLocaleString();
								let label = 'จำนวนผู้เข้าชม: ' + population + ' คน';
								return label;
								}
							}
						},
						scales:{
							yAxes:[{
								ticks:{
									beginAtZero:true,
								}
							}],
							xAxes:[{
								ticks:{
									fontColor: "black",
									fontStyle : "bold",
								}
							}]
						},
						legend: {
							display: false
						},
						title: {
							display: true,
							text: ""
						}				
					}
				});
			}
		});
	</script>



</div>