<style>
    .bg-success{
        background-color: #D4EDDA !important;
        color: #155747 !important;
    }
</style>
<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget(); ?>

    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('purchase_history'); ?></h1>
        </div>

        <div class="table-responsive">
            <table id="orders-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#orders-table").appTable({
            source: '<?php echo_uri("webhut_plugins/history_data") ?>',
            order: [[0, 'desc']],
            columns: [
                {title: "<?php echo app_lang('icon'); ?>"},
                {title: "<?php echo app_lang('plugin'); ?>"},
                {title: "<?php echo app_lang('community'); ?>"},
                {title: "<?php echo app_lang('amount'); ?>"},
                {title: "<?php echo app_lang('payment_status'); ?>", "class": "text-center"},
                {title: "<?php echo app_lang('status'); ?>", "class": "text-center"},
                {title: "<?php echo app_lang('date'); ?>"},
            ],
            printColumns: [0,1,2,3,4,5,6],
            xlsColumns:   [0,1,2,3,4,5,6]
        });
    });
</script>