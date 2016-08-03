<?php
	if($_GET['paid'] > 0 && $_GET['id'] > 0){
		$payment = $conn->prepare("UPDATE breakdown set paydate = now(), state = 1 where breakdown_id = ? and state = 0");
		$payment->bind_param("i", $_GET['paid']);
		if($payment->execute() === TRUE){
			echo '<script type="text/javascript">alert("Payment successful"); window.close();</script>';
		}else{
			echo '<script type="text/javascript">alert("Payment failed"); window.close();</script>';
		}
	}elseif($_GET['paid'] == 'all' && $_GET['id'] > 0){
		$payment = $conn->prepare("UPDATE breakdown set paydate = now(), state = 1 where loan_id = ? and deadline <= CURDATE() and state = 0");
		$payment->bind_param("i", $_GET['id']);
		if($payment->execute() === TRUE){
			echo '<script type="text/javascript">alert("Payment successful"); window.close();</script>';
		}else{
			echo '<script type="text/javascript">alert("Payment failed"); window.close();</script>';
		}
	}else{
		echo '<script type="text/javascript">alert("restricted"); window.close();</script>';
	}
?>
