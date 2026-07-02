$(document).ready(function () {
  console.log("Rad JS loaded (POST AJAX style)");

  let activeStatus = "0";
  let autoTimer = null;
  let lastData = [];
  let currentDetailId = null;

  const statusMeta = {
    0: { text: "BARU", cls: "text-bg-primary" },
    1: { text: "SELESAI", cls: "text-bg-success" },
    2: { text: "DITUNDA", cls: "text-bg-info" },
    3: { text: "DIBATALKAN", cls: "text-bg-danger" },
    4: { text: "DIKERJAKAN", cls: "text-bg-warning" },
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

  function badgeStatus(code) {
    const s = statusMeta[String(code)] || { text: code, cls: "text-bg-light" };
    return `<span class="badge ${s.cls}">${esc(s.text)}</span>`;
  }

  function identifyId(row) {
    return (
      row.id ||
      `${row.episode_id || ""}|${row.pasien_id || ""}|${row.trans_id || ""}|${
        row.trans_co || ""
      }`
    );
  }

  function setActiveFilterUI(status) {
    // alert("12");
    $(".filter-status").removeClass("active");
    $(`.filter-status[data-status="${status}"]`).addClass("active");
    const textMap = {
      0: "Baru",
      4: "Dikerjakan",
      2: "Ditunda",
      3: "Dibatalkan",
      1: "Selesai",
      9: "Menunggu Dokter",
    };
    $("#activeFilterText").html(
      `Menampilkan: <b>${textMap[String(status)] || status}</b>`,
    );
  }

  function loadCounts() {
    $.ajax({
      url: BASE_URL + "Rad-Worklist/worklist-counts",
      method: "POST",
      dataType: "json",
      data: {},
      success: function (c) {
        ["0", "1", "2", "3", "4", "9"].forEach((s) => {
          $("#count_" + s).text(c[s] ?? 0);
          $("#kpi_" + s).text(c[s] ?? 0);
        });
      },
    });
  }

  function loadWorklist() {
    // alert(activeStatus);
    $.ajax({
      url: BASE_URL + "Rad-Worklist/worklist-filterData",
      method: "POST",
      dataType: "json",
      data: {
        status: activeStatus,
        keyword: ($("#quickSearch").val() || "").trim(),
        tanggal: getSelectedTanggal(),
        semua_tanggal: isSemuaTanggal(),
      },
      success: function (res) {
        renderTable(res || []);
        loadCounts();
      },
      error: function () {
        toast("Gagal memuat data worklist radiologi.", "danger");
      },
    });
  }

  function getSelectedTanggal() {
    return $("#filterTanggalRad").val() || "";
  }

  function isSemuaTanggal() {
    return $("#chkSemuaTanggalRad").is(":checked") ? "1" : "0";
  }

  function updateStatus(id, status) {
    Swal.fire({
      title: "Konfirmasi",
      text: "Apakah Anda yakin ingin mengubah status?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Update",
      cancelButtonText: "Batal",
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "Rad-Worklist/worklist-updateStatus",
          method: "POST",
          dataType: "json",
          data: { id: id, status: status },
          success: function (out) {
            if (out && out.success) {
              Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Status berhasil diupdate",
                timer: 1500,
                showConfirmButton: false,
              });
              loadWorklist();
            } else {
              Swal.fire({
                icon: "error",
                title: "Gagal",
                text: out?.message || "Gagal update status",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Gagal menghubungi server",
            });
          },
        });
      }
    });
  }

  // function updateStatusxxxxx(id, status) {
  //   // alert(id);
  //   // alert(status);
  //   $.ajax({
  //     url: BASE_URL + "Rad-Worklist/worklist-updateStatus",
  //     method: "POST",
  //     dataType: "json",
  //     data: { id, status },
  //     success: function (out) {
  //       if (out && out.success) {
  //         toast("Status berhasil diupdate.");
  //         loadWorklist();
  //       } else {
  //         toast(out?.message || "Gagal update status.", "danger");
  //       }
  //     },
  //     error: function () {
  //       toast("Gagal update status.", "danger");
  //     },
  //   });
  // }

  $(document).on("click", ".filter-status", function () {
    activeStatus = String($(this).data("status"));
    setActiveFilterUI(activeStatus);
    loadWorklist();
    // alert("123");
  });

  $("#filterTanggalRad").on("change", function () {
    // alert("qq");
    // loadWorklist();

    if (!$("#chkSemuaTanggalRad").is(":checked")) {
      loadWorklist();
    }
  });

  $("#chkSemuaTanggalRad").on("change", function () {
    const checked = $(this).is(":checked");

    $("#filterTanggalRad").prop("disabled", checked);

    if (!checked && !$("#filterTanggalRad").val()) {
      const now = new Date();
      const yyyy = now.getFullYear();
      const mm = String(now.getMonth() + 1).padStart(2, "0");
      const dd = String(now.getDate()).padStart(2, "0");
      $("#filterTanggalRad").val(`${yyyy}-${mm}-${dd}`);
    }

    loadWorklist();
  });

  $("#quickSearch").on("input", function () {
    // alert("12");
    renderTable(lastData);
  });

  $("#btnClearSearch").on("click", function () {
    $("#quickSearch").val("");
    renderTable(lastData);
  });

  function renderTable(data) {
    lastData = Array.isArray(data) ? data : [];

    const q = ($("#quickSearch").val() || "").trim().toLowerCase();
    const filtered = !q
      ? lastData
      : lastData.filter((r) => {
          const hay = [
            r.nama_pasien,
            r.no_rm,
            r.episode_id,
            r.pasien_id,
            r.trans_id,
            r.trans_co,
            r.nama_poli,
            r.nama_dokter,
            r.catatan_rad,
            r.alasan_rencana,
          ]
            .join(" ")
            .toLowerCase();
          return hay.includes(q);
        });

    let html = "";
    if (!filtered.length) {
      html = `<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr>`;
    } else {
      html = filtered
        .map((r) => {
          const waktu = fmtDate(r.created_date || r.tanggal);
          const pasien = `
          <div class="fw-semibold">${esc(r.nama_pasien || "-")}</div>
          <div class="small text-muted">RM: <span class="mono">${esc(
            r.no_rm || "-",
          )}</span> • Episode: <span class="mono">${esc(
            r.episode_id || "-",
          )}</span></div>
        `;
          const asal = `
          <div class="fw-semibold">${esc(r.nama_poli || "-")}</div>
          <div class="small text-muted">${esc(r.nama_dokter || "-")}</div>
        `;
          const catatan = `
          <div class="small">${esc(
            r.catatan_rad || r.alasan_rencana || "-",
          )}</div>
          ${
            String(r.cito_yn || "").toUpperCase() === "Y"
              ? `<span class="badge text-bg-danger mt-1">CITO</span>`
              : ``
          }
        `;
          const files = `<span class="badge text-bg-dark">${parseInt(
            r.file_count || 0,
            10,
          )} file</span>`;
          const id = identifyId(r);
          const st = String(r.status);

          // 🔥 LOGIC KUNCI
          const isDone = st === "1";

          const btnPrint = isDone
            ? `
              <a href="${BASE_URL}RadDoctorController/print_result?id=${esc(
                identifyId(r),
              )}"
                target="_blank"
                class="btn btn-outline-primary btn-sm">
                <i class="fa fa-print"></i>
              </a>
            `
            : "";

          return `
          <tr>
            <td class="ps-3">${esc(waktu)}</td>
            <td>${pasien}</td>
            <td>${asal}</td>
            <td>${catatan}</td>
            <td>${files}</td>
            <td>${badgeStatus(r.status)}</td>
            <td class="text-end pe-3">
              <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                ${btnPrint}
                
                <button class="btn btn-outline-secondary btn-sm js-act" data-act="detail" data-id="${esc(
                  id,
                )}">
                  <i class="fa fa-circle-info me-1"></i> Detail
                </button>
                
                <button class="btn btn-outline-primary btn-sm js-act"
                            data-act="upload" data-id="${esc(id)}"
                            ${isDone ? "disabled" : ""}>
                            <i class="fa fa-upload me-1"></i> Upload Hasil
                          </button>



                <div class="btn-group btn-group-sm" role="group">
                 
                

                  <button class="btn btn-outline-warning js-act"
                    data-act="status" data-id="${esc(id)}" data-status="4"
                    ${st === "1" || st === "9" ? "disabled" : ""}>
                    Kerjakan
                  </button>
                

            <button class="btn btn-outline-info js-act"
              data-act="status" data-id="${esc(id)}" data-status="2"
              ${isDone ? "disabled" : ""}>
              Tunda
            </button>



             

   <button class="btn btn-outline-danger js-act"
              data-act="status" data-id="${esc(id)}" data-status="3"
              ${isDone ? "disabled" : ""}>
              Batal
            </button>



                </div>
                <button class="btn btn-success btn-sm js-act" data-act="status" data-id="${esc(
                  id,
                )}" data-status="1" ${["4"].includes(st) ? "" : "disabled"}>
                  <i class="fa fa-check me-1"></i> Selesai
                </button>
              </div>
            </td>
          </tr>
        `;
        })
        .join("");
    }

    $("#tblRad tbody").html(html);
    $("#tableInfo").text(`${filtered.length} data`);
    $("#lastUpdated").text(fmtDate(new Date()));
  }

  function fmtDate(dt) {
    if (!dt) return "-";
    // dt bisa "2025-12-07T..." atau "2025-12-07 10:11:12"
    const d = new Date(dt);
    if (!isNaN(d.getTime())) {
      const pad = (n) => String(n).padStart(2, "0");
      return `${pad(d.getDate())}/${pad(
        d.getMonth() + 1,
      )}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }
    return dt;
  }

  function openDetail(id, focusUpload = false) {
    currentDetailId = id;

    // alert(currentDetailId);
    const modal = new bootstrap.Modal(
      document.getElementById("modalRadDetail"),
    );
    $("#radDetailBody").html("Memuat...");
    modal.show();

    $.ajax({
      url: BASE_URL + "Rad-Worklist/worklist-detail",
      method: "POST",
      dataType: "json",
      data: { id },
      success: function (res) {
        if (!res || !res.success) {
          $("#radDetailBody").html(
            `<div class="alert alert-danger">${esc(
              res?.message || "Gagal memuat detail",
            )}</div>`,
          );
          return;
        }

        const w = res.worklist || {};
        const orders = Array.isArray(res.orders) ? res.orders : [];
        const files = Array.isArray(res.files) ? res.files : [];

        // 🔥 SIMPAN STATUS GLOBAL
        currentDetailStatus = String(w.status || "");
        const isDone = currentDetailStatus === "1";

        const ordersHtml = orders.length
          ? orders
              .map(
                (o) => `
          <tr>
            <td class="mono">${esc(o.test_id || "-")}</td>
            <td>${esc(o.nama_pemeriksaan || "-")}</td>
            <td>${esc(o.cito || "-")}</td>
            <td>${esc(o.catatan || "-")}</td>
          </tr>
        `,
              )
              .join("")
          : `<tr><td colspan="4" class="text-center text-muted">Tidak ada order detail</td></tr>`;

        const filesHtml = files.length
          ? files
              .map((f) => {
                const url =
                  BASE_URL.replace(/\/$/, "") + "/" + String(f.file_path || "");
                const open = `<a class="btn btn-sm btn-outline-primary" target="_blank" href="${esc(
                  url,
                )}">Buka</a>`;

                const del = isDone
                  ? ""
                  : `<button class="btn btn-sm btn-outline-danger js-del-file" data-fileid="${esc(
                      f.id,
                    )}">Hapus</button>`;

                // const del = `<button class="btn btn-sm btn-outline-danger js-del-file" data-fileid="${esc(
                //   f.id,
                // )}">Hapus</button>`;

                // const edit = `<button class="btn btn-sm btn-outline-warning js-edit-note"
                //                 data-fileid="${esc(f.id)}"
                //                 data-note="${esc(f.catatan || "")}">
                //                 Edit
                //               </button>`;

                const edit = isDone
                  ? ""
                  : `<button class="btn btn-sm btn-outline-warning js-edit-note"
                      data-fileid="${esc(f.id)}"
                      data-note="${esc(f.catatan || "")}">
                      Edit
                   </button>`;

                // ✅ CATATAN
                const note = f.catatan
                  ? `<div class="small text-danger mt-1">
                      <i class="fa fa-note-sticky me-1"></i>${esc(f.catatan)}
                    </div>`
                  : `<div class="small text-muted mt-1">-</div>`;

                return `
        <tr>
          <td>
            <div class="fw-semibold">${esc(f.file_name || "-")}</div>
            ${note}
          </td>
          <td class="small text-muted">
            ${esc(f.mime_type || "-")} • ${esc(f.file_size || 0)} bytes
          </td>
          <td class="small text-muted">
            ${esc(f.uploaded_by || "-")} • ${esc(fmtDate(f.uploaded_at))}
          </td>
          <td class="text-end">
            ${open} ${edit} ${del}
          </td>
        </tr>
      `;

                // return `
                //   <tr>
                //     <td>${esc(f.file_name || "-")}</td>
                //     <td class="small text-muted">${esc(f.mime_type || "-")} • ${esc(
                //       f.file_size || 0,
                //     )} bytes</td>
                //     <td class="small text-muted">${esc(f.uploaded_by || "-")} • ${esc(
                //       fmtDate(f.uploaded_at),
                //     )}</td>
                //     <td class="text-end">${open} ${del}</td>
                //   </tr>
                // `;
              })
              .join("")
          : `<tr><td colspan="4" class="text-center text-muted">Belum ada hasil diupload</td></tr>`;

        // const btnKirim =
        //   files.length > 0
        //     ? `
        //       <button class="btn btn-success btn-sm js-send-doctor">
        //         <i class="fa fa-paper-plane me-1"></i> Kirim ke Dokter
        //       </button>
        //     `
        //     : "";

        const btnKirim =
          files.length > 0
            ? `
            <button class="btn btn-success btn-sm js-send-doctor"
              ${isDone ? "disabled" : ""}>
              <i class="fa fa-paper-plane me-1"></i> Kirim ke Dokter
            </button>
          `
            : "";

        const html = `
          <div class="row g-3">
            <div class="col-md-5">
              <div class="border rounded-4 p-3 bg-white">
                <div class="fw-semibold mb-2">Identitas</div>
                <div>Pasien: <b>${esc(w.nama_pasien || "-")}</b></div>
                <div>RM: <span class="mono">${esc(w.no_rm || "-")}</span></div>
                <div>Episode: <span class="mono">${esc(
                  w.episode_id || "-",
                )}</span></div>
                <div>Status: ${badgeStatus(w.status)}</div>
                <div class="mt-2 small text-muted">Catatan: ${esc(
                  w.catatan_rad || w.alasan_rencana || "-",
                )}</div>
              </div>
            </div>

            <div class="col-md-7">
              <div class="border rounded-4 p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="fw-semibold">Upload Hasil Radiologi</div>
                  <div class="small text-muted">JPG/PNG/WebP/PDF (max 20MB/file)</div>
                </div>
                <form id="frmUploadRad" class="d-flex gap-2 flex-wrap">
                  <input type="file" class="form-control" name="files[]" id="radFiles" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" ${isDone ? "disabled" : ""}>

                  <textarea 
                    class="form-control" 
                    name="catatan_file" 
                    id="catatanFile"
                    placeholder="Tambahkan catatan untuk dokter radiologi..."
                    style="min-width:100%; height:70px;"  ${isDone ? "disabled" : ""}></textarea>

                  <button type="submit" class="btn btn-primary" ${isDone ? "disabled" : ""}>
                    <i class="fa fa-upload me-1"></i> Upload
                  </button>
                </form>
                <div id="uploadMsg" class="small mt-2"></div>
              </div>
            </div>

            <div class="col-12">
              <div class="border rounded-4 p-3 bg-white">
                <div class="fw-semibold mb-2">Order Pemeriksaan</div>
                <div class="table-responsive">
                  <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                      <tr><th>test_id</th><th>Pemeriksaan</th><th>CITO</th><th>Catatan</th></tr>
                    </thead>
                    <tbody>${ordersHtml}</tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="border rounded-4 p-3 bg-white">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="fw-semibold">Berkas Hasil</div>
                  ${btnKirim}
                </div>
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr><th>File</th><th>Tipe/Ukuran</th><th>Uploader</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody id="radFilesList">${filesHtml}</tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        `;

        $("#radDetailBody").html(html);

        if (focusUpload && !isDone) {
          setTimeout(() => $("#radFiles").trigger("focus"), 200);
        }
      },
      error: function () {
        $("#radDetailBody").html(
          `<div class="alert alert-danger">Gagal memuat detail (AJAX error).</div>`,
        );
      },
    });
  }

  $(document).on("submit", "#frmUploadRad", function (e) {
    e.preventDefault();

    if (!currentDetailId) {
      Swal.fire({
        icon: "warning",
        title: "Belum ada data",
        text: "Silakan pilih worklist radiologi dulu.",
      });
      return;
    }

    // ✅ TAMBAHKAN DI SINI
    const note = $("#catatanFile").val().trim();
    if (!note) {
      Swal.fire({
        icon: "warning",
        title: "Catatan kosong",
        text: "Harap isi catatan untuk dokter.",
      });
      return;
    }

    // if (!currentDetailId) return;
    const fd = new FormData(this);

    fd.append("id", currentDetailId);
    // ✅ pastikan ikut terkirim
    fd.append("catatan_file", note);

    // Loading swal
    Swal.fire({
      title: "Mengupload hasil...",
      text: "Mohon tunggu sebentar.",
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // $("#uploadMsg").html(`<span class="text-muted">Uploading...</span>`);

    $.ajax({
      url: BASE_URL + "Rad-Worklist/worklist-uploadResult",
      method: "POST",
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        Swal.close();

        if (!res || !res.success) {
          Swal.fire({
            icon: "error",
            title: "Upload gagal",
            text: res?.message || "Terjadi kesalahan saat upload.",
          });
          return;
        }

        // 🔥 tampilkan sukses dulu
        Swal.fire({
          icon: "success",
          title: "Upload berhasil",
          timer: 1200,
          showConfirmButton: false,
        });

        // 🔥 DELAY biar tidak bentrok modal
        setTimeout(() => {
          openDetail(currentDetailId, false);
        }, 300);

        // 🔥 INI YANG KURANG
        // openDetail(currentDetailId, false);

        // refresh list file & worklist
        // if (typeof refreshFilesList === "function") {
        //   refreshFilesList();
        // }

        // 🔥 clear input
        $("#catatanFile").val("");
        $("#radFiles").val("");

        if (typeof loadWorklist === "function") {
          loadWorklist();
        }

        const fileCount = parseInt(res.file_count || 0, 10);

        // Kalau backend sudah balikin file_count -> tawarkan kirim ke dokter
        if (fileCount > 0) {
          Swal.fire({
            icon: "success",
            title: "Upload berhasil",
            text:
              "Total " +
              fileCount +
              " file hasil radiologi tersimpan. Kirim ke dokter radiologi sekarang?",
            showCancelButton: true,
            confirmButtonText: "Ya, kirim sekarang",
            cancelButtonText: "Nanti saja",
          }).then((result) => {
            if (result.isConfirmed) {
              // Panggil endpoint kirim ke dokter (sesuaikan URL-nya kalau beda)
              $.ajax({
                url: BASE_URL + "Rad-Worklist/worklist-sendToDoctor",
                method: "POST",
                dataType: "json",
                data: { id: currentDetailId },
                success: function (r) {
                  if (r && r.success) {
                    Swal.fire({
                      icon: "success",
                      title: "Berhasil",
                      text: "Hasil sudah dikirim ke dokter radiologi.",
                    });

                    if (typeof loadWorklist === "function") {
                      loadWorklist();
                    }
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Gagal kirim",
                      text: r?.message || "Gagal mengirim ke dokter.",
                    });
                  }
                },
                error: function () {
                  Swal.fire({
                    icon: "error",
                    title: "Gagal kirim",
                    text: "Terjadi kesalahan koneksi saat kirim ke dokter.",
                  });
                },
              });
            } else {
              Swal.fire({
                icon: "info",
                title: "Upload tersimpan",
                text: "File hasil tersimpan, belum dikirim ke dokter.",
              });
            }
          });
        } else {
          // fallback kalau backend belum kirim file_count
          Swal.fire({
            icon: "success",
            title: "Upload berhasil",
            text: "File hasil radiologi berhasil diupload.",
          });
        }

        // if (res && res.success) {
        //   $("#uploadMsg").html(
        //     `<span class="text-success">Upload berhasil.</span>`
        //   );
        //   // refresh detail files list
        //   // refreshFilesList();
        //   // loadWorklist();
        // } else {
        //   $("#uploadMsg").html(
        //     `<span class="text-danger">${esc(
        //       res?.message || "Upload gagal"
        //     )}</span>`
        //   );
        // }
      },
      error: function () {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Upload gagal",
          text: "Terjadi kesalahan koneksi (AJAX error).",
        });
      },
    });

    // alert(fd);
  });

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

  function refreshFilesList() {
    // alert("refreshFilesList");
    $.ajax({
      url: BASE_URL + "RadController/worklist_resultList",
      method: "POST",
      dataType: "json",
      data: { id: currentDetailId },
      success: function (res) {
        if (!res || !res.success) return;

        const files = Array.isArray(res.files) ? res.files : [];
        const html = files.length
          ? files
              .map((f) => {
                const url =
                  BASE_URL.replace(/\/$/, "") + "/" + String(f.file_path || "");
                return `
            <tr>
              <td>${esc(f.file_name || "-")}</td>
              <td class="small text-muted">${esc(f.mime_type || "-")} • ${esc(
                f.file_size || 0,
              )} bytes</td>
              <td class="small text-muted">${esc(f.uploaded_by || "-")} • ${esc(
                fmtDate(f.uploaded_at),
              )}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" target="_blank" href="${esc(
                  url,
                )}">Buka</a>
                <button class="btn btn-sm btn-outline-danger js-del-file" data-fileid="${esc(
                  f.id,
                )}">Hapus</button>
              </td>
            </tr>
          `;
              })
              .join("")
          : `<tr><td colspan="4" class="text-center text-muted">Belum ada hasil diupload</td></tr>`;

        $("#radFilesList").html(html);
      },
    });
  }
  // $(document).on("click", ".js-edit-note", function () {
  //   const fileId = $(this).data("fileid");
  //   // const note = $(this).data("note") || "";
  //   let note = $(this).data("note") || "";

  //   // ✅ penting: escape agar tidak merusak HTML
  //   note = note.replace(/</g, "&lt;").replace(/>/g, "&gt;");

  //   Swal.fire({
  //     title: "Edit Catatan",
  //     html: `
  //             <textarea id="editNote" class="form-control"
  //               style="height:120px;">${note}</textarea>
  //           `,
  //     // input: "textarea",
  //     // inputValue: note,
  //     // inputPlaceholder: "Masukkan catatan...",
  //     // inputAttributes: {
  //     //   "aria-label": "Catatan radiologi",
  //     // },
  //     showCancelButton: true,
  //     confirmButtonText: "Simpan",
  //     cancelButtonText: "Batal",
  //     focusConfirm: false,
  //     // 🔥 WAJIB → biar bisa diketik
  //     didOpen: () => {
  //       // 🔥 FIX utama
  //       $(".modal").css("pointer-events", "none");

  //       const el = document.getElementById("editNote");
  //       if (el) el.focus();
  //     },
  //     willClose: () => {
  //       $(".modal").css("pointer-events", "auto");
  //     },
  //     preConfirm: () => {
  //       const value = document.getElementById("editNote").value.trim();

  //       if (!value) {
  //         Swal.showValidationMessage("Catatan tidak boleh kosong!");
  //         return false;
  //       }

  //       return value;
  //     },

  //     // inputValidator: (value) => {
  //     //   if (!value.trim()) {
  //     //     return "Catatan tidak boleh kosong!";
  //     //   }
  //     // },
  //   }).then((result) => {
  //     if (result.isConfirmed) {
  //       $.ajax({
  //         url: BASE_URL + "Rad-Worklist/updateNote",
  //         method: "POST",
  //         dataType: "json",
  //         data: {
  //           file_id: fileId,
  //           catatan: result.value,
  //         },
  //         success: function (res) {
  //           if (res && res.success) {
  //             Swal.fire("Berhasil", "Catatan diperbarui", "success");

  //             // refresh list
  //             if (typeof refreshFilesList === "function") {
  //               refreshFilesList();
  //             }
  //           } else {
  //             Swal.fire("Gagal", res?.message || "Gagal update", "error");
  //           }
  //         },
  //         error: function () {
  //           Swal.fire("Error", "Koneksi gagal", "error");
  //         },
  //       });
  //     }
  //   });
  // });

  $("#modalRadDetail").on("hidden.bs.modal", function () {
    // 🔥 bersihkan sisa backdrop
    $("body").removeClass("modal-open");
    $(".modal-backdrop").remove();

    // 🔥 reset scroll
    $("body").css("overflow", "");
  });

  $(document).on("click", ".js-edit-note", function () {
    const fileId = $(this).data("fileid");
    let note = $(this).data("note") || "";

    // escape biar aman
    note = note.replace(/</g, "&lt;").replace(/>/g, "&gt;");

    // 🔥 SIMPAN ID DETAIL SAAT INI
    const currentId = currentDetailId;

    // 🔥 TUTUP MODAL DETAIL DULU
    $("#modalRadDetail").modal("hide");

    setTimeout(() => {
      Swal.fire({
        title: "Edit Catatan",
        html: `
        <textarea id="editNote" class="form-control"
          style="height:120px;">${note}</textarea>
      `,
        showCancelButton: true,
        confirmButtonText: "Simpan",
        cancelButtonText: "Batal",
        focusConfirm: false,

        didOpen: () => {
          const el = document.getElementById("editNote");
          if (el) el.focus();
        },

        preConfirm: () => {
          const value = document.getElementById("editNote").value.trim();

          if (!value) {
            Swal.showValidationMessage("Catatan tidak boleh kosong!");
            return false;
          }

          return value;
        },
      }).then((result) => {
        // 🔥 BUKA KEMBALI MODAL DETAIL
        setTimeout(() => {
          openDetail(currentId, false);
        }, 200);

        if (result.isConfirmed) {
          $.ajax({
            url: BASE_URL + "Rad-Worklist/updateNote",
            method: "POST",
            dataType: "json",
            data: {
              file_id: fileId,
              catatan: result.value,
            },
            success: function (res) {
              if (res && res.success) {
                Swal.fire("Berhasil", "Catatan diperbarui", "success");

                if (typeof refreshFilesList === "function") {
                  refreshFilesList();
                }
              } else {
                Swal.fire("Gagal", res?.message || "Gagal update", "error");
              }
            },
            error: function () {
              Swal.fire("Error", "Koneksi gagal", "error");
            },
          });
        }
      });
    }, 300); // delay biar modal benar-benar hilang
  });

  $(document).on("click", ".js-send-doctor", function () {
    // 🔥 AMBIL STATUS DARI ROW
    // const row = $(this).closest("tr");
    // const w = res.worklist || {};
    // const statusText = row.find("td:nth-child(6)").text().toUpperCase();

    // alert(w);

    // 🔥 BLOCK JIKA SUDAH SELESAI
    // if (currentDetailStatus === "1") {
    //   Swal.fire({
    //     icon: "warning",
    //     title: "Tidak bisa kirim",
    //     text: "Data sudah selesai dan tidak dapat dikirim ulang ke dokter.",
    //   });
    //   return;
    // }

    Swal.fire({
      title: "Kirim ke dokter?",
      text: "Hasil akan dikirim ke dokter radiologi.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, kirim",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: BASE_URL + "Rad-Worklist/worklist-sendToDoctor",
        method: "POST",
        dataType: "json",
        data: { id: currentDetailId },
        success: function (res) {
          if (res && res.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: "Hasil sudah dikirim ke dokter.",
              timer: 1500,
              showConfirmButton: false,
            });

            // 🔥 TUTUP MODAL
            const modalEl = document.getElementById("modalRadDetail");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // 🔥 CLEAN BACKDROP (ANTI FREEZE)
            setTimeout(() => {
              $("body").removeClass("modal-open");
              $(".modal-backdrop").remove();
            }, 300);

            // refresh worklist
            if (typeof loadWorklist === "function") {
              loadWorklist();
            }

            // reload biar status berubah
            // setTimeout(() => {
            //   openDetail(currentDetailId, false);
            // }, 300);

            // loadWorklist();
          } else {
            Swal.fire("Gagal", res?.message || "Gagal kirim", "error");
          }
        },
        error: function () {
          Swal.fire("Error", "Koneksi gagal", "error");
        },
      });
    });
  });

  $(document).on("click", ".js-del-file", function () {
    // alert("123");
    const fileId = $(this).data("fileid");
    if (!fileId) return;
    // if (!confirm("Hapus file ini? (soft delete)")) return;

    Swal.fire({
      title: "Hapus file?",
      text: "File akan dihapus (soft delete) dan tidak tampil di daftar.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
      cancelButtonText: "Batal",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
    }).then((result) => {
      if (!result.isConfirmed) return;
      $.ajax({
        // url: BASE_URL + "Rad-Worklist/worklist-resultDelete",
        url: BASE_URL + "RadController/worklist_resultDelete",
        method: "POST",
        dataType: "json",
        data: { file_id: fileId },
        success: function (res) {
          if (res && res.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: "File berhasil dihapus",
              timer: 1500,
              showConfirmButton: false,
            });

            // 🔥 RELOAD DETAIL SAJA (INI KUNCI)
            setTimeout(() => {
              openDetail(currentDetailId, false);
            }, 300);

            // OPTIONAL
            if (typeof loadWorklist === "function") {
              loadWorklist();
            }

            // refreshFilesList();
            // loadWorklist();
          } else {
            Swal.fire({
              icon: "error",
              title: "Gagal",
              text: res?.message || "Gagal menghapus file",
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Terjadi kesalahan server",
          });
        },
      });
    });
  });

  $(document).on("click", ".js-act", function () {
    // alert("123");
    const act = $(this).data("act");
    const id = $(this).data("id");

    if (act === "detail") return openDetail(id, false);
    if (act === "upload") return openDetail(id, true);

    // if (act === "status") {
    //   const st = String($(this).data("status"));
    //   const label = statusMeta[st]?.text || st;
    //   if (!confirm(`Ubah status menjadi "${label}" ?`)) return;
    //   return updateStatus(id, st);
    // }

    if (act === "status") {
      const st = String($(this).data("status"));
      return updateStatus(id, st); // Swal confirm ada di sini
    }
  });

  // INIT
  setActiveFilterUI(activeStatus);
  loadWorklist();
  loadCounts();
});
