<?php
	$loan_id = mysqli_real_escape_string($conn, $_GET['view']);
	$list = "SELECT * FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = '$loan_id' and c.loan_id = '$loan_id' group by b.loan_id";
	$res = $conn->query($list)->fetch_assoc();
	if($conn->query($list)->num_rows <= 0){
		echo '<script type = "text/javascript">alert("No record found.");window.location.replace("/loan/?module=loan&action=list");</script>';
	}
	$gerate = "SELECT ".strtolower($res['type']) ." as rate,penalty FROM rate";
	$gerate = $conn->query($gerate)->fetch_assoc();
?>
<div class="container" id = "reportg">
	<div class="row">
		<div class="col-xs-6">
			<i><h4  style="margin-left: -40px;"><span class="icon-coin-dollar"></span><u> Loan Information</u></h4></i>
		</div>
		<div class="col-xs-6">
			<div class="pull-right">
				<?php
					if($access->level >= 2){
						echo ' <a href = "loan/edit/'.$loan_id.'" class = "btn btn-sm btn-warning" data-toggle="tooltip" title="Edit"><span class = "icon-quill"></span></a>';
						echo ' <a href = "loan/delete/'.$loan_id.'" onclick = \'return confirm("Are you sure?");\' class = "btn btn-sm btn-danger" data-toggle="tooltip" title="Delete"><span class = "icon-bin"></span></a>';
					}
				?>
				<a href = "loan/print/<?php echo $loan_id;?>&print" class="btn btn-success btn-sm" data-toggle="tooltip" title="Print"><span class = " icon-printer"></span></a> 
				<a href = "javascript:javascript:history.go(-1)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Back"><span class = " icon-exit"></span></a> 
			</div>
		</div>
	</div>
	<div style="border: 1px solid #eee; padding: 0px 10px 10px 10px; border-radius: 5px;">
		<div class="row">
			<div class="col-xs-12">
				<h5><b><u><i><span class="icon-user"></span> Client Information</i></u></b></h5>
			</div>
		</div>	
		<div class="row" style="margin-left: 20px;">
			<div class="col-xs-6">
				<label>Name</label>
				<p style="margin-left: 10px;"><i><?php echo $res['fname'] . ' ' . $res['mname'] . ' ' . $res['lname']; ?></i></p>
			</div>
			<div class="col-xs-6">
				<label>Conctact #</label>
				<p style="margin-left: 10px;"><i><?php echo $res['contact']; ?></i></p>
			</div>
		</div>
		<div class="row" style="margin-left: 20px;">
			<div class="col-xs-6">
				<label>Address</label>
				<p style="margin-left: 10px;"><i><?php echo $res['address']; ?></i></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<hr>
				<h5><b><u><i><span class="icon-coin-dollar"></span> Loan Details</i></u></b></h5>
			</div>
		</div>
		<div class="row" style="margin-left: 20px;">
			<div class="col-xs-2">
				<label>Loan Amount</label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format($res['principal'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Rate</label>
				<p style="margin-left: 10px;"><i> <?php echo number_format($res['rate'],2); if($res['specialrate'] == 1){ echo ' / Special Rate'; }?> </i></p>
			</div>
			<div class="col-xs-2">
				<label>Interest </label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format($res['principal'] * $res['rate'] * $res['duration'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Total </label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format((($res['principal'] * $res['rate']) * $res['duration']) + $res['principal'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Duration / Type </label>
				<p style="margin-left: 10px;"><i><?php echo $res['duration'] . ' - ' .$res['type'];?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Start Date </label>
				<p style="margin-left: 10px;"><i><?php echo date("M j, Y", strtotime($res['startdate']));?></i></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<u><i><h5><b><span class="icon-tree"></span> Break Down</b></h5></i></u>
			</div>
		</div>
		<div class="row" align="center">
			<div class="col-md-2 col-xs-3 col-md-offset-2 col-xs-offset-1">
				<label><u>Date</u></label>
				<?php
					if($res['type'] == 'Daily'){
						$res['type'] = 'Day';
					}elseif($res['type'] == 'Weekly'){
						$res['type'] = 'Week';
					}else{
						$res['type'] = 'Month';
					}
					$brkamnt = (($res['principal'] * $res['rate']) + $res['principal'])/$res['duration'];
					for($i = 0; $i < $res['duration']; $i++){		
						echo '<i><p style="margin-left: 20px;">'.date("M j, Y", strtotime("+".$i.' '. $res['type'], strtotime($res['startdate']))) .'</p></i>';
					}
				?>	
			</div>
			<div class="col-md-2 col-xs-3">
				<label><u>Principal Amount</u></label>
				<?php
					$totalprin = 0;
					for($i = 0; $i < $res['duration']; $i++){	
						if($i > 0) {
							echo '<i><p style="margin-left: 20px;"> - </p></i>';
							continue;	
						}
						echo '<i><p style="margin-left: 20px;">₱ '.number_format($res['principal'],2).'</p></i>';
					}
				?>	
			</div>
			<div class="col-md-2 col-xs-3">
				<label><u>Interest Amount</u></label>
				<?php
					$totalinte = 0;
					for($i = 0; $i < $res['duration']; $i++){		
						$totalinte += ($res['principal'] * $res['rate']);
						echo '<i><p style="margin-left: 20px;">₱ '.number_format(($res['principal'] * $res['rate']),2).'</p></i>';
					}
				?>		
			</div>
			<div class="col-md-2 col-xs-3">
				<label><u>Total Amount</u></label>
				<?php
					$totalamnt = 0;
					for($i = 0; $i < $res['duration']; $i++){
						if($i < 1){
							$amntx = ($res['principal'] * $res['rate']) + $res['principal'];
						}else{
							$amntx = ($res['principal'] * $res['rate']);
						}
						$totalamnt += $amntx;
						echo '<i><p style="margin-left: 20px;"><b>₱ '.number_format($amntx,2).'</b></p></i>';
					}
				?>	
			</div>
			<div class="row">
				<div class="col-md-2 col-xs-3 col-md-offset-2 col-xs-offset-1">

				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;">₱ <?php echo number_format($res['principal'],2); ?></p></i>
				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;">₱ <?php echo number_format($totalinte,2); ?></p></i>
				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;"><b>₱ <?php echo number_format($totalamnt,2); ?></b></p></i>
				</div>
			</div>
		</div>
		<?php
			$payhistory = "SELECT * FROM payment where loan_id = '$loan_id'";
			$reshistory = $conn->query($payhistory);
			$totalpayprin = 0;
			if($reshistory->num_rows > 0){
		?>
			<div class="row">
				<div class="col-xs-12">
					<u><i><h5><b><span class="icon-tree"></span> Payment History</b></h5></i></u>
				</div>
			</div>
			<div class="row" align="center">
				<div class="col-md-2 col-xs-3 col-xs-offset-1">
					<label><u>Pay Date</u></label>
					<?php
						while ($row = $reshistory->fetch_object()) {
							echo '<i><p style="margin-left: 20px;">'.date("M j, Y h:i A", strtotime($row->paydate)) .'</p></i>';
						}
					?>
				</div>
				<div class="col-md-2 col-xs-3">
					<label><u>Principal Amount</u></label>
					<?php
						$payhistory = "SELECT * FROM payment where loan_id = '$loan_id'";
						$reshistory = $conn->query($payhistory);
						while ($row = $reshistory->fetch_object()) {
							echo '<i><p style="margin-left: 20px;">₱ '.number_format($row->payprincipal,2).'</p></i>';
							$totalpayprin += $row->payprincipal;
						}
					?>	
				</div>
				<div class="col-md-2 col-xs-3">
					<label><u>Interest Amount</u></label>
					<?php
						$totalpayinte = 0;
						$payhistory = "SELECT * FROM payment where loan_id = '$loan_id'";
						$reshistory = $conn->query($payhistory);
						while ($row = $reshistory->fetch_object()) {
							echo '<i><p style="margin-left: 20px;">₱ '.number_format($row->payinterest,2).'</p></i>';
							$totalpayinte += $row->payinterest;
						}
					?>		
				</div>
				<div class="col-md-2 col-xs-3">
					<label><u>Penalty</u></label>
					<?php
						$totalpaypenalty = 0;
						$payhistory = "SELECT * FROM payment where loan_id = '$loan_id'";
						$reshistory = $conn->query($payhistory);
						while ($row = $reshistory->fetch_object()) {
							echo '<i><p style="margin-left: 20px;">₱ '.number_format($row->paypenalty,2).'</p></i>';
							$totalpaypenalty += $row->paypenalty;
						}
					?>		
				</div>
				<div class="col-md-2 col-xs-3">
					<label><u>Total Amount</u></label>
					<?php
						$totalpayamnt = 0;
						$payhistory = "SELECT * FROM payment where loan_id = '$loan_id'";
						$reshistory = $conn->query($payhistory);
						while ($row = $reshistory->fetch_object()) {
							echo '<i><p style="margin-left: 20px;"><b>₱ '.number_format( ( $row->payprincipal + $row->payinterest + $row->paypenalty ), 2).'</b></p></i>';
							$totalpayamnt += ( $row->payprincipal + $row->payinterest + $row->paypenalty );
						}
					?>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-xs-3 col-xs-offset-1">

				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;">₱ <?php echo number_format($totalpayprin,2); ?></p></i>
				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;">₱ <?php echo number_format($totalpayinte,2); ?></p></i>
				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;">₱ <?php echo number_format($totalpaypenalty,2); ?></p></i>
				</div>
				<div class="col-md-2 col-xs-3" align="center">
					<hr>
					<i><p style="margin-left: 20px;"><b>₱ <?php echo number_format($totalpayamnt,2); ?></b></p></i>
				</div>
			</div>
		<?php
			}
		?>
	</div>
</div>
<script type="text/javascript">
	document.title = "View Loan -> <?php echo $res['fname'] . ' ' . $res['mname'] . ' ' . $res['lname']?>"; 
</script>