<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('Pasien-All') ?>">Patients </a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Edit Patient</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div class="row">
        <div class="col-lg-9 col-md-12 col-sm-12">
            <form id="form-edit-pasien">
                <div class="card-box">
                    <h3 class="card-title">Data Identitas Utama</h3>
                    <div class="row">
                        <div class="col-12">
                            <div class="profile-img-wrap">
                                <img class="inline-block" src="<?= base_url('assets/img/user.jpg') ?>" alt="user">
                                <div class="fileupload btn">
                                    <span class="btn-text">edit</span>
                                    <input class="upload" type="file">
                                </div>
                            </div>
                            <div class="profile-basic">
                                <div class="row">
                                    <!-- <div class="col-md-6"> -->
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" id="edit_nama" name="edit_nama" class="form-control floating" value="<?= $pasien->nama ?>" required>
                                            <input type="hidden" id="edit_pasien_id" name="edit_pasien_id" class="form-control floating" value="<?= $pasien->pasien_id ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Kartu keluarga (KK) </label>
                                            <input type="text" class="form-control floating" id="edit_no_kakel" name="edit_no_kakel" value="<?= $pasien->no_kk ?>">
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nomor Induk Kependudukan (KTP) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control floating" id="edit_nik" name="edit_nik" maxlength="16" pattern="\d*" value="<?= $pasien->no_identitas ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Tempat Lahir</label>
                                            <input type="text" class="form-control floating" id="edit_tempat_lahir" name="edit_tempat_lahir" value="<?= $pasien->tempat_lahir_txt ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                            <div class="cal-icon">
                                                <input class="form-control floating datetimepicker" id="edit_tgl_lahir" name="edit_tgl_lahir" placeholder="dd/mm/yyyy" type="text" value="<?= isset($pasien->tgl_lahir) ? date('d/m/Y', strtotime($pasien->tgl_lahir)) : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                            <select id="edit_sex_id" name="edit_sex_id" class="form-control select2" required>
                                                <option value="" disabled <?= empty($pasien->sex_id) ? 'selected' : '' ?>>Pilih Jenis Kelamin</option>
                                                <option value="L" <?= isset($pasien->sex_id) && $pasien->sex_id === 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                                                <option value="P" <?= isset($pasien->sex_id) && $pasien->sex_id === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nama Suami / Istri </label>
                                            <input type="text" class="form-control floating" id="edit_nama_pasangan" name="edit_nama_pasangan" placeholder="" value="<?= $pasien->nama_pasangan ?>">
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Email</label>
                                            <input type="email" class="form-control floating" id="edit_email" name="edit_email" placeholder="" value="<?= $pasien->email ?>">
                                        </div>
                                    </div>



                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nama Ibu Kandung</label>
                                            <input type="text" class="form-control floating" id="edit_nama_ibukandung" name="edit_nama_ibukandung" value="<?= isset($pasien->nama_ibukandung) ? $pasien->nama_ibukandung : '' ?>" placeholder="">
                                        </div>
                                    </div>







                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Status</label>
                                            <select id="edit_marital_status_id" name="edit_marital_status_id" class="form-control select2">
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($statusKawin as $row) : ?>
                                                    <option value="<?= $row['global_id'] ?>" <?= (isset($pasien->marital_status_id) && $pasien->marital_status_id == $row['global_id']) ? 'selected' : '' ?>>
                                                        <?= $row['keterangan'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>











                                    <!-- 
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Golongan Darah</label>
                                            <select id="pas_goldar_id" name="pas_goldar_id" class="form-control select2 text-center">
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($dataPasBaru['goldar'] as $key => $row) : ?>
                                                    <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
 -->





                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Golongan Darah</label>
                                            <select id="edit_pas_goldar_id" name="edit_pas_goldar_id" class="form-control select2">
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($goldar as $row) : ?>
                                                    <option value="<?= $row['global_id'] ?>" <?= (isset($pasien->pas_goldar_id) && $pasien->pas_goldar_id == $row['global_id']) ? 'selected' : '' ?>>
                                                        <?= $row['keterangan'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>














                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">No Handphone <span style="color: red">*</span></label>
                                            <input type="number" class="form-control floating numeric" id="edit_no_selular" name="edit_no_selular" value="<?= isset($pasien->no_selular) ? $pasien->no_selular : '' ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-6 col-sm-6">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">No. MR Lama / Manual</label>
                                            <input type="text" class="form-control floating" id="edit_rm_lama" name="edit_rm_lama" value="<?= isset($pasien->mr_lama) ? $pasien->mr_lama : '' ?>">
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>






                <div class="card-box">
                    <h3 class="card-title">Alamat KTP</h3>
                    <div class="row">



                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Propinsi</label>
                                <select id="edit_propinsi_id" name="edit_propinsi_id" class="form-small js-example-basic-single select2" onchange="dropdownEditKota(this.value, 'edit_kabupaten_id')">
                                    <option value="">-- Pilih Propinsi --</option>
                                    <?php foreach ($propinsi as $row) : ?>
                                        <option value="<?= $row['kkk_id'] ?>" <?= (isset($pasien->propinsi_id) && $pasien->propinsi_id == $row['kkk_id']) ? 'selected' : '' ?>>
                                            <?= $row['keterangan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kabupaten / Kota</label>
                                <!-- <select name="edit_kabupaten_id" id="edit_kabupaten_id" class="form-small js-example-basic-single select2" onchange="dropdownEditKecamatan(this.value, 'edit_kecamatan_id')">
                                    <option value="">-- Pilih Kabupaten --</option>
                                </select> -->


                                <select name="edit_kabupaten_id" id="edit_kabupaten_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->kabupaten_id) ? $pasien->kabupaten_id : '' ?>" onchange="dropdownEditKecamatan(this.value, 'edit_kecamatan_id')">
                                    <option value="">-- Pilih Kabupaten --</option>
                                </select>
                            </div>
                        </div>

















                        <!-- <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kecamatan</label>
                                <select name="kecamatan_id" id="kecamatan_id" class="form-small js-example-basic-single select2" child-id="kelurahan_id" jenis-id="3" empty-label="Kecamatan" onchange="dropdownKelurahan(this.value, 'kelurahan_id')">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                        </div> -->





                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kecamatan</label>
                                <select name="edit_kecamatan_id" id="edit_kecamatan_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->kecamatan_id) ? $pasien->kecamatan_id : '' ?>" onchange="dropdownEditKelurahan(this.value, 'edit_kelurahan_id')">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                        </div>








                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kelurahan</label>
                                <select name="edit_kelurahan_id" id="edit_kelurahan_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->kelurahan_id) ? $pasien->kelurahan_id : '' ?>">
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                            </div>
                        </div>







                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Alamat</label>
                                <input type="text" class="form-control floating" id="edit_alamat" name="edit_alamat" value="<?= isset($pasien->alamat1) ? $pasien->alamat1 : '' ?>">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">RT</label>
                                <input type="number" class="form-control floating" id="edit_rt" name="edit_rt" value="<?= isset($pasien->rt) ? $pasien->rt : '' ?>">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">RW</label>
                                <input type="number" class="form-control floating" id="edit_rw" name="edit_rw" value="<?= isset($pasien->rw) ? $pasien->rw : '' ?>">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">Kode Pos</label>
                                <input type="number" class="form-control floating" id="edit_kodepos" name="edit_kodepos" value="<?= isset($pasien->kode_pos) ? $pasien->kode_pos : '' ?>">
                            </div>
                        </div>





















                    </div>
                    <h3 class="card-title">Alamat Domisili</h3>
                    <fieldset id="fieldset-alamat-domisili">
                        <div class="row">



                            <div class="col-md-12">
                                <div class="custom-checkbox mb-3">
                                    <!-- <input type="checkbox" id="edit-alamat-sama" <?= (isset($pasien->alamat1) && $pasien->alamat1 == $pasien->alamat2) ? 'checked' : '' ?>> -->
                                    <input type="checkbox" id="edit-alamat-sama" <?= (isset($pasien->alamat1) && isset($pasien->alamat2) && $pasien->alamat1 == $pasien->alamat2) ? 'checked' : '' ?>>
                                    <label for="edit-alamat-sama">Alamat domisili sama dengan alamat KTP </label>
                                </div>
                            </div>



                            <!-- <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Propinsi </label>
                                    <select name="dms_propinsi_id" id="dms_propinsi_id" class="form-small js-example-basic-single select2" child-id="dms_kabupaten_id" jenis-id="1" empty-label="Propinsi" onchange="dropdownKota(this.value, 'dms_kabupaten_id')">
                                        <option value="">-- Pilih Propinsi --</option>
                                        <?php foreach ($dataPasBaru['propinsi'] as $key => $row) : ?>
                                            <option value="<?= $row['kkk_id'] ?>"><?= $row['keterangan'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div> -->

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Propinsi Domisili</label>
                                    <select name="edit_dms_propinsi_id" id="edit_dms_propinsi_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->dms_propinsi) ? $pasien->dms_propinsi : '' ?>" onchange="dropdownEditKota(this.value, 'edit_dms_kabupaten_id')">
                                        <option value="">-- Pilih Propinsi --</option>
                                        <?php foreach ($propinsi as $row) : ?>
                                            <option value="<?= $row['kkk_id'] ?>" <?= (isset($pasien->dms_propinsi) && $pasien->dms_propinsi == $row['kkk_id']) ? 'selected' : '' ?>>
                                                <?= $row['keterangan'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>







                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kabupaten / Kota Domisili <?= $pasien->dms_kota ?></label>
                                    <select name="edit_dms_kabupaten_id" id="edit_dms_kabupaten_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->dms_kota) ? $pasien->dms_kota : '' ?>" onchange="dropdownEditKecamatan(this.value, 'edit_dms_kecamatan_id')">
                                        <option value="">-- Pilih Kabupaten --</option>
                                    </select>
                                </div>
                            </div>






                            <!-- <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kabupaten / Kota </label>
                                    <select name="dms_kabupaten_id" id="dms_kabupaten_id" class="form-small js-example-basic-single select2" child-id="dms_kecamatan_id" jenis-id="2" empty-label="Kabupaten" onchange="dropdownKecamatan(this.value, 'dms_kecamatan_id')">
                                        <option value="">-- Pilih Kabupaten --</option>
                                    </select>
                                </div>
                            </div> -->



                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kecamatan </label>
                                    <!-- <select name="dms_kecamatan_id" id="dms_kecamatan_id" class="form-small js-example-basic-single select2" child-id="dms_kelurahan_id" jenis-id="3" empty-label="Kecamatan" onchange="dropdownKelurahan(this.value, 'dms_kelurahan_id')">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select> -->

                                    <select name="edit_dms_kecamatan_id" id="edit_dms_kecamatan_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->dms_kecamatan) ? $pasien->dms_kecamatan : '' ?>" onchange="dropdownEditKelurahan(this.value, 'edit_dms_kelurahan_id')">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kelurahan </label>
                                    <!-- <select name="dms_kelurahan_id" id="dms_kelurahan_id" class="form-small js-example-basic-single select2" jenis-id="4" empty-label="Kelurahan">
                                        <option value="">-- Pilih Kelurahan --</option>
                                    </select> -->

                                    <select name="edit_dms_kelurahan_id" id="edit_dms_kelurahan_id" class="form-small js-example-basic-single select2" data-selected="<?= isset($pasien->dms_kelurahan) ? $pasien->dms_kelurahan : '' ?>">
                                        <option value="">-- Pilih Kelurahan --</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Alamat Domisili</label>
                                    <input type="text" class="form-control floating" id="edit_dms_alamat" name="edit_dms_alamat" value="<?= isset($pasien->dms_alamat) ? $pasien->dms_alamat : '' ?>" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">RT</label>
                                    <input type="number" class="form-control floating" id="edit_dms_rt" name="edit_dms_rt" value="<?= isset($pasien->dms_rt) ? $pasien->dms_rt : '' ?>">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">RW</label>
                                    <input type="number" class="form-control floating" id="edit_dms_rw" name="edit_dms_rw" value="<?= isset($pasien->dms_rw) ? $pasien->dms_rw : '' ?>">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Kode Pos</label>
                                    <input type="number" class="form-control floating" id="edit_dms_kodepos" name="edit_dms_kodepos" value="<?= isset($pasien->dms_kodepos) ? $pasien->dms_kodepos : '' ?>">
                                </div>
                            </div>







                            <!-- <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Alamat </label>
                                    <input type="text" class="form-control floating" id="dms_alamat" name="dms_alamat" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">RT </label>
                                    <input type="number" class="form-control floating" id="dms_rt" name="dms_rt" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">RW </label>
                                    <input type="number" class="form-control floating" id="dms_rw" name="dms_rw">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Kode Pos</label>
                                    <input type="number" class="form-control floating" id="dms_kodepos" name="dms_kodepos">
                                </div>
                            </div> -->
                        </div>
                    </fieldset>
                </div>


                <div class="card-box">
                    <h3 class="card-title">Pembayaran / Penjamin</h3>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Pilih Penjamin<span style="color: red">*</span></label>
                                <select id="edit_rekanan_id" name="edit_rekanan_id" class="form-small js-example-basic-single select2" required>
                                    <option value="">-- Pilih Penjamin --</option>


                                    <?php foreach ($rekanan as $key => $row) : ?>
                                        <!-- <option value="<?= $row['rekanan_id'] ?>"><?= $row['nama'] ?></option> -->

                                        <option value="<?= $row['rekanan_id'] ?>" <?= (isset($pasien->akhir_rekanan_id) && $pasien->akhir_rekanan_id == $row['rekanan_id']) ? 'selected' : '' ?>>
                                            <?= $row['nama'] ?>
                                        </option>


                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>













                        <!-- Input Nomor BPJS -->
                        <div class="col-lg-6 col-md-6 col-sm-12" id="edit-bpjs-input-container" style="display: none;">
                            <div class="input-block local-forms">
                                <!-- <label class="my-0" id="edit-no-bpjs-label">Nomor BPJS Kesehatan <span style="color: red">*</span></label> -->
                                <!-- <input type="text" class="form-control form-control-sm numeric" id="edit_bpjs_no" name="edit_bpjs_no" placeholder="No BPJS Kesehatan" value="<?= isset($pasien->no_kartuprov) ? $pasien->no_kartuprov : '' ?>"> -->

                                <input type="text" class="form-control form-control-sm numeric" id="edit_bpjs_no" name="edit_bpjs_no" placeholder="No BPJS Kesehatan" value="<?= isset($pasien->no_kartuprov) ? $pasien->no_kartuprov : '' ?>" data-selected="<?= isset($pasien->no_kartuprov) ? $pasien->no_kartuprov : '' ?>">



                            </div>
                        </div>


                    </div>
                </div>



                <div class="card-box">
                    <h3 class="card-title">Data Identitas Penunjang</h3>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Pekerjaan</label>
                                <select id="edit_pekerjaan_id" name="edit_pekerjaan_id" class="form-control select2">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <?php foreach ($pekerjaan as $key => $row) : ?>
                                        <option value="<?= $row['global_id'] ?>" <?= isset($pasien->pekerjaan_id) && $pasien->pekerjaan_id == $row['global_id'] ? 'selected' : '' ?>>
                                            <?= $row['keterangan'] ?>
                                        </option>
                                    <?php endforeach; ?>



                                </select>










                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Pendidikan</label>
                                <select id="edit_pendidikan_id" name="edit_pendidikan_id" class="form-control select2">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    <?php foreach ($pendidikan as $row) : ?>
                                        <option value="<?= $row['global_id'] ?>" <?= isset($pasien->pendidikan_id) && $pasien->pendidikan_id == $row['global_id'] ? 'selected' : '' ?>>
                                            <?= $row['keterangan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>






                        <!-- <?php foreach ($rekanan as $key => $row) : ?>
                                        <option value="<?= $row['rekanan_id'] ?>" <?= (isset($pasien->akhir_rekanan_id) && $pasien->akhir_rekanan_id == $row['rekanan_id']) ? 'selected' : '' ?>>
                                        <?= $row['nama'] ?>
                                        </option>
                                    <?php endforeach; ?> -->






                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Agama</label>
                                <select id="edit_agama_id" name="edit_agama_id" class="form-control select2">
                                    <option value="">-- Pilih Agama --</option>
                                    <?php foreach ($agama as $row) : ?>
                                        <option value="<?= $row['global_id'] ?>" <?= isset($pasien->agama_id) && $pasien->agama_id == $row['global_id'] ? 'selected' : '' ?>>
                                            <?= $row['keterangan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Suku</label>
                                <select id="edit_ethnic_id" name="edit_ethnic_id" class="form-control select2">
                                    <option value="">-- Pilih Suku --</option>
                                    <?php foreach ($suku as $row) : ?>
                                        <option value="<?= $row['suneg_id'] ?>" <?= isset($pasien->ethnic_id) && $pasien->ethnic_id == $row['suneg_id'] ? 'selected' : '' ?>>
                                            <?= $row['keterangan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="card-box">
                    <h3 class="card-title">Penanggung Jawab / Wali Pasien</h3>
                    <div class="row">
                        <!-- Nama Lengkap Penanggung Jawab -->
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Nama Lengkap Penanggung Jawab</label>
                                <input type="text" id="edit_pjp_nama" name="edit_pjp_nama" class="form-control floating" value="<?= isset($pasien->pjp_nama) ? $pasien->pjp_nama : '' ?>">
                            </div>
                        </div>







                        <!-- <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Nama Lengkap Penanggung Jawab</label>

                                <input type="text" id="pjp_nama" name="pjp_nama" class="form-control floating">

                            </div>
                        </div> -->
                        <!-- Nomor Telepon Seluler / Rumah -->
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Nomor Telepon Seluler / Rumah</label>
                                <input type="text" id="edit_pjp_hp" name="edit_pjp_hp" class="form-control floating" value="<?= isset($pasien->pjp_hp) ? $pasien->pjp_hp : '' ?>">
                            </div>
                        </div>


                        <!-- Hubungan dengan Penanggung Jawab -->
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Hubungan PJP</label>
                                <select name="edit_pjp_hubungan_id" id="edit_pjp_hubungan_id" class="form-control select2">
                                    <option value="">-- Pilih Hubungan PJP --</option>
                                    <?php foreach ($hubunganpjp as $row) : ?>
                                        <option value="<?= $row['global_id'] ?>" <?= isset($pasien->pjp_hubungan_id) && $pasien->pjp_hubungan_id == $row['global_id'] ? 'selected' : '' ?>>
                                            <?= $row['keterangan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>








                    </div>

                </div>
            </form>
        </div>









        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="card sticky-sidebar">
                <div class="card-body">
                    <button id="btnUpdatePasien" class="btn btn-primary w-100 mb-2">Update Data Pasien</button>
                    <button id="btnCancelPasien" class="btn btn-secondary w-100">Batal</button>
                    <hr>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider"> Isian </h2>
                        <input type="checkbox" id="status_1" class="check">
                        <!-- <label for="status_1" class="checktoggle">checkbox</label> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>