<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active"><?php _e('room_management') ?></li>
        </ol>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-12">
            <div id="success"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('room_management') ?>
                    <button class="btn btn-secondary float-end" style="border-radius:60px;" data-bs-toggle="modal" data-bs-target="#addRoom"><?php _e('room_add') ?></button>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['error'])) {
                        echo "<div class='alert alert-danger'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('room_delete_error') . "
                            </div>";
                    }
                    if (isset($_GET['success'])) {
                        echo "<div class='alert alert-success'>
                                <span class='fa fa-info-circle'></span> &nbsp; " . __('room_deleted') . "
                            </div>";
                    }
                    ?>
                    <div class="table-responsive">
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="rooms">
                        <thead>
                        <tr>
                            <th><?php _e('room_no') ?></th>
                            <th><?php _e('room_type') ?></th>
                            <th><?php _e('room_status') ?></th>
                            <th><?php _e('room_check_in') ?></th>
                            <th><?php _e('room_check_out') ?></th>
                            <th><?php _e('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $room_query = "SELECT r.*, rt.*, (SELECT b.booking_id FROM booking b WHERE b.room_id = r.room_id ORDER BY b.booking_id DESC LIMIT 1) AS active_booking_id FROM room r NATURAL JOIN room_type rt WHERE r.deleteStatus = 0";
                        $rooms_result = mysqli_query($connection, $room_query);
                        if (mysqli_num_rows($rooms_result) > 0) {
                            while ($rooms = mysqli_fetch_assoc($rooms_result)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rooms['room_no']) ?></td>
                                    <td><?php echo htmlspecialchars($rooms['room_type']) ?></td>
                                    <td>
                                        <?php
                                        if ($rooms['status'] == 0) {
                                            echo '<a href="index.php?reservation&room_id=' . $rooms['room_id'] . '&room_type_id=' . $rooms['room_type_id'] . '" class="btn btn-success" style="border-radius:60px;">' . __('room_book') . '</a>';
                                        } else {
                                            echo '<a href="#" class="btn btn-danger" style="border-radius:60px;">' . __('room_booked') . '</a>';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        if ($rooms['status'] == 1 && $rooms['check_in_status'] == 0) {
                                            echo '<button class="btn btn-warning" id="checkInRoom"  data-id="' . $rooms['room_id'] . '" data-bs-toggle="modal" style="border-radius:60px;" data-bs-target="#checkIn">' . __('room_check_in') . '</button>';
                                        } elseif ($rooms['status'] == 0) {
                                            echo '-';
                                        } else {
                                            echo '<a href="#" class="btn btn-danger" style="border-radius:60px;">' . __('room_check_in') . '</a>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($rooms['status'] == 1 && $rooms['check_in_status'] == 1) {
                                            echo '<button class="btn btn-primary" style="border-radius:60px;" id="checkOutRoom" data-id="' . $rooms['room_id'] . '">' . __('room_check_out') . '</button>';
                                        } elseif ($rooms['status'] == 0) {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>

                                        <button title="<?php _e('edit') ?>" style="border-radius:60px;" data-bs-toggle="modal"
                                                data-bs-target="#editRoom" data-id="<?php echo $rooms['room_id']; ?>"
                                                id="roomEdit" class="btn btn-info"><i class="fa fa-pencil"></i></button>
                                        <?php
                                        if ($rooms['status'] == 1) {
                                            echo '<button title="' . __('room_customer_details') . '" data-bs-toggle="modal" data-bs-target="#cutomerDetailsModal" data-id="' . $rooms['room_id'] . '" id="cutomerDetails" class="btn btn-warning" style="border-radius:60px;"><i class="fa fa-eye"></i></button>';
                                        }
                                        ?>

                                        <a href="ajax.php?delete_room=<?php echo $rooms['room_id']; ?>&csrf=<?php echo csrf_token(); ?>"
                                           class="btn btn-danger" style="border-radius:60px;" onclick="return confirm('<?php _e('confirm_delete') ?>')"><i
                                                     class="fa fa-trash" alt="<?php _e('delete') ?>"></i></a>
                                        <?php if (!empty($rooms['active_booking_id'])): ?>
                                        <a href="invoice.php?booking_id=<?php echo $rooms['active_booking_id']; ?>"
                                           class="btn btn-success" style="border-radius:60px;" title="<?php _e('room_invoice') ?>"><i class="fa fa-file-text"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php }
                        } else {
                            echo __('room_no_rooms');
                        }
                        ?>

                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Add Room Modal -->
    <div id="addRoom" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="modal-title"><?php _e('room_add') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="addRoom" data-toggle="validator" role="form">
                                <div class="response"></div>
                                <div class="form-group">
                                    <label><?php _e('room_type') ?></label>
                                    <select class="form-control" id="room_type_id" required
                                            data-error="<?php _e('room_type_select') ?>">
                                        <option selected disabled><?php _e('room_type_select') ?></option>
                                        <?php
                                        $query = "SELECT * FROM room_type";
                                        $result = mysqli_query($connection, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($room_type = mysqli_fetch_assoc($result)) {
                                                echo '<option value="' . $room_type['room_type_id'] . '">' . htmlspecialchars($room_type['room_type']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <label><?php _e('room_no') ?></label>
                                    <input class="form-control" placeholder="<?php _e('room_no_placeholder') ?>" id="room_no"
                                           data-error="<?php _e('room_no_required') ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <button class="btn btn-success float-end"><?php _e('save') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--Edit Room Modal -->
    <div id="editRoom" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="modal-title"><?php _e('room_edit') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="roomEditFrom" data-toggle="validator" role="form">
                                <div class="edit_response"></div>
                                <div class="form-group">
                                    <label><?php _e('room_type') ?></label>
                                    <select class="form-control" id="edit_room_type" required
                                            data-error="<?php _e('room_type_select') ?>">
                                        <option selected disabled><?php _e('room_type_select') ?></option>
                                        <?php
                                        $query = "SELECT * FROM room_type";
                                        $result = mysqli_query($connection, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($room_type = mysqli_fetch_assoc($result)) {
                                                echo '<option value="' . $room_type['room_type_id'] . '">' . htmlspecialchars($room_type['room_type']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <label><?php _e('room_no') ?></label>
                                    <input class="form-control" placeholder="<?php _e('room_no_placeholder') ?>" id="edit_room_no" required
                                           data-error="<?php _e('room_no_required') ?>">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <input type="hidden" id="edit_room_id">
                                <button class="btn btn-success float-end"><?php _e('save') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!---customer details-->
    <div id="cutomerDetailsModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="modal-title text-center"><b><?php _e('room_customer_details') ?></b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-responsive table-bordered">
                                <tbody>
                                <tr>
                                    <td><b><?php _e('reservation_customer_name') ?></b></td>
                                    <td id="customer_name"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_phone') ?></b></td>
                                    <td id="customer_contact_no"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_email') ?></b></td>
                                    <td id="customer_email"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_id_type') ?></b></td>
                                    <td id="customer_id_card_type"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_id_no') ?></b></td>
                                    <td id="customer_id_card_number"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_address') ?></b></td>
                                    <td id="customer_address"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_pending_balance') ?></b></td>
                                    <td id="remaining_price"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---customer details ends here-->

    <!-- Check In Modal -->
    <div id="checkIn" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="modal-title text-center"><b><?php _e('room_check_in') ?></b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-responsive table-bordered">
                                
                                <tbody>
                                <tr>
                                    <td><b><?php _e('reservation_client') ?></b></td>
                                    <td id="getCustomerName"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_type') ?></b></td>
                                    <td id="getRoomType"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_no') ?></b></td>
                                    <td id="getRoomNo"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_check_in') ?></b></td>
                                    <td id="getCheckIn"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_check_out') ?></b></td>
                                    <td id="getCheckOut"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_total') ?></b></td>
                                    <td id="getTotalPrice"></td>
                                </tr>
                                </tbody>
                            </table>
                            <form role="form" id="advancePayment">
                                <div class="payment-response"></div>
                                <div class="form-group col-lg-12">
                                    <label><?php _e('room_advance_payment') ?></label>
                                    <input type="number" class="form-control" id="advance_payment"
                                           placeholder="<?php _e('room_advance_placeholder') ?>">
                                </div>
                                <input type="hidden" id="getBookingID" value="">
                                <button type="submit" class="btn btn-primary float-end"><?php _e('room_pay_checkin') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Check Out Modal-->
    <div id="checkOut" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="modal-title text-center"><b><?php _e('room_check_out') ?></b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-responsive table-bordered">
                                
                                <tbody>
                                <tr>
                                    <td><b><?php _e('reservation_client') ?></b></td>
                                    <td id="getCustomerName_n"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_type') ?></b></td>
                                    <td id="getRoomType_n"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_no') ?></b></td>
                                    <td id="getRoomNo_n"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_check_in') ?></b></td>
                                    <td id="getCheckIn_n"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_check_out') ?></b></td>
                                    <td id="getCheckOut_n"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('reservation_total') ?></b></td>
                                    <td id="getTotalPrice_n"></td>
                                </tr>
                                <tr>
                                    <td><b><?php _e('room_pending_payment') ?></b></td>
                                    <td id="getRemainingPrice_n"></td>
                                </tr>
                                </tbody>
                            </table>
                            <form role="form" id="checkOutRoom_n" data-toggle="validator">
                                <div class="checkout-response"></div>
                                <div class="form-group col-lg-12">
                                    <label><b><?php _e('room_pending_payment') ?></b></label>
                                    <input type="text" class="form-control" id="remaining_amount"
                                           placeholder="<?php _e('room_pending_payment') ?>" required
                                           data-error="<?php _e('room_pending_payment') ?>">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <input type="hidden" id="getBookingId_n" value="">
                                <button type="submit" class="btn btn-primary float-end"><?php _e('room_finish_checkout') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>




</div>    
