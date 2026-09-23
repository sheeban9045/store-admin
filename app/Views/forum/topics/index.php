<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo $login_user->is_admin ? app_lang('manage_topics') : app_lang('forum_topics'); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("forum"), "<i data-feather='message-square' class='icon-16'></i> " . app_lang('forum'), array("class" => "btn btn-default", "title" => app_lang('forum'))); ?>
                <?php
                if (!$login_user->is_admin) {
                    echo anchor(get_uri("forum_topics/my_topics"), "<i data-feather='file-text' class='icon-16'></i> " . app_lang('my_topics'), array("class" => "btn btn-default", "title" => app_lang('my_topics')));
                }
                ?>
                <?php echo modal_anchor(get_uri("forum_topics/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_topic'), array("class" => "btn btn-default", "title" => app_lang('add_topic'), "data-post-list_type" => "all")); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="forum-topic-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#forum-topic-table").appTable({
            source: '<?php echo_uri("forum_topics/all_list_data") ?>',
            order: [[4, "desc"]],
            filterDropdown: [
                {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>}
            ],
            columns: [
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<?php echo app_lang("category"); ?>', "class": "w15p"},
                {title: '<?php echo app_lang("created_by"); ?>', "class": "w15p"},
                {title: '<?php echo app_lang("replies"); ?>', "class": "text-center w10p"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("last_activity"); ?>', "iDataSort": 4, "class": "w15p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 5],
            xlsColumns: [0, 1, 2, 3, 5]
        });
    });
</script>
