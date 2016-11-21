<script type="text/javascript">
	function showUser() {
		type = $('select[name = "type"]').val();
		amount = $("input[name = 'amount']").val();
		duration = $("input[name = 'duration']").val();
		strtdate = $("input[name = 'strtdate']").val();
		sprate = $("input[name = 'specialrate']").val();
		if (type == "") {
		    document.getElementById("details").innerHTML = "";
		    return;
		} else { 
		    if (window.XMLHttpRequest) {
		        // code for IE7+, Firefox, Chrome, Opera, Safari
		        xmlhttp = new XMLHttpRequest();
		    } else {
		        // code for IE6, IE5
		        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		    }
		    xmlhttp.onreadystatechange = function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		            document.getElementById("details").innerHTML = xmlhttp.responseText;
		        }
		    };
		    xmlhttp.open("GET","ajax/ajaxowner.php?amount="+amount+"&type="+type+"&duration="+duration+"&strtdate="+strtdate+"&sprate="+sprate,true);
		    xmlhttp.send();
		}
	}
<?php if(isset($_GET['action']) && $_GET['action'] == 'list') { ?>
	function paytypex(type) {
		if (type == "Check") {
	    	$('#check').show();
	    	$('input[name="checknum"]').attr('required',true);
	    	$('input[name="check"]').attr('required',true);
	    }else{
	    	$('#check').hide();
	    	$('input[name="checknum"]').attr('required',false);
	    	$('input[name="check"]').attr('required',false);
    	}
	}
	function payment(str) {
		if (str == "") {
		    document.getElementById("payment").innerHTML = "";
		    return;
		} else { 
		    if (window.XMLHttpRequest) {
		        // code for IE7+, Firefox, Chrome, Opera, Safari
		        xmlhttp = new XMLHttpRequest();
		    } else {
		        // code for IE6, IE5
		        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		    }
		    xmlhttp.onreadystatechange = function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		            document.getElementById("payment").innerHTML = xmlhttp.responseText;
		        }
		    };
		    xmlhttp.open("GET","ajax/ajaxowner.php?payment="+str,true);
		    xmlhttp.send();
		    $("#payment").modal();
		}
	}
	function loanList(str) {
	   if (window.XMLHttpRequest) {
	        // code for IE7+, Firefox, Chrome, Opera, Safari
	        xmlhttp = new XMLHttpRequest();
	    } else {
	        // code for IE6, IE5
	        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            document.getElementById("onchange").innerHTML = xmlhttp.responseText;
	        }
	    };
	    xmlhttp.open("GET","ajax/ajaxowner.php?loanlist="+str,true);
	    xmlhttp.send();		
	}
<?php } ?>
</script>