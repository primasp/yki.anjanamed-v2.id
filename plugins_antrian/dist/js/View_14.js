var lastcall  = "";
var call      = "";
// var urlParams = new URLSearchParams(window.location.search);
// var kode      = urlParams.get('param');
var kode      = "IRM";

if('speechSynthesis' in window){
	console.log('Browser mendukung sintesis suara.');
} else {
	console.log('Browser tidak mendukung sintesis suara.');
}


tensipanggil();
tensiberikutnya();

setInterval(tensiberikutnya,3000);
setInterval(tensipanggil,3000);

function tensiberikutnya(){
    $.ajax({
        url       : url+"index.php/View_14/tensiberikutnya",
        data      : {'kode':kode},
        method    : "POST",
        dataType  : "JSON",
        cache     : false,
        // beforeSend: function() {
        //     $("#tensiberikutnya").html("");
        // },
        success:function(data){
            var tensiberikutnya = "";

            if(data.responCode == "00"){
                result = data.responResult;
                for (var i in result ){  
					tensiberikutnya += "<div class='col-md-6 animate__animated animate__zoomIn'>";
					tensiberikutnya += "<div class='card'>";
					if(result[i].PANGGIL==="T"){
						tensiberikutnya += "<div class='card-body bg-secondary'>";
					}else{
						tensiberikutnya += "<div class='card-body bg-warning'>";
					}
					tensiberikutnya += "<div class='row'>";
					tensiberikutnya += "<div class='col-md-12'>";
					tensiberikutnya += "<div class='row'>";
					tensiberikutnya += "<div class='col-md-9 d-flex justify-content-start'>";
					tensiberikutnya += "<h3 class='text-truncate'>"+result[i].NAMAPASIEN+"</h3>";
					tensiberikutnya += "</div>";
					tensiberikutnya += "<div class='col-md-3 d-flex justify-content-end'>";
					tensiberikutnya += "<h3>"+result[i].URUT+"</h3>";
					tensiberikutnya += "</div>";
					tensiberikutnya += "</div>";
					tensiberikutnya += "<div class='row'>";
					if(result[i].PANGGIL==="T"){
						tensiberikutnya += "<div class='col-md-12'>";
						tensiberikutnya += "<h4 class='text-truncate'>"+result[i].PROVIDER+"</h4>";
						tensiberikutnya += "</div>";
					}else{
						tensiberikutnya += "<div class='col-md-4'>";
						tensiberikutnya += "<h4 class='text-truncate'>"+result[i].PROVIDER+"</h4>";
						tensiberikutnya += "</div>";
						tensiberikutnya += "<div class='col-md-8 d-flex justify-content-end'>";
						tensiberikutnya += "<h4 class='text-truncate'>SUDAH PERNAH DI PANGGIL</h4>";
						tensiberikutnya += "</div>";
					}
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "<hr class='mt-2 mb-2' style='border-top: 2px solid'>";
                    tensiberikutnya += "<div class='row'>";
                    tensiberikutnya += "<div class='col-md-12'>";
                    tensiberikutnya += "<h3 class='text-truncate' style='font-size:14px;'>"+result[i].NAMADOKTER+"</h3>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "<div class='col-md-6 d-flex justify-content-start'>";
                    tensiberikutnya += "<h3 class='text-truncate' style='font-size:14px;'>"+result[i].POLITUJUAN+"</h3>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "<div class='col-md-6 d-flex justify-content-end'>";
                    tensiberikutnya += "<h5 class='text-truncate' style='font-size:14px;'>"+result[i].JAMCHECKKIN+"</h5>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                    tensiberikutnya += "</div>";
                }
            }

            $("#tensiberikutnya").html(tensiberikutnya);
        }
    });
    return false;
};

function tensipanggil(){
	$.ajax({
		url:url+"index.php/View_14/tensipanggil",
		data : {'kode':kode},
		method:"POST",
		dataType:"JSON",
		cache :false,
        // beforeSend: function() {
        //     $("#tensipanggil").html("");
        // },
		success:function(data){
			
			var tensipanggil = "";
			
			if(data.responCode == "00"){
				var result = data.responResult;
				
				var antrian = result[0].URUT+" "+result[0].NAMAPASIEN+" "+result[0].NAMADOKTER+" "+result[0].POLITUJUAN;
				call    = result[0].EPISODE_ID;

				// var utterance = new SpeechSynthesisUtterance();

				// utterance.text   = antrian;
				// utterance.volume = 1;        // 0 hingga 1
				// utterance.rate   = 0.8;        // 0.1 hingga 10
				// utterance.pitch  = 1;        // 0 hingga 2
				// utterance.lang = 'id-ID';

				// window.speechSynthesis.speak(utterance);

				console.log("Call :"+call+" Last Call :"+lastcall);

				if(call===lastcall){
					tensipanggil +="<div class='col-md-12'>";
				}else{
					tensipanggil +="<div class='col-md-12 animate__animated animate__zoomIn'>";
				};

				tensipanggil +="<div class='p-3' style='height:395px; border-style: solid; border-width:5px; border-color: #234974; border-radius: 10px;'>";
				tensipanggil +="<div class='row'>";
				tensipanggil +="<div class='col-md-12 d-flex justify-content-center'>";
				tensipanggil +="<h1 style='background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin: 0; display: inline-block; font-size:340px;'>"+result[0].URUT+"</h1>";
				tensipanggil +="</div>";
				tensipanggil +="<div class='col-md-12 d-flex justify-content-start mt-5'>";
				tensipanggil +="<h1 class='text-truncate' style='background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin: 0; display: inline-block; font-size:100px;'>"+result[0].NAMAPASIEN+"</h1>";
				tensipanggil +="</div>";
				tensipanggil +="</div>";
				tensipanggil +="<hr class='mt-2 mb-2' style='border-top: 5px solid white;'>";
				tensipanggil +="<div class='row'>";
				tensipanggil +="<div class='col-md-12'>";
				tensipanggil +="<h1 class='text-truncate' style='font-size:60px; color:#234974;'>"+result[0].NAMADOKTER+"</h1>";
				tensipanggil +="</div>";
				tensipanggil +="<div class='col-md-12'>";
				tensipanggil +="<h1 class='text-truncate' style='font-size:60px; color:#234974;'>"+result[0].POLITUJUAN+"</h1>";
				tensipanggil +="</div>";
				tensipanggil +="</div>";
				tensipanggil +="</div>";
				tensipanggil +="</div>";
				
				lastcall    = result[0].EPISODE_ID;
				
			}

			$("#tensipanggil").html(tensipanggil);  
		}
	});
	return false;
};