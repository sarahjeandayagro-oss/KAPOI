<?php
  $showActions = $showActions ?? false;
  $emptyMessage = $emptyMessage ?? 'No borrow requests yet.';
  $statusColumn = $statusColumn ?? false;
  $showArchiveAction = $showArchiveAction ?? false;
  $showRejectionReason = $showRejectionReason ?? false;
?>
<table>
  <thead>
    <tr>
      <th>User</th>
      <th>Email</th>
      <th>Role</th>
      <th>Book</th>
      <th>Accession</th>
      <th>Requested At</th>
      <?php if($statusColumn): ?>
        <th>Status</th>
        <?php if($showRejectionReason): ?>
          <th>Rejection Reason</th>
        <?php endif; ?>
        <?php if($showArchiveAction): ?>
          <th>Action</th>
        <?php endif; ?>
      <?php elseif($showActions): ?>
        <th>Action</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($request->user_name ?? 'Unknown'); ?><br><span class="text-muted"><?php echo e($request->user_id); ?></span></td>
        <td><?php echo e($request->user_email ?? 'n/a'); ?></td>
        <td><?php echo e(ucfirst($request->user_role ?? 'student')); ?></td>
        <td><?php echo e($request->title ?? 'Unknown book'); ?><br><span class="text-muted"><?php echo e($request->author); ?></span></td>
        <td><?php echo e($request->accession_number ?? 'N/A'); ?></td>
        <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A')); ?></td>
        <?php if($statusColumn): ?>
          <td>
            <span class="status-chip <?php echo e(in_array(strtolower($request->status), ['approved', 'borrowed']) ? 'approved' : (strtolower($request->status) === 'rejected' ? 'danger' : 'pending')); ?>">
              <?php echo e(ucfirst($request->status ?? 'Approved')); ?>

            </span>
          </td>
          <?php if($showRejectionReason): ?>
            <td><?php echo e($request->rejection_reason ?? 'No reason provided.'); ?></td>
          <?php endif; ?>
          <?php if($showArchiveAction): ?>
            <td style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
              <?php if(empty($request->is_dummy)): ?>
                <form method="POST" action="<?php echo e(route('admin.borrow-requests.archive', $request->id)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn-sm" type="submit">Archive</button>
                </form>
              <?php else: ?>
                <span class="status-chip pending">Sample request</span>
              <?php endif; ?>
            </td>
          <?php endif; ?>
        <?php elseif($showActions): ?>
          <td style="display:flex; gap:8px; flex-wrap:wrap; align-items:flex-start;">
            <?php if(empty($request->is_dummy)): ?>
              <form method="POST" action="<?php echo e(route('admin.borrow-requests.approve', $request->id)); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="borrowing_transaction_id" value="<?php echo e($request->id); ?>">
                <button class="btn-sm" type="submit">Approve</button>
              </form>
              <form method="POST" action="<?php echo e(route('admin.borrow-requests.archive', $request->id)); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn-sm" type="submit">Archive</button>
              </form>
              <form method="POST" action="<?php echo e(route('admin.borrow-requests.reject', $request->id)); ?>" style="display:flex; flex-direction:column; gap:6px; max-width:240px;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="borrowing_transaction_id" value="<?php echo e($request->id); ?>">
                <textarea name="reason" placeholder="Reject reason" required style="width:220px; min-height:60px; padding:8px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;"></textarea>
                <button class="btn-sm danger" type="submit">Reject</button>
              </form>
            <?php else: ?>
              <span class="status-chip pending">Sample request</span>
            <?php endif; ?>
          </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" style="text-align:center;"><?php echo e($emptyMessage); ?></td></tr>
    <?php endif; ?>
  </tbody>
</table>
<?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/components/borrow-requests-table.blade.php ENDPATH**/ ?>