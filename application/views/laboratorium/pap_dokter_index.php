<div class="content">

    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <ul class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= $title ?? 'Form PAP Dokter Laboratorium' ?></li>
                </ul>
                <h4 class="mb-0 fw-semibold">Form PAP Dokter Laboratorium</h4>
                <div class="text-muted small">
                    Daftar pasien dengan hasil PAP yang sudah dikerjakan analis dan siap ACC dokter.
                </div>
            </div>
            <div class="col-sm-4 text-sm-end mt-2 mt-sm-0">
                <div class="d-inline-flex gap-2">
                    <button id="btnRefreshDokter" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-rotate-right me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE LIST -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="small text-muted">
                            Menampilkan worklist dengan status <b>Menunggu Dokter (9)</b> atau <b>Selesai (1)</b>,
                            dan form PAP sudah dikirim analis.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa fa-magnifying-glass"></i></span>
                            <input type="text" id="dokterSearch" class="form-control" placeholder="Cari: sampel / episode / pasien">
                            <button class="btn btn-outline-secondary" id="dokterClearSearch" type="button">Clear</button>
                        </div>
                    </div>
                </div>
            </div>


            <ul class="nav nav-pills mb-3" id="papDokterTabs">
                <li class="nav-item">
                    <button class="nav-link active js-dokter-tab" type="button" data-tab="waiting">
                        <i class="fa fa-hourglass-half me-1"></i> Menunggu Dokter
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link js-dokter-tab" type="button" data-tab="final">
                        <i class="fa fa-circle-check me-1"></i> Sudah Final Dokter
                    </button>
                </li>
            </ul>

            <div class="table-responsive">
                <table id="tblPapDokter" class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 dokter-sortable" data-sort="tanggal">Tanggal <i class="fa fa-sort ms-1"></i></th>
                            <th class="dokter-sortable" data-sort="no_sitologi">No. Sitologi <i class="fa fa-sort ms-1"></i></th>
                            <th class="dokter-sortable" data-sort="nama_pasien">Nama Pasien <i class="fa fa-sort ms-1"></i></th>
                            <th>Pasien</th>
                            <th class="dokter-sortable" data-sort="status">Status Worklist <i class="fa fa-sort ms-1"></i></th>
                            <th class="dokter-sortable" data-sort="status_dokter">Status Dokter <i class="fa fa-sort ms-1"></i></th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- diisi via JS -->
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex justify-content-between align-items-center small text-muted border-top">
                <div id="dokterTableInfo">0 data</div>
                <div class="d-flex gap-2 align-items-center">
                    <span>Update terakhir:</span>
                    <span class="badge text-bg-light border" id="dokterLastUpdated">-</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL FORM PAP -->
<div class="modal fade" id="modalPapDokter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Form PAP Patologi Anatomik</h5>
                    <div class="small text-muted" id="papDokterSubtitle">-</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="border rounded-3 bg-light p-3 mb-3 small">
                    <div><span class="text-muted">Crated Last:</span> <span id="papCreatedLast"></span></div>
                </div>
                <div id="papDokterBody">Memuat...</div>
            </div>
            <div class="modal-footer">
                <div class="me-auto small text-muted" id="papDokterStatusInfo"></div>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-primary" id="btnPapDokterSaveDraft">
                    <i class="fa fa-save me-1"></i> Simpan Draft
                </button>
                <button type="button" class="btn btn-success" id="btnPapDokterSubmit">
                    <i class="fa fa-check me-1"></i> ACC & Kunci
                </button>
            </div>
        </div>
    </div>
</div>