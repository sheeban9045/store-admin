<?php
$statuses = array();
foreach ($order_statuses as $status) {
    $statuses[] = array("id" => $status->id, "text" => $status->title);
}
?>

<script type="text/javascript">
    $(document).ready(function () {
        var detailsView = false;
<?php if (isset($details_view)) { ?>
            detailsView = true;
<?php } ?>

        $('body').on('click', '[data-act=update-order-status]', function () {
            var $instance = $(this);

            $(this).appModifier({
                value: $(this).attr('data-value'),
                actionUrl: '<?php echo_uri("orders/save_order_status") ?>/' + $(this).attr('data-id'),
                placement: detailsView ? "right" : "auto",
                select2Option: {data: <?php echo json_encode($statuses) ?>},
                onSuccess: function (response, newValue) {
                    if (response.success) {
                        if (detailsView) {
                            $instance.css("background-color", response.order_status_color);
                        } else {
                            $(".dataTable:visible").appTable({newData: response.data, dataId: response.id});
                        }
                    }
                }


<?php
    function get_absolute_path($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }
?>
                
            });

            return false;
        });
    });
</script>