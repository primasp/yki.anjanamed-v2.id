<div class="modal fade" id="modal_resep" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="height: 800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Resep</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">MRN</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control-sm rm_pasien" id="rm_pasien3" name="rm_pasien3" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                        </div>
                        <div class="col-sm-1"></div>
                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control form-control-sm jk_pasien" id="jk_pasien3" name="jk_pasien3" style="border: none; border-bottom: 1px solid #000;border-radius: 0;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control-sm nama_pasien" id="nama_pasien3" name="nama_pasien3" style="border: none; border-bottom: 1px solid #000;border-radius: 0;">
                        </div>
                        <div class="col-sm-1"></div>
                        <label class="col-sm-2 col-form-label">Penjamin</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control form-control-sm prov_pasien" id="prov_pasien3" name="prov_pasien3" style="border: none; border-bottom: 1px solid #000;border-radius: 0;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tgl Lahir</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control-sm lahir_pasien" id="lahir_pasien2" name="lahir_pasien2" style="border: none; border-bottom: 1px solid #000;border-radius: 0;">
                        </div>
                        <div class="col-sm-1"></div>
                        <label class="col-sm-2 col-form-label">Kunjungan Ke</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control form-control-sm kunj_ke" id="kunj_ke2" name="kunj_ke2" style="border: none; border-bottom: 1px solid #000;border-radius: 0;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <input type="hidden" class="form-control form-control-sm" id="transcoresep" name="transcoresep">
                        </div>
                        <div class="col-sm-3">
                            <input type="hidden" class="form-control form-control-sm" id="jenissimpan" name="jenissimpan">
                        </div>
                    </div>

                    <hr>

                    <div class="col-md-12">
                        <!-- <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pencarian_obat" class="col-form-label">Nama Obat</label>
                                <input type="text" class="form-control form-control-sm" id="pencarian_obat" name="pencarian_obat">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button class="btn btn-success mb-4" id="btn_bukaracik"><i class="fas fa-plus-circle"></i> Buat Racikan</button>
                            </div>
                        </div> -->


                        <div class="row mb-3 align-items-center">
                            <!-- Label Nama Obat -->
                            <div class="col-md-2 text-end">
                                <label for="pencarian_obat" class="col-form-label">Nama Obat</label>
                            </div>
                            <!-- Input Nama Obat -->
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm" id="pencarian_obat" name="pencarian_obat">
                            </div>
                            <!-- Button Buat Racikan -->
                            <div class="col-md-4 text-center">
                                <button class="btn btn-success align-middle" id="btn_bukaracik">
                                    <i class="fas fa-plus-circle"></i> Buat Racikan
                                </button>
                            </div>
                        </div>






                        <div class="row">
                            <table class="table table-striped table-hover small">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 2%"> </th>
                                        <th style="width: 1%"> </th>
                                        <th style="width: 1%"></th>
                                        <th style="display:none">Obat ID</th>
                                        <th>Nama Obat</th>
                                        <th>Jumlah</th>
                                        <th>Stok</th>
                                        <th>Satuan</th>
                                        <th>Dosis Dokter</th>
                                        <th>Signa</th>
                                        <th>Signa Teks</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody id="resultresep"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="btn-simpan-resep" onclick="simpanresep(event)">Simpan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>












































<!-- Modal Cari master obat-->
<div class="modal fade" id="modal_cariobat" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="height: 800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <label for="input_cariobat" class="col-sm-2 col-form-label">Cari</label>
                            <input class="form-control form-control-sm" id="input_cariobat" type="text" name="input_cariobat">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-sm small table-hover table-head-fixed text-nowrap">
                                <thead class="text-center">
                                    <tr>
                                        <th>Obat</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody id="listmasterobat"></tbody>
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








<div class="modal fade" id="modal_buatracik" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Buat Racikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <form>
                    <!-- <div class="mb-3">
                        <label for="namaracik" class="form-label">Nama Racikan</label>
                        <input type="text" class="form-control form-control-sm" id="namaracik" style="font-weight: bold;">
                    </div> -->

                    <div class="mb-3 row">
                        <label for="namaracik" class="col-sm-3 col-form-label">Nama Racikan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="namaracik" style="font-weight: bold;">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Pembungkus</label>
                        <div class="col-sm-4">
                            <select class="form-select form-select-sm master-bungkus" name="master-bungkus" id="master-bungkus" onchange="pilihpembungkus(this)">
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                        <div class="col-sm-1"></div>
                        <label for="stokbungkus" class="col-sm-2 col-form-label">Stok</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm" id="stokbungkus" style="font-weight: bold;" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="jmlracik" class="col-sm-3 col-form-label">Jumlah</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm" id="jmlracik" style="font-weight: bold;" onkeypress="return onlyNumberKey(event)">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Signa</label>
                        <div class="col-sm-6">
                            <select class="form-select form-select-sm master-signa" name="master-signa" id="master-signa">
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="buatracik(event)">Buat Racikan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>