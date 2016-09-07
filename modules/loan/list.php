<div class="container">
	<div class="row">
		<div class="col-xs-6">
			<i><h4  style="margin-left: -40px;"><span class="icon-list"></span><u> Loan List</u></h4></i>
		</div>
		<div class="col-xs-4 pull-right">
			<input type = "text" placeholder = "Search Box" onkeyup = "loanList(this.value)" class="form-control input-sm">
		</div>
	</div>
	<div style="border: 1px solid #eee; padding: 0px 10px 10px 10px; border-radius: 5px;">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Loan Amount</th>
					<th>Interest</th>
					<th>Total</th>
					<th>Duration / Type</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody id = "onchange">
				<?php
					$counter = "SELECT count(*) as total FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = c.loan_id and c.state = '0' group by b.loan_id";
					$counter2 = $conn->query($counter)->fetch_assoc();
					$perpage = 15;
					$totalPages = ceil($counter2['total'] / $perpage);
					if(!isset($_GET['view'])){
					    $_GET['view'] = 0;
					}else{
					    $_GET['view'] = (int)$_GET['view'];
					}
					if($_GET['view'] < 1){
					    $_GET['view'] = 1;
					}else if($_GET['view'] > $totalPages){
					    $_GET['view'] = $totalPages;
					}
					$startArticle = ($_GET['view'] - 1) * $perpage;
					$list = "SELECT * FROM customer as a,loan as b,breakdown as c where a.customer_id = b.customer_id and b.loan_id = c.loan_id and c.state = '0' group by b.loan_id LIMIT " . $startArticle . ', ' . $perpage;
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
							echo 
								'<td>
									<a href = "loan/view/'.$row['loan_id'].'" class = "btn btn-sm btn-primary" data-toggle="tooltip" title="View"><span class = "icon-search"></span></a>
									<a onclick = "payment('.$row['loan_id'].');" class = "btn btn-sm btn-success" data-toggle="tooltip" title="Payment"><span>₱</span></a>';
								if($access->level >= 2){
									echo ' <a href = "loan/edit/'.$row['loan_id'].'" class = "btn btn-sm btn-warning" data-toggle="tooltip" title="Edit"><span class = "icon-quill"></span></a>';
									echo ' <a href = "loan/delete/'.$row['loan_id'].'" class = "btn btn-sm btn-danger" data-toggle="tooltip" title="Delete"><span class = "icon-bin"></span></a>';
								}
							echo '</td>';
							echo '</tr>';
						}
					}else{
						echo '<tr><td colspan = "6" align = "center"> <h5> No Record Found </h5></td></tr>';
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="modal fade" id="payment" role="dialog"></div>
	<?php
		if(isset($_POST['paysub'])){
		    $loan_id = mysqli_real_escape_string($conn, $_POST['loan_id']);
    		$payment = "SELECT *,sum(payprincipal) as sumpayprincipal, sum(payinterest) as sumpayinterest, sum(paypenalty) as sumpaypenalty FROM loan as a, payment as b where a.loan_id = '$loan_id' and b.loan_id = '$loan_id'";
    		$payment = $conn->query($payment)->fetch_object();
    		$totprinc = $payment->principal - $payment->sumpayprincipal;
    		$totinte = ($payment->principal * $payment->rate) - $payment->sumpayinterest;
    		$diff=date_diff(date_create($payment->due),date_create(date("Y-m-d")));
			if($diff->format("%R%") == '+' && $diff->format("%a%") > 0 && $payment->due <= date('Y-m-d') && $payment->state == 0){
				$penalty = (($payment->principal + ($payment->principal * $payment->rate)) * $gerate['penalty']) * $diff->format("%a%");
			}
			$err = "";
			if($totprinc < $_POST['prin']){
				$err = $err . ' principal ';
			}
			if($totinte < $_POST['inte']){
				if($err != ""){
					$err = $err . ', interest ';
				}else{
					$err = $err . ' interest ';
				}
			}
			if($penalty < $_POST['penal']){
				if($err != ""){
					$err = $err . ', penalty ';
				}else{
					$err = $err . ' penalty ';
				}
			}
			if($err == ""){
				$payment = $conn->prepare("INSERT INTO payment (loan_id, payprincipal, payinterest, paypenalty, paydate) VALUES (?, ?, ?, ?, now())");
				$payment->bind_param("isss", $_POST['loan_id'], $_POST['prin'], $_POST['inte'], $_POST['penal']);
				if($payment->execute() == TRUE){
					savelogs("Add payment", "Payment for Loan ID: " . $_POST['loan_id'] . ' , Principal -> ₱ ' . number_format($_POST['prin'],2) . ' , Interest -> ₱ ' . number_format($_POST['inte'],2) . ' , Penalty -> ₱ ' . number_format($_POST['penal'],2));
					echo '<script type = "text/javascript">alert("Payment Successful");window.location.replace("loan/list");</script>';
				}
			}else{
				savelogs("Payment Error", "Principal Balance: ₱ " . number_format($totprinc,2) . ' -> Inputed Principal Amount: ₱ ' . number_format($_POST['prin'],2) . ', Interest Balance: ₱ ' . number_format($totinte,2) . ' -> Inputed Interest Amount: ₱ ' . number_format($_POST['inte'],2) . ', Penalty Balance: ₱ ' . number_format($penalty,2) . ' -> Inputed Penalty Amount: ₱ ' . number_format($_POST['penal'],2));
				echo '<script type = "text/javascript">alert("Payment Error check your inputed '.$err.'amount/s.");window.location.replace("loan/list");</script>';
				
			}
		}
	?>
	<div class="row" style="margin-top: 10px;">
		<div class="col-xs-12" align="center">
			<!--<label>Records <?php $startArticlex = $startArticle + 1; $perpagex = $perpage * $_GET['view']; if($perpagex > $counter2['total']){ $perpagex = $counter2['total'];} echo $startArticlex . ' - ' . $perpagex ?> </label><br>-->
			<label> Pages </label><br>
			<?php
				$prev = intval($_GET['view'])-1;					
				if($prev > 0){ echo '<a data-toggle="tooltip" title="Previous" class = "btn btn-default btn-sm" style = "margin: 5px;" href="loan/list/' . $prev . '"> < </a>'; }
				foreach(range(1, $totalPages) as $page){
				    if($page == $_GET['view']){
				        echo '<b><span class="currentpage" style = "margin: 5px;">' . $page . '</span></b>';
				    }else if($page == 1 || $page == $totalPages || ($page >= $_GET['view'] - 2 && $page <= $_GET['view'] + 2)){
				    	if($page == 0){
				    		continue;
				    	}
				        echo '<a class = "btn btn-default btn-sm" data-toggle="tooltip" title="Page ' . $page . '" style = "margin: 5px;" href="loan/list/' . $page . '">' . $page . '</a>';
				    }
				}
				$nxt = intval($_GET['view'])+1;
				if($nxt <= $totalPages){ echo '<a class = "btn btn-default btn-sm" data-toggle="tooltip" title="Next" style = "margin: 5px;" href="loan/list/' . $nxt . '"> > </a>'; }
			?>
		</div>
	</div>
</div>