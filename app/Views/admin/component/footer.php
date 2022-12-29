<div class="main-footer ht-40">
	<div class="container-fluid pd-t-0-f ht-100p">
		<p>© <span class="main-color">FM91BKK</span> website -All Rights Reserve 2022 
		<?php 
		if(isset($_SESSION['u']) && $_SESSION['u']->super_admin == 1){
			echo getConfigLink( $params );
		}else{
			echo '';
		}
		 ?>
	</p>
	</div>
</div>
<div class="chat-scroll"></div>
