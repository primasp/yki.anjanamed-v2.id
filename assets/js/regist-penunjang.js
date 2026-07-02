$(document).ready(function () {
  console.log("Regist-Penunjang JS loaded");

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
          url: BASE_URL + "Daftar-Poli/Cari-Pasien",
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
              })
            );
          },
        });
      },
      select: function (event, ui) {
        if (type === "nama") {
          $("#noRm").val(ui.item.int_pasien_id); // Isi No. RM sesuai Nama Pasien
        } else {
          $("#namaPas").val(ui.item.nama); // Isi Nama Pasien sesuai No. RM
        }
        $("#tgl_lahir").val(ui.item.tgl_lahir); // Isi Tanggal Lahir
        $("#sex_id").val(ui.item.sex_id);
        $("#alamat").val(ui.item.alamat1);
        $("#pasien_id").val(ui.item.pasien_id);
        $("#no_bpjs").val(ui.item.no_kartuprov);
      },
    });
  }

  $("#pembiayaan").on("change", function () {
    if ($(this).val() === "BPJS") {
      console.log("Pembiayaan BPJS");
      $("#dataBPJS").show();
    } else if ($(this).val() === "PROGRAM") {
      console.log("Pembiayaan PROGRAM");
      $("#dataBPJS").hide();
    } else {
      console.log("Pembiayaan LAINNYA");
      $("#dataBPJS").hide();
    }
  });
});
