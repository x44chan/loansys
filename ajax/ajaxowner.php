<?php
	include '../config/conf.php';
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
		if($search == ""){
			$where = "";
		}
		$counter = "SELECT count(*) as total FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = c.loan_id and c.state = '0' " . $where . " group by b.loan_id";
		$counter2 = $conn->query($counter)->fetch_assoc();
		$perpage = 2;
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
		if($res->num_rows > 0){
			$num = 0;
			while ($row = $res->fetch_assoc()) {
				$gerate = "SELECT ".strtolower($row['type']) ." as rate FROM rate";
				$gerate = $conn->query($gerate)->fetch_assoc();
				$num += 1;	
				echo '<tr>';
				echo '<td>' . $num . '</td>';
				echo '<td>' . $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] . ' ( ' . $row['customer_id'] . ' )</td>';
				echo '<td>₱ ' . number_format($row['principal'],2) . '</td>';
				echo '<td>₱ ' . number_format($row['principal'] * $row['rate'],2) . '</td>';
				echo '<td>₱ ' . number_format(str_replace(",", "", number_format($row['principal'] * $row['rate'],2)) + str_replace(",", "", number_format($row['principal'],2)),2) . '</td>';
				echo '<td>' . $row['duration'] . ' - ' . $row['type'] . '</td>';
				echo '<td><a href = "?module=loan&action=view&id='.$row['loan_id'].'" class = "btn btn-sm btn-primary"> View Details </a></td>';
				echo '</tr>';
			}
		}else{
			echo '<tr><td colspan = "6" align = "center"> <h5> No Record Found </h5></td></tr>';
		}
	?>
<?php
	}
?>
<?php
	if(isset($_GET['payment'])){
?>
	<div class="modal-dialog">    
    	<!-- Modal content-->
    	<div class="modal-content">
	        <div class="modal-header" style="padding:35px 50px;">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4><span class = "icon-coin-dollar"></span>Payment</h4>
	        </div>
	        <div class="modal-body" style="padding:40px 50px;">
	        	<form role="form" action = "" method = "post">
	        		<div class="form-group">
	            		<label>Principal </label>
	            		<input value = '0' type = "text" name = "prin" class="form-control input-sm" placeholder = "Enter amount" pattern = "[.0-9]*" required>
		            </div>
		            <div class="form-group">
	            		<label>Interest </label>
	            		<input value = '0' type = "text" name = "inte" class="form-control input-sm" placeholder = "Enter amount" pattern = "[.0-9]*" required>
	            	</div>
	            	<div class="form-group">
	            		<label>Penalty </label>
	            		<input value = '0' type = "text" name = "penal" class="form-control input-sm" placeholder = "Enter amount" pattern = "[.0-9]*">
	            	</div>
	            	<input type = "hidden" name = "breakdown_id" value = "<?php echo $_GET['payment'];?>">
	            	<input type = "hidden" name = "forexec" value = "<?php echo number_format($_GET['exec'],2);?>">
	            	<button type="submit" name = "paysub" class="btn btn-success btn-block"><span class ="icon-checkmark"></span> Add payment</button>
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
		<i><p style="margin-left: 20px;">₱ <?php echo number_format($amount * $gerate['rate'],2);?></p></i>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Duration - Type </u></label>
		<i><p style="margin-left: 20px;"><?php echo $duration . ' - ' . $type;?>/s</p></i>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Total </u></label>
		<i><p style="margin-left: 20px;">₱ <?php echo number_format(($amount * $gerate['rate']) + $amount,2);?></p></i>
	</div>
	<div class="col-md-2 col-xs-6" style="border-right: 1px solid #eee;">
		<label><u>Start Date </u></label>
		<i><p style="margin-left: 20px;"><?php echo date("M j, Y", strtotime($strdate));?></p></i>
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
			$brkamnt = number_format($amount/$duration,2);
			$brkamnt = str_replace(",", "", $brkamnt);
			$total = 0;
			$totalprin = 0;
			for($i = 0; $i < $duration; $i++){		
				$total += $brkamnt;
				if($i == ($duration - 1)){
					$brkamnt += $amount - $total;
				}
				$totalprin += $brkamnt;
				echo '<i><p style="margin-left: 20px;">₱ '.number_format($brkamnt,2).'</p></i>';
			}
		?>	
	</div>
	<div class="col-md-2 col-xs-3">
		<label><u>Interest Amount</u></label>
		<?php
			$brkamnt = number_format((($amount * $gerate['rate']))/$duration,2);
			$brkamnt = str_replace(",", "", $brkamnt);
			$total = 0;
			$totalinte = 0;
			for($i = 0; $i < $duration; $i++){		
				$total += $brkamnt;
				if($i == ($duration - 1)){
					$brkamnt += (($amount * $gerate['rate'])) - $total;
				}
				$totalinte += $brkamnt;
				echo '<i><p style="margin-left: 20px;">₱ '.number_format($brkamnt,2).'</p></i>';
			}
		?>		
	</div>
	<div class="col-md-2 col-xs-3">
		<label><u>Total Amount</u></label>
		<?php
			$brkamnt = number_format((($amount * $gerate['rate']) + $amount)/$duration,2);
			$brkamnt = str_replace(",", "", $brkamnt);
			$total = 0;
			$totalamnt = 0;
			for($i = 0; $i < $duration; $i++){		
				$total += $brkamnt;
				if($i == ($duration - 1)){
					$brkamnt += (($amount * $gerate['rate']) + $amount) - $total;
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
		<i><p style="margin-left: 20px;"><b>₱ <?php echo number_format($totalamnt,2); ?></p></i>
	</div>
</div>
<?php
	}	
	$conn->close();
}
?>