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

/* end dragdrop */
</style>

<div class="container fit-height">
    <div class="row">
        <div class="col-sm-6 mt-4 mb-2">
            <h2>เพิ่มแบนเนอร์</h2>
            <a href="<?php echo front_link(15) ?>" class="text-dark"><i class="fas fa-chevron-left"></i> กลับ</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-xl-6 col-md-6 col-12">
            <!-- dragdrop -->
            <form id="upload-form-banner" enctype="multipart/form-data" action="<?php echo front_link(25); ?>">
                <div class="form-upload">
                    <div class="dropzone-wrapper">
                        <div class="dropzone-desc">
                            <p><b>รูปภาพตัวอย่าง</b></p>
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                        </div>
                        <input type="file" name="banner_file" id="banner_file" class="dropzone">
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
                <label for="" class="form-label">ชื่อแบนเนอร์</label>
                <input type="text" class="form-add-blog" name="banner_name" id="banner_name" value="" placeholder="">
            </div>
            <div class="form-blog">
                <label for="" class="form-label">ประเภทของไฟล์</label>
                <select name="file_type" id="type_file" class="form-add-blog">
                    <option value="image">รูปภาพ</option>
                    <option value="video">วิดิโอ</option>
                </select>
            </div>
            <div class="form-blog">
                <label for="" class="form-label">Description</label>
                <input type="text" class="form-add-blog" name="description" id="description" value="" placeholder="">
            </div>
            <div class="d-flex">
                <button type="reset" class="btn btn-secondary  mt-3 mb-2">เคลียร์</button>
                <button id="submitBtn" type="submit" class="btn btn-primary mt-3 mb-2">ตกลง</button>
            </div>
        </div>
        </form>
    </div>

    <!-- AJAX Form uplaods -->
    <script>
    $(document).ready(function() {
        $("#--upload-form-banner").submit(function(e) {
            e.preventDefault(); // prevent the form from submitting normally
            var formData = new FormData();
            $.ajax({
                url: "<?php echo front_link(25); ?>",
                type: "GET",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {

                    // handle success
                    Swal.fire({
                        title: data.message,
                        text: 'อัปโหลดรูปภาพสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function() {
                        //location.reload();
                        window.location = data
                            .redirect;
                    });

                    // document.write("Uplaods sucees")
                    // console.log(data);
                },
                error: function(error) {
                    // handle errors

                    Swal.fire({
                        title: data.message,
                        text: 'อัปโหลดรูปภาพล้มเหลว',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    }).then(function() {

                    });

                    // document.write("Uplaods fail")
                    // console.log(error);
                }
            });
        });
    });
    </script>












    <script src="./assets/js/ckeditor.js"></script>
    <script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
    </script>













    <!-- dragdrop file -->
    <script src="./assets/js/dragdropFile.js"></script>