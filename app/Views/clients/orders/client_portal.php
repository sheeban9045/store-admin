<link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.css"); ?>" />
<script src = "<?php echo base_url("assets/js/toastr/toastr.js"); ?>"></script>
<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget();?>

    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('orders'); ?></h1>
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


<script type="text/javascript">
    $(document).ready(function () {
        $("#order-table").appTable({
            source: '<?php echo_uri("orders/order_list_data_of_client/" . $client_id) ?>',
            order: [[0, "desc"]],
            filterDropdown: [<?php echo $custom_field_filters; ?>],
            columns: [
//                 {title: "<?php echo app_lang("order") ?>", "class": "w20p"},
//                 {visible: false, searchable: false},
//                 {visible: false, searchable: false},
//                 {title: "<?php echo app_lang("order_date") ?>", "iDataSort": 2, "class": "w20p"},
//                 // {title: "<?php echo app_lang("Date") ?>", "iDataSort": 2, "class": "w20p"},
//                 {title: "<?php echo app_lang("domain_name") ?>", "class": "text-right w20p"},
//                 {title: "<?php echo app_lang("domain_status") ?>", "class": "text-center w20p"}
// <?php echo $custom_field_headers; ?>,
//                 {visible: false}
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
                // {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"},
                {title: 'Community', "class": "text-center  w100"},
                {title: 'Plan Status', "class": "text-center  w100"},
                {title: 'Renew', "class": "text-center w100"},
                {title: 'View Invoices', "class": "text-center  w100"}

            ],
            summation: [{column: 4, dataType: 'currency'}]
        });

        $(document).on('click', '[data-act="send-self-community-email"]', function () {
            var order_id = $(this).attr('data-id');

            if (!confirm("Are you sure you want to send the self-community setup request email?")) {
                return;
            }

            appLoader.show();
            $.ajax({
                url: '<?php echo_uri("orders/send_self_community_email") ?>',
                type: 'POST',
                dataType: 'json',
                data: { order_id: order_id },
                success: function (result) {
                    appLoader.hide();
                    if (result.success) {
                        toastr.success(result.message);
                    } else {
                        toastr.error(result.message);
                    }
                },
                error: function () {
                    appLoader.hide();
                    toastr.error("Something went wrong!");
                }
            });
        });
    });
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
                                mode: 'subscription',
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