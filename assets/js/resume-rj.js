$(document).ready(function () {
  console.log("Resume RJ JS loaded");

  $(".select2").select2();

  const jenisSelector = $("#jenisLayanan");
  const filterPoli = $("#filterPoliklinik");

  // fungsi reload

  function reloadData(jenisLayanan, poliId = "", dokterId = "") {
    const tanggal_mulai = $("#tgl_Mulai").val() || "";
    const tanggal_selesai = $("#tgl_selesai").val() || "";

    console.log(
      `Reload data → 
    Tanggal Mulai:${tanggal_mulai}, 
    Tanggal Selesai:${tanggal_selesai}, 
    Layanan:${jenisLayanan}, 
    Poli:${poliId}, 
    Dokter:${dokterId}`,
    );

    $.ajax({
      url: BASE_URL + "ResumeController/cariData",
      type: "POST",
      data: {
        jenisLayanan: jenisLayanan,
        poliId: poliId,
        dokterId: dokterId,
        tanggal_mulai: tanggal_mulai,
        tanggal_selesai: tanggal_selesai,
      },
      dataType: "json",

      success: function (res) {
        let html = "";

        if (!res || res.length === 0) {
          html = `
        <tr>
          <td colspan="7" class="text-center">Tidak ada data</td>
        </tr>`;
        } else {
          res.forEach((r) => {
            // ===============================
            // buat list detil pemeriksaan
            // ===============================

            let detil = "";

            if (r.detil_pemeriksaan && r.detil_pemeriksaan.length > 0) {
              detil += `<ul style="margin:3px 0 0 15px;padding:0;font-size:13px;">`;

              r.detil_pemeriksaan.forEach((d) => {
                detil += `<li>${d.nama_layan1}</li>`;
              });

              detil += `</ul>`;
            }

            html += `
          <tr>
            <td>${r.tgl_masuk ?? ""}</td>

            <td>${r.nama_pas ?? ""}</td>

            <td>
              <b>${r.nama_poli ?? ""}</b>
              ${detil}
            </td>

            <td>${r.nama_dr ?? "-"}</td>

            <td>${r.status_bayar ?? "-"}</td>

            <td>${r.kondisi_pulang ?? "-"}</td>

            <td>
              <button class="btn btn-sm btn-primary">
                <i class="feather-eye"></i> Detail
              </button>
            </td>

          </tr>
          `;
          });
        }

        $("#tableResumeRJ").html(html);
      },

      error: function () {
        $("#tableResumeRJ").html(`
        <tr>
          <td colspan="7" class="text-center text-danger">
            Gagal memuat data
          </td>
        </tr>
      `);
      },
    });
  }

  function reloadDatax(jenisLayanan, poliId = "", dokterId = "") {
    const tanggal_mulai = $("#tgl_Mulai").val() || "";
    const tanggal_selesai = $("#tgl_selesai").val() || "";

    console.log(
      `Reload data → 
      Tanggal Mulai:${tanggal_mulai}, 
      Tanggal Selesai:${tanggal_selesai}, 
      Layanan:${jenisLayanan}, 
      Poli:${poliId}, 
      Dokter:${dokterId}`,
    );

    $.ajax({
      url: BASE_URL + "ResumeController/cariData",
      type: "POST",
      data: {
        jenisLayanan: jenisLayanan,
        poliId: poliId,
        dokterId: dokterId,
        tanggal_mulai: tanggal_mulai,
        tanggal_selesai: tanggal_selesai,
      },
      dataType: "json",
      success: function (res) {
        let html = "";

        if (!res || res.length === 0) {
          html = `<tr>
              <td colspan="7" class="text-center">Tidak ada data</td>
            </tr>`;
        } else {
          res.forEach((r) => {
            html += `
      <tr>
        <td>${r.tgl_masuk ?? ""}</td>
        <td>${r.nama_pas ?? ""}</td>
        <td>${r.nama_poli ?? ""}</td>
        <td>${r.nama_dr ?? "-"}</td>
        <td>${r.status_bayar ?? "-"}</td>
        <td>${r.kondisi_pulang ?? "-"}</td>
        <td>
          <button class="btn btn-sm btn-primary">
            <i class="feather-eye"></i> Detail
          </button>
        </td>
      </tr>
      `;
          });
        }

        $("#tableResumeRJ").html(html);
      },
    });
  }

  // fungsi handle filter
  function handleFilter() {
    const jenis = jenisSelector.val();

    let poliId = "";
    let dokterId = "";

    if (jenis === "POLIKLINIK") {
      filterPoli.show();

      poliId = $("#Poliklinik").val() || "";
      dokterId = $("#Dokter").val() || "";
    } else {
      filterPoli.hide();
    }

    reloadData(jenis, poliId, dokterId);
  }

  // event change jenis layanan
  jenisSelector.on("change", function () {
    handleFilter();
  });

  // event change poli
  $("#Poliklinik").on("change", function () {
    const jenis = jenisSelector.val();
    const poliId = $(this).val();
    const dokterId = $("#Dokter").val() || "";

    // Kosongkan dulu daftar dokter
    Dokter.innerHTML = '<option value="">-- Pilih Dokter --</option>';

    // if (!poliId) {
    //   reloadData("POLIKLINIK", selectId);
    //   return;
    // }

    $.ajax({
      url: BASE_URL + "Resume-getDokter",
      method: "GET",
      data: { poli_id: poliId },
      dataType: "json",
      beforeSend: function () {
        Dokter.innerHTML = "<option>Memuat...</option>";
      },
      success: function (res) {
        let options = '<option value="">-- Pilih Dokter --</option>';
        res.forEach((r) => {
          options += `<option value="${r.dokter_id}">${r.nama}</option>`;
        });
        Dokter.innerHTML = options;

        reloadData(jenis, poliId, dokterId);
      },
      error: function () {
        Dokter.innerHTML = '<option value="">Gagal memuat dokter</option>';
      },
    });
  });

  $(".nav-link").on("click", function (e) {
    alert("Klik Pasien History");
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
                      tindakanResponse.Responresult,
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
                    '<p class="text-center text-danger">Failed to load Tindakan data.</p>',
                  );
                },
              });
            },
            error: function () {
              $("#detilHistory").append(
                '<p class="text-center text-danger">Failed to load Farmasi data.</p>',
              );
            },
          });
        } else {
          $("#detilHistory").html(
            `<p class="text-center">${response.message}</p>`,
          );
        }
      },
      error: function () {
        $("#detilHistory").html(
          '<p class="text-center text-danger">Failed to load data.</p>',
        );
      },
    });
    // alert(pasien_id);
  });

  // change jenis layanan
  $("#jenisLayanan").on("change", function () {
    const jenis = $(this).val();
    let poliId = $("#Poliklinik").val() || "";
    let dokterId = $("#Dokter").val() || "";

    reloadData(jenis, poliId, dokterId);
  });

  // event change dokter
  $("#Dokter").on("change", function () {
    const jenis = jenisSelector.val();
    const poliId = $("#Poliklinik").val() || "";
    const dokterId = $(this).val();

    reloadData(jenis, poliId, dokterId);
  });

  // change tanggal
  $("#tgl_Mulai, #tgl_selesai").on("change blur keyup", function () {
    const jenis = jenisSelector.val();
    const poliId = $("#Poliklinik").val() || "";
    const dokterId = $("#Dokter").val() || "";

    reloadData(jenis, poliId, dokterId);
  });

  // tombol cari
  $("#btnCari").on("click", function () {
    const jenis = jenisSelector.val();
    const poliId = $("#Poliklinik").val() || "";
    const dokterId = $("#Dokter").val() || "";

    reloadData(jenis, poliId, dokterId);
  });

  // load pertama kali saat page dibuka
  handleFilter();
});
