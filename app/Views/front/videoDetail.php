<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $params['title'] ?></title>


    <?php echo @$tag ?>
    <?php echo @$sub_name ?>


    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/css/swiper.min.css">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">


    <style>
        .text-muted {
            font-size: 14px;
        }

        .test {
            bottom: 0px;
        }

        html,
        body {
            min-height: 100vh;
        }

        html {
            background-color: #FFF;
            text-rendering: optimizeLegibility;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            color: #444;
            background-attachment: fixed;
        }

        span.keyboard-key {
            background: #EFF0F2;
            margin: 0 4px;
            padding: 4px 8px;
            border-radius: 4px;
            border-top: 1px solid #F5F5F5;
            box-shadow: inset 0 0 25px #E8E8E8, 0 1px 0 #C3C3C3, 0 2px 0 #C9C9C9, 0 2px 3px #333;
            line-height: 23px;
            font-family: "Arial", sans-serif;
            font-size: 12px;
            font-weight: bold;
            text-shadow: 0px 1px 0px #F5F5F5;
            white-space: nowrap;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        a {
            
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .swiper-wrapper {
            z-index: 0;
        }

        .subImgs {
            width: 100%;
            /* height: 210px; */
            object-fit: cover;
        }

        @media screen and (max-width: 376px) {
            .subImgs {
                width: 100%;
                height: 170px;
                object-fit: cover;
            }
        }

        @media screen and (max-width: 320px) {
            .subImgs {
                width: 100%;
                height: 135px;
                object-fit: cover;
            }
        }

        @media screen and (max-width: 280px) {
            .subImgs {
                width: 100%;
                height: 130px;
                object-fit: cover;
            }
        }
    </style>


    <script>
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

</head>

<body>

    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->


    <div class="container mt-3">

        <?php echo @$breadCum ?>
        <div class="row">
            <div class="col-lx-8 col-lg-8 col-md-12 col-12">
            <h1><?php echo $title ?></h1>
            </div>
            <div class="col-lx-4 col-lg-4 col-md-12 col-12"></div>
        </div>


        <!-- <h5 style="color:#999999">โดย <?php //echo @$createBy
                                            ?></h5> -->
        <p class="mt-3" style="color:#999999"><?php echo dateFormat(@$dateRelease, 1) ?></p>

        <div style="
    display: grid;
    grid-template-columns: auto auto;
    justify-content: start;
    align-items: center;
">

          <!--  <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false">Tweet</a>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>-->



            <!-- Your share button code -->
			
			
         <!--<a style="margin-left:10px;" class="fb-share-button" data-href="<?php echo  current_url() ?>" data-layout="button_count"></a> -->

        </div>

        <div class="col-lg-4 col-md-6">
		
          <!-- <a class="fb-share-button" data-href="<?php echo  current_url() ?>">
			<img alt="Facebook" width="7%" src="front/asset/png/facebookicon.png">
		   </a>-->
			<div class="share-content">
			
			
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo  current_url() ?>" target="_blank" style="text-decoration: none;">
					<img alt="Facebook" src="upload/tb_social_media_links/83fb3953048826187b06827840b53077.png">
				</a>
				<a href="https://twitter.com/intent/tweet?text=<?php echo $params['title'] ?> <?php echo  current_url() ?>" target="_blank" style="text-decoration: none;">
				 

					<img style="" src="upload/tb_social_media_links/5e6c244ed75d54b4d461223f938db4a9.png" alt="twitter" class="">
				</a>


				<a target="_blank" href="https://social-plugins.line.me/lineit/share?url=<?php echo  current_url() ?>" style="text-decoration: none;"><img alt="Line" src="front/tb_social_media_links/774a131bf1f6fe2a6996bf2e857d9547.png"></a>
				
				
				<a href="#" class="copy-url">
					<img alt="Facebook" src="front/tb_social_media_links/link-png-icon-1.png">
					<div class="copy-popup">คัดลอกแล้ว</div>
					
				</a>
			 
				<style type="text/css">
					.copy-popup {
						position: absolute;
						left: 125%;
						color: #604a4a;
						top: -1px;
						padding: 3px;
						display: none;
						font-weight: bold;
						text-align: center;
						background-color: #ffcd00;
						width: 128px;
					}
					.copy-url{
						position: relative;
					}
					.copy-notification {
						color: #ffffff;
						background-color: rgba(0,0,0,0.8);
						padding: 20px;
						border-radius: 30px;
						position: fixed;
						top: 50%;
						left: 0%;
						width: 100%;
						margin-top: -30px;
						display: none;
						text-align: center;
					}

					.share-content img{
						width: 30px;
						height: 30px;
					}

				</style>

				    <script type="text/javascript">

        $(document).ready(function () {
            $(".copy-url").click(function (event) {
                event.preventDefault();
                CopyToClipboard( "<?php echo  current_url() ?>", true, "Copied <?php echo  current_url() ?> to clipboard");
            });
        });

        function CopyToClipboard(value, showNotification, notificationText) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();

            if (typeof showNotification === 'undefined') {
                showNotification = true;
            }
            if (typeof notificationText === 'undefined') {
                notificationText = "Copied to clipboard";
            }

            var notificationTag = $( ".copy-popup" );
			
         

			notificationTag.fadeIn("fast", function () {
				
				 
				setTimeout(function () {
					notificationTag.fadeOut("slow", function () {
						//notificationTag.remove();
					});
				}, 300);
			});
        
        }
    </script>

	
				 
			</div>

		   

        </div>
        <br />
    </div>


    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-12 col-12">
                <div style="width: 100%;" class="news-pra">
                    
					
					<div class="">				
                        <?php echo $videoDetail?>
					    <?php echo @html_entity_decode($des) ?>
					 
					</div>
 			<style>
			.load-des h2 {
				font-size: 100%;
				font-weight: bold;
			}
			</style>


                </div>
            </div>
            <div class="col-md-4 webview">
                <img src="front/assets/img/adsnew.png" class="img-fluid text-center" style="width: 100%;" alt="">
                <h4 class=" text-center" style="margin-bottom: -5px;margin-top: 20px; font-weight: bold; color: black;">ข่าวยอดนิยม</h4>
                <hr>
                <?php echo $newsHighlight ?>

            </div>
           

            

            
            <style>
                .btn-tages {
                    background-color: #fff;
                    border: 1px solid #f0b53b;
                    border-radius: 99px;
                }

                @media screen and (max-width: 320px) {
                    .btn-tages {
                    background-color: #fff;
                    border: 1px solid #f0b53b;
                    border-radius: 99px;
                    overflow: hidden;
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                }
                }
            </style>
            <?php echo getAds($params) ?>

        </div>
    </div>


    <?php echo $NewsInteres ?>

    <div class="row" style="margin: 0px 10px 0px 10px;">
        <div class="col-xl-4 col-lg-4 col-md-12 col-12 mobileview">
            <a href="#"><img src="front/assets/img/adsnew.png" class="mb-2 text-center" alt="" style="width: 100%; height: auto;"></a>
            <h4 class="mt-3 text-center" style="color: black; font-weight:bold;">ข่าวยอดนิยม</h4>
            <hr class="pagealine " style="margin: 0.5rem 0;">
            
            <?php echo $newsHighlight ?>
            
            
        </div>
    </div>



    <style>
        .text-muted {
            font-size: 14px;
        }
    </style>




    <!--footer-->

    <!--footer-->

    <!-- bootstrap bundle -->

    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/js/swiper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="<?php echo base_url('front/asset/js/img.js') ?>"></script>




    <?php echo view('front/components/footer') ?>
</body>

</html>