<?php 
 $plans=$this->data['plans'];
//  $plugin_id=$this->data['plugin_id'];
 $plugin_name=$this->data['plugin_name'];
 $plugin_title=$this->data['plugin_title'];
 $my_plans=$this->data['myPlans'];

 // $req=$this->data['req'];
 // echo $req;
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Bootstrap CDN -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<title></title>
</head>
<body>
	
	<div class="row ">
		<div class="col-md-2"></div>
		<div class="card co-md-8" style="width:50%">
		  <div class="card-header">
		    <h3><?php echo $plugin_title; ?></h3>
		  </div>
		  <ul class="list-group list-group-flush">
		  	<form  action="/store-admin/index.php/Manage_PlanAndPlugin/insertPlan" >

		  		 <input type="text" name="plugin_name" value="<?php echo $plugin_name; ?>" hidden>
		  		<?php foreach($plans as $plan){ ?>
		  		<li class="list-group-item">
		  			<input type="checkbox" class="ml-4" value="<?php echo $plan->plan_Id; ?>" name="choosedPlan[]" <?php foreach($my_plans as $mp){ if($mp->plan_id == $plan->plan_Id) echo 'checked';} ?>>
		  			<label><?php echo $plan->name; ?></label>
		  			
		  		</li>
		  		<?php } ?>
		  		<li class="list-group-item">
		  			<input type="submit" class="btn btn-primary" value="submit"  name="submit">
		  			<!-- <button class="btn btn-primary">Submit</button> -->
		  		</li>
		  	</form>
		    
		  </ul>
		</div>
		<div class="col-md-2"></div>
	</div>

</body>
</html>