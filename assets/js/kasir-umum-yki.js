$(document).ready(function () {
  "use strict";

  const CFG = window.KASIR_UMUM_CONFIG || {};
  const INIT = window.KASIR_UMUM_DATA || {};
  let patients = Array.isArray(INIT.patients) ? INIT.patients : [];
  let paymentMethods = Array.isArray(INIT.payment_methods)
    ? INIT.payment_methods
    : [];
  let selectedPatient = null;
  let paymentRows = [];
  let activeKategori = "";

  init();

  function init() {
    renderWorklist();
    bindEvents();
    clearBilling();
    if (patients.length > 0) loadBilling(patients[0].episode_id, 0);
  }

  function bindEvents() {
    $("#tanggal_mulai, #tanggal_selesai").on("change", refreshWorklist);
    $("#keywordKasir").on("keyup", debounce(refreshWorklist, 300));
    $("#btnRefreshKasirUmum").on("click", refreshWorklist);

    $(".kasir-chip-filter button").on("click", function () {
      $(".kasir-chip-filter button").removeClass("active");
      $(this).addClass("active");
      activeKategori = String($(this).data("kategori") || "");
      refreshWorklist();
    });

    $(document).on("click", ".kasir-patient-card", function () {
      loadBilling(
        $(this).data("episode-id"),
        parseInt($(this).data("index"), 10),
      );
    });

    $("#btnCheckAll").on("click", function () {
      $(".billing-check").prop("checked", true);
      recalc();
    });
    $("#btnUncheckAll").on("click", function () {
      Swal.fire(
        "Informasi",
        "Pada struktur transaksi saat ini bayar_id berada di header transaksi, sehingga pembayaran harus mencakup semua tagihan belum bayar pasien.",
        "info",
      );
      $(".billing-check").prop("checked", true);
      recalc();
    });
    $(document).on("change", ".billing-check", recalc);
    $("#discountType, #discountValue").on("input change", recalc);
    $("#btnAddPayment").on("click", function () {
      addPaymentRow(0, "TUNAI");
      recalc();
    });

    $(document).on(
      "input change",
      ".payment-method, .payment-amount, .payment-ref",
      function () {
        updatePaymentRows();
        recalc();
      },
    );

    $(document).on("click", ".btnRemovePayment", function () {
      const idx = parseInt($(this).closest(".payment-row").data("index"), 10);
      paymentRows.splice(idx, 1);
      renderPaymentRows();
      recalc();
    });

    $("#btnProsesBayarTop, #btnProsesBayarBottom").on("click", processPayment);
  }

  function csrfData(data) {
    data = data || {};
    if (CFG.csrfName && CFG.csrfHash) data[CFG.csrfName] = CFG.csrfHash;
    return data;
  }
  function updateCsrf(res) {
    if (res && res.csrfHash) CFG.csrfHash = res.csrfHash;
  }

  function refreshWorklist() {
    $.ajax({
      url: CFG.ajaxListUrl,
      method: "POST",
      dataType: "json",
      data: csrfData({
        tanggal_mulai: $("#tanggal_mulai").val(),
        tanggal_selesai: $("#tanggal_selesai").val(),
        keyword: $("#keywordKasir").val(),
        kategori: activeKategori,
      }),
      beforeSend: function () {
        $("#kasirWorklist").html(
          `<div class="kasir-empty"><i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat data...</div>`,
        );
      },
      success: function (res) {
        updateCsrf(res);
        if (!res.success) {
          $("#kasirWorklist").html(
            `<div class="kasir-empty text-danger">${escapeHtml(res.message || "Gagal memuat data.")}</div>`,
          );
          return;
        }
        patients = Array.isArray(res.data) ? res.data : [];
        renderWorklist();
        if (!patients.length) clearBilling();
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $("#kasirWorklist").html(
          `<div class="kasir-empty text-danger">Gagal memuat worklist kasir.</div>`,
        );
      },
    });
  }

  function renderWorklist() {
    $("#worklistCount").text(patients.length);
    if (!patients.length) {
      $("#kasirWorklist").html(
        `<div class="kasir-empty"><i class="fa-solid fa-circle-info me-1"></i> Tidak ada transaksi UMUM yang belum dibayar.</div>`,
      );
      return;
    }
    let html = "";
    patients.forEach(function (p, idx) {
      const total = parseNumber(p.total_tagihan);
      html += `
        <div class="kasir-patient-card" data-index="${idx}" data-episode-id="${escapeAttr(p.episode_id)}">
          <div class="d-flex justify-content-between gap-2">
            <div class="d-flex gap-2 min-width-0">
              <div class="patient-mini-avatar">${getInitials(p.nama || "P")}</div>
              <div class="min-width-0">
                <div class="patient-name text-truncate">${escapeHtml(p.nama || "-")}</div>
                <div class="patient-meta">RM ${escapeHtml(p.no_rm || "-")} • ${escapeHtml(p.poli || "-")}</div>
                <div class="patient-meta">${escapeHtml(p.kelompok_tagihan || "-")} • ${escapeHtml(p.tanggal_kunjungan || "-")}</div>
              </div>
            </div>
            <div class="text-end"><span class="badge bg-success">UMUM</span><div class="patient-total">${formatRupiah(total)}</div></div>
          </div>
        </div>`;
    });
    $("#kasirWorklist").html(html);
  }

  function loadBilling(episodeId, index) {
    $.ajax({
      url: CFG.ajaxLoadBillingUrl,
      method: "POST",
      dataType: "json",
      data: csrfData({ episode_id: episodeId }),
      beforeSend: function () {
        $("#billingBody").html(
          `<tr><td colspan="6" class="text-center text-muted py-5"><i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat billing...</td></tr>`,
        );
      },
      success: function (res) {
        updateCsrf(res);
        if (!res.success) {
          Swal.fire(
            "Gagal",
            res.message || "Billing tidak ditemukan.",
            "warning",
          );
          clearBilling();
          return;
        }
        selectedPatient = res.patient;
        renderSelectedPatient();
        renderBilling();
        resetPayments();
        recalc();
        $(".kasir-patient-card").removeClass("active");
        $(`.kasir-patient-card[data-index="${index}"]`).addClass("active");
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire("Error", "Gagal memuat billing pasien.", "error");
        clearBilling();
      },
    });
  }

  function renderSelectedPatient() {
    if (!selectedPatient) return;
    $("#selectedAvatar").text(getInitials(selectedPatient.nama || "K"));
    $("#selectedNama").text(selectedPatient.nama || "-");
    $("#selectedRm").text("RM " + (selectedPatient.no_rm || "-"));
    $("#selectedPoli").text(selectedPatient.poli || "-");
    $("#selectedDokter").text(selectedPatient.dokter || "-");
    $("#selectedInvoice").text(selectedPatient.invoice_no || "-");
    $("#selectedTanggal").text(selectedPatient.tanggal_kunjungan || "-");
    $("#selectedStatus").text("BELUM BAYAR");
  }

  function renderBilling() {
    if (
      !selectedPatient ||
      !Array.isArray(selectedPatient.items) ||
      !selectedPatient.items.length
    ) {
      $("#billingBody").html(
        `<tr><td colspan="6" class="text-center text-muted py-5">Tidak ada item belum bayar.</td></tr>`,
      );
      return;
    }
    let html = "";
    selectedPatient.items.forEach(function (it, idx) {
      html += `<tr>
        <td class="text-center"><input type="checkbox" class="form-check-input billing-check" data-index="${idx}" checked></td>
        <td><div class="item-name">${escapeHtml(it.nama_item || "-")}</div><div class="item-sub">${escapeHtml(it.ref_id || "")} • ${escapeHtml(it.kategori_id || "")}</div></td>
        <td class="text-center">${getKategoriBadge(it.source_type)}</td>
        <td class="text-center">${formatQty(it.qty)}</td>
        <td class="text-end">${formatRupiah(it.tarif)}</td>
        <td class="text-end fw-bold">${formatRupiah(it.harga_total)}</td>
      </tr>`;
    });
    $("#billingBody").html(html);
  }

  function resetPayments() {
    paymentRows = [];
    addPaymentRow(0, "TUNAI", false);
    $("#discountType").val("NOMINAL");
    $("#discountValue").val(0);
    $("#dibayarOleh").val(selectedPatient ? selectedPatient.nama : "");
  }

  function addPaymentRow(amount, method, renderNow = true) {
    paymentRows.push({
      method: method || "TUNAI",
      amount: parseNumber(amount),
      ref_no: "",
    });
    if (renderNow) renderPaymentRows();
  }

  function renderPaymentRows() {
    if (!selectedPatient) {
      $("#paymentList").html(
        `<div class="kasir-empty">Pilih pasien terlebih dahulu.</div>`,
      );
      return;
    }
    if (!paymentRows.length) {
      $("#paymentList").html(
        `<div class="kasir-empty">Belum ada metode pembayaran.</div>`,
      );
      return;
    }
    let html = "";
    paymentRows.forEach(function (row, idx) {
      html += `<div class="payment-row" data-index="${idx}"><div class="row g-2 align-items-end">
        <div class="col-5"><label>Metode</label><select class="form-select form-select-sm payment-method">${paymentOptions(row.method)}</select></div>
        <div class="col-5"><label>Nominal</label><input type="number" min="0" class="form-control form-control-sm payment-amount" value="${row.amount || 0}"></div>
        <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 btnRemovePayment"><i class="fa-solid fa-trash"></i></button></div>
        <div class="col-12"><label>No. Referensi / QRIS / Debit</label><input type="text" class="form-control form-control-sm payment-ref" value="${escapeAttr(row.ref_no || "")}"></div>
      </div></div>`;
    });
    $("#paymentList").html(html);
  }

  function paymentOptions(selected) {
    let options = "";
    paymentMethods.forEach(function (m) {
      const id = m.id || m.jbayar_id || "";
      options += `<option value="${escapeAttr(id)}" ${String(selected).toUpperCase() === String(id).toUpperCase() ? "selected" : ""}>${escapeHtml(m.nama || id)}</option>`;
    });
    return options;
  }

  function updatePaymentRows() {
    const rows = [];
    $("#paymentList .payment-row").each(function () {
      rows.push({
        method: $(this).find(".payment-method").val(),
        amount: parseNumber($(this).find(".payment-amount").val()),
        ref_no: $(this).find(".payment-ref").val(),
      });
    });
    paymentRows = rows;
  }

  function recalc() {
    if (!selectedPatient || !Array.isArray(selectedPatient.items)) {
      setSummary(0, 0, 0, 0, 0, 0, 0, 0, 0);
      return;
    }
    updatePaymentRows();
    let penunjang = 0,
      tindakan = 0,
      obat = 0,
      subtotal = 0;
    const selectedItems = getSelectedItems();
    selectedItems.forEach(function (it) {
      const total = parseNumber(it.harga_total);
      subtotal += total;
      if (it.source_type === "PENUNJANG") penunjang += total;
      else if (it.source_type === "TINDAKAN") tindakan += total;
      else if (it.source_type === "OBAT") obat += total;
    });
    let diskon = calcDiskon(subtotal);
    let harusBayar = Math.max(subtotal - diskon, 0);
    let totalBayar = paymentRows.reduce(
      (sum, p) => sum + parseNumber(p.amount),
      0,
    );
    let sisa = Math.max(harusBayar - totalBayar, 0);
    let kembali = Math.max(totalBayar - harusBayar, 0);
    setSummary(
      penunjang,
      tindakan,
      obat,
      subtotal,
      diskon,
      harusBayar,
      totalBayar,
      sisa,
      kembali,
    );
    if (paymentRows.length === 1 && totalBayar === 0 && harusBayar > 0) {
      paymentRows[0].amount = harusBayar;
      renderPaymentRows();
      $("#sumTotalBayar").text(formatRupiah(harusBayar));
      $("#sumSisa").text(formatRupiah(0));
      $("#sumKembali").text(formatRupiah(0));
    }
  }

  function calcDiskon(subtotal) {
    const type = $("#discountType").val();
    let value = parseNumber($("#discountValue").val());
    if (value < 0) value = 0;
    if (type === "PERSEN") {
      if (value > 100) value = 100;
      return Math.round((subtotal * value) / 100);
    }
    if (value > subtotal) value = subtotal;
    return value;
  }

  function setSummary(
    penunjang,
    tindakan,
    obat,
    subtotal,
    diskon,
    harusBayar,
    totalBayar,
    sisa,
    kembali,
  ) {
    $("#sumPenunjang").text(formatRupiah(penunjang));
    $("#sumTindakan").text(formatRupiah(tindakan));
    $("#sumObat").text(formatRupiah(obat));
    $("#sumSubtotal").text(formatRupiah(subtotal));
    $("#sumDiskon").text(formatRupiah(diskon));
    $("#sumHarusBayar").text(formatRupiah(harusBayar));
    $("#sumTotalBayar").text(formatRupiah(totalBayar));
    $("#sumSisa").text(formatRupiah(sisa));
    $("#sumKembali").text(formatRupiah(kembali));
  }

  function getSelectedItems() {
    if (!selectedPatient || !Array.isArray(selectedPatient.items)) return [];
    const items = [];
    $(".billing-check:checked").each(function () {
      const idx = parseInt($(this).data("index"), 10);
      if (selectedPatient.items[idx]) items.push(selectedPatient.items[idx]);
    });
    return items;
  }

  function processPayment() {
    if (!selectedPatient) {
      Swal.fire("Perhatian", "Pilih pasien terlebih dahulu.", "warning");
      return;
    }
    const selectedItems = getSelectedItems();
    if (!selectedItems.length) {
      Swal.fire("Perhatian", "Pilih minimal satu item tagihan.", "warning");
      return;
    }
    if (
      selectedPatient.items &&
      selectedItems.length !== selectedPatient.items.length
    ) {
      Swal.fire(
        "Perhatian",
        "Pembayaran pasien UMUM harus mencakup semua tagihan belum bayar agar total list dan detail tetap sinkron. Silakan klik Pilih Semua.",
        "warning",
      );
      $(".billing-check").prop("checked", true);
      recalc();
      return;
    }
    updatePaymentRows();
    const subtotal = selectedItems.reduce(
      (sum, it) => sum + parseNumber(it.harga_total),
      0,
    );
    const diskon = calcDiskon(subtotal);
    const harusBayar = Math.max(subtotal - diskon, 0);
    const totalBayar = paymentRows.reduce(
      (sum, p) => sum + parseNumber(p.amount),
      0,
    );
    if (totalBayar < harusBayar) {
      Swal.fire(
        "Pembayaran Kurang",
        "Nominal pembayaran belum mencukupi total tagihan.",
        "warning",
      );
      return;
    }
    Swal.fire({
      title: "Proses pembayaran?",
      html: `<div class="text-start"><p>Pasien: <b>${escapeHtml(selectedPatient.nama || "-")}</b></p><p>Total tagihan: <b>${formatRupiah(harusBayar)}</b></p><p>Total bayar: <b>${formatRupiah(totalBayar)}</b></p></div>`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Proses",
      cancelButtonText: "Batal",
    }).then(function (result) {
      if (!result.isConfirmed) return;
      const payload = {
        episode_id: selectedPatient.episode_id,
        pasien_id: selectedPatient.pasien_id,
        dibayar_oleh: $("#dibayarOleh").val(),
        discount_type: $("#discountType").val(),
        discount_value: parseNumber($("#discountValue").val()),
        items: selectedItems.map((it) => ({ source_id: it.source_id })),
        payments: paymentRows.filter((p) => parseNumber(p.amount) > 0),
      };
      $.ajax({
        url: CFG.ajaxProsesBayarUrl,
        method: "POST",
        dataType: "json",
        data: csrfData({ payload: JSON.stringify(payload) }),
        beforeSend: function () {
          Swal.fire({
            title: "Memproses...",
            text: "Mohon tunggu, pembayaran sedang disimpan.",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
          });
        },
        success: function (res) {
          updateCsrf(res);
          if (!res.success) {
            Swal.fire(
              "Gagal",
              res.message || "Pembayaran gagal diproses.",
              "error",
            );
            return;
          }
          Swal.fire({
            title: "Pembayaran Berhasil",
            html: `<div class="text-start"><p>Bayar ID: <b>${escapeHtml(res.bayar_id || "-")}</b></p><p>Total: <b>${formatRupiah(res.total_harga || 0)}</b></p><p>Kembali: <b>${formatRupiah(res.total_kembali || 0)}</b></p></div>`,
            icon: "success",
            showCancelButton: true,
            confirmButtonText: "Cetak Kwitansi",
            cancelButtonText: "Tutup",
          }).then(function (r) {
            if (r.isConfirmed && res.bayar_id)
              window.open(
                CFG.printKwitansiUrl +
                  "?bayar_id=" +
                  encodeURIComponent(res.bayar_id),
                "_blank",
              );
            refreshWorklist();
            clearBilling();
          });
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          Swal.fire(
            "Error",
            "Terjadi kesalahan server saat proses pembayaran.",
            "error",
          );
        },
      });
    });
  }

  function clearBilling() {
    selectedPatient = null;
    $("#selectedAvatar").text("K");
    $("#selectedNama").text("Pilih Pasien");
    $("#selectedRm").text("RM -");
    $("#selectedPoli").text("Poli -");
    $("#selectedDokter").text("Dokter -");
    $("#selectedInvoice").text("-");
    $("#selectedTanggal").text("-");
    $("#selectedStatus").text("BELUM BAYAR");
    $("#billingBody").html(
      `<tr><td colspan="6" class="text-center text-muted py-5">Pilih pasien dari worklist.</td></tr>`,
    );
    $("#paymentList").html(
      `<div class="kasir-empty">Pilih pasien terlebih dahulu.</div>`,
    );
    paymentRows = [];
    setSummary(0, 0, 0, 0, 0, 0, 0, 0, 0);
  }

  function getKategoriBadge(type) {
    if (type === "PENUNJANG")
      return `<span class="badge bg-info">Penunjang</span>`;
    if (type === "TINDAKAN")
      return `<span class="badge bg-warning text-dark">Tindakan</span>`;
    if (type === "OBAT") return `<span class="badge bg-success">Obat</span>`;
    return `<span class="badge bg-secondary">${escapeHtml(type || "-")}</span>`;
  }
  function parseNumber(v) {
    if (typeof v === "number") return v;
    v = String(v || "0")
      .replace(/\./g, "")
      .replace(",", ".");
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
  }
  function formatRupiah(v) {
    return "Rp " + Math.round(parseNumber(v)).toLocaleString("id-ID");
  }
  function formatQty(v) {
    return parseNumber(v).toLocaleString("id-ID");
  }
  function getInitials(name) {
    return String(name || "P")
      .split(/\s+/)
      .filter(Boolean)
      .slice(0, 2)
      .map((w) => w.charAt(0).toUpperCase())
      .join("");
  }
  function escapeHtml(s) {
    return String(s ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
  function escapeAttr(s) {
    return escapeHtml(s).replace(/`/g, "&#096;");
  }
  function debounce(fn, delay) {
    let timer;
    return function () {
      const args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        fn.apply(null, args);
      }, delay || 300);
    };
  }
});
