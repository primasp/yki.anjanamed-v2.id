
jadwaldokter();

setInterval(jadwaldokter,15*60*1000);

function jadwaldokter()
{
    $.ajax({
        url: url + "index.php/View_2/jadwaldokter",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function(data) {
            if (data.responCode === "00") {
                var datapoliid      = [];
                var previousPOLI_ID = null;
                var containers      = {};
                var result          = data.responResult;
                var carouselInner   = document.getElementById('countainermonitoringpelayanan');

                for (var i in result) {
                    var IDCOUNTEINER = result[i].POLI_ID;

                    if (IDCOUNTEINER!=previousPOLI_ID) {
                        datapoliid.push(IDCOUNTEINER);
                    }

                    previousPOLI_ID     = IDCOUNTEINER;
                }

                carouselInner.innerHTML = '';
                datapoliid.forEach(function (item, index) {
                    var div = document.createElement('div');
                    div.className = 'carousel-item' + (index === 0 ? ' active' : ''); // Tandai item pertama sebagai aktif
                    div.setAttribute('data-interval', '5000');
                    div.innerHTML = '<div class="row overflow-hidden p-1" id="' + item + '"></div>';
                    
                    carouselInner.appendChild(div); // Append the new 'div' element to the existing 'carousel-inner'
                });

                for (var i in result) {
                    var POLI_ID = result[i].POLI_ID;

                    if (POLI_ID!=previousPOLI_ID) {
                        datapoliid.push(POLI_ID);
                        containers[POLI_ID] = "";
                    }

                    previousPOLI_ID     = POLI_ID;
                }

                for (var i in result) {
                    var POLI_ID        = result[i].POLI_ID;

                    if (containers[POLI_ID] !== undefined) {

                        if (POLI_ID !== previousPOLI_ID) {
                            containers[POLI_ID] += "<div class='col-md-12 d-flex justify-content-center'>";
                            containers[POLI_ID] += "<h1 class='text-truncate' style='color:#234974; font-weight: bold; font-size:300%;'>" + result[i].NAMAPOLI + "</h1>";
                            containers[POLI_ID] += "</div>";
                        }

                        containers[POLI_ID] += "<div class='col-md-6 d-flex justify-content-center mt-1 mb-1'>";
                        containers[POLI_ID] += "<div class='col-md-12 d-flex align-items-center p-1' style='border-style: solid; border-width: 5px; border-color: #234974; border-radius: 40px;'>";
                            containers[POLI_ID] += "<div class='col-md-2'>";
                                containers[POLI_ID] += "<div class='rounded-circle image-container' style='margin-left: -10px; width: 150px; height: 150px; border-style: solid; border-width: 2px; border-color: #fff; background-color: #fff; overflow: hidden; position: relative;'>";
                                    containers[POLI_ID] += "<img src='"+url+"dist/img/dokter/"+result[i].DOKTER_ID+".png' class='hover-image' style='width: 100%; height: 100%; object-fit: cover;'>";
                                containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "<div class='col-md-10'>";
                                containers[POLI_ID] += "<div class='col-md-12'>";
                                    containers[POLI_ID] += "<h1 class='text-truncate' style='color:#234974; font-weight: bold;'>" + result[i].NAMADOKTER + "</h1>";
                                containers[POLI_ID] += "</div>";

                                containers[POLI_ID] +="<div class='col-md-12 row'>";
                                
                                    containers[POLI_ID] +="<div class='col-md-6'>";
                                        containers[POLI_ID] += "<div class='col-md-12 animate__animated animate__zoomIn' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#234974; border-color: #234974; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                            containers[POLI_ID] += "<h4 style='margin: 0; font-weight: bold; text-align: center;'>TERDAFTAR : "+result[i].TERDAFTARALL +"</h4>";
                                        containers[POLI_ID] += "</div>";
                                    containers[POLI_ID] += "</div>";

                                    if(result[i].SISAALL > 0){
                                        containers[POLI_ID] +="<div class='col-md-6'>";
                                            containers[POLI_ID] += "<div class='col-md-12 animate__animated animate__zoomIn' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#fff; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                containers[POLI_ID] += "<h4 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA : "+result[i].SISAALL +"</h4>";
                                            containers[POLI_ID] += "</div>";
                                        containers[POLI_ID] += "</div>";
                                    }else{
                                        containers[POLI_ID] +="<div class='col-md-6'>";
                                            containers[POLI_ID] += "<div class='col-md-12 animate__animated animate__zoomIn' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                containers[POLI_ID] += "<h4 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA : 0</h4>";
                                            containers[POLI_ID] += "</div>";
                                        containers[POLI_ID] += "</div>";
                                    }

                                containers[POLI_ID] += "</div>";
                                

                            containers[POLI_ID] += "</div>";
                        containers[POLI_ID] += "</div>";
                        containers[POLI_ID] += "</div>";

                        previousPOLI_ID = POLI_ID;
                    }
                }

                for (var containerId in containers) {
                    $("#" + containerId).html(containers[containerId]);
                }
                
                lastupdate();
            }
        }
    });

    return false;
};
