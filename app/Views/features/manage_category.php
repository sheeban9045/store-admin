<div id="page-content" class="page-wrapper clearfix">
    <?php echo announcements_alert_widget();?>
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('features_types'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("features/types_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_features_types'), array("class" => "btn btn-default", "title" => app_lang('add_features_types'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="feature-types-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#feature-types-table").appTable({
        source: '<?php echo_uri("features/feature_type_list_data") ?>',
        columns: [
            {title: "<?php echo app_lang('title') ?> "},
            {title: "<?php echo app_lang('order') ?> ", "class": "text-center"},
            {title: "<i data-feather='menu' class='icon-16'></i>","class": "text-center option w100"}
        ],
        printColumns: [0, 1, 2],
        xlsColumns: [0, 1, 2]
    });
});
</script>