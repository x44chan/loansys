<?php
	unset($_SESSION['acc_id']);
	unset($_SESSION['name']);
	unset($_SESSION['level']);
	$_SESSION['logout'] = "1";
?>	
	<script type="text/javascript"> 
		window.location.replace("/loan");		
	</script>	
	