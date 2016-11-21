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
			<div  id = "new" style="display: none;">
				<div class="row" style="margin-left: 20px;">
					<div class="col-md-4 col-xs-12">
						<label>First Name<font color = "red"> * </font></label>
						<input type = "text" name = "fname" class="form-control input-sm" placeholder = "Enter First Name" autocomplete = "off">
					</div>
					<div class="col-md-4 col-xs-12">
						<label>Middle Name <font color = "red"> * </font></label>
						<input type = "text" name = "mname" class="form-control input-sm" placeholder = "Enter First Name" autocomplete = "off">
					</div>
					<div class="col-md-4 col-xs-12">
						<label>Last Name<font color = "red"> * </font></label>
						<input type = "text" name = "lname" class="form-control input-sm" placeholder = "Enter First Name" autocomplete = "off">
					</div>
				</div>
				<div class="row" style="margin-left: 20px;">
					<div class="col-md-6 col-xs-12">
						<label>Address <font color = "red"> * </font></label>
						<textarea name = "address" class="form-control input-sm" placeholder = "Enter Address" autocomplete = "off"></textarea>
					</div>
					<div class="col-md-4 col-xs-12">
						<label>Contact No. <font color = "red"> * </font></label>
						<input type = "text" name = "contact" class="form-control input-sm" placeholder = "09XXXXXXX" maxlength="11" pattern = "[0-9]*">
					</div>
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
									echo '<option value = "' . $row['customer_id'] . '"> ( '. $row['customer_id'] . ' ) '. $row['fname'] . ' ' . $row['mname'] . ', ' . $row['lname'] . '</option>';
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-md-6 col-xs-12">
					<label><input type = "checkbox" name = "checkbox" id = "checkbox"/> Add new client </label>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-md-6 col-xs-12">
					<label>Reason <font color = "red"> * </font></label>
					<textarea class="form-control input-sm" name = "reason" placeholder = "Enter reason" required></textarea>
				</div>
				<div class="col-md-6 col-xs-12">
					<label>Co - Maker <font color="red"> * </font></label>
					<input type = "text" name = "comaker" class="form-control input-sm" style = "text-transform: uppercase;" placeholder = "Enter co-maker name">
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<hr>
					<h5><b><u><i><span class="icon-coin-dollar"></span> Loan Information</i></u></b></h5>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-md-4 col-xs-12">
					<label>Principal ID<font color = "red"> *</font></label>
					<select class="form-control input-sm" name = "principalid" required>
						<option value=""> - - - - - - </option>
						<?php
							$principal = "SELECT * FROM principal";
							$principal = $conn->query($principal);
							if($principal->num_rows > 0){
								while ($row = $principal->fetch_object()) {
									$main = "SELECT *,sum(b.principal) as total_loan,sum(b.principal * b.rate) as total_interes  FROM principal as a, loan as b where a.principal_id = '$row->principal_id' and b.principal_id = '$row->principal_id' ORDER BY a.principal_date DESC";
									$main = $conn->query($main)->fetch_object();
									if($main->principal_amount - $main->total_loan <= 0){
										continue;
									}
									echo '<option value = "' . $row->principal_id . '"> ' . number_format($row->principal_amount,2) . ' ( Bal: '. number_format($main->principal_amount - $main->total_loan,2). ' ) </option>';
								}
							}
						?>
					</select>
					<hr>
				</div>
			</div>
			<div class="row" style="margin-left: 20px;">
				<div class="col-md-3 col-xs-12">
					<label>Loan/Principal Amount<font color = "red"> *</font></label>
					<input type = "text" name = "amount" class="form-control input-sm" placeholder = "Enter Loan Amount" required pattern = "[.0-9]*" autocomplete = "off" onchange="showUser()">
				</div>
				<div class="col-md-2 col-xs-12">
					<label>Type <font color = "red"> * </font></label>
					<select class="form-control input-sm" name = "type" onchange="showUser()">
						<option value="Daily"> Daily </option>
						<option value="Weekly"> Weekly </option>
						<option value="Monthly"> Monthly </option>						
					</select>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>Duration(in numbers) <font color = "red">*</font></label>
					<input type = "text" name = "duration" class="form-control input-sm" placeholder = "Enter duration" required pattern = "[0-9]*" onchange="showUser()" autocomplete = "off">
				</div>
				<div class="col-md-2 col-xs-12">
					<label>Start Date <font color = "red">*</font></label>
					<input type = "date" style="width: 100%;" data-date='{"startView": 2, "openOnMouseFocus": true}' placeholder = "YYYY-MM-DD" name = "strtdate" class="form-control input-sm" min = "<?php echo date('Y-m-d');?>" max = "<?php echo date('9999-m-d');?>" required onchange="showUser()" autocomplete = "off">
				</div>
				<div class="col-md-2 col-xs-12">
					<label>Special Rate <font color = "red" id = "asterisk" style="display: none;"> * </font></label>
					<input type = "text" name = "specialrate" class="form-control input-sm" placeholder = "Enter Rate" disabled onchange="showUser()">
					<label><input type = "checkbox" id = "specialrate"> Enable </label>
				</div>
			</div>
			<div id = "details">				
			</div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-xs-12" align="center">
					<hr>
					<button class="btn btn-primary btn-sm" name = "loansub" onclick = "return confirm('Are you sure?');"><span class = "icon-floppy-disk"></span> Save & <span class = "icon-printer"></span> Print </button>
				</div>
			</div>
		</div>		
	</form>
</div>
<?php
	if(isset($_POST['loansub'])){
		$trxn = 'EEN-'.random_string(8);
		$trnx = "SELECT * FROM loan where trxn = '$trxn'";
		$trxre = $conn->query($trnx);
		if($trxre->num_rows > 0){
			$i = 0;
			while ( $i < 1) {
				$trnxx = "SELECT * FROM loan where trxn = '$trxn'";
				$trxrex = $conn->query($trnxx);
				if($trxrex->num_rows > 0){
					$i = 1;
					$trxn = 'EEN-'.random_string(8);
				}else{
					$i = 0;
				}
			}
		}
		$principalid = mysqli_real_escape_string($conn, $_POST['principalid']);
		$main = "SELECT *,sum(b.principal) as total_loan,sum(b.principal * b.rate) as total_interes  FROM principal as a, loan as b where a.principal_id = '$principalid' and b.principal_id = '$principalid' ORDER BY a.principal_date DESC";
		$main = $conn->query($main)->fetch_object();
		if( ($main->principal_amount - $main->total_loan) < $_POST['amount'] ){
			echo '<script type = "text/javascript">alert("Not enough balance for the Principal ID.");window.location.replace("loan");</script>';
			exit;
		}
		if(!isset($_POST['customer'])){
			$cust = $conn->prepare("INSERT INTO customer (fname,mname,lname,address,contact) VALUES (?, ?, ?, ?, ?)");
			$cust->bind_param("ssssi", $_POST['fname'], $_POST['mname'], $_POST['lname'], $_POST['address'], $_POST['contact']);
			if($cust->execute() == TRUE){	
				$cust_id = 	$conn->insert_id;
			}
			savelogs("Add new customer", 'Name -> ' . $_POST['fname'] . ' ' . $_POST['mname'] . ' ' . $_POST['lname'] . ', Address -> ' . $_POST['address'] . ', Contact # -> ' . $_POST['contact']);
		}else{
			$cust_id = mysqli_real_escape_string($conn, $_POST['customer']);
		}
		$gerate = "SELECT ".strtolower($_POST['type']) ." as rate FROM rate";
		$gerate = $conn->query($gerate)->fetch_assoc();
		if(isset($_POST['specialrate']) && !empty($_POST['specialrate'])){
			$sprate = 1;
			$gerate['rate'] = $_POST['specialrate'];
		}
		if($_POST['type'] == 'Daily'){
			$type = 'Day';
		}elseif($_POST['type'] == 'Weekly'){
			$type = 'Week';
		}else{
			$type = 'Month';
		}

		$due = date("Y-m-d", strtotime("+".$_POST['duration'].' '. $type, strtotime($_POST['strtdate'])));
		$loan = $conn->prepare("INSERT INTO loan (trxn,customer_id, principal_id, principal, duration, type, startdate, rate, specialrate, due, reason, comaker) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$loan->bind_param("siisssssisss", $trxn, $cust_id, $_POST['principalid'], $_POST['amount'], $_POST['duration'], $_POST['type'], $_POST['strtdate'], $gerate['rate'], $sprate, $due, $_POST['reason'], $_POST['comaker']);
		if($loan->execute() == TRUE){
			$loan_id = $conn->insert_id;
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
				$inter = $_POST['amount'] * $gerate['rate'];
				$breakdown = $conn->prepare("INSERT INTO breakdown (loan_id, deadline, amount, interest) VALUES (?, ?, ?, ?)");
				$deadline = date("Y-m-d", strtotime("+".$i.' '. $type, strtotime(mysqli_real_escape_string($conn, $_POST['strtdate']))));
				$breakdown->bind_param("isss", $loan_id, $deadline, $_POST['amount'], $inter);
				$breakdown->execute();
			}
			savelogs("Add new loan", 'LoanID -> ' . $loan_id . ", Principal ID -> " . $_POST['principalid'] . " Principal Amount -> " . number_format($_POST['amount'],2) . ', Interest -> ' . number_format(($_POST['amount'] * $gerate['rate'])/$_POST['duration'],2) . ', Rate -> ' . $gerate['rate'] . ' Start Date -> ' . $_POST['strtdate'] . ', CustomerID -> ' . $cust_id);
			echo '
				<script type = "text/javascript">
					var r = confirm("Adding Record Successful, do you wan\'t to print?");
				    if (r == true) {
				    	window.location.replace("loan/print/'.$loan_id.'&print");
				    } else {
				    	window.location.replace("loan/list");
				    }					
				</script>';
		}
	}
	
?>