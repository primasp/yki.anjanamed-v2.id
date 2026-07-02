
countainer();
loaddata();

setInterval(loaddata,5000);

function loaddata(){
    kapasitasbed();
};

function countainer()
{
    $.ajax({
        url     : url + "index.php/View_3/countainer",
        method  : "POST",
        dataType: "JSON",
        cache   : false,
        success: function(data) {
            if (data.responCode === "00") {

                var datapoliid      = [];
                var previousPOLI_ID = null;
                var result          = data.responResult;
                var carouselInner   = document.getElementById('countainermonitoringpelayanan');

                for (var i in result) {
                    var IDCOUNTEINER = result[i].IDCOUNTEINER;

                    if (IDCOUNTEINER!=previousPOLI_ID) {
                        datapoliid.push(IDCOUNTEINER);
                    }

                    previousPOLI_ID     = IDCOUNTEINER;
                }

                carouselInner.innerHTML = '';
                datapoliid.forEach(function (item, index) {
                    var div = document.createElement('div');
                    div.className = 'carousel-item' + (index === 0 ? ' active' : ''); // Tandai item pertama sebagai aktif
                    div.setAttribute('data-interval', '60000');
                    div.innerHTML = '<div class="row overflow-hidden p-1" id="' + item + '"></div>';
                    
                    carouselInner.appendChild(div); // Append the new 'div' element to the existing 'carousel-inner'
                });
            }
        }
    });

    return false;
};

function kapasitasbed()
{
    $.ajax({
        url     : url + "index.php/View_3/kapasitasbed",
        method  : "POST",
        dataType: "JSON",
        cache   : false,
        success: function (data) {
            var legenda      = "";
            var kapasitasri1 = "";
            var kapasitasri2 = "";
            var kapasitasri3 = "";
            var kapasitasri4 = "";
            var kapasitasri5 = "";

            if (data.responCode == "00") {
                var result = data.responResult;

                for (var i in result) {
                    var cardHtml  = "<div class='col-md-6 d-flex justify-content-center'>";
                            if(result[i].STATUS==="0"&&result[i].CELLID!="DEWAS0000000001"&&result[i].CELLID!="VIP (0000000001"){
                                cardHtml += "<div class='col-md-12 row ml-1 mr-1 mt-2 mb-2' style='border-style: solid; border-width:5px; border-color: #234974; border-radius: 95px 10px 10px 10px;'>";
                            }else{
                                cardHtml += "<div class='col-md-12 row ml-1 mr-1 mt-2 mb-2' style='border-style: solid; border-width:5px; border-color: #234974; border-radius: 95px;'>";
                            }
                            
                            if(result[i].STATUS==="0"){    
                                cardHtml += "<div class='col-md-3'>";       
                            }else{
                                cardHtml += "<div class='col-md-3 d-flex align-items-center'>";
                            }
                                    cardHtml += "<div style='margin-left:-15px; width: 180px; height: 180px; border-radius: 50%; background: linear-gradient(to right, #4aac90, #234974); display: flex; align-items: center; justify-content: center;'>";
                                        cardHtml += "<i class='fa-solid fa-bed fa-4x text-white'></i>";
                                    cardHtml += "</div>";
                                cardHtml += "</div>";

                                cardHtml += "<div class='col-md-9 mt-2'>";

                                    cardHtml += "<div class='col-md-12'>";
                                        cardHtml += "<h1 class='text-truncate' style='color:#234974;'><strong>"+result[i].NAMAUNIT+"</strong></h1>";
                                        cardHtml += "<h3 class='text-truncate' style='color:#234974;'><strong>"+result[i].KETERANGAN+"</strong></h3>";

                                        cardHtml += "<div class='col-md-11'>";
                                            if(result[i].CELLID==="DEWAS0000000001"){
                                                if(result[i].TOT_KLS_1!="0"){
                                                    cardHtml += "<div class='row mt-2 mb-2' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                    cardHtml += "<div class='col-md-3'><h2 style='margin: 0; font-weight: bold;'>KELAS 1</h2></div>";
                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-chimney-medical'></i> " + result[i].TOT_KLS_1 + "</h2></div>";
                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-xmark'></i> " + result[i].ISI_KLS_1 + "</h2></div>";
                                                    if(parseFloat(result[i].KOS_KLS_1)>1){
                                                        cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-success'></i> " + result[i].KOS_KLS_1 + "</h2></div>";
                                                    }else{
                                                        if(parseFloat(result[i].KOS_KLS_1)===1){
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-warning'></i> " + result[i].KOS_KLS_1 + "</h2></div>";
                                                        }else{
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 class='text-danger' style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-danger'></i> FULL</h2></div>";
                                                        }
                                                    }
                                                    cardHtml += "</div>";
                                                }
                                            }else{
                                                if(result[i].CELLID==="VIP (0000000001"){
                                                    if(result[i].TOT_KLS_V!="0"){
                                                        cardHtml +="<div class='row mt-2 mb-2' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                            cardHtml +="<div class='col-md-3'><h2 style='margin: 0; font-weight: bold;'>KELAS V</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-chimney-medical'></i> " + result[i].TOT_KLS_V + "</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-xmark'></i> " + result[i].ISI_KLS_V + "</h2></div>";
                                                            if(parseFloat(result[i].KOS_KLS_V)>1){
                                                                cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-success'></i> " + result[i].KOS_KLS_V + "</h2></div>";
                                                            }else{
                                                                if(parseFloat(result[i].KOS_KLS_V)===1){
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-warning'></i> " + result[i].KOS_KLS_V + "</h2></div>";
                                                                }else{
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 class='text-danger' style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-danger'></i> FULL</h2></div>";
                                                                }
                                                            }
                                                        cardHtml +="</div>";
                                                    }
                                                }else{
                                                    if(result[i].TOT_KLS_1!="0"){
                                                        cardHtml +="<div class='row mt-2 mb-2' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                            cardHtml +="<div class='col-md-3'><h2 style='margin: 0; font-weight: bold;'>KELAS 1</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-chimney-medical'></i> " + result[i].TOT_KLS_1 + "</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-xmark'></i> " + result[i].ISI_KLS_1 + "</h2></div>";
                                                            if(parseFloat(result[i].KOS_KLS_1)>1){
                                                                cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-success'></i> " + result[i].KOS_KLS_1 + "</h2></div>";
                                                            }else{
                                                                if(parseFloat(result[i].KOS_KLS_1)===1){
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-warning'></i> " + result[i].KOS_KLS_1 + "</h2></div>";
                                                                }else{
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 class='text-danger' style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-danger'></i> FULL</h2></div>";
                                                                }
                                                            }
                                                        cardHtml +="</div>";
                                                    }
        
                                                    if(result[i].TOT_KLS_2!="0"){
                                                        cardHtml +="<div class='row mt-2 mb-2' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                            cardHtml +="<div class='col-md-3'><h2 style='margin: 0; font-weight: bold;'>KELAS 2</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-chimney-medical'></i> " + result[i].TOT_KLS_2 + "</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-xmark'></i> " + result[i].ISI_KLS_2 + "</h2></div>";
                                                            if(parseFloat(result[i].KOS_KLS_2)>1){
                                                                cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-success'></i> " + result[i].KOS_KLS_2 + "</h2></div>";
                                                            }else{
                                                                if(parseFloat(result[i].KOS_KLS_2)===1){
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-warning'></i> " + result[i].KOS_KLS_2 + "</h2></div>";
                                                                }else{
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 class='text-danger' style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-danger'></i> FULL</h2></div>";
                                                                }
                                                            }
                                                        cardHtml +="</div>";
                                                    }
        
                                                    if(result[i].TOT_KLS_3!="0"){
                                                        cardHtml +="<div class='row mt-2 mb-2' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                                            cardHtml +="<div class='col-md-3'><h2 style='margin: 0; font-weight: bold;'>KELAS 3</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-chimney-medical'></i> " + result[i].TOT_KLS_3 + "</h2></div>";
                                                            cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-xmark'></i> " + result[i].ISI_KLS_3 + "</h2></div>";
                                                            if(parseFloat(result[i].KOS_KLS_3)>1){
                                                                cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-success'></i> " + result[i].KOS_KLS_3 + "</h2></div>";
                                                            }else{
                                                                if(parseFloat(result[i].KOS_KLS_3)===1){
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-warning'></i> " + result[i].KOS_KLS_3 + "</h2></div>";
                                                                }else{
                                                                    cardHtml += "<div class='col-md-3' style='display: inline-block; display: flex; align-items: center; justify-content: left;'><h2 class='text-danger' style='margin: 0; font-weight: bold; text-align: center;'><i class='fa-solid fa-house-medical-circle-check text-danger'></i> FULL</h2></div>";
                                                                }
                                                            }
                                                        cardHtml +="</div>";
                                                    }
                                                }
                                            }
                                        cardHtml += "</div>"; 
                                    cardHtml += "</div>";
                                cardHtml += "</div>"; 
                            cardHtml += "</div>";    
                        cardHtml += "</div>";

                        if(result[i].STATUS==="0"){
                            if (i < 4) {
                                kapasitasri1 += cardHtml;
                            }else{
                                if (i < 8) {
                                    kapasitasri2 += cardHtml;
                                }else{
                                    kapasitasri3 += cardHtml;
                                }
                            }
                        }else{
                            if (i < 15) {
                                kapasitasri4 += cardHtml;
                            }else{
                                kapasitasri5 += cardHtml;
                            }
                        }

                        
                }
            }

            legenda  += "<div class='col-md-12 d-flex justify-content-center mt-5'>";
                legenda  += "<div class='col-md-6 d-flex justify-content-center'>";
                    
                    legenda  += "<div class='col-md-3 m-3 p-3' style='display: inline-block; border-radius: 50px; border-width: 5px; border-style: solid; border-color: #234974; color: #234974; padding: 2px; display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<div class='col-md-3' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<i class='fa-solid fa-house-chimney-medical fa-2x'></i>";
                        legenda += "</div>";
                        legenda += "<div class='col-md-9' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<h4 style='margin: 0; font-weight: bold;'>KAPASITAS</h4>";
                        legenda += "</div>";
                    legenda += "</div>"; 

                    legenda  += "<div class='col-md-3 m-3 p-3' style='display: inline-block; border-radius: 50px; border-width: 5px; border-style: solid; border-color: #234974; color: #234974; padding: 2px; display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<div class='col-md-3' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<i class='fa-solid fa-house-medical-circle-xmark fa-2x text-danger'></i>";
                        legenda += "</div>";
                        legenda += "<div class='col-md-9' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<h4 style='margin: 0; font-weight: bold;'>TERISI</h4>";
                        legenda += "</div>";
                    legenda += "</div>"; 
                    
                    legenda  += "<div class='col-md-3 m-3 p-3' style='display: inline-block; border-radius: 50px; border-width: 5px; border-style: solid; border-color: #234974; color: #234974; padding: 2px; display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<div class='col-md-3' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<i class='fa-solid fa-house-medical-circle-check fa-2x text-success'></i>";
                        legenda += "</div>";
                        legenda += "<div class='col-md-9' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<h4 style='margin: 0; font-weight: bold;'>TERSEDIA</h4>";
                        legenda += "</div>";
                    legenda += "</div>"; 

                legenda += "</div>"; 
            legenda += "</div>";

            $("#kapasitasri1").html(kapasitasri1+legenda);
            $("#kapasitasri2").html(kapasitasri2+legenda);
            $("#kapasitasri3").html(kapasitasri3+legenda);
            $("#kapasitasri4").html(kapasitasri4+legenda);
            $("#kapasitasri5").html(kapasitasri5+legenda);

            lastupdate();
        }
    });
    return false;
};