<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo titleWeb() ?></title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">

    <link rel="stylesheet" href="front/assets/style/slick.css">
    <link rel="stylesheet" href="front/assets/style/slick-theme.css">
    <link rel="stylesheet" href="front/assets/style/customSilde.css">

    <link rel="stylesheet" href="front/assets/style/customSilde.css">

    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>

    <style>
        /* .slick-dots {
			position: relative;
			top: 0;
			z-index: 10;
			left: 0;
			float: right;

		}
		
		.slick-slider-dots {
			margin-bottom: 0px;
			position: absolute;
			width: 100%;
			bottom: 0;
		} */

        .responsive_img {
            width: 100%;
            height: auto;
            object-fit: cover;
            /* Prevent the image from stretching. So it crops the image to prevent from awkward stretching */
        }

        .webcard {
            height: 44px;
        }

        .cVhight {
            border: 0px;
        }

        @media (max-width: 1400px) {


            .chight {
                height: 100% !important;
            }


        }

        @media screen and (max-width: 1200px) {



            .chight {
                height: 92% !important;
            }

            .videoSize {
                height: 530px;
            }

            .limit-line-custom {
                -webkit-line-clamp: 2;
            }
        }

        @media screen and (max-width: 1100px) {


            .chight {
                height: 92% !important;
            }
        }

        @media screen and (max-width: 1050px) {


            .chight {
                height: 91% !important;
            }

            .cmobile {
                padding: 1rem 0rem 0rem 0rem;
            }
        }

        @media screen and (max-width: 1024px) {


            .chight {
                height: 96.2% !important;
            }


        }



        @media screen and (max-width: 990px) {


            .divRes {
                margin-bottom: 15px;
            }
        }



        @media screen and (max-width: 850px) {






            .chight {
                border: 0px;
                height: 95.4% !important;
            }

        }



        @media screen and (max-width: 800px) {

            .newsMT {
                margin-top: 0rem !important;
            }



            .chight {
                border: 0px;

            }

            .imgCard {
                border: 0px;
                width: 100%;
                height: auto;
            }

            .limit-line-custom {
                font-size: 14px;
                margin-bottom: 5px;
                margin-top: -15px;
            }

            .videoSize {
                height: 250px;
            }
        }
    </style>
</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <div class="container mt-2">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                <h1 class="text-dark"><?php echo $title ?></h1>
                <hr>
            </div>
        </div>
    </div>

    <?php echo $cat_vdosssss;
  
    ?>
    
    
    <?php
    if(empty($myid)){
        echo $byCats;
    }
    ?>
    <div class="loadmore"></div>









    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>
    <script src="front/assets/js/slick.min.js"></script>
    <script src="front/assets/js/fontawesome.min.js"></script>
    <script src="front/assets/js/5d7fe28a13.js" crossorigin="anonymous"></script>

    <script type='text/javascript'>
        myVideos = <?php echo json_encode($myVideos) ?>;

        $(document).ready(function() {

            $('body').on('click', '.link-find', function() {

                me = $(this);

                $('.modal-content').html(myVideos[me.attr('data-id')])

                $('#myModal').modal('show');

                

                return false;
            });

            $("#myModal").on('hidden.bs.modal', function (e) {
                $("#myModal iframe").attr("src", $("#myModal iframe").attr("src"));
            });
        });

        
    </script>

    <div class="mt-5"></div>
    <?php echo view('front/components/footer') ?>
</body>

<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe class="videoSize my-ifrm" style="width: 100%; height:205px" src="https://www.youtube.com/embed/FycVlc9TUPM" title="<b>นักตบสาวไทยชนเจ้าภาพ เพื่อแชมป์สมัยที่ 15</b>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

</html>