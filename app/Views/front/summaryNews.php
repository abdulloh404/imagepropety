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

    <link rel="stylesheet" href="front/assets/style/style.css?rand=<?php echo rand() ?>">
    <!-- วิทยุ -->
    <link rel="stylesheet" href="front/assets/style/audio.css">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">
    <style>
        /* img anime */
        .wrapper-card-img img:hover {
            transform: scale(1.1);
        }

        .wrapper-card-img img {
            position: absolute;
            top: 0%;
            transition: 1s;
        }

        .wrapper-card-img {
            position: relative;
            padding-top: 56.25%;
            overflow: hidden;
        }

        /* end img anime */
        .text-muted {
            margin-bottom: 0px;
        }

        .imgfluidd {
            width: 100%;
        }

        .card-img-top {
            height: 200px;
        }

        @media screen and (max-width: 1024px) {
            .imgfluidd {
                width: 100%;
            }

            .card-img-top {
                height: 150px;
            }
        }
        
        @media (max-width: 800px) {
            .card-img-top {
                height: 110px;
            }
        }

        @media (max-width: 426px) {
            .card-img-top {
                height: 195px;
            }

            .imgfluidd {
                width: 100%;
            }
        }
    </style>

</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <!-- วิทยุ -->


    <div class="container mt-2">

        <h1 class="text-dark"><?php echo $title ?> </h1>


        <hr>

        <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-12 col-12">
                <?php echo $HmainNews ?>
                <div class="row mt-0" style="margin-bottom:-40px">
                    <?php echo $SmainNews ?>
                </div>

                <br><br>


            </div>



            <div class="col-xl-4 col-lg-4 col-md-12 col-12 webview">
                <img src="front/assets/img/adsnew.png" class="img-fluid text-center" style="width: 100%; margin-top: 8px;" alt="">
                <h4 class=" text-center" style="margin-bottom: -5px;margin-top: 20px; font-weight: bold; color: black;">ข่าวยอดนิยม</h4>
                <hr>
                <?php echo $newsHighlight ?>

            </div>
        </div>
        <div class="row">
            <?php echo implode('', $catMain) ?>
            <div class="col-xl-4 col-lg-4 col-md-12 col-12 MobileView">
                <img src="front/assets/img/adsnew.png" class="img-fluid text-center banner-mobile" alt="">
                <h4 class=" text-center" style="margin-bottom: -5px;margin-top: 20px; font-weight: bold; color: black;">ข่าวยอดนิยม</h4>
                <hr>
                <?php echo $newsHighlight ?>

            </div>
        </div>
    </div>


    <!-- <div class="container mt-4 mt-2">
        <div class="col pt-5 text-center">
            <img src="front/assets/png/ads.png" alt="" class="w-75 text-center">
        </div>
    </div> -->
    <br>

    <!--footer-->


    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>


    <?php echo view('front/components/footer') ?>
</body>

</html>