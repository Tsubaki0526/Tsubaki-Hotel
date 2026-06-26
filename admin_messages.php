<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('messages_title'); ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('messages_inbox'); ?></div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['msg_deleted'])) echo "<div class='alert alert-success'>" . __('messages_deleted') . "</div>";
                    ?>
                    <table class="table table-striped table-bordered" id="rooms">
                        <thead>
                            <tr><th><?php _e('messages_no'); ?></th><th><?php _e('messages_name'); ?></th><th><?php _e('messages_email'); ?></th><th><?php _e('messages_phone'); ?></th><th><?php _e('messages_message'); ?></th><th><?php _e('messages_date'); ?></th><th><?php _e('actions'); ?></th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $msgs = mysqli_query($connection, "SELECT * FROM contact_messages ORDER BY created_at DESC");
                        $num = 0;
                        while ($m = mysqli_fetch_assoc($msgs)):
                            $num++;
                        ?>
                            <tr>
                                <td><?php echo $num; ?></td>
                                <td><?php echo htmlspecialchars($m['name']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($m['email']); ?>"><?php echo htmlspecialchars($m['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($m['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars(substr($m['message'] ?? '', 0, 80)); ?>...</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($m['created_at'])); ?></td>
                                <td>
                                    <a href="#viewMsgModal" class="btn btn-info" style="border-radius:60px;" data-bs-toggle="modal"
                                       data-name="<?php echo htmlspecialchars($m['name']); ?>"
                                       data-email="<?php echo htmlspecialchars($m['email']); ?>"
                                       data-phone="<?php echo htmlspecialchars($m['phone'] ?? '-'); ?>"
                                       data-msg="<?php echo htmlspecialchars($m['message']); ?>"
                                       data-date="<?php echo date('d/m/Y H:i', strtotime($m['created_at'])); ?>"
                                       id="viewMsgBtn"><i class="fa fa-eye"></i></a>
                                    <a href="ajax.php?delete_message=<?php echo htmlspecialchars($m['id']); ?>&csrf=<?php echo csrf_token(); ?>" class="btn btn-danger" style="border-radius:60px;" onclick="return confirm('<?php _e('confirm_delete'); ?>')"><i class="fa fa-trash"></i></a>
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

<!-- Modal Ver Mensaje -->
<div id="viewMsgModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('messages_view_title'); ?></h4>
            </div>
            <div class="modal-body">
                <p><strong><?php _e('messages_name'); ?>:</strong> <span id="msg_name"></span></p>
                <p><strong><?php _e('messages_email'); ?>:</strong> <span id="msg_email"></span></p>
                <p><strong><?php _e('messages_phone'); ?>:</strong> <span id="msg_phone"></span></p>
                <p><strong><?php _e('messages_date'); ?>:</strong> <span id="msg_date"></span></p>
                <hr>
                <p><strong><?php _e('messages_content'); ?>:</strong></p>
                <p id="msg_content" style="background:#f5f5f5;padding:15px;border-radius:5px;"></p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '#viewMsgBtn', function() {
    $('#msg_name').text($(this).data('name'));
    $('#msg_email').text($(this).data('email'));
    $('#msg_phone').text($(this).data('phone'));
    $('#msg_date').text($(this).data('date'));
    $('#msg_content').text($(this).data('msg'));
});
</script>

