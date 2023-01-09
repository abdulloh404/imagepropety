<!DOCTYPE html>
<html lang="en">

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

    .btn-add-blog {
        background-color: #007299;
        border: 1px solid #007299;
        border-radius: 3px;
        color: #fff;
        padding: 5px 10px;
    }

    .btn-add-blog:hover {
        background-color: #fff;
        border: 1px solid #007299;
        border-radius: 3px;
        color: #007299;
        padding: 5px 10px;
    }
</style>

<body>

    <!-- nav -->
    <?php include('./components/navbar.php') ?>
    <!-- nav -->

    <div class="container fit-height">
        <div class="row">
            <div class="col-sm-6 mt-4 mb-2">
                <h2>จัดการเกี่ยวกับเรา</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="aboutUsAdd.php" class="btn-add-blog">
                    เพิ่มข้อมูลเกี่ยวกับเรา
                </a>
            </div>
            <div class="col-lg-12 col-sm-12 block-table mt-3">
                <table id="dataTable" class="display nowrap pt-2" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>รูปภาพปก</th>
                            <th>ชื่อ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <img src="./assets/img/009.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>2010 Image Property Management Co., Ltd.</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="aboutUsEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <img src="./assets/img/009.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>2009 "บ้านดี อยู่สบาย"</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="aboutUsEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
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
                    url: './assets/json/language.json'
                }
            });

            $('#dataTable tbody').on('click', '.btn-del', function() {
                $(this).closest("tr").remove();
            });
        });
    </script>

</body>

</html>