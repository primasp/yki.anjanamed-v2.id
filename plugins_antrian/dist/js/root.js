lastupdate();

function lastupdate()
{
    var timezone    = new Date().toLocaleString("en-US", { timeZone: "Asia/Jakarta" });
    var jakartaTime = new Date(timezone);

    var jam     = jakartaTime.getHours();
    var menit   = jakartaTime.getMinutes();
    var detik   = jakartaTime.getSeconds();
    var tanggal = jakartaTime.getDate();
    var bulan   = jakartaTime.getMonth() + 1;  // Perhatikan bahwa bulan dimulai dari 0 (Januari) hingga 11 (Desember)
    var tahun   = jakartaTime.getFullYear();

    var zonaWaktu = jakartaTime.toString().match(/\(([^)]+)\)$/)[1];

    var formattedTime = jam + ":" + (menit < 10 ? "0" + menit : menit) + ":" + (detik < 10 ? "0" + detik : detik);
    var formattedDate = tanggal + "/" + (bulan < 10 ? "0" + bulan : bulan) + "/" + tahun;

    $("#lastupdate").html("Last Update: " + formattedDate + " " + formattedTime);
};

function todesimal(bilangan)
{
    var	reverse = bilangan.toString().split('').reverse().join(''),
        ribuan 	= reverse.match(/\d{1,3}/g);
        ribuan	= ribuan.join('.').split('').reverse().join('');
    return ribuan;
};