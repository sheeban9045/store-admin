<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('search_topics'); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("forum"), "<i data-feather='arrow-left' class='icon-16'></i> " . app_lang('forum'), array("class" => "btn btn-default", "title" => app_lang('forum'))); ?>
            </div>
        </div>
        <div class="p15 b-b">
            <?php echo view("forum/search_box", array("search" => $search)); ?>
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
            source: '<?php echo_uri("forum_topics/search_list_data") ?>',
            filterParams: {datatable: true, search: <?php echo json_encode($search); ?>},
            stateSave: false,
            order: [[4, "desc"]],
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
