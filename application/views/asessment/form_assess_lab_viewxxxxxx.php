<?php
// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Edit Assessment Laboratorium';

$k = $data_kaji; // Alias biar pendek

// return var_dump($k);
// die;

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

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ul>
            </div>
        </div>
    </div>

    <form id="formAssessLabEdit" method="post" action="<?= base_url('AsessmentController/updateAssessLab'); ?>" class="needs-validation" novalidate>

        <input type="hidden" name="episode_id" value="<?= $episode_id ?>">
        <input type="hidden" name="pasien_id" value="<?= $pasien->pasien_id ?>">
        <input type="hidden" name="created_by" value="<?= htmlspecialchars($user['nama'] ?? '') ?>">
        <input type="hidden" name="form_id" value="<?= htmlspecialchars($k['form_id'] ?? '') ?>">

        <div class="card">
            <div class="card-body paper-form">

                <!-- ========================= -->
                <!-- KOP SURAT -->
                <!-- ========================= -->
                <div class="row header-kop align-items-center mb-3">
                    <div class="col-2 text-center">
                        <img src="<?= base_url('assets/img/yki_logo.png'); ?>" style="height:60px;">
                    </div>
                    <div class="col-8 text-center">
                        <h4>YAYASAN KANKER INDONESIA PROVINSI DKI JAKARTA</h4>
                        <h3>CATATAN MEDIS<br>PEMERIKSAAN DETEKSI DINI KANKER SERVIKS</h3>
                    </div>
                    <div class="col-2"></div>
                </div>

                <!-- ========================= -->
                <!-- INFORMED CONSENT -->
                <!-- ========================= -->
                <div class="section-title mt-0">PERSETUJUAN TINDAKAN MEDIS (INFORMED CONSENT)</div>

                <!-- Hubungan -->
                <div class="mb-2">
                    <span class="fw-bold">Menyatakan selaku :</span>

                    <?php
                    $hub = $k['ic_hubungan'] ?? 'Diri Sendiri';
                    $opsiHub = ["Diri Sendiri", "Orang Tua", "Suami", "Istri", "Anak", "Wali"];
                    ?>

                    <div class="d-flex flex-wrap ms-2 mt-1" style="gap:10px;">
                        <?php foreach ($opsiHub as $h) : ?>
                            <label class="form-check form-check-inline m-0">
                                <input type="radio" name="ic_hubungan" value="<?= $h ?>" class="form-check-input" <?= ($hub === $h ? 'checked' : '') ?>>
                                <span><?= $h ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Nama, JK, Umur -->
                <div class="row mb-3">

                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="me-2">Nama:</span>
                            <input type="text" name="ic_nama" value="<?= $k['ic_nama'] ?? $pasien->nama ?>" class="form-control w-100">
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="me-2">Jenis Kelamin:</span>
                            <input type="text" name="ic_jk" value="<?= $pasien->sex_id ?>" class="form-control w-100" readonly>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="me-2">Umur/Tgl Lahir:</span>
                            <input type="text" name="ic_umur" value="<?= $k['ic_umur'] ?? ($pasien->umur . ' th / ' . $pasien->tgl_lahir) ?>" class="form-control w-100">
                        </div>
                    </div>

                </div>

                <p class="text-justify" style="line-height:1.2; font-size:12px;">
                    Saya setuju mengikuti pemeriksaan Deteksi Dini Kanker Leher Rahim dengan metode IVA, Papsmear
                    dan/atau tes DNA HPV serta dilakukan pengambilan foto serviks (DoIVA/DoVIA). Jika ditemukan
                    kelainan prakanker, saya dan suami setuju untuk dilakukan penanganan lebih lanjut.
                </p>

                <!-- Tanda tangan -->
                <div class="row text-center align-items-end py-3">

                    <div class="col-4">
                        <input type="text" name="ic_saksi" value="<?= $k['ic_saksi'] ?? '' ?>" class="form-control text-center mb-1">
                        <small>( Suami/Wali/Saksi )</small>
                    </div>

                    <div class="col-4">
                        <input type="text" name="ic_petugas" value="<?= $user['nama'] ?>" class="form-control text-center mb-1" readonly>
                        <small>( Petugas )</small>
                    </div>

                    <div class="col-4">
                        <div class="text-end mb-1">
                            Jakarta, <?= date('d/m/Y'); ?>
                        </div>
                        <input type="text" name="ttd_pasien" value="<?= $k['ttd_pasien'] ?? $pasien->nama ?>" class="form-control text-center mb-1">
                        <small>( Pasien )</small>
                    </div>
                </div>


                <!-- ========================= -->
                <!-- IDENTITAS -->
                <!-- ========================= -->
                <div class="section-title">I. IDENTITAS</div>

                <div class="row g-3">

                    <!-- KIRI -->
                    <div class="col-md-6">

                        <div class="row mb-2 align-items-center">
                            <label class="col-3">Nama Klien</label>
                            <div class="col-8">
                                <input type="text" value="<?= $pasien->nama ?>" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-3">Asal/Suku</label>
                            <div class="col-3">
                                <input type="text" name="suku" value="<?= $k['suku'] ?? '' ?>" class="form-control">
                            </div>

                            <label class="col-2 text-end">Agama</label>
                            <div class="col-3">









                                <select name="agama" class="form-select">
                                    <option value="">-- Pilih --</option>

                                    <?php foreach ($agama as $a) : ?>

                                        <option value="<?= $a->global_id ?>" <?php
                                                                                // PRIORITAS: isi dari tabel ases_lab (hasil pemeriksaan)
                                                                                if (!empty($k['agama']) && $k['agama'] == $a->global_id) {
                                                                                    echo 'selected';
                                                                                }
                                                                                // fallback pakai data pasien
                                                                                elseif ($pasien->agama_id == $a->global_id) {
                                                                                    echo 'selected';
                                                                                }
                                                                                ?>>
                                            <?= $a->keterangan ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>






                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-3">Alamat</label>
                            <div class="col-8">
                                <input type="text" name="alamat" value="<?= $k['alamat'] ?? $pasien->alamat1 ?>" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-3">Kecamatan</label>
                            <div class="col-8">
                                <input type="text" name="kecamatan" value="<?= $k['kecamatan'] ?? '' ?>" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-3">Kab/Kota</label>
                            <div class="col-4">
                                <input type="text" name="kab_kota" value="<?= $k['kab_kota'] ?? '' ?>" class="form-control">
                            </div>

                            <label class="col-2 text-end">Prov</label>
                            <div class="col-2">
                                <input type="text" name="provinsi" value="<?= $k['provinsi'] ?? 'DKI Jakarta' ?>" class="form-control">
                            </div>
                        </div>

                    </div>

                    <!-- KANAN -->
                    <div class="col-md-6">

                        <div class="row mb-2 align-items-center">
                            <label class="col-2">NIK</label>
                            <div class="col-8">
                                <input type="text" value="<?= $pasien->no_identitas ?>" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-2">Tmpt/Tgl Lahir</label>
                            <div class="col-6">
                                <input type="text" class="form-control" value="<?= $pasien->tempat_lahir_txt . ', ' . $pasien->tgl_lahir ?>" readonly>
                            </div>

                            <label class="col-2 text-end">Usia</label>
                            <div class="col-2 d-flex align-items-center">
                                <input type="text" value="<?= $pasien->umur ?>" class="form-control text-center me-1">
                                <span>th</span>
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-2">BB</label>
                            <div class="col-2">
                                <input type="number" name="bb" value="<?= $k['bb'] ?? '' ?>" class="form-control text-center">
                            </div>

                            <label class="col-2 text-end">TB</label>
                            <div class="col-2">
                                <input type="number" name="tb" value="<?= $k['tb'] ?? '' ?>" class="form-control text-center">
                            </div>

                            <label class="col-2 text-end">GolD</label>
                            <div class="col-2">
                                <input type="text" name="gol_darah" value="<?= $k['gol_darah'] ?? '' ?>" class="form-control text-center">
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-2">Telp</label>
                            <div class="col-8">
                                <input type="text" name="telp" value="<?= $k['telp'] ?? $pasien->no_selular ?>" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <label class="col-2">Nama Suami</label>
                            <div class="col-8">
                                <input type="text" name="nama_suami" value="<?= $k['nama_suami'] ?? '' ?>" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>








                <div class="section-title">II. RIWAYAT IDENTITAS</div>

                <div class="row">
                    <!-- =========================== -->
                    <!-- KOLOM KIRI -->
                    <!-- =========================== -->
                    <div class="col-md-6 border-end">

                        <!-- a. Status kawin klien -->
                        <!-- <div class="row align-items-center mb-2">
                            <label class="col-4">a. Status kawin klien</label>
                            <div class="col-auto px-0">:</div>
                            <div class="col d-flex flex-wrap gap-2">

                                <?php
                                $opsiKawin = ["Belum", "Menikah", "Janda"];
                                foreach ($opsiKawin as $sk) :
                                ?>
                                    <label class="form-check form-check-inline mb-0">
                                        <input type="radio" class="form-check-input" name="status_kawin" value="<?= $sk ?>" <?= ($k['status_kawin'] ?? '') == $sk ? 'checked' : '' ?>>
                                        <?= $sk ?>
                                    </label>
                                <?php endforeach; ?>

                            </div>
                        </div> -->



                        <div class="row align-items-center mb-2">
                            <label class="col-4">a. Status kawin klien</label>
                            <div class="col-auto px-0">:</div>

                            <div class="col d-flex flex-wrap gap-2">

                                <?php
                                // nilai yang dipilih → jika data edit kosong, fallback ke data pasien
                                $selectedStatus = $k['status_kawin'] ?? $pasien->marital_status_id;

                                foreach ($statusKawin as $sk) :
                                ?>
                                    <label class="form-check form-check-inline mb-0">
                                        <input type="radio" class="form-check-input" name="status_kawin" value="<?= $sk['global_id'] ?>" <?= ($selectedStatus == $sk['global_id']) ? 'checked' : '' ?>>
                                        <?= $sk['keterangan'] ?>
                                    </label>
                                <?php endforeach; ?>

                            </div>
                        </div>










                        <!-- - Hub seks pranikah -->
                        <div class="row align-items-center mb-2">
                            <label class="col-4 text-muted ps-3">- Hub. seks pranikah</label>
                            <div class="col-auto px-0">:</div>
                            <div class="col d-flex flex-wrap gap-2">

                                <?php
                                $opsiSP = ["Pernah", "Tidak Pernah"];
                                foreach ($opsiSP as $sp) :
                                ?>
                                    <label class="form-check form-check-inline mb-0">
                                        <input type="radio" class="form-check-input" name="seks_pranikah" value="<?= $sp ?>" <?= ($k['seks_pranikah'] ?? '') == $sp ? 'checked' : '' ?>>
                                        <?= $sp ?>
                                    </label>
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <!-- b. Status kawin suami -->
                        <div class="row align-items-center mb-2">
                            <label class="col-4">b. Status kawin suami</label>
                            <div class="col-auto px-0">:</div>
                            <div class="col d-flex align-items-center">
                                <span class="me-2 text-nowrap">Menikah ke</span>
                                <input type="text" name="suami_menikah_ke" value="<?= $k['suami_menikah_ke'] ?? '' ?>" class="form-control text-center" style="width:60px;">
                            </div>
                        </div>

                        <!-- c. Pendidikan klien -->
                        <!-- <div class="row mb-2">
                            <label class="col-4 pt-1">c. Pendidikan klien</label>
                            <div class="col-auto px-0 pt-1">:</div>
                            <div class="col">
                                <div class="d-flex flex-wrap gap-2">

                                    <?php
                                    $edu = ["Tidak Sekolah", "SD", "SMP", "SLTA", "PT"];
                                    foreach ($edu as $e) :
                                    ?>
                                        <label class="form-check form-check-inline mb-0">
                                            <input type="radio" class="form-check-input" name="edu_klien" value="<?= $e ?>" <?= ($k['edu_klien'] ?? '') == $e ? 'checked' : '' ?>>
                                            <?= ($e == "Tidak Sekolah" ? "tdk sekolah" : $e) ?>
                                        </label>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div> -->








                        <div class="row mb-2">
                            <label class="col-4 pt-1">c. Pendidikan klien</label>
                            <div class="col-auto px-0 pt-1">:</div>

                            <div class="col">
                                <div class="d-flex flex-wrap gap-2">

                                    <?php
                                    // nilai terpilih:
                                    $selectedEdu = $k['edu_klien'] ?? ($pasien->pendidikan_id ?? '');

                                    foreach ($pendidikan as $p) :
                                    ?>
                                        <label class="form-check form-check-inline mb-0">
                                            <input type="radio" class="form-check-input" name="edu_klien" value="<?= $p['global_id'] ?>" <?= ($selectedEdu == $p['global_id']) ? 'checked' : '' ?>>
                                            <?= $p['keterangan'] ?>
                                        </label>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>










                    </div>

                    <!-- =========================== -->
                    <!-- KOLOM KANAN -->
                    <!-- =========================== -->
                    <div class="col-md-6">

                        <!-- d. Pendidikan suami -->
                        <!-- <div class="row mb-2">
                            <label class="col-4 pt-1">d. Pendidikan suami</label>
                            <div class="col-auto px-0 pt-1">:</div>
                            <div class="col">
                                <div class="d-flex flex-wrap gap-2">

                                    <?php
                                    foreach ($edu as $e) :
                                    ?>
                                        <label class="form-check form-check-inline mb-0">
                                            <input type="radio" class="form-check-input" name="edu_suami" value="<?= $e ?>" <?= ($k['edu_suami'] ?? '') == $e ? 'checked' : '' ?>>
                                            <?= ($e == "Tidak Sekolah" ? "tdk sekolah" : $e) ?>
                                        </label>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div> -->






                        <div class="row mb-2">
                            <label class="col-4 pt-1">d. Pendidikan suami</label>
                            <div class="col-auto px-0 pt-1">:</div>

                            <div class="col">
                                <div class="d-flex flex-wrap gap-2">

                                    <?php
                                    // Ambil data terpilih dari asesmen, fallback jika ada data pasien
                                    $selectedEduSuami = $k['edu_suami'] ?? ($pasien->pendidikan_suami_id ?? '');

                                    foreach ($pendidikan as $p) :
                                        $id = 'edu_suami_' . $p['global_id']; // ID unik agar klik label = pilih radio
                                    ?>

                                        <label class="form-check form-check-inline mb-0" for="<?= $id ?>">
                                            <input id="<?= $id ?>" type="radio" class="form-check-input" name="edu_suami" value="<?= $p['global_id'] ?>" <?= ($selectedEduSuami == $p['global_id']) ? 'checked' : '' ?>>
                                            <?= $p['keterangan'] ?>
                                        </label>

                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
















                        <!-- e. Pekerjaan Anda -->
                        <div class="row align-items-center mb-2">
                            <label class="col-4">e. Pekerjaan Anda</label>
                            <div class="col-auto px-0">:</div>
                            <div class="col">
                                <input type="text" name="pekerjaan_klien" value="<?= $k['pekerjaan_klien'] ?? '' ?>" class="form-control">
                            </div>
                        </div>

                        <!-- f. Pekerjaan Suami -->
                        <div class="row align-items-center mb-2">
                            <label class="col-4">f. Pekerjaan Suami</label>
                            <div class="col-auto px-0">:</div>
                            <div class="col">
                                <input type="text" name="pekerjaan_suami" value="<?= $k['pekerjaan_suami'] ?? '' ?>" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="my-2">







                <div class="section-title mt-3">III. RIWAYAT KESEHATAN REPRODUKSI & PERILAKU</div>

                <div class="row mb-2">

                    <!-- KIRI -->
                    <div class="col-md-6 border-end pe-md-4">

                        <!-- a. Usia pertama haid -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-4 fw-bold">a. Usia pertama haid</label>
                            <div class="col-4 d-flex align-items-center">
                                <input type="number" name="fl_usia_haid" value="<?= $k['fl_usia_haid'] ?? '' ?>" class="form-control w-num me-1">
                                <span>th</span>
                            </div>
                        </div>

                        <!-- b. Usia pertama kawin -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-4 fw-bold">b. Usia pertama kawin</label>
                            <div class="col-4 d-flex align-items-center">
                                <input type="number" name="fl_usia_kawin" value="<?= $k['fl_usia_kawin'] ?? '' ?>" class="form-control w-num me-1">
                                <span>th</span>
                            </div>
                        </div>

                        <!-- c. Usia pertama hamil -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-4 fw-bold">c. Usia pertama hamil</label>
                            <div class="col-4 d-flex align-items-center">
                                <input type="number" name="fl_usia_hamil" value="<?= $k['fl_usia_hamil'] ?? '' ?>" class="form-control w-num me-1">
                                <span>th</span>
                            </div>
                        </div>

                    </div>


                    <!-- KANAN -->
                    <div class="col-md-6 ps-md-4">

                        <!-- HPHT -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 fw-bold">HPHT</label>

                            <div class="col-sm-9">
                                <div class="d-flex align-items-center flex-wrap gap-2">

                                    <!-- HPHT -->
                                    <input type="date" name="fl_hpht" value="<?= $k['fl_hpht'] ?? '' ?>" class="form-control" style="max-width:160px;">

                                    <!-- Menopause -->
                                    <span class="ms-2">Menopause:</span>
                                    <input type="number" name="fl_usia_menopause" value="<?= $k['fl_usia_menopause'] ?? '' ?>" class="form-control w-num" style="max-width:70px;">
                                    <span>th</span>
                                </div>
                            </div>
                        </div>

                        <!-- Siklus Haid -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 fw-bold">Siklus Haid</label>
                            <div class="col-sm-8 d-flex flex-wrap gap-3">
                                <label class="form-check d-flex align-items-center mb-0">
                                    <input type="radio" name="fl_siklus_haid" value="Teratur" class="form-check-input me-1" <?= ($k['fl_siklus_haid'] ?? '') == 'Teratur' ? 'checked' : '' ?>>
                                    Teratur
                                </label>

                                <label class="form-check d-flex align-items-center mb-0">
                                    <input type="radio" name="fl_siklus_haid" value="Tidak Teratur" class="form-check-input me-1" <?= ($k['fl_siklus_haid'] ?? '') == 'Tidak Teratur' ? 'checked' : '' ?>>
                                    Tdk Teratur
                                </label>
                            </div>
                        </div>

                        <!-- Jumlah melahirkan -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 fw-bold">Jumlah Melahirkan</label>

                            <div class="col-sm-9">
                                <div class="d-flex flex-wrap align-items-center gap-2">

                                    <input type="number" name="fl_jml_lahir" value="<?= $k['fl_jml_lahir'] ?? '' ?>" class="form-control w-num" style="max-width:80px;">
                                    <span>kali</span>

                                    <label class="form-check d-flex align-items-center mb-0 ms-2">
                                        <input type="checkbox" name="fl_cek_gugur" class="form-check-input me-1" <?= ($k['fl_cek_gugur'] ?? '') ? 'checked' : '' ?>>
                                        Keguguran
                                    </label>

                                    <div id="fl_box_gugur" class="<?= ($k['fl_cek_gugur'] ?? '') ? 'd-flex' : 'd-none' ?> align-items-center gap-2 ms-1">
                                        <span>:</span>
                                        <input type="number" name="fl_jml_gugur" value="<?= $k['fl_jml_gugur'] ?? '' ?>" class="form-control w-num" style="max-width:80px;">
                                        <span>kali</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <hr>

                <!-- e. KB Riwayat -->
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">e. KB Riwayat</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_kb_status" value="Tidak Pernah" class="form-check-input me-1" <?= ($k['fl_kb_status'] ?? '') == 'Tidak Pernah' ? 'checked' : '' ?>>
                            Tidak Pernah
                        </label>

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_kb_status" value="Pernah" class="form-check-input me-1" <?= ($k['fl_kb_status'] ?? '') == 'Pernah' ? 'checked' : '' ?>>
                            Pernah
                        </label>

                        <?php
                        $kbChecked = isset($k['fl_kb_riwayat'])
                            ? explode(',', $k['fl_kb_riwayat'])
                            : [];
                        ?>

                        <div id="fl_box_kb_riwayat" class="<?= ($k['fl_kb_status'] ?? '') == 'Pernah' ? 'd-flex' : 'd-none' ?> flex-wrap gap-3 ms-3 ps-3 border-start">

                            <?php
                            $kbList = ["Pil", "Suntik", "IUD", "Steril", "Kondom", "Implant", "Lainnya"];
                            foreach ($kbList as $kb) :
                            ?>
                                <label class="form-check mb-0">
                                    <input type="checkbox" name="fl_kb_riwayat[]" value="<?= $kb ?>" class="form-check-input me-1" <?= in_array($kb, $kbChecked) ? 'checked' : '' ?>>
                                    <?= $kb ?>
                                </label>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <hr>
                <!-- f. KB Saat Ini -->
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">f. KB Saat Ini</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <?php
                        $kbNowChecked = isset($k['fl_kb_now'])
                            ? explode(',', $k['fl_kb_now'])
                            : [];
                        ?>

                        <div id="fl_box_kb_now" class="d-flex flex-wrap gap-3 ms-0">

                            <?php
                            $kbListNow = ["Pil", "Suntik", "IUD", "Steril", "Kondom", "Implant", "Lainnya", "Tidak Pakai"];
                            foreach ($kbListNow as $kbNow) :
                            ?>
                                <label class="form-check mb-0">
                                    <input type="checkbox" name="fl_kb_now[]" value="<?= $kbNow ?>" class="form-check-input me-1" <?= in_array($kbNow, $kbNowChecked) ? 'checked' : '' ?>>
                                    <?= $kbNow ?>
                                </label>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
                <hr>

                <!-- f. PAP -->
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">g. Pap Smear</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_pap_status" value="Tidak Pernah" class="form-check-input me-1" <?= ($k['fl_pap_status'] ?? '') == 'Tidak Pernah' ? 'checked' : '' ?>>
                            Tdk Pernah
                        </label>

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_pap_status" value="Pernah" class="form-check-input me-1" <?= ($k['fl_pap_status'] ?? '') == 'Pernah' ? 'checked' : '' ?>>
                            Pernah
                        </label>

                        <div id="fl_box_pap_thn" class="<?= ($k['fl_pap_status'] ?? '') == 'Pernah' ? 'd-flex' : 'd-none' ?> align-items-center gap-2">
                            <span>Thn:</span>
                            <input type="text" name="fl_pap_thn" value="<?= $k['fl_pap_thn'] ?? '' ?>" class="form-control w-year" placeholder="YYYY">
                        </div>
                    </div>
                </div>

                <!-- IVA -->
                <div class="row mb-3 align-items-center">
                    <label class="col-3 fw-bold">h. IVA</label>
                    <div class="col-9 d-flex flex-wrap gap-3">

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_iva_status" value="Tidak Pernah" class="form-check-input me-1" <?= ($k['fl_iva_status'] ?? '') == 'Tidak Pernah' ? 'checked' : '' ?>>
                            Tdk Pernah
                        </label>

                        <label class="form-check d-flex align-items-center">
                            <input type="radio" name="fl_iva_status" value="Pernah" class="form-check-input me-1" <?= ($k['fl_iva_status'] ?? '') == 'Pernah' ? 'checked' : '' ?>>
                            Pernah
                        </label>

                        <div id="fl_box_iva_thn" class="<?= ($k['fl_iva_status'] ?? '') == 'Pernah' ? 'd-flex' : 'd-none' ?> align-items-center gap-2">
                            <span>Thn:</span>
                            <input type="text" name="fl_iva_thn" value="<?= $k['fl_iva_thn'] ?? '' ?>" class="form-control w-year" placeholder="YYYY">
                        </div>
                    </div>
                </div>

                <hr>

                <!-- MEROKOK -->
                <div class="row">

                    <!-- Pasien -->
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center">
                            <label class="fw-bold me-3" style="width:220px;">i. Merokok</label>

                            <?php
                            $rokok = ["Tidak", "Ya", "Pernah"];
                            foreach ($rokok as $r) :
                            ?>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_rokok_pasien" value="<?= $r ?>" class="form-check-input me-1" <?= ($k['fl_rokok_pasien'] ?? '') == $r ? 'checked' : '' ?> data-target="#fl_box_rokok_pasien">
                                    <?= $r ?>
                                </label>
                            <?php endforeach; ?>

                            <div id="fl_box_rokok_pasien" class="<?= ($k['fl_rokok_pasien'] ?? '') == 'Ya' ? 'd-flex' : 'd-none' ?> ms-3 d-flex align-items-center">
                                <input type="number" name="fl_rokok_pasien_jml" value="<?= $k['fl_rokok_pasien_jml'] ?? '' ?>" class="form-control w-num me-2" style="max-width:80px;">
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
                                    <input type="radio" name="fl_rokok_suami" value="<?= $r ?>" class="form-check-input me-1" <?= ($k['fl_rokok_suami'] ?? '') == $r ? 'checked' : '' ?> data-target="#fl_box_rokok_suami">
                                    <?= $r ?>
                                </label>
                            <?php endforeach; ?>

                            <div id="fl_box_rokok_suami" class="<?= ($k['fl_rokok_suami'] ?? '') == 'Ya' ? 'd-flex' : 'd-none' ?> ms-3 d-flex align-items-center">
                                <input type="number" name="fl_rokok_suami_jml" value="<?= $k['fl_rokok_suami_jml'] ?? '' ?>" class="form-control w-num me-2" style="max-width:80px;">
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
                                    <input type="radio" name="fl_rokok_rumah" value="<?= $r ?>" class="form-check-input me-1" <?= ($k['fl_rokok_rumah'] ?? '') == $r ? 'checked' : '' ?> data-target="#fl_box_rokok_rumah">
                                    <?= $r ?>
                                </label>
                            <?php endforeach; ?>

                            <div id="fl_box_rokok_rumah" class="<?= ($k['fl_rokok_rumah'] ?? '') == 'Ya' ? 'd-flex' : 'd-none' ?> ms-3 d-flex align-items-center">
                                <input type="number" name="fl_rokok_rumah_jml" value="<?= $k['fl_rokok_rumah_jml'] ?? '' ?>" class="form-control w-num me-2" style="max-width:80px;">
                                <span class="mt-1">btg/hari</span>
                            </div>
                        </div>
                    </div>

                </div>













































                <div class="section-title mt-3">IV. RIWAYAT KANKER DALAM KELUARGA</div>

                <?php
                // ---- PREPARE DATA ----
                $kelKanker = $k['fl_kel_kanker'] ?? '';
                $kelSiapa  = !empty($k['fl_kel_siapa']) ? explode(',', $k['fl_kel_siapa']) : [];
                $kelJenis  = $k['fl_kel_jenis'] ?? '';

                $klienKanker = $k['fl_klien_kanker'] ?? '';
                $klienJenis  = $k['fl_klien_jenis'] ?? '';
                ?>

                <!-- a. Keluarga terkena kanker -->
                <div class="row align-items-center mb-2">
                    <div class="col-md-4 col-12">
                        <span class="fw-bold">a. Ada keluarga yang terkena :</span>
                    </div>

                    <div class="col-md-8 col-12">
                        <div class="d-flex align-items-center">
                            <?php
                            $opsiKel = ["Ada", "Tidak Ada", "Tidak Tahu"];
                            foreach ($opsiKel as $opt) :
                            ?>
                                <div class="form-check form-check-inline mb-0 me-3">
                                    <input type="radio" name="fl_kel_kanker" value="<?= $opt ?>" class="form-check-input fl-toggle-kanker" <?= ($kelKanker == $opt ? 'checked' : '') ?>>
                                    <label><?= $opt ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- BOX DETAIL KELUARGA -->
                <div id="fl_box_kel_detail" class="<?= ($kelKanker == 'Ada' ? 'd-block' : 'd-none') ?> bg-light p-2 border rounded mb-2 ms-md-4">

                    <div class="row align-items-start mb-2">
                        <div class="col-md-3 col-12 text-md-end">
                            <span class="me-2 fw-bold">- Jika ada, siapa :</span>
                        </div>

                        <div class="col-md-9 col-12">
                            <div class="d-flex flex-wrap gap-2">

                                <?php
                                $siapaList = ["Ayah", "Ibu", "Bibi", "Paman", "Kakek", "Nenek", "Kakak", "Adik"];
                                foreach ($siapaList as $s) :
                                ?>
                                    <div class="form-check form-check-inline m-0">
                                        <input type="checkbox" name="fl_kel_siapa[]" value="<?= $s ?>" class="form-check-input" <?= in_array($s, $kelSiapa) ? 'checked' : '' ?>>
                                        <?= $s ?>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-3 col-12 text-md-end">
                            <span class="me-2 fw-bold">- Kanker apa :</span>
                        </div>

                        <div class="col-md-9 col-12">
                            <input type="text" name="fl_kel_jenis" value="<?= $kelJenis ?>" class="form-control w-100">
                        </div>
                    </div>

                </div>

                <!-- b. Klien terkena kanker -->
                <div class="row align-items-center mb-2">
                    <div class="col-md-4 col-12">
                        <span class="fw-bold">b. Klien yang terkena :</span>
                    </div>

                    <div class="col-md-8 col-12">
                        <div class="d-flex align-items-center flex-wrap">

                            <?php
                            $opsiKlien = ["Ada", "Tidak Ada"];
                            foreach ($opsiKlien as $opt) :
                            ?>
                                <div class="form-check form-check-inline mb-0 me-3">
                                    <input type="radio" name="fl_klien_kanker" value="<?= $opt ?>" class="form-check-input fl-toggle-klien" <?= ($klienKanker == $opt ? 'checked' : '') ?>>
                                    <label><?= $opt ?></label>
                                </div>
                            <?php endforeach; ?>

                            <div id="fl_box_klien_jenis" class="ms-2 border-start ps-2 <?= ($klienKanker == 'Ada' ? 'd-flex' : 'd-none') ?> align-items-center flex-fill">

                                <span class="me-2 text-nowrap">Jika terkena, kanker apa :</span>

                                <input type="text" name="fl_klien_jenis" value="<?= $klienJenis ?>" class="form-control w-100">
                            </div>

                        </div>
                    </div>
                </div>








                <div class="section-title mt-3">V. KELUHAN</div>

                <?php
                $kelCair = $k['fl_kel_cairan'] ?? '';
                $lamaCair = $k['fl_lama_cairan'] ?? '';

                $kelNyeri = $k['fl_kel_nyeri'] ?? '';
                $lamaNyeri = $k['fl_lama_nyeri'] ?? '';

                $kelSeng = $k['fl_kel_senggama'] ?? '';
                $lamaSeng = $k['fl_lama_senggama'] ?? '';

                $kelNon = $k['fl_kel_nonhaid'] ?? '';
                $lamaNon = $k['fl_lama_nonhaid'] ?? '';

                $kelLain = $k['fl_kel_lain'] ?? '';
                $kelLainNama = $k['fl_kel_lain_nama'] ?? '';
                $lamaLain = $k['fl_lama_lain'] ?? '';
                ?>

                <div class="row">

                    <!-- a. Cairan -->
                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">a. Keluhan banyak cairan dari kemaluan/keputihan</div>

                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>

                                <?php foreach (['Ada', 'Tidak'] as $opt) : ?>
                                    <label class="form-check form-check-inline mb-0 me-3">
                                        <input type="radio" name="fl_kel_cairan" value="<?= $opt ?>" class="form-check-input fl-keluhan-group" data-target="#fl_box_cairan_lama" <?= ($kelCair == $opt ? 'checked' : '') ?>>
                                        <?= $opt ?>
                                    </label>
                                <?php endforeach; ?>

                                <div id="fl_box_cairan_lama" class="ms-2 align-items-center <?= ($kelCair == 'Ada' ? 'd-flex' : 'd-none') ?>">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_cairan" value="<?= $lamaCair ?>" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- b. Nyeri -->
                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">b. Sakit nyeri di perut bagian bawah/panggul</div>

                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>

                                <?php foreach (['Ada', 'Tidak'] as $opt) : ?>
                                    <label class="form-check form-check-inline mb-0 me-3">
                                        <input type="radio" name="fl_kel_nyeri" value="<?= $opt ?>" class="form-check-input fl-keluhan-group" data-target="#fl_box_nyeri_lama" <?= ($kelNyeri == $opt ? 'checked' : '') ?>>
                                        <?= $opt ?>
                                    </label>
                                <?php endforeach; ?>

                                <div id="fl_box_nyeri_lama" class="ms-2 align-items-center <?= ($kelNyeri == 'Ada' ? 'd-flex' : 'd-none') ?>">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_nyeri" value="<?= $lamaNyeri ?>" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- c. Pendarahan setelah senggama -->
                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">c. Pendarahan bila/setelah senggama</div>

                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>

                                <?php foreach (['Ada', 'Tidak'] as $opt) : ?>
                                    <label class="form-check form-check-inline mb-0 me-3">
                                        <input type="radio" name="fl_kel_senggama" value="<?= $opt ?>" class="form-check-input fl-keluhan-group" data-target="#fl_box_senggama_lama" <?= ($kelSeng == $opt ? 'checked' : '') ?>>
                                        <?= $opt ?>
                                    </label>
                                <?php endforeach; ?>

                                <div id="fl_box_senggama_lama" class="ms-2 align-items-center <?= ($kelSeng == 'Ada' ? 'd-flex' : 'd-none') ?>">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_senggama" value="<?= $lamaSeng ?>" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- d. Non-haid -->
                    <div class="col-12 mb-1">
                        <div class="row align-items-center">
                            <div class="col-md-5">d. Pendarahan di luar haid</div>

                            <div class="col-md-7 d-flex align-items-center">
                                <span class="me-2">:</span>

                                <?php foreach (['Ada', 'Tidak'] as $opt) : ?>
                                    <label class="form-check form-check-inline mb-0 me-3">
                                        <input type="radio" name="fl_kel_nonhaid" value="<?= $opt ?>" class="form-check-input fl-keluhan-group" data-target="#fl_box_nonhaid_lama" <?= ($kelNon == $opt ? 'checked' : '') ?>>
                                        <?= $opt ?>
                                    </label>
                                <?php endforeach; ?>

                                <div id="fl_box_nonhaid_lama" class="ms-2 align-items-center <?= ($kelNon == 'Ada' ? 'd-flex' : 'd-none') ?>">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_nonhaid" value="<?= $lamaNon ?>" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- e. Lain-lain -->
                    <div class="col-12 mb-1">
                        <div class="row align-items-center">

                            <div class="col-md-5 d-flex align-items-center">
                                <span class="me-2 text-nowrap">e. Lain-lain, sebutkan</span>
                                <input type="text" name="fl_kel_lain_nama" value="<?= $kelLainNama ?>" class="form-control w-100">
                            </div>

                            <div class="col-md-7 d-flex align-items-center mt-1 mt-md-0">
                                <span class="me-2">:</span>

                                <?php foreach (['Ada', 'Tidak'] as $opt) : ?>
                                    <label class="form-check form-check-inline mb-0 me-3">
                                        <input type="radio" name="fl_kel_lain" value="<?= $opt ?>" class="form-check-input fl-keluhan-group" data-target="#fl_box_lain_lama" <?= ($kelLain == $opt ? 'checked' : '') ?>>
                                        <?= $opt ?>
                                    </label>
                                <?php endforeach; ?>

                                <div id="fl_box_lain_lama" class="ms-2 align-items-center <?= ($kelLain == 'Ada' ? 'd-flex' : 'd-none') ?>">
                                    <span class="me-1 text-nowrap">Sudah berapa lama</span>
                                    <input type="text" name="fl_lama_lain" value="<?= $lamaLain ?>" class="form-control w-num text-center"> bulan
                                </div>
                            </div>
                        </div>
                    </div>

                </div>





                <?php
                function isChecked($v, $opt)
                {
                    return $v == $opt ? 'checked' : '';
                }
                function isShow($v)
                {
                    return ($v == "Ya" || $v == "Ada") ? 'd-flex' : 'd-none';
                }

                // SAFETY: konversi jadi array pasti
                function toArray($v)
                {
                    if (!$v) return [];
                    return is_array($v) ? $v : explode(',', $v);
                }

                // Gunakan toArray()
                $hasilIvaArr      = toArray($k['fl_hasil_iva'] ?? []);
                $ivaBerkalaArr    = toArray($k['fl_iva_berkala'] ?? []);
                $tindakRadangArr  = toArray($k['fl_tindak_radang'] ?? []);
                $tindakIvaArr     = toArray($k['fl_tindak_iva'] ?? []);
                $tindakImsArr     = toArray($k['fl_tindak_ims'] ?? []);
                ?>

                <div class="section-title mt-3">
                    VI. PEMERIKSAAN GINEKOLOGI dan IVA
                    <small class="fw-normal d-block d-md-inline ms-md-2 text-muted" style="font-size: .85em;">
                        - Gambarkan / Foto SSK dan kelainan (Polip Serviks, Kondiloma, Ovula Nabothi, dll)
                    </small>
                </div>

                <div class="row">

                    <!-- ===================================================== -->
                    <!-- KIRI -->
                    <!-- ===================================================== -->
                    <div class="col-md-7 border-end">

                        <!-- Kelainan Vulva -->
                        <div class="row align-items-center mb-1">
                            <div class="col-4">Kelainan Vulva :</div>
                            <div class="col-8">
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vulva" value="Tidak" class="form-check-input" <?= isChecked($k['fl_kel_vulva'] ?? '', 'Tidak') ?>> Tidak
                                </label>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vulva" value="Ya" class="form-check-input" <?= isChecked($k['fl_kel_vulva'] ?? '', 'Ya') ?>> Ya
                                </label>
                            </div>
                        </div>

                        <!-- Kelainan Vagina -->
                        <div class="row align-items-center mb-1">
                            <div class="col-4">Kelainan Vagina :</div>
                            <div class="col-8">
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vagina" value="Tidak" class="form-check-input" <?= isChecked($k['fl_kel_vagina'] ?? '', 'Tidak') ?>> Tidak
                                </label>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_kel_vagina" value="Ya" class="form-check-input" <?= isChecked($k['fl_kel_vagina'] ?? '', 'Ya') ?>> Ya
                                </label>
                            </div>
                        </div>

                        <!-- Curiga KLR -->
                        <div class="row align-items-center mb-1">
                            <div class="col-4">Curiga KLR :</div>
                            <div class="col-8">
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_curiga_klr" value="Tidak" class="form-check-input" <?= isChecked($k['fl_curiga_klr'] ?? '', 'Tidak') ?>> Tidak
                                </label>
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_curiga_klr" value="Ya" class="form-check-input" <?= isChecked($k['fl_curiga_klr'] ?? '', 'Ya') ?>> Ya, Rujuk RS
                                </label>
                            </div>
                        </div>

                        <!-- Pemeriksaan SSK -->
                        <div class="row align-items-center mb-1">
                            <div class="col-4">Pemeriksaan SSK :</div>
                            <div class="col-8">
                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_pem_ssk" value="Tampak" class="form-check-input" <?= isChecked($k['fl_pem_ssk'] ?? '', 'Tampak') ?>> Tampak
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_pem_ssk" value="Tidak Tampak" class="form-check-input" <?= isChecked($k['fl_pem_ssk'] ?? '', 'Tidak Tampak') ?>> Tdk Tampak
                                </label>
                            </div>
                        </div>

                        <!-- PAP -->
                        <div class="row align-items-center mb-1">
                            <div class="col-4">Pengambilan Pap :</div>
                            <div class="col-8 d-flex align-items-center flex-wrap">
                                <?php $val = $k['fl_ambil_pap'] ?? ''; ?>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_ambil_pap" value="Tidak" class="form-check-input fl-toggle-pap" <?= isChecked($val, 'Tidak') ?>> Tidak
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_ambil_pap" value="Ya" class="form-check-input fl-toggle-pap" <?= isChecked($val, 'Ya') ?>> Ya
                                </label>

                                <div id="fl_box_pap_detail" class="ms-1 <?= isShow($val) ?> align-items-center" style="font-size:.9em;">
                                    Tgl <input type="date" name="fl_tgl_pap" value="<?= $k['fl_tgl_pap'] ?? '' ?>" class="form-control mx-1" style="width:110px;">
                                    Hasil: <input type="text" name="fl_hasil_pap" value="<?= $k['fl_hasil_pap'] ?? '' ?>" class="form-control w-100px">
                                </div>
                            </div>
                        </div>

                        <!-- HPV -->
                        <div class="row align-items-center mb-1">
                            <div class="col-4">Tes DNA HPV :</div>
                            <div class="col-8 d-flex align-items-center flex-wrap">
                                <?php $v = $k['fl_tes_hpv'] ?? ''; ?>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_tes_hpv" value="Tidak" class="form-check-input fl-toggle-hpv" <?= isChecked($v, 'Tidak') ?>> Tidak
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_tes_hpv" value="Ya" class="form-check-input fl-toggle-hpv" <?= isChecked($v, 'Ya') ?>> Ya
                                </label>

                                <div id="fl_box_hpv_detail" class="ms-1 <?= isShow($v) ?> align-items-center" style="font-size:.9em;">
                                    Tgl <input type="date" name="fl_tgl_hpv" value="<?= $k['fl_tgl_hpv'] ?? '' ?>" class="form-control mx-1" style="width:110px;">
                                    Hasil: <input type="text" name="fl_hasil_hpv" value="<?= $k['fl_hasil_hpv'] ?? '' ?>" class="form-control w-100px">
                                </div>
                            </div>
                        </div>

                        <!-- FOTO DOIVA -->
                        <div class="row align-items-center mb-3">
                            <div class="col-4">Foto DoIVA :</div>
                            <div class="col-8 d-flex align-items-center flex-wrap">

                                <?php $v = $k['fl_foto_doiva'] ?? ''; ?>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_foto_doiva" value="Tidak" class="form-check-input fl-toggle-foto" <?= isChecked($v, 'Tidak') ?>> Tidak
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="radio" name="fl_foto_doiva" value="Ya" class="form-check-input fl-toggle-foto" <?= isChecked($v, 'Ya') ?>> Ya
                                </label>

                                <div id="fl_box_foto_detail" class="ms-1 <?= isShow($v) ?> align-items-center">
                                    Nama File:
                                    <input type="text" name="fl_kode_file" value="<?= $k['fl_kode_file'] ?? '' ?>" class="form-control w-100px">
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================== -->
                        <!-- HASIL IVA -->
                        <!-- ===================================================== -->
                        <div class="border rounded p-2 bg-light">

                            <strong class="d-block mb-2 text-decoration-underline">Hasil IVA :</strong>

                            <!-- ============================= -->
                            <!-- 1) IVA NEGATIF -->
                            <!-- ============================= -->
                            <?php $neg = in_array('Negatif', $hasilIvaArr); ?>
                            <div class="d-flex flex-wrap align-items-center mb-2">

                                <label class="form-check mb-0 me-2">
                                    <input type="checkbox" name="fl_hasil_iva[]" value="Negatif" class="form-check-input" id="fl_iva_negatif" <?= $neg ? 'checked' : '' ?>>
                                    IVA Negatif / Normal, anjuran periksa berkala :
                                </label>

                                <div id="fl_box_iva_berkala" class="align-items-center <?= $neg ? 'd-flex' : 'd-none' ?>">

                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <label class="form-check form-check-inline mb-0 ms-2">
                                            <input type="checkbox" class="form-check-input" name="fl_iva_berkala[]" value="<?= $i ?>" <?= in_array($i, $ivaBerkalaArr) ? 'checked' : '' ?>>

                                            <?= $i ?>
                                        </label>
                                    <?php endfor; ?>

                                    <span class="ms-1">th</span>
                                </div>
                            </div>

                            <!-- ============================= -->
                            <!-- 2) RADANG / SERVISITIS -->
                            <!-- ============================= -->
                            <?php $rad = in_array('Radang', $hasilIvaArr); ?>
                            <div class="d-flex flex-wrap align-items-center mb-2">

                                <label class="form-check me-2 mb-0">
                                    <input type="checkbox" name="fl_hasil_iva[]" value="Radang" class="form-check-input fl-toggle-radang" id="fl_iva_radang" <?= $rad ? 'checked' : '' ?>>
                                    Radang / Servisitis :
                                </label>

                                <div id="fl_box_radang" class="flex-wrap align-items-center <?= $rad ? 'd-flex' : 'd-none' ?>">

                                    <label class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_radang_grade" value="Ringan" class="form-check-input" <?= isChecked($k['fl_radang_grade'] ?? '', 'Ringan') ?>>
                                        Ringan
                                    </label>

                                    <label class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_radang_grade" value="Sedang" class="form-check-input" <?= isChecked($k['fl_radang_grade'] ?? '', 'Sedang') ?>>
                                        Sedang
                                    </label>

                                    <label class="form-check form-check-inline mb-0">
                                        <input type="radio" name="fl_radang_grade" value="Berat" class="form-check-input" <?= isChecked($k['fl_radang_grade'] ?? '', 'Berat') ?>>
                                        Berat
                                    </label>
                                </div>
                            </div>

                            <!-- Tindak Radang -->
                            <div id="fl_box_radang_tindak" class="<?= $rad ? 'd-flex' : 'd-none' ?> ps-4 mb-2 small flex-wrap">

                                <label class="form-check form-check-inline mb-1">
                                    <input type="checkbox" name="fl_tindak_radang[]" value="Rujuk" class="form-check-input" <?= in_array('Rujuk', $tindakRadangArr) ? 'checked' : '' ?>>
                                    Rujuk
                                </label>

                                <label class="form-check form-check-inline mb-1 ms-3">
                                    <input type="checkbox" name="fl_tindak_radang[]" value="Diobati" class="form-check-input" <?= in_array('Diobati', $tindakRadangArr) ? 'checked' : '' ?>>
                                    Diobati
                                </label>
                            </div>

                            <!-- ============================= -->
                            <!-- 3) IVA POSITIF -->
                            <!-- ============================= -->
                            <?php $pos = in_array('Positif', $hasilIvaArr); ?>
                            <div class="form-check mb-1">
                                <input type="checkbox" name="fl_hasil_iva[]" value="Positif" class="form-check-input fl-toggle-positif" id="fl_iva_positif" <?= $pos ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fl_iva_positif">IVA Positif</label>
                            </div>

                            <div id="fl_box_positif" class="ps-4 small <?= $pos ? 'd-block' : 'd-none' ?>">

                                <label class="form-check mb-1">
                                    <input type="checkbox" name="fl_tindak_iva[]" value="Krioterapi" class="form-check-input" <?= in_array('Krioterapi', $tindakIvaArr) ? 'checked' : '' ?>>
                                    Anjuran segera Krioterapi / TCA 85%
                                </label>

                                <label class="form-check">
                                    <input type="checkbox" name="fl_tindak_iva[]" value="Rujuk" class="form-check-input" <?= in_array('Rujuk', $tindakIvaArr) ? 'checked' : '' ?>>
                                    Lesi luas > 75%, Rujuk
                                </label>
                            </div>

                        </div>

                    </div>

                    <!-- ===================================================== -->
                    <!-- KANVAS SERVIKS -->
                    <!-- ===================================================== -->
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
                            *Gunakan mouse/jari untuk menggambar letak kelainan (SSK, Polip, dll)<br><br><br>

                            <textarea name="fl_temuan_lain" class="form-control" rows="2" placeholder="Tulis temuan lain di sini..."><?= isset($k['fl_temuan_lain']) ? $k['fl_temuan_lain'] : '' ?></textarea>
                        </p>

                        <input type="hidden" name="fl_gambar_serviks" id="fl_gambar_serviks" value="<?= $k['fl_gambar_serviks'] ?>">
                    </div>

                </div>

                <!-- ===================================================== -->
                <!-- IMS + BIMANUAL + PEMERIKSAAN -->
                <!-- ===================================================== -->

                <div class="row mt-3 pt-3 border-top">
                    <div class="col-12">

                        <!-- IMS -->
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2 text-nowrap">Diduga IMS :</span>
                            <input type="text" name="fl_dugaan_ims" value="<?= $k['fl_dugaan_ims'] ?? '' ?>" class="form-control flex-fill">

                            <div class="ms-3 border-start ps-3">
                                <label class="form-check form-check-inline">
                                    <input type="checkbox" name="fl_tindak_ims[]" value="Rujuk" class="form-check-input" <?= in_array('Rujuk', $tindakImsArr) ? 'checked' : '' ?>> Rujuk
                                </label>

                                <label class="form-check form-check-inline">
                                    <input type="checkbox" name="fl_tindak_ims[]" value="Diobati" class="form-check-input" <?= in_array('Diobati', $tindakImsArr) ? 'checked' : '' ?>> Diobati
                                </label>
                            </div>
                        </div>

                        <!-- BIMANUAL -->
                        <div class="mb-2">
                            <span>Pemeriksaan Bimanual :</span>
                            <input type="text" name="fl_pem_bimanual" value="<?= $k['fl_pem_bimanual'] ?? '' ?>" class="form-control w-100">
                        </div>

                        <!-- Pemeriksaan Tanggal & Lokasi -->
                        <div class="row mt-3">
                            <div class="col-md-4 mb-2">
                                Diperiksa Tgl.
                                <input type="date" name="fl_tgl_periksa" value="<?= $k['fl_tgl_periksa'] ?? date('Y-m-d') ?>" class="form-line">
                            </div>

                            <div class="col-md-4 mb-2">
                                Lokasi:
                                <input type="text" name="fl_lokasi_periksa" value="<?= $k['fl_lokasi_periksa'] ?? '' ?>" class="form-control w-75">
                            </div>

                            <div class="col-md-4 mb-2">
                                Oleh dr/Bd:
                                <input type="text" name="fl_pemeriksa_nama" value="<?= $k['fl_pemeriksa_nama'] ?? '' ?>" class="form-control w-50">
                            </div>
                        </div>
                    </div>
                </div>



                <?php
                // siapkan array hasil (jika edit)
                $tlProsedurArr  = explode(",", $k['fl_tl_prosedur'] ?? "");
                $probeArr       = explode(",", $k['fl_probe_tip'] ?? "");
                $antiArr        = explode(",", $k['fl_antibiotik'] ?? "");
                $tlKeadaanArr   = explode(",", $k['fl_tl_keadaan'] ?? "");
                $tlGejalaArr    = explode(",", $k['fl_tl_gejala'] ?? "");
                ?>



                <div class="section-title">VII. TINDAK LANJUT</div>

                <div class="row">
                    <div class="col-md-12">

                        <!-- TINDAKAN PROSEDUR -->
                        <div class="d-flex flex-wrap align-items-center mb-2 ps-3">
                            <span class="me-3 fw-bold">Tindakan Prosedur:</span>

                            <label class="form-check form-check-inline mb-0 me-3">
                                <input type="checkbox" name="fl_tl_prosedur[]" class="form-check-input" value="Krioterapi" <?= in_array("Krioterapi", $tlProsedurArr) ? "checked" : "" ?>>
                                Krioterapi
                            </label>

                            <label class="form-check form-check-inline mb-0 me-4">
                                <input type="checkbox" name="fl_tl_prosedur[]" class="form-check-input" value="TCA 85%" <?= in_array("TCA 85%", $tlProsedurArr) ? "checked" : "" ?>>
                                TCA 85%
                            </label>

                            <span class="d-inline-flex align-items-center ms-2 me-2">
                                Tgl:
                                <input type="date" name="tgl_krio" class="form-control w-input-md ms-1" value="<?= $k['tgl_krio'] ?? '' ?>">
                            </span>

                            <span class="d-inline-flex align-items-center me-2">
                                di:
                                <input type="text" name="loc_krio" class="form-control w-input-md ms-1" value="<?= $k['loc_krio'] ?? '' ?>">
                            </span>

                            <span class="d-inline-flex align-items-center me-2">
                                oleh dr/Bd:
                                <input type="text" name="dr_krio" class="form-control w-input-md ms-1" value="<?= $k['dr_krio'] ?? '' ?>">
                                <span class="ms-2">Tanda tangan: ............</span>
                            </span>
                        </div>

                        <!-- PROBE TIP -->
                        <div class="mb-2 ps-3">
                            <span class="fw-bold me-3 d-inline-block">Menggunakan Probe-KrioTip :</span>

                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_probe_tip[]" class="form-check-input" value="Ecto cervix sedang" <?= in_array("Ecto cervix sedang", $probeArr) ? "checked" : "" ?>>
                                    Ecto cervix sedang
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_probe_tip[]" class="form-check-input" value="Ecto-Endo cervix sedang" <?= in_array("Ecto-Endo cervix sedang", $probeArr) ? "checked" : "" ?>>
                                    Ecto-Endo cervix sedang
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_probe_tip[]" class="form-check-input" value="Ecto-Endo cervix besar" <?= in_array("Ecto-Endo cervix besar", $probeArr) ? "checked" : "" ?>>
                                    Ecto-Endo cervix besar
                                </label>
                            </div>
                        </div>

                        <!-- ANTIBIOTIK -->
                        <div class="mb-2 ps-3">
                            <span class="fw-bold me-3 d-block d-md-inline">
                                Pemberian antibiotik profilaksis 7 hari :
                            </span>

                            <div class="d-flex flex-wrap gap-3 mt-1">

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_antibiotik[]" class="form-check-input" value="Doksisiklin" <?= in_array("Doksisiklin", $antiArr) ? "checked" : "" ?>>
                                    Doksisiklin 2 dd 100 mg
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_antibiotik[]" class="form-check-input" value="Klindamisin" <?= in_array("Klindamisin", $antiArr) ? "checked" : "" ?>>
                                    Klindamisin 2 dd 300 mg
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    Lainnya:
                                    <input type="text" name="obat_lain" class="form-control ms-1 w-input-md" value="<?= $k['obat_lain'] ?? '' ?>">
                                </label>
                            </div>
                        </div>

                        <!-- KEADAAN SETELAH PROSEDUR -->
                        <div class="mb-2 ps-3">
                            <span class="fw-bold d-block">Menjelaskan kemungkinan keadaan setelah krioterapi/TCA:</span>

                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_keadaan[]" class="form-check-input" value="Tidak senggama 1 bulan" <?= in_array("Tidak senggama 1 bulan", $tlKeadaanArr) ? "checked" : "" ?>>
                                    Tidak senggama 1 bulan
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_keadaan[]" class="form-check-input" value="Tidak senggama 2 minggu" <?= in_array("Tidak senggama 2 minggu", $tlKeadaanArr) ? "checked" : "" ?>>
                                    Tidak senggama 2 minggu
                                </label>
                            </div>
                        </div>

                        <!-- GEJALA -->
                        <div class="mb-2 ps-3">
                            <div class="d-flex flex-wrap align-items-center gap-3">

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_gejala[]" class="form-check-input" value="Keputihan cair" <?= in_array("Keputihan cair", $tlGejalaArr) ? "checked" : "" ?>>
                                    Keputihan cair 1–2 minggu
                                </label>

                                <label class="form-check form-check-inline mb-0">
                                    <input type="checkbox" name="fl_tl_gejala[]" class="form-check-input" value="Demam" <?= in_array("Demam", $tlGejalaArr) ? "checked" : "" ?>>
                                    Adanya demam
                                </label>

                                <span class="ms-3 me-2 fw-bold">Dipesan kontrol Tgl:</span>
                                <input type="date" name="tgl_kontrol" class="form-control w-input-md" value="<?= $k['tgl_kontrol'] ?? '' ?>">
                            </div>
                        </div>

                    </div>
                </div>



























                <div class="mt-3 p-2 bg-light border text-center fst-italic">
                    <strong>Petunjuk Pengisian :</strong> Tulislah isian pada titik-titik dan berilah tanda centang (v) yang jelas di dalam kotak pilihan pengisian
                </div>

                <!-- <div class="d-flex justify-content-end gap-2 mt-4 no-print">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-secondary">Kembali</a>
             
                    <button type="button" id="btnUpdateAssessLab" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Update Assessment
                    </button>
                </div> -->

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



<script>
    document.addEventListener("DOMContentLoaded", function() {

        // disable semua input
        document.querySelectorAll("input, select, textarea").forEach(el => {

            if (el.type === "radio" || el.type === "checkbox") {
                el.disabled = true;
            } else {
                el.readOnly = true;
            }

        });

    });
</script>