<style>
    .imggroup {
        width: 25px;
        height: 25px;
        margin-left: 5px;
    }

    .has-search .form-control {
        padding-left: 2.375rem;
    }

    .has-search .form-control-feedback {
        position: absolute;
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 2.375rem;
        text-align: center;
        pointer-events: none;
        color: #aaa;
    }

    .socialicons {
        list-style: none;
        text-decoration: none;
        display: flex;
        margin-top: 15px;
    }

    @media screen and (max-width: 400px) {
        .navbar-toggler {
            margin-left: 290px;
        }
    }
    @media screen and (max-width: 450px) and (min-width: 399px) {
        .navbar-toggler {
            margin-left: 340px;
        }
    }
</style>

<div class="container mt-2 mb-2">
    <div class="row">
        <div class="col-lg-4">

        </div>
        <div class="col-lg-4" style="display: flex;justify-content: center;align-self: flex-end;">
            <h1>สวพ.FM91</h1>
        </div>
        <div class="col-lg-4">
            <ul class="socialicons justify-content-start">
                <li><a href="#"><img src="./assets/img/icon/facebook.png" alt="" class="imggroup"></a></li>
                <li><a href="#"><img src="./assets/img/icon/twitter.png" alt="" class="imggroup"></a></li>
                <li><a href="#"><img src="./assets/img/icon/instargram.png" alt="" class="imggroup"></a></li>
                <li><a href="#"><img src="./assets/img/icon/youtube.png" alt="" class="imggroup"></a></li>
                <li><a href="#"><img src="./assets/img/icon/line.png" alt="" class="imggroup"></a></li>
                <li><a href="#"><img src="./assets/img/icon/soundcloud.png" alt="" class="imggroup"></a></li>
            </ul>
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown1" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" style="margin-left: 280px;">
                    <span>ค้นหา</span>
                </button>
                <div class="form-group has-search collapse navbar-collapse" id="navbarNavDropdown1">
                    <!-- <i class="fas fa-search form-control-feedback"></i> -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16" style="position: absolute;left: 10px;">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                    </svg>
                    <input type="text" class="form-control" placeholder="ค้นหา" style="background-color: #E5E5E5;">
                </div>
            </nav>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <img class="" src="./assets/img/header.png" alt="" style="width: 100%; height: 100%; border-radius: 3px;">
        </div>
    </div>
</div>
<div class="container" style="border-bottom: solid 2px black;">
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <ul class="navbar-nav collapse navbar-collapse justify-content-between" id="navbar">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">หน้าหลัก</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="new.php" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ข่าว
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="new.php">จราจร</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">ทั่วไทย</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">อาชญากรรม</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">การเมือง</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">เศรษฐกิจ</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">ต่างประเทศ</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">เทคโนโลยี</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">กีฬา</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="new.php">บันเทิง</a></li>
                        </ul>
                    </li>
                    <li class="nav-item" id="box3">
                        <a class="nav-link" href="lostandfound.php">ของหายได้คืน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="publicrelation.php">ประชาสัมพันธ์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vdo.php">วีดีโอ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radio.php">สถานีวิทยุในเครือบริษัท</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>

<style>
    .imgcontrol {
        width: 100%;
        height: 250px;
        border-radius: 3px;
    }

    h1 {
        font-weight: bold;
    }

    .active {
        border-bottom: 3px solid #FDD400;
    }

    @media (max-width: 575.98px) {
        .imgcontrol {
            width: 100%;
            height: 120px;
            border-radius: 3px;
        }

        .socialicons {
            justify-content: center !important;
        }
    }
</style>