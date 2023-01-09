
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

    <div class="container fit-height">
        <div class="row">
            <div class="col-sm-6 mt-4 mb-2">
                <h2>ข้อมูลจัดการข่าวสารและกิจกรรม</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="blogAdd.php" class="btn-add-blog">
                    เพิ่มจัดการข่าวสารและกิจกรรม
                </a>
            </div>
            <div class="col-lg-12 col-sm-12 block-table mt-3">
                <table id="dataTable" class="display nowrap pt-2" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>รูป</th>
                            <th>หัวข้อบล๊อค</th>
                            <th>ผู้โพส</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <img src="./assets/img/image-cover-blog.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>โครงการบ้านสังคมคุณภาพ #โฮมการ์เด้นวิลล์ สามยอด 2 พื้นที่กว้าง ไซส์ใหญ่ XXL</td>
                            <td>Thannakrit</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="blogEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <img src="./assets/img/image-cover-blog.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>โครงการบ้านสังคมคุณภาพ #โฮมการ์เด้นวิลล์ สามยอด 2 พื้นที่กว้าง ไซส์ใหญ่ XXL</td>
                            <td>Thannakrit</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="blogEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>
                                <img src="./assets/img/image-cover-blog.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>โครงการบ้านสังคมคุณภาพ #โฮมการ์เด้นวิลล์ สามยอด 2 พื้นที่กว้าง ไซส์ใหญ่ XXL</td>
                            <td>Thannakrit</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="blogEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>
                                <img src="./assets/img/image-cover-blog.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>โครงการบ้านสังคมคุณภาพ #โฮมการ์เด้นวิลล์ สามยอด 2 พื้นที่กว้าง ไซส์ใหญ่ XXL</td>
                            <td>Thannakrit</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="blogEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>
                                <img src="./assets/img/image-cover-blog.png" alt="" width="50px" height="50px" style="border-radius: 5px;">
                            </td>
                            <td>โครงการบ้านสังคมคุณภาพ #โฮมการ์เด้นวิลล์ สามยอด 2 พื้นที่กว้าง ไซส์ใหญ่ XXL</td>
                            <td>Thannakrit</td>
                            <td><a class="btn btn-sm btn-success btn-edit" href="blogEdit.php"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i class="fas fa-trash-alt"></i></button></td>
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