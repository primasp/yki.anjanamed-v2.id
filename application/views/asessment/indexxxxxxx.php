<div class="content">

    <!-- Page Header -->
    <div class="page-header py-3 mb-4 border-bottom">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <ul class="breadcrumb mb-0 fs-6">
                    <li class="breadcrumb-item">
                        <a href="index.html" class="text-primary text-decoration-none">
                            <i class="feather-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <i class="feather-chevron-right text-secondary"></i>
                    </li>
                    <li class="breadcrumb-item active fw-semibold text-dark">Asesmen Pasien</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <?php if ($this->session->flashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="feather-check-circle me-2"></i>
                            <?= $this->session->flashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="feather-alert-triangle me-2"></i>
                            <?= $this->session->flashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>



                    <h4 class="fw-bold mb-4 text-primary">
                        <i class="feather-clipboard me-2 text-secondary"></i> Asesmen Pasien
                    </h4>





                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#tabBelum" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                🕒 Pasien Belum Asesmen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tabSelesai" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                ✅ Pasien Sudah Asesmen
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB BELUM ASESMEN -->
                        <div class="tab-pane show active fade p-3" id="tabBelum">
                            <?php $suffix = 'Belum';
                            $this->load->view('asessment/partials/filter_form', compact('suffix')); ?>

                            <hr class="mt-4 mb-4">

                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle text-nowrap">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>No. RM</th>
                                            <th>Nama Pasien</th>
                                            <th>Poli / Unit</th>
                                            <th>Dokter</th>
                                            <th>Tgl. Daftar</th>
                                            <th>Provider</th>
                                            <th>Kunjungan RS</th>
                                            <th>Kunjungan Poli</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($belum)) : $no = 1; ?>
                                            <?php foreach ($belum as $row) : ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td class="fw-semibold"><?= $row->int_pasien_id ?></td>
                                                    <td><?= htmlspecialchars($row->nama_pas) ?></td>

                                                    <td>
                                                        <span class="fw-semibold text-dark"><?= $row->nama_poli ?></span>
                                                        <?php if (!empty($row->detil_pemeriksaan)) : ?>
                                                            <ul class="mb-0 ps-3 small text-muted">
                                                                <?php foreach ($row->detil_pemeriksaan as $d) : ?>
                                                                    <li><?= htmlspecialchars($d->nama_layan1) ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </td>

                                                    <td><?= htmlspecialchars($row->nama_dr) ?></td>
                                                    <td><?= date('d/m/Y') ?></td>
                                                    <td><?= htmlspecialchars($row->rekanan_id) ?></td>
                                                    <td class="text-center"><?= $row->kunjungan_rs ?></td>
                                                    <td class="text-center"><?= $row->kunjungan_poli ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="feather-clock me-1"></i> Belum Anamnesa
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-primary px-3 btnMulaiAsesmen" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-poli="<?= $row->poli_id ?>" data-kunj="<?= $row->kunjungan_rs ?>">
                                                            <i class="feather-edit-3 me-1"></i> Mulai
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-3">
                                                    Tidak ada pasien belum anamnesa hari ini
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB SUDAH ASESMEN -->
                        <div class="tab-pane fade p-3" id="tabSelesai">
                            <?php $suffix = 'Selesai';
                            $this->load->view('asessment/partials/filter_form', compact('suffix')); ?>

                            <hr class="mt-4 mb-4">

                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle text-nowrap">
                                    <thead class="table-success text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>No. RM</th>
                                            <th>Nama Pasien</th>
                                            <th>Poli / Unit</th>
                                            <th>Dokter</th>
                                            <th>Tgl. Asesmen</th>
                                            <th>Provider</th>
                                            <th>Kunjungan RS</th>
                                            <th>Kunjungan Poli</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($sudah)) : $no = 1; ?>
                                            <?php foreach ($sudah as $row) : ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td class="fw-semibold text-center"><?= $row->int_pasien_id ?></td>
                                                    <td><?= htmlspecialchars($row->nama_pas) ?></td>

                                                    <td>
                                                        <span class="fw-semibold text-dark"><?= $row->nama_poli ?></span>
                                                        <?php if (!empty($row->detil_pemeriksaan)) : ?>
                                                            <!-- <ul class="mb-0 ps-3 small text-muted">
                                                                <?php foreach ($row->detil_pemeriksaan as $d) : ?>
                                                                    <li><?= htmlspecialchars($d->nama_layan1) ?></li>
                                                                <?php endforeach; ?>
                                                            </ul> -->


                                                            <ul class="mb-0 ps-3 small">
                                                                <?php foreach ($row->detil_pemeriksaan as $d) : ?>
                                                                    <li class="d-flex justify-content-between align-items-center mb-1">

                                                                        <!-- Nama layanan -->
                                                                        <span>
                                                                            <?= htmlspecialchars($d->nama_layan1) ?>
                                                                        </span>

                                                                        <!-- Aksi -->
                                                                        <!-- <?php if (!empty($d->is_done)) : ?>
                                                                            <button class="btn btn-sm btn-outline-success btnEditLayanan" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-layanan="<?= $d->kategori_id ?>">
                                                                                <i class="feather-edit"></i> Edit
                                                                            </button>
                                                                        <?php else : ?>
                                                                            <button class="btn btn-sm btn-outline-danger btnAsesmenLayanan" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-layanan="<?= $d->kategori_id ?>">
                                                                                <i class="feather-edit"></i> Isi
                                                                            </button>
                                                                        <?php endif; ?> -->

                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>


                                                        <?php endif; ?>
                                                    </td>

                                                    <td class="text-center"><?= htmlspecialchars($row->nama_dr) ?></td>
                                                    <td class="text-center"><?= date('d/m/Y') ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row->rekanan_id) ?></td>
                                                    <td class="text-center"><?= $row->kunjungan_rs ?></td>
                                                    <td class="text-center"><?= $row->kunjungan_poli ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">
                                                            <i class="feather-check me-1"></i> Sudah Asesmen
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- <button class="btn btn-sm btn-info px-3 btnEditAsesmen" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-poli="<?= $row->poli_id ?>" data-kunj="<?= $row->kunjungan_rs ?>" data-layanan='<?= json_encode($row->detil_pemeriksaan) ?>'>
                                                            <i class="feather-eye me-1"></i> Lihat / Edit
                                                        </button> -->

                                                        <?php
                                                        $kategori = array_column($row->detil_pemeriksaan ?? [], 'kategori_id');
                                                        $multi = in_array('JKL-LAB', $kategori) && in_array('JKL-RAD', $kategori);

                                                        // return var_dump($multi);
                                                        // die;
                                                        ?>
                                                        <?php if ($multi) : ?>

                                                            <!-- MULTI LAYANAN (LAB + RAD) -->
                                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                                <?php foreach ($row->detil_pemeriksaan as $d) : ?>

                                                                    <?php if (!empty($d->is_done)) : ?>
                                                                        <button class="btn btn-sm btn-outline-success btnEditLayanan" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-layanan="<?= $d->kategori_id ?>">
                                                                            <i class="feather-edit"></i> Edit <?= $d->nama_layan1 ?>
                                                                        </button>
                                                                    <?php else : ?>
                                                                        <button class="btn btn-sm btn-outline-danger btnAsesmenLayanan" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-layanan="<?= $d->kategori_id ?>">
                                                                            <i class="feather-edit"></i> Isi <?= $d->nama_layan1 ?>
                                                                        </button>
                                                                    <?php endif; ?>

                                                                <?php endforeach; ?>
                                                            </div>

                                                        <?php else : ?>

                                                            <!-- SINGLE LAYANAN (DEFAULT) -->
                                                            <button class="btn btn-sm btn-info px-3 btnEditAsesmen" data-episode="<?= $row->episode_id ?>" data-pasien="<?= $row->pasien_id ?>" data-poli="<?= $row->poli_id ?>" data-kunj="<?= $row->kunjungan_rs ?>" data-layanan='<?= json_encode($row->detil_pemeriksaan) ?>'>

                                                                <i class="feather-eye me-1"></i> Lihat / Edit
                                                            </button>

                                                        <?php endif; ?>

                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-3">
                                                    Tidak ada pasien sudah anamnesa hari ini
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col -->
    </div>
</div>