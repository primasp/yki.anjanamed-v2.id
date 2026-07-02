$(document).ready(function () {
  console.log("lab_pap_dokter.js loaded");

  let listData = [];
  let dokterTab = "waiting";
  let dokterSortField = "tanggal";
  let dokterSortDir = "desc";
  let currentWorklistId = null;
  let currentFormPayload = null;

  const worklistStatusMeta = {
    0: { text: "BARU", cls: "text-bg-primary" },
    1: { text: "SELESAI", cls: "text-bg-success" },
    2: { text: "DITUNDA", cls: "text-bg-info" },
    3: { text: "DIBATALKAN", cls: "text-bg-danger" },
    4: { text: "DIKERJAKAN", cls: "text-bg-warning" },
    9: { text: "MENUNGGU DOKTER", cls: "text-bg-secondary" },
  };

  // INIT
  loadListPapDokter();

  function loadListPapDokterxxxx() {
    // alert("123");
    const keyword = ($("#dokterSearch").val() || "").trim();

    $.ajax({
      url: BASE_URL + "LabDoctorController/pap-list",
      method: "POST",
      dataType: "json",
      data: { keyword },
      success: function (rows) {
        console.log(rows);
        listData = Array.isArray(rows) ? rows : [];
        renderListPapDokter();
      },
      error: function () {
        toast("Gagal memuat daftar PAP untuk dokter.", "danger");
      },
      complete: function () {
        $("#dokterLastUpdated").text(fmtDate(new Date()));
      },
    });
  }

  function loadListPapDokter() {
    const keyword = ($("#dokterSearch").val() || "").trim();

    $.ajax({
      url: BASE_URL + "LabDoctorController/pap-list",
      method: "POST",
      dataType: "json",
      data: {
        keyword: keyword,
        tab: dokterTab,
      },
      success: function (rows) {
        listData = Array.isArray(rows) ? rows : [];
        renderListPapDokter();
      },
      error: function () {
        toast("Gagal memuat daftar PAP untuk dokter.", "danger");
      },
      complete: function () {
        $("#dokterLastUpdated").text(fmtDate(new Date()));
      },
    });
  }

  function sortDokterRows(rows) {
    rows.sort((a, b) => {
      let A = a[dokterSortField] ?? "";
      let B = b[dokterSortField] ?? "";

      A = String(A).toLowerCase();
      B = String(B).toLowerCase();

      if (dokterSortDir === "asc") {
        return A.localeCompare(B);
      }

      return B.localeCompare(A);
    });

    return rows;
  }

  function renderListPapDokterxx() {
    // alert("11");
    // const sorted = sortDokterRows(filtered);
    const q = ($("#dokterSearch").val() || "").trim().toLowerCase();

    const filtered = !q
      ? listData
      : listData.filter((row) => {
          const hay = [
            row.no_sitologi,
            row.nama_pasien,
            row.no_rm,
            row.result_id,
          ]
            .join(" ")
            .toLowerCase();
          return hay.includes(q);
        });

    let html = "";
    if (!filtered.length) {
      html =
        '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data PAP untuk dokter.</td></tr>';
    } else {
      html = filtered
        .map((r) => {
          const tgl = fmtDateOnly(r.tanggal || r.tgl_barcode);

          const sampel = r.sampel_id
            ? `<span class="mono">${esc(r.no_sitologi)}</span>`
            : '<span class="text-muted">-</span>';

          const namapas = r.nama_pasien
            ? `<span class="mono">${esc(r.nama_pasien)}</span>`
            : '<span class="text-muted">-</span>';
          const no_rm = r.no_rm
            ? `<span class="mono">${esc(r.no_rm)}</span>`
            : '<span class="text-muted">-</span>';

          // const epPasien = `
          //   <div class="small">
          //     Nama Pasien : <span class="mono">${esc(r.nama_pasien || "-")}</span>
          //   </div>
          //   <div class="small">
          //     No. RM : <span class="mono">${esc(r.no_rm || "-")}</span>
          //   </div>
          // `;

          const epPasien = `
              <div class="small">
                Nama Pasien :
                <span class="fw-bold text-dark" style="font-size:15px;">
                  ${esc(r.nama_pasien || "-")}
                </span>
              </div>
              <div class="small">
                No. RM :
                <span class="fw-bold text-primary" style="font-size:15px;">
                  ${esc(r.no_rm || "-")}
                </span>
              </div>
            `;

          const isFinal = String(r.status_dokter || "").toUpperCase() === "A";
          const btnText = isFinal ? "Lihat / Revisi" : "Buka Form";

          let actionBtns = `
  <button class="btn btn-sm btn-primary js-open-pap" data-id="${idStr}">
    <i class="fa fa-file-pen me-1"></i> ${btnText}
  </button>
`;

          if (isFinal) {
            actionBtns += `
    <button class="btn btn-sm btn-success js-print-pap-dokter ms-1" data-id="${idStr}">
      <i class="fa fa-print me-1"></i> Cetak PDF
    </button>
  `;
          }

          // const btnText =
          //   String(r.status_dokter || "") === "A"
          //     ? "Lihat / Revisi"
          //     : "Buka Form";

          const workStatus = badgeWorklistStatus(r.status);
          const docStatus = badgeDokterStatus(r.status_dokter);

          // const idStr = esc(r.identity || r.id || r.worklist_id || "");
          // const idStr = esc(
          //   r.episode_id || r.pasien_id || r.trans_id || r.trans_id || "",
          // );

          const idStr = esc(
            `${r.episode_id ?? ""}|${r.pasien_id ?? ""}|${r.trans_id ?? ""}|${r.trans_co ?? ""}`,
          );
          // co(r.idStr);

          return `
            <tr>
              <td class="ps-3">${esc(tgl)}</td>
              <td>${sampel}</td>
              <td>${namapas} - ${no_rm}</td>
              <td>${epPasien}</td>
              <td>${workStatus}</td>
              <td>${docStatus}</td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-primary js-open-pap" data-id="${idStr}">
                  <i class="fa fa-file-pen me-1"></i> ${btnText}
                </button>




              </td>
              <td class="text-end pe-3">${actionBtns}</td>
            </tr>
          `;
        })
        .join("");
    }

    $("#tblPapDokter tbody").html(html);
    $("#dokterTableInfo").text(filtered.length + " data");
  }

  function renderListPapDokter() {
    const q = ($("#dokterSearch").val() || "").trim().toLowerCase();

    const filtered = !q
      ? listData
      : listData.filter((row) => {
          const hay = [
            row.no_sitologi,
            row.nama_pasien,
            row.no_rm,
            row.result_id,
            row.episode_id,
            row.pasien_id,
            row.sampel_id,
          ]
            .join(" ")
            .toLowerCase();

          return hay.includes(q);
        });

    const sorted = sortDokterRows(filtered.slice());

    let html = "";

    if (!sorted.length) {
      html = `
      <tr>
        <td colspan="7" class="text-center text-muted py-4">
          Tidak ada data PAP untuk dokter.
        </td>
      </tr>
    `;
    } else {
      html = sorted
        .map((r) => {
          const tgl = fmtDateOnly(r.tanggal || r.tgl_barcode || r.created_date);

          const idStr = esc(
            r.identity ||
              r.id ||
              `${r.episode_id ?? ""}|${r.pasien_id ?? ""}|${r.trans_id ?? ""}|${r.trans_co ?? ""}`,
          );

          const sampel = r.no_sitologi
            ? `<span class="mono">${esc(r.no_sitologi)}</span>`
            : `<span class="text-muted">belum dibuat</span>`;

          const namaPasien = r.nama_pasien
            ? `<span class="mono">${esc(r.nama_pasien)}</span>`
            : `<span class="text-muted">-</span>`;

          const noRm = r.no_rm
            ? `<span class="mono">${esc(r.no_rm)}</span>`
            : `<span class="text-muted">-</span>`;

          const epPasien = `
          <div class="small">
            Nama Pasien :
            <span class="fw-bold text-dark" style="font-size:15px;">
              ${esc(r.nama_pasien || "-")}
            </span>
          </div>
          <div class="small">
            No. RM :
            <span class="fw-bold text-primary" style="font-size:15px;">
              ${esc(r.no_rm || "-")}
            </span>
          </div>
          <div class="small text-muted">
            Episode :
            <span class="mono">${esc(r.episode_id || "-")}</span>
          </div>
        `;

          const isFinal = String(r.status_dokter || "").toUpperCase() === "A";
          const btnText = isFinal ? "Lihat / Revisi" : "Buka Form";

          let actionBtns = `
          <button class="btn btn-sm btn-primary js-open-pap" data-id="${idStr}">
            <i class="fa fa-file-pen me-1"></i> ${btnText}
          </button>
        `;

          if (isFinal) {
            actionBtns += `
            <button class="btn btn-sm btn-success js-print-pap-dokter ms-1" data-id="${idStr}">
              <i class="fa fa-print me-1"></i> Cetak PDF
            </button>
          `;
          }

          const workStatus = badgeWorklistStatus(r.status);
          const docStatus = badgeDokterStatus(r.status_dokter);

          return `
          <tr>
            <td class="ps-3">${esc(tgl)}</td>
            <td>${sampel}</td>
            <td>${namaPasien} - ${noRm}</td>
            <td>${epPasien}</td>
            <td>${workStatus}</td>
            <td>${docStatus}</td>
            <td class="text-end pe-3">${actionBtns}</td>
          </tr>
        `;
        })
        .join("");
    }

    $("#tblPapDokter tbody").html(html);
    $("#dokterTableInfo").text(sorted.length + " data");
  }

  // Klik buka form di tabel
  $(document).on("click", ".js-open-pap", function () {
    const id = $(this).data("id");
    openPapForWorklist(id);
  });

  $(document).on("click", ".js-dokter-tab", function () {
    dokterTab = String($(this).data("tab") || "waiting");

    $(".js-dokter-tab").removeClass("active");
    $(this).addClass("active");

    loadListPapDokter();
  });

  $(document).on("click", ".dokter-sortable", function () {
    const field = $(this).data("sort");

    if (dokterSortField === field) {
      dokterSortDir = dokterSortDir === "asc" ? "desc" : "asc";
    } else {
      dokterSortField = field;
      dokterSortDir = "asc";
    }

    renderListPapDokter();
  });

  $(document).on("click", ".js-print-pap-dokter", function () {
    const id = $(this).data("id");
    const url =
      BASE_URL + "LabDoctorController/pap-print?id=" + encodeURIComponent(id);
    window.open(
      url,
      "papPrintDokter",
      "width=900,height=900,scrollbars=yes,resizable=yes",
    );
  });

  // ==========================
  // LOAD FORM PAP (AJAX)
  // ==========================
  function openPapForWorklist(worklistId) {
    if (!worklistId) {
      toast("worklist_id kosong.", "danger");
      return;
    }

    currentWorklistId = worklistId;
    currentFormPayload = null;

    $("#papDokterBody").html("Memuat form PAP...");
    $("#papDokterStatusInfo").text("");
    $("#papDokterSubtitle").text("-");

    const modal = new bootstrap.Modal(
      document.getElementById("modalPapDokter"),
    );

    modal.show();

    $.ajax({
      url: BASE_URL + "LabDoctorController/pap-form-data",
      method: "POST",
      dataType: "json",
      data: { worklist_id: worklistId },
      success: function (res) {
        if (!res || !res.success) {
          $("#papDokterBody").html(
            `<div class="alert alert-danger">${esc(
              res?.message || "Gagal memuat form PAP.",
            )}</div>`,
          );

          return;
        }
        currentFormPayload = res;
        renderPapForm("#papDokterBody", res);
      },
      error: function () {
        $("#papDokterBody").html(
          '<div class="alert alert-danger">Gagal memuat form PAP (AJAX error).</div>',
        );
      },
    });
  }

  function renderPapForm(container, payload) {
    const $container = $(container);
    $container.empty();

    if (!payload || !payload.sections) {
      $container.html(
        '<div class="alert alert-danger">Struktur form tidak ditemukan.</div>',
      );
      return;
    }

    const values = payload.values || {};
    const result = payload.result || {};
    const w = payload.worklist || {};

    console.log("Value= " + values);
    // // Subtitle & status info
    // $("#papDokterSubtitle").html(
    //   `Episode: <span class="mono">${esc(
    //     w.episode_id || "-",
    //   )}<br></span>No. Sitologi: <span class="mono font-weight">${esc(
    //     w.no_sitologi || "-",
    //   )}</span>`,
    // );

    $("#papDokterSubtitle").html(
      `Episode: <span class="mono">${esc(w.episode_id || "-")}</span><br>
   No. Sitologi: 
   <span class="mono fw-bold text-danger" style="font-size:16px;">
     ${esc(w.no_sitologi || "-")}
   </span>`,
    );

    const statusAdmin = result.status_admin || "D";
    const statusDokter = result.status_dokter || "";
    const statusText = `Analis: <b>${
      statusAdmin === "F" ? "Final" : "Draft"
    }</b> &bull; Dokter: <b>${
      statusDokter === "A" ? "ACC" : "Draft / Belum ACC"
    }</b>`;

    $("#papDokterStatusInfo").html(statusText);

    // Apakah form harus read-only?
    const isLockedByDokter = statusDokter === "A";

    let html = "";
    // $("#papCreatedLast").text("-");
    $("#papCreatedLast").text(w.last_updated_by || "-");
    html += `<form id="papDokterForm" class="small">`;
    // html += `<input type="text" name="last_update" value="${esc(
    //   w.last_updated_by || payload.last_updated_by || "",
    // )}">`;
    html += `<input type="hidden" name="worklist_id" value="${esc(
      w.id || payload.worklist_id || "",
    )}">`;

    payload.sections.forEach((sec) => {
      html += `
        <div class="border rounded-3 mb-3">
          <div class="bg-light border-bottom px-3 py-2 fw-semibold">
            ${esc(sec.section_label || sec.section_code || "")}
          </div>
          <div class="p-3">
      `;

      (sec.fields || []).forEach((f) => {
        const code = f.field_code;
        const type = (f.field_type || "text").toLowerCase();
        const label = f.field_label || code;

        let val = values[code];
        if (val === undefined || val === null) {
          val = f.default_value || "";
        }

        const name = `fields[${code}]`;

        if (type === "checkbox") {
          const checked = String(val) === "Y";
          html += `
            <div class="form-check mb-1">
              <input class="form-check-input" type="checkbox"
                     name="${esc(name)}"
                     value="Y"
                     id="fld_${esc(code)}"
                     ${checked ? "checked" : ""}>
              <label class="form-check-label" for="fld_${esc(code)}">
                ${esc(label)}
              </label>
            </div>
          `;
        } else if (type === "textarea") {
          html += `
            <div class="mb-2">
              <label class="form-label fw-semibold small" for="fld_${esc(
                code,
              )}">${esc(label)}</label>
              <textarea class="form-control form-control-sm"
                        rows="2"
                        name="${esc(name)}"
                        id="fld_${esc(code)}">${esc(val)}</textarea>
            </div>
          `;
        } else if (type === "select") {
          const options = Array.isArray(f.options) ? f.options : [];
          html += `
            <div class="mb-2">
              <label class="form-label fw-semibold small" for="fld_${esc(
                code,
              )}">${esc(label)}</label>
              <select class="form-select form-select-sm"
                      name="${esc(name)}"
                      id="fld_${esc(code)}">
                <option value="">-- pilih --</option>
          `;
          options.forEach((opt) => {
            const v = typeof opt === "string" ? opt : opt.value;
            const text = typeof opt === "string" ? opt : opt.label;
            const sel = String(val) === String(v) ? "selected" : "";
            html += `<option value="${esc(v)}" ${sel}>${esc(text)}</option>`;
          });
          html += `</select></div>`;
        } else if (type === "date") {
          const vStr = val ? toInputDate(val) : "";
          html += `
            <div class="mb-2">
              <label class="form-label fw-semibold small" for="fld_${esc(
                code,
              )}">${esc(label)}</label>
              <input type="date"
                     class="form-control form-control-sm"
                     name="${esc(name)}"
                     id="fld_${esc(code)}"
                     value="${esc(vStr)}">
            </div>
          `;
        } else {
          // default text
          html += `
            <div class="mb-2">
              <label class="form-label fw-semibold small" for="fld_${esc(
                code,
              )}">${esc(label)}</label>
              <input type="text"
                     class="form-control form-control-sm"
                     name="${esc(name)}"
                     id="fld_${esc(code)}"
                     value="${esc(val)}">
            </div>
          `;
        }
      });

      html += `</div></div>`;
    });

    html += `</form>`;

    $container.html(html);

    // Kalau sudah ACC dokter -> read-only & sembunyikan tombol submit
    if (isLockedByDokter) {
      $("#papDokterForm")
        .find("input, textarea, select")
        .prop("disabled", true);
      $("#btnPapDokterSaveDraft").prop("disabled", true).hide();
      $("#btnPapDokterSubmit").prop("disabled", true).hide();
      $("#papDokterStatusInfo").append(
        ' &nbsp; <span class="text-danger">Form telah di-ACC dokter (read-only).</span>',
      );
    } else {
      $("#papDokterForm")
        .find("input, textarea, select")
        .prop("disabled", false);
      $("#btnPapDokterSaveDraft").prop("disabled", false).show();
      $("#btnPapDokterSubmit").prop("disabled", false).show();
    }
  }

  // Tombol simpan draft / ACC
  $("#btnPapDokterSaveDraft").on("click", function () {
    // alert("sasasasa");
    submitPapDokter("draft");
  });

  $("#btnRefreshDokter").on("click", function () {
    loadListPapDokter();
  });

  $("#dokterSearch").on("input", function () {
    renderListPapDokter();
  });

  $("#dokterClearSearch").on("click", function () {
    $("#dokterSearch").val("");
    renderListPapDokter();
  });

  $("#btnPapDokterSubmit").on("click", function () {
    submitPapDokter("submit");
  });

  function submitPapDokter(mode) {
    // alert(currentWorklistId);
    if (!currentWorklistId) {
      toast("Worklist belum dipilih.", "danger");
      return;
    }

    const $form = $("#papDokterForm");
    // alert($form.length);
    if (!$form.length) {
      toast("Form PAP belum siap.", "danger");
      return;
    }

    if (mode === "submit") {
      if (
        !confirm(
          "ACC & kunci hasil PAP?\nSetelah ACC, form terkunci dan status pemeriksaan menjadi SELESAI.",
        )
      ) {
        return;
      }
    }

    const payloadValues = $form.serializeArray();

    console.log(payloadValues);

    $.ajax({
      url: BASE_URL + "LabDoctorController/pap-form-save",
      method: "POST",
      dataType: "json",
      data: {
        worklist_id: currentWorklistId,
        mode: mode,
        values: payloadValues,
      },
      success: function (res) {
        // alert("oke");
        if (!res || !res.success) {
          toast(res?.message || "Gagal menyimpan form PAP dokter.", "danger");
          return;
        }

        toast(res.message || "Berhasil menyimpan.");

        // reload list + status form
        loadListPapDokter();
        if (mode === "submit") {
          // --- ACC & Kunci: kunci form + tutup modal ---
          // Kunci semua input di modal (kalau user masih lihat sekilas)

          $("#papDokterForm")
            .find("input, textarea, select")
            .prop("disabled", true);

          $("#btnPapDokterSaveDraft").prop("disabled", true).hide();
          $("#btnPapDokterSubmit").prop("disabled", true).hide();

          $("#papDokterStatusInfo").html(
            "Analis: <b>Final</b> &bull; Dokter: <b>ACC</b> " +
              "<span class='text-danger ms-2'>Form telah di-ACC dokter (read-only).</span>",
          );

          // Tutup modal via JS, supaya user tidak perlu klik "Tutup"
          const modalEl = document.getElementById("modalPapDokter");
          const modalInstance =
            bootstrap.Modal.getInstance(modalEl) ||
            new bootstrap.Modal(modalEl);
          modalInstance.hide();

          // reset state
          currentWorklistId = null;
        } else {
          // --- Mode DRAFT: form tetap terbuka, cukup update status info ---

          $("#papDokterStatusInfo").html(
            "Analis: <b>Final</b> &bull; Dokter: <b>Draft / Belum ACC</b> " +
              "<span class='text-muted ms-2'>Draft dokter berhasil disimpan.</span>",
          );

          // TIDAK perlu re-open modal (jangan panggil openPapForWorklist di sini)
          // Kalau mau segarkan nilai dari server, buat endpoint khusus untuk reload data,
          // tapi jangan panggil show() lagi.
        }

        // 2) Perlakuan beda untuk draft vs submit

        // load ulang form supaya status_dokter & locked ter-update
        // openPapForWorklist(currentWorklistId);
      },
      error: function () {
        alert("gagal");
        toast("Gagal menyimpan form PAP dokter (AJAX error).", "danger");
      },
    });
  }

  function toast(msg, type = "success") {
    const cls =
      type === "danger"
        ? "alert-danger"
        : type === "warning"
          ? "alert-warning"
          : "alert-success";
    const $box = $(`
      <div class="alert ${cls} shadow-sm position-fixed top-0 end-0 m-3"
           style="z-index:1060; min-width:320px;">
        ${esc(msg)}
      </div>
    `);
    $("body").append($box);
    setTimeout(() => $box.fadeOut(250, () => $box.remove()), 2200);
  }

  function fmtDateOnly(dt) {
    // alert(dt);
    if (!dt) return "-";
    const d = dt instanceof Date ? dt : new Date(dt);
    if (!isNaN(d.getTime())) {
      return (
        pad2(d.getDate()) + "/" + pad2(d.getMonth() + 1) + "/" + d.getFullYear()
      );
    }
    return String(dt);
  }

  // Konversi nilai tanggal apapun -> format HTML input[type=date] (yyyy-MM-dd)
  function toInputDate(value) {
    if (!value) return "";

    // Kalau Date object
    if (value instanceof Date && !isNaN(value.getTime())) {
      const y = value.getFullYear();
      const m = String(value.getMonth() + 1).padStart(2, "0");
      const d = String(value.getDate()).padStart(2, "0");
      return `${y}-${m}-${d}`;
    }

    const s = String(value).trim();

    // Sudah format yyyy-MM-dd
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
      return s;
    }

    // Format dengan pemisah / . -  : dd/mm/yyyy, dd.mm.yyyy, dd-mm-yyyy
    let m = s.match(/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/);
    if (m) {
      const d = m[1].padStart(2, "0");
      const mo = m[2].padStart(2, "0");
      const y = m[3];
      return `${y}-${mo}-${d}`;
    }

    // Format timestamp: 2026-02-10 01:38:00
    m = s.match(/^(\d{4}-\d{2}-\d{2})/);
    if (m) {
      return m[1];
    }

    // Kalau benar-benar tidak dikenali, kosong saja
    return "";
  }

  // function fmtDateOnly(dt) {
  //   // alert(dt);
  //   if (!dt) return "-";
  //   const d = dt instanceof Date ? dt : new Date(dt);
  //   if (!isNaN(d.getTime())) {
  //     return `${pad2(d.getDate())}/${pad2(
  //       d.getMonth() + 1,
  //     )}/${d.getFullYear()}`;
  //   }
  //   return String(dt);
  // }

  function pad2(n) {
    return String(n).padStart(2, "0");
  }

  function esc(s) {
    return String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function badgeWorklistStatus(code) {
    const meta = worklistStatusMeta[String(code)] || {
      text: code,
      cls: "text-bg-light",
    };
    return `<span class="badge ${meta.cls}">${esc(meta.text)}</span>`;
  }

  function badgeDokterStatus(code) {
    const c = String(code || "");
    if (c === "A")
      return '<span class="badge text-bg-success">ACC Dokter</span>';
    if (c === "D")
      return '<span class="badge text-bg-warning">Draft Dokter</span>';
    return '<span class="badge text-bg-light">Belum diisi</span>';
  }

  function fmtDate(dt) {
    if (!dt) return "-";
    const d = dt instanceof Date ? dt : new Date(dt);
    if (!isNaN(d.getTime())) {
      return (
        pad2(d.getDate()) +
        "/" +
        pad2(d.getMonth() + 1) +
        "/" +
        d.getFullYear() +
        " " +
        pad2(d.getHours()) +
        ":" +
        pad2(d.getMinutes())
      );
    }
    return String(dt);
  }
});
