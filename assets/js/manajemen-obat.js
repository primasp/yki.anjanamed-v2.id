console.log("Manajemen Obat JS loaded");
// alert("sas");
$(document).ready(function () {
  initTableManajemenObat();
  bindManajemenObatEvents();
});

function bindManajemenObatEvents() {
  $(document)
    .off("click", ".js-adjust-stok")
    .on("click", ".js-adjust-stok", function () {
      openAdjustmentModal($(this));
    });

  $(document)
    .off("click", ".edit-obat")
    .on("click", ".edit-obat", function (e) {
      e.preventDefault();

      const obatId = String($(this).attr("data-id") || "").trim();

      if (!obatId) {
        Swal.fire("Gagal", "Obat ID tidak ditemukan.", "error");
        return;
      }

      openEditObatModal(obatId);
    });

  $("#btn-update-obt")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      updateObat();
    });

  $("#btn-simpan-obt")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      simpanObatBaru();
    });

  $(document)
    .off(
      "input change",
      "#edit_kon_satuan, #edit_hna_besar, #edit_hna_ppn_besar, #edit_ppn_persen, #edit_margin_persen, #edit_auto_ppn, #edit_harga_final_checkbox, #edit_harga_manual, #edit_harga_final",
    )
    .on(
      "input change",
      "#edit_kon_satuan, #edit_hna_besar, #edit_hna_ppn_besar, #edit_ppn_persen, #edit_margin_persen, #edit_auto_ppn, #edit_harga_final_checkbox, #edit_harga_manual, #edit_harga_final",
      function () {
        hitungHargaEditObat();
      },
    );

  $(document)
    .off(
      "input change",
      "#add_kon_satuan, #add_hna_besar, #add_hna_ppn_besar, #add_ppn_persen, #add_margin_persen, #add_auto_ppn, #add_harga_final_checkbox, #add_harga_manual, #add_harga_final",
    )
    .on(
      "input change",
      "#add_kon_satuan, #add_hna_besar, #add_hna_ppn_besar, #add_ppn_persen, #add_margin_persen, #add_auto_ppn, #add_harga_final_checkbox, #add_harga_manual, #add_harga_final",
      function () {
        hitungHargaTambahObat();
      },
    );

  $("#modalTambahObat")
    .off("shown.bs.modal")
    .on("shown.bs.modal", function () {
      resetFormTambahObat();
      $(this).find(".modal-body").scrollTop(0);
    });

  $("#modalEditObat")
    .off("shown.bs.modal")
    .on("shown.bs.modal", function () {
      $(this).find(".modal-body").scrollTop(0);
    });
}

/* =========================
   DATATABLE
========================= */
function initTableManajemenObat() {
  if (!$("#manajemen-obat-table").length) return;

  $("#manajemen-obat-table").DataTable({
    destroy: true,
    pageLength: 10,
    searching: true,
    ordering: true,
    paging: true,
    responsive: false,
    autoWidth: false,
    scrollX: true,
    order: [],
    lengthMenu: [
      [10, 25, 50, -1],
      ["10", "25", "50", "Tampilkan Semua"],
    ],
    language: {
      lengthMenu: "Tampilkan _MENU_ data",
      zeroRecords: "Data tidak ditemukan",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(difilter dari _MAX_ total data)",
      search: "Cari cepat:",
      paginate: {
        next: "Berikutnya",
        previous: "Sebelumnya",
      },
    },
  });
}

/* =========================
   TAMBAH OBAT
========================= */
function resetFormTambahObat() {
  const form = document.getElementById("form-tambah-obat");
  if (form) form.reset();

  $("#add_kon_satuan").val("1");
  $("#add_ppn_persen").val("11");
  $("#add_margin_persen").val("20");

  $("#add_hna_besar").val("0");
  $("#add_hna_ppn_besar").val("0");
  $("#add_jual_sat").val("0");
  $("#add_jual_sat_hna_ppn").val("0");
  $("#add_harga_final").val("0");

  $("#add_auto_ppn").prop("checked", true);
  $("#add_harga_final_checkbox").prop("checked", true);
  $("#add_harga_manual").prop("checked", false);
  $("#add_harga_final").prop("readonly", true);

  $("#add_stok_awal").val("0");

  hitungHargaTambahObat();
}

function hitungHargaTambahObat() {
  const konversi = parseNumberObat($("#add_kon_satuan").val()) || 1;
  const ppnPersen = parseNumberObat($("#add_ppn_persen").val()) || 0;
  const marginPersen = parseNumberObat($("#add_margin_persen").val()) || 0;

  let hnaBesar = parseNumberObat($("#add_hna_besar").val());
  let hnaPpnBesar = parseNumberObat($("#add_hna_ppn_besar").val());

  const autoPpn = $("#add_auto_ppn").is(":checked");
  const pakaiMargin = $("#add_harga_final_checkbox").is(":checked");
  const hargaManual = $("#add_harga_manual").is(":checked");

  if (autoPpn) {
    hnaPpnBesar = hnaBesar + (hnaBesar * ppnPersen) / 100;
    $("#add_hna_ppn_besar").val(formatAngkaInput(hnaPpnBesar));
  }

  const hargaSatuan = konversi > 0 ? hnaBesar / konversi : 0;
  const hargaSatuanPpn = konversi > 0 ? hnaPpnBesar / konversi : 0;

  $("#add_jual_sat").val(formatAngkaInput(hargaSatuan));
  $("#add_jual_sat_hna_ppn").val(formatAngkaInput(hargaSatuanPpn));

  if (!hargaManual) {
    let hargaFinal = hargaSatuanPpn;

    if (pakaiMargin) {
      hargaFinal = hargaSatuanPpn + (hargaSatuanPpn * marginPersen) / 100;
    }

    $("#add_harga_final").val(formatAngkaInput(hargaFinal));
  }

  $("#add_harga_final").prop("readonly", !hargaManual);
}

function simpanObatBaru() {
  hitungHargaTambahObat();

  const required = [
    { id: "#add_nama_obat", name: "Nama Obat" },
    { id: "#add_satuan_jual", name: "Satuan Jual" },
    { id: "#add_satuan_beli", name: "Satuan Beli" },
    { id: "#add_kon_satuan", name: "Konversi Satuan" },
    { id: "#add_hna_besar", name: "Harga HNA" },
    { id: "#add_hna_ppn_besar", name: "Harga HNA + PPN" },
    { id: "#add_jual_sat", name: "Harga Satuan" },
    { id: "#add_jual_sat_hna_ppn", name: "Harga Satuan + PPN" },
    { id: "#add_harga_final", name: "Harga Jual Final" },
    { id: "#add_gudang_id", name: "Depo / Gudang" },
  ];

  for (const f of required) {
    if (!$(f.id).val()) {
      Swal.fire("Validasi", f.name + " wajib diisi.", "warning");
      $(f.id).focus();
      return;
    }
  }

  const $btn = $("#btn-simpan-obt");
  const oldHtml = $btn.html();

  $btn
    .prop("disabled", true)
    .html('<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...');

  $.ajax({
    url: BASE_URL + "MasterController/tambahObat",
    type: "POST",
    data: $("#form-tambah-obat").serialize(),
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          html:
            (response.message || "Obat berhasil ditambahkan.") +
            (response.data && response.data.obat_id
              ? "<br><b>Kode Obat:</b> " + response.data.obat_id
              : ""),
        }).then(function () {
          $("#modalTambahObat").modal("hide");
          location.reload();
        });
        return;
      }

      Swal.fire(
        "Gagal",
        response.message || "Gagal menambahkan obat.",
        "error",
      );
    },
    error: function (xhr) {
      console.error("Tambah obat error:", xhr.responseText);
      Swal.fire("Error", "Terjadi kesalahan saat menyimpan obat.", "error");
    },
    complete: function () {
      $btn.prop("disabled", false).html(oldHtml);
    },
  });
}

/* =========================
   EDIT OBAT
========================= */
function openEditObatModal(obatId) {
  $.ajax({
    url: BASE_URL + "MasterController/getObatById",
    type: "POST",
    dataType: "json",
    data: {
      obat_id: obatId,
    },
    success: function (res) {
      if (!res || res.status !== "success" || !res.data) {
        Swal.fire(
          "Gagal",
          res?.message || "Data obat tidak ditemukan.",
          "error",
        );
        return;
      }

      const data = res.data;

      $("#edit_obat_id").val(data.obat_id || "");
      $("#edit_nama_obat").val(data.nama || "");

      $("#edit_sediaan_obat").val(data.sediaan_id || "");
      $("#edit_pabrik").val(data.pabrik_id || "");
      $("#edit_route").val(data.route_id || "");
      $("#edit_satuan_jual").val(data.satuan_kecil_id || "");
      $("#edit_satuan_beli").val(data.satuan_besar_id || "");

      $("#edit_gol_obat").val(data.gol_obat || "");
      $("#edit_generik_obat").val(data.generik_id || "");

      $("#edit_kon_satuan").val(formatAngkaInput(data.konversi_satuan || 1));
      $("#edit_hna_besar").val(formatAngkaInput(data.harga_hna || 0));
      $("#edit_hna_ppn_besar").val(formatAngkaInput(data.harga_hna_ppn || 0));
      $("#edit_jual_sat").val(formatAngkaInput(data.harga || 0));
      $("#edit_jual_sat_hna_ppn").val(
        formatAngkaInput(data.harga_sat_ppn || 0),
      );
      $("#edit_harga_final").val(formatAngkaInput(data.harga_jual || 0));

      $("#edit_ecatalogue").val(data.ecatalogue || "");
      $("#edit_batasan_fornas").val(data.batasan_fornas || "");
      $("#edit_nomor_registrasi").val(data.no_registrasi || "");

      $("#edit_harga_final_checkbox").prop(
        "checked",
        isTrueValue(data.is_margin),
      );
      $("#edit_harga_manual").prop("checked", false);

      $("#edit_obat_fornas").prop("checked", data.obat_fornas === "Y");
      $("#edit_obat_hialert").prop("checked", data.obat_hialert === "Y");
      $("#edit_obat_produksi").prop("checked", data.is_produksi === "Y");
      $("#edit_is_pembungkus").prop("checked", data.is_pembungkus === "Y");
      $("#edit_alkes").prop("checked", data.jenis === "A");
      $("#edit_obat_bpjs").prop("checked", data.mandiri === "1");
      $("#edit_semuaykn_obat").prop("checked", data.tampil === "0");

      hitungHargaEditObat();

      $("#modalEditObat").modal("show");
    },
    error: function (xhr) {
      console.error("Gagal mengambil data obat:", xhr.responseText);
      Swal.fire("Error", "Gagal mengambil data obat.", "error");
    },
  });
}

function hitungHargaEditObat() {
  const konversi = parseNumberObat($("#edit_kon_satuan").val()) || 1;
  const ppnPersen = parseNumberObat($("#edit_ppn_persen").val()) || 0;
  const marginPersen = parseNumberObat($("#edit_margin_persen").val()) || 0;

  let hnaBesar = parseNumberObat($("#edit_hna_besar").val());
  let hnaPpnBesar = parseNumberObat($("#edit_hna_ppn_besar").val());

  const autoPpn = $("#edit_auto_ppn").is(":checked");
  const pakaiMargin = $("#edit_harga_final_checkbox").is(":checked");
  const hargaManual = $("#edit_harga_manual").is(":checked");

  if (autoPpn) {
    hnaPpnBesar = hnaBesar + (hnaBesar * ppnPersen) / 100;
    $("#edit_hna_ppn_besar").val(formatAngkaInput(hnaPpnBesar));
  }

  const hargaSatuan = konversi > 0 ? hnaBesar / konversi : 0;
  const hargaSatuanPpn = konversi > 0 ? hnaPpnBesar / konversi : 0;

  $("#edit_jual_sat").val(formatAngkaInput(hargaSatuan));
  $("#edit_jual_sat_hna_ppn").val(formatAngkaInput(hargaSatuanPpn));

  if (!hargaManual) {
    let hargaFinal = hargaSatuanPpn;

    if (pakaiMargin) {
      hargaFinal = hargaSatuanPpn + (hargaSatuanPpn * marginPersen) / 100;
    }

    $("#edit_harga_final").val(formatAngkaInput(hargaFinal));
  }

  $("#edit_harga_final").prop("readonly", !hargaManual);
}

function updateObat() {
  hitungHargaEditObat();

  if (!$("#form-edit-obat").length) {
    Swal.fire("Gagal", "Form #form-edit-obat tidak ditemukan.", "error");
    return;
  }

  const $btn = $("#btn-update-obt");
  const oldHtml = $btn.html();

  $btn
    .prop("disabled", true)
    .html('<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...');

  $.ajax({
    url: BASE_URL + "MasterController/updateObat",
    type: "POST",
    data: $("#form-edit-obat").serialize(),
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        Swal.fire("Berhasil", response.message, "success").then(function () {
          $("#modalEditObat").modal("hide");
          location.reload();
        });
        return;
      }

      Swal.fire(
        "Gagal",
        response.message || "Gagal memperbarui obat.",
        "error",
      );
    },
    error: function (xhr) {
      console.error("Gagal update obat:", xhr.responseText);
      Swal.fire("Error", "Terjadi kesalahan saat update obat.", "error");
    },
    complete: function () {
      $btn.prop("disabled", false).html(oldHtml);
    },
  });
}

/* =========================
   ADJUSTMENT STOK
========================= */
function openAdjustmentModal($btn) {
  const obatId = String($btn.attr("data-obat-id") || "").trim();
  const obatNama = String($btn.attr("data-obat-nama") || "").trim();

  let gudangId = String($btn.attr("data-gudang-id") || "").trim();
  let gudangNama = String($btn.attr("data-gudang-nama") || "").trim();

  const stokKomputer = parseFloat($btn.attr("data-stok-komputer") || 0);

  if (!gudangId || gudangId === "-" || gudangId.toLowerCase() === "semua") {
    gudangId = "DEPO00000000APT";
  }

  if (!gudangNama || gudangNama === "-") {
    gudangNama = "DEPO RAWAT JALAN";
  }

  if (!obatId) {
    Swal.fire(
      "Gagal",
      "Obat ID tidak ditemukan pada tombol adjustment.",
      "error",
    );
    return;
  }

  Swal.fire({
    title: "Adjustment Stok",
    html: `
      <div class="adjustment-box text-start">
        <div class="mb-3 text-center">
          <div class="fw-bold fs-5 text-primary">${escapeHtml(obatNama)}</div>
          <div class="text-muted small">${escapeHtml(obatId)} • ${escapeHtml(gudangNama)}</div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="adjust-card">
              <div class="adjust-label">Stok Komputer</div>
              <div class="adjust-value ${stokKomputer < 0 ? "text-danger" : "text-success"}">
                ${formatNumberId(stokKomputer)}
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="adjust-card">
              <div class="adjust-label">Stok Fisik</div>
              <input type="number"
                     min="0"
                     step="0.01"
                     id="stokFisik"
                     class="form-control form-control-lg text-center fw-bold"
                     placeholder="Masukkan stok fisik">
            </div>
          </div>
        </div>

        <div class="alert alert-info small mb-0">
          Sistem akan menghitung adjustment otomatis di server.
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: "Simpan Adjustment",
    cancelButtonText: "Batal",
    focusConfirm: false,
    preConfirm: function () {
      const stokFisik = $("#stokFisik").val();

      if (stokFisik === "" || isNaN(stokFisik) || parseFloat(stokFisik) < 0) {
        Swal.showValidationMessage(
          "Stok fisik wajib diisi dan tidak boleh minus.",
        );
        return false;
      }

      return {
        stok_fisik: parseFloat(stokFisik),
      };
    },
  }).then(function (result) {
    if (!result.isConfirmed) return;

    submitAdjustmentStok({
      obat_id: obatId,
      gudang_id: gudangId,
      stok_fisik: result.value.stok_fisik,
    });
  });
}

function submitAdjustmentStok(payload) {
  $.ajax({
    url: BASE_URL + "MasterController/adjust_stok_fisik",
    type: "POST",
    dataType: "json",
    data: payload,
    success: function (response) {
      if (response.status === "success") {
        const d = response.data || {};

        Swal.fire({
          icon: "success",
          title: "Adjustment Berhasil",
          html: `
            <div class="text-start">
              <div>Stok sebelum: <b>${formatNumberId(d.stok_sebelum || 0)}</b></div>
              <div>Adjust lama: <b>${formatNumberId(d.adjust_lama || 0)}</b></div>
              <div>Adjust baru: <b>${formatNumberId(d.adjust_baru || 0)}</b></div>
              <div>Stok sesudah: <b>${formatNumberId(d.stok_sesudah || 0)}</b></div>
            </div>
          `,
        }).then(function () {
          location.reload();
        });
        return;
      }

      Swal.fire(
        "Gagal",
        response.message || "Adjustment gagal disimpan.",
        "error",
      );
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      Swal.fire(
        "Error",
        "Terjadi kesalahan saat menyimpan adjustment.",
        "error",
      );
    },
  });
}

/* =========================
   HELPER
========================= */
function parseNumberObat(value) {
  if (value === null || value === undefined || value === "") return 0;

  let v = String(value).trim();
  v = v.replace(/[^0-9,.\-]/g, "");

  const hasComma = v.includes(",");
  const hasDot = v.includes(".");

  if (hasComma && hasDot) {
    v = v.replace(/\./g, "").replace(",", ".");
    return parseFloat(v) || 0;
  }

  if (hasDot && /^\d{1,3}(\.\d{3})+$/.test(v)) {
    v = v.replace(/\./g, "");
    return parseFloat(v) || 0;
  }

  if (hasDot && /^\d+\.\d{1,2}$/.test(v)) {
    return parseFloat(v) || 0;
  }

  if (hasComma && /^\d{1,3}(,\d{3})+$/.test(v)) {
    v = v.replace(/,/g, "");
    return parseFloat(v) || 0;
  }

  if (hasComma) {
    v = v.replace(",", ".");
    return parseFloat(v) || 0;
  }

  return parseFloat(v) || 0;
}

function formatAngkaInput(value) {
  const num = parseFloat(value || 0);

  return num.toLocaleString("id-ID", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });
}

function formatNumberId(value) {
  const num = parseFloat(value || 0);

  return num.toLocaleString("id-ID", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
}

function isTrueValue(value) {
  return (
    value === true ||
    value === "t" ||
    value === "true" ||
    value === "1" ||
    value === 1
  );
}

function escapeHtml(str) {
  return String(str ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}
