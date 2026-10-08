<link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.css"); ?>" />
<script src="<?php echo base_url("assets/js/toastr/toastr.js"); ?>"></script>

<div id="page-content" class="page-wrapper clearfix">
    <div class="process-order-preview">
        <div class="card">
            <?php echo form_open(get_uri("Frontend_services/place_order"), [
                "id"    => "place-order-form",
                "class" => "general-form",
                "role"  => "form"
            ]); ?>

            <div class="page-title clearfix">
                <h1>Service Checkout</h1>
                <div class="title-button-group">
                    <a href="https://webhut.net/services.php" class="btn btn-default">
                        <i data-feather="arrow-left" class="icon-16"></i> Browse Services
                    </a>
                </div>
            </div>

            <div class="p20">
                <div class="mb20 ml15 mr15">Please review your service purchase details before proceeding.</div>

                <div class="m15 pb15 mb30">
                    <div class="table-responsive">
                        <table class="display mt0 table" width="100%">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <input type="hidden" name="service_id" value="<?php echo $service->id; ?>" />

                                    <td><?php echo $service->title; ?></td>
                                    <td>
                                        <?php echo isset($service->price) ? to_currency($service->price, "$") : '-'; ?> 
                                        <?php echo isset($service->price_type) ? ' ' . ucfirst($service->price_type) : '-'; ?> 
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1" style="text-align:right; font-weight:600;">Grand Total:</td>
                                    <td colspan="1"><strong><?php echo to_currency($service->price, "$"); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="pl15 pr15">
                    <div id="order-dropzone" class="post-dropzone">
                        <div class="card-footer clearfix">
                            <button id="place-order-button" class="btn btn-primary float-end ml10">
                                <span data-feather="check-circle" class="icon-16"></span> Place Order
                            </button>
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
                            toastr.error("An error occurred during checkout.");
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>
