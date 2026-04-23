
<style type="text/css">
	.pagewrap {max-width: 30%; height: 30vh; margin: 0 auto; }

</style>
<div class="container">
<form id="confirmationForm">
<div class="row">
	<div class="col-md-12">
    	<h4 class="alert alert-primary">Are you sure you want to <?php echo ($view!=0 ? "view" : "setup");  ?> the community?</h4>
	</div>
    <div class="col-md-12">
        <?php 
            if($view == 0){
                // $href = "orders/setup_community_post?order_id=".$order_id; 
                $href = "http://webhut.net/store-admin/index.php/orders/setup_community_post?order_id=".$order_id;
            }else{
                if(isset($user_email) && !empty($user_email)) {
                    $href = "http://".$domain."/index.php/login/user_email/".$user_email."/autologin/1";
                }else {
                    $href = "http://".$domain."/index.php/";
                }
            }
            
        ?>
    	<a href=<?php echo $href; ?> target="_blank" class="btn btn-info" id="yesButton">Yes</a>
    	<button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close" >Cancel</button>
    </div>
</div>
</form>
</div>
<br><br>
<script>
$(document).ready(function() {
    $("#yesButton").click(function() {

    	console.log('<?php echo_uri("orders/setup_community_post") ?>');
        // Perform AJAX action here
        $.ajax({
            type: "POST",
            url: '<?php echo_uri("orders/setup_community_post") ?>', // Change this to your actual AJAX endpoint
            data: { called: 1 }, // Customize data as needed
            success: function(response) {
                // Handle successful response
                console.log("Community set up successfully!");
            },
            error: function(error) {
                // Handle error
                console.error("Error setting up community:", error);
            }           
        });
    });

    $("#cancelButton").click(function() {
        // Handle cancellation if needed
        console.log("Operation cancelled.");
    });
});
</script>

