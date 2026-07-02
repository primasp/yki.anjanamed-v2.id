<?php
// form_kaji_awal_full.php
// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Pengkajian Awal Pasien Rawat Jalan (Umum)';
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
    <form id="formKajiAwalFull" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/saveKajiAwalFull'); ?>">
        <input type="hidden" name="episode_id" value="<?= htmlspecialchars($episode_id ?? '') ?>">
        <input type="hidden" name="pasien_id" value="<?= htmlspecialchars($pasien->pasien_id ?? '') ?>">
        <input type="hidden" name="poli_id" value="<?= htmlspecialchars($layan->poli_id ?? '') ?>">

        <div class="card shadow-s">
            <div class="card-body p-4">
                <!-- Header form: logo + judul + identitas -->
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-7 d-flex align-items-center">
                        <img src="<?= base_url('assets/img/yki_logo.png'); ?>" alt="Logo" style="height:56px" class="me-3">
                        <div>
                            <h5 class="mb-0 fw-bold">Klinik Utama Graha YKI Jakarta</h5>
                            <div class="small text-muted">Yayasan Kanker Indonesia Cabang Jakarta</div>
                        </div>
                    </div>
                    <div class="col-md-5 text-end">
                        <span class="badge bg-success fs-6">PENGKAJIAN AWAL PASIEN (Kunjungan Pertama)</span>
                    </div>
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Nomor RM</label>
                            <input type="text" class="form-control form-control-sm" name="rm_no" value="<?= htmlspecialchars($pasien->no_rm ?? '') ?>" readonly>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Nama</label>
                            <input type="text" class="form-control form-control-sm" name="nama" value="<?= htmlspecialchars($pasien->nama ?? '') ?>" readonly>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Tanggal Lahir / Umur</label>
                            <?php
                            // pastikan ada data tgl_lahir dan konversi ke format d/m/Y
                            $tgl_lahir_fmt = !empty($pasien->tgl_lahir)
                                ? date('d/m/Y', strtotime($pasien->tgl_lahir))
                                : '';

                            // hitung umur berdasarkan tanggal lahir (jika ada)
                            $umur_pasien = '';
                            if (!empty($pasien->tgl_lahir)) {
                                $lahir = new DateTime($pasien->tgl_lahir);
                                $today = new DateTime();
                                $umur_pasien = $today->diff($lahir)->y . ' th';
                            }
                            $gabung_tgl_umur = trim($tgl_lahir_fmt . ' / ' . $umur_pasien, ' /');
                            ?>
                            <input type="text" class="form-control form-control-sm" name="tgl_lahir_umur" value="<?= htmlspecialchars($gabung_tgl_umur) ?>" placeholder="dd/mm/yyyy / xx th" readonly>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Jenis Kelamin</label>
                            <div class="d-flex gap-3 align-items-center pt-1">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="jk" id="jkL" value="L" <?= ($pasien->sex_id ?? '') === 'L' ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="jkL">L</label>
                                </div>
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="jk" id="jkP" value="P" <?= ($pasien->sex_id ?? '') === 'P' ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="jkP">P</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PENGKAJIAN KEPERAWATAN -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">PENGKAJIAN KEPERAWATAN (diisi perawat)</div>
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Tiba di Poliklinik</label>
                            <?php
                            $tgl_tiba_fmt = !empty($layan->tgl_masuk)
                                ? date('d/m/Y', strtotime($layan->tgl_masuk))
                                : '';
                            ?>
                            <input type="text" name="tiba_tgl" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_tiba_fmt) ?>" placeholder="Tanggal" readonly>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small mb-0">Pkl</label>
                            <?php
                            // Ambil waktu sekarang, format 24 jam:menit
                            $jam_sekarang = date('H:i');
                            ?>
                            <input type="time" name="tiba_jam" class="form-control form-control-sm" value="<?= htmlspecialchars($jam_sekarang) ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Pengkajian</label>
                            <?php
                            // format tanggal masuk dari DB (2025-11-05 → 05/11/2025)
                            $tgl_kaji_fmt = !empty($layan->tgl_masuk)
                                ? date('d/m/Y', strtotime($layan->tgl_masuk))
                                : '';
                            ?>
                            <input type="text" name="tgl_kaji" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_kaji_fmt) ?>" placeholder="Tanggal" readonly>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small mb-0">Pkl</label>
                            <?php
                            // Ambil waktu sekarang, format 24 jam:menit
                            $jam_sekarang = date('H:i');
                            ?>
                            <input type="time" name="jam_kaji" class="form-control form-control-sm" value="<?= htmlspecialchars($jam_sekarang) ?>">
                        </div>
                        <div class="col-12"></div>
                        <div class="col-12 col-md-6 mt-2">
                            <label class="form-label small mb-0">Diperoleh dari</label>
                            <div class="d-flex gap-3 pt-1 flex-wrap">
                                <?php $dari = ['Pasien', 'Keluarga', 'Lainnya'];
                                foreach ($dari as $i => $lbl) : ?>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="checkbox" id="dari<?= $i ?>" name="diperoleh_dari[]" value="<?= $lbl ?>">
                                        <label class="form-check-label small" for="dari<?= $i ?>"><?= $lbl ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mt-2">
                            <label class="form-label small mb-0">Hubungan dengan pasien</label>
                            <div class="d-flex gap-2 align-items-center">
                                <select name="hub_dengan_pasien" id="hub_dengan_pasien" class="form-select form-select-sm select2">
                                    <option value="">-- Pilih Hubungan --</option>
                                    <?php if (!empty($hubungan)) : foreach ($hubungan as $h) : ?>
                                            <option value="<?= htmlspecialchars($h->keterangan) ?>">
                                                <?= htmlspecialchars($h->keterangan) ?>
                                            </option>
                                    <?php endforeach;
                                    endif; ?>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>

                                <input type="text" name="hub_dengan_pasien_lain" id="hub_dengan_pasien_lain" class="form-control form-control-sm" placeholder="Sebutkan hubungan..." style="max-width: 260px; display:none;">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small mb-0">Cara Masuk</label>
                            <div class="d-flex gap-3 pt-1 flex-wrap">
                                <?php if (!empty($cara_masuk)) : ?>
                                    <?php foreach ($cara_masuk as $i => $cm) : ?>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="checkbox" id="masuk<?= $i ?>" name="cara_masuk[]" value="<?= htmlspecialchars($cm->keterangan) ?>">
                                            <label class="form-check-label small" for="masuk<?= $i ?>">
                                                <?= htmlspecialchars($cm->keterangan) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">RIWAYAT KESEHATAN</div>
                    <table class="w-100 small align-middle" style="border-collapse: separate; border-spacing:0 8px;">
                        <tr>
                            <td style="width:200px">Keluhan Utama <span class="text-danger">*</span></td>
                            <td class="pe-2 text-end">:</td>
                            <td><input type="text" class="form-control form-control-sm" name="keluhan_utama"></td>
                        </tr>
                        <tr>
                            <td>Pengobatan saat ini</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="pengobatan_skrg" value="Tidak" checked>
                                        <span class="ms-1 small">Tidak</span>
                                    </label>
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="pengobatan_skrg" value="Ya">
                                        <span class="ms-1 small">Ya, jelaskan :</span>
                                    </label>
                                    <input type="text" name="pengobatan_ket" class="form-control form-control-sm" style="max-width:300px">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Riwayat Operasi</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="riw_operasi_ada" value="Tidak ada / Tidak tahu" checked>
                                        <span class="ms-1 small">Tidak ada / Tidak tahu</span>
                                    </label>
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="riw_operasi_ada" value="Ada">
                                        <span class="ms-1 small">Ada</span>
                                    </label>
                                    <span class="small">Kapan :</span>
                                    <input type="text" name="riw_operasi_ket" class="form-control form-control-sm" style="max-width:220px">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Riwayat Penyakit Keluarga</td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <input type="text" name="riw_penyakit_keluarga_ket" class="form-control form-control-sm flex-grow-1">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Riwayat Alergi</td>
                            <td class="pe-2 text-end align-middle">:</td>
                            <td class="align-middle">
                                <div class="row g-2 align-items-center">
                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input riwAlergiRadio" type="radio" name="riw_alergi_ada" value="Tidak" checked>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                    </div>

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input riwAlergiRadio" type="radio" name="riw_alergi_ada" value="Ya">
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                    </div>


                                    <div class="col-10 alergiFields" style="display:none;">
                                        <div class="row g-2 align-items-center">
                                            <!-- <div class="col-auto small">Jumlah/hari</div> -->
                                            <div class="col-2"><input type="text" name="riw_alergi_kapan" class="form-control form-control-sm"></div>

                                            <div class="col-auto small">Reaksi :</div>
                                            <div class="col-2"><input type="text" name="riw_alergi_reaksi" class="form-control form-control-sm"></div>


                                        </div>
                                    </div>

                                </div>
                            </td>





                        </tr>
                        <tr>
                            <td class="align-middle">Riwayat Merokok</td>
                            <td class="pe-2 text-end align-middle">:</td>
                            <td class="align-middle">
                                <div class="row g-2 align-items-center">
                                    <!-- <== ini yang penting -->

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input merokokRadio" type="radio" name="merokok" value="Tidak" checked>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                    </div>

                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input merokokRadio" type="radio" name="merokok" value="Ya">
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                    </div>

                                    <div class="col-10 merokokFields" style="display:none;">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto small">Jumlah/hari</div>
                                            <div class="col-2"><input type="text" name="merokok_jml" class="form-control form-control-sm"></div>

                                            <div class="col-auto small">Lama</div>
                                            <div class="col-2"><input type="text" name="merokok_lama" class="form-control form-control-sm"></div>

                                            <div class="col-auto small">Jenis</div>
                                            <div class="col-3"><input type="text" name="merokok_jenis" class="form-control form-control-sm"></div>
                                        </div>
                                    </div>

                                </div>
                            </td>

                        </tr>
                        <tr>
                            <td class="align-middle">Riwayat Minuman Alkohol</td>
                            <td class="pe-2 text-end align-middle">:</td>
                            <td class="align-middle">
                                <div class="row g-2 align-items-center">
                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input riwAlkoholRadio" type="radio" name="alkohol" value="Tidak" checked>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                    </div>
                                    <div class="col-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input riwAlkoholRadio" type="radio" name="alkohol" value="Ya">
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                    </div>
                                    <div class="col-10 alkoholFields" style="display:none;">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto small">Jumlah/hari</div>
                                            <div class="col-2"><input type="text" name="alkohol_jml" class="form-control form-control-sm"></div>

                                            <div class="col-auto small">Lama :</div>
                                            <div class="col-2"><input type="text" name="alkohol_lama" class="form-control form-control-sm"></div>
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
                        <div class="col-md-6 pe-3" style="border-right:1px solid #ddd;">
                            <div class="row">
                                <?php
                                $vitals = [
                                    ['KU', 'ku', ''],
                                    ['TD (mmHg)', 'td', 'mmHg'],
                                    ['Nadi (x/menit)', 'nadi', 'x/menit'],
                                    ['RR (x/menit)', 'rr', 'x/menit'],
                                    ['Suhu (°C)', 'suhu', '°C'],
                                    ['BB (kg)', 'bb', 'Kg'],
                                    ['TB (cm)', 'tb', 'Cm'],
                                    ['LILA (cm)', 'lila', 'Cm'],
                                ];

                                foreach ($vitals as $v) : ?>
                                    <div class="col-3">
                                        <div class="input-block local-forms mb-4">
                                            <div class="input-group">
                                                <label class="focus-label"><?= $v[0] ?> <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="fisik_<?= $v[1] ?>">
                                                <?php if ($v[2] != '') : ?>
                                                    <span class="input-group-text fw-bold" style="font-size: 0.75rem;"><?= $v[2] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div> <!-- end .row -->
                        </div>
                        <!-- KANAN — NYERI -->
                        <div class="col-md-6 small">
                            <table class="table table-borderless table-sm mb-2">
                                <tr>
                                    <td style="width:80px">Nyeri <span class="text-danger">*</span></td>
                                    <td>:</td>
                                    <td>
                                        <label class="form-check mb-0 me-3">
                                            <input type="radio" class="form-check-input" name="nyeri_ada" value="Tidak" checked>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input type="radio" class="form-check-input" name="nyeri_ada" value="Ya">
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="nyeriFields" style="display:none;">
                                    <td class="align-top">Metode</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top small d-flex gap-3">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="nyeri_metode" value="NRS" checked>
                                            <span class="ms-1 small">NRS</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="nyeri_metode" value="VAS">
                                            <span class="ms-1 small">VAS</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="nyeri_metode" value="WBF">
                                            <span class="ms-1 small">WBF</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="nyeriFields" style="display:none;">
                                    <td class="align-top">Skor</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">
                                        <div class="d-flex flex-wrap gap-2">
                                            <!-- 6 icon -->
                                            <?php
                                            $mapping = [
                                                0 => 0,
                                                1 => 2,
                                                2 => 4,
                                                3 => 6,
                                                4 => 8,
                                                5 => 10
                                            ];
                                            foreach ($mapping as $idx => $score) : ?>
                                                <div class="painFace" data-score="<?= $score ?>" style="cursor:pointer;">
                                                    <img src="<?= base_url('assets/img/nyeri/face_' . $score . '.png') ?>" width="45">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <input type="hidden" name="nyeri_skor" id="nyeri_skor">
                                        <!-- <div class="small mt-1">
                                            Kategori : <span class="fw-bold text-primary" id="nyeri_kategori"></span>
                                        </div> -->

                                        <div class="mt-2">
                                            Kategori :
                                            <input id="nyeri_kategori" name="nyeri_kategori" class="form-control-plaintext fw-bold text-primary d-inline-block" readonly style="width:150px;">
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
                        <!-- kiri — morse jatuh -->
                        <div class="col-md-6 pe-3 border-end">
                            <div class="fw-bold text-uppercase small mb-2 text-center">RESIKO JATUH</div>
                            <div class="text-center small mb-2">Metode Morse Fall Scale</div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle">
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Riwayat jatuh</td>
                                            <td>
                                                <select class="form-select form-select-sm morseOpt" name="morse_1">
                                                    <option value="0">Tidak</option>
                                                    <option value="25">Ya</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Diagnosis medis sekunder ≥1</td>
                                            <td>
                                                <select class="form-select form-select-sm morseOpt" name="morse_2">
                                                    <option value="0">Tidak</option>
                                                    <option value="15">Ya</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Alat bantu</td>
                                            <td>
                                                <select class="form-select form-select-sm morseOpt" name="morse_3">
                                                    <option value="0">Bed rest / dibantu perawat</option>
                                                    <option value="15">Walker / tongkat</option>
                                                    <option value="30">Furniture</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Memakai terapi IV</td>
                                            <td>
                                                <select class="form-select form-select-sm morseOpt" name="morse_4">
                                                    <option value="0">Tidak</option>
                                                    <option value="20">Ya</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>5</td>
                                            <td>Cara berjalan</td>
                                            <td>
                                                <select class="form-select form-select-sm morseOpt" name="morse_5">
                                                    <option value="0">Normal / bedrest</option>
                                                    <option value="15">Lemah</option>
                                                    <option value="30">Terganggu</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>6</td>
                                            <td>Status mental</td>
                                            <td>
                                                <select class="form-select form-select-sm morseOpt" name="morse_6">
                                                    <option value="0">Orientasi baik</option>
                                                    <option value="30">Lupa keterbatasan diri</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                            <div class="mt-2">
                                Total Skor :
                                <input id="morse_total" name="morse_total" class="form-control form-control-sm d-inline-block text-center" style="width:80px" readonly>
                            </div>

                            <div class="mt-2">
                                Kategori :
                                <input id="morse_kategori" name="morse_kategori" class="form-control-plaintext fw-bold text-primary d-inline-block" readonly style="width:150px;">
                            </div>

                        </div>
                        <!-- kanan — status fungsional + mental -->
                        <div class="col-md-6 ps-4">
                            <div class="fw-bold text-uppercase small mb-2 text-center">STATUS FUNGSIONAL</div>
                            <div class="mb-3">
                                <?php
                                $func = ['Mandiri', 'Perlu Bantuan', 'Ketergantungan Total (Lapor DPJP)'];
                                foreach ($func as $i => $lbl) : ?>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="radio" name="status_fungsional" id="f<?= $i ?>" value="<?= $lbl ?>" <?= ($i == 0 ? 'checked' : '') ?>>
                                        <label class="form-check-label" for="f<?= $i ?>"><?= $lbl ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr class="my-3">

                            <div class="fw-bold text-uppercase small mb-2 text-center">STATUS MENTAL</div>
                            <?php
                            $mental = ['Orientasi baik', 'Disorientasi', 'Kooperatif', 'Tidak Kooperatif'];
                            foreach ($mental as $i => $lbl) : ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" id="m<?= $i ?>" name="status_mental[]" value="<?= $lbl ?>">
                                    <label class="form-check-label" for="m<?= $i ?>"><?= $lbl ?></label>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <!-- SKRINING GIZI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">SKRINING GIZI</div>
                    <table class="table table-borderless table-sm small">
                        <tr>
                            <td style="width:200px">Masalah gizi khusus <span class="text-danger">*</span></td>
                            <td class="pe-2 text-end">:</td>
                            <td>
                                <!-- baris 1 -->
                                <div class="mb-1">
                                    <label class="form-check form-check-inline mb-0">
                                        <input class="form-check-input giziRadio" type="radio" name="gizi_masalah" value="Tidak ada" checked>
                                        <span class="ms-1">Tidak ada</span>
                                    </label>
                                </div>
                                <!-- baris 2 -->
                                <div class="mb-1">
                                    <label class="form-check form-check-inline mb-0">
                                        <input class="form-check-input giziRadio" type="radio" name="gizi_masalah" value="Ya">
                                        <span class="ms-1">Ya</span>
                                    </label>
                                </div>
                                <!-- pilihan lanjutan -->
                                <div class="giziFields mt-1" style="display:none;">
                                    <div class="row g-2">
                                        <?php
                                        $opts = ['Tampak kurus', 'DM', 'Hipertensi', 'Gangguan Hati', 'Gangguan Ginjal'];
                                        foreach ($opts as $i => $lbl) : ?>
                                            <div class="col-auto">
                                                <label class="form-check m-0">
                                                    <input class="form-check-input" type="checkbox" name="gizi_detail[]" value="<?= $lbl ?>">
                                                    <span class="ms-1"><?= $lbl ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                        <div class="col-12 d-flex align-items-center gap-2">
                                            <label class="form-check m-0">
                                                <input class="form-check-input giziLainnyaCheck" type="checkbox" name="gizi_detail[]" value="Lainnya">
                                                <span class="ms-1">Lainnya :</span>
                                            </label>
                                            <input type="text" class="form-control form-control-sm giziLainnyaInput" name="gizi_lainnya_ket" style="max-width:350px; display:none;" placeholder="(Lapor DPJP & asesmen ahli gizi)">
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
                    <div class="row g-2 small ">
                        <!-- BICARA -->
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col-3">
                                    <label class="form-label mb-0">Bicara <span class="text-danger">*</span></label>
                                </div>
                                <!-- <label class="form-label mb-0 me-2">Bicara</label> -->
                                <div class="col-auto text-end">:</div>
                                <div class="col-6">
                                    <div class="d-flex flex-wrap gap-3 pt-1 align-items-center">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_bicara" value="Normal" checked>
                                            <span class="ms-1 small"> Normal</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_bicara" value="Tidak normal">
                                            <span class="ms-1 small">Tidak normal, gangguan bicara sejak</span>
                                        </label>
                                        <input type="text" class="form-control form-control-sm" name="edk_bicara_ket" style="max-width:340px; display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col-3">
                                    <label class="form-label mb-0">Bahasa sehari-hari <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-auto text-end">:</div>
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-sm" name="edk_bahasa">
                                </div>
                            </div>
                        </div>
                        <!-- PENERJEMAH -->
                        <div class="col-12">
                            <div class="row g-2 align-items-center">

                                <div class="col-3">
                                    <label class="form-label mb-0">Perlu penerjemah <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-auto text-end">:</div>
                                <div class="col-4">
                                    <!-- <label class="form-label mb-0 me-2">Perlu penerjemah</label> -->
                                    <div class="d-flex flex-wrap gap-3 pt-1 align-items-center">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_penerjemah" value="Tidak" checked>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_penerjemah" value="Ya">
                                            <span class="ms-1 small">Ya, bahasa</span>
                                        </label>
                                        <input type="text" class="form-control form-control-sm" name="edk_penerjemah_bahasa" style="max-width:220px; display:none;">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-wrap gap-3 pt-1 align-items-center">
                                        <label class="form-label mb-0">Bahasa Isyarat:</label>
                                        <label class="form-check mb-0 ms-1">
                                            <input class="form-check-input" type="radio" name="edk_isyarat" value="Ya">
                                            <span class="ms-1 small">Ya</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_isyarat" value="Tidak" checked>
                                            <span class="ms-1 small">Tidak</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-12">
                            <div class="row g-2 align-items-center">
                            
                                <div class="col-3">
                                    <label class="form-label mb-0">Metode belajar yang disukai <span class="text-danger">*</span></label>
                                </div>
                            
                                <div class="col-auto text-end">:</div>
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-sm" name="edk_metode">
                                </div>
                            </div>
                        </div> -->
                        <!-- HAMBATAN -->
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col-3">
                                    <label class="form-label mb-0">Hambatan menerima edukasi <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-auto text-end">:</div>
                                <!-- <label class="form-label mb-0">Hambatan menerima edukasi</label> -->
                                <div class="col-6">
                                    <div class="d-flex flex-wrap gap-3 pt-1">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_hambatan" value="Tidak ada" checked>
                                            <span class="ms-1 small">Tidak ada</span>
                                        </label>

                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="edk_hambatan" value="Ada">
                                            <span class="ms-1 small">Ada :</span>
                                        </label>

                                        <div class="hambatanItem form-check mb-0" style="display:none;">
                                            <input class="form-check-input" type="checkbox" name="edk_hambatan_list[]" value="Gangguan penglihatan">
                                            <label class="form-check-label small">Gangguan penglihatan</label>
                                        </div>

                                        <div class="hambatanItem form-check mb-0" style="display:none;">
                                            <input class="form-check-input" type="checkbox" name="edk_hambatan_list[]" value="Gangguan pendengaran">
                                            <label class="form-check-label small">Gangguan pendengaran</label>
                                        </div>

                                        <div class="hambatanItem form-check mb-0" style="display:none;">
                                            <input class="form-check-input" type="checkbox" name="edk_hambatan_list[]" value="Belum melek huruf">
                                            <label class="form-check-label small">Belum melek huruf</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- EDUKASI KESEHATAN -->
                        <div class="col-12">
                            <div class="row g-2 align-items-start align-items-center">
                                <!-- label kiri -->
                                <div class="col-3">
                                    <label class="form-label mb-0">Edukasi kesehatan yang dibutuhkan <span class="text-danger">*</span></label>
                                </div>
                                <!-- titik dua -->
                                <div class="col-auto text-end">:</div>
                                <!-- kol kanan -->
                                <div class="col ">
                                    <div class="d-flex flex-wrap gap-4 pt-1 align-items-center">

                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="edk_kebutuhan[]" value="Tentang Pasien">
                                            <span class="ms-1 small">Tentang Pasien</span>
                                        </label>

                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="edk_kebutuhan[]" value="Cuci tangan & etika batuk/bersin">
                                            <span class="ms-1 small">Cuci tangan dan etika batuk bersin</span>
                                        </label>

                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="edk_kebutuhan[]" value="Mengurangi resiko jatuh">
                                            <span class="ms-1 small">Mengurangi resiko cidera karena jatuh</span>
                                        </label>

                                        <label class="form-check mb-0">
                                            <input class="form-check-input edkLainCheck" type="checkbox" value="Lainnya">
                                            <span class="ms-1 small">Lainnya :</span>
                                        </label>

                                        <input type="text" class="form-control form-control-sm edkKebutuhanLainInput" name="edk_kebutuhan_lain" style="max-width:260px;display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SOSIAL BUDAYA -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">SOSIAL BUDAYA</div>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3">
                                <span class="small text-muted">Agama <span class="text-danger">*</span></span>
                                <?php foreach ($agama as $i => $a) : ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sos_agama" id="sos_agm<?= $i ?>" value="<?= $a->keterangan ?>">
                                        <label class="form-check-label small" for="sos_agm<?= $i ?>"><?= $a->keterangan ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3">
                                <span class="small text-muted">Pendidikan Pasien <span class="text-danger">*</span></span>
                                <?php
                                $pend = ['SD', 'SMP', 'SMA/SMK', 'Akademi/PT', 'Pasca Sarjana'];
                                foreach ($pend as $i => $p) : ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sos_pendidikan" id="sos_pend<?= $i ?>" value="<?= $p ?>">
                                        <label class="form-check-label small" for="sos_pend<?= $i ?>"><?= $p ?></label>
                                    </div>
                                <?php endforeach; ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sos_pendidikan" id="sos_pendX" value="Lainnya">
                                    <label class="form-check-label small" for="sos_pendX">Lainnya :</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="sos_pendidikan_lain" style="max-width:220px">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3">
                                <span class="small text-muted">Pekerjaan <span class="text-danger">*</span></span>
                                <?php
                                $kerja = ['PNS/TNI/POLRI', 'Swasta', 'Pensiun'];
                                foreach ($kerja as $i => $k) : ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sos_kerja" id="sos_ker<?= $i ?>" value="<?= $k ?>">
                                        <label class="form-check-label small" for="sos_ker<?= $i ?>"><?= $k ?></label>
                                    </div>
                                <?php endforeach; ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sos_kerja" id="sos_kerX" value="Lainnya">
                                    <label class="form-check-label small" for="sos_kerX">Lainnya :</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="sos_kerja_lain" style="max-width:220px">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small mb-0">Suku <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="sos_suku">
                        </div>
                        <div class="col-12">
                            <label class="form-label small mb-0">Kewarganegaraan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="sos_warga">
                        </div>
                    </div>
                </div>

                <!-- RESPON EMOSI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">RESPON EMOSI <span class="text-danger">*</span></div>
                    <div class="d-flex flex-wrap gap-3">
                        <?php
                        $emosi = [
                            'Takut terhadap terapi / pembedahan / lingkungan RS',
                            'Marah / Tegang', 'Sedih', 'Menangis', 'Senang', 'Takut', 'Mampu menahan diri',
                            'Cemas', 'Rendah diri', 'Gelisah', 'Tenang', 'Mudah tersinggung'
                        ];
                        foreach ($emosi as $i => $e) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="emosi[]" id="em<?= $i ?>" value="<?= $e ?>">
                                <label class="form-check-label small" for="em<?= $i ?>"><?= $e ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- DAFTAR MASALAH & RENCANA KEPERAWATAN -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-bold text-uppercase small mb-2">DAFTAR MASALAH KEPERAWATAN <span class="text-danger">*</span></div>
                            <?php
                            $masalah = [
                                'Nyeri', 'Pola Tidur', 'Mobilitas / Aktivitas', 'Integritas Kulit', 'Perawatan Diri',
                                'Infeksi', 'Keselamatan pasien', 'Nutrisi', 'Eliminasi', 'Pengetahuan / komunikasi',
                                'Keseimbangan Cairan dan Elektrolit', 'Pola nafas', 'Tumbuh Kembang', 'Suhu Tubuh',
                                'Perfusi jaringan', 'Konflik Peran', 'Jalan nafas / Pertukaran Gas'
                            ];
                            foreach ($masalah as $i => $m) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="masalah_keperawatan[]" id="ms<?= $i ?>" value="<?= $m ?>">
                                    <label class="form-check-label small" for="ms<?= $i ?>"><?= $m ?></label>
                                </div>
                            <?php endforeach; ?>
                            <?php for ($i = 1; $i <= 3; $i++) : ?>
                                <input type="text" class="form-control form-control-sm mt-1" name="masalah_tambahan[]" placeholder="_____________________________">
                            <?php endfor; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-bold text-uppercase small mb-2">RENCANA KEPERAWATAN <span class="text-danger">*</span></div>
                            <textarea class="form-control" name="rencana_keperawatan" rows="12" placeholder="........................................................................................................................................................................"></textarea>
                        </div>
                    </div>
                </div>

                <!-- KOLABORASI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">KOLABORASI <span class="text-danger">*</span></div>
                    <div class="d-flex flex-wrap gap-3">
                        <?php
                        $kolab = [
                            'Oksigen', 'Nebulizer', 'IVFD', 'EKG', 'Transfusi darah', 'NGT', 'DC Shock', 'Eksplorasi',
                            'Obat', 'Kateter', 'Menyiapkan Lab', 'Memberi Obat Parenteral', 'Irigasi Mata', 'RJP'
                        ];
                        foreach ($kolab as $i => $k) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="kolaborasi[]" id="kb<?= $i ?>" value="<?= $k ?>">
                                <label class="form-check-label small" for="kb<?= $i ?>"><?= $k ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="text" class="form-control form-control-sm mt-2" name="kolaborasi_ket" placeholder="……….……………………………………………………………………………………………………………………">
                </div>

                <!-- PERAWAT / BIDAN YANG MENGKAJI -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">PERAWAT / BIDAN YANG MENGKAJI</div>
                    <div class="row g-2">
                        <!-- <div class="col-md-4">
                            <label class="form-label small mb-0">Tanggal & Jam</label>
                            <input type="text" class="form-control form-control-sm" name="perawat_tgljam" placeholder="dd/mm/yyyy • hh:mm">
                        </div> -->

                        <div class="col-md-4">
                            <label class="form-label small mb-1">Tanggal & Jam</label>
                            <div class="form-control form-control-sm bg-light py-2">
                                <span id="label_tgljam"></span>
                            </div>
                            <input type="hidden" name="perawat_tgljam" id="perawat_tgljam">
                        </div>






                        <div class="col-md-4">
                            <label class="form-label small mb-1">Nama Perawat / Bidan</label>
                            <div class="form-control form-control-sm bg-light py-2">
                                <?= htmlspecialchars($user['nama_lengkap'] ?? $user['nama'] ?? $user['user_name']) ?>
                            </div>
                            <input type="hidden" name="perawat_nama" value="<?= htmlspecialchars($user['nama_lengkap'] ?? $user['nama'] ?? $user['user_name']) ?>">
                        </div>
                        <!-- <div class="col-md-4">
                            <label class="form-label small mb-0">Tanda Tangan</label>
                            <input type="text" class="form-control form-control-sm" name="perawat_ttd" placeholder="(opsional)">
                        </div> -->
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('Asessment'); ?>" class="btn btn-light">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengkajian</button>
                </div>

            </div>
        </div> <!-- end card-->
    </form>
</div>