rawatjalanselesai();

$('#carouselExampleSlidesOnly').carousel();
$('#carouselExampleSlidesOnly').on('slid.bs.carousel', function () {
    if ($(this).find('.carousel-inner .carousel-item:last').hasClass('active')) {
        // window.location.reload();
        //rawatjalanselesai();
        //setInterval(() => {
        location.reload();
        //}, 60 * 1000 * 5);
    }
});

function rawatjalanselesai() {
    $.ajax({
        url: url + "index.php/View_12/rawatjalanselesai",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            console.log(data);
            if (data.responCode === "00") {
                var datapoliid = [];
                var previousPOLI_ID = null;
                var containers = {};
                var result = data.responResult;
                var carouselInner = document.getElementById('countainermonitoringpelayanan');

                for (var i in result) {
                    var IDCOUNTEINER = result[i].COUNTEINER;

                    if (IDCOUNTEINER != previousPOLI_ID) {
                        datapoliid.push(IDCOUNTEINER);
                    }

                    previousPOLI_ID = IDCOUNTEINER;
                }

                carouselInner.innerHTML = '';
                datapoliid.forEach(function (item, index) {
                    var div = document.createElement('div');
                    div.className = 'carousel-item' + (index === 0 ? ' active' : ''); // Tandai item pertama sebagai aktif
                    // div.setAttribute('data-interval', '10000');
                    div.innerHTML = '<div class="row overflow-hidden p-1" id="' + item + '"></div>';

                    carouselInner.appendChild(div); // Append the new 'div' element to the existing 'carousel-inner'
                });

                for (var i in result) {
                    var POLI_ID = result[i].COUNTEINER;

                    if (POLI_ID != previousPOLI_ID) {
                        datapoliid.push(POLI_ID);
                        containers[POLI_ID] = "";
                    }

                    previousPOLI_ID = POLI_ID;
                }

                for (var i in result) {
                    var POLI_ID = result[i].COUNTEINER;

                    if (containers[POLI_ID] !== undefined) {

                        if (POLI_ID !== previousPOLI_ID) {
                            // containers[POLI_ID] += "<div class='col-md-12 d-flex justify-content-center'>";
                            // containers[POLI_ID] += "<h1 class='text-truncate' style='color:#234974; font-weight: bold; font-size:300%;'>" + result[i].POLITUJUAN+" [ "+result[i].PAGE+" ] </h1>";
                            // containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "<div class='col-md-6 d-flex justify-content-start mb-3' data-widget='fullscreen'>";
                            containers[POLI_ID] += "<div class='align-items-center pl-5 pr-5 pt-2 pb-2' style='border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                            containers[POLI_ID] += "<h1 class='text-white mt-2 mb-2 ml-2 mr-2'>RESEP RAWAT JALAN SUDAH SELESAI</h1>";
                            containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "<div class='col-md-6 d-flex justify-content-end mb-3' data-widget='fullscreen'>";
                            containers[POLI_ID] += "<div class='align-items-center pl-5 pr-5 pt-2 pb-2' style='border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                            containers[POLI_ID] += "<h1 class='text-white mt-2 mb-2 ml-2 mr-2'>" + result[i].POLITUJUAN + " [ " + result[i].PAGE + " ]</h1>";
                            containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";
                        }

                        containers[POLI_ID] += "<div class='col-md-4'>";
                        containers[POLI_ID] += "<div class='card bg-primary'>";
                        containers[POLI_ID] += "<div class='card-body'>";
                        containers[POLI_ID] += "<h1 class='text-truncate'>" + result[i].MRPASIEN + " | " + result[i].URUT + "</h1>";
                        containers[POLI_ID] += "<h1 class='text-truncate font-weight-bold' style='font-size: 80px;'>" + result[i].NAMAPASIEN + "</h1>";
                        containers[POLI_ID] += "<hr class='mt-2 mb-2' style='border-top: 2px solid'>";
                        containers[POLI_ID] += "<h1 class='text-truncate font-weight-bold'>" + result[i].NAMADOKTER + "</h1>";
                        containers[POLI_ID] += "</div>";
                        containers[POLI_ID] += "</div>";
                        containers[POLI_ID] += "</div>";

                        previousPOLI_ID = POLI_ID;
                    }
                }

                for (var containerId in containers) {
                    $("#" + containerId).html(containers[containerId]);
                }
            } else {
                $('#countainermonitoringpelayanan').html("<div class='align-items-center m-auto pl-5 pr-5 pt-2 pb-2' style='border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; width:fit-content;'><h1 class='text-white mt-2 mb-2 ml-2 mr-2'>BELUM ADA RESEP RAWAT JALAN YANG SUDAH SELESAI</h1></div>");
                setTimeout(function () {
                    //window.location.reload();
                    rawatjalanselesai();
                }, 3000);
            }
        },
        complete: function () {
            // lastupdate();
        }
    });
    return false;
};