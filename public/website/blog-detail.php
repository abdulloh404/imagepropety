<!DOCTYPE html>
<html lang="en">
<?php include("./components/header.php") ?>
<link rel="stylesheet" href="./assets/css/blog-detail.css">

<body>
    <?php include("./components/navbar.php") ?>
    <div class="container blog-detail my-5">
        <div class="card border-0">
            <img src="./assets/img/image (26).png" class="card-img-top" alt="">
            <div class="card-body text-center px-0">
                <h4 class="card-title py-3">โครงการบ้านสังคมคุณภาพ #โฮมการ์เด้นวิลล์ สามยอด 2 พื้นที่กว้าง ไซส์ใหญ่ XXL</h4>
                <p class="card-text">อัตราดอกเบี้ยบ้าน หรือสินเชื่อกู้ซื้อบ้าน คือ ตัวกำหนดการผ่อนชำระค่างวดของการซื้อบ้านเดี่ยว บ้านแฝด ทาวน์เฮ้าส์ และคอนโด สำหรับคนที่ทำการกู้ซื้อบ้านผ่านธนาคาร ดังนั้นใครที่ยังเลือกไม่ได้ว่าจะกู้ซื้อบ้านกับธนาคารไหนดีก็หายห่วงได้
                    ธนาคารไหนให้สินเชื่อบ้านโดยคิดดอกเบี้ยบ้านต่ำสุด มีเงื่อนไขอะไรบ้าง เพื่อช่วยอำนวยความสะดวกในการพิจารณาเลือกสินเชื่อ สำหรับผู้ที่กำลังตัดสินใจเลือกซื้อบ้าน หรือคอนโด ได้ง่ายขึ้น พร้อมตัวช่วยในการ
                </p>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 col mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="./assets/img/001.png" class="size-profile" alt="">
                            <div class="ms-3">
                                <p class="m-0">Thannakrit</p>
                            </div>
                        </div>
                        <div>
                            <p class="m-0">May 13, 2022</p>
                        </div>
                    </div>
                </div>
                <div class="icon-share">
                    <a><i class="fas fa-share-alt fa-lg"></i><span class="copied ps-2" style="display: none;">คัดลอกลิ้งค์แล้ว!</span></a>
                    <a href="" onclick="facebook()"><img src="./assets/img/logo-fb.png" alt=""></a>
                    <a href=""><img src="./assets/img/logo-line.png" alt=""></a>
                </div>
                <?php /*  <div id="box">
                    <button id="btn">
                        <i class="fas fa-share-alt" style="z-index: 11;"></i>
                    </button>

                    <ul id="list">
                        <li class="list-item">
                            <a class="list-item-link" href="#">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="list-item-link" href="#">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="list-item-link" href="#">
                                <i class="fab fa-instagram-square"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="list-item-link" href="#">
                                <i class="fab fa-whatsapp-square"></i>
                            </a>
                        </li>
                    </ul>
                </div> */ ?>
            </div>
        </div>
    </div>
    <div class="container">
        <?php include('./components/blog.php') ?>
    </div>
    <?php include("./components/footer.php") ?>

    <script>
        function facebook() {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + window.location.href);
        }

        var $temp = $("<input>");
        var $url = $(location).attr("href");

        $(".fa-share-alt").on("click", function() {
            $("body").append($temp);
            $temp.val($url).select();
            document.execCommand("copy");
            $temp.remove();
            $(".copied").show()
        });
    </script>
    </script>
</body>

</html>