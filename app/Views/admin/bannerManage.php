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
            <h2>การจัดการแบนเนอร์</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <a href="<?php echo front_link(23) ?>" class="btn-add-blog">
                เพิ่มแบนเนอร์
            </a>
        </div>
        <div class="col-lg-12 col-sm-12 block-table mt-3">
            <table id="dataTable" class="display nowrap pt-2" style="width:100%">
                <thead>
                    <tr>
                        <th>ลำดับ</th>
                        <th>รูปภาพแบนเนอร์</th>
                        <th>ชื่อ</th>
                        <th>Path</th>
                        <th>วันที่สร้าง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($banners as $banner) : ?>
                    <?php $i = 1 ?>
                    <tr>
                        <td><?php echo ("$i") ?></td>
                        <td>
                            <img src="public/upload/tb_banners/banner1.png" alt="" width="50px" height="50px"
                                style="border-radius: 5px;">
                        </td>
                        <td><?= $banner->name ?></td>

                        <td><a class="btn btn-sm btn-success btn-edit" href="<?php echo front_link(24) ?>"><i
                                    class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i
                                    class="fas fa-trash-alt"></i></button></td>
                    </tr>
                    <tr>
                        <td><?php echo ("$i") ?></td>
                        <td>
                            <img src="./assets/img/001.png" alt="" width="50px" height="50px"
                                style="border-radius: 5px;">
                        </td>
                        <td><?= $banner->created_at ?></td>
                        <td><a class="btn btn-sm btn-success btn-edit" href="<?php echo front_link(24) ?>"><i
                                    class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i
                                    class="fas fa-trash-alt"></i></button></td>
                    </tr>
                    <tr>
                        <td><?php echo ("$i") ?></td>
                        <td>
                            <img src="./assets/img/001.png" alt="" width="50px" height="50px"
                                style="border-radius: 5px;">
                        </td>
                        <td><?= $banner->update_at ?></td>
                        <td><a class="btn btn-sm btn-success btn-edit" href="<?php echo front_link(24) ?>"><i
                                    class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger btn-del"><i
                                    class="fas fa-trash-alt"></i></button></td>
                    </tr>
                    <?php endforeach; ?>
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