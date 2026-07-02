<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('Pasien-All') ?>">Patients</a>
                    </li>
                    <li class="breadcrumb-item active">Detail Pasien</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-lg-9 col-sm-12">

            <!-- Card: Identitas Utama -->
            <div class="card-box">
                <h3 class="card-title">Data Identitas Utama</h3>

                <div class="row">
                    <div class="col-12">
                        <div class="profile-img-wrap">
                            <img class="inline-block" src="<?= base_url('assets/img/user.jpg') ?>" alt="user">
                        </div>

                        <div class="profile-basic mt-3">
                            <div class="row">

                                <div class="col-md-6">
                                    <label class="small text-muted">Nama Lengkap</label>
                                    <div class="form-control-plaintext fw-bold"><?= $pasien->nama ?></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-muted">Nomor KTP</label>
                                    <div class="form-control-plaintext fw-bold"><?= $pasien->no_identitas ?></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-muted">Tempat Lahir</label>
                                    <div class="form-control-plaintext"><?= $pasien->tempat_lahir_txt ?></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-muted">Tanggal Lahir</label>
                                    <div class="form-control-plaintext">
                                        <?= date('d M Y', strtotime($pasien->tgl_lahir)) ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-muted">Jenis Kelamin</label>
                                    <div class="form-control-plaintext"><?= $pasien->sex_id == "L" ? "Laki-Laki" : "Perempuan" ?></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-muted">No HP</label>
                                    <div class="form-control-plaintext"><?= $pasien->no_selular ?></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat KTP -->
            <div class="card-box">
                <h3 class="card-title">Alamat KTP</h3>
                <div class="row">

                    <div class="col-md-6">
                        <label class="small text-muted">Propinsi</label>
                        <div class="form-control-plaintext"><?= $pasien->propinsi_txt ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted">Kabupaten/Kota</label>
                        <div class="form-control-plaintext"><?= $pasien->kabupaten_txt ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted">Kecamatan</label>
                        <div class="form-control-plaintext"><?= $pasien->kecamatan_txt ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted">Kelurahan</label>
                        <div class="form-control-plaintext"><?= $pasien->kelurahan_txt ?></div>
                    </div>

                    <div class="col-md-12">
                        <label class="small text-muted">Alamat</label>
                        <div class="form-control-plaintext"><?= $pasien->alamat1 ?></div>
                    </div>

                </div>
            </div>

            <!-- Penjamin -->
            <div class="card-box">
                <h3 class="card-title">Penjamin</h3>

                <label class="small text-muted">Penjamin Terakhir</label>
                <div class="form-control-plaintext fw-bold">
                    <?= $pasien->rekanan_txt ?>
                </div>

                <?php if (!empty($pasien->no_kartuprov)) : ?>
                    <label class="small text-muted mt-3">Nomor BPJS</label>
                    <div class="form-control-plaintext"><?= $pasien->no_kartuprov ?></div>
                <?php endif; ?>
            </div>

            <!-- Wali / PJP -->
            <div class="card-box">
                <h3 class="card-title">Penanggung Jawab Pasien</h3>

                <label class="small text-muted">Nama PJP</label>
                <div class="form-control-plaintext"><?= $pasien->pjp_nama ?></div>

                <label class="small text-muted mt-2">No HP PJP</label>
                <div class="form-control-plaintext"><?= $pasien->pjp_hp ?></div>

                <label class="small text-muted mt-2">Hubungan</label>
                <div class="form-control-plaintext"><?= $pasien->pjp_hubungan_txt ?></div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-sm-12">
            <div class="card sticky-sidebar">
                <div class="card-body">

                    <a href="<?= base_url('Pasien-Edit/' . $pasien->pasien_id) ?>" class="btn btn-warning w-100 mb-2">
                        Edit Pasien
                    </a>

                    <!-- <a href="<?= base_url('Cetak-Pasien/' . $pasien->pasien_id) ?>" class="btn btn-primary w-100 mb-2">
                        Cetak PDF
                    </a> -->

                    <a href="<?= base_url('Cetak-Pasien/' . $pasien->pasien_id) ?>" class="btn btn-primary w-100 mb-2" target="_blank" rel="noopener noreferrer">
                        Cetak PDF
                    </a>


                    <a href="<?= base_url('Pasien-All') ?>" class="btn btn-secondary w-100">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>