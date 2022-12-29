<style>
    #panel {
        display: block;
    }

    .cookies-popup {
        backdrop-filter: brightness(0.5);
        padding: .5rem .75rem;
        width: 15rem;
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 99;
        background: #00000066;
    }

    .cookies-popup-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }

    .cookies-popup-head>button {
        background: transparent;
        border: 1px solid transparent;
        color: #fff;
    }

    .cookies-popup-detail {
        display: flex;
        align-items: center;
        color: #fff;
    }

    .cookies-popup-btn {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

    .cookies-popup-btn-config {
        background: #fff;
        border: 1px solid #325160;
        border-radius: 5px;
        color: #325160;
        text-align: center;
        padding: 5px 0px;
        width: 50%;
        margin-right: 5px;
        font-size: .75rem;
    }

    .cookies-popup-btn-accept {
        background: #2E69B6;
        border: 1px solid #2E69B6;
        border-radius: 5px;
        color: #fff;
        text-align: center;
        padding: 5px 0px;
        width: 50%;
        margin-left: 5px;
        font-size: .75rem;
    }

    .btn-closeModal {
        background: transparent;
        border: 1px solid transparent;
        color: #000;
    }

    .footer-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0px;
    }

    .cookies-btn-accept {
        background: #fff;
        border: 1px solid #325160;
        border-radius: 5px;
        color: #325160;
        text-align: center;
        padding: 5px 0px;
        width: 30%;
        margin-right: 5px;
    }

    .cookies-btn-accept:hover {
        background: #325160;
        border: 1px solid #325160;
        color: #fff;
    }

    .cookies-btn-acceptAll {
        background: #2E69B6;
        border: 1px solid #2E69B6;
        border-radius: 5px;
        color: #fff;
        text-align: center;
        padding: 5px 0px;
        width: 30%;
        margin-left: 5px;
    }

    .cookies-btn-acceptAll:hover {
        background: #2E69e6;
        border: 1px solid #2E69e6;
    }

    .cookies-btn-cancel {
        background: #850000;
        border: 1px solid #850000;
        border-radius: 5px;
        color: #fff;
        text-align: center;
        padding: 5px 0px;
        width: 30%;
        margin-left: 5px;
    }

    .cookies-btn-cancel:hover {
        background: #820000;
        border: 1px solid #820000;
        color: #fff;
    }

    .form-switch-accordion {
        position: absolute;
        top: 14px;
        right: 50px;
        z-index: 9;
    }

    .button-detail {
        color: #0E3F54;
    }

    .accordion {
        --bs-accordion-color: #000;
        --bs-accordion-bg: #fff;
        --bs-accordion-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, border-radius 0.15s ease;
        --bs-accordion-border-color: #EEEEEE;
        --bs-accordion-border-width: 0px;
        --bs-accordion-border-radius: 0.375rem;
        --bs-accordion-inner-border-radius: calc(0.375rem - 1px);
        --bs-accordion-btn-padding-x: 1.25rem;
        --bs-accordion-btn-padding-y: 1rem;
        --bs-accordion-btn-color: var(--bs-body-color);
        --bs-accordion-btn-bg: var(--bs-accordion-bg);
        --bs-accordion-btn-icon: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='var%28--bs-body-color%29'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e);
        --bs-accordion-btn-icon-width: 1.25rem;
        --bs-accordion-btn-icon-transform: rotate(-180deg);
        --bs-accordion-btn-icon-transition: transform 0.2s ease-in-out;
        --bs-accordion-btn-active-icon: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230c63e4'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e);
        --bs-accordion-btn-focus-border-color: #86b7fe;
        --bs-accordion-btn-focus-box-shadow: 0 0 0 0.25remrgba(13, 110, 253, 0.25);
        --bs-accordion-body-padding-x: 1.25rem;
        --bs-accordion-body-padding-y: 1rem;
        --bs-accordion-active-color: #0E3F54;
        --bs-accordion-active-bg: #fff;
    }

    .accordion-item {
        color: var(--bs-accordion-color);
        background-color: var(--bs-accordion-bg);
        border: unset;
        border-bottom: 1px solid #EEEEEE;
        position: relative;
    }

    .accordion-body {
        padding: var(--bs-accordion-body-padding-y) var(--bs-accordion-body-padding-x);
        background: #fff;
    }

    .form-check-input:checked {
        background-color: #0E3F54;
        border-color: #0E3F54;
        background-position: right center;
    }

    @media screen and (max-width: 576px) {

        .cookies-btn-acceptAll {
            background: #2E69B6;
            border: 1px solid #2E69B6;
            border-radius: 5px;
            color: #fff;
            text-align: center;
            padding: 5px 0px;
            width: 35%;
            margin-left: 5px;
        }

        .cookies-btn-cancel {
            background: #850000;
            border: 1px solid #850000;
            border-radius: 5px;
            color: #fff;
            text-align: center;
            padding: 5px 0px;
            width: 30%;
            margin-left: 5px;
        }
    }
</style>

<div class="cookies-popup" id="panel">
    <div class="cookies-popup-head">
        <h1 class="m-0" style="font-size: .875rem;">เว็บไซต์ใช้คุกกี้</h1>
        <button type="button" onclick="dismissCookies()"><i class="fas fa-times"></i></button>
    </div>
    <div class="cookies-popup-detail">
        <span style="font-size: .625rem">เพื่อสำรวจและวิเคราะห์การใช้งานเว็บไซต์ให้มีประสิทธิภาพมากยิ่งขึ้นโดยท่านสามารถศึกษารายละเอียดการใช้คุกกี้ได้จาก <a href="cookie-policy.php" class="text-white"><u>นโยบายการใช้คุกกี้</u></a></span>
    </div>
    <div class="cookies-popup-btn">
        <button type="button" class="cookies-popup-btn-config" data-bs-toggle="modal" data-bs-target="#cookiesConfig">ตั้งค่าคุกกี้</button>
        <button type="button" class="cookies-popup-btn-accept" onclick="dismissCookies()">อนุญาติทั้งหมด</button>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="cookiesConfig" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <img src="./assets/img/logo.png" style="width: 150px;" alt="">
                <button type="button" class="btn-closeModal" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="d-grid mb-2">
                    <span><b>ศูนย์การตั้งค่าตามความชอบส่วนตัว</b></span>
                    <span class="mt-2">เมื่อท่านเข้าชมเว็บไซต์ของ Image Property อาจมีการจัดเก็บหรือดึงข้อมูลจากเบราว์เซอร์ของท่านในรูปแบบของคุกกี้ ข้อมูลนี้อาจเป็นข้อมูลเกี่ยวกับท่าน การตั้งค่าของท่าน อุปกรณ์ของท่าน ซึ่งจะถูกใช้เพื่อทำให้เว็บไซต์สามารถทำงานได้ตามที่ท่านต้องการ ซึ่งมักเป็นข้อมูลที่ไม่ได้ระบุตัวตนของท่านโดยตรง แต่ช่วยให้ท่านสามารถใช้งานเว็บไซต์แบบเฉพาะสำหรับท่านได้มากยิ่งขึ้น
                        ทั้งนี้ Image Property เคารพในสิทธิความเป็นส่วนตัวของท่าน โดยท่านสามารถเลือกปฏิเสธไม่อนุญาตให้คุกกี้บางประเภททำงานได้ ซึ่งท่านสามารถคลิกที่หัวข้อแต่ละประเภท เพื่อเรียนรู้เพิ่มเติมและปรับเปลี่ยนการตั้งค่าได้
                        อย่างไรก็ตาม การเลือกปิดการใช้งานคุกกี้บางประเภทอาจส่งผลกระทบต่อการใช้งานเว็บไซต์และบริการที่ Image Property เสนอให้แก่ท่านได้</span>
                </div>
                <span><b>การตั้งค่าคุกกี้</b></span>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                คุกกี้ที่มีความจำเป็นอย่างยิ่ง (Strictly Necessary Cookies)
                            </button>
                        </h2>

                        <?php /*<div class="form-check form-switch form-switch-accordion">
                            <input class="form-check-input" type="checkbox" role="switch">
                        </div>*/ ?>

                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                คุกกี้ประเภทนี้มีความสำคัญต่อการทำงานของเว็บไซต์ ซึ่งรวมถึงคุกกี้ที่ทำให้ท่านสามารถเข้าถึงข้อมูลและใช้งานในเว็บไซต์ของเราได้อย่างปลอดภัย
                                <br>
                                <br>
                                <a href="" class="button-detail">ข้อมูลเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                คุกกี้ที่ช่วยในการใช้งาน (Functional Cookies)
                            </button>
                        </h2>

                        <div class="form-check form-switch form-switch-accordion">
                            <input class="form-check-input" type="checkbox" role="switch">
                        </div>

                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                เราใช้ Cookies ประเภทนี้เพื่อช่วยจดจำอุปกรณ์หรือบราวเซอร์ของท่าน เพื่อให้เราสามารถจัดทำเนื้อหาได้อย่างเหมาะสมกับความสนใจส่วนบุคคลของท่านได้รวดเร็วขึ้น และช่วยให้การบริการและแพลตฟอร์มได้อย่างสะดวกสบายและเป็นประโยชน์ต่อท่านมากยิ่งขึ้น
                                <br>
                                <br>
                                <a href="" class="button-detail">ข้อมูลเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                คุกกี้เพื่อปรับเนื้อหาให้เข้ากับกลุ่มเป้าหมาย (Targeting Cookies)
                            </button>
                        </h2>

                        <div class="form-check form-switch form-switch-accordion">
                            <input class="form-check-input" type="checkbox" role="switch">
                        </div>

                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                เราใช้คุกกี้ประเภทนี้จดจำการตั้งค่าของท่านในการเข้าใช้งานหน้าเว็บไซต์ หน้าเว็บที่ท่านได้เยี่ยมชม และลิงค์ที่ท่านเยี่ยมชม เพื่อรวบรวมข้อมูลเกี่ยวกับวิธีการเข้าชมและพฤติกรรมการเยี่ยมชมเว็บไซต์และยังปรับปรุงเว็บไซต์ รวมทั้งเนื้อหาใดๆ ซึ่งปรากฏอยู่บนหน้าเว็บให้ตรงกับความสนใจของท่านมากยิ่งขึ้น และทดสอบความมีประสิทธิภาพของโฆษณาเรา และนำเสนอโฆษณาที่เหมาะสมกับท่านมากที่สุดเท่าที่จำเป็น นอกจากนี้ เรายังอาจแชร์ข้อมูลนี้ให้กับบุคคลที่สามเพื่อวัตถุประสงค์ดังกล่าว
                                <br>
                                <br>
                                <a href="" class="button-detail">ข้อมูลเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                                คุกกี้เพื่อการวิเคราะห์เชิงสถิติ (Statistic Cookies)
                            </button>
                        </h2>

                        <div class="form-check form-switch form-switch-accordion">
                            <input class="form-check-input" type="checkbox" role="switch">
                        </div>

                        <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                เราใช้ Analytics Cookies ที่ให้บริการโดย Google ซึ่งมีวัตถุประสงค์เพื่อใช้เก็บข้อมูลเชิงลึกเกี่ยวกับรูปแบบการใช้งานของท่านบนเว็บไซต์ของเรา โดยข้อมูลนี้ประกอบไปด้วย หน้าเว็บไซต์ที่ท่านเข้าชม ลิงค์ที่ท่านคลิก ระยะเวลาที่ท่านเข้าชมในแต่ละหน้า โดยเราใช้ข้อมูลนี้เพื่อวิเคราะห์รูปแบบการใช้งานของผู้ใช้ และเพื่อให้เว็บไซต์ทำงานได้อย่างถูกต้อง
                                <br>
                                <br>
                                <a href="" class="button-detail">ข้อมูลเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-btn">
                    <button type="button" class="cookies-btn-accept" data-bs-dismiss="modal" aria-label="Close">ยืนยันตั้งค่า</button>
                    <button type="button" class="cookies-btn-acceptAll" onclick="performClick('checkAll');"><span id="acceptAll">อนุญาติทั้งหมด</span><span id="cancel" style="display: none;">ยกเลิกทั้งหมด</span></button>
                    <div class="form-check form-switch" style="display: none;">
                        <input class="form-check-input" type="checkbox" id="checkAll" role="switch" hidden>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function dismissCookies() {
        document.getElementById("panel").style.display = "none";
    }
</script>

<script>
    $("#checkAll").click(function() {
        $('input:checkbox').not(this).prop('checked', this.checked)
        if ((this.checked) === true) {
            $(".cookies-btn-acceptAll").addClass("cookies-btn-cancel");
            $(".cookies-btn-acceptAll").removeClass("cookies-btn-acceptAll");
            $("#acceptAll").hide();
            $("#cancel").show();
        } else {
            $(".cookies-btn-cancel").addClass("cookies-btn-acceptAll");
            $(".cookies-btn-cancel").removeClass("cookies-btn-cancel");
            $("#acceptAll").show();
            $("#cancel").hide();
        }
    });

    function performClick(elemId) {
        var elem = document.getElementById(elemId);
        if (elem && document.createEvent) {
            var evt = document.createEvent("MouseEvents");
            evt.initEvent("click", true, false);
            elem.dispatchEvent(evt);
        }
    }
</script>