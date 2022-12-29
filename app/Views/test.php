
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
	<base href="http://gocity" />
 
<script type="text/javascript">
    AppHelper = {};
    AppHelper.baseUrl = "<?php echo base_url() ?>";
    AppHelper.assetsDirectory = "<?php echo base_url( 'assets' ) ?>";
    AppHelper.settings = {};
    AppHelper.settings.firstDayOfWeek =0 || 0;
    AppHelper.settings.currencySymbol = " บาท";
    AppHelper.settings.currencyPosition = "right" || "left";
    AppHelper.settings.decimalSeparator = ".";
    AppHelper.settings.thousandSeparator = "";
    AppHelper.settings.noOfDecimals = ("2" == "0") ? 0 : 2;
    AppHelper.settings.displayLength = "10";
    AppHelper.settings.dateFormat = "d/m/Y";
    AppHelper.settings.timeFormat = "small";
    AppHelper.settings.scrollbar = "native";
    AppHelper.settings.enableRichTextEditor = "0";
    AppHelper.settings.notificationSoundVolume = "";
    AppHelper.settings.disableKeyboardShortcuts = "";
    

    //push notification
    AppHelper.settings.enablePushNotification = "";
    AppHelper.settings.userEnableWebNotification = "1";
    AppHelper.settings.userDisablePushNotification = "";
    AppHelper.settings.pusherKey = "";
    AppHelper.settings.pusherCluster = "";
  
    AppHelper.https = "0";

    AppHelper.settings.disableResponsiveDataTableForMobile = "";
    AppHelper.settings.disableResponsiveDataTable = "";

    AppHelper.csrfTokenName = "<?php echo get_token( 'name' ) ?>";
    AppHelper.csrfHash = "<?php echo get_token( 'val' ) ?>";

    AppHelper.settings.defaultThemeColor = "8bbd61";

    AppHelper.settings.timepickerMinutesInterval = 5;
    
    AppHelper.settings.weekends = "0";

    AppLanugage = {};
    AppLanugage.locale = "th";
    AppLanugage.localeLong = "th-TH";

    AppLanugage.days = ["\u0e27\u0e31\u0e19\u0e2d\u0e32\u0e17\u0e34\u0e15\u0e22\u0e4c","\u0e27\u0e31\u0e19\u0e08\u0e31\u0e19\u0e17\u0e23\u0e4c","\u0e27\u0e31\u0e19\u0e2d\u0e31\u0e07\u0e04\u0e32\u0e23","\u0e27\u0e31\u0e19\u0e1e\u0e38\u0e18","\u0e27\u0e31\u0e19\u0e1e\u0e24\u0e2b\u0e31\u0e2a\u0e1a\u0e14\u0e35","\u0e27\u0e31\u0e19\u0e28\u0e38\u0e01\u0e23\u0e4c","\u0e27\u0e31\u0e19\u0e40\u0e2a\u0e32\u0e23\u0e4c"];
    AppLanugage.daysShort = ["\u0e2d\u0e32\u0e17\u0e34\u0e15\u0e22\u0e4c","\u0e08\u0e31\u0e19\u0e17\u0e23\u0e4c","\u0e2d\u0e31\u0e07\u0e04\u0e32\u0e23","\u0e1e\u0e38\u0e18","\u0e1e\u0e24\u0e2b\u0e31\u0e2a","\u0e28\u0e38\u0e01\u0e23\u0e4c","\u0e40\u0e2a\u0e32\u0e23\u0e4c"];
    AppLanugage.daysMin = ["\u0e2d\u0e32","\u0e08","\u0e2d","\u0e1e","\u0e1e\u0e24","\u0e28","\u0e2a"];

    AppLanugage.months = ["\u0e21\u0e01\u0e23\u0e32\u0e04\u0e21","\u0e01\u0e38\u0e21\u0e20\u0e32\u0e1e\u0e31\u0e19\u0e18\u0e4c","\u0e21\u0e35\u0e19\u0e32\u0e04\u0e21","\u0e40\u0e21\u0e29\u0e32\u0e22\u0e19","\u0e1e\u0e24\u0e29\u0e20\u0e32\u0e04\u0e21","\u0e21\u0e34\u0e16\u0e38\u0e19\u0e32\u0e22\u0e19","\u0e01\u0e23\u0e01\u0e0e\u0e32\u0e04\u0e21","\u0e2a\u0e34\u0e07\u0e2b\u0e32\u0e04\u0e21","\u0e01\u0e31\u0e19\u0e22\u0e32\u0e22\u0e19","\u0e15\u0e38\u0e25\u0e32\u0e04\u0e21","\u0e1e\u0e24\u0e28\u0e08\u0e34\u0e01\u0e32\u0e22\u0e19","\u0e18\u0e31\u0e19\u0e27\u0e32\u0e04\u0e21"];
    AppLanugage.monthsShort = ["\u0e21.\u0e04.","\u0e01.\u0e1e.","\u0e21\u0e35.\u0e04.","\u0e40\u0e21.\u0e22.","\u0e1e.\u0e04.","\u0e21\u0e34.\u0e22.","\u0e01.\u0e04.","\u0e2a.\u0e04.","\u0e01.\u0e22.","\u0e15.\u0e04.","\u0e1e.\u0e22.","\u0e18.\u0e04."];

    AppLanugage.today = "วันนี้";
    AppLanugage.yesterday = "เมื่อวาน";
    AppLanugage.tomorrow = "พรุ่งนี้";

    AppLanugage.search = "ค้นหา";
    AppLanugage.noRecordFound = "ไม่พบข้อมูล";
    AppLanugage.print = "พิมพ์";
    AppLanugage.excel = "Excel";
    AppLanugage.printButtonTooltip = "กด escape เมื่อเสร็จสิ้น";

    AppLanugage.fileUploadInstruction = "ลากและวางเอกสารที่นี่ <br /> (หรือคลิกเพื่อเรียกดู...)";
    AppLanugage.fileNameTooLong = "ชื่อไฟล์ยาวเกินไป";

    AppLanugage.custom = "กำหนดเอง";
    AppLanugage.clear = "ล้าง";

    AppLanugage.total = "รวม";
    AppLanugage.totalOfAllPages = "จำนวนหน้าทั้งหมด";

    AppLanugage.all = "ทั้งหมด";

    AppLanugage.preview_next_key = "ต่อไป (ปุ่มลูกศรขวา)";
    AppLanugage.preview_previous_key = "ก่อนหน้า (ปุ่มลูกศรซ้าย)";
    
    AppLanugage.filters = "ตัวกรอง";

</script>

    <link rel='stylesheet' type='text/css' href='test/bootstrap.min.css?v=2.6.1' />
	
	<link rel='stylesheet' type='text/css' href='test/font-awesome.min.css?v=2.6.1' />
	
	<link rel='stylesheet' type='text/css' href='test/jquery.dataTables.min.css?v=2.6.1' />
	
	<link rel='stylesheet' type='text/css' href='test/select2.css?v=2.6.1' />
	
	
	<link rel='stylesheet' type='text/css' href='test/select2-bootstrap.min.css?v=2.6.1' />
	
	<link rel='stylesheet' type='text/css' href='test/app.all.css?v=2.6.1' />
	
	<link rel='stylesheet' type='text/css' href='test/grids.css?v=2.6.1' />
	
	<link rel='stylesheet' type='text/css' href='test/custom-style.css?v=2.6.1' />
	
	
	<script type='text/javascript'  src='test/app.all.js?v=2.6.1'></script>
	

        <script>

        var data = {};
        data[AppHelper.csrfTokenName] = AppHelper.csrfHash;
        $.ajaxSetup({
            data: data
        });
    </script>
        
</head>    


<body>

     
 

<!-------->
<div id="notes-dropzone" class="post-dropzone">

	<div class="post-file-dropzone-scrollbar hide">
		<div class="post-file-previews clearfix b-t"> 
			<div class="post-file-upload-row dz-image-preview dz-success dz-complete pull-left">
				<div class="preview" style="width:85px;">
					<img data-dz-thumbnail class="upload-thumbnail-sm" />
					<span data-dz-remove="" class="delete">×</span>
					<div class="progress progress-striped upload-progress-sm active m0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
						<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
					</div>
				</div>
			</div>
		</div>
	</div>    


	<button class="btn btn-default upload-file-button pull-left btn-sm round" type="button" style="color:#7988a2"><i class="fa fa-camera"></i> อัพโหลดไฟล์</button>
      
    
</div>
<div class="show-files"></div>

 
 
<script type="text/javascript">


attachDropzoneWithForm = function (dropzoneTarget, uploadUrl, validationUrl, options) {
    var $dropzonePreviewArea = $(dropzoneTarget),
            $dropzonePreviewScrollbar = $dropzonePreviewArea.find(".post-file-dropzone-scrollbar"),
            $previews = $dropzonePreviewArea.find(".post-file-previews"),
            $postFileUploadRow = $dropzonePreviewArea.find(".post-file-upload-row"),
            $uploadFileButton = $dropzonePreviewArea.find(".upload-file-button"),
            $submitButton = $dropzonePreviewArea.find("button[type=submit]"),
            previewsContainer = getRandomAlphabet(15),
            postFileUploadRowId = getRandomAlphabet(15),
            uploadFileButtonId = getRandomAlphabet(15);

    //set random id with the previws 
    $previews.attr("id", previewsContainer);
    $postFileUploadRow.attr("id", postFileUploadRowId);
    $uploadFileButton.attr("id", uploadFileButtonId);


    //get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#" + postFileUploadRowId);
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    if (!options)
        options = {};

    var postFilesDropzone = new Dropzone(dropzoneTarget, {
        url: uploadUrl,
        thumbnailWidth: 800,
        thumbnailHeight: 800,
        parallelUploads: 20,
        maxFilesize: 3000,
        previewTemplate: previewTemplate,
        dictDefaultMessage: AppLanugage.fileUploadInstruction,
        autoQueue: true,
        previewsContainer: "#" + previewsContainer,
        clickable: "#" + uploadFileButtonId,
        maxFiles: options.maxFiles ? options.maxFiles : 1000,
        sending: function (file, xhr, formData) {
            formData.append(AppHelper.csrfTokenName, AppHelper.csrfHash);
        },
        init: function () {
            this.on("maxfilesexceeded", function (file) {
                this.removeAllFiles();
                this.addFile(file);
            });
        },
        accept: function (file, done) {
            if (file.name.length > 200) {
                done(AppLanugage.fileNameTooLong);
            }

            $dropzonePreviewScrollbar.removeClass("hide");
            initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

            $dropzonePreviewScrollbar.parent().removeClass("hide");
            $dropzonePreviewArea.find("textarea").focus();

            var postData = {file_name: file.name, file_size: file.size};

            //validate the file
            $.ajax({
                url: validationUrl,
                data: postData,
                cache: false,
                type: 'POST',
                dataType: "json",
                success: function (response) {
					
					$( '.show-files' ).append( '<input type="text" name="file_names[]" value="' + file.name + '" />\n\
                            <input type="hidden" name="file_sizes[]" value="' + file.size + '" />');
					
                    if (response.success) {
                     
                        done();
                    } else {
                        appAlert.error(response.message);
                        $(file.previewTemplate).find("input").remove();
                        done(response.message);
                    }
                }
            });
        },
        processing: function () {
            $submitButton.prop("disabled", true);
            appLoader.show();
        },
        queuecomplete: function () {
            $submitButton.prop("disabled", false);
            appLoader.hide();
        },
        reset: function (file) {
            $dropzonePreviewScrollbar.addClass("hide");
        },
        fallback: function () {
            //add custom fallback;
            $("body").addClass("dropzone-disabled");

            $uploadFileButton.click(function () {
                //fallback for old browser
                $(this).html("<i class='fa fa-camera'></i> Add more");

                $dropzonePreviewScrollbar.removeClass("hide");
                initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

                $dropzonePreviewScrollbar.parent().removeClass("hide");
                $previews.prepend("<div class='clearfix p5 file-row'><button type='button' class='btn btn-xs btn-danger pull-left mr10 remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>");

            });
            $previews.on("click", ".remove-file", function () {
                $(this).parent().remove();
            });
        },
        success: function (file) {
            setTimeout(function () {
                $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
            }, 1000);
        }
    });

    return postFilesDropzone;
};




    $(document).ready(function () {
		
		
		
		
		
        var uploadUrl = "http://gocity/Products_Table/bb";
        var validationUri = "http://gocity/Products_Table/bb";

        var dropzone = attachDropzoneWithForm("#notes-dropzone", uploadUrl, validationUri);

 
    });
</script>    


</body>
</html>