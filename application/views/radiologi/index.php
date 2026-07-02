<div class="content">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('Dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Worklist Radiologi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="good-morning-blk">
        <div class="row">
            <div class="col-md-7">
                <div class="morning-user">
                    <h2>Worklist Radiologi, <span><?= htmlspecialchars($user['nama'] ?? 'User') ?></span></h2>
                    <p class="mb-0">Lembar kerja radiologi: verifikasi order, unggah hasil, kirim ke dokter, dan monitoring status.</p>
                </div>
            </div>
            <div class="col-md-5 position-blk">
                <div class="morning-img">
                    <img src="<?= base_url(); ?>assets/img/morning-img-01.png" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <!-- KPI -->
        <div class="row g-3 mb-3">
            <?php
            $kpis = [
                ['s' => '0', 't' => 'Baru', 'cls' => 'border-primary'],
                ['s' => '4', 't' => 'Dikerjakan', 'cls' => 'border-warning'],
                ['s' => '2', 't' => 'Ditunda', 'cls' => 'border-info'],
                ['s' => '3', 't' => 'Dibatalkan', 'cls' => 'border-danger'],
                ['s' => '1', 't' => 'Selesai', 'cls' => 'border-success'],
                ['s' => '9', 't' => 'Menunggu Dokter', 'cls' => 'border-secondary'],
            ];
            foreach ($kpis as $k) : ?>
                <div class="col-6 col-md-2">
                    <div class="card shadow-sm border-2 <?= $k['cls'] ?> rounded-4">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-muted"><?= $k['t'] ?></div>
                                    <div class="h4 mb-0" id="kpi_<?= $k['s'] ?>">0</div>
                                </div>
                                <div class="badge bg-light text-dark">RAD</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Toolbar -->
        <div class="card shadow-sm rounded-4 border-0 mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary filter-status active" data-status="0">Baru <span class="badge text-bg-primary ms-1" id="count_0">0</span></button>
                        <button class="btn btn-outline-warning filter-status" data-status="4">Dikerjakan <span class="badge text-bg-warning ms-1" id="count_4">0</span></button>
                        <button class="btn btn-outline-info filter-status" data-status="2">Ditunda <span class="badge text-bg-info ms-1" id="count_2">0</span></button>
                        <button class="btn btn-outline-danger filter-status" data-status="3">Dibatalkan <span class="badge text-bg-danger ms-1" id="count_3">0</span></button>
                        <button class="btn btn-outline-success filter-status" data-status="1">Selesai <span class="badge text-bg-success ms-1" id="count_1">0</span></button>
                        <button class="btn btn-outline-secondary filter-status" data-status="9">Menunggu Dokter <span class="badge text-bg-secondary ms-1" id="count_9">0</span></button>
                    </div>

                    <!-- <div class="d-flex gap-2 align-items-center">

                        <div class="input-group" style="width: 290px;">
                            <span class="input-group-text bg-white">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="date" id="filterTanggalRad" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>














                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                            <input type="text" id="quickSearch" class="form-control" placeholder="Cari: nama/RM/episode/trans_id/catatan...">
                            <button class="btn btn-outline-secondary" id="btnClearSearch" type="button">Reset</button>
                        </div>
                        <button class="btn btn-outline-primary" id="btnRefresh" type="button">
                            <i class="fa fa-rotate me-1"></i> Refresh
                        </button>
                        <button class="btn btn-outline-primary" id="btnAutoRefresh" data-enabled="0" type="button">
                            <i class="fa fa-bolt me-1"></i> Auto
                        </button>
                    </div> -->




                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <label for="filterTanggalRad" class="small text-muted mb-0">Tanggal:</label>
                            <input type="date" id="filterTanggalRad" class="form-control" style="min-width: 180px;" value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="chkSemuaTanggalRad">
                            <label class="form-check-label small" for="chkSemuaTanggalRad">
                                Semua tanggal
                            </label>
                        </div>

                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                            <input type="text" id="quickSearch" class="form-control" placeholder="Cari: nama/RM/episode/trans_id/catatan...">
                            <button class="btn btn-outline-secondary" id="btnClearSearch" type="button">Reset</button>
                        </div>

                        <button class="btn btn-outline-primary" id="btnRefresh" type="button">
                            <i class="fa fa-rotate me-1"></i> Refresh
                        </button>

                        <button class="btn btn-outline-primary" id="btnAutoRefresh" data-enabled="0" type="button">
                            <i class="fa fa-bolt me-1"></i> Auto
                        </button>
                    </div>







                </div>

                <div class="mt-2 d-flex justify-content-between small text-muted">
                    <div id="activeFilterText">Menampilkan: <b>Baru</b></div>
                    <div>Last update: <span id="lastUpdated">-</span> • <span id="tableInfo">0 data</span></div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm rounded-4 border-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="tblRad">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Waktu</th>
                            <th>Pasien</th>
                            <th>Asal</th>
                            <th>Catatan</th>
                            <th>Files</th>
                            <th>Status</th>
                            <th class="text-end pe-3" style="width:320px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail + Upload -->
<div class="modal fade" id="modalRadDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Detail Worklist Radiologi</h5>
                    <div class="small text-muted">Order pemeriksaan & berkas hasil radiologi</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div id="radDetailBody" class="text-muted">Memuat...</div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>