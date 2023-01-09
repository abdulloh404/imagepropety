<!DOCTYPE html>
<html lang="en">

<!-- head -->
<?php include('./components/header.php') ?>
<!-- head -->

<style>
    body {
        background-color: #FFFFFF;
    }

    .signin-form {
        background: #FFFFFF;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.25);
        border-radius: 5px;
        padding: 60px 10px;
        margin-top: 50px;
    }

    .signin-form>h1 {
        font-weight: 600;
        line-height: 36px;
        color: #0967A8;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-input {
        display: flex;
        flex-direction: column;
    }

    .form-input>label {
        font-weight: 500;
        line-height: 15px;
        color: rgba(0, 0, 0, 0.73);
        margin-left: 15px;
        margin-top: 20px;
    }

    .form-input>input {
        background: #FFFFFF;
        border: 1px solid rgba(0, 0, 0, 0.7);
        border-radius: 5px;
        width: 100%;
        height: 45px;
        padding-left: 20px;
        margin-top: 5px;
    }

    .form-checkRemember {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5px;
    }

    .button-group {
        display: flex;
        margin-top: 50px;
    }

    .button-group>button[type=submit] {
        background: #007299;
        color: #ffffff;
        border: 2px solid #007299;
        border-radius: 5px;
        width: 50%;
        padding: 10px 15px;
        margin-right: 5px;
    }

    .button-group>button[type=submit]:hover {
        background: #ffffff;
        color: #007299;
        transition: 0.5s;
    }

    .button-group>button[type=reset] {
        background: #ffffff;
        color: #007299;
        border: 2px solid #007299;
        border-radius: 5px;
        width: 50%;
        padding: 10px 15px;
        margin-left: 5px;
    }

    .button-group>button[type=reset]:hover {
        background: #007299;
        color: #ffffff;
        transition: 0.5s;
    }

    .signup-link {
        text-decoration: none;
        color: #007299;
    }

    .signup-link:hover {
        color: #000000;
    }
</style>

<body>

    <div class="container">
        <div class="row mt-4 mb-4">
            <div class="col-xl-4 col-lg-3 col-md-12 col-12"></div>
            <div class="col-xl-4 col-lg-6 col-md-12 col-12">
                <form action="">
                    <div class="signin-form">
                        <h1>เข้าสู่ระบบ</h1>
                        <div class="form-input">
                            <label for="">อีเมล</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-input">
                            <label for="">รหัสผ่าน</label>
                            <input type="password" name="password">
                        </div>
                        <div class="form-checkRemember">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="">
                                <label class="form-check-label" for="">
                                    จดจำฉันไว้ในระบบ
                                </label>
                            </div>
                        </div>
                        <div class="button-group">
                            <button type="submit">เข้าสู่ระบบ</button>
                            <button type="reset">เคลียร์</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 col-12"></div>
        </div>
    </div>

    <!-- footer -->
    <div class="fixed-bottom">
    <?php include('./components/footer.php') ?>
    </div>
    
    <!-- footer -->

</body>

</html>