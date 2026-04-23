<?php 

 
 $plans = $this->data['plans'];
$count=1;
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

	<!-- swett alert cdn -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.0.min.js"></script>
    <script type="text/javascript" src="js/jquery.tooltipster.min.js"></script>

</head>

<style type="text/css">
	
	.container {
		display: flex; 
		width: 100%;
		padding-left: 0px;
	}

	button {
		background: #99A3FF;
		color: white;
		border: 0;
		padding: 6px 12px;
		border-radius: 3px;
	}

	.button-container {
	padding: 0px 8px;
	}

	.button-container:not(:last-child){
		border-right: 1px solid #ddd;
	}

	.btn{
		/* Add shadows to create the "card" effect */
		/*box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);*/
    	transition: 0.3s;
	}
	.btn:hover{
		box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
	}

</style>


<body>
    <?php echo announcements_alert_widget();?>

	<div class="card">
		<div ><h3 style="display: flex;justify-content: center;">Manage-Plans</h3></div>
		<div class="card-body">
			<table class="table table-striped table-hover">
		
				<tr>
					<th>S.No</th>
					<th>Plans</th>
					<th>Action</th>
				</tr>
				<?php foreach($plans as $plan){ ?>
				<tr>
					<td><?php echo $count++; ?></td>
					<td><?php echo $plan->name; ?></td>
					<td>
						<a class="btn btn-primary" href="/store-admin/index.php/Manage_PlanAndPlugin/activePlugins?plan_id=<?php echo $plan->plan_Id ?>">Active Plugins</a> 
					</td>
					
				</tr>
			<?php } ?>
			</table>
		</div>
	</div>

</body>
</html>

