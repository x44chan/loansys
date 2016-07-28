<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<i><h4  style="margin-left: -40px;"><span class="icon-coin-dollar"></span> <u>New Loan</u></h4></i>
		</div>
	</div>
	<form action = "" method="post">
		<div style="border: 1px solid #eee; padding: 0px 10px 10px 10px; border-radius: 5px;">
			<div class="row">
				<div class="col-xs-12">
					<h5><b><u><i><span class="icon-user"></span> Client Information</i></u></b></h5>
				</div>
			</div>
			<div class="row" style="margin-left: 20px; display: none;" id = "new">
				<div class="col-xs-4">
					<label>First Name <font color = "red"> * </font></label>
					<input type = "text" name = "fname" class="form-control input-sm" placeholder = "Enter First Name" autocomplete = "off">
				</div>
				<div class="col-xs-4">
					<label>Middle Name <font color = "red"> * </font></label>
					<input type = "text" name = "mname" class="form-control input-sm" placeholder = "Enter First Name" autocomplete = "off">
				</div>
				<div class="col-xs-4">
					<label>Last Name <font color = "red"> * </font></label>
					<input type = "text" name = "lname" class="form-control input-sm" placeholder = "Enter First Name" autocomplete = "off">
				</div>
			</div>
			<div class="row" style="margin-left: 20px;" id = "select">
				<div class="col-xs-6">
					<label>Select Client</label>
					<select class="form-control input-sm" name = "customer">
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
			<div class="row" style="margin-left: 20px;">
				<div class="col-xs-6">
					<label><input type = "checkbox" name = "checkbox" id = "checkbox"/> Add new client </label>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<hr>
					<h5><b><u><i><span class="icon-coin-dollar"></span> Loan Information</i></u></b></h5>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-xs-3">
					<label>Loan Amount <font color = "red"> * </font></label>
					<input type = "text" name = "amount" class="form-control input-sm" placeholder = "Enter Loan Amount" required pattern = "[.0-9]*" autocomplete = "off" onchange="showUser()">
				</div>
				<div class="col-xs-3">
					<label>Type <font color = "red"> * </font></label>
					<select class="form-control input-sm" name = "type" onchange="showUser()">
						<option value="Daily"> Daily </option>
						<option value="Weekly"> Weekly </option>
						<option value="Monthly"> Monthly </option>						
					</select>
				</div>
				<div class="col-xs-3">
					<label>Duration (in numbers)<font color = "red"> * </font></label>
					<input type = "text" name = "duration" class="form-control input-sm" placeholder = "Enter duration" required pattern = "[0-9]*" onchange="showUser()" autocomplete = "off">
				</div>
				<div class="col-xs-3">
					<label>Start Date <font color = "red"> * </font></label>
					<input type = "date" name = "strtdate" class="form-control input-sm" required onchange="showUser()" autocomplete = "off">
				</div>
			</div>
			<div id = "details">				
			</div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-xs-12" align="center">
					<hr>
					<button class="btn btn-primary btn-sm" name = "loansub" onclick = "return confirm('Are you sure?');"><span class = "icon-floppy-disk"></span> Save </button>
				</div>
			</div>
		</div>		
	</form>
</div>
<?php
	if(isset($_POST['loansub'])){
		if(!isset($_POST['customer'])){
			$cust = $conn->prepare("INSERT INTO customer (fname,mname,lname) VALUES (?, ?, ?)");
			$cust->bind_param("sss", $_POST['fname'], $_POST['mname'], $_POST['lname']);
			if($cust->execute() == TRUE){	
				$cust_id = 	$conn->insert_id;
			}
		}else{
			$cust_id = mysqli_real_escape_string($conn, $_POST['customer']);
		}	
		$gerate = "SELECT ".strtolower($_POST['type']) ." as rate FROM rate";
		$gerate = $conn->query($gerate)->fetch_assoc();
		$loan = $conn->prepare("INSERT INTO loan (customer_id, amount, duration, type, startdate) VALUES (?, ?, ?, ?, ?)");
		$loan->bind_param("issss", $cust_id, $_POST['amount'], $_POST['duration'], $_POST['type'], $_POST['strtdate']);
		if($loan->execute() == TRUE){
			$loan_id = $conn->insert_id;
			if($_POST['type'] == 'Daily'){
				$_POST['type'] = 'Day';
			}elseif($_POST['type'] == 'Weekly'){
				$_POST['type'] = 'Week';
			}else{
				$_POST['type'] = 'Month';
			}
			$total = 0;
			for($i = 0; $i < $_POST['duration']; $i++){	
				$brkamnt = number_format((($_POST['amount'] * $gerate['rate']) + $_POST['amount'])/$_POST['duration'],2);
				$brkamnt = str_replace(",", "", $brkamnt);
				$total += $brkamnt;
				if($i == ($_POST['duration'] - 1)){
					$brkamnt += (($_POST['amount'] * $gerate['rate']) + $_POST['amount']) - $total;
				}
				$breakdown = $conn->prepare("INSERT INTO breakdown (loan_id, deadline, amount) VALUES (?, ?, ?)");
				$deadline = date("Y-m-d", strtotime("+".$i.' '. $_POST['type'], strtotime(mysqli_real_escape_string($conn, $_POST['strtdate']))));
				$breakdown->bind_param("iss", $loan_id, $deadline, $brkamnt);
				$breakdown->execute();
			}
			echo '<script type = "text/javascript">alert("Adding Record Successful");window.location.replace("/loan/?module=loan&action=list");</script>';
		}
	}
	
?>