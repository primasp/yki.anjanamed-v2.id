<?php
// form_kaji_awal.php

// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Pengkajian Awal Pasien Rawat Jalan (Umum)';
?>

<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($title) ?></li>

                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form id="formKajiAwal" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/saveKajiAwal'); ?>">
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
                        <span class="badge bg-success fs-6">PENGKAJIAN AWAL PASIEN (Kunjungan Lanjutan)</span>
                    </div>
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Nomor RM</label>
                            <!-- <input type="text" class="form-control form-control-sm" name="rm_no"> -->
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
                            $jam_sekarang = date('H:i');
                            ?>
                            <input type="time" name="tiba_jam" class="form-control form-control-sm" value="<?= htmlspecialchars($jam_sekarang) ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-0">Pengkajian</label>
                            <?php
                            $tgl_kaji_fmt = !empty($layan->tgl_masuk)
                                ? date('d/m/Y', strtotime($layan->tgl_masuk))
                                : '';
                            ?>
                            <input type="text" name="tgl_kaji" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_kaji_fmt) ?>" placeholder="Tanggal" readonly>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small mb-0">Pkl</label>
                            <?php
                            $jam_sekarang = date('H:i');
                            ?>
                            <input type="time" name="jam_kaji" class="form-control form-control-sm" value="<?= htmlspecialchars($jam_sekarang) ?>">
                        </div>
                        <div class="col-12"></div>




                    </div>
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
                                                <label class="focus-label"><?= $v[0] ?></label>
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
                                    <td style="width:80px">Nyeri</td>
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

                                <!-- <tr class="nyeriFields" style="display:none;">
                                    <td class="align-top">Metode</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top small">NRS / VAS / WBF</td>
                                </tr> -->


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








                <!-- PERAWAT / BIDAN YANG MENGKAJI -->
                <!-- <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-2">PERAWAT / BIDAN YANG MENGKAJI</div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small mb-0">Tanggal & Jam</label>
                            <input type="text" class="form-control form-control-sm" name="perawat_tgljam" placeholder="dd/mm/yyyy • hh:mm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-0">Nama Perawat / Bidan</label>
                            <input type="text" class="form-control form-control-sm" name="perawat_nama">
                        </div>

                    </div>
                </div> -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold text-uppercase small mb-3">PERAWAT / BIDAN YANG MENGKAJI</div>

                    <div class="row g-3 align-items-center">

                        <!-- Tanggal & Jam -->
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Tanggal & Jam</label>
                            <div class="form-control form-control-sm bg-light py-2">
                                <span id="label_tgljam"></span>
                            </div>
                            <input type="hidden" name="perawat_tgljam" id="perawat_tgljam">
                        </div>

                        <!-- Nama Perawat -->
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Nama Perawat / Bidan</label>
                            <div class="form-control form-control-sm bg-light py-2">
                                <?= htmlspecialchars($user['nama_lengkap'] ?? $user['nama'] ?? $user['user_name']) ?>
                            </div>
                            <input type="hidden" name="perawat_nama" value="<?= htmlspecialchars($user['nama_lengkap'] ?? $user['nama'] ?? $user['user_name']) ?>">
                        </div>

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