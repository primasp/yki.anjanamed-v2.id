$(document).ready(function() {
    var lastcall      = "";
    var carouselInner = document.getElementById('countainermonitoringpelayanan');
    var baseurl       = window.location.href;
    var segments      = baseurl.split('/');
    var lastSegment   = segments.pop();

    const playButton     = document.getElementById('playButton');
    const displayantrian = document.getElementById('displayantrian');
    const summarykuota = document.getElementById('summarykuota');

    const autoref       = document.getElementById('autoref');
    const opening       = document.getElementById('opening');
    const ending        = document.getElementById('ending');
    const nomor_antrian = document.getElementById('nomor_antrian');
    const loket         = document.getElementById('loket');

    const seribu  = document.getElementById('seribu');
    const belas   = document.getElementById('belas');
    const puluh   = document.getElementById('puluh');
    const seratus = document.getElementById('seratus');
    const ratus   = document.getElementById('ratus');

    const nol      = document.getElementById('0');
    const satu     = document.getElementById('1');
    const dua      = document.getElementById('2');
    const tiga     = document.getElementById('3');
    const empat    = document.getElementById('4');
    const lima     = document.getElementById('5');
    const enam     = document.getElementById('6');
    const tujuh    = document.getElementById('7');
    const delapan  = document.getElementById('8');
    const sembilan = document.getElementById('9');
    const sepuluh  = document.getElementById('10');
    const sebelas  = document.getElementById('11');

    const a = document.getElementById('a');
    const b = document.getElementById('b');
    const c = document.getElementById('c');
    const d = document.getElementById('d');
    const e = document.getElementById('e');
    const f = document.getElementById('f');
    const g = document.getElementById('g');
    const h = document.getElementById('h');
    const i = document.getElementById('i');
    const j = document.getElementById('j');
    const k = document.getElementById('k');
    const l = document.getElementById('l');
    const m = document.getElementById('m');
    const n = document.getElementById('n');
    const o = document.getElementById('o');
    const p = document.getElementById('p');
    const q = document.getElementById('q');
    const r = document.getElementById('r');
    const s = document.getElementById('s');
    const t = document.getElementById('t');
    const u = document.getElementById('u');
    const v = document.getElementById('v');
    const w = document.getElementById('w');
    const x = document.getElementById('x');
    const y = document.getElementById('y');
    const z = document.getElementById('z');

    if(summarykuota){
        setInterval(pageScroll,50);
    }
    
    loaddata();

    if(lastSegment==="View_1"){
        setInterval(loadsimrs,60000);
    }

    if(lastSegment==="View_7"){
        setInterval(loaddokter,5000);
    }

    if(lastSegment==="View_8"){
        setInterval(loadbed,5000);
    }

    if(lastSegment==="View_9"){
        setInterval(loadbed,300000);
        setInterval(loaddokter,5000);
    }

    if(lastSegment==="View_10"||lastSegment==="View_11"){
        setInterval(loadantrian,10000);
    }

    if(playButton){
        playButton.addEventListener('click', function()
        {
    
            console.log("Mulai Play");
    
            opening.play();
            ending.play();
            nomor_antrian.play();
            loket.play();
    
            a.play();
            b.play();
            c.play();
            d.play();
            e.play();
            f.play();
            g.play();
            h.play();
            i.play();
            j.play();
            k.play();
            l.play();
            m.play();
            n.play();
            o.play();
            p.play();
            q.play();
            r.play();
            s.play();
            t.play();
            u.play();
            v.play();
            w.play();
            x.play();
            y.play();
            z.play();
    
            belas.play();
            puluh.play();
            seratus.play();
    
            nol.play();
            satu.play();
            dua.play();
            tiga.play();
            empat.play();
            lima.play();
            enam.play();
            tujuh.play();
            delapan.play();
            sembilan.play();
            sepuluh.play();
            sebelas.play();
    
            console.log("Selesai Play");
    
            playButton.classList.add('d-none'); 
            displayantrian.classList.remove('d-none'); 
        });
    }

    function todesimal(bilangan)
    {
        var	reverse = bilangan.toString().split('').reverse().join(''),
            ribuan 	= reverse.match(/\d{1,3}/g);
            ribuan	= ribuan.join('.').split('').reverse().join('');
        return ribuan;
    };

    function pageScroll(){
        if(document.getElementById('summarykuota').scrollTop<(document.getElementById('summarykuota').scrollHeight-document.getElementById('summarykuota').offsetHeight)){-1
        document.getElementById('summarykuota').scrollTop=document.getElementById('summarykuota').scrollTop+1
        }else {document.getElementById('summarykuota').scrollTop=0;}
    }

    function loadbed()
    {
        kapasitasbed();
        lastupdate();
    };

    function loaddokter()
    {
        jadwaldokter();
        lastupdate();
    };

    function loadantrian()
    {
        clear();
        antrianselanjutnya();
        antriansaatini();
        lastupdate();
    };
    
    function loaddata()
    {
        clear();

        if(carouselInner){
            countainer();
        }

        if(lastSegment==="View_1"){
            loadsimrs();
        }
        
        if(lastSegment==="View_8"){
            kapasitasbed();
        }

        if(lastSegment==="View_7"){
            jadwaldokter();
        }

        if(lastSegment==="View_9"){
            kapasitasbed();
            jadwaldokter();
        }

        if(lastSegment==="View_10"||lastSegment==="View_11"){
            loadantrian();
        }

        lastupdate();
    };

    function loadsimrs()
    {
        clear();
        summarykunjungan();
        alldiagnosa();
        lakidiagnosa();
        perempuandiagnosa();
        kapasitasbedv2();
        durasiperiksadokter();
        statusPoli();
        kunjunganpoli();
    };
    
    function clear()
    {
        sessionStorage.clear();
        localStorage.clear();
        $("#countainermonitoringpelayanan").html("");
        $("#liatantrian").html("");

        $("#antriansaatini").html("");
        $("#loketsaatini").html("");

        $("#saatiniloket1").html("STAND BY");
        $("#saatiniloket2").html("STAND BY");
        $("#saatiniloket3").html("STAND BY");
        $("#saatiniloket4").html("STAND BY");
    };

    function playsound($var)
    {
        var soundAntrianArray = $var.split("|");
        var jumlahArray       = soundAntrianArray.length;

        opening.play();
        setTimeout(function () {nomor_antrian.play();}, 2000);

        if (soundAntrianArray[2] === "A") {
            setTimeout(function () {
                a.play();
            }, 3000);
        }

        if (soundAntrianArray[2] === "B") {
            setTimeout(function () {
                b.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "C") {
            setTimeout(function () {
                c.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "D") {
            setTimeout(function () {
                d.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "E") {
            setTimeout(function () {
                e.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "F") {
            setTimeout(function () {
                f.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "G") {
            setTimeout(function () {
                g.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "H") {
            setTimeout(function () {
                h.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "I") {
            setTimeout(function () {
                i.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "J") {
            setTimeout(function () {
                j.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "K") {
            setTimeout(function () {
                k.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "L") {
            setTimeout(function () {
                l.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "M") {
            setTimeout(function () {
                m.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "N") {
            setTimeout(function () {
                n.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "O") {
            setTimeout(function () {
                o.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "P") {
            setTimeout(function () {
                p.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "Q") {
            setTimeout(function () {
                q.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "R") {
            setTimeout(function () {
                r.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "S") {
            setTimeout(function () {
                s.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "T") {
            setTimeout(function () {
                t.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "U") {
            setTimeout(function () {
                u.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "V") {
            setTimeout(function () {
                v.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "W") {
            setTimeout(function () {
                w.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "X") {
            setTimeout(function () {
                x.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "Y") {
            setTimeout(function () {
                y.play();
            }, 3000);
        }
        
        if (soundAntrianArray[2] === "Z") {
            setTimeout(function () {
                z.play();
            }, 3000);
        }

        
        if(jumlahArray===5)
        {
            if(soundAntrianArray[3]!="seratus"){
                if (soundAntrianArray[3] === "1") {
                    setTimeout(function () {
                        satu.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "2") {
                    setTimeout(function () {
                        dua.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "3") {
                    setTimeout(function () {
                        tiga.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "4") {
                    setTimeout(function () {
                        empat.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "5") {
                    setTimeout(function () {
                        lima.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "6") {
                    setTimeout(function () {
                        enam.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "7") {
                    setTimeout(function () {
                        tujuh.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "8") {
                    setTimeout(function () {
                        delapan.play();
                    }, 4000);
                }
                
                if (soundAntrianArray[3] === "9") {
                    setTimeout(function () {
                        sembilan.play();
                    }, 4000);
                }
    
                if (soundAntrianArray[3] === "10") {
                    setTimeout(function () {
                        sepuluh.play();
                    }, 4000);
                }
        
                if (soundAntrianArray[3] === "11") {
                    setTimeout(function () {
                        sebelas.play();
                    }, 4000);
                }

                setTimeout(function () {ending.play();}, 7000);
            }else{
                if (soundAntrianArray[3] === "seratus") {
                    setTimeout(function () {
                        seratus.play();
                    }, 4000);
                }

                setTimeout(function () {ending.play();}, 5000);
            }
            
        }

        if(jumlahArray===6)
        {
            if(soundAntrianArray[3]!="seratus"){
                if(soundAntrianArray[4]!="ratus"){
                    if (soundAntrianArray[3] === "1") {
                        setTimeout(function () {
                            satu.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "2") {
                        setTimeout(function () {
                            dua.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "3") {
                        setTimeout(function () {
                            tiga.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "4") {
                        setTimeout(function () {
                            empat.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "5") {
                        setTimeout(function () {
                            lima.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "6") {
                        setTimeout(function () {
                            enam.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "7") {
                        setTimeout(function () {
                            tujuh.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "8") {
                        setTimeout(function () {
                            delapan.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "9") {
                        setTimeout(function () {
                            sembilan.play();
                        }, 4000);
                    }
        
                    if (soundAntrianArray[4] === "belas") {
                        setTimeout(function () {
                            belas.play();
                        }, 5000);
                    }
        
                    if (soundAntrianArray[4] === "puluh") {
                        setTimeout(function () {
                            puluh.play();
                        }, 5000);
                    }
        
                    setTimeout(function () {ending.play();}, 6000);
                }else{
                    if (soundAntrianArray[3] === "1") {
                        setTimeout(function () {
                            satu.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "2") {
                        setTimeout(function () {
                            dua.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "3") {
                        setTimeout(function () {
                            tiga.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "4") {
                        setTimeout(function () {
                            empat.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "5") {
                        setTimeout(function () {
                            lima.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "6") {
                        setTimeout(function () {
                            enam.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "7") {
                        setTimeout(function () {
                            tujuh.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "8") {
                        setTimeout(function () {
                            delapan.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "9") {
                        setTimeout(function () {
                            sembilan.play();
                        }, 4000);
                    }

                    if (soundAntrianArray[4] === "ratus") {
                        setTimeout(function () {
                            ratus.play();
                        }, 5000);
                    }
                }
                
            }else{
                if (soundAntrianArray[3] === "seratus") {
                    setTimeout(function () {
                        seratus.play();
                    }, 4000);
                }

                if (soundAntrianArray[4] === "1") {
                    setTimeout(function () {
                        satu.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "2") {
                    setTimeout(function () {
                        dua.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "3") {
                    setTimeout(function () {
                        tiga.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "4") {
                    setTimeout(function () {
                        empat.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "5") {
                    setTimeout(function () {
                        lima.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "6") {
                    setTimeout(function () {
                        enam.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "7") {
                    setTimeout(function () {
                        tujuh.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "8") {
                    setTimeout(function () {
                        delapan.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "9") {
                    setTimeout(function () {
                        sembilan.play();
                    }, 5000);
                }

                if (soundAntrianArray[4] === "10") {
                    setTimeout(function () {
                        sepuluh.play();
                    }, 5000);
                }
        
                if (soundAntrianArray[4] === "11") {
                    setTimeout(function () {
                        sebelas.play();
                    }, 5000);
                }

                setTimeout(function () {ending.play();}, 6000);
                
            }
        }

        if(jumlahArray===7)
        {
            if (soundAntrianArray[3] != "seratus") {
                if (soundAntrianArray[4] != "ratus") {
                    if (soundAntrianArray[3] === "1") {
                        setTimeout(function () {
                            satu.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "2") {
                        setTimeout(function () {
                            dua.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "3") {
                        setTimeout(function () {
                            tiga.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "4") {
                        setTimeout(function () {
                            empat.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "5") {
                        setTimeout(function () {
                            lima.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "6") {
                        setTimeout(function () {
                            enam.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "7") {
                        setTimeout(function () {
                            tujuh.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "8") {
                        setTimeout(function () {
                            delapan.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "9") {
                        setTimeout(function () {
                            sembilan.play();
                        }, 4000);
                    }
        
        
                    if (soundAntrianArray[4] === "puluh") {
                        setTimeout(function () {
                            puluh.play();
                        }, 5000);
                    }
        
                    if (soundAntrianArray[5] === "1") {
                        setTimeout(function () {
                            satu.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "2") {
                        setTimeout(function () {
                            dua.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "3") {
                        setTimeout(function () {
                            tiga.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "4") {
                        setTimeout(function () {
                            empat.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "5") {
                        setTimeout(function () {
                            lima.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "6") {
                        setTimeout(function () {
                            enam.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "7") {
                        setTimeout(function () {
                            tujuh.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "8") {
                        setTimeout(function () {
                            delapan.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "9") {
                        setTimeout(function () {
                            sembilan.play();
                        }, 6000);
                    }
        
                    setTimeout(function () {ending.play();}, 7000);
                }else{
                    if (soundAntrianArray[3] === "1") {
                        setTimeout(function () {
                            satu.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "2") {
                        setTimeout(function () {
                            dua.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "3") {
                        setTimeout(function () {
                            tiga.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "4") {
                        setTimeout(function () {
                            empat.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "5") {
                        setTimeout(function () {
                            lima.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "6") {
                        setTimeout(function () {
                            enam.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "7") {
                        setTimeout(function () {
                            tujuh.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "8") {
                        setTimeout(function () {
                            delapan.play();
                        }, 4000);
                    }
                    
                    if (soundAntrianArray[3] === "9") {
                        setTimeout(function () {
                            sembilan.play();
                        }, 4000);
                    }

                    if (soundAntrianArray[4] === "ratus") {
                        setTimeout(function () {
                            ratus.play();
                        }, 5000);
                    }

                    if (soundAntrianArray[5] === "1") {
                        setTimeout(function () {
                            satu.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "2") {
                        setTimeout(function () {
                            dua.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "3") {
                        setTimeout(function () {
                            tiga.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "4") {
                        setTimeout(function () {
                            empat.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "5") {
                        setTimeout(function () {
                            lima.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "6") {
                        setTimeout(function () {
                            enam.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "7") {
                        setTimeout(function () {
                            tujuh.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "8") {
                        setTimeout(function () {
                            delapan.play();
                        }, 6000);
                    }
                    
                    if (soundAntrianArray[5] === "9") {
                        setTimeout(function () {
                            sembilan.play();
                        }, 6000);
                    }

                    if (soundAntrianArray[5] === "10") {
                        setTimeout(function () {
                            sepuluh.play();
                        }, 6000);
                    }

                    if (soundAntrianArray[5] === "11") {
                        setTimeout(function () {
                            sebelas.play();
                        }, 6000);
                    }

                    setTimeout(function () {ending.play();}, 7000);
                }
                
            }else{
                if (soundAntrianArray[3] === "seratus") {
                    setTimeout(function () {
                        seratus.play();
                    }, 4000);
                }

                if (soundAntrianArray[4] === "1") {
                    setTimeout(function () {
                        satu.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "2") {
                    setTimeout(function () {
                        dua.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "3") {
                    setTimeout(function () {
                        tiga.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "4") {
                    setTimeout(function () {
                        empat.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "5") {
                    setTimeout(function () {
                        lima.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "6") {
                    setTimeout(function () {
                        enam.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "7") {
                    setTimeout(function () {
                        tujuh.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "8") {
                    setTimeout(function () {
                        delapan.play();
                    }, 5000);
                }
                
                if (soundAntrianArray[4] === "9") {
                    setTimeout(function () {
                        sembilan.play();
                    }, 5000);
                }

                if (soundAntrianArray[5] === "belas") {
                    setTimeout(function () {
                        belas.play();
                    }, 6000);
                }

                if (soundAntrianArray[5] === "puluh") {
                    setTimeout(function () {
                        puluh.play();
                    }, 6000);
                }

                setTimeout(function () {ending.play();}, 7000);
            }
            
        }

        if(jumlahArray===8)
        {
            if (soundAntrianArray[3] === "1") {
                setTimeout(function () {
                    satu.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "2") {
                setTimeout(function () {
                    dua.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "3") {
                setTimeout(function () {
                    tiga.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "4") {
                setTimeout(function () {
                    empat.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "5") {
                setTimeout(function () {
                    lima.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "6") {
                setTimeout(function () {
                    enam.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "7") {
                setTimeout(function () {
                    tujuh.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "8") {
                setTimeout(function () {
                    delapan.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "9") {
                setTimeout(function () {
                    sembilan.play();
                }, 4000);
            }

            if (soundAntrianArray[4] === "ratus") {
                setTimeout(function () {
                    ratus.play();
                }, 5000);
            }
            

            if (soundAntrianArray[5] === "1") {
                setTimeout(function () {
                    satu.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "2") {
                setTimeout(function () {
                    dua.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "3") {
                setTimeout(function () {
                    tiga.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "4") {
                setTimeout(function () {
                    empat.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "5") {
                setTimeout(function () {
                    lima.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "6") {
                setTimeout(function () {
                    enam.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "7") {
                setTimeout(function () {
                    tujuh.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "8") {
                setTimeout(function () {
                    delapan.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "9") {
                setTimeout(function () {
                    sembilan.play();
                }, 6000);
            }

            if (soundAntrianArray[6] === "puluh") {
                setTimeout(function () {
                    puluh.play();
                }, 7000);
            }


            setTimeout(function () {ending.play();}, 8000);
        }

        if(jumlahArray===9)
        {
            if (soundAntrianArray[3] === "1") {
                setTimeout(function () {
                    satu.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "2") {
                setTimeout(function () {
                    dua.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "3") {
                setTimeout(function () {
                    tiga.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "4") {
                setTimeout(function () {
                    empat.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "5") {
                setTimeout(function () {
                    lima.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "6") {
                setTimeout(function () {
                    enam.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "7") {
                setTimeout(function () {
                    tujuh.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "8") {
                setTimeout(function () {
                    delapan.play();
                }, 4000);
            }
            
            if (soundAntrianArray[3] === "9") {
                setTimeout(function () {
                    sembilan.play();
                }, 4000);
            }

            if (soundAntrianArray[4] === "ratus") {
                setTimeout(function () {
                    ratus.play();
                }, 5000);
            }
            

            if (soundAntrianArray[5] === "1") {
                setTimeout(function () {
                    satu.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "2") {
                setTimeout(function () {
                    dua.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "3") {
                setTimeout(function () {
                    tiga.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "4") {
                setTimeout(function () {
                    empat.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "5") {
                setTimeout(function () {
                    lima.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "6") {
                setTimeout(function () {
                    enam.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "7") {
                setTimeout(function () {
                    tujuh.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "8") {
                setTimeout(function () {
                    delapan.play();
                }, 6000);
            }
            
            if (soundAntrianArray[5] === "9") {
                setTimeout(function () {
                    sembilan.play();
                }, 6000);
            }

            if (soundAntrianArray[6] === "puluh") {
                setTimeout(function () {
                    puluh.play();
                }, 7000);
            }

            if (soundAntrianArray[7] === "1") {
                setTimeout(function () {
                    satu.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "2") {
                setTimeout(function () {
                    dua.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "3") {
                setTimeout(function () {
                    tiga.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "4") {
                setTimeout(function () {
                    empat.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "5") {
                setTimeout(function () {
                    lima.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "6") {
                setTimeout(function () {
                    enam.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "7") {
                setTimeout(function () {
                    tujuh.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "8") {
                setTimeout(function () {
                    delapan.play();
                }, 8000);
            }
            
            if (soundAntrianArray[7] === "9") {
                setTimeout(function () {
                    sembilan.play();
                }, 8000);
            }


            setTimeout(function () {ending.play();}, 9000);
        }
    };

    function countainer()
    {
        var parameterValue = $('#parameterdisplay').val();
        $.ajax({
            url: url + "index.php/Raw_Data/countainer?parameterdisplay="+parameterValue,
            method: "POST",
            dataType: "JSON",
            cache: false,
            success: function(data) {
                if (data.responCode === "00") {
                    var datapoliid      = [];
                    var result          = data.responResult;
                    var previousPOLI_ID = null;

                    for (var i in result) {
                        var IDCOUNTEINER = result[i].IDCOUNTEINER;

                        if (IDCOUNTEINER!=previousPOLI_ID) {
                            datapoliid.push(IDCOUNTEINER);
                        }

                        previousPOLI_ID     = IDCOUNTEINER;
                    }

                    var carouselInner = document.getElementById('countainermonitoringpelayanan');
                    carouselInner.innerHTML = '';
                    datapoliid.forEach(function (item, index) {
                        var div = document.createElement('div');
                        div.className = 'carousel-item' + (index === 0 ? ' active' : ''); // Tandai item pertama sebagai aktif
                        
                        if(item==="summarykuota"){
                            div.setAttribute('data-interval', '60000');
                        }else{
                            div.setAttribute('data-interval', '10000');
                        }

                        if(item==="summarykuota"){
                            div.innerHTML = '<div class="row overflow-hidden" id="' + item + '" style="height: 380px;""></div>';
                        }else{
                            div.innerHTML = '<div class="row overflow-hidden" id="' + item + '"></div>';
                        }
                        
                        carouselInner.appendChild(div); // Append the new 'div' element to the existing 'carousel-inner'
                    });
                }
            }
        });
    
        return false;
    };

    function summarykunjungan()
    {
        $.ajax({
            url:url+"index.php/Raw_Data/summarykunjungan",
            method:"GET",
            dataType:"JSON",
            success:function(data){
                var tinggi = 1150;
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
                    if(result[i].STATUS==="Y"){
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
                                text: "RAWAT JALAN",
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
                                text: "RAWAT INAP",
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
                                text: "REHABMEDIK",
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
                                text: "FISIOTERAPI",
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
                                text: "IGD",
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
                                text: "MEDICAL CHECKUP",
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

                    new ApexCharts(document.querySelector("#summarykunjanganmcu"),options).render();
                };

    
            }
        });
        return false;
    };

    function alldiagnosa()
    {
        $.ajax({
            url:url+"index.php/Raw_Data/alldiagnosa",
            method:"GET",
            dataType:"JSON",
            success:function(data){
                var tinggi = 1150;
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

                    new ApexCharts(document.querySelector("#diagnosarjall"),options).render();
                };

    
            }
        });
        return false;
    };

    function lakidiagnosa()
    {
        $.ajax({
            url:url+"index.php/Raw_Data/lakidiagnosa",
            method:"GET",
            dataType:"JSON",
            success:function(data){
                var tinggi = 1150;
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

                    new ApexCharts(document.querySelector("#diagnosarjlaki"),options).render();
                };

    
            }
        });
        return false;
    };

    function perempuandiagnosa()
    {
        $.ajax({
            url:url+"index.php/Raw_Data/perempuandiagnosa",
            method:"GET",
            dataType:"JSON",
            success:function(data){
                var tinggi = 1150;
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

    function kunjunganpoli(){
        $.ajax({
            url:url+"index.php/Raw_Data/kunjunganpoli",
            method:"GET",
            dataType:"JSON",
            success:function(data){
                var tinggi = 1150;
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

                    new ApexCharts(document.querySelector("#saatini"),options).render();
                }
    
            }
        });
        return false;
    };

    function statusPoli()
    {
        $.ajax({
            url: url + "index.php/Raw_Data/statuspoli",
            method: "POST",
            dataType: "JSON",
            cache: false,
            success: function(data) {
                if (data.responCode == "00") {
                    var result = data.responResult;

                    $("#aktif").html(result[0].AKTIF);
                    $("#hadir").html(result[0].HADIR);
                    $("#anamnesa").html(result[0].ANAMNESA);
                    $("#dokter").html(result[0].DOKTER);


                    // $('.counter').counterUp({
                    //     delay: 10, // Delay between each number change
                    //     time: 1000 // Duration of the animation in milliseconds
                    // });
                
                }
            }
        });
        return false;
    };

    function durasiperiksadokter()
    {
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

    function jadwaldokter()
    {
        $.ajax({
            url: url + "index.php/Raw_Data/jadwaldokter",
            method: "POST",
            dataType: "JSON",
            cache: false,
            success: function(data) {
                if (data.responCode === "00") {
                    var summarykuota    = "";
                    var datapoliid      = [];
                    var containers      = {};
                    var result          = data.responResult;
                    var previousPOLI_ID = null;

                    for (var i in result) {
                        var POLI_ID = result[i].POLI_ID;

                        if (POLI_ID!=previousPOLI_ID) {
                            datapoliid.push(POLI_ID);
                            containers[POLI_ID] = "";
                        }

                        previousPOLI_ID     = POLI_ID;
                    }

                    summarykuota  = "<div class='col-md-12 d-flex justify-content-center'>";
                    summarykuota += "<h1 class='text-truncate' style='color:#234974; font-weight: bold; font-size:500%;'>SUMMARY</h1>";
                    summarykuota += "</div>";

                    for (var i in result) {
                        var POLI_ID        = result[i].POLI_ID;

                        if (containers[POLI_ID] !== undefined) {

                            if (POLI_ID !== previousPOLI_ID) {
                                containers[POLI_ID] += "<div class='col-md-12 d-flex justify-content-center'>";
                                containers[POLI_ID] += "<h1 class='text-truncate' style='color:#234974; font-weight: bold; font-size:500%;'>" + result[i].NAMAPOLI + "</h1>";
                                containers[POLI_ID] += "</div>";
                            }

                            containers[POLI_ID] += "<div class='col-md-6 d-flex justify-content-center'>";
                            containers[POLI_ID] += "<div class='col-md-12 row ml-1 mr-1 mt-2 mb-2' style='border-style: solid; border-width: 2px; border-color: #234974; border-radius: 40px 10px 10px 10px;'>";
                            containers[POLI_ID] += "<div class='col-md-2'>";
                            containers[POLI_ID] += "<div class='rounded-circle image-container' style='margin-left: -17px; width: 75px; height: 75px; border-style: solid; border-width: 2px; border-color: #234974; background-color: #fff; overflow: hidden; position: relative;'>";
                            containers[POLI_ID] += "<img src='"+url+"dist/img/dokter/"+result[i].DOKTER_ID+".png' class='hover-image' style='width: 100%; height: 100%; object-fit: cover;'>";
                            containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "<div class='col-md-10 p-2'>";
                            containers[POLI_ID] += "<div class='col-md-12 p-1'>";
                            containers[POLI_ID] += "<h1 class='text-truncate' style='color:#234974; font-weight: bold;'>" + result[i].NAMADOKTER + "</h1>";
                            containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "<div class='row col-md-12'>";
                                containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#234974; border-color: #234974; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERDAFTAR : "+result[i].TERDAFTARALL +"</h3>";
                                containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "<div class='row col-md-12'>";
                                if(result[i].STATUS === "0"){
                                    containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                    containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ONLINE : 0</h3>";
                                    containers[POLI_ID] += "</div>";

                                    containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                    containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ON THE SPOT : 0</h3>";
                                    containers[POLI_ID] += "</div>";
                                }else{

                                    if(parseFloat(result[i].SISAOL) < 0){
                                        containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                        containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ONLINE  : 0</h3>";
                                    }else{
                                        if(parseFloat(result[i].SISAOL) === 0){
                                            containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                            containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ONLINE  : 0</h3>";
                                        }else{
                                            containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                            containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ONLINE  : "+result[i].SISAOL +"</h3>";
                                        }
                                    }
                                    containers[POLI_ID] += "</div>";
                                    
                                    if(parseFloat(result[i].SISAOTS) < 0){
                                        containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                        containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ON THE SPOT  : FULL</h3>";
                                    }else{
                                        if(parseFloat(result[i].SISAOTS) === 0){
                                            containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                            containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ON THE SPOT  : FULL</h3>";
                                        }else{
                                            containers[POLI_ID] += "<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                                            containers[POLI_ID] += "<h3 style='margin: 0; font-weight: bold; text-align: center;'>TERSEDIA ON THE SPOT : "+result[i].SISAOTS +"</h3>";
                                        }
                                    }
                                    containers[POLI_ID] += "</div>";
                                }
                            containers[POLI_ID] += "</div>";

                            containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";
                            containers[POLI_ID] += "</div>";

                            previousPOLI_ID = POLI_ID;
                        }

                        

                        if(result[i].STATUS==="0"){
                            summarykuota +="<div class='col-md-4 d-flex justify-content-center'>";
                            summarykuota +="<div class='col-md-12 row ml-1 mr-1 mt-2 mb-2' style='border-style: solid; border-width: 2px; border-color: #234974; border-radius: 40px;'>";
                            summarykuota +="<div class='col-md-3'>";
                            summarykuota +="<div class='rounded-circle image-container' style='margin-left: -17px; width: 75px; height: 75px; border-style: solid; border-width: 2px; border-color: #234974; background-color: #fff; overflow: hidden; position: relative;'>";
                            summarykuota +="<img src='"+url+"dist/img/dokter/"+result[i].DOKTER_ID+".png' class='hover-image' style='width: 100%; height: 100%; object-fit: cover;'>";
                            summarykuota +="</div>";
                            summarykuota +="</div>";
                            summarykuota +="<div class='col-md-9 p-2'>";
                            summarykuota +="<div class='col-md-12 p-1'>";
                            summarykuota +="<h3 class='text-truncate' style='color:#234974; font-weight: bold;'>" + result[i].NAMADOKTER + "</h3>";
                            summarykuota +="</div>";
                            summarykuota +="<div class='col-md-12'>";
                            summarykuota +="<div class='animate__animated animate__zoomIn col-md-12 m-1' style='display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; background-color:#FF0000; border-color: #FF0000; color: #fff; padding: 5px; display: flex; align-items: center; justify-content: center;'>";
                            summarykuota +="<h3 style='margin: 0; font-weight: bold; text-align: center;'>FULL</h3>";
                            summarykuota +="</div>";
                            summarykuota +="</div>";
                            summarykuota +="</div>";
                            summarykuota +="</div>";
                            summarykuota +="</div>";
                        }
                        
                    }
    
                    for (var containerId in containers) {
                        $("#" + containerId).html(containers[containerId]);
                    }

                    $("#summarykuota").html(summarykuota);
                }
            }
        });
    
        return false;
    }

    function kapasitasbed()
    {
        $.ajax({
            url     : url + "index.php/Raw_Data/kapasitasbed",
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
                                cardHtml += "<div class='col-md-12 row ml-1 mr-1 mt-2 mb-2' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 40px 10px 10px 10px;'>";
                                cardHtml += "<div class='col-md-2'>";
                                                
                                    cardHtml += "<div style='margin-left:-15px; width: 75px; height: 75px; border-radius: 50%; background: linear-gradient(to right, #4aac90, #234974); display: flex; align-items: center; justify-content: center;'>";
                                    cardHtml += "<i class='fa-solid fa-bed fa-4x text-white'></i>";
                                    cardHtml += "</div>";
                                cardHtml += "</div>";
                                cardHtml += "<div class='col-md-10 mt-2'>";
                                    cardHtml += "<div class='col-md-12'>";
                                        cardHtml += "<h1 class='text-truncate' style='color:#234974;'><strong>"+result[i].NAMAUNIT+"</strong></h1>";
                                        cardHtml += "<h3 class='text-truncate' style='color:#234974;'><strong>"+result[i].KETERANGAN+"</strong></h3>";
                                    cardHtml += "</div>";
                                    cardHtml += "<div class='col-md-12 mt-3'>";

                                        if(result[i].CELLID==="DEWAS0000000001"){
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
    
                        if (i < 4) {
                            kapasitasri1 += cardHtml;
                        }else{
                            if (i < 8) {
                                kapasitasri2 += cardHtml;
                            }else{
                                if (i < 12) {
                                    kapasitasri3 += cardHtml;
                                }else{
                                    if (i < 16) {
                                        kapasitasri4 += cardHtml;
                                    }else{
                                        kapasitasri5 += cardHtml;
                                    }
                                }
                            }
                        }
                    }
                }
    
                legenda  = "<div class='col-md-12 d-flex justify-content-center'>";
                    
                    legenda += "<div class='row ml-2 mr-2' style='width:120px; height: 25px; display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 2px; display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<div class='col-md-3' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<i class='fa-solid fa-house-chimney-medical fa-2x'></i>";
                        legenda += "</div>";
                        legenda += "<div class='col-md-9' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<h4 style='margin: 0; font-weight: bold;'>KAPASITAS</h4>";
                        legenda += "</div>";
                    legenda += "</div>";

                    legenda += "<div class='row ml-2 mr-2' style='width:120px; height: 25px; display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 2px; display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<div class='col-md-3' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<i class='fa-solid fa-house-medical-circle-xmark fa-2x'></i>";
                        legenda += "</div>";
                        legenda += "<div class='col-md-9' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<h4 style='margin: 0; font-weight: bold;'>TERISI</h4>";
                        legenda += "</div>";
                    legenda += "</div>";

                    legenda += "<div class='row ml-2 mr-2' style='width:120px; height: 25px; display: inline-block; border-radius: 50px; border-width: 2px; border-style: solid; border-color: #234974; color: #234974; padding: 2px; display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<div class='col-md-3' style='display: flex; align-items: center; justify-content: center;'>";
                            legenda += "<i class='fa-solid fa-house-medical-circle-check fa-2x text-success'></i>";
                        legenda += "</div>";
                            legenda += "<div class='col-md-9' style='display: flex; align-items: center; justify-content: center;'>";
                        legenda += "<h4 style='margin: 0; font-weight: bold;'>TERSEDIA</h4>";
                    legenda += "</div>";

                    legenda += "</div>";
                legenda += "</div>";

                $("#kapasitasri1").html(kapasitasri1+legenda);
                $("#kapasitasri2").html(kapasitasri2+legenda);
                $("#kapasitasri3").html(kapasitasri3+legenda);
                $("#kapasitasri4").html(kapasitasri4+legenda);
                $("#kapasitasri5").html(kapasitasri5+legenda);
            }
        });
        return false;
    };

    function kapasitasbedv2() {
        $.ajax({
            url: url + "index.php/Raw_Data/kapasitasbed",
            method: "POST",
            dataType: "JSON",
            cache: false,
            success: function (data) {
                var kapasitasri1 = "";
                var kapasitasri2 = "";
                var kapasitasri3 = "";
                var kapasitasri4 = "";
                var kapasitasri5 = "";

                if (data.responCode == "00") {
                    var result = data.responResult;

                    for (var i in result) {
                        var cardHtml  = "<div class='col-md-6 d-flex justify-content-center p-5'>";
                            if(result[i].STATUS==="0"){
                                cardHtml += "<div class='col-md-12 row' style='border-style: solid; border-width:10px; border-color: #234974; border-radius: 110px 10px 10px 10px;'>";
                            }else{
                                cardHtml += "<div class='col-md-12 row' style='border-style: solid; border-width:10px; border-color: #234974; border-radius: 110px;'>";
                            }
                            
                                cardHtml +="<div class='col-md-2'>";
                                    cardHtml += "<div style='margin-top:-5px; margin-left:-25px; background: linear-gradient(to right, #4aac90, #234974); border-radius: 50%; width: 200px; height: 200px; display: flex; align-items: center; justify-content: center;'>";
                                        cardHtml += "<i class='fa-solid fa-bed fa-6x text-white'></i>";
                                    cardHtml += "</div>";
                                cardHtml += "</div>";
                                cardHtml +="<div class='col-md-10 pl-4 pt-3 pb-3'>";
                                    cardHtml +="<div class='col-md-12'>";
                                        cardHtml +="<h2 style='color:#234974;'>"+result[i].NAMAUNIT+"</h2>";
                                        cardHtml +="<h5 style='color:#234974;'>"+result[i].KETERANGAN+"</h5>";
                                    cardHtml += "</div>";
                                    cardHtml +="<div class='col-md-12'>";
                                        if(result[i].CELLID==="DEWAS0000000001"){
                                            if(result[i].TOT_KLS_1!="0"){
                                                cardHtml +="<div class='row'>";
                                                cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:70px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>1</h6></div>";
                                                cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>KAPASITAS : "+result[i].TOT_KLS_1+"</h6></div>";
                                                cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERISI : "+result[i].ISI_KLS_1+"</h6></div>";
                                                if(result[i].KOS_KLS_V!="0"){
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_1+"</h6></div>";
                                                }else{
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1 fa-fade' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; background-color:#ed8423; border-color: #ed8423; color: #ffff; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_1+"</h6></div>";
                                                }
                                                cardHtml +="</div>";
                                            }
                                        }else{
                                            if(result[i].CELLID==="VIP (0000000001"){
                                                if(result[i].TOT_KLS_V!="0"){
                                                    cardHtml +="<div class='row'>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:70px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>V</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>KAPASITAS : "+result[i].TOT_KLS_V+"</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERISI : "+result[i].ISI_KLS_V+"</h6></div>";
                                                    
                                                    if(result[i].KOS_KLS_V!="0"){
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_V+"</h6></div>";
                                                    }else{
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1 fa-fade' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; background-color:#ed8423; border-color: #ed8423; color: #ffff; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_V+"</h6></div>";
                                                    }
                                                    
                                                    cardHtml +="</div>";
                                                }
                                                
                                            }else{
                                                if(result[i].TOT_KLS_1!="0"){
                                                    cardHtml +="<div class='row'>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:70px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>1</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>KAPASITAS : "+result[i].TOT_KLS_1+"</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERISI : "+result[i].ISI_KLS_1+"</h6></div>";
                                                    if(result[i].KOS_KLS_1!="0"){
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_1+"</h6></div>";
                                                    }else{
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1 fa-fade' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; background-color:#ed8423; border-color: #ed8423; color: #ffff; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_1+"</h6></div>";
                                                    }
                                                    cardHtml +="</div>";
                                                }
                                                
                                                if(result[i].TOT_KLS_2!="0"){
                                                    cardHtml +="<div class='row'>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:70px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>2</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>KAPASITAS : "+result[i].TOT_KLS_2+"</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERISI : "+result[i].ISI_KLS_2+"</h6></div>";
                                                    if(result[i].KOS_KLS_2!="0"){
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_2+"</h6></div>";
                                                    }else{
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1 fa-fade' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; background-color:#ed8423; border-color: #ed8423; color: #ffff; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_2+"</h6></div>";
                                                    }
                                                    cardHtml +="</div>";
                                                }
                    
                                                if(result[i].TOT_KLS_3!="0"){
                                                    cardHtml +="<div class='row'>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:70px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>3</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>KAPASITAS : "+result[i].TOT_KLS_3+"</h6></div>";
                                                    cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERISI : "+result[i].ISI_KLS_3+"</h6></div>";
                                                    if(result[i].KOS_KLS_3!="0"){
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; border-color: #234974; color: #234974; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_3+"</h6></div>";
                                                    }else{
                                                        cardHtml +="<div class='align-items-center ml-2 mr-2 mt-1 mb-1 fa-fade' style='width:140px; display: inline-block; border-radius: 50px; border-width: 3px; border-style: solid; background-color:#ed8423; border-color: #ed8423; color: #ffff; padding: 10px; text-align: center;'><h6 style='margin: 0;'>TERSEDIA : "+result[i].KOS_KLS_3+"</h6></div>";
                                                    }
                                                    cardHtml +="</div>";
                                                }
                                            }
                                        }
                                    cardHtml += "</div>";
                                cardHtml += "</div>";
                            cardHtml += "</div>";
                            cardHtml += "</div>";

                        
                        if (i < 4) {
                            kapasitasri1 += cardHtml;
                        }else{
                            if (i < 8) {
                                kapasitasri2 += cardHtml;
                            }else{
                                if (i < 12) {
                                    kapasitasri3 += cardHtml;
                                }else{
                                    if (i < 16) {
                                        kapasitasri4 += cardHtml;
                                    }else{
                                        kapasitasri5 += cardHtml;
                                    }
                                }
                            }
                        }
                        
                    }
                }

                $("#kapasitasri1").html(kapasitasri1);
                $("#kapasitasri2").html(kapasitasri2);
                $("#kapasitasri3").html(kapasitasri3);
                $("#kapasitasri4").html(kapasitasri4);
                $("#kapasitasri5").html(kapasitasri5);
            }
        });
        return false;
    }

    function antrianselanjutnya()
    {
        var parameterValue = $('#parameterdisplay').val();
        $.ajax({
            url     : url + "index.php/Raw_Data/antrianselanjutnya?parameterdisplay="+parameterValue,
            method  : "POST",
            dataType: "JSON",
            cache   : false,
            success: function (data) {
                var liatantrian      = "";
    
                if (data.responCode == "00") {
                    var result = data.responResult;
    
                    for (var i in result) {
                        
                        liatantrian +="<div class='row col-md-12 d-flex align-items-center m-1 animate__animated animate__zoomIn' style='border-style: solid; border-width:2px; border-color: #234974; border-radius: 10px;'>";
                        liatantrian +="<div class='row col-md-9 d-flex justify-content-start align-items-center'>";
                        liatantrian +="<div class='col-md-12 d-flex justify-content-start align-items-center'>";
                        
                        if(result[i].PASIEN_ID!=null){
                            liatantrian +="<h1 class='text-truncate' style='color:#234974;'><strong>"+result[i].NAMAPASIEN+"</strong></h1>";
                        }else{
                            liatantrian +="<h1 class='text-truncate' style='color:#234974;'><strong>"+result[i].KETERANGANANTRIAN+"</strong></h1>";
                        }

                        liatantrian +="</div>";
                        liatantrian +="<div class='col-md-12 d-flex justify-content-start align-items-center'>";

                        liatantrian +="<h1 class='text-truncate' style='color:#359d9e;'><strong>"+result[i].KETERANGANANTRIAN+" [ "+result[i].KETERANGAN+" ]</strong></h1>";
                        
                        
                        liatantrian +="</div>";
                        liatantrian +="</div>";
                        liatantrian +="<div class='col-md-3 d-flex justify-content-center align-items-center'>";
                        liatantrian +="<h1 style='color:#ed8423; font-size:350%'><strong>"+result[i].KODE+"-"+result[i].URUT+"</strong></h1>";
                        liatantrian +="</div>";
                        liatantrian +="</div>";
                        
                    }
                }
    
                

                $("#liatantrian").html(liatantrian);
            }
        });
        return false;
    };

    function antriansaatini()
    {
        var parameterValue = $('#parameterdisplay').val();
        $.ajax({
            url     : url + "index.php/Raw_Data/antriansaatini?parameterdisplay="+parameterValue,
            method  : "POST",
            dataType: "JSON",
            cache   : false,
            success: function (data) {

                if (data.responCode == "00") {
                    var result = data.responResult;
                    var call   = result[0].KODE+"-"+result[0].URUT+"-"+result[0].LOKET;

                    // var audioFiles = soundAntrianArray.map(function (soundFileName) {
					// 	return new Audio(url+soundFileName.trim());
					// });

                    

                    // function playAudio(index) {
                    //     if (index >= 0 && index < audioFiles.length) {
                    //         audioFiles[index].play();

                    //         audioFiles[index].addEventListener('error', function () {
                    //             alert("Terjadi kesalahan saat memutar audio!");
                    //         });

                    //         // setTimeout(function () {
                    //         //     playAudio(index + 1);
                    //         // }, 1000); // Ubah 1000 menjadi jarak yang Anda inginkan dalam milidetik (1 detik = 1000 milidetik).
                    //     }
                    // }

                    // function playAudio(index) {
                    //     if (index >= 0 && index < audioFiles.length) {
                    //         audioFiles[index].onended = function() {
                    //             setTimeout(function () {
                    //                 playAudio(index + 1);
                    //             }, 1000);
                    //         };
                    
                    //         audioFiles[index].onerror = function() {
                    //             console.error("Error playing audio file:", audioFiles[index].src);
                    //             alert("Gagal memutar audio. Silakan coba lagi atau periksa konsol untuk detail kesalahan.");
                    //             playAudio(index + 1); // Skip to the next audio file in case of an error
                    //         };
                    
                    //         audioFiles[index].play();
                    //     }
                    // }
                    
                    
                    

                    console.log ("Last Call "+lastcall);
                    console.log ("Call "+call);

                    // opening.play();
                    // setTimeout(function () {a.play();}, 3000);
                    // setTimeout(function () {satu.play();}, 4000);
                    // setTimeout(function () {ending.play();}, 5000);

                    // opening.addEventListener('error', function() {
                    //     alert("Terjadi kesalahan saat memutar audio!");
                    // });

                    // hurufa.addEventListener('error', function() {
                    //     alert("Terjadi kesalahan saat memutar audio!");
                    // });

                    // setTimeout(function () {a.play();}, 3000);
                    // setTimeout(function () {satu.play();}, 4000);
                    // setTimeout(function () {ending.play();}, 5000);

                    if (lastcall !== call) {
                        
                        playsound(result[0].SOUNDANTRIAN);
                        updateantrian(result[0].TRANS_ID,result[0].KODE,result[0].URUT,result[0].JML_CALL);

                        // playAudio(0);
                        // opening.play();
                        // hurufa.play();
                        // satu.play();
                        // ending.play();

                        // setTimeout(function () {opening.play();}, 1000);
                        // setTimeout(function () {hurufa.play();}, 3000);
                        // setTimeout(function () {satu.play();}, 5000);
                        // setTimeout(function () {ending.play();}, 6000);

                        
                        lastcall = call;
                    }

    
                    $("#antriansaatini").html(result[0].KODE+"-"+result[0].URUT);
                    $("#loketsaatini").html("LOKET "+result[0].LOKET);

                    if(result[0].ANTRIANLOKET1!=null){
                        $("#saatiniloket1").html(result[0].ANTRIANLOKET1);
                    }else{
                        $("#saatiniloket1").html("STAND BY");
                    }

                    if(result[0].ANTRIANLOKET2!=null){
                        $("#saatiniloket2").html(result[0].ANTRIANLOKET2);
                    }else{
                        $("#saatiniloket2").html("STAND BY");
                    }

                    if(result[0].ANTRIANLOKET3!=null){
                        $("#saatiniloket3").html(result[0].ANTRIANLOKET3);
                    }else{
                        $("#saatiniloket3").html("STAND BY");
                    }

                    if(result[0].ANTRIANLOKET4!=null){
                        $("#saatiniloket4").html(result[0].ANTRIANLOKET4);
                    }else{
                        $("#saatiniloket4").html("STAND BY");
                    }

                    
                }
    
            }
        });
        return false;
    };

    function updateantrian($transid,$kode,$urut,$jmlcall){
        console.log ("Proccess Update");
        $.ajax({
            url     : url+"index.php/Raw_Data/updateantrian",
            data    : {'transid':$transid,'kode':$kode,'urut':$urut,'jmlcall':$jmlcall},
            method  : "POST",
            dataType: "JSON",
            cache   : false,
            success:function(data){
                console.log ("Proccess "+data.responHead);
            }
        });
        return false;
    }

    function lastupdate()
    {
        var timezone    = new Date().toLocaleString("en-US", { timeZone: "Asia/Jakarta" });
        var jakartaTime = new Date(timezone);
    
        var jam   = jakartaTime.getHours();
        var menit = jakartaTime.getMinutes();
        var detik = jakartaTime.getSeconds();
    
        var zonaWaktu = jakartaTime.toString().match(/\(([^)]+)\)$/)[1];
    
        var formattedTime = jam + ":" + (menit < 10 ? "0" + menit : menit) + ":" + (detik < 10 ? "0" + detik : detik) + " " + zonaWaktu;
    
        $("#lastupdate").html("Last Update : "+formattedTime);
    };
    
})