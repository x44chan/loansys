<?php
    session_start();
    include 'config/title.php';
    include 'config/header.php';
    include 'config/conf.php';
    if(!isset($_GET['module'])){
      $_GET['module'] = 'main';
    }
    if(isset($_SESSION['acc_id'])){
?>
<?php
  $access = "SELECT * FROM user where account_id = '$_SESSION[acc_id]'";
  $access = $conn->query($access)->fetch_object();
  include('config/menu.php');
?>
<?php
  if(isset($_POST['submitrate'])){
    $rate = $conn->prepare("UPDATE rate set daily = ?,weekly = ?,monthly = ?, penalty = ?");
    $rate->bind_param("ssss", $_POST['dailyrate'], $_POST['weeklyrate'], $_POST['monthlyrate'], $_POST['penaltyrate']);
    if($rate->execute()){
      echo '<script type = "text/javascript">alert("Rates Updated");window.location.replace("/loan/'.$_GET['module'].'");</script>';
      savelogs('Change Rate', 'Daily ->' . $_POST['dailyrate'] . ', Weekly -> ' . $_POST['weeklyrate'] . ', Monthly -> ' . $_POST['monthlyrate']);
    }
  }
  $gerate = "SELECT * FROM rate";
  $gerate = $conn->query($gerate)->fetch_assoc();
?>
<?php if($access->level > 1){ ?>
<!-- caModal -->
  <div class="modal fade" id="interest" role="dialog">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:25px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span class = "icon-calculator"></span> Interest Rate</h4>
        </div>
        <div class="modal-body" style="padding:20px 50px;">
          <form role="form" action = "" method = "post">
            <div class="form-group">
              <label for="usrname"> Daily Interest Rate (in decimal) </label>
              <input type = "text" <?php echo ' value = "' . $gerate['daily'] . '" ';?> class="form-control input-sm" placeholder = "Enter Daily Interest Rate" name = "dailyrate" pattern = "[.0-9]*" required>
            </div>
            <div class="form-group">
              <label for="usrname"> Weekly Interest Rate (in decimal) </label>
              <input type = "text" <?php echo ' value = "' . $gerate['weekly'] . '" ';?> class="form-control input-sm" placeholder = "Enter Weekly Interest Rate" name = "weeklyrate" pattern = "[.0-9]*" required>
            </div>
            <div class="form-group">
              <label for="usrname"> Monthly Interest Rate (in decimal) </label>
              <input type = "text" <?php echo ' value = "' . $gerate['monthly'] . '" ';?> class="form-control input-sm" placeholder = "Enter Monthly Interest Rate" name = "monthlyrate" pattern = "[.0-9]*" required>
            </div>
            <div class="form-group">
              <label for="usrname"> Penalty per day (in decimal) </label>
              <input type = "text" <?php echo ' value = "' . $gerate['penalty'] . '" ';?> class="form-control input-sm" placeholder = "Enter Monthly Interest Rate" name = "penaltyrate" pattern = "[.0-9]*" required>
            </div>
            <button type="submit" name = "submitrate" class="btn btn-success btn-block">Update</button>
          </form>
        </div>
      </div>      
    </div>
  </div>
  <!-- caModal -->
  <div class="modal fade" id="addprinci" role="dialog">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:25px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span class = "icon-coin-dollar"></span> Principal Amount</h4>
        </div>
        <div class="modal-body" style="padding:20px 50px;">
          <form role="form" action = "" method = "post">
            <div class="form-group">
              <label for="usrname"> Principal Amount <font color="red">*</font></label>
              <input type = "text" class="form-control input-sm" placeholder = "Enter Amount" name = "principal" pattern = "[.0-9]*" required>
            </div>
            <button type="submit" name = "submitprin" class="btn btn-success btn-block">Submit</button>
          </form>
        </div>
      </div>      
    </div>
  </div>
<?php
  if(isset($_POST['submitprin'])){
    $rate = $conn->prepare("INSERT INTO principal (principal_amount) VALUES (?)");
    $rate->bind_param("s", $_POST['principal']);
    if($rate->execute()){
      echo '<script type = "text/javascript">alert("Adding Principal Successfull");window.location.replace("/loan/'.$_GET['module'].'");</script>';
      savelogs('Add Principal Rate', 'Principal Amount -> ' . $_POST['principal']);
    }
  }
?>
<?php } ?>
  <div class="modal fade" id="changepass" role="dialog">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4>Update Password</h4>
        </div>
        <div class="modal-body" style="padding:40px 50px;">
          <form role="form" action = "" method = "post">
            <div class="form-group">
              <label for="usrname"><span class="glyphicon glyphicon-eye-open"></span> Password</label>
              <input type="password" class="form-control" required id="psw" name = "pword" autocomplete="off"placeholder="Enter password">
            </div>
            <div class="form-group">
              <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Confirm Password</label>
              <input type="password" class="form-control" required id="psw1" name = "pword2" autocomplete="off"placeholder="Enter password">
            </div>
              <button type="submit" id = "submitss" name = "submitpw" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span> Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>
    <div id="loader"></div>
    <!-- Page Content -->
    <div class = "container-fluid animate-bottom" id = "tohide" <?php if(!isset($_GET['print'])){ echo ' style="margin-top: 60px; display: none;"'; }else{ echo ' style = "visibility: hidden" '; }?>>
      <?php
      	/*if(!isset($_GET['module'])){
          include 'modules/main.php';
      	}elseif(!file_exists('modules/'.$_GET['module'].'.php')){
      		include 'config/404.php';
      	}else{
      		include 'modules/'.$_GET['module'].'.php';
      	}*/	
        /*  Foldering */
        include 'ajax/func.php';
        if(!isset($_GET['action'])){
            $acc = 'index.php';
        }else{
            $acc = $_GET['action'].'.php';
        }
        if(!isset($_GET['module'])){
          include 'modules/main/index.php';
        }elseif($_GET['module'] == 'logout'){
            include 'modules/logout.php';
        }elseif(!file_exists('modules/'.$_GET['module'].'/'.$acc)){
            include 'config/404.php';
        }else{
            include 'modules/'.$_GET['module'].'/'.$acc;
        }
     }elseif((isset($_GET['module']) && $_GET['module'] == 'login' && !isset($_SESSION['acc_id'])) || (!isset($_SESSION['acc_id']))){
      ?>
<style type="text/css">
	.table {border-bottom:0px !important;}
	.table th, .table td {border: 0px !important;}
</style>
		<h3 align="center"><i><span class="icon-lock"></span><i class="fa fa-desktop"></i> Login Form</i></h3>
		<form role = "form" action = "" method = "post" id = "tohide" style="display: none;">	
			<table align = "center" class = "table form-horizontal" style = "margin-top: 0px; width: 800px;" >
				<tr>
					<td><label for = "uname"><span class="icon-user"></span>  Username: </label><input <?php if(isset($_POST['uname'])){ echo 'value ="' . $_POST['uname'] . '"'; }else{ echo 'autofocus ';}?>placeholder = "Enter Username" id = "uname" title = "Input your username." type = "text" class = "form-control input-sm" required name = "uname"/></td>
				
					<td><label for = "pword"><span class="icon-eye"></span>  Password: </label><input <?php if(isset($_POST['uname'])){ echo 'autofocus '; }?> placeholder = "Enter Password" id = "pword" title = "Input your password." type = "password" class = "form-control  input-sm" required name = "password"/></td>
				</tr>
				<tr >
					<td colspan = 4 align = "center" ><button style = "width: 150px; margin: auto;" type="submit" name = "submit" class="btn btn-success btn-block btn-sm"><span class="icon-switch"></span> Login</button></td>
				</tr>
			</table>
		</form>
<?php
	if(isset($_SESSION['logout']) && $_SESSION['logout'] != null){
		echo  '<div class="alert alert-warning" align = "center">						
			<strong>You\'ve been logged out.</strong>
			</div>';
		$_SESSION['logout'] = null;
	}
?>
<?php
	if(isset($_POST['submit'])){
		$uname = mysqli_real_escape_string($conn, $_POST['uname']);
		$password =  mysqli_real_escape_string($conn, $_POST['password']);
		
		$sql = "SELECT * FROM `user` where uname = '$uname' and pword = '$password'";
		$result = $conn->query($sql);		
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){								
				$_SESSION['acc_id'] = $row['account_id'];
				$_SESSION['name'] = $row['fname'] . ' ' . $row['mname'] . ', ' . $row['lname'];
        $_SESSION['username'] = $row['uname'];
				//$_SESSION['level']=$row['level'];
			  	echo  '<div class="alert alert-success" align = "center">						
						<strong>Logging in ~!</strong>
						</div>';
			  	echo '<script type="text/javascript">setTimeout(function() {window.location.href = "/loan"},1000);; </script>';	
			}				
		}else{
	echo  '<div class="alert alert-warning" align = "center">						
				<strong>Warning!</strong> Incorrect Login.
			</div>';
			}
		$conn->close();
	}
}
include('config/footer.php');
?>
<script>
    	
</script>
