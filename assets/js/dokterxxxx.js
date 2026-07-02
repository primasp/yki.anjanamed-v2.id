var adasoap = "N";
const statPlgSelect = $("#statPlgGetApi");
const subOptionsMap = {
  tacc_time: `
      <div class="sub-options border rounded p-2 bg-light">
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Nyeri Mata" id="nyeri_mata">
              <label class="form-check-label" for="nyeri_mata">Nyeri Mata</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Mata Merah" id="mata_merah">
              <label class="form-check-label" for="mata_merah">Mata Merah</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Mata Sensitif Terhadap Sinar" id="sensitif_sinar">
              <label class="form-check-label" for="sensitif_sinar">Mata Sensitif Terhadap Sinar</label>
          </div>
      </div>`,
  tacc_age: `
      <div class="sub-options border rounded p-2 bg-light">
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="< 18 Tahun" id="umur_kurang">
              <label class="form-check-label" for="umur_kurang">&lt; 18 Tahun</label>
          </div>
      </div>`,
  tacc_komplikasi: `
      <div class="sub-options border rounded p-2 bg-light">
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Sakit Kepala" id="sakit_kepala">
              <label class="form-check-label" for="sakit_kepala">Sakit Kepala</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Mata Lelah" id="mata_lelah">
              <label class="form-check-label" for="mata_lelah">Mata Lelah</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Nyeri Disekitar Mata" id="nyeri_sekitar">
              <label class="form-check-label" for="nyeri_sekitar">Nyeri Disekitar Mata</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Mata Kering" id="mata_kering">
              <label class="form-check-label" for="mata_kering">Mata Kering</label>
          </div>
      </div>`,
  tacc_comorbid: `
      <div class="sub-options border rounded p-2 bg-light">
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="BDCVA < 1.0" id="bdcva">
              <label class="form-check-label" for="bdcva">Tajam penglihatan jauh dengan koreksi (BDCVA) kurang dari 1.0 atau 6/6</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Jaeger < 1" id="jaeger">
              <label class="form-check-label" for="jaeger">Tajam penglihatan dekat kurang dari Jaeger 1</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Anisometropia" id="aniso">
              <label class="form-check-label" for="aniso">Anisometropia</label>
          </div>
          <div class="form-check">
              <input class="form-check-input" type="radio" name="subAlasanTacc" value="Diplopia" id="diplopia">
              <label class="form-check-label" for="diplopia">Disertai kelainan organik, diplopia, spasme akomodasi (spasme otot siliar)</label>
          </div>
      </div>`,
};

// Pindahkan fokus saat modal terbuka
$("#modal_icd10").on("shown.bs.modal", function () {
  $("#input_pencarian10").focus();
});

// Hapus fokus saat modal ditutup
$("#modal_icd10").on("hidden.bs.modal", function () {
  $(this).find(":focus").blur();
});
// const sadarSelect = $("#sadarGetApi");

console.log("Dokter JS loaded");

$("#btn_mulaiperiksa").on("click", mulaiperiksa);
$("#btn_simpansoap").on("click", simpansoap);
$("#input_pencarian10").on("input", cariicd10);
$("#input_pencarian10_sek").on("input", cariicd10sek);
$("#input_pencarian9").on("input", cariicd9);
$("#btn_tindakan").on("click", bukamodaltindakan);
$("#btn_sejarah").on("click", bukamodalsejarah);
$("#btn_rujukan").on("click", bukamodalrujukan);
$("#btn_resep").on("click", bukamodalresep);
$("#input_cariobat").on("input", cariobat);
$("#btn_selesai").on("click", bukamodalselesai);
$("#input_caritindakan").on("input", caritindakan);

$(document).on("click", "#patientList .patient-item", onPatientItemClick);

// generateUUID();

function generateUUID() {
  return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
    var r = (Math.random() * 16) | 0,
      v = c === "x" ? r : (r & 0x3) | 0x8;

    console.log(v.toString(16));
    return v.toString(16);
  });
}

// Cek apakah device_id sudah disimpan di localStorage
if (!localStorage.getItem("device_id")) {
  const newId = "PC_" + generateUUID(); // ID ini akan konsisten sepanjang tidak dihapus
  localStorage.setItem("device_id", newId);
}

const device_id = localStorage.getItem("device_id");

// alert(device_id);

function toggleFieldsModalRjkn(type) {
  if (type === "ya") {
    document.getElementById("RujukanKhususRow").style.display = "";
    document.getElementById("RujukanRow").style.display = "none";

    document.getElementById("tglEstRujukKhss").setAttribute("required", true);
    document.getElementById("plhRjknKhssApi").setAttribute("required", true);
    document.getElementById("plhFasRjknKhssApi").setAttribute("required", true);

    document.getElementById("tglEstRujuk").removeAttribute("required");
    document.getElementById("spsRjknGetApi").removeAttribute("required");
    document.getElementById("subSpRjknGetApi").removeAttribute("required");
    document.getElementById("saranaGetApi").removeAttribute("required");
    document.getElementById("faskesRjknGetApi").removeAttribute("required");
  } else {
    document.getElementById("RujukanKhususRow").style.display = "none";
    document.getElementById("RujukanRow").style.display = "";

    document.getElementById("tglEstRujuk").setAttribute("required", true);
    document.getElementById("spsRjknGetApi").setAttribute("required", true);
    document.getElementById("subSpRjknGetApi").setAttribute("required", true);
    document.getElementById("saranaGetApi").setAttribute("required", true);
    document.getElementById("faskesRjknGetApi").setAttribute("required", true);

    document.getElementById("tglEstRujukKhss").removeAttribute("required");
    document.getElementById("plhRjknKhssApi").removeAttribute("required");
    document.getElementById("plhFasRjknKhssApi").removeAttribute("required");
  }
}

$(document).on("click", "#poliTabs .nav-link", function (e) {
  e.preventDefault();

  $("#poliTabs .nav-link").removeClass("active");
  $(this).addClass("active");

  let poli_id = $(this).data("poli");
  let dokter_id = $("#dokterId").text().trim();
  let tanggal = $("#filterTanggal").val();

  loadPasien(dokter_id, poli_id, tanggal);
});

// Jika tanggal berubah otomatis reload
$("#filterTanggal").on("change", function () {
  let poli_id = $("#poliTabs .nav-link.active").data("poli");
  let dokter_id = $("#dokterId").text().trim();
  let tanggal = $(this).val();

  loadPasien(dokter_id, poli_id, tanggal);
});

function loadPasien(dokter_id, poli_id, tanggal) {
  // alert(tanggal);
  $.ajax({
    url: BASE_URL + "dokter/get_pasien_by_poli",
    type: "POST",
    data: { dokter_id: dokter_id, poli_id: poli_id, tanggal: tanggal },
    dataType: "json",
    success: function (res) {
      let html = "";

      if (res.length > 0) {
        res.forEach((p) => {
          html += `
                    <div class="chat-user-group d-flex align-items-center m-0 patient-item"
                         data-episode-id="${p.episode_id}"
                         data-pasien-id="${p.pasien_id}">
                        <div class="chat-users">
                            <div class="user-titles d-flex flex-column">

                                <h5 class="patient-name" style="color:${getColor(
                                  p.status_periksa,
                                )}">
                                    ${p.nama}
                                </h5>

                                <div class="chat-user-time">
                                    <p class="small d-flex flex-wrap align-items-center" style="color:${getColor(
                                      p.status_periksa,
                                    )}">
                                        <span class="me-2 fw-bold text-primary">${
                                          p.nama_poli
                                        }</span>
                                        <span class="me-2">|</span>
                                        <span class="me-2">No: ${p.urut}</span>
                                        <span class="me-2">|</span>
                                        <span class="me-2">${
                                          p.jenis_kelamin
                                        }</span>
                                        <span class="me-2">|</span>
                                        <span>${p.rekanan_id}</span>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                `;
        });
      } else {
        html = `<div class="p-3">Tidak ada pasien</div>`;
      }

      $("#patientList").html(html);
    },
  });
}

function getColor(status) {
  if (status == "3") return "red";
  if (status == "1") return "green";
  return "black";
}

function validateSubFieldsRjkn() {
  const rjknValue = document.getElementById("plhRjknKhssApi").value;
  // alert(rjknValue);
  if (rjknValue === "THA" || rjknValue === "HEM") {
    document.getElementById("plhFasRjknKhssApi").setAttribute("required", true);
    document.getElementById("spsRjknKhssGetApi").setAttribute("required", true);
    document
      .getElementById("subSpRjknKhssGetApi")
      .setAttribute("required", true);
  } else {
    // document.getElementById("plhFasRjknKhssApi").removeAttribute("required");
    document.getElementById("spsRjknKhssGetApi").removeAttribute("required");
    document.getElementById("subSpRjknKhssGetApi").removeAttribute("required");
    // plhFasRjknKhssApi;
  }
}

function onPatientItemClick(event) {
  const $item = $(event.currentTarget);

  const episodeId = $item.data("episode-id");
  const pasienId = $item.data("pasien-id");
  const poliId = $item.data("poli-id"); // ← ambil poli
  // alert(poliId);

  hidePanelPasien();
  loadDataPasien(episodeId, pasienId);
  // alert(device_id);
  $.ajax({
    url: BASE_URL + "DokterController/insertDisplay",
    method: "POST",
    data: {
      idEpisode: episodeId,
      idPasien: pasienId,
      idDevice: device_id,
    },
    success: function (response) {
      console.log("Insert display berhasil:", response);
    },
    error: function (xhr, status, error) {
      console.error("Gagal insert display:", error);
    },
  });

  // ==========================
  // CEK POLI PALIATIF
  // ==========================
  if (poliId === "POLI0000000001") {
    // reset form dulu supaya tidak kecampur pasien sebelumnya
    resetFormPaliatif();

    // isi hidden di modal paliatif
    $("#modalPaliatif input[name='episode_id']").val(episodeId);
    $("#modalPaliatif input[name='pasien_id']").val(pasienId);

    // cek ke server apakah sudah ada asesmen
    $.ajax({
      url: BASE_URL + "Asessment/getPaliatif",
      type: "GET",
      dataType: "json",
      data: {
        episode_id: episodeId,
        pasien_id: pasienId,
      },
      success: function (res) {
        if (res && res.success && res.data) {
          // sudah pernah diisi → prefilling
          fillFormPaliatif(res.data);
        }
        // tampilkan modal setelah data (kalau ada) diisi
        const palModalEl = document.getElementById("modalPaliatif");
        const palModal = new bootstrap.Modal(palModalEl);
        palModal.show();
      },
      error: function () {
        // kalau error cek data, tetap boleh isi baru
        const palModalEl = document.getElementById("modalPaliatif");
        const palModal = new bootstrap.Modal(palModalEl);
        palModal.show();
      },
    });

    // // tampilkan modal (Bootstrap 5)
    // const palModal = new bootstrap.Modal(
    //   document.getElementById("modalPaliatif"),
    // );
    // palModal.show();
  }
}

function copyToSubject() {
  const keluhan = document.getElementById("perawat_keluhan").textContent;
  const subjectTextarea = document.getElementById("subject");
  if (keluhan) {
    subjectTextarea.value = keluhan;
  }
}
function hidePanelPasien() {
  var listPasien = document.getElementById("listpasien");
  var pemeriksaan = document.getElementById("pemeriksaan");
  var button = this;

  if (listPasien.style.display === "none") {
    listPasien.style.display = "block";
    pemeriksaan.className = "col-sm-9";
  } else {
    listPasien.style.display = "none";
    pemeriksaan.className = "col-sm-12";
  }
}

function loadDataPasien(episodeid, pasienid) {
  $.ajax({
    url: BASE_URL + "DokterController/loadDataPasien",
    method: "POST",
    dataType: "JSON",
    data: {
      episodeid: episodeid,
      pasienid: pasienid,
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
      } else {
        var hasil = data.Responresult;
        console.log(hasil);
        adasoap = hasil.ada_soap == "Y" ? "Y" : "N";

        $(".idlokasi").val(hasil.lokasi_id);
        $(".idepisode").val(hasil.episode_id);
        $(".idtrans").val(hasil.trans_id);
        $(".idpasien").val(hasil.pasien_id);
        $(".idtransco").val(hasil.trans_co);
        $(".idpoli").val(hasil.poli_id);
        $(".idpasien").val(hasil.pasien_id);
        $(".idpolibpjs2").val(hasil.poli_bpjsid);
        $(".iddokterbpjs2").val(hasil.prefix);
        $(".idrekanan").val(hasil.rekanan_id);
        $(".keluhanRujuk").val(hasil.keluhan_pasien);
        $(".cari_kode_icd10").val(hasil.icd10);

        $(".tanggalpoli").val(convertDateToDDMMYYYY(hasil.tgl_masuk));
        $(".tanggaldaftar").val(hasil.tgl_masuk);
        $(".tanggal_pulang").val(hasil.tgl_masuk);
        $(".nokartuprov").val(hasil.no_kartuprov);
        $(".rm_pasien").val(hasil.int_pasien_id);
        $(".jk_pasien").val(hasil.jenis_kelamin);
        $(".nama_pasien").val(hasil.nama);
        $(".prov_pasien").val(hasil.rekanan);
        $(".lahir_pasien").val(hasil.tgl_lahir);
        $(".kunj_ke").val(hasil.kunj_ke);

        $(".alergiMakanan").val(hasil.alergi_id_mknn);
        $(".alergi_udara").val(hasil.alergi_id_udr);
        $(".alergiObat").val(hasil.alergi_id_obat);

        $(".kdStatPlg").val(hasil.kondisi_pulang);
        $(".kdSadar").val(hasil.tingkat_sadar);
        $(".kdPrognosa").val(hasil.prognosis);

        getAlergiData("01", "#alergiMakanan", hasil.alergi_id_mknn); // Makanan
        getAlergiData("02", "#alergiUdara", hasil.alergi_id_udr); // Udara
        getAlergiData("03", "#alergiObat", hasil.alergi_id_obat); // Obat-obatan
        getStatPlgApi(hasil.kondisi_pulang);
        getSadarApi(hasil.tingkat_sadar);
        getPrognosaApi(hasil.prognosis);

        if (adasoap === "Y") {
          $("#subject").val(hasil.s);
          $("#objective").val(hasil.o);
        } else {
          $("#subject").val(hasil.keluhan_pasien);

          // $("#objective").val(
          //   `Tekanan Darah : ${parseInt(hasil.fisik_td)} / ${parseInt(
          //     hasil.tv_tekanan_darah2,
          //   )} mmHg \nFrekuensi Nafas : ${parseInt(
          //     hasil.fisik_rr,
          //   )} x/menit \nDenyut Jantung : ${parseInt(
          //     hasil.fisik_nadi,
          //   )} Bpm \nSuhu : ${parseFloat(
          //     hasil.fisik_suhu,
          //   )} \u00B0C \nTinggi Badan : ${parseInt(
          //     hasil.fisik_tb,
          //   )} cm \nBerat Badan : ${parseInt(hasil.fisik_bb)} kg \nLILA : ${
          //     hasil.fisik_lila
          //   }`,
          // );

          $("#objective").val(
            `Tekanan Darah : ${hasil.fisik_td} mmHg \nFrekuensi Nafas : ${parseInt(
              hasil.fisik_rr,
            )} x/menit \nDenyut Jantung : ${parseInt(
              hasil.fisik_nadi,
            )} Bpm \nSuhu : ${parseFloat(
              hasil.fisik_suhu,
            )} \u00B0C \nTinggi Badan : ${parseInt(
              hasil.fisik_tb,
            )} cm \nBerat Badan : ${parseInt(hasil.fisik_bb)} kg \nLILA : ${
              hasil.fisik_lila
            }`,
          );
        }

        $("#assesment").val(hasil.a);
        $("#planning").val(hasil.p);

        ceksudahmulai();
        cekstatusresep();
        loadicd10utama();
        loadicd10sek();
        loadicd9();
        loaditemharga();

        if (adasoap === "Y") {
          $("#cari_kode_icd10").prop("readonly", false);
          $("#cari_kode_icd10_sek").prop("readonly", false);
          $("#cari_kode_icd9").prop("readonly", false);
        } else {
          $("#cari_kode_icd10").prop("readonly", true);
          $("#cari_kode_icd10_sek").prop("readonly", true);
          $("#cari_kode_icd9").prop("readonly", true);
        }
      }
    },
  });
  return false;
}

// dari db
function getStatPlgApi(selectedStatPlgId) {
  $.ajax({
    url: BASE_URL + "DokterController/getStatPlgApi",
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        console.log("Data Status Pulang:", response.data); // Debugging
        let statPlgSelect = $("#statPlgGetApi");

        statPlgSelect.empty();
        statPlgSelect.append(
          '<option value="">-- Pilih Status Pulang --</option>',
        );

        response.data.forEach(function (statPlg) {
          console.log(statPlg);
          statPlgSelect.append(
            `<option value="${statPlg.kdstatuspulang}">${statPlg.nmstatuspulang}</option>`,
          );
        });

        if (selectedStatPlgId) {
          statPlgSelect.val(selectedStatPlgId);
        }

        // Gunakan event delegation untuk menangani perubahan dropdown
        $(document)
          .off("change", "#statPlgGetApi")
          .on("change", "#statPlgGetApi", function () {
            const selectedValue = $(this).val();
            console.log("Status Pulang dipilih:", selectedValue);

            if (selectedValue === "4") {
              // console.log("Membuka modal rujukan...");
              // getSaranaApi();
              // $("#modal_rujuk").modal("show"); // Pastikan modal bisa muncul
              // $("#modal_rujuk")
              //   .off("hidden.bs.modal")
              //   .on("hidden.bs.modal", function () {
              //     const episodeId = $("#idepisode").val();
              //     console.log(
              //       "Modal ditutup, cek data rujukan untuk episode:",
              //       episodeId
              //     );
              //     cekDataRujukan(episodeId)
              //       .then((isDataExist) => {
              //         console.log("Hasil cekDataRujukan:", isDataExist);
              //         if (!isDataExist) {
              //           statPlgSelect.val("");
              //           Swal.fire({
              //             title: "Data Rujukan Belum Disimpan",
              //             text: "Status Pulang dikembalikan ke default.",
              //             icon: "info",
              //             confirmButtonText: "OK",
              //           });
              //         }
              //       })
              //       .catch((error) => {
              //         console.error(
              //           "Error saat memeriksa data rujukan:",
              //           error
              //         );
              //         Swal.fire({
              //           title: "Error",
              //           text: "Terjadi kesalahan saat memeriksa data rujukan.",
              //           icon: "error",
              //           confirmButtonText: "OK",
              //         });
              //       });
              //   });
            }
          });
      } else {
        alert("Data API Status Pulang tidak ditemukan");
      }
    },
    // error: function (xhr, status, error) {
    //   console.error("Error mengambil data Status Pulang:", xhr.responseText);
    // },

    error: function (xhr, status, error) {
      try {
        let responseJson = JSON.parse(xhr.responseText);
        if (responseJson.status === "success") {
          console.warn(
            "API mengembalikan status sukses, tapi masuk ke error callback:",
            responseJson,
          );
          return;
        }
      } catch (e) {
        console.error("Gagal parsing JSON:", e);
      }
      console.error("Error mengambil data Status Pulang:", xhr.responseText);
    },
  });
}

function getPrognosaApi(selectedPrognosaId) {
  $.ajax({
    url: BASE_URL + "DokterController/getPrognosaApi", // Ambil data dari database
    type: "GET",
    dataType: "json",
    success: function (response) {
      try {
        console.log("Data Prognosa:", response); // Debugging

        let prognosaSelect = $("#plhPrognosaApi");

        if (!prognosaSelect.length) {
          console.error("Error: Elemen #plhPrognosaApi tidak ditemukan.");
          return;
        }

        if (response.status === "success" && Array.isArray(response.data)) {
          prognosaSelect.empty();
          prognosaSelect.append(
            '<option value="">-- Pilih Prognosa --</option>',
          );

          response.data.forEach(function (prognosa) {
            prognosaSelect.append(
              `<option value="${prognosa.kdprognosa}">${prognosa.nmprognosa}</option>`,
            );
          });

          if (selectedPrognosaId) {
            prognosaSelect.val(selectedPrognosaId);
          }
        } else {
          console.warn("Prognosa tidak ditemukan:", response.message);
          Swal.fire({
            title: "Error",
            text: response.message,
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      } catch (error) {
        console.error("Kesalahan dalam memproses data Prognosa:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Gagal mengambil data Prognosa:", xhr.responseText);
      Swal.fire({
        title: "Error",
        text: "Gagal mengambil data Prognosa dari database.",
        icon: "error",
        confirmButtonText: "OK",
      });
    },
  });
}

function getSadarApi(selectedSadarId) {
  $.ajax({
    url: BASE_URL + "DokterController/getSadarApi",
    type: "GET",
    dataType: "json",
    success: function (response) {
      console.log(response);
      if (response.status === "success") {
        let sadarSelect = $("#sadarGetApi"); // Target dropdown
        sadarSelect.empty(); // Kosongkan dropdown sebelum diisi
        sadarSelect.append('<option value="">-- Pilih Kesadaran --</option>'); // Tambahkan opsi default

        // Looping data poliklinik dan masukkan ke dropdown
        response.data.forEach(function (sadar) {
          sadarSelect.append(
            `<option value="${sadar.kdsadar}">${sadar.nmsadar}</option>`,
          );
        });

        // Atur opsi yang dipilih jika `selectedSadarId` tersedia
        if (selectedSadarId) {
          sadarSelect.val(selectedSadarId); // Set opsi terpilih
        }
      } else {
        alert("Data API Kesadarn tidak ditemukan");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error mengambil data Poliklinik: " + xhr.responseText);
    },
  });
}

function cekDataRujukan(episodeId) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: BASE_URL + "DokterController/cekDataRujukan", // Endpoint untuk memeriksa data
      method: "GET",
      dataType: "JSON",
      data: { episodeId: episodeId },
      success: function (response) {
        if (response.status === "success" && response.data) {
          resolve(true); // Data ditemukan
        } else {
          resolve(false); // Data tidak ditemukan
        }
      },
      error: function (xhr, status, error) {
        reject(xhr.responseText || error); // Gagal memeriksa data
      },
    });
  });
}

function getAlergiData(jenis, selectId, selectedValue) {
  $.ajax({
    url: BASE_URL + "DokterController/getAlergiApi/" + jenis, // Panggil controller untuk data alergi
    type: "GET",
    dataType: "json",
    success: function (response) {
      console.log("Data Alergi (" + jenis + "):", response); // Debugging
      if (response.status === "success") {
        let data = response.data;
        let selectElement = $(selectId);

        selectElement.empty();
        selectElement.append('<option value="">Pilih Alergi</option>'); // Opsi default

        $.each(data, function (index, item) {
          let [kdAlergi, nmAlergi] = [item.kdalergi, item.nmalergi];
          let isSelected = selectedValue === kdAlergi ? "selected" : "";
          selectElement.append(
            `<option value="${kdAlergi}" ${isSelected}>${nmAlergi}</option>`,
          );

          // selectElement.append(
          //   '<option value="' +
          //     item.kdAlergi +
          //     "|" +
          //     item.nmAlergi +
          //     '">' +
          //     item.nmAlergi +
          //     "</option>"
          // );
        });
      } else {
        alert(response.message);
      }
    },
    error: function () {
      alert("Error mengambil data alergi.");
    },
  });
}

function ceksudahmulai() {
  $.ajax({
    url: BASE_URL + "DokterController/ceksudahmulai",
    type: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
    },
    success: function (response) {
      console.log(response);
      if (response.Responcode == "00") {
        $("#subject").prop("readonly", false);
        $("#objective").prop("readonly", false);
        $("#assesment").prop("readonly", false);
        $("#planning").prop("readonly", false);
        $("#btn_mulaiperiksa").hide();
        $("#btn_simpansoap").show();

        $("#sakit_keluarga").prop("readonly", false);
        $("#alergi_keluarga").prop("readonly", false);
        $("#komplikasi").prop("readonly", false);
        $("#edukasi").prop("readonly", false);
      } else {
        $("#subject").prop("readonly", true);
        $("#objective").prop("readonly", true);
        $("#assesment").prop("readonly", true);
        $("#planning").prop("readonly", true);
        $("#btn_mulaiperiksa").show();
        $("#btn_simpansoap").hide();

        $("#sakit_keluarga").prop("readonly", true);
        $("#alergi_keluarga").prop("readonly", true);
        $("#komplikasi").prop("readonly", true);
        $("#edukasi").prop("readonly", true);
      }
    },
  });
  return false;
}

function cekstatusresep() {
  $.ajax({
    url: BASE_URL + "DokterController/cekstatusresep",
    type: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
    },
    success: function (response) {
      if (response.Responcode == "00") {
        $("h4.blinking").show();
      } else {
        $("h4.blinking").hide();
      }
    },
  });

  return false;
}

function loadicd10utama() {
  $.ajax({
    url: BASE_URL + "DokterController/loadicd10utama",
    type: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
    },
    success: function (data) {
      $("#cari_kode_icd10").val("");
      $("#cari_nama_icd10").val("");
      $("#icd10_kode").val("");

      if (data.Responcode == "01") {
        //
      } else {
        var hasil = data.Responresult;
        console.log(hasil);
        $("#cari_kode_icd10").val(hasil.kode_icd);
        $("#cari_nama_icd10").val(hasil.diagnosa);
        $("#icd10_kode").val(hasil.icd10);
        $("#diag_non_spesialis").val(hasil.diag_non_sp);
      }
    },
  });

  return false;
}

function loadicd10sek() {
  // Pastikan elemen input ada sebelum mengakses nilainya
  const lokasiid = $(".idlokasi").val();
  const episodeid = $(".idepisode").val();
  const pasienid = $(".idpasien").val();

  if (!lokasiid || !episodeid || !pasienid) {
    console.error(
      "Error: Salah satu input kosong. Pastikan semua data sudah diisi.",
    );
    alert("Silakan isi semua input terlebih dahulu.");
    return false;
  }

  $.ajax({
    url: BASE_URL + "DokterController/loadicd10sek",
    type: "POST",
    dataType: "json",
    data: {
      lokasiid: lokasiid,
      episodeid: episodeid,
      pasienid: pasienid,
    },
    success: function (data) {
      try {
        // Pastikan elemen list ICD ada sebelum mengubah isinya
        let listIcd10 = $("#listicd10");
        if (listIcd10.length === 0) {
          console.error("Error: Elemen #listicd10 tidak ditemukan di halaman.");
          return;
        }

        // Bersihkan tabel sebelum memasukkan data baru
        listIcd10.html("");

        if (!data || typeof data !== "object") {
          console.error(
            "Error: Respons dari server bukan objek JSON yang valid.",
            data,
          );
          alert("Terjadi kesalahan dalam memproses data.");
          return;
        }

        if (data.Responcode === "01") {
          console.warn("Tidak ada data ICD 10 yang ditemukan.");
          listIcd10.html("<tr><td colspan='3'>Data tidak ditemukan</td></tr>");
          return;
        }

        if (!Array.isArray(data.Responresult)) {
          console.error(
            "Error: Struktur data tidak sesuai yang diharapkan.",
            data,
          );
          return;
        }

        let diagnosa = "";
        let hasil = data.Responresult;

        // Looping data ICD dan masukkan ke tabel
        hasil.forEach((item) => {
          diagnosa += `
            <tr>
              <td style="width: 15%;">${escapeHtml(item.kode_icd)}</td>
              <td>${escapeHtml(item.diagnosa)}</td>
              <td style="width: 10%;">  
                <button class="btn btn-danger btn-sm" type="button" 
                  onclick="hapusicd10('${escapeHtml(
                    item.episode_id,
                  )}', '${escapeHtml(item.pasien_id)}', '${escapeHtml(
                    item.trans_diag,
                  )}')">
                  Hapus
                </button>  
              </td>
            </tr>`;
        });

        listIcd10.html(diagnosa);
        $("#cari_kode_icd10_sek").val(""); // Kosongkan input setelah berhasil
      } catch (error) {
        console.error("Kesalahan dalam memproses data:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", xhr.status, xhr.responseText);
      alert("Terjadi kesalahan saat mengambil data. Silakan coba lagi.");
    },
  });

  return false;
}

// Fungsi untuk mencegah XSS (Sanitasi Input)
function escapeHtml(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function loadicd9() {
  $.ajax({
    url: BASE_URL + "DokterController/loadicd9",
    method: "POST",
    dataType: "JSON",
    cache: false,
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        $("#listicd9").html("");
      } else {
        $("#listicd9").html("");
        var tindakan = "";
        var hasil = data.Responresult;

        // Menambahkan header tabel
        tindakan += `  
          <tr class="table-primary">  
            <th style="width: 15%;">Kode ICD 9</th>  
            <th>Tindakan</th>  
            <th style="width: 10%;">Aksi</th>  
          </tr>  
        `;

        for (var i in hasil) {
          tindakan += "<tr>";
          tindakan += `<td style="width: 15%;">${hasil[i].kode_icd}</td>`;
          tindakan += `<td>${hasil[i].tindakan}</td>`;
          tindakan += `<td style="width: 10%;"><button class="btn btn-danger btn-sm" type="button" onclick="hapusicd9('${hasil[i].episode_id}', '${hasil[i].pasien_id}', '${hasil[i].trans_tin}')">Hapus</button></td>`;
          tindakan += "</tr>";
        }

        $("#listicd9").html(tindakan);
        $("#cari_kode_icd9").val("");
      }
    },
  });
  return false;
}

function loaditemharga() {
  $.ajax({
    url: BASE_URL + "DokterController/loaditemharga",
    type: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        $("#listitemharga").html("");
      } else {
        $("#listitemharga").html("");
        var itemharga = "";
        var hasiltotal = 0;
        var hasil = data.Responresult;
        for (var i in hasil) {
          // itemharga += "<tr>";
          // itemharga +=
          //   `<td style="width: 50%">` + hasil[i].nama_layan + `</td>`;
          // itemharga +=
          //   `<td style="width: 20%" align='right'>` +
          //   formatRupiah(hasil[i].total_harga) +
          //   `</td>`;
          // itemharga += "</tr>";

          // hasiltotal += parseFloat(hasil[i].total_harga, 10);

          let raw = hasil[i].total_harga
            ? hasil[i].total_harga.toString()
            : "0";
          let numeric = raw.replace(/[^0-9.-]/g, ""); // hilangkan Rp, titik, koma

          itemharga += "<tr>";
          itemharga +=
            `<td style="width: 50%">` + hasil[i].nama_layan + `</td>`;
          itemharga +=
            `<td style="width: 20%" align='right'>` +
            formatRupiah(raw) +
            `</td>`;
          itemharga += "</tr>";

          hasiltotal += parseFloat(numeric);
        }
        $("#listitemharga").html(itemharga);
        // $("#hasiltotal").html("TOTAL HARGA : Rp. " + formatRupiah(hasiltotal));
        $("#hasiltotal").html(`TOTAL HARGA : ${formatRupiah(hasiltotal)}`);
      }
    },
  });

  return false;
}

function bukamodalsejarah(event) {
  event.preventDefault();
  // Mengambil nilai dari input hidden
  var episode_id = $(".idepisode").val();
  var pasien_id = $(".idpasien").val();

  // Redirect ke URL dengan parameter
  if (episode_id && pasien_id) {
    // BASE_URL + "History-Pasien/" + episode_id + "/" + pasien_id;
    window.open(
      BASE_URL + "History-Pasien/" + episode_id + "/" + pasien_id,
      "_blank",
    );
  } else {
    alert("Episode ID atau Pasien ID tidak tersedia.");
  }
}

function bukamodaltindakan(event) {
  event.preventDefault();

  if (adasoap === "Y") {
    // alert("testModal");
    $("#modal_tindakan").modal("show");

    loadtindakanmodal();
  } else {
    Swal.fire({
      title: "Belum simpan SOAP",
      icon: "error",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        //
      }
    });
  }
}

function bukamodalrujukan() {
  // event.preventDefault();

  if (adasoap === "Y") {
    // loadrujukanmodal();
    let episodeid = $(".idepisode").val();

    cekRujukan(episodeid)
      .done(function (response) {
        if (response.Responcode === "00") {
          // Jika nomor rujukan sudah ada
          Swal.fire({
            title: "Rujukan sudah pernah dibuat",
            text: "Apakah Anda ingin mengedit rujukan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Edit Rujukan",
            cancelButtonText: "Batal",
          }).then((result) => {
            if (result.isConfirmed) {
              // Jika user memilih edit rujukan, tampilkan modal
              $("#modal_rujuk").modal("show");
              getSaranaApi();
            }
          });
        } else if (response.Responcode === "01") {
          // alert("121");
          // Jika nomor rujukan belum ada

          getSaranaApi();
          // alert(episodeid);
          $("#modal_rujuk").modal("show");
        } else {
          // Jika terjadi error
          Swal.fire({
            title: "Error",
            text:
              response.Respondesc || "Terjadi kesalahan, silakan coba lagi.",
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      })
      .fail(function () {
        Swal.fire({
          title: "Error",
          text: "Gagal memeriksa data rujukan, silakan coba lagi.",
          icon: "error",
          confirmButtonText: "OK",
        });
      });

    // $("#modal_rujuk").modal("show");
  } else {
    Swal.fire({
      title: "Belum simpan SOAP",
      icon: "error",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        //
      }
    });
  }
}

function getSpesialisKhssApi() {
  $.ajax({
    url: BASE_URL + "RujukanController/getSpesialisApi", // Endpoint untuk mengambil data spesialis
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let spesialisSelect = $("#spsRjknKhssGetApi"); // Target dropdown spesialis
        spesialisSelect.empty(); // Kosongkan dropdown sebelum diisi
        spesialisSelect.append(
          '<option value="">-- Pilih Spesialis --</option>',
        ); // Tambahkan opsi default

        // Looping data spesialis dan masukkan ke dropdown
        $.each(response.data, function (index, spesialis) {
          spesialisSelect.append(
            `<option value="${spesialis.kdSpesialis}">${spesialis.nmSpesialis}</option>`,
          );
        });
      } else {
        alert("Data Spesialis tidak ditemukan.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error mengambil data Spesialis: " + xhr.responseText);
    },
  });
}

function getSpesialisApi() {
  $.ajax({
    url: BASE_URL + "RujukanController/getSpesialisApi", // Endpoint untuk mengambil data spesialis
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let spesialisSelect = $("#spsRjknGetApi"); // Target dropdown spesialis
        spesialisSelect.empty(); // Kosongkan dropdown sebelum diisi
        spesialisSelect.append(
          '<option value="">-- Pilih Spesialis --</option>',
        ); // Tambahkan opsi default

        // Looping data spesialis dan masukkan ke dropdown
        $.each(response.data, function (index, spesialis) {
          spesialisSelect.append(
            `<option value="${spesialis.kdSpesialis}">${spesialis.nmSpesialis}</option>`,
          );
        });
      } else {
        alert("Data Spesialis tidak ditemukan.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error mengambil data Spesialis: " + xhr.responseText);
    },
  });
}

function cekRujukan(episode_id) {
  return $.ajax({
    url: BASE_URL + "DokterController/cekRujukByEpisode", // Ganti dengan URL controller Anda
    method: "POST",
    dataType: "JSON",
    data: { episode_id: episode_id },
  });
}

// Tampilkan atau sembunyikan grup TACC
$('input[name="tacc_main"]').change(function () {
  if ($("#tacc_ya").is(":checked")) {
    $("#taccOptions").slideDown();
  } else {
    $("#taccOptions").slideUp();
    hideAllSubs();
    $('input[name="alasanTacc"]').prop("checked", false);
    $('input[name="subAlasanTacc"]').prop("checked", false);
  }
});

// $('input[name="tacc_main"]').change(function () {
//   const val = $(this).val();

//   if (val === "ya") {
//     $("#taccOptions").slideDown();
//     $("#sub_alasan").slideDown();
//   } else {
//     $("#taccOptions").slideUp();
//     $("#sub_alasan").slideUp();
//     $('input[name="alasanTacc"]').prop("checked", false);
//     $("#subAlasanTacc").val("");
//   }
// });

// $('input[name="alasanTacc"]').change(function () {
//   $("#sub_alasan").slideDown();
// });

// Saat memilih salah satu kategori
$('input[name="alasanTacc"]').change(function () {
  hideAllSubs();
  const val = $(this).val();
  if (val === "Time") $("#sub_time").slideDown();
  if (val === "Age") $("#sub_age").slideDown();
  if (val === "Complication") $("#sub_komplikasi").slideDown();
  if (val === "Comorbidity") $("#sub_comorbid").slideDown();
});

// $('input[name="alasanTacc"]').change(function () {
//   // Tidak perlu lagi hideAllSubs() karena hanya ada satu sub sekarang
//   $("#sub_alasan").slideDown(); // Tampilkan input alasan TACC umum
// });

// $('input[name="tacc_main"]').change(function () {
//   if ($(this).val() === "ya") {
//     $("#taccOptions").slideDown(); // Tampilkan semua opsi utama alasan
//     $("#sub_alasan").slideDown(); // Tampilkan input alasan
//   } else {
//     $("#taccOptions").slideUp(); // Sembunyikan jika tidak
//     $("#sub_alasan").slideUp(); // Sembunyikan input alasan
//   }
// });

$("#spsRjknKhssGetApi").change(function () {
  let spesialisId = $(this).val();
  if (spesialisId) {
    $.ajax({
      url: BASE_URL + "RujukanController/getSubSpesialisApi", // Endpoint untuk mengambil data sub-spesialis
      type: "GET",
      data: {
        kdSpesialis: spesialisId,
      }, // Kirim ID spesialis sebagai parameter
      dataType: "json",
      success: function (response) {
        let subSpesialisSelect = $("#subSpRjknKhssGetApi"); // Target dropdown sub-spesialis
        subSpesialisSelect.empty(); // Kosongkan dropdown sebelum diisi
        subSpesialisSelect.append(
          '<option value="">-- Pilih Sub Spesialis --</option>',
        ); // Tambahkan opsi default

        if (response.status === "success") {
          // Looping data sub-spesialis dan masukkan ke dropdown
          $.each(response.data, function (index, subSpesialis) {
            subSpesialisSelect.append(
              `<option value="${subSpesialis.kdSubSpesialis}">${subSpesialis.nmSubSpesialis}</option>`,
            );
          });
        } else {
          alert("Data Sub Spesialis tidak ditemukan.");
        }
      },
      error: function (xhr, status, error) {
        console.error(
          "Error mengambil data Sub Spesialis: " + xhr.responseText,
        );
      },
    });
  } else {
    // Reset dan blokir dropdown berikutnya
    // $("#subSpRjknGetApi").prop("disabled", true).val("");
    // $("#plhFasRjknKhssApi").prop("disabled", true).val("");
  }
});

$("#spsRjknGetApi").change(function () {
  let spesialisId = $(this).val();
  if (spesialisId) {
    // $("#subSpRjknGetApi").prop("disabled", false);
    $.ajax({
      url: BASE_URL + "RujukanController/getSubSpesialisApi", // Endpoint untuk mengambil data sub-spesialis
      type: "GET",
      data: {
        kdSpesialis: spesialisId,
      }, // Kirim ID spesialis sebagai parameter
      dataType: "json",
      success: function (response) {
        let subSpesialisSelect = $("#subSpRjknGetApi"); // Target dropdown sub-spesialis
        subSpesialisSelect.empty(); // Kosongkan dropdown sebelum diisi
        subSpesialisSelect.append(
          '<option value="">-- Pilih Sub Spesialis --</option>',
        ); // Tambahkan opsi default

        if (response.status === "success") {
          // Looping data sub-spesialis dan masukkan ke dropdown
          $.each(response.data, function (index, subSpesialis) {
            subSpesialisSelect.append(
              `<option value="${subSpesialis.kdSubSpesialis}">${subSpesialis.nmSubSpesialis}</option>`,
            );
          });
        } else {
          alert("Data Sub Spesialis tidak ditemukan.");
        }
      },
      error: function (xhr, status, error) {
        console.error(
          "Error mengambil data Sub Spesialis: " + xhr.responseText,
        );
      },
    });
  } else {
    // Reset dan blokir dropdown berikutnya
    $("#subSpRjknGetApi").prop("disabled", true).val("");
    $("#plhFasRjknKhssApi").prop("disabled", true).val("");
  }
});

function getSaranaApi() {
  $.ajax({
    url: BASE_URL + "RujukanController/getSaranaApi", // Endpoint untuk mengambil data spesialis
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let saranaSelect = $("#saranaGetApi"); // Target dropdown spesialis
        saranaSelect.empty(); // Kosongkan dropdown sebelum diisi
        saranaSelect.append('<option value="">-- Pilih Sarana --</option>'); // Tambahkan opsi default
        saranaSelect.append('<option value="0">-</option>');
        // Looping data spesialis dan masukkan ke dropdown
        $.each(response.data, function (index, sarana) {
          saranaSelect.append(
            `<option value="${sarana.kdSarana}">${sarana.nmSarana}</option>`,
          );
        });
      } else {
        alert("Data Sarana tidak ditemukan.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error mengambil data Sarana: " + xhr.responseText);
    },
  });
}

$("#subSpRjknGetApi, #saranaGetApi,#tglEstRujuk").change(function () {
  let subSpesialisId = $("#subSpRjknGetApi").val(); // Ambil ID sub spesialis yang dipilih
  let saranaId = $("#saranaGetApi").val(); // Ambil ID sarana yang dipilih
  let tglEstRujuk = $("#tglEstRujuk").val();
  if (subSpesialisId && saranaId) {
    // alert("qqq");
    $.ajax({
      url: BASE_URL + "RujukanController/getFaskesApi", // Endpoint untuk mengambil data sub-spesialis
      type: "GET",
      data: {
        kdSubSpesialis: subSpesialisId,
        kdSarana: saranaId,
        tglEstRujuk: tglEstRujuk,
      }, // Kirim ID spesialis sebagai parameter
      dataType: "json",
      success: function (response) {
        let faskesSelect = $("#faskesRjknGetApi"); // Target dropdown sub-spesialis
        faskesSelect.empty(); // Kosongkan dropdown sebelum diisi
        faskesSelect.append('<option value="">-- Pilih Faskes --</option>'); // Tambahkan opsi default

        if (response.status === "success") {
          // Looping data sub-spesialis dan masukkan ke dropdown
          $.each(response.data, function (index, faskes) {
            faskesSelect.append(
              `<option value="${faskes.kdppk}">${faskes.nmppk}</option>`,
            );
          });
        } else {
          alert("Data Faskes tidak ditemukan.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error mengambil data Faskes: " + xhr.responseText);
      },
    });
  }
});

$("input[name='tipeRjkn']").change(function () {
  if ($("#rjknKhususYa").is(":checked")) {
    $("#RujukanKhususRow").show();
    $("#RujukanRow").hide();
    $("#cttnKhssRow").show();

    $("#tglEstRujuk").val("");
    $("#spsRjknGetApi").val("");
    $("#subSpRjknGetApi").val("");
    $("#saranaGetApi").val("");
    $("#faskesRjknGetApi").val("");

    getRjknKhssApi();
    getSpesialisKhssApi();
  } else {
    $("#RujukanRow").show();
    $("#RujukanKhususRow").hide();
    $("#cttnKhssRow").show();

    $("#tglEstRujukKhss").val("");
    $("#plhRjknKhssApi").val("");
    $("#plhFasRjknKhssApi").val("");
    $("#spsRjknKhssGetApi").val("");
    $("#subSpRjknKhssGetApi").val("");

    getSpesialisApi();
  }
});

$("#plhRjknKhssApi").change(function () {
  let rjknKhsus = $(this).val();
  let tglEstRujuk = $("#tglEstRujukKhss").val();
  let nokartuprov = $(".nokartuprov").val();

  // alert(rjknKhsus);
  // alert(tglEstRujuk);
  // alert(nokartuprov);

  // alert(subSpRjknGetApi);
  if (rjknKhsus == "THA" || rjknKhsus == "HEM") {
    $("#RjknKhssSpesialis").show();

    $("#subSpRjknKhssGetApi")
      .off("change")
      .on("change", function () {
        let subSpRjknGetApi = $(this).val();
        console.log("Sub Spesialis:", subSpRjknGetApi);
        if (subSpRjknGetApi) {
          // $.ajax({
          //   url: BASE_URL + "RujukanController/getFaskesKhususApi", // Endpoint untuk mengambil data sub-spesialis
          //   type: "GET",
          //   data: {
          //     kdRjknKhsus: rjknKhsus,
          //     tglEstRujuk: tglEstRujuk,
          //     nokartuprov: nokartuprov,
          //     subSpRjknGetApi: subSpRjknGetApi,
          //   }, // Kirim ID spesialis sebagai parameter
          //   dataType: "json",
          //   success: function (response) {
          //     let FasKhususSelect = $("#plhFasRjknKhssApi"); // Target dropdown sub-spesialis
          //     FasKhususSelect.empty(); // Kosongkan dropdown sebelum diisi
          //     FasKhususSelect.append(
          //       '<option value="">-- Pilih Faskes khusus --</option>'
          //     ); // Tambahkan opsi default

          //     if (response.status === "success") {
          //       // Looping data sub-spesialis dan masukkan ke dropdown
          //       $.each(response.data, function (index, faskesKhusus) {
          //         FasKhususSelect.append(
          //           `<option value="${faskesKhusus.kdppk}">${faskesKhusus.nmppk}</option>`
          //         );
          //       });
          //     } else {
          //       Swal.fire({
          //         title: "Data Tidak Ditemukan",
          //         text: "Data Faskes Rujukan Khusus tidak ditemukan.",
          //         icon: "error",
          //         confirmButtonText: "OK",
          //       });
          //     }
          //   },
          //   error: function (xhr, status, error) {
          //     console.error(
          //       "Error mengambil data Rujukan Khusus : " + xhr.responseText
          //     );
          //   },
          // });

          getFaskesRjknKhss(
            rjknKhsus,
            tglEstRujuk,
            nokartuprov,
            subSpRjknGetApi,
          );
        }
      });
  } else {
    $("#RjknKhssSpesialis").hide();

    // alert(rjknKhsus);
    // alert(tglEstRujuk);
    // alert(nokartuprov);
    // alert(subSpRjknGetApi);
    getFaskesRjknKhss(rjknKhsus, tglEstRujuk, nokartuprov);

    // getFaskesRjknKhss(rjknKhsus, tglEstRujuk, nokartuprov, (subSpRjknGetApi = null)); // Tetap jalankan AJAX
  }

  // alert("123");
});

function getFaskesRjknKhss(
  kdRjknKhsus,
  tglEstRujuk,
  nokartuprov,
  subSpRjknGetApi = null,
) {
  $.ajax({
    url: BASE_URL + "RujukanController/getFaskesKhususApi", // Endpoint untuk mengambil data sub-spesialis
    type: "GET",
    data: {
      kdRjknKhsus: kdRjknKhsus,
      tglEstRujuk: tglEstRujuk,
      nokartuprov: nokartuprov,
      subSpRjknGetApi: subSpRjknGetApi,
    },
    dataType: "json",
    success: function (response) {
      let FasKhususSelect = $("#plhFasRjknKhssApi");
      FasKhususSelect.empty();
      FasKhususSelect.append(
        '<option value="">-- Pilih Faskes khusus3 --</option>',
      );

      if (response.status === "success") {
        $.each(response.data, function (index, faskesKhusus) {
          FasKhususSelect.append(
            `<option value="${faskesKhusus.kdppk}">${faskesKhusus.nmppk}</option>`,
          );
        });
      } else {
        Swal.fire({
          title: "Data Tidak Ditemukan",
          text: "Data Faskes Rujukan Khusus tidak ditemukan.",
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error(
        "Error mengambil data Rujukan Khusus : " + xhr.responseText,
      );
    },
  });
}

function getRjknKhssApi() {
  // alert("Oke");
  $.ajax({
    url: BASE_URL + "RujukanController/getRjknKhssApi", // Endpoint untuk mengambil data spesialis
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let rjknKhssSelect = $("#plhRjknKhssApi"); // Target dropdown spesialis
        rjknKhssSelect.empty(); // Kosongkan dropdown sebelum diisi
        rjknKhssSelect.append(
          '<option value="">-- Pilih Rujukan Khusus --</option>',
        ); // Tambahkan opsi default
        // Looping data spesialis dan masukkan ke dropdown
        $.each(response.data, function (index, rjknKhss) {
          rjknKhssSelect.append(
            `<option value="${rjknKhss.kdKhusus}">${rjknKhss.nmKhusus}</option>`,
          );
        });
      } else {
        alert("Data Rujukan Khusus tidak ditemukan.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error mengambil data Sarana: " + xhr.responseText);
    },
  });
}

function loadtindakanmodal() {
  // alert("loadtindakanmodal");
  $.ajax({
    url: BASE_URL + "DokterController/loadtindakanmodal",
    method: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
    },
    success: function (data) {
      $("#listtindakan").html("");
      if (data.Responcode == "00") {
        var tindakan = "";
        var hasil = data.Responresult;
        for (var i in hasil) {
          looptambahtindakan(
            hasil[i].layan_id,
            hasil[i].nama_layan,
            hasil[i].qty,
            hasil[i].harga,
            hasil[i].trans_alkes,
          );
        }
      }
    },
  });
  return false;
}

function caritindakan() {
  $.ajax({
    url: BASE_URL + "DokterController/loadmastertindakan",
    method: "POST",
    dataType: "JSON",
    data: {
      pencarian: $("#input_caritindakan").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#listmastertindakan").html("");
      } else {
        $("#listmastertindakan").html("");
        var tindakan = "";
        var hasil = data.Responresult;
        for (var i in hasil) {
          tindakan += "<tr>";
          tindakan += `<td><a href="#" class="select_tindakan" style="color:black; font-size: 1em" ondblclick="pilihtindakan(event, '${hasil[i].layan_id}', '${hasil[i].nama_layan1}', '${hasil[i].harga}')">${hasil[i].nama_layan1}</a></td>`;
          tindakan += `<td align='right'>${parseFloat(
            hasil[i].harga,
          ).toLocaleString("id-ID")}</td>`;
          tindakan += "</tr>";
        }

        $("#listmastertindakan").html(tindakan);
      }
    },
  });
  return false;
}

function pilihtindakan(event, layanid, tindakan, harga) {
  event.preventDefault();

  let table = document.getElementById("listtindakan");
  let bolehTambah = true;

  for (let i = 0, row; (row = table.rows[i]); i++) {
    let tindakanid = row.cells[0].innerText;

    if (tindakanid == layanid) {
      bolehTambah = false;
    }
  }

  if (bolehTambah) {
    looptambahtindakan(layanid, tindakan, 1, harga, "0");

    $("#modal_caritindakan").modal("hide");
  } else {
    $("#alertModalTindakan").modal("show");
  }
}

function deletetindakan(button) {
  let row = button.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function looptambahtindakan(layanid, tindakan, jumlah, harga, transalkes) {
  let table = document.getElementById("listtindakan");
  let newRow = table.insertRow(table.rows.length);

  let td0 = newRow.insertCell(0);
  td0.style.display = "none";
  td0.innerHTML = layanid;

  newRow.insertCell(1).innerHTML = tindakan;

  let td2 = newRow.insertCell(2);
  td2.align = "center";
  td2.style.padding = 0;
  td2.innerHTML =
    '<input type="number" min="1" style="width: 80px; height: 20px" class="text-right" onkeypress="return onlyNumberKey(event)" value=' +
    jumlah +
    "></input>";
  td2.style.verticalAlign = "middle";

  // Tambahkan event listener untuk perubahan nilai pada input
  let inputQty = td2.querySelector("input");
  inputQty.addEventListener("input", function () {
    let qty = parseFloat(inputQty.value) || 0;
    let harga = parseFloat(td6.innerHTML) || 0;
    td3.innerHTML = (qty * harga).toLocaleString("id-ID"); // Menghitung dan menampilkan harga total
  });

  let td3 = newRow.insertCell(3);
  td3.align = "right";
  td3.style.width = "20%";
  td3.innerHTML = (jumlah * harga).toLocaleString("id-ID");

  let td4 = newRow.insertCell(4);
  td4.style.width = "10%";
  let deleteButton = document.createElement("button");
  deleteButton.innerHTML = "Hapus";
  deleteButton.onclick = function () {
    deletetindakan(this);
  };
  td4.appendChild(deleteButton);

  let td5 = newRow.insertCell(5);
  td5.style.display = "none";
  td5.innerHTML = transalkes;

  let td6 = newRow.insertCell(6);
  td6.align = "right";
  td6.style.width = "20%";
  td6.innerHTML = harga;
  td6.style.display = "none";

  $("#pencarian_tindakan").val("");
}

function simpantindakan(event) {
  event.preventDefault();

  let table = document.getElementById("listtindakan");
  let datatindakan = [];
  let bolehSimpan = true;

  for (let i = 0, row; (row = table.rows[i]); i++) {
    let tindakan = {
      layanid: row.cells[0].innerText,
      namalayan: row.cells[1].innerText,
      qty: row.cells[2].querySelector("input").value,
      transalkes: row.cells[5].innerText,
    };
    datatindakan.push(tindakan);
  }

  if (bolehSimpan == true) {
    $.ajax({
      url: BASE_URL + "DokterController/simpantindakan",
      method: "POST",
      dataType: "JSON",
      cache: false,
      data: {
        lokasiid: $(".idlokasi").val(),
        episodeid: $(".idepisode").val(),
        transid: $(".idtrans").val(),
        pasienid: $(".idpasien").val(),
        transco: $(".idtransco").val(),
        tglpoli: $(".tanggalpoli").val(),
        dokterid: $(".iddokter").val(),
        createdby: $("#dokterId").text(),
        datatindakan: datatindakan,
      },
      success: function (response) {
        if (response.Responcode == "01") {
          loaditemharga();

          Swal.fire({
            title: "Tindakan Berhasil Disimpan",
            icon: "success",
            confirmButtonText: "OK",
          }).then((result) => {
            if (result.isConfirmed) {
              $("#listtindakan").html("");
            }
          });
        }
      },
    });
  } else {
    Swal.fire({
      title: "Tidak bisa simpan",
      icon: "error",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        //
      }
    });
  }
  return false;
}

function onlyNumberKey(evt) {
  // Only ASCII character in that range allowed
  var ASCIICode = evt.which ? evt.which : evt.keyCode;
  if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) return false;
  return true;
}

$(document).ready(function () {
  $("#btn_mulaiperiksa").hide();
  $("#btn_simpansoap").hide();
  $("#cari_kode_icd10").prop("readonly", true);
  $("#cari_kode_icd10_sek").prop("readonly", true);
  $("#cari_kode_icd9").prop("readonly", true);

  document
    .getElementById("toggleHidePasien")
    .addEventListener("click", function () {
      hidePanelPasien();
    });

  document
    .getElementById("toggleViewPasien")
    .addEventListener("click", function () {
      showPanelPasien();
    });

  document
    .getElementById("toggleViewPasien")
    .addEventListener("click", function () {
      showPanelPasien();
    });

  function showPanelPasien() {
    var listPasien = document.getElementById("listpasien");
    var pemeriksaan = document.getElementById("pemeriksaan");

    if (listPasien.style.display === "none") {
      listPasien.style.display = "block";
      pemeriksaan.className = "col-sm-9";
    }
  }

  document
    .getElementById("cari_kode_icd10")
    .addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();

        if (adasoap === "Y") {
          $("#input_pencarian10").val($("#cari_kode_icd10").val());
          cariicd10();
          $("#modal_icd10").modal("show");
        }
      }
    });

  document
    .getElementById("cari_kode_icd10_sek")
    .addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();

        if (adasoap === "Y") {
          $("#input_pencarian10_sek").val($("#cari_kode_icd10_sek").val());
          cariicd10sek();
          $("#modal_icd10_sek").modal("show");
        }
      }
    });

  document
    .getElementById("cari_kode_icd9")
    .addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();

        if (adasoap === "Y") {
          $("#input_pencarian9").val($("#cari_kode_icd9").val());
          cariicd9();
          $("#modal_icd9").modal("show");
        }
      }
    });

  const pencarianTindakan = document.getElementById("pencarian_tindakan");

  if (pencarianTindakan) {
    pencarianTindakan.addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();

        $("#input_caritindakan").val($("#pencarian_tindakan").val());

        // Aman: cek dulu apakah function ada
        if (typeof caritindakan === "function") {
          caritindakan();
        }

        $("#modal_caritindakan").modal("show");
      }
    });
  }

  // document
  //   .getElementById("pencarian_tindakan")
  //   .addEventListener("keydown", function (event) {
  //     if (event.key === "Enter") {
  //       event.preventDefault();

  //       $("#input_caritindakan").val($("#pencarian_tindakan").val());
  //       caritindakan();
  //       $("#modal_caritindakan").modal("show");
  //     }
  //   });

  const pencarianObat = document.getElementById("pencarian_obat");

  if (pencarianObat) {
    pencarianObat.addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();

        $("#input_cariobat").val($("#pencarian_obat").val());

        // Aman: cek dulu apakah fungsi tersedia
        if (typeof cariobat === "function") {
          cariobat();
        }

        $("#modal_cariobat").modal("show");
      }
    });
  }

  // document.getElementById("pencarian_obat").addEventListener("keydown", function (event) {
  //     if (event.key === "Enter") {
  //       event.preventDefault();

  //       $("#input_cariobat").val($("#pencarian_obat").val());
  //       cariobat();
  //       $("#modal_cariobat").modal("show");
  //     }
  //   });

  // === ENABLE / DISABLE INPUT SYMPTOM ===
  $(document).on("change", ".symptom-check", function () {
    const target = document.getElementById(this.dataset.target);
    if (!target) return;

    target.disabled = !this.checked;
    if (!this.checked) target.value = "";
  });

  // === FUNGSI GAMBAR DI CANVAS ===
  function enableDraw(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    // Kalau sudah pernah di-bind, jangan dobel
    if (canvas.dataset.drawBound === "1") return;
    canvas.dataset.drawBound = "1";

    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    let drawing = false;

    const getPos = (e) => {
      const rect = canvas.getBoundingClientRect();
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      return {
        x: clientX - rect.left,
        y: clientY - rect.top,
      };
    };

    // Mouse
    canvas.addEventListener("mousedown", (e) => {
      drawing = true;
      ctx.beginPath();
      const { x, y } = getPos(e);
      ctx.moveTo(x, y);
    });

    canvas.addEventListener("mousemove", (e) => {
      if (!drawing) return;
      const { x, y } = getPos(e);
      ctx.lineTo(x, y);
      ctx.stroke();
    });

    ["mouseup", "mouseleave"].forEach((ev) => {
      canvas.addEventListener(ev, () => {
        drawing = false;
        ctx.beginPath();
      });
    });

    // Touch (biar bisa digambar dari tablet/layar sentuh juga)
    canvas.addEventListener("touchstart", (e) => {
      e.preventDefault();
      drawing = true;
      ctx.beginPath();
      const { x, y } = getPos(e);
      ctx.moveTo(x, y);
    });

    canvas.addEventListener("touchmove", (e) => {
      e.preventDefault();
      if (!drawing) return;
      const { x, y } = getPos(e);
      ctx.lineTo(x, y);
      ctx.stroke();
    });

    canvas.addEventListener("touchend", () => {
      drawing = false;
      ctx.beginPath();
    });
  }

  // helper: ambil base64 PNG dari canvas (tanpa prefix)
  function canvasToBase64(id) {
    const canvas = document.getElementById(id);
    if (!canvas || !canvas.toDataURL) return "";
    const dataURL = canvas.toDataURL("image/png");
    return dataURL.replace(/^data:image\/png;base64,/, "");
  }

  $(document).on("click", "#btn-simpan-paliatif", function (e) {
    simpanpaliatif(e);
  });

  // handler submit modal
  function simpanpaliatif(e) {
    e.preventDefault();

    // alert("122");

    const form = document.getElementById("formPaliatif");
    const fd = new FormData(form);

    // isi hidden field gambar dari canvas
    fd.set("lung_left_img", canvasToBase64("lung_left"));
    fd.set("lung_right_img", canvasToBase64("lung_right"));
    fd.set("abdomen_img", canvasToBase64("abdomen_canvas"));
    fd.set("genogram_img", canvasToBase64("genogram_canvas"));

    Swal.fire({
      title: "Simpan asesmen paliatif?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, simpan",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: BASE_URL + "Asessment/savePaliatif",
        type: "POST",
        data: fd,
        dataType: "json",
        processData: false,
        contentType: false,
        beforeSend: function () {
          Swal.fire({
            title: "Menyimpan...",
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false,
          });
        },
        success: function (res) {
          Swal.close();
          if (res && res.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: res.message || "Asesmen paliatif tersimpan.",
            }).then(() => {
              // tutup modal, lanjut isi di dashboard dokter
              const modalEl = document.getElementById("modalPaliatif");
              const modalInstance = bootstrap.Modal.getInstance(modalEl);
              if (modalInstance) modalInstance.hide();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Gagal",
              text: res && res.message ? res.message : "Gagal menyimpan data.",
            });
          }
        },
        error: function () {
          Swal.close();
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Gagal menyimpan (AJAX error).",
          });
        },
      });
    });
  }

  // === INISIALISASI SAAT MODAL PALIATIF DIBUKA ===
  $("#modalPaliatif").on("shown.bs.modal", function () {
    ["lung_left", "lung_right", "abdomen_canvas", "genogram_canvas"].forEach(
      enableDraw,
    );
  });
});

$("#btnSetujuTacc").on("click", function () {
  // const rujukDenganTacc = $("#tacc_ya").is(":checked");
  // const rujukTanpaTacc = $("#tacc_tidak").is(":checked");

  // if (!rujukDenganTacc && !rujukTanpaTacc) {
  //   Swal.fire({
  //     icon: "error",
  //     title: "Belum Memilih",
  //     text: "Silakan pilih apakah rujuk dengan atau tanpa TACC.",
  //   });
  //   return;
  // }

  // Ambil ulang data form (agar selalu fresh)
  const dataToSend = {
    lokasiid: $(".idlokasi").val(),
    episodeid: $(".idepisode").val(),
    pasienid: $(".idpasien").val(),
    transco: $(".idtransco").val(),
    transid: $(".idtrans").val(),
    poliid: $(".idpoli").val(),
    tanggal: $(".tanggalpoli").val(),
    dokterid: $(".iddokter").val(),
    rekananid: $(".idrekanan").val(),
    createdby: $("#dokterId").text(),
    icd10: $("#icd10_kode").val(),
    diagnosa: $("#cari_nama_icd10").val(),
    diagnonspesialis: $("#diag_non_spesialis").val(),
    alergiMakanan: $("#alergiMakanan").val(),
    alergiUdara: $("#alergiUdara").val(),
    alergiObat: $("#alergiObat").val(),
    statPlg: $("#statPlgGetApi").val(),
    kdsadar: $("#sadarGetApi").val(),
    kdprognosa: $("#plhPrognosaApi").val(),
    noKartu: $("#nokartuprov").val(),
    konfirmasiRujukan: true,
  };

  // if (rujukDenganTacc) {
  // const alasan = $('input[name="alasanTacc"]:checked').val();
  // const subAlasan = $('input[name="subAlasanTacc"]:checked').val();

  // const subAlasan = $("#subAlasanTacc").val().trim();

  // alert(subAlasan);

  // if (!alasan) {
  //   Swal.fire({
  //     icon: "warning",
  //     title: "Pilih Kategori TACC",
  //     text: "Silakan pilih salah satu alasan TACC (Time, Age, dll.)",
  //   });
  //   return;
  // }

  // alert(alasan);
  // if (alasan === "Time") {
  //   dataToSend.kdTacc = "1";
  // } else if (alasan === "Age") {
  //   dataToSend.kdTacc = "2";
  // } else if (alasan === "Complication") {
  //   dataToSend.kdTacc = "3";
  // } else if (alasan === "Comorbidity") {
  //   dataToSend.kdTacc = "4";
  // }

  // subAlasanTacc;

  // if (!subAlasan) {
  //   Swal.fire({
  //     icon: "warning",
  //     title: "Alasan TACC Belum Diisi",
  //     text: "Silakan isi alasan tambahan TACC terlebih dahulu.",
  //   });
  //   return;

  // dataToSend.alasanTacc = subAlasan;
  // }

  // alert(subAlasanTacc);

  // if (!subAlasanTacc) {
  //   Swal.fire({
  //     icon: "warning",
  //     title: "Sub Alasan Belum DIisi",
  //     text: `Silakan Isi Alasan TACC`,
  //   });
  //   return;
  // }

  // dataToSend.alasanTacc = alasan;
  // dataToSend.alasanTacc = subAlasan;
  // dataToSend.kdTacc = subAlasan;
  // }

  // if (rujukTanpaTacc) {
  //   dataToSend.kdTacc = "-1";
  //   dataToSend.alasanTacc = "";
  // }

  // console.log("oke" + kdTacc + "dan" + subAlasanTacc);
  // const subAlasan = $("#subAlasanTacc").val().trim();

  // AJAX kirim dataTACC
  $.ajax({
    url: BASE_URL + "DokterController/prosesselesai",
    method: "POST",
    dataType: "JSON",
    data: dataToSend,
    success: function (res) {
      if (res.Responcode === "01") {
        $("#modalTacc").modal("hide");
        Swal.fire(
          "Berhasil",
          "Pemeriksaan berhasil diselesaikan.",
          "success",
        ).then(() => {
          showPanelPasien();
        });
      } else {
        Swal.fire(
          "Gagal",
          res.Respondesc || "Terjadi kesalahan saat kirim ulang data TACC.",
          "error",
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error", xhr, status, error);
      Swal.fire(
        "Error",
        "Terjadi kesalahan pada server saat kirim data TACC.",
        "error",
      );
    },
  });

  // alert(rujukDenganTacc);
});

const btn_bukaracik = document.getElementById("btn_bukaracik");

if (btn_bukaracik) {
  btn_bukaracik.addEventListener("click", function () {
    // Reset array
    selectedObatArray = [];
    selectedObatArray2 = [];

    // Ambil tabel
    let table = document.getElementById("resultresep");
    if (!table) return; // Tabel tidak ada → stop

    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
      let checkbox = rows[i].querySelector("td:nth-child(1) input");

      // Fallback aman untuk input lain (tidak error jika undefined)
      const getInput = (cellIndex) => {
        let inp = rows[i].cells[cellIndex]?.querySelector("input");
        return inp ? inp.value : "";
      };

      let rowData = {
        vkettipe: rows[i].cells[2]?.innerHTML ?? "",
        vobatid: rows[i].cells[3]?.innerHTML ?? "",
        vnamaobat: rows[i].cells[4]?.innerHTML ?? "",
        vqty: getInput(5),
        vstok: rows[i].cells[6]?.innerHTML ?? "",
        vsatuan: rows[i].cells[7]?.innerHTML ?? "",
        vfreedosis: getInput(8),
        vsigna: rows[i].cells[9]?.innerHTML ?? "",
        vsignateks: getInput(10),
        vcatatan: getInput(11),
        vurut: rows[i].cells[12]?.innerHTML ?? "",
        vtipeobat: rows[i].cells[13]?.innerHTML ?? "",
        vsatuanid: rows[i].cells[16]?.innerHTML ?? "",
      };

      if (checkbox && checkbox.checked) {
        selectedObatArray.push(rowData);
      } else {
        selectedObatArray2.push(rowData);
      }
    }

    // Panggil function lain
    if (typeof loadpembungkus === "function") loadpembungkus();
    if (typeof loadsigna === "function") loadsigna();

    // Reset input modal
    $("#jmlracik").val("");
    $("#namaracik").val("");

    // Tampilkan modal
    $("#modal_buatracik").modal("show");
  });
}

document.getElementById("reloadButton").addEventListener("click", function () {
  let poli_id = $("#poliTabs .nav-link.active").data("poli");
  let dokter_id = $("#dokterId").text().trim();
  let tanggal = $("#filterTanggal").val();

  // alert(poli_id);
  loadPasien(dokter_id, poli_id, tanggal);
  // location.reload();
});

function loadpembungkus() {
  $.ajax({
    // url: "<?= base_url('PoliDokter/loadpembungkus') ?>",
    url: BASE_URL + "DokterController/loadpembungkus",
    method: "POST",
    dataType: "JSON",
    cache: false,
    success: function (data) {
      if (data.Responcode == "00") {
        var bungkus = "";
        data.Responresult.map(function (d) {
          bungkus +=
            '<option value="' +
            d.obat_id +
            '" data-satuan="' +
            d.keterangan +
            '" data-satuanid="' +
            d.satuan_id +
            '" data-stok="' +
            d.stok +
            '">' +
            d.nama_obat +
            "</option>";
        });
        $("#master-bungkus").html(bungkus);
      }
    },
  });
  return false;
}

function cariicd9() {
  $.ajax({
    url: BASE_URL + "DokterController/loadmastericd9",
    method: "POST",
    dataType: "JSON",
    cache: false,
    data: {
      pencarian: $("#input_pencarian9").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#listmastericd9").html("");
      } else {
        $("#listmastericd9").html("");
        var tindakan = "";
        var hasil = data.Responresult;
        for (var i in hasil) {
          tindakan += "<tr>";
          tindakan += `<td><a href="#" class="select_icd9" style="color:black; font-size: 1em" ondblclick="pilihicd9(event, '${hasil[i].kode}', '${hasil[i].kode_icd}', '${hasil[i].long_description}')">${hasil[i].kode_icd}</a></td>`;
          tindakan += `<td><a href="#" class="select_icd9" style="color:black; font-size: 1em" ondblclick="pilihicd9(event, '${hasil[i].kode}', '${hasil[i].kode_icd}', '${hasil[i].long_description}')">${hasil[i].long_description}</a></td>`;
          tindakan += "</tr>";
        }

        $("#listmastericd9").html(tindakan);
      }
    },
  });
  return false;
}

function cariicd10() {
  $.ajax({
    url: BASE_URL + "DokterController/loadicd10",
    method: "POST",
    dataType: "JSON",
    data: {
      pencarian: $("#input_pencarian10").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        $("#listicd10utama").html("");
      } else {
        $("#listicd10utama").html("");
        var diagnosa = "";
        var hasil = data.Responresult;
        for (var i in hasil) {
          // alert(hasil[i].non_spesialis);
          const isNonSpesialis = hasil[i].non_spesialis === "Ya";
          const badgeClass = isNonSpesialis
            ? "badge bg-danger"
            : "badge bg-success";

          diagnosa += "<tr>";
          diagnosa += `<td class="text-center"><a href="#" class="select_icd10utama" style="color:black; font-size: 1em" ondblclick="pilihicd10utama(event, '${hasil[i].kode}', '${hasil[i].kode_icd}', '${hasil[i].nm_diag1}', '${hasil[i].non_spesialis}')">${hasil[i].kode_icd}</a></td>`;
          diagnosa += `<td><a href="#" class="select_icd10utama" style="color:black; font-size: 1em" ondblclick="pilihicd10utama(event, '${hasil[i].kode}', '${hasil[i].kode_icd}', '${hasil[i].nm_diag1}', '${hasil[i].non_spesialis}')">${hasil[i].nm_diag1}</a></td>`;
          diagnosa += "</tr>";
        }

        $("#listicd10utama").html(diagnosa);
      }
    },
  });
  return false;
}

function cariicd10sek() {
  $.ajax({
    url: BASE_URL + "DokterController/loadicd10",
    method: "POST",
    dataType: "JSON",
    data: {
      pencarian: $("#input_pencarian10_sek").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        $("#listicd10sekunder").html("");
      } else {
        $("#listicd10sekunder").html("");
        var diagnosa = "";
        var hasil = data.Responresult;
        for (var i in hasil) {
          diagnosa += "<tr>";
          diagnosa += `<td><a href="#" class="select_icd10sekunder" style="color:black; font-size: 1em" ondblclick="pilihicd10sekunder(event, '${hasil[i].kode}', '${hasil[i].kode_icd}', '${hasil[i].nm_diag1}')">${hasil[i].kode_icd}</a></td>`;
          diagnosa += `<td><a href="#" class="select_icd10sekunder" style="color:black; font-size: 1em" ondblclick="pilihicd10sekunder(event, '${hasil[i].kode}', '${hasil[i].kode_icd}', '${hasil[i].nm_diag1}')">${hasil[i].nm_diag1}</a></td>`;
          diagnosa += "</tr>";
        }

        $("#listicd10sekunder").html(diagnosa);
      }
    },
  });
  return false;
}
function mulaiperiksa() {
  $.ajax({
    url: BASE_URL + "DokterController/mulaiperiksa",
    type: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      transid: $(".idtrans").val(),
      pasienid: $(".idpasien").val(),
      poliid: $(".idpoli").val(),
      dokterid: $(".iddokter").val(),
      createdby: $("#dokterId").text(),
    },
    success: function (response) {
      if (response.Responcode == "00") {
        ceksudahmulai();
      }
    },
  });

  return false;
}

function convertDateToDDMMYYYY(dateStr) {
  // Pisahkan string tanggal berdasarkan tanda "-"
  let dateParts = dateStr.split("-");

  // Tanggal diambil dari urutan [2], Bulan [1], Tahun [0]
  let day = dateParts[2];
  let month = dateParts[1];
  let year = dateParts[0];

  // Kembalikan tanggal dalam format dd-mm-yyyy
  return `${day}-${month}-${year}`;
}

function filterPatients() {
  const input = document.getElementById("searchInput");
  const filter = input.value.toLowerCase();
  const patientItems = document.querySelectorAll(".patient-item");

  patientItems.forEach((item) => {
    const nameElement = item.querySelector(".patient-name");
    if (nameElement) {
      const name = nameElement.textContent.toLowerCase();
      if (name.includes(filter)) {
        item.classList.remove("d-none"); // Tampilkan elemen
      } else {
        item.classList.add("d-none"); // Sembunyikan elemen
      }
    }
  });
}

function pilihicd10utama(event, kode, kodeicd, nmdiag, nonspesialis) {
  event.preventDefault();
  // alert(nonspesialis);
  if (kode && kodeicd && nmdiag && nonspesialis) {
    $("#cari_kode_icd10").val(kodeicd);
    $("#cari_nama_icd10").val(nmdiag);
    $("#icd10_kode").val(kode);
    $("#diag_non_spesialis").val(nonspesialis);

    $("#modal_icd10").modal("hide");
  } else {
    alert("Data yang dipilih tidak valid. Silakan coba lagi.");
  }
}

function pilihicd10sekunder(event, kode, kodeicd, nmdiag) {
  event.preventDefault();
  $.ajax({
    url: BASE_URL + "DokterController/simpanicd10sek",
    method: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      transid: $(".idtrans").val(),
      pasienid: $(".idpasien").val(),
      transco: $(".idtransco").val(),
      dokterid: $(".iddokter").val(),
      kode: kode,
      kodeicd: kodeicd,
      nmdiag: nmdiag,
      createdby: $("#dokterId").text(),
    },
    success: function (data) {
      if (data.Responcode == "00") {
        loadicd10sek();
        $("#modal_icd10_sek").modal("hide");
      }
    },
  });

  return false;
}

function hapusicd10(episodeid, pasienid, transdiag) {
  $.ajax({
    // url: "<?= base_url('PoliDokter/hapusicd10') ?>",
    url: BASE_URL + "DokterController/hapusicd10",
    method: "POST",
    dataType: "JSON",
    data: {
      episodeid: episodeid,
      pasienid: pasienid,
      transdiag: transdiag,
    },
    success: function (data) {
      if (data.Responcode == "01") {
      } else {
        loadicd10sek();
      }
    },
  });
  return false;
}

function pilihicd9(event, kode, kodeicd, longdescription) {
  event.preventDefault();
  $.ajax({
    url: BASE_URL + "DokterController/simpanicd9",
    method: "POST",
    dataType: "JSON",
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      transid: $(".idtrans").val(),
      pasienid: $(".idpasien").val(),
      transco: $(".idtransco").val(),
      dokterid: $(".iddokter").val(),
      kode: kode,
      kodeicd: kodeicd,
      longdescription: longdescription,
      createdby: $("#dokterId").text(),
    },
    success: function (data) {
      if (data.Responcode == "00") {
        loadicd9();
        $("#modal_icd9").modal("hide");
      }
    },
  });

  return false;
}

function hapusicd9(episodeid, pasienid, transtin) {
  $.ajax({
    url: BASE_URL + "DokterController/hapusicd9",
    method: "POST",
    dataType: "JSON",
    data: {
      episodeid: episodeid,
      pasienid: pasienid,
      transtin: transtin,
    },
    success: function (data) {
      if (data.Responcode == "01") {
      } else {
        loadicd9();
      }
    },
  });
  return false;
}

function simpansoapxxx() {
  if (
    $.trim($("#subject").val()) != "" &&
    $.trim($("#objective").val()) != "" &&
    $.trim($("#assesment").val()) != "" &&
    $.trim($("#planning").val()) != ""
  ) {
    $.ajax({
      url: BASE_URL + "DokterController/simpanSoap",
      method: "POST",
      dataType: "JSON",
      data: {
        lokasiid: $(".idlokasi").val(),
        episodeid: $(".idepisode").val(),
        transid: $(".idtrans").val(),
        pasienid: $(".idpasien").val(),
        transco: $(".idtransco").val(),
        poliid: $(".idpoli").val(),
        dokterid: $(".iddokter").val(),
        tanggal: $(".tanggalpoli").val(),
        soap_s: $("#subject").val(),
        soap_o: $("#objective").val(),
        soap_a: $("#assesment").val(),
        soap_p: $("#planning").val(),
        createdby: $("#dokterId").text(),
      },
      success: function (response) {
        if (response.Responcode == "00") {
          Swal.fire({
            title: "SOAP Berhasil Tersimpan",
            text: response.message,
            icon: "success",
            confirmButtonText: "OK",
          }).then((result) => {
            if (result.isConfirmed) {
              loadDataPasien($(".idepisode").val(), $(".idpasien").val());
            }
          });
        }
      },
    });
  } else {
    Swal.fire({
      title: "SOAP tidak boleh kosong",
      icon: "error",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        //
      }
    });
  }

  return false;
}

function simpansoap() {
  if (
    $.trim($("#subject").val()) != "" &&
    $.trim($("#objective").val()) != "" &&
    $.trim($("#assesment").val()) != "" &&
    $.trim($("#planning").val()) != ""
  ) {
    $.ajax({
      url: BASE_URL + "DokterController/simpanSoap",
      method: "POST",
      dataType: "JSON",
      data: {
        lokasiid: $(".idlokasi").val(),
        episodeid: $(".idepisode").val(),
        transid: $(".idtrans").val(),
        pasienid: $(".idpasien").val(),
        transco: $(".idtransco").val(),
        poliid: $(".idpoli").val(),
        dokterid: $(".iddokter").val(),
        tanggal: $(".tanggalpoli").val(),
        soap_s: $("#subject").val(),
        soap_o: $("#objective").val(),
        soap_a: $("#assesment").val(),
        soap_p: $("#planning").val(),
        createdby: $("#dokterId").text(),
      },
      beforeSend: function () {
        Swal.fire({
          title: "Menyimpan SOAP...",
          text: "Mohon tunggu sebentar.",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });
      },

      success: function (response) {
        Swal.close();
        if (response.Responcode == "00") {
          // ✅ Setelah SOAP tersimpan: update Encounter ke in-progress
          const dataEncounter = {
            episode_id: $(".idepisode").val(),
            tgl_berobat: $(".tanggalpoli").val(), // atau ambil dari hidden field tgl_berobat
            waktu_mulai: moment().format(), // waktu panggil dokter / mulai pemeriksaan
          };

          Swal.fire({
            title: "SOAP Berhasil Tersimpan",
            // text: response.message,
            text: "Mengubah status Encounter menjadi IN-PROGRESS...",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            // icon: "success",
            // confirmButtonText: "OK",
            // }).then((result) => {
            //   if (result.isConfirmed) {
            //     loadDataPasien($(".idepisode").val(), $(".idpasien").val());
            //   }
          });

          updateEncounterInProgress(dataEncounter).then((hasil) => {
            Swal.close();
            if (hasil.ok) {
              Swal.fire({
                title: "SOAP Berhasil Tersimpan",
                text: "Encounter berhasil diubah ke IN-PROGRESS.",
                icon: "success",
                confirmButtonText: "OK",
              }).then((result) => {
                if (result.isConfirmed) {
                  loadDataPasien($(".idepisode").val(), $(".idpasien").val());
                }
              });
            } else {
              // ❗ Encounter gagal, tapi SOAP sudah tersimpan
              Swal.fire({
                title: "SOAP Berhasil Tersimpan",
                text:
                  "Namun update Encounter ke IN-PROGRESS gagal.\n\n" +
                  (hasil.message || ""),
                icon: "warning",
                confirmButtonText: "Tetap Lanjut",
              }).then((result) => {
                if (result.isConfirmed) {
                  loadDataPasien($(".idepisode").val(), $(".idpasien").val());
                }
              });
            }
          });
        } else {
          Swal.fire({
            title: "Gagal menyimpan SOAP",
            text: response.message || "Terjadi kesalahan.",
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      },

      error: function (xhr) {
        Swal.close();
        Swal.fire({
          title: "Error",
          text: "Gagal menghubungi server: " + (xhr.responseText || ""),
          icon: "error",
          confirmButtonText: "OK",
        });
      },
    });
  } else {
    Swal.fire({
      title: "SOAP tidak boleh kosong",
      icon: "error",
      confirmButtonText: "OK",
    });
  }

  return false;
}

function updateEncounterInProgress(dataEncounter) {
  // dataEncounter minimal: { episode_id, tgl_berobat }
  return new Promise((resolve) => {
    $.ajax({
      url: BASE_URL + "satusehat/token",
      method: "GET",
      dataType: "json",
      success: function (res) {
        if (!res || !res.token) {
          return resolve({ ok: false, message: "Token SATUSEHAT kosong." });
        }

        const payload = {
          token: res.token,
          encounter: {
            episode_id: dataEncounter.episode_id,
            tgl_berobat: dataEncounter.tgl_berobat,
            // waktu mulai pemeriksaan (panggil dokter)
            waktu_mulai: dataEncounter.waktu_mulai || moment().format(),
            status: "in-progress",
          },
        };

        $.ajax({
          // ✅ buat endpoint khusus di backend untuk update status encounter
          url: BASE_URL + "satusehat/encounterInProgress",
          method: "POST",
          contentType: "application/json",
          dataType: "json",
          data: JSON.stringify(payload),
          success: function (resp) {
            const ok =
              resp?.success === true ||
              resp?.metadata?.responCode === "00" ||
              resp?.status === "in-progress";

            if (ok) {
              resolve({ ok: true, resp });
            } else {
              resolve({
                ok: false,
                message:
                  resp?.message || "Update Encounter ke in-progress gagal.",
                resp,
              });
            }
          },
          error: function (xhr) {
            resolve({
              ok: false,
              message:
                "Gagal update Encounter (backend): " +
                (xhr.responseText || "error"),
            });
          },
        });
      },
      error: function (xhr) {
        resolve({
          ok: false,
          message:
            "Gagal ambil token SATUSEHAT: " + (xhr.responseText || "error"),
        });
      },
    });
  });
}

function bukamodalresep(e) {
  e.preventDefault();
  $("#resultresep").html("");

  if (adasoap === "Y") {
    $.ajax({
      url: BASE_URL + "DokterController/loadinputobat",
      method: "POST",
      dataType: "JSON",
      data: {
        lokasiid: $(".idlokasi").val(),
        episodeid: $(".idepisode").val(),
        pasienid: $(".idpasien").val(),
      },
      success: function (data) {
        if (data.Responcode == "01") {
          $("#transcoresep").val("");
          $("#jenissimpan").val("I");
        } else {
          // tambahin disini buat edit obat
          var hasil = data.Responresult;

          for (var i in hasil) {
            tambahobat(
              "",
              hasil[i].obat_id,
              hasil[i].nama_obat,
              hasil[i].qty,
              hasil[i].stok,
              hasil[i].satuan,
              hasil[i].free_dosis,
              hasil[i].signa_nama,
              hasil[i].signa_dokter,
              hasil[i].catatan,
              hasil[i].urut,
              hasil[i].type,
              hasil[i].header,
              hasil[i].satuan_id,
              hasil[i].signa_id,
            );
          }

          $("#transcoresep").val(hasil[i].trans_co);
          $("#jenissimpan").val("E");
        }
      },
    });

    $("#modal_resep").modal("show");
  } else {
    Swal.fire({
      title: "Belum simpan SOAP",
      icon: "error",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        //
      }
    });
  }
}

function cariobat() {
  $.ajax({
    url: BASE_URL + "DokterController/loadmasterobat",
    method: "POST",
    dataType: "JSON",
    cache: false,
    data: {
      pencarian: $("#input_cariobat").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#listmasterobat").html("");
      } else {
        $("#listmasterobat").html("");
        var loadobatms = "";
        var hasil = data.Responresult;

        console.log(hasil);

        for (var i in hasil) {
          loadobatms += "<tr>";
          loadobatms += `<td style="width: 70%"><a href="#" class="select_obat" style="color:black" ondblclick="tambahdarimasterobat(event, '', '${hasil[i].obat_id}', '${hasil[i].nama_obat}', 1, ${hasil[i].stok}, '${hasil[i].satuan}', '', '', '', '', 0, '00', '0', '${hasil[i].satuan_id}', '')">${hasil[i].nama_obat}</a></td>`;

          loadobatms +=
            `<td style="width: 20%" align='right'>` +
            hasil[i].harga_jual +
            `</td>`;
          loadobatms +=
            `<td style="width: 10%" align='right'>` + hasil[i].stok + `</td>`;
          loadobatms += "</tr>";
        }

        $("#listmasterobat").html(loadobatms);
      }
    },
  });
  return false;
}

function tambahdarimasterobat(
  event,
  vkettipe,
  vobatid,
  vnamaobat,
  vqty,
  vstok,
  vsatuan,
  vdosis,
  vsignanama,
  vsignadokter,
  vcatatan,
  vurut,
  vtipeobat,
  vheader,
  vsatuanid,
  vsignaid,
) {
  event.preventDefault();

  // console.log(vkettipe);

  let table = document.getElementById("resultresep");
  let bolehTambah = true;

  for (let i = 0, row; (row = table.rows[i]); i++) {
    let obatid = row.cells[3].innerText;
    let tipe = row.cells[13].innerText;
    let header = row.cells[15].innerText;

    if (obatid == vobatid && tipe == vtipeobat && header == vheader) {
      bolehTambah = false;
    }
  }

  if (bolehTambah) {
    tambahobat(
      vkettipe,
      vobatid,
      vnamaobat,
      vqty,
      vstok,
      vsatuan,
      vdosis,
      vsignanama,
      vsignadokter,
      vcatatan,
      vurut,
      vtipeobat,
      vheader,
      vsatuanid,
      vsignaid,
    );
  } else {
    $("#alertModalObat").modal("show");
  }
}

function loadsigna() {
  $.ajax({
    url: BASE_URL + "DokterController/loadsigna",
    method: "POST",
    dataType: "JSON",
    success: function (data) {
      if (data.Responcode == "00") {
        var signa = "";
        data.Responresult.map(function (d) {
          signa +=
            '<option value="' +
            d.signa_id +
            '" data-signa="' +
            d.signa +
            '">' +
            d.signa +
            "</option>";
        });
        $("#master-signa").html(signa);
      }
    },
  });
  return false;
}

function buatracik(e) {
  // Mendapatkan tanggal dan waktu saat ini dalam format YYYYMMDD HH24MI
  let currentDate = new Date();
  let year = currentDate.getFullYear().toString().slice(-2); // 2 digit terakhir dari tahun
  let month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Menambahkan 0 di depan jika bulan kurang dari 10
  let day = ("0" + currentDate.getDate()).slice(-2); // Menambahkan 0 di depan jika hari kurang dari 10
  let hours = ("0" + currentDate.getHours()).slice(-2); // Menambahkan 0 di depan jika jam kurang dari 10
  let minutes = ("0" + currentDate.getMinutes()).slice(-2); // Menambahkan 0 di depan jika menit kurang dari 10
  let seconds = ("0" + currentDate.getSeconds()).slice(-2); // 2 digit detik

  // buat header racikan
  let obatid = "RA" + year + month + day + hours + minutes + seconds;
  let namaRacik = document.getElementById("namaracik").value;
  let jmlRacik = document.getElementById("jmlracik").value;
  let selectedSigna = $("#master-signa option:selected").data("signa");
  let selectedSignaid = $("#master-signa").val();
  let arrayracik = [];
  let racik = {
    vkettipe: "R",
    vobatid: obatid,
    vnamaobat: namaRacik,
    vqty: 0,
    vstok: "",
    vsatuan: "",
    vfreedosis: "",
    vsigna: selectedSigna,
    vsignateks: "",
    vcatatan: "",
    vurut: 0,
    vtipeobat: "01",
    vheader: "0",
    vsatuanid: "",
    vsignaid: selectedSignaid,
  };

  arrayracik.push(racik);

  // buat pembungkus
  let selectedBungkusId = $("#master-bungkus").val(); // Ambil ID obat yang dipilih dari dropdown
  let selectedBungkusNama = $("#master-bungkus option:selected").text(); // Ambil nama obat yang dipilih dari dropdown
  let selectedKeterangan = $("#master-bungkus option:selected").data("satuan");
  let selectedSatuanid = $("#master-bungkus option:selected").data("satuanid");
  let selectedStok = $("#master-bungkus option:selected").data("stok");

  let bungkus = {
    vkettipe: "",
    vobatid: selectedBungkusId,
    vnamaobat: selectedBungkusNama,
    vqty: jmlRacik,
    vstok: selectedStok,
    vsatuan: selectedKeterangan,
    vfreedosis: "",
    vsigna: "",
    vsignateks: "",
    vcatatan: "",
    vurut: 0,
    vtipeobat: "02",
    vheader: obatid,
    vsatuanid: selectedSatuanid,
    vsignaid: "",
  };

  arrayracik.push(bungkus);

  // buat isi racikan
  selectedObatArray.map(function (e) {
    let dataobat = {
      vkettipe: e.vkettipe,
      vobatid: e.vobatid,
      vnamaobat: e.vnamaobat,
      vqty: e.vqty,
      vstok: e.vstok,
      vsatuan: e.vsatuan,
      vfreedosis: e.vfreedosis,
      vsigna: "",
      vsignateks: "",
      vcatatan: "",
      vurut: e.vurut,
      vtipeobat: "03",
      vheader: obatid,
      vsatuanid: e.vsatuanid,
      vsignaid: "",
    };
    arrayracik.push(dataobat);
  });

  arrayracik.map(function (e) {
    tambahobat(
      e.vkettipe,
      e.vobatid,
      e.vnamaobat,
      e.vqty,
      e.vstok,
      e.vsatuan,
      e.vfreedosis,
      e.vsigna,
      e.vsignateks,
      e.vcatatan,
      e.vurut,
      e.vtipeobat,
      e.vheader,
      e.vsatuanid,
      e.vsignaid,
    );
  });

  selectedObatArray2.concat(arrayracik);

  hapusobatlama();
}

function tambahobat(
  vkettipe,
  vobatid,
  vnamaobat,
  vqty,
  vstok,
  vsatuan,
  vdosis,
  vsignanama,
  vsignadokter,
  vcatatan,
  vurut,
  vtipeobat,
  vheader,
  vsatuanid,
  vsignaid,
) {
  // Buat masing-masing row
  let table = document.getElementById("resultresep");
  let newRow = table.insertRow(table.rows.length);

  // Tentukan warna berdasarkan nilai vtipeobat
  // if (vtipeobat === "01") {
  //   newRow.style.backgroundColor = "#94b8b8";
  // } else if (vtipeobat === "02" || vtipeobat === "03") {
  //   newRow.style.backgroundColor = "#c6ecd9";
  // }

  // Tentukan kelas berdasarkan nilai vtipeobat
  if (vtipeobat === "01") {
    newRow.classList.add("table-success"); // Kelas Bootstrap untuk warna hijau
  } else if (vtipeobat === "02" || vtipeobat === "03") {
    newRow.classList.add("table-info"); // Kelas Bootstrap untuk warna biru muda
  } else {
    newRow.classList.add("table-light"); // Kelas Bootstrap untuk warna abu-abu terang
  }

  // Insert data row
  if (vtipeobat === "00") {
    newRow.insertCell(0).innerHTML = '<input type="checkbox">';
  } else {
    newRow.insertCell(0).innerHTML = "";
  }
  newRow.cells[0].style.verticalAlign = "middle";

  if (vtipeobat === "00" || vtipeobat === "01") {
    newRow.insertCell(1).innerHTML =
      "<a class='m-1 btn-hapusobat' role='button' onclick='hapusobat(this)'><i class='fas fa-times'></i></a>";
  } else {
    newRow.insertCell(1).innerHTML = "";
  }

  newRow.insertCell(2).innerHTML = vkettipe; // Tipe teks
  newRow.cells[2].style.verticalAlign = "middle";

  let td3 = newRow.insertCell(3);
  td3.style.display = "none";
  td3.innerHTML = vobatid; // Obat id
  td3.style.verticalAlign = "middle";

  newRow.insertCell(4).innerHTML = vnamaobat; // Nama obat
  newRow.cells[4].style.verticalAlign = "middle";

  let td5 = newRow.insertCell(5); // Jumlah
  if (vtipeobat === "01") {
    td5.innerHTML =
      '<input type="hidden" min="0" style="width: 80px; height: 20px" class="text-right" onkeypress="return onlyNumberKey(event)" value=' +
      vqty +
      "></input>";
  } else {
    td5.align = "center";
    td5.style.padding = 0;
    td5.innerHTML =
      '<input type="number" min="0" style="width: 80px; height: 20px" class="text-right" onkeypress="return onlyNumberKey(event)" value=' +
      vqty +
      "></input>";
    td5.style.verticalAlign = "middle";
  }

  if (vtipeobat === "01") {
    // Stok
    let td6 = newRow.insertCell(6);
    td6.innerHTML = "";
  } else {
    let td6 = newRow.insertCell(6);
    td6.align = "center";
    td6.innerHTML = vstok;
    td6.style.verticalAlign = "middle";
  }

  let td7 = newRow.insertCell(7); // Satuan
  if (vtipeobat === "01") {
    td7.innerHTML = "";
  } else {
    td7.align = "center";
    td7.innerHTML = vsatuan;
    td7.style.verticalAlign = "middle";
  }

  let td8 = newRow.insertCell(8); // Dosis
  td8.align = "center";
  td8.style.padding = 0;
  td8.innerHTML =
    '<input type="text" value="' +
    vdosis +
    '" style="margin-left: 10px;"></input>';
  td8.style.verticalAlign = "middle";

  let signa = "";
  $.ajax({
    url: BASE_URL + "ValResepController/loadsigna",
    method: "POST",
    dataType: "JSON",
    cache: false,
    async: false,
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
      } else {
        signa = data.Responresult;
      }
    },
  });
  var itemsigna = "";
  signa.map(function (d) {
    let selected = d.signa_id === vsignaid ? "selected" : "";
    itemsigna +=
      '<option value="' +
      d.signa_id +
      '" ' +
      selected +
      ">" +
      d.signa +
      "</option>";
  });

  if (vtipeobat === "00" || vtipeobat === "01") {
    let td9 = newRow.insertCell(9); // Signa Nama
    td9.align = "center";
    td9.style.padding = 0;
    td9.innerHTML =
      '<select id="signa" style="margin-left: 10px;">' +
      itemsigna +
      "</select>";
    td9.style.verticalAlign = "middle";

    let selectSigna = td9.querySelector("#signa");
    selectSigna.addEventListener("change", function () {
      td17.innerHTML = selectSigna.value;
    });
  } else {
    let td9 = newRow.insertCell(9); // Signa Nama
    td9.innerHTML = "";
  }

  let td10 = newRow.insertCell(10); // Signa Dokter
  td10.align = "center";
  td10.style.padding = 0;
  td10.innerHTML =
    '<input type="text" value="' +
    vsignadokter +
    '" style="margin-left: 10px;"></input>';
  td10.style.verticalAlign = "middle";

  let td11 = newRow.insertCell(11); // Catatan
  td11.align = "center";
  td11.style.padding = 0;
  td11.innerHTML =
    '<input type="text" value="' +
    vcatatan +
    '" style="margin-left: 10px;"></input>';
  td11.style.verticalAlign = "middle";

  let td12 = newRow.insertCell(12); // urut
  td12.align = "right";
  td12.innerHTML = vurut;
  td12.style.verticalAlign = "middle";
  td12.style.display = "none";

  let td13 = newRow.insertCell(13); // tipe
  td13.align = "right";
  td13.innerHTML = vtipeobat;
  td13.style.verticalAlign = "middle";
  td13.style.display = "none";

  let td14 = newRow.insertCell(14);
  td14.innerHTML = vobatid; // Obat id
  td14.style.verticalAlign = "middle";
  td14.style.display = "none";

  let td15 = newRow.insertCell(15);
  td15.innerHTML = vheader; // header
  td15.style.verticalAlign = "middle";
  td15.style.display = "none";

  let td16 = newRow.insertCell(16);
  td16.innerHTML = vsatuanid; // satuan id
  td16.style.verticalAlign = "middle";
  td16.style.display = "none";

  let td17 = newRow.insertCell(17);
  td17.innerHTML = vsignaid; // signa id
  td17.style.verticalAlign = "middle";
  td17.style.display = "none";

  $("#pencarian_obat").val("");
  $("#modal_cariobat").modal("hide");
}

function hapusobatlama() {
  let table = document.getElementById("resultresep");
  for (let i = table.rows.length - 1; i >= 0; i--) {
    let row = table.rows[i];
    let checkboxCell = row.cells[0];
    let checkbox = checkboxCell.querySelector("input[type='checkbox']");

    if (checkbox && checkbox.checked) {
      table.deleteRow(i);
    }
  }
}

function hapusobat(button) {
  // dapetin parent dari row
  let row = button.parentNode.parentNode;

  let vobatid = row.cells[3].innerHTML;
  let vnamaobat = row.cells[4].innerHTML;
  let vtipeobat = row.cells[13].innerHTML;
  let vheader = row.cells[15].innerHTML;

  // Menghapus baris dari tabel
  if (vtipeobat === "00") {
    row.parentNode.removeChild(row);
  } else {
    row.parentNode.removeChild(row);

    let table = document.getElementById("resultresep");
    for (let i = table.rows.length - 1; i >= 0; i--) {
      let currentRow = table.rows[i];
      let currentObatId = currentRow.cells[15].innerHTML; //header
      let currentObatIdAsli = currentRow.cells[14].innerHTML; //obatid
      let currentTipeObat = currentRow.cells[13].innerHTML; //tipe

      if (currentObatId === vobatid) {
        if (currentTipeObat === "02") {
          table.deleteRow(i);
        } else {
          let cektable = document.getElementById("resultresep");
          let belumAda = true;

          for (let i = 0, row; (row = cektable.rows[i]); i++) {
            let obatid = row.cells[3].innerText;
            let tipe = row.cells[13].innerText;

            if (obatid == currentObatIdAsli && tipe == "00") {
              belumAda = false;
            }
          }

          if (belumAda) {
            let signa = "";
            $.ajax({
              url: BASE_URL + "DokterController/loadsigna",
              method: "POST",
              dataType: "JSON",
              cache: false,
              async: false,
              success: function (data) {
                if (data.Responcode == "01") {
                  toastr["info"](data.Respondesc, "INFORMATION");
                } else {
                  signa = data.Responresult;
                }
              },
            });

            let itemsignanya = "";
            signa.map(function (d) {
              itemsignanya +=
                '<option value="' + d.signa_id + '">' + d.signa + "</option>";
            });

            table.rows[i].cells[0].innerHTML = '<input type="checkbox">';
            table.rows[i].cells[1].innerHTML =
              "<a class='m-1 btn-hapusobat' role='button' onclick='hapusobat(this)'><i class='fas fa-times'></i></a>";
            table.rows[i].cells[9].align = "center"; //signa nama
            table.rows[i].cells[9].style.padding = 0;
            table.rows[i].cells[9].innerHTML =
              '<select id="signa" style="margin-left: 10px;" onchange="getSignaPilih(this)">' +
              itemsignanya +
              "</select>";
            table.rows[i].cells[9].style.verticalAlign = "middle";
            table.rows[i].cells[13].innerHTML = "00"; //tipe
            table.rows[i].cells[15].innerHTML = "0"; //header
            table.rows[i].style.backgroundColor = "";
          } else {
            table.deleteRow(i);
          }
        }
      }
    }
  }
}

function getSignaPilih(selectSigna) {
  var td17 = selectSigna.closest("tr").cells[17];
  td17.innerHTML = selectSigna.value;
}

function pilihpembungkus(element) {
  var selectedOption = $(element).find("option:selected");
  var stok = selectedOption.data("stok");
  $("#stokbungkus").val(stok);
}

function simpanresep(event) {
  event.preventDefault();

  let table = document.getElementById("resultresep");
  let dataObat = [];
  let bolehSimpan = true;

  for (let i = 0, row; (row = table.rows[i]); i++) {
    let obat = {
      kettipe: row.cells[2].innerText,
      obatid: row.cells[3].innerText,
      namaobat: row.cells[4].innerText,
      qty: row.cells[5].querySelector("input").value,
      stok: row.cells[6].innerText,
      satuan: row.cells[7].innerText,
      freedosis: row.cells[8].querySelector("input").value,
      signanama: row.cells[9].querySelector("select")
        ? row.cells[9].querySelector("select").selectedOptions[0].text
        : "",
      signadokter: row.cells[10].querySelector("input").value,
      catatan: row.cells[11].querySelector("input").value,
      urut: row.cells[12].innerText,
      tipeobat: row.cells[13].innerText,
      header: row.cells[15].innerText,
      satuanid: row.cells[16].innerText,
      signaid: row.cells[17].innerText,
      namaracikan:
        row.cells[13].innerText === "01" ? row.cells[4].innerText : "",
    };
    dataObat.push(obat);
    console.log(obat);
  }

  $.ajax({
    url: BASE_URL + "DokterController/simpanresep",
    method: "POST",
    dataType: "JSON",
    cache: false,
    data: {
      lokasiid: $(".idlokasi").val(),
      episodeid: $(".idepisode").val(),
      transid: $(".idtrans").val(),
      pasienid: $(".idpasien").val(),
      tanggal: $(".tanggalpoli").val(),
      rekananid: $(".idrekanan").val(),
      poliid: $(".idpoli").val(),
      dokterid: $(".iddokter").val(),
      createdby: $("#dokterId").text(),
      jenissimpan: $("#jenissimpan").text(),
      transcoresep: $("#transcoresep").val(),
      dataObat: dataObat,
    },
    success: function (response) {
      if (response.Responcode == "00") {
        $("#modal_resep").modal("hide");
        loaditemharga();

        Swal.fire({
          title: "Resep Tersimpan",
          text: response.message,
          icon: "success",
          confirmButtonText: "OK",
        }).then((result) => {
          if (result.isConfirmed) {
            cekstatusresep();
          }
        });
      }
    },
  });

  return false;
}
// function formatRupiah(angka) {
//   return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
// }

function formatRupiah(angka) {
  if (isNaN(angka)) return "0";
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(angka);
}

function fieldsToCheck(fields) {
  for (let field of fields) {
    if (!$(field.id).val()) {
      Swal.fire({
        title: `${field.name} Belum Diisi`,
        text: `Harap isi ${field.name} sebelum melanjutkan.`,
        icon: "warning",
        confirmButtonText: "OK",
      });
      return false; // Hentikan pengecekan jika ada field yang belum diisi
    }
  }
  return true; // Semua field telah diisi
}

function bukamodalselesai(e) {
  e.preventDefault();

  let fields = [
    { id: "#statPlgGetApi", name: "Status Pulang" },
    { id: "#sadarGetApi", name: "Status Kesadaran" },
    { id: "#plhPrognosaApi", name: "Prognosa" },
    { id: "#alergiMakanan", name: "Alergi Makanan" },
    { id: "#alergiUdara", name: "Alergi Udara" },
    { id: "#alergiObat", name: "Alergi Obat" },
  ];

  if (!fieldsToCheck(fields)) {
    return; // Hentikan eksekusi jika ada field yang belum diisi
  }

  if (adasoap === "Y" && $("#icd10_kode").val() !== "") {
    prosesSelesai();
  } else {
    Swal.fire({
      title: "Belum simpan SOAP atau belum isi ICD10",
      icon: "error",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        // Lakukan sesuatu jika diperlukan
      }
    });
  }
}

function prosesSelesai() {
  Swal.fire({
    title: "Ingin menyelesaikan pemeriksaan ?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
  }).then((result) => {
    if (result.isConfirmed) {
      const dataToSend = {
        lokasiid: $(".idlokasi").val(),
        episodeid: $(".idepisode").val(),
        pasienid: $(".idpasien").val(),
        transco: $(".idtransco").val(),
        transid: $(".idtrans").val(),
        poliid: $(".idpoli").val(),
        tanggal: $(".tanggalpoli").val(),
        dokterid: $(".iddokter").val(),
        rekananid: $(".idrekanan").val(),
        createdby: $("#dokterId").text(),
        icd10: $("#icd10_kode").val(),
        KdIcd10: $("#cari_kode_icd10").val(),
        diagnosa: $("#cari_nama_icd10").val(),
        diagnonspesialis: $("#diag_non_spesialis").val(),
        alergiMakanan: $("#alergiMakanan").val(),
        alergiUdara: $("#alergiUdara").val(),
        alergiObat: $("#alergiObat").val(),
        statPlg: $("#statPlgGetApi").val(),
        kdsadar: $("#sadarGetApi").val(),
        kdprognosa: $("#plhPrognosaApi").val(),
        noKartu: $("#nokartuprov").val(),
      };

      // Tampilkan data di console
      console.log("Data yang akan dikirim:", dataToSend);

      // getResepDr(dataToSend.episodeid, (terapiObatText) => {
      //   dataToSend.terapiObat = terapiObatText; // Tambahkan terapi obat ke data

      $.ajax({
        url: BASE_URL + "DokterController/prosesselesai",
        method: "POST",
        dataType: "JSON",
        data: dataToSend,
        success: function (response) {
          console.log("Response dari server:", response);
          // console.log(response);
          if (response.Responcode === "99") {
            window.dataSimpanTACC = dataToSend;

            // Tampilkan modal TACC
            $("#modalTacc").modal("show");
            return;
          } else if (response.Responcode === "01") {
            // Pemeriksaan berhasil diselesaikan
            $("#modal_selesai").modal("hide");
            cekstatusresep();

            // ✅ siapkan data condition (ICD saja + episode/tanggal)
            const dataCondition = {
              episode_id: dataToSend.episodeid,
              tgl_berobat: dataToSend.tanggal, // YYYY-MM-DD (punya Mas sudah)
              // icd10: dataToSend.icd10, // wajib KdIcd10
              icd10: dataToSend.KdIcd10, // wajib KdIcd10
              diagnosa: dataToSend.diagnosa || "", // opsional (display)
              dokterid: dataToSend.dokterid, // optional kalau mau dipakai backend
              pasienid: dataToSend.pasienid, // optional
            };

            // tampilkan loading kirim condition
            Swal.fire({
              title: "Selesai Periksa",
              text: "Mengirim Diagnosa (ICD) ke SATUSEHAT...",
              allowOutsideClick: false,
              didOpen: () => Swal.showLoading(),
            });

            kirimConditionSatuSehat(dataCondition).then((hasilCond) => {
              Swal.close();
              const lanjutPopupSelesai = () => {
                // === popup Mas yang sudah ada (BPJSResult dll) ===
                // let bpjsMessage = "";
                if (response.BPJSResult) {
                  const bpjsStatus = response.BPJSResult.status;
                  const bpjsMessage =
                    response.BPJSResult.message ||
                    "Tidak ada informasi tambahan.";
                  //  console.log("ww");
                  //  console.log(bpjsStatus);

                  if (bpjsStatus === "success") {
                    Swal.fire({
                      title: "Selesai Pemeriksaan",
                      html: `
                    Pemeriksaan berhasil diselesaikan.<br>
                    <strong>BPJS:</strong> Berhasil diproses.<br>
                    <strong>No Kunjungan:</strong> ${bpjsMessage || "-"}
                  `,
                      icon: "success",
                      confirmButtonText: "OK",
                    }).then((result) => {
                      if (result.isConfirmed) showPanelPasien();
                    });
                  } else {
                    Swal.fire({
                      title: "Pemeriksaan Selesai dengan Catatan",
                      html: `
                    Pemeriksaan selesai tetapi ada masalah pada BPJS:<br>
                    <strong>Kode:</strong> ${response.BPJSResult.code}<br>
                    <strong>Pesan:</strong> ${bpjsMessage}
                  `,
                      icon: "warning",
                      confirmButtonText: "OK",
                    });
                  }
                } else {
                  Swal.fire({
                    title: "Selesai Pemeriksaan",
                    text:
                      response.Respondesc ||
                      "Pemeriksaan berhasil diselesaikan.",
                    icon: "success",
                    confirmButtonText: "OK",
                  }).then((result) => {
                    if (result.isConfirmed) showPanelPasien();
                  });
                }
              };

              if (hasilCond.ok) {
                // ✅ Condition sukses -> lanjut
                lanjutPopupSelesai();
              } else {
                // ❗ Condition gagal -> warning tapi tetap lanjut selesai
                Swal.fire({
                  icon: "warning",
                  title: "Diagnosa SATUSEHAT gagal terkirim",
                  text:
                    hasilCond.message || "Gagal kirim Condition ke SATUSEHAT.",
                  showCancelButton: true,
                  confirmButtonText: "Tetap Lanjut",
                  cancelButtonText: "Batal",
                }).then((r) => {
                  if (r.isConfirmed) lanjutPopupSelesai();
                });
              }
            });
          } else {
            const bpjsMessage =
              response.BPJSResult && response.BPJSResult.message
                ? ` BPJS Error: ${response.BPJSResult.message}`
                : "";

            Swal.fire({
              title: "Error",
              // text: response.Respondesc || "Terjadi kesalahan.", response.BPJSResult.message
              text: `${
                response.Respondesc || "Terjadi kesalahan."
              }${bpjsMessage}`,
              icon: "error",
              confirmButtonText: "OK",
            });
          }
        },
        error: function (xhr, status, error) {
          console.log("AJAX Error:", xhr, status, error);
          Swal.fire({
            title: "Error",
            text: "Terjadi kesalahan pada server.",
            icon: "error",
            confirmButtonText: "OK",
          });
        },
      });
    }
  });
  return false;
}

function kirimConditionSatuSehat(dataCondition) {
  return new Promise((resolve) => {
    $.ajax({
      url: BASE_URL + "satusehat/token",
      method: "GET",
      dataType: "json",
      success: function (res) {
        if (!res || !res.token) {
          return resolve({ ok: false, message: "Token SATUSEHAT kosong." });
        }

        const payload = {
          token: res.token,
          condition: dataCondition,
        };

        $.ajax({
          url: BASE_URL + "satusehat/kirimCondition",
          method: "POST",
          contentType: "application/json",
          dataType: "json",
          data: JSON.stringify(payload),
          success: function (resp) {
            // anggap sukses jika dapat id/condition_uuid atau status success
            const ok =
              resp?.status === "success" ||
              resp?.success === true ||
              !!resp?.condition_uuid ||
              !!resp?.id;

            if (ok) resolve({ ok: true, resp });
            else
              resolve({
                ok: false,
                message: resp?.message || "Gagal kirim Condition.",
                resp,
              });
          },
          error: function (xhr) {
            resolve({
              ok: false,
              message:
                "Gagal kirim Condition: " + (xhr.responseText || "error"),
            });
          },
        });
      },

      error: function (xhr) {
        resolve({
          ok: false,
          message: "Gagal ambil token: " + (xhr.responseText || "error"),
        });
      },
    });
  });
}

function showPanelPasien() {
  var listPasien = document.getElementById("listpasien");
  var pemeriksaan = document.getElementById("pemeriksaan");

  if (listPasien.style.display === "none") {
    listPasien.style.display = "block";
    pemeriksaan.className = "col-sm-9";
  }
}

function simpanrujukan(event) {
  event.preventDefault();

  const tipeRjkn = $("input[name='tipeRjkn']:checked").val();

  const episodeId = $("#idepisode").val();

  // Tentukan tanggal estimasi rujukan berdasarkan tipe rujukan
  const tglEstRujuk =
    tipeRjkn === "ya"
      ? convertDateToDDMMYYYY($("#tglEstRujukKhss").val())
      : convertDateToDDMMYYYY($("#tglEstRujuk").val());

  // Tentukan data tambahan untuk rujukan khusus
  let spsRjknKhssGetApi = "";
  let subSpRjknKhssGetApi = "";

  if (
    $("#plhRjknKhssApi").val() === "THA" ||
    $("#plhRjknKhssApi").val() === "HEM"
  ) {
    spsRjknKhssGetApi = $("#spsRjknKhssGetApi").val() || "";
    subSpRjknKhssGetApi = $("#subSpRjknKhssGetApi").val() || "";
  }

  const dataToSend = {
    noKartu: $("#nokartuprov").val(),
    idepisode: episodeId,
    tipeRjkn: tipeRjkn,
    tglEstRujuk: tglEstRujuk,
    plhRjknKhssApi: $("#plhRjknKhssApi").val(),
    plhFasRjknKhssApi: $("#plhFasRjknKhssApi").val(),
    catRjkn: $("#catRjkn").val(),
    spsRjknGetApi: $("#spsRjknGetApi").val(),
    subSpRjknGetApi: $("#subSpRjknGetApi").val(),
    saranaGetApi: $("#saranaGetApi").val(),
    faskesRjknGetApi: $("#faskesRjknGetApi").val(),
    spsRjknKhssGetApi: spsRjknKhssGetApi,
    subSpRjknKhssGetApi: subSpRjknKhssGetApi,
  };

  console.log(dataToSend);
  $.ajax({
    url: BASE_URL + "DokterController/simpanRujukan",
    method: "POST",
    dataType: "JSON",
    data: dataToSend,
    success: function (response) {
      console.log(response);
      if (response.status === "success") {
        Swal.fire({
          title: "Berhasil",
          text: "Rujukan berhasil disimpan.",
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => {
          $("#modal_rujuk").modal("hide");
        });
      } else {
        Swal.fire({
          title: "Gagal",
          text: response.message || "Terjadi kesalahan saat menyimpan rujukan.",
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error saat menyimpan rujukan:", error);
      Swal.fire({
        title: "Error",
        text: "Terjadi kesalahan saat menyimpan rujukan.",
        icon: "error",
        confirmButtonText: "OK",
      });
    },
  });
}

function hideAllSubs() {
  $("#sub_time, #sub_age, #sub_komplikasi, #sub_comorbid").hide();
}

// function hideAllSubs() {
//   $("#sub_alasan").hide();
// }

function tampildeviceid() {
  alert(device_id);
  // $("#modalip").modal("show");
}

function resetFormPaliatif() {
  const $form = $("#formPaliatif");
  if (!$form.length) return;

  // reset form standar
  $form[0].reset();

  // matikan & kosongkan semua keterangan symptom
  $form.find(".symptom-check").each(function () {
    const targetId = $(this).data("target");
    const $target = $("#" + targetId);
    $(this).prop("checked", false);
    if ($target.length) {
      $target.prop("disabled", true).val("");
    }
  });

  // uncheck mental_state
  $form.find("input[name='mental_state[]']").prop("checked", false);

  // kalau mau, canvas bisa dibersihkan juga
  ["lung_left", "lung_right", "abdomen_canvas"].forEach((id) => {
    const canvas = document.getElementById(id);
    if (canvas) {
      const ctx = canvas.getContext("2d");
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
  });
}

function fillFormPaliatif(data) {
  if (!data) return;

  // field text / textarea sederhana
  const textFields = [
    "petugas_hhpc",
    "tgl_kunjungan",
    "no_hhpc",
    "nama_pasien",
    "umur",
    "agama",
    "suku_bangsa",
    "bahasa",
    "alamat",
    "telp",
    "fax",
    "hp",
    "masalah_1",
    "masalah_2",
    "masalah_3",
    "care_giver",
    "keluarga_sakit_sama",
    "dukungan",
    "staff_perawat",
    "nyeri",
    "mental_note",
    "diagnosis",
    "prognosis",
    "keadaan_umum",
    "bicara",
    "pucat",
    "jaundice",
    "oedema",
    "respiratory_system",
    "abdomen",
    "pr",
    "pv",
    "kesimpulan",
    "rencana",
  ];

  textFields.forEach((name) => {
    if (typeof data[name] !== "undefined" && data[name] !== null) {
      $("#formPaliatif [name='" + name + "']").val(data[name]);
    }
  });

  // radio button
  ["jenis_kelamin", "pasien_tahu", "cyanosis", "marital_status"].forEach(
    (name) => {
      if (data[name]) {
        $(
          "#formPaliatif input[name='" +
            name +
            "'][value='" +
            data[name] +
            "']",
        ).prop("checked", true);
      }
    },
  );

  // checkbox mental_state (disimpan sebagai array di JSON)
  if (Array.isArray(data.mental_state)) {
    data.mental_state.forEach((val) => {
      $("#formPaliatif input[name='mental_state[]'][value='" + val + "']").prop(
        "checked",
        true,
      );
    });
  }

  // symptom (map nama -> 1) + symptom_desc (map nama -> teks)
  if (data.symptom && typeof data.symptom === "object") {
    Object.keys(data.symptom).forEach((key) => {
      const cb = $("#formPaliatif input[name='symptom[" + key + "]']");
      if (!cb.length) return;

      cb.prop("checked", true);

      const targetId = cb.data("target");
      const $txt = $("#" + targetId);
      if ($txt.length) {
        $txt.prop("disabled", false);
        if (
          data.symptom_desc &&
          typeof data.symptom_desc === "object" &&
          data.symptom_desc[key]
        ) {
          $txt.val(data.symptom_desc[key]);
        }
      }
    });
  }

  // gambar dari DB (base64)
  if (data.lung_left_img) {
    drawBase64ToCanvas("lung_left", data.lung_left_img);
  }
  if (data.lung_right_img) {
    drawBase64ToCanvas("lung_right", data.lung_right_img);
  }
  if (data.abdomen_img) {
    drawBase64ToCanvas("abdomen_canvas", data.abdomen_img);
  }
  if (data.genogram_img) {
    drawBase64ToCanvas("genogram_canvas", data.genogram_img);
  }
}

// helper opsional utk gambar
function drawBase64ToCanvas(canvasId, base64) {
  if (!base64) return;
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const ctx = canvas.getContext("2d");
  const img = new Image();
  img.onload = function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
  };
  img.src = "data:image/png;base64," + base64;
}
