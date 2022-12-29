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
    <link rel="stylesheet" href="./assets/style/audio.css">

</head>

<body>
    <!--navbar-->
    <?php include './components/navbar.php' ?>
    <!--navbar-->

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h4>สถานีวิทยุในเครือบริษัท</h4>
                <hr>
            </div>
        </div>
    </div>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                <li class="breadcrumb-item active" aria-current="page">วิทยุในเครือบริษัท</li>
            </ol>
        </nav>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8" style="display: grid; grid-template-columns: 33% 33% 33%; align-items: center; background-color: #FDD400; border-radius: 30px;">
                <div style="display: flex;">
                    <img src="./assets/img/icon/live.png" alt="" style="width: 40px; height: 20px;">
                    <span style="font-size: 10pt; border-bottom: solid 1px black; 
                            overflow: hidden;
                            display: -webkit-box;
                            -webkit-box-orient: vertical;
                            -webkit-line-clamp: 1;
                        ">สวพ.เชียงใหม่ AM 918
                    </span>
                </div>
                <div style="text-align: center;">
                    <img src="./assets/img/icon/dvd.png" alt="" style="width: 60px; height: 60px;" class="">
                </div>
                <div class="d-flex justify-content-end">
                    <audio controls loop autoplay style="width: 100%;">
                        <source src="https://ia800905.us.archive.org/19/items/FREE_background_music_dhalius/backsound.mp3" type="audio/mp3">
                    </audio>
                </div>
                <style>
                    audio::-webkit-media-controls-play-button,
                    audio::-webkit-media-controls-panel {
                        background-color: #FDD400;
                    }
                </style>
                <!--<div class="container-audio">
                    <div>
                        <img src="./assets/img/icon/live.png" alt="" style="width: 40px; height: 20px;">
                        <span style="font-size: 10pt; border-bottom: solid 1px black;">สวพ.เชียงใหม่ AM 918</span>
                    </div><br>
                    <audio controls  loop autoplay class="volcontrol3">
                        <source src="https://ia800905.us.archive.org/19/items/FREE_background_music_dhalius/backsound.mp3" type="audio/mp3">
                    </audio>
                    <div class="wave">
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                        <div class="colum1">
                            <div class="row"></div>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-sm-12 col-md-3">

            </div>
            <div class="col-lg-6 col-sm-12 col-md-6">
                <a href=""><img src="./assets/img/news1.png" alt="" class="imgsec1"></a>
            </div>
            <div class="col-lg-3 col-sm-12 col-md-3">

            </div>
        </div>
    </div>
    <br>
    <div class="container" style="margin-bottom: 50px;">
        <div class="card">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.เชียงใหม่ AM 918</a>
                            </div>
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.นครศรีธรรมราช FM 91.5</a>
                            </div>
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.สงขลา AM 1098</a>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.นครสวรรค์ FM 105.75</a>
                            </div>
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.พิษณุโลก AM 1422</a>
                            </div>
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.ขอนแก่น FM 104.5</a>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.นครราชสีมา AM 990</a>
                            </div>
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio">สวพ.สุราษฏร์ธานี FM 99</a>
                            </div>
                            <div class="text-center mt-5">
                                <a href=""><img src="./assets/img/logoradio.png" alt="" class="logoradio"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <!--footer-->
    <?php include './components/footer.php' ?>
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>



</body>

</html>