<?php echo form_open(get_uri("forum_topics/save"), array("id" => "forum-topic-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" name="list_type" value="<?php echo $list_type; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="category_id" class=" col-md-3"><?php echo app_lang('category'); ?></label>
                <div class=" col-md-9">
                    <?php
                    if ($can_change_category) {
                        echo form_dropdown("category_id", $categories_dropdown, $model_info->category_id, "class='select2 validate-hidden' id='category_id' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    } else {
                        echo "<div class='pt5'>" . get_array_value($categories_dropdown, $model_info->category_id) . "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
                        "maxlength" => 255,
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
                <label for="description" class=" col-md-3"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => $model_info->description,
                        "class" => "form-control",
                        "style" => "height: 200px",
                        "placeholder" => app_lang('description'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rich-text-editor" => true
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
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#forum-topic-form").appForm({
            onSuccess: function (result) {
                var $table = $("#forum-topic-table");
                if (window.forumReloadOnTopicSave) {
                    location.reload();
                } else if ($table.length) {
                    var tableCategoryId = $table.attr("data-category-id");
                    if (!tableCategoryId || tableCategoryId == result.category_id) {
                        $table.appTable({newData: result.data, dataId: result.id});
                    } else {
                        $table.appTable({reload: true});
                    }
                } else {
                    window.location.href = result.topic_url;
                }
            }
        });

        $("#forum-topic-form .select2").select2();

        //show the rich text editor (when enabled in settings) for both add and edit
        setSummernote($("#forum-topic-form #description"), true);
    });
</script>
