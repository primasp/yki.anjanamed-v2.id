<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Obat </a></li>

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
                                <h4>Daftar Obat</h4>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" action="" id="formFilter" class="d-flex align-items-center mb-4 p-3 border rounded shadow-sm bg-light">
                            <div class="me-3">
                                <select name="gol_obat" class="form-control" onchange="this.form.submit()">
                                    <option value="SEMUA">Semua</option>
                                    <?php foreach ($golongan_obat as $gol) : ?>
                                        <option value="<?= $gol->golobat_id ?>" <?= isset($_GET['gol_obat']) && $_GET['gol_obat'] == $gol->golobat_id ? 'selected' : '' ?>><?= $gol->keterangan ?></option>
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
                                    <table id="obat-table" class="table border-0 custom-table   mb-0">
                                        <thead>
                                            <tr>
                                                <th>KODE OBAT</th>
                                                <th>NAMA OBAT</th>
                                                <th>KONVERSI SATUAN</th>
                                                <th>SATUAN JUAL</th>
                                                <th>SATUAN BELI</th>
                                                <th>HARGA HNA</th>
                                                <th>HARGA HNA + PPN</th>
                                                <th>HARGA SATUAN</th>
                                                <th>HARGA SATUAN + PPN</th>
                                                <th>HARGA JUAL</th>
                                                <th>SISA STOK</th>
                                                <th>DEPO</th>
                                                <th>ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($obat_list as $obat) : ?>
                                                <tr>
                                                    <td><?= $obat->obat_id ?></td>
                                                    <td><?= $obat->nama ?></td>
                                                    <!-- <td><?= $obat->konversi_satuan ?></td> -->
                                                    <td><?= formatRibuan($obat->konversi_satuan) ?></td>
                                                    <td><?= $obat->satuan_jual ?></td>
                                                    <td><?= $obat->satuan_beli ?></td>
                                                    <td><?= formatRupiah($obat->harga_hna) ?></td>
                                                    <td><?= formatRupiah($obat->harga_hna_ppn) ?></td>
                                                    <td><?= formatRupiah($obat->harga) ?></td>
                                                    <td><?= formatRupiah($obat->harga_sat_ppn) ?></td>
                                                    <td><?= formatRupiah($obat->harga_jual) ?></td>
                                                    <td><?= formatRibuan($obat->sisa_stok)  ?></td>
                                                    <td><?= $obat->nama_gudang  ?></td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-primary edit-obat" data-id="<?= $obat->obat_id ?>">Edit</a>
                                                        <a href="#" class="btn btn-sm btn-danger delete-obat" data-id="<?= $obat->obat_id ?>">Delete</a>
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








<!-- Modal Tambah Obat -->
<div class="modal fade" id="modalTambahObat" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Obat</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-tambah-obat">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" class="form-control" name="nama_obat" id="nama_obat" required>
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Sediaan</label>
                            <select name="gol_obat" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($sediaan_obat as $sediaan) : ?>
                                    <option value="<?= $sediaan->sediaan_id ?>"><?= $sediaan->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pabrik</label>
                            <select name="pabrik" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($pabrik_obat as $pabrik) : ?>
                                    <option value="<?= $pabrik->pabrik_id ?>"><?= $pabrik->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Route</label>
                            <select name="route" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($route_obat as $route) : ?>
                                    <option value="<?= $route->routeobt_id ?>"><?= $route->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <!-- <div class="col-md-4">
                            <label class="form-label">Harga HNA</label>
                            <input type="text" class="form-control" name="harga_hna">
                        </div> -->
                        <div class="col-md-6">
                            <label class="form-label">Satuan Jual</label>

                            <select name="satuan_jual" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($satuan_obat as $satuan) : ?>
                                    <option value="<?= $satuan->satuan_id ?>"><?= $satuan->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan Beli</label>


                            <select name="satuan_beli" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($satuan_obat as $satuan) : ?>
                                    <option value="<?= $satuan->satuan_id ?>"><?= $satuan->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Konversi Satuan</label>
                            <input type="number" class="form-control" name="kon_satuan" id="kon_satuan" required>
                        </div>
                    </div>


                    <div class="row mt-3">

                        <div class="col-md-6">
                            <label class="form-label">Harga HNA (Satuan Besar)</label>
                            <input type="text" class="form-control harga-format" name="hna_besar" id="hna_besar" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Harga HNA PPN (Satuan Besar)</label>
                            <input type="text" class="form-control harga-format" name="hna_ppn_besar" id="hna_ppn_besar" readonly>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <!-- <div class="col-md-2">
                            <label class="form-label">Margin (%)</label>
                            <input type="number" class="form-control" name="margin" id="margin" value="20" disabled>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="margin_checkbox" checked>
                                <label class="form-check-label">Gunakan Margin</label>
                            </div>
                        </div> -->

                        <div class="col-md-4">
                            <label class="form-label">Harga Jual Satuan</label>
                            <input type="text" class="form-control harga-format" name="jual_sat" id="jual_sat" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Harga Jual Satuan HNA PPN</label>
                            <input type="text" class="form-control readonly" name="jual_sat_hna_ppn" id="jual_sat_hna_ppn" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Harga Satuan Jual + PPN + Margin</label>
                            <input type="text" class="form-control harga-format" name="harga_final" id="harga_final" readonly>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="harga_final_checkbox" name="harga_final_checkbox" checked>
                                <label class="form-check-label">Gunakan Margin 20%</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Kategori Obat</label>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="obat_fornas" value="1"> OBAT FORNAS
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="obat_produksi" value="1"> OBAT PRODUKSI
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="alkes" value="1"> ALKES
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="obat_high_alert" value="1"> OBAT HIGH ALERT
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tidak_bpjs" value="1"> TIDAK UNTUK PASIEN BPJS
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sembunyikan_obat" value="1"> <span class="text-danger">SEMBUNYIKAN OBAT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Golongan Obat</label>
                            <select name="gol_obat" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($golongan_obat as $gol) : ?>
                                    <option value="<?= $gol->golobat_id ?>"><?= $gol->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kandungan Generik</label>
                            <select name="generik_obat" class="form-control">
                                <option value="">- Harap Pilih -</option>
                                <?php foreach ($generik_obat as $generik) : ?>
                                    <option value="<?= $generik->generik_id ?>"><?= $generik->keterangan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Batasan Fornas</label>
                            <textarea class="form-control" name="batasan_fornas"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Obat E-Catalogue</label>
                            <input type="text" class="form-control" name="ecatalogue">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Registrasi</label>
                            <input type="text" class="form-control" name="nomor_registrasi">
                        </div>
                    </div>

                    <div class="row mt-3">

                        <!-- <div class="col-md-4">
                            <label class="form-label">Kategori FDA</label>
                            <input type="text" class="form-control" name="kategori_fda">
                        </div> -->
                        <!-- <div class="col-md-4">
                            <label class="form-label">Indeks Generik</label>
                            <input type="text" class="form-control" name="indeks_generik">
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-simpan-obt" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Obat -->
<div class="modal fade" id="modalEditObat" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Obat</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-obat">
                    <input type="hidden" name="obat_id" id="edit_obat_id">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" class="form-control" name="nama_obat" id="edit_nama_obat" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Konversi Satuan</label>
                            <input type="number" class="form-control" name="edit_kon_satuan" id="edit_kon_satuan" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga HNA</label>
                            <input type="text" class="form-control harga-format" name="edit_hna_besar" id="edit_hna_besar" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga HNA + PPN</label>
                            <input type="text" class="form-control harga-format" name="edit_hna_ppn_besar" id="edit_hna_ppn_besar" readonly>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control harga-format" name="edit_jual_sat" id="edit_jual_sat" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Satuan + PPN</label>
                            <input type="text" class="form-control harga-format" name="edit_jual_sat_hna_ppn" id="edit_jual_sat_hna_ppn" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Satuan Jual + PPN + Margin</label>
                            <input type="text" class="form-control harga-format" name="harga_edit_final" id="harga_edit_final" readonly>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="harga_edit_final_checkbox" name="harga_edit_final_checkbox" checked>
                                <label class="form-check-label">Gunakan Margin 20%</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-update-obt" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>