<!DOCTYPE html>
<html lang="en">

<?php include('./components/header.php') ?>
<link rel="stylesheet" href="./assets/css/ourproject.css">

<body>
    <?php include('./components/navbar.php') ?>
    <div class="container p-md-auto p-0">
        <div id="carouselOurProject" class="carousel slide" data-bs-ride="true">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselOurProject" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./assets/img/banner-home-3.jpeg" class="d-block w-100" alt="">
                </div>
                <div class="carousel-item">
                    <iframe width="100%" src="https://www.youtube.com/embed/NFXydCGKrd8?autoplay=1&mute=1" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="carousel-item">
                    <img src="./assets/img/banner-home-4.jpg" class="d-block w-100" alt="">
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
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                        <div class="d-flex justify-content-center align-items-center mb-3 bg-white p-2">
                            <img src="./assets/img/search.png" width="20" alt="">
                            <input type="text" class="form-control border-0 rounded-0 text-xl-center text-lg-center text-md-center text-start" style="font-size: x-large; width: 153px" id="" placeholder="พิมพ์คำค้นหา" required>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                        <div class="icon-location-search"><img src="./assets/img/location.png" width="20" alt=""></div>
                        <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                            <option selected>ทำเลที่ตั้ง</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                            <img src="" alt="">
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                        <div class="icon-home-search"><img src="./assets/img/home-page.png" width="20" alt=""></div>
                        <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                            <option selected>ประเภทโครงการ</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-12 position-relative">
                        <div class="icon-tag-search"><img src="./assets/img/price-tag.png" width="20" alt=""></div>
                        <select class="form-select mb-3 p-3 border-0 rounded-0 ps-5">
                            <option selected>ช่วงราคา</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
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
        <div class="d-flex text-center mt-3">
            <div class="content">
                <div class="header-line"></div>
            </div>
            <h3 class="w-100">ประเภทโครงการ</h3>
            <div class="content">
                <div class="header-line"></div>
            </div>
        </div>

        <?php include('./components/houseType.php') ?>

        <?php include('./components/favorite.php') ?>
    </div>

    <?php include('./components/footer.php') ?>
</body>

</html>