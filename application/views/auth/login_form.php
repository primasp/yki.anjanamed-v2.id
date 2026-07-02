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
                            <!-- <img class="img-fluid" src="assets/img/login-02.png" alt="Logo"> -->
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

                                        <a href="#"><img src="<?= base_url(); ?>assets/img/yki_logo.png" alt="" width="50%" height="auto"></a>
                                    </div>
                                    <h2>Login</h2>
                                    <?php echo form_open('AuthController'); ?>
                                    <?php if ($this->session->flashdata('message')) : ?>
                                        <div class="alert alert-success"><?php echo $this->session->flashdata('message'); ?></div>
                                    <?php endif; ?>
                                    <?php if ($this->session->flashdata('error')) : ?>
                                        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                                    <?php endif; ?>
                                    <!-- Form -->
                                    <form action="index.html">
                                        <div class="input-block">
                                            <label>Username <span class="login-danger">*</span></label>
                                            <input class="form-control" type="text" name="userIdTxt" value="<?php echo set_value('userIdTxt'); ?>">
                                            <?php echo form_error('userIdTxt', '<small class="text-danger">', '</small>'); ?>
                                        </div>
                                        <div class="input-block">
                                            <label>Password <span class="login-danger">*</span></label>
                                            <input class="form-control pass-input" type="password" name="passwordTxt" id="passwordTxt" value="<?php echo set_value('passwordTxt'); ?>">
                                            <span class="profile-views feather-eye-off toggle-password"></span>
                                            <?php echo form_error('passwordTxt', '<small class="text-danger">', '</small>'); ?>
                                        </div>
                                        <input type="hidden" name="device_id" id="device_id_input">
                                        <div class="forgotpass">
                                            <div class="remember-me">
                                                <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
                                                    <input type="checkbox" name="radio">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <a href="<?php echo site_url('Forgot-Password'); ?>">Forgot Password?</a>
                                        </div>
                                        <div class="input-block login-btn">
                                            <!-- <button class="btn btn-primary btn-block" type="submit">Login</button> -->
                                            <button class="btn btn-primary-anjana w-100" type="submit">Login</button>
                                        </div>
                                    </form>
                                    <!-- /Form -->
                                    <?php echo form_close(); ?>
                                    <div class="next-sign">
                                        <p class="account-subtitle">Need an account? <a href="<?php echo site_url('Register'); ?>">Sign Up</a></p>

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

    <script>
        // Ambil device_id dari localStorage
        const deviceId = localStorage.getItem('device_id') || 'unknown_device';
        document.getElementById('device_id_input').value = deviceId;
    </script>

</body>

</html>