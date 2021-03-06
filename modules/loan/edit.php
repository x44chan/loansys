<?php
	if($access->level <= 1){
		echo '<script type = "text/javascript">alert("Restricted.");window.location.replace("loan/list");</script>';
	}else{
?>
<?php
	$loan_id = mysqli_real_escape_string($conn, $_GET['view']);
	$list = "SELECT * FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = '$loan_id' and c.loan_id = '$loan_id' group by b.loan_id";
	$res = $conn->query($list)->fetch_object();
	if($conn->query($list)->num_rows <= 0){
		echo '<script type = "text/javascript">alert("No record found.");window.location.replace("/loan/?module=loan&action=list");</script>';
	}
	$list2 = "SELECT * FROM loan as a, payment as b where a.loan_id = '$loan_id' and b.loan_id = '$loan_id'";
	if($conn->query($list2)->num_rows > 0){
		echo '<script type = "text/javascript">alert("Can\'t edit loan with payment record.");window.location.replace("/loan/?module=loan&action=list");</script>';
	}
?>
<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<i><h4  style="margin-left: -40px;"><span class="icon-coin-dollar"></span> <u>Edit Loan</u></h4></i>
		</div>
	</div>
	<form action = "" method="post">
		<div style="border: 1px solid #eee; padding: 0px 10px 10px 10px; border-radius: 5px;">
			<div class="row">
				<div class="col-xs-12">
					<h5><b><u><i><span class="icon-user"></span> Client Information</i></u></b></h5>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;" id = "select">
				<div class="col-md-6 col-xs-12">
					<label>Select Client<font color = "red"> * </font></label>
					<select class="form-control input-sm" name = "customer" required>
						<option value=""> - - - - - - </option>
						<?php
							$customer = "SELECT * FROM customer ORDER BY customer_id";
							$result = $conn->query($customer);
							if($result->num_rows > 0){
								while ($row = $result->fetch_assoc()) {
									if($res->customer_id == $row['customer_id']){
										$selec = " selected ";
									}else{
										$selec = "";
									}
									echo '<option '. $selec . ' value = "' . $row['customer_id'] . '"> ( '. $row['customer_id'] . ' ) '. $row['fname'] . ' ' . $row['mname'] . ', ' . $row['lname'] . '</option>';
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<hr>
					<h5><b><u><i><span class="icon-coin-dollar"></span> Loan Information</i></u></b></h5>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-md-3 col-xs-12">
					<label>Loan/Principal Amount<font color = "red"> *</font></label>
					<input <?php echo ' value = "'. $res->principal .'" '; ?> type = "text" name = "amount" class="form-control input-sm" placeholder = "Enter Loan Amount" required pattern = "[.0-9]*" autocomplete = "off" onchange="showUser()">
				</div>				
				<div class="col-md-2 col-xs-12">
					<label>Type <font color = "red"> * </font></label>
					<select class="form-control input-sm" name = "type" onchange="showUser()">
						<option <?php if($res->type == 'Daily'){ echo ' selected '; }?> value="Daily"> Daily </option>
						<option <?php if($res->type == 'Weekly'){ echo ' selected '; }?> value="Weekly"> Weekly </option>
						<option <?php if($res->type == 'Monthly'){ echo ' selected '; }?> value="Monthly"> Monthly </option>						
					</select>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>Duration (in numbers)<font color = "red"> * </font></label>
					<input <?php echo ' value = "'. $res->duration .'" '; ?> type = "text" name = "duration" class="form-control input-sm" placeholder = "Enter duration" required pattern = "[0-9]*" onchange="showUser()" autocomplete = "off">
				</div>
				<div class="col-md-3 col-xs-12">
					<label>Start Date <font color = "red"> * </font></label>
					<input <?php echo ' value = "'. $res->startdate .'" '; ?> type = "date" name = "strtdate" class="form-control input-sm" min = "<?php echo $res->startdate;?>" max = "<?php echo date('9999-m-d');?>" required onchange="showUser()" autocomplete = "off">
				</div>
				<div class="col-md-2 col-xs-12">
					<label>Special Rate <font color = "red" id = "asterisk" style="display: none;"> * </font></label>
					<input type = "text" name = "specialrate" class="form-control input-sm" placeholder = "Enter Rate" <?php if($res->specialrate == ""){ echo ' disabled '; } else { echo ' value = "' . $res->specialrate . '" '; }; ?> onchange="showUser()">
					<label><input <?php if($res->specialrate != ""){ echo ' checked '; }?> type = "checkbox" id = "specialrate"> Enable </label>
				</div>
			</div>
			<div id = "details">				
			</div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-xs-12" align="center">
					<hr>
					<button class="btn btn-primary btn-sm" name = "loansub" onclick = "return confirm('Are you sure?');"><span class = "icon-redo2"></span> Update </button>
				</div>
			</div>
		</div>		
	</form>
</div>
<?php
	if($conn->query($list)->num_rows > 0){
		echo '<script type = "text/javascript">$(window).load(function(){ showUser(); });</script>';
	}
	if(isset($_POST['loansub'])){
		$gerate = "SELECT ".strtolower($_POST['type']) ." as rate FROM rate";
		$gerate = $conn->query($gerate)->fetch_assoc();
		if(isset($_POST['specialrate']) && !empty($_POST['specialrate'])){
			$sprate = 1;
			$gerate['rate'] = $_POST['specialrate'];
		}
		$loan = $conn->prepare("UPDATE loan set  principal = ?, duration = ?, type = ?, startdate = ?, rate = ?, specialrate = ? where loan_id = ?");
		$loan->bind_param("sssssii",  $_POST['amount'], $_POST['duration'], $_POST['type'], $_POST['strtdate'], $gerate['rate'], $sprate, $loan_id);
		if($loan->execute() == TRUE){
			$breakdown = $conn->prepare("DELETE FROM breakdown where loan_id = ?");
			$breakdown->bind_param("i", $loan_id);
			$breakdown->execute();
			if($_POST['type'] == 'Daily'){
				$_POST['type'] = 'Day';
			}elseif($_POST['type'] == 'Weekly'){
				$_POST['type'] = 'Week';
			}else{
				$_POST['type'] = 'Month';
			}
			$total = 0;
			$totalinte = 0;
			for($i = 0; $i < $_POST['duration']; $i++){	
				$brkamnt = number_format($_POST['amount']/$_POST['duration'],2);
				$brkamnt = str_replace(",", "", $brkamnt);
				$inte = number_format(($_POST['amount'] * $gerate['rate'])/$_POST['duration'],2);
				$inte = str_replace(",", "", $inte);
				$total += $brkamnt;
				$totalinte += $inte;
				if($i == ($_POST['duration'] - 1)){
					$brkamnt += number_format($_POST['amount'] - $total,2);
					$inte += number_format(($_POST['amount'] * $gerate['rate']) - $totalinte,2);
				}
				$breakdown = $conn->prepare("INSERT INTO breakdown (loan_id, deadline, amount, interest) VALUES (?, ?, ?, ?)");
				$deadline = date("Y-m-d", strtotime("+".$i.' '. $_POST['type'], strtotime(mysqli_real_escape_string($conn, $_POST['strtdate']))));
				$breakdown->bind_param("isss", $loan_id, $deadline, $brkamnt, $inte);
				$breakdown->execute();
			}
			savelogs("Update Loan", 'LoanID -> ' . $loan_id . ", Principal Amount -> " . number_format($_POST['amount'],2) . ', Interest -> ' . number_format(($_POST['amount'] * $gerate['rate'])/$_POST['duration'],2) . ', Rate -> ' . $gerate['rate'] . ' Start Date -> ' . $_POST['strtdate'] . ', CustomerID -> ' . $res->customer_id);
			echo '<script type = "text/javascript">alert("Updating Record Successful");window.location.replace("loan/list");</script>';
		}
	}
}
?>