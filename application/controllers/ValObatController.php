<?php
defined('BASEPATH') or exit('No direct script access allowed');


class ValObatController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0003", "RU0004"])) {
            redirect('AuthController');
        }
        $this->load->model('UserModel', 'um');
        $this->load->model('ObatModel', 'om');
        $this->load->model('DokterModel', 'dm');
        $this->load->model('BpjsApiModel', 'bm');
    }
    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        // $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id')])->row_array();
        $data['script'] = 'js/val-obat.js';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('obat/formValidasi', $data);
        $this->load->view('obat/cariresep_modal');
        $this->load->view('templates/footer');
    }

    public function Caripasien()
    {
        $filtercari   = $this->input->post('pencarianpasien');
        $tanggal      = $this->input->post('tanggal');



        // Pastikan tanggal dalam format yang benar jika dikirim
        if (!empty($tanggal)) {
            $tanggal = date('Y-m-d', strtotime($tanggal)); // Format ke YYYY-MM-DD jika diperlukan
        } else {
            $tanggal = null; // Jika kosong, biarkan null agar tidak memfilter tanggal
        }

        // return var_dump($tanggal);
        // die;

        // $hasil = $this->om->listpasien($filtercari);
        $hasil = $this->om->listpasien($filtercari, $tanggal);


        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Data Pasien Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Data Pasien Ditemukan';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function simpanresep()
    {
        // pastikan tidak ada output nyelip
        if (ob_get_length()) {
            @ob_end_clean();
        }

        $ip       = $_SERVER['REMOTE_ADDR'];
        $gudangid = 'DEPO00000000APT';

        $data['EPISODE_ID']  = $this->input->post("episodeid");
        $data['PASIEN_ID']   = $this->input->post("pasienid");
        $data['TRANS_ID']    = $this->input->post("transid");
        $data['TRANS_CO']    = $this->input->post("transco");
        $data['TANGGAL']     = date('Y-m-d', strtotime($this->input->post("tglresep")));
        $data['RESEP_KE']    = $this->input->post("resepke");
        $data['TOTAL_HARGA'] = $this->input->post("totharga");

        try {
            if (empty($data['EPISODE_ID']) || empty($data['PASIEN_ID'])) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode(["Responcode" => "01", "Responhead" => "error", "Respondesc" => "Input tidak valid"]));
            }

            $gettransvalid = $this->om->gettransvalid();
            $transvalid    = $gettransvalid->trans_valid_id ?? null;

            // simpan detail
            $obatArray = $this->input->post("dataObat");
            if (!empty($obatArray)) {
                foreach ($obatArray as $obat) {
                    $obatData = [
                        'KETTIPE'      => $obat['kettipe'] ?? '',
                        'OBAT_ID'      => $obat['obatid'] ?? '',
                        'NAMA_OBAT'    => $obat['namaobat'] ?? '',
                        'QTY'          => $obat['qty'] ?? 0,
                        'STOK'         => $obat['stok'] ?? 0,
                        'SATUAN'       => $obat['satuan'] ?? '',
                        'DOSIS'        => $obat['dosis'] ?? '',
                        'FREEDOSIS'    => $obat['freedosis'] ?? '',
                        'SIGNA_NAMA'   => $obat['signanama'] ?? '',
                        'SIGNA_DOKTER' => $obat['signadokter'] ?? '',
                        'CATATAN'      => $obat['catatan'] ?? '',
                        'HARGA'        => $obat['harga'] ?? 0,
                        'TOTAL_HARGA'  => $obat['totharga'] ?? 0,
                        'URUT'         => $obat['urut'] ?? '',
                        'TIPE_OBAT'    => $obat['tipeobat'] ?? '',
                        'HEADER'       => $obat['header'] ?? '',
                        'SATUAN_ID'    => $obat['satuanid'] ?? '',
                        'SIGNA_ID'     => $obat['signaid'] ?? '',
                        'NAMA_RACIKAN' => $obat['namaracikan'] ?? ''
                    ];
                    $this->om->simpanresepit($data, $obatData, $transvalid, $ip, $gudangid);
                }
            }

            // simpan header
            if (!$this->om->simpanresephd($data, $transvalid, $ip, $gudangid)) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode(["Responcode" => "01", "Responhead" => "error", "Respondesc" => "Gagal Menyimpan header resep"]));
            }

            $kunjunganData  = $this->dm->getDtlKrmKunjungan($data['EPISODE_ID']);


            if (isset($kunjunganData['rekanan_id']) && strtoupper($kunjunganData['rekanan_id']) === 'BPJS') {

                // ====== ambil data untuk BPJS ======
                $cekNoKunjungan = $this->dm->cekNoKunjungan($data['EPISODE_ID']);
                $no_kunjungan   = $cekNoKunjungan['nomor_kunjungan'] ?? null;

                $rujukanData    = $this->dm->getDtlKrmRujukan($data['EPISODE_ID']);
                $icd10_1        = $this->dm->getIcd10Utama($data['EPISODE_ID']);
                $sek            = $this->dm->getIcd10Sekunder($data['EPISODE_ID']);

                $data['kdDiag2'] = isset($sek[0]['kode_icd']) ? trim($sek[0]['kode_icd']) : null;
                $data['kdDiag3'] = isset($sek[1]['kode_icd']) ? trim($sek[1]['kode_icd']) : null;

                // Terapi non-obat
                $tindakanData = $this->dm->gettindakan('001', $data['EPISODE_ID'], $data['PASIEN_ID']);
                $data['TERAPI_NON_OBAT'] = $tindakanData ? implode("\n", array_map(function ($t, $i) {
                    return ($i + 1) . ". " . $t['nama_layan'] . " ( qty: " . $t['qty'] . ")";
                }, $tindakanData, array_keys($tindakanData))) : "Tidak ada data Terapi Non Obat.";

                // Terapi obat
                $resepDatavalid = $this->dm->getResepValidasi($data['EPISODE_ID']);
                $data['TERAPI_OBAT'] = $resepDatavalid ? implode("\n", array_map(function ($r, $i) {
                    return ($i + 1) . ". " . $r['nama_obat'] . " (dosis: " . $r['free_dosis'] . ", qty: " . $r['qty'] . ")";
                }, $resepDatavalid, array_keys($resepDatavalid))) : "Tidak ada data resep.";

                // payload
                if (($cekNoKunjungan['kd_stat_plg'] ?? '') === '4') {
                    $result = [
                        'noKunjungan' => $no_kunjungan,
                        'noKartu' => $kunjunganData['no_kartuprov'] ?? '',
                        'keluhan' => $kunjunganData['keluhan'] ?? '',
                        'kdSadar' => $cekNoKunjungan['kd_sadar'] ?? '',
                        'sistole' => (int)($kunjunganData['sistole'] ?? 0),
                        'diastole' => (int)($kunjunganData['diastole'] ?? 0),
                        'beratBadan' => (float)($kunjunganData['berat_badan'] ?? 0),
                        'tinggiBadan' => (float)($kunjunganData['tinggi_badan'] ?? 0),
                        'respRate' => (float)($kunjunganData['respiratory_rate'] ?? 0),
                        'heartRate' => (float)($kunjunganData['heart_rate'] ?? 0),
                        'lingkarPerut' => (float)($kunjunganData['ling_perut'] ?? 0),
                        'kdStatusPulang' => $cekNoKunjungan['kd_stat_plg'] ?? '',
                        'tglPulang' => date('d-m-Y', strtotime($rujukanData['tgl_masuk'] ?? date('Y-m-d'))),
                        'kdDokter' => $kunjunganData['prefix'] ?? '',
                        'kdDiag1' => $icd10_1->kode_icd ?? '',
                        'kdDiag2' => $data['kdDiag2'],
                        'kdDiag3' => $data['kdDiag3'],
                        'kdPoliRujukInternal' => null,
                        'rujukLanjut' => $this->generateRujukanLanjut($rujukanData),
                        'kdTacc' => trim($cekNoKunjungan['kd_tacc'] ?? ''),
                        'alasanTacc' => trim($cekNoKunjungan['alasan_tacc'] ?? ''),
                        'anamnesa' => $rujukanData['anamnesa'] ?? '',
                        'alergiMakan' => $cekNoKunjungan['kd_alergi_mknn'] ?? '',
                        'alergiUdara' => $cekNoKunjungan['kd_alergi_udara'] ?? '',
                        'alergiObat' => $cekNoKunjungan['kd_alergi_obt'] ?? '',
                        'kdPrognosa' => $cekNoKunjungan['kd_prognosa'] ?? '',
                        'terapiObat' => $data['TERAPI_OBAT'],
                        'terapiNonObat' => $data['TERAPI_NON_OBAT'],
                        'bmhp' => null,
                        'suhu' => (float)($rujukanData['tv_suhu'] ?? 0),
                    ];
                } else {
                    $result = [
                        'noKunjungan' => $no_kunjungan,
                        'noKartu' => $kunjunganData['no_kartuprov'] ?? '',
                        'keluhan' => $kunjunganData['keluhan'] ?? '',
                        'kdSadar' => $cekNoKunjungan['kd_sadar'] ?? '',
                        'sistole' => (int)($kunjunganData['sistole'] ?? 0),
                        'diastole' => (int)($kunjunganData['diastole'] ?? 0),
                        'beratBadan' => (float)($kunjunganData['berat_badan'] ?? 0),
                        'tinggiBadan' => (float)($kunjunganData['tinggi_badan'] ?? 0),
                        'respRate' => (float)($kunjunganData['respiratory_rate'] ?? 0),
                        'heartRate' => (float)($kunjunganData['heart_rate'] ?? 0),
                        'lingkarPerut' => (float)($kunjunganData['ling_perut'] ?? 0),
                        'kdStatusPulang' => $cekNoKunjungan['kd_stat_plg'] ?? '',
                        'tglPulang' => date('d-m-Y', strtotime($kunjunganData['tgl_masuk'] ?? date('Y-m-d'))),
                        'kdDokter' => $kunjunganData['prefix'] ?? '',
                        'kdDiag1' => $icd10_1->kode_icd ?? '',
                        'kdDiag2' => $data['kdDiag2'],
                        'kdDiag3' => $data['kdDiag3'],
                        'kdPoliRujukInternal' => null,
                        'rujukLanjut' => null,
                        'kdTacc' => trim($cekNoKunjungan['kd_tacc'] ?? ''),
                        'alasanTacc' => trim($cekNoKunjungan['alasan_tacc'] ?? ''),
                        'anamnesa' => $kunjunganData['anamnesa'] ?? '',
                        'alergiMakan' => $cekNoKunjungan['kd_alergi_mknn'] ?? '',
                        'alergiUdara' => $cekNoKunjungan['kd_alergi_udara'] ?? '',
                        'alergiObat' => $cekNoKunjungan['kd_alergi_obt'] ?? '',
                        'kdPrognosa' => $cekNoKunjungan['kd_prognosa'] ?? '',
                        'terapiObat' => $data['TERAPI_OBAT'],
                        'terapiNonObat' => $data['TERAPI_NON_OBAT'],
                        'bmhp' => null,
                        'suhu' => (float)($kunjunganData['tv_suhu'] ?? 0),
                    ];
                }

                // ====== kirim ke BPJS ======
                $url = BPJS_BASE_URL_PCARE . "/kunjungan/v1";
                $response   = $this->bm->putToBpjsKunjungan($url, json_encode($result), "PUT");



                // === Evaluasi hasil BPJS ===
                if ($response && isset($response['metaData']['code'])) {
                    $code = (int)$response['metaData']['code'];
                    $msg  = $response['metaData']['message'] ?? '';



                    $json = [
                        "Responcode" => "00",
                        "Responhead" => "success",
                        "Respondesc" => ($code === 200 || $code === 201)
                            ? "Berhasil menyimpan & update ke BPJS ($msg)"
                            : "Berhasil menyimpan, tapi gagal update ke BPJS ($msg)",
                        "bpjs_response" => $response
                    ];



                    // if ($code === 200 || $code === 201) {
                    //     echo json_encode([
                    //         "Responcode" => "00",
                    //         "Respondesc" => "Berhasil simpan & update ke BPJS (" . $msg . ")",
                    //         "bpjs_response" => $response,
                    //     ]);
                    // } else {
                    //     echo json_encode([
                    //         "Responcode" => "00",
                    //         "Respondesc" => "Berhasil simpan, tapi gagal update ke BPJS (" . $msg . ")",
                    //         "bpjs_response" => $response,
                    //     ]);
                    // }
                } else {
                    // echo json_encode([
                    //     "Responcode" => "00",
                    //     "Respondesc" => "Berhasil simpan, namun tidak ada respons BPJS",
                    // ]);

                    $json = [
                        "Responcode" => "00",
                        "Responhead" => "success",
                        "Respondesc" => "Berhasil menyimpan, namun tidak ada respons BPJS"
                    ];
                }
            } else {
                // $json = [
                //     "Responcode" => "00",
                //     "Responhead" => "success",
                //     "Respondesc" => "Berhasil Menyimpan, tapi gagal update ke BPJS",
                //     "bpjs_response" => $response
                // ];

                $json = [
                    "Responcode" => "00",
                    "Responhead" => "success",
                    "Respondesc" => "Berhasil menyimpan (Non-BPJS, tidak dikirim ke BPJS)"
                ];
            }

            echo json_encode($json);
        } catch (Throwable $e) {
            echo json_encode([
                "Responcode" => "01",
                "Responhead" => "error",
                "Respondesc" => "Exception: " . $e->getMessage()
            ]);
        }
    }









    public function simpanresepx()
    {


        // Ambil data dasar
        $ip                     = $_SERVER['REMOTE_ADDR'];
        // $gudangid               = $this->input->post("gudangid");
        $gudangid               = 'DEPO00000000APT';
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");






        $data['TRANS_ID']       = $this->input->post("transid");

        $data['TRANS_CO']       = $this->input->post("transco");
        // $data['TANGGAL']        = $this->input->post("tglresep");
        $data['TANGGAL'] = date('Y-m-d', strtotime($this->input->post("tglresep")));

        $data['RESEP_KE']       = $this->input->post("resepke");
        $data['TOTAL_HARGA']    = $this->input->post("totharga");

        $validinput = false;
        if (!empty($data['EPISODE_ID']) && !empty($data['PASIEN_ID'])) {
            $validinput = true;
        }

        if ($validinput) {
            $gettransvalid        = $this->om->gettransvalid();
            $transvalid           = $gettransvalid->trans_valid_id;


            // Ambil data obat dari input
            $obatArray = $this->input->post("dataObat");

            if (!empty($obatArray)) {
                foreach ($obatArray as $obat) {
                    // Proses setiap obat
                    $obatData = [
                        'KETTIPE'      => $obat['kettipe'],
                        'OBAT_ID'      => $obat['obatid'],
                        'NAMA_OBAT'    => $obat['namaobat'],
                        'QTY'          => $obat['qty'],
                        'STOK'         => $obat['stok'],
                        'SATUAN'       => $obat['satuan'],
                        'DOSIS'        => $obat['dosis'],
                        'FREEDOSIS'    => $obat['freedosis'],
                        'SIGNA_NAMA'   => $obat['signanama'],
                        'SIGNA_DOKTER' => $obat['signadokter'],
                        'CATATAN'      => $obat['catatan'],
                        'HARGA'        => $obat['harga'],
                        'TOTAL_HARGA'  => $obat['totharga'],
                        'URUT'         => $obat['urut'],
                        'TIPE_OBAT'    => $obat['tipeobat'],
                        'HEADER'       => $obat['header'],
                        'SATUAN_ID'    => $obat['satuanid'],
                        'SIGNA_ID'     => $obat['signaid'],
                        'NAMA_RACIKAN' => $obat['namaracikan']
                    ];

                    $this->om->simpanresepit($data, $obatData, $transvalid, $ip, $gudangid);
                }
            }

            // Simpan header resep
            if ($this->om->simpanresephd($data, $transvalid, $ip, $gudangid)) {

                $kunjunganData = $this->dm->getDtlKrmKunjungan($data['EPISODE_ID']);
                // === Tahap 2: Kirim ke BPJS ===
                $cekNoKunjungan = $this->dm->cekNoKunjungan($data['EPISODE_ID']);
                $no_kunjungan = $cekNoKunjungan['nomor_kunjungan'];
                $kunjunganData = $this->dm->getDtlKrmKunjungan($data['EPISODE_ID']);
                $rujukanData = $this->dm->getDtlKrmRujukan($data['EPISODE_ID']);

                $icd10_1 = $this->dm->getIcd10Utama($data['EPISODE_ID']);

                $sek = $this->dm->getIcd10Sekunder($data['EPISODE_ID']);


                $data['kdDiag2'] = isset($sek[0]['kode_icd']) ? trim($sek[0]['kode_icd']) : null;
                $data['kdDiag3'] = isset($sek[1]['kode_icd']) ? trim($sek[1]['kode_icd']) : null;


                $tindakanData = $this->dm->gettindakan('001', $data['EPISODE_ID'], $data['PASIEN_ID']);
                if ($tindakanData) {
                    $terapiNonObatText = array_map(function ($tindakan, $index) {
                        return ($index + 1) . ". " . $tindakan['nama_layan'] . " ( qty: " . $tindakan['qty'] . ")";
                    }, $tindakanData, array_keys($tindakanData));

                    $data['TERAPI_NON_OBAT'] = implode("\n", $terapiNonObatText);
                } else {
                    $data['TERAPI_NON_OBAT'] = "Tidak ada data Terapi Non Obat.";
                }

                $resepDatavalid = $this->dm->getResepValidasi($data['EPISODE_ID']);

                // return var_dump($resepData);
                // die;
                if ($resepDatavalid) {
                    $terapiObatText = array_map(function ($resep, $index) {
                        return ($index + 1) . ". " . $resep['nama_obat'] . " (dosis: " . $resep['free_dosis'] . ", qty: " . $resep['qty'] . ")";
                    }, $resepDatavalid, array_keys($resepDatavalid));

                    $data['TERAPI_OBAT'] = implode("\n", $terapiObatText);
                } else {
                    $data['TERAPI_OBAT'] = "Tidak ada data resep.";
                }


                // === Susun payload untuk PUT BPJS ===
                if ($cekNoKunjungan['kd_stat_plg'] == '4') {

                    $result = [
                        'noKunjungan' => $no_kunjungan,
                        // 'noKartu' => $rujukanData['no_kartuprov'],
                        'noKartu' => $kunjunganData['no_kartuprov'],
                        // 'keluhan' => $rujukanData['keluhan'],
                        'keluhan' => $kunjunganData['keluhan'],
                        'kdSadar' => $cekNoKunjungan['kd_sadar'],

                        // 'sistole' => intval($rujukanData['sistole']),
                        'sistole' => intval($kunjunganData['sistole']),
                        'diastole' => intval($kunjunganData['diastole']),
                        'beratBadan' => (float)$kunjunganData['berat_badan'],
                        'tinggiBadan' => (float)$kunjunganData['tinggi_badan'],
                        'respRate' => (float)$kunjunganData['respiratory_rate'],
                        'heartRate' => (float)$kunjunganData['heart_rate'],
                        'lingkarPerut' => (float)$kunjunganData['ling_perut'],
                        // 'kdStatusPulang' => $data['STATPLG'],
                        'kdStatusPulang' => $cekNoKunjungan['kd_stat_plg'],
                        'tglPulang' => date('d-m-Y', strtotime($rujukanData['tgl_masuk'])),
                        'kdDokter' => $kunjunganData['prefix'],
                        'kdDiag1' => $icd10_1->kode_icd,
                        'kdDiag2' => $data['kdDiag2'],
                        'kdDiag3' => $data['kdDiag3'],
                        'kdPoliRujukInternal' => null,
                        'rujukLanjut' => $this->generateRujukanLanjut($rujukanData),
                        // 'kdTacc' => $data['KODE_TACC'],
                        // 'alasanTacc' => $data['TACC_ALASAN'],
                        'kdTacc' => isset($cekNoKunjungan['kd_tacc']) ? trim($cekNoKunjungan['kd_tacc']) : null,
                        'alasanTacc' =>  isset($cekNoKunjungan['alasan_tacc']) ? trim($cekNoKunjungan['alasan_tacc']) : null,
                        'anamnesa' => $rujukanData['anamnesa'],
                        'alergiMakan' => $cekNoKunjungan['kd_alergi_mknn'],
                        'alergiUdara' => $cekNoKunjungan['kd_alergi_udara'],
                        'alergiObat' => $cekNoKunjungan['kd_alergi_obt'],
                        'kdPrognosa' => $cekNoKunjungan['kd_prognosa'],
                        'terapiObat' => $data['TERAPI_OBAT'],
                        'terapiNonObat' => $data['TERAPI_NON_OBAT'],
                        'bmhp' => null,
                        'suhu' => (float)$rujukanData['tv_suhu'],
                    ];
                } else {
                    $result = [
                        'noKunjungan' => $no_kunjungan,
                        'noKartu' => $kunjunganData['no_kartuprov'],
                        'keluhan' => $kunjunganData['keluhan'],
                        'kdSadar' => $cekNoKunjungan['kd_sadar'],
                        'sistole' => intval($kunjunganData['sistole']),
                        'diastole' => intval($kunjunganData['diastole']),
                        'beratBadan' => (float)$kunjunganData['berat_badan'],
                        'tinggiBadan' => (float)$kunjunganData['tinggi_badan'],
                        'respRate' => (float)$kunjunganData['respiratory_rate'],
                        'heartRate' => (float)$kunjunganData['heart_rate'],
                        'lingkarPerut' => (float)$kunjunganData['ling_perut'],
                        'kdStatusPulang' => $cekNoKunjungan['kd_stat_plg'],
                        'tglPulang' => date('d-m-Y', strtotime($kunjunganData['tgl_masuk'])),
                        'kdDokter' => $kunjunganData['prefix'],
                        'kdDiag1' => $icd10_1->kode_icd,
                        'kdDiag2' => $data['kdDiag2'],
                        'kdDiag3' => $data['kdDiag3'],
                        'kdPoliRujukInternal' => null,
                        'rujukLanjut' => null,
                        'kdTacc' => isset($cekNoKunjungan['kd_tacc']) ? trim($cekNoKunjungan['kd_tacc']) : null,
                        'alasanTacc' =>  isset($cekNoKunjungan['alasan_tacc']) ? trim($cekNoKunjungan['alasan_tacc']) : null,
                        'anamnesa' => $kunjunganData['anamnesa'],
                        'alergiMakan' => $cekNoKunjungan['kd_alergi_mknn'],
                        'alergiUdara' => $cekNoKunjungan['kd_alergi_udara'],
                        'alergiObat' => $cekNoKunjungan['kd_alergi_obt'],
                        'kdPrognosa' => $cekNoKunjungan['kd_prognosa'],
                        'terapiObat' => $data['TERAPI_OBAT'],
                        'terapiNonObat' => $data['TERAPI_NON_OBAT'],
                        'bmhp' => null,
                        'suhu' => (float)$kunjunganData['tv_suhu'],
                    ];
                }
                // return var_dump($result);
                // die;

                // === Kirim ke BPJS ===
                $url = BPJS_BASE_URL_PCARE . "/kunjungan/v1";
                $response = $this->bm->putToBpjsKunjungan($url, json_encode($result), "PUT");



                // === Cek hasil BPJS ===

                if (isset($response['metaData']['code']) && $response['metaData']['code'] == "200") {
                    $json = [
                        "Responcode" => "00",
                        "Responhead" => "success",
                        "Respondesc" => "Berhasil Menyimpan dan Update ke BPJS",
                        "bpjs_response" => $response
                    ];
                } else {
                    $json = [
                        "Responcode" => "00",
                        "Responhead" => "success",
                        "Respondesc" => "Berhasil Menyimpan, tapi gagal update ke BPJS",
                        "bpjs_response" => $response
                    ];
                }







                // $json["Responcode"] = "00";
                // $json["Responhead"] = "success";
                // $json["Respondesc"] = "Berhasil Menyimpan";
            } else {
                $json["Responcode"] = "01";
                $json["Responhead"] = "error";
                $json["Respondesc"] = "Gagal Menyimpan header resep";
            }
        }

        echo json_encode($json);
    }

    public function tampildetailobat()
    { // Buat cari detil resep
        $episodeid = $this->input->post('episodeid');
        $pasienid = $this->input->post('pasienid');
        $transco = $this->input->post('transco');

        $hasil = $this->om->detailresep($episodeid, $pasienid, $transco);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Detail Resep Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Detail Resep Ditemukan';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function ambilresep()
    { // Buat ambil resep
        $episodeid = $this->input->post('episodeid');
        $pasienid = $this->input->post('pasienid');
        $transco = $this->input->post('transco');

        $hasil = $this->om->detailresep($episodeid, $pasienid, $transco);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Pilih Resep Yang Akan Diambil';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Ambil Resep';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function generateRujukanLanjut($rujukanData)
    {
        if ($rujukanData['rjkn_khusus'] === 'ya') {
            return [
                'tglEstRujuk' => date('d-m-Y', strtotime($rujukanData['tgl_rujuk'])),
                'kdppk' => $rujukanData['rjkn_faskes_khusus'],
                'subSpesialis' => null,
                'khusus' => [
                    'kdKhusus' => $rujukanData['rjkn_poli_khusus'],
                    'kdSubSpesialis' => $rujukanData['rjkn_subspes_khusus'],
                    'catatan' => $rujukanData['rjkn_cttn'],
                ],
            ];
        }

        return [
            'tglEstRujuk' => date('d-m-Y', strtotime($rujukanData['tgl_rujuk'])),
            'kdppk' => $rujukanData['rjkn_faskes'],
            'subSpesialis' => [
                'kdSubSpesialis1' => $rujukanData['rjkn_subspes'],
                'kdSarana' => $rujukanData['rjkn_sarana'],
            ],
            'khusus' => null,
        ];
    }

    public function datapasien()
    {
        $episodeid = $this->input->post('episodeid');
        $pasienid = $this->input->post('pasienid');
        $transco = $this->input->post('transco');

        $hasil = $this->om->datapasien($episodeid, $pasienid, $transco);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Data Pasien Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Data Pasien Ditemukan';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function loadhistory()
    {
        $pasienid = $this->input->post('pasienid');

        $hasil = $this->om->loadhistory($pasienid);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Tidak Ada Riwayat Pemeriksaan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Ada Riwayat Pemeriksaan';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function loadalergi()
    {
        $pasienid = $this->input->post('pasienid');

        $hasil = $this->om->loadalergi($pasienid);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Tidak Ada Riwayat Alergi';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Ada Riwayat Alergi';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function loadsigna()
    {
        $filtercari   = $this->input->post('pencarianobat');

        $hasil = $this->om->carisigna($filtercari);



        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Signa Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Signa Obat Ditemukan';
            $json['Responresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function cariobatms()
    {
        $filtercari   = $this->input->post('pencarianobat');

        $hasil = $this->om->cariobatms($filtercari);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Data Obat Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Data Obat Ditemukan';
            $json['Responeresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function loadpembungkus()
    {
        $filtercari   = $this->input->post('pencarianobat');

        $hasil = $this->om->caribungkus($filtercari);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Pembungkus Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Pembungkus Obat Ditemukan';
            $json['Responresult'] = $hasil;
        }

        echo json_encode($json);
    }
}
