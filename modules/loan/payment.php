<?php
	// Old Payment System
	/*if(!isset($_GET['paid'])){
		$_GET['paid'] = "";
	}
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
	}*/
?>
<!--<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<i><h4  style="margin-left: -40px;"><span class="icon-coin-dollar"></span><u> Payment </u></h4></i>
		</div>
	</div>
	<form action = "" method="post">
		<div style="border: 1px solid #eee; padding: 0px 10px 10px 10px; border-radius: 5px;">
			<div class="row">
				<div class="col-xs-12">
					<h5><b><u><i><span class="icon-user"></span> Client Information</i></u></b></h5>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-md-6 col-xs-12">
					<label>Select Client<font color = "red"> * </font></label>
					<select class="form-control input-sm" name = "owner" required onchange = "payDetails(this.value)">
						<option value=""> - - - - - - </option>
						<?php
							$customer = "SELECT * FROM customer ORDER BY customer_id";
							$result = $conn->query($customer);
							if($result->num_rows > 0){
								while ($row = $result->fetch_assoc()) {
									echo '<option value = "' . $row['customer_id'] . '"> ( '. $row['customer_id'] . ' ) '. $row['fname'] . ' ' . $row['mname'] . ', ' . $row['lname'] . '</option>';
								}
							}
						?>
					</select>
				</div>
			</div>
			<div id = "paydetails"></div>
		</div>
	</form>
</div>
-->