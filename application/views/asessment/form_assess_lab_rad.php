<div class="content">

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ============================= -->
    <!-- FORM LABORATORIUM (COLLAPSIBLE) -->
    <!-- ============================= -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseLab" style="cursor:pointer;">
            <h5 class="mb-0">Form Asesment Laboratorium (Papsmear / IVA / DoVIA)</h5>
            <span class="badge bg-light text-primary">Klik untuk buka/tutup</span>
        </div>

        <!-- <div id="collapseLab" class="collapse show"> -->
        <div id="collapseLab" class="collapse">
            <div class="card-body">
                <?php
                $this->load->view('asessment/form_assess_lab', [
                    'user'       => $user,
                    'pasien'     => $pasien,
                    'statusKawin'     => $statusKawin,
                    'episode_id' => $episode_id,
                ]);
                ?>
            </div>
        </div>
    </div>

    <!-- ============================= -->
    <!-- FORM RADIOLOGI (COLLAPSIBLE) -->
    <!-- ============================= -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseRad" style="cursor:pointer;">
            <h5 class="mb-0">Form Asesment Radiologi (Mammografi / USG)</h5>
            <span class="badge bg-light text-success">Klik untuk buka/tutup</span>
        </div>

        <!-- <div id="collapseRad" class="collapse show"> -->
        <div id="collapseRad" class="collapse">
            <div class="card-body">
                <?php
                $this->load->view('asessment/form_assess_rad', [
                    'user'       => $user,
                    'pasien'     => $pasien,
                    'episode_id' => $episode_id,
                ]);
                ?>
            </div>
        </div>
    </div>

</div>