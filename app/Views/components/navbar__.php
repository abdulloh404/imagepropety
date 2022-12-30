<style>
    .navbar {
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        background: rgba(255, 255, 255, 0.9);
    }

    .navbar-brand img,
    .offcanvas-header img {
        width: 150px;
    }

    .nav-item {
        padding: 0px 16px;
    }

    .nav-link {
        padding-bottom: 6px;
        font-weight: 500;
    }

    .nav-link:hover {
        border-bottom: 1px solid #000;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
        padding-bottom: 5px;
    }

    .nav-link-language {
        padding-bottom: 5px;
        color: #0E3F54;
        font-size: 24px
    }

    .nav-link-language:hover {
        color: #000;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
    }

    .offcanvas-body {
        font-size: 16px;
        font-weight: 600px;
    }

    .dropdown-menu[data-bs-popper] {
        top: 100%;
        left: -50%;
        margin-top: var(--bs-dropdown-spacer);
    }
</style>

<nav class="navbar navbar-expand-xl sticky-top">
    <div class="container p-md-0 px-2">
        <a class="navbar-brand" href="index.php">
            <img src="page/assets/img/logo.png" alt="">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <img src="page/assets/img/logo.png" alt="">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-xl-flex align-items-center">
                <ul class="navbar-nav mx-auto pe-3">
                    <li class="nav-item">
                        <a class="nav-link active-1" aria-current="page" href="index.php">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active-2" href="<?php echo front_link(2) ?>">โครงการของเรา</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active-3" href="<?php echo front_link(3) ?>">โปรโมชั่น</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active-4" href="<?php echo front_link(7) ?>">ข่าวสารและกิจกรรม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active-5" href="<?php echo front_link(11) ?>">เกี่ยวกับเรา</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active-6" href="<?php echo front_link(8) ?>">ติดต่อเรา</a>
                    </li>
                </ul>
                <ul class="navbar-nav mx-auto pe-3">
                    <li class="nav-item dropdown pt-xl-0 pt-lg-4 pt-md-4 pt-4">
                        <a class="nav-link-language dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            EN
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">EN</a></li>
                            <li><a class="dropdown-item" href="#">TH</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>