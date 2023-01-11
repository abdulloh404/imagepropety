<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:url" content="<?php echo front_link($id, $myid) ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $params['title'] ?>" />
    <meta property="og:description" content="<?php echo $params['title'] ?>" />
    <meta property="og:image" content="<?php echo $ImgUrl ?>" />
    <title><?php echo $params['title'] ?></title>

    <?php echo $tag ?>


    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/css/swiper.min.css">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">

    <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>

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

    .wrapper-iframe {
        position: relative;
        overflow: hidden;
        width: 100%;
        padding-top: 56.25%;
    }

    .wrapper-iframe>iframe {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
    }

    .wrapper-img {
        position: relative;
        overflow: hidden;
        width: 100%;
        padding-bottom: 79.5%;
    }

    .more-img-wrapper>iframe {
        max-width: 100%;
    }

    @media screen and (max-width: 820px) {
        .wrapper-img {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding-bottom: 90.5%;
        }
    }

    @media screen and (max-width: 450px) {
        .wrapper-img {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding-bottom: 190.5%;
        }
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

    <style class="embedly-css">
    .card>.hdr {
        display: none;
    }

    .card>.brd {
        display: none;
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

        <?php echo $breadCum ?>
        <div class="row">
            <div class="col-lx-8 col-lg-8 col-md-12 col-12">
                <h1><?php echo $title ?></h1>
            </div>
            <div class="col-lx-4 col-lg-4 col-md-12 col-12"></div>
        </div>

        <p class="mt-3" style="color:#999999"><?php echo dateFormat(@$dateRelease, 1) ?></p>

        <div style="
            display: grid;
            grid-template-columns: auto auto;
            justify-content: start;
            align-items: center; ">

        </div>


        <div class="col-lg-4 col-md-6">

            <div class="share-content">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo front_link($id, $myid) ?>"
                    target="_blank" style="text-decoration: none;">
                    <img alt="Facebook" src="upload/tb_social_media_links/83fb3953048826187b06827840b53077.png">
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?php echo $params['title'] ?> <?php echo  current_url() ?>"
                    target="_blank" style="text-decoration: none;">


                    <img src="upload/tb_social_media_links/5e6c244ed75d54b4d461223f938db4a9.png" alt="twitter" class="">
                </a>


                <a target="_blank" href="https://social-plugins.line.me/lineit/share?url=<?php echo  current_url() ?>"
                    style="text-decoration: none;"><img alt="Line"
                        src="front/tb_social_media_links/774a131bf1f6fe2a6996bf2e857d9547.png"></a>


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

                .copy-url {
                    position: relative;
                }

                .copy-notification {
                    color: #ffffff;
                    background-color: rgba(0, 0, 0, 0.8);
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

                .share-content img {
                    width: 30px;
                    height: 30px;
                }
                </style>

                <script type="text/javascript">
                $(document).ready(function() {
                    $(".copy-url").click(function(event) {
                        event.preventDefault();
                        CopyToClipboard("<?php echo  current_url() ?>", true,
                            "Copied <?php echo  current_url() ?> to clipboard");
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

                    var notificationTag = $(".copy-popup");



                    notificationTag.fadeIn("fast", function() {


                        setTimeout(function() {
                            notificationTag.fadeOut("slow", function() {
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


                    <div class="product-gallery">
                        <div class="product-photo-main">

                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?php
                                    if (!empty($imgSwiper)) {
                                        echo $imgSwiper;
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </div>
                                <!-- <div class="swiper-pagination"></div> -->
                            </div>
                        </div>
                        <?php /* 
                        <div class="product-photos-side">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?php echo $ImgSwiperSide ?>
                    </div>
                </div>
            </div>
            */ ?>
        </div>

        <div class="load-des mt-3">

            <?php echo @$sub_title ?>
            <?php echo html_entity_decode(@$des) ?>

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
        <h4 class=" text-center" style="margin-bottom: -5px;margin-top: 20px; font-weight: bold; color: black;">
            ข่าวยอดนิยม</h4>
        <hr>
        <?php echo $newsHighlight ?>

    </div>


    <div class="product-gallery-full-screen">
        <div class="swiper-container gallery-top">
            <div class="swiper-wrapper">
                <?php echo $zoomImgSwiper ?>
            </div>
            <div class="swiper-button-next swiper-button-white">
                <svg fill="#FFFFFF" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" />
                </svg>
            </div>
            <div class="swiper-button-prev swiper-button-white">
                <svg fill="#FFFFFF" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                </svg>
            </div>
            <div class="gallery-nav">
                <div class="swiper-pagination"></div>
                <ul class="gallery-menu">
                    <li class="zoom">
                        <svg class="zoom-icon-zoom-in" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M12 10h-2v2H9v-2H7V9h2V7h1v2h2v1z" />
                        </svg>
                        <svg class="zoom-icon-zoom-out" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path
                                d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14zM7 9h5v1H7z" />
                        </svg>
                    </li>
                    <li class="fullscreen">
                        <svg class="fs-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" />
                        </svg>
                        <svg class="fs-icon-exit" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z" />
                        </svg>
                    </li>
                    <li class="close">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            <path d="M0 0h24v24H0z" fill="none" />
                        </svg>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php echo $tags ?>
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

    <div class="mt-5"></div>

    <div class="row" style="margin: 0px 10px 0px 10px;">
        <div class="col-xl-4 col-lg-4 col-md-12 col-12 mobileview">
            <a href="#"><img src="front/assets/img/adsnew.png" class="mb-2 text-center" alt=""
                    style="width: 100%; height: auto;"></a>
            <h4 class="pt-4 text-center" style="color: black; font-weight:bold;">ข่าวยอดนิยม</h4>
            <hr class="pagealine ">

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <?php /* <script src="<?php echo base_url('front/asset/js/img.js') ?>"></script> */ ?>
    <script async charset="utf-8" src="//cdn.embedly.com/widgets/platform.js"></script>
    <!-- <script charset="utf-8" src="//cdn.iframe.ly/embed.js?api_key=d2d5a82122c4917b78b2b8da778e35c4"></script> -->

    <script>
    document.querySelectorAll('oembed[url]').forEach(element => {
        // Create the <a href="..." class="embedly-card"></a> element that Embedly uses
        // to discover the media.
        const anchor = document.createElement('a');

        anchor.setAttribute('href', element.getAttribute('url'));
        anchor.className = 'embedly-card';
        // $( ".embedly-card" ).find( "hdr" ).css( "background-color", "red" );
        element.appendChild(anchor);
    });

    // document.querySelectorAll( 'oembed[url]' ).forEach( element => {
    //     iframely.load( element, element.attributes.url.value );
    // } );
    </script>






    <?php echo view('front/components/footer') ?>
</body>

</html>