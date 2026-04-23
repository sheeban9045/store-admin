<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget();?>
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('items'); ?></h1>
            <div class="title-button-group">
                 <?php echo modal_anchor(get_uri("items/import_items_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_items'), array("class" => "btn btn-default", "title" => app_lang('import_items'))); ?>
                <?php echo modal_anchor(get_uri("items/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-default", "title" => app_lang('add_item'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="item-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#item-table").appTable({
            source: '<?php echo_uri("items/list_data") ?>',
            order: [[0, 'desc']],
            filterDropdown: [
                {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>}
            ],
            columns: [
                {title: "<?php echo app_lang('title') ?> ", "class": "w10p"},
                {title: "<?php echo app_lang('description') ?>", "class": "w30p"},
                {title: "<?php echo app_lang('category') ?>"},
                {title: "<?php echo app_lang('payment_type') ?>"},
                {title: "<?php echo app_lang('unit_type') ?>", "class": "w100"},
                {title: "<?php echo app_lang('plan_type') ?>", "class": "w100"},
                {title: "<?php echo app_lang('rate') ?>", "class": "text-right w100"},
                {title: "<?php echo app_lang('order') ?>", "class": "text-center"},
                {title: "<?php echo app_lang('installation_charges') ?>", "class": "text-center"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w15p"}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>