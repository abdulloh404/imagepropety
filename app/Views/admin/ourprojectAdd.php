
<link href="admin/assets/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" crossorigin="anonymous">
<link href="admin/assets/bootstrap-fileinput/themes/explorer-fa5/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">

<style>
    /* dragdrop */
    .box {
        position: relative;
        background: #ffffff;
        width: 100%;
    }

    .box-header {
        color: #444;
        display: block;
        padding: 10px;
        position: relative;
        border-bottom: 1px solid #f4f4f4;
        margin-bottom: 10px;
    }

    .box-tools {
        position: absolute;
        right: 10px;
        top: 5px;
    }

    .dropzone-wrapper {
        border: 2px dashed #91b0b3;
        color: #92b0b3;
        position: relative;
        height: 250px;
        width: 100%;
    }

    .dropzone-desc {
        position: absolute;
        margin: 0 auto;
        left: 0;
        right: 0;
        text-align: center;
        width: 95%;
        top: 60px;
        font-size: 16px;
    }

    .dropzone,
    .dropzone:focus {
        position: absolute;
        outline: none !important;
        width: 100%;
        height: 250px;
        cursor: pointer;
        opacity: 0;
    }

    .dropzone-wrapper:hover,
    .dropzone-wrapper.dragover {
        background: #ecf0f5;
    }

    .preview-zone {
        text-align: center;
        width: 100%;
    }

    .preview-zone .box {
        box-shadow: none;
        border-radius: 0;
        margin-bottom: 0;
    }

    .form-upload {
        display: flex;
        flex-direction: column;
    }

    .box-body>img {
        width: 90%;
        max-height: 350px;
    }

    .form-blog {
        display: flex;
        flex-direction: column;
        margin: 10px 0px;
    }

    .form-add-blog {
        padding-left: 10px;
        background: #FFFFFF;
        border: 1px solid rgba(0, 0, 0, 0.7);
        border-radius: 5px;
        height: 35px;
        width: 100%;
    }

    .ck-content {
        height: 350px;
        overflow-y: scroll;
    }

    @media (max-width: 450px) {
        .dropzone-wrapper {
            border: 2px dashed #91b0b3;
            color: #92b0b3;
            position: relative;
            height: 150px;
            width: 100%;
        }

        .preview-zone {
            text-align: center;
            width: 100%;
        }
    }

    .collapes-add {
        display: flex;
        flex-direction: column;
        margin: 10px 0px;
    }

    .collapes-add>input[type=file] {
        padding: 0px;
        background: #FFFFFF;
        border: 1px solid transparent;
        border-radius: 5px;
        height: 35px;
        width: 100%;
    }

    .form-add {
        padding-left: 10px;
        background: #FFFFFF;
        border: 1px solid rgba(0, 0, 0, 0.7);
        border-radius: 5px;
        height: 35px;
        width: 100%;
    }

    .btn-addAccountant {
        background-color: #007299;
        border: 1px solid #007299;
        border-radius: 3px;
        color: #fff;
        padding: 5px 10px;
    }

    .btn-add-blog {
        background-color: #007299;
        border: 1px solid #007299;
        border-radius: 3px;
        color: #fff;
        padding: 5px 10px;
    }

    .btn-add-blog:hover {
        background-color: #fff;
        border: 1px solid #007299;
        border-radius: 3px;
        color: #007299;
        padding: 5px 10px;
    }

    /* end dragdrop */
</style>

    <div class="container">
        <div class="row">
            <div class="col-sm-6 mt-4 mb-2">
                <h2>เพิ่มโครงการ</h2>
                <a href="<?php echo front_link(16) ?>" class="text-dark"><i class="fas fa-chevron-left"></i> กลับ</a>
            </div>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <?php echo $secret ?>
            <div class="row">
                <div class="col-lg-6 col-xl-6 col-md-6 col-12">
                    <!-- dragdrop -->
                    <div class="form-upload mt-3">
                        <div class="dropzone-wrapper">
                            <div class="dropzone-desc">
                                <p><b>รูปภาพปก</b></p>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                
                            </div>
                            <input type="file" name="img_logo" id="img_logo" class="dropzone">
                        </div>
                        <div class="preview-zone hidden">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <div><b>แสดงรูปตัวอย่าง</b></div>
                                    <div class="box-tools pull-right"></div>
                                </div>
                                <div class="box-body"></div>
                            </div>
                        </div>
                    </div>
                    <!-- dragdrop -->
                </div>
                <div class="col-lg-6 col-xl-6 col-md-6 col-12">
                    <div class="form-blog">
                        <label for="" class="form-label"><span class="text-danger" data-name="name">*</span>ชื่อโครงการ</label>
                        <input type="text" class="form-add-blog" name="name" value="" placeholder="" required>
                    </div>
                    <div class="form-blog">
                        <label for="" class="form-label"><span class="text-danger" data-name="prize">*</span>ราคา</label>
                        <input type="number" class="form-add-blog" name="prize" value="" placeholder="" required>
                    </div>
                    <div class="form-blog">
                        <label for="" class="form-label"><span class="text-danger" data-name="zone">*</span>โซน</label>
                        <input type="text" class="form-add-blog" name="zone" value="" placeholder="" required>
                    </div>
                    <div class="form-blog">
                        <label for="" class="form-label"><span class="text-danger" data-name="bannerurl">*</span>ลิ้งวิดีโอ Banner</label>
                        <input type="text" class="form-add-blog" name="bannerurl" value="" placeholder="" required>
                    </div>
                    <div class="d-flex">
                        <div class="me-2"><span>เลือกป้ายกำกับ</span></div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                                NEW PROJECT
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="my-3">
                        <h3>ข้อมูลเบื้องต้น</h3>
                    </div>
                    <div class="col-12">
                        <textarea name="detail" display="none" class="form-control" rows="10"></textarea>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="my-3">
                        <h3>แบบบ้าน</h3>
                    </div>
                    <div class="col-12">
                        <textarea name="plan" display="none" class="form-control" rows="10"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="my-3">
                        <h3>สิ่งอำนวยความสะดวก</h3>
                    </div>
                    <div class="col-12">
                        <textarea name="equipment" display="none" class="form-control" rows="10"></textarea>
                    </div>
                </div> -->
                <div class="row">
                    <div class="my-3">
                        <h3>อัลบัม</h3>
                    </div>
                    <input type="file" name="gallary[]" id="inp" multiple>                    

                </div>
                <!-- <div class="row">
                    <div class="mt-3 mb-2 d-flex align-items-center">
                        <h3 class="m-0">สถานที่ใกล้เคียง</h3>
                        <div class="ms-2">
                            <button class="btn btn-success"><i class="fas fa-plus-circle"></i> &nbsp;กดเพื่อเพิ่มหัวข้อสถานที่</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="text" class="col col-form-label">หัวข้อสถานที่</label>
                            <input type="text" class="form-control" id="text">
                        </div>
                        <textarea name="nearby_detail" display="none" class="form-control" rows="10"></textarea>
                    </div>
                </div> -->
            </div>
            <div class="">
                <button type="reset" class="btn btn-secondary mt-3 mb-2">เคลียร์</button>
                <button type="submit" class="btn btn-primary mt-3 mb-2 submit">ตกลง</button>
            </div>
        </form>
    </div>

    <script src="page/admin-assets/js/ckeditor.js"></script>
    <script src="admin/assets/bootstrap-fileinput/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="admin/assets/bootstrap-fileinput/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="admin/assets/bootstrap-fileinput/js/plugins/piexif.js" type="text/javascript"></script>
	<script src="admin/assets/bootstrap-fileinput/js/plugins/sortable.js" type="text/javascript"></script>			
	<script src="admin/assets/bootstrap-fileinput/js/fileinput.min.js" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	
	<script src="admin/assets/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
	<script src="admin/assets/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
	<script src="admin/assets/bootstrap-fileinput/themes/fa5/theme.js" type="text/javascript"></script>
	<script src="admin/assets/bootstrap-fileinput/themes/explorer-fa5/theme.js" type="text/javascript"></script>

    <script>
        $("#inp").fileinput({
            /* uploadUrl: '<?php //echo front_link( $id, 'saveMainProduct/'. $parent_id,  array(), false )?>',
            uploadExtraData: {
                '<?php //echo get_token('name')?>': '<?php //echo get_token('val')?>', 
            }, */
            // enableResumableUpload: true,
            resizeImage: true,
            maxImageWidth: 500,
            maxImageHeight: 500,
            resizePreference: 'width',
            resizeImageQuality: 0.75,
            showRotate: true,
            overwriteInitial: false,					
            showRemove: false,
            browseOnZoneClick: false ,
            showUpload: true,
            uploadClass: "mainShows btn btn-default btn-secondary",
            allowedFileExtensions: ['jpg', 'jpeg', 'png'] ,
            rotatableFileExtensions: [],
            initialPreviewAsData: true,
            deleteExtraData: {
                "<?php echo get_token('name')?>": "<?php echo get_token('val')?>",
            },
        
            initialPreview: [
                <?php echo @$img1?>
            ],
            initialPreviewConfig: [
                <?php echo @$img2?>
            ],
            
            
        }).on('filesorted', function(e, params) {
            $.ajax({
                url : "<?php echo front_link($params['id'],'sortImageProduct' )?>",
                type: "get",
                data: {
                    sort: params.stack
                }
            })	
        }).on('filebeforedelete', function() {
            var aborted = !window.confirm('Are you sure you want to delete this file?');
            if (aborted) {
                window.alert('File deletion was aborted! ');
            };
            return aborted;
        });

        $.each($( 'textarea' ),function(index,value) {
			ClassicEditor.create( value, {
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
		});
    </script>
    
        <script type="text/javascript" src="page/assets/js/jquery.form.js"></script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
	    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
        <script>
			
				$('.submit').click(function() {

					myForm = $(this).parents('form');
                    /* img = $('#img_logo')[0].files; 
                    console.log(img) */

					var completed = '0%';

					$(myForm).ajaxForm({

						beforeSubmit: function(data, form, options) {
                            //console.log(data)
							var data = {};
                            

							options["url"] = myForm.attr('action');
						},
						complete: function(response) {
                            console.log(response);

							protect = 0;

							data = $.parseJSON(response.responseText);

							//	$('.text-danger').html('<i class="fa fa-check" style="color: green;"></i>');

							if (data.token_val)
								$('[name="<?php echo get_token('name') ?>"]').val(data.token_val);

							if (data.success == 0) {


								Swal.fire({
									title: data.message,
									text: '',
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
								title: data.message,
								text: '',
								icon: 'success',
								confirmButtonText: 'ตกลง'
							}).then(function() {
								window.location = data.redirect;
							});





						}
					});
				});


		</script>

    <!-- dragdrop file -->
    <script src="page/admin-assets/js/dragdropFile.js"></script>