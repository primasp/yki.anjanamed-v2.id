$(document).ready(function () {
  console.log("Resume RJ JS loaded");

  // document
  //   .getElementById("editAsesmentBtn")
  //   .addEventListener("click", function () {
  //     document
  //       .querySelectorAll(".readonly-style")
  //       .forEach((el) => el.removeAttribute("readonly"));
  //     document.getElementById("updateDataBtn").style.display = "block";
  //   });

  $(".select2").select2();

  // $(".datetimepicker").datepicker({ format: "dd/mm/yyyy" });

  let today = new Date();
  let formattedDate =
    ("0" + today.getDate()).slice(-2) +
    "/" +
    ("0" + (today.getMonth() + 1)).slice(-2) +
    "/" +
    today.getFullYear();

  $("#tgl_Mulai, #tgl_selesai").val(formattedDate);

  // Event saat Poliklinik dipilih
  $("#Poliklinik").change(function () {
    let poli_id = $(this).val();
    loadDokter(poli_id);
  });

  // **Memicu event change pada Poliklinik saat pertama kali halaman dimuat**
  $("#Poliklinik").trigger("change");

  // Event klik tombol cari
  $("#btnCari").click(function () {
    let poliklinik = $("#Poliklinik").val();
    let dokter = $("#Dokter").val();
    let statusBayar = $("#StatusByr").val();
    let statusPulang = $("#StatusPlg").val();
    let tanggalMulai = $("#tgl_Mulai").val();
    let tanggalSelesai = $("#tgl_selesai").val();
    console.log("poliklinik :" + poliklinik);
    console.log("dokter :" + dokter);
    console.log("statusBayar :" + statusBayar);
    console.log("statusPulang :" + statusPulang);
    console.log("tanggalMulai :" + tanggalMulai);
    console.log("tanggalSelesai :" + tanggalSelesai);

    $.ajax({
      url: BASE_URL + "ResumeController/cariData",
      type: "POST",
      data: {
        poliklinik: poliklinik,
        dokter: dokter,
        statusBayar: statusBayar,
        statusPulang: statusPulang,
        tanggalMulai: tanggalMulai,
        tanggalSelesai: tanggalSelesai,
      },
      dataType: "json",
      success: function (response) {
        let resultTable = $("#resultTable");
        resultTable.empty();

        if (response.status === "success" && response.data.length > 0) {
          response.data.forEach(function (row) {
            let statusbayartext = "";
            if (row.statusbayar === "00") {
              statusbayartext = "BELUM LUNAS";
            } else if (row.statusbayar === "55") {
              statusbayartext = "LUNAS";
            } else if (row.statusbayar === "99") {
              statusbayartext = "BATAL";
            } else {
              statusbayartext = "-";
            }

            // Mapping Kondisi Pulang
            let kondisiPulangText = "";
            if (row.kondisi_pulang === "3") {
              kondisiPulangText = "Rawat Jalan";
            } else if (row.kondisi_pulang === "4") {
              kondisiPulangText = "Rujuk";
            } else {
              kondisiPulangText = "-";
            }

            resultTable.append(`
                    <tr>
                        <td>${row.tanggal}</td>
                        <td>${row.pasien}</td>
                        <td>${row.poli}</td>
                        <td>${row.dokter}</td>
                        <td>${statusbayartext}</td>
                        <td>${kondisiPulangText}</td>
                        <td>
                          <button class="btn btn-info btn-sm" onclick="viewHistory('${row.episode_id}', '${row.pasien_id}')"><i class="feather-eye"></i> Detail</button>
                        </td>
                      </tr>
                  `);
          });
        } else {
          resultTable.append(
            '<tr><td colspan="7" class="text-center">Data tidak ditemukan</td></tr>'
          );
        }
      },
      error: function () {
        alert("Gagal mengambil data");
      },
    });
  });

  function loadDokter(poli_id = "") {
    $.ajax({
      url: BASE_URL + "ResumeController/getDokterByPoli",
      type: "POST",
      data: { poli_id: poli_id },
      dataType: "json",
      success: function (data) {
        let dokterSelect = $("#Dokter");
        console.log(dokterSelect);

        dokterSelect.empty();
        dokterSelect.append('<option value="">-- Pilih Dokter --</option>');

        $.each(data, function (index, item) {
          dokterSelect.append(
            `<option value="${item.dokter_id}">${item.nama}</option>`
          );
        });

        dokterSelect.trigger("change");
      },
      error: function () {
        alert("Gagal mengambil data dokter.");
      },
    });
  }

  $(".nav-link").on("click", function (e) {
    e.preventDefault();
    let episode_id = $(this).data("episode-id");
    let pasien_id = $(this).data("pasien-id");
    // alert(episode_id);

    // Aktifkan tab yang diklik
    $(".nav-link").removeClass("active show");
    $(this).addClass("active show");

    // Tampilkan loading
    $("#detilHistory").html('<p class="text-center">Loading...</p>');

    // AJAX untuk memuat detail pasien
    $.ajax({
      url: BASE_URL + "History-Pasien/getDetailHistory",
      type: "POST",
      data: {
        episode_id: episode_id,
        pasien_id: pasien_id,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          let data = response.data;
          console.log(data);

          // Load Farmasi
          $.ajax({
            url: BASE_URL + "History-Pasien/loadinputobat",
            type: "POST",
            data: {
              lokasiid: data.lokasi_id,
              episodeid: data.episode_id,
              pasienid: data.pasien_id,
            },
            dataType: "json",
            success: function (farmasiResponse) {
              let farmasiHTML = "";
              if (farmasiResponse.Responcode === "00") {
                console.log(farmasiResponse.Responresult);
                farmasiHTML = generateFarmasiHTML(farmasiResponse.Responresult);
              } else {
                farmasiHTML = `<p class="text-center text-danger">${farmasiResponse.Respondesc}</p>`;
              }

              // Load Tindakan
              $.ajax({
                url: BASE_URL + "History-Pasien/loadinputtindakan",
                type: "POST",
                data: {
                  lokasiid: data.lokasi_id,
                  episodeid: data.episode_id,
                  pasienid: data.pasien_id,
                },
                dataType: "json",
                success: function (tindakanResponse) {
                  let tindakanHTML = "";
                  if (tindakanResponse.Responcode === "00") {
                    tindakanHTML = generateTindakanHTML(
                      tindakanResponse.Responresult
                    );
                  } else {
                    tindakanHTML = `<p class="text-center text-danger">${tindakanResponse.Respondesc}</p>`;
                  }

                  // Panggil render HTML setelah AJAX Farmasi dan Tindakan berhasil
                  renderDetailHistory(data, farmasiHTML, tindakanHTML);
                  $("#tb, #bb").on("change", function () {
                    // alert("SS");
                    let tb = parseFloat($("#tb").val());
                    let bb = parseFloat($("#bb").val());

                    if (!isNaN(tb) && !isNaN(bb) && tb > 0 && bb > 0) {
                      let tb_m = tb / 100; // Konversi cm ke meter
                      let imt = bb / (tb_m * tb_m);
                      $("#imt").val(imt.toFixed(2)); // Simpan hasil ke input IMT
                    }
                  });
                },

                error: function () {
                  $("#detilHistory").append(
                    '<p class="text-center text-danger">Failed to load Tindakan data.</p>'
                  );
                },
              });
            },
            error: function () {
              $("#detilHistory").append(
                '<p class="text-center text-danger">Failed to load Farmasi data.</p>'
              );
            },
          });
        } else {
          $("#detilHistory").html(
            `<p class="text-center">${response.message}</p>`
          );
        }
      },
      error: function () {
        $("#detilHistory").html(
          '<p class="text-center text-danger">Failed to load data.</p>'
        );
      },
    });
    // alert(pasien_id);
  });

  // ✅ Trigger otomatis saat halaman pertama kali dimuat
  let firstNavLink = $(".nav-link.active.show");
  if (firstNavLink.length > 0) {
    firstNavLink.trigger("click"); // ✅ Jalankan event klik otomatis
  }
});

function renderDetailHistory(data, farmasiHTML, tindakanHTML) {
  $("#detilHistory").html(`
              <div class="row">
                <div class="card chat-box-clinic ">
                  <div class="chat-widgets">
                    <div class="card-header" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                      <h5 class="sub-title" style="font-size: 14px; display: inline-block;">Info Kunjungan</h5>
                      <button id="toggleViewPasien" style="float: left; font-size: 10px; border: none; background: none;color: white; cursor: pointer;">
                        <i class="fas fa-arrow-right"></i>
                      </button>
                    </div>

                    <div class="card-body">
                      <button id="toggleViewPasien" style="float: left; font-size: 10px; border: none; background: none;color: white; cursor: pointer;">
                        <i class="fas fa-arrow-right"></i>
                      </button>

                      <div class="row">
                        <div class="col-md-7">
                          <ul class="personal-info">
                            <li>
                              <span class="title">Nama Pasien:</span>
                              <span class="text">${data.nama}</span>
                            </li>
                            <li>
                              <span class="title">No. Medical Record:</span>
                              <span class="text">${data.int_pasien_id}</span>
                            </li>
                            <li>
                              <span class="title">Tanggal Lahir:</span>
                              <span class="text">${formatTanggalIndonesia(
                                data.tgl_lahir
                              )} (${hitungUmur(data.tgl_lahir)})</span>
                            </li>
                            <li>
                              <span class="title">Jenis Kelamin:</span>
                              <span class="text">${data.jenis_kelamin}</span>
                            </li>
                            <li>
                              <span class="title">Pembiayaan: </span>
                              <span class="text">${data.rekanan_id}</span>
                            </li>
                          </ul>                        
                        </div>
                        <div class="col-md-5">
                          <ul class="personal-info">
                            <li>
                              <span class="title">Dokter:</span>
                              <span class="text">${data.nama_dr}</span>
                            </li>
                            <li>
                              <span class="title">Poli:</span>
                              <span class="text">${data.nama_poli}</span>
                            </li>
                            <li>
                              <span class="title">Tanggal Masuk:</span>
                              <span class="text">${formatTanggalIndonesia(
                                data.tgl_masuk
                              )}</span>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="row">
                        <div class="card-header mt-3" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                          <h5 class="sub-title" style="font-size: 14px; display: inline-block;">Pemeriksaan</h5>
                        </div>
                        <div class="row">

                          <div class="col-md-6">
                            <div class="card">
                              <div class="card-header">
                                <h5 class="card-title">Pemeriksaan Dokter</h5>
                               
                              </div>

                              <div class="card-body">
                                <div class="vertical-scroll scroll-demo" style="height: 500px; overflow-y: auto;">
                                  <div class="row mb-3 align-items-center">
                                    <label for="subject" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">S</label>
                                    <div class="col-10">
                                      <textarea name="subject" id="subject" rows="3" class="form-control form-control-sm readonly-style" readonly>${
                                        data.s
                                      }</textarea>
                                    </div>
                                  </div>
  
                                  <div class="row mb-3 align-items-center">
                                    <label for="objective" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">O</label>
                                    <div class="col-10">
                                      <textarea name="objective" id="objective" rows="3" class="form-control form-control-sm readonly-style" readonly>${
                                        data.o
                                      }</textarea>
                                    </div>
                                  </div>
                                  
                                  <div class="row mb-3 align-items-center">
                                    <label class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">A</label>
                                    <div class="col-10">
                                      <textarea name="assesment_his" id="assesment_his" rows="3" class="form-control form-control-sm readonly-style" readonly>${
                                        data.a
                                      }</textarea>
                                    </div>
                                  </div>
  
                                  <div class="row mb-3 align-items-center">
                                    <label for="planning" class="col-2 col-form-label text-danger fw-bold text-center" style="font-size: 24px;">P</label>
                                    <div class="col-10">
                                      <textarea name="planning" id="planning" rows="3" class="form-control form-control-sm readonly-style" readonly>${
                                        data.p
                                      }</textarea>
                                    </div>
                                  </div>
 
                                  <div class="row  align-items-center pt-3">
                                    <label class="col-2 col-form-label">ICD 10 Utama</label>
                                    <div class="col-3">
                                      <input class="form-control form-control-sm readonly-style" id="cari_kode_icd10_hist" type="text" value="${
                                        data.icd10
                                      }" name="cari_kode_icd10_hist" readonly>
                                    </div>
                                    <div class="col-7">
                                      <input class="form-control form-control-sm readonly-style" id="cari_nama_icd10" type="text" value="${
                                        data.diagnosa
                                      }" name="cari_nama_icd10" readonly>
                                      <input class="form-control readonly-style" id="icd10_kode" type="hidden" name="icd10_kode" readonly>
                                    </div>
                                  </div>
                                </div>
                                <button id="updateSoapBtn" class="btn btn-success btn-sm mt-3" style="display: none;">Update</button>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="card">
                         <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Asesment By : ${data.crated_anam}</h5>

    <button id="editAsesmentBtn" class="btn btn-warning btn-sm ms-auto" title="Klik untuk mengedit asesmen">
    <i class="fas fa-edit"></i> Edit
</div>
                              <div class="card-body">
                                <div class="vertical-scroll scroll-demo" style="height: 500px; overflow-y: auto;">
                                  <div class="row mb-4">
                                    <div class="report-head">
                                      <h4> <i class="fas fa-user-nurse fa-1x" style="color: #4B0082;" data-bs-toggle="tooltip"></i> &nbsp;Tanda-Tanda Vital</h4>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-12">
                                      <div class="input-block local-forms">
                                        <label>Keluhan <span class="login-danger">*</span></label>
                                        <textarea class="form-control readonly-style" name="keluhan" id="keluhan" rows="3" cols="30" readonly>${
                                          data.keluhan_pasien
                                        }</textarea>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label" title="Tinggi Badan">TB <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control numeric floating  readonly-style " id="tb" name="tb" placeholder="Tinggi Badan" value="${
                                            data.ant_tb
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">Cm</span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label" title="Berat Badan">BB <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control  readonly-style floating " id="bb" name="bb" placeholder="Berat badan" value="${
                                            data.ant_bb
                                          }"  readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">Kg</span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label">IMT <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control readonly-style  floating" id="imt" name="imt" placeholder="IMT" value="${
                                            data.ant_imt
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">kg/m2</span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label" title="Lingkar Perut">LP <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control readonly-style floating " id="lp" name="lp" placeholder="Lingkar Perut" value="${
                                            data.ant_ling_perut
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">Cm</span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label">Suhu <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control floating readonly-style" id="suhu" name="suhu" placeholder="Suhu" value="${
                                            data.tv_suhu
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">°C</span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row mb-4">
                                    <div class="report-head">
                                      <h4> <i class="fa fa-solid fa-droplet fa-1x" style="color: #4B0082;" data-bs-toggle="tooltip"></i> &nbsp;Tekanan Darah</h4>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label">Sistole <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control numeric floating readonly-style" id="sistole" name="sistole" placeholder="sistole" value="${
                                            data.tv_tekanan_darah
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">mmHG</span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label">Diastole <span class="login-danger">*</span></label>
                                          <input type="text" class="form-control numeric floating readonly-style" id="diastole" name="diastole" placeholder="diastole" value="${
                                            data.tv_tekanan_darah2
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">mmHG</span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label" title="Respiratory Rate">RR <span class="login-danger">*</span></label>
                                          <input type="number" class="form-control numeric floating readonly-style" id="respiratory_rate" name="respiratory_rate" placeholder="Respiratory Rate" value="${
                                            data.tv_frek_nafas
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">Min</span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="input-block local-forms">
                                        <div class="input-group">
                                          <label class="focus-label" title="Heart Rate">HR <span class="login-danger">*</span></label>
                                          <input type="text" class="form-control numeric floating readonly-style" id="heart_rate" name="heart_rate" placeholder="Heart Rate" value="${
                                            data.tv_heart_rate
                                          }" readonly>
                                          <span class="input-group-text fw-bold" style="font-size: 0.75rem;">Bpm</span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <input type="hidden"  id="createdAnam" value="${
                                  data.crated_anam
                                }"></input>
                                 <input type="hidden"  id="trans_id" value="${
                                   data.trans_id
                                 }"></input>
                                 </input>
                                 <input type="hidden"  id="pasien_id" value="${
                                   data.pasien_id
                                 }"></input>
                                 <input type="hidden"  id="episode_id" value="${
                                   data.episode_id
                                 }"></input>
                                <button id="updateAsesmentBtn" class="btn btn-success btn-sm mt-3" style="display: none;">Update</button>
                                <button id="cancelUpdateAsesmentBtn" class="btn btn-danger btn-sm mt-3" style="display: none;">Cancel Update</button>
                              </div>
                            </div>
                          </div>
                        </div>   
                      </div>


                      <div class="row">
                        <div class="card-header mt-3" style="margin: 0; padding: 0;color: white; background-color:rgb(18, 3, 102); text-align: center; height: 30px;">
                          <h5 class="sub-title" style="font-size: 14px; display: inline-block;">Penunjang</h5>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="card">
                            <div class="card-header">
                              <h5 class="card-title">Farmasi</h5>
                            </div>
                            <div class="card-body vertical-scroll scroll-demo" style="height: 250px; overflow-y: auto;">
                              ${farmasiHTML}
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="card">
                            <div class="card-header">
                              <h5 class="card-title">Tindakan</h5>
                            </div>
                            <div class="card-body vertical-scroll scroll-demo" style="height: 250px; overflow-y: auto;">
                              ${tindakanHTML}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  `);
}

$(document).on("click", "#editAsesmentBtn", function () {
  let createdAnam = $("#createdAnam").val(); // Ambil nilai dari input field

  let trans_id = $("#trans_id").val();
  let pasien_id = $("#pasien_id").val();
  let episode_id = $("#episode_id").val();
  // Simpan nilai awal sebelum diedit

  // let sessionUserId = "<?= $this->session->userdata('user_id_pc'); ?>"; // Ambil user ID dari session PHP
  // alert(sessionUserId);
  if (createdAnam === sessionUserId) {
    originalData = {
      keluhan: $("#keluhan").val(),
      tb: $("#tb").val(),
      bb: $("#bb").val(),
      lp: $("#lp").val(),
      suhu: $("#suhu").val(),
      sistole: $("#sistole").val(),
      diastole: $("#diastole").val(),
      respiratory_rate: $("#respiratory_rate").val(),
      heart_rate: $("#heart_rate").val(),
      imt: $("#imt").val(),
    };

    // Hapus readonly hanya dari input tertentu
    $(
      "#keluhan, #tb, #bb, #lp, #suhu, #sistole, #diastole, #respiratory_rate, #heart_rate"
    ).removeAttr("readonly");
    $("#updateAsesmentBtn").show(); // Tampilkan tombol Update
    $("#cancelUpdateAsesmentBtn").show();
  } else {
    Swal.fire({
      icon: "error",
      title: "Akses Ditolak",
      text: "Anda tidak memiliki izin untuk mengedit.",
    });
  }
});
// Perhitungan IMT akan berjalan saat TB atau BB diubah

$(document).on("click", "#cancelUpdateAsesmentBtn", function () {
  // Kembalikan nilai input ke keadaan semula
  $("#keluhan").val(originalData.keluhan);
  $("#tb").val(originalData.tb);
  $("#bb").val(originalData.bb);
  $("#lp").val(originalData.lp);
  $("#suhu").val(originalData.suhu);
  $("#sistole").val(originalData.sistole);
  $("#diastole").val(originalData.diastole);
  $("#respiratory_rate").val(originalData.respiratory_rate);
  $("#heart_rate").val(originalData.heart_rate);
  $("#imt").val(originalData.imt);

  // Kunci kembali input (readonly)
  $(
    "#keluhan, #tb, #bb, #lp, #suhu, #sistole, #diastole, #respiratory_rate, #heart_rate"
  ).attr("readonly", true);

  // Sembunyikan tombol "Update" dan "Cancel"
  $("#updateAsesmentBtn, #cancelUpdateAsesmentBtn").hide();
});

$(document).on("click", "#updateAsesmentBtn", function () {
  let formData = {
    trans_id: $("#trans_id").val(),
    pasien_id: $("#pasien_id").val(),
    episode_id: $("#episode_id").val(),
    keluhan: $("#keluhan").val(),
    sistole: $("#sistole").val(),
    diastole: $("#diastole").val(),
    bb: $("#bb").val(),
    tb: $("#tb").val(),
    imt: $("#imt").val(),
    respiratory_rate: $("#respiratory_rate").val(),
    lp: $("#lp").val(),
    heart_rate: $("#heart_rate").val(),
    suhu: $("#suhu").val(),
    created_by: sessionUserId, // Ambil dari variabel yang didefinisikan di HTML
  };

  console.log(formData);
  $.ajax({
    url: BASE_URL + "HistoryController/updateAsesment",
    type: "POST",
    data: formData,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        alert("Data berhasil diperbarui!");
        $("#updateAsesmentBtn").hide(); // Sembunyikan tombol setelah update berhasil
        $(".readonly-style").attr("readonly", true); // Kunci kembali input setelah update
      } else {
        alert("Gagal memperbarui data: " + response.message);
      }
    },
    error: function () {
      alert("Terjadi kesalahan saat memperbarui data.");
    },
  });
});

function generateTindakanHTML(data) {
  let html = `<table class="table table-bordered table-striped">
                  <thead>
                      <tr>
                          <th>Kode Tindakan</th>
                          <th>Nama Tindakan</th>
                          <th>Jumlah</th>
                      </tr>
                  </thead>
                  <tbody>`;
  data.forEach((item) => {
    html += `<tr>
                  <td>${item.layan_id}</td>
                  <td>${item.nama_layan}</td>
                  <td>${item.qty}</td>
              </tr>`;
  });
  html += `</tbody></table>`;
  return html;
}

function generateFarmasiHTML(data) {
  let html = `<table class="table table-bordered table-striped">
                   <thead>
                        <tr>
                            <th>No Resep</th>
                            <th>Nama Obat</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                  <tbody>`;
  data.forEach((item) => {
    html += `<tr>
                    <td>${item.trans_co}</td>
                    <td>${item.nama_obat}</td>
                    <td>${item.qty}</td>
                    <td>${item.satuan}</td>
                </tr>`;
  });
  html += `</tbody></table>`;
  return html;
}

// Fungsi untuk menampilkan riwayat pasien berdasarkan episode_id
function viewHistory(episode_id, pasien_id) {
  window.location.href =
    BASE_URL + "History-Pasien/" + episode_id + "/" + pasien_id;
}

function hitungUmur(tanggalLahir) {
  if (!tanggalLahir) return "-";

  let birthDate;
  if (tanggalLahir.includes(".")) {
    // Format DD.MM.YYYY
    let parts = tanggalLahir.split(".");
    let day = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10) - 1; // Bulan dimulai dari 0
    let year = parseInt(parts[2], 10);
    birthDate = new Date(year, month, day);
  } else {
    // Format YYYY-MM-DD
    birthDate = new Date(tanggalLahir);
  }

  if (isNaN(birthDate.getTime())) return "-";

  // Tanggal sekarang
  let today = new Date();

  // Hitung tahun, bulan, dan hari
  let ageYear = today.getFullYear() - birthDate.getFullYear();
  let ageMonth = today.getMonth() - birthDate.getMonth();
  let ageDay = today.getDate() - birthDate.getDate();

  // Koreksi jika bulan negatif
  if (ageMonth < 0) {
    ageYear--;
    ageMonth += 12;
  }

  // Koreksi jika hari negatif
  if (ageDay < 0) {
    ageMonth--;
    let lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
    ageDay += lastMonth.getDate();
  }

  // Buat string output
  let result = [];
  if (ageYear > 0) result.push(ageYear + " Tahun");
  if (ageMonth > 0) result.push(ageMonth + " Bulan");
  if (ageDay > 0) result.push(ageDay + " Hari");

  return result.join(" ");
}
function validateInteger(input) {
  input.value = input.value.replace(/[^0-9]/g, ""); // Hanya angka 0-9
}
function formatTanggalIndonesia(tanggal) {
  if (!tanggal) return "-";

  let date;
  if (tanggal.includes("-")) {
    // Format YYYY-MM-DD
    date = new Date(tanggal);
  } else if (tanggal.includes("/")) {
    // Format MM/DD/YYYY atau DD/MM/YYYY
    let parts = tanggal.split("/");
    if (parts[2].length === 4) {
      date = new Date(parts[2], parts[1] - 1, parts[0]); // DD/MM/YYYY
    } else {
      date = new Date(parts[2], parts[0] - 1, parts[1]); // MM/DD/YYYY
    }
  } else if (tanggal.includes(".")) {
    // Format DD.MM.YYYY
    let parts = tanggal.split(".");
    date = new Date(parts[2], parts[1] - 1, parts[0]);
  } else {
    return "-";
  }

  if (isNaN(date.getTime())) return "-";

  const bulanIndonesia = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];

  const hari = date.getDate();
  const bulan = bulanIndonesia[date.getMonth()];
  const tahun = date.getFullYear();

  return `${hari} ${bulan} ${tahun}`;
}
