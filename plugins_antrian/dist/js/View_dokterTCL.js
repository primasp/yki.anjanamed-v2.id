/* 
$(document).on('show.bs.modal', '.modal', function () {
    const zIndex = 1040 + 10 * $('.modal:visible').length;
    $(this).css('z-index', zIndex);
    setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
}); */
/* var utterance = '';
function mulai() {
    utterance = new SpeechSynthesisUtterance();
    listdokter();
}
function speak(text) {
    utterance.text = text;
    utterance.volume = 1;        // 0 hingga 1
    utterance.rate = 1;        // 0.1 hingga 10
    utterance.pitch = 1;        // 0 hingga 2
    utterance.lang = 'id-ID';
    utterance.onstart = function () {
        console.log('Speech synthesis started');
    };

    utterance.onend = function () {
        listdokter();
    };

    utterance.onerror = function (event) {
        console.error('Speech synthesis error:', event.error);
    };
    window.speechSynthesis.speak(utterance);
} */
/* const fullscreenBtn = document.getElementById('fullscreenBtn');

fullscreenBtn.addEventListener('click', () => {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) { // Firefox
        document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari and Opera
        document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
        document.documentElement.msRequestFullscreen();
    }
}); */
var wh = $(window).height() - $("#header").outerHeight() - $("#title").outerHeight();
$("#listdokter").css({
    'height': wh,
    'overflow': 'hidden'
})
function loadcontent() {
    location.reload();
}
listdokter();
function listdokter() {

    console.log('load dokter');
    $.ajax({
        url: url + 'index.php/View_dokter/alldokter',
        type: 'post',
        dataType: 'json',
        cache: false,
        success: function (res) {
            console.log('show dokter');
            if (res.responCode === '00') {
                var html = '';
                var panggil = [];
                res.responResult.map(function (d, i) {
                    var pasienpanggil = '...';
                    $.ajax({
                        url: url + 'index.php/View_dokter/pasientunggu',
                        type: 'post',
                        dataType: 'json',
                        async: false,
                        data: {
                            dokter: d.DOKTER_ID
                        },
                        success: function (res) {
                            if (res.responCode == '00') {
                                var pasien = res.responResult.NAMAPASIEN;
                                var pasien2 = pasien;
                                var pasienid = res.responResult.PASIEN_ID;
                                var status = res.responResult.STATUS;
                                var nourut = res.responResult.NO_URUT;
                                var poli = res.responResult.POLI_ID;
                                var reschedule = res.responResult.RESCHEDULE == 'Y' ? '(R)' : '';
                                pasienpanggil = (nourut !== 'null' ? nourut : '') + " " + reschedule + " " + " " + (pasien2);
                                //pasienpanggil = pasienpanggil.length > 25 ? pasienpanggil.substring(0, 25) : pasienpanggil
                                pasienpanggil = pasienpanggil
                            }
                        }
                    });
                    var img = d.JENKEL === 'L' ? url + 'dist/img/dokter/doctor.png' : url + 'dist/img/dokter/doctor2.png';
                    if (checkFileExists(url + 'dist/img/dokter/' + d.DOKTER_ID + '.png') === '200') {
                        img = url + 'dist/img/dokter/' + d.DOKTER_ID + '.png';
                    }
                    html +=
                        '<div class="col-sm-12 align-items-center" style="margin-bottom: 30px;">' +
                        '   <div class="row">';
                    html +=
                        '<div class="col-sm-12">' +
                        '   <div id="item-dokter" data-id="' + d.DOKTER_ID + '" class="d-flex bg-white" style="border-style: solid; border-width: 7px; border-color: #234974; border-radius:500px;position:relative;">' +
                        // '       <div class="image-container mr-3" style="border-radius:50%;width: 285px; height: 285px; border-style: solid; border-width: 5px; border-color: #fff; background-color: #fff; overflow: hidden; position: absolute;box-shadow:5px 10px 50px rgba(0,0,0,0.2);top:50%;transform:translateY(-50%)">' +
                        '           <img style="border-radius:50%;height:250px;width:250px;object-fit:cover;" src="' + img + '" class="hover-image" style="width: 100%; height: 100%; object-fit: cover;object-position:center;">' +
                        //'       </div>' +
                        '       <div class="ml-4 pr-5" style="overflow:hidden;width:90%;">' +
                        '           <h3 class="font-weight-bold mb-0" style="color:#234974;font-size:48px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:calc(100%);">' + d.NAMA + '</h3>' +
                        '           <h5 class="align-items-start" style="color:#234974;font-size:32px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:calc(100%);">' + d.NAMAPOLI + '</h5>' +
                        '           <div class="" id="periksa-separator" style="margin:10px 0;"><div style="background: linear-gradient(to right, #4aac90, #234974);width:100%;height:5px;border-radius:50px;"></div></div>' +
                        '           <div id="pasienperiksa" class="animate__animated animate__fast" style="font-size:120px;line-height:1;color:#4aac90;margin:0;font-weight:bold;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:calc(100%);">' + pasienpanggil + '</div>' +
                        '       </div>' +
                        '   </div>' +
                        '</div>';
                    html +=
                        '   </div>' +
                        '</div>';
                });
                $("#listdokter").html(html);
                var fh = $("#item-dokter").outerHeight() / 2.5;
                $("#pasienperiksa").css({
                    'font-size': fh
                });
                // if (panggil.length > 0) {
                setTimeout(() => {
                    panggilpasien();
                    //sedangperiksa();
                }, 1000);
            } else {
                setTimeout(() => {
                    console.log('load ga ada dokter');
                    $("#listdokter").html('');
                    loadcontent();
                    //listdokter();
                }, 2000);
            }
        },
        error: function (res) {
            console.log('error');
            setTimeout(() => {
                loadcontent();
            }, 2000);
        }
    });
}
var urlParams = new URLSearchParams(window.location.search);
var poli = urlParams.get('poli');
var h = $(window).height() - 150;
var context;
var soundSource;
var soundBuffer;
var run = 0;
var duration = 0;
var durationcont = 0;
/* $("#content").css({
    'height': h,
    'overflow': 'auto'
}); */
/* if (kode) {
    $("#allpoli").addClass('d-none');
    $("#alldokter").removeClass('d-none');
    alldokter();
} */
// function reload() {
//     loadcontent();
// }
/* if (!poli) {
    tampilpoli();
}
function tampilpoli() {
    allpoli();
} */
//if (poli) {
/* $("#allpoli").addClass('d-none');
$("#alldokter").addClass('d-none');
$("#antriandokter").removeClass('d-none');
$("#backdokter").attr('href', url + 'index.php/view_dokter?kode=' + kode); */
//panggilperawat();

//}
function init() {
    if (typeof AudioContext !== "undefined") {
        audioContext = new window.AudioContext();
    } else if (typeof webkitAudioContext !== "undefined") {
        audioContext = new window.webkitAudioContext();
    } else {
        throw new Error('AudioContext not supported.');
    }
};
function playSequentialSounds(dokterid, ip) {
    index = 0;
    //soundstop = 0;
    playsound(dokterid, ip);
};
function playsound(dokterid, ip) {
    if (soundPaths.length > index) {

        xhr = new XMLHttpRequest();
        audioFileUrl = soundPaths[index];
        /*  lastcall = soundPaths[soundPaths.length - 1];
         call = audioFileUrl; */
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
                    /*  let gainNode = audioContext.createGain();
                     gainNode.gain.value = 0.8;
                     source.connect(gainNode); */
                    const biquadFilter = audioContext.createBiquadFilter();

                    // Mengatur jenis filter (contohnya low-pass filter)
                    biquadFilter.type = 'lowpass'; // Pilihan: 'lowpass', 'highpass', 'bandpass', dll.
                    biquadFilter.frequency.setValueAtTime(1000, audioContext.currentTime); // Frekuensi cutoff di 1000 Hz
                    biquadFilter.Q.setValueAtTime(1, audioContext.currentTime); // Nilai Q

                    biquadFilter.gain.setValueAtTime(5, audioContext.currentTime);
                    // Menghubungkan node
                    source.connect(biquadFilter);  // Sambungkan sumber audio ke filter
                    biquadFilter.connect(audioContext.destination);  // Sambungkan filter ke ou
                    source.playbackRate.value = 1;
                    var duration = 0;
                    // gainNode.connect(audioContext.destination);
                    duration += buffer.duration;
                    console.log(buffer.duration);

                    setTimeout(() => {
                        console.log("index : " + index);
                        console.log("last : " + lastindex);
                        if (lastindex === currindex) {
                            updatepanggil(dokterid, ip);
                            //setTimeout(() => {
                            console.log('selesai');
                            setTimeout(() => {

                                /* audioContext.close();
                                source.disconnect(gainNode);
                                source.stop();
                                gainNode.disconnect(audioContext.destination); */
                                loadcontent();
                            }, 2000);
                            //listdokter();

                            //}, 3000);

                            //audioContext.close()
                        } else {
                            playsound(dokterid, ip);
                        }
                    }, (duration * 1000)); // Convert to milliseconds
                    /* source.onended = function () {
 
                        console.log("index : " + index);
                        console.log("last : " + lastindex);
                        if (lastindex === currindex) {
 
                            console.log('selesai');
                            listdokter();
                        } else {
                            playsound();
                        }
 
                    } */
                    source.onerror = function () {
                        loadcontent();
                    }
                    source.start();
                }, function () {
                    loadcontent();
                });
                index++;
            } else {
                loadcontent();
            }

        }
        xhr.onerror = function () {
            loadcontent();
        }
        xhr.send();
    } else {
        //audioContext.close()
    }
}
function checkFileExists(url) {
    var result = '';
    $.ajax({
        url: url,
        async: false,
        success: function (res) {
            result = '200';
        }, error: function () {
            result = '201';
        },
    })
    return result;
}
/* function allpoli() {
    $.ajax({
        url: url + 'index.php/View_dokter/allpoli',
        type: 'post',
        dataType: 'json',
        success: function (res) {
            var html = '';
            res.responResult.map(function (d) {
                html +=
                    "<div class='col-sm-4'>" +
                    "   <button type='button' class='btn btn-primary btn-block mb-2 p-3' style='font-size:32px;' onclick='pilihpoli(this)' poli-id='" + d.POLI_ID + "' >" + d.KETERANGAN + "</button>" +
                    "</div>";
            })
            $("#listdokter").html(html);
        }
    });
}
 
function pilihpoli(e) {
    poli = $(e).attr('poli-id');
    window.location = url + 'index.php/View_dokter?poli=' + poli;
} */
var episodeid = '';
var episodeidpanggil = '';
function panggilperawat() {
    $.ajax({
        url: url + 'index.php/View_dokter/panggilperawat',
        type: 'post',
        data: {
            poli: poli
        },
        dataType: 'json',
        cache: false,
        success: function (res) {
            console.log(res);
            if (res.responCode == '00') {
                episodeid = res.responResult[0].EPISODE_ID;
                if (episodeid != episodeidpanggil) {
                    $("#modalpanggilperawat").modal('show');
                    setTimeout(() => {
                        $("#modalpanggilperawat").modal('hide');

                    }, 3000);
                }
                episodeidpanggil = episodeid;
                setTimeout(() => {
                    panggilperawat();
                }, 5000);
            } else {
                setTimeout(() => {
                    panggilperawat();
                }, 3000);
            }
        }
    });
}


function updatepanggil(iddokter, ip) {
    $.ajax({
        url: url + 'index.php/View_dokter/updatepanggil',
        type: 'post',
        dataType: 'json',
        cache: false,
        data: {
            dokterid: iddokter,
            ip: ip
        },
        success: function (res) {
            console.log('berhasil update');
        }, error: function (res) {
            console.log('update error');
            setTimeout(() => {
                loadcontent();
            }, 2000);
        }
    });
}

function pembilang(nilai) {
    nilai = Math.floor(Math.abs(nilai));

    var simpanNilaiBagi = 0;
    var huruf = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas',
    ];
    var temp = '';

    if (nilai < 12) {
        temp = ' ' + huruf[nilai];
    } else if (nilai < 20) {
        temp = pembilang(Math.floor(nilai - 10)) + ' Belas';
    } else if (nilai < 100) {
        simpanNilaiBagi = Math.floor(nilai / 10);
        temp = pembilang(simpanNilaiBagi) + ' Puluh' + pembilang(nilai % 10);
    } else if (nilai < 200) {
        temp = ' Seratus' + pembilang(nilai - 100);
    } else if (nilai < 1000) {
        simpanNilaiBagi = Math.floor(nilai / 100);
        temp = pembilang(simpanNilaiBagi) + ' Ratus' + pembilang(nilai % 100);
    }/*  else if (nilai < 2000) {
            temp = ' Seribu' + pembilang(nilai - 1000);
        } else if (nilai < 1000000) {
            simpanNilaiBagi = Math.floor(nilai / 1000);
            temp = pembilang(simpanNilaiBagi) + ' Ribu' + pembilang(nilai % 1000);
        } else if (nilai < 1000000000) {
            simpanNilaiBagi = Math.floor(nilai / 1000000);
            temp = pembilang(simpanNilaiBagi) + ' Juta' + pembilang(nilai % 1000000);
        } else if (nilai < 1000000000000) {
            simpanNilaiBagi = Math.floor(nilai / 1000000000);
            temp = pembilang(simpanNilaiBagi) + ' Miliar' + pembilang(nilai % 1000000000);
        } else if (nilai < 1000000000000000) {
            simpanNilaiBagi = Math.floor(nilai / 1000000000000);
            temp = pembilang(nilai / 1000000000000) + ' Triliun' + pembilang(nilai % 1000000000000);
        } */

    return temp;
}


var interval;
function panggilpasien() {
    console.log('load panggil pasien');
    clearInterval(interval);
    $("[id='item-dokter'] #pasienperiksa").removeClass('animate__heartBeat');
    var dokter = [];
    $("[id='item-dokter']").each(function (i) {
        var dokterid = $(this).attr('data-id');
        dokter.push(dokterid);
    });
    console.log(dokter);
    $.ajax({
        url: url + 'index.php/View_dokter/panggilpasien',
        type: 'post',
        dataType: 'json',
        cache: false,
        data: {
            dokter: dokter
        },
        success: function (res) {
            console.log(res);
            if (res.responCode === '00') {
                var pasien = res.responResult.NAMA;
                var pasienid = res.responResult.PASIEN_ID;
                var nourut = res.responResult.NO_URUT;
                var poli = res.responResult.POLI_ID;
                var dokterid = res.responResult.DOKTER_ID;
                var ip = res.responResult.IP;
                var namadokter = res.responResult.NAMADOKTER;
                var namapoli = res.responResult.NAMA_POLI;
                var list = [];
                /*  namadokter = namadokter.split(' ');
                 namadokter = namadokter[1]; */
                var pasien2 = pasien;
                var reschedule = res.responResult.RESCHEDULE == 'Y' ? '(R)' : '';
                var pasienpanggil2 = (nourut !== 'null' ? nourut : '') + " " + reschedule + " " + pasien2;
                //pasienpanggil2 = pasienpanggil2.length > 25 ? pasienpanggil2.substring(0, 25) : pasienpanggil2;
                pasienpanggil2 = pasienpanggil2;
                $("[id='item-dokter'][data-id='" + dokterid + "']").find('#pasienperiksa').html(pasienpanggil2);
                interval = setInterval(() => {
                    $("[id='item-dokter'][data-id='" + dokterid + "']").find('#pasienperiksa').addClass('animate__heartBeat');
                    setTimeout(() => {
                        $("[id='item-dokter'][data-id='" + dokterid + "']").find('#pasienperiksa').removeClass('animate__heartBeat');
                    }, 1000);
                }, 2000);
                if (nourut !== 'null') {
                    nourut.split('-').map(function (f, i) {
                        if ($.isNumeric(f)) {
                            p = pembilang(f).trim();
                            p.split(' ').map(function (g) {
                                list.push(g);
                            })
                        } else {
                            list.push(f);
                        }
                    });
                }
                var audioArray = [];
                audioArray.push(new Audio('../dist/soundnew/Antrian.mp3'));
                if (nourut !== 'null') {
                    list.map(function (s) {
                        audioArray.push(new Audio('../dist/soundnew/' + s + '.mp3'));
                    });
                }
                if (poli === 'PSIKO0000000001') {
                    audioArray.push(new Audio('../dist/soundnew/dokter/psikolog.mp3'));
                } else {
                    audioArray.push(new Audio('../dist/soundnew/dokter/KeDokter.mp3'));
                }
                audioArray.push(new Audio('../dist/soundnew/dokter/' + dokterid + '.mp3'));
                audioArray.push(new Audio('../dist/soundnew/poli/' + poli + '.mp3'));
                /*  var audioArray = ending.map(function (sound) {
                     return sound;
                 }); */
                /* var audioArray = [];
                sound.map(function (d, i) {
                    audioArray.push(new Audio(d));
                }) */
                let index = 0;
                function play() {
                    console.log(index);
                    audioArray[index].play()
                    audioArray[index].addEventListener("ended", function (e) {
                        index = index + 1;
                        if (audioArray.length > index) {
                            play();
                        } else {
                            updatepanggil(dokterid, ip);
                            loadcontent();
                        }
                    });
                }
                play();
                // let currentAudioIndex = 0;

                // // Function to play the next audio in the array
                // function playNext() {
                //     // Pause the current audio if it's playing
                //     if (audioArray[currentAudioIndex]) {
                //         audioArray[currentAudioIndex].pause();
                //         audioArray[currentAudioIndex].currentTime = 0; // Reset to start
                //     }

                //     // Increment the index and loop back if at the end
                //     currentAudioIndex = (currentAudioIndex + 1) % audioArray.length;

                //     // Play the next audio
                //     audioArray[currentAudioIndex].play();
                // }
                // playNext();
                // console.log(soundPaths);
                //playSequentialSounds(dokterid, ip);
                // var audio = new Audio('../dist/soundnew/Antrian.mp3');
                // audio.play();
                // audio.addEventListener("ended", function (e) {

                //     var audio2 = new Audio('../dist/soundnew/dokter/KeDokter.mp3');
                //     audio2.play();
                //     audio2.addEventListener("ended", function (e) {
                //         var audio3 = new Audio('../dist/soundnew/dokter/' + dokterid + '.mp3');
                //         audio3.play();
                //         audio3.addEventListener("ended", function (e) {
                //             var audio4 = new Audio('../dist/soundnew/poli/' + poli + '.mp3');
                //             audio4.play();
                //             audio4.addEventListener("ended", function (e) {
                //                 updatepanggil(dokterid, ip);
                //                 location.reload();
                //             })
                //         })
                //     })
                // })

            } else {
                console.log('ga ada yg dipanggil');
                setTimeout(() => {
                    //listdokter();

                    loadcontent();
                }, 2000);
            }
        }, error: function (res) {
            console.log('error');
            setTimeout(() => {
                loadcontent();
            }, 2000);
        }
    });
}

function tampilip() {
    $("#modalip").modal('show');
}