<div class="modal-body clearfix general-form">
  <div class="p10 clearfix">
    <h4 class="mb-3">
      <?php echo $user_detail->first_name . ' ' . $user_detail->last_name; ?>
    </h4>
    <div class="table-responsive">
      <table class="table table-striped" style="text-align: center;">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Invoice Id</th>
            <th scope="col">Amount</th>
            <th scope="col">Status</th>
            <th scope="col">Date</th>
            <th scope="col">View Invoice</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($all_invoices)): ?>
            <?php $i = 1; ?>
            <?php foreach ($all_invoices as $invoice): ?>
              <?php extract($invoice); ?>
              <tr>
                <th><?php echo $i; ?></th>
                <td class="text-capitalize"><?php echo $invoice_title; ?></td>
                <td><?php echo $invoice_id ?></td>
                <td><?php echo "$" . $amount_due / 100; ?></td>
                <?php if ($status == 'paid' || $status == 'succeeded'): ?>
                  <td><span class="badge bg-success" style="background: #0abb87 !important; color: white;"><?php echo $status; ?></span></td>
                <?php else: ?>
                  <td><span class="badge bg-danger" style="color: white;"><?php echo $status; ?></span></td>
                <?php endif; ?>
                <td><?php echo $status_date; ?></td>
                <td><a href="<?php echo $url ?>" target="_blank" class="btn btn-primary btn-sm">View Invoice</a></td>
              </tr>
              <?php $i++; ?>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7">No invoices found for this order.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
</div>
