// const templateHeadersTindakan = [
//   "Nama_Layanan",
//   "Jenis_Tindakan",
//   "Kategori_Tindakan",
//   "Harga_Layanan",
// ]; // Sesuaikan dengan template

const templateHeaders = {
  tindakan: [
    "Nama_Layanan",
    "Jenis_Tindakan",
    "Kategori_Tindakan",
    "Harga_Layanan",
  ],
  obat: [
    "NAMA_OBAT",
    "SATUAN_JUAL",
    "KONVERSI_SATUAN",
    "HARGA_HNA",
    "HARGA_HNA_PPN",
    "HARGA_SATUAN",
    "HARGA_SATUAN_PPN",
    "HARGA_JUAL",
  ],
};

function loadFile(event, type) {
  // alert("Oke");
  var file = event.target.files[0];
  if (!file) {
    Swal.fire({
      icon: "warning",
      title: "Tidak ada file yang dipilih!",
      text: "Harap pilih file sebelum melanjutkan.",
    });
    return;
  }

  var fileNameSpan =
    type === "tindakan" ? "#fileNameTindakan" : "#fileNameObat";
  var previewTable =
    type === "tindakan" ? "#previewTableTindakan" : "#previewTableObat";
  var previewSection =
    type === "tindakan" ? "#previewSectionTindakan" : "#previewSectionObat";
  var expectedHeaders = templateHeaders[type];

  $(fileNameSpan).text(file.name);
  let reader = new FileReader();

  reader.onload = function (event) {
    var data = new Uint8Array(event.target.result);
    var workbook = XLSX.read(data, { type: "array" });

    if (workbook.SheetNames.length === 0) {
      Swal.fire({
        icon: "error",
        title: "File tidak valid!",
        text: "File Excel tidak mengandung sheet yang dapat dibaca.",
      });
      return;
    }

    var firstSheet = workbook.SheetNames[0];
    var worksheet = workbook.Sheets[firstSheet];
    var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

    if (jsonData.length === 0) {
      Swal.fire({
        icon: "error",
        title: "File Kosong!",
        text: "File Excel yang diunggah tidak berisi data.",
      });
      return;
    }

    var uploadedHeaders = jsonData[0];

    if (JSON.stringify(uploadedHeaders) !== JSON.stringify(expectedHeaders)) {
      Swal.fire({
        icon: "error",
        title: "Format Kolom Tidak Sesuai!",
        text: "Harap gunakan template yang benar untuk mengunggah data.",
      });
      return;
    }

    var thead =
      "<tr>" +
      expectedHeaders.map((header) => `<th>${header}</th>`).join("") +
      "</tr>";
    $(previewTable + " thead").html(thead);

    var tbody = jsonData
      .slice(1)
      .map((row) => {
        return (
          "<tr>" +
          expectedHeaders
            .map((_, index) => `<td>${row[index] || ""}</td>`)
            .join("") +
          "</tr>"
        );
      })
      .join("");
    $(previewTable + " tbody").html(tbody);

    $(previewSection).show();
  };

  reader.readAsArrayBuffer(file);
}

$(document).ready(function () {
  $("#fileTindakan").on("change", function (event) {
    loadFile(event, "tindakan");
  });

  $("#fileObat").on("change", function (event) {
    loadFile(event, "obat");
  });

  $("#saveButtonUploadTindakan").on("click", function () {
    var rows = [];

    $("#previewTableTindakan tbody tr").each(function () {
      var rowData = {};
      rowData["nama_layan1"] = $(this).find("td:nth-child(1)").text();
      rowData["nama_layan2"] = $(this).find("td:nth-child(2)").text();
      rowData["kategori_id"] = $(this).find("td:nth-child(3)").text();
      rowData["harga"] = $(this).find("td:nth-child(4)").text(); // Ambil harga dari kolom Excel
      rows.push(rowData);
    });

    // Cek jika tidak ada data
    if (rows.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Tidak ada data!",
        text: "Pastikan Anda telah mengunggah file yang benar.",
      });
      return;
    }

    Swal.fire({
      title: "Konfirmasi Upload",
      text: "Apakah Anda yakin ingin menyimpan data iniz ke database?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, Simpan!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "AdminController/uploadTindakan",
          type: "POST",
          data: JSON.stringify({ data: rows }),
          contentType: "application/json",
          success: function (response) {
            let message = response.message;

            if (response.duplicates && response.duplicates.length > 0) {
              message += `\n\nData yang sudah ada: ${response.duplicates.join(
                ", "
              )}`;
            }

            if (
              response.inserted_layan_ids &&
              response.inserted_layan_ids.length > 0
            ) {
              message += `\n\nLayanan baru yang berhasil dimasukkan: ${response.inserted_layan_ids.join(
                ", "
              )}`;
            }

            Swal.fire({
              icon: response.status === "success" ? "success" : "error",
              title: response.status === "success" ? "Berhasil!" : "Gagal!",
              text: message,
            }).then(() => {
              if (response.status === "success") location.reload();
            });
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Terjadi Kesalahan!",
              text: "Gagal menyimpan data ke database.",
            });
          },
        });
      }
    });
  });

  $("#saveButtonUploadObat").on("click", function () {
    var rows = [];
    $("#previewTableObat tbody tr").each(function () {
      var rowData = {};
      rowData["nama"] = $(this).find("td:nth-child(1)").text();
      // alert(rowData["nama_obat"]);

      rowData["satuan_kecil_id"] = $(this).find("td:nth-child(2)").text();

      rowData["konversi_satuan"] = $(this).find("td:nth-child(3)").text();
      rowData["harga_hna"] = $(this).find("td:nth-child(4)").text();
      rowData["harga_hna_ppn"] = $(this).find("td:nth-child(5)").text();
      rowData["harga"] = $(this).find("td:nth-child(6)").text();
      rowData["harga_sat_ppn"] = $(this).find("td:nth-child(7)").text();

      rowData["harga_jual"] = $(this).find("td:nth-child(8)").text();
      rows.push(rowData);
    });

    if (rows.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Tidak ada data!",
        text: "Pastikan Anda telah mengunggah file yang benar.",
      });
      return;
    }

    Swal.fire({
      title: "Konfirmasi Upload",
      text: "Apakah Anda yakin ingin menyimpan data ini ke database?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, Simpan!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "AdminController/uploadObat",
          type: "POST",
          data: JSON.stringify({ data: rows }),
          contentType: "application/json",
          success: function (response) {
            let message = response.message;
            if (response.duplicates && response.duplicates.length > 0) {
              message += `\n\nData yang sudah ada: ${response.duplicates.join(
                ", "
              )}`;
            }
            Swal.fire({
              icon: response.status === "success" ? "success" : "error",
              title: response.status === "success" ? "Berhasil!" : "Gagal!",
              text: message,
            }).then(() => {
              if (response.status === "success") location.reload();
            });
          },

          error: function () {
            Swal.fire({
              icon: "error",
              title: "Terjadi Kesalahan!",
              text: "Gagal menyimpan data ke database.",
            });
          },
        });
      }
    });
  });
});
