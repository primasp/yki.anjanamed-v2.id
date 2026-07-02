<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>assets/img/favicon2.png">
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
                            <img class="img-fluid" src="<?= base_url(); ?>assets/img/login-02.png" alt="Logo">
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
                                    <div class="account-logo">
                                        <a href="index.html"><img src="<?= base_url(); ?>assets/img/login-logo.png" alt=""></a>
                                    </div>
                                    <h2>Getting Started</h2>
                                    <?php echo form_open('AuthController/register'); ?>
                                    <?php if ($this->session->flashdata('error')) : ?>
                                        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                                    <?php endif; ?>
                                    <!-- Form -->

                                    <div class="input-block">
                                        <label>Full Name <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" name="fullNameTxt" id="fullNameTxt" value="<?php echo set_value('fullNameTxt'); ?>">
                                        <?php echo form_error('fullNameTxt', '<small class="text-danger">', '</small>'); ?>
                                    </div>
                                    <div class="input-block">
                                        <label>Email <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" name="emailTxt" value="<?php echo set_value('emailTxt'); ?>">
                                        <?php echo form_error('emailTxt', '<small class="text-danger">', '</small>'); ?>
                                    </div>
                                    <div class="input-block">
                                        <label>User Name <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" name="userNameTxt" id="userNameTxt" value="<?php echo set_value('userNameTxt'); ?>">
                                        <?php echo form_error('userNameTxt', '<small class="text-danger">', '</small>'); ?>
                                    </div>

                                    <div class="input-block">

                                        <label>Password <span class="login-danger">*</span></label>
                                        <input class="form-control pass-input" type="password" name="password" id="password" value="<?php echo set_value('password'); ?>">
                                        <span class="profile-views feather-eye-off toggle-password"></span>

                                        <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
                                    </div>






                                    <div class="input-block">
                                        <label>Confirm Password <span class="login-danger">*</span></label>
                                        <input class="form-control pass-input-confirm" type="password" name="confirmPassword" id="confirmPassword" value="<?php echo set_value('confirmPassword'); ?>">
                                        <span class="profile-views feather-eye-off confirm-password"></span>
                                        <?php echo form_error('confirmPassword', '<small class="text-danger">', '</small>'); ?>
                                    </div>

                                    <div id="password-strength" class="strength">Password strength: <span></span></div>

                                    <div class="rules mt-3 mb-3 text-muted">
                                        Password must be at least 8 characters long, contain at least one number, one letter, and one special character.
                                    </div>


                                    <div class="forgotpass">
                                        <div class="remember-me">
                                            <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> I agree to the <a href="javascript:;">&nbsp terms of service </a>&nbsp and <a href="javascript:;">&nbsp privacy policy </a>
                                                <input type="checkbox" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="input-block login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">Sign up</button>
                                    </div>

                                    <!-- /Form -->

                                    <div class="next-sign">
                                        <p class="account-subtitle">Already have account? <a href="<?php echo site_url('AuthController'); ?>">Login</a></p>

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
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            var strengthText = '';
            var strengthClass = '';

            if (password.length < 8) {
                strengthText = 'Too short';
                strengthClass = 'weak';
            } else if (password.match(/[0-9]/) && password.match(/[a-zA-Z]/) && password.match(/[\W]/)) {
                if (password.length >= 12) {
                    strengthText = 'Strong';
                    strengthClass = 'strong';
                } else {
                    strengthText = 'Medium';
                    strengthClass = 'medium';
                }
            } else {
                strengthText = 'Weak';
                strengthClass = 'weak';
            }

            var strengthElement = document.getElementById('password-strength');
            strengthElement.style.display = 'block';
            strengthElement.querySelector('span').textContent = strengthText;
            strengthElement.className = 'strength ' + strengthClass;
        });
    </script>

</body>

</html>