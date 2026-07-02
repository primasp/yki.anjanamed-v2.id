<!-- form_paliatif.php -->

<style>
    .exam-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 10px;
        align-items: center;
        margin-bottom: 8px;
    }

    .exam-row label {
        margin-bottom: 0;
    }

    .draw-box {
        border: 1px solid #adb5bd;
        border-radius: 4px;
        background: #fff;
    }

    .canvas-wrap {
        text-align: center;
    }

    .canvas-wrap small {
        display: block;
        margin-top: 4px;
        color: #6c757d;
    }
</style>


<?php
// function paliatif_value($key, $default = '')
// {
//     global $paliatif;
//     return isset($paliatif[$key]) ? htmlspecialchars($paliatif[$key]) : $default;
// }

// $title, $episode_id, $pasien_id diisi dari controller
if (!isset($title)) $title = 'Pengkajian Awal Pasien Rawat Jalan (Umum)';

$paliatif = $paliatif ?? [];

function paliatif_value($key, $default = '')
{
    global $paliatif;
    if (isset($paliatif[$key]) && $paliatif[$key] !== null && $paliatif[$key] !== '') {
        return htmlspecialchars($paliatif[$key]);
    }
    return htmlspecialchars($default);
}

function paliatif_checked($key, $value)
{
    global $paliatif;
    return (isset($paliatif[$key]) && (string)$paliatif[$key] === (string)$value) ? 'checked' : '';
}


$symptom_data = [];
if (!empty($paliatif['symptom_data'])) {
    $symptom_data = json_decode($paliatif['symptom_data'], true);
    if (!is_array($symptom_data)) $symptom_data = [];
}

$mental_state = [];
if (!empty($paliatif['mental_state'])) {
    $mental_state = json_decode($paliatif['mental_state'], true);
    if (!is_array($mental_state)) $mental_state = [];
}


?>
<?php if (!empty($is_edit)) : ?>
    <div class="alert alert-info">
        Data asesmen paliatif sebelumnya ditemukan. Form ditampilkan dengan data terakhir dan bisa diperbarui.
    </div>
<?php endif; ?>

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
    <!-- <form id="formPaliatif" method="post"> -->
    <!-- <form id="formPaliatif" class="needs-validation" novalidate method="post" action="<?= base_url('AsessmentController/savePaliatif'); ?>"> -->
    <form id="formPaliatif" class="needs-validation" novalidate method="post" action="<?= base_url('Asessment/savePaliatif'); ?>">
        <input type="hidden" name="episode_id" value="<?= htmlspecialchars($episode_id) ?>">
        <input type="hidden" name="pasien_id" value="<?= htmlspecialchars($pasien_id) ?>">

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold">Hospice Home - Palliative Care (HHPC)</h5>
                        <div class="small text-muted">Yayasan Kanker Indonesia Provinsi DKI Jakarta</div>
                    </div>
                    <span class="badge bg-primary">ASESMEN PALIATIF</span>
                </div>

                <!-- DATA PASIEN -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold mb-2 small text-uppercase">DATA PASIEN</div>


                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <label class="form-label">Oleh Tim / Relawan HHPC</label>
                            <!-- <input type="text" name="petugas_hhpc" class="form-control form-control-sm" required> -->
                            <input type="text" name="petugas_hhpc" class="form-control form-control-sm" value="<?= paliatif_value('petugas_hhpc', $paliatif['petugas_hhpc'] ?? $paliatif['petugas_hhpc'] ?? '') ?>" required>
                        </div>

                        <!-- <div class="col-md-6">
                            <label class="form-label">Hari / Tanggal</label>
                            <input type="date" name="tgl_kunjungan" class="form-control form-control-sm" required>
                        </div> -->

                        <div class="col-md-6">
                            <label class="form-label">Hari / Tanggal</label>
                            <input type="date" name="tgl_kunjungan" class="form-control form-control-sm" value="<?= !empty($paliatif['tgl_kunjungan']) ? date('Y-m-d', strtotime($paliatif['tgl_kunjungan'])) : date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor HHPC – YKI DKI Jakarta</label>
                            <input type="text" name="no_hhpc" class="form-control form-control-sm" value="<?= paliatif_value('no_hhpc', $paliatif['no_hhpc'] ?? $paliatif['no_hhpc'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" name="nama_pasien" class="form-control form-control-sm" value="<?= paliatif_value('nama_pasien', $paliatif['nama_pasien'] ?? $paliatif['nama_pasien'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Umur (tahun)</label>
                            <input type="number" name="umur" value="<?= paliatif_value('umur', $paliatif['umur'] ?? $paliatif['umur'] ?? '') ?>" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Agama</label>
                            <input type="text" name="agama" value="<?= paliatif_value('agama', $paliatif['agama'] ?? $paliatif['agama'] ?? '') ?>" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block">Jenis Kelamin</label>
                            <div class="form-check form-check-inline">
                                <!-- <input class="form-check-input" type="radio" name="jenis_kelamin" value="L"> -->
                                <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" <?= paliatif_checked('jenis_kelamin', 'L') ?: (($pasien->sex_id ?? '') == 'L' ? 'checked' : '') ?>>


                                <label class="form-check-label">Laki-laki</label>


                            </div>
                            <div class="form-check form-check-inline">
                                <!-- <input class="form-check-input" type="radio" name="jenis_kelamin" value="P"> -->
                                <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" <?= paliatif_checked('jenis_kelamin', 'P') ?: (($pasien->sex_id ?? '') == 'P' ? 'checked' : '') ?>>
                                <label class="form-check-label">Perempuan</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Suku Bangsa</label>
                            <input type="text" name="suku_bangsa" class="form-control form-control-sm" value="<?= paliatif_value('suku_bangsa', $paliatif['suku_bangsa'] ?? $paliatif['suku_bangsa'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bahasa Sehari-hari</label>
                            <input type="text" name="bahasa" class="form-control form-control-sm" value="<?= paliatif_value('bahasa', $paliatif['bahasa'] ?? $paliatif['bahasa'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2"><?= paliatif_value('alamat', $paliatif['alamat'] ?? $paliatif['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telp" class="form-control form-control-sm" value="<?= paliatif_value('telp', $paliatif['telp'] ?? $paliatif['telp'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fax</label>
                            <input type="text" name="fax" class="form-control form-control-sm" value="<?= paliatif_value('fax', $paliatif['fax'] ?? $paliatif['fax'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">HP</label>
                            <input type="text" name="hp" class="form-control form-control-sm" value="<?= paliatif_value('hp', $paliatif['hp'] ?? $paliatif['hp'] ?? '') ?>">
                        </div>
                    </div>

                </div>


                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold mb-2 small text-uppercase">MASALAH YANG DIHADAPI (PALIATIF)</div>

                    <textarea name="masalah_1" class="form-control form-control-sm mb-2" rows="2" placeholder="1."><?= paliatif_value('masalah_1', $paliatif['masalah_1'] ?? $paliatif['masalah_1'] ?? '') ?></textarea>
                    <textarea name="masalah_2" class="form-control form-control-sm mb-2" rows="2" placeholder="2."><?= paliatif_value('masalah_2', $paliatif['masalah_2'] ?? $paliatif['masalah_2'] ?? '') ?></textarea>
                    <textarea name="masalah_3" class="form-control form-control-sm" rows="2" placeholder="3."><?= paliatif_value('masalah_3', $paliatif['masalah_3'] ?? $paliatif['masalah_3'] ?? '') ?></textarea>
                </div>


                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-bold mb-2 small text-uppercase">INFORMASI PERAWATAN & KELUARGA</div>

                    <div class="mb-2">
                        <label class="form-label">Yang Merawat di Rumah (Care Giver)</label>
                        <input type="text" name="care_giver" class="form-control form-control-sm" value="<?= paliatif_value('care_giver', $paliatif['care_giver'] ?? $paliatif['care_giver'] ?? '') ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label d-block">Apakah Pasien Mengetahui Tentang Penyakitnya?</label>
                        <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="radio" name="pasien_tahu" value="YA"> -->
                            <input class="form-check-input" type="radio" name="pasien_tahu" value="YA" <?= paliatif_checked('pasien_tahu', 'YA') ?: (($paliatif['pasien_tahu'] ?? '') == 'YA' ? 'checked' : '') ?>>
                            <label class="form-check-label">YA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pasien_tahu" value="TIDAK" <?= paliatif_checked('pasien_tahu', 'TIDAK') ?: (($paliatif['pasien_tahu'] ?? '') == 'TIDAK' ? 'checked' : '') ?>>
                            <label class="form-check-label">TIDAK</label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Keluarga yang Menderita Penyakit yang Sama</label>
                        <input type="text" name="keluarga_sakit_sama" class="form-control form-control-sm" value="<?= paliatif_value('keluarga_sakit_sama', $paliatif['keluarga_sakit_sama'] ?? $paliatif['keluarga_sakit_sama'] ?? '') ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Dukungan yang Diperlukan</label>
                        <textarea name="dukungan" class="form-control form-control-sm" rows="2"><?= paliatif_value('dukungan', $paliatif['dukungan'] ?? $paliatif['dukungan'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="form-label">Staff Perawat HHPC – YKI Prov. DKI Jakarta</label>
                        <input type="text" name="staff_perawat" class="form-control form-control-sm" value="<?= paliatif_value('staff_perawat', $paliatif['staff_perawat'] ?? $paliatif['staff_perawat'] ?? '') ?>">
                    </div>
                </div>


                <div class="card shadow-sm mb-3">
                    <div class="card-body">

                        <div class="fw-bold mb-3 text-uppercase small">
                            Physical Symptoms
                        </div>

                        <!-- NYERI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nyeri</label>
                            <textarea name="nyeri" class="form-control form-control-sm" rows="2" placeholder="Lokasi, skala, durasi, faktor pencetus"> <?= paliatif_value('nyeri', $paliatif['nyeri'] ?? $paliatif['nyeri'] ?? '') ?></textarea>
                        </div>

                        <div class="row g-3 small">

                            <!-- KOLOM KIRI -->
                            <div class="col-md-6">
                                <?php
                                $leftSymptoms = [
                                    'anorexia' => 'Anorexia',
                                    'nausea' => 'Nausea',
                                    'vomiting' => 'Vomiting',
                                    'penurunan_bb' => 'Penurunan BB',
                                    'haematemesis' => 'Haematemesis',
                                    'sariawan' => 'Sariawan',
                                    'mulut_kering' => 'Mulut Kering',
                                    'difficulty_swallowing' => 'Difficulty in Swallowing',
                                    'konstipasi' => 'Konstipasi',
                                    'obstipasi' => 'Obstipasi',
                                    'diarrhoea' => 'Diarrhoea',
                                    'faecal_incontinence' => 'Faecal Incontinence',
                                    'perdarahan' => 'Perdarahan',
                                    'abdomen_distended' => 'Abdomen Distended',
                                    'other_symptoms' => 'Other Symptoms'
                                ];
                                foreach ($leftSymptoms as $name => $label) :
                                    $id = "symptom_" . $name;
                                    $checked = !empty($symptom_data[$name]['cek']);
                                    $ket = !empty($symptom_data[$name]['ket']) ? $symptom_data[$name]['ket'] : '';
                                ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="form-check me-2">
                                            <input class="form-check-input symptom-check" type="checkbox" id="<?= $id ?>" data-target="<?= $id ?>_desc" name="symptom[<?= $name ?>]" value="1" <?= $checked ? 'checked' : '' ?>>
                                        </div>

                                        <label for="<?= $id ?>" class="me-2 mb-0" style="min-width:180px;">
                                            <?= $label ?>
                                        </label>

                                        <input type="text" id="<?= $id ?>_desc" name="symptom_desc[<?= $name ?>]" class="form-control form-control-sm" placeholder="Keterangan" value="<?= htmlspecialchars($ket) ?>" <?= $checked ? '' : 'disabled' ?>>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- KOLOM KANAN -->
                            <div class="col-md-6">
                                <?php
                                $rightSymptoms = [
                                    'sesak_nafas' => 'Sesak Nafas',
                                    'batuk' => 'Batuk',
                                    'sputum' => 'Sputum',
                                    'haemoptysis' => 'Haemoptysis',
                                    'urinary_problems' => 'Urinary Problems',
                                    'swelling' => 'Swelling / Lymphoedema',
                                    'discharge' => 'Discharge',
                                    'dekubitus' => 'Dekubitus',
                                    'gangguan_tidur' => 'Gangguan Tidur',
                                    'kejang' => 'Kejang',
                                    'abnormal_sensation' => 'Abnormal Sensation',
                                    'weakness' => 'Weakness',
                                    'paraplegia' => 'Paraplegia',
                                    'mobility' => 'Mobility'
                                ];
                                foreach ($rightSymptoms as $name => $label) :
                                    $id = "symptom_" . $name;
                                    $checked = !empty($symptom_data[$name]['cek']);
                                    $ket = !empty($symptom_data[$name]['ket']) ? $symptom_data[$name]['ket'] : '';
                                ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="form-check me-2">
                                            <input class="form-check-input symptom-check" type="checkbox" id="<?= $id ?>" data-target="<?= $id ?>_desc" name="symptom[<?= $name ?>]" value="1" <?= $checked ? 'checked' : '' ?>>
                                        </div>

                                        <label for="<?= $id ?>" class="me-2 mb-0" style="min-width:180px;">
                                            <?= $label ?>
                                        </label>

                                        <input type="text" id="<?= $id ?>_desc" name="symptom_desc[<?= $name ?>]" class="form-control form-control-sm" placeholder="Keterangan" value="<?= htmlspecialchars($ket) ?>" <?= $checked ? '' : 'disabled' ?>>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    </div>
                </div>




                <div class="card shadow-sm mb-3">
                    <div class="card-body">

                        <div class="fw-bold mb-3 text-uppercase small">
                            Mental & Emotional Assessment
                        </div>

                        <!-- STATUS MENTAL -->
                        <div class="row g-3 small mb-3">
                            <div class="col-12 fw-semibold mb-1">
                                Status Mental / Emosional
                            </div>

                            <?php
                            $mentalStates = [
                                'alert' => 'Alert',
                                'cooperative' => 'Co-operative',
                                'calm' => 'Calm',
                                'withdrawn' => 'Withdrawn',
                                'irritable' => 'Irritable',
                                'angry' => 'Angry',
                                'anxious' => 'Anxious',
                                'confused' => 'Confused'
                            ];
                            foreach ($mentalStates as $key => $label) :
                                $id = "mental_" . $key;
                            ?>
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="<?= $id ?>" name="mental_state[]" value="<?= $key ?>" <?= in_array($key, $mental_state) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="<?= $id ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- CATATAN MENTAL -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Catatan Mental / Emosional
                            </label>
                            <textarea name="mental_note" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan kondisi emosional pasien"><?= paliatif_value('mental_note', $paliatif['mental_note'] ?? $paliatif['mental_note'] ?? '') ?></textarea>
                        </div>

                        <hr class="my-3">

                        <!-- DIAGNOSIS & PROGNOSIS -->
                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Diagnosis</label>
                                <textarea name="diagnosis" class="form-control form-control-sm" rows="2" placeholder="Diagnosis medis utama"><?= paliatif_value('diagnosis', $paliatif['diagnosis'] ?? $paliatif['diagnosis'] ?? '') ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prognosis</label>
                                <textarea name="prognosis" class="form-control form-control-sm" rows="2" placeholder="Prognosis pasien"><?= paliatif_value('prognosis', $paliatif['prognosis'] ?? $paliatif['prognosis'] ?? '') ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-body">

                        <div class="fw-bold mb-3 text-uppercase small">
                            Examination
                        </div>

                        <!-- KEADAAN UMUM -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keadaan Umum</label>
                            <textarea name="keadaan_umum" class="form-control form-control-sm" rows="2" placeholder="Kondisi umum pasien saat pemeriksaan"><?= paliatif_value('keadaan_umum', $paliatif['keadaan_umum'] ?? $paliatif['keadaan_umum'] ?? '') ?></textarea>
                        </div>



                        <!-- <hr class="my-3"> -->

                        <!-- KOMUNIKASI & STATUS FISIK -->
                        <div class="fw-semibold mt-5 mb-2">Komunikasi</div>
                        <div class="row g-3 small">
                            <!-- BAGIAN KIRI -->
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <!-- <div class="fw-semibold text-muted mb-2">Bagian Kiri</div> -->

                                    <div class="mb-2">
                                        <label class="form-label">Bicara</label>
                                        <input type="text" name="bicara" class="form-control form-control-sm" value="<?= paliatif_value('bicara', $paliatif['bicara'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Pucat</label>
                                        <input type="text" name="pucat" class="form-control form-control-sm" value="<?= paliatif_value('pucat', $paliatif['pucat'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Jaundice</label>
                                        <input type="text" name="jaundice" class="form-control form-control-sm" value="<?= paliatif_value('jaundice', $paliatif['jaundice'] ?? '') ?>">
                                    </div>


                                    <div class="mb-2">
                                        <label class="form-label d-block">Cyanosis</label>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cyanosis" value="Y" <?= paliatif_checked('cyanosis', 'Y') ?: (($paliatif['cyanosis'] ?? '') == 'Y' ? 'checked' : '') ?>>
                                            <label class="form-check-label">Ya</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cyanosis" value="N" <?= paliatif_checked('cyanosis', 'N') ?: (($paliatif['cyanosis'] ?? '') == 'N' ? 'checked' : '') ?>>
                                            <label class="form-check-label">Tidak</label>
                                        </div>
                                    </div>



                                </div>
                            </div>



                            <!-- BAGIAN TENGAH -->
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <!-- <div class="fw-semibold text-muted mb-2">Bagian Tengah</div> -->
                                    <div class="mb-2">
                                        <label class="form-label">Pendengaran</label>
                                        <input type="text" name="pendengaran" class="form-control form-control-sm" value="<?= paliatif_value('pendengaran', $paliatif['pendengaran'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Hydration</label>
                                        <input type="text" name="hydration" class="form-control form-control-sm" value="<?= paliatif_value('hydration', $paliatif['hydration'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Mouth</label>
                                        <input type="text" name="mouth" class="form-control form-control-sm" value="<?= paliatif_value('mouth', $paliatif['mouth'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label d-block">Clubbing</label>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="clubbing" value="Y" <?= paliatif_checked('clubbing', 'Y') ?: (($paliatif['clubbing'] ?? '') == 'Y' ? 'checked' : '') ?>>
                                            <label class="form-check-label">Ya</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="clubbing" value="N" <?= paliatif_checked('clubbing', 'N') ?: (($paliatif['clubbing'] ?? '') == 'N' ? 'checked' : '') ?>>
                                            <label class="form-check-label">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BAGIAN KANAN -->
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">

                                    <div class="mb-2">
                                        <label class="form-label">Penglihatan</label>
                                        <input type="text" name="penglihatan" class="form-control form-control-sm" value="<?= paliatif_value('penglihatan', $paliatif['penglihatan'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Skin</label>
                                        <input type="text" name="skin" class="form-control form-control-sm" value="<?= paliatif_value('skin', $paliatif['skin'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Sinus</label>
                                        <input type="text" name="sinus" class="form-control form-control-sm" value="<?= paliatif_value('sinus', $paliatif['sinus'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Fistula</label>
                                        <input type="text" name="fistula" class="form-control form-control-sm" value="<?= paliatif_value('fistula', $paliatif['fistula'] ?? '') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Dekubitus</label>
                                        <input type="text" name="dekubitus_exam" class="form-control form-control-sm" value="<?= paliatif_value('dekubitus_exam', $paliatif['dekubitus_exam'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>






                        <div class="row g-3 mt-2 mb-2 small">
                            <div class="col-md-12">
                                <div class="border rounded-3 p-3 h-100">

                                    <div class="mb-2 row align-items-center">
                                        <label class="col-md-2 col-form-label form-label mb-0">Oedema / Lymphoedema</label>
                                        <div class="col-md-10">
                                            <input type="text" name="oedema" class="form-control form-control-sm" value="<?= paliatif_value('oedema', $paliatif['oedema'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="mb-2 row align-items-center">
                                        <label class="col-md-2 col-form-label form-label mb-0">Cardiovascular System</label>
                                        <div class="col-md-10">
                                            <input type="text" name="cardiovascular" class="form-control form-control-sm" value="<?= paliatif_value('cardiovascular', $paliatif['cardiovascular'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="mb-2 row align-items-center">
                                        <label class="col-md-2 col-form-label form-label mb-0">Respiratory System</label>
                                        <div class="col-md-6">
                                            <!-- <input type="text" name="cardiovascular" class="form-control form-control-sm" value="<?= paliatif_value('cardiovascular', $paliatif['cardiovascular'] ?? '') ?>"> -->
                                            <textarea name="respiratory_system" class="form-control form-control-sm" rows="4"> <?= paliatif_value('respiratory_system', $paliatif['respiratory_system'] ?? $paliatif['respiratory_system'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-center gap-3">
                                                <div class="canvas-wrap">
                                                    <canvas id="lung_left" width="120" height="160" class="draw-box"></canvas>
                                                    <small>Paru Kiri</small>
                                                </div>
                                                <div class="canvas-wrap">
                                                    <canvas id="lung_right" width="120" height="160" class="draw-box"></canvas>
                                                    <small>Paru Kanan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2 row align-items-center">
                                        <label class="col-md-2 col-form-label form-label mb-0">Abdomen</label>
                                        <div class="col-md-6">
                                            <textarea name="abdomen" class="form-control form-control-sm" rows="4"><?= paliatif_value('abdomen', $paliatif['abdomen'] ?? $paliatif['abdomen'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="canvas-wrap">
                                                <canvas id="abdomen_canvas" width="220" height="320" class="draw-box"></canvas>
                                                <small>Gambar Lokasi Abdomen / Luka</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="fw-semibold mt-5 mb-2">PR / PV</div>
                        <div class="row g-3  small">
                            <div class="col-md-12">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="mb-2 row align-items-center">
                                        <label class="col-md-2 col-form-label form-label mb-0">PR (Per Rectal)</label>
                                        <div class="col-md-10">
                                            <textarea name="pr" class="form-control form-control-sm" rows="2" placeholder="Temuan PR"><?= paliatif_value('pr_note', $paliatif['pr_note'] ?? $paliatif['pr_note'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="mb-2 row align-items-center">
                                        <label class="col-md-2 col-form-label form-label mb-0">PV (Per Vaginam)</label>
                                        <div class="col-md-10">
                                            <textarea name="pv" class="form-control form-control-sm" rows="2" placeholder="Temuan PV"><?= paliatif_value('pv_note', $paliatif['pv_note'] ?? $paliatif['pv_note'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

















                    <!-- ACTION -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('Asessment'); ?>" class="btn btn-light">Kembali</a>

                        <button type="submit" class="btn btn-primary">
                            <?= !empty($is_edit) ? 'Simpan Perubahan Asesmen Paliatif' : 'Simpan Asesmen Paliatif' ?>
                        </button>
                    </div>
                </div>
            </div>
    </form>

</div>




<script>
    document.querySelectorAll('.symptom-check').forEach(cb => {
        cb.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.target);
            if (!target) return;

            target.disabled = !this.checked;
            if (!this.checked) target.value = '';
        });
    });


    function enableDraw(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        let drawing = false;

        canvas.addEventListener('mousedown', () => drawing = true);
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('mouseleave', () => drawing = false);

        canvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        });
    }

    ['lung_left', 'lung_right', 'abdomen_canvas'].forEach(enableDraw);


    function drawBase64ToCanvas(canvasId, base64) {
        if (!base64) return;

        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.onload = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };

        img.src = base64;
    }

    drawBase64ToCanvas('lung_left', '<?= !empty($paliatif["lung_left_img"]) ? addslashes($paliatif["lung_left_img"]) : '' ?>');
    drawBase64ToCanvas('lung_right', '<?= !empty($paliatif["lung_right_img"]) ? addslashes($paliatif["lung_right_img"]) : '' ?>');
    drawBase64ToCanvas('abdomen_canvas', '<?= !empty($paliatif["abdomen_img"]) ? addslashes($paliatif["abdomen_img"]) : '' ?>');
</script>