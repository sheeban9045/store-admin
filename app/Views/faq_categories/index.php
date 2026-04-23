<style>
    @media (min-width: 992px){
        .modal-lg, .modal-xl {
    max-width: 875px;
}
   
}
td.option a.undo-delete:hover{
    background-color: #f1c40f;
    border-color: #f1c40f;
}
    </style>
<div class="modal-body clearfix">
    <div class="container-fluid">
    <div class="card">
                <div class="page-title clearfix">
                    <h4> Faq category</h4>
                    <div class="title-button-group">
                        <?php echo modal_anchor(get_uri("faq_categories/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_category'), array("class" => "btn btn-default", "title" => app_lang('add_category'))); ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="category-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#category-table").appTable({
            source: '<?php echo_uri("faq_categories/list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });

   
    });

</script>