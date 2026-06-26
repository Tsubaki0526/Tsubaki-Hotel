<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('room_types_title'); ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('room_types_manage'); ?>
                    <button class="btn btn-secondary float-end" style="border-radius:60px;" data-bs-toggle="modal" data-bs-target="#addRoomTypeModal"><?php _e('room_types_new'); ?></button>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['rt_success'])) echo "<div class='alert alert-success'>" . __('room_types_saved') . "</div>";
                    if (isset($_GET['rt_error'])) echo "<div class='alert alert-danger'>" . __('room_types_error') . "</div>";
                    ?>
                    <table class="table table-striped table-bordered" id="rooms">
                        <thead>
                            <tr>
                                <th><?php _e('room_types_id'); ?></th>
                                <th><?php _e('room_types_image'); ?></th>
                                <th><?php _e('room_types_name'); ?></th>
                                <th><?php _e('room_types_price'); ?></th>
                                <th><?php _e('room_types_persons'); ?></th>
                                <th><?php _e('room_types_description'); ?></th>
                                <th><?php _e('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $types = getRoomTypes();
                        foreach ($types as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['room_type_id']); ?></td>
                                <td>
                                    <?php if (!empty($t['image'])): 
                                        $img_src = (strpos($t['image'], 'http') === 0) ? $t['image'] : 'uploads/' . $t['image'];
                                    ?>
                                        <img src="<?php echo htmlspecialchars($img_src); ?>" style="width:80px;height:50px;object-fit:cover;border-radius:4px;">
                                    <?php else: ?>
                                        <span class="text-muted"><?php _e('room_types_no_image'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($t['room_type']); ?></strong></td>
                                <td>$<?php echo number_format($t['price']); ?></td>
                                <td><?php echo htmlspecialchars($t['max_person']); ?></td>
                                <td><?php echo htmlspecialchars(substr($t['description'] ?? '', 0, 50)); ?>...</td>
                                <td>
                                    <a href="#editRoomTypeModal" class="btn btn-info" style="border-radius:60px;" data-bs-toggle="modal"
                                       data-id="<?php echo htmlspecialchars($t['room_type_id']); ?>"
                                       data-type="<?php echo htmlspecialchars($t['room_type']); ?>"
                                       data-price="<?php echo htmlspecialchars($t['price']); ?>"
                                       data-max="<?php echo htmlspecialchars($t['max_person']); ?>"
                                       data-desc="<?php echo htmlspecialchars($t['description'] ?? ''); ?>"
                                       data-image="<?php echo htmlspecialchars($t['image'] ?? ''); ?>"
                                       data-amenities="<?php echo htmlspecialchars($t['amenities'] ?? ''); ?>"
                                       id="editRoomTypeBtn"><i class="fa fa-pencil"></i></a>
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

<!-- Modal Agregar Tipo -->
<div id="addRoomTypeModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('room_types_new_title'); ?></h4>
            </div>
            <form action="ajax.php" method="post" enctype="multipart/form-data" id="addRoomForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('room_types_name_label'); ?></label>
                                <input type="text" name="room_type" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php _e('room_types_price_per_night'); ?></label>
                                <input type="number" name="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php _e('room_types_max_persons'); ?></label>
                                <input type="number" name="max_person" class="form-control" value="2" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php _e('room_types_description'); ?></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="<?php _e('form_description_placeholder') ?>"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php _e('room_types_image_upload'); ?></label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                        <small><?php _e('room_types_image_formats'); ?></small>
                    </div>
                    <div class="form-group">
                        <label><?php _e('room_types_amenities'); ?></label>
                        <textarea name="amenities" class="form-control" rows="2" placeholder="<?php _e('room_types_amenities_placeholder'); ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="save_room_type"><?php _e('room_types_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Tipo -->
<div id="editRoomTypeModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('room_types_edit_title'); ?></h4>
            </div>
            <form action="ajax.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="room_type_id" id="edit_rt_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('room_types_name_label'); ?></label>
                                <input type="text" name="room_type" id="edit_rt_type" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php _e('room_types_price_per_night'); ?></label>
                                <input type="number" name="price" id="edit_rt_price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php _e('room_types_max_persons'); ?></label>
                                <input type="number" name="max_person" id="edit_rt_max" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php _e('room_types_description'); ?></label>
                        <textarea name="description" id="edit_rt_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php _e('room_types_image_current'); ?></label>
                        <div id="edit_rt_image_preview" style="margin-bottom:5px;">
                            <span class="text-muted" id="edit_rt_image_name"><?php _e('room_types_none'); ?></span>
                        </div>
                        <input type="file" name="image" accept="image/*" class="form-control">
                        <small><?php _e('room_types_keep_image'); ?></small>
                    </div>
                    <div class="form-group">
                        <label><?php _e('room_types_amenities'); ?></label>
                        <textarea name="amenities" id="edit_rt_amenities" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="edit_room_type"><?php _e('room_types_save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).on('click', '#editRoomTypeBtn', function() {
    $('#edit_rt_id').val($(this).data('id'));
    $('#edit_rt_type').val($(this).data('type'));
    $('#edit_rt_price').val($(this).data('price'));
    $('#edit_rt_max').val($(this).data('max'));
    $('#edit_rt_desc').val($(this).data('desc'));
    $('#edit_rt_amenities').val($(this).data('amenities'));
    var img = $(this).data('image');
    if (img) {
        var imgSrc = img.indexOf('http') === 0 ? img : 'uploads/' + img;
        $('#edit_rt_image_name').html('<img src="' + imgSrc + '" style="height:60px;border-radius:4px;"> <small>' + img + '</small>');
    } else {
        $('#edit_rt_image_name').text('<?php _e('room_types_none'); ?>');
    }
});
</script>
