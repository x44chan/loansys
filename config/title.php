<?php
	$page = array("loan" => "New Loan Application",
				 "newstore" => "Store Management",
				 "storelist" => "Store List");

	foreach($page as $x => $tag) {
	    if(isset($_GET['module']) && $_GET['module'] == $x){
	    	$title = $tag;
	    }elseif(isset($_SESSION['acc_id']) && isset($_GET['module']) != $x){
			$title="Dashboard";
		}elseif(!isset($_SESSION['acc_id'])){
			$title="Login Page";
		}
	}
?>