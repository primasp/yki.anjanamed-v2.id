sensorinternal();
sensorexternal();
sensordigital();

function todesimal(bilangan)
{
    var	reverse = bilangan.toString().split('').reverse().join(''),
        ribuan 	= reverse.match(/\d{1,3}/g);
        ribuan	= ribuan.join('.').split('').reverse().join('');
    return ribuan;
};

function sensorinternal()
{
    $.ajax({
        url:url+"index.php/SIMRS_View_1/sensorinternal",
        method:"GET",
        dataType:"JSON",
        success:function(data){
            var sensorinternal ="";
            var result = data.data.isens;
            
            for(var i in result){

                if(result[i].status!=1){
                    playNotificationSound();
                }

                sensorinternal +="<div class='col-12 col-sm-6 col-md-4 border'>";
                sensorinternal +="<div style='border-style: solid; border-width:10px; border-color: #234974; border-radius: 110px;'>";
                sensorinternal +="</div>";
                sensorinternal +="</div>";
                  
            }

            $("#sensorinternal").html(sensorinternal);
        }
    });
    return false;
};

function sensorexternal()
{
	$.ajax({
		url:url+"index.php/SIMRS_View_1/sensorexternal",
		method:"GET",
		dataType:"JSON",
		success:function(data){
            var sensorexternal ="";
			var result = data.data.esens;
			
			for(var i in result){
				
                if(result[i].status!=1){
                    playNotificationSound();
                    sendwa(result[i].desc,result[i].val);
                }

                sensorexternal +="<div class='col-12 col-sm-6 col-md-4'>";
                sensorexternal +="<div class='info-box animate__animated animate__zoomIn'>";

                if(result[i].status===1){
                    if(result[i].idx===0||result[i].idx===2){
                        sensorexternal +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-snowflake fa-2xl'></i></span>";
                    }else{
                        if(result[i].idx===1||result[i].idx===3){
                            sensorexternal +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-percent fa-2xl'></i></span>";
                        }else{
                            if(result[i].idx===4||result[i].idx===6){
                                sensorexternal +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-droplet-slash fa-2xl'></i></span>";
                            }else{
                                if(result[i].idx===8){
                                    sensorexternal +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-fire fa-2xl'></i></span>";
                                }else{
                                    sensorexternal +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-wind fa-2xl'></i></span>";
                                }
                            }
                        }
                    }
                }else{
                    if(result[i].idx===0||result[i].idx===2){
                        sensorexternal +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-snowflake fa-spin fa-2xl'></i></span>";
                    }else{
                        if(result[i].idx===1||result[i].idx===3){
                            sensorexternal +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-percent fa-flip fa-2xl' style='--fa-flip-x: 1; --fa-flip-y: 0;'></i></span>";
                        }else{
                            if(result[i].idx===4||result[i].idx===6){
                                sensorexternal +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-droplet-slash fa-bounce fa-2xl'></i></span>";
                            }else{
                                if(result[i].idx===8){
                                    sensorexternal +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-fire fa-beat fa-2xl'></i></span>";
                                }else{
                                    sensorexternal +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-wind fa-flip fa-2xl' style='--fa-animation-duration: 3s;'></i></span>";
                                }
                            }
                        }
                    }
                }
                
                
                sensorexternal +="<div class='info-box-content'>";
                sensorexternal +="<h4 class='info-box-text'>"+result[i].desc+"</h4>";

                if(result[i].status===1){
                    if(result[i].idx===0||result[i].idx===2){
                        sensorexternal +="<h4 class='info-box-number text-success display-5'>"+result[i].val.substr(0,5)+" &degC</h4>";
                    }else{
                        if(result[i].idx===1||result[i].idx===3){
                            sensorexternal +="<h4 class='info-box-number text-success display-5'>"+result[i].val.substr(0,5)+" %</h4>";
                        }else{
                            if(result[i].idx===4||result[i].idx===6){
                                sensorexternal +="<h4 class='info-box-number text-success display-5'>CLEAR</h4>";
                            }else{
                                if(result[i].idx===8){
                                    sensorexternal +="<h4 class='info-box-number text-success display-5'>NORMAL</h4>";
                                }else{
                                    if(result[i].idx===14){
                                        sensorexternal +="<h4 class='info-box-number text-success display-5'>NORMAL</h4>";
                                    }else{
                                        sensorexternal +="<h4 class='info-box-number text-success display-5'>NORMAL</h4>";
                                    }
                                }
                                
                            }
                            
                        }
                        
                    }
                }else{
                    if(result[i].idx===0||result[i].idx===2){
                        sensorexternal +="<h4 class='info-box-number text-danger display-5'>"+result[i].val.substr(0,5)+" &degC</h4>";
                    }else{
                        if(result[i].idx===1||result[i].idx===3){
                            sensorexternal +="<h4 class='info-box-number text-danger display-5'>"+result[i].val.substr(0,5)+" %</h4>";
                        }else{
                            if(result[i].idx===4||result[i].idx===6){
                                sensorexternal +="<h4 class='info-box-number text-danger display-5'>WATER</h4>";
                            }else{
                                if(result[i].idx===8){
                                    sensorexternal +="<h4 class='info-box-number text-danger display-5'>FIRE</h4>";
                                }else{
                                    if(result[i].idx===14){
                                        sensorexternal +="<h4 class='info-box-number text-danger display-5'>FIRE</h4>";
                                    }else{
                                        sensorexternal +="<h4 class='info-box-number text-danger display-5'>FAULT</h4>";
                                    }
                                }
                            }
                        }
                    }
                }
                
                
                sensorexternal +="</div>";
                sensorexternal +="</div>";
                sensorexternal +="</div>";
				
			}

            $("#sensorexternal").html(sensorexternal); 
		}
	});
	return false;
};

function sensordigital()
{
	$.ajax({
		url:url+"index.php/SIMRS_View_1/sensordigital",
		method:"GET",
		dataType:"JSON",
		success:function(data){
            var sensordigital ="";
			var result = data.data.diginp;
			
			for(var i in result){

                if(result[i].status!=1){
                    playNotificationSound();
                    sendwa(result[i].desc,result[i].val);
                }

                sensordigital +="<div class='col-12 col-sm-6 col-md-4'>";
                sensordigital +="<div class='info-box animate__animated animate__zoomIn'>";

                if(result[i].status===1){
                    if(result[i].idx===0||result[i].idx===1){
                        sensordigital +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-fan fa-2xl'></i></span>";
                    }else{
                        sensordigital +="<span class='info-box-icon bg-primary p-5'><i class='fa-solid fa-bolt-lightning fa-2xl'></i></span>";
                    }
                }else{
                    if(result[i].idx===0||result[i].idx===1){
                        sensordigital +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-fan fa-spin fa-2xl'></i></span>";
                    }else{
                        sensordigital +="<span class='info-box-icon bg-danger p-5'><i class='fa-solid fa-bolt-lightning fa-bounce fa-2xl'></i></span>";
                    }
                }

                sensordigital +="<div class='info-box-content'>";
                sensordigital +="<h4 class='info-box-text'>"+result[i].desc+"</h4>";

                if(result[i].status===1){
                    if(result[i].idx===0||result[i].idx===1){
                        sensordigital +="<h4 class='info-box-number text-success display-5'>NORMAL</h4>";
                    }else{
                        sensordigital +="<h4 class='info-box-number text-success display-5'>NORMAL</h4>";
                    }
                }else{
                    if(result[i].idx===0||result[i].idx===1){
                        sensordigital +="<h4 class='info-box-number text-success display-5'>ALERT</h4>";
                    }else{
                        sensordigital +="<h4 class='info-box-number text-success display-5'>ALERT</h4>";
                    }
                }
                
                
                sensordigital +="</div>";
                sensordigital +="</div>";
                sensordigital +="</div>";
				
			}

            $("#sensordigital").html(sensordigital);
		}
	});
	return false;
};