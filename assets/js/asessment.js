$(document).ready(function () {
  console.log("Asessment JS loaded");

  $(".select2").select2({
    // placeholder: "-- Pilih Propinsi --", // Placeholder saat belum ada pilihan
    allowClear: true, // Tambahkan tombol untuk menghapus pilihan
    width: "100%", // Sesuaikan lebar dengan elemen parent
  });

  const fisikLabels = {
    fisik_ku: "Keadaan Umum (KU)",
    fisik_td: "Tekanan Darah (TD)",
    fisik_nadi: "Nadi",
    fisik_rr: "RR (Respiratory Rate)",
    fisik_suhu: "Suhu",
    fisik_bb: "Berat Badan (BB)",
    fisik_tb: "Tinggi Badan (TB)",
    fisik_lila: "LILA",
  };

  let initialName = $("#ic_nama").val();

  let now = new Date();

  let tgl = String(now.getDate()).padStart(2, "0");
  let bln = String(now.getMonth() + 1).padStart(2, "0");
  let thn = now.getFullYear();

  let jam = String(now.getHours()).padStart(2, "0");
  let mnt = String(now.getMinutes()).padStart(2, "0");

  let label = `${tgl}/${bln}/${thn} • ${jam}:${mnt}`;

  // fungsi untuk menampilkan filter poli/dokter jika POLIKLINIK dipilih

  // $("#ic_saksi").val(initialName);

  // Event listener: Setiap kali input nama berubah, saksi ikut berubah
  $("#ic_nama").on("input keyup change", function () {
    let namaInput = $(this).val();
    $("#ic_saksi").val(namaInput);
  });

  function handleFilterVisibility(selectId, containerId) {
    // alert("121121");
    const selector = document.getElementById(selectId);
    const container = document.getElementById(containerId);

    // jika elemen tidak ditemukan, hentikan
    if (!selector || !container) return;

    selector.addEventListener("change", function () {
      if (this.value === "POLIKLINIK") {
        container.style.display = "block";
      } else {
        container.style.display = "none";
      }

      // Reload data setiap kali jenis layanan berubah
      reloadData(this.value, selectId);
    });
  }

  // === Fungsi umum untuk load dokter berdasarkan poli (AJAX) ===
  //   function handleDokterLoader(poliSelectId, dokterSelectId) {
  function handleDokterLoader(poliSelectId, dokterSelectId, selectId) {
    const poliSelect = document.getElementById(poliSelectId);
    const dokterSelect = document.getElementById(dokterSelectId);

    if (!poliSelect || !dokterSelect) return;

    poliSelect.addEventListener("change", function () {
      const poliId = this.value;

      // Kosongkan dulu daftar dokter
      dokterSelect.innerHTML = '<option value="">-- Pilih Dokter --</option>';

      if (!poliId) {
        reloadData("POLIKLINIK", selectId);
        return;
      }

      // Kosongkan dokter jika belum pilih poli
      //   if (!poliId) {
      //     dokterSelect.innerHTML = '<option value="">-- Pilih Dokter --</option>';
      //     return;
      //   }

      // Load data dokter via AJAX
      $.ajax({
        url: BASE_URL + "Asessment-getDokter",
        method: "GET",
        data: { poli_id: poliId },
        dataType: "json",
        beforeSend: function () {
          dokterSelect.innerHTML = "<option>Memuat...</option>";
        },
        success: function (res) {
          let options = '<option value="">-- Pilih Dokter --</option>';
          res.forEach((r) => {
            options += `<option value="${r.dokter_id}">${r.nama}</option>`;
          });
          dokterSelect.innerHTML = options;

          // reload data berdasarkan poli yang dipilih
          reloadData("POLIKLINIK", selectId, poliId);
        },
        error: function () {
          dokterSelect.innerHTML =
            '<option value="">Gagal memuat dokter</option>';
        },
      });
    });

    dokterSelect.addEventListener("change", function () {
      reloadData("POLIKLINIK", selectId, poliSelect.value, this.value);
    });
  }

  $(document).on("click", "#btnPaliatif", function () {
    let episodeId = $(this).data("episode");
    let pasienId = $(this).data("pasien");

    const url =
      BASE_URL +
      "Asessment/paliatifForm" +
      "?episode_id=" +
      encodeURIComponent(episodeId) +
      "&pasien_id=" +
      encodeURIComponent(pasienId);

    window.open(url, "_blank");
  });

  $(document).on("click", ".btnAsesmenLayanan", function () {
    let episode = $(this).data("episode");
    let pasien = $(this).data("pasien");
    let layanan = $(this).data("layanan");

    if (layanan === "JKL-RAD") {
      window.location.href =
        BASE_URL +
        "AsessmentController/formAssessmentRad/" +
        episode +
        "/" +
        pasien;
    }

    if (layanan === "JKL-LAB") {
      window.location.href =
        BASE_URL +
        "AsessmentController/formAssessmentLab/" +
        episode +
        "/" +
        pasien;
    }
  });

  // =========================
  // EDIT (SUDAH)
  // =========================
  $(document).on("click", ".btnEditLayanan", function () {
    let episode = $(this).data("episode");
    let pasien = $(this).data("pasien");
    let layanan = $(this).data("layanan");

    if (layanan === "JKL-RAD") {
      window.location.href =
        BASE_URL + "AsessmentController/editAssesRad/" + episode + "/" + pasien;
    }

    if (layanan === "JKL-LAB") {
      window.location.href =
        BASE_URL + "AsessmentController/editAssesLab/" + episode + "/" + pasien;
    }
  });

  $(document).on("click", ".btnMulaiAsesmen", function () {
    const episode_id = $(this).data("episode");
    const pasien_id = $(this).data("pasien");
    const poli_id = $(this).data("poli");
    const jml_kunj = $(this).data("kunj");
    // alert("oke");
    // console.log(
    //   `Mulai asesmen → Episode: ${episode_id}, Pasien: ${pasien_id}, Poli: ${poli_id}`
    // );

    Swal.fire({
      title: "Mulai Asesmen?",
      text: "Sistem akan membuka form asesmen sesuai jenis layanan.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, lanjutkan",
      cancelButtonText: "Batal",
    }).then((res) => {
      if (res.isConfirmed) {
        $.ajax({
          url: BASE_URL + "AsessmentController/getFormAsesmen",
          type: "POST",
          dataType: "json",
          data: {
            episode_id: episode_id,
            poli_id: poli_id,
            pasien_id: pasien_id,
            jml_kunj: jml_kunj,
          },
          beforeSend: function () {
            Swal.fire({
              title: "Memuat...",
              text: "Menyiapkan form asesmen...",
              allowOutsideClick: false,
              didOpen: () => Swal.showLoading(),
            });
          },

          success: function (res) {
            Swal.close();

            if (res.success) {
              // redirect ke halaman form asesmen yang sesuai
              window.location.href = BASE_URL + res.redirect_url;
            } else {
              Swal.fire({
                icon: "warning",
                title: "Gagal",
                text: res.message,
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Kesalahan Server",
              text: "Tidak dapat membuka form asesmen.",
            });
          },
        });
      }
    });
  });

  // === 3️⃣ Fungsi untuk reload tabel data berdasarkan filter ===
  function reloadData(jenisLayanan, selectId, poliId = "", dokterId = "") {
    // alert("d/");
    let tab = selectId.includes("Belum") ? "Belum" : "Selesai";

    // Ambil tanggal sesuai tab
    const tanggal = $(`#filterTanggalAses${tab}`).val() || "";
    // alert(tanggal);
    // console.log(
    //   `Reload data ${tab} → Layanan:${jenisLayanan}, Poli:${poliId}, Dokter:${dokterId}`,
    // );

    console.log(
      `Reload data ${tab} → Tanggal:${tanggal}, Layanan:${jenisLayanan}, Poli:${poliId}, Dokter:${dokterId}`,
    );

    $.ajax({
      url: BASE_URL + "Asessment-filterData",
      method: "POST",
      data: {
        jenis_layanan: jenisLayanan,
        poli_id: poliId,
        dokter_id: dokterId,
        tanggal: tanggal, // <-- kirim ke controller
        tab: tab, // untuk membedakan tab Belum/Selesai
      },
      dataType: "html",
      beforeSend: function () {
        $(`#tab${tab} tbody`).html(
          `<tr><td colspan="11" class="text-center text-muted">Memuat data...</td></tr>`,
        );
      },
      success: function (res) {
        $(`#tab${tab} tbody`).html(res);
      },
      error: function () {
        $(`#tab${tab} tbody`).html(
          `<tr><td colspan="11" class="text-center text-danger">Gagal memuat data</td></tr>`,
        );
      },
    });
  }

  // ==============================
  // GLOBAL FILTER FUNCTION
  // ==============================
  function applyFilter(suffix) {
    let tanggal = $("#filterTanggalAses" + suffix).val();
    let jenis = $("#jenisLayanan" + suffix).val();
    let poli = $("#poliSelect" + suffix).val();
    let dokter = $("#dokterSelect" + suffix).val();
    let search = $("#filterSearch" + suffix)
      .val()
      .toLowerCase();

    // target table berdasarkan tab
    let table =
      suffix === "Belum"
        ? $("#tabBelum table tbody tr")
        : $("#tabSelesai table tbody tr");

    table.each(function () {
      let row = $(this);

      let nama = row.find("td:eq(2)").text().toLowerCase();
      let mr = row.find("td:eq(1)").text().toLowerCase();
      let poliText = row.find("td:eq(3)").text().toLowerCase();
      let dokterText = row.find("td:eq(4)").text().toLowerCase();

      let matchSearch = !search || nama.includes(search) || mr.includes(search);

      let matchPoli =
        !poli ||
        poliText.includes(
          $("#poliSelect" + suffix + " option:selected")
            .text()
            .toLowerCase(),
        );
      let matchDokter =
        !dokter ||
        dokterText.includes(
          $("#dokterSelect" + suffix + " option:selected")
            .text()
            .toLowerCase(),
        );

      if (matchSearch && matchPoli && matchDokter) {
        row.show();
      } else {
        row.hide();
      }
    });

    // alert(tanggal);
    // alert(jenis);
    // alert(poli);
    // alert(dokter);
    // alert(search);
  }

  $(document).on("keyup", ".filterSearch", function () {
    let suffix = $(this).attr("id").replace("filterSearch", "");
    applyFilter(suffix);
  });

  $(document).on(
    "change",
    "#jenisLayananBelum, #jenisLayananSelesai, .poliSelect, .dokterSelect",
    function () {
      // alert("1111");
      let suffix = $(this)
        .attr("id")
        .replace(/(jenisLayanan|poliSelect|dokterSelect)/, "");
      applyFilter(suffix);
    },
  );

  // === 4️⃣ Inisialisasi untuk dua tab (Belum dan Selesai) ===
  handleFilterVisibility("jenisLayananBelum", "filterPoliklinikBelum");
  handleFilterVisibility("jenisLayananSelesai", "filterPoliklinikSelesai");

  //   handleDokterLoader("poliSelectBelum", "dokterSelectBelum");
  handleDokterLoader(
    "poliSelectBelum",
    "dokterSelectBelum",
    "jenisLayananBelum",
  );
  //   handleDokterLoader("poliSelectSelesai", "dokterSelectSelesai");
  handleDokterLoader(
    "poliSelectSelesai",
    "dokterSelectSelesai",
    "jenisLayananSelesai",
  );

  $(".filterTanggal").on("change", function () {
    const id = this.id; // filterTanggalAsesBelum / filterTanggalAsesSelesai
    const tab = id.includes("Belum") ? "Belum" : "Selesai";

    const jenis = $(`#jenisLayanan${tab}`).val() || "SEMUA";

    let poliId = "";
    let dokterId = "";

    if (jenis === "POLIKLINIK") {
      poliId = $(`#poliSelect${tab}`).val() || "";
      dokterId = $(`#dokterSelect${tab}`).val() || "";
    }
    // alert(jenis);

    reloadData(jenis, `jenisLayanan${tab}`, poliId, dokterId);
  });

  $("#jenisLayananBelum, #jenisLayananSelesai").on("change", function () {
    // alert("12");
    const jenis = $(this).val(); // SEMUA / PENUNJANG / POLIKLINIK
    const tab = this.id.includes("Belum") ? "Belum" : "Selesai";

    let poliId = "";
    let dokterId = "";

    if (jenis === "POLIKLINIK") {
      poliId = $(`#poliSelect${tab}`).val() || "";
      dokterId = $(`#dokterSelect${tab}`).val() || "";
    }

    reloadData(jenis, `jenisLayanan${tab}`, poliId, dokterId);
  });

  $(document).on("change", "input[name='riw_alergi_ada']", function () {
    if ($(this).val() === "Ya") {
      $(".alergiFields").show();
    } else {
      $(".alergiFields").hide().find("input").val("");
    }
  });

  $(document).on("change", 'input[name="merokok"]', function () {
    if ($(this).val() === "Ya") {
      $(".merokokFields").show();
    } else {
      $(".merokokFields").hide();
      $(
        'input[name="merokok_jml"], input[name="merokok_lama"], input[name="merokok_jenis"]',
      ).val("");
    }
  });

  $(document).on("change", 'input[name="alkohol"]', function () {
    if ($(this).val() === "Ya") {
      $(".alkoholFields").show();
    } else {
      $(".alkoholFields").hide();
      $('input[name="alkohol_jml"], input[name="alkohol_lama"]').val("");
    }
  });

  $('input[name="nyeri_ada"]').on("change", function () {
    if ($(this).val() == "Ya") {
      $(".nyeriFields").show();
    } else {
      $(".nyeriFields").hide();
      $("#nyeri_skor").val("");
      $("#nyeri_kategori").val("");
      $(".painFace").removeClass("border border-primary rounded");
    }
  });

  $(".painFace").on("click", function () {
    let sc = parseInt($(this).data("score"));
    $("#nyeri_skor").val(sc);

    $(".painFace").removeClass("border border-primary rounded");
    $(this).addClass("border border-primary rounded");

    let k = "";
    if (sc <= 3) k = "Ringan";
    else if (sc <= 6) k = "Sedang";
    else k = "Berat";

    $("#nyeri_kategori").val(k);
  });

  function hitungMorse() {
    let total = 0;
    $(".morseOpt").each(function () {
      total += parseInt($(this).val());
    });
    $("#morse_total").val(total);

    let kat = "";
    if (total <= 24) kat = "Resiko Rendah";
    else if (total <= 44) kat = "Resiko Sedang";
    else kat = "Resiko Tinggi";

    // $("#morse_kategori").text(kat);
    $("#morse_kategori").val(kat);
  }

  $(".morseOpt").on("change", hitungMorse);

  hitungMorse(); // initial

  $(".giziRadio").on("change", function () {
    if ($(this).val() == "Ya") {
      $(".giziFields").show();
    } else {
      $(".giziFields").hide();

      // reset semua
      $("input[name='gizi_detail[]']").prop("checked", false);
      $(".giziLainnyaInput").val("").hide().prop("required", false);
    }
  });

  // ketika tick lainnya
  $(".giziLainnyaCheck").on("change", function () {
    if ($(this).is(":checked")) {
      $(".giziLainnyaInput").show().prop("required", true);
    } else {
      $(".giziLainnyaInput").hide().prop("required", false).val("");
    }
  });

  // 1) bicara
  $("input[name='edk_bicara']").on("change", function () {
    if ($(this).val() == "Tidak normal") {
      $("input[name='edk_bicara_ket']").show().prop("required", true);
    } else {
      $("input[name='edk_bicara_ket']").hide().prop("required", false).val("");
    }
  });
  //   .trigger("change");

  // 2) penerjemah
  $("input[name='edk_penerjemah']").on("change", function () {
    if ($(this).val() == "Ya") {
      $("input[name='edk_penerjemah_bahasa']").show().prop("required", true);
    } else {
      $("input[name='edk_penerjemah_bahasa']")
        .hide()
        .prop("required", false)
        .val("");
    }
  });
  // .trigger("change");

  // 3) hambatan
  $("input[name='edk_hambatan']").on("change", function () {
    if ($(this).val() == "Ada") {
      $("input[name='edk_hambatan_list[]']").closest(".form-check").show();
    } else {
      $("input[name='edk_hambatan_list[]']")
        .prop("checked", false)
        .closest(".form-check")
        .hide();
    }
  });
  // .trigger("change");

  // 4) edukasi kesehatan lainnya
  $("#edk_k4").on("change", function () {
    if ($(this).is(":checked")) {
      $("input[name='edk_kebutuhan_lain']").show().prop("required", true);
    } else {
      $("input[name='edk_kebutuhan_lain']")
        .hide()
        .prop("required", false)
        .val("");
    }
  });
  // .trigger("change");

  $(".edkLainCheck").on("change", function () {
    if ($(this).is(":checked")) {
      $(".edkKebutuhanLainInput").show().attr("required", true);
    } else {
      $(".edkKebutuhanLainInput").hide().val("").attr("required", false);
    }
  });

  $("#formAssessLab").on("submit", function (e) {
    let errors = [];
    let firstErrorEl = null;

    function setError(element, message) {
      errors.push(message);
      element.addClass("is-invalid");
      if (!firstErrorEl) firstErrorEl = element;
    }

    function clearError(element) {
      element.removeClass("is-invalid");
    }

    // ==================================================
    // 1️⃣ VALIDASI FIELD TEXT WAJIB
    // ==================================================

    let requiredFields = [
      "ic_nama",
      "ic_umur",
      "suku",
      "agama",
      "alamat",
      "kecamatan",
      "kab_kota",
      "nik",
      // "bb",
      // "tb",
      // "gol_darah",
      "telp",
      "nama_suami",
    ];

    requiredFields.forEach((f) => {
      let el = $(`input[name="${f}"]`);
      clearError(el);

      if (!el.length) return;

      let v = el.val()?.trim() ?? "";
      if (v === "" || v === "th /") {
        setError(el, `Kolom <b>${f.replace("_", " ")}</b> wajib diisi.`);
      }
    });

    // ==================================================
    // 2️⃣ VALIDASI RADIO GROUP – WAJIB PILIH
    // ==================================================

    let requiredRadios = [
      "ic_hubungan",
      "status_kawin",
      // "seks_pranikah",
      // "edu_klien",
      // "edu_suami",
      // "fl_siklus_haid",
      // "fl_kel_vulva",
      // "fl_kel_vagina",
      // "fl_curiga_klr",
      // "fl_pem_ssk",
      // "fl_ambil_pap",
      // "fl_tes_hpv",
      // "fl_foto_doiva",
      // "fl_kel_kanker",
      // "fl_klien_kanker",
      // "fl_kel_cairan",
      // "fl_kel_nyeri",
      // "fl_kel_senggama",
      // "fl_kel_nonhaid",
      // "fl_kel_lain",
      // "fl_rokok_pasien",
      // "fl_rokok_suami",
      // "fl_rokok_rumah",
      // "fl_kb_status",
      // "fl_pap_status",
      // "fl_iva_status",
    ];

    requiredRadios.forEach((name) => {
      let group = $(`input[name="${name}"]`);
      group.removeClass("is-invalid");

      if (group.length && !$(`input[name="${name}"]:checked`).length) {
        setError(
          group.eq(0),
          `Pilihan <b>${name.replace("_", " ")}</b> wajib dipilih.`,
        );
      }
    });

    // ==================================================
    // 3️⃣ VALIDASI SUB-FIELD YANG MUNCUL DINAMIS
    // ==================================================

    // ---- KB Riwayat (jika Pernah, minimal 1 terpilih)
    if ($('input[name="fl_kb_status"]:checked').val() === "Pernah") {
      // alert("123");
      let kb = $('input[name="fl_kb_riwayat[]"]:checked');
      if (!kb.length) {
        setError($("#fl_box_kb_riwayat"), "Pilih minimal 1 jenis KB.");
      }
    }

    // ---- KB Saat Ini (WAJIB pilih minimal 1)
    let kbNow = $('input[name="fl_kb_now[]"]:checked');

    if (!kbNow.length) {
      setError(
        $("#fl_box_kb_now"),
        "Pilih minimal 1 KB yang digunakan saat ini.",
      );
    }

    // ---- PAP Smear Tahun (jika Pernah)
    if ($('input[name="fl_pap_status"]:checked').val() === "Pernah") {
      let el = $("#fl_pap_thn");
      clearError(el);
      if (el.val().trim() === "") {
        setError(el, "Tahun Pap Smear wajib diisi.");
      }
    }

    // ---- IVA Tahun (jika Pernah)
    if ($('input[name="fl_iva_status"]:checked').val() === "Pernah") {
      let el = $("#fl_iva_thn");
      clearError(el);
      if (el.val().trim() === "") {
        setError(el, "Tahun IVA wajib diisi.");
      }
    }

    // ---- IVA NEGATIF – cek 1/3/5 tahun
    if ($("#fl_iva_negatif").is(":checked")) {
      // pastikan box terlihat dulu supaya errors tidak hilang
      // tampilkan box untuk validasi
      $("#fl_box_iva_berkala").removeClass("d-none").addClass("d-flex");

      let berkala = $('input[name="fl_iva_berkala[]"]:checked');

      if (!berkala.length) {
        setError(
          $("#fl_box_iva_berkala"),
          "Pilih minimal 1 tahun (1/3/5) untuk pemeriksaan berkala.",
        );
        // hasError = true;
      }
    }

    // ---- ROKOK (jika Ya/Pernah → wajib isi jumlah)
    let rokokFields = [
      ["fl_rokok_pasien", "fl_rokok_pasien_jml"],
      ["fl_rokok_suami", "fl_rokok_suami_jml"],
      ["fl_rokok_rumah", "fl_rokok_rumah_jml"],
    ];

    rokokFields.forEach(([radio, textbox]) => {
      let val = $(`input[name="${radio}"]:checked`).val();
      let el = $(`input[name="${textbox}"]`);
      clearError(el);

      if (val === "Ya" || val === "Pernah") {
        if (el.val().trim() === "") {
          setError(el, `Kolom jumlah rokok (${radio}) wajib diisi.`);
        }
      }
    });

    // ---- Keluarga Kanker "Ada" → wajib pilih siapa
    if ($('input[name="fl_kel_kanker"]:checked').val() === "Ada") {
      let siapa = $('input[name="fl_kel_siapa[]"]:checked');
      if (!siapa.length) {
        setError(
          $("#fl_box_kel_detail"),
          "Pilih siapa keluarga yang terkena kanker.",
        );
      }

      let jenis = $("#fl_kel_jenis");
      clearError(jenis);
      if (jenis.val().trim() === "") {
        setError(jenis, "Jenis kanker keluarga wajib diisi.");
      }
    }

    // ---- Klien terkena kanker
    if ($('input[name="fl_klien_kanker"]:checked').val() === "Ada") {
      let el = $("#fl_klien_jenis");
      clearError(el);
      if (el.val().trim() === "") {
        setError(el, "Jenis kanker klien wajib diisi.");
      }
    }

    // ==================================================
    // 4️⃣ VALIDASI ANGKA / NUMERIC
    // ==================================================

    // Field INT (harus bilangan bulat atau kosong)
    const intFields = [
      "suami_menikah_ke",
      "fl_usia_haid",
      "fl_usia_kawin",
      "fl_usia_hamil",
      "fl_usia_menopause",
      "fl_jml_lahir",
      "fl_jml_gugur",
    ];

    intFields.forEach((f) => {
      const el = $(`input[name="${f}"]`);
      if (!el.length) return;
      clearError(el);

      const raw = el.val();
      if (raw == null || raw === "") return; // boleh kosong

      const v = raw.trim();

      if (v === "") {
        // kalau cuma spasi, treat sebagai kosong
        el.val("");
        return;
      }

      if (!/^\d+$/.test(v)) {
        setError(
          el,
          `Kolom <b>${f.replace(/_/g, " ")}</b> hanya boleh berisi angka.`,
        );
      } else {
        el.val(v); // normalisasi (hilangkan spasi)
      }
    });

    // Field NUMERIC(5,2) → BB, TB (boleh desimal)
    const decimalFields = ["bb", "tb"];

    decimalFields.forEach((f) => {
      const el = $(`input[name="${f}"]`);
      if (!el.length) return;
      clearError(el);

      let raw = el.val();
      if (raw == null || raw === "") return; // boleh kosong

      raw = raw.trim();
      if (raw === "") {
        el.val("");
        return;
      }

      // ganti koma jadi titik (65,5 → 65.5)
      raw = raw.replace(",", ".");

      if (!/^\d+(\.\d+)?$/.test(raw)) {
        setError(
          el,
          `Kolom <b>${f.toUpperCase()}</b> harus berupa angka (boleh desimal).`,
        );
      } else {
        el.val(raw); // normalisasi ke format yang bisa diterima Postgres
      }
    });

    // ==================================================
    // 4️⃣ FINAL CHECK
    // ==================================================

    if (errors.length > 0) {
      e.preventDefault();

      let errorList = errors.map((err) => `<li>${err}</li>`).join("");

      Swal.fire({
        icon: "warning",
        title: "Perhatian",
        html: `
                <div style="
                    text-align:left;
                    font-size:13px;
                    background:#f8d7da;
                    color:#721c24;
                    border:1px solid #f5c6cb;
                    padding:15px;
                    border-radius:5px;">
                    <strong>Data belum lengkap:</strong>
                    <ul style="margin:0;padding-left:18px;">
                        ${errorList}
                    </ul>
                </div>
            `,
        confirmButtonText: "Siap, Saya Lengkapi",
      }).then(() => {
        if (firstErrorEl) {
          $("html, body").animate(
            { scrollTop: firstErrorEl.offset().top - 150 },
            400,
          );
          firstErrorEl.focus();
        }
      });
    }
  });

  // ===============================
  // FORM PALIATIF
  // ===============================

  const $formPaliatif = $("#formPaliatif");

  if ($formPaliatif.length) {
    // aktif/nonaktif input keterangan symptom
    $(document)
      .off("change.paliatifSymptom", ".symptom-check")
      .on("change.paliatifSymptom", ".symptom-check", function () {
        const targetId = $(this).data("target");
        const $target = $("#" + targetId);

        if (!$target.length) return;

        if ($(this).is(":checked")) {
          $target.prop("disabled", false);
        } else {
          $target.prop("disabled", true).val("").removeClass("is-invalid");
        }
      });

    // inisialisasi kondisi awal symptom desc
    $(".symptom-check").each(function () {
      const targetId = $(this).data("target");
      const $target = $("#" + targetId);

      if (!$target.length) return;

      if ($(this).is(":checked")) {
        $target.prop("disabled", false);
      } else {
        $target.prop("disabled", true).val("");
      }
    });

    function clearPaliatifValidation() {
      $formPaliatif.find(".is-invalid").removeClass("is-invalid");
      $formPaliatif.find(".invalid-feedback.dynamic-feedback").remove();
      $formPaliatif
        .find(".border-danger")
        .removeClass("border border-danger rounded p-2");
    }

    function setInvalid($el, message) {
      if (!$el || !$el.length) return;

      $el.addClass("is-invalid");

      if (message && !$el.next(".invalid-feedback.dynamic-feedback").length) {
        $el.after(
          `<div class="invalid-feedback dynamic-feedback">${message}</div>`,
        );
      }
    }

    function setRadioInvalid(name, message) {
      const $first = $(`[name='${name}']`).first();
      if (!$first.length) return;

      // const $wrap = $first.closest(".col-md-6, .mb-2, .exam-row");
      const $wrap = $first.closest(".col-md-6, .col-md-4, .mb-2, .exam-row");

      const $targetWrap = $wrap.length ? $wrap : $first.parent();

      $targetWrap.addClass("border border-danger rounded p-2");

      if (!$targetWrap.find(".invalid-feedback.dynamic-feedback").length) {
        $targetWrap.append(
          `<div class="invalid-feedback dynamic-feedback d-block">${message}</div>`,
        );
      }
    }

    function getVal(name) {
      return ($(`[name='${name}']`).val() || "").trim();
    }

    function getCanvasBase64(canvasId) {
      const canvas = document.getElementById(canvasId);
      if (!canvas) return "";
      return canvas.toDataURL("image/png");
    }

    $formPaliatif.off("submit.paliatif").on("submit.paliatif", function (e) {
      e.preventDefault();
      e.stopPropagation();

      clearPaliatifValidation();

      let errors = [];
      let firstErrorEl = null;

      const episodeId = getVal("episode_id");
      const pasienId = getVal("pasien_id");
      const petugasHhpc = getVal("petugas_hhpc");
      const tglKunjungan = getVal("tgl_kunjungan");
      const namaPasien = getVal("nama_pasien");
      const umur = getVal("umur");
      const telp = getVal("telp");
      const hp = getVal("hp");
      const careGiver = getVal("care_giver");
      const masalah1 = getVal("masalah_1");
      const nyeri = getVal("nyeri");
      const diagnosis = getVal("diagnosis");
      const jenisKelamin = $("[name='jenis_kelamin']:checked").val() || "";

      if (!episodeId || !pasienId) {
        errors.push("Episode ID atau Pasien ID tidak ditemukan.");
      }

      if (!petugasHhpc) {
        const $el = $("[name='petugas_hhpc']");
        setInvalid($el, "Oleh Tim / Relawan HHPC wajib diisi.");
        errors.push("Oleh Tim / Relawan HHPC wajib diisi.");
        if (!firstErrorEl) firstErrorEl = $el;
      }

      if (!tglKunjungan) {
        const $el = $("[name='tgl_kunjungan']");
        setInvalid($el, "Hari / Tanggal wajib diisi.");
        errors.push("Hari / Tanggal wajib diisi.");
        if (!firstErrorEl) firstErrorEl = $el;
      }

      if (!namaPasien) {
        const $el = $("[name='nama_pasien']");
        setInvalid($el, "Nama pasien wajib diisi.");
        errors.push("Nama pasien wajib diisi.");
        if (!firstErrorEl) firstErrorEl = $el;
      }

      if (!jenisKelamin) {
        setRadioInvalid("jenis_kelamin", "Jenis kelamin wajib dipilih.");
        errors.push("Jenis kelamin wajib dipilih.");
        if (!firstErrorEl) firstErrorEl = $("[name='jenis_kelamin']").first();
      }

      if (umur) {
        const umurInt = parseInt(umur, 10);
        if (isNaN(umurInt) || umurInt < 0 || umurInt > 150) {
          const $el = $("[name='umur']");
          setInvalid($el, "Umur harus antara 0 sampai 150.");
          errors.push("Umur harus antara 0 sampai 150.");
          if (!firstErrorEl) firstErrorEl = $el;
        }
      }

      if (telp) {
        const telpNum = telp.replace(/\D/g, "");
        if (telpNum.length < 6) {
          const $el = $("[name='telp']");
          setInvalid($el, "Nomor telepon terlalu pendek.");
          errors.push("Nomor telepon terlalu pendek.");
          if (!firstErrorEl) firstErrorEl = $el;
        }
      }

      if (hp) {
        const hpNum = hp.replace(/\D/g, "");
        if (hpNum.length < 8) {
          const $el = $("[name='hp']");
          setInvalid($el, "Nomor HP terlalu pendek.");
          errors.push("Nomor HP terlalu pendek.");
          if (!firstErrorEl) firstErrorEl = $el;
        }
      }

      if (!masalah1 && !nyeri && !diagnosis) {
        const $el = $("[name='masalah_1']");
        setInvalid(
          $el,
          "Isi minimal salah satu: masalah, nyeri, atau diagnosis.",
        );
        errors.push(
          "Minimal isi salah satu: masalah paliatif, nyeri, atau diagnosis.",
        );
        if (!firstErrorEl) firstErrorEl = $el;
      }

      if (!careGiver) {
        const $el = $("[name='care_giver']");
        setInvalid($el, "Nama care giver wajib diisi.");
        errors.push("Nama care giver wajib diisi.");
        if (!firstErrorEl) firstErrorEl = $el;
      }

      $(".symptom-check:checked").each(function () {
        const targetId = $(this).data("target");
        const $target = $("#" + targetId);

        if ($target.length && !$target.val().trim()) {
          setInvalid($target, "Keterangan symptom wajib diisi.");
          errors.push("Keterangan untuk symptom yang dicentang wajib diisi.");
          if (!firstErrorEl) firstErrorEl = $target;
        }
      });

      // const pemeriksaanxxx = [
      //   getVal("keadaan_umum"),
      //   getVal("bicara"),
      //   getVal("pucat"),
      //   getVal("jaundice"),
      //   getVal("oedema"),
      //   getVal("respiratory_system"),
      //   getVal("abdomen"),
      //   getVal("pr"),
      //   getVal("pv"),
      // ];

      const pemeriksaan = [
        getVal("keadaan_umum"),

        // Komunikasi kiri
        getVal("bicara"),
        getVal("pucat"),
        getVal("jaundice"),
        $("[name='cyanosis']:checked").val() || "",

        // Komunikasi tengah
        getVal("pendengaran"),
        getVal("hydration"),
        getVal("mouth"),
        $("[name='clubbing']:checked").val() || "",

        // Komunikasi kanan
        getVal("penglihatan"),
        getVal("skin"),
        getVal("sinus"),
        getVal("fistula"),
        getVal("dekubitus_exam"),

        // Pemeriksaan lanjutan
        getVal("oedema"),
        getVal("cardiovascular"),
        getVal("respiratory_system"),
        getVal("abdomen"),
        getVal("pr"),
        getVal("pv"),
      ];

      const hasPemeriksaan = pemeriksaan.some((v) => v !== "");
      if (!hasPemeriksaan) {
        errors.push("Minimal isi salah satu bagian examination / pemeriksaan.");
        if (!firstErrorEl) firstErrorEl = $("[name='keadaan_umum']");
      }

      if (errors.length > 0) {
        Swal.fire({
          icon: "warning",
          title: "Validasi Gagal",
          html: errors.map((e) => `• ${e}`).join("<br>"),
        }).then(() => {
          if (firstErrorEl && firstErrorEl.length) {
            $("html, body").animate(
              { scrollTop: firstErrorEl.offset().top - 150 },
              300,
            );
            firstErrorEl.focus();
          }
        });
        return false;
      }

      $("#canvas_lung_left, #canvas_lung_right, #canvas_abdomen").remove();

      $("<input>", {
        type: "hidden",
        id: "canvas_lung_left",
        name: "canvas_lung_left",
        value: getCanvasBase64("lung_left"),
      }).appendTo($formPaliatif);

      $("<input>", {
        type: "hidden",
        id: "canvas_lung_right",
        name: "canvas_lung_right",
        value: getCanvasBase64("lung_right"),
      }).appendTo($formPaliatif);

      $("<input>", {
        type: "hidden",
        id: "canvas_abdomen",
        name: "canvas_abdomen",
        value: getCanvasBase64("abdomen_canvas"),
      }).appendTo($formPaliatif);

      const formData = new FormData(this);
      const url =
        $formPaliatif.attr("action") || BASE_URL + "Asessment/savePaliatif";

      Swal.fire({
        title: "Simpan asesmen paliatif?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, simpan",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
          url: url,
          type: "POST",
          dataType: "json",
          data: formData,
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

            if (res && (res.success || res.status)) {
              Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.message || "Asesmen paliatif tersimpan.",
              }).then(() => {
                if (res.redirect) {
                  window.location.href = res.redirect;
                } else {
                  window.location.href = BASE_URL + "Asessment";
                }
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Gagal",
                text:
                  res && res.message ? res.message : "Gagal menyimpan data.",
              });
            }
          },
          error: function (xhr) {
            Swal.close();

            let msg = "Gagal menyimpan (AJAX error).";
            if (xhr.responseJSON && xhr.responseJSON.message) {
              msg = xhr.responseJSON.message;
            }

            Swal.fire({
              icon: "error",
              title: "Error",
              text: msg,
            });
          },
        });
      });
    });
  }

  $("#formAssessRad").on("submit", function (e) {
    let errors = [];
    let firstErrorEl = null;

    // --- Fungsi Helper ---
    function setError(element, message) {
      errors.push(message);
      element.addClass("is-invalid");
      if (!firstErrorEl) firstErrorEl = element;
    }

    function clearError(element) {
      element.removeClass("is-invalid");
    }

    // 1. Validasi Tanggal & Lokasi
    let tgl = $('input[name="tgl_pemeriksaan"]');
    let lok = $('input[name="lokasi"]');
    clearError(tgl);
    clearError(lok);
    if (tgl.val() === "") setError(tgl, "Tanggal pemeriksaan wajib diisi.");
    if (lok.val().trim() === "") setError(lok, "Lokasi wajib diisi.");

    // 2. Validasi Identitas Dasar (Nama, Umur, Alamat)
    let fields = ["nama", "nama_suami", "umur", "alamat", "telp"];
    fields.forEach(function (f) {
      let el = $(`input[name="${f}"]`);
      clearError(el);
      if (el.val().trim() === "")
        setError(el, "Identitas (" + f + ") wajib dilengkapi.");
    });

    // ==========================================
    // VALIDASI: JENIS KB
    // ==========================================
    let jenisKb = $('input[name="jenis_kb"]');
    let lamaKb = $('input[name="lama_kb"]');

    // Reset status error sebelumnya
    clearError(jenisKb);
    clearError(lamaKb);

    // Cek Konsistensi:
    if (jenisKb.val().trim() === "") {
      setError(jenisKb, "Jenis KB wajib diisi.");
    } else if (lamaKb.val().trim() === "") {
      setError(lamaKb, "Lama pemakaian KB wajib diisi.");
    }

    // Validasi TB/BB
    // let tb = $('input[name="tb"]');
    // // let tb = $('input[name="tb"]').val();
    // // alert("1111");
    // // alert(tb);
    // let bb = $('input[name="bb"]');
    // console.log("TB:", $(this).find('input[name="tb"]'));
    // console.log("BB:", $('input[name="bb"]').val());

    let tb = $(this).find('input[name="tb"]');
    let bb = $(this).find('input[name="bb"]');

    // let tbVal = $('input[name="tb"]').val();
    // let bbVal = $('input[name="bb"]').val();

    let tbVal = parseFloat(tb.val());
    let bbVal = parseFloat(bb.val());

    // clearError(tb);
    // clearError(bb);
    // if (tb.val().trim() === "") setError(tb, "Tinggi Badan wajib diisi.");
    // if (bb.val().trim() === "") setError(bb, "Berat Badan wajib diisi.");
    // clearError(tb);
    // clearError(bb);

    if (isNaN(tbVal)) setError(tb, "Tinggi Badan wajib diisi.");
    if (isNaN(bbVal)) setError(bb, "Berat Badan wajib diisi.");

    // 3. Validasi Fisik & KB (Contoh beberapa field wajib)
    let haid = $('input[name="haid_pertama"]');
    let jmlAnak = $('input[name="jumlah_anak"]');

    // Reset error sebelumnya
    clearError(haid);
    clearError(jmlAnak);

    // 1. Cek Usia Haid
    if (haid.val().trim() === "") {
      setError(haid, "Usia Haid Pertama wajib diisi.");
    }

    // 2. Cek Jumlah Anak
    if (jmlAnak.val().trim() === "") {
      setError(
        jmlAnak,
        "Jumlah Anak wajib diisi (isi '0' jika belum memiliki anak).",
      );
    }

    // ==========================================
    // VALIDASI: RIWAYAT KESEHATAN (WAJIB)
    // ==========================================
    // let riwayatKes = $('input[name="riwayat_kesehatan"]');

    // Reset error sebelumnya
    // clearError(riwayatKes);

    // Cek apakah kosong
    // if (riwayatKes.val().trim() === "") {
    //   setError(
    //     riwayatKes,
    //     "Riwayat Kesehatan wajib diisi (isi '-' jika tidak ada).",
    //   );
    // }

    // 4. Validasi Merokok Pasien

    $('input[name="merokok_pasien"]').change(function () {
      let inputJml = $('input[name="merokok_pasien_jml"]');

      if ($(this).val() === "Ya") {
        // Jika Ya: Enable input & arahkan kursor
        inputJml.prop("disabled", false).focus();
      } else {
        // Jika Tidak: Disable input, kosongkan isinya, hapus error merah
        inputJml.prop("disabled", true).val("").removeClass("is-invalid");
      }
    });

    $('input[name="merokok_pasien"]:checked').trigger("change");

    let rokokPasien = $('input[name="merokok_pasien"]:checked');
    let rokokPasienJml = $('input[name="merokok_pasien_jml"]');
    // Reset error sebelumnya
    clearError($('input[name="merokok_pasien"]').first()); // Reset error radio
    clearError(rokokPasienJml); // Reset error text

    // Cek apakah radio dipilih
    if (rokokPasien.length === 0) {
      setError(
        $('input[name="merokok_pasien"]').first(),
        "Status merokok pasien wajib dipilih (Ya/Tidak).",
      );
    }
    // Cek logika kondisional: Jika Ya, text wajib isi
    else if (rokokPasien.val() === "Ya" && rokokPasienJml.val().trim() === "") {
      setError(rokokPasienJml, "Jumlah rokok pasien wajib diisi.");
    }

    // 5. Validasi Merokok Suami
    $('input[name="merokok_suami"]').change(function () {
      let inputJml = $('input[name="merokok_suami_jml"]');

      if ($(this).val() === "Ya") {
        // Jika Ya: Enable input & fokus
        inputJml.prop("disabled", false).focus();
      } else {
        // Jika Tidak: Disable, kosongkan, hapus error
        inputJml.prop("disabled", true).val("").removeClass("is-invalid");
      }
    });

    $('input[name="merokok_suami"]:checked').trigger("change");

    let rokokSuami = $('input[name="merokok_suami"]:checked');
    let rokokSuamiJml = $('input[name="merokok_suami_jml"]');
    let radioSuamiEl = $('input[name="merokok_suami"]').first(); // Ambil elemen pertama untuk scroll error

    // Reset error sebelumnya
    clearError(radioSuamiEl);
    clearError(rokokSuamiJml);

    // 1. Cek apakah Radio dipilih?
    if (rokokSuami.length === 0) {
      setError(radioSuamiEl, "Status suami merokok wajib dipilih (Ya/Tidak).");
    }
    // 2. Jika Ya, Cek apakah jumlah diisi?
    else if (rokokSuami.val() === "Ya" && rokokSuamiJml.val().trim() === "") {
      setError(rokokSuamiJml, "Jumlah rokok suami wajib diisi.");
    }

    // ==========================================
    // INTERAKSI: KELUARGA SAKIT KANKER
    // ==========================================
    $('input[name="keluarga_sakit"]').change(function () {
      let val = $(this).val();
      let inputSiapa = $('input[name="keluarga_sakit_siapa"]');
      let inputJenis = $('input[name="jenis_kanker"]');

      if (val === "Ada") {
        // Jika Ada: Buka inputan
        inputSiapa.prop("disabled", false).focus();
        inputJenis.prop("disabled", false);
      } else {
        // Jika Tidak: Kunci inputan, kosongkan isinya, hapus error
        inputSiapa.prop("disabled", true).val("").removeClass("is-invalid");
        inputJenis.prop("disabled", true).val("").removeClass("is-invalid");
      }
    });

    // Trigger saat halaman dimuat (agar status tersimpan tetap sesuai)
    $('input[name="keluarga_sakit"]:checked').trigger("change");

    let kelSakit = $('input[name="keluarga_sakit"]:checked');
    let kelSiapa = $('input[name="keluarga_sakit_siapa"]');
    let kelJenis = $('input[name="jenis_kanker"]');

    // Element radio pertama untuk scroll jika error
    let kelSakitEl = $('input[name="keluarga_sakit"]').first();

    // Reset error sebelumnya
    clearError(kelSakitEl);
    clearError(kelSiapa);
    clearError(kelJenis);

    // 1. Cek apakah Radio dipilih?
    if (kelSakit.length === 0) {
      setError(
        kelSakitEl,
        "Status keluarga sakit kanker wajib dipilih (Ada/Tidak).",
      );
    }
    // 2. Jika Ada, Cek kelengkapannya
    else if (kelSakit.val() === "Ada") {
      // Validasi Siapa
      if (kelSiapa.val().trim() === "") {
        setError(kelSiapa, "Sebutkan siapa keluarga yang sakit kanker.");
      }
      // Validasi Jenis Kanker
      if (kelJenis.val().trim() === "") {
        setError(kelJenis, "Sebutkan jenis kankernya.");
      }
    }

    // ==========================================
    // VALIDASI: RIWAYAT KANKER KELUARGA (TEXT)
    // ==========================================
    let riwayatKanker = $('input[name="riwayat_kanker_keluarga"]');

    // Reset error
    clearError(riwayatKanker);

    // Cek apakah kosong
    if (riwayatKanker.val().trim() === "") {
      setError(
        riwayatKanker,
        "Riwayat Kanker dalam Keluarga wajib diisi (isi '-' atau 'Tidak Ada' jika kosong).",
      );
    }

    // 7. Validasi Keluhan Payudara (Minimal pilih 1)
    let keluhanCheck = $('input[name="keluhan_payudara[]"]');
    let keluhanLain = $('input[name="keluhan_lain"]');
    let isLainChecked = $(
      'input[name="keluhan_payudara[]"][value="Lain-lain"]',
    ).is(":checked");
    clearError(keluhanCheck);
    clearError(keluhanLain);

    if ($('input[name="keluhan_payudara[]"]:checked').length === 0) {
      errors.push("Pilih minimal satu keluhan payudara.");
      keluhanCheck.addClass("is-invalid");
      if (!firstErrorEl) firstErrorEl = keluhanCheck.first();
    }
    // Jika centang Lain-lain, text tidak boleh kosong
    if (isLainChecked && keluhanLain.val().trim() === "") {
      setError(keluhanLain, "Sebutkan keluhan lainnya.");
    }

    // 8. Validasi Tindakan / Informed Consent (Minimal pilih 1)
    let tindakanCheck = $('input[name="tindakan[]"]');
    let tindakanLain = $('input[name="tindakan_lain"]');

    // Reset error sebelumnya
    tindakanCheck.removeClass("is-invalid");
    clearError(tindakanLain);

    // Cek Kondisi: Apakah ada checkbox dipilih ATAU text lain diisi?
    let isChecked = $('input[name="tindakan[]"]:checked').length > 0;
    let isTextFilled = tindakanLain.val().trim() !== "";

    if (!isChecked && !isTextFilled) {
      // Tandai error pada checkbox pertama dan input text
      tindakanLain.addClass("is-invalid");
      setError(
        tindakanCheck.first(),
        "Pilih minimal satu Tindakan Medis atau isi 'Lainnya'.",
      );
    }

    // ==========================================
    // 🏁 FINAL CHECK
    // ==========================================
    if (errors.length > 0) {
      e.preventDefault();

      let errorList = errors.map((err) => `<li>${err}</li>`).join("");

      Swal.fire({
        icon: "warning",
        title: "Perhatian",
        html: `
            <div style="
                text-align: left; 
                font-size: 13px; 
                background-color: #f8d7da; 
                color: #721c24; 
                border: 1px solid #f5c6cb; 
                padding: 15px; 
                border-radius: 5px;">
                <strong style="display:block; margin-bottom:8px;">Data belum lengkap:</strong>
                <ul style="padding-left: 20px; margin: 0;">
                    ${errorList}
                </ul>
            </div>
        `,
        confirmButtonText: "Siap, Saya Lengkapi",
        confirmButtonColor: "#3085d6",
      }).then(() => {
        if (firstErrorEl) {
          $("html, body").animate(
            { scrollTop: firstErrorEl.offset().top - 150 },
            500,
          );
          firstErrorEl.focus();
        }
      });
    }
  });

  $("#formKajiAwal").on("submit", function (e) {
    // alert("123");

    e.preventDefault(); // SELALU kita handle sendiri

    let errors = [];
    let firstErrorEl = null;

    // ============================
    // 🔥 VALIDASI PEMERIKSAAN FISIK
    // ============================
    $.each(fisikLabels, function (name, label) {
      $(`input[name='${name}']`).removeClass("is-invalid");
    });

    $.each(fisikLabels, function (name, label) {
      let input = $(`input[name='${name}']`);
      let val = input.val().trim();

      if (val === "") {
        // Tambahkan pesan error spesifik
        errors.push(`• <b>${label}</b> belum diisi.`);

        // Highlight merah
        input.addClass("is-invalid");
        if (!firstErrorEl) firstErrorEl = input;
      }
    });

    // =============================
    // 🔥 Validasi NYERI
    // =============================
    if ($("input[name='nyeri_ada']:checked").val() === "Ya") {
      // Validasi metode nyeri
      let metodeNyeri = $("input[name='nyeri_metode']:checked");
      if (metodeNyeri.length === 0) {
        errors.push("Metode nyeri wajib dipilih");
        $("input[name='nyeri_metode']").addClass("is-invalid");
        if (!firstErrorEl)
          firstErrorEl = $("input[name='nyeri_metode']").first();
      } else {
        $("input[name='nyeri_metode']").removeClass("is-invalid");
      }

      // Validasi skor nyeri
      let nyeri_skor = $("#nyeri_skor");
      if (nyeri_skor.val().trim() === "") {
        errors.push("Skor nyeri wajib dipilih");
        nyeri_skor.addClass("is-invalid");
        if (!firstErrorEl) firstErrorEl = nyeri_skor;
      } else {
        nyeri_skor.removeClass("is-invalid");
      }
    } else {
      // Reset error jika Nyeri = Tidak
      $("input[name='nyeri_metode']").removeClass("is-invalid");
      $("#nyeri_skor").removeClass("is-invalid");
    }

    // =============================
    // 🔥 VALIDASI — STATUS MENTAL
    // =============================
    $("input[name='status_mental[]']").removeClass("is-invalid");

    let mentalChecked = $("input[name='status_mental[]']:checked");

    if (mentalChecked.length === 0) {
      errors.push("Pilih minimal 1 Status Mental.");
      $("input[name='status_mental[]']").addClass("is-invalid");

      if (!firstErrorEl)
        firstErrorEl = $("input[name='status_mental[]']").first();
    } else {
      $("input[name='status_mental[]']").removeClass("is-invalid");
    }

    // =============================
    // 🔥 VALIDASI — RESIKO JATUH (Morse)
    // =============================
    $(".morseOpt").removeClass("is-invalid");
    $("#morse_total").removeClass("is-invalid");

    let allMorseFilled = true;

    // cek setiap select morse
    $(".morseOpt").each(function () {
      if ($(this).val() === "" || $(this).val() === null) {
        allMorseFilled = false;
        $(this).addClass("is-invalid");

        if (!firstErrorEl) firstErrorEl = $(this);
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    // cek total skor harus ada
    if ($("#morse_total").val().trim() === "") {
      errors.push("Morse Fall Scale belum dihitung.");
      $("#morse_total").addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = $("#morse_total");
    }

    if (!allMorseFilled) {
      errors.push("Semua item pada Resiko Jatuh (Morse Scale) wajib diisi.");
    }

    // =============================
    // STOP SUBMIT + ARAHKAN FIELD
    // =============================
    if (errors.length > 0) {
      e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Validasi Gagal",
        html: errors.join("<br>"),
      }).then(() => {
        if (firstErrorEl) {
          $("html, body").animate(
            {
              scrollTop: firstErrorEl.offset().top - 150,
            },
            300,
          );
          firstErrorEl.focus();
        }
      });
      return false;
    }

    // =============================
    // TIDAK ADA ERROR → SIMPAN VIA AJAX
    // =============================
    const $form = $(this);
    const formData = $form.serialize(); // semua field form
    const poliId = $form.find("input[name='poli_id']").val() || "";
    const PALIATIF_ID = "POLI0000000001";

    console.log("qqqqqq");
    console.log(formData);
    // alert("123");
    // alert(PALIATIF_ID);
    // optional: disable tombol submit biar ga double klik
    const $btnSubmit = $form.find("button[type='submit']");
    $btnSubmit.prop("disabled", true).text("Menyimpan...");

    $.ajax({
      url: $form.attr("action"),
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (res) {
        if (!res || !res.success) {
          Swal.fire({
            icon: "error",
            title: "Gagal",
            text:
              res && res.message ? res.message : "Gagal menyimpan pengkajian.",
          });
          return;
        }
        // ========== BERHASIL SIMPAN ==========
        const episodeId = res.episode_id;
        const pasienId = res.pasien_id;

        // Kalau BUKAN poli paliatif → langsung info sukses + kembali
        if (poliId !== PALIATIF_ID) {
          Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: res.message || "Pengkajian berhasil disimpan.",
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            // balik ke list asesment / kemana pun Mas Prima mau
            window.location.href = BASE_URL + "Asessment";
          });
          return;
        }

        // Kalau POLI PALIATIF → tawarkan isi assesment paliatif
        Swal.fire({
          title: "Pengkajian awal tersimpan",
          text: "Pasien berasal dari Poli Paliatif. Isi asesmen paliatif sekarang?",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Ya, isi sekarang",
          cancelButtonText: "Nanti saja",
        }).then((result) => {
          if (result.isConfirmed) {
            // Redirect ke halaman form asesmen paliatif
            window.location.href =
              BASE_URL +
              "Asessment/paliatifForm" +
              "?episode_id=" +
              encodeURIComponent(episodeId) +
              "&pasien_id=" +
              encodeURIComponent(pasienId);
          } else {
            Swal.fire({
              icon: "success",
              title: "Tersimpan",
              text: "Pengkajian awal sudah tersimpan.",
              timer: 2000,
              showConfirmButton: false,
            }).then(() => {
              window.location.href = BASE_URL + "Asessment";
            });
          }
        });
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Terjadi kesalahan saat menyimpan pengkajian (AJAX error).",
        });
      },
      complete: function () {
        $btnSubmit.prop("disabled", false).text("Simpan Pengkajian");
      },
    });
  });

  $("#formKajiAwalFull").on("submit", function (e) {
    e.preventDefault(); // SELALU kita handle sendiri

    let errors = [];
    let firstErrorEl = null; // utk menyimpan elemen error pertama

    // =============================
    // 🔥 VALIDASI: DIPEROLEH DARI
    // =============================
    $("input[name='diperoleh_dari[]']").removeClass("is-invalid");
    let diperoleh = $("input[name='diperoleh_dari[]']:checked");

    if (diperoleh.length === 0) {
      errors.push("Pilih minimal satu sumber data 'Diperoleh dari'.");
      $("input[name='diperoleh_dari[]']").addClass("is-invalid");

      if (!firstErrorEl)
        firstErrorEl = $("input[name='diperoleh_dari[]']").first();
    } else {
      $("input[name='diperoleh_dari[]']").removeClass("is-invalid");
    }

    // =====================================
    // 🔥 VALIDASI HUBUNGAN DENGAN PASIEN
    // =====================================
    $("#hub_dengan_pasien").removeClass("is-invalid");
    $("#hub_dengan_pasien_lain").removeClass("is-invalid");

    let hubSelect = $("#hub_dengan_pasien");
    let hubVal = hubSelect.val();

    // Wajib pilih salah satu
    if (hubVal === "") {
      errors.push("Hubungan dengan pasien wajib dipilih.");
      hubSelect.addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = hubSelect;
    }

    // Jika pilih LAINNYA → wajib isi textbox
    if (hubVal === "LAINNYA") {
      let lainTxt = $("#hub_dengan_pasien_lain");
      if (lainTxt.val().trim() === "") {
        errors.push("Keterangan hubungan (Lainnya) wajib diisi.");
        lainTxt.addClass("is-invalid");

        if (!firstErrorEl) firstErrorEl = lainTxt;
      } else {
        lainTxt.removeClass("is-invalid");
      }
    }

    // =============================
    // 🔥 VALIDASI: CARA MASUK
    // =============================
    $("input[name='cara_masuk[]']").removeClass("is-invalid");

    let caraMasuk = $("input[name='cara_masuk[]']:checked");

    if (caraMasuk.length === 0) {
      errors.push("Pilih minimal satu 'Cara Masuk' pasien.");
      $("input[name='cara_masuk[]']").addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = $("input[name='cara_masuk[]']").first();
    } else {
      $("input[name='cara_masuk[]']").removeClass("is-invalid");
    }

    // ============================
    // 🔥 Validasi Keluhan Utama
    // ============================
    let keluhan = $("input[name='keluhan_utama']");
    if (keluhan.val().trim() === "") {
      errors.push("Keluhan utama wajib diisi");
      // highlight merah
      keluhan.addClass("is-invalid");
      if (!firstErrorEl) firstErrorEl = keluhan;
    } else {
      // jika sudah terisi, hapus merah
      keluhan.removeClass("is-invalid");
    }

    // ============================
    // 🔥 VALIDASI PEMERIKSAAN FISIK
    // ============================
    $.each(fisikLabels, function (name, label) {
      $(`input[name='${name}']`).removeClass("is-invalid");
    });

    $.each(fisikLabels, function (name, label) {
      let input = $(`input[name='${name}']`);
      let val = input.val().trim();

      if (val === "") {
        // Tambahkan pesan error spesifik
        errors.push(`• <b>${label}</b> belum diisi.`);

        // Highlight merah
        input.addClass("is-invalid");
        if (!firstErrorEl) firstErrorEl = input;
      }
    });

    // =============================
    // 🔥 VALIDASI — RESIKO JATUH (Morse)
    // =============================
    $(".morseOpt").removeClass("is-invalid");
    $("#morse_total").removeClass("is-invalid");

    let allMorseFilled = true;

    // cek setiap select morse
    $(".morseOpt").each(function () {
      if ($(this).val() === "" || $(this).val() === null) {
        allMorseFilled = false;
        $(this).addClass("is-invalid");

        if (!firstErrorEl) firstErrorEl = $(this);
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    // cek total skor harus ada
    if ($("#morse_total").val().trim() === "") {
      errors.push("Morse Fall Scale belum dihitung.");
      $("#morse_total").addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = $("#morse_total");
    }

    if (!allMorseFilled) {
      errors.push("Semua item pada Resiko Jatuh (Morse Scale) wajib diisi.");
    }

    // =============================
    // 🔥 VALIDASI — STATUS FUNGSIONAL
    // =============================
    $("input[name='status_fungsional']").removeClass("is-invalid");

    let fungsional = $("input[name='status_fungsional']:checked");
    if (fungsional.length === 0) {
      errors.push("Status fungsional wajib dipilih.");

      $("input[name='status_fungsional']").addClass("is-invalid");

      if (!firstErrorEl)
        firstErrorEl = $("input[name='status_fungsional']").first();
    } else {
      $("input[name='status_fungsional']").removeClass("is-invalid");
    }

    // =============================
    // 🔥 VALIDASI — STATUS MENTAL
    // =============================
    $("input[name='status_mental[]']").removeClass("is-invalid");

    let mentalChecked = $("input[name='status_mental[]']:checked");

    if (mentalChecked.length === 0) {
      errors.push("Pilih minimal 1 Status Mental.");
      $("input[name='status_mental[]']").addClass("is-invalid");

      if (!firstErrorEl)
        firstErrorEl = $("input[name='status_mental[]']").first();
    } else {
      $("input[name='status_mental[]']").removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi NYERI
    // =============================
    if ($("input[name='nyeri_ada']:checked").val() === "Ya") {
      // Validasi metode nyeri
      let metodeNyeri = $("input[name='nyeri_metode']:checked");
      if (metodeNyeri.length === 0) {
        errors.push("Metode nyeri wajib dipilih");
        $("input[name='nyeri_metode']").addClass("is-invalid");
        if (!firstErrorEl)
          firstErrorEl = $("input[name='nyeri_metode']").first();
      } else {
        $("input[name='nyeri_metode']").removeClass("is-invalid");
      }

      // Validasi skor nyeri
      let nyeri_skor = $("#nyeri_skor");
      if (nyeri_skor.val().trim() === "") {
        errors.push("Skor nyeri wajib dipilih");
        nyeri_skor.addClass("is-invalid");
        if (!firstErrorEl) firstErrorEl = nyeri_skor;
      } else {
        nyeri_skor.removeClass("is-invalid");
      }
    } else {
      // Reset error jika Nyeri = Tidak
      $("input[name='nyeri_metode']").removeClass("is-invalid");
      $("#nyeri_skor").removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi GIZI
    // =============================
    if ($("input[name='gizi_masalah']:checked").val() === "Ya") {
      // Minimal 1 checklist dipilih
      let giziDetail = $("input[name='gizi_detail[]']:checked");
      if (giziDetail.length === 0) {
        errors.push("Pilih minimal 1 masalah gizi.");
        $("input[name='gizi_detail[]']").addClass("is-invalid");
        if (!firstErrorEl)
          firstErrorEl = $("input[name='gizi_detail[]']").first();
      } else {
        $("input[name='gizi_detail[]']").removeClass("is-invalid");
      }

      // Jika "Lainnya" → wajib isi
      if ($(".giziLainnyaCheck").is(":checked")) {
        let lainInput = $(".giziLainnyaInput");
        if (lainInput.val().trim() === "") {
          errors.push("Keterangan masalah gizi (Lainnya) wajib diisi.");
          lainInput.addClass("is-invalid");
          if (!firstErrorEl) firstErrorEl = lainInput;
        } else {
          lainInput.removeClass("is-invalid");
        }
      } else {
        $(".giziLainnyaInput").removeClass("is-invalid");
      }
    } else {
      $("input[name='gizi_detail[]']").removeClass("is-invalid");
      $(".giziLainnyaInput").removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi BICARA
    // =============================
    if ($("input[name='edk_bicara']:checked").val() === "Tidak normal") {
      let bicaraKet = $("input[name='edk_bicara_ket']");
      if (bicaraKet.val().trim() === "") {
        errors.push("Keterangan bicara tidak normal wajib diisi");
        bicaraKet.addClass("is-invalid");
        if (!firstErrorEl) firstErrorEl = bicaraKet;
      } else {
        bicaraKet.removeClass("is-invalid");
      }
    } else {
      $("input[name='edk_bicara_ket']").removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi BAHASA SEHARI-HARI
    // =============================
    let bahasa = $("input[name='edk_bahasa']");
    bahasa.removeClass("is-invalid");

    if (bahasa.val().trim() === "") {
      errors.push("Bahasa sehari-hari wajib diisi.");
      bahasa.addClass("is-invalid");
      if (!firstErrorEl) firstErrorEl = bahasa;
    }

    // =============================
    // 🔥 Validasi PENERJEMAH
    // =============================
    let penerjemah = $("input[name='edk_penerjemah']:checked").val();
    let penerjemahBahasa = $("input[name='edk_penerjemah_bahasa']");

    $(
      "input[name='edk_penerjemah'], input[name='edk_penerjemah_bahasa']",
    ).removeClass("is-invalid");

    // Jika pilih YA → wajib isi bahasa penerjemah
    if (penerjemah === "Ya") {
      if (penerjemahBahasa.val().trim() === "") {
        errors.push("Bahasa penerjemah wajib diisi.");
        penerjemahBahasa.addClass("is-invalid");
        if (!firstErrorEl) firstErrorEl = penerjemahBahasa;
      } else {
        penerjemahBahasa.removeClass("is-invalid");
      }
    } else {
      // Reset jika pilih Tidak
      penerjemahBahasa.removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi METODE BELAJAR
    // =============================
    // let metode = $("input[name='edk_metode']");
    // metode.removeClass("is-invalid");

    // if (metode.val().trim() === "") {
    //   errors.push("Metode belajar yang disukai wajib diisi.");
    //   metode.addClass("is-invalid");
    //   if (!firstErrorEl) firstErrorEl = metode;
    // }

    // =============================
    // 🔥 Validasi HAMBATAN EDUKASI
    // =============================
    if ($("input[name='edk_hambatan']:checked").val() === "Ada") {
      let hambatanList = $("input[name='edk_hambatan_list[]']:checked");
      if (hambatanList.length === 0) {
        errors.push("Pilih minimal 1 hambatan edukasi");
        $("input[name='edk_hambatan_list[]']").addClass("is-invalid");
        if (!firstErrorEl)
          firstErrorEl = $("input[name='edk_hambatan_list[]']").first();
      } else {
        $("input[name='edk_hambatan_list[]']").removeClass("is-invalid");
      }
    }

    $("input[name='edk_kebutuhan[]']").removeClass("is-invalid");
    $(".edkKebutuhanLainInput").removeClass("is-invalid");

    let kebutuhanChecked = $("input[name='edk_kebutuhan[]']:checked");
    let lainChecked = $(".edkLainCheck").is(":checked");
    let lainInput = $(".edkKebutuhanLainInput");

    // RESET STATUS INVALID
    $("input[name='edk_kebutuhan[]']").removeClass("is-invalid");
    lainInput.removeClass("is-invalid");

    // === 1. Jika "Lainnya" dicentang ===
    if (lainChecked) {
      if (lainInput.val().trim() === "") {
        // Lainnya Wajib Diisi
        errors.push("Keterangan edukasi (Lainnya) wajib diisi.");
        lainInput.addClass("is-invalid");

        if (!firstErrorEl) firstErrorEl = lainInput;
      } else {
        // Lainnya dianggap valid → tidak perlu checkbox lain
        lainInput.removeClass("is-invalid");
      }
    } else {
      // === 2. Jika “Lainnya” TIDAK dicentang → wajib pilih minimal 1 dari checkbox lain ===
      if (kebutuhanChecked.length === 0) {
        errors.push("Pilih minimal 1 edukasi kesehatan yang dibutuhkan.");
        $("input[name='edk_kebutuhan[]']").addClass("is-invalid");

        if (!firstErrorEl)
          firstErrorEl = $("input[name='edk_kebutuhan[]']").first();
      } else {
        $("input[name='edk_kebutuhan[]']").removeClass("is-invalid");
      }
    }

    // =============================
    // 🔥 Validasi SOSIAL BUDAYA
    // =============================

    // --- Agama ---
    let agama = $("input[name='sos_agama']:checked");
    if (agama.length === 0) {
      errors.push("Agama wajib dipilih");
      $("input[name='sos_agama']").addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = $("input[name='sos_agama']").first();
    } else {
      $("input[name='sos_agama']").removeClass("is-invalid");
    }

    // --- Pendidikan ---
    let pendidikan = $("input[name='sos_pendidikan']:checked");

    if (pendidikan.length === 0) {
      errors.push("Pendidikan pasien wajib dipilih");
      $("input[name='sos_pendidikan']").addClass("is-invalid");

      if (!firstErrorEl)
        firstErrorEl = $("input[name='sos_pendidikan']").first();
    } else {
      $("input[name='sos_pendidikan']").removeClass("is-invalid");

      if (pendidikan.val() === "Lainnya") {
        let pendLain = $("input[name='sos_pendidikan_lain']");
        if (pendLain.val().trim() === "") {
          errors.push("Keterangan pendidikan (Lainnya) wajib diisi");
          pendLain.addClass("is-invalid");

          if (!firstErrorEl) firstErrorEl = pendLain;
        } else {
          pendLain.removeClass("is-invalid");
        }
      } else {
        $("input[name='sos_pendidikan_lain']").removeClass("is-invalid");
      }
    }

    // --- Pekerjaan ---
    let kerja = $("input[name='sos_kerja']:checked");

    if (kerja.length === 0) {
      errors.push("Pekerjaan wajib dipilih");
      $("input[name='sos_kerja']").addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = $("input[name='sos_kerja']").first();
    } else {
      $("input[name='sos_kerja']").removeClass("is-invalid");

      if (kerja.val() === "Lainnya") {
        let kerjaLain = $("input[name='sos_kerja_lain']");
        if (kerjaLain.val().trim() === "") {
          errors.push("Keterangan pekerjaan (Lainnya) wajib diisi");
          kerjaLain.addClass("is-invalid");

          if (!firstErrorEl) firstErrorEl = kerjaLain;
        } else {
          kerjaLain.removeClass("is-invalid");
        }
      } else {
        $("input[name='sos_kerja_lain']").removeClass("is-invalid");
      }
    }

    // --- Suku ---
    let suku = $("input[name='sos_suku']");
    if (suku.val().trim() === "") {
      errors.push("Suku wajib diisi");
      suku.addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = suku;
    } else {
      suku.removeClass("is-invalid");
    }

    // --- Kewarganegaraan ---
    let warga = $("input[name='sos_warga']");
    if (warga.val().trim() === "") {
      errors.push("Kewarganegaraan wajib diisi");
      warga.addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = warga;
    } else {
      warga.removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi RESPON EMOSI
    // =============================
    let emosi = $("input[name='emosi[]']:checked");
    $("input[name='emosi[]']").removeClass("is-invalid");

    if (emosi.length === 0) {
      errors.push("Pilih minimal 1 respon emosi.");
      $("input[name='emosi[]']").addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = $("input[name='emosi[]']").first();
    }

    // =============================
    // 🔥 Validasi DAFTAR MASALAH KEPERAWATAN
    // =============================
    let masalah = $("input[name='masalah_keperawatan[]']:checked");
    let masalahTambahan = $("input[name='masalah_tambahan[]']")
      .map(function () {
        return $(this).val().trim();
      })
      .get()
      .filter((v) => v !== "");

    $(
      "input[name='masalah_keperawatan[]'], input[name='masalah_tambahan[]']",
    ).removeClass("is-invalid");

    if (masalah.length === 0 && masalahTambahan.length === 0) {
      errors.push("Pilih atau isi minimal 1 masalah keperawatan.");

      $("input[name='masalah_keperawatan[]']").addClass("is-invalid");
      $("input[name='masalah_tambahan[]']").addClass("is-invalid");

      if (!firstErrorEl)
        firstErrorEl = $("input[name='masalah_keperawatan[]']").first();
    }

    // =============================
    // 🔥 Validasi RENCANA KEPERAWATAN (textarea wajib)
    // =============================
    let rencana = $("textarea[name='rencana_keperawatan']");
    if (rencana.val().trim() === "") {
      errors.push("Rencana keperawatan wajib diisi.");
      rencana.addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = rencana;
    } else {
      rencana.removeClass("is-invalid");
    }

    // =============================
    // 🔥 Validasi KOLABORASI (WAJIB)
    // =============================
    let kolab = $("input[name='kolaborasi[]']:checked");
    let kolabKet = $("input[name='kolaborasi_ket']");

    // Reset dulu
    $("input[name='kolaborasi[]']").removeClass("is-invalid");
    kolabKet.removeClass("is-invalid");

    // ------ WAJIB minimal 1 kolaborasi ------
    if (kolab.length === 0) {
      errors.push("Pilih minimal 1 tindakan kolaborasi.");

      $("input[name='kolaborasi[]']").addClass("is-invalid");

      // arahkan fokus ke checkbox pertama
      if (!firstErrorEl) firstErrorEl = $("input[name='kolaborasi[]']").first();
    } else {
      $("input[name='kolaborasi[]']").removeClass("is-invalid");
    }

    // ------ Jika ada keterangan tapi tidak pilih tindakan (optional validasi tambahan) ------
    if (kolabKet.val().trim() !== "" && kolab.length === 0) {
      kolabKet.addClass("is-invalid");

      if (!firstErrorEl) firstErrorEl = kolabKet;
    } else {
      kolabKet.removeClass("is-invalid");
    }

    // =============================
    // STOP SUBMIT + ARAHKAN FIELD
    // =============================
    if (errors.length > 0) {
      // e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Validasi Gagal",
        html: errors.join("<br>"),
      }).then(() => {
        if (firstErrorEl) {
          $("html, body").animate(
            {
              scrollTop: firstErrorEl.offset().top - 150,
            },
            300,
          );
          firstErrorEl.focus();
        }
      });
      return false;
    }

    // =============================
    // TIDAK ADA ERROR → SIMPAN VIA AJAX
    // =============================

    const $form = $(this);
    const formData = $form.serialize(); // semua field form
    const poliId = $form.find("input[name='poli_id']").val() || "";
    const PALIATIF_ID = "POLI0000000001";

    // optional: disable tombol submit biar ga double klik
    const $btnSubmit = $form.find("button[type='submit']");
    $btnSubmit.prop("disabled", true).text("Menyimpan...");

    $.ajax({
      url: $form.attr("action"),
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (res) {
        if (!res || !res.success) {
          Swal.fire({
            icon: "error",
            title: "Gagal",
            text:
              res && res.message ? res.message : "Gagal menyimpan pengkajian.",
          });
          return;
        }
        // ========== BERHASIL SIMPAN ==========
        const episodeId = res.episode_id;
        const pasienId = res.pasien_id;

        // Kalau BUKAN poli paliatif → langsung info sukses + kembali
        if (poliId !== PALIATIF_ID) {
          Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: res.message || "Pengkajian berhasil disimpan.",
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            // balik ke list asesment / kemana pun Mas Prima mau
            window.location.href = BASE_URL + "Asessment";
          });
          return;
        }

        // Kalau POLI PALIATIF → tawarkan isi assesment paliatif
        Swal.fire({
          title: "Pengkajian awal tersimpan",
          text: "Pasien berasal dari Poli Paliatif. Isi asesmen paliatif sekarang?",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Ya, isi sekarang",
          cancelButtonText: "Nanti saja",
        }).then((result) => {
          if (result.isConfirmed) {
            // Redirect ke halaman form asesmen paliatif
            window.location.href =
              BASE_URL +
              "Asessment/paliatifForm" +
              "?episode_id=" +
              encodeURIComponent(episodeId) +
              "&pasien_id=" +
              encodeURIComponent(pasienId);
          } else {
            Swal.fire({
              icon: "success",
              title: "Tersimpan",
              text: "Pengkajian awal sudah tersimpan.",
              timer: 2000,
              showConfirmButton: false,
            }).then(() => {
              window.location.href = BASE_URL + "Asessment";
            });
          }
        });
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Terjadi kesalahan saat menyimpan pengkajian (AJAX error).",
        });
      },
      complete: function () {
        $btnSubmit.prop("disabled", false).text("Simpan Pengkajian");
      },
    });
  });

  // OLD
  // document.getElementById("label_tgljam").innerText = label;
  // document.getElementById("perawat_tgljam").value = label;

  // UPDATE
  const lbl = document.getElementById("label_tgljam");
  if (lbl) lbl.innerText = label;
  const perawatTgljam = document.getElementById("perawat_tgljam");
  if (perawatTgljam) perawatTgljam.value = label;

  $(document).on("click", ".btnEditAsesmen", function () {
    let episode = $(this).data("episode");
    let pasien = $(this).data("pasien");
    let poli = $(this).data("poli");
    let layan = $(this).data("layanan"); // array layanan APS
    let jml_kunj = $(this).data("kunj");
    // kategori_id;
    // POLI APS → tentukan jenis pemeriksaan
    if (poli === "APS") {
      // console.log(layan);
      // alert(layan.length);
      if (layan && layan.length > 0) {
        for (let i = 0; i < layan.length; i++) {
          let id = layan[i].kategori_id;
          // alert(id);
          if (id === "JKL-LAB") {
            window.location.href =
              BASE_URL +
              `AsessmentController/editAssesLab/${episode}/${pasien}`;
            return;
          }
          if (id === "JKL-RAD") {
            window.location.href =
              BASE_URL +
              `AsessmentController/editAssesRad/${episode}/${pasien}`;
            return;
          }
          if (id === "TDK000000000002") {
            window.location.href =
              BASE_URL +
              `AsessmentController/editMammoUsg/${episode}/${pasien}?jenis=usg`;
            return;
          }
        }
      }
      Swal.fire({
        icon: "warning",
        text: "Tidak ditemukan tipe pemeriksaan APS.",
      });
      return;
    }

    // POLI BIASA → kembali ke Form Pengkajian Awal
    window.location.href =
      BASE_URL +
      `AsessmentController/editPengkajianAwal/${episode}/${pasien}/${jml_kunj}/${poli}`;
  });

  $("#btnUpdateAssessLab").click(function () {
    Swal.fire({
      title: "Yakin mengupdate data?",
      text: "Pastikan data sudah benar sebelum disimpan.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Update!",
      cancelButtonText: "Batal",
    }).then((res) => {
      if (res.isConfirmed) {
        $("#formAssessLabEdit").submit();
      }
    });
  });

  $("#btnUpdateAssessRad").click(function () {
    Swal.fire({
      title: "Yakin mengupdate data?",
      text: "Pastikan data sudah benar sebelum disimpan.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Update!",
      cancelButtonText: "Batal",
    }).then((res) => {
      if (res.isConfirmed) {
        $("#formAssessRadEdit").submit();
      }
    });
  });

  $("#btnUpdateKajiAwal").click(function () {
    Swal.fire({
      title: "Yakin mengupdate data?",
      text: "Pastikan data sudah benar sebelum disimpan.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Update!",
      cancelButtonText: "Batal",
    }).then((res) => {
      if (res.isConfirmed) {
        $("#formKajiAwalFullEdit").submit();
      }
    });
  });

  $('input[name="fl_kb_status"]').change(function () {
    if ($(this).val() === "Pernah") {
      $("#fl_box_kb_riwayat").removeClass("d-none").addClass("d-flex");
    } else {
      $("#fl_box_kb_riwayat").addClass("d-none").removeClass("d-flex");
      $('input[name="fl_kb_riwayat[]"]').prop("checked", false);
    }
  });

  // ---- KB Saat Ini: Tidak Pakai eksklusif
  $('input[name="fl_kb_now[]"]').on("change", function () {
    if ($(this).val() === "Tidak Pakai" && this.checked) {
      // alert("11");
      $('input[name="fl_kb_now[]"]').not(this).prop("checked", false);
    } else if ($(this).val() !== "Tidak Pakai") {
      $('input[name="fl_kb_now[]"][value="Tidak Pakai"]').prop(
        "checked",
        false,
      );
    }
  });

  // Trigger saat load (untuk edit mode)
  $('input[name="fl_kb_status"]:checked').trigger("change");

  // --- 2. LOGIKA KEGUGURAN ---
  // Toggle gugur
  $("#fl_cek_gugur").change(function () {
    if ($(this).is(":checked")) {
      $("#fl_box_gugur").removeClass("d-none").addClass("d-inline-block"); // tampil sejajar
      $("#fl_jml_gugur").focus();
    } else {
      $("#fl_box_gugur").addClass("d-none").removeClass("d-inline-block"); // sembunyikan
      $("#fl_jml_gugur").val("");
    }
  });

  // Jalankan sekali saat halaman dimuat
  if ($("#fl_cek_gugur").is(":checked")) {
    $("#fl_box_gugur").removeClass("d-none").addClass("d-inline-block");
  }

  // --- 3. LOGIKA PAP SMEAR ---
  $('input[name="fl_pap_status"]').change(function () {
    if ($(this).val() === "Pernah") {
      // tampilkan box tahun
      $("#fl_box_pap_thn")
        .removeClass("d-none")
        .css("display", "flex")
        .hide()
        .fadeIn();
    } else {
      // sembunyikan dan kosongkan input
      $("#fl_box_pap_thn").fadeOut(function () {
        $(this).addClass("d-none");
      });

      $("#fl_pap_thn").val("");
    }
  });
  $('input[name="fl_pap_status"]:checked').trigger("change");

  // --- 4. LOGIKA IVA ---
  $('input[name="fl_iva_status"]').change(function () {
    if ($(this).val() === "Pernah") {
      // Tampilkan sebagai FLEX supaya sejajar
      $("#fl_box_iva_thn").removeClass("d-none").css("display", "flex");
      $("#fl_iva_thn").focus();
    } else {
      // Sembunyikan & kosongkan input
      $("#fl_box_iva_thn").addClass("d-none").hide();
      $("#fl_iva_thn").val("");
    }
  });

  // Jalankan saat load (untuk mode edit)
  $('input[name="fl_iva_status"]:checked').trigger("change");

  // --- 5. LOGIKA MEROKOK (Pasien, Suami, Keluarga) ---
  // Class .fl-rokok-group dipasang di semua radio merokok
  // Script ini sudah ada di code sebelumnya dan akan otomatis bekerja:
  // Menggunakan .prop('checked') untuk memastikan trigger pada mode edit berfungsi.

  $(".fl-rokok-group").change(function () {
    let targetBox = $(this).data("target");
    let val = $(this).val();

    if (val === "Ya" || val === "Pernah") {
      $(targetBox).removeClass("d-none").addClass("d-inline-block");
    } else {
      $(targetBox).addClass("d-none").removeClass("d-inline-block");
      $(targetBox).find("input").val("");
    }
  });

  // Jalankan hanya untuk radio yang sudah checked
  $(".fl-rokok-group:checked").each(function () {
    $(this).trigger("change");
  });

  // --- 6. LOGIKA KANKER KELUARGA (Poin a) ---
  $('input[name="fl_kel_kanker"]').change(function () {
    if ($(this).val() === "Ada") {
      $("#fl_box_kel_detail").removeClass("d-none");
      // Fokus ke input jenis kanker agar user aware
      $("#fl_kel_jenis").focus();
    } else {
      $("#fl_box_kel_detail").addClass("d-none");
      // Reset nilai child jika di-hide
      $('#fl_box_kel_detail input[type="text"]').val("");
      $('#fl_box_kel_detail input[type="checkbox"]').prop("checked", false);
    }
  });
  $('input[name="fl_kel_kanker"]:checked').trigger("change");

  // --- 7. LOGIKA KANKER KLIEN (Poin b) ---
  $('input[name="fl_klien_kanker"]').change(function () {
    if ($(this).val() === "Ada") {
      // Gunakan d-flex agar sejajar dengan radio button
      $("#fl_box_klien_jenis").removeClass("d-none").addClass("d-flex");
      $("#fl_klien_jenis").focus();
    } else {
      $("#fl_box_klien_jenis").removeClass("d-flex").addClass("d-none");
      $("#fl_klien_jenis").val("");
    }
  });
  $('input[name="fl_klien_kanker"]:checked').trigger("change");

  // --- 8. LOGIKA KELUHAN (Poin a - e) ---
  // Menggunakan class .fl-keluhan-group agar berlaku untuk semua baris keluhan
  $(".fl-keluhan-group").change(function () {
    let targetBox = $(this).data("target");
    let val = $(this).val();

    if (val === "Ada") {
      // Tampilkan dengan d-flex agar teks dan input sejajar
      $(targetBox).removeClass("d-none").addClass("d-flex");
      $(targetBox).find("input").focus();
    } else {
      $(targetBox).addClass("d-none").removeClass("d-flex");
      $(targetBox).find("input").val("");
    }
  });
  // Trigger saat load untuk handle mode edit
  $(".fl-keluhan-group:checked").trigger("change");

  // --- LOGIKA SHOW/HIDE PEMERIKSAAN ---
  function toggleFlex(trigger, target) {
    $(trigger).change(function () {
      if ($(this).val() === "Ya") {
        $(target).removeClass("d-none").addClass("d-flex");
      } else {
        $(target).removeClass("d-flex").addClass("d-none");
        $(target).find("input").val("");
      }
    });
    $(trigger + ":checked").trigger("change");
  }

  toggleFlex(".fl-toggle-pap", "#fl_box_pap_detail");
  toggleFlex(".fl-toggle-hpv", "#fl_box_hpv_detail");
  toggleFlex(".fl-toggle-foto", "#fl_box_foto_detail");

  // Logika Hasil IVA (Checkbox)
  $(".fl-toggle-radang").change(function () {
    if ($(this).is(":checked")) {
      $("#fl_box_radang, #fl_box_radang_tindak").removeClass("d-none");
    } else {
      $("#fl_box_radang, #fl_box_radang_tindak").addClass("d-none");
    }
  });
  $(".fl-toggle-positif").change(function () {
    if ($(this).is(":checked")) {
      $("#fl_box_positif").removeClass("d-none");
    } else {
      $("#fl_box_positif").addClass("d-none");
    }
  });

  // --- LOGIKA CANVAS DRAWING ---
  const canvas = document.getElementById("cervixCanvas");
  if (canvas) {
    const ctx = canvas.getContext("2d");
    let isDrawing = false;
    let history = []; // Untuk fitur Undo

    // Simpan state awal (kosong)
    function saveState() {
      if (history.length > 10) history.shift(); // Batasi history
      history.push(canvas.toDataURL());
      // Simpan ke hidden input setiap ada perubahan
      $("#fl_gambar_serviks").val(canvas.toDataURL());
    }

    // Event Mouse
    canvas.addEventListener("mousedown", startDraw);
    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", stopDraw);
    canvas.addEventListener("mouseout", stopDraw);

    // Event Touch (HP/Tablet)
    canvas.addEventListener("touchstart", (e) => {
      e.preventDefault(); // Cegah scroll
      startDraw(e.touches[0]);
    });
    canvas.addEventListener("touchmove", (e) => {
      e.preventDefault();
      draw(e.touches[0]);
    });
    canvas.addEventListener("touchend", stopDraw);

    function startDraw(e) {
      isDrawing = true;
      ctx.beginPath();
      ctx.lineWidth = 2;
      ctx.lineCap = "round";
      ctx.strokeStyle = "#ff0000"; // Warna merah untuk lesi

      // Posisi relatif terhadap canvas
      const rect = canvas.getBoundingClientRect();
      const x = (e.clientX || e.pageX) - rect.left;
      const y = (e.clientY || e.pageY) - rect.top;
      ctx.moveTo(x, y);
    }

    function draw(e) {
      if (!isDrawing) return;
      const rect = canvas.getBoundingClientRect();
      const x = (e.clientX || e.pageX) - rect.left;
      const y = (e.clientY || e.pageY) - rect.top;
      ctx.lineTo(x, y);
      ctx.stroke();
    }

    function stopDraw() {
      if (isDrawing) {
        isDrawing = false;
        saveState();
      }
    }

    // Tombol Clear
    $("#clearCanvas").click(function () {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      $("#fl_gambar_serviks").val("");
      history = [];
    });

    // Tombol Undo
    $("#undoCanvas").click(function () {
      if (history.length > 0) {
        history.pop(); // Hapus state terakhir (current)
        const prevData = history[history.length - 1]; // Ambil state sebelumnya

        const img = new Image();
        if (prevData) {
          img.src = prevData;
          img.onload = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            $("#fl_gambar_serviks").val(prevData);
          };
        } else {
          // Jika history habis
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          $("#fl_gambar_serviks").val("");
        }
      }
    });

    // --- LOGIKA HASIL IVA NEGATIF (Periksa Berkala) ---
    $("#fl_iva_negatif").change(function () {
      const targetBox = $("#fl_box_iva_berkala");

      if ($(this).is(":checked")) {
        // Tampilkan pilihan tahun
        targetBox.removeClass("d-none").addClass("d-flex");
      } else {
        // Sembunyikan dan reset pilihan tahun
        // JANGAN hide jika sedang validasi
        if (!targetBox.hasClass("force-show")) {
          targetBox.removeClass("d-flex").addClass("d-none");
          targetBox.find('input[type="checkbox"]').prop("checked", false);
        }
      }
    });
    // Trigger saat load (untuk mode edit)
    $("#fl_iva_negatif").trigger("change");
  }

  // UPDATE
  const imgInput = document.getElementById("fl_gambar_serviks");
  const savedImg = imgInput ? imgInput.value : "";

  if (savedImg && savedImg.trim() !== "") {
    const canvas = document.getElementById("cervixCanvas");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    const img = new Image();
    img.onload = () => ctx.drawImage(img, 0, 0);
    img.src = savedImg;
  }

  setTimeout(() => {
    $(".alert").alert("close");
  }, 4000);
});
