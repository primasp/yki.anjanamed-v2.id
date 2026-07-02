<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('Pasien-All') ?>">Patients </a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Add Patient</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div class="row">
        <div class="col-lg-9 col-md-12 col-sm-12">
            <form id="form-add-pasien">
                <div class="card-box">
                    <h3 class="card-title">Data Identitas Utama</h3>
                    <div class="row">
                        <div class="col-12">
                            <div class="profile-img-wrap">
                                <img class="inline-block" src="assets/img/user.jpg" alt="user">
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
                                            <input type="text" id="nama" name="nama" class="form-control floating" value="<?= isset($dataPasBaru['nama']) ? $dataPasBaru['nama'] : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Kartu keluarga (KK) </label>
                                            <input type="text" class="form-control floating" id="no_kakel" name="no_kakel">
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nomor Induk Kependudukan (KTP) <span class="text-danger">*</span></label>
                                            <!-- <input type="text" class="form-control floating" id="nik" name="nik" maxlength="16" pattern="\d*" value="<?= isset($dataPasBaru['nik']) ? $dataPasBaru['nik'] : '' ?>" required> -->
                                            <input type="text" class="form-control floating" id="nik" name="nik" maxlength="16" minlength="16" pattern="[0-9]{16}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="<?= isset($dataPasBaru['nik']) ? $dataPasBaru['nik'] : '' ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Tempat Lahir</label>
                                            <input type="text" class="form-control floating" id="tempat_lahir" name="tempat_lahir">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                            <div class="cal-icon">
                                                <input class="form-control floating datetimepicker" id="tgl_lahir" name="tgl_lahir" placeholder="dd/mm/yyyy" type="text" value="<?= isset($dataPasBaru['tglLahir']) ? $dataPasBaru['tglLahir'] : '' ?>" required>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Jenis Kelamin<span class="text-danger">*</span></label>
                                            <select id="sex_id" name="sex_id" class="form-control select2" required>
                                                <option value="" disabled <?= empty($dataPasBaru['sex']) ? 'selected' : '' ?>>Pilih Jenis Kelamin</option>
                                                <option value="L" <?= isset($dataPasBaru['sex']) && $dataPasBaru['sex'] === 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                                                <option value="P" <?= isset($dataPasBaru['sex']) && $dataPasBaru['sex'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                            </select>

                                            <!-- <select name="pjp_hubungan_id" id="pjp_hubungan_id" class="form-control form-control-sm"> -->
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nama Suami / Istri </label>
                                            <input type="text" class="form-control floating" id="nama_pasangan" name="nama_pasangan" placeholder="">
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Email</label>
                                            <input type="email" class="form-control floating" id="email" name="email" placeholder="">
                                        </div>
                                    </div>



                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Nama Ibu Kandung</label>
                                            <input type="text" class="form-control floating" id="nama_ibukandung" name="nama_ibukandung" placeholder="">
                                        </div>
                                    </div>



                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Status</label>
                                            <select id="marital_status_id" name="marital_status_id" class="form-control select2">
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($dataPasBaru['statusKawin'] as $key => $row) : ?>
                                                    <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

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

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">No Handphone <span style="color: red">*</span></label>
                                            <input type="number" class="form-control floating numeric" id="no_selular" name="no_selular" value="<?= isset($dataPasBaru['noHp']) ? $dataPasBaru['noHp'] : '' ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-6 col-sm-6">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">No. MR Lama / Manual</label>
                                            <input type="text" class="form-control floating" id="rm_lama" name="rm_lama">
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
                                <select id="propinsi_id" name="propinsi_id" class="form-small js-example-basic-single select2" child-id="kabupaten_id" jenis-id="1" empty-label="Propinsi" onchange="dropdownKota(this.value, 'kabupaten_id')" required>
                                    <option value="">-- Pilih Propinsi --</option>
                                    <?php foreach ($dataPasBaru['propinsi'] as $key => $row) : ?>
                                        <option value="<?= $row['kkk_id'] ?>"><?= $row['keterangan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kabupaten / Kota</label>
                                <select name="kabupaten_id" id="kabupaten_id" class="form-small js-example-basic-single select2" child-id="kecamatan_id" jenis-id="2" empty-label="Kabupaten" onchange="dropdownKecamatan(this.value, 'kecamatan_id')" required>
                                    <option value="">-- Pilih Kabupaten --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kecamatan</label>
                                <select name="kecamatan_id" id="kecamatan_id" class="form-small js-example-basic-single select2" child-id="kelurahan_id" jenis-id="3" empty-label="Kecamatan" onchange="dropdownKelurahan(this.value, 'kelurahan_id')" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Kelurahan </label>
                                <select name="kelurahan_id" id="kelurahan_id" class="form-small js-example-basic-single select2" jenis-id="4" empty-label="Kelurahan" required>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Alamat </label>
                                <input type="text" class="form-control floating" id="alamat" name="alamat" required>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">RT </label>
                                <input type="number" class="form-control floating" id="rt" name="rt" required>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">RW </label>
                                <input type="number" class="form-control floating" id="rw" name="rw" required>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">Kode Pos</label>
                                <input type="number" class="form-control floating" id="kodepos" name="kodepos">
                            </div>
                        </div>
                    </div>
                    <h3 class="card-title">Alamat Domisili</h3>
                    <fieldset id="fieldset-alamat-domisili">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-checkbox mb-3">
                                    <input type="checkbox" id="alamat-sama">
                                    <label>Alamat domisili sama dengan alamat KTP</label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Propinsi </label>
                                    <select name="dms_propinsi_id" id="dms_propinsi_id" class="form-small js-example-basic-single select2" child-id="dms_kabupaten_id" jenis-id="1" empty-label="Propinsi" onchange="dropdownKota(this.value, 'dms_kabupaten_id')">
                                        <option value="">-- Pilih Propinsi --</option>
                                        <?php foreach ($dataPasBaru['propinsi'] as $key => $row) : ?>
                                            <option value="<?= $row['kkk_id'] ?>"><?= $row['keterangan'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kabupaten / Kota </label>
                                    <select name="dms_kabupaten_id" id="dms_kabupaten_id" class="form-small js-example-basic-single select2" child-id="dms_kecamatan_id" jenis-id="2" empty-label="Kabupaten" onchange="dropdownKecamatan(this.value, 'dms_kecamatan_id')">
                                        <option value="">-- Pilih Kabupaten --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kecamatan </label>
                                    <select name="dms_kecamatan_id" id="dms_kecamatan_id" class="form-small js-example-basic-single select2" child-id="dms_kelurahan_id" jenis-id="3" empty-label="Kecamatan" onchange="dropdownKelurahan(this.value, 'dms_kelurahan_id')">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="input-block local-forms">
                                    <label class="my-0">Kelurahan </label>
                                    <select name="dms_kelurahan_id" id="dms_kelurahan_id" class="form-small js-example-basic-single select2" jenis-id="4" empty-label="Kelurahan">
                                        <option value="">-- Pilih Kelurahan --</option>
                                    </select>
                                </div>
                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-12">
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
                            </div>
                        </div>
                    </fieldset>
                </div>


                <div class="card-box">
                    <h3 class="card-title">Pembayaran / Penjamin</h3>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Pilih Penjamin <span style="color: red">*</span></label>
                                <select id="rekanan_id" name="rekanan_id" class="form-small js-example-basic-single select2" required>
                                    <option value="">-- Pilih Penjamin --</option>
                                    <?php foreach ($dataPasBaru['rekanan'] as $key => $row) : ?>
                                        <option value="<?= $row['rekanan_id'] ?>"><?= $row['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12" id="bpjs-input-container" style="display: none;">
                            <div class="input-block local-forms">
                                <label class="my-0" id="no-bpjs-label">Nomor BPJS Kesehatan <span style="color: red">*</span></label>
                                <input type="text" class="form-control form-control-sm numeric" id="bpjs_no" name="bpjs_no" placeholder="No BPJS Kesehatan">
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
                                <select id="pekerjaan_id" name="pekerjaan_id" class="form-control select2">

                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <?php foreach ($dataPasBaru['pekerjaan'] as $key => $row) : ?>
                                        <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Pendidikan</label>
                                <select id="pendidikan_id" name="pendidikan_id" class="form-control select2">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    <?php foreach ($dataPasBaru['pendidikan'] as $key => $row) : ?>
                                        <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Agama</label>
                                <select id="agama_id" name="agama_id" class="form-control select2">
                                    <option value="">-- Pilih Agama --</option>
                                    <?php foreach ($dataPasBaru['agama'] as $key => $row) : ?>
                                        <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Suku</label>
                                <select id="ethnic_id" name="ethnic_id" class="form-small js-example-basic-single select2" child-id="kabupaten_id" jenis-id="1" empty-label="Propinsi" onchange="dropdownKota(this.value, 'kabupaten_id')">
                                    <option value="">-- Pilih Suku --</option>
                                    <?php foreach ($dataPasBaru['suku'] as $key => $row) : ?>
                                        <option value="<?= $row['suneg_id'] ?>"><?= $row['keterangan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="card-box">
                    <h3 class="card-title">Penanggung Jawab / Wali Pasien</h3>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Nama Lengkap Penanggung Jawab</label>

                                <input type="text" id="pjp_nama" name="pjp_nama" class="form-control floating">

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Nomor Telepon Seluler / Rumah</label>

                                <input type="text" id="pjp_hp" name="pjp_hp" class="form-control floating">

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="my-0">Hubungan PJP </label>
                                <select name="pjp_hubungan_id" id="pjp_hubungan_id" class="form-control form-control-sm">
                                    <option value="">-- Pilih Hubungan PJP --</option>
                                    <?php foreach ($dataPasBaru['hubungan'] as $key => $row) : ?>
                                        <option value="<?= $row['global_id'] ?>"><?= $row['keterangan'] ?></option>
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
                    <button id="btnSavePasien" class="btn btn-primary w-100 mb-2">Simpan</button>
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