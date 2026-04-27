<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget();?>
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('features'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("features/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_features'), array("class" => "btn btn-default", "title" => app_lang('add_features'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="features-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#features-table").appTable({
            source: '<?php echo_uri("features/list_data") ?>',
            order: [[0, 'desc']],
            columns: [
                {title: "<?php echo app_lang('title') ?> "},
                {title: "<?php echo app_lang('type') ?> "},
                {title: "<?php echo app_lang('order') ?> ", "class": "text-center"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>