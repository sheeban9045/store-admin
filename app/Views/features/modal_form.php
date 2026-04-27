<?php echo form_open(get_uri("features/save"), array("id" => "features-form", "class" => "general-form", "role" => "form")); ?>
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
                    <label for="payment_type" class=" col-md-3"><?php echo app_lang('type'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown(
                            "type",
                            $types_dropdown,
                            isset($model_info->type) ? $model_info->type : "",
                            "class='select2 validate-hidden' id='type' data-rule-required='true'"
                        );
                        ?>
                    </div>
                </div>
            </div> 
            <div class="form-group">
                <div class="row">
                    <label for="sort_order" class=" col-md-3"><?php echo app_lang('order'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "sort_order",
                            "name" => "sort_order",
                            "type" => "number",
                            "value" => $model_info->sort_order,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('order'),
                            "autofocus" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
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
        $("#features-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#features-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#features-form .select2").select2();
    });
</script>