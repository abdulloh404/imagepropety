<style>
    .imggroup1 {
        width: 40px;
        height: 40px;
    }

    .bg-footer {
        background-color: #FDD400;
        padding-bottom: 20px;
        font-weight: 700;
    }

    .footer-link a {
        color: #393939;
        line-height: 40px;
        font-size: 16px;
        transition: all 0.5s;
        text-decoration: none;
    }

    .footer-link a:hover {
        color: #ffff;
        background-color: #FDD400;
        padding: 10px;
        border-radius: 5px;
    }

    .footer-mobile {
        display: none;
    }

    @media (max-width: 575.98px) {

        .footer-desktop {
            display: none;
        }

        .footer-mobile {
            display: block;
        }

        .footer-linkk a {
            color: #393939;
            font-size: 12px;
            text-decoration: none;
        }

        .imggroup1 {
            width: 20px;
            height: 20px;
        }
    }

    @media (max-width: 768px) {

        .footer-desktop {
            display: none;
        }

        .footer-mobile {
            display: block;
        }

        .footer-linkk a {
            color: #393939;
            font-size: 10px;
            text-decoration: none;
        }

        .imggroup1 {
            width: 20px;
            height: 20px;
        }
    }

    @media (max-width: 365px) {

        .footer-desktop {
            display: none;
        }

        .footer-mobile {
            display: block;
        }

        .footer-linkk a {
            color: #393939;
            font-size: 8px;
            text-decoration: none;
        }

        .imggroup1 {
            width: 20px;
            height: 20px;
        }
    }

    @media (max-width: 280px) {

        .footer-desktop {
            display: none;
        }

        .footer-mobile {
            display: block;
        }

        .footer-linkk a {
            color: #393939;
            font-size: 6px;
            text-decoration: none;
        }

        .imggroup1 {
            width: 20px;
            height: 20px;
        }
    }

    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
    }
</style>

<?php
$params['limit'] = ' LIMIT 0, 1 ';
//echo radioPageF($params)

?>

<footer class="section bg-footer footer-desktop">
    <div class="container">
        <br>
        <div class="row">
            <div class="col-lg-1 col-md-1">

            </div>
            <div class="col-lg-10 col-md-10">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="text-center">
                            <ul class="list-unstyled footer-link">
                                <li><a href="<?php echo front_link(1) ?>">หน้าหลัก</a></li>
                                <li><a href="<?php echo front_link(19) ?>">ข่าว</a></li>
                                <li><a href="<?php echo front_link(8) ?>">ประชาสัมพันธ์</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="text-center">
                            <ul class="list-unstyled footer-link">
                                <li><a href="<?php echo front_link(3) ?>">ของหายได้คืน</a></li>
                                <li><a href="<?php echo front_link(10) ?>">วีดีโอ</a></li>
                                <li><a href="<?php echo front_link(9) ?>">สถานีวิทยุในเครือบริษัท</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="text-center">
                            <ul class="list-unstyled footer-link">
                                <li><a href="<?php echo front_link(11) ?>">เกี่ยวกับเรา</a></li>
                                <li><a href="<?php echo front_link(2) ?>">ติดต่อเรา</a></li>
                                <li><a href="<?php echo front_link(18) ?>">นโยบายความเป็นส่วนตัว</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-md-1">

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="d-flex justify-content-center">
                <div>
                    <?php echo getSocialIcons(2) ?>
                </div>
            </div>

            <div class="d-flex justify-content-center" style="margin-top: 15px;">
                <span style="font-size: .6em;">© 2017-2022 FM91. All rights reserved.</span>
            </div>
        </div>
        <br>
    </div>
  
</footer>

<footer class="section bg-footer footer-mobile">
    <div class="container-fluid">
        <div class="row" style="padding-top: 15px;">
            <div class="col-4">
                <div class="text-center">
                    <ul class="list-unstyled footer-linkk">
                        <li><a href="<?php echo front_link(1) ?>">หน้าหลัก</a></li>
                        <li><a href="<?php echo front_link(4) ?>">ข่าว</a></li>
                        <li><a href="<?php echo front_link(8) ?>">ประชาสัมพันธ์</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center">
                    <ul class="list-unstyled footer-linkk">
                        <li><a href="<?php echo front_link(3) ?>">ของหายได้คืน</a></li>
                        <li><a href="<?php echo front_link(10) ?>">วีดีโอ</a></li>
                        <li><a href="<?php echo front_link(9) ?>">สถานีวิทยุในเครือบริษัท</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center">
                    <ul class="list-unstyled footer-linkk">
                        <li><a href="<?php echo front_link(11) ?>">เกี่ยวกับเรา</a></li>
                        <li><a href="<?php echo front_link(2) ?>">ติดต่อเรา</a></li>
                        <li><a href="<?php echo front_link(18) ?>">นโยบายความเป็นส่วนตัว</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="d-flex justify-content-center">
                <div>
                    <?php echo getSocialIcons(2) ?>
                </div>
            </div>

            <div class="d-flex justify-content-center" style="margin-top: 15px;">
                <span style="font-size: .6em;">© 2017-2022 FM91. All rights reserved.</span>
            </div>
        </div>
        <br>
    </div>
</footer>


<link rel="stylesheet" href="cookit.min.css" />
<script src="cookit.min.js"></script>
<style>
	#cookit-container a#cookit-link {
		color: #fad04c !important;
	}
	#cookit.hidden{
		display: none;
	}
	
	#cookit-message{
		font-size: 75%;

	}
	
	#cookit-link{
		font-size: 75%;
	}
	
	#cookit-button{
		font-size: 75%;
	}
</style>
<script>

$( function() {

	$.cookit({
		messageText: "เว็บไซต์นี้ใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานเว็บไซต์ให้ผู้ใช้งานและจะรวบรวมข้อมูลพฤติกรรมการใช้งานระบบของผู้ใช้ การเรียกดูเว็บไซต์ของเราในหน้าต่างๆ กรุณายอมรับนโยบายความเป็นส่วนตัวของเรา",
		linkText: "อ่านเพิ่มเติม",
		linkUrl: "<?php echo front_link( 18 ) ?>",
		buttonText: "ยอมรับ",
		backgroundColor: '#1c1c1c',
		messageColor: '#fff',
		linkColor: '#fad04c',
		buttonColor: '#fad04c',
		buttonTextColor: '#00000',
		lifetime: 14, //in days
	});
});

	
	
	
</script>



	
	