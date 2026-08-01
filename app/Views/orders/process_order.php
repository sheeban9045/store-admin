<link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.css"); ?>" />
 <script src = "<?php echo base_url("assets/js/toastr/toastr.js"); ?>"></script>
<div id="page-content" class="page-wrapper clearfix">
    <div class="process-order-preview">
        <div class="card">
            <?php echo form_open(get_uri("orders/place_order"), array("id" => "place-order-form", "class" => "general-form", "role" => "form")); ?>
            <div class="page-title clearfix">
                <h1> <?php echo app_lang('process_order'); ?></h1>
            </div>
            <div class="p20">
                <div class="mb20 ml15 mr15"><?php echo app_lang("process_order_info_message"); ?></div>
                <div class="m15 pb15 mb30">
                    <div class="table-responsive">
                        <table id="order-item-table" class="display mt0" width="100%">
                        </table>
                    </div>
                    <div class="clearfix row">
                        <div class="col-sm-8">
                        </div>
                        <div class="float-end" id="order-total-section">
                            <?php echo view("orders/processing_order_total_section"); ?>
                        </div>
                    </div>
                </div>
                <div class="pl15 pr15">
                    <?php if ($login_user->user_type === "staff" && count($companies_dropdown) > 1) { ?>
                        <div class="form-group mt15 clearfix">
                            <div class="row">
                                <label for="company_id" class=" col-md-3"><?php echo app_lang('company'); ?></label>
                                <div class="col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "company_id",
                                        "name" => "company_id",
                                        "value" => get_default_company_id(),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('company')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (isset($clients_dropdown) && $clients_dropdown) { ?>
                        <div class="form-group mt15 clearfix">
                            <div class="row">
                                <label for="client_id" class=" col-md-3"><?php echo app_lang('client'); ?></label>
                                <div class="col-md-9">
                                    <?php
                                    echo form_dropdown("client_id", $clients_dropdown, array(), "class='select2 validate-hidden' id='client_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php helper('cookie'); ?>
                    <div class="form-group clearfix">
                        <div class="row">
                            <label for="order_note" class=" col-md-3"><?php echo app_lang('note'); ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_textarea(array(
                                    "id" => "order_note",
                                    "name" => "order_note",
                                    "class" => "form-control",
                                    "placeholder" => app_lang('note'),
                                    "data-rich-text-editor" => true,
                                    "value" => get_cookie("default_order_note")
                                ));
                                ?>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="row">
                            <label class=" col-md-3"><?php echo app_lang('domain'); ?></label>
                            <div class=" col-md-9">

                                <!-- Radio options -->
                                <div class="mb10">
                                    <label class="radio-inline mr15">
                                        <input type="radio" name="domain_type" value="webhut" id="domain_type_webhut" checked>
                                        <?php echo app_lang('with_webhut_domain'); // "webhut.net ke sath" ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="domain_type" value="self" id="domain_type_self">
                                        <?php echo app_lang('self_domain'); // "Apna Domain" ?>
                                    </label>
                                </div>

                                <!-- webhut.net wala option (default) -->
                                <div class="row" id="webhut_domain_wrapper">
                                    <div class="col-sm-6">
                                        <?php
                                        echo form_input(array(
                                            "id" => "domain_name",
                                            "name" => "domain_name",
                                            "class" => "form-control",
                                            "placeholder" => app_lang('domain_name'),
                                            "data-rule-required" => "true",
                                            "data-msg-required" => app_lang('field_required')
                                        ));
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" disabled value=".webhut.net" class="form-control">
                                    </div>
                                </div>

                                <!-- Self domain wala option (hidden by default) -->
                                <div class="row" id="self_domain_wrapper" style="display:none;">
                                    <div class="col-sm-12">
                                        <?php
                                        echo form_input(array(
                                            "id" => "self_domain_name",
                                            "name" => "self_domain_name",
                                            "class" => "form-control",
                                            "placeholder" => "example.com"
                                        ));
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
            <div  id="order-dropzone" class="post-dropzone">
                <?php echo view("includes/dropzone_preview"); ?>
                <div class="card-footer clearfix">
                    <!-- <button class="btn btn-default upload-file-button float-start me-auto btn-sm round m-1" type="button" style="color:#7988a2"> <?php echo app_lang("upload_file"); ?></button> -->
                    
                    <button id="place-order-button" class="btn btn-primary float-end ml10"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('place_order'); ?></button>
                    <!-- <?php if ($login_user->user_type === "staff") echo modal_anchor(get_uri("orders/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-default float-end ml10", "title" => app_lang('add_item'))); ?>
                    <?php echo anchor(get_uri("items/grid_view"), "<i data-feather='search' class='icon-16'></i> " . app_lang('find_more_items'), array("class" => "btn btn-default float-end")); ?> -->
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>

<script type="text/javascript">
    // $(document).ready(function () {

    //   //#Stripe Payment Gateway code by Harsh
    //     // Replace 'YOUR_PUBLISHABLE_KEY' with your actual Stripe publishable key.
    //     //const stripe = Stripe('pk_test_51NtQ6RSEwmFRxEh6eVlUmzbFuiDbRQMyVN1iXGrSIK8QZGSgS41wzKsGWER81vQOoZBg0GRpy0lXgcw1GBgndDuW00dSBCp2DW');
    //     const stripe = Stripe('pk_test_51NQULtK9SAenM6X5XWU3Dt042VqoQJ8c7WtgJPZOv2kHTdxYlFuw5dCcugZHhgLPGLq9T1r42IQU5UnAbHjS1sAp00mIWyfaoN');

    //     // Replace 'YOUR_SUCCESS_URL' and 'YOUR_CANCEL_URL' with the appropriate URLs.
        
    //     // Create a function to handle the redirection to Stripe Checkout.
    //     const redirectToStripeCheckout = async (order_id) => {
    //       const successURL = 'https://www.webhut.net/store-admin/index.php/orders/success_page?order_id='+order_id+'&session_id={CHECKOUT_SESSION_ID}';
    //     const cancelURL = 'https://www.webhut.net/store-admin/index.php/orders/error_page?order_id='+order_id+'&session_id={CHECKOUT_SESSION_ID}';
    
    //       const { error } = await stripe.redirectToCheckout({
    //         lineItems: [{ price: 'price_1PR7RXK9SAenM6X5BZztJENG', quantity: 1 }],
    //         mode: 'subscription',
    //         successUrl: successURL,
    //         cancelUrl: cancelURL,
    //         //payment_method_types: ['card', 'ideal', 'sepa_debit', 'sofort', 'giropay', 'eps', 'p24']
    //       }).then(function(result){
    //         console.log("Hii");
    //       });
    //     };
    //     //$("#test-button").click(function() {
    //         //redirectToStripeCheckout(); // Call your function when the button is clicked
    //     //});

    //      $("#place-order-button").on("click", function (e) {
    //         // Prevent the default form submission behavior
    //         e.preventDefault();

    //         // Serialize the form data
    //         var formData = $("#place-order-form").serialize();

    //         // Send an AJAX request to submit the form data
    //         $.ajax({
    //             url: $("#place-order-form").attr("action"),
    //             type: "POST",
    //             data: formData,
    //             dataType: "json",
    //             success: function (response) {
    //                 // Check if the AJAX request was successful
    //                 if (response.success) {
    //                     // Call the redirectCheckout() function
    //                     let my_url = response.redirect_to.split("/");
    //                     let order_id = my_url[my_url.length-1];
    //                     redirectToStripeCheckout(order_id);
    //                 } else {
    //                     // Handle any errors or display a message
    //                     // You can use Toastr or other notification methods here
    //                     toastr.error(response.message);
    //                 }
    //             },
    //             error: function (xhr, status, error) {
    //                 // Handle AJAX errors
    //                 console.error(xhr.responseText);
    //             },
    //         });
    //     });   
    
        // Code Add By anuj
        $(document).ready(function () {
            $('input[name="domain_type"]').on('change', function () {
                if ($(this).val() === 'self') {
                    $('#webhut_domain_wrapper').hide();
                    $('#self_domain_wrapper').show();

                    $('#domain_name').removeAttr('data-rule-required');
                    $('#self_domain_name').attr('data-rule-required', 'true');
                } else {
                    $('#self_domain_wrapper').hide();
                    $('#webhut_domain_wrapper').show();

                    $('#self_domain_name').removeAttr('data-rule-required');
                    $('#domain_name').attr('data-rule-required', 'true');
                }
            });
        // Initialize Stripe
        // const stripe = Stripe('pk_test_51NtQ6RSEwmFRxEh6eVlUmzbFuiDbRQMyVN1iXGrSIK8QZGSgS41wzKsGWER81vQOoZBg0GRpy0lXgcw1GBgndDuW00dSBCp2DW');
        const stripe = Stripe("<?php echo $payment_setting->publishable_key;?>");
        // Function to handle redirection to Stripe Checkout
        const redirectToStripeCheckout = async (order_id, lineItems) => {
            const successURL = 'http://webhut.net/store-admin/index.php/orders/success_page?order_id='+order_id+'&session_id={CHECKOUT_SESSION_ID}';
            const cancelURL = 'http://webhut.net/store-admin/index.php/orders/error_page?order_id='+order_id+'&session_id={CHECKOUT_SESSION_ID}';
            
            const { error } = await stripe.redirectToCheckout({
                lineItems: lineItems,
                mode: 'subscription',
                successUrl: successURL,
                cancelUrl: cancelURL,
            }).then(function(result){
                console.log("Redirecting to Stripe Checkout...");
            });
        };
    
    
        // Event listener for place order button
        $("#place-order-button").on("click", function (e) {
            e.preventDefault(); // Prevent the default form submission behavior
            
            // Serialize the form data
            var formData = $("#place-order-form").serialize();
    
            // Send an AJAX request to submit the form data
            $.ajax({
                url: $("#place-order-form").attr("action"),
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    // Check if the AJAX request was successful
                    if (response.success) {
                        // Iterate over products in the response
                        response.products.forEach(function(product) {
                            // Check if the product price ID is available
                            if (product.stripe_price_id) {
                                // If available, create line item with product price ID
                                redirectToStripeCheckout(response.order_id, [{ price: product.stripe_price_id, quantity: 1 }]);
                            }
                        });
                    } else {
                        // Handle any errors or display a message
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle AJAX errors
                    console.error(xhr.responseText);
                },
            });
        });

        //Payment Gateway Code Ends Here.
        $("#client_id").select2();
        $("#order-item-table").appTable({
            source: '<?php echo_uri("orders/item_list_data_of_login_user") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("item") ?> ", "bSortable": false},
                {title: "<?php echo app_lang("quantity") ?>", "class": "w15p", "bSortable": false},
                //  {title: "<?php echo app_lang("limit") ?>", "class": "w15p", "bSortable": false},
                {title: "<?php echo app_lang("rate") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<?php echo app_lang("total") ?>", "class": "text-right w15p", "bSortable": false},
                // {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", "bSortable": false}
            ],
            onInitComplete: function () {
                //apply sortable
                $("#order-item-table").find("tbody").attr("id", "order-item-table-sortable");
                var $selector = $("#order-item-table-sortable");
                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        //prepare sort indexes
                        var data = "";
                        $.each($selector.find(".item-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }
                            data += $(ele).attr("data-id") + "-" + index;
                        });
                        //update sort indexes
                        $.ajax({
                            url: '<?php echo_uri("orders/update_item_sort_values") ?>',
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });
            },
            onDeleteSuccess: function (result) {
                $("#order-total-section").html(result.order_total_view);
                //Redirect To pricig page after successfull deletion.
                if (window.location.protocol === "http:") {
                    window.location.href = 'http://localhost/matt-subscription/pricing.php';
                } else if (window.location.protocol === "https:") {
                   window.location.href = 'https://www.webhut.net/pricing.php';
                } else {
                    console.log("The protocol is neither HTTP nor HTTPS.");
                }
                
            },
            onUndoSuccess: function (result) {
                $("#order-total-section").html(result.order_total_view);
            }
        });
        $("#company_id").select2({data: <?php echo json_encode($companies_dropdown); ?>});  

        //Code of uplaod File Button
        // var uploadUrl = "<?php echo get_uri("orders/upload_file"); ?>";
        // var validationUri = "<?php echo get_uri("orders/validate_orders_file"); ?>";
        // var dropzone = attachDropzoneWithForm("#order-dropzone", uploadUrl, validationUri);
        // $("#place-order-form").appForm({
        //     isModal: false,
        //     onSubmit: function () {
        //         appLoader.show();
        //         $("#place-order-form").find('[type="submit"]').attr('disabled', 'disabled');
        //     },
        //     onSuccess: function (result) {
        //         appLoader.hide();
        //         window.location = result.redirect_to;
        //     }
        // });
    });

    function manageOrderQty(id, type){
        
        var action = 'plus';
        if( type == 0 )
            action = 'minus';

        $.ajax({
            url: "<?php echo get_uri('items/change_cart_item_quantity') ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                action: action,
                order_note: $('#order_note').val(),
                delivery_date: $('#delivery_date').val()
            },
            success: function (result) {
                if (result.success) {
                    toastr.success("Quantity updated successfully");

                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                    
                }else {
                    toastr.error(result.message);
                    // alert("Error");
                }
            }
        });
    };

</script>

   

    </script>
</div>
