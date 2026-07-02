


var h = $(window).height() - 180;
$("#content").css({
    'height': h,
    'overflow': 'hidden'
});
/* $('.carousel').carousel({
    interval: 2000
}) */
/* function antrian() {
    $.ajax({
        url: url + 'index.php/View_rad/listantrian',
        type: 'post',
        dataType: 'json',
        success: function (res) {
            if (res.responCode === '00') {
                var poliid = [];
                var previousPOLI_ID = null;
                res.responResult.map(function (d) {
                    if (d.POLI_ID !== previousPOLI_ID) {
                        var data = {
                            "ID": d.POLI_ID,
                            "NAMA": d.NAMAPOLI
                        }
                        poliid.push(data);
                    }
                    previousPOLI_ID = d.POLI_ID;
                })


                poliid.map(function (d, i) {
                    setTimeout(() => {
                        var html = '';
                        html +=
                            '<div class="animate__animated animate__faster animate__slideInRight">';
                        html += "<div class='mb-4 text-center' data-widget='fullscreen'><div class='align-items-center pl-5 pr-5 pt-2 pb-2' style='border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                        html += "<h2 class='text-white mt-2 mb-2 ml-2 mr-2'>" + d.NAMA + "</h2>";
                        html += "</div></div>";
                        html += "<div class='row'>";
                        res.responResult.map(function (e) {
                            if (e.POLI_ID === d.ID) {
                                html +=
                                    '<div class="col-md-4" style="margin-bottom:15px;" id="col-periksa" data-id="' + e.EPISODE_ID + '">' +
                                    '   <div class="card mb-0" style="background:#234974; color: #ffff;">' +
                                    '   <div class="card-body">' +
                                    '       <h1 style="font-size:40px;" id="periksa-pasien">' + e.NAMA + '</h1>' +
                                    '       <div id="periksa-separator" style="margin:5px 0;"><div style="background: #4aac90;width:100%;height:3px;"></div></div>' +
                                    // '       <h1 class="animate__animated text-truncate animate__fast" style="font-size: 60px; color:#234974;margin:0;" id="periksa-dokter">' + d.NAMADOKTER + '</h1>' +
                                    '       <h1 style="font-size: 30px; color:#ffffff;margin:0;" id="periksa-poli">' + e.NAMADOKTER + '</h1 > ' +
                                    '   </div>' +
                                    '</div>' +
                                    '</div> ';
                            }
                        });
                        html += "</div>";
                        html += '</div>';
                        $("#listantrian").html(html)
                        if (poliid.length - 1 === i) {
                            setTimeout(function () {
                                antrian();
                            }, 5000);
                        }
                    }, 5000 * (i));

                })
            } else {
                $("#listantrian").html('<div class="" style="margin:30px auto;padding:10px 20px;font-size:45px;border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;">Belum ada pasien yang diperiksa</div>');
                setTimeout(function () {
                    antrian();
                }, 5000);
            }
        }
    })
} */

listpanggil();
function listpanggil() {
    $.ajax({
        url: url + 'index.php/View_rad/listpanggil',
        type: 'post',
        dataType: 'json',
        success: function (res) {
            console.log(res);
            if (res.responCode === '00') {
                var poliid = [];
                var previousPOLI_ID = null;
                res.responResult.map(function (d) {
                    if (d.POLIID !== previousPOLI_ID) {
                        var data = {
                            "ID": d.POLIID,
                            "NAMA": d.NAMAPOLI
                        }
                        poliid.push(data);
                    }
                    previousPOLI_ID = d.POLIID;
                })



                poliid.map(function (d, i) {
                    setTimeout(() => {
                        var html = '';
                        html +=
                            '<div class="animate__animated animate__faster animate__slideInRight">';
                        html += "<div class='mb-4 text-center' data-widget='fullscreen'><div class='align-items-center pl-5 pr-5 pt-2 pb-2' style='border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                        html += "<h1 class='text-white mt-2 mb-2 ml-2 mr-2'>" + d.NAMA + "</h1>";
                        html += "</div></div>";
                        html += "<div class='row justify-content-center'>";
                        res.responResult.map(function (e) {
                            if (e.POLIID === d.ID) {
                                html +=
                                    '<div class="col-md-6" style="margin-bottom:15px;" id="col-periksa" data-id="' + e.EPISODE_ID + '">' +
                                    '   <div class="card mb-0 rounded-pill" style="background:#234974; color: #ffff;">' +
                                    '   <div class="card-body">' +
                                    '       <h1 class="m-0" style="font-size:65px;" id="periksa-pasien">' + e.NAMAPASIEN + '</h1>' +
                                    /* '       <div id="periksa-separator" style="margin:5px 0;"><div style="background: #4aac90;width:100%;height:4px;border-radius:50px;"></div></div>' +

                                    '       <h1 style="font-size: 46px; color:#ffffff;margin:0;" id="periksa-poli">' + e.NAMADOKTER + '</h1 > ' + */
                                    '   </div>' +
                                    '</div>' +
                                    '</div> ';
                            }
                        });
                        html += "</div>";
                        html += '</div>';

                        $("#listantrian").html(html)
                        if (poliid.length - 1 === i) {
                            setTimeout(function () {
                                listpanggil();
                            }, 5000);
                        }
                    }, 5000 * (i));

                })
            } else {
                $("#listantrian").html('<div class="" style="margin:30px auto;padding:10px 20px;font-size:45px;border-radius: 50px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;">Belum ada pasien yang diperiksa</div>');
                setTimeout(function () {
                    listpanggil();
                }, 5000);
            }
        }
    });
}