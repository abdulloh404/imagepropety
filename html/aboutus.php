<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สวพ.FM91</title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="./assets/style/font.css">

    <link rel="stylesheet" href="./assets/style/style.css">
    

</head>
<body>
    <!--navbar-->
    <?php include './components/navbar.php' ?>
    <!--navbar-->

    <div class="container mapcontainer">
        <div id="map"></div>
    </div>
    <div class="container">
        <div class="text-center mt-5">
            <p>สถานีวิทยุพิทักษ์สันติราษฎร์ สวพ. FM91 กองตำรวจสื่อสาร สำนักงานตำรวจแห่งชาติ</p>
            <p>82 ซอยข้างกรมพัฒนาที่ดิน ถนนพหลโยธิน แขวงลาดยาว เขตจตุจักร, 10900 กรุงเทพฯ</p>
            <p><img src="./assets/img/icon/smartphone.png" alt="" class="imgphone">1644 โทรฟรีทั่วประเทศไทย ตลอด 24 ชั่วโมง</p>
        </div>
    </div>
    <div class="container">
        <div class="text-center mt-5">
            <span class="address">ที่อยู่ปัจจุบัน</span>
        </div>
    </div>
    <div class="container">
        <div class="text-center mt-5">
            <p>82 ซอยข้างกรมพัฒนาที่ดิน ถนนพหลโยธิน ลาดยาว จตุจักร กรุงเทพมหานคร 10900</p>
            <p><img src="./assets/img/icon/smartphone.png" alt="" class="imgphone">ติดต่อ : 02 562 0033-4 , 02 941 0847-50</p>
        </div>
    </div>


    <!--footer-->
    <?php include './components/footer.php' ?>
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!--Map-->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap&v=weekly" async></script>
    <script src="./assets/js/map.js"></script>
    
</body>
</html>