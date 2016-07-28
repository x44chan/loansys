<script type="text/javascript">
	function showUser() {
		type = $('select[name = "type"]').val();
		amount = $("input[name = 'amount']").val();
		duration = $("input[name = 'duration']").val();
		strtdate = $("input[name = 'strtdate']").val();
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
		    xmlhttp.open("GET","ajax/ajaxowner.php?amount="+amount+"&type="+type+"&duration="+duration+"&strtdate="+strtdate,true);
		    xmlhttp.send();
		}
	}
</script>