<div class="content" style="height: 100vh;  display: flex; flex-direction: column;">
    <div class="page-header mb-0">
        <div class="row align-items-center g-1 pb-2">
            <div class="col-sm-4 col-md-4 col-lg-5">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">History</li>
                </ul>
            </div>
        </div>
        <div class="row align-items-center g-1">
            <div class="col-12 text-center">
                <h4 class="blinking text-danger fw-bold " style="display: none;">Resep Belum Terkirim !!</h4>
            </div>
        </div>
    </div>
    <div class="row flex-grow-1">
        <div class="col-sm-4 col-md-4 col-lg-3" id="listHistory">
            <div class="card chat-box-clinic ">
                <div class="chat-widgets" style="height: 100vh; overflow-y: auto;">
                    <div class="card-header" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                        <h5 class="sub-title" style="font-size: 14px; display: inline-block;">
                            History Kunjungan Pasien
                        </h5>
                        <button id="toggleHidePasien" style="float: right; font-size: 10px; border: none; background: none;color: white; cursor: pointer;">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                    <div class="chat-user-group-head d-flex align-items-center">
                        <div class="img-users call-user">
                            <a href="profile.html"><img src="<?= base_url('assets/img/profiles/avatar-01.jpg') ?>" alt="img"></a>
                            <span class="active-users"></span>
                        </div>
                        <div class="chat-users user-main">
                            <div class="user-titles">
                                <h5></h5>
                                <h4 id="dokterId" class="text-start" style="display:none;"></h4>
                                <div class="chat-user-time">
                                    <p>Doctor</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="top-liv-search top-chat-search">
                        <form id="searchForm" onsubmit="return false;">
                            <div class="chat-search">
                                <div class="input-block me-2 mb-0">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search here" onkeyup="filterPatients()" autocomplete="off">
                                    <button type="button" class="btn">
                                        <img src="<?= base_url('assets/img/icons/search-normal.svg') ?>" alt="Search">
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="listKunjungan">
                        <div class="nav flex-column nav-pills p-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <?php if (!empty($listKunjungan)) : ?>
                                <?php foreach ($listKunjungan as $kunjPasien) : ?>
                                    <a class="nav-link mb-1 <?= ($kunjPasien->episode_id == $selected_episode_id) ? 'active show' : '' ?>" data-episode-id="<?= $kunjPasien->episode_id ?>" data-pasien-id="<?= $kunjPasien->pasien_id ?>" role="tab" style="display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 5px 10px; font-size: 13px; line-height: 1.2; height: 35px;">
                                        <span style="width: 33.33%; text-align: left;"><?= formatTanggalIndonesia($kunjPasien->tgl_masuk) ?></span>
                                        <span style="width: 33.33%; text-align: center;"><?= htmlspecialchars($kunjPasien->nama_poli) ?></span>
                                        <span style="width: 33.33%; text-align: right;"><?= htmlspecialchars($kunjPasien->nama_dokter) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="text-center">No Patients Found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-md-8 col-lg-9" id="detilHistory">
        </div>
    </div>
</div>