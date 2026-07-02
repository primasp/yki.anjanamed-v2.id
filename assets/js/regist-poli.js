$(document).ready(function () {
  console.log("Regist-Poli JS loaded");

  let namaPas = $("#namaPas").val().trim();
  let noRm = $("#noRm").val().trim();
  let pembiayaan = $("#pembiayaan").val();

  autocompleteInput("#namaPas", "nama");
  autocompleteInput("#noRm", "no_rm");

  function autocompleteInput(selector, type) {
    $(selector).autocomplete({
      minLength: 2, // Minimal karakter sebelum pencarian dimulai
      appendTo: "body", // Pastikan tampil di atas elemen lain
      position: { my: "left top", at: "left bottom", collision: "none" }, // Posisi tepat di bawah input
      source: function (request, response) {
        $.ajax({
          url: BASE_URL + "Daftar-Layanan/Cari-Pasien",
          method: "POST",
          data: {
            term: request.term.toLowerCase(),
            type: type,
          },
          dataType: "json",
          success: function (data) {
            $("#episodebooking").val("");
            $("#no_antri_booking").val("");
            $("#No_antri_booking").hide();
            $("#jam_slot").closest(".col-12").show();
            $("#poli").val("").trigger("change").prop("disabled", false);
            $("#dokter").val("").trigger("change").prop("disabled", false);
            $("#jam_slot").val("").trigger("change").prop("disabled", false);
            $("#dokterkdbpjs").val(null);

            console.log("Response Data:", data); // Debugging

            if (!Array.isArray(data)) {
              console.error("Invalid JSON response", data);
              return;
            }

            response(
              $.map(data, function (item) {
                console.log("Mapping:", item);
                return {
                  label:
                    type === "nama"
                      ? `${item.nama} - ${item.int_pasien_id} - ${item.tgl_lahir}`
                      : `${item.int_pasien_id} - ${item.nama} - ${item.tgl_lahir}`,
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
        });
      },
      select: function (event, ui) {
        // Format Tanggal Lahir: dd.mm.yyyy
        let tglFormatted = formatTanggal(ui.item.tgl_lahir);

        if (type === "nama") {
          $("#noRm").val(ui.item.int_pasien_id); // Isi No. RM sesuai Nama Pasien
        } else {
          $("#namaPas").val(ui.item.nama); // Isi Nama Pasien sesuai No. RM
        }
        $("#tgl_lahir").val(tglFormatted); // Isi Tanggal Lahir
        $("#sex_id").val(ui.item.sex_id);
        $("#alamat").val(ui.item.alamat1);
        $("#pasien_id").val(ui.item.pasien_id);
        $("#no_bpjs").val(ui.item.no_kartuprov);
      },
    });
  }

  function formatTanggal(tgl) {
    if (!tgl) return "";
    let d = new Date(tgl);
    if (isNaN(d)) return tgl; // fallback kalau format tidak dikenal
    let day = String(d.getDate()).padStart(2, "0");
    let month = String(d.getMonth() + 1).padStart(2, "0");
    let year = d.getFullYear();
    return `${day}.${month}.${year}`;
  }

  $("#pembiayaan").on("change", function () {
    if ($(this).val() === "BPJS") {
      console.log("Pembiayaan BPJS");

      $("#dataBPJS").show();
    } else if ($(this).val() === "PROGRAM") {
      checkPoli();
      $("#poli, #dokter, #jam_slot").val("");
      $("#jam_slot").html('<option value="">-- Pilih Jam Slot --</option>');
      // alert("Oke");
      console.log("Pembiayaan PROGRAM");
      $("#dataBPJS").hide();
    } else {
      console.log("Pembiayaan LAINNYA");
      $("#dataBPJS").hide();
      $("#poli, #dokter, #jam_slot").val("");
      $("#jam_slot").html('<option value="">-- Pilih Jam Slot --</option>');
      checkPoli();
    }
  });

  jk = $('input[name="jenis_kunjungan"]:checked').val();

  $('input[name="jenis_kunjungan"]').on("change", function () {
    if ($(this).val() === "1") {
      $("#chkLab, #chkRad").prop("checked", false);
      $("#labContainer, #radContainer").hide();
      $("#labItemsList select, #radItemsList select").val("");
      $(
        "#labItemsList input[type='number'], #radItemsList input[type='number']",
      ).val(1);

      jk = this.value;
      checkPoli(jk);

      $("#fieldsetPoli").show();
      $("#fieldsetPenunjang").hide();
      $("#label_layan").text("Registrasi Poli");
    } else if ($(this).val() === "2") {
      jk = this.value;
      checkPoli(jk);
      $("#fieldsetPoli").hide();
      $("#fieldsetPenunjang").show();
      $("#label_layan").text("Registrasi Penunjang");
    }
  });

  function checkPoli() {
    let pembiayaan = $("#pembiayaan").val();

    // alert(pembiayaan);

    $.ajax({
      url: BASE_URL + "RajalController/check_poli",
      type: "post",
      data: {
        jenis_kunjungan: jk,
        pembiayaan: pembiayaan,
      },
      success: function (response) {
        let data = JSON.parse(response);

        let option = '<option value="">-- Pilih Poliklinik --</option>';

        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].poli_id}">${data[i].keterangan}</option>`;
        }

        $("#poli").html(option);
      },
    });
  }

  // Checkbox Logic
  $("#chkLab").change(function () {
    let isChecked = this.checked;
    let container = $("#labContainer");

    // Tampilkan atau sembunyikan container
    container.toggle(isChecked);

    // Jika di-uncheck ? kosongkan semua isi
    if (!isChecked) {
      // Reset semua dropdown pemeriksaan ke default
      container.find(".lab-item").val("").trigger("change");

      // Reset label harga ke Rp 0
      container.find(".harga-labelLab").text("Rp 0");

      // Reset input qty ke 1
      container.find('input[name="lab_qty[]"]').val("1");

      // Hapus baris tambahan, hanya sisakan baris pertama
      container.find("#labItemsList .row:not(:first)").remove();
    }
  });

  $(document).on("change", ".lab-item", function () {
    let selected = $(this).find(":selected");
    let hargaLab = selected.data("hargalab") || 0;
    // alert(hargaLab);
    let parentRow = $(this).closest(".row");

    parentRow.find(".harga-labelLab").text("Rp " + hargaLab);
  });

  $("#chkRad").change(function () {
    let isChecked = this.checked;
    let container = $("#radContainer");

    container.toggle(isChecked);

    // Jika di-uncheck ? kosongkan semua isi
    if (!isChecked) {
      // Reset semua dropdown pemeriksaan ke default
      container.find(".rad-item").val("").trigger("change");

      // Reset label harga ke Rp 0
      container.find(".harga-labelRad").text("Rp 0");

      // Reset input qty ke 1
      container.find('input[name="rad_qty[]"]').val("1");

      // Hapus baris tambahan, hanya sisakan baris pertama
      container.find("#radItemsList .row:not(:first)").remove();
    }
  });

  $(document).on("change", ".rad-item", function () {
    // alert("123");
    let selected = $(this).find(":selected");
    let hargaRad = selected.data("hargarad") || 0;

    let parentRow = $(this).closest(".row");

    parentRow.find(".harga-labelRad").text("Rp " + hargaRad);
  });

  // Tambah item Lab
  $(document).on("click", ".addLabItem", function () {
    let newRow = `
    <div class="row g-2 align-items-center mb-2">
      <div class="col-md-6">
        <select class="form-select lab-item" name="lab_item[]">
          ${$("#labItemsList select.lab-item:first").html()}
        </select>
      </div>

      <div class="col-md-2">
        <span class="harga-labelLab text-muted">Rp 0</span>
      </div>



      <div class="col-md-2">
        <input type="number" class="form-control" name="lab_qty[]" placeholder="Qty" min="1" value="1">
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-sm addLabItem"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-danger btn-sm removeItem"><i class="fa fa-trash"></i></button>
      </div>
    </div>`;
    $("#labItemsList").append(newRow);
  });

  // Tambah item Rab
  $(document).on("click", ".addRadItem", function () {
    let newRow = `
    <div class="row g-2 align-items-center mb-2">
      <div class="col-md-6">
        <select class="form-select rad-item" name="lab_item[]">
          ${$("#radItemsList select.rad-item:first").html()}
        </select>
      </div>

      <div class="col-md-2">
        <span class="harga-labelRad text-muted">Rp 0</span>
      </div>



      <div class="col-md-2">
        <input type="number" class="form-control" name="rad_qty[]" placeholder="Qty" min="1" value="1">
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-sm addRadItem"><i class="fa fa-plus"></i></button>
       <button type="button" class="btn btn-danger btn-sm removeItem"><i class="fa fa-trash"></i></button>
      </div>
    </div>`;
    $("#radItemsList").append(newRow);
  });

  // Hapus Item
  $(document).on("click", ".removeItem", function () {
    $(this).closest(".row").remove();
  });

  document
    .getElementById("bookingModal")
    .addEventListener("shown.bs.modal", () => {
      const today = moment().format("YYYY-MM-DD");
      const tglInput = document.getElementById("filterTanggalBook");

      // Hanya set jika belum ada value
      if (!tglInput.value) {
        tglInput.value = today;
      }

      // alert(today);
      fetchBookingList();
      // alert(tglInput);
    });

  ["filterTanggalBook", "filterPoliBook", "filterDokterBook"].forEach(
    function (id) {
      $("#" + id).on("change", function () {
        fetchBookingList();
      });
    },
  );

  $("#formRegistLayan").on("submit", function (e) {
    e.preventDefault();
    let pembiayaan = $("#pembiayaan").val();
    let namaPas = $("#namaPas").val().trim();
    let noRm = $("#noRm").val().trim();
    let pasien_id = $("#pasien_id").val();
    let jenisKunjungan = $("input[name='jenis_kunjungan']:checked").val();

    // Validasi umum (pastikan data pasien terisi)
    if (pembiayaan === "" || namaPas === "" || noRm === "") {
      Swal.fire({
        icon: "warning",
        title: "Perhatian!",
        text: "Harap isi Nama Pasien / No. Rekam Medis / Pembiayaan sebelum menyimpan.",
        confirmButtonText: "OK",
      });
      return;
    }

    // === Validasi jenis kunjungan -> Penunjang ===
    if (jenisKunjungan === "2") {
      let isLabChecked = $("#chkLab").is(":checked");
      let isRadChecked = $("#chkRad").is(":checked");

      // Kosongkan input Poliklinik
      $("#poli, #dokter, #jam_slot").val("");

      if (!isLabChecked && !isRadChecked) {
        Swal.fire({
          icon: "warning",
          title: "Perhatian!",
          text: "Pilih minimal salah satu pemeriksaan: Laboratorium atau Radiologi.",
          confirmButtonText: "OK",
        });
        return;
      }

      // Jika Lab dipilih tapi belum ada item
      if (
        isLabChecked &&
        $("select.lab-item").filter((i, el) => $(el).val() !== "").length === 0
      ) {
        Swal.fire({
          icon: "warning",
          title: "Perhatian!",
          text: "Harap pilih minimal satu item pemeriksaan laboratorium.",
        });
        return;
      }

      // Jika Rad dipilih tapi belum ada item
      if (
        isRadChecked &&
        $("select.rad-item").filter((i, el) => $(el).val() !== "").length === 0
      ) {
        Swal.fire({
          icon: "warning",
          title: "Perhatian!",
          text: "Harap pilih minimal satu item pemeriksaan radiologi.",
        });
        return;
      }
    } else if (jenisKunjungan === "1") {
      // === Bersihkan bagian penunjang ===
      $("#chkLab, #chkRad").prop("checked", false);
      $("#labContainer, #radContainer").hide();
      $("#labItemsList select, #radItemsList select").val("");
      $(
        "#labItemsList input[type='number'], #radItemsList input[type='number']",
      ).val(1);

      if (
        $("#tgl_berobat").val() === "" ||
        $("#poli").val() === "" ||
        $("#dokter").val() === "" ||
        $("#jam_slot").val() === ""
      ) {
        Swal.fire({
          icon: "warning",
          title: "Perhatian!",
          text: "Harap lengkapi seluruh data Poliklinik (tanggal, poli, dokter, jam slot).",
          confirmButtonText: "OK",
        });
        return;
      }
    }

    // let formData = $(this).serialize();
    let formDataArr = $(this).serializeArray();

    // === Jika PEMBIAYAAN = UMUM ===
    if (pembiayaan === "UMUM") {
      Swal.fire({
        title: "Pembayaran",
        text: "Pilih cara pembayaran pasien:",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Bayar Sekarang",
        cancelButtonText: "Bayar Nanti",
        showDenyButton: true,
        denyButtonText: "Batal", // 🔴 Tombol batal tambahan
      }).then((result) => {
        if (result.isConfirmed) {
          // --- Ambil tarif dasar dari server berdasarkan jenis kunjungan ---

          $.ajax({
            url: BASE_URL + "RajalController/getTarifByJenis/" + jenisKunjungan,
            method: "GET",
            data: { poli_id: $("#poli").val() }, // ⬅️ kirim poli_id
            dataType: "json",
            success: function (res) {
              let tarifData = res.data || [];
              // let total = 0;
              let totalSebelumDiskon = 0;
              let rincian = "";

              // --- Tambahkan biaya dasar (pendaftaran / pemeriksaan) ---
              // tarifData.forEach((t) => {
              //   total += parseInt(t.harga);
              //   rincian += `<li>${t.nama_layan1} - Rp ${parseInt(
              //     t.harga,
              //   ).toLocaleString("id-ID")}</li>`;
              // });

              // === Biaya dasar
              tarifData.forEach((t) => {
                let harga = parseInt(t.harga) || 0;
                totalSebelumDiskon += harga;
                rincian += `<li>${t.nama_layan1} - Rp ${formatRupiah(harga)}</li>`;
              });

              // --- Jika Penunjang, tambahkan harga lab/rad yang dipilih ---
              // === Penunjang: LAB
              if (jenisKunjungan === "2") {
                $("select.lab-item").each(function () {
                  let val = $(this).val();
                  if (val) {
                    let nama = $(this).find(":selected").text();
                    let harga = toNumber(
                      $(this).find(":selected").data("hargalab"),
                    );
                    // let harga = parseInt(
                    //   ($(this).find(":selected").data("hargalab") || "0")
                    //     .toString()
                    //     .replace(/\D/g, ""),
                    // );
                    let qty =
                      parseInt(
                        $(this)
                          .closest(".row")
                          .find("input[name='lab_qty[]']")
                          .val(),
                      ) || 1;

                    let sub = harga * qty;
                    // total += sub;
                    totalSebelumDiskon += sub;

                    // rincian += `<li>${nama} x${qty} - Rp ${sub.toLocaleString(
                    //   "id-ID",
                    // )}</li>`;

                    rincian += `<li>${nama} x${qty} - Rp ${formatRupiah(sub)}</li>`;
                  }
                });

                // RADIOLOGI
                $("select.rad-item").each(function () {
                  let val = $(this).val();
                  if (val) {
                    let nama = $(this).find(":selected").text();
                    // let harga = parseInt(
                    //   ($(this).find(":selected").data("hargarad") || "0")
                    //     .toString()
                    //     .replace(/\D/g, ""),
                    // );
                    let harga = toNumber(
                      $(this).find(":selected").data("hargarad"),
                    );
                    let qty =
                      parseInt(
                        $(this)
                          .closest(".row")
                          .find("input[name='rad_qty[]']")
                          .val(),
                      ) || 1;

                    let sub = harga * qty;
                    // total += sub;
                    totalSebelumDiskon += sub;
                    // rincian += `<li>${nama} x${qty} - Rp ${sub.toLocaleString(
                    //   "id-ID",
                    // )}</li>`;
                    rincian += `<li>${nama} x${qty} - Rp ${formatRupiah(sub)}</li>`;
                  }
                });
              }

              // --- Tampilkan popup total dan metode pembayaran ---
              Swal.fire({
                title: "Total Pembayaran",
                html: `
              <div style="text-align:left;">
                <p><b>Rincian Biaya:</b></p>
                <ul>${rincian}</ul>
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
                  <input type="text" id="uangCash" class="swal2-input" placeholder="Masukkan nominal..."   inputmode="numeric">
                </div>
              </div>
            `,
                focusConfirm: false,
                showCancelButton: true, // ✅ tampilkan tombol Batal
                confirmButtonText: "Lanjutkan",
                cancelButtonText: "Batal",
                didOpen: () => {
                  const uangInput = document.getElementById("uangCash");
                  const discountType = document.getElementById("discountType");
                  const discountValue =
                    document.getElementById("discountValue");
                  const discountContainer = document.getElementById(
                    "discountInputContainer",
                  );
                  const discountLabel =
                    document.getElementById("discountLabel");
                  const totalBefore = totalSebelumDiskon;

                  function hitungDiskon() {
                    let tipe = $("#discountType").val();
                    let nilai = toNumber($("#discountValue").val());
                    let diskon = 0;

                    if (tipe === "PERSEN") {
                      if (nilai > 100) nilai = 100;
                      diskon = Math.round((totalBefore * nilai) / 100);
                    } else if (tipe === "NOMINAL") {
                      diskon = nilai;
                    }

                    if (diskon > totalBefore) diskon = totalBefore;

                    let grandTotal = totalBefore - diskon;

                    $("#discountAmountText").text(formatRupiah(diskon));
                    $("#grandTotalText").text(formatRupiah(grandTotal));

                    return {
                      total_before_discount: totalBefore,
                      discount_type: tipe,
                      discount_value: nilai,
                      discount_amount: diskon,
                      grand_total: grandTotal,
                    };
                  }

                  discountType.addEventListener("change", function () {
                    if (this.value === "") {
                      discountContainer.style.display = "none";
                      $("#discountValue").val("");
                    } else {
                      discountContainer.style.display = "block";
                      discountLabel.textContent =
                        this.value === "PERSEN"
                          ? "Diskon Persen (%):"
                          : "Diskon Nominal (Rp):";
                    }
                    hitungDiskon();
                  });

                  discountValue.addEventListener("input", function () {
                    let raw = this.value.replace(/\D/g, "");
                    this.value = raw
                      ? parseInt(raw).toLocaleString("id-ID")
                      : "";
                    hitungDiskon();
                  });

                  uangInput.addEventListener("input", function () {
                    let raw = this.value.replace(/\D/g, "");
                    this.value = raw
                      ? parseInt(raw).toLocaleString("id-ID")
                      : "";
                  });

                  uangInput.addEventListener("focus", function () {
                    this.select();
                  });

                  // 🧮 Event: format ribuan saat user mengetik
                  // const uangInput = document.getElementById("uangCash");
                  // uangInput.addEventListener("input", function (e) {
                  //   // Ambil angka murni tanpa karakter non-digit
                  //   let raw = this.value.replace(/\D/g, "");

                  //   // Format ribuan pakai locale Indonesia
                  //   if (raw) {
                  //     this.value = parseInt(raw).toLocaleString("id-ID");
                  //   } else {
                  //     this.value = "";
                  //   }
                  // });

                  // Tambahkan juga agar bisa select-all saat klik input
                  // uangInput.addEventListener("focus", function () {
                  //   this.select();
                  // });
                },

                preConfirm: () => {
                  let metode = $("#metodeBayar").val();
                  // let uangCash = parseInt($("#uangCash").val()) || 0;
                  // let uangCashStr =
                  //   $("#uangCash").val().replace(/\D/g, "") || "0";
                  let uangCash = toNumber($("#uangCash").val());
                  // let uangCash = parseInt(uangCashStr);

                  let tipeDiskon = $("#discountType").val();
                  let nilaiDiskon = toNumber($("#discountValue").val());
                  let discountAmount = 0;
                  if (tipeDiskon === "PERSEN") {
                    if (nilaiDiskon > 100) nilaiDiskon = 100;
                    discountAmount = Math.round(
                      (totalSebelumDiskon * nilaiDiskon) / 100,
                    );
                  } else if (tipeDiskon === "NOMINAL") {
                    discountAmount = nilaiDiskon;
                  }

                  if (discountAmount > totalSebelumDiskon) {
                    discountAmount = totalSebelumDiskon;
                  }

                  let grandTotal = totalSebelumDiskon - discountAmount;
                  let kembalian = uangCash - grandTotal;

                  // let kembalian = uangCash - total;
                  // return { metode, uangCash, kembalian };

                  return {
                    metode,
                    uangCash,
                    kembalian,
                    totalSebelumDiskon,
                    discount_type: tipeDiskon,
                    discount_value: nilaiDiskon,
                    discount_amount: discountAmount,
                    grand_total: grandTotal,
                  };
                },
              }).then((res2) => {
                // 🔁 Jika user tekan "Kembali", tampilkan popup pertama lagi
                if (res2.isDismissed) {
                  Swal.fire({
                    title: "Pembayaran",
                    text: "Pilih cara pembayaran pasien:",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Bayar Sekarang",
                    cancelButtonText: "Bayar Nanti",
                    showDenyButton: true,
                    denyButtonText: "Batal",
                  }).then((r) => {
                    if (r.isConfirmed) {
                      // Panggil ulang popup rincian
                      $("#formRegistLayan").submit();
                    } else if (r.isDenied) {
                      Swal.fire({
                        icon: "info",
                        title: "Dibatalkan",
                        text: "Proses registrasi dibatalkan.",
                        timer: 1500,
                        showConfirmButton: false,
                      });
                    } else if (r.isDismissed) {
                      formDataArr.push({
                        name: "metode_bayar",
                        value: "TUNDA",
                      });
                      formDataArr.push({ name: "uang_bayar", value: 0 });
                      formDataArr.push({ name: "total_bayar", value: 0 });
                      formDataArr.push({ name: "total_kembali", value: 0 });
                      formDataArr.push({ name: "bayar_nanti", value: 1 });

                      formDataArr.push({ name: "discount_type", value: "" });
                      formDataArr.push({ name: "discount_value", value: 0 });
                      formDataArr.push({ name: "discount_amount", value: 0 });
                      formDataArr.push({
                        name: "total_before_discount",
                        value: 0,
                      });

                      kirimDataRegistrasi(formDataArr);
                    }
                  });
                  return;
                }

                if (res2.isConfirmed) {
                  // let { metode, uangCash, kembalian } = res2.value;

                  let {
                    metode,
                    uangCash,
                    kembalian,
                    totalSebelumDiskon,
                    discount_type,
                    discount_value,
                    discount_amount,
                    grand_total,
                  } = res2.value;

                  // if (metode === "CASH" && uangCash < total) {
                  //   Swal.fire({
                  //     icon: "error",
                  //     title: "Uang tidak cukup!",
                  //     text: "Nominal uang tunai kurang dari total pembayaran.",
                  //   });
                  //   return;
                  // }

                  if (metode === "CASH" && uangCash < grand_total) {
                    Swal.fire({
                      icon: "error",
                      title: "Uang tidak cukup!",
                      text: "Nominal uang tunai kurang dari total pembayaran setelah diskon.",
                    });
                    return;
                  }

                  let textConfirm =
                    `Metode: ${metode}\n` +
                    `Total Sebelum Diskon: Rp ${formatRupiah(totalSebelumDiskon)}\n` +
                    `Diskon: Rp ${formatRupiah(discount_amount)}\n` +
                    `Total Bayar: Rp ${formatRupiah(grand_total)}`;

                  if (discount_type) {
                    textConfirm += `\nJenis Diskon: ${discount_type}`;
                    textConfirm += `\nNilai Diskon Input: ${
                      discount_type === "PERSEN"
                        ? discount_value + "%"
                        : "Rp " + formatRupiah(discount_value)
                    }`;
                  }

                  // --- Teks konfirmasi sebelum registrasi ---
                  // let textConfirm = `Metode: ${metode}\nTotal: Rp ${total.toLocaleString(
                  //   "id-ID",
                  // )}`;
                  // if (metode === "CASH") {
                  //   textConfirm += `\nUang Tunai: Rp ${uangCash.toLocaleString(
                  //     "id-ID",
                  //   )}`;
                  //   textConfirm += `\nKembalian: Rp ${kembalian.toLocaleString(
                  //     "id-ID",
                  //   )}`;
                  // }

                  if (metode === "CASH") {
                    textConfirm += `\nUang Tunai: Rp ${formatRupiah(uangCash)}`;
                    textConfirm += `\nKembalian: Rp ${formatRupiah(kembalian)}`;
                  }

                  // --- Tampilkan konfirmasi pembayaran ---
                  Swal.fire({
                    icon: "info",
                    title: "Konfirmasi Pembayaran",
                    text: textConfirm,
                    showCancelButton: true,
                    confirmButtonText: "Lanjut Registrasi",
                    cancelButtonText: "Batal",
                  }).then((res3) => {
                    if (res3.isConfirmed) {
                      // Tambahkan field tambahan sebelum kirim
                      formDataArr.push({ name: "metode_bayar", value: metode });
                      formDataArr.push({ name: "uang_bayar", value: uangCash });
                      formDataArr.push({
                        name: "total_bayar",
                        value: grand_total,
                      });
                      formDataArr.push({
                        name: "total_kembali",
                        value: kembalian,
                      });
                      formDataArr.push({ name: "bayar_nanti", value: 0 });

                      formDataArr.push({
                        name: "total_before_discount",
                        value: totalSebelumDiskon,
                      });

                      formDataArr.push({
                        name: "discount_type",
                        value: discount_type || "",
                      });

                      formDataArr.push({
                        name: "discount_value",
                        value: discount_value || 0,
                      });

                      formDataArr.push({
                        name: "discount_amount",
                        value: discount_amount || 0,
                      });

                      // Kirim ke controller
                      kirimDataRegistrasi(formDataArr);
                    }
                  });
                }
              });
            },
            error: function () {
              Swal.fire({
                icon: "error",
                title: "Gagal memuat tarif dasar",
                text: "Tidak dapat mengambil data tarif dari server.",
              });
            },
          });
        } else if (result.isDismissed) {
          formDataArr.push({ name: "metode_bayar", value: "TUNDA" });
          formDataArr.push({ name: "uang_bayar", value: 0 });
          formDataArr.push({ name: "total_bayar", value: 0 });
          formDataArr.push({ name: "total_kembali", value: 0 });
          formDataArr.push({ name: "bayar_nanti", value: 1 });

          formDataArr.push({ name: "discount_type", value: "" });
          formDataArr.push({ name: "discount_value", value: 0 });
          formDataArr.push({ name: "discount_amount", value: 0 });
          formDataArr.push({ name: "total_before_discount", value: 0 });
          kirimDataRegistrasi(formDataArr);
        }

        // 🔴 Jika pilih "Batal"
        else if (result.isDenied) {
          Swal.fire({
            icon: "info",
            title: "Dibatalkan",
            text: "Proses registrasi dibatalkan.",
            timer: 1500,
            showConfirmButton: false,
          });
        }
      });

      return; // stop di sini sampai user pilih bayar atau tidak
    }

    // === Jika PEMBIAYAAN = PROGRAM ===
    if (pembiayaan === "PROGRAM") {
      // alert("oke");

      Swal.fire({
        title: "Pengecekan Program",
        text: "Sedang memeriksa apakah pasien sudah pernah melakukan layanan ini tahun ini...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      $.ajax({
        url: BASE_URL + "RajalController/cekRiwayatProgram",
        type: "POST",
        data: {
          pasien_id: pasien_id,
          jenis_kunjungan: jenisKunjungan,
          poli: $("#poli").val(),
          lab_item: $("select.lab-item")
            .map(function () {
              return $(this).val();
            })
            .get(),
          rad_item: $("select.rad-item")
            .map(function () {
              return $(this).val();
            })
            .get(),
        },
        dataType: "json",
        success: function (res) {
          Swal.close();
          if (res.sudah_pernah) {
            Swal.fire({
              icon: "warning",
              title: "Sudah Pernah!",
              text:
                res.message ||
                "Pasien sudah pernah melakukan pemeriksaan ini tahun ini.",
            });
          } else {
            kirimDataRegistrasi(formDataArr);
          }
        },
        error: function () {
          Swal.close();
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Gagal memeriksa riwayat program pasien.",
          });
        },
      });
      return;
    }

    // === Default: lanjut kirim data jika pembiayaan lain ===
    kirimDataRegistrasi(formDataArr);
  });

  // function attachBatalListeners() {
  //   document.querySelectorAll(".btnBatalDaftar").forEach((btn) => {
  //     btn.addEventListener("click", () => {
  //       const episodeID = btn.getAttribute("data-episodeid");
  //       const poliBpjsID = btn.getAttribute("data-polibpjsid");
  //       const tglMasuk = btn.getAttribute("data-tanggalmsk");
  //       const lokasiID = btn.getAttribute("data-lokasiid");
  //       const noBpjs = btn.getAttribute("data-noBpjs");

  //       Swal.fire({
  //         title: "Konfirmasi Pembatalan",
  //         text: "Apakah Anda yakin ingin membatalkan pendaftaran ini?",
  //         icon: "warning",
  //         showCancelButton: true,
  //         confirmButtonText: "Ya, Batalkan!",
  //         cancelButtonText: "Tidak",
  //       }).then((result) => {
  //         if (result.isConfirmed) {
  //           // Kirim request ke backend
  //           $.ajax({
  //             url: BASE_URL + "RajalController/batal_booking",
  //             method: "POST",
  //             data: {
  //               episode_id: episodeID,
  //               poli_bpjs: poliBpjsID,
  //               lokasi_id: lokasiID,
  //               tgl_masuk: tglMasuk,
  //               no_kartuprov: noBpjs,
  //             },
  //             dataType: "json",
  //             beforeSend: function () {
  //               Swal.fire({
  //                 title: "Memproses...",
  //                 text: "Sedang membatalkan booking",
  //                 allowOutsideClick: false,
  //                 didOpen: () => {
  //                   Swal.showLoading();
  //                 },
  //               });
  //             },
  //             success: function (res) {
  //               Swal.close();
  //               if (res.status) {
  //                 Swal.fire({
  //                   icon: "success",
  //                   title: "Berhasil",
  //                   // text: "Booking berhasil dibatalkan",
  //                   text: res.message || "Booking berhasil dibatalkan",
  //                 }).then(() => {
  //                   fetchBookingList();
  //                 });

  //                 // Refresh tabel setelah batal
  //                 // fetchBookingList();
  //               } else {
  //                 Swal.fire({
  //                   icon: "error",
  //                   title: "Gagal",
  //                   // text: res.message || "Gagal membatalkan booking",
  //                   text: res.message || "Gagal membatalkan booking",
  //                 });
  //               }
  //             },
  //             error: function (xhr) {
  //               Swal.close();
  //               Swal.fire({
  //                 icon: "error",
  //                 title: "Error",
  //                 text: "Terjadi kesalahan saat membatalkan booking",
  //               });

  //               console.log(xhr.responseText);
  //             },
  //           });
  //         }
  //       });
  //     });
  //   });
  // }

  // function attachBatalListeners() {
  //   document.querySelectorAll(".btnBatalDaftar").forEach((btn) => {
  //     btn.addEventListener("click", () => {

  //     })})};

  function toNumber(val) {
    return parseInt(String(val || "0").replace(/\D/g, ""), 10) || 0;
  }

  function formatRupiah(val) {
    return Number(val || 0).toLocaleString("id-ID");
  }

  function kirimDataRegistrasi(formDataArr) {
    console.log("Form data kirim:", formDataArr);

    $.ajax({
      url: BASE_URL + "RajalController/proses_regis_layan",
      method: "POST",
      data: formDataArr,
      dataType: "json",
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

        // =========================
        // 1) SUKSES RAJAL (POLI)
        // =========================
        if (res.metadata && res.metadata.responCode === "00") {
          let metadata = res.metadata;
          let antrolStatus = res.NoAntrianKlinik || "-";
          let episode_id = res.episode_id;
          let tgl_berobat = res.tgl_berobat;
          let no_antrian_klinik = res.NoAntrianKlinik;

          const dataEncounter = { episode_id, tgl_berobat };

          const lanjutCetak = () => {
            Swal.fire({
              title: "Sukses",
              text: metadata.responDesc + "\nNomor Antrian: " + antrolStatus,
              icon: "success",
              confirmButtonText: "Lanjut & Cetak Antrian",
            }).then((result) => {
              if (result.isConfirmed) {
                const url =
                  BASE_URL +
                  "Struk-Layan/" +
                  episode_id +
                  "/" +
                  no_antrian_klinik +
                  "/" +
                  tgl_berobat;

                // const newWindow = window.open(
                //   url,
                //   "_blank",
                //   "width=600,height=800,scrollbars=yes,resizable=yes",
                // );

                const newWindow = window.open(
                  url,
                  "_blank",
                  "width=700,height=900,scrollbars=yes,resizable=yes",
                );

                if (!newWindow) {
                  Swal.fire({
                    icon: "warning",
                    title: "Popup diblokir",
                    text: "Izinkan popup browser untuk membuka bukti pendaftaran.",
                  });
                  return;
                }

                // if (newWindow) newWindow.location.href = url;

                // window.location.href = BASE_URL + "Daftar-Layanan";

                newWindow.focus();
                window.location.href = BASE_URL + "Daftar-Layanan";
              }
            });
          };

          // ===============================
          // CHECKLIST PERSETUJUAN SATUSEHAT
          // ===============================
          Swal.fire({
            title: "Persetujuan Pengiriman Data",
            html: `
                  <div style="text-align:left;font-size:14px">
                    <p>
                      Data pasien ini akan dikirimkan ke <b>SATUSEHAT Kemenkes RI</b>
                      untuk keperluan integrasi rekam medis nasional.
                    </p>

                    <div class="form-check mt-3">
                      <input type="checkbox" class="form-check-input" id="chkSatuSehat">
                      <label class="form-check-label" for="chkSatuSehat">
                        Saya menyatakan bahwa pasien <b>sudah diinformasikan</b>
                        dan <b>menyetujui</b> pengiriman data ke SATUSEHAT
                      </label>
                    </div>
                  </div>
                `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Lanjut Kirim",
            cancelButtonText: "Batal",
            preConfirm: () => {
              if (!document.getElementById("chkSatuSehat").checked) {
                Swal.showValidationMessage(
                  "Persetujuan wajib dicentang sebelum melanjutkan.",
                );
                return false;
              }
              return true;
            },
          }).then((consent) => {
            // ❌ Jika batal
            if (!consent.isConfirmed) {
              return;
            }

            // ===============================
            // LOADING KIRIM KE SATUSEHAT
            // ===============================
            Swal.fire({
              title: "Mengirim Encounter SATUSEHAT...",
              text: "Mohon tunggu sebentar.",
              allowOutsideClick: false,
              didOpen: () => Swal.showLoading(),
            });

            kirimEncounterSatuSehat(dataEncounter)
              .then((hasil) => {
                Swal.close();

                if (hasil.ok) {
                  // ✅ sukses SATUSEHAT => langsung lanjut cetak
                  lanjutCetak();
                } else {
                  // ❗ gagal SATUSEHAT => warning tapi tetap bisa cetak
                  Swal.fire({
                    icon: "warning",
                    title: "Encounter SATUSEHAT gagal",
                    text:
                      hasil.message || "Gagal kirim Encounter ke SATUSEHAT.",
                    showCancelButton: true,
                    confirmButtonText: "Lanjut & Cetak Antrian",
                    cancelButtonText: "Batal",
                  }).then((r) => {
                    if (r.isConfirmed) lanjutCetak();
                  });
                }
              })
              .catch(() => {
                Swal.close();
                Swal.fire({
                  icon: "warning",
                  title: "Encounter SATUSEHAT gagal",
                  text: "Terjadi kesalahan saat mengirim Encounter.",
                  showCancelButton: true,
                  confirmButtonText: "Lanjut & Cetak Antrian",
                  cancelButtonText: "Batal",
                }).then((r) => {
                  if (r.isConfirmed) lanjutCetak();
                });
              });
          });

          // ✅ coba kirim encounter dulu

          return;
        }

        if (res.success && res.episode_id) {
          // === Registrasi PENUNJANG (Lab / Rad) ===
          let episode_id = res.episode_id;
          let tgl_berobat = res.tgl_berobat || moment().format("YYYY-MM-DD");
          let no_antrian_klinik = res.NoAntrianKlinik || "-";

          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: res.message || "Data registrasi penunjang berhasil disimpan.",
            confirmButtonText: "Cetak Struk",
          }).then(() => {
            let url =
              BASE_URL +
              "Struk-Layan/" +
              episode_id +
              "/" +
              no_antrian_klinik +
              "/" +
              tgl_berobat;

            let newWindow = window.open(
              url,
              "_blank",
              "width=600,height=800,scrollbars=yes,resizable=yes",
            );

            newWindow.location.href = url;
            window.location.href = BASE_URL + "Daftar-Layanan";

            // let newWindow = window.open(
            //   url,
            //   "_blank",
            //   "width=600,height=800,scrollbars=yes,resizable=yes"
            // );

            // window.location.href = BASE_URL + "Daftar-Layanan";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text:
              res.message ||
              (res.metadata ? res.metadata.responDesc : "Terjadi kesalahan."),
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Error!",
          text: "Gagal menghubungi server: " + error,
        });
      },
    });
  }

  function kirimEncounterSatuSehat(dataEncounter) {
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
            encounter: dataEncounter,
          };

          $.ajax({
            url: BASE_URL + "satusehat/kirimEncounter",
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify(payload),
            success: function (response) {
              // ✅ indikator sukses (sesuaikan kalau backend punya field lain)
              const ok =
                response?.success === true ||
                response?.metadata?.responCode === "00" ||
                !!response?.encounter_uuid ||
                !!response?.id;

              if (ok) {
                resolve({ ok: true, resp: response });
              } else {
                resolve({
                  ok: false,
                  message:
                    response?.message || "Encounter tidak berhasil dikirim.",
                  resp: response,
                });
              }
            },
            error: function (xhr) {
              resolve({
                ok: false,
                message:
                  "Gagal kirim Encounter: " + (xhr.responseText || "error"),
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

  function kirimEncounterSatuSehatxxx(dataEncounter) {
    $.ajax({
      url: BASE_URL + "satusehat/token", // endpoint PHP
      method: "GET",
      dataType: "json",
      success: function (res) {
        if (res.token) {
          console.log("Token : " + res.token);
          // Gabungkan dataEncounter dan token
          const payload = {
            token: res.token,
            encounter: dataEncounter,
          };
          alert("Kirim Encounter");
          // Kirim ke controller
          $.ajax({
            url: BASE_URL + "satusehat/kirimEncounter",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
            success: function (response) {
              console.log("Encounter berhasil dikirim ke SATUSEHAT:", response);
            },
            error: function (xhr) {
              console.error(
                "? Gagal kirim Encounter ke backend:",
                xhr.responseText,
              );
            },
          });
        } else {
          console.error("Token kosong dari backend.");
        }
      },
      error: function (xhr) {
        console.error("Gagal ambil token dari backend:", xhr.responseText);
      },
    });
  }

  $("#tgl_berobat, #poli").on("change", function () {
    let tanggal = $("#tgl_berobat").val();
    let poli = $("#poli").val();

    if (!tanggal || !poli) {
      console.log("Tanggal atau poliklinik belum dipilih.");
      resetDropdowns(); // Reset dokter dan jam slot
      return;
    }

    if (tanggal && poli) {
      cariDokter(tanggal, poli);
    } else {
      $("#dokter").html('<option value="">-- Pilih Dokter3 --</option>');
    }
  });

  function cariDokter(tanggal, poli, callback = null) {
    tanggal = moment(tanggal, "DD/MM/YYYY").format("YYYY-MM-DD");
    let day = moment(tanggal).locale("id").format("dddd");
    // alert(day);

    $.ajax({
      url: BASE_URL + "RajalController/check_dokter",
      type: "POST",
      data: {
        tgl_berobat: tanggal,
        poli_id: poli,
        hari: day,
      },
      success: function (response) {
        let data = JSON.parse(response);
        console.log(data);

        let options = '<option value="">-- Pilih Dokter --</option>';

        if (data.length > 0) {
          data.forEach(function (item) {
            options += `<option value="${item.dokter_id}">${item.nama}</option>`; // Tambahkan dokter ke dropdown
          });
        } else {
          options = '<option value="">Tidak ada dokter tersedia</option>'; // Jika tidak ada dokter
        }

        $("#dokter").html(options); // Perbarui dropdown dokter
        $("#jam_slot").html('<option value="">-- Pilih Jam Slot --</option>');

        if (typeof callback === "function") {
          callback();
        }
      },

      error: function (xhr, status, error) {
        console.error("Error fetching doctors:", error);

        resetDropdowns();
      },
    });
  }

  $("#dokter").on("change", function () {
    let tanggal = $("#tgl_berobat").val();
    let poli = $("#poli").val();
    let dokter = $(this).val();

    if (tanggal && poli && dokter) {
      cariJamSlot(tanggal, poli, dokter);
    } else {
      $("#jam_slot").html('<option value="">-- Pilih Jam Slot --</option>'); // Reset dropdown jam slot
    }
    // alert("Slot");
  });

  function cariJamSlot(tanggal, poli, dokter, callback = null) {
    tanggal = moment(tanggal, "DD/MM/YYYY").format("YYYY-MM-DD");
    let day = moment(tanggal).locale("id").format("dddd");

    console.log("Tanggal dipilih ok: " + tanggal);
    console.log("Poli dipilih: " + poli);
    console.log("Day dipilih: " + day);
    console.log("Dokter dipilih: " + dokter);

    $.ajax({
      url: BASE_URL + "RajalController/check_jam_slot",
      type: "POST",
      data: {
        tgl_berobat: tanggal,
        poli_id: poli,
        dokter_id: dokter,
        day: day,
      },
      success: function (response) {
        let data = JSON.parse(response);
        console.log(data);

        if (data.length > 0) {
          let formattedData = [];
          data.forEach(function (item) {
            formattedData.push({
              id: JSON.stringify({
                antrian: item.antrian,
                jam_mulai: item.jam_mulai,
                jam_selesai: item.jam_selesai,
                hari_id: item.hari_id,
                antrian_id: item.antrian_id,
              }),
              text: `Antrian ${item.antrian} (${item.jam_mulai} - ${item.jam_selesai})`,
            });
          });

          // Inisialisasi Select2 dengan template custom
          $("#jam_slot").select2({
            data: formattedData,
            placeholder: "-- Pilih Jam Slot --",
            allowClear: true,
            width: "100%",
            templateResult: formatOption,
            templateSelection: formatSelected,
          });

          // Jalankan callback jika diberikan
          if (typeof callback === "function") {
            callback();
          }
        } else {
          $("#jam_slot").html(
            '<option value="">Tidak ada jam slot tersedia</option>',
          );
        }
      },
    });
  }

  function resetDropdowns() {
    $("#dokter").html('<option value="">-- Pilih Dokter --</option>');
    $("#jam_slot").html('<option value="">-- Pilih Jam Slot --</option>');
  }

  function formatOption(option) {
    if (!option.id) return option.text;
    const data = JSON.parse(option.id);

    return `Antrian ${data.antrian} (${data.jam_mulai} - ${data.jam_selesai})`;
  }

  function formatSelected(option) {
    if (!option.id) return option.text;
    const data = JSON.parse(option.id);
    // console.log("yy" + data);
    console.log("yy", JSON.stringify(data));
    return `Antrian ${data.antrian}`;
  }
});

function fetchBookingList() {
  const tglBook = document.getElementById("filterTanggalBook").value;
  const poliBook = document.getElementById("filterPoliBook").value;
  const dokterBook = document.getElementById("filterDokterBook").value;

  const url =
    BASE_URL +
    "RajalController/get_booking_list" +
    `?tgl=${tglBook}&poli=${poliBook}&dokter=${dokterBook}`;
  // alert(url);

  fetch(url, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((res) => res.json())
    .then((data) => {
      const tbody = document.querySelector("#tabelBooking tbody");
      tbody.innerHTML = "";
      data.forEach((row) => {
        const tglView = row.tgl_masuk
          ? moment(row.tgl_masuk, "YYYY-MM-DD").format("DD.MM.YYYY")
          : "";

        tbody.innerHTML += `
            <tr>
              <td>${row.episode_id}</td>
              <td>${row.nama}</td>
              <td>${row.int_pasien_id}</td>
              <td>${row.no_kartuprov}</td>            
              <td>${tglView}</td>
              <td>${row.nama_poli}</td>
              <td>${row.nama_dr}</td>
             
              <td>${row.aktif == "1" ? "Aktif" : "Tidak Aktif"}</td>
              <td>
            

                 <button class="btn btn-sm btn-danger btnBatalDaftar" 
                        data-episodeid="${row.episode_id}"
                        data-polibpjsid="${row.poli_bpjsid}" 
                         data-noBpjs="${row.no_kartuprov}"
                        data-tanggalmsk="${row.tgl_masuk}"
                         data-lokasiid="${row.lokasi_id}">
                  Batalkan
                </button>
             
              </td>
            </tr>`;
      });

      // attachButtonListeners();
      attachBatalListeners();
    });

  function attachBatalListeners() {
    $(document).off("click", ".btnBatalDaftar");

    $(document).on("click", ".btnBatalDaftar", function () {
      const episodeID = $(this).data("episodeid");
      const lokasiID = $(this).data("lokasiid");

      Swal.fire({
        title: "Konfirmasi Pembatalan",
        text: "Apakah Anda yakin ingin membatalkan pendaftaran ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Batalkan!",
        cancelButtonText: "Tidak",
        reverseButtons: true,
      }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
          url: BASE_URL + "RajalController/batal_booking",
          type: "POST",
          dataType: "json",
          data: {
            episode_id: episodeID,
            lokasi_id: lokasiID,
          },

          beforeSend: function () {
            Swal.fire({
              title: "Memproses...",
              text: "Sedang membatalkan booking",
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              },
            });
          },

          success: function (res) {
            Swal.close();

            if (res.status) {
              Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.message,
              }).then(() => {
                fetchBookingList();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Gagal",
                text: res.message,
              });
            }
          },

          error: function (xhr) {
            Swal.close();

            console.log(xhr.responseText);

            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Terjadi kesalahan server",
            });
          },
        });
      });
    });
  }

  function attachBatalListenersxx() {
    // alert("ss");
    document.querySelectorAll(".btnBatalDaftar").forEach((btn) => {
      btn.addEventListener("click", () => {
        const episodeID = btn.getAttribute("data-episodeid");
        const poliBpjsID = btn.getAttribute("data-polibpjsid");
        const tglMasuk = btn.getAttribute("data-tanggalmsk");
        const lokasiID = btn.getAttribute("data-lokasiid");
        const noBpjs = btn.getAttribute("data-noBpjs");

        // Konfirmasi batal
        Swal.fire({
          title: "Konfirmasi Pembatalan",
          text: "Apakah Anda yakin ingin membatalkan pendaftaran ini?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ya, Batalkan!",
          cancelButtonText: "Tidak",
        }).then((result) => {
          if (result.isConfirmed) {
            // Kirim request ke backend
            $.ajax({
              url: BASE_URL + "RajalController/batal_booking",
              method: "POST",
              data: {
                episode_id: episodeID,
                poli_bpjs: poliBpjsID,
                lokasi_id: lokasiID,
                tgl_masuk: tglMasuk,
                no_kartuprov: noBpjs,
              },
              dataType: "json",
              beforeSend: function () {
                Swal.fire({
                  title: "Memproses...",
                  text: "Sedang membatalkan booking",
                  allowOutsideClick: false,
                  didOpen: () => {
                    Swal.showLoading();
                  },
                });
              },
              success: function (res) {
                Swal.close();
                if (res.status) {
                  Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    // text: "Booking berhasil dibatalkan",
                    text: res.message || "Booking berhasil dibatalkan",
                  }).then(() => {
                    fetchBookingList();
                  });

                  // Refresh tabel setelah batal
                  // fetchBookingList();
                } else {
                  Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    // text: res.message || "Gagal membatalkan booking",
                    text: res.message || "Gagal membatalkan booking",
                  });
                }
              },
              error: function (xhr) {
                Swal.close();
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Terjadi kesalahan saat membatalkan booking",
                });

                console.log(xhr.responseText);
              },
            });
          }
        });
      });
    });
  }
}
