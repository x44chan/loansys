<?php
    session_start();
    include 'config/title.php';
    include 'config/header.php';
    include 'config/conf.php';
    if(isset($_SESSION['acc_id'])){
?>
<!-- Static navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div style = "float: bottom" class="navbar-header">
        	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        		<span class="sr-only">Toggle navigation</span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        	</button>
        	<a class="navbar-brand" href="/loan"><span class="icon-office" style = "color: #009999;"></span> Loan </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        	<ul class="nav navbar-nav navbar-left">
        		<li><a  role = "button" href="/loan"><span class="icon-home3" style = "font-weight: bold;"></span> Home</a></li>
        		<li class="dropdown">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-coin-dollar"></span> Loan <b class="caret"></b></a>
	            <ul class="dropdown-menu" role="menu">
	             <li><a role = "button" href = "?module=loan"><span class="icon-plus"></span> New Loan Application </a></li>
               <li><a role = "button" href = "?module=loan&action=list"><span class="icon-list"></span> Loan List </a></li>
	            </ul>
	          </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="icon-cogs"></span> System Management <b class="caret"></b></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#"  data-toggle="modal" data-target="#interest"> <span class = "icon-calculator"></span> Interest Rate</a></li>
                </ul>
            </li> 
          </ul>
        	<ul class="nav navbar-nav navbar-right">
        		<li class="dropdown">
            		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-user"></span> <?php echo $_SESSION['name']; ?> <b class="caret"></b></a>
            		<ul class="dropdown-menu" role="menu">            			
						<li><a role = "button" href = "?module=changepass"><span class="icon-eye"></span> Change Password </a></li>
                		<li><a style = "color: red;" role = "button" href = "?module=logout"><span class="icon-switch"></span> Log Out </a></li>					
            		</ul>
            	</li> 
        	</ul>
        </div>
      </div>
    </nav>
    <?php
  if(isset($_POST['submitrate'])){
    $rate = $conn->prepare("UPDATE rate set daily = ?,weekly = ?,monthly = ?");
    $rate->bind_param("sss", $_POST['dailyrate'], $_POST['weeklyrate'], $_POST['monthlyrate']);
    if($rate->execute()){
      echo '<script type = "text/javascript">alert("Rates Updated");window.location.replace("/loan/?module='.$_GET['module'].'");</script>';
    }
  }
  $gerate = "SELECT * FROM rate";
  $gerate = $conn->query($gerate)->fetch_assoc();
?>
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
            <button type="submit" name = "submitrate" class="btn btn-success btn-block">Update</button>
          </form>
        </div>
      </div>      
    </div>
  </div>
    <!-- Page Content -->
    <div class = "container-fluid" id = "tohide" style="margin-top: 60px; visibility: hidden;">
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
				$_SESSION['name']=$row['fname'];
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
    NProgress.start();
    $("#b-0").click(function() { NProgress.start(); });
    $("#b-40").click(function() { NProgress.set(0.4); });
    $("#b-inc").click(function() { NProgress.inc(); });
    setTimeout(function() { NProgress.done(); $("#tohide").css('visibility','visible').hide().fadeIn('slow'); }, 1000);
    $("#b-100").click(function() { NProgress.done(); });	
</script>
