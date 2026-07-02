<!-- dokter/dokter_dashboard.php -->

<div class="content" style="height: 100vh;  display: flex; flex-direction: column;">

    <!-- Page Header -->
    <div class="page-header mb-0">
        <div class="row align-items-center g-1 pb-2">
            <div class="col-sm-4 col-md-4 col-lg-5 d-flex align-items-center">
                <ul class="breadcrumb mb-0 me-2">
                    <li class="breadcrumb-item active">Pemeriksaan Dokter</li>
                </ul>
                <button onclick="tampildeviceid()" class="btn btn-outline-secondary btn-sm" title="Info Device ID">
                    <i class="fas fa-info-circle fa-lg"></i>
                </button>
            </div>

            <div class="col-sm-8 col-md-8 col-lg-7 text-end">
                <button id="btn_sejarah" class="btn btn-primary mb-2"><i class="fa-solid fa-syringe"></i> Sejarah</button>
                <button id="btn_tindakan" class="btn btn-primary mb-2"><i class="fa-solid fa-syringe"></i> Tindakan</button>
                <button id="btn_resep" class="btn btn-primary mb-2"><i class="fa-solid fa-mortar-pestle"></i> Resep</button>
                <button id="btn_selesai" class="btn btn-primary mb-2"><i class="fas fa-save"></i> Selesai</button>
            </div>
        </div>

        <div class="row align-items-center g-1">
            <div class="col-12 text-center">
                <h4 class="blinking text-danger fw-bold" style="display: none;">Resep Belum Terkirim !!</h4>
            </div>
        </div>
    </div>

    <!-- /Page Header -->

    <div class="row flex-grow-1">
        <!-- <div class="col-md-3" id="listpasien"> -->
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-3" id="listpasien">
            <div class="card chat-box-clinic ">
                <div class="chat-widgets" style="height: 200vh; overflow-y: auto;">

                    <div class="card-header" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                        <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                            List Pasien
                        </h5>
                        <button id="toggleHidePasien" style="float: right; font-size: 10px; border: none; background: none;color: white; cursor: pointer;">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>






                    <div class="chat-user-group-head d-flex align-items-center">
                        <div class="img-users call-user">
                            <a href="profile.html"><img src="assets/img/profiles/avatar-01.jpg" alt="img"></a>
                            <span class="active-users"></span>
                        </div>
                        <div class="chat-users user-main">
                            <div class="user-titles">
                                <h5><?= $namadokter['nama']; ?></h5>
                                <h4 id="dokterId" class="text-start" style="display:none;"><?= $iddokter; ?></h4>
                                <div class="chat-user-time">
                                    <p>Doctor</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="top-liv-search top-chat-search">
                                <form id="searchForm" onsubmit="return false;">
                                    <div class="chat-search">
                                        <div class="input-block me-2 mb-0">
                                            <input type="text" id="searchInput" class="form-control" placeholder="Search here" onkeyup="filterPatients()" autocomplete="off">
                                            <button type="button" class="btn">
                                                <img src="assets/img/icons/search-normal.svg" alt="Search">
                                            </button>


                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-4 pt-3">

                            <input type="date" id="filterTanggal" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>">

                        </div>
                        <div class="col-2 pt-4">

                            <button id="reloadButton" class="btn btn-outline-secondary w-80" title="Reload Page">
                                <i class="fa fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="legend-container border-top py-2" style="background-color: rgb(178, 199, 221); position: sticky; bottom: 0; width: 100%; color: white; font-size: 14px;">
                        <div class="container">
                            <div class="row text-center align-items-center">
                                <div class="col-md-4 col-12 d-flex align-items-center justify-content-center py-1" title="Resep belum terkirim">
                                    <i class="fa-solid fa-prescription-bottle text-danger" style="font-size: 20px; margin-right: 8px;"></i>
                                    <span>Resep </span>
                                </div>

                                <div class="col-md-4 col-12 d-flex align-items-center justify-content-center py-1" title="Selesai periksa">
                                    <i class="fa-solid fa-check-circle text-success" style="font-size: 20px; margin-right: 8px;"></i>
                                    <span>Selesai</span>
                                </div>

                                <div class="col-md-4 col-12 d-flex align-items-center justify-content-center py-1" title="Belum selesai periksa">
                                    <i class="fa-solid fa-user-clock text-black" style="font-size: 20px; margin-right: 8px;"></i>
                                    <span>Menunggu</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php if (count($listPoli) > 1) : ?>
                        <ul class="nav nav-tabs" id="poliTabs">
                            <?php foreach ($listPoli as $index => $poli) : ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $index == 0 ? 'active' : '' ?>" data-poli="<?= $poli->poli_id ?>" href="#">
                                        <?= $poli->nama_poli ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>


                    <div id="patientList" class="pt-3">
                        <?php if (!empty($listPasien)) : ?>
                            <?php foreach ($listPasien as $pasien) : ?>
                                <div class="chat-user-group d-flex align-items-center m-0 patient-item" data-episode-id="<?= htmlspecialchars($pasien->episode_id); ?>" data-pasien-id="<?= htmlspecialchars($pasien->pasien_id); ?>" data-poli-id="<?= htmlspecialchars($pasien->poli_id); ?>">

                                    <div class="chat-users">
                                        <div class="user-titles d-flex flex-column">

                                            <!-- NAMA PASIEN -->
                                            <h5 class="patient-name" style="color:
                                <?= $pasien->status_periksa == '3' ? 'red' : ($pasien->status_periksa == '1' ? 'green' : 'black'); ?>;">
                                                <?= htmlspecialchars($pasien->nama); ?>
                                            </h5>

                                            <!-- DETAIL POLI / URUT / JENIS KELAMIN / REKANAN -->
                                            <div class="chat-user-time">
                                                <p class="small d-flex flex-wrap align-items-center" style="color:
                                <?= $pasien->status_periksa == '3' ? 'red' : ($pasien->status_periksa == '1' ? 'green' : 'black'); ?>;">

                                                    <span class="me-2 fw-bold text-primary">
                                                        <?= htmlspecialchars($pasien->nama_poli); ?>
                                                    </span>

                                                    <span class="me-2">|</span>

                                                    <span class="me-2">
                                                        No: <?= htmlspecialchars($pasien->urut); ?>
                                                    </span>

                                                    <span class="me-2">|</span>

                                                    <span class="me-2">
                                                        <?= htmlspecialchars($pasien->jenis_kelamin); ?>
                                                    </span>

                                                    <span class="me-2">|</span>

                                                    <span>
                                                        <?= htmlspecialchars($pasien->rekanan_id); ?>
                                                    </span>
                                                </p>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <div class="chat-user-group d-flex align-items-center m-0">
                                <div class="chat-users">
                                    <div class="user-titles">
                                        <h5>No Patients Found</h5>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        </div>


        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-9" id="pemeriksaan">
            <!-- <div class="col-md-9" id="pemeriksaan" style="height: 100vh; "> -->

            <div class="row">
                <div class="card chat-box-clinic ">
                    <div class="chat-widgets">

                        <div class="card-header" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                            <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                                Identitas Pasien
                            </h5>

                            <button id="toggleViewPasien" style="float: left; font-size: 10px; border: none; background: none;color: white; cursor: pointer;">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="row gy-2">
                                <!-- MRN -->
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="row align-items-center">
                                        <label for="rm_pasien" class="col-4 form-label text-end pt-2">MRN :</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm rm_pasien" id="rm_pasien" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="col-12 col-md-6">
                                    <div class="row align-items-center">
                                        <label for="jk_pasien1" class="col-4 form-label text-end pt-2">Jenis Kelamin :</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm jk_pasien" id="jk_pasien1" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama -->
                                <div class="col-12 col-md-6">
                                    <div class="row align-items-center">
                                        <label for="nama_pasien" class="col-4 form-label text-end pt-2">Nama :</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm nama_pasien" id="nama_pasien" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Penjamin -->
                                <div class="col-12 col-md-6">
                                    <div class="row align-items-center">
                                        <label for="prov_pasien" class="col-4 form-label text-end pt-2">Penjamin :</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm prov_pasien" id="prov_pasien" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tgl Lahir -->
                                <div class="col-12 col-md-6">
                                    <div class="row align-items-center">
                                        <label for="lahir_pasien" class="col-4 form-label text-end pt-2">Tgl Lahir :</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm lahir_pasien" id="lahir_pasien" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Kunjungan Ke -->
                                <div class="col-12 col-md-6">
                                    <div class="row align-items-center">
                                        <label for="kunj_ke1" class="col-4 form-label text-end pt-2">Kunjungan Ke :</label>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm kunj_ke" id="kunj_ke1" style="border: none; border-bottom: 1px solid #000; border-radius: 0;">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row d-none">
                                <div class="col-12">
                                    <input type="hidden" name="idlokasi" class="idlokasi">
                                    <input type="hidden" name="idepisode" class="idepisode">
                                    <input type="hidden" name="idtrans" class="idtrans">
                                    <input type="hidden" name="idpasien" class="idpasien">
                                    <input type="hidden" name="idtransco" class="idtransco">
                                    <input type="hidden" name="idpoli" class="idpoli">
                                    <input type="hidden" name="iddokter" class="iddokter" value="<?= $iddokter; ?>">
                                    <input type="hidden" name="idrekanan" class="idrekanan">
                                    <input type="hidden" name="tanggalpoli" class="tanggalpoli">
                                    <input type="hidden" name="nokartuprov" class="nokartuprov">
                                </div>
                            </div>

                        </div>



                        <div class="card-header mt-3" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                            <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                                Riwayat Alergi
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- Alergi Makanan -->
                                <div class="col-md-4">
                                    <label for="alergiMakanan" class="form-label fw-bold">Alergi Makanan</label>
                                    <!-- <textarea class="form-control" id="alergi_makanan" name="alergi_makanan" rows="4" placeholder="Contoh: Udang, Kacang..."></textarea> -->
                                    <select id="alergiMakanan" class="form-select"></select>
                                </div>

                                <!-- Alergi Udara -->
                                <div class="col-md-4">
                                    <label for="alergiUdara" class="form-label fw-bold">Alergi Udara</label>
                                    <!-- <textarea class="form-control" id="alergi_udara" name="alergi_udara" rows="4" placeholder="Contoh: Debu, Serbuk Sari..."></textarea> -->
                                    <select id="alergiUdara" class="form-select"></select>
                                </div>

                                <!-- Alergi Obat-obatan -->
                                <div class="col-md-4">
                                    <label for="alergiObat" class="form-label fw-bold">Alergi Obat-obatan</label>
                                    <!-- <textarea class="form-control" id="alergi_obat" name="alergi_obat" rows="4" placeholder="Contoh: Penicillin, Aspirin..."></textarea> -->
                                    <select id="alergiObat" class="form-select"></select>
                                </div>
                            </div>
                        </div>
















                        <div class="card-header mt-3" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                            <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                                Pemeriksaan
                            </h5>
                        </div>







                        <div class="card-body">
                            <div class="row">
                                <!-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" style="overflow-y: auto; height: 65vh;"> -->
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <div class="d-flex justify-content-center mb-3">
                                        <button class="btn btn-success" id="btn_mulaiperiksa">Mulai Periksa</button>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="subject" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">S</label>
                                        <div class="col-10">
                                            <textarea name="subject" id="subject" rows="3" class="form-control form-control-sm readonly-style" readonly></textarea>
                                        </div>
                                    </div>



                                    <div class="row mb-3 align-items-center">
                                        <label for="objective" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">O</label>
                                        <div class="col-10">
                                            <textarea name="objective" id="objective" rows="5" class="form-control form-control-sm readonly-style" readonly></textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="assesment" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">A</label>
                                        <div class="col-10">
                                            <textarea name="assesment" id="assesment" rows="3" class="form-control form-control-sm readonly-style" readonly></textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="planning" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">P</label>
                                        <div class="col-10">
                                            <textarea name="planning" id="planning" rows="3" class="form-control form-control-sm readonly-style" readonly></textarea>
                                        </div>
                                    </div>




                                    <div class="d-flex justify-content-center">
                                        <button class="btn btn-success mb-3" id="btn_simpansoap">Simpan SOAP</button>
                                    </div>





















                                    <div class="card" style="margin-top: 0 !important; padding-top: 0 !important;">


                                        <div class="card-header mt-0" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 25px;">
                                            <h6 class="sub-title pb-0" style="display: inline-block; font-size: 14px;">
                                                ICD10
                                            </h6>
                                        </div>





                                        <div class="card-body fw-bold">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label for="cari_nama_icd10" class="form-label">ICD 10 Utama</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4 col-md-4 col-lg-2">
                                                    <input class="form-control form-control-sm readonly-style" id="cari_kode_icd10" type="text" name="cari_kode_icd10" readonly>

                                                </div>
                                                <div class="col-sm-8 col-md-8  col-lg-10">
                                                    <input class="form-control form-control-sm readonly-style" id="cari_nama_icd10" type="text" name="cari_nama_icd10" readonly>
                                                    <input class="form-control readonly-style" id="icd10_kode" type="hidden" name="icd10_kode" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4 col-md-4 col-lg-2">
                                                    <input id="diag_non_spesialis" type="hidden" name="diag_non_spesialis">
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">ICD 10 Sekunder</label>
                                                </div>
                                                <div class="col-12">
                                                    <input class="form-control form-control-sm readonly-style" id="cari_kode_icd10_sek" type="text" name="cari_kode_icd10_sek" readonly>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    <tbody id="listicd10"></tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card" style="margin-top: 0 !important; padding-top: 0 !important;">

                                        <div class="card-header mt-0" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 25px;">
                                            <h5 class="sub-title pb-0" style=" display: inline-block;">
                                                ICD9
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group row fw-bold mb-3">
                                                <!-- Menambahkan margin bawah -->
                                                <div class="col-sm-12">
                                                    <label class="col-form-label fw-bold" style="font-size: 12px;">ICD 9</label>
                                                </div>
                                                <div class="col-sm-12">
                                                    <input class="form-control form-control-sm readonly-style" id="cari_kode_icd9" type="text" name="cari_kode_icd9" readonly>
                                                </div>
                                            </div>
                                            <div class="card-body table-responsive p-0">
                                                <table class="table small table-head-fixed table-borderless text-nowrap">
                                                    <tbody id="listicd9" style="line-height: 1.2; padding: 0;">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <!-- <div class="col-sm-0 col-md-0 col-lg-12 col-xl-12" style="overflow-y: auto; height: 65vh;"> -->
                                <div class="col-sm-0 col-md-0 col-lg-12 col-xl-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card" style="margin-top: 0 !important; padding-top: 0 !important;">
                                                <div class="card-header " style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                                                    <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                                                        Pemeriksaan Lainnya
                                                    </h5>
                                                </div>
                                                <div class="card-body " style="font-size: 12px;">
                                                    <div class="form-group row align-items-center ">
                                                        <label class="col-sm-12 col-md-4 col-form-label">Status Pulang</label>
                                                        <div class="col-sm-0 col-md-8">
                                                            <div class="row align-items-center mt-1 p-3">
                                                                <select id="statPlgGetApi" class="form-select"></select>
                                                            </div>
                                                        </div>

                                                        <label class="col-sm-12 col-md-4  col-form-label">Pilih Kesadaran</label>
                                                        <div class="col-sm-0 col-md-8">
                                                            <div class="row align-items-center mt-1 p-3">
                                                                <select id="sadarGetApi" class="form-select"></select>
                                                            </div>
                                                        </div>

                                                        <label class="col-sm-12 col-md-4  col-form-label">Pilih Prognosa</label>
                                                        <div class="col-sm-0 col-md-8 mb-1">
                                                            <div class="row align-items-center mt-1 p-3">
                                                                <select id="plhPrognosaApi" class="form-select"></select>
                                                            </div>
                                                        </div>


                                                    </div>




                                                </div>
                                            </div>



                                        </div>
                                        <div class="col-6">
                                            <div class="card" style="margin-top: 0 !important; padding-top: 0 !important;">

                                                <div class="card-header " style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                                                    <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                                                        Perkiraan Harga
                                                    </h5>
                                                </div>
                                                <div class="card-body table-responsive p-0 mt-2" style="height: 200px;">
                                                    <table class="table table-sm small table-hover table-head-fixed text-nowrap">
                                                        <thead class="text-center" style="font-size: 12px;">
                                                            <tr>
                                                                <th style="width: 50%">Item</th>
                                                                <th style="width: 20%">Harga</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="listitemharga"></tbody>
                                                    </table>
                                                </div>
                                                <strong style="display: block; text-align: right;" id="hasiltotal">
                                                    TOTAL HARGA :
                                                </strong>

                                            </div>
                                        </div>
                                    </div>





                                </div>
                            </div>
                        </div>



                    </div>
                </div>



























            </div>
        </div>



    </div>
</div>