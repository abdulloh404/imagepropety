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

        <div class="container mainbody">
        <div class="row">
            <div class="col-md-2 col-12"></div>
            <div class="col-md-8 col-12">
            <h4>สถานีวิทยุพิทักษ์สันติราษฎร์ สวพ. FM91<br>กองตำรวจสื่อสาร สำนักงานตำรวจแห่งชาติ</h4>
            <hr>
            <p>82 ซอยข้างกรมพัฒนาที่ดิน ถนนพหลโยธิน แขวงลาดยาว เขตจตุจักร, 10900 กรุงเทพฯ</p>
            <hr>
            <p>1644 โทรฟรีทั่วประเทศไทย ตลอด 24 ชั่วโมง</p>
            </div>
            <div class="col-md-2 col-12"></div>
        </div>
            <div class="row mb-3">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <h4 style="color: #555555;"><u>แบบฟอร์มติดต่อ</u></h4>
                    <div class="row mt-3">
                        <div class="col-md-3 col-12">
                        <label for="name" class="form-label">ชื่อ</label>
                        </div>
                        <div class="col-md-9 col-12">
                        <input type="email" class="form-control" id="name" placeholder="">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3 col-12">
                        <label for="email" class="form-label">อีเมล์</label>
                        </div>
                        <div class="col-md-9 col-12">
                        <input type="email" class="form-control" id="email" placeholder="">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3 col-12">
                        <label for="content" class="form-label">ข้อความ</label>
                        </div>
                        <div class="col-md-9 col-12" style="text-align: center;">
                        <textarea class="form-control mb-3" id="content" rows="5"></textarea>
                            <button type="button" class="btn btn-warning mt-3" style="padding: 8px 40px;">ส่งถึงเรา</button>
                            <!-- <a href="" style="padding: 5px 40px;border: 1px solid #FDD400;background-color: #FDD400;border-radius: 5px;">ส่งถึงเรา</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="mb-3">
            </div>
        </div>

        <style>
            .mainbody {
                padding: 40px;
                border: 1px solid;
                border-radius: 10px;
                margin-top: 40px;
                margin-bottom: 40px;
            }
            h4 {
               color:#333333;
            }
        </style>


    <!--footer-->
    <?php include './components/footer.php' ?>
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


    
</body>
</html>