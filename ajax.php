<?php
session_start();
include_once 'db.php';
lang_init();

function json_output($data) {
    echo json_encode($data);
    exit;
}

// === Rate limiting (login) ===
function check_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $key = 'login_attempts_' . md5($ip);
    $attempts = $_SESSION[$key] ?? 0;
    $blocked_until = $_SESSION[$key . '_blocked'] ?? 0;
    if ($blocked_until > time()) {
        return false; // blocked
    }
    if ($attempts >= 5) {
        $_SESSION[$key . '_blocked'] = time() + 300; // 5 min block
        return false;
    }
    return true;
}

function increment_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $key = 'login_attempts_' . md5($ip);
    $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
}

function reset_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $key = 'login_attempts_' . md5($ip);
    unset($_SESSION[$key], $_SESSION[$key . '_blocked']);
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header('Location:login.php?empty');
        exit;
    }

    if (!check_login_attempts()) {
        header('Location:login.php?loginE');
        exit;
    }

    $stmt = mysqli_prepare($connection, "SELECT * FROM user WHERE (username = ? OR email = ?)");
    mysqli_stmt_bind_param($stmt, "ss", $email, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            reset_login_attempts();
            header('Location:index.php?dashboard');
            exit;
        } elseif (md5($password) === $user['password']) {
            session_regenerate_id(true);
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $update = mysqli_prepare($connection, "UPDATE user SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "si", $new_hash, $user['id']);
            mysqli_stmt_execute($update);
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            reset_login_attempts();
            header('Location:index.php?dashboard');
            exit;
        }
    }
    increment_login_attempts();
    header('Location:login.php?loginE');
    exit;
}

if (isset($_POST['add_room'])) {
    if (!isset($_SESSION['user_id'])) { json_output(['done' => false, 'data' => __('ajax_unauthorized')]); }
    require_csrf();
    $room_type_id = $_POST['room_type_id'];
    $room_no = trim($_POST['room_no']);

    if ($room_no == '') {
        json_output(['done' => false, 'data' => __('ajax_room_number_required')]);
    }

    $check = mysqli_prepare($connection, "SELECT * FROM room WHERE room_no = ?");
    mysqli_stmt_bind_param($check, "s", $room_no);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) >= 1) {
        json_output(['done' => false, 'data' => __('ajax_room_exists')]);
    }

    $stmt = mysqli_prepare($connection, "INSERT INTO room (room_type_id, room_no) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $room_type_id, $room_no);
    $result = mysqli_stmt_execute($stmt);

    json_output([
        'done' => $result,
        'data' => $result ? __('ajax_room_added') : __('ajax_database_error')
    ]);
}

if (isset($_POST['room'])) {
    $room_id = $_POST['room_id'];
    $stmt = mysqli_prepare($connection, "SELECT * FROM room WHERE room_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $room = mysqli_fetch_assoc($result)) {
        json_output([
            'done' => true,
            'room_no' => $room['room_no'],
            'room_type_id' => $room['room_type_id']
        ]);
    }
    json_output(['done' => false, 'data' => __('ajax_database_error')]);
}

if (isset($_POST['edit_room'])) {
    if (!isset($_SESSION['user_id'])) { json_output(['done' => false, 'data' => __('ajax_unauthorized')]); }
    require_csrf();
    $room_type_id = $_POST['room_type_id'];
    $room_no = trim($_POST['room_no']);
    $room_id = $_POST['room_id'];

    if ($room_no == '') {
        json_output(['done' => false, 'data' => __('ajax_room_number_required')]);
    }

    $stmt = mysqli_prepare($connection, "UPDATE room SET room_no = ?, room_type_id = ? WHERE room_id = ?");
    mysqli_stmt_bind_param($stmt, "sii", $room_no, $room_type_id, $room_id);
    $result = mysqli_stmt_execute($stmt);

    json_output([
        'done' => $result,
        'data' => $result ? __('ajax_room_edited') : __('ajax_database_error')
    ]);
}

if (isset($_GET['delete_room'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?room_mang&error"); exit; }
    if (!isset($_GET['csrf']) || !verify_csrf($_GET['csrf'])) { header("Location:index.php?room_mang&error"); exit; }
    $room_id = intval($_GET['delete_room']);
    $stmt = mysqli_prepare($connection, "UPDATE room SET deleteStatus = '1' WHERE room_id = ? AND status IS NULL");
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    $result = mysqli_stmt_execute($stmt);
    header("Location:index.php?room_mang&" . ($result ? "success" : "error"));
    exit;
}

if (isset($_POST['fetch_rooms_by_type'])) {
    $room_type_id = $_POST['room_type_id'];
    $stmt = mysqli_prepare($connection, "SELECT * FROM room WHERE room_type_id = ? AND status IS NULL AND deleteStatus = '0'");
    mysqli_stmt_bind_param($stmt, "i", $room_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    echo "<option selected disabled>" . __('ajax_select_room') . "</option>";
    while ($room = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $room['room_id'] . "'>" . $room['room_no'] . "</option>";
    }
    exit;
}

if (isset($_POST['room_price'])) {
    $room_id = $_POST['room_id'];
    $stmt = mysqli_prepare($connection, "SELECT * FROM room NATURAL JOIN room_type WHERE room_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $room = mysqli_fetch_assoc($result)) {
        echo $room['price'];
    } else {
        echo "0";
    }
    exit;
}

if (isset($_POST['booking'])) {
    if (!isset($_SESSION['user_id'])) { json_output(['done' => false, 'data' => __('ajax_unauthorized')]); }
    require_csrf();
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $total_price = $_POST['total_price'];
    $name = $_POST['name'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $id_card_id = $_POST['id_card_id'];
    $id_card_no = $_POST['id_card_no'];
    $address = $_POST['address'];

    mysqli_begin_transaction($connection);

    $customer_sql = "INSERT INTO customer (customer_name, contact_no, email, id_card_type_id, id_card_no, address) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_c = mysqli_prepare($connection, $customer_sql);
    mysqli_stmt_bind_param($stmt_c, "sssiss", $name, $contact_no, $email, $id_card_id, $id_card_no, $address);
    $customer_result = mysqli_stmt_execute($stmt_c);

    if ($customer_result) {
        $customer_id = mysqli_insert_id($connection);
        $booking_sql = "INSERT INTO booking (customer_id, room_id, check_in, check_out, total_price, remaining_price, payment_status) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt_b = mysqli_prepare($connection, $booking_sql);
        mysqli_stmt_bind_param($stmt_b, "iissii", $customer_id, $room_id, $check_in, $check_out, $total_price, $total_price);
        $booking_result = mysqli_stmt_execute($stmt_b);

        if ($booking_result) {
            $booking_id = mysqli_insert_id($connection);
            $inv_no = 'INV-' . str_pad($booking_id, 5, '0', STR_PAD_LEFT);
            $upd = mysqli_prepare($connection, "UPDATE booking SET invoice_no=? WHERE booking_id=?");
            mysqli_stmt_bind_param($upd, "si", $inv_no, $booking_id);
            mysqli_stmt_execute($upd);

            $room_update = mysqli_prepare($connection, "UPDATE room SET status = '1' WHERE room_id = ?");
            mysqli_stmt_bind_param($room_update, "i", $room_id);
            $room_ok = mysqli_stmt_execute($room_update);

            if ($room_ok) {
                mysqli_commit($connection);
                json_output(['done' => true, 'data' => __('ajax_booking_success'), 'booking_id' => $booking_id]);
            } else {
                mysqli_rollback($connection);
                json_output(['done' => false, 'data' => __('ajax_status_update_error')]);
            }
        } else {
            mysqli_rollback($connection);
            json_output(['done' => false, 'data' => __('ajax_booking_error')]);
        }
    } else {
        mysqli_rollback($connection);
        json_output(['done' => false, 'data' => __('ajax_customer_add_error')]);
    }
}

if (isset($_POST['cutomerDetails'])) {
    $room_id = $_POST['room_id'];

    if ($room_id != '') {
        $stmt = mysqli_prepare($connection, "SELECT * FROM room NATURAL JOIN room_type NATURAL JOIN booking NATURAL JOIN customer WHERE room_id = ? AND payment_status = '0'");
        mysqli_stmt_bind_param($stmt, "i", $room_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && $customer_details = mysqli_fetch_assoc($result)) {
            $id_type = $customer_details['id_card_type_id'];
            $stmt2 = mysqli_prepare($connection, "SELECT id_card_type FROM id_card_type WHERE id_card_type_id = ?");
            mysqli_stmt_bind_param($stmt2, "i", $id_type);
            mysqli_stmt_execute($stmt2);
            $res2 = mysqli_stmt_get_result($stmt2);
            $id_type_name = mysqli_fetch_assoc($res2);

            json_output([
                'done' => true,
                'customer_id' => $customer_details['customer_id'],
                'customer_name' => $customer_details['customer_name'],
                'contact_no' => $customer_details['contact_no'],
                'email' => $customer_details['email'],
                'id_card_no' => $customer_details['id_card_no'],
                'id_card_type_id' => $id_type_name['id_card_type'],
                'address' => $customer_details['address'],
                'remaining_price' => $customer_details['remaining_price']
            ]);
        }
        json_output(['done' => false, 'data' => __('ajax_database_error')]);
    }
}

if (isset($_POST['booked_room'])) {
    $room_id = $_POST['room_id'];
    $stmt = mysqli_prepare($connection, "SELECT * FROM room NATURAL JOIN room_type NATURAL JOIN booking NATURAL JOIN customer WHERE room_id = ? AND payment_status = '0'");
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $room = mysqli_fetch_assoc($result)) {
        json_output([
            'done' => true,
            'booking_id' => $room['booking_id'],
            'name' => $room['customer_name'],
            'room_no' => $room['room_no'],
            'room_type' => $room['room_type'],
            'check_in' => date('M j, Y', strtotime($room['check_in'])),
            'check_out' => date('M j, Y', strtotime($room['check_out'])),
            'total_price' => $room['total_price'],
            'remaining_price' => $room['remaining_price']
        ]);
    }
    json_output(['done' => false, 'data' => __('ajax_database_error')]);
}

if (isset($_POST['check_in_room'])) {
    if (!isset($_SESSION['user_id'])) { json_output(['done' => false, 'data' => __('ajax_unauthorized')]); }
    require_csrf();
    $booking_id = $_POST['booking_id'];
    $advance_payment = (int)$_POST['advance_payment'];

    if ($booking_id == '') {
        json_output(['done' => false, 'data' => __('ajax_booking_error')]);
    }

    mysqli_begin_transaction($connection);

    $stmt = mysqli_prepare($connection, "SELECT * FROM booking WHERE booking_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking_details = mysqli_fetch_assoc($result);

    $room_id = $booking_details['room_id'];
    $remaining_price = $booking_details['total_price'] - $advance_payment;

    $updateBooking = mysqli_prepare($connection, "UPDATE booking SET remaining_price = ? WHERE booking_id = ?");
    mysqli_stmt_bind_param($updateBooking, "ii", $remaining_price, $booking_id);
    $pay_ok = mysqli_stmt_execute($updateBooking);

    if ($pay_ok) {
        $updateRoom = mysqli_prepare($connection, "UPDATE room SET check_in_status = '1' WHERE room_id = ?");
        mysqli_stmt_bind_param($updateRoom, "i", $room_id);
        $room_ok = mysqli_stmt_execute($updateRoom);

        if ($room_ok) {
            mysqli_commit($connection);
            json_output(['done' => true]);
        } else {
            mysqli_rollback($connection);
            json_output(['done' => false, 'data' => __('ajax_checkin_update_error')]);
        }
    } else {
        mysqli_rollback($connection);
        json_output(['done' => false, 'data' => __('ajax_payment_error')]);
    }
}

if (isset($_POST['check_out_room'])) {
    if (!isset($_SESSION['user_id'])) { json_output(['done' => false, 'data' => __('ajax_unauthorized')]); }
    require_csrf();
    $booking_id = $_POST['booking_id'];
    $remaining_amount = (int)$_POST['remaining_amount'];

    if ($booking_id == '') {
        json_output(['done' => false, 'data' => __('ajax_booking_error')]);
    }

    $stmt = mysqli_prepare($connection, "SELECT * FROM booking WHERE booking_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking_details = mysqli_fetch_assoc($result);

    $room_id = $booking_details['room_id'];
    $remaining_price = $booking_details['remaining_price'];

    if ($remaining_price == $remaining_amount) {
        mysqli_begin_transaction($connection);

        $updateBooking = mysqli_prepare($connection, "UPDATE booking SET remaining_price = '0', payment_status = '1' WHERE booking_id = ?");
        mysqli_stmt_bind_param($updateBooking, "i", $booking_id);
        $pay_ok = mysqli_stmt_execute($updateBooking);

        if ($pay_ok) {
            $updateRoom = mysqli_prepare($connection, "UPDATE room SET status = NULL, check_in_status = '0', check_out_status = '1' WHERE room_id = ?");
            mysqli_stmt_bind_param($updateRoom, "i", $room_id);
            $room_ok = mysqli_stmt_execute($updateRoom);

            if ($room_ok) {
                mysqli_commit($connection);
                json_output(['done' => true]);
            } else {
                mysqli_rollback($connection);
                json_output(['done' => false, 'data' => __('ajax_checkout_update_error')]);
            }
        } else {
            mysqli_rollback($connection);
            json_output(['done' => false, 'data' => __('ajax_payment_error')]);
        }
    } else {
        json_output(['done' => false, 'data' => __('ajax_enter_full_payment')]);
    }
}

if (isset($_POST['add_employee'])) {
    if (!isset($_SESSION['user_id'])) { json_output(['done' => false, 'data' => __('ajax_unauthorized')]); }
    require_csrf();
    $staff_type = $_POST['staff_type'];
    $shift = $_POST['shift'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $name = $first_name . ' ' . $last_name;
    $contact_no = $_POST['contact_no'];
    $id_card_id = $_POST['id_card_id'];
    $id_card_no = $_POST['id_card_no'];
    $address = $_POST['address'];
    $salary = $_POST['salary'];

    if ($staff_type == '' || $shift == '' || $salary == '') {
        json_output(['done' => false, 'data' => __('ajax_complete_fields')]);
    }

    mysqli_begin_transaction($connection);

    $stmt = mysqli_prepare($connection, "INSERT INTO staff (emp_name, staff_type_id, shift_id, id_card_type, id_card_no, address, contact_no, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siiisssi", $name, $staff_type, $shift, $id_card_id, $id_card_no, $address, $contact_no, $salary);
    $emp_ok = mysqli_stmt_execute($stmt);
    $emp_id = mysqli_insert_id($connection);

    $hist = mysqli_prepare($connection, "INSERT INTO emp_history (emp_id, shift_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($hist, "ii", $emp_id, $shift);
    $hist_ok = mysqli_stmt_execute($hist);

    if ($emp_ok && $hist_ok) {
        mysqli_commit($connection);
        json_output(['done' => true, 'data' => __('ajax_employee_added')]);
    } else {
        mysqli_rollback($connection);
        json_output(['done' => false, 'data' => __('ajax_database_error')]);
    }
}

if (isset($_POST['createComplaint'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?complain&error"); exit; }
    require_csrf();
    $complainant_name = $_POST['complainant_name'];
    $complaint_type = $_POST['complaint_type'];
    $complaint = $_POST['complaint'];

    $stmt = mysqli_prepare($connection, "INSERT INTO complaint (complainant_name, complaint_type, complaint) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $complainant_name, $complaint_type, $complaint);
    $result = mysqli_stmt_execute($stmt);

    header("Location:index.php?complain&" . ($result ? "success" : "error"));
    exit;
}

if (isset($_POST['resolve_complaint'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?complain&resolveError"); exit; }
    require_csrf();
    $budget = $_POST['budget'];
    $complaint_id = $_POST['complaint_id'];

    $stmt = mysqli_prepare($connection, "UPDATE complaint SET budget = ?, resolve_status = '1' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $budget, $complaint_id);
    $result = mysqli_stmt_execute($stmt);

    header("Location:index.php?complain&" . ($result ? "resolveSuccess" : "resolveError"));
    exit;
}

if (isset($_POST['change_shift'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?staff_mang&error"); exit; }
    require_csrf();
    $emp_id = $_POST['emp_id'];
    $shift_id = $_POST['shift_id'];

    mysqli_begin_transaction($connection);

    $stmt = mysqli_prepare($connection, "UPDATE staff SET shift_id = ? WHERE emp_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $shift_id, $emp_id);
    $ok1 = mysqli_stmt_execute($stmt);

    $to_date = date("Y-m-d H:i:s");
    $update = mysqli_prepare($connection, "UPDATE emp_history SET to_date = ? WHERE emp_id = ? AND to_date IS NULL");
    mysqli_stmt_bind_param($update, "si", $to_date, $emp_id);
    $ok2 = mysqli_stmt_execute($update);

    $insert = mysqli_prepare($connection, "INSERT INTO emp_history (emp_id, shift_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($insert, "ii", $emp_id, $shift_id);
    $ok3 = mysqli_stmt_execute($insert);

    if ($ok1 && $ok2 && $ok3) {
        mysqli_commit($connection);
        header("Location:index.php?staff_mang&success");
    } else {
        mysqli_rollback($connection);
        header("Location:index.php?staff_mang&error");
    }
    exit;
}

// =========== ADMIN SITIO WEB ===========

if (isset($_POST['save_blog'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_blog&blog_error"); exit; }
    require_csrf();
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    if (empty($slug)) {
        $slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower(trim($title)));
        $slug = trim($slug, '-');
    }
    $excerpt = trim($_POST['excerpt']);
    $content = $_POST['content'];
    $color = $_POST['color'] ?? '#1a5276';
    $color2 = $_POST['color2'] ?? '#2980b9';

    $stmt = mysqli_prepare($connection, "INSERT INTO blog (title, slug, excerpt, content, color, color2) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $title, $slug, $excerpt, $content, $color, $color2);
    $ok = mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_blog&" . ($ok ? "blog_success" : "blog_error"));
    exit;
}

if (isset($_POST['edit_blog'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_blog&blog_error"); exit; }
    require_csrf();
    $id = intval($_POST['blog_id']);
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    if (empty($slug)) {
        $slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower(trim($title)));
        $slug = trim($slug, '-');
    }
    $excerpt = trim($_POST['excerpt']);
    $content = $_POST['content'];
    $color = $_POST['color'] ?? '#1a5276';
    $color2 = $_POST['color2'] ?? '#2980b9';

    $stmt = mysqli_prepare($connection, "UPDATE blog SET title=?, slug=?, excerpt=?, content=?, color=?, color2=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssssi", $title, $slug, $excerpt, $content, $color, $color2, $id);
    $ok = mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_blog&" . ($ok ? "blog_success" : "blog_error"));
    exit;
}

if (isset($_GET['delete_blog'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_blog&blog_error"); exit; }
    if (!isset($_GET['csrf']) || !verify_csrf($_GET['csrf'])) { header("Location:index.php?admin_blog&blog_error"); exit; }
    $id = intval($_GET['delete_blog']);
    $stmt = mysqli_prepare($connection, "DELETE FROM blog WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_blog&blog_deleted");
    exit;
}

if (isset($_POST['save_service'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_services&srv_error"); exit; }
    require_csrf();
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $stmt = mysqli_prepare($connection, "INSERT INTO services (title, description, icon) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $title, $description, $icon);
    $ok = mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_services&" . ($ok ? "srv_success" : "srv_error"));
    exit;
}

if (isset($_POST['edit_service'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_services&srv_error"); exit; }
    require_csrf();
    $id = intval($_POST['service_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $stmt = mysqli_prepare($connection, "UPDATE services SET title=?, description=?, icon=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $icon, $id);
    $ok = mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_services&" . ($ok ? "srv_success" : "srv_error"));
    exit;
}

if (isset($_GET['delete_service'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_services&srv_error"); exit; }
    if (!isset($_GET['csrf']) || !verify_csrf($_GET['csrf'])) { header("Location:index.php?admin_services&srv_error"); exit; }
    $id = intval($_GET['delete_service']);
    $stmt = mysqli_prepare($connection, "DELETE FROM services WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_services&srv_deleted");
    exit;
}

function uploadImage($field_name) {
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$field_name];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        return false;
    }
    $filename = uniqid('img_') . '.' . $ext;
    $dest = __DIR__ . '/uploads/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $filename;
    }
    return false;
}

if (isset($_POST['save_room_type'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_room_types&rt_error"); exit; }
    require_csrf();
    $room_type = trim($_POST['room_type']);
    $price = intval($_POST['price']);
    $max_person = intval($_POST['max_person']);
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');

    $image = uploadImage('image');
    if ($image === false) {
        header("Location:index.php?admin_room_types&rt_error");
        exit;
    }
    $image = $image ?? '';

    $stmt = mysqli_prepare($connection, "INSERT INTO room_type (room_type, price, max_person, description, image, amenities) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siisss", $room_type, $price, $max_person, $description, $image, $amenities);
    $ok = mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_room_types&" . ($ok ? "rt_success" : "rt_error"));
    exit;
}

if (isset($_POST['edit_room_type'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_room_types&rt_error"); exit; }
    require_csrf();
    $id = intval($_POST['room_type_id']);
    $room_type = trim($_POST['room_type']);
    $price = intval($_POST['price']);
    $max_person = intval($_POST['max_person']);
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');

    $new_image = uploadImage('image');
    if ($new_image === false) {
        header("Location:index.php?admin_room_types&rt_error");
        exit;
    }

    if ($new_image) {
        $stmt = mysqli_prepare($connection, "SELECT image FROM room_type WHERE room_type_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $old = mysqli_fetch_assoc($res);
        if (!empty($old['image'])) {
            $old_path = __DIR__ . '/uploads/' . $old['image'];
            if (file_exists($old_path)) { unlink($old_path); }
        }
        $image = $new_image;
    } else {
        $stmt = mysqli_prepare($connection, "SELECT image FROM room_type WHERE room_type_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $old = mysqli_fetch_assoc($res);
        $image = $old['image'] ?? '';
    }

    $stmt = mysqli_prepare($connection, "UPDATE room_type SET room_type=?, price=?, max_person=?, description=?, image=?, amenities=? WHERE room_type_id=?");
    mysqli_stmt_bind_param($stmt, "siisssi", $room_type, $price, $max_person, $description, $image, $amenities, $id);
    $ok = mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_room_types&" . ($ok ? "rt_success" : "rt_error"));
    exit;
}

if (isset($_GET['delete_message'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_messages&msg_error"); exit; }
    if (!isset($_GET['csrf']) || !verify_csrf($_GET['csrf'])) { header("Location:index.php?admin_messages&msg_error"); exit; }
    $id = intval($_GET['delete_message']);
    $stmt = mysqli_prepare($connection, "DELETE FROM contact_messages WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_messages&msg_deleted");
    exit;
}

if (isset($_POST['save_settings'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_settings&cfg_error"); exit; }
    require_csrf();

    // Helper: handle image field with file + URL fallback
    $handleImageField = function($field, $file_field, $url_field) use ($connection) {
        $val = '';
        if (isset($_FILES[$file_field]) && $_FILES[$file_field]['error'] === UPLOAD_ERR_OK) {
            $result = uploadImage($file_field);
            if ($result === false) return false;
            if ($result) {
                $val = $result;
                $stmt = mysqli_prepare($connection, "SELECT key_value FROM site_settings WHERE key_name=?");
                mysqli_stmt_bind_param($stmt, "s", $field);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $old = mysqli_fetch_assoc($res);
                if ($old && !empty($old['key_value']) && strpos($old['key_value'], 'http') !== 0) {
                    $old_path = __DIR__ . '/uploads/' . $old['key_value'];
                    if (file_exists($old_path)) { unlink($old_path); }
                }
            }
        } elseif (!empty(trim($_POST[$url_field] ?? ''))) {
            $val = trim($_POST[$url_field]);
        } else {
            $stmt = mysqli_prepare($connection, "SELECT key_value FROM site_settings WHERE key_name=?");
            mysqli_stmt_bind_param($stmt, "s", $field);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $old = mysqli_fetch_assoc($res);
            $val = $old['key_value'] ?? '';
        }
        $_POST[$field] = $val;
        unset($_POST[$file_field], $_POST[$url_field]);
        return true;
    };

    if ($handleImageField('about_image', 'about_image_file', 'about_image_url') === false) {
        header("Location:index.php?admin_settings&cfg_error");
        exit;
    }
    if ($handleImageField('hero_image', 'hero_image_file', 'hero_image_url') === false) {
        header("Location:index.php?admin_settings&cfg_error");
        exit;
    }

    $keys = ['site_name','site_tagline','site_description','hero_title','hero_subtitle','hero_text','hero_image',
             'about_title','about_text','about_image','contact_email','contact_phone','contact_address',
             'social_facebook','social_instagram','social_twitter','social_youtube','social_whatsapp',
             'business_hours','map_embed','copyright_text',
             'stripe_publishable_key','stripe_secret_key','stripe_webhook_secret','stripe_currency',
             'paypal_client_id','paypal_secret','paypal_mode','gateway_enabled',
             'mercadopago_public_key','mercadopago_access_token',
             'payment_bank_instructions','payment_bank_country',
             'check_in_time','check_out_time','footer_about'];
    $encrypted_keys = get_encrypted_keys();
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = trim($_POST[$key]);
            // Skip encrypted keys if empty (field was left blank to keep existing value)
            if (in_array($key, $encrypted_keys) && empty($val)) {
                continue;
            }
            if (in_array($key, $encrypted_keys) && !empty($val)) {
                $encrypted = encrypt_value($val);
                if ($encrypted !== false) $val = $encrypted;
            }
            $stmt = mysqli_prepare($connection, "INSERT INTO site_settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value=?");
            mysqli_stmt_bind_param($stmt, "sss", $key, $val, $val);
            mysqli_stmt_execute($stmt);
        }
    }
    header("Location:index.php?admin_settings&cfg_success");
    exit;
}

if (isset($_POST['save_bank_account'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_settings"); exit; }
    require_csrf();
    $stmt = mysqli_prepare($connection, "INSERT INTO bank_accounts (bank_name, account_type, account_number, account_holder, document_type, document_number, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $country = $_POST['payment_bank_country'] ?? 'CO';
    mysqli_stmt_bind_param($stmt, "sssssss", $_POST['bank_name'], $_POST['account_type'], $_POST['account_number'], $_POST['account_holder'], $_POST['document_type'], $_POST['document_number'], $country);
    mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_settings");
    exit;
}

if (isset($_GET['delete_bank'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:index.php?admin_settings"); exit; }
    if (!isset($_GET['csrf']) || !verify_csrf($_GET['csrf'])) { header("Location:index.php?admin_settings"); exit; }
    $id = intval($_GET['delete_bank']);
    $stmt = mysqli_prepare($connection, "DELETE FROM bank_accounts WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location:index.php?admin_settings");
    exit;
}

if (isset($_POST['save_payment'])) {
    if (!isset($_SESSION['user_id'])) { header("Location:login.php"); exit; }
    require_csrf();
    $booking_id = intval($_POST['pay_booking_id']);
    $amount = floatval($_POST['pay_amount']);
    $method = trim($_POST['pay_method'] ?? __('ajax_cash'));
    $notes = trim($_POST['pay_notes'] ?? '');

    if ($amount <= 0) { header("Location:invoice.php?booking_id=$booking_id&err=invalid"); exit; }

    // Get current booking
    $stmt = mysqli_prepare($connection, "SELECT remaining_price, total_price, payment_status FROM booking WHERE booking_id=?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $b = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if (!$b) { header("Location:invoice.php?booking_id=$booking_id&err=notfound"); exit; }

    mysqli_begin_transaction($connection);

    // Insert payment record
    $pst = mysqli_prepare($connection, "INSERT INTO payments (booking_id, amount, payment_method, notes) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($pst, "idss", $booking_id, $amount, $method, $notes);
    $pay_ok = mysqli_stmt_execute($pst);

    if ($pay_ok) {
        $new_remaining = $b['remaining_price'] - $amount;
        if ($new_remaining < 0) $new_remaining = 0;
        $paid_full = ($new_remaining <= 0) ? 1 : 0;

        $upd = mysqli_prepare($connection, "UPDATE booking SET remaining_price=?, payment_status=? WHERE booking_id=?");
        mysqli_stmt_bind_param($upd, "dii", $new_remaining, $paid_full, $booking_id);
        $upd_ok = mysqli_stmt_execute($upd);

        if ($upd_ok) {
            mysqli_commit($connection);
            header("Location:invoice.php?booking_id=$booking_id&pay=ok");
            exit;
        }
    }
    mysqli_rollback($connection);
    header("Location:invoice.php?booking_id=$booking_id&err=error");
    exit;
}
