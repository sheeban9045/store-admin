<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget(); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('plugins'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("webhut_plugins/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_plugin'), array(
                    "class" => "btn btn-default",
                    "title" => app_lang('add_plugin')
                )); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="plugins-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#plugins-table").appTable({
            source: '<?php echo_uri("webhut_plugins/list_data") ?>',
            order: [[0, 'desc']],
            columns: [
                {title: "<?php echo app_lang('icon') ?>", "class": "text-center", "orderable": false},
                {title: "<?php echo app_lang('name') ?>"},
                {title: "<?php echo app_lang('code') ?>"},
                {title: "<?php echo app_lang('version') ?>"},
                {title: "<?php echo app_lang('rate') ?>"},
                {title: "<?php echo app_lang('discount') ?>"},
                {title: "<?php echo app_lang('label') ?>"},
                {title: "<?php echo app_lang('status') ?>", "class": "text-center"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4, 5, 6],
            xlsColumns:   [1, 2, 3, 4, 5, 6]
        });
    });
</script>