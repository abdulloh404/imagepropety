<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo base_url() ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo titleWeb()?></title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="page/assets/img/icon/favicon.ico">

    <!-- font -->
    <link rel="stylesheet" href="front/assets/style/font.css">

    <link rel="stylesheet" href="front/assets/style/style.css">


</head>

<body>
    <!--navbar-->
    <?php echo view('front/components/navbar') ?>
    <!--navbar-->

    <style>
    @media (max-width: 575.98px) {
        img {
        width: 60%;
    }
 }

 @media (min-width: 576px) and (max-width: 991.98px) {
    img {
            width: 60%;
        }
 }

    </style>
    <div class="container mainbody"><?php echo $html ?> </div>
    

    
    

    <!--footer-->
    
    <!--footer-->

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('page/assets/js/jquery.form.js') ?>"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   


<?php echo view('front/components/footer') ?></body>

</html>