<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
<?php 
	$headings = $this->data['headings'];
	$options = $this->data['options'];
	$plans = $this->data['plans'];
	// echo "<pre>";print_r($headings);
	//echo "<pre>";print_r($options);die;
	
	//echo print_r($options[1][0]->title);die;
 ?>
<div>
<?php for($i=1;$i<=5;$i++){ ?>
		<div class="card card-body">
			<?php
				echo form_open_multipart(get_uri('Manage_PlanAndPlugin/editPlansPost'), array("id" => "profile-image-form", "class" => "general-form", "role" => "form"));
	 		?>
				<div class="row">
				<div class="col-md-3"></div>	
				<div class="col-md-6">
					<label><b>Heading <?php  echo $i; ?></b></label>
					<input type="text" name="heading" class="form-control" value="<?php if(array_key_exists($i,$headings)) echo $headings[$i]->title ?>">
					<input type="text" name="heading_id" value="<?php echo $i; ?>" hidden>
				</div>
				
				<?php for($j=1;$j<=10;$j++){ ?>
					<div class="col-md-6 mt-1">
						<label><b>Option <?php echo $j; ?></b></label>
						<?php 
						$option_title = "";
						//Finding out Title of options 
							if(  array_key_exists($i,$options) && array_key_exists($j-1,$options[$i]))
								$option_title = $options[$i][$j-1]->title;
						?>
						<input type="text" name="option_<?php echo $j  ?>" class="form-control" value="<?php  echo $option_title;?>" >
						<br>
						<div class="d-inline">
							<?php for($cb=1;$cb<=4;$cb++){
							    $checked = "";
								if(  array_key_exists($i,$options) && array_key_exists($j-1,$options[$i])){
									if($cb == 1){
										$val = $options[$i][$j-1]->plan_1;
									}else if($cb == 2){
										$val = $options[$i][$j-1]->plan_2;
									}else if($cb == 3){
										$val = $options[$i][$j-1]->plan_3;
									}else if($cb == 4){
										$val = $options[$i][$j-1]->plan_4;
									}
									$checked = $val ==1? "checked" : "";
								}
							 ?>

								<input type="checkbox" name="checkbox<?php echo $j;
								  ?>[]" class="design" value="<?php echo $cb; ?>" <?php echo $checked; ?> >
								<label><i><?php echo $plans[$cb-1]->name; ?></i></label>
							<?php } ?>
						</div>
					</div>
				<?php } ?>	
				</div>
				<div>
					<input type="submit" value="Submit" class="btn btn-success">
				</div>
			<?php echo form_close(); ?>
		</div>
<?php } ?>	
</div>

