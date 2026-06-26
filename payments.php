<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="index.php?dashboard"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('payment_history') ?></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-credit-card"></i> <?php _e('payment_history') ?>
                    <a href="index.php?room_mang" class="btn btn-sm btn-outline-primary float-end">
                        <i class="fa fa-bed"></i> <?php _e('nav_rooms') ?>
                    </a>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered" id="paymentsTable">
                        <thead>
                            <tr>
                                <th><?php _e('payment_invoice') ?></th>
                                <th><?php _e('payment_customer') ?></th>
                                <th><?php _e('payment_room') ?></th>
                                <th><?php _e('payment_amount') ?></th>
                                <th><?php _e('payment_method') ?></th>
                                <th><?php _e('payment_date') ?></th>
                                <th><?php _e('payment_notes') ?></th>
                                <th><?php _e('payment_actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $pq = mysqli_query($connection, "
                            SELECT p.*, b.invoice_no, c.customer_name, r.room_no, b.booking_id
                            FROM payments p
                            JOIN booking b ON p.booking_id = b.booking_id
                            JOIN customer c ON b.customer_id = c.customer_id
                            JOIN room r ON b.room_id = r.room_id
                            ORDER BY p.payment_date DESC
                        ");
                        while ($p = mysqli_fetch_assoc($pq)):
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['invoice_no'] ?? 'INV-' . str_pad($p['booking_id'], 5, '0', STR_PAD_LEFT)); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['customer_name']); ?></td>
                                <td>N° <?php echo htmlspecialchars($p['room_no']); ?></td>
                                <td><strong>$<?php echo number_format($p['amount'], 2); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['payment_method']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($p['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($p['notes'] ?: '-'); ?></td>
                                <td>
                                    <a href="invoice.php?booking_id=<?php echo htmlspecialchars($p['booking_id']); ?>" class="btn btn-info btn-sm" style="border-radius:60px;" title="<?php _e('payment_view_invoice') ?>">
                                        <i class="fa fa-file-text"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
