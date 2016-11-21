<?php
	include '../config/conf.php';
	if(!isset($_GET['module'])){
		echo '<script type = "text/javascript">window.location.replace("/loan/");</script>';
	}
	session_start();
	$access = "SELECT * FROM user where account_id = '$_SESSION[acc_id]'";
	$access = $conn->query($access)->fetch_object();
	if(!isset($_GET['module'])){
		echo '<script type = "text/javascript">window.location.replace("/loan/");</script>';
	}
?>
<?php
	if(isset($_GET['loanlist'])){
?>
	<?php
		$search = mysqli_real_escape_string($conn, $_GET['loanlist']);
		$search = str_replace(",", "", $search);
		$where = " and (fname like '%$search%' or mname like '%$search%' or lname like '%$search%' or principal like '%$search%' or duration like '%$search%' or type like '%$search%') ";
		if(empty($_GET['loanlist'])){
			$where = "";
		}
		$counter = "SELECT count(*) as total FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = c.loan_id and c.state = '0' " . $where . " group by b.loan_id";
		$counter2 = $conn->query($counter)->fetch_assoc();
		$perpage = 15;
		$totalPages = ceil($counter2['total'] / $perpage);
		if(!isset($_GET['page'])){
		    $_GET['page'] = 0;
		}else{
		    $_GET['page'] = (int)$_GET['page'];
		}
		if($_GET['page'] < 1){
		    $_GET['page'] = 1;
		}else if($_GET['page'] > $totalPages){
		    $_GET['page'] = $totalPages;
		}
		$startArticle = ($_GET['page'] - 1) * $perpage;
		$list = "SELECT * FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = c.loan_id and c.state = '0' " . $where . " group by b.loan_id LIMIT " . $startArticle . ', ' . $perpage;
		$res = $conn->query($list);
		$totalloan = 0; $totalinterest = 0; $total = 0; $moninte = 0;
		if($res->num_rows > 0){
			$num = 0;
			while ($row = $res->fetch_assoc()) {
				$gerate = "SELECT ".strtolower($row['type']) ." as rate FROM rate";
				$gerate = $conn->query($gerate)->fetch_assoc();
				$num += 1;	
				$totalloan += $row['principal'];
				$totalinterest += ($row['principal'] * $row['rate']) * $row['duration'];
				$moninte += ($row['principal'] * $row['rate']);
				$total += ( (($row['principal'] * $row['rate']) * $row['duration']) +  $row['principal'] );
				echo '<tr>';
				echo '<td>' . $num . '</td>';
				echo '<td>' . $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] . ' ( ' . $row['customer_id'] . ' )</td>';
				echo '<td>₱ ' . number_format($row['principal'],2) . '</td>';
				echo '<td>₱ ' . number_format( ($row['principal'] * $row['rate']), 2) . '</td>';
				echo '<td>₱ ' . number_format( ($row['principal'] * $row['rate']) * $row['duration'], 2) . '</td>';
				echo '<td>₱ ' . number_format(str_replace(",", "", number_format(($row['principal'] * $row['rate']) * $row['duration'],2)) + str_replace(",", "", number_format($row['principal'],2)),2) . '</td>';
				echo '<td>' . date("M j, Y", strtotime($row['startdate'])) . '</td>';
				echo '<td>' . $row['duration'] . ' - ' . $row['type'] . '</td>';
				echo 
					'<td>
						<a href = "loan/view/'.$row['loan_id'].'" class = "btn btn-sm btn-primary" data-toggle="tooltip" title="View"><span class = "icon-search"></span></a>
						<a onclick = "payment('.$row['loan_id'].');" class = "btn btn-sm btn-success" data-toggle="tooltip" title="Payment"><span>₱</span></a>';
				echo '</td>';
				echo '</tr>';
			}
			echo '<tr>';
				echo '<td></td><td align = "right"> <b>Total:</b></td>';
				echo '<td>₱ ' . number_format($totalloan,2) . '</td>';
				echo '<td>₱ ' . number_format($moninte,2) . '</td>';
				echo '<td>₱ ' . number_format($totalinterest,2) . '</td>';
				echo '<td>₱ ' . number_format($total,2) . '</td>';
				echo '<td></td><td></td><td></td>';
			echo '</tr>';
		}else{
			echo '<tr><td colspan = "6" align = "center"> <h5> No Record Found </h5></td></tr>';
		}
	?>
<?php
	}
?>
<?php
	if(isset($_GET['payment'])){
		$gerate = "SELECT * FROM rate";
  		$gerate = $conn->query($gerate)->fetch_assoc();
?>
	<div class="modal-dialog">    
    	<!-- Modal content-->
    	<div class="modal-content">
	        <div class="modal-header" style="padding:35px 50px;">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4><span class = "icon-coin-dollar"></span> Payment</h4>
	        </div>
	        <div class="modal-body" style="padding:40px 50px;">
	        	<?php
	        		$loan_id = mysqli_real_escape_string($conn, $_GET['payment']);
	        		$payment = "SELECT *,sum(payprincipal) as sumpayprincipal, sum(payinterest) as sumpayinterest, sum(paypenalty) as sumpaypenalty FROM loan as a, payment as b where a.loan_id = '$loan_id' and b.loan_id = '$loan_id'";
	        		$payment = $conn->query($payment)->fetch_object();
	        		echo '<div class = "form-group"><label>Principal Balance: ₱ ' . number_format($payment->principal - $payment->sumpayprincipal,2) . ' </label></div>';
	        		echo '<hr>';
	        		echo '<div class = "form-group"><label>Interest Balance (* '. $payment->rate .' ): ₱ ' . number_format((($payment->principal * $payment->rate) * $payment->duration) - $payment->sumpayinterest,2) . ' </label></div>';
	        		echo '<hr>';
	        		$diff=date_diff(date_create($payment->due),date_create(date("Y-m-d")));
					if($diff->format("%R%") == '+' && $diff->format("%a%") > 0 && $payment->due <= date('Y-m-d') && $payment->state == 0){
						$penalty = number_format((($payment->principal + ($payment->principal * $payment->rate)) * $gerate['penalty']) * $diff->format("%a%"),2);
						if( (str_replace(",", "", $penalty) - $payment->sumpaypenalty) > 0 ){	
							echo '<div class = "form-group"><label>Penalty Balance (* '. $gerate['penalty'] .' ): ₱ '. $penalty . ' <font color = "red">( '. $diff->format("%a%") . ' day/s) </font> </label></div>';
		        			echo '<hr>';
						}
					}
	        	?>
	        	<form role="form" action = "" method = "post">
	        		<div class="form-group">
	        			<label>Type of Payment <font color = "red"> * </font></label>
	        			<select class="form-control input-sm" required name = "paytype" onchange="paytypex(this.value)">
	        				<option> - - - - - </option>
	        				<option value = "Cash">Cash</option>
	        				<option value = "Check">Check</option>
	        			</select>
	        		</div>
	        		<div id = "check" style="display: none;">
	        			<div class="form-group">
	        				<label>Check No. <font color = "red"> * </font></label>
	        				<input type = "text" name = "checknum" class="form-control input-sm" placeholder = "Enter check no.">
	        			</div>
	        			<div class="form-group">
	        				<label>Check Date <font color = "red"> * </font></label>
	        				<input type = "date" name = "check" class="form-control input-sm">
	        			</div>
	        		</div>
	        		<div class="form-group">
	            		<label>Principal <font color = "red"> * </font></label>
	            		<input value = '0' type = "text" name = "prin" class="form-control input-sm" placeholder = "Enter amount" pattern = "[.0-9]*" required>
		            </div>
		            <div class="form-group">
	            		<label>Interest <font color = "red"> * </font></label>
	            		<input value = '0' type = "text" name = "inte" class="form-control input-sm" placeholder = "Enter amount" pattern = "[.0-9]*" required>
	            	</div>
	            	<div class="form-group">
	            		<label>Penalty <font color = "red"> * </font></label>
	            		<input value = '0' type = "text" name = "penal" class="form-control input-sm" placeholder = "Enter amount" pattern = "[.0-9]*">
	            	</div>
	            	<button type="submit" name = "paysub" class="btn btn-success btn-block" onclick="return confirm('Are you sure?');"><span class ="icon-checkmark"></span> Add payment</button>
	            	<input type = "hidden" value = "<?php echo $loan_id;?>" name = "loan_id"/>
	          	</form>
	    	</div>
    	</div>
    </div>
<?php
	}

?>

<?php
if(isset($_GET['amount']) && isset($_GET['type']) && isset($_GET['duration']) && isset($_GET['strtdate'])){
	$amount = mysqli_real_escape_string($conn, $_GET['amount']);
	$type = mysqli_real_escape_string($conn, $_GET['type']);
	$duration = mysqli_real_escape_string($conn, intval($_GET['duration']));
	$strdate = mysqli_real_escape_string($conn, $_GET['strtdate']);
	if(empty($strdate)){
		$strdate = date('Y-m-d');
	}
	if($amount == ""){
		$amount = 0;
	}
	$gerate = "SELECT ".strtolower($type) ." as rate FROM rate";
	$gerate = $conn->query($gerate)->fetch_assoc();
	if(!empty($_GET['sprate'])){
		$throu = ' style = "text-decoration: line-through; " ';
		$sprate = '<i><p style="margin-left: 20px;">'.number_format($_GET['sprate'],2).'</p></i>';
	}else{
		$throu = "";
		$sprate = "";
	}
	if($type == 'Daily'){
		$type = 'Day';
	}elseif($type == 'Weekly'){
		$type = 'Week';
	}else{
		$type = 'Month';
	}
?>
<div class="row">
	<div class="col-md-12 col-xs-12">
		<hr>
		<h5><b><u><i><span class="icon-coin-dollar"></span> Loan Details</i></u></b></h5>
	</div>
</div>
<div class="row">
	<div class="col-md-2 col-md-offset-1 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Principal Amount</u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($amount,2);?></p></i>
	</div>
	<div class="col-md-1 col-xs-6" style="border-right: 1px solid #eee;">
		<label <?php echo $throu;?>><u>Rate</u></label>
		<i <?php echo $throu;?>><p style="margin-left: 20px;" ><?php echo number_format($gerate['rate'],2);?></p></i>
		<?php echo $sprate; if(isset($sprate) && !empty($sprate)){ $gerate['rate'] = $_GET['sprate']; }?>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Interest</u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($amount * $gerate['rate'] * $duration,2);?></p></i>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Duration - Type </u></label>
		<i><p style="margin-left: 20px;"><?php echo $duration . ' - ' . $type;?>/s</p></i>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Total </u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format((($amount * $gerate['rate']) * $duration) + $amount,2);?></p></i>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Due Date </u></label>
		<i><p style="margin-left: 20px;"><?php echo date("M j, Y", strtotime("+".$duration.' '. $type, strtotime($strdate)));?></p></i>
	</div>
</div>
<?php if($duration > 0){ ?>
<div class="row">
	<div class="col-md-12 col-xs-12" align="center">
		<i><h5 style="margin-left: -40px;"><b><span class = "icon-tree"></span> Break Down</b></h5></i>
	</div>
</div>
<div class="row" align="center">
	<div class="col-md-2 col-xs-3 col-md-offset-2 col-xs-offset-1">
		<label><u>Date</u></label>
		<?php
			$brkamnt = (($amount * $gerate['rate']) + $amount)/$duration;
			for($i = 0; $i < $duration; $i++){		
				echo '<i><p style="margin-left: 20px;">'.date("M j, Y", strtotime("+".$i.' '. $type, strtotime($strdate))) .'</p></i>';
			}
		?>	
	</div>
	<div class="col-md-2 col-xs-3">
		<label><u>Principal Amount</u></label>
		<?php
			$totalprin = 0;
			for($i = 0; $i < $duration; $i++){		
				if($i > 0) {
					echo '<i><p style="margin-left: 20px;"> - </p></i>';
					continue;	
				}
				$totalprin = $amount;
				echo '<i><p style="margin-left: 20px;">₱ '.number_format($amount,2).'</p></i>';
			}
		?>	
	</div>
	<div class="col-md-2 col-xs-3">
		<label><u>Interest Amount</u></label>
		<?php
			$totalinte = 0;
			for($i = 0; $i < $duration; $i++){		
				$totalinte += ($amount * $gerate['rate']);
				echo '<i><p style="margin-left: 20px;">₱ '.number_format(($amount * $gerate['rate']),2).'</p></i>';
			}
		?>		
	</div>
	<div class="col-md-2 col-xs-3">
		<label><u>Total Amount</u></label>
		<?php
			$totalamnt = 0;
			for($i = 0; $i < $duration; $i++){		
				if($i < 1){
					$amntx = (($amount * $gerate['rate']) + $amount);
				}else{
					$amntx = ($amount * $gerate['rate']);
				}
				$totalamnt += $amntx;
				echo '<i><p style="margin-left: 20px;"><b>₱ '.number_format($amntx,2).'</b></p></i>';
			}
		?>	
	</div>
</div>
<div class="row">
	<div class="col-md-2 col-xs-3 col-md-offset-2 col-xs-offset-1">

	</div>
	<div class="col-md-2 col-xs-3" align="center">
		<hr>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($totalprin,2); ?></p></i>
	</div>
	<div class="col-md-2 col-xs-3" align="center">
		<hr>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($totalinte,2); ?></p></i>
	</div>
	<div class="col-md-2 col-xs-3" align="center">
		<hr>
		<i><p style="margin-left: 20px;"><b>₱ <?php echo number_format($totalamnt,2); ?></p></i>
	</div>
</div>
<?php
	}	
	$conn->close();
}
?>