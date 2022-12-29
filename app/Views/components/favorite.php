<style>
    .swiper {
        width: 100%;
        height: 100%;
        position: relative;
        overflow-x: hidden;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;
        width: 100%;
        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .swiper-slide img {
        display: block;
        width: 300px;
        height: 300px;
        object-fit: cover;
        transition: all 0.3s;
    }

    .swiper-slide a:hover>img {
        filter: brightness(50%);
    }

    .otherProduct>a {
        position: relative;
    }

    .otherProduct>a>span {
        font-size: 1.25rem;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        position: absolute;
        transition: all 0.4s;
        color: #fff;
        width: 100%;
        opacity: 0;
    }

    .otherProduct>a:hover>span {
        opacity: 1;
    }

    @media screen and (max-width:640px) {
        .swiper-slide img {
            width: 150px;
            height: 150px;
        }
    }
    @media screen and (min-width:641px) and (max-width:960px) {
        .swiper-slide img {
            width: 170px;
            height: 170px;
        }
    }
    @media screen and (min-width:961px) and (max-width:1160px) {
        .swiper-slide img {
            width: 200px;
            height: 200px;
        }
    }
    @media screen and (min-width:1161px) and (max-width:1366px) {
        .swiper-slide img {
            width: 225px;
            height: 225px;
        }
    }
</style>

<div class="d-flex text-center mt-3">
    <div class="content">
        <div class="header-line"></div>
    </div>

    <h3 class="w-100">คุณอาจชื่นชอบ</h3>
    <div class="content">
        <div class="header-line"></div>
    </div>
</div>

<div class="swiper productSwiper my-3 pb-5">
    <div class="swiper-wrapper">
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/006.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/001.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/2022-10-20_111519.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/2022-10-20_111442.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/006.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/001.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/2022-10-20_111519.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/2022-10-20_111442.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/006.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/001.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/2022-10-20_111519.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
        <div class="swiper-slide otherProduct">
            <a href="item-detail.php">
                <img src="page/assets/img/2022-10-20_111442.png" alt="">
                <span>โฮมการ์เด้นวิว<br>
                    จอหอ-บายพาส
                </span>
            </a>
        </div>
    </div>
    <div class="swiper-pagination"></div>
</div>
<script>
    var swiper = new Swiper(".productSwiper", {
        slidesPerView: 1,
        slidesPerGroup: 1,
        spaceBetween: 10,
        pagination: {
            el: ".swiper-pagination",
            dynamicBullets: true,
        },
        // Responsive breakpoints
        breakpoints: {
            320: {
                slidesPerView: 2,
                slidesPerGroup: 2,
                spaceBetween: 20
            },
            540: {
                slidesPerView: 3,
                slidesPerGroup: 3,
                spaceBetween: 30
            },   
            1024: {
                slidesPerView: 4,
                slidesPerGroup: 4,
                spaceBetween: 40
            }
        }
    })
</script>