var context;
var soundSource;
var soundBuffer;

var lastcall = "";

init();

function conversibilangan(angka, kode, loket) {
    const satuan = ['', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    const belasan = ['sepuluh', 'sebelas', '2|belas', '3|belas', '4|belas', '5|belas', '6|belas', '7|belas', '8|belas', '9|belas'];
    const puluhan = ['', 'sepuluh', '2|puluh', '3|puluh', '4|puluh', '5|puluh', '6|puluh', '7|puluh', '8|puluh', '9|puluh'];

    function konversiSatuan(angka) {
        return satuan[angka];
    }

    function konversiBelasan(angka) {
        return belasan[angka - 10];
    }

    function konversiPuluhan(angka) {
        const puluhanValue = Math.floor(angka / 10);
        const satuanValue = angka % 10;
        if (satuanValue > 0) {
            return puluhan[puluhanValue] + '|' + konversiSatuan(satuanValue);
        } else {
            return puluhan[puluhanValue];

        }
    }

    function konversiRatusan(angka) {
        const ratusanValue = Math.floor(angka / 100);
        const sisaRatusan = angka % 100;

        if (ratusanValue === 1) {
            return 'seratus|' + konversiPuluhan(sisaRatusan);
        } else if (ratusanValue > 1) {
            return konversiSatuan(ratusanValue) + '|ratus|' + konversiPuluhan(sisaRatusan);
        } else {
            return konversiPuluhan(sisaRatusan);
        }
    }

    if (angka < 10) {
        return 'opening|nomor_antrian|' + kode + '|' + konversiSatuan(angka) + '|kasir|' + loket + '|ending';
    } else if (angka < 20) {
        return 'opening|nomor_antrian|' + kode + '|' + konversiBelasan(angka) + '|kasir|' + loket + '|ending';
    } else if (angka < 100) {
        return 'opening|nomor_antrian|' + kode + '|' + konversiPuluhan(angka) + '|kasir|' + loket + '|ending';
    } else if (angka < 1000) {
        return 'opening|nomor_antrian|' + kode + '|' + konversiRatusan(angka) + '|kasir|' + loket + '|ending';
    } else if (angka < 10000) {
        const ribuanValue = Math.floor(angka / 1000);
        const sisaRibuan = angka % 1000;
        return 'opening|nomor_antrian|' + strtolower(kode) + '|' + konversiSatuan(ribuanValue) + '|ribu|' + konversiRatusan(sisaRibuan) + '|kasir|' + loket + '|ending';
    } else {
        return 'Angka diluar batas konversi (1-9999)';
    }


};

function antrianselanjutnya() {
    $.ajax({
        url: url + "index.php/View_5/antrianselanjutnya",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            var liatantrian = "";

            if (data.responCode == "00") {
                var result = data.responResult;

                for (var i in result) {

                    if (i === "0") {
                        liatantrian += "<div class='row col-md-12 d-flex align-items-center mb-1 mr-1 ml-1' style='border-style: solid; border-width:5px; border-color: #234974; border-radius: 10px;'>";
                    } else {
                        liatantrian += "<div class='row col-md-12 d-flex align-items-center m-1' style='border-style: solid; border-width:5px; border-color: #234974; border-radius: 10px;'>";
                    }

                    liatantrian += "<div class='row col-md-9 d-flex justify-content-start align-items-center'>";
                    liatantrian += "<div class='col-md-12 d-flex justify-content-start align-items-center'>";

                    if (result[i].PASIEN_ID != null) {
                        liatantrian += "<h1 class='text-truncate' style='color:#234974;'><strong>" + result[i].NAMAPASIEN + "</strong></h1>";
                    } else {
                        liatantrian += "<h1 class='text-truncate' style='color:#234974;'><strong>" + result[i].KETERANGANANTRIAN + "</strong></h1>";
                    }

                    liatantrian += "</div>";
                    liatantrian += "<div class='col-md-12 d-flex justify-content-start align-items-center'>";

                    if (result[i].PASIEN_ID != null) {
                        liatantrian += "<h1 class='text-truncate' style='color:#359d9e;'><strong>" + result[i].KETERANGANANTRIAN + "</strong></h1>";
                    }

                    liatantrian += "</div>";
                    liatantrian += "</div>";
                    liatantrian += "<div class='col-md-3 d-flex justify-content-center align-items-center'>";
                    liatantrian += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].KODE + "-" + result[i].URUT + "</strong></h1>";
                    liatantrian += "</div>";
                    liatantrian += "</div>";

                }
            }

            $("#liatantrian").html(liatantrian);
        },
        complete: function () {
            lastupdate();
        },
        error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = jqXHR.statusText + " [ " + jqXHR.status + " ] ";
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }

            console.log(jqXHR + " [ " + msg + " ]");
            //setInterval(antrianselanjutnya, 1000);
        }
    });
    return false;
};

function antriansaatini() {
    $.ajax({
        url: url + "index.php/View_5/antriansaatini",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            //console.log(data);
            if (data.responCode == "00") {
                var result = data.responResult;
                var call = result[0].KODE + "-" + result[0].URUT + "-" + result[0].LOKET;
                const hasilKonversi = conversibilangan(result[0].URUT, result[0].KODE, result[0].LOKET);
                //const hasilKonversi = conversibilangan('30', 'E', '2');


                soundPaths = hasilKonversi.split('|').map(function (sound) {
                    return '../dist/sound/' + sound.toLowerCase() + '.mp3';
                });

                //if (lastcall !== call) {
                playSequentialSounds();
                updateantrian(result[0].TRANS_ID, result[0].KODE, result[0].URUT, result[0].JML_CALL);
                //lastcall = call;
                /*   } else {
                      setTimeout(antriansaatini, 2000);
                  } */

                $("#antriansaatini").html(result[0].KODE + "-" + result[0].URUT);
                $("#namapasiensaatini").html(result[0].NAMAPASIEN);
                $("#loketsaatini").html("KASIR " + result[0].LOKET);

                if (result[0].ANTRIANLOKET1 != null) {
                    $("#saatiniloket1").html(result[0].ANTRIANLOKET1);
                } else {
                    $("#saatiniloket1").html("STAND BY");
                }

                if (result[0].ANTRIANLOKET2 != null) {
                    $("#saatiniloket2").html(result[0].ANTRIANLOKET2);
                } else {
                    $("#saatiniloket2").html("STAND BY");
                }

                if (result[0].ANTRIANLOKET3 != null) {
                    $("#saatiniloket3").html(result[0].ANTRIANLOKET3);
                } else {
                    $("#saatiniloket3").html("STAND BY");
                }

            } else {
                setTimeout(loaddata, 2000);
            }
        },
        complete: function () {
            lastupdate();
        },
        error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = jqXHR.statusText + " [ " + jqXHR.status + " ] ";
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }

            console.log(jqXHR + " [ " + msg + " ]");
            setTimeout(loaddata, 2000);
        }
    });
    return false;
};

function updateantrian($transid, $kode, $urut, $jmlcall) {
    $.ajax({
        url: url + "index.php/View_5/updateantrian",
        data: { 'transid': $transid, 'kode': $kode, 'urut': $urut, 'jmlcall': $jmlcall },
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            console.log("Proccess " + data.responHead);
        }
    });
    return false;
}

function init() {
    if (typeof AudioContext !== "undefined") {
        context = new AudioContext();
    } else if (typeof webkitAudioContext !== "undefined") {
        context = new webkitAudioContext();
    } else {
        throw new Error('AudioContext not supported.');
    }
};

function playSequentialSounds() {
    currentIndex = 0; // Reset indeks ketika tombol Play diklik
    playNextSound();
};

function playNextSound() {
    if (currentIndex < soundPaths.length) {
        var request = new XMLHttpRequest();
        request.open("GET", soundPaths[currentIndex], true);
        request.responseType = "arraybuffer";

        request.onload = function () {
            var audioData = request.response;
            startSound(audioData);
        };

        request.send();
    } else {
        antriansaatini();
    }
};

function startSound(audioData) {
    soundSource = context.createBufferSource();
    context.decodeAudioData(
        audioData,
        function (buffer) {
            soundBuffer = buffer;
            soundSource.buffer = soundBuffer;

            var volumeNode = context.createGain();
            soundSource.connect(volumeNode);
            volumeNode.connect(context.destination);
            volumeNode.gain.value = 0.5; // Adjust as needed
            soundSource.onended = playNextSound; // Play the next sound when the current one ends
            soundSource.onended = function () {
                playNextSound();
            }
            // Start playing the current sound
            soundSource.start(context.currentTime);
        },
        function (e) {
            console.log("Error decoding audio data", e);
        }
    );
    currentIndex++;
};

function lastupdate() {
    var timezone = new Date().toLocaleString("en-US", { timeZone: "Asia/Jakarta" });
    var jakartaTime = new Date(timezone);

    var jam = jakartaTime.getHours();
    var menit = jakartaTime.getMinutes();
    var detik = jakartaTime.getSeconds();
    var tanggal = jakartaTime.getDate();
    var bulan = jakartaTime.getMonth() + 1;  // Perhatikan bahwa bulan dimulai dari 0 (Januari) hingga 11 (Desember)
    var tahun = jakartaTime.getFullYear();

    var zonaWaktu = jakartaTime.toString().match(/\(([^)]+)\)$/)[1];

    var formattedTime = jam + ":" + (menit < 10 ? "0" + menit : menit) + ":" + (detik < 10 ? "0" + detik : detik);
    var formattedDate = tanggal + "/" + (bulan < 10 ? "0" + bulan : bulan) + "/" + tahun;

    $("#lastupdate").html("Last Update: " + formattedDate + " " + formattedTime);
};

/* document.querySelector('#playButton').addEventListener('click', function () {
    $("#playButton").addClass("d-none");
    $("#displayantrian").removeClass("d-none"); */
loaddata();
function loaddata() {
    antrianselanjutnya();
    antriansaatini();
}
//});