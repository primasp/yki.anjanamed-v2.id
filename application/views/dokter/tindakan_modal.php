<!-- tindakan_modal.php - YKI Tahap 2 -->

<div class="modal fade dokter-action-modal" id="modal_tindakan" tabindex="-1" role="dialog" aria-labelledby="modalTindakanLabel" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content dokter-modal-content">
            <div class="modal-header dokter-modal-header">
                <div>
                    <h5 class="modal-title" id="modalTindakanLabel">
                        <i class="fas fa-notes-medical me-2"></i>Tindakan Umum
                    </h5>
                    <small>Khusus layanan kategori JKL-UMU. Lab/Radiologi diinput dari panel Penunjang Dokter.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body dokter-modal-body">
                <div class="doctor-patient-strip mb-3">
                    <div>
                        <span>MRN</span>
                        <strong><input type="text" class="rm_pasien" id="rm_pasien2" name="rm_pasien2" readonly></strong>
                    </div>
                    <div>
                        <span>Nama</span>
                        <strong><input type="text" class="nama_pasien" id="nama_pasien4" name="nama_pasien4" readonly></strong>
                    </div>
                    <div>
                        <span>Jenis Kelamin</span>
                        <strong><input type="text" class="jk_pasien" id="jk_pasien2" name="jk_pasien2" readonly></strong>
                    </div>
                    <div>
                        <span>Provider</span>
                        <strong><input type="text" class="prov_pasien" id="prov_pasien4" name="prov_pasien4" readonly></strong>
                    </div>
                    <div>
                        <span>Tgl Lahir</span>
                        <strong><input type="text" class="lahir_pasien" id="lahir_pasien3" name="lahir_pasien3" readonly></strong>
                    </div>
                    <div>
                        <span>Kunjungan</span>
                        <strong><input type="text" class="kunj_ke" id="kunj_ke4" name="kunj_ke4" readonly></strong>
                    </div>
                </div>

                <div class="alert alert-info d-flex align-items-start gap-2 py-2">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div>
                        <strong>Catatan alur baru:</strong>
                        Tindakan yang dicari di sini hanya tindakan umum. Layanan Papsmear, Mammografi, dan USG tidak muncul di menu ini.
                    </div>
                </div>

                <div class="doctor-inline-form mb-3">
                    <div class="flex-grow-1">
                        <label for="pencarian_tindakan" class="form-label">Nama Tindakan</label>
                        <input type="text" class="form-control form-control-sm" id="pencarian_tindakan" name="pencarian_tindakan" placeholder="Klik tambah/cari untuk memilih tindakan umum">
                    </div>
                    <div style="width:110px">
                        <label class="form-label">Jumlah</label>
                        <input type="number" min="1" class="form-control form-control-sm" id="jumlah_tindakan" name="jumlah_tindakan" value="1">
                    </div>
                    <div class="pt-4">
                        <button type="button" class="btn btn-primary" id="btn_tambah_tindakan" data-bs-toggle="modal" data-bs-target="#modal_caritindakan">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>

                    <input type="hidden" id="nama_tindakan" name="nama_tindakan">
                    <input type="hidden" id="kode_tindakan" name="kode_tindakan">
                    <input type="hidden" id="harga_tindakan" name="harga_tindakan">
                </div>

                <div class="table-responsive dokter-table-wrap">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Tindakan</th>
                                <th class="text-center" style="width: 15%">Jumlah</th>
                                <th class="text-end" style="width: 20%">Total</th>
                                <th class="text-center" style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="listtindakan"></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer dokter-modal-footer">
                <button type="button" class="btn btn-primary" id="btn-simpan-tindakan" onclick="simpantindakan(event)">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade dokter-action-modal" id="modal_caritindakan" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content dokter-modal-content">
            <div class="modal-header dokter-modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-search me-2"></i>Cari Tindakan Umum</h5>
                    <small>Master yang tampil sudah dibatasi kategori JKL-UMU.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body dokter-modal-body">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label for="input_caritindakan" class="form-label">Cari tindakan</label>
                        <input class="form-control form-control-sm" id="input_caritindakan" type="text" name="input_caritindakan" placeholder="Ketik nama tindakan umum...">
                    </div>
                </div>

                <div class="table-responsive dokter-table-wrap">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tindakan</th>
                                <th class="text-end" style="width: 25%">Harga</th>
                            </tr>
                        </thead>
                        <tbody id="listmastertindakan"></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer dokter-modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>