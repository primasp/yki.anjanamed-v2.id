<div class="content">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('Rajal-All') ?>">Resume Medis</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Resume Rawat Jalan</li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card-box ">
                <div class="row">
                    <div class="mailview-header comman-space-flex">
                        <div class="sender-info comman-flex">
                            <div class="send-user send-user-name">
                                <h4>Resume Rawat Jalan</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="formFilterRsm" class=" align-items-center mb-4 p-3 border rounded shadow-sm bg-light">
                    <div class="row ">
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">Pilih Jenis Layanan</label>
                                <select id="jenisLayanan" name="jenisLayanan" class="form-small js-example-basic-single select2">
                                    <option value="SEMUA">Semua</option>
                                    <option value="PENUNJANG">Penunjang (Lab / Radiologi)</option>
                                    <option value="POLIKLINIK">Poliklinik</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" id="tgl_Mulai" name="tgl_Mulai" placeholder="dd/mm/yyyy" type="text" value="<?= date('d/m/Y') ?>" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="input-block local-forms">
                                <label class="focus-label">Tanggal Selesai<span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <!-- <input class="form-control floating datetimepicker" id="tgl_selesai" name="tgl_selesai" placeholder="dd/mm/yyyy" type="text" value="<?= isset($dataPasBaru['tglLahir']) ? $dataPasBaru['tglLahir'] : '' ?>" required> -->
                                    <input type="text" id="tgl_selesai" name="tgl_selesai" class="form-control floating datetimepicker" value="<?= date('d/m/Y') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="filterPoliklinik" style="display:none;">
                        <div class="row ">
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Poliklinik</label>
                                    <select id="Poliklinik" name="Poliklinik" class="form-small js-example-basic-single select2">
                                        <option value="">-- Pilih Poliklinik --</option>
                                        <?php foreach ($poli as $p) : ?>
                                            <option value="<?= $p->poli_id ?>"><?= $p->keterangan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Dokter</label>
                                    <select id="Dokter" name="Dokter" class="form-small js-example-basic-single select2">
                                        <option value="">-- Pilih Dokter --</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pasien</th>
                                    <th>Poliklinik</th>
                                    <th>Dokter</th>
                                    <th>Status Bayar</th>
                                    <th>Kondisi Pulang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="tableResumeRJ">
                                <tr>
                                    <td colspan="7" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>



                </form>




            </div>
        </div>
    </div>






</div>