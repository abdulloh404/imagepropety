<!DOCTYPE html>
<html lang="en">

<?php echo view('components/adminheader') ?>
<!-- <link rel="stylesheet" href="page/assets/css/index.css">
<link rel="stylesheet" href="page/assets/css/about.css">
<link rel="stylesheet" href="page/assets/css/blog.css">
<link rel="stylesheet" href="page/assets/css/contact-us.css"> -->


<body>

	<!-- nav -->
    <?php echo view('components/adminnavbar') ?>
    <!-- nav -->
    <?php echo $page; ?>
    <?php echo view('components/adminfooter') ?>
    <!-- <div class="fixed-bottom">
    	<?php //echo view('components/adminfooter') ?>
    </div> -->

</body>

</html>