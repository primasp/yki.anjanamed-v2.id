loaddata();

$('#carouselExampleSlidesOnly').on('slid.bs.carousel', function () {
    if ($(this).find('.carousel-inner .carousel-item:last').hasClass('active')) {
        // window.location.reload();
        // loaddata();

        setTimeout(function() {
            // window.location.reload();
            loaddata();
        }, 2000);
    }
});

function loaddata(){
    kunjunganpoli();
    statusPoli();
    durasiperiksadokter();
    summarykunjungan();
    alldiagnosa();
    lakidiagnosa();
    perempuandiagnosa();
};

function kunjunganpoli(){
    $.ajax({
        url:url+"index.php/Raw_Data/kunjunganpoli",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var tinggi = 830;
            var label  = [];
            var nilai1 = [];
            var nilai2 = [];
            var result = data.responResult;
            
            for (var i in result) {
                label.push(result[i].NAMAPOLI);
                nilai1.push(result[i].JML);
                nilai2.push(result[i].LALU);
            }

            if($('#saatini').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'BULAN INI',data: nilai1},
                        {name: 'BULAN LALU', data: nilai2}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "POLIKLINIK SPESIALIS",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#saatini"),options).render();
            }

        }
    });
    return false;
};

function statusPoli(){
    $.ajax({
        url: url + "index.php/Raw_Data/statuspoli",
        method: "POST",
        dataType: "JSON",
        cache: false,
        beforeSend : function () {
            $("#aktif").html("");
            $("#hadir").html("");
            $("#anamnesa").html("");
            $("#dokter").html("");
        },
        success: function(data) {
            if (data.responCode == "00") {
                var result = data.responResult;

                $("#aktif").html(todesimal(result[0].AKTIF));
                $("#hadir").html(todesimal(result[0].HADIR));
                $("#anamnesa").html(todesimal(result[0].ANAMNESA));
                $("#dokter").html(todesimal(result[0].DOKTER));


                // $('.counter').counterUp({
                //     delay: 10,
                //     time: 1000
                // });
            
            }
        }
    });
    return false;
};

function durasiperiksadokter(){
    $.ajax({
        url:url+"index.php/Raw_Data/durasiperiksadokter",
        method:"POST",
        dataType:"JSON",
        cache :false,
        success:function(data){
            var durasiperiksadokter = "";

            if(data.responCode == "00"){
                var result        = data.responResult;

                durasiperiksadokter +="<div class='col-md-12 d-flex justify-content-center'>"
                    durasiperiksadokter +="<div class='align-items-center pl-5 pr-5 pt-2 pb-2' style='display: inline-block; border-radius: 50px; border-width: 10px; background: linear-gradient(to right, #4aac90, #234974); color: #ffff;'>";
                        durasiperiksadokter +="<span class='counter' style='font-size: 200px;'>"+result[0].MENIT+"</span>"+"<span style='font-size: 200px;'> MENIT </span>"+"<span class='counter' style='font-size: 200px;'>"+result[0].DETIK+"</span>"+"<span style='font-size: 200px;'> DETIK </span>";
                    durasiperiksadokter +="</div>";
                durasiperiksadokter +="</div>";
                durasiperiksadokter +="<h1 class='m-5'>Durasi Rata Rata Pemeriksaan Dokter Spesialis</h1>";

            }

            $("#durasiperiksadokter").html(durasiperiksadokter);

            $('.counter').counterUp({
                delay: 10, // Delay between each number change
                time: 2000 // Duration of the animation in milliseconds
            });
        }
    });
    return false;
};

function summarykunjungan(){
    $.ajax({
        url:url+"index.php/Raw_Data/summarykunjungan",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var tinggi = 830;
            var label  = [];
            var nilai1 = [];
            var nilai2 = [];
            var nilai3 = [];
            var nilai4 = [];
            var nilai5 = [];
            var nilai6 = [];

            var nilai11 = [];
            var nilai22 = [];
            var nilai33 = [];
            var nilai44 = [];
            var nilai55 = [];
            var nilai66 = [];

            var result  = data.responResult;
            
            for(var i in result){
                if(result[i].STATUS==="X"){
                    label.push(result[i].BULAN);
                }
                
                if(result[i].STATUS==="Y"){
                    nilai1.push(result[i].RJ); 
                    nilai2.push(result[i].RI); 
                    nilai3.push(result[i].REHAB);
                    nilai4.push(result[i].FISIO);
                    nilai5.push(result[i].IGD);
                    nilai6.push(result[i].MCU);
                }

                if(result[i].STATUS==="X"){
                    nilai11.push(result[i].RJ);
                    nilai22.push(result[i].RI); 
                    nilai33.push(result[i].REHAB);
                    nilai44.push(result[i].FISIO);
                    nilai55.push(result[i].IGD);
                    nilai66.push(result[i].MCU);
                }
            }
            
            if($('#summarykunjanganrj').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'TAHUN INI',data: nilai1},
                        {name: 'TAHUN LALU', data: nilai11}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PERIODE",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#summarykunjanganrj"),options).render();
            };

            if($('#summarykunjanganri').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'TAHUN INI',data: nilai2},
                        {name: 'TAHUN LALU', data: nilai22}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PERIODE",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#summarykunjanganri"),options).render();
            };

            if($('#summarykunjanganrehab').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'TAHUN INI',data: nilai3},
                        {name: 'TAHUN LALU', data: nilai33}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PERIODE",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#summarykunjanganrehab"),options).render();
            };

            if($('#summarykunjanganfisio').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'TAHUN INI',data: nilai4},
                        {name: 'TAHUN LALU', data: nilai44}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PERIODE",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#summarykunjanganfisio"),options).render();
            };

            if($('#summarykunjanganigd').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'TAHUN INI',data: nilai5},
                        {name: 'TAHUN LALU', data: nilai55}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PERIODE",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#summarykunjanganigd"),options).render();
            };

            if($('#summarykunjanganmcu').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'TAHUN INI',data: nilai6},
                        {name: 'TAHUN LALU', data: nilai66}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return todesimal(val)
                        },
                        offsetY: -1,
                        offsetX: -10,
                        style: {
                            fontSize: '20px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 300,
                            maxWidth: 400,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PERIODE",
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 40,
                            height: 40,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#summarykunjanganmcu"),options).render();
            };

        }
    });
    return false;
};

function alldiagnosa(){
    $.ajax({
        url:url+"index.php/Raw_Data/alldiagnosa",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var tinggi = 830;
            var label  = [];
            var nilai1 = [];
            var nilai2 = [];
            var nilai3 = [];

            var result  = data.responResult;
            
            for(var i in result){
                label.push(result[i].DIAGNOSA);
                nilai1.push(result[i].L); 
                nilai2.push(result[i].P); 
                nilai3.push(result[i].PL); 
            }

            
            if($('#diagnosarjall').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'bottom',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'ALL',data: nilai3}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        style: {
                            fontSize: '30px',
                            colors: ['#ed8423']
                        },
                        formatter: function (val, opt) {
                            return opt.w.globals.labels[opt.dataPointIndex] + " :  " + todesimal(Math.abs(Math.round(val)))
                        },
                        offsetX: 0
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: false,
                            align: 'right',
                            minWidth: 100,
                            maxWidth: 100,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "ALL GENDER",
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: false,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 50,
                            height: 50,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#diagnosarjall"),options).render();
            };
        }
    });
    return false;
};

function lakidiagnosa(){
    $.ajax({
        url:url+"index.php/Raw_Data/lakidiagnosa",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var tinggi = 830;
            var label  = [];
            var nilai1 = [];
            var nilai2 = [];
            var nilai3 = [];

            var result  = data.responResult;
            
            for(var i in result){
                label.push(result[i].DIAGNOSA);
                nilai1.push(result[i].L); 
                nilai2.push(result[i].P); 
                nilai3.push(result[i].PL); 
            }

            
            if($('#diagnosarjlaki').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'bottom',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'LAKI-LAKI',data: nilai1}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        style: {
                            fontSize: '30px',
                            colors: ['#ed8423']
                        },
                        formatter: function (val, opt) {
                            return opt.w.globals.labels[opt.dataPointIndex] + ":  " + todesimal(Math.abs(Math.round(val)))
                        },
                        offsetX: 0
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: false,
                            align: 'right',
                            minWidth: 100,
                            maxWidth: 100,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "LAKI-LAKI",
                            style: {
                                color: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: false,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 50,
                            height: 50,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#diagnosarjlaki"),options).render();
            };


        }
    });
    return false;
};

function perempuandiagnosa(){
    $.ajax({
        url:url+"index.php/Raw_Data/perempuandiagnosa",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var tinggi = 830;
            var label  = [];
            var nilai1 = [];
            var nilai2 = [];
            var nilai3 = [];

            var result  = data.responResult;
            
            for(var i in result){
                label.push(result[i].DIAGNOSA);
                nilai1.push(result[i].L); 
                nilai2.push(result[i].P); 
                nilai3.push(result[i].PL); 
            }

            
            if($('#diagnosarjperempuan').length) {
                var options = {
                    chart: {
                        type: 'bar',
                        height: tinggi,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '90%',
                            barHeight: '80%',
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'bottom',
                            },
                        }
                    },
                    colors: ['#234974','#4aac90'],
                    series: [
                        {name: 'PEREMPUAN',data: nilai2}
                    ],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        style: {
                            fontSize: '30px',
                            colors: ['#ed8423']
                        },
                        formatter: function (val, opt) {
                            return opt.w.globals.labels[opt.dataPointIndex] + ":  " + todesimal(Math.abs(Math.round(val)))
                        },
                        offsetX: 0
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                            },
                            offsetX: 0,
                            offsetY: 10,
                            formatter: function (val) {
                                return todesimal(Math.abs(Math.round(val)))
                            }
                        },
                    },
                    yaxis: {
                        labels: {
                            show: false,
                            align: 'right',
                            minWidth: 100,
                            maxWidth: 100,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        },
                        title: {
                            text: "PEREMPUAN",
                            style: {
                                color: '#ed8423',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            }
                        }
                    },
                    legend: {
                        show: false,
                        position: "top",
                        horizontalAlign: 'center',
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                        fontSize: '25px',
                        markers: {
                            width: 50,
                            height: 50,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 50,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: -20,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 50,
                            vertical: 0
                        },
                    }
                };

                new ApexCharts(document.querySelector("#diagnosarjperempuan"),options).render();
            };


        }
    });
    return false;
};