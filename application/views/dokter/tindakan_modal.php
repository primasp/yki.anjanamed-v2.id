<div class="modal fade" id="modal_tindakan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="height: 800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="card-body">
                    <div class="form-group row" style="margin: 0; padding: 0;">
                        <label for="rm_pasien" class="col-sm-2 col-form-label">MRN</label>
                        <div class="col-sm-4" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm rm_pasien" id="rm_pasien2" name="rm_pasien2" style="border: none; border-bottom: 1px solid #000; margin: 0; padding: 0; border-radius: 0;">
                        </div>
                        <div class="col-sm-1"></div>
                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                        <div class="col-sm-3" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm jk_pasien" id="jk_pasien2" name="jk_pasien2" style="border: none; border-bottom: 1px solid #000; margin: 0; padding: 0; border-radius: 0">
                        </div>
                    </div>
                    <div class="form-group row" style="margin: 0; padding: 0;">
                        <label class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-4" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm nama_pasien" id="nama_pasien4" name="nama_pasien4" style="border: none; border-bottom: 1px solid #000; margin: 0; padding: 0; border-radius: 0">
                        </div>
                        <div class="col-sm-1"></div>
                        <label class="col-sm-2 col-form-label">Penjamin</label>
                        <div class="col-sm-3" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm prov_pasien" id="prov_pasien4" name="prov_pasien4" style="border: none; border-bottom: 1px solid #000; margin: 0; padding: 0; border-radius: 0">
                        </div>
                    </div>
                    <div class="form-group row" style="margin: 0; padding: 0;">
                        <label for="lahir_pasien" class="col-sm-2 col-form-label">Tgl Lahir</label>
                        <div class="col-sm-4" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm lahir_pasien" id="lahir_pasien3" name="lahir_pasien3" style="border: none; border-bottom: 1px solid #000; margin: 0; padding: 0; border-radius: 0">
                        </div>
                        <div class="col-sm-1"></div>
                        <label class="col-sm-2 col-form-label">Kunjungan Ke</label>
                        <div class="col-sm-3" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm kunj_ke" id="kunj_ke4" name="kunj_ke4" style="border: none; border-bottom: 1px solid #000; margin: 0; padding: 0; border-radius: 0">
                        </div>
                    </div>

                    <hr>

                    <div class="form-group row" style="margin: 0; padding: 0;">
                        <label for="pencarian_tindakan" class="col-sm-2 col-form-label">Nama Tindakan</label>
                        <div class="col-sm-3" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm" id="pencarian_tindakan" name="pencarian_tindakan">
                        </div>
                        <label class="col-sm-1 col-form-label">Jumlah</label>
                        <div class="col-sm-1" style="margin: 0; padding: 0;">
                            <input type="text" class="form-control form-control-sm" id="jumlah_tindakan" name="jumlah_tindakan">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary mb-3" id="btn_tambah_tindakan">
                                <i class="fas fa-search"></i> Tambah
                            </button>
                        </div>
                        <div class="col-sm-1" style="margin: 0; padding: 0;">
                            <input type="hidden" class="form-control form-control-sm" id="nama_tindakan" name="nama_tindakan">
                        </div>
                        <div class="col-sm-1" style="margin: 0; padding: 0;">
                            <input type="hidden" class="form-control form-control-sm" id="kode_tindakan" name="kode_tindakan">
                        </div>
                        <div class="col-sm-1" style="margin: 0; padding: 0;">
                            <input type="hidden" class="form-control form-control-sm" id="harga_tindakan" name="harga_tindakan">
                        </div>
                    </div>

                    <div class="form-group row" style="margin: 0; padding: 0;">
                        <div class="col-md-7">
                            <div class="row">
                                <table class="table small table-hover table-head-fixed text-nowrap">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Nama Tindakan</th>
                                            <th style="width: 20%">Jumlah</th>
                                            <th style="width: 20%">Total</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="listtindakan"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="btn-simpan-tindakan" onclick="simpantindakan(event)">Simpan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cari tindakan-->
<div class="modal fade" id="modal_caritindakan" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="height: 800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <label for="input_caritindakan" class="col-sm-2 col-form-label">Cari</label>
                            <input class="form-control form-control-sm" id="input_caritindakan" type="text" name="input_caritindakan">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-sm small table-hover table-head-fixed text-nowrap">
                                <thead class="text-center">
                                    <tr>
                                        <th>Tindakan</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody id="listmastertindakan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>