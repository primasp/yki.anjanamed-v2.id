/* 
$(document).on('show.bs.modal', '.modal', function () {
    const zIndex = 1040 + 10 * $('.modal:visible').length;
    $(this).css('z-index', zIndex);
    setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
}); */

var urlParams = new URLSearchParams(window.location.search);
var kode = urlParams.get('kode');
var dokter = urlParams.get('dokter');
var poli = urlParams.get('poli');
var pos = urlParams.get('pos');
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
function reload() {
    location.reload();
}
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
listdokter();
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
function playSequentialSounds() {
    index = 0;
    //soundstop = 0;
    playsound();
};
var playingsound = false;
function playsound() {
    if (soundPaths.length > index) {
        playingsound = true;
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
                        playsound();
                        playingsound = false;
                        /* console.log(index);
                        console.log(lastindex); */
                        if (lastindex === currindex) {
                            //audioContext.close()
                        }
                    }
                    /*  source.onerror = function () {
                         alert('error');
                     } */
                    source.start();
                }, function () {
                    //alert('error');
                });
                index++;
            } /* else {
                alert('error');
            } */

        }
        xhr.onerror = function () {
            //alert('error');
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
/* function adaantrian() {
    var l = $("#col-periksa").length;
    if (l === 0) {
        $("#sedangperiksa").html('<div class="animate__animated animate__fast animate__bounceIn" style="margin:30px auto;padding:10px 20px;font-size:45px;border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;">BELUM ADA PASIEN YANG DIPERIKSA</div>');
    } else {
        $("#sedangperiksa").html('');
    }
} */
function allpoli() {
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

function pilihdokter(e) {
    var val = $(e).val();
    var dokter = $("[id*='dokter_']:checked");
    var tr = $(e).closest("#itemdokter");

    if (dokter.length >= 2) {
        $("[id*='dokter_']").prop('disabled', true);
    } else {
        $("[id*='dokter_']").prop('disabled', false);
    }
    dokter.prop('disabled', false);
    if ($(e).is(":checked")) {
        tr.attr('flag-checked', 'Y');
        tr.find("[id*='pos']").prop('disabled', false);
    } else {
        tr.removeAttr('flag-checked');
        tr.find("[id*='pos']").prop('disabled', true);
        tr.find("[id*='pos']").prop('checked', false);
    }
    // pilihpos();
}
function pilihpos() {
    ceklanjut();
}
function ceklanjut() {
    var dokter = $("[id*='dokter_']:checked");
    var right = $("[id*='pos_r']:checked");
    var left = $("[id*='pos_l']:checked");
    console.log(right.length);
    console.log(left.length);
    var length = dokter.length;
    if (length > 1) {
        if (length <= 2 && length > 0 && right.length === 1 && left.length === 1) {
            $("#btn-lanjut-antrian").prop('disabled', false);
        } else {
            $("#btn-lanjut-antrian").prop('disabled', true);
        }
    } else {
        if (length > 0) {
            $("#btn-lanjut-antrian").prop('disabled', false);
        } else {
            $("#btn-lanjut-antrian").prop('disabled', true);
        }
    }
}
$("#btn-lanjut-antrian").on('click', function () {
    var dokter2 = [];
    $("[id*='dokter_']:checked").each(function (i) {
        dokter2[i] = $(this).val();
    })
    var pos2 = [];
    $("[id*='pos_']:checked").each(function (i) {
        pos2[i] = $(this).val();
    })
    /*    var kode = $('#modaldokter').attr('kode'); */
    window.location = url + 'index.php/view_dokter?dokter=' + dokter2 + '&pos=' + pos2;
})
function pilihpoli(e) {
    poli = $(e).attr('poli-id');
    /* $("#modaldokter").modal('show');
    alldokter(poliid); */
    window.location = url + 'index.php/view_dokter?poli=' + poli;
}

function listdokter() {
    $.ajax({
        url: url + 'index.php/View_dokter/alldokter',
        type: 'post',
        dataType: 'json',
        success: function (res) {
            console.log(res);
            if (res.responCode === '00') {

                var html = '';
                res.responResult.map(function (d, i) {
                    //sedangperiksa(d.DOKTER_ID);
                    var img = d.JENKEL === 'L' ? url + 'dist/img/dokter/doctor.png' : url + 'dist/img/dokter/doctor2.png';
                    if (checkFileExists(url + 'dist/img/dokter/' + d.DOKTER_ID + '.png') === '200') {
                        img = url + 'dist/img/dokter/' + d.DOKTER_ID + '.png';
                    }
                    html +=
                        '<div class="col-sm-12 align-items-center" style="margin-bottom: 70px;">' +
                        '   <div class="row">';
                    /* if (pos[i] === 'L') {
                        html += '<div class="col-sm-1 text-right tag-left"><i class="fas fa-arrow-left fa-10x" style="background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin:0;position:absolute;top:50%;transform:translateY(-50%);left:0;"></i></div>';
                    } */
                    html +=
                        '<div class="col-sm-11">' +
                        '   <div id="item-dokter" nama-dokter="' + d.NAMA + '" data-id="' + d.DOKTER_ID + '" class="d-flex bg-white" style="border-style: solid; border-width: 7px; border-color: #234974; border-radius:500px;height:300px;">' +
                        '       <div class="image-container mr-3" style="border-radius:50%;width: 285px; height: 285px; border-style: solid; border-width: 5px; border-color: #fff; background-color: #fff; overflow: hidden; position: absolute;box-shadow:5px 10px 50px rgba(0,0,0,0.2);top:50%;transform:translateY(-50%)">' +
                        '           <img src="' + img + '" class="hover-image" style="width: 100%; height: 100%; object-fit: cover;object-position:center;">' +
                        '       </div>' +
                        '       <div class="pr-5 pt-3" style="margin-left:330px;width:81%;">' +
                        '           <h3 class="d-flex align-items-end text-truncate font-weight-bold mb-0" style="color:#234974;font-size:48px;">' + d.NAMA + '</h3>' +
                        '           <h5 class="d-flex align-items-start" style="color:#234974;font-size:32px;">' + d.NAMAPOLI + '</h5>' +
                        '           <div class="" id="periksa-separator" style="margin:10px 0;"><div style="background: linear-gradient(to right, #4aac90, #234974);width:100%;height:5px;border-radius:50px;"></div></div>' +
                        //'           <h3 style="color:#234974; font-size:24px;" class="m-0">Pasien di Periksa :</h3>' +
                        '           <div id="pasienperiksa" style="font-size:120px;line-height:1;width:max-content;background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin:0;font-weight:bold;white-space:nowrap;">...</div>' +
                        '       </div>' +
                        '   </div>' +
                        '</div>';
                    /* if (pos[i] === 'R') {
                        html += '<div class="col-sm-1 tag-right"><i class="fas fa-arrow-right fa-10x" style="background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin:0;position:absolute;top:50%;transform:translateY(-50%);right:0;" ></i></div>';
                    } */
                    html +=
                        '   </div>' +
                        '</div>';
                });
                $("#listdokter").html(html);
                sedangperiksa();
                pos = urlParams.get('pos');
            } else {
                setTimeout(() => {
                    listdokter();
                    $("#listdokter").html('');
                }, 1000);
               
                
            }
        }
    });
}
function updatepanggil(iddokter) {
    $.ajax({
        url: url + 'index.php/View_dokter/updatepanggil',
        type: 'post',
        dataType: 'json',
        data: {
            dokterid: iddokter
        },
        success: function (res) {
            console.log('berhasil update');
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
        'sepuluh',
        'sebelas',
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
function durationfile(src) {
    const audio = document.getElementById('myAudio');

    audio.addEventListener('loadedmetadata', function () {
        // The 'loadedmetadata' event is fired when the metadata has been loaded, including the duration of the audio
        const durationInSeconds = audio.duration;
        console.log('Audio duration:', durationInSeconds, 'seconds');
    });
}

/* function sedangperiksa(dokterid) {
    $.ajax({
        url: url + 'index.php/View_dokter/pasientunggu',
        type: 'post',
        dataType: 'json',
        data: {
            dokter: dokterid
        },
        success: function (res) {
            var pasien = res.responResult.PASIEN;
            pasien = pasien.length > 16 ? pasien.substring(0, 16) : pasien
            var html =
                "<div class='d-flex'>" +
                "   <div>" + res.responResult.NO_URUT + "</div>" +
                "   <div style='width:5px;background:#234974;display:block;margin:20px;'></div>" +
                "   <div>" + pasien + "</div>" +
                "</div>";
            $("[id='item-dokter'][data-id='" + dokterid + "'] #pasienperiksa").html(html);
        }
    });
} */

function sedangperiksa() {
    var duration = 15000;
    $("[id='item-dokter']").each(function (i) {

        setTimeout(() => {
            console.log(i);
            var $this = $(this);

            var iddokter = $this.attr('data-id');
            var namadokter = $this.attr('nama-dokter');
            namadokter = namadokter.split('.');
            var dokter1 = namadokter[0];
            var dokter2 = $.trim(namadokter[1]).split(' ')[0];
            var pasienperiksa = $this.find('#pasienperiksa').attr('data-id');
            $.ajax({
                url: url + 'index.php/View_dokter/pasientunggu',
                type: 'post',
                dataType: 'json',
                data: {
                    dokter: iddokter
                },
                success: function (res) {
                    $("#soundduration").html('');

                    if (res.responCode === '00') {
                        var pasien = res.responResult.PASIEN;
                        var pasienid = res.responResult.PASIEN_ID;
                        var status = res.responResult.STATUS;
                        var nourut = res.responResult.NO_URUT;
                        var poli = res.responResult.POLI_ID;
                        var dokterid = res.responResult.DOKTER_ID;
                        var list = [];
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
                        $this.addClass('active');
                        $this.find('#pasienperiksa').attr('data-id', pasienid);
                        var pasien2 = pasien.length > 16 ? pasien.substring(0, 16) : pasien
                        var pasienpanggil = nourut + " | " + (pasien2);
                        $this.find('#pasienperiksa').html(pasienpanggil);
                        if (status === '1') {
                            var ending = [];
                            ending.push('../dist/soundnew/opening.mp3');
                            ending.push('../dist/soundnew/Nomor_Antrian.mp3');
                            list.map(function (s) {
                                ending.push('../dist/soundnew/' + s + '.mp3');
                            });
                            ending.push('../dist/soundnew/Ke.mp3');
                            ending.push('../dist/soundnew/dokter/Dokter.mp3');
                            ending.push('../dist/soundnew/dokter/' + dokterid + '.mp3');
                            ending.push('../dist/soundnew/Di.mp3');
                            ending.push('../dist/soundnew/poli/Poli.mp3');
                            ending.push('../dist/soundnew/poli/' + poli + '.mp3');
                            soundPaths = ending.map(function (sound) {
                                return sound;
                                //return sound;
                            });
                            playSequentialSounds();
                            /* var durr = [];
                            ending.split('-').map(function (sound, i) {
                                var audio = new Audio();
                                audio.onloadedmetadata = function () {
                                    durr.push(audio.duration);
                                    $("#soundduration").text(durr);
                                };
                                audio.src = '../dist/sound/' + sound + '.mp3';
                            }) */
                        }
                        setTimeout(() => {
                            //$this.removeClass('active');
                            if (($("[id='item-dokter']").length - 1) === i) {
                                listdokter();
                            }
                        }, duration + 500);
                    } else {
                        console.log('ga ada');
                        $this.find('#pasienperiksa').attr('data-id', '');
                        $this.find('#pasienperiksa').html('...');
                        setTimeout(() => {
                            if (($("[id='item-dokter']").length - 1) === i) {
                                listdokter();
                            }
                        }, 500);
                    }
                }
            });

        }, duration * (i + 1));
    });
    return false;
}

function panggilpasien() {

}