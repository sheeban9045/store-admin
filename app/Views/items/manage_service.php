<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget();?>
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('services'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("items/service_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_service'), array("class" => "btn btn-default", "title" => app_lang('add_service'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="service-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#service-table").appTable({
            source: '<?php echo_uri("items/service_list_data") ?>',
            columns: [
                {title: "<?php echo app_lang('title') ?> ", "class": "w10p"},
                {title: "<?php echo app_lang('description') ?>", "class": "w30p"},
                {title: "<?php echo app_lang('rate') ?>", "class": "text-right w100"},
                {title: "<?php echo app_lang('order') ?>", "class": "text-center"},
                {title: "<?php echo app_lang('status') ?>", "class": "text-center"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w15p"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });
    });
</script>