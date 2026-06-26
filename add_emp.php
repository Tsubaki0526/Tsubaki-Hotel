<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active"><?php _e('add_emp_title') ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('staff_details') ?></div>
                <div class="panel-body">
                    <div class="emp-response"></div>
                    <form role="form" id="addEmployee" data-toggle="validator">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_position') ?></label>
                                <select class="form-control" id="staff_type" required data-error="<?php _e('form_select_position') ?>">
                                    <option selected disabled><?php _e('staff_select_position') ?></option>
                                    <?php
                                    $query = "SELECT * FROM staff_type";
                                    $result = mysqli_query($connection, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($staff = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . $staff['staff_type_id'] . '">' . htmlspecialchars(translate_db($staff['staff_type'])) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_shift') ?></label>
                                <select class="form-control" id="shift" required data-error="<?php _e('form_select_shift') ?>">
                                    <option selected disabled><?php _e('staff_select_shift') ?></option>
                                    <?php
                                    $query = "SELECT * FROM shift";
                                    $result = mysqli_query($connection, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($shift = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . $shift['shift_id'] . '">' . htmlspecialchars(translate_db($shift['shift'])) . ' - ' . htmlspecialchars($shift['shift_timing']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_first_name') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('add_emp_first_name') ?>" id="first_name" required data-error="<?php _e('form_enter_name') ?>">
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_last_name') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('add_emp_last_name') ?>" id="last_name">
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_id_type') ?></label>
                                <select class="form-control" id="id_card_id" required onchange="validId(this.value);">
                                    <option selected disabled><?php _e('form_select_id_type') ?></option>
                                    <?php
                                    $query = "SELECT * FROM id_card_type";
                                    $result = mysqli_query($connection, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($id_card_type = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . $id_card_type['id_card_type_id'] . '">' . htmlspecialchars(translate_db($id_card_type['id_card_type'])) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_id_no') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('add_emp_id_no') ?>" id="id_card_no" required>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_phone') ?></label>
                                <input type="number" class="form-control" placeholder="<?php _e('add_emp_phone') ?>" id="contact_no" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_address') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('add_emp_address') ?>" id="address" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('add_emp_salary') ?></label>
                                <input type="number" class="form-control" placeholder="<?php _e('add_emp_salary') ?>" id="salary" data-error="<?php _e('form_enter_salary') ?>" required>
                                <div class="help-block with-errors"></div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-lg btn-success" style="border-radius:60px;"><?php _e('add_emp_save') ?></button>
                        <button type="reset" class="btn btn-lg btn-danger" style="border-radius:60px;"><?php _e('add_emp_reset') ?></button>
                    </form>
                </div>
            </div>
        </div>


    </div>



</div>    
