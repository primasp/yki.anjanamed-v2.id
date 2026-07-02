<div class="content">

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Dokter Radiologi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="good-morning-blk mb-3">
        <div class="row">
            <div class="col-md-8">
                <div class="morning-user">
                    <h2>Radiology Worklist, <span><?= $user['nama'] ?? '' ?></span></h2>
                    <p>Dashboard bacaan & validasi hasil radiologi</p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="small text-muted">Terakhir update: <span id="lastUpdatedRad">-</span></div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <!-- FILTER BAR -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body py-2">
                <div class="row g-2 align-items-center">
                    <div class="col-md-auto">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary btn-sm rad-filter" data-status="9">
                                Menunggu Dokter <span class="badge bg-secondary ms-1" id="rad_count_9">0</span>
                            </button>
                            <button class="btn btn-outline-warning btn-sm rad-filter" data-status="4">
                                Sedang Dibaca <span class="badge bg-secondary ms-1" id="rad_count_4">0</span>
                            </button>
                            <button class="btn btn-outline-success btn-sm rad-filter" data-status="1">
                                Selesai <span class="badge bg-secondary ms-1" id="rad_count_1">0</span>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm rad-filter" data-status="">
                                Semua <span class="badge bg-secondary ms-1" id="rad_count_all">0</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" id="radSearch" placeholder="Cari pasien / RM / episode / kesan singkat">
                            <button class="btn btn-outline-secondary" type="button" id="radClearSearch">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-auto text-md-end">
                        <button class="btn btn-outline-primary btn-sm" id="radBtnRefresh">
                            <i class="fa fa-rotate me-1"></i> Refresh
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="radBtnAutoRefresh" data-enabled="0">
                            <i class="fa fa-bolt me-1"></i> Auto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card shadow-sm">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div id="radActiveFilterText">Menampilkan: <b>Menunggu Dokter</b></div>
                <div class="small text-muted" id="radTableInfo">0 data</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0" id="tblRadDoctor">
                        <!-- <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>Pasien</th>
                                <th>Asal</th>
                                <th>Rekanan</th>
                                <th>File</th>
                                <th>Status</th>
                                <th width="170" class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead> -->

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 rad-sortable" data-sort="tanggal">
                                    Tanggal <i class="fa fa-sort ms-1 rad-sort-icon"></i>
                                </th>
                                <th class="rad-sortable" data-sort="pasien">
                                    Pasien <i class="fa fa-sort ms-1 rad-sort-icon"></i>
                                </th>
                                <th class="rad-sortable" data-sort="asal">
                                    Asal <i class="fa fa-sort ms-1 rad-sort-icon"></i>
                                </th>
                                <th class="rad-sortable" data-sort="rekanan">
                                    Rekanan <i class="fa fa-sort ms-1 rad-sort-icon"></i>
                                </th>
                                <th class="rad-sortable" data-sort="file">
                                    File <i class="fa fa-sort ms-1 rad-sort-icon"></i>
                                </th>
                                <th class="rad-sortable" data-sort="status">
                                    Status <i class="fa fa-sort ms-1 rad-sort-icon"></i>
                                </th>
                                <th width="170" class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- MODAL DETAIL + VIEWER + FORM EXPERTISE -->
<div class="modal fade" id="modalRadDrDetail" tabindex="-1" aria-hidden="true">
    <!-- <div class="modal-dialog modal-xl modal-dialog-scrollable"> -->
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pemeriksaan Radiologi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">

                <div id="radDetailBody">
                    <!-- akan diisi via JS (viewer + form) -->
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>