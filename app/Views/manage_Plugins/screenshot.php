<div class="container alert alert-primary">
	<h3> Add Screenshots </h3>
	<?php 

	echo form_open_multipart(get_uri('Manage_PlanAndPlugin/insertScreenshots'), array("id" => "profile-image-form", "class" => "general-form", "role" => "form"));
	 ?>
	    <input type="text" name="plugin_name" value="<?php echo $plugin_name; ?>" hidden>
	 	<input type="file" name="image" class="form-control">
	 	<br>
		<input type="submit" name="submit" class="btn btn-success">

	<?php echo form_close(); ?>

</div>