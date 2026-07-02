loaddata();

setInterval(loaddata,60000);

function loaddata(){
    kategoritenaga();
    resource();
    mappinglocation();
};

function kategoritenaga(){
        $.ajax({
            url:url+"index.php/View_16/kategoritenaga",
            method:"GET",
            dataType:"JSON",
            success:function(data){
                var tinggi = 1150;
                var label  = [];
                var nilai1 = [];
                var nilai2 = [];
    
                var result  = data.responResult;
                
                for(var i in result){
                    label.push(result[i].KATEGORITENAGA); 
                    nilai1.push(result[i].JMLKARYAWAN); 
                    nilai2.push(result[i].MAPPING); 
                }


                if($('#chart1').length) {
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
                            {name: 'JML KARYAWAN',data: nilai1},
                            {name: 'MAPPING SATUSEHAT', data: nilai2}
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
                            offsetY: -5,
                            offsetX: -50,
                            style: {
                                fontSize: '30px',
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
                                    fontSize: '25px',
                                },
                                offsetX: 0,
                                offsetY: 20,
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
                                text: "MAPPING PRACTITIONER",
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

                    new ApexCharts(document.querySelector("#chart1"),options).render();
                };

    
            }
        });
        return false;
};

function resource(){
    $.ajax({
        url:url+"index.php/View_16/resource",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var tinggi = 1150;
            var label  = [];
            var nilai1 = [];

            var result  = data.responResult;
            
            for(var i in result){
                label.push(result[i].TYPERESOURCE); 
                nilai1.push(result[i].JML); 
            }


            if($('#chart2').length) {
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
                        {name: 'RESOURCE',data: nilai1}
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
                        offsetY: -5,
                        offsetX: -50,
                        style: {
                            fontSize: '30px',
                            colors: ['#fff']
                        },
                        textAnchor: 'middle'
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories:label,
                        labels: {
                            show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 500,
                            style: {
                                colors: '#234974',
                                fontFamily: 'Roboto, sans-serif',
                                fontWeight: 800,
                                fontSize: '25px',
                            },
                            offsetX: 0,
                            offsetY: 20,
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
                            text: "RESOURCE",
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

                new ApexCharts(document.querySelector("#chart2"),options).render();
            };


        }
    });
    return false;
};

function mappinglocation(){
    $.ajax({
        url:url+"index.php/View_16/mappinglocation",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var mappingloc  = "";
            var result  = data.responResult;
            
            for(var i in result){
                mappingloc +="<div class='col-md-3'>";
                    mappingloc +="<div class='card'>";
                        mappingloc +="<div class='card-body'>";
                        mappingloc +="<h3 class='text-truncate'>"+result[i].NAME+"</h3>";
                        mappingloc +="<h6 class='text-truncate font-italic'>"+result[i].DESCRIPTION+"</h6>";
                        if(result[i].STATUS==="active"){
                            mappingloc +="<div class='col-md-12'><span class='badge badge-info badge-pill'>"+result[i].TYPEBUILD+"</span><span class='badge badge-success badge-pill'>"+result[i].STATUS+"</span></div>";
                        }else{
                            if(result[i].STATUS==="inactive"){
                                mappingloc +="<div class='col-md-12'><span class='badge badge-info badge-pill'>"+result[i].TYPEBUILD+"</span><span class='badge badge-danger badge-pill'>"+result[i].STATUS+"</span></div>";
                            }else{
                                mappingloc +="<div class='col-md-12'><span class='badge badge-info badge-pill'>"+result[i].TYPEBUILD+"</span><span class='badge badge-warning badge-pill'>"+result[i].STATUS+"</span></div>";
                            }
                            
                        }
                            
                        mappingloc +="</div>";
                    mappingloc +="</div>";
                mappingloc +="</div>";
            }

            $("#mappingloc").html(mappingloc);

        }
    });
    return false;
};