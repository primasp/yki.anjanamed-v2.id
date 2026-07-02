let lastLogTimePaten = Date.now();
let lastLogTimeRacikan = Date.now();
let lastLogTimeParam = Date.now();

setInterval(pagescroll, 15);

setInterval(cekparam, 2000);

function pagescroll() {

    let listpaten = document.getElementById('listobatpaten');
    if (listpaten.scrollTop < (listpaten.scrollHeight - listpaten.offsetHeight)) {
        listpaten.scrollTop += 1;
    } else {
        listpaten.scrollTop = 0;
        
        // Cek apakah sudah lebih dari 5 detik sejak log terakhir
        if (Date.now() - lastLogTimePaten >= 5000) {
            obatpaten();
            lastLogTimePaten = Date.now(); // Reset waktu log terakhir
        }
    }

    let listracikan = document.getElementById('listobatracikan');
    if (listracikan.scrollTop < (listracikan.scrollHeight - listracikan.offsetHeight)) {
        listracikan.scrollTop += 1;
    } else {
        listracikan.scrollTop = 0;
        
        // Cek apakah sudah lebih dari 5 detik sejak log terakhir
        if (Date.now() - lastLogTimeRacikan >= 5000) {
            obatracikan();
            lastLogTimeRacikan = Date.now(); // Reset waktu log terakhir
        }
    }
};


function obatpaten() {
    $.ajax({
        url: url + "index.php/View_17/obatpaten",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            var listpasien = "<div class='row'>";
            var result = data.responResult;
            var nomorakhir = 0;

            if (data.responCode === "00") {
                for (var i in result) {
                    listpasien += "<div class='col-md-12 mb-3'>";
                    listpasien += "<div class='card' style='background-color: #b8e0d2'>";
                    listpasien += "<div class='card-body'>";


                    listpasien += "<div class='d-flex justify-content-between align-items-center mb-2'>";
                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                    if (result[i].URUT == null) {
                        listpasien += result[i].MRPASIEN + " | ";
                    } else {
                        listpasien += result[i].MRPASIEN + " | " + result[i].URUT;
                    }
                    listpasien += "</div>";

                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                    listpasien += "Estimasi Selesai: <strong>" + result[i].ESTIMASI + "</strong>";
                    listpasien += "</div>";
                    listpasien += "</div>";


                    listpasien += "<h1 class='text-truncate font-weight-bold' style='font-size: 80px;'>" + result[i].NAMAPASIEN + "</h1>";
                    listpasien += "<hr class='mt-2 mb-2' style='border-top: 2px solid'>";


                    listpasien += "<div class='d-flex justify-content-between align-items-center mb-2'>";
                    listpasien += "<div class='text-truncate font-weight-bold' style='font-size: 40px;'>";
                    listpasien += result[i].POLITUJUAN;
                    listpasien += "</div>";

                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                    listpasien += result[i].NOMOR;
                    listpasien += "</div>";
                    listpasien += "</div>";


                    listpasien += "</div>"; // Close card-body
                    listpasien += "</div>"; // Close card
                    listpasien += "</div>"; // Close column

                    nomorakhir = result[i].NOMOR;
                }
            }

            listpasien += "</div>"; // Close row
            $("#listobatpaten").html(listpasien);
            $("#teksobatjadi").html('OBAT PATEN (' + nomorakhir + ' ANTRIAN)');

        }, error: function () {
            console.log('Belum Ada Data');
        }
    });
    return false;
}


function obatracikan() {
    $.ajax({
        url: url + "index.php/View_17/obatracikan",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            var listpasien = "<div class='row'>";
            var result = data.responResult;
            var nomorakhir = 0;

            if (data.responCode === "00") {
                for (var i in result) {
                    listpasien += "<div class='col-md-12 mb-3'>";
                    listpasien += "<div class='card' style='background-color: #a0ced9'>";
                    listpasien += "<div class='card-body'>";


                    listpasien += "<div class='d-flex justify-content-between align-items-center mb-2'>";
                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                    if (result[i].URUT == null) {
                        listpasien += result[i].MRPASIEN + " | ";
                    } else {
                        listpasien += result[i].MRPASIEN + " | " + result[i].URUT;
                    }
                    listpasien += "</div>";

                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                    listpasien += "Estimasi Selesai: <strong>" + result[i].ESTIMASI + "</strong>";
                    listpasien += "</div>";
                    listpasien += "</div>";


                    listpasien += "<h1 class='text-truncate font-weight-bold' style='font-size: 80px;'>" + result[i].NAMAPASIEN + "</h1>";
                    listpasien += "<hr class='mt-2 mb-2' style='border-top: 2px solid'>";


                    listpasien += "<div class='d-flex justify-content-between align-items-center mb-2'>";
                    listpasien += "<div class='text-truncate font-weight-bold' style='font-size: 40px;'>";
                    listpasien += result[i].POLITUJUAN;
                    listpasien += "</div>";

                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                    listpasien += result[i].NOMOR;
                    listpasien += "</div>";
                    listpasien += "</div>";


                    listpasien += "</div>"; // Close card-body
                    listpasien += "</div>"; // Close card
                    listpasien += "</div>"; // Close column

                    nomorakhir = result[i].NOMOR;
                }
            }

            listpasien += "</div>";
            $("#listobatracikan").html(listpasien);
            $("#teksobatracikan").html('OBAT RACIKAN (' + nomorakhir + ' ANTRIAN)');

        }, error: function () {
            console.log('Belum Ada Data');
        }
    });
    return false;
};


function cekparam(){
    console.log('cekparam');
    // Cek apakah sudah lebih dari 5 detik sejak log terakhir
    if (Date.now() - lastLogTimeParam >= 5000) {
        getparameter();
        lastLogTimeParam = Date.now(); // Reset waktu log terakhir
    }
}


function panggilbunyi() {
    var loksound = [];

    loksound.push('../dist/soundnew/farmasi/Farmasi_announce.mp3');
    // loksound.push('../dist/soundnew/poli/POLI0000000049.mp3');

    soundPaths = loksound.map(function (sound) {
        return sound;
    });

    playSequentialSounds();
};

function playSequentialSounds() {
    index = 0;

    updatepanggil();

    playSound();
};

function playSound() {
    console.log('playsoun');
    if (soundPaths.length > index) {

        xhr = new XMLHttpRequest();
        audioFileUrl = soundPaths[index];
        lastindex = soundPaths.length - 1;
        currindex = index;

        xhr.open('GET', audioFileUrl, true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function () {
            if (xhr.status === 200) {
                audioData = xhr.response;
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                source = audioContext.createBufferSource();
                audioContext.decodeAudioData(audioData, function (buffer) {
                    source.buffer = buffer;
                    let gainNode = audioContext.createGain();
                    gainNode.gain.value = 1;
                    source.connect(gainNode);
                    //source.playbackRate.value = 1.2;
                    var duration = 0;
                    gainNode.connect(audioContext.destination);
                    duration += buffer.duration;
                    console.log(buffer.duration);

                    setTimeout(() => {
                        console.log("index : " + index);
                        console.log("last : " + lastindex);
                        if (lastindex === currindex) {

                            console.log('selesai');
                        
                            updatesoundselesai();
                            setTimeout(() => {
                                updateselesai();
                            }, 10*1000); //jarak antar bunyi tiap 15 menit

                        }// else {
                        //    playSound();
                        //}
                    }, (duration * 1000) - 300); // Convert to milliseconds

                    source.onerror = function () {
                        // loadcontent();
                    }
                    source.start();
                }, function () {
                    // loadcontent();
                });
                index++;
            } else {
                // loadcontent();
            }

        }
        xhr.onerror = function () {
            // loadcontent();
        }
        xhr.send();
    } else {
        //audioContext.close()
    }
};


function getparameter() {
    console.log('getparam');
    $.ajax({
        url: url + "index.php/View_17/getparameter",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {            
            if (data.responCode === "00") {
                console.log('Panggil Otomatis : ' + data.responResult.PAKEINTERVAL + '; Nyala : ' + data.responResult.BUNYIIN);
                if (data.responResult && data.responResult.PAKEINTERVAL) {

                    if (data.responResult.PAKEINTERVAL === "Y" && (data.responResult.BUNYIIN != "X" && data.responResult.BUNYIIN != "XX")) {

                        console.log('mmbunyiii otomatis');
                        // panggilbunyi();

                    } else if (data.responResult.PAKEINTERVAL === "N" && data.responResult.BUNYIIN === "Y") {

                        console.log('mmbunyiii manual');
                        panggilbunyi();

                    }
                }
            } else {
                console.log(data.responDesc);
            }
        },
        error: function (xhr, status, error) {
            console.log("Terjadi kesalahan:", error);
        }
    });
    //return false;
}

function updatepanggil() {
    $.ajax({
        url: url + "index.php/View_17/updatepanggil",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            if (data.responCode === "00") {
                console.log('updatepanggil');
                //
            }
        },
        error: function (xhr, status, error) {
            console.error("Terjadi kesalahan:", error);
        }
    });
    return false;
}

function updatesoundselesai() {
    $.ajax({
        url: url + "index.php/View_17/updatesoundselesai",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            if (data.responCode === "00") {
                console.log('sound selesai');
                //
            }
        },
        error: function (xhr, status, error) {
            console.error("Terjadi kesalahan:", error);
        }
    });
    return false;
}

function updateselesai() {
    $.ajax({
        url: url + "index.php/View_17/updateselesai",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            if (data.responCode === "00") {
                console.log('update benar benar selesai');
                //
            }
        },
        error: function (xhr, status, error) {
            console.error("Terjadi kesalahan:", error);
        }
    });
    return false;
}
