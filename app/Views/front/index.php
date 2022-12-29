<?php echo view('components/navbar') ?>

<div class="container p-0">
    <div id="carouselOurProject" class="carousel slide" data-bs-ride="true">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <iframe width="100%" src="https://www.youtube.com/embed/NFXydCGKrd8?autoplay=1&mute=1" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="carousel-item">
                <img src="assets/img/banner-home-3.jpeg" class="d-block w-100" alt="">
            </div>
            <div class="carousel-item">
                <img src="assets/img/banner-home-4.jpg" class="d-block w-100" alt="">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselOurProject" data-bs-slide="prev">
            <i class="fas fa-chevron-left fa-2x"></i>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselOurProject" data-bs-slide="next">
            <i class="fas fa-chevron-right fa-2x"></i>
        </button>
    </div>

    <div class="search-content">
        <form action="" method="post">
            <div class="text-center mb-4">
                <h3>ค้นหาโครงการ</h3>
                <p>บ้านเดี่ยว ทาวน์โฮม คอนโด</p>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                    <div class="icon-location-search"><img src="assets/img/location.png" width="20" alt=""></div>
                    <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                        <option selected>ทำเลที่ตั้ง</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                        <img src="" alt="">
                    </select>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                    <div class="icon-home-search"><img src="assets/img/home-page.png" width="20" alt=""></div>
                    <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                        <option selected>ประเภทโครงการ</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                    <div class="icon-tag-search"><img src="assets/img/price-tag.png" width="20" alt=""></div>
                    <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                        <option selected>ช่วงราคา</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <div class="d-flex justify-content-center align-items-center mb-3 bg-white p-2">
                        <img src="assets/img/search.png" width="20" alt="">
                        <input type="text" class="form-control border-0 rounded-0 text-xl-center text-lg-center text-md-center text-start" style="font-size: x-large; width: 153px" id="" placeholder="พิมพ์คำค้นหา" required>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn-search">ค้นหา</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="header-slide">
            <h2>ประเภทโครงการ</h2>
        </div>
    </div>
    <?php echo view('components/houseType') ?>
    <div class="row">
        <div class="header-slide">
            <h2>โครงการของเรา</h2>
            <a href="ourproject-items.php">ดูทั้งหมด</a>
        </div>
    </div>
    <?php echo view('components/slider') ?>
</div>

<div class="container-fluid" style="background: rgba(209, 174, 140, 0.1);">
    <div class="container">
        <div class="row">
            <div class="col-xl-7 col-lg-7 col-md-7 col-12">
                <img class="w-100 pb-5 pt-5" src="assets/img/013.png" alt="">
            </div>
            <div class="col-xl-5 col-lg-5 col-md-5 col-12 my-auto">
                <div class="index-detail1 mb-5">
                    <h1><b>ทำเลดี ที่อยู่สบาย</b></h1>
                    <h4>กับเรา Image Property ครบที่เดียว</h4>
                    <a href="blog-detail.php">อ่านต่อ คลิ๊ก <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo view('components/blog') ?>
</div>

<div class="container">
    <div class="index-detail2">
        <img src="assets/img/015.png" alt="">
        <div class="index-detail2-text">
            <h1><b>ชีวิตเลือกได้ แค่ตัวคุณ</b></h1>
            <h3>กับทาวน์โฮม อาคารพาณิชย์ บ้านเดี่ยวของเรา</h3>
        </div>
    </div>
</div>

<?php echo view('components/cookiesPopup') ?>