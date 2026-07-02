$(document).ready(function() {
    var lastcall = "";
    var context;
    var soundSource;
    var soundBuffer;
    var currentIndex = 0;

    init();
    loaddata();
    
    setInterval(antrianselanjutnya,2000);

    function loaddata(){
        antrianselanjutnya();
        antriansaatini();
    };

    function init() {
        if (typeof AudioContext !== "undefined") {
            context = new AudioContext();
        } else if (typeof webkitAudioContext !== "undefined") {
            context = new webkitAudioContext();
        } else {
            throw new Error('AudioContext not supported.');
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
                volumeNode.gain.value = 0.5; // Sesuaikan sesuai kebutuhan
    
                // Tambahkan event listener untuk menangani kejadian 'ended'
                soundSource.addEventListener('ended', function () {
                    playNextSound();
                    // Tambahkan alert di sini setelah pemutaran selesai
                    // alert("Pemutaran selesai!");
                    

                    if (currentIndex === soundPaths.length) {
                        updateAntrian(0);
                    $("#loketsaatini").html("LOKET " + result[0].LOKET);
                    }
                });
    
                // Mulai memutar suara saat ini
                soundSource.start(context.currentTime);
            },
            function (e) {
                console.log("Error decoding audio data", e);
            }
        );
        currentIndex++;
    }

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
        }
    }

    function playSequentialSounds() {
        currentIndex = 0; // Reset indeks ketika tombol Play diklik
        isLoopingFinished = false; // Setel variabel menjadi false sebelum looping dimulai
        playNextSound();
    }

    function setSound() {
        if (currentIndex < soundPaths.length) {
            var request = new XMLHttpRequest();
            request.open("GET", soundPaths[currentIndex], true);
            request.responseType = "arraybuffer";

            request.onload = function () {
                var audioData = request.response;
                startSound(audioData);
            };

            request.send();
            currentIndex++;
        }
    }

    function updateAntrian(index, noantrian, result) {
        $("#antriansaatini").html(noantrian[index]);

        if (index < noantrian.length - 1) {
            setTimeout(function () {
                updateAntrian(index + 1, noantrian, result);
            }, 3000);
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
                    var result     = data.responResult;
                    var noantrian  = result[0].NO_ANTRIAN.split('|');
                    var dataString = result[0].SOUNDANTRIAN;

                    soundPaths = dataString.split('|').map(function (sound) {
                        return '../dist/sound/' + sound + '.wav';
                    });

                    playSequentialSounds();

                    // function updateAntrian(index) {
                    //     $("#antriansaatini").html(noantrian[index]);

                    //     if (index < noantrian.length - 1) {
                    //         setTimeout(function () {
                    //             updateAntrian(index + 1);
                    //         }, 3000);
                    //     }
                    // }

                    // updateAntrian(0);
                    // $("#loketsaatini").html("LOKET " + result[0].LOKET);
                    
                }
            },
            complete: function() {
                lastupdate();
            }
        });
        return false;
    };

    function antrianselanjutnya()
    {
        $.ajax({
            url     : url + "index.php/View_4/antrianselanjutnya",
            method  : "POST",
            dataType: "JSON",
            cache   : false,
            success: function (data) {
                var disabilitas = "";
                var daftarrj    = "";
                var rujukan     = "";
    
                if (data.responCode == "00") {
                    var result = data.responResult;


                    for (var i in result) {
                        
                        if(result[i].KODE==="A"){
                            daftarrj +="<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                            daftarrj +="<h1 style='color:#ed8423; font-size:350%'><strong>"+result[i].ANTRIAN+"</strong></h1>";
                            daftarrj +="</div>";
                        }
                        
                        if(result[i].KODE==="B"){
                            disabilitas +="<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                            disabilitas +="<h1 style='color:#ed8423; font-size:350%'><strong>"+result[i].ANTRIAN+"</strong></h1>";
                            disabilitas +="</div>";
                        }

                        if(result[i].KODE==="C"){
                            rujukan +="<div class='m-2 col-md-12 d-flex justify-content-center align-items-center' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                            rujukan +="<h1 style='color:#ed8423; font-size:350%'><strong>"+result[i].ANTRIAN+"</strong></h1>";
                            rujukan +="</div>";
                        }
                    }   
                }


                $("#disabilitas").html(disabilitas);
                $("#daftarrj").html(daftarrj);
                $("#rujukan").html(rujukan);
            },
            complete: function() {
                lastupdate();
            }
        });
        return false;
    };

    function updateantrian($transid,$kode,$urut,$jmlcall){
        console.log ("Proccess Update");
        $.ajax({
            url     : url+"index.php/View_4/updateantrian",
            data    : {'transid':$transid,'kode':$kode,'urut':$urut,'jmlcall':$jmlcall},
            method  : "POST",
            dataType: "JSON",
            cache   : false,
            success:function(data){
                console.log ("Proccess "+data.responHead);
            }
        });
        return false;
    };

    function lastupdate()
    {
        var timezone    = new Date().toLocaleString("en-US", { timeZone: "Asia/Jakarta" });
        var jakartaTime = new Date(timezone);
    
        var jam   = jakartaTime.getHours();
        var menit = jakartaTime.getMinutes();
        var detik = jakartaTime.getSeconds();
    
        var zonaWaktu = jakartaTime.toString().match(/\(([^)]+)\)$/)[1];
    
        var formattedTime = jam + ":" + (menit < 10 ? "0" + menit : menit) + ":" + (detik < 10 ? "0" + detik : detik) + " " + zonaWaktu;
    
        $("#lastupdate").html("Last Update : "+formattedTime);
    };

   document.querySelector('.play').addEventListener('click', function () {
        soundPaths =[];
        playSequentialSounds();
        $("#playButton").addClass("d-none");
        $("#displayantrian").removeClass("d-none");
    });
});