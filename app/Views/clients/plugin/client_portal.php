
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- swett alert cdn -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.0.min.js"></script>
    <script type="text/javascript" src="js/jquery.tooltipster.min.js"></script>
    

    <script>
        $(document).ready(function() {
            $('.tooltip').tooltipster({
                // theme: 'tooltipster-noir',
                animation: 'fade',
                   delay: 5,
                   theme: 'tooltipster-shadow',
                   touchDevices: false,
                   trigger: 'hover'
            });
        });
    </script>

</head>



<body>

<?php   
    $cr_Plan=1;
?>
    <?php 
    $plugin_data = $this->data['pluginlist'];
     ?>
    <div id="page-content" class="page-wrapper clearfix">
        <div class="card clearfix">

    <div id="page-content" class="page-wrapper clearfix">

        
        <div class="card">
    <center><h3><b>Plugins List According to Subscription Plans</b></h3></center><br>
    <div class="card">
        <table class="table table-striped table-hover">
            <h5>Subscription Plan - Moo Social </h5>
            <br>
          <tr>
            <th  style="text-align: center;">Sr no</th>
            <th >Plugins list</th>
            <th  style="text-align: center;">Status</th>
            <th  style="text-align: center;">Action</th>
          </tr>

          <?php $i=1;  foreach ($plugin_data as  $value) { ?>
           <!--  <style type="text/css">
                .dialo<?php echo $i?>{
                    display: none;
                }
                
                .Pname<?php echo $i?>:hover + .dialo<?php echo $i?> {
                    /*background-color: red;*/
                    display: block;
                    position: relative;
                    background: yellow;
                    width: 320px;
                    top: <?php echo 20+$i*30?>px;
                    left: 266px;
                }
            </style> -->
            <?php  if ($value->plan_id==1) { ?>
              <tr >
                <td style="text-align: center;">
                    <?php echo $i; ?>
                </td>
                <td >
                    <span><?php echo $value->plugin_name; ?></span>
                    <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
                </td>

                <!-- Current Status -->
                    <td  style="text-align: center;">
                       <?php if ( empty($value->status) )  {
                       echo '<img src="\store-admin\assets\images\enabled0.png">';
                        }

                        else{
                            echo '<img src="\store-admin\assets\images\enabled1.png">';
                        } ?>
                    </td>
                <!-- Change Status -->
                    <td  style="text-align: center;">
                        
                        <button  class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>" onclick="changeStatus('<?php echo $value->status ?>','<?php echo $value->plugin_id ?>')" >
                        <?php echo !empty($value->status)? "Disable": "Enable"; ?>
                        </button>

                    </td>

                <?php $i++;  ?>  
                  </tr>
            <?php } } ?>
        </table>
    </div>

    <br>
    
    <div class="card">
        <table class="table table-striped table-hover">
            <h5>Subscription Plan - pro </h5>
            <br>
            <tr>
            <th  style="text-align: center;">Sr no</th>
            <th>Plugins list</th>   
            <th  style="text-align: center;">Status</th>
            <th  style="text-align: center;">Action</th>
          </tr>

          <?php $i=1;  foreach ($plugin_data as  $value) {

         if ($value->plan_id==2) { ?>
          <tr>
            <td  style="text-align: center;"><?php echo $i; ?></td>
            <td >
                <span><?php echo $value->plugin_name; ?></span>
                <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
            </td>
            <td  style="text-align: center;">
                <?php if ( empty($value->status) )  {
                   echo '<img src="\store-admin\assets\images\enabled0.png">';
                    }

                    else{
                        echo '<img src="\store-admin\assets\images\enabled1.png">';
                    } ?>
            </td>
            <td  style="text-align: center;">

                <!-- <a href="/matt-subscription/store-admin/index.php/plugin/changeStatus?status=<?php echo $value->status; ?>&id=<?php echo $value->plugin_id ?>" class="btn btn-info">
                    <?php echo !empty($value->status)? "Disable": "Enable"; ?>
                </a> -->

                 <button  class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>" onclick="changeStatus('<?php echo $value->status ?>','<?php echo $value->plugin_id ?>')" >
                    <?php echo !empty($value->status)? "Disable": "Enable"; ?></button>
            </td>
            <?php $i++;  ?>  
          </tr>
          <?php } } ?>

        </table>
    </div>


    <br>
    
    <div class="card">
        <table class="table table-striped table-hover">
            <h5>Subscription Plan - Deluxe </h5>
            <br>
            <tr>
            <th  style="text-align: center;">Sr no</th>
            <th>Plugins list</th>   
            <th  style="text-align: center;">Status</th>
            <th  style="text-align: center;">Action</th>
          </tr>

          <?php $i=1;  foreach ($plugin_data as  $value) {

         if ($value->plan_id==3) { ?>
          <tr>
            <td  style="text-align: center;"><?php echo $i; ?></td>
            <td >
                <span><?php echo $value->plugin_name; ?></span>
                <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
            </td>
            <td  style="text-align: center;">
               <?php if ( empty($value->status) )  {
               echo '<img src="\store-admin\assets\images\enabled0.png">';
                }

                else{
                    echo '<img src="\store-admin\assets\images\enabled1.png">';
                } ?>
            </td>
             <td  style="text-align: center;">
                <!--< a href="/matt-subscription/store-admin/index.php/plugin/changeStatus?status=<?php 
                    echo $value->status; ?>&id=<?php echo $value->plugin_id ?>" class="btn btn-info">
                <?php echo !empty($value->status)? "Disable": "Enable"; ?>
                </a> -->
                 <button  class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>" onclick="changeStatus('<?php echo $value->status ?>','<?php echo $value->plugin_id ?>')" >
                    <?php echo !empty($value->status)? "Disable": "Enable"; ?></button>
             </td>
            <?php $i++;  ?>  
          </tr>
          <?php } } ?>

        </table>
     </div>

    <br>
    <div class="card">
        <table class="table table-striped table-hover">
            <h5>Subscription Plan - Ultimate</h5>
            <br>
            <tr>
            <th style="text-align: center;">Sr no</th>
            <th>Plugins list</th>  
            <th  style="text-align: center;">Status</th> 
            <th  style="text-align: center;">Action</th>
          </tr>

          <?php $i=1;  foreach ($plugin_data as  $value) {

         if ($value->plan_id == 4) { ?>
          <tr>
            <td  style="text-align: center;"><?php echo $i; ?></td>
            <td >
                <span><?php echo $value->plugin_name; ?></span>
                <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
            </td>
            <td  style="text-align: center;">
               <?php if ( empty($value->status) )  {
               echo '<img src="\store-admin\assets\images\enabled0.png">';
                }

                else{
                    echo '<img src="\store-admin\assets\images\enabled1.png">';
                } ?>
            </td>
             <td  style="text-align: center;">
                <!-- <a href="/matt-subscription/store-admin/index.php/plugin/changeStatus?status=
                <?php 
                echo $value->status; ?>&id=<?php echo $value->plugin_id ?>" class="btn btn-info">
                <?php echo !empty($value->status)? "Disable": "Enable"; ?>
                </a> -->

                 <button  class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>" onclick="changeStatus('<?php echo $value->status ?>','<?php echo $value->plugin_id ?>')" >
                    <?php echo !empty($value->status)? "Disable": "Enable"; ?></button>
            </td>
            <?php $i++;  ?>  
          </tr>
          <?php } } ?>

        </table>
    </div>


            <div class="page-title clearfix">
                <h1> <?php echo app_lang(''); ?></h1>
                <div class="title-button-group">
                    <!-- <?php echo anchor(get_uri("items/grid_view"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_order'), array("class" => "btn btn-default", "id" => "add-order-btn")); ?>  -->
                </div>
            </div>
            <div class="table-responsive">
                <table id="order-table" class="display" cellspacing="0" width="100%">            
                </table>
            </div>
        </div>
    </div>

</body>
</html>



<!-- <script type="text/javascript">
    $(document).ready(function () {
        $("#order-table").appTable({
            source: '<?php echo_uri("plugin/order_list_data_of_client/" . $client_id) ?>',
            order: [[0, "desc"]],
            filterDropdown: [<?php echo $custom_field_filters; ?>],
            columns: [
                {title: "<?php echo ("checkboxes") ?>", "class": "w20p"},
                
             {title: "<?php echo ("plugins list") ?>", "iDataSort": 2, "class": "w20p"},
                {title: "<?php echo ("subscription plan") ?>", "iDataSort": 2, "class": "w20p"},
           
            ],
            summation: []
        });
    });
</script> -->

<script>
    function changeStatus(stauts,id){
        Swal.fire({
        title: 'Change-Status',
        text: "Do you really want to change the status ?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed){
               $.ajax({
                    type: 'GET',
                    url: '/store-admin/index.php/plugin/changeStatus',
                    data: {
                        status: stauts,
                        id: id
                    },
                    success: function() {
                        location.reload();
                    }
                });
            }
        });
    }

</script>
        