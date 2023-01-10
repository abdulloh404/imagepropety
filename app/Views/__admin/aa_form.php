
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


	<title>ImageProperty</title>

    <link rel="icon" type="image/x-icon" href="page/admin-assets/img/logo.png">

	<link href="admin/assets/css/dropify.min.css" rel="stylesheet">

	<!-- Icons css -->
	<link href="admin/assets/css/icons.css" rel="stylesheet">

	<!-- Bootstrap css -->
	<link href="admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Internal Select2 css -->
	<link href="admin/assets/plugins/select2/css/select2.min.css" rel="stylesheet">

	<!---Internal Fileupload css-->
	<link href="admin/assets/plugins/fileuploads/css/fileupload.css" rel="stylesheet" type="text/css" />

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
	<link href="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" />

	<!--- Style css-->
	<link href="admin/assets/css/style.css" rel="stylesheet">
	<link href="admin/assets/css/style-dark.css" rel="stylesheet">
	<link href="admin/assets/css/boxed.css" rel="stylesheet">
	<link href="admin/assets/css/dark-boxed.css" rel="stylesheet">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!---Skinmodes css-->
	<link href="admin/assets/css/skin-modes.css" rel="stylesheet" />

	<!--- Animations css-->
	<link href="admin/assets/css/animate.css" rel="stylesheet">
	<!-- <script src="admin/assets/redactor/jquery-1.9.0.min.js"></script> -->
	<script src="admin/assets/plugins/jquery/jquery.min.js"></script>
	


	<script type="text/javascript">
		AppHelper = {};
	</script>
	<!--



<script src="<?php echo base_url('form/app.all.js') ?>"></script>	-->
	<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
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
		<?php //echo view('__admin/component/top_bar', $params) ?>
		<?php echo view('components/adminheader') ?>


		<!-- nav -->
		<?php echo view('components/adminnavbar') ?>

		<!-- /main-header -->



		<!--Horizontal-main -->

		<!--Horizontal-main -->
		<!-- เมนู -->

		<div class="jumps-prevent" style="padding-top: 53.2969px;"></div>
		<div class="main-content horizontal-content">

			<script type="text/javascript" src="<?php echo base_url('form/jquery.form.js') ?>"></script>
			<script src="<?php echo base_url('form/sweetalert2@11.js') ?>"></script>
			<!-- content -->
			<div class="container">
				<div class="card">
					<div class="card-body">
						<div class="card  box-shadow-0">
							<div class="card-header">
								<h2><?php echo $title ?></h2>
								<p class="mb-2">กรุณากรอกข้อมูลให้ครบถ้วน และตรวจสอบความถูกต้อง</p>
							</div>
							<div class="card-body pt-0">


								<script type="text/javascript" src="formConfig/jquery.dataTables.min.js"></script>
								<!-- <script type="text/javascript" src="formConfig/custom.js"></script> -->
								<script type="text/javascript" src="admin/assets/redactor/redactor.min.js"></script>
								<!-- D:\MusionProject\fm91-news-portal\public\admin\assets\redactor -->
								<link href="formConfig/redactor.css" rel="stylesheet" />

								


								<?php echo $form ?>


								<script>
									$(function() {

										$('.dropify').dropify();

										
										//
										$('.save-form').click(function() {

											$('.hidden_submit').trigger('click');
										});

										$('.hidden_submit').click(function() {

											myForm = $(this).parents('form');


											var completed = '0%';

											$(myForm).ajaxForm({

												beforeSubmit: function(data, form, options) {

													var data = {};

													options["url"] = myForm.attr('action') + '';
													
													$( '.overlay' ).css({"display":"grid"});
												},
												complete: function(response) {
													
													$( '.overlay' ).css({"display":"none"});
													
													protect = 0;

													data = $.parseJSON(response.responseText);

													$('.text-danger').html('<i class="fa fa-check" style="color: green;"></i>');

													i = 0;
													for ( x in data.field ) {

														if ( i == 0 ) {

															$('[name="' + x + '"]').focus();
														}


														$('.text-danger[data-name="' + x + '"]').html(data.field[x]);

														++i;
													}

													if ( data.success == 0 ) {

														if ( data.message) {

															Swal.fire({
																title: data.message,
																text: '',
																icon: 'error',
																confirmButtonText: 'ตกลง'
															}).then(function() {

															});


														}

														return false;
													}
													
													

													///if ( data.success == 1) {
														
														
														Swal.fire({
															title: 'บันทึกสำเร็จ',
															text: '',
															icon: 'success',
															confirmButtonText: 'ตกลง'
														}).then(function() {
															// location.reload();
															window.location = data.redirect;
														});
													///}


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
			<!-- end contect -->
		</div>



		<!-- Footer opened -->

		<?php //echo view('admin/component/footer', $params) ?>


		<!-- Footer closed -->

	</div>
	<!-- End Page -->

	<!-- Back-to-top -->
	<a href="#top" id="back-to-top"><i class="fas fa-angle-double-up"></i></a>



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


	<!-- Custom Scroll bar Js-->
	<script src="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

	<!-- Horizontalmenu js-->
	<script src="admin/assets/plugins/horizontal-menu/horizontal-menu-2/horizontal-menu.js"></script>

	<!-- Sticky js -->
	<script src="admin/assets/js/sticky.js"></script>

	<!-- Right-sidebar js -->

	<script src="admin/assets/plugins/sidebar/sidebar-custom.js"></script>

	<!-- eva-icons js -->
	<script src="admin/assets/js/eva-icons.min.js"></script>

	<!-- custom js -->
	<script src="admin/assets/js/custom.js"></script>

	<!-- Internal form-elements js -->
	<script src="admin/assets/js/form-elements.js"></script>


	<!-- dropify min js -->
	<script src="admin/assets/js/dropify.min.js"></script>
	<script src="admin/assets/plugins/ckeditor5/packages/ckeditor5-build-classic/build/ckeditor.js"></script>
		<style>
	.overlay{
		
		display:none;
		position: fixed;
		width: 100%;
		height: 100%;
		background-color: #0000003b;
		z-index: 99999999999;
		align-items: center;
		justify-items: center;
	}
	
	
	</style>
	 
		<div class="overlay"><div class="show-load" style="
		font-size: 38px;
		color: white;
		">LOADING</div>

		</div>

		<script>
		$(function() {
			ClassicEditor.create( document.querySelector( 'textarea' ), {
			toolbar: {
				items: [
					'heading', '|',
					'alignment', '|',
					'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
					'link', '|',
					'bulletedList', 'numberedList', 'todoList',
					
					'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', '|',
					'code', 'codeBlock', '|',
					'insertTable', '|',
					'outdent', 'indent', '|',
					'uploadImage','mediaEmbed', 'blockQuote', '|',
					'undo', 'redo','SourceEditing','htmlEmbed'
				],
				shouldNotGroupWhenFull: true,
				plugins: [ 'HtmlEmbed' ],
				htmlEmbed: {
					showPreviews: true,
					sanitizeHtml: ( inputHtml ) => {
						// Strip unsafe elements and attributes, e.g.:
						// the `<script>` elements and `on*` attributes.
						const outputHtml = sanitize( inputHtml );

						return {
							html: outputHtml,
							// true or false depending on whether the sanitizer stripped anything.
							hasChanged: true
						};
					}
				}
			}
		} )
		.then( editor => {
			// editor.execute( 'ckfinder' );
			window.editor = editor;
		} )
		.catch( err => {
			console.error( err.stack );
		} );

			// $('textarea')
			// 	.redactor({
			// 		buttons: [
			// 			'formatting',
			// 			'|',
			// 			'alignleft',
			// 			'aligncenter',
			// 			'alignright',
			// 			'justify',
			// 			'|',
			// 			'bold',
			// 			'italic',
			// 			'underline',
			// 			'|',
			// 			'unorderedlist',
			// 			'orderedlist',
			// 			'|',
			// 			'image', 'video',
			// 			'link',
			// 			'|',
			// 			'html',
			// 		],
			// 		formattingTags: ['p', 'pre', 'h3', 'h4', 'h1', 'h2'],
			// 		minHeight: 350,
			// 		changeCallback: function(e) {
			// 			var editor = this.$editor.next('textarea');
			// 			if ($(editor).attr('required')) {
			// 				$('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $(editor).attr('name'));
			// 			}
			// 		},
			// 	});
		});
	</script>

</body>

</html>