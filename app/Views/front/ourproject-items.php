<!DOCTYPE html>
<html lang="en">

<?php echo view('components/header') ?>
<link rel="stylesheet" href="page/assets/css/ourproject-items.css">

<body>
    <?php echo view('components/navbar') ?>
    <div class="container p-md-auto p-0">
        <div id="carouselOurProject" class="carousel slide" data-bs-ride="true">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="page/assets/img/banner-home-3.jpeg" class="d-block w-100" alt="">
                </div>
                <div class="carousel-item">
                    <iframe width="100%" src="https://www.youtube.com/embed/NFXydCGKrd8&mute=1" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="carousel-item">
                    <img src="page/assets/img/banner-home-4.jpg" class="d-block w-100" alt="">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselOurProject" data-bs-slide="prev">
                <i class="fas fa-chevron-left fa-2x"></i>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselOurProject" data-bs-slide="next">
                <i class="fas fa-chevron-right fa-2x"></i>
            </button>
        </div>
        <div class="search-content mb-3">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                    <div class="icon-location-search"><img src="page/assets/img/location.png" width="20" alt=""></div>
                    <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                        <option selected>ทำเลที่ตั้ง</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                        <img src="" alt="">
                    </select>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                    <div class="icon-home-search"><img src="page/assets/img/home-page.png" width="20" alt=""></div>
                    <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                        <option selected>ประเภทโครงการ</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                    <div class="icon-tag-search"><img src="page/assets/img/price-tag.png" width="20" alt=""></div>
                    <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                        <option selected>ช่วงราคา</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <div class="d-flex justify-content-center align-items-center mb-3 bg-white p-2">
                        <img src="page/assets/img/search.png" width="20" alt="">
                        <input type="text" class="form-control border-0 rounded-0 text-xl-center text-lg-center text-md-center text-start" style="font-size: x-large; width: 153px" id="" placeholder="พิมพ์คำค้นหา" required>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn-search">ค้นหา</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pt-3 ps-2 mb-4">
            <h5>
                <?php echo $count ?> โครงการ
            </h5>
            <h6 class="text-muted">
                สำหรับ 'บ้านเดี่ยว'
            </h6>
        </div>

        <div class="row mb-5">
            <?php echo $html ?>
            <!-- <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="<?php echo front_link(12) ?>" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="new-project">NEW PROJECT</span>
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div> -->
        </div>
    </div>

</body>

</html>