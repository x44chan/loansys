<?php
if(isset($_GET['amount']) && isset($_GET['type']) && isset($_GET['duration']) && isset($_GET['strtdate'])){
	include '../config/conf.php';
	$amount = mysqli_real_escape_string($conn, $_GET['amount']);
	$type = mysqli_real_escape_string($conn, $_GET['type']);
	$duration = mysqli_real_escape_string($conn, intval($_GET['duration']));
	$strdate = mysqli_real_escape_string($conn, $_GET['strtdate']);
	if($amount == ""){
		$amount = 0;
	}
	$gerate = "SELECT ".strtolower($type) ." as rate FROM rate";
	$gerate = $conn->query($gerate)->fetch_assoc();
	if($type == 'Daily'){
		$type = 'Day';
	}elseif($type == 'Weekly'){
		$type = 'Week';
	}else{
		$type = 'Month';
	}
?>
<div class="row">
	<div class="col-xs-12">
		<hr>
		<h5><b><u><i><span class="icon-coin-dollar"></span> Loan Details</i></u></b></h5>
	</div>
</div>
<div class="row">
	<div class="col-xs-2 col-xs-offset-1" style="border-right: 1px solid #eee;">
		<label><u>Principal Amount</u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($amount,2);?></p></i>
	</div>
	<div class="col-xs-1" style="border-right: 1px solid #eee;">
		<label><u>Rate</u></label>
		<i><p style="margin-left: 20px;"><?php echo number_format($gerate['rate'],2);?></p></i>
	</div>
	<div class="col-xs-2" style="border-right: 1px solid #eee;">
		<label><u>Interest</u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($amount * $gerate['rate'],2);?></p></i>
	</div>
	<div class="col-xs-2" style="border-right: 1px solid #eee;">
		<label><u>Duration - Type </u></label>
		<i><p style="margin-left: 20px;"><?php echo $duration . ' - ' . $type;?>/s</p></i>
	</div>
	<div class="col-xs-2" style="border-right: 1px solid #eee;">
		<label><u>Total </u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format(($amount * $gerate['rate']) + $amount,2);?></p></i>
	</div>
	<div class="col-xs-2" style="border-right: 1px solid #eee;">
		<label><u>Start Date </u></label>
		<i><p style="margin-left: 20px;"><?php echo date("M j, Y", strtotime($strdate));?></p></i>
	</div>
</div>
<?php if($duration > 0){ ?>
<div class="row">
	<div class="col-xs-12" align="center">
		<i><h5 style="margin-left: -40px;"><b><span class = "icon-tree"></span> Break Down</b></h5></i>
	</div>
</div>
<div class="row" align="center">
	<div class="col-xs-7">
		<label><u>Date</u></label>
		<?php
			$brkamnt = (($amount * $gerate['rate']) + $amount)/$duration;
			for($i = 0; $i < $duration; $i++){		
				echo '<i><p style="margin-left: 20px;">'.date("M j, Y", strtotime("+".$i.' '. $type, strtotime($strdate))) .'</p></i>';
			}
		?>	
	</div>
	<div class="col-xs-2">
		<label><u>Amount</u></label>
		<?php
			$brkamnt = number_format((($amount * $gerate['rate']) + $amount)/$duration,2);
			$brkamnt = str_replace(",", "", $brkamnt);
			$total = 0;
			for($i = 0; $i < $duration; $i++){		
				$total += $brkamnt;
				if($i == ($duration - 1)){
					$brkamnt += (($amount * $gerate['rate']) + $amount) - $total;
				}
				echo '<i><p style="margin-left: 20px;">₱ '.number_format($brkamnt,2).'</p></i>';
			}
		?>	
	</div>
</div>
<?php
	}	
	$conn->close();
}
?>