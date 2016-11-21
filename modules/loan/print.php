<?php
	if(!isset($_GET['view'])){
		echo '<script type = "text/javascript">alert("Restricted.");window.location.replace("loan/list");</script>';
	}else{
		$loan_id = mysqli_real_escape_string($conn, $_GET['view']);
		$print = "SELECT * FROM customer as a, loan as b where a.customer_id = b.customer_id and b.loan_id = '$loan_id'";
		$print = $conn->query($print);
		if($print->num_rows <= 0){
			echo '<script type = "text/javascript">alert("No Record Found.");window.location.replace("loan/list");</script>';
		}
		$print = $print->fetch_object();
		if($print->type == 'Daily'){
			$type = 'Day/s';
		}elseif($print->type == 'Weekly'){
			$type = 'Week/s';
		}else{
			$type = 'Month/s';
		}
?>
<div class="container" id = "reportg">
	<table class="table table-bordered" style="text-align: center;">
		<tbody>
			<tr>
				<td colspan="4"><p class="pull-left"> Borrower's Copy  <br><b>Transaction Number: <?php echo $print->trxn;?></b></p><b><h4>LOANS</h4></td>
			</tr>
			<tr>
				<td>Name: </td>
				<td><?php echo $print->fname . ' ' . $print->mname . ' ' . $print->lname; ?></td>
				<td>Start Date: </td>
				<td><?php echo date("M j, Y");?></td>
			</tr>
			<tr>
				<td>Address: </td>
				<td><?php echo $print->address; ?></td>
				<td>Interest: </td>
				<td><?php echo $print->rate * 100;?>%</td>
			</tr>
			<tr>
				<td>Loan Amount: </td>
				<td>₱ <?php echo number_format($print->principal,2);?></td>
				<td>Terms: </td>
				<td><?php echo $print->duration . ' - ' . $type; ?></td>
			</tr>
			<tr>
				<td>Reason: </td>
				<td><?php echo nl2br($print->reason);?></td>
				<td>POID: </td>
				<td></td>
			</tr>
			<tr>
				<td>Deductions: </td>
				<td><?php echo $print->type . ' - ' . number_format(($print->principal + ($print->principal * $print->rate)) / ($print->duration),2); ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Release Date: </td>
				<td><?php echo date("M j, Y");?></td>
				<td>Loan Due: </td>
				<td><?php echo date("M j, Y", strtotime("+".$print->duration.' '. str_replace("/s", "", $type), strtotime($print->startdate)));?></td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td colspan="4">
					This is to certify that I <u><?php echo '&nbsp      ' .$print->fname . ' ' . $print->mname . ' ' . $print->lname . '      &nbsp'; ?></u> and EEN Loans both agreed in terms and deductions. <br>If in any
					case that there will be delay in payment for a valid reason, interest will be charge accordingly. In the case that borrower <br>
					cant continue the payment for a valid reason; co-maker shall be responsible for the balance loan amount. For shortened loan term
					versus agreed; interest will be considerable <br> and subject for discussion between the borrower and EEN Loans.
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="row">
						<div class="col-xs-3 col-xs-offset-1">
							<br><br>
							<br><br>
							<?php echo $print->fname . ' ' . $print->mname . ' ' . $print->lname; ?>
							<hr>
							(Signature Over Printed Name)<br>
							Borrower
						</div>
						<div class="col-xs-3 col-xs-offset-4">
							<br><br>
							<br><br>
							<?php echo $print->comaker;?>
							<hr>
							(Signature Over Printed Name)<br>
							Co-Maker
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<hr style="border-top: 3px dotted black">
	<br>
	<table class="table table-bordered" style="text-align: center;">
		<tbody>
			<tr align="center">
				<td colspan="4" align="center"><p class="pull-left"> Lender's Copy <br><b>Transaction Number: <?php echo $print->trxn;?></b></p><b><h4>LOANS</h4></b></td>
			</tr>
			<tr>
				<td>Name: </td>
				<td><?php echo $print->fname . ' ' . $print->mname . ' ' . $print->lname; ?></td>
				<td>Start Date: </td>
				<td><?php echo date("M j, Y");?></td>
			</tr>
			<tr>
				<td>Address: </td>
				<td><?php echo $print->address; ?></td>
				<td>Interest: </td>
				<td><?php echo $print->rate * 100;?>%</td>
			</tr>
			<tr>
				<td>Loan Amount: </td>
				<td>₱ <?php echo number_format($print->principal,2);?></td>
				<td>Terms: </td>
				<td><?php echo $print->duration . ' - ' . $type; ?></td>
			</tr>
			<tr>
				<td>Reason: </td>
				<td><?php echo nl2br($print->reason);?></td>
				<td>POID: </td>
				<td></td>
			</tr>
			<tr>
				<td>Deductions: </td>
				<td><?php echo $print->type . ' - ' . number_format(($print->principal + ($print->principal * $print->rate)) / ($print->duration),2); ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Release Date: </td>
				<td><?php echo date("M j, Y");?></td>
				<td>Loan Due: </td>
				<td><?php echo date("M j, Y", strtotime("+".$print->duration.' '. str_replace("/s", "", $type), strtotime($print->startdate)));?></td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td colspan="4">
					This is to certify that I <u><?php echo '&nbsp      ' .$print->fname . ' ' . $print->mname . ' ' . $print->lname . '      &nbsp'; ?></u> and EEN Loans both agreed in terms and deductions. <br>If in any
					case that there will be delay in payment for a valid reason, interest will be charge accordingly. In the case that borrower <br>
					cant continue the payment for a valid reason; co-maker shall be responsible for the balance loan amount. For shortened loan term
					versus agreed; interest will be considerable <br> and subject for discussion between the borrower and EEN Loans.
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="row">
						<div class="col-xs-3 col-xs-offset-1">
							<br><br>
							<br><br>
							<?php echo $print->fname . ' ' . $print->mname . ' ' . $print->lname; ?>
							<hr>
							(Signature Over Printed Name)<br>
							Borrower
						</div>
						<div class="col-xs-3 col-xs-offset-4">
							<br><br>
							<br><br>
							<?php echo $print->comaker;?>
							<hr>
							(Signature Over Printed Name)<br>
							Co-Maker
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php
	if(isset($_GET['print'])){
		echo '<script type = "text/javascript">	window.print();window.location.href = "'.$_GET['module'].'/view/'. $loan_id .'";</script>';
	}
}
?>