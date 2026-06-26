<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('services_title'); ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('services_manage'); ?>
                    <button class="btn btn-secondary float-end" style="border-radius:60px;" data-bs-toggle="modal" data-bs-target="#addServiceModal"><?php _e('services_new'); ?></button>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['srv_success'])) echo "<div class='alert alert-success'>" . __('services_saved') . "</div>";
                    if (isset($_GET['srv_error'])) echo "<div class='alert alert-danger'>" . __('services_error') . "</div>";
                    if (isset($_GET['srv_deleted'])) echo "<div class='alert alert-success'>" . __('services_deleted') . "</div>";
                    ?>
                    <table class="table table-striped table-bordered" id="rooms">
                        <thead>
                            <tr><th><?php _e('services_id'); ?></th><th><?php _e('services_icon'); ?></th><th><?php _e('services_title_label'); ?></th><th><?php _e('services_description'); ?></th><th><?php _e('actions'); ?></th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $svrs = getServices();
                        foreach ($svrs as $s): ?>
                            <tr>
                                <td><?php echo $s['id']; ?></td>
                                <td><i class="fas fa-<?php echo htmlspecialchars($s['icon']); ?> fa-2x" style="color:var(--primary);"></i></td>
                                <td><?php echo htmlspecialchars($s['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($s['description'], 0, 60)); ?>...</td>
                                <td>
                                    <a href="#editServiceModal" class="btn btn-info" style="border-radius:60px;" data-bs-toggle="modal"
                                       data-id="<?php echo $s['id']; ?>"
                                       data-title="<?php echo htmlspecialchars($s['title']); ?>"
                                       data-desc="<?php echo htmlspecialchars($s['description']); ?>"
                                       data-icon="<?php echo htmlspecialchars($s['icon']); ?>"
                                       id="editSrvBtn"><i class="fa fa-pencil"></i></a>
                                    <a href="ajax.php?delete_service=<?php echo $s['id']; ?>&csrf=<?php echo csrf_token(); ?>" class="btn btn-danger" style="border-radius:60px;" onclick="return confirm('<?php _e('confirm_delete'); ?>')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Servicio -->
<div id="addServiceModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('services_new_title'); ?></h4>
            </div>
            <form action="ajax.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php _e('services_title_label'); ?></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><?php _e('services_description'); ?></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php _e('services_icon_help'); ?></label>
                        <input type="text" name="icon" class="form-control" placeholder="wifi" required>
                        <small><?php _e('services_icon_search'); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="save_service"><?php _e('services_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Servicio -->
<div id="editServiceModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('services_edit_title'); ?></h4>
            </div>
            <form action="ajax.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="service_id" id="edit_srv_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php _e('services_title_label'); ?></label>
                        <input type="text" name="title" id="edit_srv_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><?php _e('services_description'); ?></label>
                        <textarea name="description" id="edit_srv_desc" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php _e('services_icon'); ?></label>
                        <input type="text" name="icon" id="edit_srv_icon" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="edit_service"><?php _e('services_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).on('click', '#editSrvBtn', function() {
    $('#edit_srv_id').val($(this).data('id'));
    $('#edit_srv_title').val($(this).data('title'));
    $('#edit_srv_desc').val($(this).data('desc'));
    $('#edit_srv_icon').val($(this).data('icon'));
});
</script>
