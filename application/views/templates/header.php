<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>assets/img/favicon2.png">
    <title>AnjanaMed | Integrated Medical Platform</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/bootstrap.min.css">



    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/fontawesome/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/select2.min.css">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap-datetimepicker.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/datatables/datatables.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/datatables/datatables.min.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/feather.css">

    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/primacare.css?v=<?= time() ?>">

    <?php if (!empty($page_css)) : ?>
        <link rel="stylesheet" href="<?= base_url('assets/' . $page_css . '?v=' . time()); ?>">
    <?php endif; ?>

    <!-- <script src="<?= base_url(); ?>assets/plugins/moment/moment.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css"> -->

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/scrollbar/scroll.min.css">



</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <!-- <div class="header-left d-flex align-items-center"> -->
            <!-- <div class="header-left d-flex align-items-center">
                <a href="#">
                    <img src="<?= base_url(); ?>assets/img/yki_logo.png" alt="Primacare Logo" width="35" height="35">
                </a>
            </div> -->
            <div class="header-left">
                <a href="index.html" class="logo">
                    <!-- <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>Pre Clinic</span> -->
                    <!-- <img src="<?= base_url(); ?>assets/img/logo-Prima-Care.png" alt="Primacare Logo" width="35" height="35"> <span>Prima Care</span> -->
                    <img src="<?= base_url(); ?>assets/img/LogoAM.png" alt="Anjanamed Logo" width="42" height="40"> <span><i>Anjana-Med</i></span>

                </a>
            </div>


            <a id="toggle_btn" href="javascript:void(0);"><img src="<?= base_url(); ?>assets/img/icons/bar-icon.svg" alt=""></a>
            <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img src="<?= base_url(); ?>assets/img/icons/bar-icon.svg" alt=""></a>
            <div class="top-nav-search mob-view">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <a class="btn"><img src="<?= base_url(); ?>assets/img/icons/search-normal.svg" alt=""></a>
                </form>
            </div>
            <ul class="nav user-menu float-end">

                <li class="nav-item dropdown has-arrow user-profile-list">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                        <div class="user-names">
                            <h5><?= $user['nama']; ?> </h5>
                            <span><?= $user['role_name']; ?></span>
                        </div>
                        <span class="user-img">
                            <img src="<?= base_url(); ?>assets/img/user-06.jpg" alt="Admin">
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">Edit Profile</a>
                        <a class="dropdown-item" href="#">Settings</a>
                        <a class="dropdown-item" href="<?php echo site_url('AuthController/logout'); ?>">Logout</a>
                    </div>
                </li>
                <!-- <li class="nav-item ">
                    <a href="settings.html" class="hasnotifications nav-link"><img src="<?= base_url(); ?>assets/img/icons/setting-icon-01.svg" alt=""> </a>
                </li> -->
            </ul>
            <div class="dropdown mobile-user-menu float-end">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#">My Profile</a>
                    <a class="dropdown-item" href="#">Edit Profile</a>
                    <a class="dropdown-item" href="#">Settings</a>
                    <!-- <a class="dropdown-item" href="login.html">Logout</a> -->
                    <a class="dropdown-item" href="<?php echo site_url('AuthController/logout'); ?>">Logout</a>
                </div>
            </div>
        </div>