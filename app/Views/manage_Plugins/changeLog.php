<?php 

//  $plugin_id=$this->data['plugin_id'];
 $plugin_name=$this->data['plugin_name'];
 $plugin_title=$this->data['plugin_title'];
 $logs = $this->data['logs'];
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<title></title>
	<style type="text/css">

		.output{
	        padding: 10px;
	        min-height: 50px;
	        border: 1px solid #e4e4e4;
    	}

		textarea {
		  width: 100%;
		  height: 150px;
		  padding: 12px 20px;
		  box-sizing: border-box;
		  border: 2px solid #ccc;
		  border-radius: 4px;
		  background-color: #f8f8f8;
		  font-size: 16px;
		  resize: none;
		}
		input{
			border: 2px solid #ccc;
		  border-radius: 4px;
		  background-color: #f8f8f8;
		  font-size: 10px;
		  resize: none;
		}

	</style>
</head>
<body>
	<div class="row ">
		<div class="col-md-2"></div>
		<div class="card co-md-8" style="width:50%">
		  <div class="card-header">
		    <h3>Logs Of <?php echo $plugin_title; ?></h3>
		  </div>
		  <div>
		  		
			  	<div class="output">
			  			<table class="table">
						  <thead>
						    <tr>
						      <th scope="col">Version</th>
						      <th scope="col">Changes</th>
						    </tr>
						  </thead>
						  <tbody>
						  	<?php foreach($logs as $log){ ?>
						    <tr>
						      <td><?php echo $log->version ?></td>
						      <td><?php echo $log->body ?></td>
						    </tr>
						   <?php } ?>
						  </tbody>
					</table>
			  	</div>
			  	<br>
		  		<div>
		  			<form>
		  				<label><b>Enter new logs<b></label>
		  				<textarea id="myTextarea" required>
		  				
		  				</textarea>
		  				<br>
		  				<label>	<b>Enter Version</b></label>
		  				<br>
		  				<input type="text" id="version" required>
		  			</form>
		  		</div>
		  		<br>
		  		<div class="d-inline"> 
		  			<button class="btn btn-primary" id="update">Update Log</button>

		  			<a class="btn btn-info" href="/store-admin/index.php/Manage_PlanAndPlugin/index_Plugins">Back To Plugins</a>
		  		</div>
		  		<br>
		  </div>
		</div>
		<div class="col-md-2"></div>
	</div>

	<script>
	$(document).ready(function(){

        $("#update").click(function(){	
        	var txt = $("#myTextarea").val();
        	console.log(txt);
        	var version = $("#version").val();
        	console.log(version);
        	var plugin_name = "<?php echo $plugin_name ?>";
        	
        	console.log(plugin_name);
	        $.ajax({
		        	url: "/store-admin/index.php/Manage_PlanAndPlugin/updateLog",
		        	type : "GET",
		        	dataType: "json",
		        	data : {
		        		text : txt,
		        		version : version,
		        		plugin_name  : plugin_name,
		        	},
		            success: function(result){
		            	location.reload(true);
		            	console.log("Success");
		        	},
		        	error: function(){
		        		location.reload(true);
		        		console.log("Error");
		        	}
	        	});

	    	 }); 
		});
	</script>
</body>
</html>