<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo $login_user->is_admin ? app_lang('manage_replies') : app_lang('forum_replies'); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("forum"), "<i data-feather='message-square' class='icon-16'></i> " . app_lang('forum'), array("class" => "btn btn-default", "title" => app_lang('forum'))); ?>
                <?php
                if (!$login_user->is_admin) {
                    echo anchor(get_uri("forum_replies/my_replies"), "<i data-feather='message-circle' class='icon-16'></i> " . app_lang('my_replies'), array("class" => "btn btn-default", "title" => app_lang('my_replies')));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="forum-reply-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#forum-reply-table").appTable({
            source: '<?php echo_uri("forum_replies/all_list_data") ?>',
            order: [[4, "desc"]],
            columns: [
                {title: '<?php echo app_lang("reply"); ?>'},
                {title: '<?php echo app_lang("topic"); ?>', "class": "w20p"},
                {title: '<?php echo app_lang("category"); ?>', "class": "w15p"},
                {title: '<?php echo app_lang("created_by"); ?>', "class": "w15p"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("date"); ?>', "iDataSort": 4, "class": "w15p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 5],
            xlsColumns: [0, 1, 2, 3, 5]
        });
    });
</script>
