<!DOCTYPE html>
<html lang="en">

<?php echo view('components/header') ?>
<link rel="stylesheet" href="page/assets/css/promotion.css">

<body>
    <?php echo view('components/navbar') ?>
    <div class="container p-md-auto p-0">
        <div class="text-center my-5 p-3">
            <h1>
                โปรโมชัน บ้านเดี่ยว บ้านแฝด อาคารพาณิชย์ จาก Image Property
            </h1>
            <h4 class="text-muted pt-3">
                พบโปรโมชัน บ้านเดี่ยว บ้านแฝด และอาคารพาณิชย์ พร้อมสิทธิพิเศษมากมายไม่ว่าจะเป็นส่วนลด แพ็กเกจ หรือสิ่งดีๆ ที่คัดสรรมาเฉพาะสำหรับลูกค้า Image property ที่ไม่ควรพลาด
            </h4>
        </div>
        <div class="search-content mb-3">
            <form action="" method="post">
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
                            <button type="submit" class="btn-search">ค้นหา</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="container">
        <div class="pt-2 ps-2 mb-3">
            <h4 class="fw-bold" style="color:#14306D;">
                ผลการค้นหาโปรโมชั่น
            </h4>
            <h6 class="text-muted">
                จำนวนทั้งหมด 4 โปรโมชั่น
            </h6>
        </div>

        <div class="row mb-5 g-3">
            <div class="col-lg-3 col-md-4 col-6">
                <a href="" class="productItem">
                    <img src="page/assets/img/003.png">
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="" class="productItem">
                    <img src="page/assets/img/011.png">
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="" class="productItem">
                    <img src="page/assets/img/010.png">
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="" class="productItem">
                    <img src="page/assets/img/001.png">
                    <span class="text-dark mt-2"><b>โฮมการ์เด้นวิลล์ บายพาส</b><br>ราคาเริ่มต้น 2.39 ล้านบาท<br>โซน : จอหอ-บายพาส</span>
                </a>
            </div>
        </div>
        <?php echo view('components/register-form') ?>
    </div>
</body>

</html>