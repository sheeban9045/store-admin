<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget();?>
    <div class="card clearfix">
        <ul id="order-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
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


        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="monthly-orders">
                
                <div class="table-responsive">
                    
                    <table id="monthly-order-table" class="display" cellspacing="0" width="100%">   
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-orders"></div>
            <div role="tabpanel" class="tab-pane fade" id="production-orders"></div>
        </div>        
    </div>
</div>

<script type="text/javascript">
    var default_date_range = 'monthly';
    loadOrdersTable = function (selector, dateRange) {
        console.log("Harsh-2");
        if(dateRange)
            default_date_range = dateRange;
        else
            default_date_range = 'monthly';

        $(selector).appTable({
            source: '<?php echo_uri("orders/list_data") ?>?datarange=' + dateRange,
            order: [[0, "desc"]],
            dateRangeType: dateRange,
            filterDropdown: [{name: "status_id", class: "w150", options: <?php echo view("orders/order_statuses_dropdown"); ?>}, <?php echo $custom_field_filters; ?>],
            columns: [
              
                {title: "<?php echo app_lang("change_status") ?> ", name: "change_status", value: "change_status", isChecked: true} ,
                
            
                {title: "<?php echo app_lang("order") ?> ", "class": "w15p"},
                {title: "<?php echo app_lang("client") ?>"},
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("order_date") ?>", "iDataSort": 2, "class": "w20p"},
                 {title: "<?php echo app_lang("start_date") ?>", "iDataSort": 2, "class": "w20p"},
                        {title: "<?php echo app_lang("end_date") ?>", "iDataSort": 2, "class": "w20p"},
                  {title: "<?php echo app_lang("domain_name") ?>", "iDataSort": 2, "class": "w20p"},
                    
                  // {title: "<?php echo app_lang("limit") ?>", "iDataSort": 2, "class": "w20p"},
                 // {title: "<?php echo app_lang("quantity_product") ?>", "class": "text-right "},
                {title: "<?php echo app_lang("amount") ?>", "class": "text-right w20p"},
                {title: "<?php echo app_lang("status") ?>", "class": "text-center"}
                <?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center w100"},
                {title: 'Community', "class": "text-center  w100"},
                {title: 'Renew', "class": "text-center w100"},
                {title: 'View Invoices', "class": "text-center  w100"}

            ],

            printColumns: combineCustomFieldsColumns([0, 1, 3, 4, 5,6], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>'),
        //  summation: [ {column: 6, dataType: 'number'},{column: 7, dataType: 'currency', currencySymbol: AppHelper.settings.currencySymbol}]
        });
    };


    $(document).ready(function () {
        console.log("Harsh-1");
        loadOrdersTable("#monthly-order-table", "monthly");

        $("#add-order-btn").click(function () {
            window.location.href = "<?php echo get_uri("items/grid_view"); ?>";
        });
    });

</script>



<?php echo view("orders/update_order_status_script"); ?>

<script>
    function changeOredrStatus() {
        var should_refresh = 0;
        var current_order_status = $("#order_status option:selected").val();

        $('input:checkbox[name=' + default_date_range + '_checkbox_order_ids]').each(function() 
        {
            if($(this).is(':checked')) {

                var order_id = $(this).val();
                if( order_id && current_order_status ) {
                    should_refresh = 1;

                    $.ajax({
                        url: '<?php echo_uri("orders/save_order_status") ?>/' + order_id,
                        type: 'POST',
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


</script>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("<?php echo $payment_setting->publishable_key; ?>");

    $(document).on('click', '[data-act="renew-order"]', function () {
        var order_id = $(this).attr('data-id');

        if (!confirm("Are you sure you want to renew this plan?")) {
            return;
        }

        appLoader.show();
        $.ajax({
            url: '<?php echo_uri("orders/renew_checkout") ?>',
            type: 'POST',
            dataType: 'json',
            data: { order_id: order_id },
            success: function (response) {
                appLoader.hide();
                if (response.success) {
                    response.products.forEach(function (product) {
                        if (product.stripe_price_id) {
                            stripe.redirectToCheckout({
                                lineItems: [{ price: product.stripe_price_id, quantity: 1 }],
                                mode: 'payment',
                                successUrl: 'http://webhut.net/store-admin/index.php/orders/renew_success_page?order_id=' + response.order_id + '&session_id={CHECKOUT_SESSION_ID}',
                                cancelUrl: 'http://webhut.net/store-admin/index.php/orders/error_page?order_id=' + response.order_id + '&session_id={CHECKOUT_SESSION_ID}',
                            });
                        }
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                appLoader.hide();
                toastr.error("Something went wrong!");
            }
        });
    });
</script>




