<div class="content kasir-umum-yki-page">
    <div class="kasir-umum-header">
        <div>
            <ul class="breadcrumb kasir-breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('Dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Kasir Umum</li>
            </ul>
            <h3>Kasir Pembayaran Umum</h3>
            <p>Memproses pembayaran tagihan penunjang, tindakan, dan obat yang belum dibayar.</p>
        </div>

        <div class="kasir-header-actions">
            <span class="kasir-user-badge">
                <i class="fa-solid fa-user-tie me-1"></i>
                <?= html_escape($kasir_umum_data['kasir']['nama_petugas'] ?? '-'); ?>
            </span>
            <button type="button" class="btn btn-light border" id="btnRefreshKasirUmum">
                <i class="fa-solid fa-rotate me-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-success" id="btnProsesBayarTop">
                <i class="fa-solid fa-money-bill-wave me-1"></i> Proses Bayar
            </button>
        </div>
    </div>

    <div class="kasir-info-strip">
        <div>
            <strong>Aturan alur baru:</strong>
            Kasir hanya mengambil transaksi UMUM yang belum lunas.
            Biaya pendaftaran akan muncul hanya jika saat registrasi dipilih <b>Bayar Nanti</b>.
            Jika sudah dibayar di awal, transaksi tidak akan ditarik ulang.
        </div>
        <div class="kasir-info-pill">
            <i class="fa-solid fa-shield-heart me-1"></i> Provider: UMUM
        </div>
    </div>

    <div class="kasir-umum-layout">
        <aside class="kasir-worklist-panel">
            <div class="kasir-card sticky-worklist">
                <div class="kasir-card-header">
                    <div>
                        <h6><i class="fa-solid fa-users me-2 text-primary"></i>Worklist Belum Bayar</h6>
                        <small>Pasien umum dengan tagihan aktif</small>
                    </div>
                    <span class="badge bg-primary" id="worklistCount">0</span>
                </div>

                <div class="kasir-filter-box">
                    <div class="row g-2">
                        <div class="col-6">
                            <label>Tanggal Awal</label>
                            <input type="date" id="tanggal_mulai" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="col-6">
                            <label>Tanggal Akhir</label>
                            <input type="date" id="tanggal_selesai" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="input-group input-group-sm mt-2">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="keywordKasir" class="form-control" placeholder="Cari nama / RM / episode">
                    </div>

                    <div class="kasir-chip-filter mt-2">
                        <button class="btn btn-sm btn-outline-primary active" data-kategori="">Semua</button>
                        <button class="btn btn-sm btn-outline-secondary" data-kategori="PENDAFTARAN">Pendaftaran</button>
                        <button class="btn btn-sm btn-outline-info" data-kategori="PENUNJANG">Penunjang</button>
                        <button class="btn btn-sm btn-outline-warning" data-kategori="TINDAKAN">Tindakan</button>
                        <button class="btn btn-sm btn-outline-success" data-kategori="OBAT">Obat</button>
                    </div>
                </div>

                <div class="kasir-worklist" id="kasirWorklist">
                    <div class="kasir-empty">Memuat worklist...</div>
                </div>
            </div>
        </aside>

        <main class="kasir-main-panel">
            <div class="kasir-card selected-patient-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kasir-avatar" id="selectedAvatar">K</div>
                        <div>
                            <h5 id="selectedNama">Pilih Pasien</h5>
                            <div class="selected-meta">
                                <span id="selectedRm">RM -</span>
                                <span>•</span>
                                <span id="selectedPoli">Poli -</span>
                                <span>•</span>
                                <span id="selectedDokter">Dokter -</span>
                            </div>
                        </div>
                    </div>

                    <div class="selected-pills">
                        <span><i class="fa-solid fa-file-invoice me-1"></i><span id="selectedInvoice">-</span></span>
                        <span><i class="fa-solid fa-calendar me-1"></i><span id="selectedTanggal">-</span></span>
                        <strong id="selectedStatus">BELUM BAYAR</strong>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-8">
                    <div class="kasir-card">
                        <div class="kasir-card-header">
                            <div>
                                <h6><i class="fa-solid fa-receipt me-2 text-primary"></i>Detail Tagihan</h6>
                                <small>Centang item yang akan dibayar sekarang</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-light border" id="btnCheckAll"><i class="fa-solid fa-check-double me-1"></i>Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-light border" id="btnUncheckAll"><i class="fa-solid fa-ban me-1"></i>Kosongkan</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-kasir mb-0">
                                <thead>
                                    <tr>
                                        <th width="40"></th>
                                        <th>Item</th>
                                        <th width="120" class="text-center">Kategori</th>
                                        <th width="80" class="text-center">Qty</th>
                                        <th width="120" class="text-end">Tarif</th>
                                        <th width="130" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="billingBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">Pilih pasien dari worklist.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="kasir-card mb-3">
                        <div class="kasir-card-header">
                            <div>
                                <h6><i class="fa-solid fa-calculator me-2 text-success"></i>Ringkasan</h6>
                                <small>Total otomatis dari item terpilih</small>
                            </div>
                        </div>

                        <div class="summary-list">
                            <div><span>Penunjang</span><strong id="sumPenunjang">Rp 0</strong></div>
                            <div><span>Tindakan</span><strong id="sumTindakan">Rp 0</strong></div>
                            <div><span>Obat</span><strong id="sumObat">Rp 0</strong></div>
                            <div><span>Subtotal</span><strong id="sumSubtotal">Rp 0</strong></div>

                            <div class="discount-row">
                                <label>Diskon Global</label>
                                <div class="d-flex gap-2">
                                    <select id="discountType" class="form-select form-select-sm">
                                        <option value="NOMINAL">Rp</option>
                                        <option value="PERSEN">%</option>
                                    </select>
                                    <input type="number" id="discountValue" class="form-control form-control-sm" min="0" value="0">
                                </div>
                            </div>

                            <div><span>Total Diskon</span><strong id="sumDiskon">Rp 0</strong></div>
                            <div class="grand"><span>Harus Dibayar</span><strong id="sumHarusBayar">Rp 0</strong></div>
                        </div>
                    </div>

                    <div class="kasir-card">
                        <div class="kasir-card-header">
                            <div>
                                <h6><i class="fa-solid fa-wallet me-2 text-primary"></i>Pembayaran</h6>
                                <small>Support split payment</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddPayment"><i class="fa-solid fa-plus"></i></button>
                        </div>

                        <div class="px-3 pt-3 mb-2">
                            <label class="form-label">Diterima dari</label>
                            <input type="text" id="dibayarOleh" class="form-control form-control-sm" placeholder="Nama pembayar / keluarga">
                        </div>

                        <div id="paymentList" class="payment-list">
                            <div class="kasir-empty">Pilih pasien terlebih dahulu.</div>
                        </div>

                        <div class="payment-total mt-3">
                            <div><span>Total Dibayar</span><strong id="sumTotalBayar">Rp 0</strong></div>
                            <div><span>Sisa</span><strong id="sumSisa">Rp 0</strong></div>
                            <div><span>Kembali</span><strong id="sumKembali">Rp 0</strong></div>
                        </div>

                        <button type="button" class="btn btn-success w-100 mt-3" id="btnProsesBayarBottom">
                            <i class="fa-solid fa-money-bill-wave me-1"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    window.KASIR_UMUM_DATA = <?= json_encode($kasir_umum_data ?? [], JSON_UNESCAPED_UNICODE); ?>;
    window.KASIR_UMUM_CONFIG = {
        ajaxListUrl: "<?= site_url('KasirUmumController/ajax_list_unpaid'); ?>",
        ajaxLoadBillingUrl: "<?= site_url('KasirUmumController/ajax_load_billing'); ?>",
        ajaxProsesBayarUrl: "<?= site_url('KasirUmumController/ajax_proses_bayar'); ?>",
        printKwitansiUrl: "<?= site_url('KasirUmumController/cetak_kwitansi'); ?>",
        csrfName: "<?= $this->security->get_csrf_token_name(); ?>",
        csrfHash: "<?= $this->security->get_csrf_hash(); ?>"
    };
</script>