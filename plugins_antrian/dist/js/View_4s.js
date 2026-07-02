


$(document).ready(function () {

    function refreshContent() {
        var body = document.body.innerHTML;
        $('body').html(body);
        console.log('test');
        //
        // Create a new XMLHttpRequest object

    }

    var antriansaatinihtml = $("#antriansaatini");
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
            temp =
                pembilang(simpanNilaiBagi) + ' miliar' + pembilang(nilai % 1000000000);
        } else if (nilai < 1000000000000000) {
            simpanNilaiBagi = Math.floor(nilai / 1000000000000);
            temp = pembilang(nilai / 1000000000000) + ' triliun' + pembilang(nilai % 1000000000000);
        }

        return temp;
    }

    antriansaatini();
    antrianselanjutnya();

    function antrianselanjutnya() {
        $.ajax({
            url: url + "index.php/View_4/antrianselanjutnya",
            method: "POST",
            dataType: "JSON",
            cache: false,
            success: function (data) {
                var disabilitas = "";
                var daftarrj = "";
                var rujukan = "";

                if (data.responCode == "00") {
                    var result = data.responResult;


                    for (var i in result) {

                        if (result[i].KODE === "A") {
                            daftarrj += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                            daftarrj += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                            daftarrj += "</div>";
                        }

                        if (result[i].KODE === "B") {
                            disabilitas += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                            disabilitas += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                            disabilitas += "</div>";
                        }

                        if (result[i].KODE === "C") {
                            rujukan += "<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                            rujukan += "<h1 style='color:#ed8423; font-size:350%'><strong>" + result[i].ANTRIAN + "</strong></h1>";
                            rujukan += "</div>";
                        }
                    }
                }


                $("#disabilitas").html(disabilitas);
                $("#daftarrj").html(daftarrj);
                $("#rujukan").html(rujukan);
            },
            complete: function () {
                lastupdate();
            }
        });
        return false;
    };

    function antriansaatini() {
        $.ajax({
            url: url + "index.php/View_4/antriansaatini",
            method: "POST",
            dataType: "JSON",
            cache: false,
            success: function (data) {
                teguh = false;

                if (data.responCode == "00") {

                    var result = data.responResult;
                    var noantrian = result[0].NO_ANTRIAN;
                    //var dataString = result[0].SOUNDANTRIAN;
                    /*   $.ajax({
                          url: url + "index.php/View_4/updateantrianpanggil",
                          type: 'post',
                          data: {
                              noantrian: noantrian
                          },
                          success: function (res) {
                              console.log(res);
                          }
                      }); */
                    const audioCtx = new AudioContext();
                    const audio = new Audio('../dist/sound/opening.mp3');
                    const source = audioCtx.createMediaElementSource(audio);
                    source.connect(audioCtx.destination);
                    audio.play();
                    audio.addEventListener('ended', function () {
                        const audio = new Audio('../dist/sound/nomor_antrian.mp3');
                        const source = audioCtx.createMediaElementSource(audio);
                        source.connect(audioCtx.destination);
                        audio.play();
                        audio.addEventListener('ended', function () {
                            var arr = noantrian;
                            var antrian = [];
                            arr.split('|').map(function (d, i) {
                                antrian.push(d);
                            })
                            var ant = antrian.map(function (d) {
                                return d;
                            });
                            var index = 0;
                            antrianplay();
                            var p = '';
                            function antrianplay() {

                                if (ant[index]) {
                                    console.log(index);
                                    /*  $.ajax({
                                         url: url + "index.php/View_4/updateantrian",
                                         type: 'post',
                                         data: {
                                             noantrian: ant[index]
                                         },
                                         success: function (res) {
                                             console.log(res);
                                         }
                                     }); */
                                    var index2 = index;
                                    var list = [];
                                    antriansaatinihtml.html(ant[index]);
                                    antriansaatinihtml.addClass('animate__fadeInUp').removeClass('animate__fadeOutUp');
                                    ant[index].split('-').map(function (f, i) {
                                        if ($.isNumeric(f)) {
                                            p = pembilang(f).trim();
                                            p.split(' ').map(function (g) {
                                                list.push(g);
                                            })
                                        } else {
                                            list.push(f.toLowerCase());
                                        }

                                    });
                                    setTimeout(() => {

                                        list.map(function (d, i) {
                                            timer = 1000 * (i + 1);
                                            setTimeout(() => {
                                                const audio = new Audio('../dist/sound/' + d + '.mp3');
                                                const source = audioCtx.createMediaElementSource(audio);
                                                source.connect(audioCtx.destination);
                                                audio.play();
                                                if (list.length === (i + 1)) {
                                                    setTimeout(() => {
                                                        antrianplay();

                                                    }, 1000);
                                                    if (ant.length === (index2 + 1)) {
                                                        setTimeout(() => {
                                                            refreshContent();
                                                        }, 2000);
                                                    }
                                                    setTimeout(() => {
                                                        antriansaatinihtml.removeClass('animate__fadeInUp').addClass('animate__fadeOutUp');
                                                        setTimeout(() => {
                                                            antriansaatinihtml.html('');
                                                        }, 300);
                                                    }, 500);

                                                }

                                            }, timer);
                                        })
                                    }, 1000);
                                    index++;
                                }


                            }
                            /*   antrian.map(function (d) {
                                  $.when(antrianplay()).done(function () {
  
                                  });
                              }); */



                        });
                    });



                    /* setTimeout(() => {
    
                        function updateAntrian(index) {
                            playSequentialSounds();
    
                            $("#antriansaatini").html(noantrian[index]);
    
                            if (index < noantrian.length - 1) {
                                setTimeout(function () {
                                    updateAntrian(index + 1);
                                }, 3000);
                            }
                        }
    
                        updateAntrian(0);
    
                        $("#loketsaatini").html("LOKET " + result[0].LOKET);
                    }, 4000); */
                } else {
                    setTimeout(() => {
                        refreshContent();
                    }, 2000);
                }
            },
            complete: function () {
                lastupdate();
            }
        });
        return false;
    };

    function lastupdate() {
        var timezone = new Date().toLocaleString("en-US", { timeZone: "Asia/Jakarta" });
        var jakartaTime = new Date(timezone);

        var jam = jakartaTime.getHours();
        var menit = jakartaTime.getMinutes();
        var detik = jakartaTime.getSeconds();

        var zonaWaktu = jakartaTime.toString().match(/\(([^)]+)\)$/)[1];

        var formattedTime = jam + ":" + (menit < 10 ? "0" + menit : menit) + ":" + (detik < 10 ? "0" + detik : detik) + " " + zonaWaktu;

        $("#lastupdate").html("Last Update : " + formattedTime);
    };


});