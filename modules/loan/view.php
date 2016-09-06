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
			<a href = "javascript:javascript:history.go(-1)" class="btn btn-danger btn-sm pull-right" data-toggle="tooltip" title="Back"><span class = " icon-exit"></span> Back to List </a>
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
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format($res['principal'] * $res['rate'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Total </label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format(($res['principal'] * $res['rate']) + $res['principal'],2);?></i></p>
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
					}elseif($print->type == 'Weekly'){
						$res['type'] = 'Week';
					}else{
						$res['type'] = 'Month';
					}
					$brkamnt = (($res['principal'] * $gerate['rate']) + $res['principal'])/$res['duration'];
					for($i = 0; $i < $res['duration']; $i++){		
						echo '<i><p style="margin-left: 20px;">'.date("M j, Y", strtotime("+".$i.' '. $res['type'], strtotime($res['startdate']))) .'</p></i>';
					}
				?>	
			</div>
			<div class="col-md-2 col-xs-3">
				<label><u>Principal Amount</u></label>
				<?php
					$brkamnt = number_format($res['principal']/$res['duration'],2);
					$brkamnt = str_replace(",", "", $brkamnt);
					$total = 0;
					$totalprin = 0;
					for($i = 0; $i < $res['duration']; $i++){		
						$total += $brkamnt;
						if($i == ($res['duration'] - 1)){
							$brkamnt += $res['principal'] - $total;
						}
						$totalprin += $brkamnt;
						echo '<i><p style="margin-left: 20px;">₱ '.number_format($brkamnt,2).'</p></i>';
					}
				?>	
			</div>
			<div class="col-md-2 col-xs-3">
				<label><u>Interest Amount</u></label>
				<?php
					$brkamnt = number_format((($res['principal'] * $gerate['rate']))/$res['duration'],2);
					$brkamnt = str_replace(",", "", $brkamnt);
					$total = 0;
					$totalinte = 0;
					for($i = 0; $i < $res['duration']; $i++){		
						$total += $brkamnt;
						if($i == ($res['duration'] - 1)){
							$brkamnt += (($res['principal'] * $gerate['rate'])) - $total;
						}
						$totalinte += $brkamnt;
						echo '<i><p style="margin-left: 20px;">₱ '.number_format($brkamnt,2).'</p></i>';
					}
				?>		
			</div>
			<div class="col-md-2 col-xs-3">
				<label><u>Total Amount</u></label>
				<?php
					$brkamnt = number_format((($res['principal'] * $gerate['rate']) + $res['principal'])/$res['duration'],2);
					$brkamnt = str_replace(",", "", $brkamnt);
					$total = 0;
					$totalamnt = 0;
					for($i = 0; $i < $res['duration']; $i++){		
						$total += $brkamnt;
						if($i == ($res['duration'] - 1)){
							$brkamnt += (($res['principal'] * $gerate['rate']) + $res['principal']) - $total;
						}
						$totalamnt += $brkamnt;
						echo '<i><p style="margin-left: 20px;"><b>₱ '.number_format($brkamnt,2).'</b></p></i>';
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
				<i><p style="margin-left: 20px;"><b>₱ <?php echo number_format($totalamnt,2); ?></b></p></i>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	document.title = "View Loan -> <?php echo $res['fname'] . ' ' . $res['mname'] . ' ' . $res['lname']?>"; 
</script>