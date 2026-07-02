<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Tindakan </a></li>

                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="row">
                    <div class="mailview-header comman-space-flex">
                        <div class="sender-info comman-flex">
                            <div class="send-user send-user-name">
                                <h4>Daftar Tindakan</h4>





                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" action="" id="formFilter" class="d-flex align-items-center mb-4 p-3 border rounded shadow-sm bg-light">
                            <div class="me-3">
                                <select name="gol_tindakan" class="form-control" onchange="this.form.submit()">
                                    <option value="SEMUA">Semua</option>
                                    <?php foreach ($golongan_tindakan as $gol) : ?>
                                        <option value="<?= $gol->goltindakan_id ?>" <?= isset($_GET['gol_tindakan']) && $_GET['gol_tindakan'] == $gol->goltindakan_id ? 'selected' : '' ?>><?= $gol->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="me-3">

                            </div>
                            <div class="add-group">
                                <button type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#modalTambahTindakan">
                                    <img src="assets/img/icons/plus.svg" alt="">
                                </button>


                            </div>


                        </form>



                    </div>

                    <div class="col-md-12">
                        <div class="card card-table show-entire">
                            <div class="card-body">
                                <div class="table-responsive">

                                    <table id="tindakan-table" class="table table-hover align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width:15%">Kode Tindakan</th>
                                                <th style="width:25%">Nama Tindakan</th>
                                                <th style="width:25%">Nama Layan</th>
                                                <th style="width:15%">Jenis</th>
                                                <th style="width:10%" class="text-end">Harga</th>
                                                <th style="width:10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tindakan_list as $tindakan) : ?>
                                                <tr>

                                                    <td class="fw-semibold">
                                                        <?= $tindakan->layan_id ?>
                                                    </td>

                                                    <td>
                                                        <?= $tindakan->nama_layan1 ?>
                                                    </td>

                                                    <td class="text-muted">
                                                        <?= $tindakan->nama_layan2 ?>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            <?= $tindakan->kategori_id ?>
                                                        </span>
                                                    </td>

                                                    <td class="text-end fw-bold">
                                                        <?= formatRupiah($tindakan->harga) ?>
                                                    </td>

                                                    <td class="text-center">

                                                        <div class="btn-group btn-group-sm">

                                                            <button class="btn btn-outline-primary edit-tindakan" data-id="<?= $tindakan->layan_id ?>" title="Edit">

                                                                <i class="fas fa-edit"></i>
                                                            </button>

                                                            <button class="btn btn-outline-danger delete-tindakan" data-id="<?= $tindakan->layan_id ?>" title="Hapus">

                                                                <i class="fas fa-trash"></i>
                                                            </button>

                                                        </div>

                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalTambahTindakan">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Tindakan</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="form-tambah-tindakan">

                    <div class="mb-3">
                        <label>Nama Tindakan</label>
                        <input type="text" name="nama_layan1" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Nama Layan</label>
                        <input type="text" name="nama_layan2" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Kategori</label>
                        <input type="text" name="kategori_id" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="text" name="harga" id="harga_tindakan" class="form-control text-end">
                    </div>

                </form>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                <button class="btn btn-primary" id="btn-simpan-tindakan">
                    Simpan
                </button>

            </div>

        </div>
    </div>
</div>



<div class="modal fade" id="modalEditTindakan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Edit Tindakan
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="form-edit-tindakan">

                    <input type="hidden" id="edit_layan_id" name="layan_id">

                    <div class="mb-3">
                        <label>Nama Tindakan</label>
                        <input type="text" class="form-control" id="edit_nama_layan1" name="nama_layan1">
                    </div>

                    <div class="mb-3">
                        <label>Nama Layan</label>
                        <input type="text" class="form-control" id="edit_nama_layan2" name="nama_layan2">
                    </div>

                    <div class="mb-3">
                        <label>Kategori</label>
                        <input type="text" class="form-control" id="edit_kategori_id" name="kategori_id">
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="text" class="form-control harga-format text-end" id="edit_harga" name="harga">
                    </div>

                </form>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button class="btn btn-primary" id="btn-update-tindakan">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>

            </div>

        </div>
    </div>
</div>