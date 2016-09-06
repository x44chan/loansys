<div class="container">
	<i><h4  style="margin-left: -40px;"><span class="icon-credit-card"></span> <u>Loan System</u></h4></i>
	<table class="table table-responsive">
		<thead>
			<tr>
				<th> Deposit Date </th>
				<th> Principal Amount </th>
				<th> Remaining Principal </th>
				<th> Interest Income </th>
				<th> Grand Total (Principal + Income) </th>
			</tr>
		</thead>
		<tbody>
	<?php
		$totalprin = 0; $totalremain = 0; $totalinte = 0; $totalgrand = 0;
		$main = "SELECT *,sum(b.principal) as total_loan,sum(b.principal * b.rate) as total_interes  FROM principal as a, loan as b where a.principal_id = b.principal_id ORDER BY a.principal_date DESC";
		$main = $conn->query($main);
		if($main->num_rows > 0){
			while ($row = $main->fetch_object()) {
				echo '<tr>';
				echo	'<td>' . date("M j, Y h:i A", strtotime($row->principal_date)) . '</td>';
				echo	'<td>₱ ' . number_format($row->principal_amount, 2) . '</td>';
				echo	'<td>₱ ' . number_format($row->principal_amount - $row->total_loan, 2) . '</td>';
				echo	'<td>₱ ' . number_format($row->total_interes, 2) . '</td>';
				echo	'<td>₱ ' . number_format($row->principal_amount + $row->total_interes, 2) . '</td>';
				echo '</tr>';
				$totalprin += $row->principal_amount;
				$totalremain += $row->principal_amount - $row->total_loan;
				$totalinte += $row->total_interes;
				$totalgrand += $row->principal_amount + $row->total_interes;
			}
		}
		$main = "SELECT * FROM principal as a where a.principal_id NOT IN (SELECT principal_id FROM loan) ORDER BY a.principal_date DESC";
		$main = $conn->query($main);
		if($main->num_rows > 0){
			while ($row = $main->fetch_object()) {
				echo '<tr>';
				echo	'<td>' . date("M j, Y h:i A", strtotime($row->principal_date)) . '</td>';
				echo	'<td>₱ ' . number_format($row->principal_amount, 2) . '</td>';
				echo	'<td> - </td>';
				echo	'<td> - </td>';
				echo	'<td>₱ ' . number_format($row->principal_amount, 2) . '</td>';
				echo '</tr>';
				$totalprin += $row->principal_amount;
				$totalgrand += $row->principal_amount;
			}
		}
		echo '<tr>';
		echo	'<td style = "border-top: 1px solid black;"><label> Total </label></td>';
		echo	'<td style = "border-top: 1px solid black;"><label>₱ ' . number_format($totalprin, 2) . '</label></td>';
		echo	'<td style = "border-top: 1px solid black;"><label>₱ ' . number_format($totalremain, 2) . '</label></td>';
		echo	'<td style = "border-top: 1px solid black;"><label>₱ ' . number_format($totalinte, 2) . '</label></td>';
		echo	'<td style = "border-top: 1px solid black;"><label>₱ ' . number_format($totalgrand, 2) . '</label></td>';
		echo '</tr>';
	?>
		</tbody>
	</table>
</div>