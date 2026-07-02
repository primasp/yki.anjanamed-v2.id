<?php
defined('BASEPATH') or exit('No direct script access allowed');


class RajalController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('RajalModel', 'rm');
    }

    public function registLayan($pasien_id = null)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data['user'] = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/regist-poli.js';

        if ($pasien_id) {
            $data['pasien'] = $this->rm->get_pasien_v2($pasien_id, 'id');
            // Ambil data pertama dari array untuk menghindari error
            $data['pasien'] = !empty($data['pasien']) ? $data['pasien'][0] : null;
        } else {
            $data['pasien'] = null;
        }

        $data['lab_items'] = $this->rm->get_lab_items();
        $data['rad_items'] = $this->rm->get_radiologi_items();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('Rajal/registPoli_v', $data);
        $this->load->view('modals/list_rj_modal'); // <--- Tambahkan ini
        $this->load->view('templates/footer');
    }


    public function batal_booking()
    {
        $this->output->set_content_type('application/json');

        $episode_id = $this->input->post('episode_id', true);
        $lokasi_id  = $this->input->post('lokasi_id', true);

        if (empty($episode_id) || empty($lokasi_id)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        $this->db->trans_begin();

        try {

            $batal = $this->rm->batalBooking($episode_id, $lokasi_id);

            if (!$batal) {
                throw new Exception("Booking tidak ditemukan atau sudah dibatalkan.");
            }

            if ($this->db->trans_status() === false) {
                throw new Exception("Gagal membatalkan booking.");
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'message' => 'Booking berhasil dibatalkan'
            ]);
        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get_booking_list()
    {
        $tgl = $this->input->get('tgl');     // Format: YYYY-MM-DD
        $poli = $this->input->get('poli');   // Optional
        $dokter = $this->input->get('dokter'); // Optional

        $lokasi_id = '001';

        // $result = $this->rm->get_booking_rj([
        //     'tgl'    => $tgl,
        //     'poli'   => $poli,
        //     'dokter' => $dokter,
        //     'lokasi_id'  => $lokasi_id
        // ]);

        $result = $this->rm->get_daftar_rj([
            'tgl'    => $tgl,
            'poli'   => $poli,
            'dokter' => $dokter,
            'lokasi_id'  => $lokasi_id
        ]);



        echo json_encode($result);
    }

    public function cariPasien()
    {
        $term = $this->input->post('term');
        $type = $this->input->post('type');

        if (empty($term)) {
            echo json_encode([]); // Jika tidak ada input, kembalikan array kosong
            return;
        }

        $result = $this->rm->get_pasien($term, $type);

        // return var_dump($result);
        // die;
        if (!empty($result)) {
            echo json_encode($result);
        } else {
            echo json_encode([]);
        }
    }

    public function check_poli()
    {
        $pembiayaan = $this->input->post('pembiayaan');
        $jenis_kunjungan = $this->input->post('jenis_kunjungan');

        $data = $this->rm->getPoliByPembiayaan($pembiayaan);
        echo json_encode($data);
    }

    public function hari_indo($day)
    {
        // Mapping nama hari dari Inggris ke Indonesia
        $days = [
            "Sunday" => "Minggu",
            "Monday" => "Senin",
            "Tuesday" => "Selasa",
            "Wednesday" => "Rabu",
            "Thursday" => "Kamis",
            "Friday" => "Jumat",
            "Saturday" => "Sabtu"
        ];

        // Kembalikan hasil terjemahan, atau nama asli jika tidak ditemukan
        return $days[$day] ?? $day;
    }

    public function md_hari($keterangan)
    {
        $query = "select * from pc01_gen_global_ms where jenis_id = 'JDOWEEK' and keterangan = '$keterangan';";

        return $this->db->query($query)->row();
    }


    public function check_dokter()
    {

        $tgl_berobat = $this->input->post('tgl_berobat');
        $poli_id = $this->input->post('poli_id');
        $hari = $this->hari_indo($this->input->post('hari'));
        $hari_id = $this->md_hari(strtoupper($hari))->global_id;


        $data = $this->rm->getDokterbyTgl($poli_id, $hari_id);

        if (empty($tgl_berobat) || empty($poli_id)) {
            echo json_encode([]);
            return;
        }
        echo json_encode($data);
    }

    public function check_jam_slot()
    {
        $tgl_berobat = $this->input->post('tgl_berobat');
        $poli_id = $this->input->post('poli_id');
        $dokter_id = $this->input->post('dokter_id');
        $hari = $this->hari_indo($this->input->post('day'));
        $hari_id = $this->md_hari(strtoupper($hari))->global_id;

        if (empty($tgl_berobat) || empty($poli_id) || empty($dokter_id) || empty($hari_id)) {
            echo json_encode(['error' => 'Data tidak lengkap.']);
            return;
        }

        $jam_slots = $this->rm->get_jam_slot($tgl_berobat, $poli_id, $dokter_id, $hari_id);

        // Jika tidak ada slot yang tersedia
        if (empty($jam_slots)) {
            echo json_encode([]);
            return;
        }

        // Kembalikan data slot dalam format JSON
        echo json_encode($jam_slots);
    }

    public function proses_regis_layan()
    {
        $this->output->set_content_type('application/json');
        $jenis_kunjungan = $this->input->post('jenis_kunjungan'); // 1 = Poliklinik, 2 = Penunjang
        $pasien_id = $this->input->post('pasien_id');
        $pembiayaan = $this->input->post('pembiayaan');
        $user_id = $this->session->userdata('user_id_pc');

        if (empty($pasien_id)) {
            echo json_encode(['success' => false, 'message' => 'Pasien tidak ditemukan.']);
            return;
        }

        // Ambil nominal pembayaran dari frontend
        $bayar_nanti = $this->input->post('bayar_nanti') ?? 0;
        $metode_bayar   = $this->input->post('metode_bayar') ?? 'TUNDA';
        $uang_bayar     = (float) ($this->input->post('uang_bayar') ?? 0);
        $total_bayar    = (float) ($this->input->post('total_bayar') ?? 0);
        $total_kembali  = (float) ($this->input->post('total_kembali') ?? 0);

        // tambahan diskon
        $discount_type          = trim((string) ($this->input->post('discount_type') ?? ''));
        $discount_value         = (float) ($this->input->post('discount_value') ?? 0);
        $discount_amount_front  = (float) ($this->input->post('discount_amount') ?? 0);
        $total_before_discount  = (float) ($this->input->post('total_before_discount') ?? 0);



        $transaksi_id = $this->rm->buat_transaksi_id()->buat_id_transaksi_id;


        $hitungDiskon = function ($grossTotal, $discountType, $discountValue) {
            $grossTotal = (float) $grossTotal;
            $discountValue = (float) $discountValue;

            $diskPct = 0;
            $diskNominal = 0;

            if ($discountType === 'PERSEN') {
                if ($discountValue < 0) $discountValue = 0;
                if ($discountValue > 100) $discountValue = 100;

                $diskPct = $discountValue;
                $diskNominal = round(($grossTotal * $diskPct) / 100, 2);
            } elseif ($discountType === 'NOMINAL') {
                if ($discountValue < 0) $discountValue = 0;
                if ($discountValue > $grossTotal) $discountValue = $grossTotal;

                $diskNominal = $discountValue;
                $diskPct = $grossTotal > 0 ? round(($diskNominal / $grossTotal) * 100, 4) : 0;
            }

            if ($diskNominal > $grossTotal) {
                $diskNominal = $grossTotal;
            }

            $netTotal = $grossTotal - $diskNominal;
            if ($netTotal < 0) $netTotal = 0;

            return [
                'gross_total'   => $grossTotal,
                'disk_pct'      => $diskPct,
                'disk_nominal'  => $diskNominal,
                'net_total'     => $netTotal,
            ];
        };




        if ($jenis_kunjungan == '2') {


            $idAps = $this->rm->generateIdAps();

            $episode_id = $idAps['episode_id'];
            $trans_id   = $idAps['trans_id'];
            $trans_co   = $idAps['trans_co'];
            $shift_id   = $idAps['shift_id'];

            // ⚙️ Logika baru: jika pembiayaan selain UMUM → bayar_id NULL
            if ($pembiayaan == 'UMUM' && $bayar_nanti != 1) {
                $bayar_id = $idAps['bayar_id'];
            } else {
                $bayar_id = null;
            }

            $lokasi_id = '001';
            $kelas_id  = '6';
            $poli_id   = 'APS';
            $dokter_id = 'APS';
            $rekanan_id = $pembiayaan;
            // $total_harga = 0;
            $status_tr = '0';

            $this->db->trans_begin();

            try {
                // === 1. Episode & Medis ===
                $this->rm->insertEpisodeAps($lokasi_id, $episode_id, $pasien_id, $kelas_id, $poli_id, $dokter_id, $rekanan_id, $status_tr, $user_id);
                $this->rm->insertMedPrwt($lokasi_id, $episode_id, $trans_id, $pasien_id, $poli_id, $dokter_id, $rekanan_id, $user_id);


                // =========================================
                // 1. Kumpulkan semua item dulu
                // =========================================
                $detailItems = [];


                // Pendaftaran
                $tarif_daftar = $this->rm->getTarifPenunjang();

                foreach ($tarif_daftar as $t) {
                    // $harga = (int)$t['harga'];
                    $harga = (float) $t['harga'];
                    // $total_harga += $harga;

                    $detailItems[] = [
                        'layan_id'      => $t['layan_id'],
                        'dokter_id'     => $dokter_id,
                        'qty'           => 1,
                        'harga_satuan'  => $harga,
                        'gross_total'   => $harga,
                        'kategori'      => 'DAFTAR',
                        'is_lab'        => false,
                        'is_rad'        => false,
                    ];
                }


                // LAB
                $lab_item = $this->input->post('lab_item') ?? [];
                $lab_qty  = $this->input->post('lab_qty') ?? [];

                foreach ($lab_item as $i => $layan_id) {
                    if (!$layan_id) continue;

                    $qty   = (int)($lab_qty[$i] ?? 1);
                    if ($qty < 1) $qty = 1;

                    $harga = (float) $this->rm->getHargaLayan($layan_id);
                    $subtotal = $harga * $qty;
                    $kategoriLayan = $this->rm->getKategoriLayan($layan_id);

                    $detailItems[] = [
                        'layan_id'      => $layan_id,
                        'dokter_id'     => $dokter_id,
                        'qty'           => $qty,
                        'harga_satuan'  => $harga,
                        'gross_total'   => $subtotal,
                        'kategori'      => $kategoriLayan,
                        'is_lab'        => ($kategoriLayan == 'JKL-LAB'),
                        'is_rad'        => false,
                    ];
                }

                // RAD
                $rad_item = $this->input->post('rad_item') ?? [];
                $rad_qty  = $this->input->post('rad_qty') ?? [];


                foreach ($rad_item as $i => $layan_id) {
                    if (!$layan_id) continue;

                    $qty = (int)($rad_qty[$i] ?? 1);
                    if ($qty < 1) $qty = 1;

                    $harga = (float) $this->rm->getHargaLayan($layan_id);
                    $subtotal = $harga * $qty;
                    $kategoriLayan = $this->rm->getKategoriLayan($layan_id);

                    $detailItems[] = [
                        'layan_id'      => $layan_id,
                        'dokter_id'     => $dokter_id,
                        'qty'           => $qty,
                        'harga_satuan'  => $harga,
                        'gross_total'   => $subtotal,
                        'kategori'      => $kategoriLayan,
                        'is_lab'        => false,
                        'is_rad'        => ($kategoriLayan == 'JKL-RAD'),
                    ];
                }

                // =========================================
                // 2. Hitung total gross
                // =========================================
                $grossTotal = 0;
                foreach ($detailItems as $it) {
                    $grossTotal += (float) $it['gross_total'];
                }


                // =========================================
                // 3. Hitung diskon header
                // =========================================
                $diskon = $hitungDiskon($grossTotal, $discount_type, $discount_value);

                $total_sub       = $diskon['gross_total'];
                $total_diskpct   = $diskon['disk_pct'];
                $total_diskon    = $diskon['disk_nominal'];
                $total_harga_net = $diskon['net_total'];

                // =========================================
                // 4. Distribusi diskon ke detail proporsional
                // =========================================
                $sumNetDetail = 0;

                foreach ($detailItems as $k => $it) {
                    $itemGross = (float) $it['gross_total'];

                    if ($grossTotal > 0) {
                        $itemDisk = round(($itemGross / $grossTotal) * $total_diskon, 2);
                    } else {
                        $itemDisk = 0;
                    }

                    $itemNet = $itemGross - $itemDisk;
                    if ($itemNet < 0) $itemNet = 0;

                    $detailItems[$k]['disk_pct'] = $total_diskpct;
                    $detailItems[$k]['disk_tot'] = $itemDisk;
                    $detailItems[$k]['harga_total'] = $itemNet;

                    $sumNetDetail += $itemNet;
                }


                // Koreksi rounding item terakhir
                $selisih = round($total_harga_net - $sumNetDetail, 2);
                if (!empty($detailItems) && $selisih != 0) {
                    $lastIndex = count($detailItems) - 1;
                    $detailItems[$lastIndex]['disk_tot'] = round($detailItems[$lastIndex]['disk_tot'] - $selisih, 2);
                    $detailItems[$lastIndex]['harga_total'] = round($detailItems[$lastIndex]['harga_total'] + $selisih, 2);

                    if ($detailItems[$lastIndex]['disk_tot'] < 0) {
                        $detailItems[$lastIndex]['disk_tot'] = 0;
                    }
                    if ($detailItems[$lastIndex]['harga_total'] < 0) {
                        $detailItems[$lastIndex]['harga_total'] = 0;
                    }
                }

                // =========================================
                // 5. Insert detail transaksi
                // =========================================
                foreach ($detailItems as $it) {
                    $data_it = [
                        'lokasi_id'      => $lokasi_id,
                        'episode_id'     => $episode_id,
                        'trans_id'       => $trans_id,
                        'pasien_id'      => $pasien_id,
                        'layan_id'       => $it['layan_id'],
                        'dokter_id'      => $it['dokter_id'],
                        'qty'            => $it['qty'],
                        'harga_satuan'   => $it['harga_satuan'],
                        'disk_pct'       => $it['disk_pct'],
                        'disk_tot'       => $it['disk_tot'],
                        'harga_total'    => $it['harga_total'],
                        'aktif'          => '1',
                        'created_by'     => $user_id,
                        'created_date'   => date('Y-m-d H:i:s'),
                        'tgl_transaksi'  => date('Y-m-d'),
                        'trans_co'       => $trans_co,
                    ];

                    if ($pembiayaan == 'UMUM') {
                        $data_it['total_pribadi'] = $it['harga_total'];
                        $data_it['total_rekanan'] = 0;
                    } else {
                        $data_it['total_pribadi'] = 0;
                        $data_it['total_rekanan'] = $it['harga_total'];
                    }

                    $this->rm->insertTransDetail($data_it);

                    // insert LAB
                    if ($it['is_lab']) {
                        $this->rm->insertLab([
                            'trans_co'       => $trans_co,
                            'trans_id'       => $trans_id,
                            'pasien_id'      => $pasien_id,
                            'tanggal'        => date('Y-m-d'),
                            'test_id'        => $it['layan_id'],
                            'show_item'      => 1,
                            'created_date'   => date('Y-m-d H:i:s'),
                            'created_by'     => $user_id,
                            'episode_id'     => $episode_id,
                            'trans_bayar_id' => $bayar_id ?? null,
                            'cito'           => 'N',
                            'dikerjakan'     => 'T',
                            'iscover'        => 'T',
                            'nilcover'       => 0
                        ]);
                    }


                    // insert RAD
                    if ($it['is_rad']) {
                        $this->rm->insertRad([
                            'trans_co'       => $trans_co,
                            'trans_id'       => $trans_id,
                            'pasien_id'      => $pasien_id,
                            'tanggal'        => date('Y-m-d'),
                            'test_id'        => $it['layan_id'],
                            'kanan'          => 'N',
                            'kiri'           => 'N',
                            'show_item'      => 1,
                            'created_date'   => date('Y-m-d H:i:s'),
                            'created_by'     => $user_id,
                            'episode_id'     => $episode_id,
                            'trans_bayar_id' => $bayar_id ?? null,
                            'iscover'        => 'T',
                            'nilcover'       => 0
                        ]);
                    }
                }



                // =========================================
                // 6. Header transaksi
                // =========================================
                $data_hd = [
                    'lokasi_id'       => $lokasi_id,
                    'episode_id'      => $episode_id,
                    'trans_id'        => $trans_id,
                    'pasien_id'       => $pasien_id,
                    'shift_id'        => $shift_id,
                    'bayar_id'        => $bayar_id,
                    'jenis_tr'        => '009',
                    'kelas_id'        => $kelas_id,
                    'poli_id'         => $poli_id,
                    'dokter_id'       => $dokter_id,
                    'rekanan_id'      => $rekanan_id,
                    'perjanjian_yn'   => 'T',
                    'status_tr'       => '00',
                    'tgl_transaksi'   => date('Y-m-d'),
                    'total_sub'       => $total_sub,
                    'total_diskpct'   => $total_diskpct,
                    'total_diskon'    => $total_diskon,
                    'total_harga'     => $total_harga_net,
                    'aktif'           => '1',
                    'created_by'      => $user_id,
                    'created_date'    => date('Y-m-d H:i:s'),
                    'total_tanggung'  => 0,
                    'sdh_rehitung'    => 'T'
                ];

                if ($pembiayaan == 'UMUM') {
                    $data_hd['total_pribadi'] = $total_harga_net;
                    $data_hd['total_rekanan'] = 0;
                } else {
                    $data_hd['total_pribadi'] = 0;
                    $data_hd['total_rekanan'] = $total_harga_net;
                }

                $this->rm->insertTransHeader($data_hd);

                // =========================================
                // 6.1 Insert pembayaran ke sr01_keu_trnsbayar
                // =========================================

                if ($pembiayaan == 'UMUM' && $bayar_nanti != 1) {
                    // mapping metode bayar → jbayar_id
                    $mapMetode = [
                        'CASH'     => 'CASH',
                        'QRIS'     => 'QRIS',
                        'DEBIT'    => 'DEBIT',
                        'TRANSFER' => 'TRANSFER'
                    ];

                    $jbayar_id = $mapMetode[$metode_bayar] ?? 'LAIN';

                    $data_bayar_trx = [
                        'lokasi_id'     => $lokasi_id,
                        'episode_id'    => $episode_id,
                        'bayar_id'      => $bayar_id,
                        'pasien_id'     => $pasien_id,
                        'jbayar_id'     => $jbayar_id,
                        'keterangan'    => 'Pembayaran ' . $metode_bayar,
                        'pembayaran'    => $uang_bayar > 0 ? $uang_bayar : $total_harga_net,
                        'kartu_no'      => null,
                        'kartu_nama'    => null,
                        'no_konf'       => null,
                        'aktif'         => '1',
                        'created_by'    => $user_id,
                        'created_date'  => date('Y-m-d H:i:s'),
                        'trans_id'      => $trans_id ?? $transaksi_id
                    ];

                    $this->db->insert('pc01_keu_trnsbayar', $data_bayar_trx);
                }


                // =========================================
                // 7. Episode bayar
                // =========================================
                if ($pembiayaan == 'UMUM') {
                    if ($bayar_nanti == 1) {
                        $uang_bayar = 0;
                        $total_kembali = 0;
                        $metode_bayar = 'TUNDA';
                    } else {
                        if ($uang_bayar <= 0 && in_array($metode_bayar, ['QRIS', 'DEBIT', 'TRANSFER'])) {
                            $uang_bayar = $total_harga_net;
                        }

                        if ($metode_bayar === 'CASH' && $uang_bayar < $total_harga_net) {
                            throw new Exception('Uang bayar kurang dari total tagihan setelah diskon.');
                        }

                        $total_kembali = $metode_bayar === 'CASH'
                            ? ($uang_bayar - $total_harga_net)
                            : 0;
                    }

                    $this->rm->insertEpisodeBayar([
                        'lokasi_id'       => $lokasi_id,
                        'episode_id'      => $episode_id,
                        'pasien_id'       => $pasien_id,
                        'bayar_id'        => $bayar_id,
                        'shift_id'        => $shift_id,
                        'rekanan_id'      => $rekanan_id,
                        'tgl_transaksi'   => date('Y-m-d H:i:s'),
                        'tgl_lunas'       => ($bayar_nanti == 1 ? null : date('Y-m-d H:i:s')),
                        'total_harga'     => $total_harga_net,
                        'total_pribadi'   => $total_harga_net,
                        'bayar_pribadi'   => $uang_bayar,
                        'total_kembali'   => $total_kembali,
                        'created_by'      => $user_id,
                        'created_date'    => date('Y-m-d H:i:s'),
                        'jenis_tr'        => 1,
                        'issplit'         => 'T'
                    ]);
                } else {
                    $this->rm->insertEpisodeBayar([
                        'lokasi_id'       => $lokasi_id,
                        'episode_id'      => $episode_id,
                        'pasien_id'       => $pasien_id,
                        'bayar_id'        => $bayar_id,
                        'shift_id'        => $shift_id,
                        'rekanan_id'      => $rekanan_id,
                        'tgl_transaksi'   => date('Y-m-d H:i:s'),
                        'tgl_lunas'       => date('Y-m-d H:i:s'),
                        'total_harga'     => $total_harga_net,
                        'total_rekanan'   => $total_harga_net,
                        'bayar_rekanan'   => $total_harga_net,
                        'created_by'      => $user_id,
                        'created_date'    => date('Y-m-d H:i:s'),
                        'jenis_tr'        => 1,
                        'issplit'         => 'T'
                    ]);
                }

                $this->db->trans_commit();

                echo json_encode([
                    'success'               => true,
                    'message'               => "Registrasi penunjang berhasil (pembiayaan: $pembiayaan)",
                    'episode_id'            => $episode_id,
                    'NoAntrianKlinik'       => '-',
                    'tgl_berobat'           => date('Y-m-d'),
                    'total_before_discount' => $total_sub,
                    'discount_type'         => $discount_type,
                    'discount_value'        => $discount_value,
                    'discount_amount'       => $total_diskon,
                    'total_harga'           => $total_harga_net,
                    'uang_bayar'            => $uang_bayar,
                    'total_kembali'         => $total_kembali,
                    'metode_bayar'          => $metode_bayar
                ]);




                // echo json_encode([
                //     'success' => true,
                //     'message' => "Registrasi penunjang berhasil (pembiayaan: $pembiayaan)",
                //     'episode_id' => $episode_id,
                //     'NoAntrianKlinik' => '-', // placeholder agar URL sama
                //     'tgl_berobat' => date('Y-m-d'),
                //     'total_harga' => $total_harga,
                //     'uang_bayar' => $uang_bayar,
                //     'total_kembali' => $total_kembali,
                //     'metode_bayar' => $metode_bayar
                // ]);
            } catch (Exception $e) {
                $this->db->trans_rollback();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            // === Poliklinik ===
            $poli = $this->input->post('poli');
            $dokter_id = $this->input->post('dokter');
            $tgl_berobat = convertDateTime($this->input->post('tgl_berobat'), 'isoDate', 'slashDate');
            $jam_slot = $this->input->post('jam_slot');

            $episode_id = $this->rm->buat_episode_id('1')->buat_id_episode_id;
            $transaksi_id = $this->rm->buat_transaksi_id()->buat_id_transaksi_id;
            $bayar_id     = ($pembiayaan == 'UMUM' && $bayar_nanti != 1)
                ? $this->rm->buat_bayar_id('1')->buat_id_bayar_id
                : null;
            // $bayar_id = ($pembiayaan == 'UMUM') ? $this->rm->buat_bayar_id('1')->buat_id_bayar_id : null;

            $jam_slot_raw = $this->input->post('jam_slot');

            $antrian   = null;
            $jam_awal  = null;
            $jam_akhir = null;
            $hari_id   = null;
            $antrian_id = null;

            if (!empty($jam_slot_raw)) {
                $jam_slot = json_decode($jam_slot_raw, true);


                if (json_last_error() === JSON_ERROR_NONE) {
                    $antrian     = $jam_slot['antrian'];
                    $jam_awal   = $jam_slot['jam_mulai'];
                    $jam_akhir = $jam_slot['jam_selesai'];
                    $hari_id = $jam_slot['hari_id'];
                    $antrian_id = $jam_slot['antrian_id'];

                    log_message('debug', "Antrian: $antrian, Jam Mulai: $jam_awal, Jam Selesai: $jam_akhir");
                }
            }
            // return var_dump($jam_awal);
            // die;

            $dataReg = [
                'jenis_kunjungan' => $jenis_kunjungan,
                'tgl_berobat' => $tgl_berobat,
                'poli' => $poli,
                'dokter_id' => $dokter_id,
                'jam_awal' => $jam_awal,
                'jam_akhir' => $jam_akhir,
                'hari_id' => $hari_id,
                'antrian_id' => $antrian_id,
                'antrian' => $antrian,
            ];

            $this->db->trans_begin();

            try {
                // =========================================
                // 1. Registrasi poli seperti existing
                // =========================================
                $antrianData = $this->rm->getAntrian(
                    $dataReg['tgl_berobat'],
                    $dataReg['hari_id'],
                    $dataReg['antrian_id'],
                    $dataReg['poli'],
                    $dataReg['dokter_id']
                );
                $antrianKlinik = $antrianData->antrian;
                $noUrut = "";

                $this->rm->registPoliPasien(
                    $user_id,
                    $dataReg,
                    $antrianData,
                    $noUrut,
                    $episode_id,
                    $transaksi_id,
                    $pasien_id,
                    $pembiayaan,
                    ''
                );



                $lokasi_id  = '001';
                $shift_id   = $this->session->userdata('shift_id_pc') ?? null;
                $kelas_id   = '6';
                $rekanan_id = $pembiayaan;

                $tarif_poli = $this->rm->getTarifPoli($poli);

                $detailItems = [];


                foreach ($tarif_poli as $t) {
                    $harga = (float) ($t['harga'] ?? 0);
                    $layan_id = $t['layan_id'] ?? null;

                    if (!$layan_id) continue;

                    $detailItems[] = [
                        'layan_id'     => $layan_id,
                        'dokter_id'    => $dokter_id,
                        'qty'          => 1,
                        'harga_satuan' => $harga,
                        'gross_total'  => $harga,
                        'kategori'     => $t['kategori_id'] ?? 'POLI',
                    ];
                }



                // =========================================
                // 3. Hitung total gross
                // =========================================
                $grossTotal = 0;
                foreach ($detailItems as $it) {
                    $grossTotal += (float) $it['gross_total'];
                }


                // =========================================
                // 4. Hitung diskon header
                // =========================================
                $diskon = $hitungDiskon($grossTotal, $discount_type, $discount_value);

                $total_sub       = $diskon['gross_total'];
                $total_diskpct   = $diskon['disk_pct'];
                $total_diskon    = $diskon['disk_nominal'];
                $total_harga_net = $diskon['net_total'];


                // =========================================
                // 5. Distribusi diskon ke detail proporsional
                // =========================================
                $sumNetDetail = 0;

                foreach ($detailItems as $k => $it) {
                    $itemGross = (float) $it['gross_total'];

                    if ($grossTotal > 0) {
                        $itemDisk = round(($itemGross / $grossTotal) * $total_diskon, 2);
                    } else {
                        $itemDisk = 0;
                    }

                    $itemNet = $itemGross - $itemDisk;
                    if ($itemNet < 0) {
                        $itemNet = 0;
                    }

                    $detailItems[$k]['disk_pct']    = $total_diskpct;
                    $detailItems[$k]['disk_tot']    = $itemDisk;
                    $detailItems[$k]['harga_total'] = $itemNet;

                    $sumNetDetail += $itemNet;
                }



                // Koreksi rounding item terakhir
                $selisih = round($total_harga_net - $sumNetDetail, 2);
                if (!empty($detailItems) && $selisih != 0) {
                    $lastIndex = count($detailItems) - 1;

                    $detailItems[$lastIndex]['disk_tot'] =
                        round(($detailItems[$lastIndex]['disk_tot'] ?? 0) - $selisih, 2);

                    $detailItems[$lastIndex]['harga_total'] =
                        round(($detailItems[$lastIndex]['harga_total'] ?? 0) + $selisih, 2);

                    if ($detailItems[$lastIndex]['disk_tot'] < 0) {
                        $detailItems[$lastIndex]['disk_tot'] = 0;
                    }

                    if ($detailItems[$lastIndex]['harga_total'] < 0) {
                        $detailItems[$lastIndex]['harga_total'] = 0;
                    }
                }





                // =========================================
                // 6. Insert detail transaksi
                // =========================================
                foreach ($detailItems as $it) {
                    $data_it = [
                        'lokasi_id'     => $lokasi_id,
                        'episode_id'    => $episode_id,
                        'trans_id'      => $transaksi_id,
                        'pasien_id'     => $pasien_id,
                        'layan_id'      => $it['layan_id'],
                        'dokter_id'     => $it['dokter_id'],
                        'qty'           => $it['qty'],
                        'harga_satuan'  => $it['harga_satuan'],
                        'disk_pct'      => $it['disk_pct'] ?? 0,
                        'disk_tot'      => $it['disk_tot'] ?? 0,
                        'harga_total'   => $it['harga_total'] ?? $it['gross_total'],
                        'aktif'         => '1',
                        'created_by'    => $user_id,
                        'created_date'  => date('Y-m-d H:i:s'),
                        'tgl_transaksi' => date('Y-m-d'),
                    ];

                    if ($pembiayaan == 'UMUM') {
                        $data_it['total_pribadi'] = $data_it['harga_total'];
                        $data_it['total_rekanan'] = 0;
                    } else {
                        $data_it['total_pribadi'] = 0;
                        $data_it['total_rekanan'] = $data_it['harga_total'];
                    }

                    $this->rm->insertTransDetail($data_it);
                }


                // =========================================
                // 7. Insert header transaksi
                // =========================================
                // $data_hd = [
                //     'lokasi_id'      => $lokasi_id,
                //     'episode_id'     => $episode_id,
                //     'trans_id'       => $transaksi_id,
                //     'pasien_id'      => $pasien_id,
                //     'shift_id'       => $shift_id,
                //     'bayar_id'       => $bayar_id,
                //     'jenis_tr'       => '001', // sesuaikan jika kode jenis transaksi poli berbeda
                //     'kelas_id'       => $kelas_id,
                //     'poli_id'        => $poli,
                //     'dokter_id'      => $dokter_id,
                //     'rekanan_id'     => $rekanan_id,
                //     'perjanjian_yn'  => 'T',
                //     'status_tr'      => '00',
                //     'tgl_transaksi'  => date('Y-m-d'),
                //     'total_sub'      => $total_sub,
                //     'total_diskpct'  => $total_diskpct,
                //     'total_diskon'   => $total_diskon,
                //     'total_harga'    => $total_harga_net,
                //     'aktif'          => '1',
                //     'created_by'     => $user_id,
                //     'created_date'   => date('Y-m-d H:i:s'),
                //     'total_tanggung' => 0,
                //     'sdh_rehitung'   => 'T'
                // ];



                // if ($pembiayaan == 'UMUM') {
                //     $data_hd['total_pribadi'] = $total_harga_net;
                //     $data_hd['total_rekanan'] = 0;
                // } else {
                //     $data_hd['total_pribadi'] = 0;
                //     $data_hd['total_rekanan'] = $total_harga_net;
                // }

                // =========================================
                // 7. Update header transaksi existing
                // =========================================
                if ($pembiayaan == 'UMUM') {
                    $total_pribadi = $total_harga_net;
                    $total_rekanan = 0;
                } else {
                    $total_pribadi = 0;
                    $total_rekanan = $total_harga_net;
                }

                $this->rm->updateTransHeaderPoli($lokasi_id, $episode_id, $transaksi_id, [
                    'bayar_id'          => $bayar_id,
                    'rekanan_id'        => $rekanan_id,
                    'status_tr'         => '00',
                    'total_sub'         => $total_sub,
                    'total_diskpct'     => $total_diskpct,
                    'total_diskon'      => $total_diskon,
                    'total_harga'       => $total_harga_net,
                    'total_pribadi'     => $total_pribadi,
                    'total_rekanan'     => $total_rekanan,
                    'total_tanggung'    => 0,
                    'sdh_rehitung'      => 'T',
                    'last_updated_by'   => $user_id,
                    'last_updated_date' => date('Y-m-d H:i:s')
                ]);



                // $this->rm->insertTransHeader($data_hd);

                // return var_dump($data_hd);
                // die;
                // =========================================
                // 8. Insert episode bayar
                // =========================================
                if ($pembiayaan == 'UMUM') {
                    if ($bayar_nanti == 1) {
                        $uang_bayar = 0;
                        $total_kembali = 0;
                        $metode_bayar = 'TUNDA';
                    } else {
                        if ($uang_bayar <= 0 && in_array($metode_bayar, ['QRIS', 'DEBIT'])) {
                            $uang_bayar = $total_harga_net;
                        }

                        if ($metode_bayar === 'CASH' && $uang_bayar < $total_harga_net) {
                            throw new Exception('Uang bayar kurang dari total tagihan setelah diskon.');
                        }

                        $total_kembali = ($metode_bayar === 'CASH')
                            ? ($uang_bayar - $total_harga_net)
                            : 0;
                    }

                    $data_bayar = [
                        'bayar_id'          => $bayar_id,
                        'rekanan_id'        => $rekanan_id,
                        'tgl_transaksi'     => date('Y-m-d H:i:s'),
                        'tgl_lunas'         => ($bayar_nanti == 1 ? null : date('Y-m-d H:i:s')),
                        'total_harga'       => $total_harga_net,
                        'total_pribadi'     => $total_harga_net,
                        'total_rekanan'     => 0,
                        'bayar_pribadi'     => $uang_bayar,
                        'bayar_rekanan'     => 0,
                        'total_kembali'     => $total_kembali,
                        'last_updated_by'   => $user_id,
                        'last_updated_date' => date('Y-m-d H:i:s')
                    ];
                } else {
                    $total_kembali = 0;

                    $data_bayar = [
                        'bayar_id'          => $bayar_id,
                        'rekanan_id'        => $rekanan_id,
                        'tgl_transaksi'     => date('Y-m-d H:i:s'),
                        'tgl_lunas'         => date('Y-m-d H:i:s'),
                        'total_harga'       => $total_harga_net,
                        'total_pribadi'     => 0,
                        'total_rekanan'     => $total_harga_net,
                        'bayar_pribadi'     => 0,
                        'bayar_rekanan'     => $total_harga_net,
                        'total_kembali'     => 0,
                        'last_updated_by'   => $user_id,
                        'last_updated_date' => date('Y-m-d H:i:s')
                    ];
                }


                $this->rm->updateEpisodeBayarPoli($lokasi_id, $episode_id, $pasien_id, $data_bayar);
                $this->db->trans_commit();
                // return var_dump($tarif_poli);
                // die;



                // $antrian = $this->rm->getAntrian($data['tgl_berobat'], $data['hari_id'], $data['antrian_id'], $data['poli'], $data['dokter_id']);
                // $antrianKlinik = $antrian->antrian;

                // $noUrut =  "";
                // $this->rm->registPoliPasien($user_id, $data, $antrian, $noUrut, $episode_id, $transaksi_id, $pasien_id, $pembiayaan, ''); //ORI
                // return var_dump("oke");
                // die;


                $data = [
                    'metadata' => [
                        'responCode' => '00',
                        'responDesc' => 'Registrasi Berhasil No. Antrian : ' . $antrianKlinik,
                    ],
                    'NoAntrianKlinik'       => $antrianKlinik,
                    'episode_id'            => $episode_id,
                    'tgl_berobat'           => $dataReg['tgl_berobat'],
                    'total_before_discount' => $total_sub,
                    'discount_type'         => $discount_type,
                    'discount_value'        => $discount_value,
                    'discount_amount'       => $total_diskon,
                    'total_harga'           => $total_harga_net,
                    'uang_bayar'            => $uang_bayar,
                    'total_kembali'         => $total_kembali,
                    'metode_bayar'          => $metode_bayar
                ];
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                $data = [
                    'message' => $e->getMessage(),
                    'status' => false
                ];
            }
            echo json_encode($data);
        }
    }


    public function cekRiwayatProgram()
    {
        $pasien_id = $this->input->post('pasien_id');
        $jenis_kunjungan = $this->input->post('jenis_kunjungan');

        $lab_item = $this->input->post('lab_item');
        $rad_item = $this->input->post('rad_item');
        $layan_ids = array_merge($lab_item ?? [], $rad_item ?? []);
        $tahun = date('Y');

        $ada = $this->db->query("SELECT COUNT(*) AS jml 
            FROM pc01_keu_transctr_it a 
            join pc01_keu_episode b on a.episode_id =b.episode_id 
            WHERE a.pasien_id = ? 
              AND a.layan_id IN ?
              AND EXTRACT(YEAR FROM a.tgl_transaksi) = ? and b.status_episode in ('55','00') 
        ", [$pasien_id, $layan_ids, $tahun])->row()->jml ?? 0;

        echo json_encode([
            'sudah_pernah' => $ada > 0,
            'message' => $ada > 0 ? 'Pasien sudah pernah melakukan pemeriksaan ini tahun ini.' : ''
        ]);
    }


    public function getTarifByJenis($jenis_kunjungan)
    {
        $poli_id = $this->input->get('poli_id');

        if ($jenis_kunjungan == '1') {
            // Poliklinik → pendaftaran + pemeriksaan
            // $data = $this->rm->getTarifPoli();

            $data = $this->rm->getTarifPoliByJenis($poli_id);
        } elseif ($jenis_kunjungan == '2') {
            // Penunjang → hanya pendaftaran
            $data = $this->rm->getTarifPenunjang();
        } else {
            $data = [];
        }

        echo json_encode(['data' => $data]);
    }
}
