<?php
// application/views/asessment/partials/filter_form.php
if (!isset($suffix)) $suffix = 'Belum';

$today = date('Y-m-d');
?>

<div class="assessment-filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <label class="form-label">Tanggal Asesmen</label>
            <div class="assessment-input-icon">
                <i class="feather-calendar"></i>
                <input type="date" class="form-control filterTanggal" id="filterTanggalAses<?= $suffix ?>" value="<?= $today ?>">
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6">
            <label class="form-label">Jenis Layanan</label>
            <select class="form-select" id="jenisLayanan<?= $suffix ?>">
                <option value="SEMUA">Semua Poliklinik</option>
                <option value="POLIKLINIK">Filter Poliklinik</option>
            </select>
            <small class="text-muted d-block mt-1">
                Penunjang Lab/Radiologi tidak tampil di asesmen perawat.
            </small>
        </div>

        <div class="col-xl-4 col-lg-4 col-md-12">
            <label class="form-label">Cari Pasien</label>
            <div class="assessment-input-icon">
                <i class="feather-search"></i>
                <input type="text" class="form-control filterSearch" id="filterSearch<?= $suffix ?>" placeholder="Cari nama pasien / No. RM">
            </div>
        </div>
    </div>

    <div id="filterPoliklinik<?= $suffix ?>" class="assessment-subfilter" style="display:none;">
        <div class="row g-3">
            <div class="col-xl-4 col-lg-6 col-md-6">
                <label class="form-label">Nama Poliklinik</label>
                <select class="form-select poliSelect" id="poliSelect<?= $suffix ?>">
                    <option value="">-- Semua Poliklinik --</option>
                    <?php foreach ($poli as $p) : ?>
                        <option value="<?= htmlspecialchars($p->poli_id, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($p->keterangan, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6">
                <label class="form-label">Nama Dokter</label>
                <select class="form-select dokterSelect" id="dokterSelect<?= $suffix ?>">
                    <option value="">-- Semua Dokter --</option>
                </select>
            </div>
        </div>
    </div>
</div>