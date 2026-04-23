 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <h4 class="m-3" >
    <?php echo $user_detail->first_name.' '.$user_detail->last_name; ?>
  </h4>
  <a href="<?php echo "http://".$order_detail->domain_name."/index.php/login/user_email/".$user_detail->email."/autologin/1"; ?>" class="m-3 fw-bold" style="text-decoration: none;">
    <?php echo "http://".$order_detail->domain_name."/index.php/admin" ?>
  <a>
<div class="m-3">
<table class="table table-striped" style = "text-align: center;">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Title</th>
      <th scope="col">Invoice Id</th>
      <th scope="col">Amount</th>
      <th scope="col">interval</th>
      <th scope="col">Status</th>
      <th scope="col">Date</th>
      <th scope="col">View Invoice</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
  	<?php $i=1; ?>
  	<?php foreach($all_invoices as $invoice): ?>
  	<?php extract($invoice);?>
    <tr>
      <th><?php echo $i; ?></th>  
      <td class="text-capitalize" ><?php echo $invoice_title; ?></td>	
      <td><?php echo $invoice_id ?></td>
      <td><?php echo "$".$amount_due/100;?></td>
      <td class="text-capitalize" ><?php echo $interval ?></td>
      <?php if($status  == 'paid'): ?>
      	<td><span class="badge text-bg-success text-capitalize"><?php echo $status; ?></span></td>
      <?php else: ?>
        <td><span class="badge text-bg-danger text-capitalize"><?php echo $status; ?></span></td>
      <?php endif;?>
      	<td><?php echo $status_date; ?></td>
      <td><a href="<?php echo $url ?>" target = "_blank" class="btn btn-outline-primary" >View Invoice</a></td>
    </tr>
    <?php $i++; ?>
	<?php endforeach;?>
  </tbody>
</table>
</div>