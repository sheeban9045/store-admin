<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title></title>
    <style>
        .badge {
            border-radius: 10%;
            padding-left: 10%;

            padding-right: 10%;
        }

        .badge-success {
            background-color: #80f09e;
        }

        .badge-danger {
            background-color: #f0405a;
        }

        .green-dot {
            width: 15px;
            height: 15px;
            background-color: #10702a;
            /* Green color */
            /* background-color: #ea0e0e; Red color */
            border-radius: 50%;
            display: inline-block;
            margin-left: 5px;
            /* Adjust the margin for spacing */
            transition: background-color 0.3s;
            /* Add a transition effect */
            position: relative;
            /* Make it relative for absolute positioning of pseudo-element */
            cursor: pointer;
            /* Change cursor to pointer on hover */
        }

        /* Hover effect for the green dot */
        .green-dot:hover {
            background-color: lightgreen;
            /* Red color when hovered */
        }

        /* Style for the text on hover */
        .green-dot:hover::before {
            content: "Active";
            /* Text to display on hover */
            position: absolute;
            top: -15px;
            /* Adjust the top position */
            left: 50%;
            transform: translateX(-50%);
            /* Center horizontally */
            background-color: lightgreen;
            /* Red background color */
            color: #fff;
            /* White text color */
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
        }


        .red-dot {
            width: 15px;
            height: 15px;
            background-color: #e01919;
            /* Green color */
            /* background-color: #ea0e0e; Red color */
            border-radius: 50%;
            display: inline-block;
            margin-left: 5px;
            /* Adjust the margin for spacing */
            transition: background-color 0.3s;
            /* Add a transition effect */
            position: relative;
            /* Make it relative for absolute positioning of pseudo-element */
            cursor: pointer;
            /* Change cursor to pointer on hover */
        }

        /* Hover effect for the green dot */
        .red-dot:hover {
            background-color: #e86e6e;
            /* Red color when hovered */
        }

        /* Style for the text on hover */
        .red-dot:hover::before {
            content: "Inactive";
            /* Text to display on hover */
            position: absolute;
            top: -15px;
            /* Adjust the top position */
            left: 50%;
            transform: translateX(-50%);
            /* Center horizontally */
            background-color: #e86e6e;
            /* Red background color */
            color: #fff;
            /* White text color */
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
        }
        .card-body {
        	min-height: 33vh;
        }        
    </style>
</head>

<body>


    <div id="page-content" class="page-wrapper clearfix">
        <?php
        if (count($dashboards) && !get_setting("disable_dashboard_customization_by_clients")) {
            echo view("dashboards/dashboard_header");
        }

        echo announcements_alert_widget();

        app_hooks()->do_action('app_hook_dashboard_announcement_extension');
        ?>
        <div class="">
            <?php echo view("clients/info_widgets/index"); ?>
        </div>

        <?php if (!in_array("projects", $hidden_menu)) { ?>
            <div class="">
                <?php echo view("clients/projects/index"); ?>
            </div>
        <?php } ?>

        <!-- Dropdown -->
        <div class="d-flex justify-content-end">
            <select id="dropdown" class=" btn btn-success">
                <option value="">Select</option>
                <?php if(isset($my_orders) && !empty($my_orders)) :?>
                <?php $cnt =1; foreach($my_orders as $order){ ?>
                    <option value="<?php echo $cnt; ?>"><?php echo $order->domain_name ?>
                    </option>
                <?php $cnt++;} ?>
            <?php endif; ?>
            </select>
        </div><br>

        <!-- Cards -->
        <?php if(isset($recent_order) && !empty($recent_order)): ?>
        <div class="row">
            <div class="col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="fs-md text-muted mb-0">Payment Status</h5>

                        <!-- text-warning text-danger -->
                        <div class="row mt-4 align-items-end">
                        <div class="col-lg-2"></div>
                            <div class="col-lg-8">
                                <?php if( isset($stripeResponse->payment_status) && $stripeResponse->payment_status == "paid"){ ?>
                                    <h6 class="alert alert-success rounded-pill text-center mx-auto" role="alert"><strong>Success!</strong></h6>
                                <?php }else{ ?>  
                                    <h6 class="alert alert-warning rounded-pill text-center mx-auto" role="alert"><strong>Pending!</strong></h6>
                                <?php } ?>      
                            </div>
                            <div class="col-lg-12">
                                <br>
                                <span>Plan Price: <strong>
                                    $<?php echo (isset($stripeResponse->amount_total) && !empty($stripeResponse->amount_total))?( $stripeResponse->amount_total )/100:0 ?>
                                </strong></span><br>

                               <span class="counter-value">Validity: <strong>
                                   <?php 
                                        $originalDate = (isset($recent_order) && !empty($recent_order))? $recent_order[0]->order_date:'';

                                        // Convert the original date to a Unix timestamp
                                        $timestamp = strtotime($originalDate);

                                        // Calculate the date one month in advance
                                        $oneMonthLater = date("Y-m-d", strtotime("+1 month", $timestamp));
                                        echo explode(' ', $originalDate)[0]." To ".$oneMonthLater;
                                   ?>
                               </strong></span>
                                <p class="text-warning mb-0"><i class="bi bi-arrow-up me-1">Due Date: </i><?php echo $oneMonthLater?></p>
                                <span>You have to pay 
                                    $<?php echo (isset($stripeResponse->amount_total) && !empty($stripeResponse->amount_total))?( $stripeResponse->amount_total )/100:0; ?>        
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!--end col-->
            <div class="col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body">

                        <h5 class="fs-md text-muted mb-0">Current Plan</h5>

                        <div class="row mt-4 align-items-end">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-8">
                                <h6 class="alert alert-info rounded-pill text-center mx-auto" role="alert"><strong><?php echo $plan_detail->title; ?></strong></h6>
                            </div>
                            <div class="col-lg-6">
                                <br>
                                <span> Plan Price: <strong>
                                    $<?php  echo (isset($stripeResponse->amount_total) && !empty($stripeResponse->amount_total))?( $stripeResponse->amount_total )/100:0; ?>
                                </strong></span>
                                <br><br>
                                <a href="
                                    <?php
                                        if (!$_SERVER['HTTP_HOST'] !== 'localhost') {
                                            // $baseURL = 'http://www.webhut.net';
                                            $baseURL = 'http://webhut.net';
                                            
                                        } else {
                                             $baseURL = 'http://localhost/matt-subscription';
                                        }
                                        echo $baseURL."/pricing.php";
                                    ?>" 
                                     class="btn btn-primary" style="margin-bottom: 5%;">Change Plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="fs-md text-muted mb-0">Communities</h5>

                        <div class="row mt-4 align-items-end">

                            <div class="col-lg-12">

                                <div id="info-div">
                                    <?php if(isset($my_orders) && !empty($my_orders)) :?>
                                    <?php $cnt =1; foreach($my_orders as $order){ ?>
                                         <div class="community_block" id="community<?php echo $cnt; ?>" style="display: <?php echo ($cnt==1?"block":"none"); ?>;">
                                        <div>
                                            <h6 class="alert alert-info rounded-pill text-center mx-auto" role="alert">
                                                <strong><a href=""><?php echo $order->domain_name ?></a></strong>
                                                <span class="<?php echo ($order->is_community_set !=0 ?"green":"red")?>-dot float-end"></span>
                                            </h6>

                                        </div>
                                        <?php if(!empty($order->is_community_set)): ?>
                                        <div style="margin-bottom: 3%;">
                                            <br><br><br>
                                            <a href="<?php echo "http://".$order->domain_name."/index.php/login/user_email/".$user_email."/autologin/1";?>" target = "_blank" class="btn btn-primary ml-2">Community</a>
                                            <!--<a href="<?php echo $baseURL."/".$order->domain_name."/login/community_success/1"?>" target = "_blank" class="btn btn-primary ml-2">Community</a>-->

                                            <a href="<?php echo "http://".$order->domain_name."/index.php/admin"?>" target = "_blank" class="btn btn-primary ml-2">Admin</a>
                                            
                                            <a href="<?php echo $baseURL."/webhut-parent-community/index.php/store/community-plugins-store/user_email/".$user_email;?>" target = "_blank" class="btn btn-primary ml-2">Manage Plugins</a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php $cnt++; } ?>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end col-->
        </div>
        <?php endif; ?>

        <!-- Table  -->
        <div class="card" style = "display:">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Monthly Payments Plan</h4>
                <span>List of the monthly subscripton plans.</span>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card mt-0">
                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0 table-striped">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col" class="sort cursor-pointer" data-sort="courses_Name">Invoice ID</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="category">Subscription ID</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="instructor">Name</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="lessons">Community</th>
                                <!-- <th scope="col" class="sort cursor-pointer" data-sort="duration">Plan Name</th> -->
                                <th scope="col" class="sort cursor-pointer" data-sort="fees">Invoice Created</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="fees">Amount</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="status">Status</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php if(isset($recent_order) && !empty($recent_order)) :?>
                             <?php //foreach(st as $order){ 
                                 foreach($recent_order as $order){
                                    if(strstr($order->stripe_response,"StripeCheckoutSession JSON:")){
                                        $order->stripe_response = str_replace("StripeCheckoutSession JSON:","",$order->stripe_response);
                                    }
                                    $stripe_response = json_decode($order->stripe_response);
                            ?>
                                <tr>
                                    <td>
                                        <?php echo isset($stripe_response->invoice) ?  $stripe_response->invoice:""; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($stripe_response->subscription) ?  $stripe_response->subscription:""; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($stripe_response->customer_details->name) ?  $stripe_response->customer_details->name:""; ?>
                                    </td>
                                    <td class="lessons">
                                        <?php echo isset($order->domain_name) ?  $order->domain_name:""; ?>
                                    </td>
                                    <!-- <td>
                                        Moo Social
                                    </td> -->
                                    <td>
                                        <span class="fw-medium fees">
                                            <?php 
                                            if(isset($order->order_date) && !empty($order->order_date)){
                                            echo explode(' ', $order->order_date)[0];
                                           } ?>
                                                
                                            </span>
                                    </td>
                                    <td>
                                        <?php 
                                            if(isset($stripe_response->amount_total) && !empty($stripe_response->amount_total)){
                                            echo "$".$stripe_response->amount_total/100;
                                           } ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if(isset($stripe_response->payment_status) && !empty($stripe_response->payment_status)){
                                                ?>
                                                <span class="badge badge-success">Complete</span>
                                            
                                           <?php }else{ ?>
                                                <span class="badge badge-danger">Pending</span>
                                           <?php }?>
                                        
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php endif; ?>
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
                <!-- Pagination -->
                <!-- <div class="align-items-center mt-4 pt-2 row">
                    <div class="col-sm">
                        <div class="text-muted text-center text-sm-start">
                            Showing <span class="fw-semibold">5</span> of <span class="fw-semibold">6</span> Results
                        </div>
                    </div>
                    <div class="col-sm-auto mt-3 mt-sm-0">
                        <div class="pagination-wrap hstack gap-2 justify-content-center">
                            <a class="page-item pagination-prev disabled" href="javascript:void(0)">
                                Previous
                            </a>
                            <ul class="pagination listjs-pagination mb-0">
                                <li class="active"><a class="page" href="#" data-i="1" data-page="5">1</a></li>
                                <li><a class="page" href="#" data-i="2" data-page="5">2</a></li>
                            </ul>
                            <a class="page-item pagination-next" href="javascript:void(0)">
                                Next
                            </a>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- Table Ends Here -->
    </div>

    <script>
        // Get the dropdown element
        const dropdown = document.getElementById('dropdown');

        // Get the info div
        const infoDiv = document.getElementById('info-div');

        // Add an event listener to the dropdown
        dropdown.addEventListener('change', function() {
            // Get the selected option value
            const selectedOption = dropdown.value;

            var variableToMatch = 'community'+selectedOption; 

            // Get all elements with the class "community_block"
            var communityBlocks = document.querySelectorAll('.community_block');

            // Loop through each community_block element
            for (var i = 0; i < communityBlocks.length; i++) {
                var communityBlock = communityBlocks[i];
                var id = communityBlock.getAttribute('id');
                
                // Check if the id matches the variableToMatch
                if (id === variableToMatch) {
                    document.getElementById(id).style.display = 'block';
                }else{
                    document.getElementById(id).style.display = 'none';
                }
            }
        });
    </script>

</body>

</html>