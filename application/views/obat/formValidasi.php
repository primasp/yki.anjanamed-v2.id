<div class="content" style="height: 100vh;  display: flex; flex-direction: column;">

    <!-- Page Header -->
    <div class="page-header mb-0">
        <div class="row align-items-center mb-3">
            <div class="col-sm-5">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Validasi Resep</li>
                </ul>
            </div>


        </div>
        <div class="row align-items-center g-1">
            <div class="col-sm-2">
                <button id="btn_cari" class="btn btn-primary w-100 mb-2"> <i class="fa-solid fa-syringe"></i>
                    <span id="btnText">Buka Resep</span>
                </button>
            </div>
            <div class="col-sm-2">
                <button id="btn_batal" class="btn btn-primary w-100 mb-2"> <i class="fa-solid fa-syringe"></i>
                    <span id="btnText">Batal</span>
                </button>
            </div>
            <div class="col-sm-2">
                <button id="btn_simpan" class="btn btn-primary w-100 mb-2"> <i class="fa-solid fa-syringe"></i>
                    <span id="btnText">Validasi</span>
                </button>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row mb-3">


        <div class="col-md-3">
            <div class="card shadow-sm mb-3">
                <div class="card-header text-center py-2 text-white" style="background-color: rgb(18, 3, 102);">
                    <h5 class="sub-title mb-0" style="font-size: 14px;">Data Pasien</h5>
                </div>
                <div class="card-body p-2">
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="idlokasi" class="idlokasi">
                    <input type="hidden" name="idepisode" class="idepisode">
                    <input type="hidden" name="idtrans" class="idtrans">
                    <input type="hidden" name="idpasien" class="idpasien">
                    <input type="hidden" name="idtransco" class="idtransco">
                    <input type="hidden" name="tglresep" class="tglresep">
                    <input type="hidden" name="resepke" class="resepke">

                    <!-- Table Container -->
                    <div class="table-responsive" style="max-height: 30vh; overflow-y: auto;">
                        <table class="table table-sm table-borderless text-nowrap mb-0">
                            <tbody id="resultdatapasien"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>






        <div class="col-md-9">
            <div class="card chat-box-clinic mb-1">
                <div class="chat-widgets" style="height: 30vh; overflow-y: auto;">
                    <div class="card-header" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                        <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                            List Pasien
                        </h5>

                    </div>


                    <div class="card-body">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-4" id="tabMenu" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active d-flex align-items-center" id="tabresepdokter-tab" data-bs-toggle="tab" data-bs-target="#tabresepdokter" type="button" role="tab" aria-controls="tabresepdokter" aria-selected="true">
                                    <span class="set-about-icon me-2">
                                        <img src="assets/img/icons/menu-icon-02.svg" alt="Resep Dokter Icon" width="20">
                                    </span>
                                    <span>Resep Dokter</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="tabriwayatobat-tab" data-bs-toggle="tab" data-bs-target="#tabriwayatobat" type="button" role="tab" aria-controls="tabriwayatobat" aria-selected="false">
                                    <span class="set-about-icon me-2">
                                        <img src="assets/img/icons/menu-icon-16.svg" alt="Riwayat Obat Icon" width="20">
                                    </span>
                                    <span>Riwayat Obat</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="tabriwayatalergi-tab" data-bs-toggle="tab" data-bs-target="#tabriwayatalergi" type="button" role="tab" aria-controls="tabriwayatalergi" aria-selected="false">
                                    <span class="set-about-icon me-2">
                                        <img src="assets/img/icons/menu-icon-16.svg" alt="Riwayat Alergi Icon" width="20">
                                    </span>
                                    <span>Riwayat Alergi</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="tabContent">
                            <!-- Resep Dokter Tab -->
                            <div class="tab-pane fade show active" id="tabresepdokter" role="tabpanel" aria-labelledby="tabresepdokter-tab">
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm table-striped table-hover">
                                        <thead class="table-primary text-center">
                                            <tr>
                                                <th>Keterangan</th>
                                                <th>Nama Obat</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Dosis</th>
                                                <th>Signa</th>
                                                <th>Signa Teks</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resultresepdokter"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Riwayat Obat Tab -->
                            <div class="tab-pane fade" id="tabriwayatobat" role="tabpanel" aria-labelledby="tabriwayatobat-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                            <table class="table table-sm table-striped table-hover">
                                                <thead class="table-success text-center">
                                                    <tr>
                                                        <th colspan="2">Riwayat Kunjungan</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="resultriwayattransaksi"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                            <table class="table table-sm table-striped table-hover">
                                                <thead class="table-success text-center">
                                                    <tr>
                                                        <th>Nomor Resep</th>
                                                        <th>Resep Ke</th>
                                                        <th>Nama Obat</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="resultriwayatobat"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Alergi Tab -->
                            <div class="tab-pane fade" id="tabriwayatalergi" role="tabpanel" aria-labelledby="tabriwayatalergi-tab">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                            <table class="table table-sm table-striped table-hover">
                                                <thead class="table-warning text-center">
                                                    <tr>
                                                        <th>Alergi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="resultalergi"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#formambilalergi" role="button">
                                            <i class="fas fa-plus-circle"></i> Tambah Alergi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>







                </div>
            </div>
        </div>
    </div>
    <div class="row flex-grow-1">
        <div class="col-md-12" id="listpasien">
            <div class="card chat-box-clinic mb-1">
                <div class="chat-widgets" style="height: 40vh; overflow-y: auto;">
                    <div class="card-header" style="margin: 2; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                        <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                            List Pasien
                        </h5>

                    </div>
                    <!-- <div class="chat-user-group-head d-flex align-items-center"> -->
                    <div class="row align-items-center m-3">
                        <div class="col-md-2  col-sm-4">
                            <button id="btn_cariobat" class="btn btn-primary w-100 mb-2 h"> <i class="fa-solid fa-syringe"></i>
                                <span id="btnText">Tambah Obat</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <button id="btn_bukaracik" class="btn btn-primary w-100 mb-2"> <i class="fa-solid fa-syringe"></i>
                                <span id="btn_bukaracik">Racikan</span>
                            </button>
                        </div>

                    </div>




                    <div class="card-body" style="line-height: 0.3;">
                        <table class="table small table-head-fixed text-nowrap">
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
                                    <th>Dosis Obat</th>
                                    <th>Signa</th>
                                    <th>Signa Teks</th>
                                    <th>Catatan</th>
                                    <th>Harga Satuan</th>
                                    <th>Total harga</th>
                                </tr>
                            </thead>
                            <tbody id="resultvalidasiobat"></tbody>
                        </table>
                        <div class="col-md-12 text-right" style="font-size: 20px; font-weight: bold;">
                            <label for="totalharga">TOTAL HARGA : </label>
                            <input type="text" id="totalharga" readonly style="border: none; text-align: right; font-weight: bold;">
                        </div>

                    </div>



                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
</div>