<!DOCTYPE html>
<html lang="en">
<?php include("./components/header.php") ?>

<style>
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

    .btn-paynow {
        background-color: #70d940;
        color: #fff;
        border: 1px solid #70d940;
    }

    .btn-paynow:hover {
        background-color: #00d940;
        color: #fff;
        border: 1px solid #00d940;
    }

    .card-header {
        border-left: 2px solid #70d940;
        border-radius: 0;
    }

    .card-header:first-child {
        border-radius: unset;
    }
</style>

<body>
    <div class="container-fluid my-5 fit-height">
        <div class="row">
            <div class="col-2 p-5 bg-gray-900"></div>
            <div class="col-md-10 col-12">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                        <div class="position-relative p-5 shadow-lg rounded-3 mb-5 bg-white">
                            <div class="position-absolute top-0 start-50 translate-middle px-4 py-2 rounded-5 text-white" style="background-color: #70d940;"><span>Startup</span>
                            </div>
                            <div class="mb-3">
                                <div class="h4">$99 / ต่อเดือน</div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    5 ผู้ใช้
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    5 ลูกค้า
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    5 ผู้ขาย
                                </label>
                            </div>
                        </div>
                        <div class="position-relative p-5 shadow-lg rounded-3 mb-3 bg-white">
                            <div class="position-absolute top-0 start-50 translate-middle px-4 py-2 rounded-5 text-white" style="background-color: #70d940;"><span style="font-size: 10px;">Scan เพื่อชำระเงิน</span>
                            </div>
                            <div class="mb-3">
                                <div class="h4">$99 / ต่อเดือน</div>
                            </div>
                            <div class="main-qr-code">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/QR_code_for_mobile_English_Wikipedia.svg/1024px-QR_code_for_mobile_English_Wikipedia.svg.png" alt="qr-code" width="100%">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8 col-md-8 col-12">
                        <div class="py-4 px-0 shadow-lg rounded-3 mb-4 bg-white">
                            <div class="card mb-3 border-0">
                                <div class="card-header px-3 py-2 bg-white">เลขที่บัญชี</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">ชื่อบัญชี : บริษัท มิวชั่น จำกัด <img src="./assets/img/logo-scb.png" alt="logo-scb" width="4%" class="rounded-1"> ธนาคารไทยพาณิชย</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="เลขที่บัญชี : 191-215128-6" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form action="" method="POST">
                            <div class="py-4 px-0 shadow-lg rounded-3 mb-3 bg-white">
                                <div class="card mb-3 border-0">
                                    <div class="card-header px-3 py-2 bg-white">แบบฟอร์มแจ้งชำระเงินค่าบริการ</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">ชื่อ</label>
                                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">นามสกุล</label>
                                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">หมายเลขโทรศัพท์</label>
                                                <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">ที่อยู่</label>
                                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">ตำบล/แขวง</label>
                                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">อำเภอ/เขต</label>
                                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">จังหวัด</label>
                                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">รหัสไปรษณีย์</label>
                                                <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">วันที่ชำระเงิน</label>
                                                <input type="date" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">วันที่ชำระเงิน</label>
                                                <input type="time" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">จำนวนเงิน</label>
                                                <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <!-- dragdrop -->
                                <div class="form-upload">
                                    <div class="dropzone-wrapper">
                                        <div class="dropzone-desc">
                                            <p><b>รูปภาพปก</b></p>
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>ขนาดไฟล์สูงสุด : 1 MB<br>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                            <p class="text-danger" style="display: none;" id="text-alert-image">(ถ้าหากต้องการเปลี่ยนภาพให้เลือกอัพโหลดไฟล์อีกครั้ง)</p>
                                        </div>
                                        <input type="file" name="img_logo" class="dropzone">
                                    </div>
                                    <div class="preview-zone hidden">
                                        <div class="box box-solid">
                                            <div class="box-header with-border">
                                                <div class="mt-2"><b>แสดงรูปตัวอย่าง</b></div>
                                                <div class="box-tools pull-right"></div>
                                            </div>
                                            <div class="box-body shadow-lg bg-white"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- dragdrop -->
                            </div>
                            <div class="d-grid col-3 mx-auto">
                                <button type="submit" class="btn btn-paynow">จ่ายตอนนี้</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- dragdrop file -->
    <script>
        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var htmlPreview =
                        '<img src="' + e.target.result + '" />' +
                        '<p>' + input.files[0].name + '</p>';
                    var wrapperZone = $(input).parent();
                    var previewZone = $(input).parent().parent().find('.preview-zone');
                    var boxZone = $(input).parent().parent().find('.preview-zone').find('.box').find('.box-body');

                    wrapperZone.removeClass('dragover');
                    previewZone.removeClass('hidden');
                    boxZone.empty();
                    boxZone.append(htmlPreview);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".dropzone").change(function() {
            readFile(this);
            $("#text-alert-image").show();
        });

        $('.dropzone-wrapper').on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });

        $('.dropzone-wrapper').on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
    </script>
</body>

</html>