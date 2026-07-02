<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Stok Obat</a></li>

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
                                <h4>Daftar Stok Obat</h4>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" action="" class="d-flex align-items-center mb-4 p-3 border rounded shadow-sm bg-light">
                            <div class="me-3">
                                <select name="depo_obat" class="form-control" onchange="this.form.submit()">
                                    <option value="SEMUA">Semua</option>
                                    <?php foreach ($depo_obat as $depo) : ?>
                                        <option value="<?= $depo->gudang_id ?>" <?= isset($_GET['depo_obat']) && $_GET['depo_obat'] == $depo->gudang_id ? 'selected' : '' ?>><?= $depo->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="me-3">
                                <!-- <input type="text" name="search" class="form-control" placeholder="Cari obat..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>"> -->
                            </div>
                            <div class="add-group">
                                <!-- <a href="add-patient.html" class="btn btn-primary add-pluss ms-2"><img src="assets/img/icons/plus.svg" alt=""></a> -->

                                <button type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#modalTambahObat">
                                    <img src="assets/img/icons/plus.svg" alt="">
                                </button>


                            </div>

                            <!-- <div class="col-auto text-end float-end ms-auto download-grp">
                                <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-01.svg" alt=""></a>
                                <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-02.svg" alt=""></a>
                                <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-03.svg" alt=""></a>
                                <a href="javascript:;"><img src="assets/img/icons/pdf-icon-04.svg" alt=""></a>

                            </div> -->
                        </form>



                    </div>

                    <div class="col-md-12">
                        <div class="card card-table show-entire">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="stok-table" class="table border-0 custom-table   mb-0">
                                        <thead>
                                            <tr>
                                                <th>KODE OBAT</th>
                                                <th>NAMA OBAT</th>
                                                <th>SALDO AWAL</th>
                                                <th>MUTASI BELI</th>
                                                <th>MUTASI JUAL</th>
                                                <th>RETUR BELI</th>
                                                <th>RETUR JUAL</th>
                                                <th>ADJUSTMENT</th>

                                                <th>STOK SAAT INI</th>
                                                <th>DEPO</th>
                                                <th>ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stok_list as $stok) : ?>
                                                <tr>
                                                    <td><?= $stok->obat_id ?></td>
                                                    <td><?= $stok->nama_obat ?></td>
                                                    <td><?= formatRibuan($stok->salwal) ?></td>
                                                    <td><?= formatRibuan($stok->mutbl) ?></td>
                                                    <td><?= formatRibuan($stok->mutjl) ?></td>
                                                    <td><?= formatRibuan($stok->retbl) ?></td>
                                                    <td><?= formatRibuan($stok->retjl) ?></td>
                                                    <td><?= formatRibuan($stok->adjust) ?></td>
                                                    <td><strong><?= formatRibuan($stok->stok_komputer) ?></strong></td>
                                                    <td><?= $stok->depo ?></td>


                                                    <td>
                                                        <button class="btn btn-sm btn-warning adjust-stok" data-id="<?= $stok->obat_id ?>" data-nama="<?= $stok->nama_obat ?>" data-gudangid="<?= $stok->gudang_id ?>" data-gudangnm="<?= $stok->depo ?>" data-stok-komputer="<?= $stok->stok_komputer ?>" data-adjust="<?= $stok->adjust ?>">
                                                            Adjustment
                                                        </button>
                                                        <!-- <a href="#" class="btn btn-sm btn-primary edit-obat" data-id="<?= $stok->obat_id ?>">Edit</a>
                                                        <a href="#" class="btn btn-sm btn-danger delete-obat" data-id="<?= $stok->obat_id ?>">Delete</a> -->
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