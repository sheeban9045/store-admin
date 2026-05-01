<link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.css"); ?>" />
<script src="<?php echo base_url("assets/js/toastr/toastr.js"); ?>"></script>
<div id="page-content" class="page-wrapper clearfix">
    <div class="process-order-preview">
        <div class="card">
            <?php echo form_open(get_uri("webhut_plugins/place_order"), array("id" => "place-order-form", "class" => "general-form", "role" => "form")); ?>

            <input type="hidden" name="plugin_id" value="<?php echo $plugin_data->id ?? ''; ?>" />
            <div class="page-title clearfix">
                <h1> <?php echo app_lang('process_order'); ?></h1>
            </div>
            <div class="p20">
                <div class="mb20 ml15 mr15"><?php echo app_lang("process_order_info_message"); ?></div>
                <div class="m15 pb15 mb30">
                    <div class="table-responsive">
                        <table class="display mt0 table  " width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('item'); ?></th>
                                    <th><?php echo app_lang('description'); ?></th>
                                    <th><?php echo app_lang('rate'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> <?php echo $plugin_data->name; ?> </td>
                                    <td><?php echo substr($plugin_data->description, 0, 70) . '...'; ?></td>
                                    <?php 
                                        $original_price = floatval($plugin_data->rate);
                                        $discount_value = floatval($plugin_data->discount_value);
                                        $discount_type  = $plugin_data->discount_type;

                                        if ($discount_type === 'percentage')
                                            $final_price    = $original_price * (1 - $discount_value / 100);
                                        else
                                            $final_price    = $original_price - $discount_value;
                                        
                                    ?>
                                    <td> <?php echo to_currency($final_price); ?> </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pl15 pr15">

                    <?php helper('cookie'); ?>

                    <div class="form-group">

                        <div class="row">
                            <label for="domain_name" class=" col-md-3"><?php echo app_lang('domain'); ?></label>

                            <div class=" col-md-9">
                                <div class="row">

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo app_lang('select_community'); ?></label>
                                            <?php
                                            $community_options = ['' => app_lang('select')];

                                            if (!empty($communities)) {
                                                foreach ($communities as $community) {
                                                    $community_options[$community] = $community;
                                                }
                                            }
                    
                                            echo form_dropdown(
                                                "community", 
                                                $community_options, 
                                                "", 
                                                "class='select2 form-control' id='community'"
                                            );
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="order-dropzone" class="post-dropzone">
                        <?php echo view("includes/dropzone_preview"); ?>
                        <div class="card-footer clearfix">

                            <button id="place-order-button" class="btn btn-primary float-end ml10"><span
                                    data-feather="check-circle" class="icon-16"></span>
                                    <?php echo app_lang('place_order'); ?></button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <script src="https://js.stripe.com/v3/"></script>

            <script>
                $("#place-order-button").on("click", function(e) {
                    e.preventDefault();

                    $("#place-order-button").attr("disabled", true);

                    var formData = $("#place-order-form").serialize();

                    $.ajax({
                        url: $("#place-order-form").attr("action"),
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        success: function(response) {
                            
                            console.log(response);
                            if (response.success) {
                                // Add stripe redirection logic here
                                const stripe = Stripe("<?php echo $payment_setting->publishable_key;?>");

                                stripe.redirectToCheckout({
                                    sessionId: response.session_id
                                }); 
                            } else {
                                $("#place-order-button").attr("disabled", false);
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            $("#place-order-button").attr("disabled", false);
                            console.error(xhr.responseText);
                        },
                    });
                });

            </script>
        </div>
    </div>
