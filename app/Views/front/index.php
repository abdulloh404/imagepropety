<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo titleWeb()?></title>

    <!-- bootstrap -->
    <link rel="stylesheet" href="front/assets/style/bootstrap.min.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/slick.css">
    <link rel="stylesheet" href="front/assets/style/slick-theme.css">
    <link rel="stylesheet" href="front/assets/style/customSilde.css">

    <link rel="stylesheet" href="front/assets/style/fontawesome.min.css">

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
            font-size: 14px;
            margin-bottom: 0px;
        }

        .cardMoblie{
            height: 325px;
            
        }
        .slick-dots li ul{
            list-style-type: none !important;
        }
       
        .responsive_img {
            width: 100%;
            height: auto;
            object-fit: cover;
            /* Prevent the image from stretching. So it crops the image to prevent from awkward stretching */
        }
        
        
    </style>

</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <div class="container mt-4">
        <div class="row">
            <div class="col-xl-7 col-lg-7 col-md-12 col-12">
                <div id="carouselExampleCaptions" class="carousel slide news-height" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php echo $btnCas?>
                        
                    </div>
                    <div class="carousel-inner">
                        <?php echo $paginationNews ?>

                        
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

            </div>


            <div class="col-xl-5 col-lg-5 col-md-12 col-12 mobile-hidden">

                <div class="row">
                    <?php echo $headerNews ?>
                </div>
                
            </div>
        </div>

        
    </div>
    
    <!-- <div class="pagination slick-slider-dots" role="group" aria-label="Page navigation"></div> -->


    <!-- วิทยุ -->

    <br/>
    <div class="container">
        <div class="mt-3 newsMT">
            <?php echo implode('', $newdivs) ?>
        </div>
    </div>
    
    <!-- ข่าวฮอตฮิต -->





    <?php echo $navVideo ?>

    <div style="clear: both;"></div>
    <div class="container">
        <hr>
        <div class="row">
            <?php echo getVdos() ?>
        </div>
    </div>


    <script>
        /*
	$( function() {
		
		$( '.close-video' ).click( function() {
			
			
			setTimeout(function(){
				 
				$( this ).parents( '.modal' ).find( 'video' ).pause();
			}, 7000);
			 
			
		});
		
	});*/
    </script>
    <br>
    <div class="container mt-4 mt-2">
        <div class="col pt-5 text-center">
            <img src="front/assets/png/ads.png" alt="" class="w-75 text-center">
        </div>
    </div>
    <br>

    <!--footer-->


    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="front/assets/js/5d7fe28a13.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>
    <!-- <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script> -->

    <script src="front/assets/js/slick.min.js"></script>
    <script src="front/assets/js/fontawesome.min.js"></script>

    <script type='text/javascript'>
        $(document).ready(function() {

            i = 1;
            createPagination(i);
            // setInterval(function() {

            //     if (i == 1) {
            //         i = 2;
            //     } else if (i == 5) {
            //         i = 1;
            //     }


            //     createPagination(i);
            //     i++;

            // }, 4000);

            $('.newsSlide').slick({
                autoplay: false,
                dots: true,
                appendDots: $('.slick-slider-dots'),
                arrows: false,
                draggable: true,
                mobileFirst: true ,
                

            });

            $('#pagination').on('click', 'a', function(e) {
                e.preventDefault();

                var link = $(this).attr('data-link');
                $("#NewsLastestList").show("slide", {
                    direction: "up"
                }, 2000);
                createPagination(link);

            });

            function createPagination(pageNum) {

               

                links = '<?php echo front_link(341, 'getLoadNewsPage', NULL, false) ?>';


                $.ajax({
                    url: links,
                    type: 'get',
                    data: {
                        page: pageNum
                    },
                    dataType: 'json',
                    success: function(responseData) {

                        $('#pagination').html(responseData.pagination);

                        paginationData(responseData.NewsData);
                        paginationDateTime(responseData.dateTime);
                    }
                });
            }

            function paginationData(data) {
                $('#NewsLastestList').empty();

                for (news in data) {

                    $('#NewsLastestList').append(data[news]);
                }
            }

            function paginationDateTime(data) {
                $('#NewsLastestDateTime').empty();

                for (dateTime in data) {

                    $('#NewsLastestDateTime').text(data[dateTime]);
                }
            }
        });
    </script>

    <?php echo view('front/components/footer') ?>



</body>

</html>