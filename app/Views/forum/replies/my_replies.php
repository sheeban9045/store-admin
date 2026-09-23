<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('my_replies'); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("forum"), "<i data-feather='message-square' class='icon-16'></i> " . app_lang('forum'), array("class" => "btn btn-default", "title" => app_lang('forum'))); ?>
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
            source: '<?php echo_uri("forum_replies/my_list_data") ?>',
            order: [[3, "desc"]],
            columns: [
                {title: '<?php echo app_lang("reply"); ?>'},
                {title: '<?php echo app_lang("topic"); ?>', "class": "w20p"},
                {title: '<?php echo app_lang("category"); ?>', "class": "w15p"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("date"); ?>', "iDataSort": 3, "class": "w15p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 4],
            xlsColumns: [0, 1, 2, 4]
        });
    });
</script>
