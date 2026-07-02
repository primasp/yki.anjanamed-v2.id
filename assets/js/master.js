console.log("Master JS loaded");

$(document).ready(function () {
  $(document).on("click", ".adjust-stok", function () {
    const obatId = $(this).data("id");
    const obatNama = $(this).data("nama");
    const gudangid = $(this).data("gudangid");
    const gudangnm = $(this).data("gudangnm");
    // const adjust = float($(this).data("adjust"));
    const adjust = parseFloat($(this).data("adjust"));
    // alert(adjust);
    const stokKomputer = parseFloat($(this).data("stok-komputer")); // Hitung dari SQL
    // const currentAdjust = parseFloat($(this).data("adjust")); // Ambil nilai adjust saat ini
    // alert(stokKomputer);
    Swal.fire({
      title: `🛠 Adjustment Stok`,
      // title: `🛠 Adjustment Stok <span style="display: block; color: #007bff; font-size: 20px; margin-top: 5px;">${obatNama}</span>`,

      html: ` <div style="max-width: 400px; margin: auto; text-align: center;"> <!-- Menentukan lebar modal -->
                <table style="width: 100%; font-size: 16px; text-align: left; border-collapse: collapse;">

                <!-- Nama Obat -->
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: center; font-size: 18px; font-weight: bold; color: #007bff;">
                            ${obatNama}
                        </td>
                    </tr>
                     <tr>
                        <td colspan="3" style="padding: 10px; text-align: center; font-size: 18px; font-weight: bold; color:rgb(7, 7, 7);">
                            ${gudangnm}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px; font-weight: bold;">📊 Stok Komputer</td>
                        <td>:</td>
                        <td style="padding: 3px; background: #f8f9fa; border-radius: 5px; ">
                            <span style="font-size: 20px; font-weight: bold; color: #28a745;">${stokKomputer}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px; font-weight: bold;">✍️ Stok Fisik</td>
                        <td>:</td>
                        <td style="padding: 3px;">
                            <input type="number" id="stokFisik" class="swal2-input" 
                                   style="width: 100%; padding: 12px; font-size: 18px; text-align: right; border: 1px solid #ccc; border-radius: 5px;" 
                                   placeholder="Masukkan stok fisik">
                        </td>
                    </tr>
                </table>
            </div>
        `,
      showCancelButton: true,
      confirmButtonText: "✅ Simpan",
      cancelButtonText: "❌ Batal",
      allowOutsideClick: false, // Mencegah modal tertutup saat klik di luar
      backdrop: true, // Simulasi data-bs-backdrop="static"
      width: "30%", // Memperlebar modal menjadi 50% dari layar
      preConfirm: () => {
        return {
          stokFisik: parseFloat(document.getElementById("stokFisik").value),
        };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        const stokFisik = result.value.stokFisik;

        if (isNaN(stokFisik) || stokFisik < 0) {
          Swal.fire(
            "⚠️ Error",
            "Masukkan angka stok fisik yang valid!",
            "error",
          );
          return;
        }

        // Hitung nilai adjustment berdasarkan rumus: Adjust = St
        // ok Fisik - Stok Komputer
        // const adjustmentValue = stokFisik - stokKomputer;adjust

        // const adjustmentValue1 = float(stokFisik - stokKomputer);
        // alert(adjust);
        const adjustmentValue = adjust + (stokFisik - stokKomputer);
        // alert(adjustmentValue);
        console.log(adjustmentValue);
        alert(adjustmentValue);

        // Kirim data ke server
        $.ajax({
          url: BASE_URL + "MasterController/adjust_stok",
          type: "POST",
          data: {
            obat_id: obatId,
            adjustment_value: adjustmentValue,
            gudang_id: gudangid,
          },
          success: function (response) {
            Swal.fire(
              "✅ Berhasil",
              "Adjustment stok berhasil diperbarui!",
              "success",
            );
            location.reload();
          },
          error: function () {
            Swal.fire(
              "❌ Error",
              "Gagal memperbarui stok. Coba lagi!",
              "error",
            );
          },
        });
      }
    });
  });

  if ($("#obat-table").length) {
    initDataTable("#obat-table", "Data Obat");
  }

  if ($("#tindakan-table").length) {
    initDataTable("#tindakan-table", "Data Tindakan");
  }

  if ($("#stok-table").length) {
    initDataTable("#stok-table", "Data Stok");
  }

  $("#stok-table").DataTable({
    destroy: true, // Memungkinkan reinitialisasi tabel
    pageLength: 10, // Default jumlah data per halaman
    searching: true,
    ordering: true,
    paging: true,
    responsive: true,
    autoWidth: true,

    // Menonaktifkan sorting pada kolom pertama
    columnDefs: [
      { orderable: false, targets: 0 }, // Nonaktifkan sorting di kolom pertama (KODE OBAT)
    ],

    // Menampilkan dropdown pilihan jumlah data per halaman termasuk "Tampilkan Semua"
    lengthMenu: [
      [10, 25, 50, -1],
      ["10", "25", "50", "Tampilkan Semua"],
    ],

    // Menonaktifkan default sorting (agar kolom pertama tidak otomatis diurutkan)
    order: [],

    dom: "lBfrtip", // Pastikan dropdown "Tampilkan Semua" muncul

    buttons: [
      {
        extend: "excelHtml5",
        text: "Download Excel",
        className: "btn btn-success",
        title: "Data Obat", // Nama file Excel
        exportOptions: {
          columns: ":visible", // Hanya kolom yang terlihat yang diekspor
        },
      },
    ],

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

  // let table = $("#obat-table").DataTable({
  //   destroy: true, // Memungkinkan reinitialisasi tabel
  //   pageLength: 10, // Default jumlah data per halaman
  //   searching: true,
  //   ordering: true,
  //   paging: true,
  //   responsive: true,
  //   autoWidth: true,

  //   // Menonaktifkan sorting pada kolom pertama
  //   columnDefs: [
  //     { orderable: false, targets: 0 }, // Nonaktifkan sorting di kolom pertama (KODE OBAT)
  //   ],

  //   // Menampilkan dropdown pilihan jumlah data per halaman termasuk "Tampilkan Semua"
  //   lengthMenu: [
  //     [10, 25, 50, -1],
  //     ["10", "25", "50", "Tampilkan Semua"],
  //   ],

  //   // Menonaktifkan default sorting (agar kolom pertama tidak otomatis diurutkan)
  //   order: [],

  //   dom: "lBfrtip", // Pastikan dropdown "Tampilkan Semua" muncul

  //   buttons: [
  //     {
  //       extend: "excelHtml5",
  //       text: "Download Excel",
  //       className: "btn btn-success",
  //       title: "Data Obat", // Nama file Excel
  //       exportOptions: {
  //         columns: ":visible", // Hanya kolom yang terlihat yang diekspor
  //       },
  //     },
  //   ],

  //   language: {
  //     lengthMenu: "Tampilkan _MENU_ data per halaman",
  //     zeroRecords: "Data tidak ditemukan",
  //     info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
  //     infoEmpty: "Tidak ada data",
  //     infoFiltered: "(disaring dari _MAX_ total data)",
  //     search: "Cari:",
  //     paginate: {
  //       next: "Berikutnya",
  //       previous: "Sebelumnya",
  //     },
  //   },
  // });

  // console.log("DataTables Loaded:", table);

  function formatRibuan(angka) {
    if (!angka) return "0";
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
  $(".harga-format").on("input", function () {
    let value = $(this)
      .val()
      .replace(/[^0-9]/g, "");
    if (value) {
      $(this).val(parseInt(value).toLocaleString("id-ID").replace(/\./g, ","));
    }
  });

  $("#btn-simpan-obt").click(function () {
    var formData = $("#form-tambah-obat").serialize();

    $.ajax({
      url: BASE_URL + "MasterController/tambahObat",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          alert(response.message);
          location.reload();
        } else {
          alert(response.message);
        }
      },
      error: function () {
        alert("Terjadi kesalahan saat mengirim data");
      },
    });
  });

  // $(".edit-obat").click(function () {
  $(document).on("click", ".edit-obat", function () {
    var obatId = $(this).data("id");
    // alert(obatId);
    // AJAX untuk mengambil data obat berdasarkan ID
    $.ajax({
      url: BASE_URL + "MasterController/getObatById",
      type: "POST",
      data: { obat_id: obatId },
      dataType: "json",
      success: function (data) {
        $("#edit_obat_id").val(data.obat_id);
        $("#edit_nama_obat").val(data.nama);
        $("#edit_kon_satuan").val(data.konversi_satuan);
        $("#edit_hna_besar").val(formatRibuan(data.harga_hna));
        $("#edit_hna_ppn_besar").val(formatRibuan(data.harga_hna_ppn));
        $("#edit_jual_sat").val(formatRibuan(data.harga));
        $("#edit_jual_sat_hna_ppn").val(formatRibuan(data.harga_sat_ppn));
        $("#harga_edit_final").val(formatRibuan(data.harga_jual));

        $("#modalEditObat").removeAttr("aria-hidden").css("display", "block");
        $("#modalEditObat").modal("show");
      },
      error: function () {
        alert("Gagal mengambil data obat");
      },
    });
  });

  $(document).on("click", ".delete-obat", function (e) {
    e.preventDefault(); // Mencegah default action dari <a> link
    let obatId = $(this).data("id");

    Swal.fire({
      title: "Apakah Anda yakin?",
      text: "Data obat ini akan dihapus secara permanen!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Ya, Hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "MasterController/deleteObat",
          type: "POST",
          data: { obat_id: obatId },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire("Deleted!", response.message, "success");
              location.reload(); // Refresh halaman setelah hapus sukses
            } else {
              Swal.fire("Gagal!", response.message, "error");
            }
          },
          error: function () {
            Swal.fire(
              "Error!",
              "Terjadi kesalahan dalam menghapus data.",
              "error",
            );
          },
        });
      }
    });
  });

  $(document).on("click", ".edit-tindakan", function () {
    let id = $(this).data("id");

    $.ajax({
      url: BASE_URL + "MasterController/getTindakanById",
      type: "POST",
      data: { id: id },
      dataType: "JSON",

      success: function (data) {
        $("#edit_layan_id").val(data.layan_id);
        $("#edit_nama_layan1").val(data.nama_layan1);
        $("#edit_nama_layan2").val(data.nama_layan2);
        $("#edit_kategori_id").val(data.kategori_id);
        $("#edit_harga").val(formatNumber(data.harga));

        $("#modalEditTindakan").modal("show");
      },
    });
  });

  $("#btn-update-tindakan").click(function () {
    $.ajax({
      url: BASE_URL + "MasterController/updateTindakan",
      type: "POST",
      data: $("#form-edit-tindakan").serialize(),
      dataType: "JSON",

      success: function (res) {
        if (res.Responcode == "00") {
          Swal.fire("Berhasil", res.Respondesc, "success").then(() =>
            location.reload(),
          );
        }
      },
    });
  });

  $("#btn-simpan-tindakan").click(function () {
    $.ajax({
      url: BASE_URL + "MasterController/simpanTindakan",
      type: "POST",
      data: $("#form-tambah-tindakan").serialize(),
      dataType: "JSON",

      success: function (res) {
        if (res.Responcode == "00") {
          Swal.fire("Berhasil", res.Respondesc, "success").then(() =>
            location.reload(),
          );
        }
      },
    });
  });

  $(document).on("click", ".delete-tindakan", function () {
    let id = $(this).data("id");

    Swal.fire({
      title: "Hapus tindakan?",
      text: "Data tidak dapat dikembalikan",
      icon: "warning",
      showCancelButton: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "MasterController/deleteTindakan",
          type: "POST",
          data: { id: id },
          dataType: "JSON",

          success: function (res) {
            if (res.Responcode == "00") {
              Swal.fire("Berhasil", res.Respondesc, "success").then(() =>
                location.reload(),
              );
            }
          },
        });
      }
    });
  });

  $("#btn-update-obt").click(function () {
    var formData = $("#form-edit-obat").serialize();
    $.ajax({
      url: BASE_URL + "MasterController/updateObat",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          alert(response.message);
          location.reload();
        } else {
          alert(response.message);
        }
      },
      error: function () {
        alert("Terjadi kesalahan saat mengupdate data");
      },
    });
  });
});
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function calculatePPN(value) {
  // alert("eee");
  // alert(value);
  value = value.toString().replace(/[,\.]/g, ""); // Menghapus koma dan titik
  // alert(value);
  console.log(value);
  // return (parseFloat(value) * 1.11).toFixed(2);
  return Math.round(parseFloat(value) * 1.11);
}

function calculateMargin(value, margin) {
  // return (parseFloat(value) * (1 + margin / 100)).toFixed(2);
  return Math.round(parseFloat(value) * (1 + margin / 100));
}

function calculateJualSatuan() {
  // let hnaBesar = $("#hna_besar").val().replace(/,/g, "") || 0;
  let hnaBesar = $("#hna_besar").val();

  let hnaBesarPPN = formatNumber(calculatePPN(hnaBesar));
  $("#hna_ppn_besar").val(hnaBesarPPN);
  let konversi = $("#kon_satuan").val() || 1;

  hnaBesarint = hnaBesar.toString().replace(/[,\.]/g, "");
  console.log("hnaBesar:" + hnaBesarint);
  console.log("konversi:" + konversi);
  let hargaJualSatuan = hnaBesarint / konversi;
  let hargaJualSatuanPPN = calculatePPN(hargaJualSatuan);

  // console.log("hnaBesar" + hnaBesar);
  // console.log("konversi" + konversi);
  // console.log("hargaJualSatuan" + hargaJualSatuan);
  // console.log("hargaJualSatuanPPN" + hargaJualSatuanPPN);
  // console.log("hnaBesarPPN" + hnaBesarPPN);

  $("#jual_sat").val(formatNumber(hargaJualSatuan));
  $("#jual_sat_hna_ppn").val(formatNumber(hargaJualSatuanPPN));

  if ($("#harga_final_checkbox").prop("checked")) {
    let hargaFinal = calculateMargin(hargaJualSatuanPPN, 20);
    $("#harga_final").val(formatNumber(hargaFinal));
    $("#harga_final").prop("readonly", true);
  } else {
    $("#harga_final").val("");
    $("#harga_final").prop("readonly", false);
  }
}

// $("#hna_besar, #kon_satuan").on("input", function () {
//   calculateJualSatuan();
// });

$("#hna_besar, #kon_satuan").on("input change", function () {
  calculateJualSatuan();
});

$("#edit_hna_besar, #edit_kon_satuan").on("input change", function () {
  calculateEditJualSatuan();
});

function calculateEditJualSatuan() {
  let hnaBesarEdit = $("#edit_hna_besar").val();
  let hnaBesarEditPPN = formatNumber(calculatePPN(hnaBesarEdit));

  $("#edit_hna_ppn_besar").val(hnaBesarEditPPN);
  // let hnaBesar = $("#hna_besar").val().replace(/,/g, "") || 0;
  console.log("hnaBesarEditPPN" + hnaBesarEditPPN);
  let konversiEdit = $("#edit_kon_satuan").val() || 1;

  hnaBesarEditint = hnaBesarEdit.toString().replace(/[,\.]/g, "");
  // hnaBesarEditint = hnaBesarEdit.replace(/\./g, ",");

  // hnaBesarEditint = hnaBesarEdit
  //   .replace(/\./g, "#") // titik → placeholder
  //   .replace(/,/g, ".") // koma → titik
  //   .replace(/#/g, ","); // placeholder → koma

  console.log("hnaBesar:" + hnaBesarEditint);
  console.log("konversi:" + konversiEdit);
  let hargaJualSatuanEdit = hnaBesarEditint / konversiEdit;
  // let hargaJualFormatted = hargaJualSatuanEdit.toString().replace(".", ",");

  let hargaJualSatuanEditBulat = Math.round(hargaJualSatuanEdit);
  console.log("dads " + hargaJualSatuanEditBulat);

  let hargaJualSatuanPPNEdit = calculatePPN(hargaJualSatuanEditBulat);
  console.log("hargaJualSatuanPPNEdit " + hargaJualSatuanPPNEdit);

  $("#edit_jual_sat").val(formatNumber(hargaJualSatuanEditBulat));
  $("#edit_jual_sat_hna_ppn").val(formatNumber(hargaJualSatuanPPNEdit));

  if ($("#harga_edit_final_checkbox").prop("checked")) {
    let hargaFinalEdit = calculateMargin(hargaJualSatuanPPNEdit, 20);
    console.log("sshargaFinalEdit" + hargaFinalEdit);
    $("#harga_edit_final").val(formatNumber(hargaFinalEdit));
    $("#harga_edit_final").prop("readonly", true);
  } else {
    $("#harga_edit_final").val("");
    $("#harga_edit_final").prop("readonly", false);
  }
}

function initDataTable(selector, titleFile) {
  $(selector).DataTable({
    destroy: true,
    pageLength: 10,
    searching: true,
    ordering: true,
    paging: true,
    responsive: true,
    autoWidth: false,

    columnDefs: [
      { orderable: false, targets: 0 }, // kolom pertama tidak bisa sort
    ],

    lengthMenu: [
      [10, 25, 50, -1],
      ["10", "25", "50", "Tampilkan Semua"],
    ],

    order: [],

    dom: "lBfrtip",

    buttons: [
      {
        extend: "excelHtml5",
        text: "Download Excel",
        className: "btn btn-success btn-sm",
        title: titleFile,
        exportOptions: {
          columns: ":visible",
        },
      },
    ],

    language: {
      lengthMenu: "Tampilkan _MENU_ data",
      zeroRecords: "Data tidak ditemukan",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(difilter dari _MAX_ total data)",
      search: "Cari:",
      paginate: {
        next: "Berikutnya",
        previous: "Sebelumnya",
      },
    },
  });
}

$("#harga_final_checkbox").change(function () {
  calculateJualSatuan();
});

$("#harga_edit_final_checkbox").change(function () {
  calculateEditJualSatuan();
});
