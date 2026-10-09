<style>
    .view-service-details {
        background-color: transparent !important;
        color: #283747 !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        text-decoration: none;
        -webkit-tap-highlight-color: transparent;
    }

    .view-service-details:hover,
    .view-service-details:focus,
    .view-service-details:active,
    .view-service-details:focus-visible {
        background-color: transparent !important;
        color: #283747 !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        text-decoration: none;
    }
</style>
<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1>My Services</h1>
            <div class="title-button-group">
                <a href="https://webhut.net/services.php" class="btn btn-primary">
                    <i data-feather="plus-circle" class="icon-16"></i> Browse Services
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table id="my-services-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#my-services-table").appTable({
            source: '<?php echo_uri("Frontend_services/my_services_list_data") ?>',
            order: [[2, "desc"]],
            columns: [
                {title: "Image", "class": "w10p text-center"},
                {title: "Service Name", "class": "w25p"},
                {title: "Order", "class": "w10p"},
                {title: "Amount Paid", "class": "w10p text-right"},
                {title: "Purchase Date", "class": "w20p"},
                {title: "Payment Status", "class": "w10p text-center"},
                {title: "Action", "class": "text-center option w15p"}
            ],
            onInitComplete: function () {
                var table = $("#my-services-table").DataTable();
                if (!table.data().any()) {
                    var emptyMessage = `
                        <div class="text-center p20">
                            <h4>You haven't purchased any services yet.</h4>
                        </div>
                    `;
                    $("#my-services-table").parent().html(emptyMessage);
                }
            }
        });
    });
</script>
