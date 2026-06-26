<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('blog_title'); ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('blog_manage'); ?>
                    <button class="btn btn-secondary float-end" style="border-radius:60px;" data-bs-toggle="modal" data-bs-target="#addBlogModal"><?php _e('blog_new'); ?></button>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['blog_success'])) {
                        echo "<div class='alert alert-success'>" . __('blog_saved') . "</div>";
                    }
                    if (isset($_GET['blog_error'])) {
                        echo "<div class='alert alert-danger'>" . __('blog_error') . "</div>";
                    }
                    if (isset($_GET['blog_deleted'])) {
                        echo "<div class='alert alert-success'>" . __('blog_deleted') . "</div>";
                    }
                    ?>
                    <table class="table table-striped table-bordered" id="rooms">
                        <thead>
                            <tr>
                                <th><?php _e('blog_id'); ?></th>
                                <th><?php _e('blog_title_label'); ?></th>
                                <th><?php _e('blog_slug'); ?></th>
                                <th><?php _e('blog_date'); ?></th>
                                <th><?php _e('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $posts = getBlogPosts(100);
                        foreach ($posts as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['id']); ?></td>
                                <td><?php echo htmlspecialchars($p['title']); ?></td>
                                <td><?php echo htmlspecialchars($p['slug']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                                <td>
                                    <a href="#editBlogModal" class="btn btn-info" style="border-radius:60px;" data-bs-toggle="modal"
                                       data-id="<?php echo htmlspecialchars($p['id']); ?>"
                                       data-title="<?php echo htmlspecialchars($p['title']); ?>"
                                       data-slug="<?php echo htmlspecialchars($p['slug']); ?>"
                                       data-excerpt="<?php echo htmlspecialchars($p['excerpt']); ?>"
                                       data-content="<?php echo htmlspecialchars($p['content']); ?>"
                                       data-color="<?php echo htmlspecialchars($p['color']); ?>"
                                       data-color2="<?php echo htmlspecialchars($p['color2']); ?>"
                                       id="editBlogBtn"><i class="fa fa-pencil"></i></a>
                                    <a href="ajax.php?delete_blog=<?php echo htmlspecialchars($p['id']); ?>&csrf=<?php echo csrf_token(); ?>" class="btn btn-danger" style="border-radius:60px;" onclick="return confirm('<?php _e('confirm_delete'); ?>')"><i class="fa fa-trash"></i></a>
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

<!-- Modal Agregar Blog -->
<div id="addBlogModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('blog_new_title'); ?></h4>
            </div>
            <form action="ajax.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php _e('blog_title_label'); ?></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><?php _e('blog_slug_auto'); ?></label>
                        <input type="text" name="slug" class="form-control" placeholder="<?php _e('blog_placeholder_slug'); ?>">
                    </div>
                    <div class="form-group">
                        <label><?php _e('blog_excerpt'); ?></label>
                        <textarea name="excerpt" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php _e('blog_content'); ?></label>
                        <textarea name="content" class="form-control" rows="8" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('blog_color_primary'); ?></label>
                                <input type="color" name="color" class="form-control" value="#1a5276" style="height:40px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('blog_color_secondary'); ?></label>
                                <input type="color" name="color2" class="form-control" value="#2980b9" style="height:40px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="save_blog"><?php _e('blog_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Blog -->
<div id="editBlogModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title"><?php _e('blog_edit_title'); ?></h4>
            </div>
            <form action="ajax.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="blog_id" id="edit_blog_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php _e('blog_title_label'); ?></label>
                        <input type="text" name="title" id="edit_blog_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><?php _e('blog_slug'); ?></label>
                        <input type="text" name="slug" id="edit_blog_slug" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?php _e('blog_excerpt'); ?></label>
                        <textarea name="excerpt" id="edit_blog_excerpt" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php _e('blog_content'); ?></label>
                        <textarea name="content" id="edit_blog_content" class="form-control" rows="8" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('blog_color_primary'); ?></label>
                                <input type="color" name="color" id="edit_blog_color" class="form-control" style="height:40px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('blog_color_secondary'); ?></label>
                                <input type="color" name="color2" id="edit_blog_color2" class="form-control" style="height:40px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="edit_blog"><?php _e('blog_save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).on('click', '#editBlogBtn', function() {
    $('#edit_blog_id').val($(this).data('id'));
    $('#edit_blog_title').val($(this).data('title'));
    $('#edit_blog_slug').val($(this).data('slug'));
    $('#edit_blog_excerpt').val($(this).data('excerpt'));
    $('#edit_blog_content').val($(this).data('content'));
    $('#edit_blog_color').val($(this).data('color'));
    $('#edit_blog_color2').val($(this).data('color2'));
});
</script>
