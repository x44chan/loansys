<?php
	$loan_id = mysqli_real_escape_string($conn, $_GET['id']);
	$list = "SELECT *,b.amount as loanamount FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = '$loan_id' and c.loan_id = '$loan_id' group by b.loan_id";
	$res = $conn->query($list)->fetch_assoc();
	if($conn->query($list)->num_rows <= 0){
		echo '<script type = "text/javascript">alert("No record found.");window.location.replace("/loan/?module=loan&action=list");</script>';
	}
	$gerate = "SELECT ".strtolower($res['type']) ." as rate FROM rate";
	$gerate = $conn->query($gerate)->fetch_assoc();
?>
<div class="container">
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
				<label>Loan Amount <font color="red"> * </font></label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format($res['loanamount'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Rate <font color="red"> * </font></label>
				<p style="margin-left: 10px;"><i> <?php echo number_format($res['rate'],2); if($res['specialrate'] == 1){ echo ' / Special Rate'; }?> </i></p>
			</div>
			<div class="col-xs-2">
				<label>Interest <font color="red"> * </font></label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format($res['loanamount'] * $res['rate'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Total <font color="red"> * </font></label>
				<p style="margin-left: 10px;"><i>₱ <?php echo number_format(($res['loanamount'] * $res['rate']) + $res['loanamount'],2);?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Duration / Type <font color="red"> * </font></label>
				<p style="margin-left: 10px;"><i><?php echo $res['duration'] . ' - ' .$res['type'];?></i></p>
			</div>
			<div class="col-xs-2">
				<label>Start Date <font color="red"> * </font></label>
				<p style="margin-left: 10px;"><i><?php echo date("M j, Y", strtotime($res['startdate']));?></i></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<u><i><h5><b><span class="icon-tree"></span> Break Down</b></h5></i></u>
			</div>
		</div>
		<div class = "row">
			<div class = "col-xs-2 col-xs-offset-1"><label>Date</label></div>
			<div class = "col-xs-2"><label>Amount</label></div>
			<div class = "col-xs-2"><label>Interest</label></div>
			<div class = "col-xs-1"><label>Penalty</label></div>
			<div class = "col-xs-2"><label>Due</label></div>
			<div class = "col-xs-2"><label>Action/Status</label></div>
		</div>
		<?php
			$breakdown = "SELECT * FROM breakdown where loan_id = '$res[loan_id]'";
			$breakdown = $conn->query($breakdown);
			$totalamount = 0;
			$totalpen = 0;
			$totaldue = 0;
			$totalinte = 0;
			$counter = 0;
			if($breakdown->num_rows > 0){
				while ($row = $breakdown->fetch_assoc()) {
					$throu = "";
					$counter += 1;
					$count = "SELECT count(*) as count FROM breakdown where loan_id = '$res[loan_id]' and deadline <= CURDATE()";
					$count = $conn->query($count)->fetch_assoc();
					$diff=date_diff(date_create($row['deadline']),date_create(date("Y-m-d")));
					if($diff->format("%R%") == '+' && $diff->format("%a%") > 0 && $row['deadline'] <= date('Y-m-d')){
						$onepen = number_format($row['amount'] * '.01', 2);
						$penalty = '₱ ' . number_format($onepen * $diff->format("%a%"), 2);
						$pen = number_format($onepen * $diff->format("%a%"),2);
						$due = ' style = "color: red; font-weight: bold;" ';
						$diff = '( ' . $diff->format("%a%") . ' day/s )';
						$pay = 1;
					}else{
						$pay = 0;
						$pen = 0;
						$diff = "";
						$due = "";
						$penalty = ' - ';
					}
					if($row['state'] != 0){
						$throu = ' style = "text-decoration: line-through; " ';
						$due = " style = 'color: green; font-weight: bold;'";
					}else{						
						$totalpen += str_replace(",", "", number_format($pen,2));
						$totaldue += str_replace(",", "", number_format($pen + $row['amount'] + $row['interest'],2));
						$totalamount += str_replace(",", "", number_format($row['amount'],2));
						$totalinte += str_replace(",", "", number_format($row['interest'],2));
					}
					echo '<div class = "row"' . $due .'>';
					echo	'<div class = "col-xs-2 col-xs-offset-1"><i><p>' . date("M j, Y", strtotime($row['deadline'])) . ' ' . $diff . '</p></i></div>';
					echo	'<div class = "col-xs-2"><i><p '.$throu.'>₱ ' . number_format($row['amount'],2) . '</p></i></div>';
					echo	'<div class = "col-xs-2"><i><p '.$throu.'>₱ ' . number_format($row['interest'],2) . '</p></i></div>';
					echo	'<div class = "col-xs-1"><i><p '.$throu.'>' . $penalty . '</p></i></div>';
					echo	'<div class = "col-xs-2"><i><p '.$throu.'>₱ ' . number_format($pen + $row['amount'] + $row['interest'],2) . '</p></i></div>';
					if($row['state'] == 0 && $row['deadline'] <= date('Y-m-d')){
						echo 	'<div class = "col-xs-2"><i><p><a onclick = "setTimeout(\'window.location.href=window.location.href\', 0);" target = "_blank" href = "?module=loan&action=payment&id=' . $_GET['id'] . '&paid='.$row['breakdown_id'].'" class = "btn btn-primary btn-sm"  onclick = "return confirm(\'Are you sure?\');"> Paid </a></div>';
					}elseif($pay == 0 && $row['deadline'] > date('Y-m-d')){
						echo 	'<div class = "col-xs-2"><i><p> - </p></i></div>';	
					}else{
						echo 	'<div class = "col-xs-2"><i><p>Paid</p></i></div>';	
					}
					echo '</div>';
					if($counter == $count['count'] && $row['state'] == 0){
						echo '<div class = "row">';
						echo	'<div class = "col-xs-12"><hr></div>';
						echo '</div>';
						echo '<div class = "row" style = "font-weight: bold; font-style: italic;">';
						echo	'<div class = "col-xs-2 col-xs-offset-1" style = "text-align: right;"><label>Total: <label></div>';
						echo	'<div class = "col-xs-2">₱ ' . number_format($totalamount,2) . '</div>';
						echo	'<div class = "col-xs-2">₱ ' . number_format($totalinte,2) . '</div>';
						echo	'<div class = "col-xs-1">₱ ' . number_format($totalpen,2) . '</div>';
						echo	'<div class = "col-xs-2">₱ ' . number_format($totaldue,2) . '</div>';
						echo 	'<div class = "col-xs-2"><i><p><a onclick = "setTimeout(\'window.location.href=window.location.href\', 0);" target = "_blank" href = "?module=loan&action=payment&id=' . $_GET['id'] . '&paid=all" class = "btn btn-success btn-sm"  onclick = "return confirm(\'Are you sure?\');"> Paid All </a></div>';
						echo '</div>';
						echo '<div class = "row">';
						echo	'<div class = "col-xs-12"><hr></div>';
						echo '</div>';
					}
				}
			}
		?>
	</div>
</div>