<?php
function formatTanggalIndonesia($tanggal)
{
    if (empty($tanggal)) return '-';

    // Konversi string ke format DateTime
    $date = DateTime::createFromFormat('Y-m-d', $tanggal);

    if ($date) {
        return $date->format('d-m-Y');
    } else {
        return '-';
    }
}

function hitung_umur($tgl_lahir)
{
    if (!$tgl_lahir) return '-';

    // Format tanggal lahir
    $tgl_lahir_obj = DateTime::createFromFormat('d.m.Y', $tgl_lahir);

    if (!$tgl_lahir_obj) return '-'; // Jika format salah, kembalikan '-'

    $today = new DateTime();
    $umur = $today->diff($tgl_lahir_obj);

    return $umur->y . ' tahun ' . $umur->m . ' bulan ' . $umur->d . ' hari';
}


function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatRibuan($angka)
{
    return number_format($angka, 0, ',', '.');
}

function generateMemberID($urutan)
{
    // Konversi $urutan menjadi string
    $urutanStr = strval($urutan);

    // return var_dump($urutanStr);
    // die;

    // Format ID anggota dengan tanggal hari ini (YYYYMMDD) dan urutan (3 digit)
    $formattedDate = date("Ymd");
    $formattedUrutan = str_pad($urutanStr, 3, '0', STR_PAD_LEFT);
    $memberID = $formattedDate . $formattedUrutan;
    return $memberID;
}

function getBulanMap()
{
    return [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];
}




function convertDateTime($input, $to, $from = '')
{
    $bulanMap = getBulanMap();

    $formatMap = [
        'userDate' => [
            'format' => 'd.m.Y',
            'regex' => '/^\d{2}\.\d{2}\.\d{4}$/'
        ],
        'userDateTime' => [
            'format' => 'd.m.Y H:i:s',
            'regex' => '/^\d{2}\.\d{2}\.\d{4} \d{2}\:\d{2}\:\d{2}$/'
        ],
        'idUserDate' => [
            'format' => 'd-m-Y',
            'regex' => '/^\d{2}\-\d{2}\-\d{4}$/'
        ],
        'idUserDateTime' => [
            'format' => 'd-m-Y H:i:s',
            'regex' => '/^\d{2}\.\d{2}\.\d{4} \d{2}\:\d{2}\:\d{2}$/'
        ],
        'slashDate' => [
            'format' => 'd/m/Y',
            'regex' => '/^\d{2}\/\d{2}\/\d{4}$/'
        ],
        'isoDate' => [
            'format' => 'Y-m-d',
            'regex' => '/^\d{4}\-\d{2}\-\d{2}$/'
        ],
        'isoDateTime' => [
            'format' => 'Y-m-d H:i:s',
            'regex' => '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}$/'
        ],
        'clock' => [
            'format' => 'H:i:s',
            'regex' => '/^\d{2}\:\d{2}:\d{2}$/'
        ],
    ];

    $fromFormat = '';

    if (empty($from)) {
        $hasFromFormat = false;
        foreach ($formatMap as $key => $row) {
            if (preg_match($row['regex'], $input)) {
                $fromFormat = $key;
                $hasFromFormat = true;
            }
        }

        if (!$hasFromFormat) {
            throw new Exception("Unknown source format for date: {$input}");
        }
    } else {
        if (!array_key_exists($from, $formatMap)) {
            throw new Exception("Unknown from format {$from} for date {$input}");
        }
        $fromFormat = $from;
    }

    // Buat objek DateTime dari format yang ditemukan
    $originalDate = DateTime::createFromFormat($formatMap[$fromFormat]['format'], $input);

    // Validasi apakah $originalDate berhasil dibuat
    if (!$originalDate) {
        throw new Exception("Failed to parse date: {$input} with format {$formatMap[$fromFormat]['format']}");
    }

    // Tambahan format dan fungsi
    $formatMap['userDateTextMonth'] = [
        'function' => function () use ($bulanMap, $originalDate) {
            return $originalDate->format('d') . ' ' . $bulanMap[$originalDate->format('m')] . ' ' . $originalDate->format('Y');
        }
    ];
    $formatMap['userDateShortTextMonth'] = [
        'function' => function () use ($bulanMap, $originalDate) {
            return $originalDate->format('d') . ' ' . substr($bulanMap[$originalDate->format('m')], 0, 3) . ' ' . $originalDate->format('Y');
        }
    ];
    $formatMap['userDateTimeTextMonth'] = [
        'function' => function () use ($bulanMap, $originalDate) {
            return $originalDate->format('d') . ' ' . $bulanMap[$originalDate->format('m')] . ' ' . $originalDate->format('Y H:i:s');
        }
    ];
    $formatMap['userDateTimeShortTextMonth'] = [
        'function' => function () use ($bulanMap, $originalDate) {
            return $originalDate->format('d') . ' ' . substr($bulanMap[$originalDate->format('m')], 0, 3) . ' ' . $originalDate->format('Y H:i:s');
        }
    ];
    $formatMap['textMonth'] = [
        'function' => function () use ($bulanMap, $originalDate) {
            return $bulanMap[$originalDate->format('m')];
        }
    ];
    $formatMap['shortTextMonth'] = [
        'function' => function () use ($bulanMap, $originalDate) {
            return substr($bulanMap[$originalDate->format('m')], 0, 3);
        }
    ];

    if (!array_key_exists($to, $formatMap)) {
        return '[invalid conversion to format: ' . $input . ']';
    }

    if (isset($formatMap[$to]['function'])) {
        return $formatMap[$to]['function']();
    } else {
        return $originalDate->format($formatMap[$to]['format']);
    }
}
