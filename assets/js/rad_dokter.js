$(document).ready(function () {
  console.log("Rad Dokter JS loaded");

  let radActiveStatus = ""; // default: menunggu dokter
  let radAutoTimer = null;
  let radLastData = [];
  let radCurrentId = null; // id worklist yang sedang dibuka detail
  let radDetailModal = null;
  let radSort = {
    key: "tanggal",
    dir: "desc",
  };

  const radStatusMeta = {
    0: { text: "BARU", cls: "text-bg-primary" },
    1: { text: "SELESAI", cls: "text-bg-success" },
    2: { text: "DITUNDA", cls: "text-bg-info" },
    3: { text: "DIBATALKAN", cls: "text-bg-danger" },
    4: { text: "SEDANG DIBACA", cls: "text-bg-warning" },
    9: { text: "MENUNGGU DOKTER", cls: "text-bg-secondary" },
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
    const s = radStatusMeta[String(code)] || {
      text: code,
      cls: "text-bg-light",
    };
    return `<span class="badge ${s.cls}">${esc(s.text)}</span>`;
  }

  function identifyId(row) {
    return (
      row.id ||
      `${row.episode_id || ""}|${row.pasien_id || ""}|${row.trans_id || ""}|${
        row.trans_co || ""
      }|${row.rad_ke || ""}`
    );
  }

  function toast(msg, type = "success") {
    const cls =
      type === "danger"
        ? "alert-danger"
        : type === "warning"
          ? "alert-warning"
          : "alert-success";
    const $box = $(
      `<div class="alert ${cls} shadow-sm position-fixed top-0 end-0 m-3"
           style="z-index:1060; min-width:320px;">
        ${esc(msg)}
      </div>`,
    );
    $("body").append($box);
    setTimeout(() => $box.fadeOut(250, () => $box.remove()), 2200);
  }

  function setLoadingRad(isLoading) {
    $("#radBtnRefresh").prop("disabled", !!isLoading);
  }

  // =======================
  // LOAD WORKLIST
  // =======================
  function loadRadWorklist() {
    setLoadingRad(true);

    $.ajax({
      url: BASE_URL + "Rad-Doctor/worklist-filterData",
      method: "POST",
      dataType: "json",
      data: {
        status: radActiveStatus,
        keyword: ($("#radSearch").val() || "").trim(),
      },
      success: function (rows) {
        rows = Array.isArray(rows) ? rows : [];
        radLastData = rows;
        radRenderTable(rows);
        radUpdateCounts(rows);
        $("#lastUpdatedRad").text(fmtDate(new Date()));
      },
      error: function () {
        toast("Gagal memuat data radiologi.", "danger");
      },
      complete: function () {
        setLoadingRad(false);
      },
    });
  }

  function radUpdateCounts(rows) {
    const cnt = { 1: 0, 4: 0, 9: 0, all: 0 };
    rows.forEach((r) => {
      const s = String(r.status);
      if (cnt[s] !== undefined) cnt[s]++;
      cnt.all++;
    });
    $("#rad_count_1").text(cnt["1"]);
    $("#rad_count_4").text(cnt["4"]);
    $("#rad_count_9").text(cnt["9"]);
    $("#rad_count_all").text(cnt.all);
    $("#radTableInfo").text(`${cnt.all} data`);
  }

  function getRadDateValue(r) {
    const raw = r.tgl_kirim || r.tanggal || r.created_date || "";
    const normalized = String(raw).replace(" ", "T");
    const time = new Date(normalized).getTime();
    return isNaN(time) ? 0 : time;
  }

  function getRadSortValue(r, key) {
    switch (key) {
      case "tanggal":
        return getRadDateValue(r);

      case "pasien":
        return String(r.nama_pasien || "").toLowerCase();

      case "asal":
        return String(r.nama_poli || "").toLowerCase();

      case "rekanan":
        return String(r.rekanan_nama || r.rekanan_id || "").toLowerCase();

      case "file":
        return parseInt(r.file_count || 0, 10);

      case "status":
        return String(r.status || "");

      default:
        return "";
    }
  }

  function radSortRows(rows) {
    return rows.slice().sort(function (a, b) {
      const va = getRadSortValue(a, radSort.key);
      const vb = getRadSortValue(b, radSort.key);

      if (typeof va === "number" && typeof vb === "number") {
        return radSort.dir === "asc" ? va - vb : vb - va;
      }

      const sa = String(va);
      const sb = String(vb);

      if (sa < sb) return radSort.dir === "asc" ? -1 : 1;
      if (sa > sb) return radSort.dir === "asc" ? 1 : -1;
      return 0;
    });
  }

  function radRenderSortIcon() {
    $("#tblRadDoctor thead th.rad-sortable").each(function () {
      const key = $(this).data("sort");
      const $icon = $(this).find(".rad-sort-icon");

      $icon.removeClass("fa-sort fa-sort-up fa-sort-down");

      if (key === radSort.key) {
        $icon.addClass(radSort.dir === "asc" ? "fa-sort-up" : "fa-sort-down");
      } else {
        $icon.addClass("fa-sort");
      }
    });
  }

  function radRenderTable(rows) {
    const q = ($("#radSearch").val() || "").trim().toLowerCase();

    const filtered = !q
      ? rows
      : rows.filter((r) => {
          const hay = [
            r.nama_pasien,
            r.no_rm,
            r.episode_id,
            r.trans_id,
            r.kesan_singkat,
            r.nama_poli,
            r.nama_dokter_pengirim,
          ]
            .join(" ")
            .toLowerCase();
          return hay.includes(q);
        });

    const sorted = radSortRows(filtered);

    let html = "";
    if (!sorted.length) {
      html = `<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr>`;
    } else {
      html = sorted
        .map((r) => {
          const waktu = fmtDate(r.tgl_kirim || r.tanggal || r.created_date);
          const pasien = `
            <div class="fw-semibold">${esc(r.nama_pasien || "-")}</div>
            <div class="small text-muted">
              RM: <span class="mono">${esc(r.no_rm || "-")}</span>
              • Episode: <span class="mono">${esc(r.episode_id || "-")}</span>
            </div>
          `;
          const asal = `
            <div class="fw-semibold">${esc(r.nama_poli || "-")}</div>
            <div class="small text-muted">${esc(
              r.nama_dokter_pengirim || "-",
            )}</div>
          `;
          const rekanan = esc(r.rekanan_nama || r.rekanan_id || "-");
          const fileInfo = `
            <div>${r.file_count || 0} file</div>
            <div class="small text-muted">${
              r.log_count ? `${r.log_count} log bacaan` : "Belum dibaca"
            }</div>
          `;

          const id = identifyId(r);

          return `
            <tr>
              <td class="ps-3">${esc(waktu)}</td>
              <td>${pasien}</td>
              <td>${asal}</td>
              <td>${rekanan}</td>
              <td>${fileInfo}</td>
              <td>${badgeStatus(r.status)}</td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-secondary rad-act"
                        data-act="detail" data-id="${esc(id)}">
                  <i class="fa fa-eye me-1"></i> Baca
                </button>
              </td>
            </tr>
          `;
        })
        .join("");
    }

    $("#tblRadDoctor tbody").html(html);

    radRenderSortIcon();
  }

  function setRadFilterUI(status) {
    $(".rad-filter").removeClass("active");
    $(`.rad-filter[data-status="${status}"]`).addClass("active");
    const textMap = {
      9: "Menunggu Dokter",
      4: "Sedang Dibaca",
      1: "Selesai",
      "": "Semua",
    };
    $("#radActiveFilterText").html(
      `Menampilkan: <b>${textMap[String(status)] || status}</b>`,
    );
  }

  // =======================
  // DETAIL + FORM EXPERTISE
  // =======================
  function radOpenDetail(id) {
    radCurrentId = id;
    const modal = new bootstrap.Modal(
      document.getElementById("modalRadDrDetail"),
    );

    if (!radDetailModal) {
      const el = document.getElementById("modalRadDrDetail");
      // pakai getOrCreateInstance supaya tidak dobel instance
      radDetailModal = bootstrap.Modal.getOrCreateInstance(el);

      // cleanup ketika modal benar2 tertutup
      $("#modalRadDrDetail").on("hidden.bs.modal", function () {
        radCurrentId = null;
        $("#radDetailBody").empty();

        // safety: kalau sampai ada backdrop nyangkut
        $("body").removeClass("modal-open");
        $(".modal-backdrop").remove();
      });
    }

    $("#radDetailBody").html("Memuat...");
    modal.show();

    $.ajax({
      url: BASE_URL + "RadDoctorController/worklist_detail",
      method: "POST",
      dataType: "json",
      data: { id: id },
      success: function (res) {
        if (!res || !res.success) {
          $("#radDetailBody").html(
            `<div class="alert alert-danger">${esc(
              res?.message || "Gagal memuat detail.",
            )}</div>`,
          );
          return;
        }
        radRenderDetail(res);
      },
      error: function () {
        $("#radDetailBody").html(
          `<div class="alert alert-danger">Gagal memuat detail.</div>`,
        );
      },
    });
  }

  function radRenderDetail(res) {
    const w = res.worklist || {};
    const files = Array.isArray(res.files) ? res.files : [];
    const last = res.last_expertise || {};
    const history = Array.isArray(res.history) ? res.history : [];

    const created = fmtDate(w.created_date || w.tanggal);
    const tglKirim = fmtDate(w.tgl_kirim);
    const tglDibaca = fmtDate(w.tgl_dibaca);
    const tglValid = fmtDate(w.tgl_validasi);

    // ================= FILE LIST =================
    const fileListHtml = files.length
      ? files
          .map((f) => {
            const isImg = /^image\//.test(f.mime_type || "");
            const isPdf = (f.mime_type || "").toLowerCase().includes("pdf");

            const url =
              BASE_URL.replace(/\/$/, "") + "/" + String(f.file_path || "");

            const ikon = isImg ? "fa-image" : isPdf ? "fa-file-pdf" : "fa-file";

            const sizeMb = f.file_size
              ? (f.file_size / 1024 / 1024).toFixed(2).replace(/\.00$/, "")
              : "0";

            const thumb = isImg
              ? `<img src="${esc(url)}"
                class="border rounded"
                style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                data-url="${esc(url)}">`
              : `<i class="fa ${ikon} fa-2x text-muted"></i>`;

            const latestBadge =
              String(f.is_latest || "0") === "1"
                ? '<span class="badge text-bg-primary ms-1">Terbaru</span>'
                : "";

            const lockedBadge =
              String(f.is_locked || "0") === "1"
                ? '<span class="badge text-bg-warning ms-1">LOCKED</span>'
                : "";

            return `
          <div class="d-flex align-items-start mb-2">

            <div class="me-2 rad-open-file-thumb" data-url="${esc(url)}">
              ${thumb}
            </div>

            <div class="flex-grow-1" style="min-width:0;">
              <div class="small fw-semibold">
                v${esc(f.versi || 1)} - ${esc(f.file_name || "")}
                ${latestBadge}
                ${lockedBadge}
              </div>

              <div class="small text-muted">
                ${esc(f.mime_type || "-")} • ${sizeMb} MB
              </div>

              ${
                f.catatan
                  ? `<div class="small text-danger mt-1">
                      <i class="fa fa-note-sticky me-1"></i>${esc(f.catatan)}
                    </div>`
                  : ""
              }
            </div>

            <button class="btn btn-outline-secondary btn-sm ms-2 rad-open-file"
              data-url="${esc(url)}"
              data-mime="${esc(f.mime_type || "")}">
              <i class="fa fa-up-right-from-square"></i>
            </button>

          </div>
        `;
          })
          .join("")
      : `<div class="text-muted small">Belum ada file hasil.</div>`;

    // ================= HISTORY =================
    const histHtml = history.length
      ? history
          .map((h) => {
            const t = fmtDate(h.created_date);
            const statusLap =
              String(h.status_lap || "D") === "F" ? "Final" : "Draft";

            return `
          <div class="border rounded p-2 mb-1">
            <div class="d-flex justify-content-between">
              <div class="small fw-semibold">
                ${esc(statusLap)} • v${esc(h.file_version || "-")}
              </div>
              <div class="small text-muted">${esc(t)}</div>
            </div>

            <div class="small mt-1">
              <b>Hasil Bacaan:</b>
              <pre style="white-space:pre-wrap;">${esc(
                h.hasil_bacaan || "-",
              )}</pre>
            </div>
          </div>
        `;
          })
          .join("")
      : `<div class="text-muted small">Belum ada riwayat.</div>`;

    // ================= HTML =================
    const html = `
    <div class="row g-3">

      <!-- LEFT -->
      <div class="col-md-3">

        <div class="border rounded p-2 mb-2">
          <div class="fw-semibold mb-1">Identitas Pasien</div>
          <div>Nama: <b>${esc(w.nama_pasien || "-")}</b></div>
          <div>No.RM: <span class="mono">${esc(w.no_rm || "-")}</span></div>
          <div>Episode: <span class="mono">${esc(w.episode_id || "-")}</span></div>
        </div>

        <div class="border rounded p-2 mb-2">
          <div class="fw-semibold mb-1">Permintaan</div>
          <div>Asal: ${esc(w.nama_poli || "-")}</div>
          <div>Dokter: ${esc(w.nama_dokter_pengirim || "-")}</div>
          <div>Rekanan: ${esc(w.rekanan_nama || "-")}</div>
          <div>CITO:
            ${
              String(w.cito_yn || "n").toUpperCase() === "Y"
                ? '<span class="badge text-bg-danger">CITO</span>'
                : '<span class="badge text-bg-secondary">REGULER</span>'
            }
          </div>
          <div class="small text-muted mt-1">
            Kirim: ${esc(tglKirim)}<br/>
            Dibaca: ${esc(tglDibaca)}<br/>
            Validasi: ${esc(tglValid)}
          </div>
        </div>

        <div class="border rounded p-2">
          <div class="fw-semibold mb-1">Status</div>
          <div>${badgeStatus(w.status)}</div>
          <div class="small text-muted mt-1">
            Buat: ${esc(created)} oleh ${esc(w.created_by || "-")}
          </div>
        </div>

      </div>

      <!-- FILE -->
      <div class="col-md-3">
        <div class="border rounded p-2 h-100">
          <div class="fw-semibold mb-1">File Hasil</div>
          <div class="small text-muted mb-2">Klik untuk preview.</div>

          <div id="radFileList">
            ${fileListHtml}
          </div>
        </div>
      </div>

      <!-- HASIL BACAAN -->
      <div class="col-md-6">
        <form id="frmRadExpertise">

          <input type="hidden" name="id" value="${esc(identifyId(w))}">
          <input type="hidden" name="file_version" value="${
            (files[0] && files[0].versi) || ""
          }">

          <div class="border rounded p-2 mb-2">
            <div class="fw-semibold mb-1">Hasil Bacaan</div>

         <textarea
            class="form-control mb-2"
            name="hasil_bacaan"
            placeholder="Tulis hasil radiologi..."
            style="
              min-height: 520px;
              resize: vertical;
              font-size: 15px;
              line-height: 1.6;
              font-family: Arial, sans-serif;
            "
          >${last.hasil_bacaan ? esc(last.hasil_bacaan) : ""}</textarea>

            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" id="templateSelect">
                <option value="">Pilih Template...</option>
              </select>

              <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLoadTemplate">
                Gunakan
              </button>
            </div>

          </div>

          <div class="d-flex justify-content-between align-items-center">
            <div class="small text-muted" id="radExpertiseMsg">-</div>

            <div class="btn-group">

              <button type="button"
                class="btn btn-outline-secondary btn-sm"
                id="btnRadDraft">
                Simpan Draft
              </button>

              <button type="button"
                class="btn btn-success btn-sm"
                id="btnRadFinal">
                <i class="fa fa-check me-1"></i> Selesai & Kunci
              </button>

              

              <select class="form-select form-select-sm rad-print-paper" id="radPaperSize">
                <option value="A4">A4</option>
                <option value="F4">F4</option>
              </select>

              <button type="button"
                      class="btn btn-outline-primary btn-sm rad-print-result"
                      data-id="${esc(identifyId(w))}">
                <i class="fa fa-print me-1"></i> Print
              </button>

            </div>
          </div>

        </form>
      </div>

      <!-- HISTORY -->
      <div class="col-12">
        <div class="border rounded p-2">
          <div class="fw-semibold mb-1">Riwayat Bacaan</div>
          ${histHtml}
        </div>
      </div>

    </div>
  `;

    $("#radDetailBody").html(html);

    // load template setelah render
    loadTemplates();
  }

  function radRenderDetailxxx(res) {
    const w = res.worklist || {};
    const files = Array.isArray(res.files) ? res.files : [];
    const last = res.last_expertise || {};
    const history = Array.isArray(res.history) ? res.history : [];

    const created = fmtDate(w.created_date || w.tanggal);
    const tglKirim = fmtDate(w.tgl_kirim);
    const tglDibaca = fmtDate(w.tgl_dibaca);
    const tglValid = fmtDate(w.tgl_validasi);

    const fileListHtml = files.length
      ? files
          .map((f) => {
            const isImg = /^image\//.test(f.mime_type || "");
            const isPdf = (f.mime_type || "").toLowerCase().includes("pdf");

            const url =
              BASE_URL.replace(/\/$/, "") + "/" + String(f.file_path || "");

            const ikon = isImg ? "fa-image" : isPdf ? "fa-file-pdf" : "fa-file";

            const sizeMb = f.file_size
              ? (f.file_size / 1024 / 1024).toFixed(2).replace(/\.00$/, "")
              : "0";

            const thumb = isImg
              ? `<img src="${esc(url)}"
                  alt="preview"
                  class="border rounded"
                  style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                  data-url="${esc(url)}">`
              : `<i class="fa ${ikon} fa-2x text-muted"></i>`;

            const latestBadge =
              String(f.is_latest || "0") === "1"
                ? '<span class="badge text-bg-primary ms-1 flex-shrink-0">Terbaru</span>'
                : "";

            const lockedBadge =
              String(f.is_locked || "0").toUpperCase() === "1"
                ? '<span class="badge text-bg-warning ms-1 flex-shrink-0">LOCKED</span>'
                : "";

            return `
          <div class="d-flex align-items-center mb-2">
            <div class="me-2 rad-open-file-thumb" data-url="${esc(url)}">
              ${thumb}
            </div>

            <!-- min-width:0 WAJIB supaya text-truncate jalan di dalam flex -->
            <div class="flex-grow-1" style="min-width:0;">
              <div class="small fw-semibold d-flex align-items-center">
                <span class="text-truncate d-block" style="max-width:100%;" title="${esc(
                  f.file_name || "",
                )}">
                  v${esc(f.versi || 1)} - ${esc(f.file_name || "")}
                </span>
                ${latestBadge}
                ${lockedBadge}
              </div>
              <div class="small text-muted text-truncate">
                ${esc(f.mime_type || "-")} • ${sizeMb} MB
              </div>
            </div>

            <button class="btn btn-outline-secondary btn-sm ms-2 rad-open-file"
                    type="button"
                    data-url="${esc(url)}"
                    data-mime="${esc(f.mime_type || "")}">
              <i class="fa fa-up-right-from-square"></i>
            </button>
          </div>
        `;
          })
          .join("")
      : `<div class="text-muted small">Belum ada file hasil yang aktif.</div>`;

    const histHtml = history.length
      ? history
          .map((h) => {
            const t = fmtDate(h.created_date);
            const statusLap =
              String(h.status_lap || "D") === "F" ? "Final" : "Draft";
            return `
            <div class="border rounded p-2 mb-1">
              <div class="d-flex justify-content-between">
                <div class="small fw-semibold">
                  ${esc(statusLap)} • v${esc(h.file_version || "-")}
                </div>
                <div class="small text-muted">${esc(t)}</div>
              </div>
              <div class="small mt-1"><b>Temuan:</b> ${esc(
                h.temuan || "-",
              )}</div>
              <div class="small"><b>Kesan:</b> ${esc(h.kesan || "-")}</div>
              <div class="small"><b>Saran:</b> ${esc(h.saran || "-")}</div>
            </div>
          `;
          })
          .join("")
      : `<div class="text-muted small">Belum ada log bacaan.</div>`;

    const html = `
      <div class="row g-3">

        <div class="col-md-4">
          <div class="border rounded p-2 mb-2">
            <div class="fw-semibold mb-1">Identitas Pasien</div>
            <div>Nama: <b>${esc(w.nama_pasien || "-")}</b></div>
            <div>No.RM: <span class="mono">${esc(w.no_rm || "-")}</span></div>
            <div>Episode: <span class="mono">${esc(
              w.episode_id || "-",
            )}</span></div>
          </div>

          <div class="border rounded p-2 mb-2">
            <div class="fw-semibold mb-1">Permintaan</div>
            <div>Asal: ${esc(w.nama_poli || "-")}</div>
            <div>Dokter: ${esc(w.nama_dokter_pengirim || "-")}</div>
            <div>Rekanan: ${esc(w.rekanan_nama || "-")}</div>
            <div>CITO: ${
              String(w.cito_yn || "n").toUpperCase() === "Y"
                ? '<span class="badge text-bg-danger">CITO</span>'
                : '<span class="badge text-bg-secondary">REGULER</span>'
            }</div>
            <div class="small text-muted mt-1">
              Kirim: ${esc(tglKirim)}<br/>
              Dibaca: ${esc(tglDibaca)}<br/>
              Validasi: ${esc(tglValid)}
            </div>
          </div>

          <div class="border rounded p-2">
            <div class="fw-semibold mb-1">Status</div>
            <div>${badgeStatus(w.status)}</div>
            <div class="small text-muted mt-1">
              Buat: ${esc(created)} oleh ${esc(w.created_by || "-")}
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="border rounded p-2 h-100">
            <div class="fw-semibold mb-1">File Hasil</div>
            <div class="small text-muted mb-2">Klik ikon untuk buka di tab baru.</div>
            <div id="radFileList">
              ${fileListHtml}
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <form id="frmRadExpertise">
            <input type="hidden" name="id" value="${esc(identifyId(w))}">
            <input type="hidden" name="file_version" value="${
              (files[0] && files[0].versi) || ""
            }">

            <div class="border rounded p-2 mb-2">
              <div class="fw-semibold mb-1">Temuan</div>
              <textarea class="form-control form-control-sm" rows="4" name="temuan">${
                last.temuan ? esc(last.temuan) : ""
              }</textarea>
            </div>

            <div class="border rounded p-2 mb-2">
              <div class="fw-semibold mb-1">Kesan</div>
              <textarea class="form-control form-control-sm" rows="3" name="kesan">${
                last.kesan ? esc(last.kesan) : ""
              }</textarea>
            </div>

            <div class="border rounded p-2 mb-2">
              <div class="fw-semibold mb-1">Saran</div>
              <textarea class="form-control form-control-sm" rows="2" name="saran">${
                last.saran ? esc(last.saran) : ""
              }</textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div class="small text-muted" id="radExpertiseMsg">-</div>
              <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRadDraft">
                  Simpan Draft
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btnRadFinal">
                  <i class="fa fa-check me-1"></i> Selesai & Kunci
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="col-12">
          <div class="border rounded p-2">
            <div class="fw-semibold mb-1">Riwayat Bacaan</div>
            <div id="radHistoryBody">${histHtml}</div>
          </div>
        </div>
      </div>
    `;

    $("#radDetailBody").html(html);
  }

  // Buka file di tab baru (klik ikon)
  $(document).on("click", ".rad-open-file", function () {
    const url = $(this).data("url");
    if (!url) return toast("URL file tidak tersedia.", "danger");
    window.open(url, "_blank");
  });

  // Buka file di tab baru (klik thumbnail)
  $(document).on("click", ".rad-open-file-thumb img", function () {
    const url = $(this).closest(".rad-open-file-thumb").data("url");
    if (!url) return toast("URL file tidak tersedia.", "danger");
    window.open(url, "_blank");
  });
  function loadTemplates() {
    $.ajax({
      url: BASE_URL + "RadDoctorController/get_templates",
      method: "GET",
      dataType: "json", // 🔥 WAJIB
      success: function (res) {
        if (res && res.success) {
          let opt = '<option value="">Pilih Template...</option>';

          res.data.forEach((t) => {
            opt += `<option value="${t.isi_template}">${t.nama_template}</option>`;
          });

          $("#templateSelect").html(opt);
        } else {
          console.log("Template kosong / gagal", res);
        }
      },
      error: function (err) {
        console.error("Error load template:", err);
      },
    });
  }
  function loadTemplatesxx() {
    // alert("12");
    $.get(BASE_URL + "RadDoctorController/get_templates", function (res) {
      if (res.success) {
        let opt = '<option value="">Pilih Templatex...</option>';
        res.data.forEach((t) => {
          // opt += `<option value="${t.isi_template}">${t.nama_template}</option>`;
          opt += `<option value="${t.isi_template.replace(/\n/g, "\\n")}">${t.nama_template}</option>`;
        });
        $("#templateSelect").html(opt);
      }
    });
  }

  function radSubmitExpertise(mode) {
    if (!radCurrentId) return;

    const $form = $("#frmRadExpertise");
    if (!$form.length) return;

    // const temuan = $form.find('[name="temuan"]').val();
    // const kesan = $form.find('[name="kesan"]').val();
    // const saran = $form.find('[name="saran"]').val();

    const hasil = $form.find('[name="hasil_bacaan"]').val();

    const fileVersion = $form.find('[name="file_version"]').val() || "";

    if (mode === "final" && !hasil.trim()) {
      toast("Hasil wajib diisi untuk laporan final.", "warning");
      return;
    }

    Swal.fire({
      title: mode === "final" ? "Finalisasi laporan?" : "Simpan sebagai draft?",
      text:
        mode === "final"
          ? "Setelah final, laporan dianggap sah dan file versi terkait akan dikunci."
          : "Draft bisa diubah kembali sebelum final.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: mode === "final" ? "Ya, finalkan" : "Ya, simpan",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (!result.isConfirmed) return;

      $("#radExpertiseMsg").html(
        `<span class="text-muted">Menyimpan ${mode}...</span>`,
      );

      $.ajax({
        url: BASE_URL + "RadDoctorController/worklist_saveExpertise",
        method: "POST",
        dataType: "json",
        data: {
          id: radCurrentId,
          hasil_bacaan: hasil,
          file_version: fileVersion,
          mode: mode,
        },
        success: function (res) {
          if (res && res.success) {
            $("#radExpertiseMsg").html(
              `<span class="text-success">Berhasil disimpan.</span>`,
            );
            toast(
              mode === "final"
                ? "Laporan radiologi difinalkan."
                : "Draft laporan tersimpan.",
            );
            loadRadWorklist();
            // reload detail supaya history & status update
            radOpenDetail(radCurrentId);
          } else {
            $("#radExpertiseMsg").html(
              `<span class="text-danger">${esc(
                res?.message || "Gagal menyimpan.",
              )}</span>`,
            );
          }
        },
        error: function () {
          $("#radExpertiseMsg").html(
            `<span class="text-danger">Gagal menyimpan (AJAX error).</span>`,
          );
        },
      });
    });
  }

  $(document).on("click", "#btnLoadTemplate", function () {
    let val = $("#templateSelect").val();
    if (!val) return;

    // 🔥 ubah kembali \n jadi enter
    val = val.replace(/\\n/g, "\n");

    const textarea = $('[name="hasil_bacaan"]');
    // textarea.val(textarea.val() + "\n" + val);

    textarea.val(textarea.val() ? textarea.val() + "\n\n" + val : val);
  });

  // =======================
  // EVENT BINDING
  // =======================
  $(document).on("click", ".rad-print-result", function () {
    const id = $(this).data("id");
    const paper = $("#radPaperSize").val() || "A4";

    if (!id) {
      toast("ID cetakan tidak ditemukan.", "danger");
      return;
    }

    const url =
      BASE_URL +
      "RadDoctorController/print_result?id=" +
      encodeURIComponent(id) +
      "&paper=" +
      encodeURIComponent(paper);

    window.open(url, "_blank");
  });

  $(document).on("click", "#tblRadDoctor thead th.rad-sortable", function () {
    const key = String($(this).data("sort") || "");

    if (!key) return;

    if (radSort.key === key) {
      radSort.dir = radSort.dir === "asc" ? "desc" : "asc";
    } else {
      radSort.key = key;
      radSort.dir = key === "tanggal" ? "desc" : "asc";
    }

    radRenderTable(radLastData);
  });

  $(document).on("click", ".rad-filter", function () {
    radActiveStatus = String($(this).data("status") || "");
    setRadFilterUI(radActiveStatus);
    loadRadWorklist();
  });

  $("#radSearch").on("input", function () {
    radRenderTable(radLastData);
  });

  $("#radClearSearch").on("click", function () {
    $("#radSearch").val("");
    radRenderTable(radLastData);
  });

  $("#radBtnRefresh").on("click", function () {
    loadRadWorklist();
  });

  $("#radBtnAutoRefresh").on("click", function () {
    const enabled = $(this).attr("data-enabled") === "1";
    if (enabled) {
      clearInterval(radAutoTimer);
      radAutoTimer = null;
      $(this)
        .attr("data-enabled", "0")
        .removeClass("btn-primary")
        .addClass("btn-outline-primary")
        .html(`<i class="fa fa-bolt me-1"></i> Auto`);
      toast("Auto refresh dimatikan.", "warning");
    } else {
      radAutoTimer = setInterval(() => loadRadWorklist(), 10000);
      $(this)
        .attr("data-enabled", "1")
        .removeClass("btn-outline-primary")
        .addClass("btn-primary")
        .html(`<i class="fa fa-bolt me-1"></i> Auto ON`);
      toast("Auto refresh aktif (10 detik).");
    }
  });

  $(document).on("click", ".rad-act", function () {
    const act = $(this).data("act");
    const id = $(this).data("id");
    if (act === "detail") {
      radOpenDetail(id);
    }
  });

  // $(document).on("click", ".rad-open-file", function () {
  //   const url = $(this).data("url");
  //   if (!url) return;
  //   window.open(url, "_blank");
  // });

  $(document).on("click", "#btnRadDraft", function () {
    radSubmitExpertise("draft");
  });

  $(document).on("click", "#btnRadFinal", function () {
    radSubmitExpertise("final");
  });

  // INIT
  setRadFilterUI(radActiveStatus);
  loadRadWorklist();
});
