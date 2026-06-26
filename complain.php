<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active"><?php _e('complaint_title') ?></li>
        </ol>
    </div>

    

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('complaint_register') ?></div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['error'])) {
                        echo "<div class='alert alert-danger'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('complaint_resolve_error') . "
                            </div>";
                    }
                    if (isset($_GET['success'])) {
                        echo "<div class='alert alert-success'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('complaint_resolved') . "
                            </div>";
                    }
                    ?>
                    <form role="form"  data-toggle="validator" method="post" action="ajax.php">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label><?php _e('complaint_complainant') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('form_name_placeholder') ?>" name="complainant_name" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('complaint_type') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('form_type_placeholder') ?>" name="complaint_type" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label><?php _e('complaint_describe') ?></label>
                                <textarea class="form-control" name="complaint" placeholder="<?php _e('form_complaint_placeholder') ?>" required></textarea>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-lg btn-success" name="createComplaint" style="border-radius:60px;"><?php _e('complaint_submit') ?></button>
                        <button type="reset" class="btn btn-lg btn-danger" style="border-radius:60px;"><?php _e('reset') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('complaint_management') ?></div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['resolveError'])) {
                        echo "<div class='alert alert-danger'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('complaint_resolve_error') . "
                            </div>";
                    }
                    if (isset($_GET['resolveSuccess'])) {
                        echo "<div class='alert alert-success'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('complaint_resolved') . "
                            </div>";
                    }
                    ?>
                    <div class="table-responsive"><table class="table table-striped table-bordered" cellspacing="0" width="100%" id="rooms">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php _e('complaint_complainant') ?></th>
                            <th><?php _e('complaint_type') ?></th>
                            <th><?php _e('complaint_describe') ?></th>
                            <th><?php _e('complaint_date') ?></th>
                            <th><?php _e('complaint_resolution') ?></th>
                            <th><?php _e('complaint_budget') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $complaint_query = "SELECT * FROM complaint";
                        $complaint_result = mysqli_query($connection, $complaint_query);
                        if (mysqli_num_rows($complaint_result) > 0) {
                            $num = 0;
                            while ($complaint = mysqli_fetch_assoc($complaint_result)) {
                                $num++;
                                ?>
                                <tr>
                                    <td><?php echo $num ?></td>
                                    <td><?php echo htmlspecialchars($complaint['complainant_name']) ?></td>
                                    <td><?php echo htmlspecialchars($complaint['complaint_type']) ?></td>
                                    <td><?php echo htmlspecialchars($complaint['complaint']) ?></td>
                                    <td><?php echo date('M j, Y',strtotime($complaint['created_at'])) ?></td>
                                    <td>
                                        <?php if(!$complaint['resolve_status']){
                                            echo '<button class="btn btn-info" data-bs-toggle="modal" style="border-radius:60px;" data-bs-target="#complaintModal" data-id="' . htmlspecialchars($complaint['id']) . '" id="complaint">' . __('complaint_resolve') . '</button>';
                                        } else{
                                            echo date('M j, Y',strtotime($complaint['resolve_date']));
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($complaint['budget']) ?></td>


                                </tr>
                            <?php }
                        } else {
                            echo __('no_records');
                        }
                        ?>

                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="complaintModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="modal-title"><?php _e('complaint_modal_title') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form data-toggle="validator" role="form" method="post" action="ajax.php">
                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                <div class="form-group">
                                    <label><?php _e('complaint_budget') ?></label>
                                    <input class="form-control" placeholder="<?php _e('complaint_budget') ?>" name="budget" data-error="<?php _e('field_required') ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <input type="hidden" id="complaint_id" name="complaint_id" value="">
                                <button class="btn btn-success float-end" name="resolve_complaint"><?php _e('complaint_resolve') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



</div>    
