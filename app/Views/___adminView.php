<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
        content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4" />

    <!-- Title -->
    <title><?php echo titleWeb() ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- Icons css -->
    <link href="admin/assets/css/icons.css" rel="stylesheet">

    <!-- Bootstrap css -->
    <link href="admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!--  Custom Scroll bar-->
    <link href="admin/assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" />

    <!--  Sidebar css -->
    <link href="admin/assets/plugins/sidebar/sidebar.css" rel="stylesheet">

    <!--- Internal Morris css-->
    <link href="admin/assets/plugins/morris.js/morris.css" rel="stylesheet">

    <!--- Style css --->
    <link href="admin/assets/css/style.css" rel="stylesheet">
    <link href="admin/assets/css/boxed.css" rel="stylesheet">
    <link href="admin/assets/css/dark-boxed.css" rel="stylesheet">

    <!--- Dark-mode css --->
    <link href="admin/assets/css/style-dark.css" rel="stylesheet">

    <!---Skinmodes css-->
    <link href="admin/assets/css/skin-modes.css" rel="stylesheet" />
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <style>
    .dropdown-menu {
        top: 46px;
        border-radius: 6px;
        -webkit-box-shadow: 0px 0px 15px 1px rgba(69, 65, 78, 0.2);
        box-shadow: 0px 0px 15px 1px rgba(69, 65, 78, 0.2);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 35px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 13px;
        width: 13px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(13px);
        -ms-transform: translateX(13px);
        transform: translateX(13px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 17px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    </style>
</head>

<body class="main-body">

    <!-- Loader -->
    <div id="global-loader">
        <img src="admin/assets/img/loader.svg" class="loader-img" alt="Loader">
    </div>

    <div class="page">


        <?php echo view('__admin/component/top_bar', $params) ?>


        <div class="jumps-prevent" style="padding-top: 53.2969px;"></div>
        <!-- main-content opened -->

        <?php echo $page ?>




        <!-- Footer opened -->


        <?php echo view('__admin/component/footer', $params) ?>
        <!-- Footer closed -->

    </div>
    <!-- End Page -->

    <!-- Back-to-top -->
    <a href="#top" id="back-to-top"><i class="fas fa-angle-double-up"></i></a>

    <!-- JQuery min js -->


    <!-- Bootstrap Bundle js -->
    <script src="admin/assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="admin/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Ionicons js -->
    <script src="admin/assets/plugins/ionicons/ionicons.js"></script>

    <!-- Moment js -->
    <script src="admin/assets/plugins/moment/moment.js"></script>

    <!--Internal Sparkline js -->
    <script src="admin/assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

    <!-- Moment js -->
    <script src="admin/assets/plugins/raphael/raphael.min.js"></script>

    <!-- Internal Piety js -->
    <script src="admin/assets/plugins/peity/jquery.peity.min.js"></script>

    <!-- Rating js-->
    <script src="admin/assets/plugins/rating/jquery.rating-stars.js"></script>
    <script src="admin/assets/plugins/rating/jquery.barrating.js"></script>

    <!-- P-scroll js -->
    <script src="admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="admin/assets/plugins/perfect-scrollbar/p-scroll.js"></script>

    <!-- Sidemenu js-->
    <script src="admin/assets/plugins/sidebar/sidebar.js"></script>
    <script src="admin/assets/plugins/sidebar/sidebar-custom.js"></script>

    <!-- Eva-icons js -->
    <script src="admin/assets/js/eva-icons.min.js"></script>

    <!--Internal Apexchart js-->
    <!-- <script src="admin/assets/js/apexcharts.js"></script> -->

    <!-- Horizontalmenu js-->
    <script src="admin/assets/plugins/horizontal-menu/horizontal-menu-2/horizontal-menu.js"></script>

    <!-- Sticky js -->
    <script src="admin/assets/js/sticky.js"></script>

    <!-- Internal Map -->
    <script src="admin/assets/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="admin/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

    <!-- Internal Chart js -->
    <script src="admin/assets/plugins/chart.js/Chart.bundle.min.js"></script>

    <!--Internal  index js -->
    <script src="admin/assets/js/index.js"></script>
    <script src="admin/assets/js/jquery.vmap.sampledata.js"></script>

    <!-- custom js -->
    <script src="admin/assets/js/custom.js"></script>
    <script src="admin/assets/js/jquery.vmap.sampledata.js"></script>

    <script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    $('body').on('click', '#btnDelete', function() {

        let link = '<?php echo front_link($id, 'deleteData') ?>';
        let data = $(this).attr('data-link');

        Swal.fire({
            title: 'คุณต้องการลบ ?',
            text: "ข้อมูลนี้อาจมีการใช้งานร่วมกันกับข้อมูลอื่น เมื่อลบแแล้วข้อมูลที่เกี่ยวข้องจะถูกลบไปด้วย",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ต้องการลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: link,
                    type: "GET",
                    data: {
                        val: data
                    },
                    success: function(res) {
                        data = JSON.parse(res);
                        if (data.success == true) {
                            Toast.fire({
                                icon: 'success',
                                title: data.msg
                            }).then((result) => {
                                window.location.reload();
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: data.msg,
                                timer: 3000,
                            })

                        }

                    },
                })


            }
        })
    })

    $('body').on('click', '.switchAct', function() {
        val = $(this).val();
        type = $(this).attr('data-type');

        $.ajax({
            url: '<?php echo front_link($id, 'switchAct') ?>',
            type: 'GET',
            data: {
                code: val,
                type: type
            },
            success: function(respon) {
                var data = JSON.parse(respon);


                if (data.success == true) {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: data.msg,
                        timer: 3000,
                    })

                }

            }
        })
    });
    </script>


</body>

</html>