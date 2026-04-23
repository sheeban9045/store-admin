<div class="card mb-0">
    <div class="table-responsive ">
        <table id="client-table" class="display client-table-res" cellspacing="0" width="100%">            
        </table>
    </div>
</div>
<style type="text/css">
body:not(.sidebar-toggled) .client-table-res {
    max-height: calc(100vh - 410px);
    overflow: auto;
    display: block;
}

body:not(.sidebar-toggled) .page-wrapper {
    padding: 0;
}

body:not(.sidebar-toggled) div#page-content > div {
    margin: 15px;
}

body:not(.sidebar-toggled) .ps__rail-y {
    display: none !important;
    height: 0 !important;
    overflow: hidden !important;
}


.zoomActive .client-table-res {
    max-height: calc(100vh - 220px) !important;
    overflow: auto;
    display: block;
}

.zoomActive .page-wrapper {
    padding: 0;
}

.zoomActive div#page-content > div {
    margin: 15px;
}

.zoomActive .ps__rail-y {
    display: none !important;
    height: 0 !important;
    overflow: hidden !important;
}
.zoom.zoomActive {
    background: #fff;
    overflow: auto;
    padding: 10px;

}
</style>
<script type="text/javascript">
    loadClientsTable = function (selector) {
    var showInvoiceInfo = true;
    if (!"<?php echo $show_invoice_info; ?>") {
    showInvoiceInfo = false;
    }

    var showOptions = true;
    if (!"<?php echo $can_edit_clients; ?>") {
    showOptions = false;
    }

    var quick_filters_dropdown = <?php echo view("clients/quick_filters_dropdown"); ?>;
    if (window.selectedClientQuickFilter){
    var filterIndex = quick_filters_dropdown.findIndex(x => x.id === window.selectedClientQuickFilter);
    if ([filterIndex] > - 1){
    //match found
    quick_filters_dropdown[filterIndex].isSelected = true;
    }
    }

    $(selector).appTable({
    source: '<?php echo_uri("clients/list_data") ?>',
            filterDropdown: [
            {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
            {name: "quick_filter", class: "w200", options: quick_filters_dropdown}
<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
                , {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>}
<?php } ?>
            ,<?php echo $custom_field_filters; ?>
            ],
            columns: [
            {title: "<?php echo app_lang("id") ?>", "class": "text-center w50"},
            {title: "<?php echo app_lang("company_name") ?>"},
            {title: "<?php echo app_lang("primary_contact") ?>"},
            {title: "<?php echo app_lang("date") ?>"},
            {title: "<?php echo app_lang("client_groups") ?>"},
            {title: "<?php echo app_lang("projects") ?>"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang("total_invoiced") ?>"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang("payment_received") ?>"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang("due") ?>"}
<?php echo $custom_field_headers; ?>,
            {title: "<?php echo "login as client";?>"},
            {title: "<?php echo "Community";?>"},
            {title: "<?php echo "Community Admin";?>"},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>')
    });
    document.querySelector('th[colspan="1"][aria-sort="ascending"]')?.click();
    };
    $(document).ready(function () {
    loadClientsTable("#client-table");
    });
</script>