<!-- Modal Cari Resep-->
<div class="modal fade" id="modal_cariresep" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <!-- <div class="modal-dialog modal-xl" role="document"> -->
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- <div class="modal-header"> -->
            <div class="modal-header bg-primary text-white">
                <!-- <h5 class="modal-title" id="exampleModalLabel">Daftar Resep</h5> -->
                <h5 class="modal-title" id="modalCariresepLabel"><i class="fas fa-prescription-bottle-alt"></i> Daftar Resep</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="input_pencarian" name="pencarian" placeholder="Pencarian RM / Nama Pasien" style="width: 40%;">
                            <input type="date" class="form-control form-control-sm" id="tanggal_cari" name="tanggal_cari" style="width: 10%;">
                            <button id="btn-caripasien" class="btn btn-warning btn-sm" type="button">
                                <i class="fa fa-search"></i> Cari
                            </button>
                            <button id="btn-clear" class="btn btn-secondary btn-sm" type="button">
                                <i class="fa fa-times"></i> Clear All
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="idepisode" class="idepisode">
                <input type="hidden" name="idpasien" class="idpasien">
                <input type="hidden" name="idtransco" class="idtransco">


                <div class="row mb-4">
                    <div class="col-sm-12">
                        <!-- <div class="card"> -->
                        <div class="card shadow-sm">

                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-users"></i> List Pasien</h6>
                            </div>




                            <div class="card-body">
                                <!-- <h5 class="sub-title">
                                    <i class="fas fa-id-card"></i> List Pasien
                                </h5> -->
                                <!-- <div class="table-responsive"> -->
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">

                                    <!-- <table class="table table-hover table-sm text-nowrap"> -->
                                    <table class="table table-hover table-sm text-nowrap align-middle table-bordered table-striped">


                                        <!-- <thead class="text-center"> -->
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>No Resep</th>
                                                <th>Tanggal</th>
                                                <th>No. RM</th>
                                                <th>Nama Pasien</th>
                                                <th>Tgl Lahir</th>
                                                <th>Umur</th>
                                                <th>Provider</th>
                                                <th>Poli</th>
                                                <th>Dokter</th>
                                                <th>Resep Ke</th>
                                            </tr>
                                        </thead>
                                        <tbody id="listpasien2"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <!-- <div class="card"> -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-pills"></i> Detail Resep</h6>
                            </div>
                            <div class="card-body">
                                <!-- <h5 class="sub-title">
                                    <i class="fas fa-id-card"></i> Detail Resep
                                </h5> -->
                                <!-- <div class="table-responsive"> -->
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-hover table-sm text-nowrap">

                                        <!-- <thead class="text-center"> -->
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th></th>
                                                <th>Nama Obat</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Dosis</th>
                                                <th>Signa</th>
                                                <th>Signa Teks</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="listresepall"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" id="btn-ambilresep" onclick="ambilresep(event)"><i class="fas fa-check"></i> Ambil Resep</button>
                <!-- <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button> -->

                <!-- <button type="button" class="btn btn-primary btn-sm" id="btn-ambilresep" onclick="ambilresep(event)">
                    <i class="fas fa-check"></i> Ambil Resep
                </button> -->
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Tambah Obat -->
<div class="modal fade" id="modal_cariobat" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCariObatLabel"><i class="fas fa-pills"></i> Master Obat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form Modal -->
            <form id="formcariobat" class="form-horizontal" method="POST">
                <div class="modal-body">
                    <!-- Pencarian Obat -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="input_cariobat" name="cariobat" placeholder="Pencarian obat">
                                <button id="cari-obat" class="btn btn-warning btn-sm" type="button">
                                    <i class="fa fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="hidden" name="idlokasi" class="idlokasi">
                    <input type="hidden" name="idpasien" class="idpasien">
                    <input type="hidden" name="idalergi" class="idalergi">

                    <!-- List Obat -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-capsules"></i> Daftar Obat</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-hover table-sm text-nowrap">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th>Nama Obat</th>
                                                    <th>Stok</th>
                                                </tr>
                                            </thead>
                                            <tbody id="listobatms"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Racikan -->
<div class="modal fade" id="modal_buatracik" tabindex="-1" aria-labelledby="modalBuatracikLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBuatracikLabel">
                    <i class="fas fa-flask"></i> Racikan Obat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formracikobat" class="form-horizontal" method="POST">
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="namaracik" class="col-sm-3 col-form-label text-end">Nama Racikan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="namaracik" placeholder="Masukkan nama racikan" style="font-weight: bold;">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label text-end">Pembungkus</label>
                        <div class="col-sm-4">
                            <select class="form-select form-select-sm" id="master-bungkus" onchange="pilihpembungkus(this)">
                                <option value="" disabled selected>Pilih Pembungkus</option>
                            </select>
                        </div>
                        <div class="col-sm-1 d-flex align-items-center justify-content-center">
                            <span class="text-secondary">atau</span>
                        </div>
                        <label for="stokbungkus" class="col-sm-2 col-form-label text-end">Stok</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm text-center" id="stokbungkus" readonly style="font-weight: bold;">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="jmlracik" class="col-sm-3 col-form-label text-end">Jumlah</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm text-center" id="jmlracik" placeholder="0" onkeypress="return onlyNumberKey(event)" style="font-weight: bold;">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label text-end">Signa</label>
                        <div class="col-sm-6">
                            <select class="form-select form-select-sm" id="master-signa">
                                <option value="" disabled selected>Pilih Signa</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-success btn-sm px-4" data-bs-dismiss="modal" id="btn-buatracik" onclick="buatracik(event)">
                            <i class="fas fa-mortar-pestle"></i> Racik
                        </button>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>