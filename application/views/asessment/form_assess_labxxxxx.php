<?php
/* @var $pasien object */
/* @var $user object */
?>

<style>
    .section-title {
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 10px;
        border-bottom: 2px solid #000;
        padding-bottom: 3px;
    }

    .form-label {
        font-weight: 600;
    }

    /* --- CSS VISUAL (Meniru Kertas Form) --- */
    .paper-form {
        font-family: 'Arial Narrow', Arial, sans-serif;
        font-size: 12px;
        /* Font lebih kecil agar muat */
        color: #000;
        background-color: #fff;
        padding: 30px 40px;
        border: 1px solid #ccc;
    }

    /* Header */
    .header-kop {
        border-bottom: 2px solid #000;
        margin-bottom: 10px;
        padding-bottom: 5px;
    }

    .header-kop h3,
    .header-kop h4 {
        margin: 0;
        font-weight: bold;
        text-align: center;
    }

    /* Section Blocks */
    .section-title {
        background-color: #e9ecef;
        padding: 3px 10px;
        font-weight: bold;
        border: 1px solid #000;
        margin-top: 10px;
        margin-bottom: 5px;
        text-transform: uppercase;
        font-size: 13px;
    }

    .sub-label {
        font-weight: bold;
        margin-right: 5px;
    }

    /* Custom Input Styles (Garis Bawah Saja) */
    .form-control {
        border: none;
        border-bottom: 1px dotted #000;
        border-radius: 0;
        padding: 0 2px;
        background: transparent;
        height: 20px;
        font-size: 12px;
        display: inline-block;
    }

    .form-line.is-invalid {
        border-bottom: 2px solid #dc3545 !important;
        box-shadow: 0 2px 0 #dc3545;
    }


    .form-line:focus {
        box-shadow: none;
        border-bottom: 1px solid blue;
    }

    /* Width Utilities */
    .w-20 {
        width: 20px;
    }

    .w-30 {
        width: 30px;
    }

    .w-50px {
        width: 50px;
    }

    .w-80px {
        width: 80px;
    }

    .w-100px {
        width: 100px;
    }

    .w-full {
        width: 100%;
    }

    /* Checkbox & Radio compact */
    .form-check {
        margin-right: 10px;
        margin-bottom: 2px;
    }

    .form-check-input {
        margin-top: 2px;
    }

    .form-check-label {
        font-size: 12px;
    }

    /* Visual Diagram Serviks (Bulatan) */
    .cervix-diagram {
        width: 120px;
        height: 120px;
        border: 2px solid #000;
        border-radius: 50%;
        position: relative;
        margin: 10px auto;
    }

    .cervix-cross-v {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        border-left: 1px solid #000;
    }

    .cervix-cross-h {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        border-top: 1px solid #000;
    }

    /* Layout Helper */
    .row-compact {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 2px;
    }

    .col-label {
        flex: 0 0 150px;
    }

    /* Kotak Informed Consent */
    .informed-consent-box {
        border: 1px solid #000;
        padding: 10px;
        background-color: #fdfdfd;
        margin-bottom: 10px;
    }


    /* Canvas untuk menggambar */
    #cervixCanvas {
        border: 2px solid #333;
        border-radius: 50%;
        /* Membuat canvas bulat */
        cursor: crosshair;
        background-color: #fff;
        touch-action: none;
        /* Mencegah scroll saat menggambar di HP */
    }

    .canvas-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }

    /* Garis Bantu Cross (Overlay di atas canvas, tapi pointer-events none agar tembus) */
    .cervix-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        /* Klik tembus ke canvas */
        border-radius: 50%;
    }

    .cross-v {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        border-left: 1px dashed #ccc;
    }

    .cross-h {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        border-top: 1px dashed #ccc;
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

    <form id="formAssessLab" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/saveAssessLab'); ?>">
        <input type="hidden" name="episode_id" value="<?= htmlspecialchars($episode_id) ?>">
        <input type="hidden" name="pasien_id" value="<?= htmlspecialchars($pasien->pasien_id ?? '') ?>">

        <div class="card">
            <div class="card-body paper-form">

                <div class="row header-kop align-items-center">
                    <div class="col-2 text-center">
                        <img src="<?= base_url('assets/img/yki_logo.png'); ?>" alt="Logo" style="height: 60px;">
                    </div>
                    <div class="col-8 text-center">
                        <h4>YAYASAN KANKER INDONESIA PROVINSI DKI JAKARTA</h4>
                        <h3>CATATAN MEDIS<br>PEMERIKSAAN DETEKSI DINI KANKER SERVIKS</h3>
                    </div>
                    <div class="col-2"></div>
                </div>

                <div class="informed-consent-box">
                    <div class="section-title mt-0">PERSETUJUAN TINDAKAN MEDIS (INFORMED CONSENT)</div>
                    <div class="mb-2">
                        <span class="fw-bold d-block d-md-inline mb-1 mb-md-0">Menyatakan dengan sesungguhnya selaku :</span>

                        <div class="d-flex flex-wrap ms-md-2" style="gap: 10px;">
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="ic_hubungan" id="hub_diri" value="Diri Sendiri" checked>
                                <label class="form-check-label" for="hub_diri">Saya Sendiri</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="ic_hubungan" id="hub_ortu" value="Orang Tua">
                                <label class="form-check-label" for="hub_ortu">Orang Tua</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="ic_hubungan" id="hub_suami" value="Suami">
                                <label class="form-check-label" for="hub_suami">Suami</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="ic_hubungan" id="hub_istri" value="Istri">
                                <label class="form-check-label" for="hub_istri">Istri</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="ic_hubungan" id="hub_anak" value="Anak">
                                <label class="form-check-label" for="hub_anak">Anak</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="ic_hubungan" id="hub_wali" value="Wali">
                                <label class="form-check-label" for="hub_wali">Wali</label>
                            </div>
                        </div>
                    </div>



                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <span class="me-2" style="white-space: nowrap; min-width: 50px;">Nama :</span>
                                <input type="text" name="ic_nama" id="ic_nama" value="<?= $pasien->nama ?>" class="form-control w-100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <span class="me-2" style="white-space: nowrap;">Jenis Kelamin :</span>
                                <input type="text" name="ic_jk" value="<?= $pasien->sex_id ?? '' ?>" class="form-control w-100" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <span class="me-2" style="white-space: nowrap;">Umur/Tgl Lahir :</span>
                                <input type="text" name="ic_umur" value="<?= $pasien->umur ?> th / <?= $pasien->tgl_lahir ?>" class="form-control w-100">
                            </div>
                        </div>
                    </div>


                    <p class="text-justify mb-4" style="line-height: 1.2;">
                        Saya setuju mengikuti pemeriksaan Deteksi Dini Kanker Leher Rahim dengan metode IVA, Papsmear dan/atau tes DNA HPV serta dilakukan pengambilan foto serviks (DoIVA/DoVIA). Jika ditemukan kelainan prakanker leher rahim, saya dan suami setuju untuk dilakukan penanganan lebih lanjut.
                    </p>

                    <div class="row text-center align-items-end py-3">

                        <div class="col-4">
                            <!-- <input type="text" name="ic_saksi" id="ic_saksi" class="form-control text-center mb-1" > -->
                            <input type="text" name="ic_saksi" id="ic_saksi" class="form-control text-center mb-1" value="<?= $pasien->nama_pasangan ?>">
                            <small>( Suami/Wali/Saksi )</small>
                        </div>

                        <div class="col-4">
                            <input type="text" name="ic_petugas" value="<?= $user['nama'] ?? '' ?>" class="form-control text-center mb-1" readonly>
                            <small>( Petugas )</small>
                        </div>

                        <div class="col-4">
                            <div class="text-end mb-1">
                                Jakarta, <?= date('d/m/Y') ?>
                            </div>

                            <input type="text" name="ttd_pasien" class="form-control text-center mb-1" value="<?= $pasien->nama ?>">
                            <small>( Pasien )</small>
                        </div>

                    </div>
                </div>

                <div class="section-title">I. IDENTITAS</div>

                <div class="row g-3">

                    <!-- ========================= -->
                    <!-- KOLOM KIRI -->
                    <!-- ========================= -->
                    <div class="col-md-6">

                        <!-- Nama -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Nama Klien</label>
                            <div class="col-8">
                                <input type="text" name="nama" value="<?= $pasien->nama ?>" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- Suku & Agama -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Asal / Suku</label>

                            <div class="col-3">
                                <input type="text" name="suku" class="form-control" value="<?= $pasien->nama ?>">
                            </div>

                            <label class="col-2 form-label text-end mb-0">Agama</label>

                            <div class="col-3">
                                <select name="agama" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($agama as $a) : ?>
                                        <option value="<?= $a->global_id ?>" <?= ($pasien->agama_id == $a->global_id) ? 'selected' : '' ?>>
                                            <?= $a->keterangan ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Alamat</label>
                            <div class="col-8">
                                <input type="text" name="alamat" value="<?= $pasien->alamat1 ?>" class="form-control">
                            </div>
                        </div>

                        <!-- Kecamatan -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Kecamatan</label>
                            <div class="col-8">
                                <input type="text" name="kecamatan" class="form-control" value="<?= $pasien->kecamatan ?>">
                            </div>
                        </div>

                        <!-- Kab/Kota & Prov -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Kab/Kota</label>

                            <div class="col-4">
                                <input type="text" name="kab_kota" class="form-control" value="<?= $pasien->kabupaten ?>">
                            </div>

                            <label class="col-2 form-label text-end mb-0">Prov</label>

                            <div class="col-2">
                                <input type="text" name="provinsi" value="<?= $pasien->provinsi ?>" class="form-control">
                            </div>
                        </div>

                    </div>

                    <!-- ========================= -->
                    <!-- KOLOM KANAN -->
                    <!-- ========================= -->
                    <div class="col-md-6">

                        <!-- NIK -->
                        <div class="row mb-2 align-items-center">
                            <label class="col-2 form-label mb-0">NIK</label>
                            <div class="col-9">
                                <input type="text" name="nik" value="<?= $pasien->no_identitas ?>" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- Tempat/Tgl Lahir & Usia -->
                        <div class="row mb-2 align-items-center">
                            <label class="col-2 form-label mb-0">Tmpt / Tgl Lahir</label>

                            <div class="col-6">
                                <input type="text" class="form-control" value="<?= $pasien->tempat_lahir_txt ?>, <?= $pasien->tgl_lahir ?>" readonly>
                            </div>

                            <label class="col-2 form-label text-end mb-0">Usia</label>

                            <div class="col-2 d-flex align-items-center">
                                <input type="text" value="<?= $pasien->umur ?>" class="form-control text-center me-1">
                                <span></span>
                            </div>
                        </div>

                        <!-- BB / TB / Gol Darah -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Berat Badan</label>

                            <div class="col-2">
                                <input type="number" name="bb" class="form-control text-center">
                            </div>
                            <label class="col-2 form-label text-end mb-0">Tinggi</label>

                            <div class="col-2">
                                <input type="number" name="tb" class="form-control text-center">
                            </div>

                            <label class="col-2 form-label text-end mb-0">Gol Darah</label>
                            <div class="col-2">
                                <input type="text" name="gol_darah" class="form-control text-center" value="<?= $pasien->pas_goldar_id ?>">
                            </div>
                        </div>

                        <!-- Telp/HP -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Telp / HP</label>
                            <div class="col-8">
                                <input type="text" name="telp" value="<?= $pasien->no_selular ?>" class="form-control">
                            </div>
                        </div>

                        <!-- Nama Suami -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-2 form-label mb-0">Nama Pasangan</label>
                            <div class="col-8">
                                <input type="text" name="nama_suami" class="form-control" value="<?= $pasien->nama_pasangan ?>">
                            </div>
                        </div>

                    </div>

                </div>





                <div class="section-title">II. RIWAYAT IDENTITAS</div>
                <div class="row">
                    <div class="col-md-6 border-end">


                        <?php
                        $selectedSK = $pasien->marital_status_id ?? '';
                        ?>

                        <div class="row align-items-center mb-1">
                            <div class="col-3">a. Status kawin klien</div>
                            <div class="col-auto px-0">:</div>
                            <div class="col">

                                <?php foreach ($statusKawin as $i => $sk) : ?>
                                    <?php
                                    $id = "sk_" . strtolower($sk['global_id']);
                                    $checked = ($selectedSK == $sk['global_id']) ? "checked" : "";
                                    ?>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="status_kawin" value="<?= $sk['global_id'] ?>" id="<?= $id ?>" class="form-check-input" <?= $checked ?>>

                                        <label for="<?= $id ?>" class="form-check-label">
                                            <?= $sk['keterangan'] ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>


                        <div class="row align-items-center mb-2">
                            <div class="col-3 ps-4 text-muted">- Hub. seks pranikah</div>
                            <div class="col-auto px-0">:</div>
                            <div class="col">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="seks_pranikah" value="Pernah" class="form-check-input" id="sp_pernah">
                                    <label for="sp_pernah">Pernah</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="seks_pranikah" value="Tidak Pernah" class="form-check-input" id="sp_tidak">
                                    <label for="sp_tidak">Tidak Pernah</label>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-2">
                            <div class="col-3">b. Status kawin suami</div>
                            <div class="col-auto px-0">:</div>
                            <div class="col d-flex align-items-center">
                                <span class="me-2 text-nowrap">Menikah ke</span>
                                <input type="text" name="suami_menikah_ke" class="form-control text-center" style="width: 50px;">
                            </div>
                        </div>



                        <?php
                        $selectedPD = $pasien->pendidikan_id ?? '';
                        ?>

                        <div class="row align-items-center mb-1">
                            <div class="col-3">c. Pendidikan klien</div>
                            <div class="col-auto px-0">:</div>
                            <div class="col">

                                <?php foreach ($pendidikan as $i => $pd) : ?>
                                    <?php
                                    $id = "pd_" . strtolower($pd['global_id']);
                                    $checked = ($selectedPD == $pd['global_id']) ? "checked" : "";
                                    ?>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="edu_klien" value="<?= $pd['global_id'] ?>" id="<?= $id ?>" class="form-check-input" <?= $checked ?>>

                                        <label for="<?= $id ?>" class="form-check-label">
                                            <?= $pd['keterangan'] ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>

















                    </div>

                    <div class="col-md-6">

                        <div class="row align-items-start mb-2">
                            <div class="col-3 pt-1">d. Pendidikan Suami</div>
                            <div class="col-auto px-0 pt-1">:</div>
                            <div class="col">
                                <div class="row g-2">



                                    <?php foreach ($pendidikan as $i => $ps) : ?>
                                        <?php
                                        $id = "ps_" . strtolower($ps['global_id']);
                                        // $checked = ($selectedPD == $pd['global_id']) ? "checked" : "";
                                        ?>
                                        <div class="col-auto">
                                            <div class="form-check form-check-inline mb-0">
                                                <input type="radio" name="edu_suami" value="<?= $ps['global_id'] ?>" id="<?= $id ?>" class="form-check-input">

                                                <label for="edu_<?= $id ?>" class="form-check-label">
                                                    <?= $ps['keterangan'] ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>


                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-2">
                            <div class="col-3">e. Pekerjaan Anda</div>
                            <div class="col-auto px-0">:</div>
                            <div class="col">
                                <input type="text" name="pekerjaan_klien" class="form-control w-100">
                            </div>
                        </div>

                        <div class="row align-items-center mb-2">
                            <div class="col-3">f. Pekerjaan Suami</div>
                            <div class="col-auto px-0">:</div>
                            <div class="col">
                                <input type="text" name="pekerjaan_suami" class="form-control w-100">
                            </div>
                        </div>

                    </div>
                </div>





                <div class="section-title mt-3">III. RIWAYAT KESEHATAN REPRODUKSI & PERILAKU</div>


                <div class="row mb-2">

                    <!-- KIRI -->
                    <div class="col-md-6 border-end pe-md-4">

                        <!-- a -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-4 col-md-4 fw-bold">a. Usia pertama haid</label>
                            <div class="col-4 d-flex align-items-center">
                                <input type="number" name="fl_usia_haid" class="form-control w-num me-1">
                                <span>th</span>
                            </div>
                        </div>

                        <!-- b -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-4 fw-bold">b. Usia pertama kawin</label>
                            <div class="col-4 d-flex align-items-center">
                                <input type="number" name="fl_usia_kawin" class="form-control w-num me-1">
                                <span>th</span>
                            </div>
                        </div>

                        <!-- c -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-4 fw-bold">c. Usia pertama hamil</label>
                            <div class="col-4 d-flex align-items-center">
                                <input type="number" name="fl_usia_hamil" class="form-control w-num me-1">
                                <span>th</span>
                            </div>
                        </div>

                    </div>


                    <!-- KANAN -->
                    <div class="col-md-6 ps-md-4">

                        <!-- HPHT + menopuse -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 fw-bold">HPHT</label>

                            <div class="col-sm-9">
                                <div class="d-flex align-items-center flex-wrap gap-2">

                                    <!-- HPHT -->
                                    <input type="date" name="fl_hpht" class="form-control" style="max-width:160px;">

                                    <!-- Menopause -->
                                    <span class="ms-2">Menopause:</span>
                                    <input type="number" name="fl_usia_menopause" class="form-control w-num" style="max-width:70px;">
                                    <span>th</span>

                                </div>
                            </div>
                        </div>


                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 fw-bold">Siklus Haid</label>

                            <div class="col-sm-8 d-flex flex-wrap gap-3">

                                <?php
                                $siklusOptions = [
                                    'Teratur'        => 'teratur',
                                    'Tidak Teratur'  => 'tidak_teratur'
                                ];
                                ?>

                                <?php foreach ($siklusOptions as $label => $id) : ?>
                                    <label class="form-check d-flex align-items-center mb-0 me-3">
                                        <input type="radio" name="fl_siklus_haid" value="<?= $label ?>" id="siklus_<?= $id ?>" class="form-check-input me-1">
                                        <span><?= $label ?></span>
                                    </label>
                                <?php endforeach; ?>

                            </div>
                        </div>






                        <!-- Jumlah melahirkan -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 fw-bold">Jumlah Melahirkan</label>

                            <div class="col-sm-9">
                                <div class="d-flex flex-wrap align-items-center gap-2">

                                    <!-- Jumlah melahirkan -->
                                    <input type="number" name="fl_jml_lahir" class="form-control w-num" style="max-width:80px;">
                                    <span>kali</span>

                                    <!-- Checkbox Keguguran -->
                                    <label class="form-check d-flex align-items-center mb-0 ms-2">
                                        <input type="checkbox" name="fl_cek_gugur" id="fl_cek_gugur" class="form-check-input me-1">
                                        Keguguran
                                    </label>

                                    <!-- Input jumlah keguguran -->
                                    <div id="fl_box_gugur" class="d-none d-flex align-items-center gap-2 ms-1">
                                        <span>:</span>
                                        <input type="number" name="fl_jml_gugur" class="form-control w-num" style="max-width:80px;">
                                        <span>kali</span>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <hr>

                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">e. KB Riwayat</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_kb_status" value="Tidak Pernah" class="form-check-input me-1"> Tidak Pernah
                        </label>

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_kb_status" value="Pernah" class="form-check-input me-1"> Pernah
                        </label>

                        <div id="fl_box_kb_riwayat" class="d-none d-flex flex-wrap gap-3 ms-3 ps-3 border-start">

                            <?php
                            $kbList = ["Pil", "Suntik", "IUD", "Steril", "Kondom", "Implant", "Lainnya"];
                            foreach ($kbList as $kb) : ?>
                                <label class="form-check mb-0">
                                    <input type="checkbox" name="fl_kb_riwayat[]" value="<?= $kb ?>" class="form-check-input me-1">
                                    <?= $kb ?>
                                </label>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>


                <hr>
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">f. KB Saat Ini</label>
                    <div class="col-9 d-flex flex-wrap gap-3">
                        <div id="fl_box_kb_now" class="d-flex flex-wrap gap-3">
                            <?php
                            $kbListNow = ["Pil", "Suntik", "IUD", "Steril", "Kondom", "Implant", "Lainnya", "Tidak Pakai"];
                            foreach ($kbListNow as $kbNow) : ?>
                                <label class="form-check mb-0">
                                    <input type="checkbox" name="fl_kb_now[]" value="<?= $kbNow ?>" class="form-check-input me-1">
                                    <?= $kbNow ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <hr>

                <!-- PAP -->
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">g. Pap Smear</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_pap_status" value="Tidak Pernah" class="form-check-input me-1"> Tdk Pernah
                        </label>

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_pap_status" value="Pernah" class="form-check-input me-1"> Pernah
                        </label>

                        <div id="fl_box_pap_thn" class="d-none d-flex align-items-center gap-2">
                            <span>Thn:</span>
                            <input type="text" id="fl_pap_thn" name="fl_pap_thn" class="form-control w-year" placeholder="YYYY">
                        </div>
                    </div>
                </div>

                <!-- IVA -->
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">h. IVA</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_iva_status" value="Tidak Pernah" class="form-check-input me-1"> Tdk Pernah
                        </label>

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_iva_status" value="Pernah" class="form-check-input me-1"> Pernah
                        </label>

                        <div id="fl_box_iva_thn" class="d-none d-flex align-items-center gap-2">
                            <span>Thn:</span>
                            <input type="text" name="fl_iva_thn" class="form-control w-year" placeholder="YYYY">
                        </div>
                    </div>
                </div>




                <hr>

                <div class="row">

                    <!-- Pasien merokok -->
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center">
                            <label class="fw-bold me-3" style="width:220px;">i. Merokok</label>

                            <?php
                            $rokok = [
                                ["Tidak", "fl_rp_no"],
                                ["Ya", "fl_rp_yes"],
                                ["Pernah", "fl_rp_pernah"]
                            ];
                            foreach ($rokok as $r) : ?>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_rokok_pasien" value="<?= $r[0] ?>" id="<?= $r[1] ?>" class="form-check-input me-1 fl-rokok-group" data-target="#fl_box_rokok_pasien">
                                    <?= $r[0] ?>
                                </label>
                            <?php endforeach; ?>

                            <div id="fl_box_rokok_pasien" class="d-none ms-3 d-flex align-items-center">
                                <input type="number" name="fl_rokok_pasien_jml" class="form-control w-num me-2" style="max-width:80px;">
                                <span class="mt-1">btg/hari</span>
                            </div>
                        </div>
                    </div>

                    <!-- Suami -->
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold me-3 ps-3" style="width:220px;">- Suami merokok</span>

                            <?php foreach ($rokok as $r) : ?>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_rokok_suami" value="<?= $r[0] ?>" class="form-check-input me-1 fl-rokok-group" data-target="#fl_box_rokok_suami">
                                    <?= $r[0] ?>
                                </label>
                            <?php endforeach; ?>

                            <div id="fl_box_rokok_suami" class="d-none ms-3 d-flex align-items-center">
                                <input type="number" name="fl_rokok_suami_jml" class="form-control w-num me-2" style="max-width:80px;">
                                <span class="mt-1">btg/hari</span>
                            </div>
                        </div>
                    </div>

                    <!-- Serumah -->
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold me-3 ps-3" style="width:220px;">- Anggota serumah</span>

                            <?php foreach ($rokok as $r) : ?>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_rokok_rumah" value="<?= $r[0] ?>" class="form-check-input me-1 fl-rokok-group" data-target="#fl_box_rokok_rumah">
                                    <?= $r[0] ?>
                                </label>
                            <?php endforeach; ?>

                            <div id="fl_box_rokok_rumah" class="d-none ms-3 d-flex align-items-center">
                                <input type="number" name="fl_rokok_rumah_jml" class="form-control w-num me-2" style="max-width:80px;">
                                <span class="mt-1">btg/hari</span>
                            </div>
                        </div>
                    </div>

                </div>







                <div class="section-title mt-3">IV. RIWAYAT KANKER DALAM KELUARGA</div>

                <div class="row align-items-center mb-2">
                    <div class="col-md-4 col-12">
                        <span class="fw-bold">a. Ada keluarga yang terkena :</span>
                    </div>
                    <div class="col-md-8 col-12">
                        <div class="d-flex align-items-center">
                            <div class="form-check form-check-inline mb-0 me-3">
                                <input type="radio" name="fl_kel_kanker" value="Ada" class="form-check-input fl-toggle-kanker" id="fl_kel_ada">
                                <label for="fl_kel_ada">Ada</label>
                            </div>
                            <div class="form-check form-check-inline mb-0 me-3">
                                <input type="radio" name="fl_kel_kanker" value="Tidak Ada" class="form-check-input fl-toggle-kanker" id="fl_kel_tidak">
                                <label for="fl_kel_tidak">Tidak ada</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input type="radio" name="fl_kel_kanker" value="Tidak Tahu" class="form-check-input fl-toggle-kanker" id="fl_kel_tahu">
                                <label for="fl_kel_tahu">Tidak tahu</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="fl_box_kel_detail" class="d-none bg-light p-2 border rounded mb-2 ms-md-4">
                    <div class="row align-items-start mb-2">
                        <div class="col-md-3 col-12 text-md-end">
                            <span class="me-2 fw-bold">- Jika ada, siapa :</span>
                        </div>

                        <div class="col-md-9 col-12">
                            <div class="d-flex flex-wrap gap-3">

                                <?php
                                $kelOptions = [
                                    'Ayah', 'Ibu', 'Bibi', 'Paman',
                                    'Kakek', 'Nenek', 'Kakak', 'Adik'
                                ];

                                // Untuk keperluan edit (array)
                                $selectedKel = isset($data_kaji['fl_kel_siapa'])
                                    ? explode(',', $data_kaji['fl_kel_siapa'])
                                    : [];
                                ?>

                                <?php foreach ($kelOptions as $opt) : ?>
                                    <?php $id = 'kel_' . strtolower($opt); ?>

                                    <label class="form-check form-check-inline m-0 d-flex align-items-center">
                                        <input type="checkbox" name="fl_kel_siapa[]" value="<?= $opt ?>" id="<?= $id ?>" class="form-check-input me-1" <?= in_array($opt, $selectedKel) ? 'checked' : '' ?>>
                                        <span><?= $opt ?></span>
                                    </label>

                                <?php endforeach; ?>

                            </div>
                        </div>

                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-3 col-12 text-md-end">
                            <span class="me-2 fw-bold">- Kanker apa :</span>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="text" name="fl_kel_jenis" id="fl_kel_jenis" class="form-control w-100">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center mb-2">
                    <div class="col-md-4 col-12">
                        <span class="fw-bold">b. Klien yang terkena :</span>
                    </div>
                    <div class="col-md-8 col-12">
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="form-check form-check-inline mb-0 me-3">
                                <input type="radio" name="fl_klien_kanker" value="Ada" class="form-check-input fl-toggle-klien" id="fl_klien_ada">
                                <label for="fl_klien_ada">Ada</label>
                            </div>
                            <div class="form-check form-check-inline mb-0 me-3">
                                <input type="radio" name="fl_klien_kanker" value="Tidak Ada" class="form-check-input fl-toggle-klien" id="fl_klien_tidak">
                                <label for="fl_klien_tidak">Tidak ada</label>
                            </div>

                            <div id="fl_box_klien_jenis" class="d-none align-items-center flex-fill ms-2 border-start ps-2">
                                <span class="me-2 text-nowrap">Jika terkena, kanker apa :</span>
                                <input type="text" name="fl_klien_jenis" id="fl_klien_jenis" class="form-control w-100">
                            </div>
                        </div>
                    </div>
                </div>



                <div class="section-title mt-3">V. KELUHAN</div>

                <div class="row">
                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">a. Keluhan banyak cairan dari kemaluan/keputihan</div>
                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_cairan" value="Ada" class="form-check-input fl-keluhan-group" data-target="#fl_box_cairan_lama" id="fl_ka_ada">
                                    <label for="fl_ka_ada">Ada</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_cairan" value="Tidak" class="form-check-input fl-keluhan-group" data-target="#fl_box_cairan_lama" id="fl_ka_tidak">
                                    <label for="fl_ka_tidak">Tidak</label>
                                </div>
                                <div id="fl_box_cairan_lama" class="d-none align-items-center ms-2">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_cairan" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">b. Sakit nyeri di perut bagian bawah/panggul</div>
                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_nyeri" value="Ada" class="form-check-input fl-keluhan-group" data-target="#fl_box_nyeri_lama" id="fl_nyeri_ada">
                                    <label for="fl_nyeri_ada">Ada</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_nyeri" value="Tidak" class="form-check-input fl-keluhan-group" data-target="#fl_box_nyeri_lama" id="fl_nyeri_tidak">
                                    <label for="fl_nyeri_tidak">Tidak</label>
                                </div>
                                <div id="fl_box_nyeri_lama" class="d-none align-items-center ms-2">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_nyeri" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">c. Pendarahan bila/setelah senggama</div>
                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_senggama" value="Ada" class="form-check-input fl-keluhan-group" data-target="#fl_box_senggama_lama" id="fl_kc_ada">
                                    <label for="fl_kc_ada">Ada</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_senggama" value="Tidak" class="form-check-input fl-keluhan-group" data-target="#fl_box_senggama_lama" id="fl_kc_tidak">
                                    <label for="fl_kc_tidak">Tidak</label>
                                </div>
                                <div id="fl_box_senggama_lama" class="d-none align-items-center ms-2">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_senggama" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">d. Pendarahan di luar haid</div>
                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_nonhaid" value="Ada" class="form-check-input fl-keluhan-group" data-target="#fl_box_nonhaid_lama" id="fl_kd_ada">
                                    <label for="fl_kd_ada">Ada</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_nonhaid" value="Tidak" class="form-check-input fl-keluhan-group" data-target="#fl_box_nonhaid_lama" id="fl_kd_tidak">
                                    <label for="fl_kd_tidak">Tidak</label>
                                </div>
                                <div id="fl_box_nonhaid_lama" class="d-none align-items-center ms-2">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_nonhaid" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5 d-flex align-items-center">
                                <span class="me-2 text-nowrap">e. Lain-lain, sebutkan</span>
                                <input type="text" name="fl_kel_lain_nama" class="form-control w-100">
                            </div>
                            <div class="col-md-7 d-flex align-items-center mt-1 mt-md-0">
                                <span class="me-2">:</span>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_lain" value="Ada" class="form-check-input fl-keluhan-group" data-target="#fl_box_lain_lama" id="fl_ke_ada">
                                    <label for="fl_ke_ada">Ada</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_lain" value="Tidak" class="form-check-input fl-keluhan-group" data-target="#fl_box_lain_lama" id="fl_ke_tidak">
                                    <label for="fl_ke_tidak">Tidak</label>
                                </div>
                                <div id="fl_box_lain_lama" class="d-none align-items-center ms-2">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_lain" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="section-title">V. KELUHAN</div>
                <table class="table table-borderless table-sm mb-0" style="font-size: 12px;">
                    <tr>
                        <td width="40%">a. Keluhan banyak cairan dari kemaluan/keputihan</td>
                        <td width="10%">: <input type="checkbox" name="keluhan_cairan"> ada</td>
                        <td width="10%"><input type="checkbox"> tidak</td>
                        <td>Sudah berapa lama <input type="text" name="lama_cairan" class="form-control w-50px"> bulan</td>
                    </tr>
                    <tr>
                        <td>b. Sakit nyeri di perut bagian bawah/panggul</td>
                        <td>: <input type="checkbox" name="keluhan_nyeri"> ada</td>
                        <td><input type="checkbox"> tidak</td>
                        <td>Sudah berapa lama <input type="text" name="lama_nyeri" class="form-control w-50px"> bulan</td>
                    </tr>
                    <tr>
                        <td>c. Pendarahan bila/setelah senggama</td>
                        <td>: <input type="checkbox" name="keluhan_darah_senggama"> ada</td>
                        <td><input type="checkbox"> tidak</td>
                        <td>Sudah berapa lama <input type="text" name="lama_darah_senggama" class="form-control w-50px"> bulan</td>
                    </tr>
                    <tr>
                        <td>d. Pendarahan di luar haid</td>
                        <td>: <input type="checkbox" name="keluhan_darah_nonhaid"> ada</td>
                        <td><input type="checkbox"> tidak</td>
                        <td>Sudah berapa lama <input type="text" name="lama_darah_nonhaid" class="form-control w-50px"> bulan</td>
                    </tr>
                    <tr>
                        <td>e. Lain-lain, sebutkan <input type="text" name="keluhan_lain_text" class="form-control w-100px"></td>
                        <td>: <input type="checkbox" name="keluhan_lain"> ada</td>
                        <td><input type="checkbox"> tidak</td>
                        <td>Sudah berapa lama <input type="text" name="lama_lain" class="form-control w-50px"> bulan</td>
                    </tr>
                </table> -->










                <div class="section-title mt-3">
                    VI. PEMERIKSAAN GINEKOLOGI dan IVA
                    <small class="fw-normal d-block d-md-inline ms-md-2 text-muted" style="font-size: 0.85em;">
                        - Gambarkan / Foto SSK dan kelainan (Polip Serviks, Kondiloma, Ovula Nabothi, dll)
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-7 border-end">

                        <div class="row align-items-center mb-1">
                            <div class="col-4">Kelainan Vulva :</div>
                            <div class="col-8">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vulva" value="Tidak" class="form-check-input" id="vulva_no"> <label for="vulva_no">Tidak</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vulva" value="Ya" class="form-check-input" id="vulva_yes"> <label for="vulva_yes">Ya</label>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col-4">Kelainan Vagina :</div>
                            <div class="col-8">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vagina" value="Tidak" class="form-check-input" id="vagina_no"> <label for="vagina_no">Tidak</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vagina" value="Ya" class="form-check-input" id="vagina_yes"> <label for="vagina_yes">Ya</label>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col-4">Curiga KLR :</div>
                            <div class="col-8">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_curiga_klr" value="Tidak" class="form-check-input" id="klr_no"> <label for="klr_no">Tidak</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_curiga_klr" value="Ya" class="form-check-input" id="klr_yes"> <label for="klr_yes">Ya, Rujuk RS</label>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col-4">Pemeriksaan SSK :</div>
                            <div class="col-8">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_pem_ssk" value="Tampak" class="form-check-input" id="ssk_tampak"> <label for="ssk_tampak">Tampak</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_pem_ssk" value="Tidak Tampak" class="form-check-input" id="ssk_tidak"> <label for="ssk_tidak">Tdk Tampak</label>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col-4">Pengambilan Pap :</div>
                            <div class="col-8">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_ambil_pap" value="Tidak" class="form-check-input fl-toggle-pap" id="pap_no"> <label for="pap_no">Tidak</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_ambil_pap" value="Ya" class="form-check-input fl-toggle-pap" id="pap_yes"> <label for="pap_yes">Ya</label>
                                    </div>
                                    <div id="fl_box_pap_detail" class="d-none align-items-center ms-1" style="font-size: 0.9em;">
                                        Tgl <input type="date" name="fl_tgl_pap" class="form-control mx-1" style="width: 110px;">
                                        Hasil: <input type="text" name="fl_hasil_pap" class="form-control w-100px">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col-4">Tes DNA HPV :</div>
                            <div class="col-8">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_tes_hpv" value="Tidak" class="form-check-input fl-toggle-hpv" id="hpv_no"> <label for="hpv_no">Tidak</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_tes_hpv" value="Ya" class="form-check-input fl-toggle-hpv" id="hpv_yes"> <label for="hpv_yes">Ya</label>
                                    </div>
                                    <div id="fl_box_hpv_detail" class="d-none align-items-center ms-1" style="font-size: 0.9em;">
                                        Tgl <input type="date" name="fl_tgl_hpv" class="form-control mx-1" style="width: 110px;">
                                        Hasil: <input type="text" name="fl_hasil_hpv" class="form-control w-100px">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-4">Foto DoIVA :</div>
                            <div class="col-8">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_foto_doiva" value="Tidak" class="form-check-input fl-toggle-foto" id="foto_no"> <label for="foto_no">Tidak</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_foto_doiva" value="Ya" class="form-check-input fl-toggle-foto" id="foto_yes"> <label for="foto_yes">Ya</label>
                                    </div>
                                    <div id="fl_box_foto_detail" class="d-none align-items-center ms-1">
                                        Nama File: <input type="text" name="fl_kode_file" class="form-control w-100px">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-2 bg-light">
                            <strong class="d-block mb-2 text-decoration-underline">Hasil IVA :</strong>

                            <!-- <div class="form-check mb-1">
                                <input type="checkbox" name="fl_hasil_iva[]" value="Negatif" class="form-check-input">
                                <label>IVA Negatif / Normal (Periksa berkala: 1/3/5 th)</label>
                            </div> -->

                            <div class="d-flex flex-wrap align-items-center mb-1">

                                <div class="form-check mb-0 me-2">
                                    <input type="checkbox" name="fl_hasil_iva[]" value="Negatif" class="form-check-input" id="fl_iva_negatif">
                                    <label class="form-check-label" for="fl_iva_negatif">
                                        IVA Negatif / Normal, anjuran periksa berkala :
                                    </label>
                                </div>

                                <div id="fl_box_iva_berkala" class="d-none align-items-center">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <div class="form-check form-check-inline mb-0 ms-2">
                                            <input class="form-check-input" type="checkbox" name="fl_iva_berkala[]" value="<?= $i ?>" id="iva_th_<?= $i ?>">
                                            <label class="form-check-label" for="iva_th_<?= $i ?>"><?= $i ?></label>
                                        </div>
                                    <?php endfor; ?>

                                    <span class="ms-1">th</span>
                                </div>

                            </div>

                            <div class="d-flex align-items-center mb-1">

                                <div class="form-check me-2 mb-0">
                                    <input type="checkbox" name="fl_hasil_iva[]" value="Radang" class="form-check-input fl-toggle-radang" id="fl_radang_check">
                                    <label class="form-check-label" for="fl_radang_check">Radang/Servisitis :</label>
                                </div>

                                <div id="fl_box_radang" class="d-none d-flex flex-wrap align-items-center">

                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_radang_grade" value="Ringan" class="form-check-input" id="fl_radang_ringan">
                                        <label class="form-check-label" for="fl_radang_ringan">Ringan</label>
                                    </div>

                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_radang_grade" value="Sedang" class="form-check-input" id="fl_radang_sedang">
                                        <label class="form-check-label" for="fl_radang_sedang">Sedang</label>
                                    </div>

                                    <div class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_radang_grade" value="Berat" class="form-check-input" id="fl_radang_berat">
                                        <label class="form-check-label" for="fl_radang_berat">Berat</label>
                                    </div>
                                </div>
                            </div>



                            <!-- <div class="d-flex align-items-center mb-1">
                                <div class="form-check me-2 mb-0">
                                    <input type="checkbox" name="fl_hasil_iva[]" value="Radang" class="form-check-input fl-toggle-radang">
                                    <label>Radang/Servisitis :</label>
                                </div>
                                <div id="fl_box_radang" class="d-none">
                                    <div class="form-check form-check-inline mb-0"><input type="radio" name="fl_radang_grade" value="Ringan" class="form-check-input"> Ringan</div>
                                    <div class="form-check form-check-inline mb-0"><input type="radio" name="fl_radang_grade" value="Sedang" class="form-check-input"> Sedang</div>
                                    <div class="form-check form-check-inline mb-0"><input type="radio" name="fl_radang_grade" value="Berat" class="form-check-input"> Berat</div>
                                </div>
                            </div> -->
                            <div id="fl_box_radang_tindak" class="d-none ps-4 mb-2 small">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="fl_tindak_radang[]" value="Rujuk" class="form-check-input" id="fl_tindak_rujuk">
                                    <label class="form-check-label" for="fl_tindak_rujuk">Rujuk</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="fl_tindak_radang[]" value="Diobati" class="form-check-input" id="fl_tindak_diobati">
                                    <label class="form-check-label" for="fl_tindak_diobati">Diobati</label>
                                </div>
                            </div>

                            <div class="form-check mb-1">
                                <input type="checkbox" name="fl_hasil_iva[]" value="Positif" class="form-check-input fl-toggle-positif" id="fl_iva_positif">
                                <label class="form-check-label" for="fl_iva_positif">IVA Positif</label>
                            </div>

                            <div id="fl_box_positif" class="d-none ps-4 small">
                                <div class="form-check mb-1">
                                    <input type="checkbox" name="fl_tindak_iva[]" value="Krioterapi" class="form-check-input" id="fl_tindak_krio">
                                    <label class="form-check-label" for="fl_tindak_krio">Anjuran segera Krioterapi / TCA 85%</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input type="checkbox" name="fl_tindak_iva[]" value="Rujuk" class="form-check-input" id="fl_tindak_rujuk_luas">
                                    <label class="form-check-label" for="fl_tindak_rujuk_luas">Lesi luas > 75%, Rujuk</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-5 text-center">
                        <label class="fw-bold mb-2">Diagram Serviks</label>

                        <div class="canvas-container mb-2">
                            <canvas id="cervixCanvas" width="200" height="200"></canvas>
                            <div class="cervix-overlay">
                                <div class="cross-v"></div>
                                <div class="cross-h"></div>
                            </div>
                        </div>

                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="clearCanvas"><i class="fa fa-eraser"></i> Hapus</button>
                            <button type="button" class="btn btn-outline-primary" id="undoCanvas"><i class="fa fa-undo"></i> Undo</button>
                        </div>
                        <p class="small text-muted mt-2 fst-italic">
                            *Gunakan mouse/jari untuk menggambar letak kelainan (SSK, Polip, dll) <br><br><br>
                            <textarea name="fl_temuan_lain" class="form-control" rows="2" placeholder="Tulis temuan lain di sini..."></textarea>
                        </p>

                        <input type="hidden" name="fl_gambar_serviks" id="fl_gambar_serviks">
                    </div>
                </div>

                <div class="row mt-3 pt-3 border-top">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2 text-nowrap">Diduga IMS :</span>
                            <input type="text" name="fl_dugaan_ims" class="form-control flex-fill">
                            <div class="ms-3 border-start ps-3">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="fl_tindak_ims[]" value="Rujuk" class="form-check-input" id="fl_ims_rujuk">
                                    <label class="form-check-label" for="fl_ims_rujuk">Rujuk</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="fl_tindak_ims[]" value="Diobati" class="form-check-input" id="fl_ims_diobati">
                                    <label class="form-check-label" for="fl_ims_diobati">Diobati</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span>Pemeriksaan Bimanual :</span>
                            <input type="text" name="fl_pem_bimanual" class="form-control w-100" placeholder="...">
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4 mb-2">
                                Diperiksa Tgl. <input type="date" name="fl_tgl_periksa" value="<?= date('Y-m-d') ?>" class="form-line">
                            </div>
                            <div class="col-md-4 mb-2">
                                Lokasi: <input type="text" name="fl_lokasi_periksa" class="form-control w-75">
                            </div>
                            <div class="col-md-4 mb-2">
                                Oleh dr/Bd: <input type="text" name="fl_pemeriksa_nama" class="form-control w-50">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="section-title">VII. TINDAK LANJUT</div>
                <div class="row">
                    <div class="col-md-12">

                        <div class="d-flex flex-wrap align-items-center mb-2 ps-3">

                            <span class="me-3 fw-bold">Tindakan Prosedur:</span>

                            <div class="form-check form-check-inline mb-0 me-3">
                                <input type="checkbox" name="fl_tl_prosedur[]" value="Krioterapi" id="fl_tl_krio" class="form-check-input">
                                <label class="form-check-label" for="fl_tl_krio">Krioterapi</label>
                            </div>

                            <div class="form-check form-check-inline mb-0 me-4">
                                <input type="checkbox" name="fl_tl_prosedur[]" value="TCA 85%" id="fl_tl_tca" class="form-check-input">
                                <label class="form-check-label" for="fl_tl_tca">TCA 85%</label>
                            </div>

                            <span class="d-inline-flex align-items-center ms-2 me-2">
                                Tgl: <input type="date" name="tgl_krio" class="form-control w-input-md ms-1">
                            </span>

                            <span class="d-inline-flex align-items-center me-2">
                                di: <input type="text" name="loc_krio" class="form-control w-input-md ms-1">
                            </span>

                            <span class="d-inline-flex align-items-center me-2">
                                oleh dr/Bd: <input type="text" name="dr_krio" class="form-control w-input-md ms-1">
                                Tandatangan: ..............
                            </span>
                        </div>

                        <div class="mb-2 ps-3">
                            <span class="fw-bold me-3 d-inline-block">Menggunakan Probe-KrioTip :</span>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_probe_tip[]" value="Ecto cervix sedang" id="probe_ecs" class="form-check-input">
                                    <label class="form-check-label" for="probe_ecs">Ecto cervix sedang</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_probe_tip[]" value="Ecto-Endo cervix sedang" id="probe_ees" class="form-check-input">
                                    <label class="form-check-label" for="probe_ees">Ecto-Endo cervix sedang</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_probe_tip[]" value="Ecto-Endo cervix besar" id="probe_eeb" class="form-check-input">
                                    <label class="form-check-label" for="probe_eeb">Ecto-Endo cervix besar</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 ps-3">
                            <span class="fw-bold me-3 d-block d-md-inline">Pemberian antibiotik profilaksis selama 7 hari :</span>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_antibiotik[]" value="Doksisiklin" id="anti_doxy" class="form-check-input">
                                    <label class="form-check-label" for="anti_doxy">Doksisiklin 2 dd 100 mg</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_antibiotik[]" value="Klindamisin" id="anti_klind" class="form-check-input">
                                    <label class="form-check-label" for="anti_klind">Klindamisin 2 dd 300 mg</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <label>Lainnya: <input type="text" name="obat_lain" class="form-control ms-1 w-input-md"></label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 ps-3">
                            <span class="fw-bold d-block">Menjelaskan kemungkinan keadaan dialami setelah krioterapi/TCA :</span>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_keadaan[]" value="Tidak senggama 1 bulan" id="k_senggama1" class="form-check-input">
                                    <label class="form-check-label" for="k_senggama1">Tidak senggama 1 bulan</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_keadaan[]" value="Tidak senggama 2 minggu" id="k_senggama2" class="form-check-input">
                                    <label class="form-check-label" for="k_senggama2">Tidak senggama 2 minggu</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 ps-3">
                            <div class="d-flex flex-wrap align-items-center gap-3">

                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_gejala[]" value="Keputihan cair" id="g_keputihan" class="form-check-input">
                                    <label class="form-check-label" for="g_keputihan">Keputihan cair (watery discharge) bening (+) 1-2 minggu</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_gejala[]" value="Demam" id="g_demam" class="form-check-input">
                                    <label class="form-check-label" for="g_demam">Adanya demam</label>
                                </div>

                                <span class="ms-3 me-2 fw-bold">Dipesan kontrol Tgl:</span>
                                <input type="date" name="tgl_kontrol" class="form-control w-input-md">
                            </div>
                        </div>

                    </div>
                </div>























                <div class="mt-3 p-2 bg-light border text-center fst-italic">
                    <strong>Petunjuk Pengisian :</strong> Tulislah isian pada titik-titik dan berilah tanda centang (v) yang jelas di dalam kotak pilihan pengisian
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 no-print">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // Jika menggunakan jQuery, bisa ditambahkan logika disable/enable inputan
    // Contoh: Jika "Tidak Pernah" KB, checkbox jenis KB dimatikan.
</script>