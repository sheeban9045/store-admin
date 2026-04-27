<?php echo form_open(get_uri("items/save_service"), array("id" => "service-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "title",
                            "name" => "title",
                            "value" => $model_info->title,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('title'),
                            "autofocus" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="description" class="col-md-3"><?php echo app_lang('description'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "description",
                            "name" => "description",
                            "value" => $model_info->description ? $model_info->description : "",
                            "class" => "form-control",
                            "placeholder" => app_lang('description'),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="order" class=" col-md-3"><?php echo app_lang('rate'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "rate",
                            "name" => "rate",
                            "value" => $model_info->rate,
                            "class" => "form-control",
                            "placeholder" => app_lang('rate')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="order" class=" col-md-3"><?php echo app_lang('order'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "order",
                            "name" => "order",
                            "value" => $model_info->order,
                            "class" => "form-control",
                            "placeholder" => app_lang('order')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="payment_type" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        $status_dropdown = array(1=>"Active",0=>"Inactive");
                        echo form_dropdown("status", $status_dropdown, $model_info->status, "class='select2 validate-hidden' id='status' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#service-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#service-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#service-form .select2").select2();
    });
</script>