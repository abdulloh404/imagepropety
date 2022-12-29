<!DOCTYPE html>
<html lang="en">
<?php include("./components/header.php") ?>
<link rel="stylesheet" href="./assets/css/contact-us.css">

<body>
    <?php include("./components/navbar.php") ?>
    <div class="container contact-us p-md-auto p-0">
        <div class="container d-xl-block d-lg-block d-md-block d-none px-xl px-0">
            <div class="p-0">
                <!--Pattern HTML-->
                <div id="pattern" class="pattern">
                    <div class="map">
                        <a href="https://maps.google.com/maps?q=Pittsburgh,+PA&hl=en&sll=40.697488,-73.979681&sspn=0.667391,1.447449&oq=Pittsburgh&hnear=Pittsburgh,+Allegheny,+Pennsylvania&t=m&z=12" class="btn map-link"></a>
                    </div>
                </div>
                <!--End Pattern HTML-->
            </div>
        </div>
        <div class="container mt-md-0 mt-5">
            <form action="" method="post">
                <div class="row">
                    <div class="col-md-8 col-12 mx-auto">
                        <div class="row mb-5">
                            <div class="text-center mb-3">
                                <h3>ติดต่อเรา</h3>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-5">
                                    <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="ชื่อ" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-5">
                                    <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="นามสกุล" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-5">
                                    <input type="tel" pattern="[0-9]{10}" class="form-control" id="exampleFormControlInput1" placeholder="หมายเลขโทรศัพท์" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-5">
                                    <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="อีเมล" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-5">
                                    <select class="form-select form-select-sm" aria-label=".form-select-sm example">
                                        <option selected>หัวข้อ</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-5">
                                    <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="แสดงความคิดเห็นเพิ่มเติม" required>
                                </div>
                            </div>
                            <div class="d-grid col-xl-4 col-lg-4 col-md-6 col mx-auto">
                                <button role="submit" class="btn-send-message p-3 rounded-pill">ส่งข้อความ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php include("./components/footer.php") ?>

    <script>
        $(document).ready(function() {
            buildMap();
        });

        var sw = document.body.clientWidth,
            bp = 550,
            $map = $('.map');
        var embed = '<iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=thaicc tower&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>';

        function buildMap() {
            if (sw > bp) { //If Large Screen
                if ($('.map-container').length < 1) { //If map doesn't already exist
                    buildEmbed();
                }
            } else {
                if ($('.static-img').length < 1) { //If static image doesn't exist
                    buildStatic();
                }
            }
        };

        function buildEmbed() { //Build iframe view
            $('<div class="map-container"/>').html(embed).prependTo($map);
        };

        $(window).resize(function() {
            sw = document.body.clientWidth;
            buildMap();
            google.maps.event.trigger(map, "resize");
        });
    </script>

</body>

</html>