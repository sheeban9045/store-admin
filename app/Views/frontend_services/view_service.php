<div class="modal-body clearfix general-form">
    <div class="p10 clearfix">
        <div class="d-flex bg-white">
            <div class="flex-shrink-0 p20 text-center" style="width: 200px;">
                <?php 
                if ($service->image) {
                    $images = @unserialize($service->image);
                    if ($images && is_array($images) && count($images) > 0) {
                        $image_url = get_source_url_of_file($images, get_setting("timeline_file_path"), "thumbnail");
                        echo "<img style='max-width:100%; border-radius:4px;' src='{$image_url}' alt='{$service->title}'>";
                    }
                }
                ?>
            </div>
            <div class="flex-grow-1 p20 pl0">
                <h4 class="mb-3"><?php echo $service->title; ?></h4>
                <div class="text-off mb-3"><?php echo $service->description; ?></div>
                
                <table class="table table-borderless table-sm m0">
                    <tr>
                        <td class="w150"><strong>Order ID:</strong></td>
                        <td>#<?php echo $order->id; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Amount Paid:</strong></td>
                        <td><?php echo to_currency($order->total_amount, "$"); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Purchase Date:</strong></td>
                        <td><?php echo format_to_datetime($order->created_at); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Status:</strong></td>
                        <td>
                            <span class="badge bg-success">Success</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
</div>
