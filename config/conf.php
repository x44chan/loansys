<?php
	$host = "127.0.0.1";
	$uname = "root";
	$pword = "";
	$db = "loan";
	
	$conn = new mysqli($host, $uname, $pword, $db);
	
	if($conn->connect_error){
		die("Connection error:". $conn->connect_error);
	}
	function savelogs($transaction,$transdetails){
		$hostname = 'localhost';
		$username = 'root';
		$password =  '';
		$database = 'loan';
		$conn = mysqli_connect($hostname, $username, $password, $database);
		if (mysqli_connect_errno()){
			die ('Unable to connect to database '. mysqli_connect_error());
		}
		$pcname = gethostname();
		
	    $username = $_SESSION['username'];
		$realname = $_SESSION['name'];
	    $sqllogs = "insert into audit_trail(username,realname,transaction,datetrans,transdetail,pcname) values('$username','$realname','$transaction',now(),'$transdetails','$pcname')";            
	    $result = mysqli_query($conn, $sqllogs);
	}
	function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9));
	    $keys2 = array_merge(range('A', 'Z'));
	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	        if($i %3 == true){
	       		$key .= $keys2[array_rand($keys2)];
	        }
	    }
	    return $key;
	}
?>
