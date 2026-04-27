<?php echo form_open(get_uri("items/save_features"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <div class="form-group">
                <div class="row">

                    <?php foreach ($feature_types as $type): ?>

                        <?php if (!empty($grouped_features[$type->id])): ?>

                            <div class="form-group border-bottom mb-3 pb-2">
                                <div class="row">

                                    <label class="col-md-3">
                                        <strong><?= $type->title; ?></strong> <!-- ✅ FIX -->
                                    </label>

                                    <div class="col-md-9">

                                        <?php foreach ($grouped_features[$type->id] as $feature): ?>
                                            <div class="form-check">
                                                <label>
                                                    <input type="checkbox"
                                                        name="feature_ids[]"
                                                        value="<?= $feature->id ?>"
                                                        <?= in_array($feature->id, $selected_features) ? 'checked' : '' ?>>

                                                    <?= $feature->title ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>

                                </div>
                            </div>

                        <?php endif; ?>

                    <?php endforeach; ?>
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

        $("#item-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#item-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#item-form .select2").select2();
    });
</script>