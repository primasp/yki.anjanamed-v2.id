bedakelas();
losinap();
transit();
kamarkosong();
belumvisit();
belumsoap();

setInterval(refreshdata, 60000);
setInterval(tampilkanulang, 120000);
setInterval(pagescroll,200);

function refreshdata(){
    bedakelas();
    losinap();
    transit();
    kamarkosong();
    belumvisit();
    belumsoap();
};

function tampilkanulang(){
    window.open(url+"index.php/view_13", "_self");
};

function pagescroll(){
    if(document.getElementById('displaybedakelas').scrollTop<(document.getElementById('displaybedakelas').scrollHeight-document.getElementById('displaybedakelas').offsetHeight)){-1
        document.getElementById('displaybedakelas').scrollTop=document.getElementById('displaybedakelas').scrollTop+1
        }else {document.getElementById('displaybedakelas').scrollTop=0;}
    
    if(document.getElementById('displaylosinap').scrollTop<(document.getElementById('displaylosinap').scrollHeight-document.getElementById('displaylosinap').offsetHeight)){-1
        document.getElementById('displaylosinap').scrollTop=document.getElementById('displaylosinap').scrollTop+1
        }else {document.getElementById('displaylosinap').scrollTop=0;}
    
    if(document.getElementById('displaytransit').scrollTop<(document.getElementById('displaytransit').scrollHeight-document.getElementById('displaytransit').offsetHeight)){-1
        document.getElementById('displaytransit').scrollTop=document.getElementById('displaytransit').scrollTop+1
        }else {document.getElementById('displaytransit').scrollTop=0;}
    
    if(document.getElementById('kamarkosongscroll').scrollTop<(document.getElementById('kamarkosongscroll').scrollHeight-document.getElementById('kamarkosongscroll').offsetHeight)){-1
        document.getElementById('kamarkosongscroll').scrollTop=document.getElementById('kamarkosongscroll').scrollTop+1
        }else {document.getElementById('kamarkosongscroll').scrollTop=0;}
    
    if(document.getElementById('displaybelumvisit').scrollTop<(document.getElementById('displaybelumvisit').scrollHeight-document.getElementById('displaybelumvisit').offsetHeight)){-1
        document.getElementById('displaybelumvisit').scrollTop=document.getElementById('displaybelumvisit').scrollTop+1
        }else {document.getElementById('displaybelumvisit').scrollTop=0;}
    
    if(document.getElementById('displaybelumsoap').scrollTop<(document.getElementById('displaybelumsoap').scrollHeight-document.getElementById('displaybelumsoap').offsetHeight)){-1
        document.getElementById('displaybelumsoap').scrollTop=document.getElementById('displaybelumsoap').scrollTop+1
        }else {document.getElementById('displaybelumsoap').scrollTop=0;}
};

function bedakelas(){
    $.ajax({
        url:url+"index.php/view_13/bedakelas",
        method:"POST",
        dataType:"JSON",
        data    : {
            },
        success:function(data){
            var headerbedakelas = "";
            var displaybedakelas = "";
            var result  = data.responResult;
            
            headerbedakelas +="<div class='info-box pl-3 pr-3' style='border-radius: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                headerbedakelas +="<h5>KELAS BPJS</h5>";
                headerbedakelas +="<h5 style='text-align: right;'>"+data.responCount+"</h5>";
            headerbedakelas +="</div>";


            if(data.responCode==="00"){
                for(var i in result){
                    displaybedakelas +="<div class='col-md-12'>";
                        displaybedakelas +="<div class='info-box animate__animated animate__zoomIn' style='border-style: solid; border-width: 3px; border-color: #234974; border-radius: 10px;'>";
                            displaybedakelas +="<div class='info-box-content' style='margin-left: -10px;'>";
                            displaybedakelas +="<h6 class='text-truncate' style='font-size:16px;max-width: 250px;'><span class='badge rounded-pill bg-info'>"+result[i].NOMOR+"</span> "+result[i].NAMA+"</h6>";
                                displaybedakelas +="<div class='row d-flex justify-content-around'>";
                                    displaybedakelas +="<div class='col-md-5'>";
                                        displaybedakelas +="<h6 class='text-truncate' style='font-size:10px'>RM : "+result[i].INT_PASIEN_ID+"</h6>";
                                    displaybedakelas +="</div>";  
                                    displaybedakelas +="<div class='col-md-7'>";
                                        displaybedakelas +="<h6 class='text-truncate' style='font-size:10px'>Tgl Masuk : "+result[i].TGL_MASUK+"</h6>";
                                    displaybedakelas +="</div>"; 
                                    displaybedakelas +="<div class='col-md-5'>";
                                        displaybedakelas +="<h6 class='text-truncate' style='font-size:10px'>Hak Kelas : "+result[i].KELAS_SEP+"</h6>";
                                    displaybedakelas +="</div>";  
                                    displaybedakelas +="<div class='col-md-7'>";
                                        displaybedakelas +="<h6 class='text-truncate' style='font-size:10px'>Kelas Rawat : "+result[i].KELAS+"</h6>";
                                    displaybedakelas +="</div>";                                     
                                    displaybedakelas +="<div class='col-md-12'>";
                                        displaybedakelas +="<h6 class='text-truncate' style='font-size:10px;max-width: 250px;'>DPJP : "+result[i].DOKTER+"</h6>";
                                    displaybedakelas +="</div>";
                                    displaybedakelas +="<div class='col-md-12'>";
                                        displaybedakelas +="<h6 class='text-break' style='font-size:10px'>Diagnosa : "+result[i].DIAGNOSA+"</h6>";
                                    displaybedakelas +="</div>";  
                                displaybedakelas +="</div>";
                            displaybedakelas +="</div>";
                        displaybedakelas +="</div>";
                    displaybedakelas +="</div>";
                }
            }

            $("#headerbedakelas").html(headerbedakelas);
            $("#displaybedakelas").html(displaybedakelas);
            
            
        }
    });
    return false;
};

function losinap(){
    $.ajax({
        url:url+"index.php/view_13/losinap",
        method:"POST",
        dataType:"JSON",
        data    : {
            },
        success:function(data){
            var headerlosinap = "";
            var displaylosinap = "";            
            var result  = data.responResult;

            headerlosinap +="<div class='info-box pl-3 pr-3' style='border-radius: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                headerlosinap +="<h5>LOS > 7 HARI</h5>";
                headerlosinap +="<h5 style='text-align: right;'>"+data.responCount+"</h5>";
            headerlosinap +="</div>";
            
            if(data.responCode==="00"){
                for(var i in result){
                    displaylosinap +="<div class='col-md-12'>";
                        displaylosinap +="<div class='info-box animate__animated animate__zoomIn' style='border-style: solid; border-width: 3px; border-color: #234974; border-radius: 10px;'>";
                            displaylosinap +="<div class='info-box-content' style='margin-left: -10px;'>";
                            displaylosinap +="<h6 class='text-truncate' style='font-size:16px;max-width: 250px;'><span class='badge rounded-pill bg-info'>"+result[i].NOMOR+"</span> "+result[i].NAMA+"</h6>";
                                displaylosinap +="<div class='row d-flex justify-content-around'>";
                                    displaylosinap +="<div class='col-md-5'>";
                                        displaylosinap +="<h6 class='text-truncate' style='font-size:10px'>RM : "+result[i].INT_PASIEN_ID+"</h6>";
                                    displaylosinap +="</div>";  
                                    displaylosinap +="<div class='col-md-7'>";
                                        displaylosinap +="<h6 class='text-truncate' style='font-size:10px'>Tgl Masuk : "+result[i].TGL_MASUK+"</h6>";
                                    displaylosinap +="</div>";  
                                    displaylosinap +="<div class='col-md-12'>";
                                        displaylosinap +="<h6 class='text-truncate' style='font-size:10px;max-width: 250px;'>Provider : "+result[i].PROVIDER+"</h6>";
                                    displaylosinap +="</div>";                                   
                                    displaylosinap +="<div class='col-md-12'>";
                                        displaylosinap +="<h6 class='text-truncate' style='font-size:10px;max-width: 250px;'>DPJP : "+result[i].DOKTER+"</h6>";
                                    displaylosinap +="</div>";
                                    displaylosinap +="<div class='col-md-12'>";
                                        displaylosinap +="<h6 class='text-break' style='font-size:10px'>Diagnosa : "+result[i].DIAGNOSA+"</h6>";
                                    displaylosinap +="</div>";  
                                displaylosinap +="</div>";
                            displaylosinap +="</div>";
                        displaylosinap +="</div>";
                    displaylosinap +="</div>";
                }
            }
            
            $("#headerlosinap").html(headerlosinap);
            $("#displaylosinap").html(displaylosinap);
            
            
        }
    });
    return false;
};

function transit(){
    $.ajax({
        url:url+"index.php/view_13/transit",
        method:"POST",
        dataType:"JSON",
        data    : {
            },
        success:function(data){
            var headertransit = "";
            var displaytransit = "";
            var result  = data.responResult;

            headertransit +="<div class='info-box pl-3 pr-3' style='border-radius: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                headertransit +="<h5>RUANG TRANSIT</h5>";
                headertransit +="<h5 style='text-align: right;'>"+data.responCount+"</h5>";
            headertransit +="</div>";
            
            if(data.responCode==="00"){
                for(var i in result){
                    displaytransit +="<div class='col-md-12'>";
                        displaytransit +="<div class='info-box animate__animated animate__zoomIn' style='border-style: solid; border-width: 3px; border-color: #234974; border-radius: 10px;'>";
                            displaytransit +="<div class='info-box-content' style='margin-left: -10px;'>";
                            displaytransit +="<h6 class='text-truncate' style='font-size:16px;max-width: 250px;'><span class='badge rounded-pill bg-info'>"+result[i].NOMOR+"</span> "+result[i].NAMA+"</h6>";
                                displaytransit +="<div class='row d-flex justify-content-around'>";
                                    displaytransit +="<div class='col-md-5'>";
                                        displaytransit +="<h6 class='text-truncate' style='font-size:10px'>RM : "+result[i].INT_PASIEN_ID+"</h6>";
                                    displaytransit +="</div>";  
                                    displaytransit +="<div class='col-md-7'>";
                                        displaytransit +="<h6 class='text-truncate' style='font-size:10px'>Tgl Masuk : "+result[i].TGL_MASUK+"</h6>";
                                    displaytransit +="</div>";  
                                    displaytransit +="<div class='col-md-12'>";
                                        displaytransit +="<h6 class='text-truncate' style='font-size:10px;max-width: 250px;'>Provider : "+result[i].PROVIDER+"</h6>";
                                    displaytransit +="</div>";  
                                    displaytransit +="<div class='col-md-12'>";
                                        displaytransit +="<h6 class='text-truncate' style='font-size:10px;max-width: 250px;'>Hak Kelas : "+(data.responResult.KELAS_SEP === null ? '' : result[i].KELAS_SEP)+"</h6>";
                                    displaytransit +="</div>";                                 
                                    displaytransit +="<div class='col-md-12'>";
                                        displaytransit +="<h6 class='text-truncate' style='font-size:10px;max-width: 250px;'>DPJP : "+result[i].DOKTER+"</h6>";
                                    displaytransit +="</div>";
                                    displaytransit +="<div class='col-md-12'>";
                                        displaytransit +="<h6 class='text-break' style='font-size:10px'>Diagnosa : "+result[i].DIAGNOSA+"</h6>";
                                    displaytransit +="</div>";  
                                displaytransit +="</div>";
                            displaytransit +="</div>";
                        displaytransit +="</div>";
                    displaytransit +="</div>";
                }
            }
            
            $("#headertransit").html(headertransit);
            $("#displaytransit").html(displaytransit);
            
            
        }
    });
    return false;
};

function kamarkosong(){
    $.ajax({
        url:url+"index.php/view_13/kamarkosong",
        method:"POST",
        dataType:"JSON",
        data    : {
            },
        success:function(data){
            var headerkamarkosong = "";
            var displaykamarkosong = "";
            var result  = data.responResult;

            var prevKelas    = "";
            var prevNamaUnit = "";
            
            headerkamarkosong +="<div class='info-box pl-3 pr-3' style='border-radius: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                headerkamarkosong +="<h5>KAMAR KOSONG</h5>";
                headerkamarkosong +="<h5 style='text-align: right;'>"+data.responCount+"</h5>";
            headerkamarkosong +="</div>";

            if(data.responCode==="00"){     
                            
                for(var i in result) {    
                    
                    if (result[i].KELAS !== prevKelas) {                        
                        displaykamarkosong += "<h6 class='text-truncate badge-pill badge-info' style='font-size:16px; text-align: center'>" + result[i].KELAS + "<span style='font-size:11px'> " + "(" + result[i].JUMLAH + ")" + "</span></h6>";
                    }
                            displaykamarkosong += "<div class='row d-flex justify-content-around'>";

                                if (result[i].NAMA_UNIT !== prevNamaUnit) {
                                displaykamarkosong += "<div class='col-md-12'>";
                                    displaykamarkosong += "<h6 class='text-truncate badge-secondary' style='font-size:12px; text-align: center'>" + result[i].NAMA_UNIT + "</h6>";
                                displaykamarkosong += "</div>";
                                }

                                displaykamarkosong += "<div class='col-md-12'>";
                                    displaykamarkosong += "<h6 class='text-truncate' style='font-size:10px'>" + result[i].NAMA_RUANG + "</h6>";
                                displaykamarkosong += "</div>"; 
                            displaykamarkosong += "</div>";


                            prevNamaUnit = result[i].NAMA_UNIT;
                    
                        if (result[i].KELAS !== prevKelas) {
                            displaykamarkosong += "</div>";                                                    
                        }                    

                    prevKelas = result[i].KELAS;

                }      
                
            }
            
            $("#headerkamarkosong").html(headerkamarkosong);
            $("#displaykamarkosong").html(displaykamarkosong);
            
            
        }
    });
    return false;
};


function belumvisit(){
    $.ajax({
        url:url+"index.php/view_13/belumvisit",
        method:"POST",
        dataType:"JSON",
        data    : {
            },
        success:function(data){
            var headerbelumvisit = "";
            var displaybelumvisit = "";
            var prevDokter = "";
            var result  = data.responResult;
            
            headerbelumvisit +="<div class='info-box pl-3 pr-3' style='border-radius: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                headerbelumvisit +="<h5>BELUM VISIT</h5>";
                headerbelumvisit +="<h5 style='text-align: right;'>"+data.responCount+"</h5>";
            headerbelumvisit +="</div>";

            if(data.responCode==="00"){
                for(var i in result){
                    if (result[i].DOKTER_ID !== prevDokter) {
                        displaybelumvisit +="<div class='col-md-12'>";
                        displaybelumvisit +="<span class='badge badge-info text-truncate' style='font-size:16px;max-width: 270px;'>"+result[i].DOKTER+"</span>"
                        displaybelumvisit +="</div>";
                    }

                    displaybelumvisit +="<div class='col-md-12'>";
                        displaybelumvisit +="<div class='info-box animate__animated animate__zoomIn' style='border-style: solid; border-width: 3px; border-color: #234974; border-radius: 10px;'>";
                            displaybelumvisit +="<div class='info-box-content' style='margin-left: -10px;'>";
                            displaybelumvisit +="<h6 class='text-truncate' style='font-size:16px;max-width: 250px;'><span class='badge rounded-pill bg-info'>"+result[i].NOMOR+"</span> "+result[i].NAMA+"</h6>";
                                displaybelumvisit +="<div class='row d-flex justify-content-around'>";
                                    displaybelumvisit +="<div class='col-md-5'>";
                                        displaybelumvisit +="<h6 class='text-truncate' style='font-size:10px'>RM : "+result[i].INT_PASIEN_ID+"</h6>";
                                    displaybelumvisit +="</div>";  
                                    displaybelumvisit +="<div class='col-md-7'>";
                                        displaybelumvisit +="<h6 class='text-truncate' style='font-size:10px'>Tgl Masuk : "+result[i].TGL_MASUK+"</h6>";
                                    displaybelumvisit +="</div>";   
                                    displaybelumvisit +="<div class='col-md-12'>";
                                        displaybelumvisit +="<h6 class='text-truncate' style='font-size:10px'>Ruang : "+result[i].RUANG_ID+"</h6>";
                                    displaybelumvisit +="</div>";  
                                displaybelumvisit +="</div>";
                            displaybelumvisit +="</div>";
                        displaybelumvisit +="</div>";
                    displaybelumvisit +="</div>";

                    prevDokter = result[i].DOKTER_ID;
                }
            }
            
            $("#headerbelumvisit").html(headerbelumvisit);
            $("#displaybelumvisit").html(displaybelumvisit);
            
            
        }
    });
    return false;
};

function belumsoap(){
    $.ajax({
        url:url+"index.php/view_13/belumsoap",
        method:"POST",
        dataType:"JSON",
        data    : {
            },
        success:function(data){
            var headerbelumsoap = "";
            var displaybelumsoap = "";
            var prevDokter = "";
            var result  = data.responResult;
            
            headerbelumsoap +="<div class='info-box pl-3 pr-3' style='border-radius: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff; display: inline-block;'>";
                headerbelumsoap +="<h5>BELUM SOAP</h5>";
                headerbelumsoap +="<h5 style='text-align: right;'>"+data.responCount+"</h5>";
            headerbelumsoap +="</div>";

            if(data.responCode==="00"){
                for(var i in result){
                    if (result[i].DOKTER_ID !== prevDokter) {
                        displaybelumsoap +="<div class='col-md-12'>";
                        displaybelumsoap +="<span class='badge badge-info text-truncate' style='font-size:16px;max-width: 270px;'>"+result[i].DOKTER+"</span>"
                        displaybelumsoap +="</div>";
                    }

                    displaybelumsoap +="<div class='col-md-12'>";
                        displaybelumsoap +="<div class='info-box animate__animated animate__zoomIn' style='border-style: solid; border-width: 3px; border-color: #234974; border-radius: 10px;'>";
                            displaybelumsoap +="<div class='info-box-content' style='margin-left: -10px;'>";
                                displaybelumsoap +="<h6 class='text-truncate' style='font-size:16px;max-width: 250px;'><span class='badge rounded-pill bg-info'>"+result[i].NOMOR+"</span> "+result[i].NAMA+"</h6>";
                                displaybelumsoap +="<div class='row d-flex justify-content-around'>";
                                    displaybelumsoap +="<div class='col-md-5'>";
                                        displaybelumsoap +="<h6 class='text-truncate' style='font-size:10px'>RM : "+result[i].INT_PASIEN_ID+"</h6>";
                                    displaybelumsoap +="</div>";  
                                    displaybelumsoap +="<div class='col-md-7'>";
                                        displaybelumsoap +="<h6 class='text-truncate' style='font-size:10px'>Tgl Masuk : "+result[i].TGL_MASUK+"</h6>";
                                    displaybelumsoap +="</div>";   
                                    displaybelumsoap +="<div class='col-md-12'>";
                                        displaybelumsoap +="<h6 class='text-truncate' style='font-size:10px'>Ruang : "+result[i].RUANG_ID+"</h6>";
                                    displaybelumsoap +="</div>";  
                                displaybelumsoap +="</div>";
                            displaybelumsoap +="</div>";
                        displaybelumsoap +="</div>";
                    displaybelumsoap +="</div>";

                    prevDokter = result[i].DOKTER_ID;
                }
            }
            
            $("#headerbelumsoap").html(headerbelumsoap);
            $("#displaybelumsoap").html(displaybelumsoap);
            
            
        }
    });
    return false;
};

