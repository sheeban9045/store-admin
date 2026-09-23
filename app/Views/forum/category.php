<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo esc($category_info->title); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("forum"), "<i data-feather='arrow-left' class='icon-16'></i> " . app_lang('forum'), array("class" => "btn btn-default", "title" => app_lang('forum'))); ?>
                <?php echo modal_anchor(get_uri("forum_topics/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_topic'), array("class" => "btn btn-default", "title" => app_lang('add_topic'), "data-post-category_id" => $category_info->id, "data-post-list_type" => "category")); ?>
            </div>
        </div>
        <?php if ($category_info->description) { ?>
            <div class="p15 b-b text-off"><?php echo nl2br(esc($category_info->description)); ?></div>
        <?php } ?>
        <div class="table-responsive">
            <table id="forum-topic-table" class="display" cellspacing="0" width="100%" data-category-id="<?php echo $category_info->id; ?>">
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#forum-topic-table").appTable({
            source: '<?php echo_uri("forum_topics/category_list_data/" . $category_info->id) ?>',
            order: [[3, "desc"]],
            columns: [
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<?php echo app_lang("created_by"); ?>', "class": "w15p"},
                {title: '<?php echo app_lang("replies"); ?>', "class": "text-center w10p"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("last_activity"); ?>', "iDataSort": 3, "class": "w15p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 4],
            xlsColumns: [0, 1, 2, 4]
        });
    });
</script>
