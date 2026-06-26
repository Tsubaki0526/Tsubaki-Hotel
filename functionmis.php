<?php
include_once 'db.php';
lang_init();

if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit;
}

if (isset($_POST['submit'])) {
    require_csrf();
    $emp_id = $_POST['emp_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $staff_type_id = $_POST['staff_type_id'];
    $shift_id = $_POST['shift_id'];
    $id_card_type = $_POST['id_card_type'];
    $id_card_no = $_POST['id_card_no'];
    $address = $_POST['address'];
    $contact_no = $_POST['contact_no'];
    $joining_date = strtotime($_POST['joining_date']);
    $salary = $_POST['salary'];

    $stmt = mysqli_prepare($connection, "UPDATE staff SET emp_name=?, staff_type_id=?, shift_id=?, id_card_type=?, id_card_no=?, address=?, contact_no=?, joining_date=?, salary=? WHERE emp_id=?");
    mysqli_stmt_bind_param($stmt, "siiisssiii", $first_name . ' ' . $last_name, $staff_type_id, $shift_id, $id_card_type, $id_card_no, $address, $contact_no, $joining_date, $salary, $emp_id);
    if (mysqli_stmt_execute($stmt)) {
        header('Location: index.php?staff_mang');
    } else {
        error_log('Error al actualizar empleado: ' . mysqli_error($connection));
        echo __('ajax_database_error');
    }
    exit;
}

if (isset($_GET['empid'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location:login.php');
        exit;
    }
    if (!isset($_GET['csrf']) || !verify_csrf($_GET['csrf'])) {
        header('Location:login.php?error=csrf');
        exit;
    }
    $emp_id = intval($_GET['empid']);
    $stmt = mysqli_prepare($connection, "DELETE FROM staff WHERE emp_id=?");
    mysqli_stmt_bind_param($stmt, "i", $emp_id);
    if (mysqli_stmt_execute($stmt)) {
        header('Location: index.php?staff_mang');
    } else {
        error_log('Error al eliminar empleado: ' . mysqli_error($connection));
        echo __('ajax_database_error');
    }
    exit;
}
