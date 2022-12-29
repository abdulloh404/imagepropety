<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo titleWeb()?></title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">
    <style>
        .text-muted {
            font-size: 14px;
        }
    </style>

</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <div class="container mt-2">

        <div class="row">

            <div class="col-md-8">
                
                    <?php echo $forYou ?>
                
                

                <!-- <div style="text-align: center;" class="mt-4 mb-4">
                    <h2><?php //echo $title ?></h2>
                </div>

                <hr>
                <div class="row" id="#targetDiv">
                    <?php //echo $HisHtml ?>
                </div> -->

                

                <br>
                <!-- <div class="container mt-4 mt-2">
                <div class="col pt-5 text-center">
                    <img src="front/assets/png/ads.png" alt="" class="w-100 text-center">
                </div>
            </div> -->


            </div>



            <div class="col-md-4">
                <img style="width: 100%;" src="<?php echo base_url('front/assets/img/adsnew.png')?>" class="img-fluid mb-5 text-center" alt="">
                <h4 class="mt-5 text-center">ข่าวยอดนิยม</h4>
                <hr>
                <?php echo $newsHighlight ?>

            </div>
        </div>
    </div>



    <br /><br />
    <!-- <div class="container">

        <div class="row mt-4 pt-2 pb-2">
            <div class="col-md-4">
                <h3 class="text-danger">China</h3>
                <hr class="pagealine">
                <img src="front/assets/jpg/news.jpg" class="img-fluid" alt="">
                <p>ข่าวบันเทิง ล่าสุด รวมข่าววันนี้ของ ดารา นักแสดง คนดัง ละคร หนัง</p>

                
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>
                <div style="text-align: center;padding-top: 40px;padding-bottom: 30px;">
                    <button type="button" class="btn btn-tages">ดูเพิ่มเติม</button>
                </div>
            </div>
            <div class="col-md-4 pt-2 pb-2">
                <h3 class="text-danger">World</h3>
                <hr class="pagealine">
                <img src="front/assets/jpg/news.jpg" class="img-fluid" alt="">
                <p>ข่าวบันเทิง ล่าสุด รวมข่าววันนี้ของ ดารา นักแสดง คนดัง ละคร หนัง</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>
                <div style="text-align: center;padding-top: 40px;padding-bottom: 30px;">
                    <button type="button" class="btn btn-tages">ดูเพิ่มเติม</button>
                </div>
            </div>
            <div class="col-md-4 pt-2 pb-2">
                <h3 class="text-danger">Business</h3>
                <hr class="pagealine">
                <img src="front/assets/jpg/news.jpg" class="img-fluid" alt="">
                <p>ข่าวบันเทิง ล่าสุด รวมข่าววันนี้ของ ดารา นักแสดง คนดัง ละคร หนัง</p>
               
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>
                <div style="text-align: center;padding-top: 40px;padding-bottom: 30px;">
                    <button type="button" class="btn btn-tages">ดูเพิ่มเติม</button>
                </div>
            </div>
        </div>


        <div class="row mt-4 pt-2 pb-2">
            <div class="col-md-4">
                <h3 class="text-danger">Tech</h3>
                <hr class="pagealine">
                <img src="front/assets/jpg/news.jpg" class="img-fluid" alt="">
                <p>ข่าวบันเทิง ล่าสุด รวมข่าววันนี้ของ ดารา นักแสดง คนดัง ละคร หนัง</p>

               
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>
                <div style="text-align: center;padding-top: 40px;padding-bottom: 30px;">
                    <button type="button" class="btn btn-tages">ดูเพิ่มเติม</button>
                </div>
            </div>
            <div class="col-md-4 pt-2 pb-2">
                <h3 class="text-danger">Environment</h3>
                <hr class="pagealine">
                <img src="front/assets/jpg/news.jpg" class="img-fluid" alt="">
                <p>ข่าวบันเทิง ล่าสุด รวมข่าววันนี้ของ ดารา นักแสดง คนดัง ละคร หนัง</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>
                <div style="text-align: center;padding-top: 40px;padding-bottom: 30px;">
                    <button type="button" class="btn btn-tages">ดูเพิ่มเติม</button>
                </div>
            </div>
            <div class="col-md-4 pt-2 pb-2">
                <h3 class="text-danger">LGBT</h3>
                <hr class="pagealine">
                <img src="front/assets/jpg/news.jpg" class="img-fluid" alt="">
                <p>ข่าวบันเทิง ล่าสุด รวมข่าววันนี้ของ ดารา นักแสดง คนดัง ละคร หนัง</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <img class="card-img" src="front/assets/png/news/news1.jpg" alt="Card image">
                    </div>

                    <div class="col-md-6">
                        <p>จัดให้แบบจุกๆ "เชอรี่ สามโคก" ชุดใหญ่ไฟกะพริบ เชลซี</p>
                    </div>
                </div>
                <div style="text-align: center;padding-top: 40px;padding-bottom: 30px;">
                    <button type="button" class="btn btn-tages">ดูเพิ่มเติม</button>
                </div>
            </div>
        </div>


        <style>
            .btn-tages {
                background-color: #fff;
                border: 1px solid #f0b53b;
                border-radius: 99px;
            }
        </style>
    </div>


    <div class="col pt-5 pb-5 text-center">
        <img src="front/assets/png/ads.png" alt="" class="w-50 text-center">
    </div> -->


    <!--footer-->
    
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>
<?php echo view('front/components/footer') ?></body>

</html>