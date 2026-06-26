<div class="col-sm-9 offset-sm-3 col-lg-10 offset-lg-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active"><?php _e('emp_history_title') ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php _e('emp_history_title') ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('emp_history_shift') ?></div>
                <div class="panel-body">
                    <?php
                    if(isset($_GET['empid'])){
                        $emp_id = $_GET['empid'];
                    }else{
                        header('Location:404.php');
                        exit;
                    }
                    $emp = "SELECT * FROM staff WHERE emp_id = ?";
                    $stmt = mysqli_prepare($connection, $emp);
                    mysqli_stmt_bind_param($stmt, "i", $emp_id);
                    mysqli_stmt_execute($stmt);
                    $emp_result = mysqli_stmt_get_result($stmt);
                    $employee = mysqli_fetch_assoc($emp_result);
                    ?>
                    <p><b><?php _e('emp_history_name') ?>: </b> <?php echo htmlspecialchars($employee['emp_name']); ?></p>
                    <p><b><?php _e('emp_history_salary') ?>: </b> <?php echo htmlspecialchars($employee['salary']).'/-'; ?></p>
                    <div class="table-responsive"><table class="table table-striped table-bordered" cellspacing="0" width="100%"
                           id="rooms">
                        <thead>
                        <tr>
                            <th><?php _e('emp_history_no') ?></th>
                            <th><?php _e('emp_history_shift_name') ?></th>
                            <th><?php _e('emp_history_from') ?></th>
                            <th><?php _e('emp_history_to') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $staff_query = "SELECT * FROM emp_history NATURAL JOIN shift WHERE emp_id = ? ORDER BY created_at DESC";
                        $stmt2 = mysqli_prepare($connection, $staff_query);
                        mysqli_stmt_bind_param($stmt2, "i", $emp_id);
                        mysqli_stmt_execute($stmt2);
                        $staff_result = mysqli_stmt_get_result($stmt2);

                        if (mysqli_num_rows($staff_result) > 0) {
                            $num = 0;
                            while ($staff = mysqli_fetch_assoc($staff_result)) {
                                $num++;
                                ?>
                                <tr>
                                    <td><?php echo $num; ?></td>
                                    <td><?php echo htmlspecialchars($staff['shift'].' - '.$staff['shift_timing']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($staff['from_date'])); ?></td>
                                    <td>
                                        <?php
                                        if ($staff['to_date'] == NULL){
                                            echo "<div class='color-blue'>" . __('emp_history_current') . "</div>";
                                        }else{
                                            echo date('M j, Y', strtotime($staff['to_date']));


                                        }?>
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
