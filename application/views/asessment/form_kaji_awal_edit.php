<?php
// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Edit Pengkajian Awal Pasien Rawat Jalan (Umum)';
// return var_dump($poli_id);
// die;
?>

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
    <form id="formKajiAwalFullEdit" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/updatePengkajianAwal'); ?>">
        <!-- <form id="formPengkajianEdit" class="needs-validation" method="post" action="<?= base_url('AsessmentController/updatePengkajianAwal'); ?>"> -->
        <input type="hidden" name="episode_id" value="<?= $episode_id ?>">
        <input type="hidden" name="pasien_id" value="<?= $pasien->pasien_id ?>">
        <input type="hidden" name="form_id" value="<?= $data_kaji['form_id'] ?>">


        <?php if (isset($poli_id) && $poli_id == 'POLI0000000001') : ?>
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-danger btn-sm shadow-sm" id="btnPaliatif" data-episode="<?= $episode_id ?>" data-pasien="<?= $pasien->pasien_id ?>">

                    <i class="fa fa-heartbeat me-1"></i>
                    Asesment Paliatif
                </button>
            </div>
        <?php endif; ?>
        <div class="card shadow-sm">
            <div class="card-body p-4">


                <!-- HEADER LOGO + JUDUL -->
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-7 d-flex align-items-center">
                        <img src="<?= base_url('assets/img/yki_logo.png'); ?>" alt="Logo" style="height:56px" class="me-3">
                        <div>
                            <h5 class="mb-0 fw-bold">Klinik Utama Graha YKI Jakarta</h5>
                            <div class="small text-muted">Yayasan Kanker Indonesia Cabang Jakarta</div>
                        </div>
                    </div>
                    <div class="col-md-5 text-end">
                        <span class="badge bg-warning fs-6">EDIT PENGKAJIAN AWAL PASIEN</span>
                    </div>
                </div>





                <!-- IDENTITAS PASIEN -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="row g-2">

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Nomor RM</label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($pasien->no_rm ?? '') ?>" readonly>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Nama</label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($pasien->nama ?? '') ?>" readonly>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Tanggal Lahir / Umur</label>
                            <?php
                            $tgl_lahir_fmt = !empty($pasien->tgl_lahir)
                                ? date('d/m/Y', strtotime($pasien->tgl_lahir))
                                : '';

                            $umur = '';
                            if (!empty($pasien->tgl_lahir)) {
                                $lahir = new DateTime($pasien->tgl_lahir);
                                $today = new DateTime();
                                $umur = $today->diff($lahir)->y . ' th';
                            }
                            ?>
                            <input type="text" class="form-control form-control-sm" value="<?= $tgl_lahir_fmt . ' / ' . $umur ?>" readonly>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Jenis Kelamin</label>
                            <div class="d-flex gap-3 pt-1">
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" <?= ($pasien->sex_id == 'L') ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label small">L</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" <?= ($pasien->sex_id == 'P') ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label small">P</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>




                <!-- PENGKAJIAN KEPERAWATAN -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">PENGKAJIAN KEPERAWATAN</div>

                    <div class="row g-2 align-items-end">

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Tiba di Poliklinik</label>
                            <input type="text" name="tiba_tanggal" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['tiba_tanggal']) ?>" readonly>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label small mb-0">Pkl</label>
                            <input type="time" name="tiba_jam" class="form-control form-control-sm" value="<?= htmlspecialchars(substr($data_kaji['tiba_jam'], 0, 5)) ?>">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Pengkajian</label>
                            <input type="text" name="kaji_tanggal" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['kaji_tanggal']) ?>" readonly>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label small mb-0">Pkl</label>
                            <input type="time" name="kaji_jam" class="form-control form-control-sm" value="<?= htmlspecialchars(substr($data_kaji['kaji_jam'], 0, 5)) ?>">
                        </div>

                        <!-- DIPEROLEH DARI -->
                        <div class="col-12 col-md-6 mt-2">
                            <label class="form-label small mb-0">Diperoleh dari</label>
                            <div class="d-flex gap-3 flex-wrap pt-1">
                                <?php
                                $dari_opt = ['Pasien', 'Keluarga', 'Lainnya'];
                                $dari_val = explode(',', $data_kaji['diperoleh_dari'] ?? '');
                                ?>
                                <?php foreach ($dari_opt as $i => $lbl) : ?>
                                    <label class="form-check m-0">
                                        <input class="form-check-input" type="checkbox" name="diperoleh_dari[]" value="<?= $lbl ?>" <?= in_array($lbl, $dari_val) ? 'checked' : '' ?>>
                                        <span class="small"><?= $lbl ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- HUBUNGAN -->
                        <div class="col-12 col-md-6 mt-2">
                            <label class="form-label small mb-0">Hubungan dengan pasien</label>
                            <div class="d-flex gap-2">
                                <select name="hubungan" id="hubungan" class="form-select form-select-sm select2">
                                    <option value="">-- Pilih Hubungan --</option>
                                    <?php foreach ($hubungan as $h) : ?>
                                        <option value="<?= $h->keterangan ?>" <?= ($data_kaji['hubungan'] == $h->keterangan ? 'selected' : '') ?>>
                                            <?= $h->keterangan ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="LAINNYA" <?= ($data_kaji['hubungan'] == 'LAINNYA' ? 'selected' : '') ?>>
                                        LAINNYA
                                    </option>
                                </select>

                                <input type="text" name="hubungan_lain" id="hubungan_lain" class="form-control form-control-sm" placeholder="Sebutkan..." style="max-width:200px; <?= ($data_kaji['hubungan'] == 'LAINNYA') ? '' : 'display:none;' ?>" value="<?= htmlspecialchars($data_kaji['hubungan_lain']) ?>">
                            </div>
                        </div>

                        <!-- CARA MASUK -->
                        <div class="col-12 col-md-6 mt-2">
                            <label class="form-label small mb-0">Cara Masuk</label>
                            <div class="d-flex gap-3 flex-wrap pt-1">
                                <?php
                                $cara_val = explode(',', $data_kaji['cara_masuk'] ?? '');
                                foreach ($cara_masuk as $i => $cm) : ?>
                                    <label class="form-check m-0">
                                        <input class="form-check-input" type="checkbox" name="cara_masuk[]" value="<?= $cm->keterangan ?>" <?= in_array($cm->keterangan, $cara_val) ? 'checked' : '' ?>>
                                        <span class="small"><?= $cm->keterangan ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div>












                <!-- RIWAYAT KESEHATAN -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">RIWAYAT KESEHATAN</div>

                    <table class="w-100 small" style="border-collapse:separate; border-spacing:0 8px;">
                        <tr>
                            <td style="width:200px">Keluhan Utama <span class="text-danger">*</span></td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" name="keluhan_utama" value="<?= htmlspecialchars($data_kaji['keluhan_utama']) ?>">
                            </td>
                        </tr>

                        <!-- Pengobatan saat ini -->
                        <tr>
                            <td>Pengobatan saat ini</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <?php $obat = $data_kaji['pengobatan_skrg']; ?>

                                    <label class="form-check mb-0">
                                        <input type="radio" class="form-check-input" name="pengobatan_skrg" value="Tidak" <?= ($obat == 'Tidak') ? 'checked' : '' ?>>
                                        <span class="ms-1">Tidak</span>
                                    </label>

                                    <label class="form-check mb-0">
                                        <input type="radio" class="form-check-input" name="pengobatan_skrg" value="Ya" <?= ($obat == 'Ya') ? 'checked' : '' ?>>
                                        <span class="ms-1">Ya, jelaskan :</span>
                                    </label>

                                    <input type="text" name="pengobatan_ket" class="form-control form-control-sm" style="max-width:300px" value="<?= htmlspecialchars($data_kaji['pengobatan_ket']) ?>">
                                </div>
                            </td>
                        </tr>

                        <!-- Riwayat operasi -->
                        <tr>
                            <td>Riwayat Operasi</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <?php $op = $data_kaji['riw_operasi_ada']; ?>

                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="riw_operasi_ada" value="Tidak ada / Tidak tahu" <?= ($op == 'Tidak ada / Tidak tahu') ? 'checked' : '' ?>>
                                        <span class="ms-1">Tidak ada / Tidak tahu</span>
                                    </label>

                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="riw_operasi_ada" value="Ada" <?= ($op == 'Ada') ? 'checked' : '' ?>>
                                        <span class="ms-1">Ada</span>
                                    </label>

                                    <span class="small">Kapan :</span>

                                    <input type="text" name="riw_operasi_ket" class="form-control form-control-sm" style="max-width:220px" value="<?= htmlspecialchars($data_kaji['riw_operasi_ket']) ?>">
                                </div>
                            </td>
                        </tr>

                        <!-- Riwayat penyakit keluarga -->
                        <tr>
                            <td>Riwayat Penyakit Keluarga</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <input type="text" name="riw_penyakit_keluarga" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['riw_penyakit_keluarga']) ?>">
                            </td>
                        </tr>

                        <!-- Riwayat alergi -->
                        <tr>
                            <td class="align-middle">Riwayat Alergi</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <div class="row g-2 align-items-center">
                                    <?php $alg = $data_kaji['riw_alergi_ada']; ?>

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input riwAlergiRadio" type="radio" name="riw_alergi_ada" value="Tidak" <?= ($alg == 'Tidak') ? 'checked' : '' ?>>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                    </div>

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input riwAlergiRadio" type="radio" name="riw_alergi_ada" value="Ya" <?= ($alg == 'Ya') ? 'checked' : '' ?>>
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                    </div>

                                    <div class="col-10 alergiFields" style="<?= ($alg == 'Ya') ? '' : 'display:none;' ?>">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-2">
                                                <input type="text" name="riw_alergi_kapan" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['riw_alergi_kapan']) ?>">
                                            </div>

                                            <div class="col-auto small">Reaksi :</div>

                                            <div class="col-3">
                                                <input type="text" name="riw_alergi_reaksi" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['riw_alergi_reaksi']) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Merokok -->
                        <tr>
                            <td class="align-middle">Riwayat Merokok</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <?php $m = $data_kaji['merokok']; ?>

                                <div class="row g-2 align-items-center">

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input type="radio" class="form-check-input merokokRadio" name="merokok" value="Tidak" <?= ($m == 'Tidak') ? 'checked' : '' ?>>
                                            <span class="ms-1">Tidak</span>
                                        </label>
                                    </div>

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input type="radio" class="form-check-input merokokRadio" name="merokok" value="Ya" <?= ($m == 'Ya') ? 'checked' : '' ?>>
                                            <span class="ms-1">Ya</span>
                                        </label>
                                    </div>

                                    <div class="col-10 merokokFields" style="<?= ($m == 'Ya') ? '' : 'display:none;' ?>">

                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto small">Jumlah/hari</div>
                                            <div class="col-2">
                                                <input type="text" name="merokok_jml" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['merokok_jml']) ?>">
                                            </div>

                                            <div class="col-auto small">Lama</div>
                                            <div class="col-2">
                                                <input type="text" name="merokok_lama" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['merokok_lama']) ?>">
                                            </div>

                                            <div class="col-auto small">Jenis</div>
                                            <div class="col-3">
                                                <input type="text" name="merokok_jenis" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['merokok_jenis']) ?>">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>

                        <!-- Alkohol -->
                        <tr>
                            <td class="align-middle">Riwayat Alkohol</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <?php $a = $data_kaji['alkohol']; ?>

                                <div class="row g-2 align-items-center">

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input type="radio" class="form-check-input riwAlkoholRadio" name="alkohol" value="Tidak" <?= ($a == 'Tidak') ? 'checked' : '' ?>>
                                            <span class="ms-1">Tidak</span>
                                        </label>
                                    </div>

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input type="radio" class="form-check-input riwAlkoholRadio" name="alkohol" value="Ya" <?= ($a == 'Ya') ? 'checked' : '' ?>>
                                            <span class="ms-1">Ya</span>
                                        </label>
                                    </div>

                                    <div class="col-10 alkoholFields" style="<?= ($a == 'Ya') ? '' : 'display:none;' ?>">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto small">Jumlah/hari</div>
                                            <div class="col-2">
                                                <input type="text" name="alkohol_jml" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['alkohol_jml']) ?>">
                                            </div>

                                            <div class="col-auto small">Lama :</div>
                                            <div class="col-2">
                                                <input type="text" name="alkohol_lama" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['alkohol_lama']) ?>">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>

                    </table>
                </div>

                <!-- PEMERIKSAAN FISIK -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">PEMERIKSAAN FISIK</div>

                    <div class="row ps-4">

                        <!-- KIRI: VITAL SIGN -->
                        <div class="col-md-6 pe-3" style="border-right:1px solid #ddd;">
                            <div class="row">

                                <?php
                                $vitals = [
                                    ['KU', 'ku', ''], ['TD', 'td', 'mmHg'],
                                    ['Nadi', 'nadi', 'x/menit'], ['RR', 'rr', 'x/menit'],
                                    ['Suhu', 'suhu', '°C'], ['BB', 'bb', 'kg'],
                                    ['TB', 'tb', 'cm'], ['LILA', 'lila', 'cm'],
                                ];
                                ?>

                                <?php foreach ($vitals as $v) : ?>
                                    <div class="col-3">
                                        <div class="input-block local-forms mb-3">
                                            <div class="input-group">
                                                <label class="focus-label"><?= $v[0] ?> <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="fisik_<?= $v[1] ?>" value="<?= htmlspecialchars($data_kaji['fisik_' . $v[1]]) ?>">

                                                <?php if ($v[2] != '') : ?>
                                                    <span class="input-group-text fw-bold" style="font-size:0.75rem;"><?= $v[2] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <!-- KANAN: NYERI -->
                        <div class="col-md-6 small">

                            <table class="table table-borderless table-sm mb-2">
                                <tr>
                                    <td style="width:80px">Nyeri</td>
                                    <td>:</td>
                                    <td>
                                        <?php $ny = $data_kaji['nyeri_ada']; ?>
                                        <label class="form-check mb-0 me-3">
                                            <input type="radio" name="nyeri_ada" class="form-check-input" value="Tidak" <?= ($ny == 'Tidak') ? 'checked' : '' ?>>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>

                                        <label class="form-check mb-0">
                                            <input type="radio" name="nyeri_ada" class="form-check-input" value="Ya" <?= ($ny == 'Ya') ? 'checked' : '' ?>>
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                    </td>
                                </tr>

                                <tr class="nyeriFields" style="<?= ($ny == 'Ya') ? '' : 'display:none;' ?>">
                                    <td class="align-top">Metode</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top d-flex gap-3">
                                        <?php $metode = $data_kaji['nyeri_metode']; ?>

                                        <?php foreach (['NRS', 'VAS', 'WBF'] as $mtd) : ?>
                                            <label class="form-check mb-0">
                                                <input type="radio" class="form-check-input" name="nyeri_metode" value="<?= $mtd ?>" <?= ($metode == $mtd) ? 'checked' : '' ?>>
                                                <span class="ms-1 small"><?= $mtd ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>

                                <tr class="nyeriFields" style="<?= ($ny == 'Ya') ? '' : 'display:none;' ?>">
                                    <td class="align-top">Skor</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">

                                        <div class="d-flex flex-wrap gap-2">
                                            <?php
                                            $mapping = [0, 2, 4, 6, 8, 10];
                                            foreach ($mapping as $score) : ?>
                                                <div class="painFace <?= ($data_kaji['nyeri_skor'] == $score) ? 'border border-primary' : '' ?>" data-score="<?= $score ?>" style="cursor:pointer;">
                                                    <img src="<?= base_url('assets/img/nyeri/face_' . $score . '.png') ?>" width="45">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <input type="hidden" name="nyeri_skor" id="nyeri_skor" value="<?= htmlspecialchars($data_kaji['nyeri_skor']) ?>">

                                        <!-- <div class="small mt-1">
                                            Kategori :
                                            <span class="fw-bold text-primary" id="nyeri_kategori">
                                                <?= htmlspecialchars($data_kaji['nyeri_kategori']) ?>
                                            </span>
                                        </div> -->

                                        <div class="small mt-1">
                                            Kategori :
                                            <input id="nyeri_kategori" name="nyeri_kategori" class="form-control-plaintext fw-bold text-primary d-inline-block" readonly style="width:150px;" value="<?= htmlspecialchars($data_kaji['nyeri_kategori']) ?>">
                                        </div>
                                    </td>
                                </tr>

                            </table>

                        </div>
                    </div>
                </div>




                <!-- RESIKO JATUH, STATUS FUNGSIONAL, STATUS MENTAL -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="row small">

                        <!-- Kiri — MORSE -->
                        <div class="col-md-6 pe-3 border-end">

                            <div class="fw-bold text-uppercase small mb-2 text-center">
                                RESIKO JATUH <span class="text-danger">*</span>
                            </div>
                            <div class="text-center small mb-2">Metode Morse Fall Scale</div>

                            <?php
                            // Jika data morse tersimpan per komponen → split jika perlu
                            // Bila hanya morse_total disimpan, maka semua dropdown default = 0
                            $morse_total   = intval($data_kaji['morse_total']);
                            $morse_kategori = $data_kaji['morse_kategori'];

                            // Default value 0 setiap komponen saat edit
                            // (Anda dapat expand jika nanti field per-komponen dibuat)
                            ?>

                            <table class="table table-sm table-bordered align-middle">
                                <tbody>
                                    <?php
                                    // Daftar opsi Morse
                                    $morseOptionsx = [
                                        ['Riwayat jatuh', '0', '25'],
                                        ['Diagnosis medis sekunder ≥1', '0', '15'],
                                        ['Alat bantu', '0', '15', '30'],
                                        ['Memakai terapi IV', '0', '20'],
                                        ['Cara berjalan', '0', '15', '30'],
                                        ['Status mental', '0', '30'],
                                    ];

                                    $morse_vals = [
                                        intval($data_kaji['morse_1'] ?? 0),
                                        intval($data_kaji['morse_2'] ?? 0),
                                        intval($data_kaji['morse_3'] ?? 0),
                                        intval($data_kaji['morse_4'] ?? 0),
                                        intval($data_kaji['morse_5'] ?? 0),
                                        intval($data_kaji['morse_6'] ?? 0),
                                    ];

                                    $morseOptions = [
                                        // label, value => label tampil
                                        [
                                            'Riwayat jatuh',
                                            ['0' => 'Tidak', '25' => 'Ya']
                                        ],
                                        [
                                            'Diagnosis medis sekunder ≥1',
                                            ['0' => 'Tidak', '15' => 'Ya']
                                        ],
                                        [
                                            'Alat bantu',
                                            [
                                                '0'  => 'Bed rest / dibantu perawat',
                                                '15' => 'Walker / tongkat',
                                                '30' => 'Furniture'
                                            ]
                                        ],
                                        [
                                            'Memakai terapi IV',
                                            ['0' => 'Tidak', '20' => 'Ya']
                                        ],
                                        [
                                            'Cara berjalan',
                                            [
                                                '0'  => 'Normal / bedrest',
                                                '15' => 'Lemah',
                                                '30' => 'Terganggu'
                                            ]
                                        ],
                                        [
                                            'Status mental',
                                            [
                                                '0'  => 'Orientasi baik',
                                                '30' => 'Lupa keterbatasan diri'
                                            ]
                                        ],
                                    ];
                                    ?>

                                    <?php foreach ($morseOptions as $idx => $row) : ?>
                                        <tr>
                                            <td><?= $idx + 1 ?></td>
                                            <td><?= $row[0] ?></td>
                                            <td>
                                                <!-- <select class="form-select form-select-sm morseOpt" data-index="<?= $idx ?>">
                                                    <?php foreach ($opt as $k => $v) : ?>
                                                        <?php if ($k === 0) continue; // skip label 
                                                        ?>
                                                        <option value="<?= $v ?>"><?= ($v == 0 ? 'Tidak' : $v) ?></option>
                                                    <?php endforeach; ?>
                                                </select> -->

                                                <select class="form-select form-select-sm morseOpt" name="morse_<?= $idx + 1 ?>" data-index="<?= $idx ?>">
                                                    <?php foreach ($row[1] as $val => $label) : ?>
                                                        <option value="<?= $val ?>" <?= ($morse_vals[$idx] == $val) ? 'selected' : '' ?>>
                                                            <?= $label ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- <div class="mt-2">
                                Total Skor :
                                <input id="morse_total" class="form-control form-control-sm d-inline-block text-center" style="width:80px" readonly value="<?= $morse_total ?>">
                            </div>

                            <div class="mt-1 small">
                                Kategori :
                                <span id="morse_kategori" class="fw-bold text-primary">
                                    <?= htmlspecialchars($morse_kategori) ?>
                                </span>
                            </div> -->

                            <div class="mt-2">
                                Total Skor :
                                <input id="morse_total" name="morse_total" class="form-control form-control-sm d-inline-block text-center" style="width:80px" readonly>
                            </div>

                            <div class="mt-2">
                                Kategori :
                                <input id="morse_kategori" name="morse_kategori" class="form-control-plaintext fw-bold text-primary d-inline-block" readonly style="width:150px;">
                            </div>

                        </div>

                        <!-- Kanan — STATUS FUNGSIONAL + MENTAL -->
                        <div class="col-md-6 ps-4">

                            <!-- STATUS FUNGSIONAL -->
                            <div class="fw-bold text-uppercase small mb-2 text-center">
                                STATUS FUNGSIONAL <span class="text-danger">*</span>
                            </div>
                            <div class="mb-3">
                                <?php
                                $funcList = [
                                    'Mandiri',
                                    'Perlu Bantuan',
                                    'Ketergantungan Total (Lapor DPJP)'
                                ];
                                $funcValue = $data_kaji['status_fungsional'];
                                ?>
                                <?php foreach ($funcList as $i => $lbl) : ?>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="radio" name="status_fungsional" id="f<?= $i ?>" value="<?= $lbl ?>" <?= ($funcValue == $lbl) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="f<?= $i ?>"><?= $lbl ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr class="my-3">

                            <!-- STATUS MENTAL -->
                            <div class="fw-bold text-uppercase small mb-2 text-center">
                                STATUS MENTAL <span class="text-danger">*</span>
                            </div>
                            <?php
                            $mentalList = ['Orientasi baik', 'Disorientasi', 'Kooperatif', 'Tidak Kooperatif'];
                            $mentalChecked = explode(',', $data_kaji['status_mental']);
                            ?>
                            <?php foreach ($mentalList as $i => $lbl) : ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="status_mental[]" id="m<?= $i ?>" value="<?= $lbl ?>" <?= in_array($lbl, $mentalChecked) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="m<?= $i ?>"><?= $lbl ?></label>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <!-- SKRINING GIZI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">SKRINING GIZI</div>

                    <?php
                    $gizi_masalah = $data_kaji['gizi_masalah'];
                    $gizi_detail_arr = explode(',', $data_kaji['gizi_detail']);
                    $gizi_lain = $data_kaji['gizi_lainnya_ket'];
                    ?>

                    <table class="table table-borderless table-sm small">
                        <tr>
                            <td style="width:200px">Masalah gizi khusus <span class="text-danger">*</span></td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <!-- TIDAK ADA -->
                                <label class="form-check form-check-inline mb-1">
                                    <input class="form-check-input giziRadio" type="radio" name="gizi_masalah" value="Tidak ada" <?= ($gizi_masalah == 'Tidak ada') ? 'checked' : '' ?>>
                                    <span class="ms-1">Tidak ada</span>
                                </label>

                                <!-- ADA -->
                                <label class="form-check form-check-inline mb-1">
                                    <input class="form-check-input giziRadio" type="radio" name="gizi_masalah" value="Ya" <?= ($gizi_masalah == 'Ya') ? 'checked' : '' ?>>
                                    <span class="ms-1">Ya</span>
                                </label>

                                <!-- DETAIL GIZI -->
                                <div class="giziFields mt-2" style="<?= ($gizi_masalah == 'Ya') ? '' : 'display:none;' ?>">

                                    <div class="row g-2">

                                        <?php
                                        $opts = ['Tampak kurus', 'DM', 'Hipertensi', 'Gangguan Hati', 'Gangguan Ginjal'];
                                        foreach ($opts as $i => $o) : ?>
                                            <div class="col-auto">
                                                <label class="form-check m-0">
                                                    <input class="form-check-input" type="checkbox" name="gizi_detail[]" value="<?= $o ?>" <?= in_array($o, $gizi_detail_arr) ? 'checked' : '' ?>>
                                                    <span class="ms-1"><?= $o ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- GIZI LAINNYA -->
                                        <div class="col-12 d-flex align-items-center gap-2">
                                            <label class="form-check m-0">
                                                <input class="form-check-input giziLainnyaCheck" type="checkbox" name="gizi_detail[]" value="Lainnya" <?= ($gizi_lain != '') ? 'checked' : '' ?>>
                                                <span class="ms-1">Lainnya :</span>
                                            </label>

                                            <input type="text" class="form-control form-control-sm giziLainnyaInput" name="gizi_lainnya_ket" style="max-width:350px; <?= ($gizi_lain != '') ? '' : 'display:none;' ?>;" value="<?= htmlspecialchars($gizi_lain) ?>" placeholder="(Lapor DPJP & asesmen ahli gizi)">
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </table>
                </div>





                <!-- KEBUTUHAN BELAJAR / EDUKASI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">KEBUTUHAN BELAJAR / EDUKASI</div>

                    <?php
                    $edk_bicara = $data_kaji['edk_bicara'];
                    $edk_bicara_ket = $data_kaji['edk_bicara_ket'];

                    $edk_penerjemah = $data_kaji['edk_penerjemah'];
                    $edk_penerjemah_bahasa = $data_kaji['edk_penerjemah_bahasa'];

                    $edk_isyarat = $data_kaji['edk_isyarat'];
                    // $edk_metode = $data_kaji['edk_metode'];

                    $edk_hambatan = $data_kaji['edk_hambatan'];
                    $edk_hambatan_list = explode(',', $data_kaji['edk_hambatan_list']);

                    $edk_kebutuhan = explode(',', $data_kaji['edk_kebutuhan']);
                    $edk_kebutuhan_lain = $data_kaji['edk_kebutuhan_lain'];
                    ?>

                    <!-- Bicara -->
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-3"><label class="form-label mb-0">Bicara</label></div>
                        <div class="col-auto text-end">:</div>
                        <div class="col-6">
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <label class="form-check mb-0">
                                    <input type="radio" name="edk_bicara" value="Normal" class="form-check-input" <?= ($edk_bicara == 'Normal') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Normal</span>
                                </label>

                                <label class="form-check mb-0">
                                    <input type="radio" name="edk_bicara" value="Tidak normal" class="form-check-input" <?= ($edk_bicara == 'Tidak normal') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Tidak normal, sejak :</span>
                                </label>

                                <input type="text" name="edk_bicara_ket" class="form-control form-control-sm" style="max-width:300px; <?= ($edk_bicara == 'Tidak normal') ? '' : 'display:none;' ?>" value="<?= htmlspecialchars($edk_bicara_ket) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Bahasa sehari-hari -->
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-3"><label class="form-label mb-0">Bahasa sehari-hari</label></div>
                        <div class="col-auto text-end">:</div>
                        <div class="col-6">
                            <input type="text" name="edk_bahasa" class="form-control form-control-sm" value="<?= htmlspecialchars($data_kaji['edk_bahasa']) ?>">
                        </div>
                    </div>

                    <!-- Penerjemah -->
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-3"><label class="form-label mb-0">Perlu penerjemah</label></div>
                        <div class="col-auto text-end">:</div>

                        <div class="col-4">
                            <div class="d-flex gap-3 align-items-center">
                                <label class="form-check mb-0">
                                    <input type="radio" name="edk_penerjemah" value="Tidak" class="form-check-input" <?= ($edk_penerjemah == 'Tidak') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Tidak</span>
                                </label>

                                <label class="form-check mb-0">
                                    <input type="radio" name="edk_penerjemah" value="Ya" class="form-check-input" <?= ($edk_penerjemah == 'Ya') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Ya, bahasa :</span>
                                </label>

                                <input type="text" name="edk_penerjemah_bahasa" class="form-control form-control-sm" style="max-width:200px; <?= ($edk_penerjemah == 'Ya') ? '' : 'display:none;' ?>" value="<?= htmlspecialchars($edk_penerjemah_bahasa) ?>">
                            </div>
                        </div>

                        <!-- Bahasa Isyarat -->
                        <div class="col-3">
                            <div class="d-flex gap-3 align-items-center">
                                <label class="form-label mb-0">Bahasa Isyarat:</label>

                                <label class="form-check mb-0">
                                    <input type="radio" class="form-check-input" name="edk_isyarat" value="Ya" <?= ($edk_isyarat == 'Ya') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Ya</span>
                                </label>

                                <label class="form-check mb-0">
                                    <input type="radio" class="form-check-input" name="edk_isyarat" value="Tidak" <?= ($edk_isyarat == 'Tidak') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Tidak</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Metode belajar -->
                    <!-- <div class="row g-2 align-items-center mb-2">
                        <div class="col-3"><label class="form-label mb-0">Metode belajar</label></div>
                        <div class="col-auto text-end">:</div>
                        <div class="col-6">
                            <input type="text" name="edk_metode" class="form-control form-control-sm" value="<?= htmlspecialchars($edk_metode) ?>">
                        </div>
                    </div> -->

                    <!-- Hambatan -->
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-3"><label class="form-label mb-0">Hambatan edukasi</label></div>
                        <div class="col-auto text-end">:</div>
                        <div class="col-6">
                            <div class="d-flex flex-wrap gap-3 align-items-center">

                                <label class="form-check mb-0">
                                    <input type="radio" name="edk_hambatan" value="Tidak ada" class="form-check-input" <?= ($edk_hambatan == 'Tidak ada') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Tidak ada</span>
                                </label>

                                <label class="form-check mb-0">
                                    <input type="radio" name="edk_hambatan" value="Ada" class="form-check-input" <?= ($edk_hambatan == 'Ada') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Ada :</span>
                                </label>

                                <?php
                                $hambatan_options = [
                                    'Gangguan penglihatan',
                                    'Gangguan pendengaran',
                                    'Belum melek huruf'
                                ];
                                ?>

                                <?php foreach ($hambatan_options as $ho) : ?>
                                    <div class="hambatanItem form-check mb-0" style="<?= ($edk_hambatan == 'Ada') ? '' : 'display:none;' ?>">
                                        <input class="form-check-input" type="checkbox" name="edk_hambatan_list[]" value="<?= $ho ?>" <?= in_array($ho, $edk_hambatan_list) ? 'checked' : '' ?>>
                                        <label class="form-check-label small"><?= $ho ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- EDUKASI KESEHATAN -->
                    <div class="row g-2 align-items-start mb-2">
                        <div class="col-3"><label class="form-label mb-0">Edukasi kesehatan</label></div>
                        <div class="col-auto text-end">:</div>
                        <div class="col">
                            <div class="d-flex flex-wrap gap-4">

                                <?php
                                $kebutuhan_opts = [
                                    'Tentang Pasien',
                                    'Cuci tangan & etika batuk/bersin',
                                    'Mengurangi resiko jatuh'
                                ];
                                ?>

                                <?php foreach ($kebutuhan_opts as $ko) : ?>
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="edk_kebutuhan[]" value="<?= $ko ?>" <?= in_array($ko, $edk_kebutuhan) ? 'checked' : '' ?>>
                                        <span class="ms-1 small"><?= $ko ?></span>
                                    </label>
                                <?php endforeach; ?>

                                <!-- lainnya -->
                                <label class="form-check mb-0">
                                    <input class="form-check-input edkLainCheck" type="checkbox" <?= ($edk_kebutuhan_lain != '') ? 'checked' : '' ?>>
                                    <span class="ms-1 small">Lainnya :</span>
                                </label>

                                <input type="text" class="form-control form-control-sm edkKebutuhanLainInput" name="edk_kebutuhan_lain" style="max-width:260px; <?= ($edk_kebutuhan_lain != '') ? '' : 'display:none;' ?>" value="<?= htmlspecialchars($edk_kebutuhan_lain) ?>">

                            </div>
                        </div>
                    </div>
                </div>

                <!-- SOSIAL BUDAYA -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">SOSIAL BUDAYA</div>

                    <?php
                    $sos_agama = $data_kaji['sos_agama'];
                    $sos_pendidikan = $data_kaji['sos_pendidikan'];
                    $sos_pendidikan_lain = $data_kaji['sos_pendidikan_lain'];
                    $sos_kerja = $data_kaji['sos_kerja'];
                    $sos_kerja_lain = $data_kaji['sos_kerja_lain'];
                    ?>

                    <div class="mb-2">
                        <span class="small text-muted">Agama</span>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($agama as $i => $a) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sos_agama" value="<?= $a->keterangan ?>" <?= ($sos_agama == $a->keterangan) ? 'checked' : '' ?>>
                                    <label class="form-check-label small"><?= $a->keterangan ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Pendidikan -->
                    <div class="mb-2">
                        <span class="small text-muted">Pendidikan Pasien</span>
                        <div class="d-flex flex-wrap gap-3">
                            <?php
                            $pend = ['SD', 'SMP', 'SMA/SMK', 'Akademi/PT', 'Pasca Sarjana'];
                            foreach ($pend as $p) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sos_pendidikan" value="<?= $p ?>" <?= ($sos_pendidikan == $p) ? 'checked' : '' ?>>
                                    <label class="form-check-label small"><?= $p ?></label>
                                </div>
                            <?php endforeach; ?>

                            <!-- lainnya -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sos_pendidikan" value="Lainnya" <?= ($sos_pendidikan == 'Lainnya') ? 'checked' : '' ?>>
                                <label class="form-check-label small">Lainnya :</label>
                            </div>

                            <input type="text" class="form-control form-control-sm" name="sos_pendidikan_lain" style="max-width:220px; <?= ($sos_pendidikan == 'Lainnya') ? '' : 'display:none;' ?>" value="<?= htmlspecialchars($sos_pendidikan_lain) ?>">
                        </div>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="mb-2">
                        <span class="small text-muted">Pekerjaan</span>
                        <div class="d-flex flex-wrap gap-3">
                            <?php
                            $kerja_opts = ['PNS/TNI/POLRI', 'Swasta', 'Pensiun'];
                            foreach ($kerja_opts as $k) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sos_kerja" value="<?= $k ?>" <?= ($sos_kerja == $k) ? 'checked' : '' ?>>
                                    <label class="form-check-label small"><?= $k ?></label>
                                </div>
                            <?php endforeach; ?>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sos_kerja" value="Lainnya" <?= ($sos_kerja == 'Lainnya') ? 'checked' : '' ?>>
                                <label class="form-check-label small">Lainnya :</label>
                            </div>

                            <input type="text" class="form-control form-control-sm" name="sos_kerja_lain" style="max-width:220px; <?= ($sos_kerja == 'Lainnya') ? '' : 'display:none;' ?>" value="<?= htmlspecialchars($sos_kerja_lain) ?>">
                        </div>
                    </div>

                    <!-- Suku -->
                    <div class="col-12 mb-2">
                        <label class="form-label small mb-0">Suku</label>
                        <input type="text" class="form-control form-control-sm" name="sos_suku" value="<?= htmlspecialchars($data_kaji['sos_suku']) ?>">
                    </div>

                    <!-- Warga -->
                    <div class="col-12 mb-2">
                        <label class="form-label small mb-0">Kewarganegaraan</label>
                        <input type="text" class="form-control form-control-sm" name="sos_kewarganegaraan" value="<?= htmlspecialchars($data_kaji['sos_kewarganegaraan']) ?>">
                    </div>

                </div>

                <!-- RESPON EMOSI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">RESPON EMOSI</div>

                    <?php
                    $emosi_checked = explode(',', $data_kaji['emosi']);
                    ?>

                    <div class="d-flex flex-wrap gap-3">
                        <?php
                        $emosi_list = [
                            'Takut terhadap terapi / pembedahan / lingkungan RS', 'Marah / Tegang', 'Sedih', 'Menangis',
                            'Senang', 'Takut', 'Mampu menahan diri', 'Cemas', 'Rendah diri', 'Gelisah', 'Tenang', 'Mudah tersinggung'
                        ];
                        foreach ($emosi_list as $i => $e) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="emosi[]" value="<?= $e ?>" <?= in_array($e, $emosi_checked) ? 'checked' : '' ?>>
                                <label class="form-check-label small"><?= $e ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- MASALAH & RENCANA -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="row g-3">

                        <!-- Masalah -->
                        <div class="col-md-6">
                            <div class="fw-bold text-uppercase small mb-2">DAFTAR MASALAH KEPERAWATAN</div>

                            <?php
                            $masalah_checked = explode(',', $data_kaji['masalah_keperawatan']);
                            $masalah_list = [
                                'Nyeri', 'Pola Tidur', 'Mobilitas / Aktivitas', 'Integritas Kulit', 'Perawatan Diri',
                                'Infeksi', 'Keselamatan pasien', 'Nutrisi', 'Eliminasi', 'Pengetahuan / komunikasi',
                                'Keseimbangan Cairan dan Elektrolit', 'Pola nafas', 'Tumbuh Kembang', 'Suhu Tubuh',
                                'Perfusi jaringan', 'Konflik Peran', 'Jalan nafas / Pertukaran Gas'
                            ];
                            $masalah_tambahan = explode(',', $data_kaji['masalah_tambahan']);
                            ?>

                            <?php foreach ($masalah_list as $m) : ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="masalah_keperawatan[]" value="<?= $m ?>" <?= in_array($m, $masalah_checked) ? 'checked' : '' ?>>
                                    <label class="form-check-label small"><?= $m ?></label>
                                </div>
                            <?php endforeach; ?>

                            <!-- 3 input tambahan -->
                            <?php for ($i = 0; $i < 3; $i++) : ?>
                                <input type="text" class="form-control form-control-sm mt-1" name="masalah_tambahan[]" value="<?= htmlspecialchars($masalah_tambahan[$i] ?? '') ?>" placeholder="_____________________________">
                            <?php endfor; ?>
                        </div>

                        <!-- Rencana -->
                        <div class="col-md-6">
                            <div class="fw-bold text-uppercase small mb-2">RENCANA KEPERAWATAN</div>

                            <textarea class="form-control" name="rencana_keperawatan" rows="12"><?= htmlspecialchars($data_kaji['rencana_keperawatan']) ?></textarea>
                        </div>

                    </div>
                </div>

                <!-- KOLABORASI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">KOLABORASI</div>

                    <?php
                    $kolab_list = [
                        'Oksigen', 'Nebulizer', 'IVFD', 'EKG', 'Transfusi darah', 'NGT', 'DC Shock', 'Eksplorasi',
                        'Obat', 'Kateter', 'Menyiapkan Lab', 'Memberi Obat Parenteral', 'Irigasi Mata', 'RJP'
                    ];
                    $kolab_checked = explode(',', $data_kaji['kolaborasi']);
                    ?>

                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($kolab_list as $k) : ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="kolaborasi[]" value="<?= $k ?>" <?= in_array($k, $kolab_checked) ? 'checked' : '' ?>>
                                <label class="form-check-label small"><?= $k ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <input type="text" class="form-control form-control-sm mt-2" name="kolaborasi_ket" value="<?= htmlspecialchars($data_kaji['kolaborasi_ket']) ?>" placeholder="……….……………………………………………………………………………………………………………………">
                </div>

                <!-- PERAWAT / BIDAN -->
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-bold text-uppercase small mb-3">PERAWAT / BIDAN YANG MENGKAJI</div>

                    <div class="row g-3 align-items-center">

                        <div class="col-md-4">
                            <label class="form-label small mb-1">Tanggal & Jam</label>
                            <div class="form-control form-control-sm bg-light py-2">
                                <?= htmlspecialchars($data_kaji['perawat_tgljam']) ?>
                            </div>
                            <input type="hidden" name="perawat_tgljam" value="<?= htmlspecialchars($data_kaji['perawat_tgljam']) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small mb-1">Nama Perawat / Bidan</label>
                            <div class="form-control form-control-sm bg-light py-2">
                                <?= htmlspecialchars($data_kaji['perawat_nama']) ?>
                            </div>
                            <input type="hidden" name="perawat_nama" value="<?= htmlspecialchars($data_kaji['perawat_nama']) ?>">
                        </div>

                    </div>
                </div>

                <!-- ACTION BUTTON -->
                <!-- <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-light">Kembali</a>
                    <button type="submit" class="btn btn-primary">Update Pengkajian</button>
                </div> -->


                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-light">
                        <i class="fa fa-arrow-left me-1"></i> Kembali
                    </a>

                    <!-- <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Update Pengkajian
                    </button> -->

                    <button type="button" id="btnUpdateKajiAwal" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Update Pengkajian
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