<div class="content assessment-page-v2">

    <div class="assessment-hero">
        <div>
            <ul class="breadcrumb assessment-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('Home') ?>"><i class="feather-home me-1"></i> Dashboard</a>
                </li>
                <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                <li class="breadcrumb-item active">Asesmen Pasien</li>
            </ul>

            <h3>Asesmen Pasien</h3>
            <p>
                Alur baru: Perawat hanya mengisi <strong>Pengkajian Awal</strong>.
                Form <strong>Paliatif</strong> dilanjutkan setelah pengkajian awal jika pasien terdaftar pada Poli Paliatif.
            </p>
        </div>

        <div class="assessment-hero-badge">
            <span>YKI</span>
            <strong>Tahap 2</strong>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show assessment-alert" role="alert">
            <i class="feather-check-circle me-2"></i>
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show assessment-alert" role="alert">
            <i class="feather-alert-triangle me-2"></i>
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="assessment-summary-grid">
        <div class="assessment-summary-card">
            <span class="assessment-summary-icon warning"><i class="feather-clock"></i></span>
            <div>
                <small>Belum Asesmen</small>
                <strong><?= !empty($belum) ? count($belum) : 0 ?></strong>
            </div>
        </div>

        <div class="assessment-summary-card">
            <span class="assessment-summary-icon success"><i class="feather-check-circle"></i></span>
            <div>
                <small>Sudah Asesmen</small>
                <strong><?= !empty($sudah) ? count($sudah) : 0 ?></strong>
            </div>
        </div>

        <div class="assessment-summary-card">
            <span class="assessment-summary-icon primary"><i class="feather-clipboard"></i></span>
            <div>
                <small>Jenis Form</small>
                <strong>Kaji Awal</strong>
            </div>
        </div>

        <div class="assessment-summary-card">
            <span class="assessment-summary-icon danger"><i class="fa fa-heartbeat"></i></span>
            <div>
                <small>Lanjutan Khusus</small>
                <strong>Paliatif</strong>
            </div>
        </div>
    </div>

    <div class="assessment-card">
        <div class="assessment-card-header">
            <div>
                <span class="assessment-section-kicker">Worklist</span>
                <h4>Daftar Asesmen Perawat</h4>
                <p>Penunjang Lab/Radiologi tidak lagi ditampilkan pada menu ini.</p>
            </div>
        </div>

        <ul class="nav assessment-tabs" role="tablist">
            <li class="nav-item">
                <a href="#tabBelum" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                    <i class="feather-clock me-1"></i> Pasien Belum Asesmen
                </a>
            </li>
            <li class="nav-item">
                <a href="#tabSelesai" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="feather-check-circle me-1"></i> Pasien Sudah Asesmen
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane show active fade" id="tabBelum">
                <?php $suffix = 'Belum';
                $this->load->view('asessment/partials/filter_form', compact('suffix')); ?>

                <div class="table-responsive assessment-table-wrap">
                    <table class="table assessment-table align-middle text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Poliklinik</th>
                                <th>Dokter</th>
                                <th>Tgl. Daftar</th>
                                <th>Provider</th>
                                <th>Kunj. RS</th>
                                <th>Kunj. Poli</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($belum)) : $no = 1; ?>
                                <?php foreach ($belum as $row) : ?>
                                    <?php $isPaliatif = (($row->poli_id ?? '') === 'POLI0000000001'); ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td class="fw-semibold text-center"><?= htmlspecialchars($row->int_pasien_id, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <div class="assessment-patient-name"><?= htmlspecialchars($row->nama_pas, ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if ($isPaliatif) : ?>
                                                <small class="assessment-note"><i class="fa fa-heartbeat me-1"></i> Lanjut paliatif setelah kaji awal</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($row->nama_poli, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if ($isPaliatif) : ?>
                                                <span class="assessment-mini-badge danger">Paliatif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars($row->nama_dr, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= date('d/m/Y') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row->rekanan_id, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row->kunjungan_poli, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <span class="assessment-badge assessment-badge-warning">
                                                <i class="feather-clock me-1"></i> Belum Anamnesa
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary px-3 btnMulaiAsesmen" data-episode="<?= htmlspecialchars($row->episode_id, ENT_QUOTES, 'UTF-8') ?>" data-pasien="<?= htmlspecialchars($row->pasien_id, ENT_QUOTES, 'UTF-8') ?>" data-poli="<?= htmlspecialchars($row->poli_id, ENT_QUOTES, 'UTF-8') ?>" data-kunj="<?= htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') ?>">
                                                <i class="feather-edit-3 me-1"></i> Mulai
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        Tidak ada pasien belum anamnesa hari ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabSelesai">
                <?php $suffix = 'Selesai';
                $this->load->view('asessment/partials/filter_form', compact('suffix')); ?>

                <div class="table-responsive assessment-table-wrap">
                    <table class="table assessment-table align-middle text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Poliklinik</th>
                                <th>Dokter</th>
                                <th>Tgl. Asesmen</th>
                                <th>Provider</th>
                                <th>Kunj. RS</th>
                                <th>Kunj. Poli</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sudah)) : $no = 1; ?>
                                <?php foreach ($sudah as $row) : ?>
                                    <?php $isPaliatif = (($row->poli_id ?? '') === 'POLI0000000001'); ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td class="fw-semibold text-center"><?= htmlspecialchars($row->int_pasien_id, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <div class="assessment-patient-name"><?= htmlspecialchars($row->nama_pas, ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if ($isPaliatif) : ?>
                                                <small class="assessment-note"><i class="fa fa-heartbeat me-1"></i> Cek/isi form paliatif dari halaman edit</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($row->nama_poli, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if ($isPaliatif) : ?>
                                                <span class="assessment-mini-badge danger">Paliatif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars($row->nama_dr, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= date('d/m/Y') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row->rekanan_id, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row->kunjungan_poli, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <span class="assessment-badge assessment-badge-success">
                                                <i class="feather-check me-1"></i> Sudah Asesmen
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info px-3 btnEditAsesmen" data-episode="<?= htmlspecialchars($row->episode_id, ENT_QUOTES, 'UTF-8') ?>" data-pasien="<?= htmlspecialchars($row->pasien_id, ENT_QUOTES, 'UTF-8') ?>" data-poli="<?= htmlspecialchars($row->poli_id, ENT_QUOTES, 'UTF-8') ?>" data-kunj="<?= htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') ?>">
                                                <i class="feather-eye me-1"></i> Lihat / Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        Tidak ada pasien sudah asesmen hari ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>