<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo titleWeb() ?></title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">
    <link rel="stylesheet" href="front/assets/style/audio.css?rand=<?php echo rand() ?>">
    <link rel="stylesheet" href="front/asset/css/jquery-ui.css">
    <style>
        .activeRadio {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <div class="container mt-2">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="text-dark">สถานีวิทยุในเครือบริษัท</h1>
                <hr>
            </div>
        </div>
    </div>


    <?php echo $playRadio ?>

    <div class="container" style="margin-bottom: 50px;">
        <div class="row">

            <?php echo $radioHtml ?>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="admin/assets/plugins/jquery/jquery-ui.js"></script>


    <?php echo view('front/components/footer') ?>

    <script>
        $('.radiosbtn').click(function() {
            let val = $(this).attr('data-radio');

            $.ajax({
                url: '<?php front_link($id, 'radioPage') ?>',
                type: "POST",
                data: {
                    token: '<?php echo csrf_hash()?>',
                    ajax: true,
                    data: val
                },
                success: function(respons) {
                    data = JSON.parse(respons);                  

                    if(data.errors == false){
                        $('#radios-text').html(data.radio_name);
                        $('#source-radio').html(data.sources);
                    }
                },
            })

        });
    </script>

</body>

</html>