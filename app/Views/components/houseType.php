<style>
    .house-type {
        margin: 20px 0px 20px 0px;
    }

    .house-type-link {
        position: relative;
        text-align: center;
        color: #fff;
    }

    .house-type-link:hover>img {
        filter: brightness(50%);

    }

    .house-type-link>img {
        width: 100%;
        max-height: 384px;
        transition: all 0.3s;
    }

    .house-type-link>h1 {
        position: absolute;
        width: 100%;
        text-align: center;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #ffffff;
        text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25), 0px 4px 4px rgba(0, 0, 0, 0.25),
            0px 4px 4px rgba(0, 0, 0, 0.25), 0px 4px 4px rgba(0, 0, 0, 0.25),
            0px 4px 4px rgba(0, 0, 0, 0.25), 0px 4px 4px rgba(0, 0, 0, 0.25),
            0px 4px 4px rgba(0, 0, 0, 0.25), 0px 4px 4px rgba(0, 0, 0, 0.25);
    }

    @media screen and (max-width: 820px) {
        .house-type-link>h1 {
            font-size: 28px;
        }
    }

    @media screen and (max-width: 450px) {
        .house-type {
            margin: 10px 0px 10px 0px;
        }

        .house-type-link>h1 {
            font-size: 35px;
        }
    }
</style>

<div class="row">
    <div class="col-xl-4 col-lg-4 col-md-4 col-12 m-0">
        <div class="house-type">
            <a class="house-type-link" href="<?php echo front_link(10) ?>">
                <img src="page/assets/img/004.png" alt="">
                <h1>บ้านเดี่ยว</h1>
            </a>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4 col-md-4 col-12 m-0">
        <div class="house-type">
            <a class="house-type-link" href="<?php echo front_link(10) ?>">
                <img src="page/assets/img/005.png" alt="">
                <h1>บ้านแฝด</h1>
            </a>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4 col-md-4 col-12 m-0">
        <div class="house-type">
            <a class="house-type-link" href="<?php echo front_link(10) ?>">
                <img src="page/assets/img/003.png" alt="">
                <h1>อาคารพาณิชย์</h1>
            </a>
        </div>
    </div>
</div>