<style>
    .content {
        position: relative;
        width: 100%;
    }

    .header-line-form::before {
        content: '';
        position: absolute;
        border-bottom: 1px rgba(0, 0, 0, 0.3) solid;
        left: 0;
        height: 100%;
        width: 100%;
        transform: translateY(-50%);
    }

    .btn-confirm {
        border: 1px solid #000;
        background: #fff;
        width: 250px;
    }

    .btn-confirm:hover {
        border: 1px solid #2E69B6;
        background: #2E69B6;
        color: #fff;
    }
</style>
<div class="text-center mb-4">
    <h2>ลงทะเบียนรับข้อมูลโครงการและสิทธิพิเศษ</h2>
    <p class="text-muted">สะดวกกว่าเดิม เมื่อลงทะเบียนผ่าน</p>
    <div class="d-flex justify-content-center align-items-center mb-3">
        <button class="btn btn-primary me-2">Continue with facebook</button>
        <button class="btn btn-secondary">Continue with google</button>
    </div>

</div>
<div class="d-flex text-center mb-3">
    <div class="content">
        <div class="header-line-form"></div>
    </div>
    <h4 class="w-100">หรือกรอกข้อมูลด้านล่าง</h4>
    <div class="content">
        <div class="header-line-form"></div>
    </div>
</div>

<form class="row">
    <div class="col-md-6 col-12 mb-3">
        <input type="text" class="form-control" id="validationCustom01" placeholder="ชื่อ*" required>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <input type="text" class="form-control" id="validationCustom02" placeholder="นามสกุล*" required>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <input type="email" class="form-control" id="validationCustom03" placeholder="อีเมล*" required>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <input type="tel" pattern="[0-9]{10}" class="form-control" id="validationCustom04" placeholder="เบอร์โทรศัพท์มือถือ*" required>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <select class="form-select" id="validationCustom05" required>
            <option selected disabled value="">เลือกจังหวัดที่ท่านอาศัยอยู่</option>
            <option>...</option>
        </select>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <select class="form-select" id="validationCustom06" required>
            <option selected disabled value="">เลือกอำเภอที่ท่านอาศัยอยู่</option>
            <option>...</option>
        </select>
    </div>
    <div class="col-12 mb-3">
        <input type="tel" class="form-control" id="validationCustom04" placeholder="ระบุข้อความ" required>
        <?php /* <textarea name="" id="" cols="30" rows="10" placeholder="ระบุข้อความ" style="resize: none;width:100%;height:150px;padding:10px 15px;"></textarea> */ ?>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <select class="form-select" id="validationCustom07" required>
            <option selected disabled value="">เลือกอำเภอที่ทำงานของท่าน</option>
            <option>...</option>
        </select>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <select class="form-select" id="validationCustom08" required>
            <option selected disabled value="">เลือกอำเภอที่ทำงานของท่าน</option>
            <option>...</option>
        </select>
    </div>
    <div class="mb-3">
        <h5 class="text-muted">ข้อมูลทั่วไป*</h5>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
            <label class="form-check-label" for="flexRadioDefault1">
                20 - 25 ล้าน
            </label>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
            <label class="form-check-label" for="flexRadioDefault2">
                25 - 30 ล้าน
            </label>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault3">
            <label class="form-check-label" for="flexRadioDefault3">
                31 - 35 ล้าน
            </label>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault4">
            <label class="form-check-label" for="flexRadioDefault4">
                35 - 40 ล้าน
            </label>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <select class="form-select" id="validationCustom09" required>
            <option selected disabled value="">ข่าวสารโครงการ</option>
            <option>...</option>
        </select>
    </div>
    <div class="d-grid col-12 my-3 d-flex justify-content-center">
        <button class="btn-confirm p-3 rounded-pill" type="submit">ลงทะเบียน</button>
    </div>
</form>