<?php
    $qty_min_order = get_setting('qty_min_order');
    $qty_max_order = get_setting('qty_max_order');
?>
   
<table id="order-item-table" class="mt0 table dataTable text-right strong table-responsive">
 
    <?php if ($order_total_summary->tax) { ?>
        <tr>
            <td></td>
            <td></td>
            <td><?php echo $order_total_summary->tax_name; ?></td>
            <td><?php echo to_currency($order_total_summary->tax); ?></td>
            <td></td>
        </tr>
    <?php } ?>
    <?php if ($order_total_summary->tax2) { ?>
        <tr>
            <td></td>
            <td></td>
            <td><?php echo $order_total_summary->tax_name2; ?></td>
            <td><?php echo to_currency($order_total_summary->tax2); ?></td>
            <td></td>
        </tr>
    <?php } ?>



    
    

 
       
  



     


    
</table>