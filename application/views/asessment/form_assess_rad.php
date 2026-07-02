<?php
/* @var $pasien object: {no_rm,nama,umur,alamat,telp} */
?>

<style>
    /* --- CSS VISUAL (Hanya untuk tampilan kertas) --- */
    .paper-form {
        font-family: 'Arial', sans-serif;
        font-size: 14px;
        color: #000;
        background-color: #fff;
        padding: 40px;
    }

    /* Header Kop Surat */
    .kop-surat {
        border-bottom: 3px solid #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .kop-text h4 {
        font-weight: bold;
        font-size: 18px;
        margin: 0;
    }

    .kop-text p {
        margin: 0;
        font-size: 12px;
    }

    /* Kotak Tanggal */
    .box-date {
        border: 1px solid #000;
        padding: 10px;
        margin-bottom: 20px;
    }

    /* Input Garis Bawah Custom */
    .form-line {
        border: none;
        border-bottom: 1px solid #000;
        border-radius: 0;
        padding: 0 5px;
        background: transparent;
        height: 25px;
        font-size: 14px;
        width: 100%;
        /* Default full width di dalam kolomnya */
    }

    .form-line:focus {
        box-shadow: none;
        border-bottom: 2px solid #000;
        background: #f9f9f9;
    }

    /* Checkbox & Radio Scale */
    input[type=radio],
    input[type=checkbox] {
        transform: scale(1.2);
        margin-right: 5px;
    }

    /* Helper untuk titik dua (:) agar rapi */
    .sep {
        margin-right: 8px;
        font-weight: bold;
    }

    /* Utilitas Lebar Input Khusus */
    .w-input-sm {
        max-width: 80px;
        display: inline-block;
    }

    .w-input-md {
        max-width: 150px;
        display: inline-block;
    }
</style>

<div class="content">
    <div class="page-header mb-3 no-print">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- <form id="formRad" method="post" action="<?= base_url('Asessment/saveMammoUsg'); ?>"> -->
    <form id="formAssessRad" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/saveAssessRad'); ?>">
        <input type="hidden" name="episode_id" value="<?= htmlspecialchars($episode_id) ?>">
        <input type="hidden" name="pasien_id" value="<?= htmlspecialchars($pasien->pasien_id ?? '') ?>">
        <input type="hidden" name="created_by" value="<?= htmlspecialchars($user['nama'] ?? '') ?>">

        <div class="card">
            <div class="card-body paper-form">

                <div class="row kop-surat align-items-center">
                    <div class="col-md-2 text-center">
                        <img src="<?= base_url('assets/img/yki_logo.png'); ?>" alt="Logo" style="height: 80px; width: auto;">
                    </div>
                    <div class="col-md-8 text-center kop-text">
                        <h4>YAYASAN KANKER INDONESIA PROVINSI DKI JAKARTA</h4>
                        <p>Jl. Baru Sunter Permai Raya No. 2 Jakarta Utara 14340</p>
                        <p>Telp 021 6509144 (Hunting) Fax 021 6507748</p>
                        <p>www.yki-dki.or.id || email : yki.dki.jakarta@gmail.com</p>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-5 col-sm-12">
                        <div class="box-date">
                            <div class="d-flex align-items-center mb-2">
                                <span style="width: 80px;">Tanggal :</span>
                                <input type="date" name="tgl_pemeriksaan" class="form-control form-control-sm border-0 bg-transparent p-0" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="d-flex align-items-center">
                                <span style="width: 80px;">Lokasi :</span>
                                <input type="text" name="lokasi" class="form-control form-control-sm border-0 border-bottom p-0">
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="text-center font-weight-bold mb-4" style="text-decoration: underline;">
                    CATATAN MEDIS DETEKSI DINI KANKER PAYUDARA
                </h5>

                <div class="identity-section mb-4">
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-2 col-sm-12">Nama</div>

                        <div class="col-md-6 col-sm-12 d-flex align-items-center">
                            <span class="sep me-2">:</span>
                            <input type="text" name="nama" value="<?= htmlspecialchars($pasien->nama ?? '') ?>" class="form-control form-line">
                        </div>

                        <div class="col-md-4 col-sm-12 d-flex align-items-center justify-content-md-end mt-2 mt-md-0">
                            <span class="me-2">Umur :</span>

                            <input type="text" name="umur" value="<?= $pasien->umur ?> ( <?= $pasien->tgl_lahir ?>) " class="form-control form-line" style="max-width: 260px;">
                        </div>
                    </div>

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-2 col-sm-12">Nama Suami</div>
                        <div class="col-md-10 col-sm-12 d-flex align-items-center">
                            <span class="sep">:</span>
                            <input type="text" name="nama_suami" class="form-control form-line" value="<?= $pasien->nama_pasangan ?>">
                        </div>
                    </div>

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-2 col-sm-12">Alamat</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center">
                            <span class="sep">:</span>
                            <input type="text" name="alamat" value="<?= htmlspecialchars($pasien->alamat1 ?? '') ?>" class="form-control form-line">
                        </div>
                        <div class="col-md-4 col-sm-12 d-flex align-items-center mt-2 mt-md-0 justify-content-md-end">
                            <span class="me-2">Tlp :</span>
                            <input type="text" name="telp" value="<?= htmlspecialchars($pasien->no_selular ?? '') ?>" class="form-control form-line w-50">
                        </div>
                    </div>
                </div>

                <hr style="border-top: 3px solid #000;">
                <!-- 
                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Riwayat Kesehatan</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span> <input type="text" name="riwayat_kesehatan" class="form-control form-line">
                    </div>
                </div> -->

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Usia Haid Pertama</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <input type="number" name="haid_pertama" class="form-control form-line w-input-sm text-center">
                        <span class="mx-2">tahun, Jumlah Anak :</span>
                        <input type="number" name="jumlah_anak" class="form-control form-line w-input-sm text-center">
                        <span class="ms-2">Orang</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Jenis KB</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <input type="text" name="jenis_kb" class="form-control form-line w-input-md">
                        <span class="mx-2">Lama Pemakaian :</span>
                        <input type="text" name="lama_kb" class="form-control form-line w-input-sm">
                        <span class="ms-2">Tahun</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">TB / BB</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <input type="number" name="tb" class="form-control form-line w-input-sm text-center">
                        <span class="mx-2">cm /</span>
                        <input type="number" name="bb" class="form-control form-line w-input-sm text-center">
                        <span class="ms-2">kg</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Merokok (Pasien)</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="merokok_pasien" value="Ya">
                            <label class="form-check-label">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="merokok_pasien" value="Tidak">
                            <label class="form-check-label">Tidak,</label>
                        </div>
                        <input type="text" name="merokok_pasien_jml" class="form-control form-line w-input-sm text-center mx-2">
                        <span>btg/bks per hari</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Suami Merokok</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="merokok_suami" value="Ya">
                            <label class="form-check-label">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="merokok_suami" value="Tidak">
                            <label class="form-check-label">Tidak,</label>
                        </div>
                        <input type="text" name="merokok_suami_jml" class="form-control form-line w-input-sm text-center mx-2">
                        <span>btg/bks per hari</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Riwayat Kanker dalam Keluarga</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <input type="text" name="riwayat_kanker_keluarga" class="form-control form-line">
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Ada Keluarga yang Sakit Kanker</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="keluarga_sakit" value="Ada">
                            <label class="form-check-label">Ada</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="keluarga_sakit" value="Tidak">
                            <label class="form-check-label">Tidak,</label>
                        </div>
                        <span class="mx-2">Siapa :</span>
                        <input type="text" name="keluarga_sakit_siapa" class="form-control form-line w-input-md">
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Jenis Kanker</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <input type="text" name="jenis_kanker" class="form-control form-line">
                    </div>
                </div>

                <div class="row mb-2 align-items-start">
                    <div class="col-md-2 col-sm-12 mt-1">Keluhan Payudara</div>
                    <div class="col-md-8 col-sm-12">
                        <div class="d-flex align-items-center mb-2">
                            <span class="sep d-none d-md-block">:</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Nyeri">
                                <label class="form-check-label">Nyeri di payudara</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Benjolan">
                                <label class="form-check-label">Benjolan di payudara</label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="sep d-none d-md-block" style="visibility: hidden;">:</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Lain-lain">
                                <label class="form-check-label">Lain-lain</label>
                            </div>
                            <input type="text" name="keluhan_lain" class="form-control form-line w-75">
                        </div>
                    </div>
                </div>

                <hr style="border-top: 3px solid #000; margin: 30px 0;">

                <div class="p-3 border rounded bg-light">
                    <h6 class="fw-bold text-uppercase text-center mb-3" style="text-decoration: underline;">PERSETUJUAN MEDIS (INFORMED CONSENT)</h6>

                    <p class="mb-2">
                        Dengan ini menyatakan <b>*SETUJU / MENOLAK</b> untuk dilakukan Tindakan Medis berupa :
                    </p>

                    <div class="d-flex flex-wrap gap-4 mb-3 ms-md-4">
                        <label class="d-flex align-items-center">
                            <input type="checkbox" name="tindakan[]" value="Mammografi">
                            <span class="ms-2 fw-bold">Mammografi</span>
                        </label>
                        <label class="d-flex align-items-center">
                            <input type="checkbox" name="tindakan[]" value="USG">
                            <span class="ms-2 fw-bold">USG</span>
                        </label>
                        <div class="d-flex align-items-center">
                            <span class="me-2">Lainnya:</span>
                            <input type="text" name="tindakan_lain" class="form-control form-line w-input-md">
                        </div>
                    </div>

                    <p class="mb-4 text-justify" style="line-height: 1.5;">
                        Dari penjelasan yang diberikan, saya telah mengerti segala hal yang berhubungan dengan pemeriksaan tersebut, serta Tindakan medis yang akan dilakukan dan kemungkinan pasca Tindakan yang dapat terjadi sesuai penjelasan yang diberikan.
                    </p>

                    <div class="row mt-5 text-center">
                        <div class="col-12 text-end mb-4">
                            <p class="mb-0">Jakarta, <?= date('d F Y') ?></p>
                        </div>

                        <div class="col-md-4 col-sm-12 mb-5 mb-md-0">
                            <p class="mb-5">Mengetahui,</p>
                            <input type="text" class="form-control form-line text-center w-75 mx-auto" placeholder="( <?= htmlspecialchars($user['nama']) ?> )">
                            <p class="small mt-1">Dokter/Bidan/Perawat</p>
                        </div>

                        <div class="col-md-4 col-sm-12 mb-5 mb-md-0">
                            <p class="mb-5">&nbsp;</p>
                            <input type="text" name="saksi" class="form-control form-line text-center w-75 mx-auto" placeholder="( ........................... )">
                            <p class="small mt-1">Saksi</p>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <p class="mb-5">&nbsp;</p>
                            <input type="text" name="pasien_ttd" class="form-control form-line text-center w-75 mx-auto" value="( <?= htmlspecialchars($pasien->nama) ?> )">
                            <p class="small mt-1">Pasien</p>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 3px solid #000; margin: 30px 0;">

                <div class="mb-4">
                    <h6 class="fw-bold">HASIL PEMERIKSAAN MAMMOGRAFI / USG</h6>
                    <textarea name="hasil_pemeriksaan" class="form-control border border-dark" rows="5"></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 no-print">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>

            </div>
        </div>
    </form>
</div>