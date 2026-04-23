<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('faqs'); ?></h1>
            <div class="title-button-group">
                        <?php echo modal_anchor(get_uri("faq_categories"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('manage_faqs_cat'), array("class" => "btn btn-default", "title" => app_lang('manage_faqs_cat'))); ?>
                        <?php echo modal_anchor(get_uri("faqs/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_faqs'), array("class" => "btn btn-default", "title" => app_lang('add_faqs'))); ?>
                    </div>
        </div>
        <div class="table-responsive">
            <table id="faqs-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#faqs-table").appTable({
            source: '<?php echo_uri("faqs/list_data") ?>',
            order: [[0, 'desc']],
            
            columns: [
                {title: "<?php echo app_lang('que') ?> ", "class": "w20p"},
                {title: "<?php echo app_lang('ans') ?>"},
                {title: "Category"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2],
            xlsColumns: [0, 1, 2]
        });
    });
</script>