<style>
    footer {
        background-color: #2E69B6;
        color: #ffffff;
        padding-top: 50px;
        font-size: 14px;
    }

    footer a {
        color: #f0f2f5;
        text-decoration: none;
        transition: all 0.3s ease-in-out;
    }

    footer a:hover {
        color: #ffffff;
        letter-spacing: 1.5px;
    }

    footer ul {
        list-style-type: none;
        padding-left: 0;
    }

    footer div>h4 {
        position: relative;
    }

    footer div>h4::before {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 1px;
        width: 70%;
        border-bottom: 1px solid #ffffff;
    }

    .social-link>a>img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .social-link>a {
        margin-right: 5px;
    }

    .img-container {
        width: 300px;
        height: auto;
        align-items: center;
    }

    .img-container>img {
        width: 100%;
    }

    @media screen and (max-width: 576px) {
        footer div>h4::before {
            width: 28%;
        }
    }
</style>
<footer>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-3 col-lg-5 col-md-12 col-12 d-flex justify-content-center align-items-center mb-xl-0 mb-lg-0 mb-md-5 mb-3">
                <div class="img-container">
                    <img src="page/assets/img/footer imp/image (23).png" class="w-100" alt="">
                    <div class="row g-1">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-6">
                            <img src="page/assets/img/logo-partner.png" class="w-100" alt="">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-6">
                            <img src="page/assets/img/logo-partner.png" class="w-100" alt="">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-6">
                            <img src="page/assets/img/logo-partner.png" class="w-100" alt="">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-6">
                            <img src="page/assets/img/logo-partner.png" class="w-100" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-12">
                <div class="third-container">
                    <h4>
                        ติดต่อเรา
                    </h4>
                    <p class="mb-1">
                        79 หมู่ 8 ถนนมิตรภาพ-หนองคาย ต.หมื่นไวย อ.เมืองนครราชสีมา จ.นครราชสีมา 30000
                    </p>
                    <p class="mb-1">
                        <a href="tel:096-965-4555">Call Center: 096-965-4555</a>
                    </p>
                    <p class="mb-1">
                        Fax: 044-244-830
                    </p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-12">
                <div class="second-container">
                    <h4>
                        เกี่ยวกับเรา
                    </h4>
                    <ul>
                        <li><a href="<?php echo front_link(11) ?>">เกี่ยวกับเรา</a></li>
                        <li><a href="<?php echo front_link(7) ?>">ข่าวสารและกิจกรรม</a></li>
                        <li><a href="<?php echo front_link(4) ?>">นโยบายความเป็นส่วนตัว</a></li>
                        <li><a href="<?php echo front_link(5) ?>">นโยบายคุกกี้</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-12">
                <div class="fourth-container">
                    <h4>
                        ติดตามเรา
                    </h4>
                    <div class="d-flex align-items-center social-link">
                        <a href="https://www.instagram.com/homegardenville/" target="_blank">
                            <img src="page/assets/img/footer imp/image (28).png" alt="instagram">
                        </a>
                        <a href="https://www.facebook.com/homegardenville" target="_blank">
                            <img src="page/assets/img/footer imp/image (27).png" alt="facebook">
                        </a>
                        <a href="">
                            <img src="page/assets/img/footer imp/image (26).png" alt="line">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                <div class="text-center mt-3 pb-3"><small>Copyright <?php echo date("Y") ?> Image Property Management Co,.Ltd www.imageproperty.co.th</small></div>
            </div>
        </div>
    </div>
</footer>