console.log("Validasi Obat JS loaded");

$("#btn-caripasien").on("click", caripasien);
$("#cari-obat").on("click", cariobatms);

$(document).ready(function () {
  let today = new Date().toISOString().split("T")[0];
  $("#tanggal_cari").val(today);

  $("#btn_cari").on("click", function () {
    $("#modal_cariresep").modal("show");
  });

  $("#btn_simpan").on("click", validasiresep);

  $("#btn_cariobat").on("click", function () {
    $("#modal_cariobat").modal("show");
  });

  // Event listener untuk tombol "Clear"
  $("#btn-clear").on("click", function () {
    $("#input_pencarian").val(""); // Kosongkan input pencarian
    $("#tanggal_cari").val(""); // Reset tanggal ke hari ini
  });
});

function validasiresep() {
  var $table = $("#resultvalidasiobat");
  var dataObat = [];
  var bolehSimpan = true;

  function parseCurrency(v) {
    if (!v) return 0;
    return (
      parseFloat(
        String(v)
          .replace(/[^0-9,]/g, "")
          .replace(",", ".")
      ) || 0
    );
  }

  // Loop baris tabel (jQuery)
  $table.find("tr").each(function () {
    var $row = $(this);
    var $qty = $row.find("td:eq(5) input");
    var $stok = $row.find("td:eq(6)");

    if ($qty.length === 0 || $stok.length === 0) return;

    var cekqty = parseInt($qty.val()) || 0;
    var cekstok = parseInt($.trim($stok.text())) || 0;

    if (cekqty > cekstok) {
      bolehSimpan = false;
      $row.css("background-color", "#ffe6e6");
    } else {
      $row.css("background-color", "");
    }

    dataObat.push({
      kettipe: $.trim($row.find("td:eq(2)").text()),
      obatid: $.trim($row.find("td:eq(3)").text()),
      namaobat: $.trim($row.find("td:eq(4)").text()),
      qty: $qty.val(),
      stok: $.trim($row.find("td:eq(6)").text()),
      satuan: $.trim($row.find("td:eq(7)").text()),
      dosis: $.trim($row.find("td:eq(8)").text()),
      freedosis: $row.find("td:eq(9) input").val() || "",
      signanama: $row.find("td:eq(10) select").val() || "",
      signadokter: $row.find("td:eq(11) input").val() || "",
      catatan: $row.find("td:eq(12) input").val() || "",
      // kirim angka bersih agar backend enak proses
      harga: parseCurrency($row.find("td:eq(13)").text()),
      totharga: parseCurrency($row.find("td:eq(14)").text()),
      urut: $.trim($row.find("td:eq(15)").text()),
      tipeobat: $.trim($row.find("td:eq(16)").text()),
      header: $.trim($row.find("td:eq(18)").text()),
      satuanid: $.trim($row.find("td:eq(19)").text()),
      signaid: $.trim($row.find("td:eq(20)").text()),
      namaracikan:
        $.trim($row.find("td:eq(16)").text()) === "01"
          ? $.trim($row.find("td:eq(4)").text())
          : "",
    });
  });

  if (!bolehSimpan) {
    alert(
      "Stok kurang pada beberapa item. Silakan periksa baris yang disorot."
    );
    return false;
  }

  $.ajax({
    url: BASE_URL + "ValObatController/simpanresep",
    method: "POST",
    dataType: "JSON", // <- terima raw agar aman dari <pre>
    // cache: false,
    data: {
      episodeid: $(".idepisode").val(),
      transid: $(".idtrans").val(),
      pasienid: $(".idpasien").val(),
      transco: $(".idtransco").val(),
      tglresep: $(".tglresep").val(),
      resepke: $(".resepke").val(),
      totharga: parseCurrency($("#totalharga").val()),
      gudangid: $(".idgudang").val(),
      dataObat: dataObat,
    },
    beforeSend: function () {
      // pakai jQuery only
      $(".btn-validasi").prop("disabled", true).text("Menyimpan...");
    },
    complete: function () {
      $(".btn-validasi").prop("disabled", false).text("Validasi");
    },
    success: function (res) {
      if (res.Responcode === "00") {
        Swal.fire(
          "Berhasil",
          res.Respondesc || "Resep berhasil divalidasi.",
          "success"
        ).then(() => {
          location.reload();
        });
      } else {
        Swal.fire(
          "Gagal",
          res.Respondesc || "Terjadi kesalahan saat menyimpan resep.",
          "error"
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error", xhr.responseText);
      Swal.fire(
        "Error",
        "Terjadi kesalahan pada server saat validasi resep.",
        "error"
      );
    },
  });

  return false;
}

function validasiresepx() {
  // Buat validasi resep
  let table = document.getElementById("resultvalidasiobat");
  let dataObat = [];
  let bolehSimpan = true;

  // Fungsi untuk membersihkan format mata uang
  function parseCurrency(value) {
    // return parseFloat(value.replace(/[^\d.-]/g, "")) || 0;

    const normalizedValue = value.replace(/[^0-9,]/g, "").replace(",", ".");
    return parseFloat(normalizedValue) || 0;
  }

  for (let i = 0, row; (row = table.rows[i]); i++) {
    let cekqty = parseInt(row.cells[5].querySelector("input").value);
    let cekstok = parseInt(row.cells[6].innerText);

    if (cekqty > cekstok) {
      bolehSimpan = false;
    }
    console.log(bolehSimpan);

    let obat = {
      kettipe: row.cells[2].innerText,
      obatid: row.cells[3].innerText,
      namaobat: row.cells[4].innerText,
      qty: row.cells[5].querySelector("input").value,
      stok: row.cells[6].innerText,
      satuan: row.cells[7].innerText,
      dosis: row.cells[8].innerText,
      freedosis: row.cells[9].querySelector("input").value,
      signanama: row.cells[10].querySelector("select")
        ? row.cells[10].querySelector("select").value
        : "",
      signadokter: row.cells[11].querySelector("input").value,
      catatan: row.cells[12].querySelector("input").value,
      harga: row.cells[13].innerText,
      totharga: row.cells[14].innerText,

      // harga: parseCurrency(row.cells[13].innerText), // Format harga ke angka
      // totharga: parseCurrency(row.cells[14].innerText), // Format total harga ke angka

      urut: row.cells[15].innerText,
      tipeobat: row.cells[16].innerText,
      header: row.cells[18].innerText,
      satuanid: row.cells[19].innerText,
      signaid: row.cells[20].innerText,
      namaracikan:
        row.cells[16].innerText === "01" ? row.cells[4].innerText : "",
    };

    console.log(obat);
    dataObat.push(obat);
  }

  // alert("1");
  // alert(parseCurrency($("#totalharga").val()));

  // Jika stok kurang, tampilkan error
  if (!bolehSimpan) {
    Swal.fire({
      title: "Stok Kurang",
      text: "Terdapat item dengan jumlah melebihi stok tersedia.",
      icon: "error",
      confirmButtonText: "OK",
    });
    return false;
  }

  $.ajax({
    url: BASE_URL + "ValObatController/simpanresep",
    method: "POST",
    dataType: "JSON",
    cache: false,
    data: {
      episodeid: $(".idepisode").val(),
      transid: $(".idtrans").val(),
      pasienid: $(".idpasien").val(),
      transco: $(".idtransco").val(),
      tglresep: $(".tglresep").val(),
      resepke: $(".resepke").val(),
      // totharga: $("#totalharga").val(),
      totharga: parseCurrency($("#totalharga").val()), // Format total harga ke angka
      gudangid: $(".idgudang").val(),
      dataObat: dataObat,
    },

    beforeSend: function () {
      Swal.fire({
        title: "Menyimpan...",
        text: "Sedang memproses validasi resep",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });
    },

    success: function (response) {
      Swal.close();

      if (response.Responcode == "00") {
        let pesan =
          response.Respondesc ||
          response.message ||
          "Berhasil menyimpan resep.";

        if (
          response.bpjs_response &&
          response.bpjs_response.metaData &&
          response.bpjs_response.metaData.code == "200"
        ) {
          pesan += " Update ke BPJS berhasil.";
        } else if (response.bpjs_response) {
          pesan += " Namun update ke BPJS gagal.";
        }

        Swal.fire({
          title: "Berhasil Validasi",
          text: pesan,
          icon: "success",
          confirmButtonText: "OK",
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          }
        });
      } else {
        Swal.fire({
          title: "Gagal Menyimpan",
          text: response.Respondesc || "Terjadi kesalahan saat menyimpan.",
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    },

    error: function (xhr, status, error) {
      Swal.close();
      Swal.fire({
        title: "Error",
        text: "Gagal mengirim data ke server: " + error,
        icon: "error",
        confirmButtonText: "OK",
      });
    },

    // if (response.Responcode == "00") {
    //   Swal.fire({
    //     title: "Berhasil Validasi",
    //     text: response.message,
    //     icon: "success",
    //     confirmButtonText: "OK",
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       location.reload();
    //     }
    //   });
    // }
    // },
  });
  // }
  return false;
}

function caripasien() {
  // alert($("#tanggal_cari").val());
  $.ajax({
    url: BASE_URL + "ValObatController/caripasien",
    method: "POST",
    dataType: "JSON",
    data: {
      pencarianpasien: $("#input_pencarian").val(),
      tanggal: $("#tanggal_cari").val(), // Tambahkan data tanggal
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#listpasien2").html("");
        $("#listresepall").html("");
        $(".idepisode").val("");
        $(".idtrans").val("");
        $(".idpasien").val("");
        $(".idtransco").val("");
        $(".tglresep").val("");
        $(".resepke").val("");
      } else {
        var loadpasien = "";
        var hasil = data.Responeresult;

        for (var i in hasil) {
          loadpasien += "<tr class='align-middle'>";

          loadpasien += `<td class="text-nowrap"><a href="#" class="text-dark select_pasien fw-semibold" onclick="selectPasien(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}', '${hasil[i].trans_co}')">${hasil[i].trans_co}</a></td>`;

          loadpasien += `<td class="text-nowrap"><a href="#" class="text-dark select_pasien" onclick="selectPasien(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}', '${hasil[i].trans_co}')">${hasil[i].created_date}</a></td>`;

          loadpasien += `<td><a href="#" class="text-dark select_pasien" onclick="selectPasien(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}', '${hasil[i].trans_co}')">${hasil[i].int_pasien_id}</a></td>`;

          if (hasil[i].sex_id === "L") {
            loadpasien += `<td><a href="#" class="text-dark select_pasien" onclick="selectPasien(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}', '${hasil[i].trans_co}')">${hasil[i].nama} <i class='fas fa-mars text-primary'></i></a></td>`;
          } else {
            loadpasien += `<td><a href="#" class="text-dark select_pasien" onclick="selectPasien(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}', '${hasil[i].trans_co}')">${hasil[i].nama} <i class='fas fa-venus text-danger'></i></a></td>`;
          }

          loadpasien += `<td class="text-center">${hasil[i].tgl_lahir}</td>`;
          loadpasien += `<td class="text-center">${hasil[i].umur}</td>`;
          loadpasien += `<td class="text-center">${hasil[i].rekanan}</td>`;
          loadpasien += `<td class="text-center">${hasil[i].poli}</td>`;
          loadpasien += `<td>${hasil[i].dokter}</td>`;
          loadpasien += `<td class="text-center">${hasil[i].frm_ke}</td>`;

          loadpasien += "</tr>";
        }

        $("#listpasien2").html(loadpasien);
      }
    },
  });
}

function selectPasien(event, episodeid, pasienid, transco) {
  event.preventDefault();

  $(".idepisode").val(episodeid);
  $(".idpasien").val(pasienid);
  $(".idtransco").val(transco);

  tampildetailobat(episodeid, pasienid, transco);
}

function tampildetailobat(episodeid, pasienid, transco) {
  //Buat nampilin detail obat
  $.ajax({
    url: BASE_URL + "ValObatController/tampildetailobat",
    method: "POST",
    dataType: "JSON",
    data: {
      episodeid: episodeid,
      pasienid: pasienid,
      transco: transco,
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#listresepall").html("");
        $("#resultresepdokter").html("");
      } else {
        var loadresep = "";
        var hasil = data.Responeresult;
        for (var i in hasil) {
          if (hasil[i].tipe_obat === "00") {
            loadresep += "<tr>";
            loadresep += "<td align='center'>" + hasil[i].ket_tipe + "</td>";
            loadresep += "<td>" + hasil[i].nama_obat + "</td>";
            loadresep += "<td align='right'>" + hasil[i].qty + "</td>";
            loadresep += "<td align='center'>" + hasil[i].satuan + "</td>";
            loadresep += "<td align='center'>" + hasil[i].free_dosis + "</td>";
            loadresep += "<td align='center'>" + hasil[i].signa_nama + "</td>";
            loadresep +=
              "<td align='center'>" + hasil[i].signa_dokter + "</td>";
            loadresep += "<td>" + hasil[i].catatan + "</td>";
            loadresep += "</tr>";
          } else if (hasil[i].tipe_obat === "01") {
            loadresep +=
              "<tr class='table-primary'>" + // Ganti warna dengan kelas Bootstrap
              "<td align='center'>" +
              hasil[i].ket_tipe +
              "</td>" +
              "<td>" +
              hasil[i].nama_obat +
              "</td>" +
              "<td align='right'></td>" +
              "<td align='center'></td>" +
              "<td align='center'>" +
              hasil[i].free_dosis +
              "</td>" +
              "<td align='center'>" +
              hasil[i].signa_nama +
              "</td>" +
              "<td align='center'>" +
              hasil[i].signa_dokter +
              "</td>" +
              "<td>" +
              hasil[i].catatan +
              "</td>" +
              "</tr>";

            // loadresep += "<tr>";
            // loadresep +=
            //   "<td align='center' style='background-color: #94b8b8;'>" +
            //   hasil[i].ket_tipe +
            //   "</td>";
            // loadresep +=
            //   "<td style='background-color: #94b8b8;'>" +
            //   hasil[i].nama_obat +
            //   "</td>";
            // loadresep +=
            //   "<td align='right' style='background-color: #94b8b8;'></td>";
            // loadresep +=
            //   "<td align='center' style='background-color: #94b8b8;'></td>";
            // loadresep +=
            //   "<td align='center' style='background-color: #94b8b8;'>" +
            //   hasil[i].free_dosis +
            //   "</td>";
            // loadresep +=
            //   "<td align='center' style='background-color: #94b8b8;'>" +
            //   hasil[i].signa_nama +
            //   "</td>";
            // loadresep +=
            //   "<td align='center' style='background-color: #94b8b8;'>" +
            //   hasil[i].signa_dokter +
            //   "</td>";
            // loadresep +=
            //   "<td style='background-color: #94b8b8;'>" +
            //   hasil[i].catatan +
            //   "</td>";
            // loadresep += "</tr>";
          } else {
            loadresep +=
              "<tr class='table-success'>" + // Ganti warna dengan kelas Bootstrap
              "<td align='center'>" +
              hasil[i].ket_tipe +
              "</td>" +
              "<td>" +
              hasil[i].nama_obat +
              "</td>" +
              "<td align='right'>" +
              hasil[i].qty +
              "</td>" +
              "<td align='center'>" +
              hasil[i].satuan +
              "</td>" +
              "<td align='center'>" +
              hasil[i].free_dosis +
              "</td>";

            // loadresep += "<tr>";
            // loadresep +=
            //   "<td align='center' style='background-color: #c6ecd9;'>" +
            //   hasil[i].ket_tipe +
            //   "</td>";
            // loadresep +=
            //   "<td style='background-color: #c6ecd9;'>" +
            //   hasil[i].nama_obat +
            //   "</td>";
            // loadresep +=
            //   "<td align='right' style='background-color: #c6ecd9;'>" +
            //   hasil[i].qty +
            //   "</td>";
            // loadresep +=
            //   "<td align='center' style='background-color: #c6ecd9;'>" +
            //   hasil[i].satuan +
            //   "</td>";
            // loadresep +=
            //   "<td align='center' style='background-color: #c6ecd9;'>" +
            //   hasil[i].free_dosis +
            //   "</td>";
            if (hasil[i].signa_nama == null) {
              // loadresep +=
              //   "<td align='center' style='background-color: #c6ecd9;'></td>";
              loadresep += "<td align='center'></td>";
            } else {
              // loadresep +=
              //   "<td align='center' style='background-color: #c6ecd9;'>" +
              //   hasil[i].signa_nama +
              //   "</td>";

              loadresep +=
                "<td align='center'>" + hasil[i].signa_nama + "</td>";
            }
            if (hasil[i].signa_dokter == null) {
              // loadresep +=
              //   "<td align='center' style='background-color: #c6ecd9;'></td>";

              loadresep += "<td align='center'></td>";
            } else {
              // loadresep +=
              //   "<td align='center' style='background-color: #c6ecd9;'>" +
              //   hasil[i].signa_dokter +
              //   "</td>";

              loadresep +=
                "<td align='center'>" + hasil[i].signa_dokter + "</td>";
            }
            // loadresep += "<td align='center' style='background-color: #c6ecd9;'>"+hasil[i].signa_dokter+"</td>";
            // loadresep +=
            //   "<td style='background-color: #c6ecd9;'>" +
            //   hasil[i].catatan +
            //   "</td>";
            // loadresep += "</tr>";

            loadresep += "<td>" + hasil[i].catatan + "</td>";
            loadresep += "</tr>";
          }
        }

        $("#infotransco").html(
          "Detail Resep " +
            hasil[i].trans_co +
            " / " +
            hasil[i].nama +
            " (" +
            hasil[i].int_pasien_id +
            ")"
        );
        $("#listresepall").html(loadresep);
        $("#resultresepdokter").html(loadresep);
      }
    },
  });
  return false;
}

function ambilresep(event) {
  //Buat tarik resep
  event.preventDefault();
  $.ajax({
    url: BASE_URL + "ValObatController/ambilresep",
    method: "POST",
    dataType: "JSON",
    data: {
      episodeid: $(".idepisode").val(),
      pasienid: $(".idpasien").val(),
      transco: $(".idtransco").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
      } else {
        $("#resultdatapasien").html("");
        $("#resultvalidasiobat").html("");

        var hasil = data.Responeresult;
        for (var i in hasil) {
          tambahobat(
            hasil[i].ket_tipe,
            hasil[i].obat_id,
            hasil[i].nama_obat,
            hasil[i].qty,
            hasil[i].stok,
            hasil[i].satuan,
            hasil[i].free_dosis,
            hasil[i].signa_nama,
            hasil[i].signa_dokter,
            hasil[i].catatan,
            hasil[i].harga_satuan,
            hasil[i].total_harga,
            hasil[i].urut,
            hasil[i].tipe_obat,
            hasil[i].header,
            hasil[i].satuan_id,
            hasil[i].signa_id
          );
        }

        datapasien(hasil[i].episode_id, hasil[i].pasien_id, hasil[i].trans_co);
        loadhistory(hasil[i].pasien_id);
        loadalergi(hasil[i].pasien_id);

        $("#listpasien2").html("");
        $("#listresepall").html("");
        $("#infotransco").html("Detail Resep");
      }
    },
  });
}

function datapasien(episodeid, pasienid, transco) {
  //Buat narik data pasien yang dipilih
  $.ajax({
    url: BASE_URL + "ValObatController/datapasien",
    method: "POST",
    dataType: "JSON",
    data: {
      episodeid: episodeid,
      pasienid: pasienid,
      transco: transco,
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#resultdatapasien").html("");
      } else {
        var loadpasien = "";
        var hasil = data.Responeresult;

        loadpasien += `<tr style="heigth: 10px">`;
        loadpasien += `<td style="width: 30%; font-weight:bold">No. RM</td>`;
        loadpasien += "<td>" + hasil.int_pasien_id + "</td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">Nama Pasien</td>`;
        if (hasil.sex_id === "L") {
          // loadpasien += `<td>`+hasil.nama+`  <i class='fa-solid fas fa-mars text-primary align-top' style='line-height: 0.5'></i></td>`;
          loadpasien += `<td>` + hasil.nama + `</td>`;
        } else {
          // loadpasien += `<td>`+hasil.nama+`  <i class='fa-solid fas fa-venus text-danger align-top' style='line-height: 0.5'></i></td>`;
          loadpasien += `<td>` + hasil.nama + `</td>`;
        }

        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">Tgl Lahir</td>`;
        loadpasien +=
          "<td>" + hasil.tgl_lahir + " ( " + hasil.umur + " )" + "</td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">Resep Dari</td>`;
        loadpasien += "<td>" + hasil.dokter + "</td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">Poli</td>`;
        loadpasien += "<td>" + hasil.poli + "</td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">Diagnosa</td>`;
        loadpasien += "<td>" + hasil.icd10 + " - " + hasil.diagnosa + "</td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">Provider</td>`;
        loadpasien += "<td>" + hasil.rekanan + "</td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">No. BPJS</td>`;
        loadpasien += "<td></td>";
        loadpasien += "</tr>";

        loadpasien += "<tr>";
        loadpasien += `<td style="width: 30%; font-weight:bold">No. SEP</td>`;
        loadpasien += "<td></td>";
        loadpasien += "</tr>";

        $(".idlokasi").val(hasil.lokasi_id);
        $(".idepisode").val(hasil.episode_id);
        $(".idtrans").val(hasil.trans_id);
        $(".idpasien").val(hasil.pasien_id);
        $(".idtransco").val(hasil.trans_co);
        $(".tglresep").val(hasil.tanggal);
        $(".resepke").val(hasil.frm_ke);

        $("#resultdatapasien").html(loadpasien);
      }
    },
  });
  return false;
}

function loadhistory(pasienid) {
  //Buat cari riwayat obat
  $.ajax({
    url: BASE_URL + "ValObatController/loadhistory",
    method: "POST",
    dataType: "JSON",
    data: {
      pasienid: pasienid,
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
      } else {
        var loadhistory = "";
        var hasil = data.Responeresult;
        for (var i in hasil) {
          loadhistory += "<tr>";
          loadhistory += `<td align='center'><a href="#" class="select_riwayat_obat" style="color:black; font-size: 1em" onclick="selectRiwayatObat(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}')">${hasil[i].tgl_masuk}</a></td>`;
          loadhistory += `<td><a href="#" class="select_riwayat_obat" style="color:black; font-size: 1em" onclick="selectRiwayatObat(event, '${hasil[i].episode_id}', '${hasil[i].pasien_id}')">${hasil[i].poli}</a></td>`;
          loadhistory += "</tr>";
        }

        $("#resultriwayattransaksi").html(loadhistory);
      }
    },
  });
  return false;
}

function loadalergi(pasienid) {
  //Buat cari riwayat alergi
  $.ajax({
    url: BASE_URL + "ValObatController/loadalergi",
    method: "POST",
    dataType: "JSON",
    data: {
      pasienid: pasienid,
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
      } else {
        var loadalergi = "";
        var hasil = data.Responeresult;
        for (var i in hasil) {
          loadalergi += "<tr>";
          loadalergi += "<td>" + hasil[i].alergi + "</td>";
          loadalergi += "</tr>";
        }

        $("#resultalergi").html(loadalergi);
      }
    },
  });
  return false;
}

function tambahobat(
  vkettipe,
  vobatid,
  vnamaobat,
  vqty,
  vstok,
  vsatuan,
  vdosis,
  vsignanama,
  vsignadokter,
  vcatatan,
  vharga,
  vtotharga,
  vurut,
  vtipeobat,
  vheader,
  vsatuanid,
  vsignaid
) {
  // Buat masing-masing row
  let table = document.getElementById("resultvalidasiobat");
  let newRow = table.insertRow(table.rows.length);

  // Tentukan warna berdasarkan nilai vtipeobat

  // if (vtipeobat === "01") {
  //   newRow.style.backgroundColor = "#94b8b8";
  // } else if (vtipeobat === "02" || vtipeobat === "03") {
  //   newRow.style.backgroundColor = "#c6ecd9";
  // }

  // Tentukan warna berdasarkan nilai vtipeobat
  if (vtipeobat === "01") {
    newRow.classList.add("table-primary"); // Menggunakan kelas Bootstrap
  } else if (vtipeobat === "02" || vtipeobat === "03") {
    newRow.classList.add("table-success"); // Menggunakan kelas Bootstrap
  } else {
    newRow.classList.add("table-light"); // Warna default
  }

  // Insert data row
  if (vtipeobat === "00") {
    newRow.insertCell(0).innerHTML = '<input type="checkbox">';
  } else {
    newRow.insertCell(0).innerHTML = "";
  }
  newRow.cells[0].style.verticalAlign = "middle";

  if (vtipeobat === "00" || vtipeobat === "01") {
    newRow.insertCell(1).innerHTML =
      "<a class='m-1 btn-hapusobat' role='button' onclick='hapusobat(this)'><i class='fas fa-times'></i></a>";
  } else {
    newRow.insertCell(1).innerHTML = "";
  }

  newRow.insertCell(2).innerHTML = vkettipe; // Tipe teks
  newRow.cells[2].style.verticalAlign = "middle";

  let td3 = newRow.insertCell(3);
  td3.style.display = "none";
  td3.innerHTML = vobatid; // Obat id
  td3.style.verticalAlign = "middle";

  newRow.insertCell(4).innerHTML = vnamaobat; // Nama obat
  newRow.cells[4].style.verticalAlign = "middle";

  let td5 = newRow.insertCell(5); // Jumlah
  if (vtipeobat === "01") {
    td5.innerHTML =
      '<input type="hidden" min="0" style="width: 80px; height: 20px" class="text-right" onkeypress="return onlyNumberKey(event)" value=' +
      vqty +
      "></input>";
  } else {
    td5.align = "center";
    td5.style.padding = 0;
    td5.innerHTML =
      '<input type="number" min="0" style="width: 80px; height: 20px" class="text-right" onkeypress="return onlyNumberKey(event)" value=' +
      vqty +
      "></input>";
    td5.style.verticalAlign = "middle";

    // Tambahkan event listener untuk perubahan nilai pada input
    let inputQty = td5.querySelector("input");
    inputQty.addEventListener("input", function () {
      let qty = parseFloat(inputQty.value) || 0;
      let harga = parseFloat(td13.innerHTML) || 0;
      td14.innerHTML = (qty * harga).toFixed(2); // Menghitung dan menampilkan harga total
      hitungTotalHarga();
    });
  }

  if (vtipeobat === "01") {
    // Stok
    let td6 = newRow.insertCell(6);
    td6.innerHTML = "";
  } else {
    let td6 = newRow.insertCell(6);
    td6.align = "center";
    td6.innerHTML = vstok;
    td6.style.verticalAlign = "middle";
  }

  let td7 = newRow.insertCell(7); // Satuan
  if (vtipeobat === "01") {
    td7.innerHTML = "";
  } else {
    td7.align = "center";
    td7.innerHTML = vsatuan;
    td7.style.verticalAlign = "middle";
  }

  newRow.insertCell(8).innerHTML = vdosis; // Dosis
  newRow.cells[8].style.verticalAlign = "middle";

  let td9 = newRow.insertCell(9); // Free Dosis
  td9.align = "center";
  td9.style.padding = 0;
  td9.innerHTML =
    '<input type="text" style="width: 80px; height: 20px"></input>';
  td9.style.verticalAlign = "middle";

  let signa = "";
  $.ajax({
    url: BASE_URL + "ValObatController/loadsigna",
    method: "POST",
    dataType: "JSON",
    cache: false,
    async: false,
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
      } else {
        signa = data.Responresult;
      }
    },
  });
  var itemsigna = "";
  signa.map(function (d) {
    let selected = d.signa_id === vsignaid ? "selected" : "";
    itemsigna +=
      '<option value="' +
      d.signa_id +
      '" ' +
      selected +
      ">" +
      d.signa +
      "</option>";
  });

  if (vtipeobat === "00" || vtipeobat === "01") {
    let td10 = newRow.insertCell(10); // Signa Nama
    td10.align = "center";
    td10.style.padding = 0;
    td10.innerHTML =
      '<select id="signa" style="margin-left: 10px;">' +
      itemsigna +
      "</select>";
    td10.style.verticalAlign = "middle";

    let selectSigna = td10.querySelector("#signa");
    selectSigna.addEventListener("change", function () {
      td20.innerHTML = selectSigna.value;
    });
  } else {
    let td10 = newRow.insertCell(10); // Signa Nama
    td10.innerHTML = "";
  }

  let td11 = newRow.insertCell(11); // Signa Dokter
  td11.align = "center";
  td11.style.padding = 0;
  td11.innerHTML =
    '<input type="text" value="' +
    vsignadokter +
    '" style="margin-left: 10px;"></input>';
  td11.style.verticalAlign = "middle";

  let td12 = newRow.insertCell(12); // Catatan
  td12.align = "center";
  td12.style.padding = 0;
  td12.innerHTML =
    '<input type="text" value="' +
    vcatatan +
    '" style="margin-left: 10px;"></input>';
  td12.style.verticalAlign = "middle";

  let td13 = newRow.insertCell(13); // Harga satuan
  td13.align = "right";
  if (vharga == null) {
    vharga = 0;
  }
  td13.innerHTML = vharga;
  td13.style.verticalAlign = "middle";

  let td14 = newRow.insertCell(14); // Harga total
  td14.align = "right";
  if (vtotharga == null) {
    vtotharga = 0;
  }
  td14.innerHTML = vtotharga;
  td14.style.verticalAlign = "middle";

  let td15 = newRow.insertCell(15); // urut
  td15.align = "right";
  td15.innerHTML = vurut;
  td15.style.verticalAlign = "middle";
  td15.style.display = "none";

  let td16 = newRow.insertCell(16); // tipe
  td16.align = "right";
  td16.innerHTML = vtipeobat;
  td16.style.verticalAlign = "middle";
  td16.style.display = "none";

  let td17 = newRow.insertCell(17);
  td17.innerHTML = vobatid; // Obat id
  td17.style.verticalAlign = "middle";
  td17.style.display = "none";

  let td18 = newRow.insertCell(18);
  td18.innerHTML = vheader; // header
  td18.style.verticalAlign = "middle";
  td18.style.display = "none";

  let td19 = newRow.insertCell(19);
  td19.innerHTML = vsatuanid; // satuan id
  td19.style.verticalAlign = "middle";
  td19.style.display = "none";

  let td20 = newRow.insertCell(20);
  td20.innerHTML = vsignaid; // signa id
  td20.style.verticalAlign = "middle";
  td20.style.display = "none";

  hitungTotalHarga();

  $("#modal_cariobat").modal("hide");
}

function hitungTotalHarga() {
  let table = document.getElementById("resultvalidasiobat");
  let totalHarga = 0;
  let vurutbaru = 1;

  // Loop melalui semua baris di tabel dan tambahkan nilai dari total harga
  for (let i = 0; i < table.rows.length; i++) {
    let td14 = table.rows[i].cells[14]; // Mengambil sel total harga
    let hargaTotal = parseFloat(td14.innerHTML) || 0;
    totalHarga += hargaTotal;

    table.rows[i].cells[15].innerHTML = vurutbaru;
    vurutbaru++;
  }

  $("#totalharga").val(formatRupiah(totalHarga.toFixed(2)));
}

function formatRupiah(angka) {
  if (isNaN(angka)) return "0";
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(angka);
}

function hapusobat(button) {
  // dapetin parent dari row
  let row = button.parentNode.parentNode;

  let vobatid = row.cells[3].innerHTML;
  let vnamaobat = row.cells[4].innerHTML;
  let vtipeobat = row.cells[16].innerHTML;

  Swal.fire({
    title: "Hapus Obat ?",
    text: vnamaobat,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "OK",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      // Menghapus baris dari tabel
      if (vtipeobat === "00") {
        row.parentNode.removeChild(row);
      } else {
        row.parentNode.removeChild(row);

        let table = document.getElementById("resultvalidasiobat");
        for (let i = table.rows.length - 1; i >= 0; i--) {
          let currentRow = table.rows[i];
          let currentObatId = currentRow.cells[18].innerHTML; //header
          let currentTipeObat = currentRow.cells[16].innerHTML; //tipe

          if (currentObatId === vobatid) {
            if (currentTipeObat === "02") {
              table.deleteRow(i);
            } else {
              let signa = "";
              $.ajax({
                url: BASE_URL + "ValObatController/loadsigna",
                method: "POST",
                dataType: "JSON",
                cache: false,
                async: false,
                success: function (data) {
                  if (data.Responcode == "01") {
                    toastr["info"](data.Respondesc, "INFORMATION");
                  } else {
                    signa = data.Responresult;
                  }
                },
              });

              let itemsignanya = "";
              signa.map(function (d) {
                itemsignanya +=
                  '<option value="' + d.signa_id + '">' + d.signa + "</option>";
              });

              table.rows[i].cells[0].innerHTML = '<input type="checkbox">';
              table.rows[i].cells[1].innerHTML =
                "<a class='m-1 btn-hapusobat' role='button' onclick='hapusobat(this)'><i class='fas fa-times'></i></a>";
              table.rows[i].cells[10].align = "center"; //signa nama
              table.rows[i].cells[10].style.padding = 0;
              table.rows[i].cells[10].innerHTML =
                '<select id="signa" style="margin-left: 10px;" onchange="getSignaPilih(this)">' +
                itemsignanya +
                "</select>";
              table.rows[i].cells[10].style.verticalAlign = "middle";
              table.rows[i].cells[16].innerHTML = "00"; //tipe
              table.rows[i].cells[18].innerHTML = "0"; //header
              table.rows[i].style.backgroundColor = "";
            }
          }
        }
      }

      hitungTotalHarga();
    }
  });
}

function getSignaPilih(selectSigna) {
  var td20 = selectSigna.closest("tr").cells[20];
  td20.innerHTML = selectSigna.value;
}

function cariobatms() {
  //Buat cari master obat
  $.ajax({
    url: BASE_URL + "ValObatController/cariobatms",
    method: "POST",
    dataType: "JSON",
    cache: false,
    data: {
      pencarianobat: $("#input_cariobat").val(),
    },
    success: function (data) {
      if (data.Responcode == "01") {
        toastr["info"](data.Respondesc, "INFORMATION");
        $("#listobatms").html("");
      } else {
        var loadobatms = "";
        var hasil = data.Responeresult;
        for (var i in hasil) {
          loadobatms += "<tr>";
          loadobatms += `<td style="width: 90%"><a href="#" class="select_obat" style="color:black" ondblclick="tambahobat('', '${hasil[i].obat_id}', '${hasil[i].nama_obat}', 1, ${hasil[i].stok}, '${hasil[i].satuan}', '', '', '', '', ${hasil[i].harga}, ${hasil[i].harga}, 0, '00', '0', '${hasil[i].satuan_id}', '')">${hasil[i].nama_obat}</a></td>`;
          loadobatms +=
            `<td style="width: 10%" align='right'>` + hasil[i].stok + `</td>`;
          loadobatms += "</tr>";
        }

        $("#listobatms").html(loadobatms);
      }
    },
  });
  return false;
}

document.getElementById("btn_bukaracik").addEventListener("click", function () {
  // alert("11");
  // Reset array setiap kali tombol ditekan
  selectedObatArray = [];
  selectedObatArray2 = [];

  // Ambil tabel dan iterasi setiap row
  let table = document.getElementById("resultvalidasiobat");
  let rows = table.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    let checkbox = rows[i].cells[0].getElementsByTagName("input")[0];

    // Jika checkbox di-check, ambil data dari row tersebut
    if (checkbox && checkbox.checked) {
      let obatData = {
        vkettipe: rows[i].cells[2].innerHTML,
        vobatid: rows[i].cells[3].innerHTML,
        vnamaobat: rows[i].cells[4].innerHTML,
        vqty: rows[i].cells[5].getElementsByTagName("input")[0].value,
        vstok: rows[i].cells[6].innerHTML,
        vsatuan: rows[i].cells[7].innerHTML,
        vdosis: rows[i].cells[8].innerHTML,
        vfreedosis: rows[i].cells[9].getElementsByTagName("input")[0].value,
        vsigna: rows[i].cells[10].innerHTML,
        vsignateks: rows[i].cells[11].getElementsByTagName("input")[0].value,
        vcatatan: rows[i].cells[12].getElementsByTagName("input")[0].value,
        vharga: rows[i].cells[13].innerHTML,
        vtotharga: rows[i].cells[14].innerHTML,
        vurut: rows[i].cells[15].innerHTML,
        vtipeobat: rows[i].cells[16].innerHTML,
        vsatuanid: rows[i].cells[19].innerHTML,
      };
      console.log(obatData);
      // Masukkan data obat ke dalam array
      selectedObatArray.push(obatData);
    } else {
      let obatData2 = {
        vkettipe: rows[i].cells[2].innerHTML,
        vobatid: rows[i].cells[3].innerHTML,
        vnamaobat: rows[i].cells[4].innerHTML,
        vqty: rows[i].cells[5].getElementsByTagName("input")[0].value,
        vstok: rows[i].cells[6].innerHTML,
        vsatuan: rows[i].cells[7].innerHTML,
        vdosis: rows[i].cells[8].innerHTML,
        vfreedosis: rows[i].cells[9].getElementsByTagName("input")[0].value,
        vsigna: rows[i].cells[10].innerHTML,
        vsignateks: rows[i].cells[11].getElementsByTagName("input")[0].value,
        vcatatan: rows[i].cells[12].getElementsByTagName("input")[0].value,
        vharga: rows[i].cells[13].innerHTML,
        vtotharga: rows[i].cells[14].innerHTML,
        vurut: rows[i].cells[15].innerHTML,
        vtipeobat: rows[i].cells[16].innerHTML,
        vsatuanid: rows[i].cells[19].innerHTML,
      };
      selectedObatArray2.push(obatData2);
    }
  }

  loadpembungkus();
  loadsigna();

  $("#modal_buatracik").modal("show");
});

function loadpembungkus() {
  $.ajax({
    // url: "<?= base_url('valresep/loadpembungkus') ?>",
    url: BASE_URL + "ValObatController/loadpembungkus",
    method: "POST",
    dataType: "JSON",
    cache: false,
    success: function (data) {
      console.log(data);
      if (data.Responcode == "00") {
        var bungkus = "";
        data.Responresult.map(function (d) {
          bungkus +=
            '<option value="' +
            d.obat_id +
            '" data-satuan="' +
            d.keterangan +
            '" data-satuanid="' +
            d.satuan_id +
            '" data-stok="' +
            d.stok +
            '">' +
            d.nama_obat +
            "</option>";
        });
        $("#master-bungkus").html(bungkus);
      }
    },
  });
  return false;
}

function loadsigna() {
  $.ajax({
    url: BASE_URL + "ValObatController/loadsigna",
    method: "POST",
    dataType: "JSON",
    cache: false,
    success: function (data) {
      console.log(data);
      if (data.Responcode == "00") {
        var signa = "";
        data.Responresult.map(function (d) {
          signa +=
            '<option value="' +
            d.signa_id +
            '" data-signa="' +
            d.signa +
            '">' +
            d.signa +
            "</option>";
        });
        $("#master-signa").html(signa);
      }
    },
  });
  return false;
}

function pilihpembungkus(element) {
  var selectedOption = $(element).find("option:selected");

  var stok = selectedOption.data("stok");

  $("#stokbungkus").val(stok);
}

function buatracik(e) {
  // Mendapatkan tanggal dan waktu saat ini dalam format YYYYMMDD HH24MI
  let currentDate = new Date();
  let year = currentDate.getFullYear();
  let month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Menambahkan 0 di depan jika bulan kurang dari 10
  let day = ("0" + currentDate.getDate()).slice(-2); // Menambahkan 0 di depan jika hari kurang dari 10
  let hours = ("0" + currentDate.getHours()).slice(-2); // Menambahkan 0 di depan jika jam kurang dari 10
  let minutes = ("0" + currentDate.getMinutes()).slice(-2); // Menambahkan 0 di depan jika menit kurang dari 10

  // buat header racikan
  let obatid = "RA" + year + month + day + hours + minutes;
  let namaRacik = document.getElementById("namaracik").value;
  let jmlRacik = document.getElementById("jmlracik").value;
  let selectedSigna = $("#master-signa option:selected").data("signa");
  let selectedSignaid = $("#master-signa").val();
  let arrayracik = [];
  let racik = {
    vkettipe: "R",
    vobatid: obatid,
    vnamaobat: namaRacik,
    vqty: 0,
    vstok: "",
    vsatuan: "",
    vdosis: "",
    vfreedosis: "",
    vsigna: selectedSigna,
    vsignateks: "",
    vcatatan: "",
    vharga: 0,
    vtotharga: 0,
    vurut: 0,
    vtipeobat: "01",
    vheader: "0",
    vsatuanid: "",
    vsignaid: selectedSignaid,
  };

  arrayracik.push(racik);

  // buat pembungkus
  let selectedBungkusId = $("#master-bungkus").val(); // Ambil ID obat yang dipilih dari dropdown
  let selectedBungkusNama = $("#master-bungkus option:selected").text(); // Ambil nama obat yang dipilih dari dropdown
  let selectedKeterangan = $("#master-bungkus option:selected").data("satuan");
  let selectedSatuanid = $("#master-bungkus option:selected").data("satuanid");
  let selectedStok = $("#master-bungkus option:selected").data("stok");

  let bungkus = {
    vkettipe: "",
    vobatid: selectedBungkusId,
    vnamaobat: selectedBungkusNama,
    vqty: jmlRacik,
    vstok: selectedStok,
    vsatuan: selectedKeterangan,
    vdosis: "",
    vfreedosis: "",
    vsigna: "",
    vsignateks: "",
    vcatatan: "",
    vharga: 0,
    vtotharga: 0,
    vurut: 0,
    vtipeobat: "02",
    vheader: obatid,
    vsatuanid: selectedSatuanid,
    vsignaid: "",
  };

  arrayracik.push(bungkus);

  // buat isi racikan
  selectedObatArray.map(function (e) {
    let dataobat = {
      vkettipe: e.vkettipe,
      vobatid: e.vobatid,
      vnamaobat: e.vnamaobat,
      vqty: e.vqty,
      vstok: e.vstok,
      vsatuan: e.vsatuan,
      vdosis: e.vdosis,
      vfreedosis: e.vfreedosis,
      vsigna: "",
      vsignateks: "",
      vcatatan: "",
      vharga: e.vharga,
      vtotharga: e.vtotharga,
      vurut: e.vurut,
      vtipeobat: "03",
      vheader: obatid,
      vsatuanid: e.vsatuanid,
      vsignaid: "",
    };
    arrayracik.push(dataobat);
  });

  arrayracik.map(function (e) {
    tambahobat(
      e.vkettipe,
      e.vobatid,
      e.vnamaobat,
      e.vqty,
      e.vstok,
      e.vsatuan,
      e.vdosis,
      e.vsigna,
      e.vsignateks,
      e.vcatatan,
      e.vharga,
      e.vtotharga,
      e.vurut,
      e.vtipeobat,
      e.vheader,
      e.vsatuanid,
      e.vsignaid
    );
  });

  selectedObatArray2.concat(arrayracik);

  hapusobatlama();
  hitungTotalHarga();
}

function hapusobatlama() {
  let table = document.getElementById("resultvalidasiobat");
  for (let i = table.rows.length - 1; i >= 0; i--) {
    let row = table.rows[i];
    let checkboxCell = row.cells[0];
    let checkbox = checkboxCell.querySelector("input[type='checkbox']");

    if (checkbox && checkbox.checked) {
      table.deleteRow(i);
    }
  }
}
