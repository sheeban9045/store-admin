<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('services'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("services/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_service'), array("class" => "btn btn-default", "title" => app_lang('add_service'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="services-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#services-table").appTable({
            source: '<?php echo_uri("services/list_data") ?>',
            order: [[5, "asc"]],
            columns: [
                {title: '<?php echo app_lang("image"); ?>', "class": "text-center w80", sortable: false},
                {title: '<?php echo app_lang("service_name"); ?>'},
                {title: '<?php echo app_lang("slug"); ?>'},
                {title: '<?php echo app_lang("price_usd"); ?>', "class": "text-right w120"},
                {title: '<?php echo app_lang("price_type"); ?>', "class": "w150"},
                {title: '<?php echo app_lang("sort_order"); ?>', "class": "text-center w100"},
                {title: '<?php echo app_lang("status"); ?>', "class": "text-center w100"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4, 5, 6],
            xlsColumns: [1, 2, 3, 4, 5, 6]
        });
    });
</script>
