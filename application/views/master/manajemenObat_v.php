<div class="content p-3">

    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold mb-1 text-primary">
                    <i class="fa fa-pills me-2"></i> Manajemen Obat
                </h4>
                <p class="text-muted mb-0">
                    Kelola master obat, harga, satuan, depo, stok, dan adjustment dalam satu halaman.
                </p>
            </div>

            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahObat">
                    <i class="fa fa-plus me-1"></i> Tambah Obat
                </button>
            </div>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="stock-summary-card">
                <div class="summary-label">Total Item</div>
                <div class="summary-value"><?= number_format($summary->total_item ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stock-summary-card danger">
                <div class="summary-label">Stok Minus</div>
                <div class="summary-value"><?= number_format($summary->total_minus ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stock-summary-card warning">
                <div class="summary-label">Stok Habis</div>
                <div class="summary-value"><?= number_format($summary->total_habis ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stock-summary-card info">
                <div class="summary-label">Stok Menipis</div>
                <div class="summary-value"><?= number_format($summary->total_menipis ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="card border-0 shadow-sm mb-3 obat-filter-card">
        <div class="card-body">
            <form method="GET" action="<?= site_url('Manajemen-Obat') ?>">
                <div class="row g-2 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Depo / Gudang</label>
                        <select name="depo_obat" class="form-select form-select-sm">
                            <option value="semua">Semua Depo</option>
                            <?php foreach ($depo_obat as $d) : ?>
                                <option value="<?= $d->gudang_id ?>" <?= (($filter['depo_obat'] ?? '') == $d->gudang_id) ? 'selected' : '' ?>>
                                    <?= $d->keterangan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Golongan Obat</label>
                        <select name="gol_obat" class="form-select form-select-sm">
                            <option value="semua">Semua Golongan</option>
                            <?php foreach ($golongan_obat as $g) : ?>
                                <option value="<?= $g->golobat_id ?>" <?= (($filter['gol_obat'] ?? '') == $g->golobat_id) ? 'selected' : '' ?>>
                                    <?= $g->keterangan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status Stok</label>
                        <select name="status_stok" class="form-select form-select-sm">
                            <?php
                            $status = $filter['status_stok'] ?? 'semua';
                            ?>
                            <option value="semua" <?= $status == 'semua' ? 'selected' : '' ?>>Semua</option>
                            <option value="MINUS" <?= $status == 'MINUS' ? 'selected' : '' ?>>Minus</option>
                            <option value="HABIS" <?= $status == 'HABIS' ? 'selected' : '' ?>>Habis</option>
                            <option value="MENIPIS" <?= $status == 'MENIPIS' ? 'selected' : '' ?>>Menipis</option>
                            <option value="AMAN" <?= $status == 'AMAN' ? 'selected' : '' ?>>Aman</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cari Obat</label>
                        <input type="text" name="search" value="<?= html_escape($filter['search'] ?? '') ?>" class="form-control form-control-sm" placeholder="Kode / nama obat / depo">
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <ul class="nav nav-tabs obat-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabSemuaObat" type="button">
                        Semua Obat
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabStokMinus" type="button">
                        Stok Bermasalah
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane fade show active" id="tabSemuaObat">
                    <?php $this->load->view('master/partials/table_manajemen_obat', ['obat_list' => $obat_list]); ?>
                </div>

                <div class="tab-pane fade" id="tabStokMinus">
                    <?php
                    $stok_bermasalah = array_filter($obat_list, function ($o) {
                        return in_array($o->status_stok, ['MINUS', 'HABIS', 'MENIPIS']);
                    });
                    $this->load->view('master/partials/table_manajemen_obat', ['obat_list' => $stok_bermasalah]);
                    ?>
                </div>

            </div>

        </div>
    </div>

</div>



<div class="modal fade" id="modalEditObat" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable obat-edit-dialog">
        <div class="modal-content obat-edit-modal">

            <form id="form-edit-obat">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Edit Obat</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="edit_obat_id" name="obat_id">

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-capsules"></i> Informasi Obat
                        </div>



                        <div class="mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" id="edit_nama_obat" name="nama_obat" class="form-control form-control-sm">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Sediaan</label>
                                <select id="edit_sediaan_obat" name="sediaan_obat" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($sediaan_obat as $s) : ?>
                                        <option value="<?= $s->sediaan_id ?>"><?= $s->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pabrik</label>
                                <select id="edit_pabrik" name="pabrik" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($pabrik_obat as $p) : ?>
                                        <option value="<?= $p->pabrik_id ?>"><?= $p->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Route</label>
                                <select id="edit_route" name="route" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($route_obat as $r) : ?>
                                        <option value="<?= $r->routeobt_id ?>"><?= $r->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan Jual / Satuan Kecil</label>
                                <select id="edit_satuan_jual" name="satuan_jual" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($satuan_obat as $st) : ?>
                                        <option value="<?= $st->satuan_id ?>"><?= $st->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan Beli / Satuan Besar</label>
                                <select id="edit_satuan_beli" name="satuan_beli" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($satuan_obat as $st) : ?>
                                        <option value="<?= $st->satuan_id ?>"><?= $st->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-calculator"></i> Perhitungan Harga
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Konversi Satuan</label>
                                <input type="text" id="edit_kon_satuan" name="kon_satuan" class="form-control form-control-sm js-edit-price" placeholder="Contoh: 100">
                                <div class="text-muted small">Isi satuan kecil dalam 1 satuan besar.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">PPN (%)</label>
                                <input type="number" id="edit_ppn_persen" class="form-control form-control-sm js-edit-price" value="11" step="0.01">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Margin (%)</label>
                                <input type="number" id="edit_margin_persen" class="form-control form-control-sm js-edit-price" value="20" step="0.01">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Harga HNA Satuan Besar</label>
                                <input type="text" id="edit_hna_besar" name="hna_besar" class="form-control form-control-sm js-edit-price" placeholder="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Harga HNA + PPN Satuan Besar</label>
                                <input type="text" id="edit_hna_ppn_besar" name="hna_ppn_besar" class="form-control form-control-sm js-edit-price" placeholder="0">
                                <div class="form-check mt-1">
                                    <input type="checkbox" id="edit_auto_ppn" class="form-check-input" checked>
                                    <label for="edit_auto_ppn" class="form-check-label small">
                                        Hitung PPN otomatis dari HNA
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Satuan</label>
                                <input type="text" id="edit_jual_sat" name="jual_sat" class="form-control form-control-sm" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Satuan + PPN</label>
                                <input type="text" id="edit_jual_sat_hna_ppn" name="jual_sat_hna_ppn" class="form-control form-control-sm" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Jual Final</label>
                                <input type="text" id="edit_harga_final" name="harga_final" class="form-control form-control-sm js-edit-price" placeholder="0">

                                <div class="form-check mt-1">
                                    <input type="checkbox" id="edit_harga_final_checkbox" name="harga_final_checkbox" class="form-check-input" checked>
                                    <label for="edit_harga_final_checkbox" class="form-check-label small">
                                        Gunakan margin otomatis
                                    </label>
                                </div>

                                <div class="form-check mt-1">
                                    <input type="checkbox" id="edit_harga_manual" class="form-check-input">
                                    <label for="edit_harga_manual" class="form-check-label small">
                                        Harga jual manual
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-tags"></i> Kategori & Referensi
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Golongan Obat</label>
                                <select id="edit_gol_obat" name="gol_obat" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($golongan_obat as $g) : ?>
                                        <option value="<?= $g->golobat_id ?>"><?= $g->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kandungan Generik</label>
                                <select id="edit_generik_obat" name="generik_obat" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($generik_obat as $ge) : ?>
                                        <option value="<?= $ge->generik_id ?>"><?= $ge->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Obat E-Catalogue</label>
                                <input type="text" id="edit_ecatalogue" name="ecatalogue" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nomor Registrasi</label>
                                <input type="text" id="edit_nomor_registrasi" name="nomor_registrasi" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Batasan Fornas</label>
                                <textarea id="edit_batasan_fornas" name="batasan_fornas" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-sliders"></i> Pengaturan Obat
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_obat_fornas" name="obat_fornas" class="form-check-input">
                                    <span class="form-check-label">Obat Fornas</span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_obat_hialert" name="obat_hialert" class="form-check-input">
                                    <span class="form-check-label">Obat High Alert</span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_obat_produksi" name="obat_produksi" class="form-check-input">
                                    <span class="form-check-label">Obat Produksi</span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_alkes" name="alkes" class="form-check-input">
                                    <span class="form-check-label">Alkes</span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_obat_bpjs" name="obat_bpjs" class="form-check-input">
                                    <span class="form-check-label">Tidak untuk Pasien BPJS</span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_semuaykn_obat" name="semuaykn_obat" class="form-check-input">
                                    <span class="form-check-label text-danger">Sembunyikan Obat</span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" id="edit_is_pembungkus" name="is_pembungkus" class="form-check-input">
                                    <span class="form-check-label">Pembungkus Racikan</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="button" id="btn-update-obt" class="btn btn-primary btn-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>






<div class="modal fade" id="modalTambahObat" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable obat-edit-dialog">
        <div class="modal-content obat-edit-modal">

            <form id="form-tambah-obat">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">
                        <i class="fa fa-plus-circle me-1"></i> Tambah Obat Baru
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-capsules"></i> Informasi Obat
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                            <input type="text" id="add_nama_obat" name="nama_obat" class="form-control form-control-sm" placeholder="Contoh: Paracetamol 500 mg">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Sediaan</label>
                                <select id="add_sediaan_obat" name="sediaan_obat" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($sediaan_obat as $s) : ?>
                                        <option value="<?= $s->sediaan_id ?>"><?= $s->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pabrik</label>
                                <select id="add_pabrik" name="pabrik" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($pabrik_obat as $p) : ?>
                                        <option value="<?= $p->pabrik_id ?>"><?= $p->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Route</label>
                                <select id="add_route" name="route" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($route_obat as $r) : ?>
                                        <option value="<?= $r->routeobt_id ?>"><?= $r->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan Jual / Satuan Kecil <span class="text-danger">*</span></label>
                                <select id="add_satuan_jual" name="satuan_jual" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($satuan_obat as $st) : ?>
                                        <option value="<?= $st->satuan_id ?>"><?= $st->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan Beli / Satuan Besar <span class="text-danger">*</span></label>
                                <select id="add_satuan_beli" name="satuan_beli" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($satuan_obat as $st) : ?>
                                        <option value="<?= $st->satuan_id ?>"><?= $st->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-calculator"></i> Perhitungan Harga
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Konversi Satuan <span class="text-danger">*</span></label>
                                <input type="text" id="add_kon_satuan" name="kon_satuan" class="form-control form-control-sm js-add-price" value="1">
                                <div class="text-muted small">Isi satuan kecil dalam 1 satuan besar.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">PPN (%)</label>
                                <input type="number" id="add_ppn_persen" class="form-control form-control-sm js-add-price" value="11" step="0.01">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Margin (%)</label>
                                <input type="number" id="add_margin_persen" class="form-control form-control-sm js-add-price" value="20" step="0.01">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Harga HNA Satuan Besar <span class="text-danger">*</span></label>
                                <input type="text" id="add_hna_besar" name="hna_besar" class="form-control form-control-sm js-add-price" value="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Harga HNA + PPN Satuan Besar <span class="text-danger">*</span></label>
                                <input type="text" id="add_hna_ppn_besar" name="hna_ppn_besar" class="form-control form-control-sm js-add-price" value="0">
                                <div class="form-check mt-1">
                                    <input type="checkbox" id="add_auto_ppn" class="form-check-input" checked>
                                    <label for="add_auto_ppn" class="form-check-label small">Hitung PPN otomatis dari HNA</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                                <input type="text" id="add_jual_sat" name="jual_sat" class="form-control form-control-sm" readonly value="0">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Satuan + PPN <span class="text-danger">*</span></label>
                                <input type="text" id="add_jual_sat_hna_ppn" name="jual_sat_hna_ppn" class="form-control form-control-sm" readonly value="0">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Jual Final <span class="text-danger">*</span></label>
                                <input type="text" id="add_harga_final" name="harga_final" class="form-control form-control-sm js-add-price" value="0">

                                <div class="form-check mt-1">
                                    <input type="checkbox" id="add_harga_final_checkbox" name="harga_final_checkbox" class="form-check-input" checked>
                                    <label for="add_harga_final_checkbox" class="form-check-label small">Gunakan margin otomatis</label>
                                </div>

                                <div class="form-check mt-1">
                                    <input type="checkbox" id="add_harga_manual" class="form-check-input">
                                    <label for="add_harga_manual" class="form-check-label small">Harga jual manual</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-boxes-stacked"></i> Stok Awal
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Depo / Gudang <span class="text-danger">*</span></label>
                                <select id="add_gudang_id" name="gudang_id" class="form-select form-select-sm">
                                    <?php foreach ($depo_obat as $d) : ?>
                                        <option value="<?= $d->gudang_id ?>" <?= ($d->gudang_id == ($default_gudang_id ?? 'DEPO00000000APT')) ? 'selected' : '' ?>>
                                            <?= $d->keterangan ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" min="0" step="0.01" id="add_stok_awal" name="stok_awal" class="form-control form-control-sm" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-tags"></i> Kategori & Referensi
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Golongan Obat</label>
                                <select id="add_gol_obat" name="gol_obat" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($golongan_obat as $g) : ?>
                                        <option value="<?= $g->golobat_id ?>"><?= $g->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kandungan Generik</label>
                                <select id="add_generik_obat" name="generik_obat" class="form-select form-select-sm">
                                    <option value="">- Harap Pilih -</option>
                                    <?php foreach ($generik_obat as $ge) : ?>
                                        <option value="<?= $ge->generik_id ?>"><?= $ge->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Obat E-Catalogue</label>
                                <input type="text" id="add_ecatalogue" name="ecatalogue" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nomor Registrasi</label>
                                <input type="text" id="add_nomor_registrasi" name="nomor_registrasi" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Batasan Fornas</label>
                                <textarea id="add_batasan_fornas" name="batasan_fornas" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="obat-edit-section">
                        <div class="obat-edit-section-title">
                            <i class="fa fa-sliders"></i> Pengaturan Obat
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_obat_fornas" name="obat_fornas" class="form-check-input"> <span class="form-check-label">Obat Fornas</span></label></div>
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_obat_hialert" name="obat_hialert" class="form-check-input"> <span class="form-check-label">Obat High Alert</span></label></div>
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_obat_produksi" name="obat_produksi" class="form-check-input"> <span class="form-check-label">Obat Produksi</span></label></div>
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_alkes" name="alkes" class="form-check-input"> <span class="form-check-label">Alkes</span></label></div>
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_obat_bpjs" name="obat_bpjs" class="form-check-input"> <span class="form-check-label">Tidak untuk Pasien BPJS</span></label></div>
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_semuaykn_obat" name="semuaykn_obat" class="form-check-input"> <span class="form-check-label text-danger">Sembunyikan Obat</span></label></div>
                            <div class="col-md-4"><label class="form-check"><input type="checkbox" id="add_is_pembungkus" name="is_pembungkus" class="form-check-input"> <span class="form-check-label">Pembungkus Racikan</span></label></div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-simpan-obt" class="btn btn-primary btn-sm">
                        <i class="fa fa-save me-1"></i> Simpan Obat
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>