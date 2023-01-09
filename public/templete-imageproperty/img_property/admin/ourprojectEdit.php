<!DOCTYPE html>
<html lang="en">

<!-- head -->
<?php include('./components/header.php') ?>
<!-- head -->

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

<body>

    <!-- nav -->
    <?php include('./components/navbar.php') ?>
    <!-- nav -->

    <div class="container">
        <div class="row">
            <div class="col-sm-6 mt-4 mb-2">
                <h2>แก้ไขโครงการ</h2>
                <a href="ourprojectManage.php" class="text-dark"><i class="fas fa-chevron-left"></i> กลับ</a>
            </div>
        </div>
        <form action="">
            <div class="row">
                <div class="col-lg-6 col-xl-6 col-md-6 col-12">
                    <!-- dragdrop -->
                    <div class="form-upload mt-3">
                        <div class="dropzone-wrapper">
                            <div class="dropzone-desc">
                                <p><b>รูปภาพปก</b></p>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                <?php /* <p class="text-danger" style="display: none;" id="text-alert-image">(ถ้าหากต้องการเปลี่ยนภาพให้เลือกอัพโหลดไฟล์อีกครั้ง)</p> */ ?>
                            </div>
                            <input type="file" name="img_logo" class="dropzone">
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
                        <label for="" class="form-label">ชื่อโครงการ</label>
                        <input type="text" class="form-add-blog" name="headerpost" value="" placeholder="">
                    </div>
                    <div class="form-blog">
                        <label for="" class="form-label">ราคา</label>
                        <input type="number" class="form-add-blog" name="postname" value="" placeholder="">
                    </div>
                    <div class="form-blog">
                        <label for="" class="form-label">โซน</label>
                        <input type="text" class="form-add-blog" name="date" value="" placeholder="">
                    </div>
                    <div class="form-blog">
                        <label for="" class="form-label">ลิ้งวิดีโอ Banner</label>
                        <input type="text" class="form-add-blog" name="date" value="" placeholder="">
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
                    <!-- <div class="">
                        <button type="reset" class="btn btn-secondary mt-3 mb-2">เคลียร์</button>
                        <button type="submit" class="btn btn-primary mt-3 mb-2">ตกลง</button>
                    </div> -->
                </div>
                <div class="row">
                    <div class="my-3">
                        <h3>ข้อมูลเบื้องต้น</h3>
                    </div>
                    <div class="col-12">
                        <div id="editor"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="my-3">
                        <h3>แบบบ้าน</h3>
                    </div>
                    <div class="col-12">
                        <div id="editor-1"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="my-3">
                        <h3>สิ่งอำนวยความสะดวก</h3>
                    </div>
                    <div class="col-12">
                        <div id="editor-2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="my-3">
                        <h3>อัลบัม</h3>
                    </div>
                    <div class="col-lg-6 col-xl-6 col-md-6 col-12">
                        <!-- dragdrop -->
                        <div class="form-upload">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <p><b>รูปภาพปก</b></p>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone">
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
                        <!-- dragdrop -->
                        <div class="form-upload">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <p><b>รูปภาพปก</b></p>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone">
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
                        <!-- dragdrop -->
                        <div class="form-upload">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <p><b>รูปภาพปก</b></p>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone">
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
                        <!-- dragdrop -->
                        <div class="form-upload">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <p><b>รูปภาพปก</b></p>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone">
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
                        <!-- dragdrop -->
                        <div class="form-upload">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <p><b>รูปภาพปก</b></p>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone">
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
                        <!-- dragdrop -->
                        <div class="form-upload">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <p><b>รูปภาพปก</b></p>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone">
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
                </div>
                <div class="row">
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
                        <div id="editor-3"></div>
                    </div>
                </div>
            </div>
            <div class="">
                <button type="reset" class="btn btn-secondary mt-3 mb-2">เคลียร์</button>
                <button type="submit" class="btn btn-primary mt-3 mb-2">ตกลง</button>
            </div>
        </form>
    </div>

    <script src="./assets/js/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
        ClassicEditor
            .create(document.querySelector('#editor-1'))
            .catch(error => {
                console.error(error);
            });
        ClassicEditor
            .create(document.querySelector('#editor-2'))
            .catch(error => {
                console.error(error);
            });
        ClassicEditor
            .create(document.querySelector('#editor-3'))
            .catch(error => {
                console.error(error);
            });
    </script>

    <!-- dragdrop file -->
    <script src="./assets/js/dragdropFile.js"></script>

    <!-- footer -->
    <?php include('./components/footer.php') ?>
    <!-- footer -->

</body>

</html>