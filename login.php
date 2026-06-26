<?php
session_start();
require_once __DIR__ . '/lang/lang_helper.php';
lang_init();
?>
<html>
<head>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css"/>
</head>
<body>

<div class="container">
    <div class="card card-container">
        <img id="profile-img" class="profile-img-card" src="img/htl.png"/>
        
        <br>
        <div class="result">
            <?php
            if (isset($_GET['empty'])){
                echo '<div class="alert alert-danger">' . __('login_empty') . '</div>';
            }elseif (isset($_GET['loginE'])){
                echo '<div class="alert alert-danger">' . __('login_invalid') . '</div>';
            } ?>
        </div>
        <form class="form-signin" data-toggle="validator" action="ajax.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="form-group col-lg-12">
                    <label><?php _e('login_username') ?></label>
                    <input type="text" name="email" class="form-control" placeholder="" required
                           data-error="<?php _e('form_enter_user') ?>">
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-lg-12">
                    <label><?php _e('login_password') ?></label>
                    <input type="password" name="password" class="form-control" placeholder="" required
                           data-error="<?php _e('form_enter_password') ?>">
                    <div class="help-block with-errors"></div>
                </div>
            </div>

            <button class="btn btn-lg btn-success btn-block btn-signin" type="submit" name="login"><?php _e('login_btn') ?></button>

        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/validator.min.js"></script>
</body>
</html>
