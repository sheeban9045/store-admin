<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('forum_categories'); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("forum"), "<i data-feather='message-square' class='icon-16'></i> " . app_lang('forum'), array("class" => "btn btn-default", "title" => app_lang('forum'))); ?>
                <?php echo modal_anchor(get_uri("forum_categories/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_category'), array("class" => "btn btn-default", "title" => app_lang('add_category'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="forum-category-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#forum-category-table").appTable({
            source: '<?php echo_uri("forum_categories/list_data") ?>',
            order: [[3, "asc"]],
            columns: [
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<?php echo app_lang("description"); ?>'},
                {title: '<?php echo app_lang("topics"); ?>', "class": "text-center w10p"},
                {title: '<?php echo app_lang("sort"); ?>', "class": "text-center w10p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>
