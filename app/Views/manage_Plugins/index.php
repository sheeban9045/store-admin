<?php 
$plugins = $this->data['plugins'];
$plans = $this->data['plans'];
$sno=1;
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
	.no-wrap {
    	text-wrap: nowrap;
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
	
	.btn-main {
    	float: right;
    	margin: 0% 2% 1% 0%;
    }

</style>


<body>
       <?php echo announcements_alert_widget();?>

	<div class="card">
		<div ><h3 style="display: flex;justify-content: center;">Manage-Plugins</h3></div>
		<div class="card-body">
	        <a class="btn btn-primary btn-lg btn-main" href="<?php echo "/webhut-parent-community/index.php/store/community-plugins-store/user_email/".$login_user->email;?>" target = "_blank">Create/Edit Plugins</a>
			<table class="table table-striped table-hover">
			    
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Description</th>
					<th>Individual Price</th>
					<th>Status</th>
					<th>Plans</th>
					<th>Action</th>
				</tr>
					<?php foreach($plugins as $plugin){ ?>
						<tr>
						<td><?php echo $sno++; ?></td>
						<td><a href="/plugins.php?plugin_name=<?php echo $plugin->plugin_name; ?>">
							<?php echo $plugin->title; ?>
						</a></td>
						<td>
							<?php echo $plugin->body; ?>
						</td>
						<td>
						    
							<?php echo '$'.$plugin->price-$plugin->discount_amount; ?>
						</td>
						<td><?php if($plugin->allow_purchase==1){echo "Active";}else{echo "Inactive";} ?></td>
						
						<td>
							<?php foreach($plans[$plugin->plugin_name] as $plan){ ?>
								<div class="d-inline">
									<?php  echo $plan->name.", " ?>
								</div>
							<?php } ?>
						</td>
						
						<td>
							<div class="container">
								<div class="button-container">
									<a class="btn btn-primary no-wrap" href="<?php echo "/webhut-parent-community/stores/product/edit/$plugin->product_id/user_email/".$login_user->email;?>" target = "_blank">Edit-Plugin</a>
								</div>
								<div class="button-container">
									<a class="btn btn-primary no-wrap" href="/store-admin/index.php/Manage_PlanAndPlugin/addToPlan?plugin_name=<?php echo $plugin->plugin_name; ?>&name=<?php echo $plugin->title; ?>" >Manage-Plan</a>
								</div>
								<div class="button-container">
									<a class="btn btn-primary no-wrap" href="/store-admin/index.php/Manage_PlanAndPlugin/addLog?plugin_name=<?php echo $plugin->plugin_name; ?>&name=<?php echo $plugin->title; ?>">Change-Log</a>
								</div>
								<!--<div class="button-container">-->
								<!--	<a class="btn btn-success">Downloads</a>-->
								<!--</div>-->
								<!--<div class="button-container">-->
								<!--	<a class="btn btn-primary no-wrap" href="/store-admin/index.php/Manage_PlanAndPlugin/addScreenshots?plugin_name=<?php echo $plugin->plugin_name; ?>&name=<?php echo $plugin->title; ?>">ScreenShots</a>-->
								<!--</div>-->
							</div>
						</td>
						</tr>
					<?php } ?>
				

				<!-- <tr>
					<td>2</td>
					<td>BpPOKE</td>
					<td>Active/Inactive</td>
					<td>
						<div class="container">
							<div class="button-container"><a class="btn btn-warning">Edit</a></div>
							<a class="btn btn-primary no-wrap" href="/store-admin/index.php/Manage_PlanAndPlugin/addToPlan?plugin_name=<?php echo 1; ?>&name=<?php echo 'BpPOKE'; ?>">Add-Plan</a>
							<div class="button-container"><a class="btn btn-primary no-wrap">Change-Log</a></div>
							<div class="button-container"><a class="btn btn-success">Downloads</a></div>
							<div class="button-container"><a class="btn btn-primary no-wrap">ScreenShots</a></div>
						</div>
					</td>
				</tr> -->


			</table>
		</div>
	</div>
</body>
</html>

