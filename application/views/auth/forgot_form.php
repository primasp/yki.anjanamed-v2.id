<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <title>AnjanaMed | Integrated Medical Platform</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/bootstrap.min.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/feather.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/fontawesome/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css">

</head>

<body>

    <!-- Main Wrapper -->
    <div class="main-wrapper login-body">
        <div class="container-fluid px-0">
            <div class="row">

                <!-- Login logo -->
                <div class="col-lg-6 login-wrap">
                    <div class="login-sec">
                        <div class="log-img">
                            <!-- <img class="img-fluid" src="<?= base_url(); ?>assets/img/login-02.png" alt="Logo"> -->
                            <img class="img-fluid" src="<?= base_url(); ?>assets/img/login-02a.png" alt="Logo">
                        </div>
                    </div>
                </div>
                <!-- /Login logo -->

                <!-- Login Content -->
                <div class="col-lg-6 login-wrap-bg">
                    <div class="login-wrapper">
                        <div class="loginbox">
                            <div class="login-right">
                                <div class="login-right-wrap">
                                    <div class="account-logo text-center">
                                        <a href="#"><img src="<?= base_url(); ?>assets/img/yki_logo.png" alt="" width="40%" height="auto"></a>
                                    </div>
                                    <h2>Forgot Password</h2>
                                    <!-- Form -->
                                    <!-- <form action="login.html"> -->
                                    <?php echo form_open('AuthController/forgot_password'); ?>

                                    <?php if ($this->session->flashdata('message')) : ?>
                                        <div class="alert alert-success">
                                            <?php echo $this->session->flashdata('message'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($this->session->flashdata('error')) : ?>
                                        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                                    <?php endif; ?>
                                    <div class="input-block">
                                        <label>Email <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" name="email" value="<?php echo set_value('email'); ?>">
                                    </div>
                                    <div class="input-block login-btn">
                                        <!-- <button class="btn btn-primary btn-block" type="submit">Reset Password</button> -->
                                        <button class="btn btn-primary-anjana w-100" type="submit">Reset Password</button>
                                    </div>
                                    <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                                    <!-- </form> -->
                                    <?php echo form_close(); ?>
                                    <!-- /Form -->

                                    <div class="next-sign">
                                        <p class="account-subtitle">Need an account? <a href="<?php echo site_url('Login'); ?>">Login</a></p>

                                        <!-- Social Login -->
                                        <div class="social-login">
                                            <a href="javascript:;"><img src="<?= base_url(); ?>assets/img/icons/login-icon-01.svg" alt=""></a>
                                            <a href="javascript:;"><img src="<?= base_url(); ?>assets/img/icons/login-icon-02.svg" alt=""></a>
                                            <a href="javascript:;"><img src="<?= base_url(); ?>assets/img/icons/login-icon-03.svg" alt=""></a>
                                        </div>
                                        <!-- /Social Login -->

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /Login Content -->

            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="<?= base_url(); ?>assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="<?= base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>

    <!-- Feather Js -->
    <script src="<?= base_url(); ?>assets/js/feather.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= base_url(); ?>assets/js/app.js"></script>

</body>

</html>