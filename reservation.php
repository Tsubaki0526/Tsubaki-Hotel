<?php
if (isset($_GET['room_id'])){
    $get_room_id = $_GET['room_id'];
    $get_room_sql = "SELECT * FROM room NATURAL JOIN room_type WHERE room_id = ?";
    $stmt = mysqli_prepare($connection, $get_room_sql);
    mysqli_stmt_bind_param($stmt, "i", $get_room_id);
    mysqli_stmt_execute($stmt);
    $get_room_result = mysqli_stmt_get_result($stmt);
    $get_room = mysqli_fetch_assoc($get_room_result);

    $get_room_type_id = $get_room['room_type_id'];
    $get_room_type = $get_room['room_type'];
    $get_room_no = $get_room['room_no'];
    $get_room_price = $get_room['price'];
}

?>
<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active"><?php _e('reservation_title') ?></li>
        </ol>
    </div>

    

    <div class="row">
        <div class="col-lg-12">
            <form role="form" id="bookingForm" data-toggle="validator">
                <div class="response"></div>
                <div class="col-lg-12">
                    <?php
                    if (isset($_GET['room_id'])){?>

                        <div class="panel panel-default">
                            <div class="panel-heading"><?php _e('reservation_room_info') ?>
                                <a class="btn btn-secondary float-end" href="index.php?room_mang"><?php _e('room_reschedule') ?></a>
                            </div>
                            <div class="panel-body">
                                <div class="form-group col-lg-6">
                                    <label><?php _e('room_type') ?></label>
                                    <select class="form-control" id="room_type_booking" data-error="<?php _e('room_type_select') ?>" required>
                                        <option selected disabled><?php _e('room_type_select') ?></option>
                                        <option selected value="<?php echo $get_room_type_id; ?>"><?php echo htmlspecialchars($get_room_type); ?></option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label><?php _e('room_no') ?></label>
                                    <select class="form-control" id="room_no_booking" onchange="fetch_price(this.value)" required data-error="<?php _e('room_type_select') ?>">
                                        <option selected disabled><?php _e('room_no') ?></option>
                                        <option selected value="<?php echo $get_room_id; ?>"><?php echo htmlspecialchars($get_room_no); ?></option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label><?php _e('reservation_check_in') ?></label>
                                    <input type="text" class="form-control" placeholder="<?php _e('reservation_date_format') ?>" id="check_in_date_booking" data-error="<?php _e('field_required') ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label><?php _e('reservation_check_out') ?></label>
                                    <input type="text" class="form-control" placeholder="<?php _e('reservation_date_format') ?>" id="check_out_date_booking" data-error="<?php _e('field_required') ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="col-lg-12">
                                    <h4 style="font-weight: bold"><?php _e('reservation_total_days') ?> : <span id="staying_day_booking">0</span> <?php _e('reservation_days') ?></h4>
                                    <h4 style="font-weight: bold"><?php _e('reservation_price') ?>: <span id="price_booking"><?php echo $get_room_price; ?></span> /-</h4>
                                    <h4 style="font-weight: bold"><?php _e('reservation_total') ?> : <span id="total_price_booking">0</span> /-</h4>
                                </div>
                            </div>
                        </div>
                    <?php } else{?>
                        <div class="panel panel-default">
                            <div class="panel-heading"><?php _e('reservation_room_info') ?>
                                <a class="btn btn-secondary float-end" style="border-radius:60px;" href="index.php?reservation"><?php _e('room_reschedule') ?></a>
                            </div>
                            <div class="panel-body">
                                <div class="form-group col-lg-6">
                                    <label><?php _e('room_type') ?></label>
                                    <select class="form-control" id="room_type_booking" onchange="fetch_room(this.value);" required data-error="<?php _e('room_type_select') ?>">
                                        <option selected disabled><?php _e('room_type_select') ?></option>
                                        <?php
                                        $query  = "SELECT * FROM room_type";
                                        $result = mysqli_query($connection,$query);
                                        if (mysqli_num_rows($result) > 0){
                                            while ($room_type = mysqli_fetch_assoc($result)){
                                                echo '<option value="'.$room_type['room_type_id'].'">'.htmlspecialchars($room_type['room_type']).'</option>';
                                            }}
                                        ?>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label><?php _e('room_no') ?></label>
                                    <select class="form-control" id="room_no_booking" onchange="fetch_price(this.value)" required data-error="<?php _e('room_type_select') ?>">

                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label><?php _e('reservation_check_in') ?></label>
                                    <input type="text" class="form-control" placeholder="<?php _e('reservation_date_format') ?>" id="check_in_date_booking" data-error="<?php _e('field_required') ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label><?php _e('reservation_check_out') ?></label>
                                    <input type="text" class="form-control" placeholder="<?php _e('reservation_date_format') ?>" id="check_out_date_booking" data-error="<?php _e('field_required') ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="col-lg-12">
                                    <h4 style="font-weight: bold"><?php _e('reservation_total_days') ?> : <span id="staying_day_booking">0</span> <?php _e('reservation_days') ?></h4>
                                    <h4 style="font-weight: bold"><?php _e('reservation_price') ?>: <span id="price_booking">0</span> /-</h4>
                                    <h4 style="font-weight: bold"><?php _e('reservation_total') ?> : <span id="total_price_booking">0</span> /-</h4>
                                </div>
                            </div>
                        </div>
                    <?php }
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><?php _e('reservation_customer_data') ?></div>
                        <div class="panel-body">
                            <div class="form-group col-lg-6">
                                <label><?php _e('reservation_customer_name') ?></label>
                                <input class="form-control" placeholder="<?php _e('reservation_customer_name') ?>" id="first_name_booking" data-error="<?php _e('field_required') ?>" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('reservation_customer_lastname') ?></label>
                                <input class="form-control" placeholder="<?php _e('reservation_customer_lastname') ?>" id="last_name_booking">
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('reservation_phone') ?></label>
                                <input type="number" class="form-control" data-error="<?php _e('field_minlength') ?>" data-minlength="10" placeholder="<?php _e('reservation_phone') ?>" id="contact_no_booking" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('reservation_email') ?></label>
                                <input type="email" class="form-control" placeholder="<?php _e('reservation_email') ?>" id="email_booking" data-error="<?php _e('field_email') ?>" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('reservation_id_type') ?></label>
                                <select class="form-control" id="id_card_id_booking" data-error="<?php _e('select_option') ?>" required onchange="validId(this.value);">
                                    <option selected disabled><?php _e('select_option') ?></option>
                                    <?php
                                    $query  = "SELECT * FROM id_card_type";
                                    $result = mysqli_query($connection,$query);
                                    if (mysqli_num_rows($result) > 0){
                                        while ($id_card_type = mysqli_fetch_assoc($result)){
                                            echo '<option value="'.$id_card_type['id_card_type_id'].'">'.htmlspecialchars($id_card_type['id_card_type']).'</option>';
                                        }}
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label><?php _e('reservation_id_no') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('reservation_id_no') ?>" id="id_card_no_booking" data-error="<?php _e('field_required') ?>" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label><?php _e('reservation_address') ?></label>
                                <input type="text" class="form-control" placeholder="<?php _e('reservation_address') ?>" id="address_booking" required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-lg btn-success float-end" style="border-radius:60px;"><?php _e('reservation_book_btn') ?></button>
                </div>
            </form>
        </div>
    </div>



</div>


<!-- Booking Confirmation-->
<div id="bookingConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <h4 class="modal-title text-center"><b><?php _e('reservation_confirm_title') ?></b></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-success"><em class="fa fa-lg fa-check-circle">&nbsp;</em><?php _e('reservation_success') ?></div>
                        <table class="table table-striped table-bordered table-responsive">
                            <tbody>
                            <tr>
                                <td><b><?php _e('reservation_client') ?></b></td>
                                <td id="confirmName"></td>
                            </tr>
                            <tr>
                                <td><b><?php _e('room_type') ?></b></td>
                                <td id="confirmRoomType"></td>
                            </tr>
                            <tr>
                                <td><b><?php _e('room_no') ?></b></td>
                                <td id="confirmRoomNo"></td>
                            </tr>
                            <tr>
                                <td><b><?php _e('reservation_check_in') ?></b></td>
                                <td id="confirmCheckIn"></td>
                            </tr>
                            <tr>
                                <td><b><?php _e('reservation_check_out') ?></b></td>
                                <td id="confirmCheckOut"></td>
                            </tr>
                            <tr>
                                <td><b><?php _e('reservation_total') ?></b></td>
                                <td id="confirmTotalPrice"></td>
                            </tr>
                            <tr>
                                <td><b><?php _e('reservation_status') ?></b></td>
                                <td id="confirmPaymentStatus"></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-success" style="border-radius:60px;" id="confirmInvoiceBtn" href="#" target="_blank"><i class="fa fa-file-text"></i> <?php _e('reservation_view_invoice') ?></a>
                <a class="btn btn-primary" style="border-radius:60px;" href="index.php?reservation"><i class="fa fa-check-circle"></i> <?php _e('reservation_close') ?></a>
            </div>
        </div>

    </div>
</div>
