<link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.css"); ?>" />
<script src="<?php echo base_url("assets/js/toastr/toastr.js"); ?>"></script>

<div id="page-content" class="page-wrapper clearfix">
    <div class="process-order-preview">
        <div class="card">
            <?php echo form_open(get_uri("webhut_plugins/place_order"), [
                "id"    => "place-order-form",
                "class" => "general-form",
                "role"  => "form"
            ]); ?>

            <div class="page-title clearfix">
                <h1><?php echo app_lang('process_order'); ?></h1>
                <div class="title-button-group">
                    <a href="https://webhut.net/list-plugins.php" class="btn btn-default">
                        <i data-feather="arrow-left" class="icon-16"></i> <?php echo app_lang('browse_plugins'); ?>
                    </a>
                </div>
            </div>

            <div class="p20">
                <div class="mb20 ml15 mr15"><?php echo app_lang("process_order_info_message"); ?></div>

                <div class="m15 pb15 mb30">
                    <div class="table-responsive">
                        <table class="display mt0 table" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('item'); ?></th>
                                    <th><?php echo app_lang('description'); ?></th>
                                    <th style="width: 250px"><?php echo app_lang('community'); ?></th>
                                    <th><?php echo app_lang('rate'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grand_total = 0;
                                foreach ($plugins as $index => $plugin):
                                    $original_price = floatval($plugin->rate);
                                    $discount_value = floatval($plugin->discount_value);
                                    $discount_type  = $plugin->discount_type;

                                    if ($discount_type === 'percentage')
                                        $final_price = $original_price * (1 - $discount_value / 100);
                                    else
                                        $final_price = $original_price - $discount_value;

                                    $grand_total += $final_price;

                                    $community_options = ['' => app_lang('select')];
                                    if (!empty($communities)) {
                                        foreach ($communities as $community) {
                                            $community_options[$community] = $community;
                                        }
                                    }
                                ?>
                                <tr>
                                    <input type="hidden" name="plugin_ids[]" value="<?php echo $plugin->id; ?>" />

                                    <td><?php echo $plugin->name; ?></td>
                                    <td><?php echo substr($plugin->description, 0, 70) . '...'; ?></td>
                                    <td>
                                        <?php if (!empty($communities)): ?>
                                            <select name="community[<?php echo $plugin->id; ?>]"
                                                    class='select2 form-control'
                                                    required>
                                                <?php foreach ($community_options as $val => $label): ?>
                                                    <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <span class="text-danger" style="font-size:12px;">
                                                No subscription found
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo to_currency($final_price); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align:right; font-weight:600;">Grand Total:</td>
                                    <td colspan="1"><strong><?php echo to_currency($grand_total); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if (empty($communities)): ?>
                        <div class="alert alert-info mt15 ml15 mr15">
                            You don't have any subscription yet. Please purchase a subscription to access this feature.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pl15 pr15">
                    <div id="order-dropzone" class="post-dropzone">
                        <?php echo view("includes/dropzone_preview"); ?>
                        <div class="card-footer clearfix">
                            <button id="place-order-button" class="btn btn-primary float-end ml10">
                                <span data-feather="check-circle" class="icon-16"></span>
                                <?php echo app_lang('place_order'); ?>
                            </button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>

            <script src="https://js.stripe.com/v3/"></script>
            <script>
                $("#place-order-button").on("click", function(e) {
                    console.log($("#place-order-form").serialize());    
                    e.preventDefault();

                    let valid = true;
                    $("select[name^='community']").each(function() {
                        if ($(this).val() === '') {
                            valid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!valid) {
                        toastr.error('Please select a community for each plugin.');
                        return;
                    }

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
                                const stripe = Stripe("<?php echo $payment_setting->publishable_key; ?>");
                                stripe.redirectToCheckout({ sessionId: response.session_id });
                            } else {
                                $("#place-order-button").attr("disabled", false);
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            $("#place-order-button").attr("disabled", false);
                            console.error(xhr.responseText);
                        }
                    });
                });

                function updatePlaceOrderBtn() {
                    let hasInvalid = false;
                    $("select[name^='community']").each(function () {
                        if ($(this).hasClass("is-invalid")) {
                            hasInvalid = true;
                        }
                    });
                    if (hasInvalid) {
                        $("#place-order-button").attr("disabled", true);
                    } else {
                        $("#place-order-button").attr("disabled", false);
                    }
                }

                $(document).on("change", "select[name^='community']", function () {
                    const $select  = $(this);
                    const pluginId = $select.attr("name").match(/\d+/)[0];
                    const community = $select.val();

                    $select.next(".already-purchased-msg").remove();
                    $select.removeClass("is-invalid is-valid");

                    updatePlaceOrderBtn();

                    if (!community) {
                        updatePlaceOrderBtn();
                        return;
                    }

                    $.ajax({
                        url: "<?php echo get_uri('webhut_plugins/check_already_purchased'); ?>",
                        type: "POST",
                        data: {
                            plugin_id: pluginId,
                            community: community
                        },
                        dataType: "json",
                        success: function (response) {
                            if (response.already_purchased) {
                                $select.addClass("is-invalid");

                                $select.after(
                                    '<div class="already-purchased-msg text-danger mt5" style="font-size:11px;">' +
                                    '<span data-feather="x" class="icon-16"></span> Plugin already purchased for the community. Please select another community or remove the item from the cart.' +
                                    '</div>'
                                );
                            } else {
                                $select.addClass("is-valid");
                            }

                            updatePlaceOrderBtn();
                        },
                        error: function () {
                            console.error("Check failed.");
                            updatePlaceOrderBtn();
                        }
                    });
                });

                $(document).ready(function () {
                    $(".select2").select2();
                });
            </script>
        </div>
    </div>
</div>