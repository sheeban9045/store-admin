<?php echo form_open_multipart(get_uri("webhut_plugins/save"), array(
    "id"    => "plugins-form",
    "class" => "general-form",
    "role"  => "form"
)); ?>

<div class="modal-body clearfix">
    <div class="container-fluid">

        <input type="hidden" name="id" value="<?php echo $model_info->id ?? ''; ?>" />
        <input type="hidden" name="hidden_zip_file" value="<?php echo $model_info->zip_file ?? ''; ?>" />
        <input type="hidden" name="hidden_icon"    value="<?php echo $model_info->icon ?? ''; ?>" />
        <input type="hidden" name="existing_photos" value="<?php echo $model_info->photos ?? '[]'; ?>" />

        <!-- Name -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('name'); ?></label>
                <div class="col-md-9">
                    <?php echo form_input(array(
                        "name"             => "name",
                        "value"            => $model_info->name ?? '',
                        "class"            => "form-control",
                        "placeholder"      => app_lang('name'),
                        "data-rule-required" => true,
                        "data-msg-required"  => app_lang('field_required'),
                    )); ?>
                </div>
            </div>
        </div>

        <!-- Code -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3">
                    <?php echo app_lang('code'); ?>
                    <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true"
                         title="Enter a unique code for the plugin (only letters, numbers, underscore or dash). This will be used to identify the plugin internally.">
                        <span data-feather="info" class="icon-14"></span>
                    </button>
                </label>
                <div class="col-md-9">
                    <?php echo form_input(array(
                        "name"             => "code",
                        "value"            => $model_info->code ?? '',
                        "class"            => "form-control",
                        "placeholder"      => app_lang('enter_code__placeholder'),
                        "data-rule-required" => true,
                        "data-msg-required"  => app_lang('field_required'),
                    )); ?>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('description'); ?></label>
                <div class="col-md-9">
                    <?php echo form_textarea(array(
                        "name"        => "description",
                        "value"       => $model_info->description ?? '',
                        "class"       => "form-control",
                        "placeholder" => app_lang('description'),
                        "rows"        => 3,
                    )); ?>
                </div>
            </div>
        </div>

        <!-- Version -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('version'); ?></label>
                <div class="col-md-9">
                    <?php echo form_input(array(
                        "name"             => "version",
                        "value"            => $model_info->version ?? '',
                        "class"            => "form-control",
                        "placeholder"      => "e.g. 1.0.0",
                        "data-rule-required" => true,
                        "data-msg-required"  => app_lang('field_required'),
                    )); ?>
                </div>
            </div>
        </div>

        <!-- Rate -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('rate'); ?></label>

                <div class="col-md-9">
                    <div class="input-group">
                        
                        <!-- Left $ -->
                        <span class="input-group-text">$</span>

                        <!-- Input -->
                        <?php echo form_input(array(
                            "name" => "rate",
                            "type" => "number",
                            "step" => "1",
                            "value" => $model_info->rate ?? '',
                            "class" => "form-control",
                            "placeholder" => "0.00",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang('field_required'),
                        )); ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Type -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('discount_type'); ?></label>
                <div class="col-md-9">
                    <?php echo form_dropdown("discount_type", array(
                        "percentage" => app_lang('percentage'),
                        "fixed"      => app_lang('fixed'),
                    ), $model_info->discount_type ?? "percentage", "class='select2 form-control' id='discount_type'"); ?>
                </div>
            </div>
        </div>

        <!-- Discount Value -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('discount_value'); ?></label>
                <div class="col-md-9">
                    <?php echo form_input(array(
                        "name"        => "discount_value",
                        "type"        => "number",
                        "value"       => $model_info->discount_value ?? '',
                        "class"       => "form-control",
                        "placeholder" => "0",
                    )); ?>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('status'); ?></label>
                <div class="col-md-9">
                    <?php echo form_dropdown("status", array(
                        "active" => app_lang('active'),
                        "inactive" => app_lang('inactive'),
                    ), $model_info->status ?? "active", "class='select2 form-control' id='status'"); ?>
                </div>
            </div>
        </div>

        <!-- Label -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('label'); ?></label>
                <div class="col-md-9">
                    <?php echo form_dropdown("label", array(
                        "released" => app_lang('released'),
                        "upcoming" => app_lang('upcoming'),
                    ), $model_info->label ?? "released", "class='select2 form-control' id='label'"); ?>
                </div>
            </div>
        </div>

        <!-- ZIP File -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('zip_file'); ?></label>
                <div class="col-md-9">
                    <input type="file" name="zip_file" class="form-control" accept=".zip" />
                    <?php if (!empty($model_info->zip_file)): ?>
                        <small class="text-muted mt-1 d-block">
                            <i data-feather="file" class="icon-14"></i>
                            <?php echo $model_info->zip_file; ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Icon -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('icon'); ?></label>
                <div class="col-md-9">
                    <input type="file" name="icon" class="form-control" accept="image/*" />
                    <?php if (!empty($model_info->icon)): ?>
                        <div class="mt-2">
                            <img src="<?php echo base_url('uploads/plugins/icons/' . $model_info->icon); ?>"
                                 style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" />
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Photos -->
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('photos'); ?></label>
                <div class="col-md-9">
                    <input type="file" name="photos[]" class="form-control" accept="image/*" multiple />
                    <?php
                    $existing_photos = json_decode($model_info->photos ?? '[]', true);
                    if (!empty($existing_photos)):
                    ?>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <?php foreach ($existing_photos as $photo): ?>
                                <img src="<?php echo base_url('uploads/plugins/photos/' . $photo); ?>"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" />
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
    <button type="submit" class="btn btn-primary">
        <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?>
    </button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#plugins-form").appForm({
            onSuccess: function (result) {
                $("#plugins-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $("#plugins-form .select2").select2();
    });
</script>