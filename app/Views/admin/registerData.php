
<style>
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
    <div class="container fit-height">
        <div class="row">
            <div class="col-sm-6 mt-4 mb-2">
                <h2>ข้อมูลผู้ลงทะเบียน</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table id="dataTable" class="display nowrap pt-2" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อ</th>
                            <th>นามสกุล</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>อีเมล</th>
                            <th>จังหวัดที่อาศัย</th>
                            <th>อำเภอที่อาศัย</th>
                            <th>ข้อความ</th>
                            <th>จังหวัดที่ทำงาน</th>
                            <th>อำเภอที่ทำงาน</th>
                            <th>ข้อมูลทั่วไป</th>
                            <th>ข่าวสารโครงการ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                                    <label for="" class="form-label">ชื่อ-นามสกุล</label>
                                    <input type="text" class="form-edit" name="fullname" value="" placeholder="">
                                </div>
                                <div class="modal-edit">
                                    <label for="" class="form-label">เลขที่บัตรประชาชน</label>
                                    <input type="number" class="form-edit" name="id" value="" placeholder="">
                                </div>
                                <div class="modal-edit">
                                    <label for="" class="form-label">เลขที่ผู้เสียภาษี</label>
                                    <input type="number" class="form-edit" name="taxid" value="" placeholder="">
                                </div>
                                <div class="modal-edit">
                                    <label for="" class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="number" class="form-edit" name="telnumber" value="" placeholder="">
                                </div>
                                <div class="modal-edit">
                                    <label for="" class="form-label">อีเมล</label>
                                    <input type="email" class="form-edit" name="email" value="" placeholder="">
                                </div>
                                <div class="modal-edit">
                                    <label for="" class="form-label">เว็บไซด์/โซเชียลมีเดีย</label>
                                    <input type="text" class="form-edit" name="website" value="" placeholder="">
                                </div>
                                <div class="modal-edit">
                                    <label for="" class="form-label">ประเภทสมาชิก</label>
                                    <select class="form-edit" value="">
                                        <option value="0">บุคคลทั่วไป</option>
                                        <option value="1">นิติบุคคล</option>
                                    </select>
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

    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: './assets/json/language.json',
                },
                ajax: './assets/data/registerData.txt',
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
