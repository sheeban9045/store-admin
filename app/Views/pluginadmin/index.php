
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
    $plugin_data = $this->data['pluginlist'];
     ?>
    <div id="page-content" class="page-wrapper clearfix">
        <div class="card clearfix">
    
    <div id="page-content" class="page-wrapper clearfix">
    
        
        <div class="card">
    <center><h3>Plugins List According to Subscription Plans</h3></center><br>
    <div class="card">
    <table class="table table-striped table-hover">
        <h5>Subscription Plan - Moo Social </h5>
        <br>
      <tr>
       
        <th>Plugins list</th>
         <th>Status</th>
          <th>Change log</th>
          <th>Edit-Title</th>
      </tr>
    
      <?php $i=1; foreach ($plugin_data as  $value) {
        // echo $value->plugin_name;
        if ($value->plan_id == 1) {
       
        ?>
      <tr>
       
        <td><?php echo $value->plugin_name; ?>
            <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
        </td>
       <td>  
        <?php 
            if ( empty($value->status) )
            {
                echo '<img src="\store-admin\assets\images\enabled0.png">';
            }
            else
            {
                echo '<img src="\store-admin\assets\images\enabled1.png">';
            } ?>
           
        </td>
        <td>
            <a  href="/store-admin/index.php/pluginadmin?<?php echo !empty($value->status)?"status=0&id=".$value->plugin_id:"status=1&id=".$value->plugin_id ?>"
             class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>">
                <?php echo !empty($value->status)? "Disable": "Enable"; ?>
            </a>
    
        </td>
        <td>
            <button class="btn" onclick="changePluginName('<?php echo $value->plugin_id ?>')">
                <i class="fa fa-edit"></i>
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
        
        <th>Plugins list</th>
        <th>Status</th>
         <th>Change log</th>
         <th>Edit-Title</th>
      </tr>
    
      <?php $i=1; foreach ($plugin_data as  $value) {
        // echo $value->plugin_name;
        if ($value->plan_id == 2) {
       
        ?>
      <tr>
      
        <td><?php echo $value->plugin_name; ?>
            <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
        </td>
        <td>     
        <?php 
            if ( empty($value->status) )
            {
                echo '<img src="\store-admin\assets\images\enabled0.png">';
            }
            else
            {
                echo '<img src="\store-admin\assets\images\enabled1.png">';
            } ?>
           
        </td>
        <td>
            <a  href="/store-admin/index.php/pluginadmin?<?php echo !empty($value->status)?"status=0&id=".$value->plugin_id:"status=1&id=".$value->plugin_id ?>"
             class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>">
                <?php echo !empty($value->status)? "Disable": "Enable"; ?>
            </a>
    
        </td>
        <td>
            <button class="btn" onclick="changePluginName('<?php echo $value->plugin_id ?>')">
                <i class="fa fa-edit"></i>
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
        <h5>Subscription Plan - Deluxe </h5>
        <br>
      <tr>
       
        <th>Plugins list</th>
         <th>Status</th>
          <th>Change log</th>
          <th>Edit-Title</th>
      </tr>
    
      <?php $i=1; foreach ($plugin_data as  $value) {
        // echo $value->plugin_name;
        if ($value->plan_id == 3) {
       
        ?>
      <tr>
      
        <td><?php echo $value->plugin_name; ?>
        <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
        </td>
        <td>
        <?php 
            if ( empty($value->status) )
            {
                echo '<img src="\store-admin\assets\images\enabled0.png">';
            }
            else
            {
                echo '<img src="\store-admin\assets\images\enabled1.png">';
            } ?>
           
        </td>
        <td>
            <a  href="/store-admin/index.php/pluginadmin?<?php echo !empty($value->status)?"status=0&id=".$value->plugin_id:"status=1&id=".$value->plugin_id ?>"
             class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>">
                <?php echo !empty($value->status)? "Disable": "Enable"; ?>
            </a>
    
        </td>
        <td>
            <button class="btn" onclick="changePluginName('<?php echo $value->plugin_id ?>')">
                <i class="fa fa-edit"></i>
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
        <h5>Subscription Plan - Ultimate</h5>
        <br>
      <tr>
        
        <th>Plugins list</th>
        <th>Status</th>
        <th>Change log</th>
        <th>Edit-Title</th>
      </tr>
    
      <?php $i=1; foreach ($plugin_data as  $value) {
        // echo $value->plugin_name;
        if ($value->plan_id == 4) {
       
        ?>
      <tr>
       
        <td><?php echo $value->plugin_name; ?>
        <button class="btn"><i class="fa fa-info-circle tooltip<?php echo $i;?>" title="<?php  echo $value->plugin_name;?>"></i></button>
        </td>
       <td>
        <?php 
            if ( empty($value->status) )
            {
                echo '<img src="\store-admin\assets\images\enabled0.png">';
            }
            else
            {
                echo '<img src="\store-admin\assets\images\enabled1.png">';
            } ?>
           
        </td>
        <td>
            <a  href="/store-admin/index.php/pluginadmin?<?php echo !empty($value->status)?"status=0&id=".$value->plugin_id:"status=1&id=".$value->plugin_id ?>"
             class="btn btn-<?php echo !empty($value->status)? "danger": "success";  ?>">
                <?php echo !empty($value->status)? "Disable": "Enable"; ?>
            </a>
    
        </td>
        <td>
            <button class="btn" onclick="changePluginName('<?php echo $value->plugin_id ?>')">
                <i class="fa fa-edit"></i>
            </button>
        </td
        <?php $i++;  ?>  
      </tr>
      <?php } } ?>
    
    </table>
    
    
        </div>
    
    
        
       
    <!-- </form> -->
    
    
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
    
    
    
    
    
            <!-- <ul id="order-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang('orders'); ?></h4></li>
                <li><a id="monthly-order-button"  role="presentation" href="javascript:;" data-bs-target="#monthly-orders" onclick="changeDateRange('monthly')"><?php echo app_lang("monthly"); ?></a></li>
                <li><a role="presentation" href="<?php echo_uri("orders/yearly/"); ?>" data-bs-target="#yearly-orders" onclick="changeDateRange('yearly')"><?php echo app_lang('yearly'); ?></a></li>
                  <li><a role="presentation" href="<?php echo_uri("orders/production/"); ?>" data-bs-target="#production-orders" onclick="changeDateRange('production')"><?php echo app_lang('production'); ?></a></li>
    
    
    
    
                <div class="tab-title clearfix no-border">
    
                    <div class="title-button-group">
                        <?php echo js_anchor("<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_order'), array("class" => "btn btn-default", "id" => "add-order-btn")); ?>           
                    </div>  
    
    
                   
    
                    <?php if( !empty($order_statuses) ): ?>
                        <div class="title-button-group">
                            <?php echo js_anchor( app_lang('apply'), array("class" => "btn btn-default", "id" => "add-order-btn", "onclick" => "changeOredrStatus()")); ?>   
                                    
                        </div>
                        
    
                          <input type="checkbox" onclick='mycheck(this)' value="Select All" class="btn btn-default", id = "add-order-btn" />  
                          <div class="btn btn-default">
                            
                             <select id="order_status" style="width: 125px; border: none;">
                                <option value=""><?php echo app_lang('bulk_action'); ?></option>
                                <?php  foreach($order_statuses as $order_status): ?>
                                        <option value="<?php echo $order_status->id; ?>"><?php echo $order_status->title; ?></option>
                                <?php endforeach; ?>
                              </select>
    
                      
              
                              
    
                        </div>  
                    <?php endif; ?> 
    
            </ul>
     -->
    
            <!-- <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade" id="monthly-orders">
                    
                    <div class="table-responsive">
                        <table id="monthly-order-table" class="display" cellspacing="0" width="100%"> 
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="yearly-orders"></div>
                <div role="tabpanel" class="tab-pane fade" id="production-orders"></div>
            </div>   -->      
        </div>
    </div>
</body>


<script type="text/javascript">
    
   async function changePluginName(pluginId){
        //alert(pluginId);

        const { value: PluginName } = await Swal.fire({
        title: 'Change Plugin Title !',
        input: 'text',
        inputPlaceholder: 'Enter new title ?',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirm'
        })
        if(PluginName){
            var newname = PluginName;
           $.ajax({
                type: 'GET',
                url: '/store-admin/index.php/pluginadmin/changePluginName',
                data: {
                    id: pluginId,
                    name:newname 
                },
                success: function() {
                    Swal.fire({
                      title: 'Plugin title changed successfully !',
                      type : 'success'
                    }).then((result) => {
                      // Reload the Page
                      location.reload();
                    });
                }
            });
        };

    }

</script>



<!-- <script type="text/javascript">
    var default_date_range = 'monthly';
  = $("#order_status option:selected").val();

        $('input:checkbox[name=' + default_date_range + '_checkbox_order_ids]').each(function() 
        {
            if($(this).is(':checked')) {

                var order_id = $(this).val();
                if( order_id && current_order_status ) {
                    should_refresh = 1;

                    $.ajax({
                   
                        dataType: 'json',
                        data: {value: current_order_status},
                        success: function (result) {
                            
                        }
                    });
                }
            }
        });


        if( should_refresh == 1 )
            window.location.reload();
    }


    function changeDateRange(value) {
        default_date_range = value;
    }

        function mycheck(main)
        { 
            all = document.getElementsByName(default_date_range + '_checkbox_order_ids');  
            for(var i=0; i<all.length; i++){

                all[i].checked = main.checked;
            }
            
        }

</script> -->
<!-- <script>
    function changeImage(){
   var image = document.getElementById('myImage');
   if (image.src.match("s"))
   {image.src ="enabled0.png";}
   else
    {image.src ="enabled1.png";}  
}
</script> -->



