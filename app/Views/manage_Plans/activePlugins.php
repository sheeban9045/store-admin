<?php 

 
 $plugins = $this->data['plugins'];
$count=1;
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	
	<div class="card">
		<div ><h3 style="display: flex;justify-content: center;">Manage-Plans</h3></div>
		<div class="card-body">
			<table class="table table-striped text-center table-hover">
		
				<tr>
					<th>S.No</th>
					<th>Plugins</th>
					<!--<th>Actions</th>-->
				</tr>
				<?php foreach($plugins as $plugin){ ?>
				
					<tr>
						<td><?php echo $count++; ?></td>
						<td>

							<a href="/plugin_profile.php?plugin_name=<?php echo $plugin->plugin_name ?>" style="color: black;" target="_blank">
								<?php echo $plugin->plugin_title; ?>		
							</a>
						</td>
						<!--<td><a href="<?php echo get_uri('Manage_PlanAndPlugin/editPlans') ?>" class="btn btn-success">Edit</a></td>-->
					</tr>
					
				
			<?php } ?>
			</table>
		</div>
	</div>
</body>
</html>


