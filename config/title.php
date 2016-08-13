<?php
	$page = array(
				"loan" => "New Loan Application",
				"list" => "Loan List",
				"due" => "Due List",
				"audit" => "Audit Logs",
				"view" => "View Loan",
				);

	foreach($page as $x => $tag) {
	    if(isset($_GET['action']) && $_GET['action'] == $x){
			$title = $tag;
	    }elseif(isset($_GET['module']) && $_GET['module'] == $x){
			$title = $tag;
	    }elseif(isset($_SESSION['acc_id']) && isset($_GET['module']) != $x){
			$title = "Dashboard";
		}elseif(!isset($_SESSION['acc_id'])){
			$title = "Login Page";
		}
	}
?>