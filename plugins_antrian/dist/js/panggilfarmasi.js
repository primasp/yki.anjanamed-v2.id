
var urlParams = new URLSearchParams(window.location.search);
var lantai = urlParams.get('lt');

setInterval(() => {
    lastupdate();
}, 1000);
panggilantrian();

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
//var episodeid = '';
function playSequentialSounds() {

    index = 0;
    //soundstop = 0;
    playsound();
};
function playsound() {

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
                    let gainNode = audioContext.createGain();
                    gainNode.gain.value = 0.5;
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

                            updatepanggil();
                            //setTimeout(() => {
                            console.log('selesai');
                            setTimeout(() => {

                                /* audioContext.close();
                                source.disconnect(gainNode);
                                source.stop();
                                gainNode.disconnect(audioContext.destination); */
                                panggilantrian();
                            }, 2000);
                            //listdokter();

                            //}, 3000);

                            //audioContext.close()
                        } else {
                            playsound();
                        }
                    }, (duration * 1000)); // C
                    source.onerror = function () {
                        panggilantrian();
                    }
                    source.start();
                }, function () {
                    //alert('error');
                });
                index++;
            } else {
                // loadcontent();
            }

        }
        xhr.onerror = function () {
            panggilantrian();
        }
        xhr.send();
    } else {
        //audioContext.close()
    }
}

function updatepanggil(episodeid) {
    //console.log(episodeid);
    //return;
    $.ajax({
        url: url + 'index.php/Panggilfarmasi/updatepanggil',
        type: 'post',
        //dataType: 'json',
        //cache: false,
        data: {
            episodeid: episodeid
        },
        success: function (res) {
            console.log('berhasil update');
        }, error: function (res) {
            console.log('update error');
            setTimeout(() => {
                panggilantrian();
            }, 1000);
        }
    });
}



function tampilantrian(d, i) {
    /* const synth = window.speechSynthesis;
    const voices = synth.getVoices();
    console.log(voices); */
    var length = d.length;
    if (length > i) {
        var nourut = d[i].URUT;
        var poli = d[i].NAMAPOLI;
        var idpoli = d[i].POLI_ID;
        var episodeid = d[i].EPISODE_ID;
        var namapasien = d[i].NAMAPASIEN;

        $("#antrianpanggil").html(nourut).addClass('animate__fadeInUp').removeClass('animate__fadeOutUp');
        $("#namapasien").html(namapasien).addClass('animate__fadeInUp').removeClass('animate__fadeOutUp');
        $("#poliantrian").html(poli).addClass('animate__fadeInUp').removeClass('animate__fadeOutUp');
        if (idpoli === 'POLI0000000035') {
            poli = 'POLI RADIOTERAPI';
        } else if (idpoli === 'POLI0000000041') {
            poli = 'POLI GIGI SPESIALIS ORTODONTI';
        } else {
            poli = poli;
        }
        if (nourut) {
            var utterance = new SpeechSynthesisUtterance(namapasien.toLowerCase() + " / dari " + poli);
        } else {
            var utterance = new SpeechSynthesisUtterance(namapasien.toLowerCase() + " / dari " + poli);
        }

        utterance.lang = "id-ID";
        utterance.rate = 1;
        utterance.volume = 2;
        window.speechSynthesis.cancel()
        window.speechSynthesis.speak(utterance);
        utterance.addEventListener("end", (event) => {
            $("#antrianpanggil").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');
            $("#namapasien").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');
            $("#poliantrian").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');

            setTimeout(() => {
                updatepanggil(episodeid)
                tampilantrian(d, i + 1);
            }, 1000);
        });
        utterance.addEventListener("error", (event) => {
            panggilantrian();
            console.log(
                `An error has occurred with the speech synthesis: ${event.error}`,
            );
        });
        // utterance.addEventListener("error", (event) => {
        //     tampilantrian(d, i + 1);
        //     /*  $("#antrianpanggil").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');
        //      $("#namapasien").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');
        //      $("#poliantrian").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');

        //      setTimeout(() => {
        //          //updatepanggil(episodeid)
        //          tampilantrian(d, i + 1);
        //      }, 1000); */
        // });
        // responsiveVoice.speak("antrian " + nourut + " / " + namapasien.toLowerCase() + " / dari " + poli, "Indonesian Female", {
        //     onend: function () {
        //         $("#antrianpanggil").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');
        //         $("#poliantrian").removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');

        //         setTimeout(() => {

        //             tampilantrian(d, i + 1);
        //         }, 500);
        //     }
        // });
    } else {
        setTimeout(() => {
            panggilantrian();
        }, 2000);
    }
}

function panggilantrian() {
    $.ajax({
        url: url + 'index.php/Panggilfarmasi/panggilantrian',
        type: 'post',
        data: {
            lantai: lantai
        },
        dataType: 'json',
        success: function (res) {
            //console.log(res);
            if (res.code === '200') {
                //responsiveVoice.resume();
                //var d = res.data;
                $("#tdkadaantrian").html('');
                tampilantrian(res.data, 0);

                //return;
                //res.data.map(function (d, i) {
                //setTimeout(() => {

                //}, 1000 * i);

                //responsiveVoice.resume();


                // }
                // responsiveVoice.speak("antrian " + nourut + " dari " + poli, "Indonesian Female", {
                //     onend: function () {
                //         responsiveVoice.cancel();
                //         //updatepanggil();
                //         panggilantrian();
                //     }
                // });

                // let utterance = new SpeechSynthesisUtterance("antrian " + nourut.toLowerCase() + " dari " + poli.toLowerCase());
                // utterance.lang = "id-ID";
                // utterance.rate = 1;
                // speechSynthesis.speak(utterance);
                // utterance.addEventListener("end", (event) => {
                //     //updatepanggil();
                //     /* setTimeout(() => {
                //         panggilantrian();
                //     }, 1000); */
                // });

                // utterance.addEventListener("error", (event) => {
                //     alert(`An error has occurred with the speech synthesis: ${event.error}`);
                // });
                //setTimeout(() => {


                //updatepanggil();
                // var list = [];
                // if (nourut !== 'null') {
                //     nourut.split('-').map(function (f, i) {
                //         if ($.isNumeric(f)) {
                //             p = pembilang(f).trim();
                //             p.split(' ').map(function (g) {
                //                 list.push(g);
                //             })
                //         } else {
                //             list.push(f);
                //         }
                //     });
                // }
                // var sounds = [];
                // sounds.push('../dist/soundnew/Antrian.mp3');
                // if (sounds !== 'null') {
                //     list.map(function (s) {
                //         sounds.push('../dist/soundnew/' + s + '.mp3');
                //     });
                // }
                // sounds.push('../dist/soundnew/poli/' + idpoli + '.mp3');
                // soundPaths = sounds.map(function (sound) {
                //     return sound;
                // });
                // console.log(soundPaths);
                // playSequentialSounds();
                //}, 1000);
                //})
            } else {
                /*  $("#antrianpanggil").removeClass('animate__fadeInUp animate__fadeOutUp');
                 $("#poliantrian").removeClass('animate__fadeInUp animate__fadeOutUp'); */
                $("#tdkadaantrian").html(res.message);
                setTimeout(() => {
                    panggilantrian();
                }, 2000);
            }
        }, error: function (res) {

            setTimeout(() => {
                panggilantrian();
            }, 2000);
        }
    });
}