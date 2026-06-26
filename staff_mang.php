<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active"><?php _e('nav_staff') ?></li>
        </ol>
    </div>

   

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('staff_details') ?>
                    <a href="index.php?add_emp" class="btn btn-secondary float-end" style="border-radius:60px;"><?php _e('staff_add') ?></a>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['error'])) {
                        echo "<div class='alert alert-danger'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('staff_shift_error') . "
                            </div>";
                    }
                    if (isset($_GET['success'])) {
                        echo "<div class='alert alert-success'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('staff_shift_changed') . "
                            </div>";
                    }
                    ?>
                    <div class="table-responsive"><table class="table table-striped table-bordered" cellspacing="0" width="100%"
                           id="rooms">
                        <thead>
                        <tr>
                            <th><?php _e('staff_no') ?></th>
                            <th><?php _e('staff_name') ?></th>
                            <th><?php _e('staff_position') ?></th>
                            <th><?php _e('staff_shift') ?></th>
                            <th><?php _e('staff_joining') ?></th>
                            <th><?php _e('staff_salary') ?></th>
                            <th><?php _e('staff_change_shift') ?></th>
                            <th><?php _e('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $staff_query = "SELECT * FROM staff NATURAL JOIN staff_type NATURAL JOIN shift";
                        $staff_result = mysqli_query($connection, $staff_query);

                        if (mysqli_num_rows($staff_result) > 0) {
                            while ($staff = mysqli_fetch_assoc($staff_result)) { ?>
                                <tr>

                                    <td><?php echo $staff['emp_id']; ?></td>
                                    <td><?php echo htmlspecialchars($staff['emp_name']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['staff_type']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['shift'] . ' - ' . $staff['shift_timing']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($staff['joining_date'])); ?></td>
                                    <td><?php echo $staff['salary']; ?></td>
                                    <td>
                                        <button class="btn btn-warning" style="border-radius:60px;" data-bs-toggle="modal" data-bs-target="#changeShift"
                                                data-id="<?php echo $staff['emp_id']; ?>" id="change_shift"><?php _e('staff_change_shift') ?></button>
                                    </td>
                                    <td>

                                        <button data-bs-toggle="modal"
                                                data-bs-target="#empDetail<?php echo $staff['emp_id']; ?>"
                                                data-id="<?php echo $staff['emp_id']; ?>" id="editEmp"
                                                class="btn btn-info" style="border-radius:60px;"><i class="fa fa-pencil"></i></button>
                                        <a href='functionmis.php?empid=<?php echo $staff['emp_id']; ?>'
                                           class="btn btn-danger" onclick="return confirm('<?php _e('confirm_delete') ?>')" style="border-radius:60px;"><i
                                                     class="fa fa-trash"></i></a>
                                        <a href='index.php?emp_history&empid=<?php echo $staff['emp_id']; ?>'
                                           class="btn btn-success" title="<?php _e('staff_history') ?>" style="border-radius:60px;"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>


                                <?php
                            }
                        }
                        ?>


                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>    

<?php
$staff_query = "SELECT * FROM staff NATURAL JOIN staff_type NATURAL JOIN shift";
$staff_result = mysqli_query($connection, $staff_query);

if (mysqli_num_rows($staff_result) > 0) {
    while ($staffGlobal = mysqli_fetch_assoc($staff_result)) {
        $fullname = explode(" ", $staffGlobal['emp_name']);
        ?>

        <!-- Employee Detail-->
        <div id="empDetail<?php echo $staffGlobal['emp_id']; ?>" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        <h4 class="modal-title"><?php _e('staff_detail_title') ?></h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Detalles:</div>
                                    <div class="panel-body">
                                        <form data-toggle="validator" role="form" action="functionmis.php"
                                              method="post">
                                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_position') ?></label>
                                                    <select class="form-control" id="staff_type" name="staff_type_id"
                                                            required>
                                                        <option selected disabled><?php _e('staff_select_position') ?></option>
                                                        <?php
                                                        $query = "SELECT * FROM staff_type";
                                                        $result = mysqli_query($connection, $query);
                                                        if (mysqli_num_rows($result) > 0) {
                                                            while ($staff = mysqli_fetch_assoc($result)) {
                                                                echo '<option value="' . $staff['staff_type_id'] . '" ' . (($staff['staff_type_id'] == $staffGlobal['staff_type_id']) ? 'selected="selected"' : "") . '>' . htmlspecialchars($staff['staff_type']) . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <select style="visibility: hidden;" class="form-control" id="shift" name="shift_id" required>
                                                        <option selected disabled><?php _e('staff_select_shift') ?></option>
                                                        <?php
                                                        $query = "SELECT * FROM shift";
                                                        $result = mysqli_query($connection, $query);
                                                        if (mysqli_num_rows($result) > 0) {
                                                            while ($shift = mysqli_fetch_assoc($result)) {
                                                                echo '<option value="' . $shift['shift_id'] . '" ' . (($shift['shift_id'] == $staffGlobal['shift_id']) ? 'selected="selected"' : "") . '>' . htmlspecialchars($shift['shift_timing']) . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" value="<?php echo $staffGlobal['emp_id']; ?>"
                                                       id="emp_id" name="emp_id">

                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_first_name') ?></label>
                                                    <input type="text" value="<?php echo htmlspecialchars($fullname[0]); ?>"
                                                           class="form-control" placeholder="<?php _e('staff_first_name') ?>" id="first_name"
                                                           name="first_name" required>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_last_name') ?></label>
                                                    <input type="text" value="<?php echo isset($fullname[1]) ? htmlspecialchars($fullname[1]) : ''; ?>"
                                                           class="form-control" placeholder="<?php _e('staff_last_name') ?>" id="last_name"
                                                           name="last_name" required>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_id_type') ?></label>
                                                    <select class="form-control" id="id_card_id" name="id_card_type"
                                                            required>
                                                        <option selected disabled>Seleccione Tipo ID</option>
                                                        <?php
                                                        $query = "SELECT * FROM id_card_type";
                                                        $result = mysqli_query($connection, $query);

                                                        if (mysqli_num_rows($result) > 0) {
                                                            while ($id_card_type = mysqli_fetch_assoc($result)) {
                                                                echo '<option  value="' . $id_card_type['id_card_type_id'] . '" ' . (($id_card_type['id_card_type_id'] == $staffGlobal['id_card_type']) ? 'selected="selected"' : "") . '>' . htmlspecialchars($id_card_type['id_card_type']) . '</option>';
                                                            }
                                                        }

                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_id_no') ?></label>
                                                    <input type="text" class="form-control" placeholder="<?php _e('staff_id_no') ?>"
                                                           id="id_card_no"
                                                           value="<?php echo htmlspecialchars($staffGlobal['id_card_no']); ?>"
                                                           name="id_card_no" required>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_phone') ?></label>
                                                    <input type="number" class="form-control"
                                                           placeholder="<?php _e('staff_phone') ?>" id="contact_no"
                                                           value="<?php echo htmlspecialchars($staffGlobal['contact_no']); ?>"
                                                           name="contact_no" required>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_address') ?></label>
                                                    <input type="text" class="form-control" placeholder="<?php _e('staff_address') ?>"
                                                           id="address" value="<?php echo htmlspecialchars($staffGlobal['address']); ?>"
                                                           name="address">
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <label><?php _e('staff_salary') ?></label>
                                                    <input type="number" class="form-control" placeholder="<?php _e('staff_salary') ?>"
                                                           id="salary" value="<?php echo htmlspecialchars($staffGlobal['salary']); ?>"
                                                           name="salary" required>
                                                </div>

                                            </div>

                                            <button type="submit" class="btn btn-lg btn-primary" name="submit"><?php _e('staff_save') ?>
                                            </button>
                                            <button type="reset" class="btn btn-lg btn-danger"><?php _e('staff_reset') ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Employee Detail-->
        <div id="changeShift" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        <h4 class="modal-title"><?php _e('staff_change_shift') ?></h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form data-toggle="validator" role="form" action="ajax.php" method="post">
                                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                            <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label><?php _e('staff_shift') ?></label>
                                                <select class="form-control" id="shift" name="shift_id" required>
                                                    <option selected disabled><?php _e('staff_select_shift') ?></option>
                                                    <?php
                                                    $query = "SELECT * FROM shift";
                                                    $result = mysqli_query($connection, $query);
                                                    if (mysqli_num_rows($result) > 0) {
                                                        while ($shift = mysqli_fetch_assoc($result)) {
                                                            echo '<option value="' . $shift['shift_id'] . '" ' . (($shift['shift_id'] == $staffGlobal['shift_id']) ? 'selected="selected"' : "") . '>' . htmlspecialchars($shift['shift_timing']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            </div>
                                            <input type="hidden" name="emp_id" value="" id="getEmpId">
                                            <button type="submit" class="btn btn-lg btn-primary" name="change_shift"><?php _e('staff_save') ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}
