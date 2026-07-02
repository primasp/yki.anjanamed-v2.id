<div class="content">

    <!-- Page Header -->
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <ul class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= $title ?? 'Worklist Laboratorium' ?></li>
                </ul>
                <h4 class="mb-0 fw-semibold">Worklist Laboratorium</h4>
                <div class="text-muted small">Lembar kerja pemeriksaan lab • filter, kerjakan, cetak label, dan update status</div>
            </div>
            <div class="col-sm-4 text-sm-end mt-2 mt-sm-0">
                <div class="d-inline-flex gap-2">
                    <button id="btnRefresh" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-rotate-right me-1"></i> Refresh
                    </button>
                    <button id="btnAutoRefresh" class="btn btn-outline-primary btn-sm" data-enabled="0">
                        <i class="fa fa-bolt me-1"></i> Auto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Greeting Card (optional, keep your style but make it lighter) -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <div class="text-muted small">Selamat datang</div>
                <div class="h5 mb-0">Petugas: <span class="fw-semibold"><?= $user['nama'] ?? '-' ?></span></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill text-bg-light border" id="nowClock">--:--</span>
                <span class="badge rounded-pill text-bg-light border">Bootstrap v5.3.2</span>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="small text-muted">Baru</div>
                    <div class="h4 mb-0 fw-bold" id="kpi_0">0</div>
                    <div class="small text-muted mt-1">Status: 0</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="small text-muted">Dikerjakan</div>
                    <div class="h4 mb-0 fw-bold" id="kpi_4">0</div>
                    <div class="small text-muted mt-1">Status: 4</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="small text-muted">Menunggu Dokter</div>
                    <div class="h4 mb-0 fw-bold" id="kpi_9">0</div>
                    <div class="small text-muted mt-1">Status: 9</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="small text-muted">Ditunda</div>
                    <div class="h4 mb-0 fw-bold" id="kpi_2">0</div>
                    <div class="small text-muted mt-1">Status: 2</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="small text-muted">Dibatalkan</div>
                    <div class="h4 mb-0 fw-bold" id="kpi_3">0</div>
                    <div class="small text-muted mt-1">Status: 3</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="small text-muted">Selesai</div>
                    <div class="h4 mb-0 fw-bold" id="kpi_1">0</div>
                    <div class="small text-muted mt-1">Status: 1</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters + Search -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="text-muted small me-2">Filter Status:</div>

                        <div class="btn-group" role="group" aria-label="status-filter">
                            <button class="btn btn-sm btn-outline-primary filter-status active" data-status="0">
                                Baru <span class="badge text-bg-primary ms-1" id="count_0">0</span>
                            </button>
                            <button class="btn btn-sm btn-outline-warning filter-status" data-status="4">
                                Dikerjakan <span class="badge text-bg-warning ms-1" id="count_4">0</span>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary filter-status" data-status="9">
                                Dokter belum selesai <span class="badge text-bg-secondary ms-1" id="count_9">0</span>
                            </button>
                            <button class="btn btn-sm btn-outline-info filter-status" data-status="2">
                                Ditunda <span class="badge text-bg-info ms-1" id="count_2">0</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger filter-status" data-status="3">
                                Dibatalkan <span class="badge text-bg-danger ms-1" id="count_3">0</span>
                            </button>
                            <!-- <button class="btn btn-sm btn-outline-success filter-status" data-status="1">
                                Selesai <span class="badge text-bg-success ms-1" id="count_1">0</span>
                            </button> -->
                            <button class="btn btn-sm btn-outline-success filter-status" data-status="1">
                                Selesai / Siap Cetak <span class="badge text-bg-success ms-1" id="count_1">0</span>
                            </button>
                        </div>

                        <div class="ms-2 small text-muted" id="activeFilterText">Menampilkan: <b>Baru</b></div>
                    </div>
                </div>

                <div class="col-lg-3">

                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fa fa-magnifying-glass"></i></span>
                        <input type="text" id="quickSearch" class="form-control" placeholder="Cari: sampel id / nama / rm / episode...">
                        <button class="btn btn-outline-secondary" id="btnClearSearch" type="button">Clear</button>
                    </div>
                </div>

                <div class="col-lg-1">
                    <select id="pageLimit" class="form-select form-select-sm" style="width:90px;">

                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="999999">All</option>

                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tblWorklist" class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light position-sticky top-0" style="z-index: 1;">
                        <tr>
                            <th class="ps-3 sortable" data-sort="created_date">
                                Waktu <i class="fa fa-sort ms-1"></i>
                            </th>

                            <th class="sortable" data-sort="no_sitologi">
                                No. Sitologi <i class="fa fa-sort ms-1"></i>
                            </th>

                            <th class="sortable" data-sort="nama_pasien">
                                Pasien <i class="fa fa-sort ms-1"></i>
                            </th>

                            <th class="sortable" data-sort="nama_poli">
                                Asal <i class="fa fa-sort ms-1"></i>
                            </th>

                            <th class="sortable" data-sort="rekanan_nama">
                                Rekanan <i class="fa fa-sort ms-1"></i>
                            </th>

                            <th class="sortable" data-sort="status">
                                Status <i class="fa fa-sort ms-1"></i>
                            </th>
                            <th class="text-end pe-3" style="min-width: 320px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- diisi via JS -->
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex justify-content-between align-items-center small text-muted border-top">
                <div id="tableInfo">
                    0 data
                </div>
                <div class="small text-muted" id="tablePageInfo">
                    -
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span>Update terakhir:</span>
                    <span class="badge text-bg-light border" id="lastUpdated">-</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Detail Worklist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="detailBody" class="small">Memuat...</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Label -->
<div class="modal fade" id="modalLabelxxx" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Cetak Label Sampel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border small mb-3">
                    Pastikan printer label terhubung. Label akan menggunakan <b>sampel_id</b> pada worklist.
                </div>
                <div id="labelPreview" class="border rounded p-3 bg-white">
                    <div class="fw-semibold">Sampel ID: <span id="lblSample">-</span></div>
                    <div>Pasien: <span id="lblPasien">-</span></div>
                    <div>RM/Episode: <span id="lblRmEpisode">-</span></div>
                    <div>Tanggal: <span id="lblTgl">-</span></div>
                    <div class="mt-2 small text-muted">*Preview sederhana (nanti bisa upgrade ke barcode/QR)</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-primary" id="btnPrintLabel">
                    <i class="fa fa-print me-1"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>




<!-- Modal Cetak Label -->
<!-- Modal Cetak Label -->
<div class="modal fade" id="modalLabel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Cetak Label Sampel (40×20 mm)</h5>
                    <div class="small text-muted">Pilih jumlah cetak per pemeriksaan</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <!-- Preview -->
                    <div class="col-md-5">
                        <div class="border rounded-3 p-3 bg-white">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="border rounded-3 p-1" style="width:96px;height:96px;display:flex;align-items:center;justify-content:center;">
                                    <img id="lblQr" alt="QR" style="width:88px;height:88px;object-fit:contain;">
                                </div>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="fw-bold text-truncate" id="lblSample">-</div>
                                    <div class="small text-muted text-truncate">RM: <span id="lblNoRm">-</span></div>
                                    <div class="small text-truncate">Tes: <span id="lblTest">-</span></div>
                                    <div class="small text-muted text-truncate">Ambil: <span id="lblAmbil">-</span></div>
                                </div>
                            </div>

                            <div class="mt-2 small text-muted">
                                *Label print: QR + sampel_id + nama pemeriksaan (+RM opsional)
                            </div>
                        </div>
                    </div>

                    <!-- List pemeriksaan -->
                    <div class="col-md-7">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Daftar Pemeriksaan</div>
                                <button type="button" class="btn btn-sm btn-primary" id="btnPrintAll">
                                    <i class="fa fa-print me-1"></i> Cetak Semua
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pemeriksaan</th>
                                            <th style="width:110px;">Jumlah</th>
                                            <th style="width:120px;" class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="labelTestList">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Memuat...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="small text-muted mt-2">
                                Tips: isi jumlah per tes (misal 2) lalu klik “Cetak” per baris, atau “Cetak Semua”.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal Asesmen Hasil Pemeriksaan Lab (Admin/Analis) -->
<div class="modal fade" id="modalPapForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Asesmen Hasil Pemeriksaan Lab</h5>
                    <div class="small text-muted">
                        Form hasil diisi oleh Admin/Analis, struktur diambil dari master form.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">

                <div class="border rounded-3 bg-light p-3 mb-3 small">
                    <!-- Header + Tombol -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold text-secondary">
                            Informasi Pasien
                        </div>

                        <!-- <button type="button" class="btn btn-outline-primary btn-sm" onclick="openPengkajianLab()">
                            <i class="fa fa-file-medical"></i> Lihat Pengkajian Lab
                        </button> -->
                        <button type="button" class="btn btn-sm btn-primary px-3 btnLihatPengkajian" id="btnLihatPengkajian" data-episode="" data-pasienid="" data-poliid="">
                            <i class="feather-eye me-1"></i> Lihat Pengkajian
                        </button>
                    </div>
                    <div class="row g-3">
                        <!-- Nama Pasien -->
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Nama Pasien</label>
                            <div class="fw-semibold">
                                <span id="papNamaPasien">-</span>
                                <span class="text-muted">(No.MR : <span id="papNoRm">-</span>)</span>
                            </div>
                        </div>

                        <!-- Nama Pasangan -->
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Nama Pasangan</label>
                            <input type="text" id="papNamaPasangan" name="nama_pasangan" class="form-control form-control-sm" placeholder="Isi nama pasangan bila ada">
                        </div>

                        <!-- Alamat -->
                        <div class="col-md-5">
                            <label class="form-label text-muted mb-1">Alamat</label>
                            <input type="text" id="papAlamat" name="alamat" class="form-control form-control-sm" placeholder="Alamat pasien">
                        </div>

                        <!-- Umur -->
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Umur</label>
                            <input type="text" id="papUmur" name="umur" class="form-control form-control-sm" placeholder="Umur pasien" disabled>
                        </div>

                        <!-- Episode -->
                        <!-- <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Episode</label>
                            <div class="form-control form-control-sm bg-white">
                                <span id="papEpisode">-</span>
                            </div>
                        </div> -->

                        <!-- Sampel -->
                        <!-- <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Sampel ID</label>
                            <div class="form-control form-control-sm bg-white">
                                <span id="papSampelId">-</span>
                            </div>
                        </div> -->

                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Nomor Sitologi</label>
                            <div class="form-control form-control-sm bg-white">
                                <span id="papSitologiNo">-</span>
                            </div>
                        </div>

                        <div class="col-md-4"></div>

                        <!-- Tanggal -->
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Tanggal kedatangan</label>
                            <div class="form-control form-control-sm bg-white">
                                <span id="papTanggal">-</span>
                            </div>
                        </div>

                        <!-- Tanggal Kerjakan-->
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Tanggal Dikerjakan</label>
                            <input type="text" id="papSitologiTgl" name="SitologiTgl" class="form-control form-control-sm" placeholder="Tgl Kerjakan" disabled>
                        </div>


                        <!-- Tanggal Kerjakan-->
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Tanggal Skrinning</label>
                            <!-- <input type="text" id="papSkriningTgl" name="SkriningTgl" class="form-control form-control-sm" placeholder="Tgl Skrining" disabled> -->
                            <input type="datetime-local" id="papSkriningTgl" name="SkriningTgl" class="form-control form-control-sm">
                        </div>
                    </div>



                </div>


                <form id="papForm">
                    <input type="hidden" name="worklist_id" id="papWorklistId">
                    <div id="papFormBody">

                        <div class="text-center text-muted py-3 small">
                            Silakan pilih pasien dan klik "Isi Hasil" pada worklist.
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="me-auto small text-muted" id="papFormStatusText">

                </div>
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-outline-primary" type="button" id="btnPapSaveDraft">
                    <i class="fa fa-save me-1"></i> Simpan Draft
                </button>
                <button class="btn btn-primary" type="button" id="btnPapSendToDoctor">
                    <i class="fa fa-paper-plane me-1"></i> Kirim ke Dokter
                </button>
            </div>
        </div>
    </div>
</div>







<style>
    /* .kpi-card {
        border-radius: 1rem;
    }

    .table thead th {
        font-weight: 600;
    }

    .badge {
        font-weight: 600;
    }

    .btn-group .btn.active {
        box-shadow: inset 0 0 0 9999px rgba(13, 110, 253, .08);
    }

    .mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    } */
</style>

<style>
    /* Preview label (tampilan di layar). Saat print akan pakai ukuran mm */
    .label-40x20 {
        width: 240px;
        /* preview-only */
        height: 120px;
        /* preview-only */
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px;
        display: flex;
        gap: 8px;
        align-items: stretch;
    }

    .label-40x20 .qr {
        width: 92px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
    }

    .label-40x20 .qr img {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }

    .label-40x20 .info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }

    .label-40x20 .sid {
        font-weight: 800;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 14px;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .label-40x20 .pname {
        font-weight: 700;
        font-size: 12px;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .label-40x20 .meta {
        font-size: 10px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .label-40x20 .meta .dot {
        margin: 0 4px;
    }
</style>