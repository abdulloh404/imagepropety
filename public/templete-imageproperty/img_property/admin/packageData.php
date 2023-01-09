<!DOCTYPE html>
<html lang="en">

<!-- head -->
<?php include('./components/header.php') ?>
<!-- head -->

<style>
    .collapes-add {
        display: flex;
        flex-direction: column;
        margin: 10px 0px;
    }

    .collapes-add>input[type=file] {
        padding: 0px;
        background: #FFFFFF;
        border: 1px solid transparent;
        border-radius: 5px;
        height: 35px;
        width: 100%;
    }

    .form-add {
        padding-left: 10px;
        background: #FFFFFF;
        border: 1px solid rgba(0, 0, 0, 0.7);
        border-radius: 5px;
        height: 35px;
        width: 100%;
    }

    .btn-addAccountant {
        background-color: #007299;
        border: 1px solid #007299;
        border-radius: 3px;
        color: #fff;
        padding: 5px 10px;
    }

    .modal-edit {
        display: flex;
        flex-direction: column;
        margin: 10px 0px;
    }

    .form-edit {
        padding-left: 10px;
        background: #FFFFFF;
        border: 1px solid rgba(0, 0, 0, 0.7);
        border-radius: 5px;
        height: 35px;
        width: 100%;
    }
</style>

<body>

    <!-- nav -->
    <?php include('./components/navbar.php') ?>
    <!-- nav -->

    <div class="container fit-height">
        <div class="row">
            <div class="col-sm-6 mt-4 mb-2">
                <h2>ข้อมูลแพคเกจ</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn-addAccountant" type="button" data-bs-toggle="modal" data-bs-target="#addPackage">
                    เพิ่มแพคเกจ
                </button>
                <!-- Modal -->
                <div class="modal fade" id="addPackage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="">เพิ่มแพคเกจ</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="">
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ชื่อแพคเกจ</label>
                                        <input type="text" class="form-edit" name="packagename" value="" placeholder="">
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ราคา</label>
                                        <input type="number" class="form-edit" name="price" value="" placeholder="">
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ประเภทแพคเกจ</label>
                                        <select class="form-edit" value="">
                                            <option value="0">รายเดือน</option>
                                            <option value="1">รายปี</option>
                                        </select>
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ประเภทผู้ใช้งาน</label>
                                        <select class="form-edit" value="">
                                            <option value="0">ฟรีแลนซ์</option>
                                            <option value="1">นักบัญชี</option>
                                        </select>
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">รายละเอียด</label>
                                        <input type="text" class="form-edit" name="detail" value="" placeholder="">
                                    </div>

                                    <div class="">
                                        <button type="button" class="btn btn-secondary mt-2 mb-2" data-bs-dismiss="modal">ปิด</button>
                                        <button type="button" class="btn btn-primary mt-2 mb-2">ตกลง</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12 block-table mt-3">
                <table id="dataTable" class="display nowrap pt-2" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อแพคเกจ</th>
                            <th>ราคา</th>
                            <th>ประเภทแพคเกจ</th>
                            <th>ประเภทผู้ใช้งาน</th>
                            <th>รายละเอียด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <!-- Modal -->
                <div class="modal fade" id="editData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="">แก้ไขข้อมูล</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="">
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ชื่อแพคเกจ</label>
                                        <input type="text" class="form-edit" name="packagename" value="" placeholder="">
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ราคา</label>
                                        <input type="number" class="form-edit" name="price" value="" placeholder="">
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ประเภทแพคเกจ</label>
                                        <select class="form-edit" value="">
                                            <option value="0">รายเดือน</option>
                                            <option value="1">รายปี</option>
                                        </select>
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">ประเภทผู้ใช้งาน</label>
                                        <select class="form-edit" value="">
                                            <option value="0">ฟรีแลนซ์</option>
                                            <option value="1">นักบัญชี</option>
                                        </select>
                                    </div>
                                    <div class="modal-edit">
                                        <label for="" class="form-label">รายละเอียด</label>
                                        <input type="text" class="form-edit" name="detail" value="" placeholder="">
                                    </div>

                                    <div class="">
                                        <button type="button" class="btn btn-secondary mt-2 mb-2" data-bs-dismiss="modal">ปิด</button>
                                        <button type="button" class="btn btn-primary mt-2 mb-2">ตกลง</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: './assets/json/language.json',
                },
                ajax: './assets/data/packageData.txt',
                columnDefs: [{
                    targets: -1,
                    data: null,
                    defaultContent: '<button class="btn btn-sm btn-success btn-edit" data-bs-toggle="modal" data-bs-target="#editData"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button>',
                }, ],
            });

            $('#dataTable tbody').on('click', '.btn-del', function() {
                $(this).closest("tr").remove();
            });
        });
    </script>

    <!-- footer -->
    <?php include('./components/footer.php') ?>
    <!-- footer -->

</body>

</html>