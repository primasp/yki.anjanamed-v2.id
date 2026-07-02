<?php
// Jika suffix belum diset, default ke "Belum"
if (!isset($suffix)) $suffix = 'Belum';

// Tanggal default = hari ini (format HTML5 date => Y-m-d)
$today = date('Y-m-d');
// return var_dump($today);
// die;
?>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-bold">Tanggal Asesmen:</label>
        <input type="date" class="form-control filterTanggal" id="filterTanggalAses<?= $suffix ?>" value="<?= $today ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Pilih Jenis Layanan:</label>
        <select class="form-select" id="jenisLayanan<?= $suffix ?>">
            <!-- <option value="">==Pilih Jenis Layanan==</option> -->
            <option value="SEMUA">Semua</option>
            <option value="PENUNJANG">Penunjang (Lab / Radiologi)</option>
            <option value="POLIKLINIK">Poliklinik</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Cari Pasien:</label>
        <input type="text" class="form-control filterSearch" id="filterSearch<?= $suffix ?>" placeholder="Nama pasien / No. RM">
    </div>
</div>

<!-- Filter tambahan bila POLIKLINIK -->
<div id="filterPoliklinik<?= $suffix ?>" style="display:none;">
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <label class="form-label">Nama Poliklinik:</label>
            <select class="form-select poliSelect" id="poliSelect<?= $suffix ?>">
                <option value="">-- Pilih Poliklinik --</option>
                <?php foreach ($poli as $p) : ?>
                    <option value="<?= $p->poli_id ?>"><?= $p->keterangan ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nama Dokter:</label>
            <select class="form-select dokterSelect" id="dokterSelect<?= $suffix ?>">
                <option value="">-- Pilih Dokter --</option>
            </select>
        </div>
    </div>
</div>