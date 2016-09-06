<?php
	if($access->level <= 1){
		echo '<script type = "text/javascript">alert("Restricted.");window.location.replace("loan/list");</script>';
	}else{
?>
<?php
		$loan_id = mysqli_real_escape_string($conn, $_GET['view']);
		$list2 = "SELECT * FROM breakdown as a, payment as b where a.loan_id = '$loan_id' and a.breakdown_id = b.breakdown_id";
		if($conn->query($list2)->num_rows > 0){
			echo '<script type = "text/javascript">alert("Can\'t delete loan with payment record.");window.location.replace("loan/list");</script>';
		}else{
			$breakdown = $conn->prepare("DELETE FROM loan where loan_id = ?");
			$breakdown->bind_param("i", $loan_id);
			$breakdown->execute();
			$breakdown = $conn->prepare("DELETE FROM breakdown where loan_id = ?");
			$breakdown->bind_param("i", $loan_id);
			$breakdown->execute();
			echo '<script type = "text/javascript">alert("Delete loan successful.");window.location.replace("loan/list");</script>';
			savelogs("Delete Loan", "Loan ID -> " . $loan_id);
		}
	}