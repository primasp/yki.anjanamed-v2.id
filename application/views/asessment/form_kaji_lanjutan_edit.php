<?php
// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Edit Pengkajian Awal Pasien Rawat Jalan (Umum)';

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