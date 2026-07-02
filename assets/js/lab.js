$(document).ready(function () {
  console.log("Lab JS loaded (POST AJAX style)");

  // const BASE_URL = window.BASE_URL || "";

  let activeStatus = "0";
  let autoTimer = null;
  let lastData = [];
  let lastRowForPrint = null;
  let currentSortField = "created_date";
  let currentSortDir = "desc";

  let currentPage = 1;
  let currentLimit = 10;
  // let labelPayload = null; // hasil dari label_data

  const statusMeta = {
    0: { text: "BARU", cls: "text-bg-primary" },
    1: { text: "SELESAI", cls: "text-bg-success" },
    2: { text: "DITUNDA", cls: "text-bg-info" },
    3: { text: "DIBATALKAN", cls: "text-bg-danger" },
    4: { text: "DIKERJAKAN", cls: "text-bg-warning" },
    9: { text: "DOKTER BELUM SELESAI", cls: "text-bg-secondary" },
  };

  function esc(s) {
    return String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }
  function pad2(n) {
    return String(n).padStart(2, "0");
  }
  function fmtDate(dt) {
    if (!dt) return "-";
    const d = dt instanceof Date ? dt : new Date(dt);
    if (!isNaN(d.getTime())) {
      return `${pad2(d.getDate())}/${pad2(
        d.getMonth() + 1,
      )}/${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
    }
    return String(dt);
  }
  function badgeStatus(code) {
    const s = statusMeta[String(code)] || { text: code, cls: "text-bg-light" };
    return `<span class="badge ${s.cls}">${esc(s.text)}</span>`;
  }
  function citoBadge(row) {
    const y = String(row.cito_yn ?? row.cito ?? "").toUpperCase();
    return y === "Y"
      ? `<span class="badge text-bg-danger ms-2">CITO</span>`
      : "";
  }
  function identifyId(row) {
    return (
      row.id ||
      row.worklist_id ||
      row.pk ||
      `${row.episode_id || ""}|${row.pasien_id || ""}|${row.trans_id || ""}|${
        row.trans_co || ""
      }`
    );
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

  function setLoading(isLoading) {
    $("#btnRefresh").prop("disabled", !!isLoading);
  }

  // =========================
  // PAP FORM – helper builder
  // =========================

  function buildPapField(field, valuesMap, sectionCode) {
    const code = field.field_code || field.code || "";
    if (!code) return "";
    const label = esc(field.field_label || field.label || code);
    const type = String(field.field_type || field.type || "text").toLowerCase();
    const options = Array.isArray(field.options) ? field.options : [];
    const value = valuesMap[code] ?? field.value ?? field.default_value ?? "";

    const inputId = `pap_${code}`;

    // textarea
    if (type === "textarea") {
      return `
        <div class="mb-2">
          <label class="form-label small mb-1" for="${esc(inputId)}">${label}</label>
          <textarea class="form-control form-control-sm pap-field"
                    id="${esc(inputId)}"
                    data-field="${esc(code)}"
                    rows="2">${esc(value)}</textarea>
        </div>
      `;
    }

    // checkbox (boolean)
    // if (type === "checkbox") {
    //   const checked =
    //     String(value).toUpperCase() === "Y" ||
    //     String(value) === "1" ||
    //     value === true;
    //   return `
    //     <div class="form-check mb-1">
    //       <input class="form-check-input pap-field"
    //              type="checkbox"
    //              id="${esc(inputId)}"
    //              data-field="${esc(code)}"
    //              ${checked ? "checked" : ""}>
    //       <label class="form-check-label small" for="${esc(inputId)}">${label}</label>
    //     </div>
    //   `;
    // }

    // checkbox
    if (type === "checkboxxxxxx") {
      // jika ada options -> multiple checkbox
      if (options.length) {
        const html = options
          .map((opt, idx) => {
            const optVal = opt.value ?? opt.code ?? opt;
            const optLbl = esc(opt.label ?? opt.text ?? optVal);

            const checked = Array.isArray(value)
              ? value.includes(optVal)
              : String(value) === String(optVal);

            const cid = `${inputId}_${idx}`;

            return `
        <div class="form-check">
          <input class="form-check-input pap-field"
                 type="checkbox"
                 id="${esc(cid)}"
                 data-field="${esc(code)}"
                 value="${esc(optVal)}"
                 ${checked ? "checked" : ""}>
          <label class="form-check-label small" for="${esc(cid)}">
            ${optLbl}
          </label>
        </div>
        `;
          })
          .join("");

        return `
      <div class="mb-2">
        <div class="form-label small mb-1">${label}</div>
        ${html}
      </div>
    `;
      }

      // checkbox boolean (tanpa options)
      const checked =
        String(value).toUpperCase() === "Y" ||
        String(value) === "1" ||
        value === true;

      return `
    <div class="form-check mb-1">
      <input class="form-check-input pap-field"
             type="checkbox"
             id="${esc(inputId)}"
             data-field="${esc(code)}"
             ${checked ? "checked" : ""}>
      <label class="form-check-label small" for="${esc(inputId)}">${label}</label>
    </div>
  `;
    }

    if (type === "checkbox") {
      const checked =
        String(value).toUpperCase() === "Y" ||
        String(value) === "1" ||
        value === true;

      return `
      <div class="form-check mb-1">
        <input class="form-check-input pap-field pap-checkbox"
               type="checkbox"
               id="${esc(inputId)}"
               data-field="${esc(code)}"
               data-section="${esc(sectionCode)}"
               ${checked ? "checked" : ""}>

        <label class="form-check-label small" for="${esc(inputId)}">
          ${label}
        </label>
      </div>
    `;
    }

    // select
    if (type === "select" && options.length) {
      const optsHtml = options
        .map((opt) => {
          const optVal = opt.value ?? opt.code ?? opt;
          const optLbl = esc(opt.label ?? opt.text ?? optVal);
          const selected = String(optVal) === String(value) ? "selected" : "";
          return `<option value="${esc(optVal)}" ${selected}>${optLbl}</option>`;
        })
        .join("");
      return `
        <div class="mb-2">
          <label class="form-label small mb-1" for="${esc(inputId)}">${label}</label>
          <select class="form-select form-select-sm pap-field"
                  id="${esc(inputId)}"
                  data-field="${esc(code)}">
            <option value="">- Pilih -</option>
            ${optsHtml}
          </select>
        </div>
      `;
    }

    // radio
    if (type === "radio" && options.length) {
      const radios = options
        .map((opt, idx) => {
          const optVal = opt.value ?? opt.code ?? opt;
          const optLbl = esc(opt.label ?? opt.text ?? optVal);
          const checked = String(optVal) === String(value) ? "checked" : "";
          const rid = `${inputId}_${idx}`;
          return `
            <div class="form-check form-check-inline">
              <input class="form-check-input pap-field"
                     type="radio"
                     name="${esc(inputId)}"
                     id="${esc(rid)}"
                     data-field="${esc(code)}"
                     value="${esc(optVal)}"
                     ${checked}>
              <label class="form-check-label small" for="${esc(rid)}">${optLbl}</label>
            </div>
          `;
        })
        .join("");
      return `
        <div class="mb-2">
          <div class="form-label small mb-1">${label}</div>
          <div>${radios}</div>
        </div>
      `;
    }

    // default: text / number / date
    const inputType = type === "number" || type === "date" ? type : "text";

    return `
      <div class="mb-2">
        <label class="form-label small mb-1" for="${esc(inputId)}">${label}</label>
        <input type="${esc(inputType)}"
               class="form-control form-control-sm pap-field"
               id="${esc(inputId)}"
               data-field="${esc(code)}"
               value="${esc(value)}">
      </div>
    `;
  }

  function collectPapFormValuesxxxx() {
    const values = {};

    $("#papFormBody .pap-field").each(function () {
      const $el = $(this);
      const code = $el.data("field");
      if (!code) return;

      const tag = this.tagName.toLowerCase();
      const t = ($el.attr("type") || "").toLowerCase();

      if (t === "checkbox") {
        values[code] = $el.is(":checked") ? "Y" : "T";
      } else if (t === "radio") {
        if ($el.is(":checked")) {
          values[code] = $el.val();
        }
      } else {
        values[code] = $el.val();
      }

      // console.log(tag);
    });
    return values;
  }

  function collectPapFormValues() {
    const values = {};

    $("#papFormBody .pap-field").each(function () {
      const $el = $(this);
      const code = $el.data("field");
      if (!code) return;

      const type = ($el.attr("type") || "").toLowerCase();

      // ---------------------
      // CHECKBOX
      // ---------------------
      if (type === "checkbox") {
        const val = $el.val();

        // checkbox boolean (tidak punya value khusus)
        if (!val || val === "on") {
          values[code] = $el.is(":checked") ? "Y" : "T";
        }
        // checkbox multi-option
        else {
          if (!values[code]) values[code] = [];

          if ($el.is(":checked")) {
            values[code].push(val);
          }
        }
      }

      // ---------------------
      // RADIO
      // ---------------------
      else if (type === "radio") {
        if ($el.is(":checked")) {
          values[code] = $el.val();
        }
      }

      // ---------------------
      // TEXT / DATE / TEXTAREA
      // ---------------------
      else {
        values[code] = $el.val();
      }
    });

    return values;
  }

  function updatePapStatusInfo(result) {
    if (!result) {
      $("#papFormStatusText").text("");
      return;
    }

    const by = result.last_updated_by || result.created_by || "-";
    const t =
      result.last_updated_date || result.created_date || result.tanggal || "";
    let text = "";

    if (String(result.status_result) === "1") {
      text = `Sudah dikirim ke Dokter oleh ${by} pada ${fmtDate(t)}`;
    } else if (result.result_id) {
      text = `Draft terakhir disimpan oleh ${by} pada ${fmtDate(t)}`;
    }

    $("#papFormStatusText").text(text);
  }

  function openPapFormModalById(id) {
    const modalEl = document.getElementById("modalPapForm");
    if (!modalEl) {
      toast(
        "Modal form hasil (#modalPapForm) belum ditambahkan di view.",
        "danger",
      );
      return;
    }

    const modal = new bootstrap.Modal(modalEl);

    $("#papWorklistId").val(id);
    $("#papNamaPasien").text("-");
    $("#papNamaPasangan").val("-");
    $("#papAlamat").val("-");
    $("#papUmur").val("-");
    $("#papNoRm").text("-");
    $("#papEpisode").text("-");
    $("#papSampelId").text("-");
    $("#papTanggal").text("-");
    $("#papFormBody").html(
      `<div class="text-center text-muted py-3 small">Memuat form...</div>`,
    );
    $("#papFormStatusText").text("");
    modal.show();

    $.ajax({
      url: BASE_URL + "LabController/pap-form-data",
      method: "POST",
      dataType: "json",
      data: {
        worklist_id: id,
      },

      success: function (res) {
        if (!res || !res.success) {
          $("#papFormBody").html(`
        <div class="alert alert-danger small mb-0">
          ${esc(res?.message || "Gagal memuat form.")}
        </div>
      `);

          return;
        }

        const w = res.worklist || {};

        $("#papWorklistId").val(w.id || id);

        $("#papNamaPasien").text(w.nama_pasien || "-");

        $("#papNamaPasangan").val(w.nama_pasangan || "-");

        $("#papAlamat").val(w.alamat1 || "-");

        $("#papUmur").val(w.tgl_lahir || "-");

        $("#papNoRm").text(w.no_rm || w.pasien_id || "-");

        $("#papEpisode").text(w.episode_id || "-");

        $("#papSitologiNo").text(w.no_sitologi || "-");

        $("#papTanggal").text(fmtDateOnly(w.tanggal || w.created_date));

        $("#papSitologiTgl").val(w.tgl_sitologi || "-");

        // =========================
        // TANGGAL SKRINNING ADMIN
        // =========================

        if (w.tgl_skrinning_admin) {
          // format dari backend:
          // DD-MM-YYYY HH24:MI:SS

          const parts = w.tgl_skrinning_admin.split(" ");

          if (parts.length >= 2) {
            const datePart = parts[0];
            const timePart = parts[1];

            const d = datePart.split("-");

            if (d.length === 3) {
              const formatted = `${d[2]}-${d[1]}-${d[0]}T${timePart.substring(0, 5)}`;

              $("#papSkriningTgl").val(formatted);
            }
          }
        } else {
          // default datetime sekarang

          const now = new Date();

          const yyyy = now.getFullYear();

          const mm = String(now.getMonth() + 1).padStart(2, "0");

          const dd = String(now.getDate()).padStart(2, "0");

          const hh = String(now.getHours()).padStart(2, "0");

          const mi = String(now.getMinutes()).padStart(2, "0");

          // FORMAT datetime-local
          const nowValue = `${yyyy}-${mm}-${dd}T${hh}:${mi}`;

          $("#papSkriningTgl").val(nowValue);
        }

        // =========================
        // TANGGAL ACC DOKTER
        // =========================

        $("#papSkriningDokterTgl").val(w.tgl_skrinning_dokter || "-");

        // =========================
        // BUTTON PENGKAJIAN
        // =========================

        $("#btnLihatPengkajian")
          .attr("data-episode", w.episode_id || "")
          .attr("data-pasienid", w.pasien_id || "")
          .attr("data-poliid", w.poli_id || "");

        const sections = Array.isArray(res.sections) ? res.sections : [];

        const values = res.values || {};

        console.log(values);

        const html = buildPapSectionsHtml(sections, values);

        $("#papFormBody").html(html);

        updatePapStatusInfo(res.result || null);
      },

      error: function () {
        $("#papFormBody").html(`
      <div class="alert alert-danger small mb-0">
        Gagal memuat form (AJAX error).
      </div>
    `);
      },
    });
    // alert("123");
  }

  $(document).on("click", ".sortable", function () {
    const field = $(this).data("sort");

    if (currentSortField === field) {
      currentSortDir = currentSortDir === "asc" ? "desc" : "asc";
    } else {
      currentSortField = field;
      currentSortDir = "asc";
    }

    renderTable(lastData);
  });

  $(document).on("click", "#btnLihatPengkajian", function () {
    const episodeid = $(this).data("episode");
    const pasienid = $(this).data("pasienid");
    const poliid = $(this).data("poliid");

    if (!episodeid || !pasienid) {
      toast("Data episode atau pasien tidak tersedia.", "warning");
      return;
    }

    // const url = `${BASE_URL}AsessmentController/editAssesLab/${episodeid}/${pasienid}`;
    const url = `${BASE_URL}AsessmentController/viewAssesLab/${episodeid}/${pasienid}`;

    window.open(url, "_blank", "width=1200,height=800");
  });
  function buildPapSectionsHtmlx(sections, values) {
    // console.log(sections);
    if (!Array.isArray(sections) || !sections.length) {
      return `
        <div class="alert alert-warning small mb-0">
          Struktur form belum dikonfigurasikan di master form.
        </div>
      `;
    }

    return sections
      .map((sec) => {
        const sLabel = esc(sec.section_label || sec.label || "-");
        const sCode = sec.section_code || ""; // ⭐ ambil section_code

        const fields = Array.isArray(sec.fields) ? sec.fields : [];

        // const fieldsHtml = fields
        //   .map((f) => buildPapField(f, values || {}))
        //   .join("");
        const fieldsHtml = fields
          .map((f) => buildPapField(f, values || {}, sCode)) // ⭐ kirim ke field
          .join("");
        // console.log(fieldsHtml);
        return `
          <div class="mb-3">
            <div class="fw-semibold border-bottom pb-1 mb-2">
              ${sLabel}
            </div>
            <div class="row g-2 small">
              <div class="col-md-12"> 
                ${fieldsHtml}
              </div>
            </div>
          </div>
        `;
      })
      .join("");
  }

  function buildPapSectionsHtml(sections, values) {
    if (!Array.isArray(sections) || !sections.length) {
      return `
      <div class="alert alert-warning small mb-0">
        Struktur form belum dikonfigurasikan di master form.
      </div>
    `;
    }

    return sections
      .map((sec) => {
        const sLabel = esc(sec.section_label || "-");
        const sCode = sec.section_code || "";

        const fieldsHtml = (sec.fields || [])
          .map((f) => buildPapField(f, values || {}, sCode))
          .join("");

        return `
      <div class="mb-3 pap-section" data-section="${esc(sCode)}">

        <div class="fw-semibold border-bottom pb-1 mb-2">
          ${sLabel}
        </div>

        <div class="small">
          ${fieldsHtml}
        </div>

      </div>
    `;
      })
      .join("");
  }

  $("#pageLimit").on("change", function () {
    currentLimit = parseInt($(this).val()) || 25;

    currentPage = 1;

    renderTable(lastData);
  });

  $(document).on("change", ".pap-checkbox", function () {
    const section = String($(this).data("section")).toLowerCase();

    const singleSections = [
      "dr_pengirim",
      "interpretasi",
      "rincian",
      "anjuran",
    ];

    if (singleSections.includes(section)) {
      if ($(this).is(":checked")) {
        $(`.pap-checkbox[data-section]`)
          .filter(function () {
            return String($(this).data("section")).toLowerCase() === section;
          })
          .not(this)
          .prop("checked", false);
      }
    }
  });

  function savePapForm(mode) {
    // alert("123");
    const worklistId = $("#papWorklistId").val();
    if (!worklistId) {
      toast("Worklist tidak diketahui.", "danger");
      return;
    }
    const values = collectPapFormValues();

    // console.log(values);

    $.ajax({
      url: BASE_URL + "LabController/pap-form-save",
      method: "POST",
      dataType: "json",
      data: {
        worklist_id: worklistId,
        mode: mode, // 'draft' | 'submit'
        // values: values,
        values: JSON.stringify(values),
      },
      success: function (res) {
        if (!res || !res.success) {
          toast(res?.message || "Gagal menyimpan form.", "danger");
          return;
        }
        toast(res.message || "Form berhasil disimpan.");

        if (res.result) {
          updatePapStatusInfo(res.result);
        }

        // kalau submit, refresh worklist (status bisa berubah ke 9)
        if (mode === "submit") {
          loadWorklist();
        }
      },
      error: function () {
        toast("Gagal menyimpan form (AJAX error).", "danger");
      },
    });
    // console.log(mode);
  }

  // ==========
  // COUNT (POST)
  // ==========
  function loadCounts() {
    $.ajax({
      url: BASE_URL + "LabController/worklist-counts",
      method: "POST",
      dataType: "json",
      data: {}, // kalau nanti mau filter by tanggal/ruang, isi di sini
      success: function (c) {
        ["0", "1", "2", "3", "4", "9"].forEach((s) => {
          $("#count_" + s).text(c[s] ?? 0);
          $("#kpi_" + s).text(c[s] ?? 0);
        });
      },
      error: function () {
        // silent
      },
    });
  }

  // ==========
  // LIST (POST) = style Anda
  // ==========
  function loadWorklist() {
    // alert("121");
    setLoading(true);

    $.ajax({
      url: BASE_URL + "LabController/worklist-filterData",
      method: "POST",
      dataType: "json",
      data: {
        status: activeStatus,
        keyword: ($("#quickSearch").val() || "").trim(), // optional server-side
      },
      success: function (res) {
        renderTable(res || []);
        loadCounts();
      },
      error: function () {
        toast("Gagal memuat data worklist.", "danger");
      },
      complete: function () {
        setLoading(false);
      },
    });
  }

  function buildActions(row) {
    const id = identifyId(row);
    const st = String(row.status);

    // const canKerjakan = st === "0";
    const canKerjakan = st === "0" || st === "2";
    // const canCetak = !!row.sampel_id;
    // const canSelesai;
    // const canSelesai = st === "4" || st === "9";
    // const canSelesai = st === "";

    // PAP FORM: boleh diisi saat status DIKERJAKAN (4) dan tetap bisa lihat/edit saat 9
    const canCetakHasil = st === "1" && isFinalDokter(row);
    // const canPapForm = st === "4" || st === "9";
    const canPapForm = st === "4" || st === "9" || canCetakHasil;
    const canSelesai = false;

    return `
      <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
        <button class="btn btn-outline-secondary btn-sm js-act" data-act="detail" data-id="${esc(
          id,
        )}">
          <i class="fa fa-circle-info me-1"></i> Detail
        </button>

        <button class="btn btn-warning btn-sm js-act" data-act="kerjakan" data-id="${esc(
          id,
        )}" ${canKerjakan ? "" : "disabled"}>
          <i class="fa fa-play me-1"></i> Kerjakan
        </button>


        <button class="btn btn-outline-success btn-sm js-act"
                data-act="pap-form"
                data-id="${esc(id)}"
                ${canPapForm ? "" : "disabled"}>
          <i class="fa fa-clipboard-list me-1"></i> Hasil
        </button>


       
        
        <button class="btn btn-success btn-sm js-act"
        data-act="pap-print"
        data-id="${esc(id)}"
        ${canCetakHasil ? "" : "disabled"}>
          <i class="fa fa-print me-1"></i> Cetak Hasil
        </button>

        <div class="btn-group btn-group-sm" role="group">
          <button class="btn btn-outline-info js-act" data-act="status" data-id="${esc(
            id,
          )}" data-status="2">Tunda</button>
          <button class="btn btn-outline-danger js-act" data-act="status" data-id="${esc(
            id,
          )}" data-status="3">Batal</button>
        </div>

        <button class="btn btn-success btn-sm js-act" data-act="status" data-id="${esc(
          id,
        )}" data-status="1" ${canSelesai ? "" : "disabled"}>
          <i class="fa fa-check me-1"></i> Selesai
        </button>
      </div>
    `;
  }

  function isFinalDokter(row) {
    return (
      String(row.status_admin || "").toUpperCase() === "F" &&
      String(row.status_dokter || "").toUpperCase() === "A"
    );
  }

  function renderTable(data) {
    lastData = Array.isArray(data) ? data : [];
    // console.log("sss" + data);
    // console.log("sss " + JSON.stringify(data));

    // client-side search tetap jalan (kalau server belum pakai keyword)
    const q = ($("#quickSearch").val() || "").trim().toLowerCase();
    const filtered = !q
      ? lastData
      : lastData.filter((r) => {
          const hay = [
            r.no_sitologi,
            r.sampel_id,
            r.nama_pasien,
            r.no_rm,
            r.episode_id,
            r.pasien_id,
            r.trans_id,
            r.trans_co,
            r.nama_poli,
            r.nama_dokter,
            r.rekanan_nama,
            r.rekanan_id,
          ]
            .join(" ")
            .toLowerCase();
          return hay.includes(q);
        });

    filtered.sort((a, b) => {
      let valA = a[currentSortField] ?? "";
      let valB = b[currentSortField] ?? "";

      valA = String(valA).toLowerCase();
      valB = String(valB).toLowerCase();

      if (currentSortDir === "asc") {
        return valA.localeCompare(valB);
      } else {
        return valB.localeCompare(valA);
      }
    });

    // ======================
    // PAGINATION
    // ======================

    const totalData = filtered.length;

    const start = (currentPage - 1) * currentLimit;

    const end = start + currentLimit;

    const paginated =
      currentLimit >= 999999 ? filtered : filtered.slice(start, end);

    let html = "";
    if (!filtered.length) {
      html = `<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr>`;
    } else {
      html = paginated
        .map((r) => {
          const waktu = fmtDate(r.created_date || r.tanggal);
          // const sampel = r.sampel_id
          //   ? `<span class="mono">${esc(r.sampel_id)}</span>`
          //   : `<span class="text-muted">belum dibuat</span>`;

          const sampel = r.no_sitologi
            ? `<span class="mono">${esc(r.no_sitologi)}</span>`
            : `<span class="text-muted">belum dibuat</span>`;

          const pasien = `
          <div class="fw-semibold">${esc(r.nama_pasien || "-")}</div>
          <div class="small text-muted">
            RM: <span class="mono">${esc(r.no_rm || "-")}</span>
            • Episode: <span class="mono">${esc(r.episode_id || "-")}</span>
          </div>
        `;

          const asal = `
          <div class="fw-semibold">${esc(r.nama_poli || "-")}</div>
          <div class="small text-muted">${esc(r.nama_dokter || "-")}</div>
        `;

          const rekanan = r.rekanan_nama || r.rekanan_id || "-";

          return `
          <tr>
            <td class="ps-3">${esc(waktu)}</td>
            <td>${sampel}${citoBadge(r)}</td>
            <td>${pasien}</td>
            <td>${asal}</td>
            <td>${esc(rekanan)}</td>
            <td>${badgeStatus(r.status)}</td>
            <td class="text-end pe-3">${buildActions(r)}</td>
          </tr>
        `;
        })
        .join("");
    }

    $("#tblWorklist tbody").html(html);
    $("#tableInfo").text(`${filtered.length} data`);

    const from = filtered.length === 0 ? 0 : start + 1;

    const to = Math.min(end, filtered.length);

    $("#tablePageInfo").text(
      `Menampilkan ${from}-${to} dari ${filtered.length}`,
    );
    $("#lastUpdated").text(fmtDate(new Date()));
  }

  function setActiveFilterUI(status) {
    $(".filter-status").removeClass("active");
    $(`.filter-status[data-status="${status}"]`).addClass("active");

    const textMap = {
      0: "Baru",
      4: "Dikerjakan",
      2: "Ditunda",
      3: "Dibatalkan",
      1: "Selesai",
      9: "Dokter belum selesai",
    };
    $("#activeFilterText").html(
      `Menampilkan: <b>${textMap[String(status)] || status}</b>`,
    );
  }

  // ==========
  // ACTIONS (POST)
  // ==========

  function actionKerjakan(id, no_sitologi) {
    $.ajax({
      url: BASE_URL + "LabController/worklist-kerjakan",
      method: "POST",
      dataType: "json",
      data: {
        id: id,
        no_sitologi: no_sitologi,
      },

      beforeSend: function () {
        Swal.fire({
          title: "Memproses...",
          text: "Sedang mengerjakan sampel",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });
      },

      success: function (out) {
        Swal.close();

        if (out && out.success) {
          toast(
            `✅ Berhasil<br>
           Sampel: <b>${out.sampel_id || "-"}</b><br>
           Sitologi: <b>${out.no_sitologi || no_sitologi}</b>`,
          );

          loadWorklist();
        } else {
          toast(out?.message || "Gagal memproses Kerjakan.", "danger");
        }
      },

      error: function (xhr) {
        Swal.close();

        console.error("ERROR:", xhr.responseText);

        toast("Terjadi kesalahan server.", "danger");
      },
    });
  }

  function actionUpdateStatus(id, status) {
    $.ajax({
      url: BASE_URL + "LabController/worklist-updateStatus",
      method: "POST",
      dataType: "json",
      data: { id: id, status: status },
      success: function (out) {
        if (out && out.success) {
          toast("Status berhasil diupdate.");
          loadWorklist();
        } else {
          toast(out?.message || "Gagal update status.", "danger");
        }
      },
      error: function () {
        toast("Gagal update status.", "danger");
      },
    });
  }

  function actionDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById("modalDetail"));
    $("#detailBody").html("Memuat...");
    modal.show();

    $.ajax({
      url: BASE_URL + "LabController/worklist-detail",
      method: "POST",
      dataType: "json",
      data: { id: id },
      success: function (d) {
        if (!d || !d.success) {
          $("#detailBody").html(
            `<div class="alert alert-danger">${esc(
              d?.message || "Gagal memuat detail.",
            )}</div>`,
          );
          return;
        }

        const tests = Array.isArray(d.tests) ? d.tests : [];
        const html = `
          <div class="row g-2">
            <div class="col-md-6">
              <div class="border rounded p-2">
                <div class="fw-semibold mb-1">Identitas</div>
                <div>Pasien: <b>${esc(d.nama_pasien || "-")}</b></div>
                <div>RM: <span class="mono">${esc(d.no_rm || "-")}</span></div>
                <div>Episode: <span class="mono">${esc(
                  d.episode_id || "-",
                )}</span></div>
                <div>Sampel: <span class="mono">${esc(
                  d.sampel_id || "-",
                )}</span></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="border rounded p-2">
                <div class="fw-semibold mb-1">Asal & Status</div>
                <div>Poli: ${esc(d.nama_poli || "-")}</div>
                <div>Dokter: ${esc(d.nama_dokter || "-")}</div>
                <div>Status: ${badgeStatus(d.status)}</div>
                <div>Waktu: ${esc(fmtDate(d.created_date || d.tanggal))}</div>
              </div>
            </div>

            <div class="col-12">
              <div class="border rounded p-2">
                <div class="fw-semibold mb-2">Daftar Pemeriksaan (pc01_co_lab_dt)</div>
                <div class="table-responsive">
                  <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>test_id</th>
                        <th>Pemeriksaan</th>
                        <th>test_lab_id</th>
                        <th>cito</th>
                        <th>sampel_id</th>
                        <th>dikerjakan</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${
                        tests.length
                          ? tests
                              .map(
                                (t) => `
                              <tr>
                                <td>${esc(t.test_id || "-")}</td>
                                <td>${esc(t.nama_pemeriksaan || "-")}</td>
                                <td>${esc(t.test_lab_id || "-")}</td>
                              
                                <td>${esc(t.cito || "-")}</td>
                                <td><span class="mono">${esc(
                                  t.sampel_id || "-",
                                )}</span></td>
                                <td>${esc(t.dikerjakan || "-")}</td>
                              </tr>
                            `,
                              )
                              .join("")
                          : `<tr><td colspan="5" class="text-center text-muted">Tidak ada detail pemeriksaan</td></tr>`
                      }
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        `;
        $("#detailBody").html(html);
      },
      error: function () {
        $("#detailBody").html(
          `<div class="alert alert-danger">Gagal memuat detail.</div>`,
        );
      },
    });
  }

  function actionOpenLabelx(id) {
    const row = lastData.find((x) => String(identifyId(x)) === String(id));
    if (!row) return toast("Data label tidak ditemukan.", "danger");
    if (!row.sampel_id) return toast("Sampel ID belum dibuat.", "warning");

    lastRowForPrint = row;

    $("#lblSample").text(row.sampel_id || "-");
    $("#lblPasien").text(row.nama_pasien || "-");
    $("#lblRmEpisode").text(`${row.no_rm || "-"} / ${row.episode_id || "-"}`);
    $("#lblTgl").text(fmtDate(row.created_date || row.tanggal));

    new bootstrap.Modal(document.getElementById("modalLabel")).show();
  }

  function actionOpenLabel(id) {
    // ambil data label + QR dari server (Endroid)
    $.ajax({
      url: BASE_URL + "LabController/label_data",
      method: "POST",
      dataType: "json",
      data: { id: id },
      success: function (out) {
        if (!out || !out.success) {
          toast(out?.message || "Gagal mengambil data label.", "danger");
          return;
        }

        lastRowForPrint = {
          sampel_id: out.sampel_id,
          no_rm: out.no_rm,
          nama_pemeriksaan: out.nama_pemeriksaan,
          qr: out.qr,
        };

        $("#lblCopies").val(1);

        $("#lblSample").text(out.sampel_id || "-");
        $("#lblNoRm").text(out.no_rm || "-");
        $("#lblTest").text(out.nama_pemeriksaan || "-");
        $("#lblQr").attr("src", out.qr);

        new bootstrap.Modal(document.getElementById("modalLabel")).show();
      },
      error: function () {
        toast("Gagal memuat data label.", "danger");
      },
    });
  }

  // =========================
  // EVENTS
  // =========================
  $(document).on("click", ".filter-status", function () {
    const field = $(this).data("sort");

    if (currentSortField === field) {
      currentSortDir = currentSortDir === "asc" ? "desc" : "asc";
    } else {
      currentSortField = field;
      currentSortDir = "asc";
    }

    renderTable(lastData);

    activeStatus = String($(this).data("status"));
    setActiveFilterUI(activeStatus);
    loadWorklist();
  });

  $("#quickSearch").on("input", function () {
    renderTable(lastData);
  });

  $("#btnClearSearch").on("click", function () {
    $("#quickSearch").val("");
    renderTable(lastData);
  });

  $("#btnRefresh").on("click", function () {
    loadWorklist();
  });

  $("#btnAutoRefresh").on("click", function () {
    const enabled = $(this).attr("data-enabled") === "1";

    if (enabled) {
      clearInterval(autoTimer);
      autoTimer = null;
      $(this)
        .attr("data-enabled", "0")
        .removeClass("btn-primary")
        .addClass("btn-outline-primary")
        .html(`<i class="fa fa-bolt me-1"></i> Auto`);
      toast("Auto refresh dimatikan.", "warning");
    } else {
      autoTimer = setInterval(() => loadWorklist(), 5000);
      $(this)
        .attr("data-enabled", "1")
        .removeClass("btn-outline-primary")
        .addClass("btn-primary")
        .html(`<i class="fa fa-bolt me-1"></i> Auto ON`);
      toast("Auto refresh aktif (5 detik).");
    }
  });

  let labelPayload = null; // hasil dari label_data

  function fmtDateOnly(dt) {
    // alert(dt);
    if (!dt) return "-";
    const d = dt instanceof Date ? dt : new Date(dt);
    if (!isNaN(d.getTime())) {
      return `${pad2(d.getDate())}/${pad2(
        d.getMonth() + 1,
      )}/${d.getFullYear()}`;
    }
    return String(dt);
  }

  function openLabelModalById(id) {
    const modal = new bootstrap.Modal(document.getElementById("modalLabel"));
    labelPayload = null;

    $("#lblSample").text("-");
    $("#lblNoRm").text("-");
    $("#lblTest").text("-");
    $("#lblAmbil").text("-");
    $("#lblQr").attr("src", "");
    $("#labelTestList").html(
      `<tr><td colspan="3" class="text-center text-muted py-3">Memuat...</td></tr>`,
    );

    modal.show();

    $.ajax({
      url: BASE_URL + "LabController/label-data",
      method: "POST",
      dataType: "json",
      data: { id },
      success: function (res) {
        if (!res || !res.success) {
          $("#labelTestList").html(
            `<tr><td colspan="3" class="text-center text-danger py-3">${esc(
              res?.message || "Gagal memuat label",
            )}</td></tr>`,
          );
          return;
        }

        labelPayload = res;

        $("#lblSample").text(res.sampel_id || "-");
        $("#lblNoRm").text(res.no_rm || "-");
        $("#lblAmbil").text(fmtDate(res.ambil));
        $("#lblQr").attr("src", res.qr || "");

        const tests = Array.isArray(res.tests) ? res.tests : [];
        if (!tests.length) {
          $("#lblTest").text("-");
          $("#labelTestList").html(
            `<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada pemeriksaan</td></tr>`,
          );
          return;
        }

        // preview pakai tes pertama
        $("#lblTest").text(
          tests[0].nama_pemeriksaan || tests[0].test_id || "-",
        );

        const rowsHtml = tests
          .map((t, idx) => {
            const nama = t.nama_pemeriksaan || t.test_id || "-";
            const key = `${t.test_id || idx}`;
            return `
            <tr data-testkey="${esc(key)}">
              <td>
                <div class="fw-semibold">${esc(nama)}</div>
                <div class="small text-muted">test_id: ${esc(
                  t.test_id || "-",
                )}</div>
              </td>
              <td>
                <input type="number"
                       class="form-control form-control-sm text-center js-test-copies"
                       min="0" max="50" value="1">
              </td>
              <td class="text-end">
                <button type="button"
                        class="btn btn-sm btn-outline-primary js-print-one"
                        data-testname="${esc(nama)}">
                  <i class="fa fa-print me-1"></i> Cetak
                </button>
              </td>
            </tr>
          `;
          })
          .join("");

        $("#labelTestList").html(rowsHtml);
      },
      error: function () {
        $("#labelTestList").html(
          `<tr><td colspan="3" class="text-center text-danger py-3">Gagal memuat label (AJAX error)</td></tr>`,
        );
      },
    });
  }

  function printLabels40x20Lab(payload, items) {
    // items: [{ testName, copies }]
    const sampelId = payload?.sampel_id || "-";
    const qr = payload?.qr || "";
    const noRm = payload?.no_rm || "-";
    const ambil = fmtDate(payload?.ambil);

    // build label pages
    let labels = "";
    items.forEach((it) => {
      const copies = Math.max(1, Math.min(50, parseInt(it.copies, 10) || 1));
      for (let i = 0; i < copies; i++) {
        labels += `
        <div class="label">
          <div class="qr"><img src="${qr}" alt="QR"></div>
          <div class="info">
            <div class="line sid">${esc(sampelId)}</div>
            <div class="line sub">RM: <span class="mono">${esc(
              noRm,
            )}</span></div>
            <div class="line test">Tes: ${esc(it.testName || "-")}</div>
            <div class="line sub">Ambil: ${esc(ambil)}</div>
          </div>
        </div>
      `;
      }
    });

    const html = `
  <html>
  <head>
    <meta charset="utf-8" />
    <title>Label ${esc(sampelId)}</title>
    <style>
      @page { size: 40mm 20mm; margin: 0; }
      html, body { margin: 0; padding: 0; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

      .label{
        width: 40mm;
        height: 20mm;
        box-sizing: border-box;
        padding: 1.1mm;
        display: grid;
        grid-template-columns: 18mm 1fr;
        column-gap: 1.0mm;
        align-items: center;
        overflow: hidden;
        page-break-after: always;
      }

      .qr{
        width: 18mm;
        height: 18mm;
        display:flex;
        align-items:center;
        justify-content:center;
      }
      .qr img{
        width: 18mm;
        height: 18mm;
        object-fit: contain;
        image-rendering: crisp-edges;
      }

      .info{ min-width:0; }
      .line{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.05;
      }

      .sid{
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
        font-weight: 800;
        font-size: 8.2pt;
      }
      .test{
        font-family: Arial, sans-serif;
        font-weight: 700;
        font-size: 6.8pt;
        margin-top: 0.6mm;
      }
      .sub{
        font-family: Arial, sans-serif;
        font-size: 6.2pt;
        margin-top: 0.4mm;
      }
      .mono{
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      }
    </style>
  </head>
  <body>
    ${labels}
    <script>window.onload=()=>{window.print();window.close();};</script>
  </body>
  </html>`;

    const w = window.open("", "_blank", "width=520,height=720");
    w.document.open();
    w.document.write(html);
    w.document.close();
  }

  function printLabel40x20Labxxx(row, copies = 1) {
    const safeCopies = Math.max(1, Math.min(50, parseInt(copies, 10) || 1));

    const labelsHtml = Array.from({ length: safeCopies })
      .map(() => {
        return `
        <div class="label">
          <div class="qr">
            <img src="${row.qr}" alt="QR">
          </div>
          <div class="info">
            <div class="sid">${esc(row.sampel_id || "-")}</div>
            <div class="rm">RM: <span class="mono">${esc(
              row.no_rm || "-",
            )}</span></div>
            <div class="test">${esc(row.nama_pemeriksaan || "-")}</div>
          </div>
        </div>
      `;
      })
      .join("");

    const html = `
  <html>
  <head>
    <title>Label ${esc(row.sampel_id || "")}</title>
    <meta charset="utf-8" />
    <style>
      @page { size: 40mm 20mm; margin: 0; }
      html, body { margin: 0; padding: 0; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

      .label{
        width: 40mm;
        height: 20mm;
        box-sizing: border-box;
        padding: 1.2mm;
        display: grid;
        grid-template-columns: 18mm 1fr;
        column-gap: 1.2mm;
        align-items: center;
        overflow: hidden;
        page-break-after: always;
      }
      .qr{
        width: 18mm;
        height: 18mm;
        display:flex;
        align-items:center;
        justify-content:center;
      }
      .qr img{
        width: 18mm;
        height: 18mm;
        object-fit: contain;
        image-rendering: crisp-edges;
      }
      .info{ min-width: 0; }
      .sid{
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
        font-weight: 800;
        font-size: 8.5pt;
        line-height: 1.05;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .rm{
        font-family: Arial, sans-serif;
        font-size: 6.6pt;
        line-height: 1.05;
        margin-top: 0.6mm;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .test{
        font-family: Arial, sans-serif;
        font-weight: 700;
        font-size: 7.0pt;
        line-height: 1.05;
        margin-top: 0.6mm;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .mono{
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      }
    </style>
  </head>
  <body>
    ${labelsHtml}
    <script>
      window.onload = () => { window.print(); window.close(); };
    </script>
  </body>
  </html>`;

    const w = window.open("", "_blank", "width=520,height=720");
    w.document.open();
    w.document.write(html);
    w.document.close();
  }

  $(document)
    .off("click", ".js-print-one")
    .on("click", ".js-print-one", function (e) {
      e.preventDefault();
      if (!labelPayload) return toast("Data label belum siap.", "danger");

      const $tr = $(this).closest("tr");
      const copies = Math.max(
        1,
        Math.min(50, parseInt($tr.find(".js-test-copies").val(), 10) || 1),
      );
      const testName = $(this).data("testname") || "-";

      printLabels40x20Lab(labelPayload, [{ testName, copies }]);
    });

  $(document)
    .off("click", "#btnPrintAll")
    .on("click", "#btnPrintAll", function (e) {
      e.preventDefault();
      if (!labelPayload) return toast("Data label belum siap.", "danger");

      const items = [];
      $("#labelTestList tr").each(function () {
        const testName = $(this).find(".js-print-one").data("testname") || "-";
        const copies = parseInt($(this).find(".js-test-copies").val(), 10) || 0;
        if (copies > 0) items.push({ testName, copies });
      });

      if (!items.length) return toast("Jumlah cetak semua 0.", "warning");
      printLabels40x20Lab(labelPayload, items);
    });

  $(document).on("click", ".js-act", function () {
    // alert("123");

    const act = $(this).data("act");
    // alert(act);
    const id = $(this).data("id");

    if (act === "detail") return actionDetail(id);

    if (act === "kerjakan") {
      Swal.fire({
        title: "Input No Sitologi",
        input: "text",
        inputLabel: "Nomor Sitologi",
        inputPlaceholder: "Masukkan nomor sitologi...",
        showCancelButton: true,
        confirmButtonText: "Simpan & Kerjakan",
        cancelButtonText: "Batal",
        inputValidator: (value) => {
          if (!value) {
            return "No Sitologi wajib diisi!";
          }
        },
      }).then((result) => {
        if (!result.isConfirmed) return;

        const no_sitologi = result.value;

        $(this).prop("disabled", true);

        // 🔥 kirim ke backend
        actionKerjakan(id, no_sitologi);

        setTimeout(() => $(this).prop("disabled", false), 1500);
      });

      return;

      // if (
      //   !confirm(
      //     "Kerjakan permintaan lab ini? (akan membuat Sampel ID & bisa cetak label)",
      //   )
      // )
      //   return;
      // $(this).prop("disabled", true);
      // actionKerjakan(id);
      // setTimeout(() => $(this).prop("disabled", false), 1500);
      // return;
    }

    // if (act === "label") return actionOpenLabel(id);
    if (act === "label") return openLabelModalById(id);

    if (act === "pap-form") return openPapFormModalById(id);

    if (act === "pap-print") {
      const url =
        BASE_URL + "LabController/pap-print?id=" + encodeURIComponent(id);
      window.open(
        url,
        "papPrintLab",
        "width=900,height=900,scrollbars=yes,resizable=yes",
      );
      return;
    }

    if (act === "status") {
      const st = String($(this).data("status"));
      const label = statusMeta[st]?.text || st;
      if (!confirm(`Ubah status menjadi "${label}" ?`)) return;
      return actionUpdateStatus(id, st);
    }
  });

  // Tombol simpan draft / kirim ke dokter di modal PAP
  $(document)
    .off("click", "#btnPapSaveDraft")
    .on("click", "#btnPapSaveDraft", function (e) {
      e.preventDefault();
      // alert("Save");
      savePapForm("draft");
    });

  $(document)
    .off("click", "#btnPapSendToDoctor")
    .on("click", "#btnPapSendToDoctor", function (e) {
      e.preventDefault();

      Swal.fire({
        title: "Kirim ke Dokter?",
        text: "Hasil akan dikirim ke Dokter Lab dan status berubah menjadi 'Menunggu Dokter'",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Kirim",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          savePapForm("submit");
        }
      });
    });
  $(document)
    .off("click", "#btnPrintLabel")
    .on("click", "#btnPrintLabel", function (e) {
      e.preventDefault();
      alert("Print!");

      if (!lastRowForPrint) return toast("Data label kosong.", "danger");

      const copies = Math.max(
        1,
        Math.min(50, parseInt($("#lblCopies").val(), 10) || 1),
      );

      printLabel40x20Lab(lastRowForPrint, copies);
    });

  // =========================
  // INIT
  // =========================
  setActiveFilterUI(activeStatus);
  loadWorklist();
  loadCounts();
});
