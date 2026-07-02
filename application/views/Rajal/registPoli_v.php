<!-- <link rel="stylesheet" href="<?= base_url('assets/css/regist-poli.css?v=' . time()) ?>"> -->
<?php if (!empty($page_css)) : ?>
    <link rel="stylesheet" href="<?= base_url('assets/' . $page_css . '?v=' . time()); ?>">
<?php endif; ?>

<div class="content registrasi-yki-v3">
    <div class="page-header yki-page-header">
        <div>
            <ul class="breadcrumb yki-breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('Rajal-All') ?>">Rawat jalan</a></li>
                <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                <li class="breadcrumb-item active">Registrasi Layanan YKI Tahap 2</li>
            </ul>
            <h3>Registrasi Layanan</h3>
            <p>Alur baru: Pemeriksaan Penunjang Rujukan dan Kunjungan ke Poliklinik.</p>
        </div>
        <div class="yki-header-badge">
            <span>YKI</span>
            <strong>Tahap 2</strong>
        </div>
    </div>

    <form id="formRegistLayan" autocomplete="off">
        <div class="row g-3">
            <div class="col-xl-10 col-lg-9 col-md-8">
                <div class="yki-card">
                    <div class="yki-section-head">
                        <div>
                            <span class="yki-section-kicker">01</span>
                            <h4>Data Pasien</h4>
                            <p>Pastikan pasien yang dipilih sudah sesuai sebelum menentukan layanan.</p>
                        </div>
                        <button type="button" class="btn yki-btn-light forwrd-btn-data-pas">
                            <i class="fas fa-redo-alt me-2"></i>Atur Ulang
                        </button>
                    </div>

                    <div class="row g-3 pt-2">
                        <div class="col-xl-5 col-lg-5 col-md-11">
                            <div class="yki-field">
                                <label>Nama Lengkap <span>*</span></label>
                                <input type="text" id="namaPas" name="namaPas" class="form-control" value="<?= isset($pasien) ? $pasien['nama'] : '' ?>" required>
                            </div>
                        </div>

                        <div class="col-xl-1 col-lg-1 col-md-1 d-flex align-items-end">
                            <button type="button" class="btn yki-btn-icon" data-bs-toggle="modal" data-bs-target="#bookingModal" title="Pasien Perjanjian">
                                <i class="fas fa-calendar-check"></i>
                            </button>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="yki-field">
                                <label>No. Rekam Medis <span>*</span></label>
                                <input type="text" id="noRm" name="noRm" class="form-control" value="<?= isset($pasien) ? $pasien['int_pasien_id'] : '' ?>" required>
                                <input type="hidden" name="pasien_id" id="pasien_id" value="<?= isset($pasien) ? $pasien['pasien_id'] : '' ?>">
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="yki-field">
                                <label>Tanggal Lahir <span>*</span></label>
                                <input class="form-control" id="tgl_lahir" name="tgl_lahir" type="text" value="<?= isset($pasien) ? $pasien['tgl_lahir'] : '' ?>" disabled required>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="yki-field">
                                <label>Jenis Kelamin <span>*</span></label>
                                <input class="form-control" id="sex_id" name="sex_id" type="text" value="<?= isset($pasien) ? $pasien['sex_id'] : '' ?>" disabled required>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-4 col-md-12">
                            <div class="yki-field">
                                <label>Alamat <span>*</span></label>
                                <input class="form-control" id="alamat" name="alamat" type="text" value="<?= isset($pasien) ? $pasien['alamat1'] : '' ?>" disabled required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="yki-card mt-3">
                    <div class="yki-section-head">
                        <div>
                            <span class="yki-section-kicker">02</span>
                            <h4>Data Registrasi</h4>
                            <p>Pilih pembiayaan terlebih dahulu agar sistem menyesuaikan layanan yang boleh digunakan.</p>
                        </div>
                        <button type="button" class="btn yki-btn-light forwrd-btn-biaya">
                            <i class="fas fa-redo-alt me-2"></i>Atur Ulang
                        </button>
                    </div>

                    <div class="row g-3 pt-2">
                        <div class="col-xl-5 col-lg-6 col-md-12">
                            <div class="yki-field">
                                <label>Pembiayaan <span>*</span></label>
                                <select name="pembiayaan" id="pembiayaan" class="form-control" required>
                                    <option value="">-- Pilih Pembiayaan --</option>
                                    <option value="UMUM">Umum</option>
                                    <option value="PROGRAM">Program</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="yki-card mt-3">
                    <div class="yki-section-head yki-section-head-border">
                        <div>
                            <span class="yki-section-kicker">03</span>
                            <h4 id="label_layan">Registrasi Penunjang Rujukan</h4>
                            <p id="label_layan_desc">Khusus rujukan dari luar. Layanan yang aktif pada tahap awal adalah Papsmear.</p>
                        </div>
                        <button type="button" class="btn yki-btn-light forwrd-btn-regist">
                            <i class="fas fa-redo-alt me-2"></i>Atur Ulang
                        </button>
                    </div>

                    <div class="yki-visit-switch mt-3">
                        <label class="yki-radio-card active" for="radioPenunjang" id="cardPenunjang">
                            <input class="form-check-input" type="radio" name="jenis_kunjungan" id="radioPenunjang" value="2" checked>
                            <span class="yki-radio-icon"><i class="fas fa-vial"></i></span>
                            <span>
                                <strong>Pemeriksaan Penunjang</strong>
                                <small>Rujukan luar, tampil Papsmear jika pembiayaan UMUM.</small>
                            </span>
                        </label>

                        <label class="yki-radio-card" for="radioPoli" id="cardPoli">
                            <input class="form-check-input" type="radio" name="jenis_kunjungan" id="radioPoli" value="1">
                            <span class="yki-radio-icon"><i class="fas fa-user-md"></i></span>
                            <span>
                                <strong>Kunjungan ke Poliklinik</strong>
                                <small>Pilih poli, dokter, jam slot, dan validasi riwayat layanan pasien.</small>
                            </span>
                        </label>
                    </div>

                    <fieldset id="fieldsetPenunjang" class="yki-fieldset mt-4">
                        <legend>Data Pemeriksaan Penunjang Rujukan</legend>

                        <div id="penunjangAlert" class="yki-alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <span>Pilih pembiayaan <b>UMUM</b> agar layanan Papsmear (Rujukan) dapat ditampilkan.</span>
                        </div>

                        <input type="hidden" id="chkLab" value="1">

                        <div id="labContainer">
                            <div class="yki-mini-head">
                                <div>
                                    <strong>Laboratorium</strong>
                                    <small>Pelayanan khusus rujukan dari luar</small>
                                </div>
                                <span class="yki-pill">Papsmear Rujukan</span>
                            </div>

                            <div id="labItemsList">
                                <div class="row g-3 align-items-end yki-item-row mb-2">
                                    <div class="col-xl-7 col-lg-7 col-md-12">
                                        <div class="yki-field mb-0">
                                            <label>Pelayanan Laboratorium <span>*</span></label>
                                            <select class="form-control lab-item" name="lab_item[]">
                                                <option value="">-- Pilih Pemeriksaan --</option>
                                                <?php foreach ($lab_items as $lab) : ?>
                                                    <option value="<?= html_escape($lab['layan_id']) ?>" data-hargalab="<?= number_format((float) $lab['harga'], 0, ',', '.') ?>">
                                                        <?= html_escape($lab['nama_pemeriksaan']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-4">
                                        <div class="yki-price-box harga-labelLab">Rp 0</div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-4">
                                        <div class="yki-field mb-0">
                                            <label>Qty</label>
                                            <input type="number" class="form-control" name="lab_qty[]" min="1" value="1">
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-4">
                                        <button type="button" class="btn yki-btn-add addLabItem" title="Tambah item"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset id="fieldsetPoli" class="yki-fieldset mt-4" style="display:none;">
                        <legend>Data Kunjungan Poliklinik</legend>
                        <div class="row g-3">
                            <div class="col-xl-7 col-lg-7 col-md-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="yki-field">
                                            <label>Tgl. Berobat <span>*</span></label>
                                            <input type="text" id="tgl_berobat" name="tgl_berobat" class="form-control" value="<?= date('d/m/Y') ?>" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="yki-field">
                                            <label>Pilih Poliklinik <span>*</span></label>
                                            <select name="poli" id="poli" class="form-control select2">
                                                <option value="">-- Pilih Poliklinik --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="yki-field">
                                            <label>Pilih Dokter <span>*</span></label>
                                            <select name="dokter" id="dokter" class="form-control select2">
                                                <option value="">-- Pilih Dokter --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="yki-field">
                                            <label>Pilih Jam Slot <span>*</span></label>
                                            <select name="jam_slot" id="jam_slot" class="form-control select2">
                                                <option value="">-- Pilih Jam Slot --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5 col-lg-5 col-md-12">
                                <div class="yki-history-card">
                                    <div class="yki-history-head">
                                        <div>
                                            <strong>History Layanan Pasien</strong>
                                            <small>Validasi sebelum memakai provider Program</small>
                                        </div>
                                        <button type="button" class="btn yki-btn-refresh" id="btnRefreshHistory" title="Refresh history">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <div id="historyLayananPasien" class="yki-history-body">
                                        <div class="yki-empty-state">
                                            <i class="fas fa-file-medical-alt"></i>
                                            <p>Pilih pasien untuk melihat riwayat layanan sebelumnya.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="yki-sticky-actions">
                    <button type="submit" id="btnRegistPasien" class="btn yki-btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <button type="button" id="btnCancelPasien" class="btn yki-btn-secondary w-100">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <hr>
                    <div class="yki-summary-box">
                        <span>Status Isian</span>
                        <strong id="statusIsianText">Belum lengkap</strong>
                        <div class="yki-progress mt-2"><span id="statusIsianBar" style="width:0%"></span></div>
                    </div>
                    <div class="yki-summary-note mt-3">
                        <i class="fas fa-shield-alt"></i>
                        <p>Pastikan pembiayaan dan jenis kunjungan sudah sesuai alur baru sebelum disimpan.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>