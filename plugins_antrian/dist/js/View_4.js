


var antriansaatinihtml = $("#antriansaatini");
var loketsaatinihtml = $("#loketsaatini");
var saatinirj = $("#saatinirj");
var saatinilaboratorium = $("#saatinilaboratorium");
var saatinirujukan = $("#saatinirujukan");
var xhr, xhr2, xhr3;
var lastcall = '';
var lastantri = '';
var call = '';
var callantri = '';
var audioContext;
var antrian;
var ending;
var index2;
var index;
var source;
var audioFileUrl;
var audioData;
var loket;
var lastindex;
var currindex;
var noantrian;
init();
loaddata();
function loaddata() {
    antrianselanjutnya();
    antriansaatini();
};

function init() {
    if (typeof AudioContext !== "undefined") {
        audioContext = new window.AudioContext();
    } else if (typeof webkitAudioContext !== "undefined") {
        audioContext = new window.webkitAudioContext();
    } else {
        throw new Error('AudioContext not supported.');
    }
};
setInterval(() => {
    $(document).trigger('click');
}, 5000);
/* setInterval(function () {
    if (window.performance && window.performance.memory) {
        const memoryInfo = window.performance.memory;

        var html = "Memory Usage Information:<br/>";
        html += "Total JS Heap Size: " + memoryInfo.totalJSHeapSize + "<br/>";
        html += "Used JS Heap Size: " + memoryInfo.usedJSHeapSize + "<br/>";
        html += "JS Heap Size Limit: " + memoryInfo.jsHeapSizeLimit + "<br/>";
    } else {
        var html = "Memory information not available in this environment.";
    }
    $("#antrian").html(html);
}, 1000) */
/* antrianselanjutnya(); */
function pembilang(nilai) {
    nilai = Math.floor(Math.abs(nilai));

    var simpanNilaiBagi = 0;
    var huruf = [
        '',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        'sepuluh',
        'sebelas',
    ];
    var temp = '';

    if (nilai < 12) {
        temp = ' ' + huruf[nilai];
    } else if (nilai < 20) {
        temp = pembilang(Math.floor(nilai - 10)) + ' belas';
    } else if (nilai < 100) {
        simpanNilaiBagi = Math.floor(nilai / 10);
        temp = pembilang(simpanNilaiBagi) + ' puluh' + pembilang(nilai % 10);
    } else if (nilai < 200) {
        temp = ' seratus' + pembilang(nilai - 100);
    } else if (nilai < 1000) {
        simpanNilaiBagi = Math.floor(nilai / 100);
        temp = pembilang(simpanNilaiBagi) + ' ratus' + pembilang(nilai % 100);
    } else if (nilai < 2000) {
        temp = ' seribu' + pembilang(nilai - 1000);
    } else if (nilai < 1000000) {
        simpanNilaiBagi = Math.floor(nilai / 1000);
        temp = pembilang(simpanNilaiBagi) + ' ribu' + pembilang(nilai % 1000);
    } else if (nilai < 1000000000) {
        simpanNilaiBagi = Math.floor(nilai / 1000000);
        temp = pembilang(simpanNilaiBagi) + ' juta' + pembilang(nilai % 1000000);
    } else if (nilai < 1000000000000) {
        simpanNilaiBagi = Math.floor(nilai / 1000000000);
        temp = pembilang(simpanNilaiBagi) + ' miliar' + pembilang(nilai % 1000000000);
    } else if (nilai < 1000000000000000) {
        simpanNilaiBagi = Math.floor(nilai / 1000000000000);
        temp = pembilang(nilai / 1000000000000) + ' triliun' + pembilang(nilai % 1000000000000);
    }

    return temp;
}
function antrianselanjutnya() {
    $.ajax({
        url: url + "index.php/View_4/antrianselanjutnya",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            var lab = "";
            var daftarrj = "";
            var rujukan = "";
            if (data.responCode == "00") {
                var result = data.responResult;
                for (var i in result) {

                    if (result[i].KODE === "A" && result[i].STATUS === '1') {
                        daftarrj += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                        daftarrj += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                        daftarrj += "</div>";
                    } else if (result[i].KODE === "A" && result[i].STATUS === '2') {
                        daftarrj += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                        daftarrj += "<h1 style='color:#777777; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                        daftarrj += "</div>";
                    }

                    if (result[i].KODE === "B" && result[i].STATUS === '1') {
                        lab += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                        lab += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                        lab += "</div>";
                    } else if (result[i].KODE === "B" && result[i].STATUS === '2') {
                        lab += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                        lab += "<h1 style='color:#777777; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                        lab += "</div>";
                    }

                    // if (result[i].KODE === "C" && result[i].STATUS === '1') {
                    //     rujukan += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                    //     rujukan += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                    //     rujukan += "</div>";
                    // } else if (result[i].KODE === "C" && result[i].STATUS === '2') {
                    //     rujukan += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                    //     rujukan += "<h1 style='color:#777777; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                    //     rujukan += "</div>";
                    // }
                }
            }
            $("#laboratorium").html(lab);
            $("#daftarrj").html(daftarrj);
            // $("#rujukan").html(rujukan);
        }/* ,
        compvare: function () {
            lastupdate();
        } */
    });
    return false;
};

function playSequentialSounds(t = null, last = null) {
    index = 0;
    playsound(t, last);
};
function playsound(t = null, last = null) {
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
                    gainNode.connect(audioContext.destination);
                    source.onended = function () {
                        audioContext.close();
                        source.disconnect(gainNode);
                        source.stop();
                        gainNode.disconnect(audioContext.destination);
                        if (lastindex === currindex) {
                            if (t === 'antri') {
                                if (last !== 'last') {
                                    antriansaatinihtml.removeClass('animate__flipInX').addClass('animate__flipOutX');
                                    /* saatinirj.removeClass('animate__flipInX').addClass('animate__flipOutX');
                                    saatinidisabilitas.removeClass('animate__flipInX').addClass('animate__flipOutX');
                                    saatinirujukan.removeClass('animate__flipInX').addClass('animate__flipOutX'); */
                                    setTimeout(() => {
                                        antrianarr();
                                    }, 200);
                                } else {

                                    ending = 'ending';
                                    soundPaths = ending.split('|').map(function (sound) {
                                        return '../dist/sound/' + sound + '.mp3';
                                    });
                                    playSequentialSounds('ending', '');
                                }
                            } else if (t === 'ending') {
                                $.ajax({
                                    url: url + "index.php/View_4/updateantrianpanggil",
                                    type: 'post',
                                    data: {
                                        noantrian: noantrian
                                    },
                                    success: function (res) {
                                    }
                                });
                                //location.reload();
                                setTimeout(loaddata, 2000);
                            } else {
                                index2 = 0;
                                antrianarr();
                            }
                        } else {
                            setTimeout(function () {
                                playsound(t, last);
                            }, 200)

                        }

                    }
                    source.onerror = function () {
                        alert('error');
                    }
                    source.start();
                }, function () {
                    alert('error');
                });
                index++;
            } else {
                alert('error');
            }

        }
        xhr.onerror = function () {
            alert('error');
        }
        xhr.send();
    } else {
        alert('error');
    }
}
var ant = '';
index2 = 0;
function antrianarr() {
    if (ant.length > index2) {
        $.ajax({
            url: url + "index.php/View_4/updateantrian",
            type: 'post',
            data: {
                noantrian: ant[index2]
            },
            success: function (res) {
            }
        });
        var list = [];
        antriansaatinihtml.html(ant[index2]);
        loketsaatinihtml.html("LOKET " + loket);
        antriansaatinihtml.addClass('animate__flipInX').removeClass('animate__flipOutX');
        loketsaatinihtml.addClass('animate__flipInX').removeClass('animate__flipOutX');
        ant[index2].split('-').map(function (f, i) {

            if (f[0] === 'A') {
                saatinirj.html(ant[index2]);
                saatinirj.addClass('animate__flipInX').removeClass('animate__flipOutX');
            }
            if (f[0] === 'B') {
                saatinilaboratorium.html(ant[index2]);
                saatinilaboratorium.addClass('animate__flipInX').removeClass('animate__flipOutX');
            }
            if (f[0] === 'C') {
                saatinirujukan.html(ant[index2]);
                saatinirujukan.addClass('animate__flipInX').removeClass('animate__flipOutX');
            }
            if ($.isNumeric(f)) {
                p = pembilang(f).trim();
                p.split(' ').map(function (g) {
                    list.push(g);
                })
            } else {
                list.push(f.toLowerCase());
            }

        });
        soundPaths = list.map(function (sound) {
            return '../dist/sound/' + sound + '.mp3';
        });
        if (ant.length - 1 === index2) {
            last = 'last';
        } else {

            last = 'no';
        }
        playSequentialSounds('antri', last);
        index2++;
    } else {
        alert('error');
    }
}

function antriansaatini() {
    $.ajax({
        url: url + "index.php/View_4/antriansaatini",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            if (data.responCode == "00") {
                loketsaatinihtml.removeClass('animate__flipInX').addClass('animate__flipOutX');
                antriansaatinihtml.removeClass('animate__flipInX').addClass('animate__flipOutX');
                /* saatinirj.removeClass('animate__flipInX').addClass('animate__flipOutX');
                saatinidisabilitas.removeClass('animate__flipInX').addClass('animate__flipOutX');
                saatinirujukan.removeClass('animate__flipInX').addClass('animate__flipOutX'); */
                var result = data.responResult;
                noantrian = result[0].NO_ANTRIAN;
                var dataString = result[0].SOUNDANTRIAN;
                loket = result[0].LOKET;
                soundPaths = dataString.split('|').map(function (sound) {
                    return '../dist/sound/' + sound + '.mp3';
                });
                index2 = 0;
                playSequentialSounds('', '');
                antrian = [];
                noantrian.split('|').map(function (d) {
                    antrian.push(d);
                })
                ant = antrian.map(function (d) {
                    return d;
                });
                // var interval = setInterval(() => {
                //     if ($("#playopening").attr('action') === 'selesai') {
                //         var antrian = [];
                //         noantrian.split('|').map(function (d) {
                //             antrian.push(d);
                //         })
                //         ant = antrian.map(function (d) {
                //             return d;
                //         });
                //         index2 = 0;
                //         antrianarr();
                //         clearInterval(interval);
                //     }
                // }, 1000);
                // function plays() {
                //     if (soundPaths.length > currindex) {
                //         xhr = new XMLHttpRequest();
                //         var audioFileUrl = soundPaths[currindex];

                //         xhr.open('GET', audioFileUrl, true);
                //         xhr.responseType = 'arraybuffer';
                //         xhr.onload = function () {
                //             if (xhr.status === 200) {
                //                 var index2 = currindex;
                //                 var audioData = xhr.response;
                //                 //var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                //                 audioContext.decodeAudioData(audioData, function (buffer) {
                //                     var source = audioContext.createBufferSource();
                //                     source.buffer = buffer;
                //                     /* var volumeNode = audioContext.createGain();
                //                     source.connect(volumeNode);
                //                     volumeNode.gain.value = 0.5; */
                //                     source.connect(audioContext.destination);
                //                     source.onended = function () {
                //                         source.stop();
                //                         plays();
                //                         if (soundPaths.length === index2) {
                //                             loketsaatinihtml.html("LOKET " + loket);
                //                             loketsaatinihtml.addClass('animate__flipInX').removeClass('animate__flipOutX');
                //                             //console.log('selesai');
                //                             var antrian = [];
                //                             noantrian.split('|').map(function (d) {
                //                                 antrian.push(d);
                //                             })
                //                             var ant = antrian.map(function (d) {
                //                                 return d;
                //                             });
                //                             var index = 0;
                //                             playantrian();
                //                             var p = '';
                //                             function playantrian() {

                //                                 if (ant.length > index) {
                //                                     $.ajax({
                //                                         url: url + "index.php/View_4/updateantrian",
                //                                         type: 'post',
                //                                         data: {
                //                                             noantrian: ant[index]
                //                                         },
                //                                         success: function (res) {
                //                                             //console.log(res);
                //                                         }
                //                                     });
                //                                     lastantri = ant[ant.length - 1];
                //                                     callantri = ant[index];
                //                                     var list = [];
                //                                     antriansaatinihtml.html(ant[index]);
                //                                     antriansaatinihtml.addClass('animate__flipInX').removeClass('animate__flipOutX');
                //                                     ant[index].split('-').map(function (f, i) {
                //                                         if (f[0] === 'A') {
                //                                             saatinirj.html(ant[index]);
                //                                             //setTimeout(() => {
                //                                             saatinirj.addClass('animate__flipInX').removeClass('animate__flipOutX');
                //                                             //}, 500);
                //                                         }
                //                                         if (f[0] === 'B') {
                //                                             saatinidisabilitas.html(ant[index]);
                //                                             // setTimeout(() => {
                //                                             saatinidisabilitas.addClass('animate__flipInX').removeClass('animate__flipOutX');
                //                                             // }, 500);
                //                                         }
                //                                         if (f[0] === 'C') {
                //                                             saatinirujukan.html(ant[index]);
                //                                             // setTimeout(() => {
                //                                             saatinirujukan.addClass('animate__flipInX').removeClass('animate__flipOutX');
                //                                             //}, 500);
                //                                         }
                //                                         if ($.isNumeric(f)) {
                //                                             p = pembilang(f).trim();
                //                                             p.split(' ').map(function (g) {
                //                                                 list.push(g);
                //                                             })
                //                                         } else {
                //                                             list.push(f.toLowerCase());
                //                                         }

                //                                     });
                //                                     soundnomor = list.map(function (sound) {
                //                                         return '../dist/sound/' + sound + '.mp3';
                //                                     });
                //                                     var indexnomor = 0;
                //                                     playnomor();

                //                                     function playnomor() {
                //                                         if (soundnomor.length > indexnomor) {
                //                                             xhr = new XMLHttpRequest();
                //                                             var soundnomorp = soundnomor[indexnomor];
                //                                             lastcall = soundnomor[soundnomor.length - 1];
                //                                             call = soundnomorp;
                //                                             xhr.open('GET', soundnomorp, true);
                //                                             xhr.responseType = 'arraybuffer';
                //                                             xhr.onload = function () {
                //                                                 // var indexnomor2 = indexnomor;
                //                                                 var audioData = xhr.response;
                //                                                 //var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                //                                                 audioContext.decodeAudioData(audioData, function (buffer) {
                //                                                     var source = audioContext.createBufferSource();
                //                                                     source.buffer = buffer;
                //                                                     /* var volumeNode = audioContext.createGain();
                //                                                     source.connect(volumeNode);
                //                                                     volumeNode.gain.value = 0.5; */
                //                                                     source.connect(audioContext.destination);
                //                                                     source.onended = function () {
                //                                                         source.stop();
                //                                                         if (lastcall === call) {
                //                                                             if (lastantri === callantri) {
                //                                                                 // $.ajax({
                //                                                                 //     url: url + "index.php/View_4/updateantrianpanggil",
                //                                                                 //     type: 'post',
                //                                                                 //     data: {
                //                                                                 //         noantrian: noantrian
                //                                                                 //     },
                //                                                                 //     success: function (res) {
                //                                                                 //         //console.log(res);
                //                                                                 //     }
                //                                                                 // });
                //                                                                 xhr = new XMLHttpRequest();
                //                                                                 var audioFileUrl = '../dist/sound/ending.mp3';
                //                                                                 xhr.open('GET', audioFileUrl, true);
                //                                                                 xhr.responseType = 'arraybuffer';
                //                                                                 xhr.onload = function () {
                //                                                                     if (xhr.status === 200) {
                //                                                                         var audioData = xhr.response;
                //                                                                         //var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                //                                                                         audioContext.decodeAudioData(audioData, function (buffer) {
                //                                                                             var source = audioContext.createBufferSource();
                //                                                                             source.buffer = buffer;
                //                                                                             /* var volumeNode = audioContext.createGain();
                //                                                                             source.connect(volumeNode);
                //                                                                             volumeNode.gain.value = 0.5; */
                //                                                                             source.connect(audioContext.destination);
                //                                                                             source.onended = function () {
                //                                                                                 source.stop();
                //                                                                                 //setTimeout(() => {
                //                                                                                 loaddata();
                //                                                                                 //}, 2000);
                //                                                                             }
                //                                                                             source.start(audioContext.currentTime);
                //                                                                         })

                //                                                                     }
                //                                                                 }
                //                                                                 xhr.send();
                //                                                             } else {
                //                                                                 playantrian();
                //                                                             }
                //                                                         } else {
                //                                                             playnomor();
                //                                                         }


                //                                                         //return false;
                //                                                         // if (soundnomor.length === (indexnomor)) {
                //                                                         //     setTimeout(() => {
                //                                                         //         playantrian();
                //                                                         //     }, 1000);
                //                                                         //     if (ant.length === (index + 1)) {
                //                                                         //         $.ajax({
                //                                                         //             url: url + "index.php/View_4/updateantrianpanggil",
                //                                                         //             type: 'post',
                //                                                         //             data: {
                //                                                         //                 noantrian: noantrian
                //                                                         //             },
                //                                                         //             success: function (res) {
                //                                                         //                 //console.log(res);
                //                                                         //             }
                //                                                         //         });

                //                                                         //         setTimeout(() => {
                //                                                         //             xhr = new XMLHttpRequest();
                //                                                         //             var audioFileUrl = '../dist/sound/ending.mp3';
                //                                                         //             xhr.open('GET', audioFileUrl, true);
                //                                                         //             xhr.responseType = 'arraybuffer';

                //                                                         //             xhr.onload = function () {
                //                                                         //                 if (xhr.status === 200) {
                //                                                         //                     var audioData = xhr.response;
                //                                                         //                     var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                //                                                         //                     audioContext.decodeAudioData(audioData, function (buffer) {
                //                                                         //                         var source = audioContext.createBufferSource();
                //                                                         //                         source.buffer = buffer;
                //                                                         //                         source.connect(audioContext.destination);
                //                                                         //                         source.onended = function () {
                //                                                         //                             setTimeout(() => {
                //                                                         //                                 loaddata();
                //                                                         //                             }, 2000);
                //                                                         //                         }
                //                                                         //                         source.start();
                //                                                         //                     })

                //                                                         //                 }
                //                                                         //             }
                //                                                         //             xhr.send();
                //                                                         //         }, 1000);

                //                                                         //     } else {
                //                                                         //         // /setTimeout(() => {
                //                                                         //         antriansaatinihtml.removeClass('animate__flipInX').addClass('animate__flipOutX');
                //                                                         //         saatinirj.removeClass('animate__flipInX').addClass('animate__flipOutX');
                //                                                         //         saatinidisabilitas.removeClass('animate__flipInX').addClass('animate__flipOutX');
                //                                                         //         saatinirujukan.removeClass('animate__flipInX').addClass('animate__flipOutX');
                //                                                         //         //}, 500);
                //                                                         //     }
                //                                                         // }
                //                                                     }
                //                                                     source.start(audioContext.currentTime);
                //                                                 }, function (e) {
                //                                                     $.ajax({
                //                                                         url: url + "index.php/View_4/errorlog",
                //                                                         type: 'post',
                //                                                         data: {
                //                                                             //antrian: ant[index2],
                //                                                             sound: 'test',
                //                                                             status: '201',
                //                                                             statusText: 'Error decoding audio data'
                //                                                         },
                //                                                         success: function (res) {
                //                                                             //console.log(res);
                //                                                         }
                //                                                     });
                //                                                     console.log("Error decoding audio data", e);
                //                                                 });

                //                                             };
                //                                             xhr.onerror = function () {
                //                                                 $.ajax({
                //                                                     url: url + "index.php/View_4/errorlog",
                //                                                     type: 'post',
                //                                                     data: {
                //                                                         //antrian: ant[index2],
                //                                                         sound: 'test',
                //                                                         status: '201',
                //                                                         statusText: 'Error decoding audio data'
                //                                                     },
                //                                                     success: function (res) {
                //                                                         //console.log(res);
                //                                                     }
                //                                                 });
                //                                             }
                //                                             indexnomor++;
                //                                             xhr.send();
                //                                         }
                //                                     }
                //                                     index++;
                //                                 }
                //                             }
                //                         }
                //                     }
                //                     source.start(audioContext.currentTime);
                //                 }, function (error) {
                //                     $.ajax({
                //                         url: url + "index.php/View_4/errorlog",
                //                         type: 'post',
                //                         data: {
                //                             //antrian: ant[index2],
                //                             sound: 'test',
                //                             status: '201',
                //                             statusText: 'Error decoding audio data'
                //                         },
                //                         success: function (res) {
                //                             //console.log(res);
                //                         }
                //                     });
                //                 });
                //             }
                //         };
                //         currindex++;
                //         xhr.send();
                //     }

                // }

            } else {
                setTimeout(loaddata, 2000);
            }
        },
        error: function (data) {
            setTimeout(loaddata, 2000);
        }
    });
    return false;
};
/*  document.querySelector('.play').addEventListener('click', function () {
     $("#playButton").addClass("d-none");
     $("#displayantrian").removeClass("d-none");
     loaddata();
 }); */
/* document.addEventListener("click", function (evt) {
    audioContext.close();
    antriansaatini();
    setInterval(antrianselanjutnya, 1000);
}); */