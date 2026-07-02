<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('Rajal-All') ?>">Rawat jalan</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Registrasi-V2</li>
                </ul>
            </div>
        </div>
    </div>
    <form id="formRegistLayan">
        <div class="row">

            <div class="col-xl-10 col-lg-9 col-md-8 col-sm-8">

                <div class="card-box">

                    <div class="row">
                        <div class="col-12">
                            <div class="mailview-header comman-space-flex">
                                <div class="sender-info comman-flex">
                                    <div class="send-user send-user-name">
                                        <h4>Data Pasien</h4>
                                    </div>
                                </div>
                                <div class="forward-send">
                                    <a href="javascript" class="btn btn-primary forwrd-btn-data-pas">
                                        <img src="<?= base_url('assets/img/icons/replay-01.svg') ?>" class="me-2" alt="img">Atur Ulang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row pt-3">

                        <!-- <div class="col-md-6"> -->
                        <div class="col-xl-5 col-lg-5 col-md-11 col-sm-11">
                            <div class="input-block local-forms">
                                <label class="focus-label">Nama Lengkap <span class="login-danger">*</span></label>
                                <input type="text" id="namaPas" name="namaPas" class="form-control floating" value="<?= isset($pasien) ? $pasien['nama'] : '' ?>" required>
                            </div>
                        </div>


                        <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 pt-1">
                            <button type="button" class="btn btn-primary shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#bookingModal" title="Pasien Perjanjian">
                                <!-- <i class="fas fa-file-signature"></i> -->
                                <i class="fas fa-calendar-check"></i>
                            </button>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">No. Rekam medis <span class="login-danger">*</span></label>
                                <input type="text" id="noRm" name="noRm" class="form-control floating" value="<?= isset($pasien) ? $pasien['int_pasien_id'] : '' ?>" required>
                                <input type="text" name="pasien_id" id="pasien_id" value="<?= isset($pasien) ? $pasien['pasien_id'] : '' ?>" hidden>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control floating " id="tgl_lahir" name="tgl_lahir" type="text" value="<?= isset($pasien) ? $pasien['tgl_lahir'] : '' ?>" disabled required>

                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Jenis Kelamin <span class="login-danger">*</span></label>

                                <input class="form-control floating" id="sex_id" name="sex_id" type="text" value="<?= isset($pasien) ? $pasien['sex_id'] : '' ?>" disabled required>

                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-4 col-md-12 col-sm-12">
                            <div class="input-block local-forms">
                                <label class="focus-label">Alamat <span class="login-danger">*</span></label>

                                <input class="form-control floating" id="alamat" name="alamat" type="text" value="<?= isset($pasien) ? $pasien['alamat1'] : '' ?>" disabled required>

                            </div>
                        </div>
                    </div>
                    <div class="row pt-5">
                        <div class="col-12">
                            <div class="mailview-header comman-space-flex">
                                <div class="sender-info comman-flex">
                                    <div class="send-user send-user-name">
                                        <h4>Data Registrasi</h4>
                                    </div>
                                </div>
                                <div class="forward-send">
                                    <a href="javascript" class="btn btn-primary forwrd-btn-biaya">
                                        <img src="<?= base_url('assets/img/icons/replay-01.svg') ?>" class="me-2" alt="img">Atur Ulang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row pt-3">
                        <!-- <div class="col-md-6"> -->
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                            <div class="input-block local-forms text-center">
                                <label class="my-0  fw-bold">Pembiayaan<span class="login-danger">*</span></label>
                                <select name="pembiayaan" id="pembiayaan" class="form-control" required>
                                    <option value="">-Pilih-</option>
                                    <option value="UMUM">Umum</option>
                                    <option value="PROGRAM">Program</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row pt-3">
                        <!-- <div class="col-6"> -->
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="row pt-2">
                                        <div class=" comman-space-flex">
                                            <div class="sender-info comman-flex">
                                                <div class="send-user send-user-name">
                                                    <!-- <h4>Poliklinik</h4> -->
                                                    <h4> <i class="fa fa-edit fa-2x" style="color: #4B0082;" data-bs-toggle="tooltip"></i> &nbsp;<label id="label_layan">Registrasi Penunjang</label></h4>
                                                </div>
                                            </div>
                                            <div class="forward-send">
                                                <a href="javascript" class="btn btn-primary forwrd-btn-regist">
                                                    <img src="<?= base_url('assets/img/icons/replay-01.svg') ?>" class="me-2" alt="img">Atur Ulang
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                    <hr class="pb-4" style="border-top: 2px solid #6a0dad; opacity: 1;">

                                    <div class="row ps-4">
                                        <!-- <div class="col-md-7"> -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                            <div class="input-block">
                                                <label class="gen-label fw-bold">Jenis Kunjungan<span class="login-danger">*</span></label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_kunjungan" id="radioPenunjang" value="2" checked>
                                                    <label class="form-check-label" for="radioPenunjang">Pemeriksaan Penunjang</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_kunjungan" id="radioPoli" value="1">
                                                    <label class="form-check-label" for="radioPoli">Kunjungan ke Poliklinik</label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fieldset Penunjang -->
                                    <fieldset id="fieldsetPenunjang" class="border p-3 mt-3 mb-5 rounded bg-light">
                                        <legend class="fw-bold text-primary">Data Pemeriksaan Penunjang</legend>

                                        <div class="row pt-4 mb-3">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="chkLab">
                                                    <label class="form-check-label fw-bold" for="chkLab">Laboratorium</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="chkRad">
                                                    <label class="form-check-label fw-bold" for="chkRad">Radiologi</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Laboratorium -->
                                        <div id="labContainer" style="display:none;">
                                            <h6 class="fw-bold text-secondary">Pemeriksaan Laboratorium</h6>
                                            <div id="labItemsList">
                                                <div class="row g-2 align-items-center mb-2">
                                                    <div class="col-md-6">
                                                        <select class="form-select lab-item" name="lab_item[]">
                                                            <option value="">-- Pilih Pemeriksaan --</option>
                                                            <?php foreach ($lab_items as $lab) : ?>
                                                                <!-- <option value="<?= $lab['layan_id'] ?>"><?= $lab['nama_pemeriksaan'] ?></option> -->
                                                                <option value="<?= $lab['layan_id'] ?>" data-hargalab="<?= number_format($lab['harga'], 0, ',', '.') ?>">
                                                                    <?= $lab['nama_pemeriksaan'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <span class="harga-labelLab text-muted">Rp 0</span>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <input type="number" class="form-control" name="lab_qty[]" placeholder="Qty" min="1" value="1">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-success btn-sm addLabItem"><i class="fa fa-plus"></i></button>
                                                        <!-- <button type="button" class="btn btn-danger btn-sm removeItem"><i class="fa fa-trash"></i></button> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Radiologi -->
                                        <div id="radContainer" style="display:none;" class="mt-4">
                                            <h6 class="fw-bold text-secondary">Pemeriksaan Radiologi</h6>
                                            <div id="radItemsList">
                                                <div class="row g-2 align-items-center mb-2">
                                                    <div class="col-md-6">
                                                        <select class="form-select rad-item" name="rad_item[]">
                                                            <option value="">-- Pilih Pemeriksaan --</option>
                                                            <?php foreach ($rad_items as $r) : ?>
                                                                <option value="<?= $r['layan_id'] ?>" data-hargarad="<?= number_format($r['harga'], 0, ',', '.') ?>">
                                                                    <?= $r['nama_pemeriksaan'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <span class="harga-labelRad text-muted">Rp 0</span>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number" class="form-control" name="rad_qty[]" placeholder="Qty" min="1" value="1">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-success btn-sm addRadItem"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <!-- Fieldset Poliklinik -->
                                    <fieldset id="fieldsetPoli" class="border p-3 mt-3 mb-5 rounded bg-light" style="display:none;">
                                        <legend class="fw-bold text-primary">Data Poliklinik</legend>
                                        <div class="row pt-4">
                                            <div class="col-12">
                                                <div class="input-block local-forms cal-icon">
                                                    <label class="focus-label fw-bold">Tgl. Berobat<span class="text-danger">*</span></label>
                                                    <input type="text" id="tgl_berobat" name="tgl_berobat" class="form-control floating" value="<?= date('d/m/Y') ?>" min="<?= date('d/m/Y') ?>" readonly required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-block local-forms text-center">
                                                    <label class="my-0  fw-bold" id="label_poli">Pilih Poliklinik <span class="login-danger">*</span></label>
                                                    <select name="poli" id="poli" class="form-control select2">
                                                        <option value="">-- Pilih Poliklinik --</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="input-block local-forms text-center">
                                                    <label class="my-0  fw-bold" id="label_poli">Pilih Dokter <span class="login-danger">*</span></label>
                                                    <select name="dokter" id="dokter" class="form-control select2">
                                                        <option value="">-- Pilih Dokter --</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="input-block local-forms text-center">
                                                    <label class="my-0  fw-bold" id="label_poli">Pilih Jam Slot <span class="login-danger">*</span></label>
                                                    <select name="jam_slot" id="jam_slot" class="form-control select2">
                                                        <option value="">-- Pilih Jam Slot --</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4">
                <div class="card sticky-sidebar">
                    <div class="card-body">
                        <button type="submit" id="btnRegistPasien" class="btn btn-primary w-100 mb-2">Simpan</button>
                        <button id="btnCancelPasien" class="btn btn-secondary w-100">Batal</button>
                        <hr>
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider">Isian</h2>
                            <input type="checkbox" id="status_1" class="check">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>