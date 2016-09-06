<div class="container-fluid" id = "reportg" style="margin: 0px 5px; 5px; 0px;">
	<div class="row">
		<div class="col-xs-6">
			<h4><b><u><i><span class="icon-paste"></span> Due List <?php if(isset($_GET['date']) && $_GET['date'] != ""){ echo '( ' .date('M j Y') . ' to ' . date("M j, Y", strtotime($_GET['date'])) . ' )'; }?></i></u></b></h4>
			<p style="font-size: 13px; margin-left: 10px;"><i>(as of <?php echo date("M j, Y h:i:s A"); ?>)</i></p>
		</div>
		<div class="col-xs-6 form-inline" align="right" id = "backs">
			<form action="" method="get">
				<label> Due By: </label>
				<input <?php if(isset($_GET['date']) && $_GET['date'] != ""){ echo ' value = "' . $_GET['date'] . '" '; $date = mysqli_real_escape_string($conn, $_GET['date']); } else { $date = date("Y-m-d"); }?> type = "date" class="form-control input-sm" name = "date"/>
				<button type="submit" class="btn btn-sm btn-primary"><span class="icon-checkmark"></span> Submit </button>
				<a href="loan/due" class="btn btn-danger btn-sm"><span class = "icon-spinner11"></span> Clear </a>
				<a href = '<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>&print' id = "backs" class = "btn btn-success btn-sm"><span class = "icon-printer"></span> Print </a>
			</form>
		</div>
	</div>
	<div style="border: 1px solid #eee; padding: 5px; border-radius: 5px;">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Principal Amount</th>
					<th>Interest</th>
					<th>Penalty</th>
					<th>Total Due</th>
					<th>Deadline</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$curmonth = date("m");
				$due = "SELECT * FROM loan,breakdown,customer where loan.customer_id = customer.customer_id and loan.loan_id = breakdown.loan_id and breakdown.state = '0' and (breakdown.deadline <= CURDATE() or deadline <= '$date')";
				$due = $conn->query($due);
				$totalprin = 0;
				$totalint = 0;
				$totalpen = 0;
				$totaldue = 0;
				$gerate = "SELECT * FROM rate";
				$gerate = $conn->query($gerate)->fetch_assoc();
				$counter = 0;
				if($due->num_rows > 0){
					while ($row = $due->fetch_object()) {
						$counter += 1;
						$xpayment = "SELECT sum(payprincipal) as principal, sum(payinterest) as interest, sum(paypenalty) as penalty FROM payment where loan_id = '$row->loan_id'";
						$xpayment = $conn->query($xpayment)->fetch_object();					
						$diff=date_diff(date_create($row->deadline),date_create(date("Y-m-d")));
						if($diff->format("%R%") == '+' && $diff->format("%a%") > 0 && $row->deadline <= date('Y-m-d')){
							$onepen = ($row->amount + $row->interest) * $gerate['penalty'];
							$penalty = number_format($onepen * $diff->format("%a%"), 2);
							$duex = ' id = "redx" ';
							$diff = '( ' . $diff->format("%a%") . ' day/s )';
						}else{
							$diff = "";
							$duex = "";
							$penalty = 0;
						}
						$totalprin += $row->amount;
						$totalint += $row->interest;
						$totalpen += str_replace(",", "", $penalty);
						$totaldue += $row->amount + $row->interest + str_replace(",", "", $penalty);
			?>
				<tr <?php echo $duex;?>>
					<td><?php echo $counter;?></td>
					<td><?php echo $row->fname . ' ' . $row->mname . ' ' . $row->lname; ?></td>
					<td>₱ <?php echo number_format($row->amount - $xpayment->principal,2); ?></td>
					<td>₱ <?php echo number_format($row->interest - $xpayment->interest,2); ?></td>
					<td>₱ <?php echo number_format(str_replace(",", "", $penalty) - $xpayment->penalty,2); ?></td>
					<td>₱ <?php echo number_format(($row->amount + $row->interest + str_replace(",", "", $penalty)) - ($xpayment->principal + $xpayment->interest + $xpayment->penalty),2); ?></td>
					<td><?php echo date("M j, Y", strtotime($row->deadline)) . ' ' . $diff; ?></td>
				</tr>
			<?php	
					}
			?>
				<tr>
					<td></td>
					<td align="center"><b><i>Total</b></td>
					<td><b><i>₱ <?php echo number_format($totalprin - $xpayment->principal,2); ?></td>
					<td><b><i>₱ <?php echo number_format($totalint - $xpayment->interest,2); ?></td>
					<td><b><i>₱ <?php echo number_format($totalpen - $xpayment->penalty,2); ?></td>
					<td><b><i>₱ <?php echo number_format($totaldue - ($xpayment->principal + $xpayment->interest + $xpayment->penalty),2); ?></td>
					<td></td>
				</tr>
			<?php
				}else{
					echo '<td colspan = 7 align = center> <h4> No Due </h4></td>';
				}
			?>
				
			</tbody>
		</table>
	</div>	
</div>

<?php
	if(isset($_GET['print'])){
		echo '<script type = "text/javascript">	window.print();window.location.href = "'.$_GET['module'].'/'.$_GET['action'].'";</script>';
	}
?>