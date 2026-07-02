<?php
// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Edit Assessment Radiologi';

$k = $data_kaji; // Alias biar pendek
$keluhan_selected = isset($k['keluhan_payudara']) ? explode(',', $k['keluhan_payudara']) : [];
$tindakan_selected = isset($k['tindakan']) ? explode(',', $k['tindakan']) : [];
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

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($title) ?></li>

                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- <form class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/saveKajiAwalFull'); ?>"> -->
    <form id="formAssessRadEdit" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/updateAssessRad'); ?>">

        <input type="hidden" name="episode_id" value="<?= htmlspecialchars($episode_id) ?>">
        <input type="hidden" name="pasien_id" value="<?= htmlspecialchars($pasien->pasien_id ?? '') ?>">
        <input type="hidden" name="created_by" value="<?= htmlspecialchars($user['nama'] ?? '') ?>">
        <input type="hidden" name="form_id" value="<?= htmlspecialchars($k['form_id'] ?? '') ?>">

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
                                <input type="date" name="tgl_pemeriksaan" class="form-control form-control-sm border-0 bg-transparent p-0" value="<?= htmlspecialchars($k['tgl_pemeriksaan'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="d-flex align-items-center">
                                <span style="width: 80px;">Lokasi :</span>
                                <input type="text" name="lokasi" class="form-control form-control-sm border-0 border-bottom p-0" value="<?= htmlspecialchars($k['lokasi'] ?? '') ?>">

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
                            <span class="sep">:</span>
                            <!-- <input type="text" name="nama" value="<?= htmlspecialchars($pasien->nama ?? '') ?>" class="form-control form-line"> -->
                            <input type="text" name="nama" value="<?= htmlspecialchars($pasien->nama ?? '') ?>" class="form-control form-line" readonly>
                        </div>
                        <div class="col-md-4 col-sm-12 d-flex align-items-center mt-2 mt-md-0 justify-content-md-end">
                            <span class="me-2">Umur :</span>
                            <!-- <input type="number" name="umur" value="<?= htmlspecialchars($pasien->umur ?? '') ?>" class="form-control form-line w-input-sm text-center"> -->
                            <!-- <input type="number" name="umur" value="<?= htmlspecialchars($pasien->umur ?? '') ?>" class="form-control form-line w-input-sm text-center" readonly> -->


                            <input type="text" name="umur" value="<?= $pasien->umur ?> ( <?= $pasien->tgl_lahir ?>) " class="form-control form-line" style="max-width: 260px;">


                            <span class="ms-2">Tahun</span>
                        </div>
                    </div>

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-2 col-sm-12">Nama Suami</div>
                        <div class="col-md-10 col-sm-12 d-flex align-items-center">
                            <span class="sep">:</span>
                            <!-- <input type="text" name="nama_suami" class="form-control form-line"> -->
                            <!-- <input type="text" name="nama_suami" value="<?= htmlspecialchars($k['nama_suami'] ?? '') ?>" class="form-control form-line"> -->
                            <!-- <input type="text" name="nama_suami" class="form-control form-line" value="<?= $pasien->nama_pasangan ?>"> -->
                            <input type="text" name="nama_suami" class="form-control form-line" value="<?= !empty($k['nama_suami']) ? htmlspecialchars($k['nama_suami']) : htmlspecialchars($pasien->nama_pasangan ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-2 col-sm-12">Alamat</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center">
                            <span class="sep">:</span>
                            <!-- <input type="text" name="alamat" value="<?= htmlspecialchars($pasien->alamat1 ?? '') ?>" class="form-control form-line"> -->
                            <input type="text" name="alamat" value="<?= htmlspecialchars($pasien->alamat1 ?? '') ?>" class="form-control form-line" readonly>
                        </div>
                        <div class="col-md-4 col-sm-12 d-flex align-items-center mt-2 mt-md-0 justify-content-md-end">
                            <span class="me-2">Tlp :</span>
                            <!-- <input type="text" name="telp" value="<?= htmlspecialchars($pasien->no_selular ?? '') ?>" class="form-control form-line w-50"> -->
                            <input type="text" name="telp" value="<?= htmlspecialchars($pasien->no_selular ?? '') ?>" class="form-control form-line w-50" readonly>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 3px solid #000;">

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Riwayat Kesehatan</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <!-- <input type="text" name="riwayat_kesehatan" class="form-control form-line"> -->
                        <!-- <input type="text" name="riwayat_kesehatan" value="<?= htmlspecialchars($k['riwayat_kesehatan'] ?? '') ?>" class="form-control form-line"> -->
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Usia Haid Pertama</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <!-- <input type="number" name="haid_pertama" class="form-control form-line w-input-sm text-center"> -->
                        <input type="number" name="haid_pertama" value="<?= htmlspecialchars($k['haid_pertama'] ?? '') ?>" class="form-control form-line w-input-sm text-center">
                        <span class="mx-2">tahun, Jumlah Anak :</span>
                        <!-- <input type="number" name="jumlah_anak" class="form-control form-line w-input-sm text-center"> -->
                        <input type="number" name="jumlah_anak" value="<?= htmlspecialchars($k['jumlah_anak'] ?? '') ?>" class="form-control form-line w-input-sm text-center">
                        <span class="ms-2">Orang</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Jenis KB</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <!-- <input type="text" name="jenis_kb" class="form-control form-line w-input-md"> -->
                        <input type="text" name="jenis_kb" value="<?= htmlspecialchars($k['jenis_kb'] ?? '') ?>" class="form-control form-line w-input-md">
                        <span class="mx-2">Lama Pemakaian :</span>
                        <!-- <input type="text" name="lama_kb" class="form-control form-line w-input-sm"> -->
                        <input type="text" name="lama_kb" value="<?= htmlspecialchars($k['lama_kb'] ?? '') ?>" class="form-control form-line w-input-sm">
                        <span class="ms-2">Tahun</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">TB / BB</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <!-- <input type="number" name="tb" class="form-control form-line w-input-sm text-center"> -->
                        <input type="number" name="tb" value="<?= htmlspecialchars($k['tb'] ?? '') ?>" class="form-control form-line w-input-sm text-center">
                        <span class="mx-2">cm /</span>
                        <!-- <input type="number" name="bb" class="form-control form-line w-input-sm text-center"> -->
                        <input type="number" name="bb" value="<?= htmlspecialchars($k['bb'] ?? '') ?>" class="form-control form-line w-input-sm text-center">
                        <span class="ms-2">kg</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Merokok (Pasien)</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="merokok_pasien" value="Ya"> -->
                            <input class="form-check-input" type="radio" name="merokok_pasien" value="Ya" <?= (isset($k['merokok_pasien']) && $k['merokok_pasien'] == 'Ya') ? 'checked' : '' ?>>
                            <label class="form-check-label">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="merokok_pasien" value="Tidak"> -->
                            <input class="form-check-input" type="radio" name="merokok_pasien" value="Tidak" <?= (isset($k['merokok_pasien']) && $k['merokok_pasien'] == 'Tidak') ? 'checked' : '' ?>>
                            <label class="form-check-label">Tidak,</label>
                        </div>
                        <!-- <input type="text" name="merokok_pasien_jml" class="form-control form-line w-input-sm text-center mx-2"> -->
                        <input type="text" name="merokok_pasien_jml" value="<?= htmlspecialchars($k['merokok_pasien_jml'] ?? '') ?>" class="form-control form-line w-input-sm text-center mx-2">
                        <span>btg/bks per hari</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Suami Merokok</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="merokok_suami" value="Ya"> -->
                            <input class="form-check-input" type="radio" name="merokok_suami" value="Ya" <?= (isset($k['merokok_suami']) && $k['merokok_suami'] == 'Ya') ? 'checked' : '' ?>>
                            <label class="form-check-label">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="merokok_suami" value="Tidak"> -->
                            <input class="form-check-input" type="radio" name="merokok_suami" value="Tidak" <?= (isset($k['merokok_suami']) && $k['merokok_suami'] == 'Tidak') ? 'checked' : '' ?>>
                            <label class="form-check-label">Tidak,</label>
                        </div>
                        <!-- <input type="text" name="merokok_suami_jml" class="form-control form-line w-input-sm text-center mx-2"> -->
                        <input type="text" name="merokok_suami_jml" value="<?= htmlspecialchars($k['merokok_suami_jml'] ?? '') ?>" class="form-control form-line w-input-sm text-center mx-2">
                        <span>btg/bks per hari</span>
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Riwayat Kanker dalam Keluarga</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <!-- <input type="text" name="riwayat_kanker_keluarga" class="form-control form-line"> -->
                        <input type="text" name="riwayat_kanker_keluarga" value="<?= htmlspecialchars($k['riwayat_kanker_keluarga'] ?? '') ?>" class="form-control form-line">
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Ada Keluarga yang Sakit Kanker</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                        <span class="sep d-none d-md-block">:</span>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="keluarga_sakit" value="Ada"> -->
                            <input class="form-check-input" type="radio" name="keluarga_sakit" value="Ada" <?= (isset($k['keluarga_sakit']) && $k['keluarga_sakit'] == 'Ada') ? 'checked' : '' ?>>
                            <label class="form-check-label">Ada</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="keluarga_sakit" value="Tidak"> -->
                            <input class="form-check-input" type="radio" name="keluarga_sakit" value="Tidak" <?= (isset($k['keluarga_sakit']) && $k['keluarga_sakit'] == 'Tidak') ? 'checked' : '' ?>>
                            <label class="form-check-label">Tidak,</label>
                        </div>
                        <span class="mx-2">Siapa :</span>
                        <!-- <input type="text" name="keluarga_sakit_siapa" class="form-control form-line w-input-md"> -->
                        <input type="text" name="keluarga_sakit_siapa" value="<?= htmlspecialchars($k['keluarga_sakit_siapa'] ?? '') ?>" class="form-control form-line w-input-md">
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-2 col-sm-12">Jenis Kanker</div>
                    <div class="col-md-8 col-sm-12 d-flex align-items-center">
                        <span class="sep d-none d-md-block">:</span>
                        <!-- <input type="text" name="jenis_kanker" class="form-control form-line"> -->
                        <input type="text" name="jenis_kanker" value="<?= htmlspecialchars($k['jenis_kanker'] ?? '') ?>" class="form-control form-line">
                    </div>
                </div>

                <div class="row mb-2 align-items-start">
                    <div class="col-md-2 col-sm-12 mt-1">Keluhan Payudara</div>
                    <div class="col-md-8 col-sm-12">
                        <div class="d-flex align-items-center mb-2">
                            <span class="sep d-none d-md-block">:</span>
                            <div class="form-check form-check-inline">
                                <!-- <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Nyeri"> -->
                                <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Nyeri" <?= in_array('Nyeri', $keluhan_selected) ? 'checked' : '' ?>>
                                <label class="form-check-label">Nyeri di payudara</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <!-- <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Benjolan"> -->
                                <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Benjolan" <?= in_array('Benjolan', $keluhan_selected) ? 'checked' : '' ?>>
                                <label class="form-check-label">Benjolan di payudara</label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="sep d-none d-md-block" style="visibility: hidden;">:</span>
                            <div class="form-check form-check-inline">
                                <!-- <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Lain-lain"> -->
                                <input class="form-check-input" type="checkbox" name="keluhan_payudara[]" value="Lain-lain" <?= in_array('Lain-lain', $keluhan_selected) ? 'checked' : '' ?>>
                                <label class="form-check-label">Lain-lain</label>
                            </div>
                            <!-- <input type="text" name="keluhan_lain" class="form-control form-line w-75"> -->
                            <input type="text" name="keluhan_lain" value="<?= htmlspecialchars($k['keluhan_lain'] ?? '') ?>" class="form-control form-line w-75">
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
                            <!-- <input type="checkbox" name="tindakan[]" value="Mammografi"> -->
                            <input type="checkbox" name="tindakan[]" value="Mammografi" <?= in_array('Mammografi', $tindakan_selected) ? 'checked' : '' ?>>
                            <span class="ms-2 fw-bold">Mammografi</span>
                        </label>
                        <label class="d-flex align-items-center">
                            <!-- <input type="checkbox" name="tindakan[]" value="USG"> -->
                            <input type="checkbox" name="tindakan[]" value="USG" <?= in_array('USG', $tindakan_selected) ? 'checked' : '' ?>>
                            <span class="ms-2 fw-bold">USG</span>
                        </label>
                        <div class="d-flex align-items-center">
                            <span class="me-2">Lainnya:</span>
                            <!-- <input type="text" name="tindakan_lain" class="form-control form-line w-input-md"> -->
                            <input type="text" name="tindakan_lain" value="<?= htmlspecialchars($k['tindakan_lain'] ?? '') ?>" class="form-control form-line w-input-md">
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
                            <!-- <input type="text" class="form-control form-line text-center w-75 mx-auto" placeholder="( <?= htmlspecialchars($user['nama']) ?> )"> -->
                            <input type="text" class="form-control form-line text-center w-75 mx-auto" value="( <?= htmlspecialchars($user['nama'] ?? '') ?> )" readonly>
                            <p class="small mt-1">Dokter/Bidan/Perawat</p>
                        </div>

                        <div class="col-md-4 col-sm-12 mb-5 mb-md-0">
                            <p class="mb-5">&nbsp;</p>
                            <!-- <input type="text" name="saksi" class="form-control form-line text-center w-75 mx-auto" placeholder="( ........................... )"> -->
                            <input type="text" name="saksi" value="<?= htmlspecialchars($k['saksi'] ?? '') ?>" class="form-control form-line text-center w-75 mx-auto" placeholder="( ........................... )">
                            <p class="small mt-1">Saksi</p>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <p class="mb-5">&nbsp;</p>
                            <!-- <input type="text" name="pasien_ttd" class="form-control form-line text-center w-75 mx-auto" value="( <?= htmlspecialchars($pasien->nama) ?> )"> -->
                            <input type="text" name="pasien_ttd" class="form-control form-line text-center w-75 mx-auto" value="<?= htmlspecialchars($k['pasien_ttd'] ?? '( ' . $pasien->nama . ' )') ?>">
                            <p class="small mt-1">Pasien</p>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 3px solid #000; margin: 30px 0;">

                <div class="mb-4">
                    <h6 class="fw-bold">HASIL PEMERIKSAAN MAMMOGRAFI / USG</h6>
                    <!-- <textarea name="hasil_pemeriksaan" class="form-control border border-dark" rows="5"></textarea> -->
                    <textarea name="hasil_pemeriksaan" class="form-control border border-dark" rows="5"><?= htmlspecialchars($k['hasil_pemeriksaan'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 no-print">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-secondary">Kembali</a>
                    <!-- <button type="submit" class="btn btn-primary">Simpan Data</button> -->
                    <!-- <button type="button" class="btn btn-primary">Update Data</button> -->

                    <button type="button" id="btnUpdateAssessRad" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Update Assessment
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>































<!-- ====================================================== -->
<!-- SUBMIT -->
<!-- ====================================================== -->
<!-- <div class="d-flex justify-content-end mb-5">
        <a href="<?= base_url('Asessment'); ?>" class="btn btn-light me-2">Kembali</a>
        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
    </div> -->
<!-- </form> -->
</div>