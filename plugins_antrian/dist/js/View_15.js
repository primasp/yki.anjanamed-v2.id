antriandokter();

setInterval(antriandokter,3000);

function antriandokter(){
    $.ajax({
        url       : url+"index.php/View_15/antriandokter",
        method    : "POST",
        dataType  : "JSON",
        cache     : false,
        success:function(data){
            var antriandokter = "";

            if(data.responCode == "00"){
                result = data.responResult;
                for (var i in result ){  
                    var animationClass = (i % 2 === 0) ? 'animate__backInLeft' : 'animate__backInRight';

					antriandokter +="<div class='col-md-12 ml-5 mr-5 mt-5 row pt-3 animate__animated "+animationClass+"' style='border-style: solid; border-width:5px; border-color: #234974; border-radius: 100px;'>";
                        antriandokter +="<div class='col-md-3 d-flex flex-column justify-content-center  align-items-center' style='width: 300px; height: 170px; overflow: hidden; position: relative; border-radius:100px;'>";
                            // antriandokter +="<i class='" + result[i].ICON + " fa-8x text-center text-white'></i>";
                            antriandokter +="<img src='" + url + "/dist/img/dokter/" + result[i].DOKTER_ID + ".png' class='hover-image' style='width: 100%; height: 100%; object-fit: cover;'>";
                        antriandokter +="</div>";
                        antriandokter +="<div class='col-md-9 pl-5'>";
                            antriandokter +="<h1 class='col-md-12 text-truncate font-weight-bold' style='color:#234974; font-size:60px;'>"+result[i].POLITUJUAN+"</h1>";
                            antriandokter +="<h1 class='col-md-12 text-truncate font-weight-bold' style='color:#234974; font-size:60px;'>"+result[i].NAMADOKTER+"</h1>";
                            antriandokter +="<hr class='mt-2 mb-2' style='border-top: 5px solid #234974;'>";
                            antriandokter +="<h1 class='col-md-12 text-truncate' style='background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin: 0; display: inline-block; font-size:60px;'><strong>"+result[i].MRPASIEN+"</strong></h1>";
                            antriandokter +="<h1 class='col-md-12 text-truncate' style='background: linear-gradient(to right, #4aac90, #234974); -webkit-background-clip: text; color: transparent; margin: 0; display: inline-block; font-size:60px;'><strong>"+result[i].NAMAPASIEN+"</strong></h1>";
                        antriandokter +="</div>";
                    antriandokter +="</div>";
                }
            }

            $("#antriandokter").html(antriandokter);
        }
    });
    return false;
};