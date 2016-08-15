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
							echo '<td><a href = "?module=loan&action=view&id='.$row['loan_id'].'" class = "btn btn-sm btn-primary"> View Details </a></td>';
							echo '</tr>';
						}
					}else{
						echo '<tr><td colspan = "6" align = "center"> <h5> No Record Found </h5></td></tr>';
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="col-xs-12" align="center">
			<!--<label>Records <?php $startArticlex = $startArticle + 1; $perpagex = $perpage * $_GET['page']; if($perpagex > $counter2['total']){ $perpagex = $counter2['total'];} echo $startArticlex . ' - ' . $perpagex ?> </label><br>-->
			<label> Pages </label><br>
			<?php
				$prev = intval($_GET['page'])-1;					
				if($prev > 0){ echo '<a data-toggle="tooltip" title="Previous" class = "btn btn-default btn-sm" style = "margin: 5px;" href="?module=loan&action=list&page=' . $prev . '"> < </a>'; }
				foreach(range(1, $totalPages) as $page){
				    if($page == $_GET['page']){
				        echo '<b><span class="currentpage" style = "margin: 5px;">' . $page . '</span></b>';
				    }else if($page == 1 || $page == $totalPages || ($page >= $_GET['page'] - 2 && $page <= $_GET['page'] + 2)){
				    	if($page == 0){
				    		continue;
				    	}
				        echo '<a class = "btn btn-default btn-sm" data-toggle="tooltip" title="Page ' . $page . '" style = "margin: 5px;" href="?module=loan&action=list&page=' . $page . '">' . $page . '</a>';
				    }
				}
				$nxt = intval($_GET['page'])+1;
				if($nxt <= $totalPages){ echo '<a class = "btn btn-default btn-sm" data-toggle="tooltip" title="Next" style = "margin: 5px;" href="?module=loan&action=list&page=' . $nxt . '"> > </a>'; }
			?>
		</div>
	</div>
</div>