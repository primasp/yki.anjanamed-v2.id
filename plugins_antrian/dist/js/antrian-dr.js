console.log("Antrian Dr JS loaded");

// alert(device_id);
function loadcontent() {
  location.reload();
}

listdokter();

function listdokter() {
  console.log("load dokter");
  $.ajax({
    url: url + "AntrianDrController/alldokter",
    type: "post",
    dataType: "json",
    data: {
      device_id: device_id,
    },
    cache: false,
    success: function (res) {
      console.log("show dokter");
      console.log(res);
      if (res.responCode === "00") {
        var html = "";
        var panggil = [];
        res.responResult.map(function (d, i) {
          var pasienpanggil = "...";
          $.ajax({
            url: url + "AntrianDrController/pasientunggu",
            type: "post",
            dataType: "json",
            async: false,
            data: {
              dokter: d.dokter_id,
              device_id: device_id,
            },
            success: function (res) {
              if (res.responCode == "00") {
                var pasien = res.responResult.namapasien;
                var pasien2 = pasien;
                var pasienid = res.responResult.pasien;
                var status = res.responResult.status;
                var nourut = res.responResult.no_urut;
                var poli = res.responResult.poli_id;
                var reschedule =
                  res.responResult.reschedule == "Y" ? "(R)" : "";
                pasienpanggil =
                  (nourut !== "null" ? nourut : "") +
                  " " +
                  reschedule +
                  " " +
                  " " +
                  pasien2;
                //pasienpanggil = pasienpanggil.length > 25 ? pasienpanggil.substring(0, 25) : pasienpanggil
                pasienpanggil = pasienpanggil;
              }
            },
          });

          var img =
            d.JENKEL === "L"
              ? url + "plugins_antrian/dist/img/dokter/doctor.png"
              : url + "plugins_antrian/dist/img/dokter/doctor2.png";
          if (
            checkFileExists(
              url + "plugins_antrian/dist/img/dokter/" + d.dokter_id + ".png"
            ) === "200"
          ) {
            img =
              url + "plugins_antrian/dist/img/dokter/" + d.dokter_id + ".png";
          }
          html +=
            '<div class="col-sm-12 align-items-center" style="margin-bottom: 30px;">' +
            '   <div class="row">';
          html +=
            '<div class="col-sm-12">' +
            '   <div id="item-dokter" data-id="' +
            d.dokter_id +
            '" class="d-flex bg-white" style="border-style: solid; border-width: 7px; border-color: #234974; border-radius:500px;position:relative;">' +
            // '       <div class="image-container mr-3" style="border-radius:50%;width: 285px; height: 285px; border-style: solid; border-width: 5px; border-color: #fff; background-color: #fff; overflow: hidden; position: absolute;box-shadow:5px 10px 50px rgba(0,0,0,0.2);top:50%;transform:translateY(-50%)">' +
            '           <img style="border-radius:50%;height:250px;width:250px;object-fit:cover;" src="' +
            img +
            '" class="hover-image" style="width: 100%; height: 100%; object-fit: cover;object-position:center;">' +
            //'       </div>' +
            '       <div class="ml-4 pr-5" style="overflow:hidden;width:90%;">' +
            '           <h3 class="font-weight-bold mb-0" style="color:#234974;font-size:48px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:calc(100%);">' +
            d.nama +
            "</h3>" +
            '           <h5 class="align-items-start" style="color:#234974;font-size:32px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:calc(100%);">' +
            d.namapoli +
            "</h5>" +
            '           <div class="" id="periksa-separator" style="margin:10px 0;"><div style="background: linear-gradient(to right, #4aac90, #234974);width:100%;height:5px;border-radius:50px;"></div></div>' +
            '           <div id="pasienperiksa" class="animate__animated animate__fast" style="font-size:120px;line-height:1;color:#4aac90;margin:0;font-weight:bold;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:calc(100%);">' +
            pasienpanggil +
            "</div>" +
            "       </div>" +
            "   </div>" +
            "</div>";
          html += "   </div>" + "</div>";
        });

        $("#listdokter").html(html);
        var fh = $("#item-dokter").outerHeight() / 2.5;
        $("#pasienperiksa").css({
          "font-size": fh,
        });
        setTimeout(() => {
          // alert("23");
          panggilpasien();
          //sedangperiksa();
        }, 5000);
      } else {
        setTimeout(() => {
          console.log("load ga ada dokter");
          $("#listdokter").html("");
          loadcontent();
          //listdokter();
        }, 2000);
      }
    },
    error: function (res) {
      console.log("error");
      setTimeout(() => {
        loadcontent();
      }, 2000);
    },
  });
}
function tampilip() {
  $("#modalip").modal("show");
}
var interval;
function panggilpasien() {
  console.log("load panggil pasien");
  clearInterval(interval);
  $("[id='item-dokter'] #pasienperiksa").removeClass("animate__heartBeat");
  var dokter = [];
  $("[id='item-dokter']").each(function (i) {
    var dokterid = $(this).attr("data-id");
    dokter.push(dokterid);
  });
  console.log("xc" + dokter);
  $.ajax({
    url: url + "AntrianDrController/panggilpasien",
    type: "post",
    dataType: "json",
    cache: false,
    data: {
      dokter: dokter,
    },
    success: function (res) {
      if (res.responCode === "00") {
        var pasien = res.responResult.nama;
        var pasienid = res.responResult.pasien_id;
        var nourut = res.responResult.NO_URUT;
        console.log("Pasien dipanggil: " + pasien);

        loadcontent();
      } else {
        console.warn("Tidak ada data pasien yang dipanggil.");
        // Anda bisa menampilkan alert atau log tambahan di sini jika perlu

        console.log("ga ada yg dipanggil");
        setTimeout(() => {
          //listdokter();

          loadcontent();
        }, 2000);
      }
    },
    error: function (res) {
      console.log("error");
      setTimeout(() => {
        loadcontent();
      }, 2000);
    },
    //  url: url + "AntrianDrController/alldokter",
  });
  // alert(interval);
}

function checkFileExists(url) {
  var result = "";
  $.ajax({
    url: url,
    async: false,
    success: function (res) {
      result = "200";
    },
    error: function () {
      result = "201";
    },
  });
  return result;
}
