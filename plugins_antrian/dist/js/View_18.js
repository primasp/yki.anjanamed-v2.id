let lastLogTime = Date.now();

rawatjalanselesai();

setInterval(pagescroll, 20);

function pagescroll() {

    let listpaten = document.getElementById('rawatjalanselesai');
    if (listpaten.scrollTop < (listpaten.scrollHeight - listpaten.offsetHeight)) {
        listpaten.scrollTop += 1;
    } else {
        listpaten.scrollTop = 0;
        
        // Cek apakah sudah lebih dari 5 detik sejak log terakhir
        if (Date.now() - lastLogTime >= 5000) {
            rawatjalanselesai();
            lastLogTime = Date.now(); // Reset waktu log terakhir
        }
    }

};


function rawatjalanselesai() {
    console.log('load data');
    $.ajax({
        url: url + "index.php/View_18/rawatjalanselesai",
        method: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            var listpasien = "<div class='row'>";
            var result = data.responResult;
            var nomorakhir = 0;

            if (data.responCode === "00") {
                for (var i in result) {
                    listpasien += "<div class='col-md-4 mb-3'>";                

                        listpasien += "<div class='card' style='background-color: #ffc09f'>";
                            listpasien += "<div class='card-body'>";
                            
                                listpasien += "<div class='d-flex justify-content-between align-items-center mb-2'>";
                                    listpasien += "<div class='text-truncate' style='font-size: 40px;'>";
                                        if(result[i].URUT == null){
                                            listpasien += result[i].MRPASIEN + " | ";
                                        }else{
                                            listpasien += result[i].MRPASIEN + " | " + result[i].URUT;
                                        }                                        
                                    listpasien += "</div>";
                                listpasien += "</div>";


                                listpasien += "<h1 class='text-truncate font-weight-bold' style='font-size: 80px;'>" + result[i].NAMAPASIEN + "</h1>";
                                listpasien += "<hr class='mt-2 mb-2' style='border-top: 2px solid'>";


                                listpasien += "<div class='d-flex justify-content-between align-items-center mb-2'>";
                                    listpasien += "<div class='text-truncate font-weight-bold' style='font-size: 40px;'>";
                                        listpasien += result[i].POLITUJUAN;
                                    listpasien += "</div>";

                                    listpasien += "<div class='text-truncate' style='font-size: 30px;'>";
                                        listpasien += result[i].NOMOR;
                                    listpasien += "</div>";
                                listpasien += "</div>";

                            listpasien += "</div>";
                        listpasien += "</div>";                     

                    listpasien += "</div>";

                    nomorakhir = result[i].NOMOR;
                }
            }

            listpasien += "</div>"; // Close row
            $("#rawatjalanselesai").html(listpasien);
            $("#teksobatselesai").html('OBAT SUDAH SELESAI (' + nomorakhir + ' ANTRIAN)');

        }
    });
    return false;
}
