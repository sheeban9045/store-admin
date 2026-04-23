<?php 

//  $plugin_id=$this->data['plugin_id'];
 $plugin_name=$this->data['plugin_name'];
 $plugin = $this->data['plugin'];

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
		
		#body{
		  width: 100%;
		  height: 100px;
		  padding: 12px 20px;
		  box-sizing: border-box;
		  border: 2px solid #ccc;
		  border-radius: 4px;
		  background-color: #f8f8f8;
		  font-size: 16px;
		  resize: none;
		}
		#title{
		  width: 50%;
		  height: 50px;	
		  border: 2px solid #ccc;
		  border-radius: 4px;
		  background-color: #f8f8f8;
		  font-size: 20px;
		  resize: none;
		}

	</style>



</head>
<body>
	<div class="row ">
		<div class="col-md-2"></div>
		<div class="card co-md-8" style="width:50%">
			<div class="card-header">
		    	<h3>Edit <?php echo $plugin_name; ?></h3>
		    </div>

		    <div>
		    	<?php 

				echo form_open_multipart(get_uri('Manage_PlanAndPlugin/editPost'), array("id" => "edit-plugin-form", "class" => "general-form", "role" => "form"));
				 ?>

                    <input hidden type="name" name="name" value="<?php echo $plugin_name ?>" >
		    		<label><b>Title<b></label><br>
		    		<input type="text" name="title" value="<?php echo $plugin[0]->title ?>" >
		    		<br>
		    		<br>

		    		<!--<label><b>Choose Image</b></label>-->
		    		<!--<input type="file" name="image" class="form-control">-->
		    		<!--<br>-->

		    		<label><b>Status<b></label><br>
		    		 <i>Active </i><input type="radio" id="status" value="1" name="status" <?php 
		    		 if($plugin[0]->enabled == 1) echo "checked"; ?>>
		    		<i>Inactive </i> <input type="radio" id="status" value="0" name="status" <?php if($plugin[0]->enabled == 0) echo "checked"; ?>>
		    		<br>
		    		<br>
		    		<label><b>Description<b></label><br>
		    		<textarea id="body" name="body"><?php echo $plugin[0]->description; ?></textarea>
		    		<br><br>
			    	
			    	<div class="d-inline" id="btnDiv"> 
			  			<input type="submit" value="Update" class="btn btn-primary">

			  			<button class="btn btn-warning"><a href="/store-admin/index.php/Manage_PlanAndPlugin/index_Plugins">Back To Plugins</a></button>
			  		</div>
			</div>

		    	<?php echo form_close(); ?>
		    	
		    </div>
		    
		<div class="col-md-2"></div>
	</div>
   

	<script>
		
	$(document).ready(function(){

		$("#update").click(function(){
		    	
	        	var body = $("#body").val();
	        	var title = $("#title").val();
	        	var status = $("input[type='radio']:checked").val();

	        	// console.log(body);
	        	// console.log(title);	
	        	// console.log(status);	
	        	// console.log(plugin_id);	

	        	$.ajax({
		        	url: "/store-admin/index.php/Manage_PlanAndPlugin/editPost",
		        	type : "POST",
		        	dataType: "json",
		        	data : {
		        		body : body,
		        		title : title,
		        		status : status,
		        		name  : <?php echo $plugin_name ?>,
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