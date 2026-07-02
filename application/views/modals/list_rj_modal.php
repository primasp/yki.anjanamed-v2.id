<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" data-bs-backdrop="static">
    <div class="modal-dialog " role="document" style="max-width: 75%;">



        <!-- <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false"> -->
        <!-- <div class="modal-dialog modal-xl"> -->
        <!-- <div class="modal-dialog modal-xl"> -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Daftar Pasien Sudah Daftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">

                    <div class="col-md-4">
                        <label for="filterTanggalBook">Tanggal Daftar</label>
                        <input type="date" id="filterTanggalBook" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="filterPoliBook">Poli</label>
                        <select id="filterPoliBook" class="form-control">
                            <option value="">Semua Poli</option>

                            <?php foreach ($this->db->get('pc01_med_poli_ms')->result() as $poli) : ?>
                                <option value="<?= $poli->poli_id ?>"><?= $poli->keterangan ?></option>
                            <?php endforeach; ?>



                            <!-- Opsi Poli dinamis diisi via JS/AJAX -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterDokterBook">Dokter</label>
                        <select id="filterDokterBook" class="form-control">
                            <option value="">Semua Dokter</option>

                            <!-- Opsi Dokter dinamis diisi via JS/AJAX -->
                        </select>
                    </div>
                </div>







                <div class="table-responsive mt-3">
                    <table class="table table-striped table-hover table-bordered border-primary align-middle text-center" id="tabelBooking">
                        <thead class="table-primary text-uppercase">
                            <tr>
                                <th scope="col">Episode ID</th>
                                <th scope="col">Nama</th>
                                <th scope="col">No. RM</th>
                                <th scope="col">No. BPJS</th>


                                <th scope="col">Tgl Masuk</th>

                                <th scope="col">Poli</th>
                                <th scope="col">Dokter</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data diisi via JavaScript -->
                        </tbody>
                    </table>
                </div>










            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>