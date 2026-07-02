<div class="content">

    <!-- Loading Overlay -->
    <div id="page-loading">
        <div class="loading-box">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Memuat data pasien...</p>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Pasien </a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">

                <div class="row">
                    <div class="col-md-12">
                        <div class="page-table-header mb-2">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="doctor-table-blk">
                                        <h3>Daftar Pasien</h3>
                                        <div class="doctor-search-blk">
                                            <div class="top-nav-search table-search-blk">
                                            </div>

                                            <div class="add-group">
                                                <button type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#modalTambahPasien">
                                                    <img src="assets/img/icons/plus.svg" alt="">
                                                </button>
                                                <!-- <a href="javascript:;" class="btn btn-primary doctor-refresh ms-2"><img src="assets/img/icons/re-fresh.svg" alt=""></a> -->
                                                <a href="javascript:;" class="btn btn-primary pasien-refresh ms-2" title="Refresh Data Pasien">
                                                    <img src="assets/img/icons/re-fresh.svg" alt="">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-01.svg" alt=""></a>
                                    <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-02.svg" alt=""></a>
                                    <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-03.svg" alt=""></a>
                                    <a href="javascript:;"><img src="assets/img/icons/pdf-icon-04.svg" alt=""></a>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12">
                        <div class="card card-table show-entire">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="pasienTable" class="table border-0 custom-table comman-table datatable mb-0">

                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check check-tables">
                                                        <input class="form-check-input" type="checkbox" value="something">
                                                    </div>
                                                </th>
                                                <th>NAMA PASIEN</th>
                                                <th>TGL LAHIR</th>
                                                <th>NO. IDENTITAS</th>
                                                <th>PENJAMIN</th>

                                                <th class="sticky-col">ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>

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




















<!-- Center modal content -->
<div class="modal fade" id="modalTambahPasien" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Tambah Pasien</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Isi modal -->
                <form id="form-tambah-pasien">
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipePasien" id="umum" value="umum" checked>
                            <label class="form-check-label">Umum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipePasien" id="bpjs" value="bpjs" disabled>
                            <label class="form-check-label">BPJS</label>
                        </div>
                    </div>
                    <div class="mb-3" id="input-bpjs" style="display: none;">
                        <label class="form-label">Nomor BPJS / Nomor KTP</label>
                        <input type="text" class="form-control" id="noBpjs" name="noBpjs" placeholder="Masukkan Nomor BPJS / Nomor KTP">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="button" class="btn btn-primary" id="actionButton">Tambah Pasien</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- Modal Detail Pasien -->
<div class="modal fade" id="modalDetailPasien" tabindex="-1" aria-labelledby="modalDetailPasienLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailPasienLabel">Detail Pasien BPJS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nama:</strong> <span id="modal-nama">-</span></p>
                <p><strong>No Kartu BPJS:</strong> <span id="modal-noKartu">-</span></p>
                <p><strong>No KTP:</strong> <span id="modal-noKTP">-</span></p>
                <p><strong>Status Kepesertaan:</strong> <span id="modal-ketAktif">-</span></p>
                <p><strong>Jenis Peserta:</strong> <span id="modal-jnsPeserta">-</span></p>
                <p><strong>Tanggal Lahir:</strong> <span id="modal-tglLahir">-</span></p>
                <p><strong>Jenis Kelamin:</strong> <span id="modal-sex">-</span></p>
                <p><strong>Faskes:</strong> <span id="modal-provider">-</span></p>
                <p><strong>No HP:</strong> <span id="modal-noHp">-</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="btnTambahPasien" class="btn btn-primary">Tambah Pasien</button>

            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalEditPasien" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Pasien</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-pasien">
                    <input type="hidden" name="edit_pasien_id" id="edit_pasien_id">

                    <div class="card-box">
                        <h3 class="card-title">Data Identitas Utama</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="profile-img-wrap">
                                    <img class="inline-block" id="edit_foto_pasien" src="assets/img/user.jpg" alt="user">
                                    <div class="fileupload btn">
                                        <span class="btn-text">edit</span>
                                        <input class="upload" type="file" name="edit_foto">
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" id="edit_nama" name="edit_nama" class="form-control floating" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control floating" id="edit_no_kakel" name="edit_no_kakel">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Nomor Induk Kependudukan (KTP) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control floating" id="edit_nik" name="edit_nik" maxlength="16" pattern="\d*" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Tempat Lahir</label>
                                                <input type="text" class="form-control floating" id="edit_tempat_lahir" name="edit_tempat_lahir">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                                <input class="form-control floating datetimepicker" id="edit_tgl_lahir" name="edit_tgl_lahir" placeholder="dd/mm/yyyy" type="text" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Jenis Kelamin<span class="text-danger">*</span></label>
                                                <select id="edit_sex_id" name="edit_sex_id" class="form-control select2" required>
                                                    <option value="L">Laki-Laki</option>
                                                    <option value="P">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Nama Suami / Istri </label>
                                                <input type="text" class="form-control floating" id="edit_nama_pasangan" name="edit_nama_pasangan">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Email</label>
                                                <input type="email" class="form-control floating" id="edit_email" name="edit_email">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Nama Ibu Kandung</label>
                                                <input type="text" class="form-control floating" id="edit_nama_ibukandung" name="edit_nama_ibukandung">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Status</label>
                                                <select id="edit_marital_status_id" name="edit_marital_status_id" class="form-control select2">
                                                    <option value="">Pilih Status</option>
                                                    <?php foreach ($status_kawin as $row) : ?>
                                                        <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Golongan Darah</label>
                                                <select id="edit_pas_goldar_id" name="edit_pas_goldar_id" class="form-control select2">
                                                    <option value="">-- Pilih Golongan Darah --</option>
                                                    <?php foreach ($goldar as $row) : ?>
                                                        <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>


                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">No Handphone <span style="color: red">*</span></label>
                                                <input type="number" class="form-control floating numeric" id="edit_no_selular" name="edit_no_selular" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">No. Rekam Medis Lama</label>
                                                <input type="text" class="form-control floating" id="edit_rm_lama" name="edit_rm_lama">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </div> -->

                        <!-- <div class="card-box"> -->
                        <hr>
                        <h4 class="card-title ">Alamat KTP</h4>
                        <hr class="pb-3">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Propinsi <span style="color: red">*</span></label>
                                    <select id="edit_propinsi_id" name="edit_propinsi_id" class="form-small js-example-basic-single select2" child-id="edit_kabupaten_id" jenis-id="1" empty-label="Propinsi" onchange="dropdownEditKota(this.value, 'edit_kabupaten_id')" required>
                                        <option value="">-- Pilih Propinsi --</option>
                                        <?php foreach ($propinsi as $row) : ?>
                                            <option value="<?= $row['kkk_id'] ?>"><?= $row['keterangan'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kabupaten / Kota <span style="color: red">*</span></label>
                                    <select name="edit_kabupaten_id" id="edit_kabupaten_id" class="form-small js-example-basic-single select2" child-id="edit_kabupaten_id" jenis-id="2" empty-label="Kabupaten" onchange="dropdownEditKecamatan(this.value, 'edit_kecamatan_id')" required>
                                        <option value="">-- Pilih Kabupaten --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kecamatan <span style="color: red">*</span></label>
                                    <select name="edit_kecamatan_id" id="edit_kecamatan_id" class="form-small js-example-basic-single select2" child-id="edit_kelurahan_id" jenis-id="3" empty-label="Kecamatan" onchange="dropdownEditKelurahan(this.value, 'edit_kelurahan_id')" required>
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kelurahan <span style="color: red">*</span></label>
                                    <select name="edit_kelurahan_id" id="edit_kelurahan_id" class="form-small js-example-basic-single select2" jenis-id="4" empty-label="Kelurahan" required>
                                        <option value="">-- Pilih Kelurahan --</option>
                                    </select>
                                </div>
                            </div>








                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Alamat <span style="color: red">*</span></label>
                                    <input type="text" class="form-control floating" id="edit_alamat" name="edit_alamat" required>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="input-block local-forms">
                                    <label class="focus-label">RT <span style="color: red">*</span></label>
                                    <input type="number" class="form-control floating" id="edit_rt" name="edit_rt" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-block local-forms">
                                    <label class="focus-label">RW <span style="color: red">*</span></label>
                                    <input type="number" class="form-control floating" id="edit_rw" name="edit_rw" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Kode Pos</label>
                                    <input type="number" class="form-control floating" id="edit_kodepos" name="edit_kodepos">
                                </div>
                            </div>













                        </div>

                        <hr>
                        <h3 class="card-title">Alamat Domisili</h3>
                        <hr class="pb-3">

                        <fieldset id="fieldset-alamat-domisili">
                            <div class="row">
                                <div class="col-12">
                                    <div class="custom-checkbox mb-3">
                                        <input type="checkbox" id="alamat-sama">
                                        <label>Alamat domisili sama dengan alamat KTP</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Propinsi</label>
                                        <!-- <select name="dms_propinsi_id" id="dms_propinsi_id" class="form-control select2" onchange="dropdownEditKota(this.value, 'dms_kabupaten_id')">
                                            <option value="">-- Pilih Propinsi --</option>
                                        </select> -->


                                        <select name="dms_propinsi_id2" id="dms_propinsi_id2" class="form-control select2">
                                            <option value="">-- Pilih Propinsi --</option>
                                            <?php foreach ($propinsi as $row) : ?>
                                                <option value="<?= $row['kkk_id'] ?>"><?= $row['keterangan'] ?></option>
                                            <?php endforeach; ?>
                                        </select>





                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Kabupaten/Kota</label>
                                        <select name="dms_kabupaten_id2" id="dms_kabupaten_id2" class="form-control select2" onchange="dropdownEditKecamatan(this.value, 'dms_kecamatan_id')">
                                            <option value="">-- Pilih Kabupaten --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Kecamatan</label>
                                        <select name="dms_kecamatan_id" id="dms_kecamatan_id" class="form-control select2" onchange="dropdownEditKelurahan(this.value, 'dms_kelurahan_id')">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Kelurahan</label>
                                        <select name="dms_kelurahan_id" id="dms_kelurahan_id" class="form-control select2">
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Alamat</label>
                                        <input type="text" class="form-control floating" id="dms_alamat" name="dms_alamat">
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">RT</label>
                                        <input type="number" class="form-control floating" id="dms_rt" name="dms_rt">
                                    </div>
                                </div>


                                <div class="col-lg-2">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">RW</label>
                                        <input type="number" class="form-control floating" id="dms_rw" name="dms_rw">
                                    </div>
                                </div>


                                <div class="col-lg-2">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Kode Pos</label>
                                        <input type="number" class="form-control floating" id="dms_kodepos" name="dms_kodepos">
                                    </div>
                                </div>












                            </div>
                        </fieldset>







                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-update-pasien">Simpan</button>
            </div>
        </div>
    </div>
</div>