
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
                <h2>ข้อมูลผู้ติดต่อ</h2>
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
                            <th>หมายเลขโทรศัพท์</th>
                            <th>อีเมล</th>
                            <th>หัวข้อ</th>
                            <th>ความคิดเห็น</th>
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
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: 'page/admin-assets/json/language.json',
                },
                ajax: 'page/admin-assets/data/contactusData.txt',
                columnDefs: [{
                    targets: -1,
                    data: null,
                    defaultContent: '</button><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button>',
                }, ],
            });

            $('#dataTable tbody').on('click', '.btn-del', function() {
                $(this).closest("tr").remove();
            });
        });
    </script>