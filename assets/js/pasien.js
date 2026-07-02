function dropdownKota(value, element) {
  if (value != "") {
    $.ajax({
      url: BASE_URL + "PasienController/get_kota",
      type: "post",
      data: {
        prov_id: value,
      },
      success: function (response) {
        let data = JSON.parse(response);
        let option = '<option value="">-- Pilih Kabupaten --</option>';
        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].kkk_id}">${data[i].keterangan}</option>`;
        }
        $("#" + element).html(option);

        if (selectedKota != null) {
          $("#" + element)
            .val(selectedKota)
            .trigger("change");
        }
      },
    });
  } else {
    $("#" + element).html('<option value="">-- Pilih Kabupaten --</option>');
  }
}

function dropdownKecamatan(value, element) {
  if (value != "") {
    $.ajax({
      url: BASE_URL + "PasienController/get_kecamatan",
      type: "post",
      data: {
        kota_id: value,
      },
      success: function (response) {
        let data = JSON.parse(response);
        let option = '<option value="">-- Pilih Kecamatan --</option>';
        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].kkk_id}">${data[i].keterangan}</option>`;
        }
        $("#" + element).html(option);

        if (selectedKecamatan != null) {
          $("#" + element)
            .val(selectedKecamatan)
            .trigger("change");
        }
      },
    });
  } else {
    $("#" + element).html('<option value="">-- Pilih Kecamatan --</option>');
  }
}

function dropdownKelurahan(value, element) {
  if (value != "") {
    $.ajax({
      url: BASE_URL + "PasienController/get_kelurahan",
      type: "post",
      data: {
        kecamatan_id: value,
      },
      success: function (response) {
        let data = JSON.parse(response);
        let option = '<option value="">-- Pilih Kelurahan --</option>';
        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].kkk_id}">${data[i].keterangan}</option>`;
        }
        $("#" + element).html(option);

        if (selectedKelurahan != null) {
          $("#" + element)
            .val(selectedKelurahan)
            .trigger("change");
        }
      },
    });
  } else {
    $("#" + element).html('<option value="">-- Pilih Kelurahan --</option>');
  }
}

function toggleEditBPJSInput() {
  let selectedRekanan = $("#edit_rekanan_id").val();
  // alert(selectedRekanan);
  // Hapus alert agar tidak mengganggu pengguna
  if (selectedRekanan === "BPJS") {
    $("#edit-bpjs-input-container").show();
    $("#edit_bpjs_no").val($("#edit_bpjs_no").attr("data-selected")); // Isi otomatis dari atribut
  } else {
    $("#edit-bpjs-input-container").hide();
    $("#edit_bpjs_no").val(""); // Kosongkan jika bukan BPJS
  }
}

$(document).ready(function () {
  console.log("Pasien JS loaded");

  // ✅ jika sudah pernah init, destroy dulu
  if ($.fn.DataTable.isDataTable("#pasienTable")) {
    $("#pasienTable").DataTable().clear().destroy();
  }

  const table = $("#pasienTable").DataTable({
    processing: true,
    serverSide: true,
    searching: true,
    pageLength: 10,
    responsive: true,
    autoWidth: false,
    order: [[1, "asc"]],
    ajax: {
      url: BASE_URL + "PasienController/ajax_pasien_list",
      type: "POST",
      dataType: "json", // ✅ paksa JSON
      dataSrc: function (json) {
        console.log("DT JSON:", json);
        return json.data || [];
      },
      error: function (xhr, status, err) {
        console.error("DT Ajax error:", xhr.status, status, err);
        console.error("Response:", xhr.responseText); // ✅ biasanya kelihatan HTML login / error PHP
      },
    },
    columnDefs: [
      {
        targets: 0,
        orderable: false,
        searchable: false,
        render: function () {
          return `
            <div class="form-check check-tables">
              <input class="form-check-input" type="checkbox" value="something">
            </div>`;
        },
      },
      { targets: 5, orderable: false, searchable: false },
    ],
    language: {
      lengthMenu: "Tampilkan _MENU_ data per halaman",
      zeroRecords: "Data tidak ditemukan",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(disaring dari _MAX_ total data)",
      search: "Cari (min 3 huruf):",
      paginate: { next: "Berikutnya", previous: "Sebelumnya" },
    },
    initComplete: function () {
      setTimeout(() => {
        const loader = document.getElementById("page-loading");
        if (loader) loader.style.display = "none";
      }, 200);
    },
  });

  // ✅ threshold minimal 3 karakter
  const $search = $("div.dataTables_filter input");
  $search.off("keyup"); // matikan handler default
  $search.on("keyup", function () {
    const v = this.value.trim();
    if (v.length === 0) return table.search("").draw();
    if (v.length >= 3) return table.search(v).draw();
  });

  // Set nilai awal jika ada data dari database
  $("#edit_propinsi_id").trigger("change");
  $("#edit_dms_propinsi_id").trigger("change");
  toggleEditBPJSInput();

  // Cek jika pengguna mengubah pilihan penjamin
  $("#edit_rekanan_id").on("change", function () {
    toggleEditBPJSInput();
  });

  $("#alamat-sama").on("change", function () {});

  window.selectedKota = null;
  window.selectedKecamatan = null;
  window.selectedKelurahan = null;

  $(document).on("click", ".pasien-refresh", function () {
    // let table = $("#pasienTable").DataTable();

    // table.ajax.reload(null, false);

    let table = $("#pasienTable").DataTable();

    Swal.fire({
      title: "Memuat Data Pasien...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    table.ajax.reload(function () {
      Swal.close();
    }, false);
  });

  $("#pasien-table").DataTable({
    destroy: true, // Memungkinkan reinitialisasi
    pageLength: 10,
    searching: true,
    ordering: true,
    paging: true,
    responsive: true,
    autoWidth: false,
    order: [[1, "asc"]],
    language: {
      lengthMenu: "Tampilkan _MENU_ data per halaman",
      zeroRecords: "Data tidak ditemukan",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(disaring dari _MAX_ total data)",
      search: "Cari:",
      paginate: {
        next: "Berikutnya",
        previous: "Sebelumnya",
      },
    },
  });

  window.selectedKota = null;
  window.selectedKecamatan = null;
  window.selectedKelurahan = null;
  // Inisialisasi Select2 pada elemen dengan class `select2`

  $("#btnCancelPasien").on("click", function () {
    window.location.href = BASE_URL + "Pasien-All"; // Arahkan ke halaman Pasien-All
  });

  $("#btnUpdatePasien").on("click", function () {
    $("#form-edit-pasien").find(".is-invalid").removeClass("is-invalid");
    $("#form-edit-pasien").find(".invalid-feedback").remove();
    let isValid = true;
    $("#form-edit-pasien")
      .find("input, select, textarea")
      .each(function () {
        let value = $(this).val();

        if ($(this).prop("required") && (!value || value.trim() === "")) {
          isValid = false;
          $(this).addClass("is-invalid");
          $(this)
            .closest(".input-block")
            .append(
              '<div class="invalid-feedback">Field ini wajib diisi.</div>',
            );
        } else {
          $(this).removeClass("is-invalid");
          $(this).closest(".input-block").find(".invalid-feedback").remove();
        }
      });

    if (
      $("#edit_rekanan_id").val() === "BPJS" &&
      $("#edit_bpjs_no").val().trim() === ""
    ) {
      isValid = false;
      const $bpjsInput = $("#edit_bpjs_no");
      $bpjsInput.addClass("is-invalid");
      $bpjsInput.closest(".input-block").find(".invalid-feedback").show();
    }

    // Jika validasi gagal, hentikan proses
    if (!isValid) {
      Swal.fire({
        title: "Error",
        text: "Silakan isi semua field yang wajib diisi.",
        icon: "error",
      });
      return;
    }
    let formDataEdit = $("#form-edit-pasien").serialize();

    $.ajax({
      url: BASE_URL + "Pasien-Edit", // Endpoint penyimpanan
      type: "POST",
      data: formDataEdit,
      dataType: "json",
      success: function (response) {
        console.log(response);
        if (response.success == true) {
          Swal.fire({
            title: "Berhasil",
            text: "Data pasien berhasil di update.",
            icon: "success",
          }).then((result) => {
            window.location.href = BASE_URL + "Pasien-All"; // Kembali ke halaman Pasien-All
          });
        } else {
          $.each(response.messages, function (key, value) {
            Swal.fire({
              title: "Gagal",
              text: resp.message || "Terjadi kesalahan saat update data.",
              icon: "error",
            });
          });
        }
      },
      error: function () {
        Swal.fire({
          title: "Error",
          text: "Gagal menghubungi server. Silakan coba lagi.",
          icon: "error",
        });
      },
    });
  });

  $("#btnSavePasien").on("click", function () {
    // Reset semua validasi sebelumnya
    $("#form-add-pasien").find(".is-invalid").removeClass("is-invalid");
    $("#form-add-pasien").find(".invalid-feedback").remove();

    let isValid = true;
    $("#form-add-pasien")
      .find("input, select, textarea")
      .each(function () {
        // if ($(this).prop("required") && $(this).val().trim() === "") {
        let value = $(this).val();
        if ($(this).prop("required") && (!value || value.trim() === "")) {
          isValid = false;
          $(this).addClass("is-invalid");
          $(this)
            .closest(".input-block")
            .append(
              '<div class="invalid-feedback">Field ini wajib diisi.</div>',
            );
        } else {
          $(this).removeClass("is-invalid");
          $(this).closest(".input-block").find(".invalid-feedback").remove();
        }
      });

    if (
      $("#rekanan_id").val() === "BPJS" &&
      $("#bpjs_no").val().trim() === ""
    ) {
      isValid = false;
      const $bpjsInput = $("#bpjs_no");
      $bpjsInput.addClass("is-invalid");
      $bpjsInput.closest(".input-block").find(".invalid-feedback").show();
    }

    // VALIDASI NIK HARUS 16 DIGIT
    const nik = $("#nik").val().trim();

    if (!/^\d{16}$/.test(nik)) {
      isValid = false;

      $("#nik").addClass("is-invalid");

      if (
        $("#nik").closest(".input-block").find(".invalid-feedback").length === 0
      ) {
        $("#nik")
          .closest(".input-block")
          .append(
            '<div class="invalid-feedback">NIK wajib 16 digit angka.</div>',
          );
      }
    }

    // Jika validasi gagal, hentikan proses
    if (!isValid) {
      Swal.fire({
        title: "Error",
        text: "Silakan isi semua field yang wajib diisi.",
        icon: "error",
      });
      return;
    }

    // Ambil data form
    let formData = $("#form-add-pasien").serialize();

    console.log(formData);

    $.ajax({
      url: BASE_URL + "Pasien-Save", // Endpoint penyimpanan
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        // let resp = JSON.parse(response);
        console.log(response);
        if (response.success == true) {
          Swal.fire({
            title: "Berhasil",
            text: "Data pasien berhasil disimpan.",
            icon: "success",
          }).then((result) => {
            window.location.href = BASE_URL + "Pasien-All"; // Kembali ke halaman Pasien-All
          });
        } else {
          Swal.fire({
            title: "Gagal",
            text: response.message || "Terjadi kesalahan saat menyimpan data.",
            icon: "error",
          });
        }
      },
      error: function () {
        Swal.fire({
          title: "Error",
          text: "Gagal menghubungi server. Silakan coba lagi.",
          icon: "error",
        });
      },
    });
  });

  function toggleBPJSInput(value) {
    const $bpjsContainer = $("#bpjs-input-container");
    const $bpjsInput = $("#bpjs_no");

    if (value === "BPJS") {
      // Tampilkan input nomor BPJS
      $bpjsContainer.show();
      $bpjsInput.prop("disabled", false);
    } else {
      // Sembunyikan input nomor BPJS
      $bpjsContainer.hide();
      $bpjsInput.prop("disabled", true).val(""); // Kosongkan input
      $bpjsInput.removeClass("is-invalid");
      $bpjsContainer.find(".invalid-feedback").hide();
    }
  }

  // Event saat dropdown berubah
  $("#rekanan_id").on("change", function () {
    const selectedValue = $(this).val();
    toggleBPJSInput(selectedValue);
  });

  // Inisialisasi saat halaman dimuat jika ada nilai default
  const initialValue = $("#rekanan_id").val();
  toggleBPJSInput(initialValue);

  // $("#edit-alamat-sama").on("change", function () {
  //   if (this.checked) {
  //     let editProvKTP = $("#edit_propinsi_id").val();
  //     let editKabKTP = $("#edit_kabupaten_id").val();
  //     let editKecKTP = $("#edit_kecamatan_id").val();
  //     let editKelKTP = $("#edit_kelurahan_id").val();
  //     let editAlamatKTP = $("#edit_alamat").val();
  //     let editRtKTP = $("#edit_rt").val();
  //     let editRwKTP = $("#edit_rw").val();
  //     let editKodeposKTP = $("#edit_kodepos").val();

  //     selectedKota = editKabKTP;
  //     selectedKecamatan = editKecKTP;
  //     selectedKelurahan = editKelKTP;

  //     if (
  //       editProvKTP != "" &&
  //       editKabKTP != "" &&
  //       editKecKTP != "" &&
  //       editKelKTP != "" &&
  //       editAlamatKTP != ""
  //     ) {
  //       $("#edit_dms_propinsi_id").val(editProvKTP).trigger("change");
  //       // $("#dms_kabupaten_id").prop('disabled', true);
  //       // $("#dms_kecamatan_id").prop('disabled', true);
  //       // $("#dms_kelurahan_id").prop('disabled', true);

  //       $("#edit_dms_alamat").val(editAlamatKTP);
  //       $("#edit_dms_rt").val(editRtKTP);
  //       $("#edit_dms_rw").val(editRwKTP);
  //       $("#edit_dms_kodepos").val(editKodeposKTP);
  //     }
  //   }
  // });

  $("#alamat-sama").on("change", function () {
    if (this.checked) {
      let provKTP = $("#propinsi_id").val();
      let kabKTP = $("#kabupaten_id").val();
      let kecKTP = $("#kecamatan_id").val();
      let kelKTP = $("#kelurahan_id").val();
      let alamatKTP = $("#alamat").val();
      let rtKTP = $("#rt").val();
      let rwKTP = $("#rw").val();
      let kodeposKTP = $("#kodepos").val();

      selectedKota = kabKTP;
      selectedKecamatan = kecKTP;
      selectedKelurahan = kelKTP;
      if (
        provKTP != "" &&
        kabKTP != "" &&
        kecKTP != "" &&
        kelKTP != "" &&
        alamatKTP != ""
      ) {
        $("#dms_propinsi_id").val(provKTP).trigger("change");

        $("#dms_alamat").val(alamatKTP);
        $("#dms_rt").val(rtKTP);
        $("#dms_rw").val(rwKTP);
        $("#dms_kodepos").val(kodeposKTP);
      } else {
        Swal.fire(
          "Gagal",
          "Harap isi propinsi, kabupaten/kota, kecamatan, kelurahan dan alamat pada KTP",
          "warning",
        );
        $("#alamat-sama").prop("checked", false);
      }
    } else {
      $("#dms_propinsi_id").val("").trigger("change");
      $("#dms_kabupaten_id").val("").trigger("change");
      $("#dms_kecamatan_id").val("").trigger("change");
      $("#dms_kelurahan_id").val("").trigger("change");
      $("#dms_alamat").val("");
      $("#dms_rt").val("");
      $("#dms_rw").val("");
      $("#dms_kodepos").val("");
    }
  });

  $("#edit-alamat-sama").on("change", function () {
    if (this.checked) {
      let provKTP = $("#edit_propinsi_id").val();
      let kabKTP = $("#edit_kabupaten_id").val();
      let kecKTP = $("#edit_kecamatan_id").val();
      let kelKTP = $("#edit_kelurahan_id").val();
      let alamatKTP = $("#edit_alamat").val();
      let rtKTP = $("#edit_rt").val();
      let rwKTP = $("#edit_rw").val();
      let kodeposKTP = $("#edit_kodepos").val();

      selectedKota = kabKTP;
      selectedKecamatan = kecKTP;
      selectedKelurahan = kelKTP;

      if (provKTP && kabKTP && kecKTP && kelKTP && alamatKTP) {
        $("#edit_dms_propinsi_id").val(provKTP).trigger("change");

        setTimeout(function () {
          $("#edit_dms_kabupaten_id").val(kabKTP).trigger("change");

          setTimeout(function () {
            $("#edit_dms_kecamatan_id").val(kecKTP).trigger("change");

            setTimeout(function () {
              $("#edit_dms_kelurahan_id").val(kelKTP).trigger("change");
            }, 500);
          }, 500);
        }, 500);

        // Mengisi alamat domisili dengan data KTP
        $("#edit_dms_alamat").val(alamatKTP);
        $("#edit_dms_rt").val(rtKTP);
        $("#edit_dms_rw").val(rwKTP);
        $("#edit_dms_kodepos").val(kodeposKTP);
      } else {
        Swal.fire(
          "Gagal",
          "Harap isi propinsi, kabupaten/kota, kecamatan, kelurahan, dan alamat pada KTP",
          "warning",
        );
        $("#edit-alamat-sama").prop("checked", false);
      }
    } else {
      // Reset data domisili jika checkbox tidak dicentang
      $("#edit_dms_propinsi_id").val("").trigger("change");
      setTimeout(function () {
        $("#edit_dms_kabupaten_id").val("").trigger("change");
        setTimeout(function () {
          $("#edit_dms_kecamatan_id").val("").trigger("change");
          setTimeout(function () {
            $("#edit_dms_kelurahan_id").val("").trigger("change");
          }, 500);
        }, 500);
      }, 500);

      $("#edit_dms_alamat").val("");
      $("#edit_dms_rt").val("");
      $("#edit_dms_rw").val("");
      $("#edit_dms_kodepos").val("");
    }
  });

  // Panggil initializeModalTambahPasien saat modal dibuka
  $("#modalTambahPasien").on("shown.bs.modal", function () {
    initializeModalTambahPasien(); // Inisialisasi logika modal
  });

  // Pasang event listener untuk perubahan tipe pasien
  $('input[name="tipePasien"]').on("change", function () {
    initializeModalTambahPasien(); // Panggil ulang logika saat tipe pasien berubah
  });

  function initializeModalTambahPasien() {
    const selectedType = $('input[name="tipePasien"]:checked').val(); // Cek tipe pasien yang dipilih
    if (selectedType === "bpjs") {
      $("#input-bpjs").show(); // Tampilkan input nomor BPJS
      $("#actionButton").text("Cek Data"); // Ubah teks tombol
      $("#actionButton")
        .off("click")
        .on("click", function () {
          cekDataBPJS(); // Panggil fungsi cek data BPJS
        });
    } else {
      $("#input-bpjs").hide(); // Sembunyikan input nomor BPJS
      $("#noBpjs").val(""); // Kosongkan input nomor BPJS
      $("#actionButton").text("Tambah Pasien"); // Ubah teks tombol
      $("#actionButton")
        .off("click")
        .on("click", function () {
          // Arahkan ke halaman Pasien-Create

          // alert(selectedProvinsi);

          window.location.href = BASE_URL + "Pasien-Create";
        });
    }
  }

  // Fungsi untuk cek data BPJS
  function cekDataBPJS() {
    var noBpjs = $("#noBpjs").val().trim();

    if (noBpjs === "") {
      Swal.fire("Peringatan", "Nomor BPJS / KTP harus diisi.", "warning");
      return;
    }
    // alert("Melakukan pengecekan BPJS untuk nomor: " + noBpjs);

    $.ajax({
      // url: '<?= base_url("PasienController/getDataPeserta") ?>', // Endpoint untuk API BPJS
      url: BASE_URL + "PasienController/getDataPeserta",
      type: "POST",
      data: { noBpjs: noBpjs }, // Kirim data nomor BPJS ke server
      success: function (response) {
        // Parsing respons dari server

        let resp;

        try {
          resp = JSON.parse(response);
        } catch (e) {
          Swal.fire("Error", "Respons server tidak valid.", "error");
          console.error("JSON Parsing Error:", e);
          return;
        }
        if (!resp.status) {
          // Jika status false
          Swal.fire(
            "Gagal",
            resp.message || "Data tidak ditemukan.",
            "warning",
          );
          return;
        }

        let dataPasien = resp.data || {};
        let metadata = resp.metadata || {};
        // let noKTP = resp.noKTP || {};
        let noKTP = resp.noKTP || dataPasien.noKTP || "";
        let noKartu = resp.noKartu || dataPasien.noKartu || "";
        console.log(dataPasien);
        console.log(metadata);
        // alert(noKartu);

        if (metadata.responCode === "00") {
          // Jika data ditemukan
          Swal.fire({
            title: "Sukses",
            text: "Data pasien ditemukan!",
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            fillModalWithData(dataPasien, noKTP, noKartu);
            $("#modalTambahPasien").modal("hide");
            $("#modalDetailPasien").modal("show"); // Tampilkan modal
          });

          // Event saat modalDetailPasien ditutup
          $("#modalDetailPasien").on("hidden.bs.modal", function () {
            $("#modalTambahPasien").modal("show"); // Buka kembali modalTambahPasien
          });
        } else {
          // Jika data tidak ditemukan dengan kode respons tertentu
          Swal.fire(
            "Peringatan",
            metadata.responDesc || "Data tidak ditemukan.",
            "warning",
          );
        }

        // console.log(resp);
      },
      error: function (xhr, status, error) {
        // Jika terjadi kesalahan pada AJAX request
        Swal.fire(
          "Error",
          "Terjadi kesalahan saat menghubungi server.",
          "error",
        );
        console.error("AJAX Error:", status, error);
      },
    });

    // Tambahkan AJAX untuk API BPJS di sini
  }

  // Fungsi untuk mengisi modal dengan data pasien
  function fillModalWithData(dataPasienBpjs, noKTP, noKartu) {
    $("#modal-nama").text(dataPasienBpjs.nama || "-");
    $("#modal-noKartu").text(noKartu);
    $("#modal-tglLahir").text(dataPasienBpjs.tglLahir || "-");
    $("#modal-noKTP").text(noKTP);
    $("#modal-ketAktif").text(dataPasienBpjs.ketAktif || "-");
    $("#modal-jnsPeserta").text(dataPasienBpjs.jnsPeserta?.nama || "-");
    $("#modal-sex").text(
      dataPasienBpjs.sex === "L" ? "Laki-laki" : "Perempuan",
    );
    $("#modal-provider").text(
      (dataPasienBpjs.kdProviderPst?.kdProvider || "-") +
        " - " +
        (dataPasienBpjs.kdProviderPst?.nmProvider || "-"),
    );
    $("#modal-noHp").text(dataPasienBpjs.noHP || "-");
  }

  // Event handler untuk tombol Cek Data
  $("#cekData").on("click", function () {
    var tipePasien = $('input[name="tipePasien"]:checked').val();
    var noBpjs = $("#noBpjs").val();

    if (tipePasien === "bpjs" && noBpjs.trim() === "") {
      alert("Nomor BPJS / KTP harus diisi.");
      return;
    }

    if (tipePasien === "bpjs") {
      alert("Melakukan pengecekan BPJS untuk nomor: " + noBpjs);
      // Tambahkan AJAX untuk API BPJS di sini
    } else {
      alert("Pasien Umum diproses.");
    }
  });

  $("#btnTambahPasien").on("click", function () {
    var nik = $("#modal-noKTP").text().trim();
    var noBpjs = $("#modal-noKartu").text().trim();
    var nama = $("#modal-nama").text().trim();
    var tglLahir = $("#modal-tglLahir").text().trim();
    var noHp = $("#modal-noHp").text().trim();
    var sex = $("#modal-sex").text().trim();

    $.ajax({
      url: BASE_URL + "PasienController/cekPasienTerdaftar",
      type: "POST",
      data: {
        nik: nik,
        noBpjs: noBpjs,
      },
      success: function (response) {
        let resp = JSON.parse(response);

        if (!resp.terdaftar) {
          Swal.fire({
            title: "Pasien tidak terdaftar",
            // text: "Pasien tidak terdaftar sebagai Peserta Anda, ingin tetap melanjutkan proses ini?",
            text: "Pasien tidak ditemukan di klinik ini, ingin tetap melanjutkan proses ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
          }).then((result) => {
            if (result.isConfirmed) {
              console.log(resp);

              // let nama = resp.data?.nama || ""; // Default kosong jika nama tidak ada
              // let alamat = resp.data?.alamat || "";tglLahir

              window.location.href =
                BASE_URL +
                "Pasien-Create?nik=" +
                encodeURIComponent(nik) +
                "&noBpjs=" +
                encodeURIComponent(noBpjs) +
                "&nama=" +
                nama +
                "&tglLahir=" +
                tglLahir +
                "&noHp=" +
                noHp +
                "&sex=" +
                sex;

              // window.location.href = BASE_URL + "Pasien-Create";
            } else {
              // Tutup proses
              Swal.fire("Proses dibatalkan", "", "info");
            }
          });
        } else {
          Swal.fire(
            "Pasien sudah terdaftar",
            "Tidak perlu mendaftarkan ulang.",
            "info",
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire(
          "Error",
          "Terjadi kesalahan saat mengecek data pasien.",
          "error",
        );
        console.error("AJAX Error:", status, error);
      },
    });
  });

  $(".send-data-btn").on("click", function () {
    // Ambil nilai data-id dari elemen yang diklik
    var idReg = $(this).data("id");

    // Tampilkan alert dengan data idReg
    alert(`ID Registrasi: ${idReg}`);

    // Cetak data di konsol (untuk debugging atau log tambahan)
    console.log(`Data ID yang dikirim: ${idReg}`);
  });
});

$(".select2").select2({
  // placeholder: "-- Pilih Propinsi --", // Placeholder saat belum ada pilihan
  allowClear: true, // Tambahkan tombol untuk menghapus pilihan
  width: "100%", // Sesuaikan lebar dengan elemen parent
});

function dropdownEditKota(value, element) {
  if (value != "") {
    $.ajax({
      // url: "<?= base_url('PasienController/get_kota') ?>",
      url: BASE_URL + "PasienController/get_kota",
      type: "post",
      data: { prov_id: value },
      success: function (response) {
        let data = JSON.parse(response);
        let option = '<option value="">-- Pilih Kabupaten --</option>';

        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].kkk_id}">${data[i].keterangan}</option>`;
        }
        $("#" + element).html(option);
        console.log(option);
        // Ambil nilai Kabupaten/Kota dari database
        let selectedKota = $("#edit_kabupaten_id").attr("data-selected");
        let selectedDmsKota = $("#edit_dms_kabupaten_id").attr("data-selected");
        // alert(selectedDmsKota);
        // Pilih data yang sesuai
        if (element === "edit_kabupaten_id" && selectedKota) {
          $("#" + element)
            .val(selectedKota)
            .trigger("change");
        } else if (element === "edit_dms_kabupaten_id" && selectedDmsKota) {
          $("#" + element)
            .val(selectedDmsKota)
            .trigger("change");
        }
      },
    });
  } else {
    $("#" + element).html('<option value="">-- Pilih Kabupaten --</option>');
  }
}

function dropdownEditKecamatan(value, element) {
  if (value != "") {
    $.ajax({
      url: BASE_URL + "PasienController/get_kecamatan",
      type: "post",
      data: { kota_id: value },
      success: function (response) {
        let data = JSON.parse(response);
        let option = '<option value="">-- Pilih Kecamatan --</option>';
        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].kkk_id}">${data[i].keterangan}</option>`;
        }
        $("#" + element).html(option);

        // Ambil nilai Kecamatan dari database
        let selectedKecamatan = $("#edit_kecamatan_id").attr("data-selected");
        let selectedDmsKecamatan = $("#edit_dms_kecamatan_id").attr(
          "data-selected",
        );

        // Pilih data yang sesuai
        if (element === "edit_kecamatan_id" && selectedKecamatan) {
          $("#" + element)
            .val(selectedKecamatan)
            .trigger("change");
        } else if (
          element === "edit_dms_kecamatan_id" &&
          selectedDmsKecamatan
        ) {
          $("#" + element)
            .val(selectedDmsKecamatan)
            .trigger("change");
        }
      },
    });
  } else {
    $("#" + element).html('<option value="">-- Pilih Kecamatan --</option>');
  }
}

function dropdownEditKelurahan(value, element) {
  if (value != "") {
    $.ajax({
      url: BASE_URL + "PasienController/get_kelurahan",
      type: "post",
      data: { kecamatan_id: value },
      success: function (response) {
        let data = JSON.parse(response);
        let option = '<option value="">-- Pilih Kelurahan --</option>';
        for (let i = 0; i < data.length; i++) {
          option += `<option value="${data[i].kkk_id}">${data[i].keterangan}</option>`;
        }
        $("#" + element).html(option);

        // Ambil nilai Kelurahan dari database
        let selectedKelurahan = $("#edit_kelurahan_id").attr("data-selected");
        let selectedDmsKelurahan = $("#edit_dms_kelurahan_id").attr(
          "data-selected",
        );

        // Pilih data yang sesuai
        if (element === "edit_kelurahan_id" && selectedKelurahan) {
          $("#" + element)
            .val(selectedKelurahan)
            .trigger("change");
        } else if (
          element === "edit_dms_kelurahan_id" &&
          selectedDmsKelurahan
        ) {
          $("#" + element)
            .val(selectedDmsKelurahan)
            .trigger("change");
        }
      },
    });
  } else {
    $("#" + element).html('<option value="">-- Pilih Kelurahan --</option>');
  }
}
