<style>
    .left-item-nav {
        position: relative;
        display: flex;
    }

    .btn-menu {
        display: block;
        background-color: #f8f9fa;
        border: 1px solid #f8f9fa;
        margin-left: 15px;
    }

    .navbar-brand {
        margin-left: 15px;
    }

    .form-search {
        display: flex;
        position: relative;
        height: 35px;
        align-items: center;
        justify-content: end;
    }

    .form-search>input {
        padding: 10px;
        border-radius: 3px;
        border: 1px solid #d9d9d9;
        width: 95%;
    }

    .form-search>button {
        position: absolute;
        background-color: #fff;
        border: 1px solid #fff;
        right: 5px;
        top: 5px
    }

    .btn-signout {
        padding: 5px 10px;
        background-color: #007299;
        border-radius: 5px;
        border: 1px solid #007299;
        color: #fff;
    }

    .btn-signout:hover {
        background-color: #fff;
        border: 1px solid #007299;
        color: #007299;
        transition: 0.5s;
    }

    .offcanvas {
        position: fixed;
        bottom: 0;
        z-index: 1045;
        display: flex;
        flex-direction: column;
        max-width: 100%;
        color: var(--bs-offcanvas-color);
        visibility: hidden;
        background-color: var(--bs-offcanvas-bg);
        background-clip: padding-box;
        outline: 0;
        transition: transform .3s ease-in-out;
    }

    .offcanvas.offcanvas-start {
        top: 0;
        left: 0;
        width: 20%;
        border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
        transform: translateX(-100%);
    }

    .side-menus {
        display: flex;
        flex-direction: column;
        margin: 0px 0px 0px -35px;
    }

    .side-menus>li {
        list-style: none;
        padding: 15px;
    }

    .side-menus>li>a {
        color: #333;
        padding: 10px;
        background-color: #fff;
        border: 1px solid #fff;
        border-radius: 3px;
    }

    .side-menus>li>a:hover {
        color: #fff;
        background-color: #000;
        border: 1px solid #000;
        border-radius: 3px;
        transition: 0.5s;
    }

    @media screen and (max-width: 820px) {
        .offcanvas.offcanvas-start {
            top: 0;
            left: 0;
            width: 50%;
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }

    @media screen and (max-width: 450px) {
        .offcanvas.offcanvas-start {
            top: 0;
            left: 0;
            width: 90%;
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }
</style>


<nav class="navbar navbar-expand-lg bg-light" style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
    <div class="container">
        <div class="left-item-nav">
            <button class="btn-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuOffcanvas" aria-controls="menuOffcanvas">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="<?php echo front_link(1) ?>"><img src="page/admin-assets/img/logo.png" alt="" width="100px" height="45px"></a>
        </div>
        <div class="d-flex">
            <a href="signIn.php" class="btn-signout"><i class="fas fa-sign-out-alt"></i>&nbsp; ลงชื่อออก</a>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="menuOffcanvas" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="form-search mb-3">
            <input type="search" placeholder="Search" aria-label="Search">
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>
        <ul class="side-menus">
            <li><a href="<?php echo front_link(14) ?>"><i class="fas fa-tachometer-alt"></i>&nbsp; แดชบอร์ด</a></li>
            <li><a href="<?php echo front_link(15) ?>"><i class="fas fa-image"></i>&nbsp; จัดการแบนเนอร์</a></li>
            <li><a href="<?php echo front_link(16) ?>"><i class="fas fa-building"></i>&nbsp; จัดการโครงการ</a></li>
            <li><a href="<?php echo front_link(17) ?>"><i class="fas fa-clipboard"></i>&nbsp;
                    จัดการข้อมูลผู้ลงทะเบียน</a></li>
            <li><a href="<?php echo front_link(18) ?>"><i class="fas fa-star"></i></i>&nbsp; จัดการโปรโมชั่น</a></li>
            <li><a href="<?php echo front_link(19) ?>"><i class="fas fa-ad"></i>&nbsp; จัดการข่าวสารและกิจกรรม</a></li>
            <li><a href="<?php echo front_link(20) ?>"><i class="fas fa-address-card"></i>&nbsp; จัดการเกี่ยวกับเรา</a>
            </li>
            <li><a href="<?php echo front_link(21) ?>"><i class="fas fa-file-contract"></i>&nbsp; จัดการติดต่อเรา</a>
            </li>
        </ul>
    </div>
</div>