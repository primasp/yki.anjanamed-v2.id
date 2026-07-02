/* =========================================================
   Registrasi Layanan YKI Tahap 2
   Alur baru:
   1) Pemeriksaan Penunjang = khusus rujukan luar, hanya UMUM, layanan Papsmear.
   2) Kunjungan Poliklinik = alur lama + panel history layanan pasien.
   ========================================================= */

$(document).ready(function () {
  const state = {
    jenisKunjungan: $('input[name="jenis_kunjungan"]:checked').val() || "2",
  };

  console.log("Regist Poli YKI Tahap 2 loaded");

  initSelect2();
  initAutocomplete();
  bindEvents();
  applyJenisKunjunganState();
  updatePenunjangByProvider();
  updateStatusIsian();

  if ($("#pasien_id").val()) {
    loadHistoryLayanan();
  }

  function initSelect2() {
    if ($.fn.select2) {
      $("#pembiayaan, #poli, #dokter, #jam_slot, .lab-item").select2({
        width: "100%",
      });
    }
  }

  function initAutocomplete() {
    autocompleteInput("#namaPas", "nama");
    autocompleteInput("#noRm", "no_rm");
  }

  function autocompleteInput(selector, type) {
    $(selector).autocomplete({
      minLength: 2,
      appendTo: "body",
      position: { my: "left top", at: "left bottom", collision: "none" },
      source: function (request, response) {
        $.ajax({
          url: BASE_URL + "Daftar-Layanan/Cari-Pasien",
          method: "POST",
          dataType: "json",
          data: {
            term: request.term.toLowerCase(),
            type: type,
          },
          success: function (data) {
            if (!Array.isArray(data)) {
              response([]);
              return;
            }

            response(
              $.map(data, function (item) {
                return {
                  label:
                    type === "nama"
                      ? `${item.nama} - ${item.int_pasien_id} - ${formatTanggal(item.tgl_lahir)}`
                      : `${item.int_pasien_id} - ${item.nama} - ${formatTanggal(item.tgl_lahir)}`,
                  value: type === "nama" ? item.nama : item.int_pasien_id,
                  nama: item.nama,
                  int_pasien_id: item.int_pasien_id,
                  tgl_lahir: item.tgl_lahir,
                  sex_id: item.sex_id,
                  alamat1: item.alamat1,
                  pasien_id: item.pasien_id,
                  no_kartuprov: item.no_kartuprov,
                };
              }),
            );
          },
          error: function () {
            response([]);
          },
        });
      },
      select: function (event, ui) {
        if (type === "nama") {
          $("#noRm").val(ui.item.int_pasien_id);
        } else {
          $("#namaPas").val(ui.item.nama);
        }

        $("#tgl_lahir").val(formatTanggal(ui.item.tgl_lahir));
        $("#sex_id").val(ui.item.sex_id);
        $("#alamat").val(ui.item.alamat1);
        $("#pasien_id").val(ui.item.pasien_id);
        $("#no_bpjs").val(ui.item.no_kartuprov || "");

        resetPoliSelection();
        loadHistoryLayanan();
        updateStatusIsian();
      },
    });
  }

  function bindEvents() {
    $("#pembiayaan").on("change", function () {
      resetPoliSelection();
      updatePenunjangByProvider();

      if (state.jenisKunjungan === "1") {
        checkPoli();
        loadHistoryLayanan();
      }

      updateStatusIsian();
    });

    $('input[name="jenis_kunjungan"]').on("change", function () {
      state.jenisKunjungan = $(this).val();
      applyJenisKunjunganState();
      updateStatusIsian();
    });

    $(document).on("change", ".lab-item", function () {
      const harga = $(this).find(":selected").data("hargalab") || 0;
      $(this)
        .closest(".yki-item-row")
        .find(".harga-labelLab")
        .text("Rp " + harga);
      updateStatusIsian();
    });

    $(document).on(
      "input change",
      "input[name='lab_qty[]']",
      updateStatusIsian,
    );

    $(document).on("click", ".addLabItem", function () {
      addLabRow();
    });

    $(document).on("click", ".removeItem", function () {
      $(this).closest(".yki-item-row").remove();
      updateStatusIsian();
    });

    $("#btnRefreshHistory").on("click", loadHistoryLayanan);

    $("#tgl_berobat, #poli").on("change", function () {
      const tanggal = $("#tgl_berobat").val();
      const poli = $("#poli").val();

      resetDokterDanSlot();

      if (tanggal && poli) {
        cariDokter(tanggal, poli);
      }

      updateStatusIsian();
    });

    $("#dokter").on("change", function () {
      const tanggal = $("#tgl_berobat").val();
      const poli = $("#poli").val();
      const dokter = $(this).val();

      resetJamSlot();

      if (tanggal && poli && dokter) {
        cariJamSlot(tanggal, poli, dokter);
      }

      updateStatusIsian();
    });

    $("#jam_slot, #namaPas, #noRm").on("change input", updateStatusIsian);

    $(".forwrd-btn-data-pas").on("click", resetDataPasien);
    $(".forwrd-btn-biaya").on("click", resetPembiayaan);
    $(".forwrd-btn-regist").on("click", resetRegistrasi);

    $("#btnCancelPasien").on("click", function () {
      window.location.href = BASE_URL + "Daftar-Layanan";
    });

    $("#formRegistLayan").on("submit", handleSubmit);

    document
      .getElementById("bookingModal")
      ?.addEventListener("shown.bs.modal", () => {
        const today = moment().format("YYYY-MM-DD");
        const tglInput = document.getElementById("filterTanggalBook");

        if (tglInput && !tglInput.value) {
          tglInput.value = today;
        }

        if (typeof fetchBookingList === "function") {
          fetchBookingList();
        }
      });

    ["filterTanggalBook", "filterPoliBook", "filterDokterBook"].forEach(
      function (id) {
        $("#" + id).on("change", function () {
          if (typeof fetchBookingList === "function") {
            fetchBookingList();
          }
        });
      },
    );
  }

  function applyJenisKunjunganState() {
    state.jenisKunjungan =
      $('input[name="jenis_kunjungan"]:checked').val() || "2";

    $(".yki-radio-card").removeClass("active");

    if (state.jenisKunjungan === "1") {
      $("#cardPoli").addClass("active");
      $("#fieldsetPoli").show();
      $("#fieldsetPenunjang").hide();
      $("#label_layan").text("Registrasi Kunjungan Poliklinik");
      $("#label_layan_desc").text(
        "Pilih poli, dokter, jam slot, lalu validasi riwayat layanan pasien di sisi kanan.",
      );

      clearPenunjangSelection();
      checkPoli();
      loadHistoryLayanan();
    } else {
      $("#cardPenunjang").addClass("active");
      $("#fieldsetPoli").hide();
      $("#fieldsetPenunjang").show();
      $("#label_layan").text("Registrasi Penunjang Rujukan");
      $("#label_layan_desc").text(
        "Khusus rujukan dari luar. Layanan yang aktif pada tahap awal adalah Papsmear.",
      );

      resetPoliSelection();
      updatePenunjangByProvider();
    }
  }

  function updatePenunjangByProvider() {
    const pembiayaan = $("#pembiayaan").val();

    if (state.jenisKunjungan !== "2") return;

    if (pembiayaan === "UMUM") {
      $("#labContainer").show();
      $("#penunjangAlert")
        .removeClass("yki-alert-warning")
        .html(
          '<i class="fas fa-check-circle"></i><span>Layanan <b>Papsmear (Rujukan)</b> aktif untuk pembiayaan UMUM.</span>',
        );
      $(".lab-item").prop("disabled", false).trigger("change.select2");
    } else if (pembiayaan === "PROGRAM") {
      clearPenunjangSelection();
      $("#labContainer").hide();
      $("#penunjangAlert")
        .addClass("yki-alert-warning")
        .html(
          '<i class="fas fa-exclamation-triangle"></i><span>Pemeriksaan Penunjang Rujukan tidak tersedia untuk provider PROGRAM. Gunakan <b>Kunjungan ke Poliklinik</b> jika pasien masuk Program.</span>',
        );
    } else {
      clearPenunjangSelection();
      $("#labContainer").hide();
      $("#penunjangAlert")
        .removeClass("yki-alert-warning")
        .html(
          '<i class="fas fa-info-circle"></i><span>Pilih pembiayaan <b>UMUM</b> agar layanan Papsmear (Rujukan) dapat ditampilkan.</span>',
        );
    }
  }

  function clearPenunjangSelection() {
    $(".lab-item").val("").trigger("change");
    $(".harga-labelLab").text("Rp 0");
    $("input[name='lab_qty[]']").val("1");
    $("#labItemsList .yki-item-row:not(:first)").remove();
  }

  function addLabRow() {
    const options = $("#labItemsList select.lab-item:first").html();
    const newRow = `
      <div class="row g-3 align-items-end yki-item-row mb-2">
        <div class="col-xl-7 col-lg-7 col-md-12">
          <div class="yki-field mb-0">
            <label>Pelayanan Laboratorium <span>*</span></label>
            <select class="form-control lab-item" name="lab_item[]">${options}</select>
          </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4">
          <div class="yki-price-box harga-labelLab">Rp 0</div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4">
          <div class="yki-field mb-0">
            <label>Qty</label>
            <input type="number" class="form-control" name="lab_qty[]" min="1" value="1">
          </div>
        </div>
        <div class="col-xl-1 col-lg-1 col-md-4 d-flex gap-1">
          <button type="button" class="btn yki-btn-add addLabItem" title="Tambah item"><i class="fas fa-plus"></i></button>
          <button type="button" class="btn yki-btn-delete removeItem" title="Hapus item"><i class="fas fa-trash"></i></button>
        </div>
      </div>`;

    $("#labItemsList").append(newRow);

    if ($.fn.select2) {
      $("#labItemsList .lab-item:last").select2({ width: "100%" });
    }
  }

  function checkPoli() {
    const pembiayaan = $("#pembiayaan").val();

    if (!pembiayaan || state.jenisKunjungan !== "1") {
      $("#poli")
        .html('<option value="">-- Pilih Poliklinik --</option>')
        .trigger("change.select2");
      return;
    }

    $.ajax({
      url: BASE_URL + "RajalController/check_poli",
      type: "POST",
      dataType: "json",
      data: {
        jenis_kunjungan: state.jenisKunjungan,
        pembiayaan: pembiayaan,
      },
      success: function (data) {
        let option = '<option value="">-- Pilih Poliklinik --</option>';

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(function (item) {
            option += `<option value="${item.poli_id}">${item.keterangan}</option>`;
          });
        } else {
          option += '<option value="">Tidak ada poliklinik tersedia</option>';
        }

        $("#poli").html(option).trigger("change.select2");
      },
      error: function () {
        $("#poli")
          .html('<option value="">Gagal memuat poli</option>')
          .trigger("change.select2");
      },
    });
  }

  function cariDokter(tanggal, poli, callback = null) {
    const tanggalIso = moment(tanggal, "DD/MM/YYYY").format("YYYY-MM-DD");
    const day = moment(tanggalIso).locale("id").format("dddd");

    $.ajax({
      url: BASE_URL + "RajalController/check_dokter",
      type: "POST",
      dataType: "json",
      data: {
        tgl_berobat: tanggalIso,
        poli_id: poli,
        hari: day,
      },
      success: function (data) {
        let options = '<option value="">-- Pilih Dokter --</option>';

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(function (item) {
            options += `<option value="${item.dokter_id}">${item.nama}</option>`;
          });
        } else {
          options = '<option value="">Tidak ada dokter tersedia</option>';
        }

        $("#dokter").html(options).trigger("change.select2");
        resetJamSlot();

        if (typeof callback === "function") callback();
      },
      error: function () {
        resetDokterDanSlot();
      },
    });
  }

  function cariJamSlot(tanggal, poli, dokter, callback = null) {
    const tanggalIso = moment(tanggal, "DD/MM/YYYY").format("YYYY-MM-DD");
    const day = moment(tanggalIso).locale("id").format("dddd");

    $.ajax({
      url: BASE_URL + "RajalController/check_jam_slot",
      type: "POST",
      dataType: "json",
      data: {
        tgl_berobat: tanggalIso,
        poli_id: poli,
        dokter_id: dokter,
        day: day,
      },
      success: function (data) {
        let options = '<option value="">-- Pilih Jam Slot --</option>';

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(function (item) {
            const payload = JSON.stringify({
              antrian: item.antrian,
              jam_mulai: item.jam_mulai,
              jam_selesai: item.jam_selesai,
              hari_id: item.hari_id,
              antrian_id: item.antrian_id,
            });

            const disabled = item.flag === "T" ? "disabled" : "";
            const labelStatus = item.slot_status
              ? ` - ${item.slot_status}`
              : "";
            options += `<option value='${payload}' ${disabled}>Antrian ${item.antrian} (${item.jam_mulai} - ${item.jam_selesai})${labelStatus}</option>`;
          });
        } else {
          options = '<option value="">Tidak ada jam slot tersedia</option>';
        }

        $("#jam_slot").html(options).trigger("change.select2");
        if (typeof callback === "function") callback();
      },
      error: resetJamSlot,
    });
  }

  function loadHistoryLayanan() {
    const pasienId = $("#pasien_id").val();
    const target = $("#historyLayananPasien");

    if (!pasienId) {
      renderHistoryEmpty(
        "Pilih pasien untuk melihat riwayat layanan sebelumnya.",
      );
      return;
    }

    target.html(`
      <div class="yki-empty-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Memuat history layanan pasien...</p>
      </div>
    `);

    $.ajax({
      url: BASE_URL + "RajalController/get_history_layanan_pasien",
      type: "POST",
      dataType: "json",
      data: { pasien_id: pasienId },
      success: function (res) {
        if (
          !res ||
          !res.status ||
          !Array.isArray(res.data) ||
          res.data.length === 0
        ) {
          renderHistoryEmpty("Belum ada history layanan yang ditemukan.");
          return;
        }

        renderHistory(res.data);
      },
      error: function () {
        renderHistoryEmpty("Gagal memuat history layanan pasien.");
      },
    });
  }

  function renderHistory(data) {
    const html = data
      .map(function (item) {
        const providerClass =
          String(item.provider || "").toLowerCase() === "program"
            ? "program"
            : "umum";
        const layanan = item.layanan || "-";
        const dokter = item.nama_dokter || "-";
        const poli = item.nama_poli || "-";

        return `
          <div class="yki-history-item">
            <div class="yki-history-item-top">
              <span class="yki-history-date">${item.tgl_masuk || "-"}</span>
              <span class="yki-provider-badge ${providerClass}">${item.provider || "-"}</span>
            </div>
            <p><b>${poli}</b> · ${dokter}</p>
            <p class="layanan">${layanan}</p>
          </div>
        `;
      })
      .join("");

    $("#historyLayananPasien").html(html);
  }

  function renderHistoryEmpty(message) {
    $("#historyLayananPasien").html(`
      <div class="yki-empty-state">
        <i class="fas fa-file-medical-alt"></i>
        <p>${message}</p>
      </div>
    `);
  }

  function handleSubmit(e) {
    e.preventDefault();

    const pembiayaan = $("#pembiayaan").val();
    const namaPas = $("#namaPas").val().trim();
    const noRm = $("#noRm").val().trim();
    const pasienId = $("#pasien_id").val();
    const jenisKunjungan = $('input[name="jenis_kunjungan"]:checked').val();

    if (!pembiayaan || !namaPas || !noRm || !pasienId) {
      showWarning("Harap isi data pasien dan pembiayaan sebelum menyimpan.");
      return;
    }

    if (jenisKunjungan === "2") {
      if (pembiayaan !== "UMUM") {
        showWarning(
          "Pemeriksaan Penunjang Rujukan hanya dapat digunakan untuk pembiayaan UMUM.",
        );
        return;
      }

      const selectedLab = $("select.lab-item").filter(function () {
        return $(this).val() !== "";
      }).length;

      if (selectedLab === 0) {
        showWarning(
          "Harap pilih pelayanan Papsmear (Rujukan) terlebih dahulu.",
        );
        return;
      }

      // Alur baru tahap awal tidak memakai radiologi pada pemeriksaan penunjang.
      $("select.rad-item").val("");
    }

    if (jenisKunjungan === "1") {
      clearPenunjangSelection();

      if (
        !$("#tgl_berobat").val() ||
        !$("#poli").val() ||
        !$("#dokter").val() ||
        !$("#jam_slot").val()
      ) {
        showWarning(
          "Harap lengkapi data poliklinik: tanggal, poli, dokter, dan jam slot.",
        );
        return;
      }
    }

    const formDataArr = $("#formRegistLayan").serializeArray();

    if (pembiayaan === "UMUM") {
      prosesPembayaranUmum(formDataArr, jenisKunjungan);
      return;
    }

    if (pembiayaan === "PROGRAM") {
      validasiProgramLaluSimpan(formDataArr, jenisKunjungan, pasienId);
      return;
    }

    kirimDataRegistrasi(formDataArr);
  }

  function prosesPembayaranUmum(formDataArr, jenisKunjungan) {
    // Pemeriksaan Penunjang Rujukan UMUM wajib dibayar di awal.
    // Tujuannya supaya Pendaftaran + Papsmear Preparat langsung memiliki bayar_id
    // dan tidak tertarik ulang di Menu Kasir.
    if (jenisKunjungan === "2") {
      Swal.fire({
        title: "Pembayaran Wajib di Awal",
        text: "Pemeriksaan Penunjang Rujukan UMUM harus dibayar saat registrasi.",
        icon: "info",
        confirmButtonText: "Lanjut Pembayaran",
      }).then((result) => {
        if (result.isConfirmed) {
          hitungTarifDanBayar(formDataArr, jenisKunjungan);
        }
      });
      return;
    }

    Swal.fire({
      title: "Pembayaran Registrasi Poli",
      text: "Biaya registrasi poli dapat dibayar sekarang atau ditagihkan ke Kasir setelah pelayanan dokter.",
      icon: "question",
      showCancelButton: true,
      showDenyButton: true,
      confirmButtonText: "Bayar Sekarang",
      cancelButtonText: "Bayar Nanti di Kasir",
      denyButtonText: "Batal",
    }).then((result) => {
      if (result.isDenied) return;

      if (result.dismiss === Swal.DismissReason.cancel) {
        formDataArr.push({ name: "bayar_nanti", value: 1 });
        formDataArr.push({ name: "metode_bayar", value: "TUNDA" });
        formDataArr.push({ name: "uang_bayar", value: 0 });
        formDataArr.push({ name: "total_bayar", value: 0 });
        formDataArr.push({ name: "total_kembali", value: 0 });
        formDataArr.push({ name: "discount_type", value: "" });
        formDataArr.push({ name: "discount_value", value: 0 });
        formDataArr.push({ name: "discount_amount", value: 0 });
        formDataArr.push({ name: "total_before_discount", value: 0 });
        kirimDataRegistrasi(formDataArr);
        return;
      }

      if (result.isConfirmed) {
        hitungTarifDanBayar(formDataArr, jenisKunjungan);
      }
    });
  }

  function hitungTarifDanBayar(formDataArr, jenisKunjungan) {
    $.ajax({
      url: BASE_URL + "RajalController/getTarifByJenis/" + jenisKunjungan,
      method: "GET",
      dataType: "json",
      data: { poli_id: $("#poli").val() },
      success: function (res) {
        const tarifData = res.data || [];
        let totalSebelumDiskon = 0;
        let rincian = "";

        tarifData.forEach((t) => {
          const harga = parseInt(t.harga) || 0;
          totalSebelumDiskon += harga;
          rincian += `<li>${t.nama_layan1} - Rp ${formatRupiah(harga)}</li>`;
        });

        if (jenisKunjungan === "2") {
          $("select.lab-item").each(function () {
            if ($(this).val()) {
              const nama = $(this).find(":selected").text();
              const harga = toNumber(
                $(this).find(":selected").data("hargalab"),
              );
              const qty =
                parseInt(
                  $(this)
                    .closest(".yki-item-row")
                    .find("input[name='lab_qty[]']")
                    .val(),
                ) || 1;
              const sub = harga * qty;
              totalSebelumDiskon += sub;
              rincian += `<li>${nama} x${qty} - Rp ${formatRupiah(sub)}</li>`;
            }
          });
        }

        showModalTotalPembayaran(formDataArr, totalSebelumDiskon, rincian);
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: "Gagal mengambil data tarif dari server.",
        });
      },
    });
  }

  function showModalTotalPembayaran(formDataArr, totalSebelumDiskon, rincian) {
    Swal.fire({
      title: "Total Pembayaran",
      html: `
        <div style="text-align:left;font-size:13px">
          <p><b>Rincian Biaya:</b></p>
          <ul>${rincian || "<li>Tidak ada rincian tarif</li>"}</ul>
          <hr>
          <p><b>Total Sebelum Diskon: Rp <span id="totalBeforeDiscount">${formatRupiah(totalSebelumDiskon)}</span></b></p>

          <label>Jenis Diskon:</label>
          <select id="discountType" class="swal2-input">
            <option value="">Tanpa Diskon</option>
            <option value="PERSEN">Persen</option>
            <option value="NOMINAL">Nominal</option>
          </select>

          <div id="discountInputContainer" style="display:none;">
            <label id="discountLabel">Nilai Diskon:</label>
            <input type="text" id="discountValue" class="swal2-input" placeholder="Masukkan diskon..." inputmode="numeric">
          </div>

          <p><b>Total Diskon: Rp <span id="discountAmountText">0</span></b></p>
          <p><b>Total Setelah Diskon: Rp <span id="grandTotalText">${formatRupiah(totalSebelumDiskon)}</span></b></p>

          <hr>
          <label>Metode Pembayaran:</label>
          <select id="metodeBayar" class="swal2-input">
            <option value="CASH">Cash</option>
            <option value="QRIS">QRIS</option>
            <option value="DEBIT">Debit</option>
            <option value="TRANSFER">Transfer</option>
          </select>

          <div id="cashInputContainer">
            <label>Uang Tunai:</label>
            <input type="text" id="uangCash" class="swal2-input" placeholder="Masukkan nominal..." inputmode="numeric">
          </div>
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: "Lanjutkan",
      cancelButtonText: "Batal",
      didOpen: () => {
        const refreshTotal = () => {
          const tipe = $("#discountType").val();
          let nilai = toNumber($("#discountValue").val());
          let diskon = 0;

          if (tipe === "PERSEN") {
            if (nilai > 100) nilai = 100;
            diskon = Math.round((totalSebelumDiskon * nilai) / 100);
          } else if (tipe === "NOMINAL") {
            diskon = nilai;
          }

          if (diskon > totalSebelumDiskon) diskon = totalSebelumDiskon;

          const grandTotal = totalSebelumDiskon - diskon;
          $("#discountAmountText").text(formatRupiah(diskon));
          $("#grandTotalText").text(formatRupiah(grandTotal));

          return {
            total_before_discount: totalSebelumDiskon,
            discount_type: tipe,
            discount_value: nilai,
            discount_amount: diskon,
            grand_total: grandTotal,
          };
        };

        $(document)
          .off("change.ykiDisc")
          .on("change.ykiDisc", "#discountType", function () {
            if (this.value === "") {
              $("#discountInputContainer").hide();
              $("#discountValue").val("");
            } else {
              $("#discountInputContainer").show();
              $("#discountLabel").text(
                this.value === "PERSEN"
                  ? "Diskon Persen (%):"
                  : "Diskon Nominal (Rp):",
              );
            }
            refreshTotal();
          });

        $(document)
          .off("input.ykiDisc")
          .on("input.ykiDisc", "#discountValue, #uangCash", function () {
            this.value = formatRupiah(toNumber(this.value));
            refreshTotal();
          });

        $(document)
          .off("change.ykiPay")
          .on("change.ykiPay", "#metodeBayar", function () {
            if (this.value === "CASH") {
              $("#cashInputContainer").show();
            } else {
              $("#cashInputContainer").hide();
              $("#uangCash").val("");
            }
          });
      },
      preConfirm: () => {
        const total = getCurrentTotalPayment(totalSebelumDiskon);
        const metodeBayar = $("#metodeBayar").val();
        const uangCash = toNumber($("#uangCash").val());

        if (metodeBayar === "CASH" && uangCash < total.grand_total) {
          Swal.showValidationMessage(
            "Uang tunai kurang dari total pembayaran.",
          );
          return false;
        }

        return {
          metode_bayar: metodeBayar,
          uang_bayar: metodeBayar === "CASH" ? uangCash : total.grand_total,
          total_bayar: total.grand_total,
          total_kembali:
            metodeBayar === "CASH" ? uangCash - total.grand_total : 0,
          total_before_discount: total.total_before_discount,
          discount_type: total.discount_type,
          discount_value: total.discount_value,
          discount_amount: total.discount_amount,
        };
      },
    }).then((result) => {
      if (!result.isConfirmed) return;

      Object.entries(result.value).forEach(([name, value]) => {
        formDataArr.push({ name, value });
      });

      formDataArr.push({ name: "bayar_nanti", value: 0 });
      kirimDataRegistrasi(formDataArr);
    });
  }

  function getCurrentTotalPayment(totalSebelumDiskon) {
    const tipe = $("#discountType").val();
    let nilai = toNumber($("#discountValue").val());
    let diskon = 0;

    if (tipe === "PERSEN") {
      if (nilai > 100) nilai = 100;
      diskon = Math.round((totalSebelumDiskon * nilai) / 100);
    } else if (tipe === "NOMINAL") {
      diskon = nilai;
    }

    if (diskon > totalSebelumDiskon) diskon = totalSebelumDiskon;

    return {
      total_before_discount: totalSebelumDiskon,
      discount_type: tipe,
      discount_value: nilai,
      discount_amount: diskon,
      grand_total: totalSebelumDiskon - diskon,
    };
  }

  function validasiProgramLaluSimpan(formDataArr, jenisKunjungan, pasienId) {
    // alert("ss");
    Swal.fire({
      title: "Validasi Program",
      text: "Sistem memeriksa riwayat layanan pasien pada tahun berjalan.",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    $.ajax({
      url: BASE_URL + "RajalController/cekRiwayatProgram",
      type: "POST",
      dataType: "json",
      data: {
        pasien_id: pasienId,
        jenis_kunjungan: jenisKunjungan,
        poli: $("#poli").val(),
        lab_item: $("select.lab-item")
          .map(function () {
            return $(this).val();
          })
          .get(),
        rad_item: [],
      },
      success: function (res) {
        Swal.close();

        if (res.sudah_pernah) {
          Swal.fire({
            icon: "warning",
            title: "Riwayat Program Ditemukan",
            text:
              res.message ||
              "Pasien memiliki riwayat Program. Admin perlu validasi sebelum melanjutkan.",
            showCancelButton: true,
            confirmButtonText: "Tetap Lanjutkan",
            cancelButtonText: "Batal",
          }).then((confirm) => {
            if (confirm.isConfirmed) kirimDataRegistrasi(formDataArr);
          });
          return;
        }

        kirimDataRegistrasi(formDataArr);
      },
      error: function () {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: "Gagal memeriksa riwayat Program pasien.",
        });
      },
    });
  }

  function kirimDataRegistrasi(formDataArr) {
    $.ajax({
      url: BASE_URL + "RajalController/proses_regis_layan",
      method: "POST",
      dataType: "json",
      data: formDataArr,
      beforeSend: function () {
        Swal.fire({
          title: "Menyimpan Data...",
          text: "Mohon tunggu sebentar.",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });
      },
      success: function (res) {
        Swal.close();

        if (res.metadata && res.metadata.responCode === "00") {
          showSuccessCetak(
            res,
            res.metadata.responDesc || "Registrasi berhasil.",
          );
          return;
        }

        if (res.success && res.episode_id) {
          showSuccessCetak(res, res.message || "Registrasi berhasil disimpan.");
          return;
        }

        Swal.fire({
          icon: "error",
          title: "Gagal",
          text:
            res.message ||
            (res.metadata
              ? res.metadata.responDesc
              : "Terjadi kesalahan saat menyimpan data."),
        });
      },
      error: function (xhr, status, error) {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Gagal menghubungi server: " + error,
        });
      },
    });
  }

  function showSuccessCetak(res, message) {
    const episodeId = res.episode_id;
    const tglBerobat = res.tgl_berobat || moment().format("YYYY-MM-DD");
    const noAntrian = res.NoAntrianKlinik || "-";

    Swal.fire({
      icon: "success",
      title: "Berhasil",
      text: message,
      confirmButtonText: "Cetak Struk",
    }).then(() => {
      const url =
        BASE_URL +
        "Struk-Layan/" +
        episodeId +
        "/" +
        noAntrian +
        "/" +
        tglBerobat;
      const win = window.open(
        url,
        "_blank",
        "width=700,height=900,scrollbars=yes,resizable=yes",
      );

      if (!win) {
        Swal.fire({
          icon: "warning",
          title: "Popup diblokir",
          text: "Izinkan popup browser untuk membuka bukti pendaftaran.",
        });
        return;
      }

      win.focus();
      window.location.href = BASE_URL + "Daftar-Layanan";
    });
  }

  function resetDataPasien() {
    $(
      "#namaPas, #noRm, #tgl_lahir, #sex_id, #alamat, #pasien_id, #no_bpjs",
    ).val("");
    renderHistoryEmpty(
      "Pilih pasien untuk melihat riwayat layanan sebelumnya.",
    );
    resetPoliSelection();
    updateStatusIsian();
  }

  function resetPembiayaan() {
    $("#pembiayaan").val("").trigger("change");
    updateStatusIsian();
  }

  function resetRegistrasi() {
    clearPenunjangSelection();
    resetPoliSelection();
    updatePenunjangByProvider();
    updateStatusIsian();
  }

  function resetPoliSelection() {
    $("#poli")
      .html('<option value="">-- Pilih Poliklinik --</option>')
      .trigger("change.select2");
    resetDokterDanSlot();
  }

  function resetDokterDanSlot() {
    $("#dokter")
      .html('<option value="">-- Pilih Dokter --</option>')
      .trigger("change.select2");
    resetJamSlot();
  }

  function resetJamSlot() {
    $("#jam_slot")
      .html('<option value="">-- Pilih Jam Slot --</option>')
      .trigger("change.select2");
  }

  function updateStatusIsian() {
    const jenis = $('input[name="jenis_kunjungan"]:checked').val();
    let total = 4;
    let filled = 0;

    if ($("#namaPas").val().trim()) filled++;
    if ($("#noRm").val().trim() && $("#pasien_id").val()) filled++;
    if ($("#pembiayaan").val()) filled++;

    if (jenis === "2") {
      total = 4;
      if (
        $("#pembiayaan").val() === "UMUM" &&
        $("select.lab-item").filter(function () {
          return $(this).val();
        }).length > 0
      )
        filled++;
    } else {
      total = 6;
      if ($("#poli").val()) filled++;
      if ($("#dokter").val()) filled++;
      if ($("#jam_slot").val()) filled++;
    }

    const pct = Math.min(100, Math.round((filled / total) * 100));
    $("#statusIsianBar").css("width", pct + "%");
    $("#statusIsianText").text(pct >= 100 ? "Lengkap" : "Belum lengkap");
  }

  function showWarning(message) {
    Swal.fire({
      icon: "warning",
      title: "Perhatian",
      text: message,
      confirmButtonText: "OK",
    });
  }

  function formatTanggal(tgl) {
    if (!tgl) return "";
    const d = new Date(tgl);
    if (isNaN(d)) return tgl;
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    return `${day}.${month}.${year}`;
  }

  function toNumber(val) {
    return parseInt(String(val || "0").replace(/\D/g, ""), 10) || 0;
  }

  function formatRupiah(val) {
    return Number(val || 0).toLocaleString("id-ID");
  }
});

function fetchBookingList() {
  const tglInput = document.getElementById("filterTanggalBook");
  const poliInput = document.getElementById("filterPoliBook");
  const dokterInput = document.getElementById("filterDokterBook");

  if (!tglInput) return;

  const tglBook = tglInput.value;
  const poliBook = poliInput ? poliInput.value : "";
  const dokterBook = dokterInput ? dokterInput.value : "";

  const url =
    BASE_URL +
    "RajalController/get_booking_list" +
    `?tgl=${tglBook}&poli=${poliBook}&dokter=${dokterBook}`;

  $.ajax({
    url: url,
    type: "GET",
    dataType: "json",
    success: function (data) {
      if (typeof renderBookingList === "function") {
        renderBookingList(data);
      }
    },
  });
}
