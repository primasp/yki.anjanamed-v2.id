<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MasterModel extends CI_Model
{
    public function getGolonganObat()
    {
        $this->db->select('golobat_id, keterangan');
        $this->db->from('pc01_frm_golobat_ms');
        return $this->db->get()->result();
    }

    public function getSediaanObat()
    {
        $this->db->select('sediaan_id, keterangan');
        $this->db->from('pc01_frm_sediaan_ms');
        return $this->db->get()->result();
    }

    public function getSatuanObat()
    {
        $this->db->select('satuan_id, keterangan');
        $this->db->from('pc01_frm_satuan_ms');
        return $this->db->get()->result();
    }

    public function getGenerikObat()
    {
        $this->db->select('generik_id, keterangan');
        $this->db->from('pc01_frm_generik_ms');
        return $this->db->get()->result();
    }

    public function getPabrikObat()
    {
        $this->db->select('pabrik_id, keterangan');
        $this->db->from('pc01_frm_pabrik_ms');
        return $this->db->get()->result();
    }

    public function getRouteObat()
    {
        $this->db->select('routeobt_id, keterangan');
        $this->db->from('pc01_frm_routeobt_ms');
        return $this->db->get()->result();
    }

    public function getAllObat($gol_obat = null, $search = null)
    {
        $sql = "SELECT 
                    a.obat_id, 
                    a.nama, 
                    a.konversi_satuan, 
                    b.keterangan AS satuan_jual, 
                    c.keterangan AS satuan_beli, 
                    a.harga_hna, 
                    a.harga_hna_ppn, 
                    a.harga, 
                    a.harga_sat_ppn, 
                    a.gol_obat, 
                    a.harga_jual,
                    g.gudang_id,
                    d.keterangan AS nama_gudang,  
                    g.salwal AS saldo_awal,
                    g.mutbl AS mutasi_beli,
                    g.mutjl AS mutasi_jual,
                    (g.salwal + g.mutbl - g.mutjl) AS sisa_stok
                FROM pc01_frm_obat_ms a
                LEFT JOIN pc01_frm_satuan_ms b ON a.satuan_kecil_id = b.satuan_id
                LEFT JOIN pc01_frm_satuan_ms c ON a.satuan_besar_id = c.satuan_id
                LEFT JOIN pc01_frm_gudang_stok g ON a.obat_id = g.obat_id
                LEFT JOIN pc01_frm_gudang_ms d ON g.gudang_id = d.gudang_id  
                WHERE a.aktif = '1'";

        // Parameter untuk binding
        $params = [];

        // Filter gol_obat jika bukan null dan bukan "SEMUA"
        if (!empty($gol_obat) && strtolower($gol_obat) !== 'semua') {
            $sql .= " AND LOWER(a.gol_obat) = LOWER(?)";
            $params[] = $gol_obat;
        }

        // Filter pencarian jika ada input search
        if (!empty($search)) {
            $sql .= " AND (LOWER(a.nama) LIKE ? 
                        OR LOWER(a.obat_id) LIKE ?
                        OR LOWER(b.keterangan) LIKE ?
                        OR LOWER(c.keterangan) LIKE ?
                        OR LOWER(d.keterangan) LIKE ?)";
            $searchTerm = '%' . strtolower($search) . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        // $sql .= " ORDER BY a.nama DESC";
        $sql .= "ORDER BY sisa_stok ASC, a.nama ASC";

        // Jalankan query dengan binding parameter
        $query = $this->db->query($sql, $params);

        return $query->result();
    }


    public function insertObatxxxx($data)
    {
        $insertData = [
            'nama' => $data['nama_obat'],
            'generik_id' => $data['generik_obat'],
            'gol_obat' => $data['gol_obat'],
            'pabrik_id' => $data['pabrik'],
            'route_id' => $data['route'],
            'satuan_kecil_id' => $data['satuan_jual'],
            'satuan_besar_id' => $data['satuan_beli'],
            'konversi_satuan' => $data['kon_satuan'],
            'harga_hna' => $data['hna_besar'],
            'harga_hna_ppn' => $data['hna_ppn_besar'],
            'harga' => $data['jual_sat'],
            'harga_sat_ppn' => $data['jual_sat_hna_ppn'],
            'harga_jual' => $data['harga_final'],
            'is_margin' => isset($data['harga_final_checkbox']) ? '1' : '0',
            'obat_fornas' => isset($data['obat_fornas']) ? 'Y' : 'N',
            'is_produksi' => isset($data['obat_produksi']) ? 'Y' : 'N',
            'ecatalogue' => $data['ecatalogue'],
            'batasan_fornas' => $data['batasan_fornas'],
            'no_registrasi' => $data['nomor_registrasi'],
            'created_by' => $this->session->userdata('user_id_pc'),
            'created_date' => date('Y-m-d'),
            'aktif' => '1'
        ];

        $this->db->trans_begin();

        // --- INSERT ke obat + ambil obat_id (PostgreSQL) ---
        // CI Query Builder tidak punya returning langsung, jadi pakai query manual.

        $sql = "INSERT INTO pcare_manager.pc01_frm_obat_ms
            (nama, generik_id, gol_obat, pabrik_id, route_id, satuan_kecil_id, satuan_besar_id,
             konversi_satuan, harga_hna, harga_hna_ppn, harga, harga_sat_ppn, harga_jual,
             is_margin, obat_fornas, is_produksi, ecatalogue, batasan_fornas, no_registrasi,
             created_by, created_date, aktif)
            VALUES
            (?, ?, ?, ?, ?, ?, ?,
             ?, ?, ?, ?, ?, ?,
             ?, ?, ?, ?, ?, ?,
             ?, ?, ?)
            RETURNING obat_id";

        $params = [
            $insertData['nama'],
            $insertData['generik_id'],
            $insertData['gol_obat'],
            $insertData['pabrik_id'],
            $insertData['route_id'],
            $insertData['satuan_kecil_id'],
            $insertData['satuan_besar_id'],
            $insertData['konversi_satuan'],
            $insertData['harga_hna'],
            $insertData['harga_hna_ppn'],
            $insertData['harga'],
            $insertData['harga_sat_ppn'],
            $insertData['harga_jual'],
            $insertData['is_margin'],
            $insertData['obat_fornas'],
            $insertData['is_produksi'],
            $insertData['ecatalogue'],
            $insertData['batasan_fornas'],
            $insertData['no_registrasi'],
            $insertData['created_by'],
            $insertData['created_date'],
            $insertData['aktif']
        ];

        $q = $this->db->query($sql, $params);

        if (!$q) {
            $this->db->trans_rollback();
            return false;
        }

        $row = $q->row_array();
        $obat_id = $row['obat_id'] ?? null;

        if (!$obat_id) {
            $this->db->trans_rollback();
            return false;
        }

        // --- INSERT ke gudang stok (default salwal=0, gudang_id fixed) ---
        $stokData = [
            'lokasi_id'   => '001',
            'obat_id'     => $obat_id,
            'nama_obat'   => $insertData['nama'],
            'satuan_jl'   => $insertData['satuan_kecil_id'], // atau kalau mau teks satuan, isi dari master satuan
            'satuan_bl'   => $insertData['satuan_besar_id'],
            'isi_bl'      => $insertData['konversi_satuan'],
            'salwal'      => 0,
            'hna'         => $insertData['harga_hna'],
            'hna_ppn'     => $insertData['harga_hna_ppn'],
            'generik_id'  => $insertData['generik_id'],
            'pabrik_id'   => $insertData['pabrik_id'],
            'golobat_id'  => $insertData['gol_obat'],
            'gudang_id'   => 'DEPO00000000APT',
            'aktif'       => '1',
            'created_date' => date('Y-m-d'),
            'created_by'  => $this->session->userdata('user_id_pc'),
            'periode'     => date('Ym')
        ];

        $okStok = $this->db->insert('pcare_manager.pc01_frm_gudang_stok', $stokData);

        if (!$okStok) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;

        // return $this->db->insert('pc01_frm_obat_ms', $insertData);
    }


    public function insertObat($data)
    {
        $lokasi_id  = isset($data['lokasi_id']) && $data['lokasi_id'] !== ''
            ? $data['lokasi_id']
            : $this->session->userdata('lokasi_id_pc');

        $created_by = isset($data['created_by']) && $data['created_by'] !== ''
            ? $data['created_by']
            : $this->session->userdata('user_id_pc');

        $gudang_id = isset($data['gudang_id']) && $data['gudang_id'] !== ''
            ? $data['gudang_id']
            : 'DEPO00000000APT';

        $stok_awal = isset($data['stok_awal']) && $data['stok_awal'] !== ''
            ? (float) $data['stok_awal']
            : 0;

        /*
     * Helper ambil value dengan beberapa kemungkinan nama field.
     */
        $pick = function ($keys, $default = null) use ($data) {
            foreach ($keys as $key) {
                if (isset($data[$key]) && $data[$key] !== '') {
                    return $data[$key];
                }
            }

            return $default;
        };

        $num = function ($value) {
            if ($value === null || $value === '') {
                return 0;
            }

            return (float) $value;
        };

        $checked = function ($keys) use ($data) {
            foreach ($keys as $key) {
                if (isset($data[$key])) {
                    $val = $data[$key];

                    if ($val === 'on' || $val === '1' || $val === 1 || $val === true || $val === 'Y') {
                        return true;
                    }
                }
            }

            return false;
        };

        /*
     * Mapping dari form ke tabel master obat.
     */
        $nama_obat = trim($pick(['nama_obat', 'nama'], ''));

        // if ($nama_obat === '') {
        //     return false;
        // }
        if ($nama_obat === '') {
            return [
                'status'  => 'error',
                'message' => 'Nama obat wajib diisi.'
            ];
        }

        $generik_id      = $pick(['generik_obat', 'generik_id'], null);
        $gol_obat        = $pick(['gol_obat', 'golongan_obat'], null);
        $sediaan_id      = $pick(['sediaan_obat', 'sediaan_id', 'sediaan'], null);
        $pabrik_id       = $pick(['pabrik_obat', 'pabrik_id', 'pabrik'], null);
        $route_id        = $pick(['route_obat', 'route_id', 'route'], null);

        $satuan_kecil_id = $pick(['satuan_jual', 'satuan_kecil_id'], null);
        $satuan_besar_id = $pick(['satuan_beli', 'satuan_besar_id'], null);

        $konversi_satuan = $num($pick(['kon_satuan', 'konversi_satuan'], 0));

        $harga_hna       = $num($pick(['hna_besar', 'harga_hna'], 0));
        $harga_hna_ppn   = $num($pick(['hna_ppn_besar', 'harga_hna_ppn'], 0));
        $harga_satuan    = $num($pick(['jual_sat', 'harga'], 0));
        $harga_sat_ppn   = $num($pick(['jual_sat_hna_ppn', 'harga_sat_ppn'], 0));
        $harga_jual      = $num($pick(['harga_final', 'harga_jual'], 0));

        $ecatalogue      = $pick(['ecatalogue', 'obat_ecatalogue'], null);
        $batasan_fornas  = $pick(['batasan_fornas'], null);
        $no_registrasi   = $pick(['nomor_registrasi', 'no_registrasi'], null);

        /*
     * Checkbox.
     */
        $obat_fornas   = $checked(['obat_fornas']) ? 'Y' : 'N';
        $obat_hialert  = $checked(['obat_hialert', 'obat_high_alert']) ? 'Y' : 'N';
        $is_produksi   = $checked(['obat_produksi', 'is_produksi']) ? 'Y' : 'N';
        $is_pembungkus = $checked(['is_pembungkus', 'pembungkus']) ? 'Y' : 'N';

        /*
     * ALKES:
     * jenis default O = Obat.
     * Kalau checkbox alkes dicentang, jenis = A.
     */
        $jenis = $checked(['alkes']) ? 'A' : 'O';

        /*
     * TIDAK UNTUK PASIEN BPJS:
     * Mapping ke mandiri = 1.
     * Jika di sistem Bapak maknanya berbeda, tinggal sesuaikan field ini.
     */
        $mandiri = $checked(['obat_bpjs', 'tidak_bpjs']) ? '1' : '0';

        /*
     * SEMBUNYIKAN OBAT:
     * Jangan nonaktifkan obat, cukup tampil = 0.
     */
        $tampil = $checked(['semuaykn_obat', 'sembunyikan_obat', 'hide_obat']) ? '0' : '1';

        /*
     * Gunakan margin 20% atau tidak.
     * Field tabel is_margin bertipe boolean.
     */
        // $is_margin = $checked(['harga_final_checkbox', 'is_margin']) ? 'true' : 'false';
        $is_margin = $checked(['harga_final_checkbox', 'is_margin']) ? true : false;
        $this->db->trans_begin();

        /*
        * Generate obat_id manual.
        * Jangan pakai RETURNING karena di CI3 PostgreSQL bisa result object kosong.
        */
        $getId = $this->db->query("
                    SELECT pcare_manager.generate_id(
                        'OBT',
                        15,
                        'pc01_frm_obat_ms',
                        'obat_id'
                    ) AS obat_id
                ");

        if ($getId === false || empty($getId->row()) || empty($getId->row()->obat_id)) {
            $error = $this->db->error();
            $this->db->trans_rollback();

            return [
                'status'  => 'error',
                'message' => 'Gagal generate obat_id: ' . ($error['message'] ?? 'Unknown error')
            ];
        }

        $obat_id = $getId->row()->obat_id;

        $sqlObat = "INSERT INTO pcare_manager.pc01_frm_obat_ms
        (
            lokasi_id,
            obat_id,
            nama,
            generik_id,
            gol_obat,
            sediaan_id,
            pabrik_id,
            route_id,
            satuan_kecil_id,
            satuan_besar_id,
            konversi_satuan,
            harga_hna,
            harga_hna_ppn,
            harga,
            harga_sat_ppn,
            harga_jual,
            is_margin,
            obat_fornas,
            obat_hialert,
            is_produksi,
            is_pembungkus,
            jenis,
            mandiri,
            tampil,
            ecatalogue,
            batasan_fornas,
            no_registrasi,
            aktif,
            created_by,
            created_date
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, '1', ?, CURRENT_DATE
        )
        RETURNING obat_id
        ";

        $paramsObat = [
            $lokasi_id,
            $obat_id,
            $nama_obat,
            $generik_id,
            $gol_obat,
            $sediaan_id,
            $pabrik_id,
            $route_id,
            $satuan_kecil_id,
            $satuan_besar_id,
            $konversi_satuan,
            $harga_hna,
            $harga_hna_ppn,
            $harga_satuan,
            $harga_sat_ppn,
            $harga_jual,
            $is_margin,
            $obat_fornas,
            $obat_hialert,
            $is_produksi,
            $is_pembungkus,
            $jenis,
            $mandiri,
            $tampil,
            $ecatalogue,
            $batasan_fornas,
            $no_registrasi,
            $created_by
        ];

        // $queryObat = $this->db->query($sqlObat, $paramsObat);

        // if (!$queryObat) {
        //     $this->db->trans_rollback();
        //     return false;
        // }

        // $rowObat = $queryObat->row();

        $queryObat = $this->db->query($sqlObat, $paramsObat);

        if ($queryObat === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();

            return [
                'status'  => 'error',
                'message' => 'Insert master obat gagal: ' . ($error['message'] ?? 'Unknown error'),
                'debug'   => $error
            ];
        }

        // return var_dump($queryObat);
        // die;

        // if (!is_object($queryObat)) {
        //     $error = $this->db->error();
        //     $this->db->trans_rollback();

        //     return [
        //         'status'  => 'error',
        //         'message' => 'Insert master obat gagal: ' . ($error['message'] ?? 'Unknown error'),
        //         'debug'   => $error
        //     ];
        // }
        // $rowObat = $queryObat->row();

        // if (empty($rowObat) || empty($rowObat->obat_id)) {
        //     $this->db->trans_rollback();
        //     return false;
        // }

        // $obat_id = $rowObat->obat_id;

        /*
     * Insert stok awal ke pc01_frm_gudang_stok.
     * Stok awal masuk ke salwal.
     * adjust tetap 0.
     */
        $sqlStok = "INSERT INTO pcare_manager.pc01_frm_gudang_stok
        (
            lokasi_id,
            obat_id,
            nama_obat,
            satuan_jl,
            satuan_bl,
            isi_bl,
            is_resep,
            is_anfrah,
            salwal,
            mutbl,
            mutjl,
            retbl,
            retjl,
            promsk,
            proklr,
            adjust,
            hna,
            hna_ppn,
            hna_satuan,
            generik_id,
            pabrik_id,
            golobat_id,
            gudang_id,
            aktif,
            created_date,
            created_by,
            periode,
            is_penuh
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?,
            'Y',
            'N',
            ?, 0, 0, 0, 0, 0, 0, 0,
            ?, ?, ?,
            ?, ?, ?,
            ?,
            '1',
            CURRENT_DATE,
            ?,
            ?,
            'Y'
        )
        ";

        $paramsStok = [
            $lokasi_id,
            $obat_id,
            $nama_obat,
            $satuan_kecil_id,
            $satuan_besar_id,
            $konversi_satuan,
            $stok_awal,
            $harga_hna,
            $harga_hna_ppn,
            $harga_satuan,
            $generik_id,
            $pabrik_id,
            $gol_obat,
            $gudang_id,
            $created_by,
            date('Ym')
        ];

        $queryStok = $this->db->query($sqlStok, $paramsStok);


        if ($queryStok === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();

            return [
                'status'  => 'error',
                'message' => 'Insert stok obat gagal: ' . ($error['message'] ?? 'Unknown error'),
                'debug'   => $error
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();

            return [
                'status'  => 'error',
                'message' => 'Transaksi tambah obat gagal: ' . ($error['message'] ?? 'Unknown error'),
                'debug'   => $error
            ];
        }



        $this->db->trans_commit();

        return [
            'obat_id'    => $obat_id,
            'nama_obat'  => $nama_obat,
            'lokasi_id'  => $lokasi_id,
            'gudang_id'  => $gudang_id,
            'stok_awal'  => $stok_awal
        ];
    }


    public function getObatByIdxx($obat_id)
    {
        return $this->db->get_where('pc01_frm_obat_ms', ['obat_id' => $obat_id])->row();
    }

    public function getObatById($lokasi_id, $obat_id)
    {
        return $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('obat_id', $obat_id)
            ->where('aktif', '1')
            ->get('pcare_manager.pc01_frm_obat_ms')
            ->row_array();
    }

    public function updateObatxxxx($data)
    {
        $updateData = [
            'nama' => $data['nama_obat'],
            'konversi_satuan' => $data['edit_kon_satuan'],
            'harga_hna' => $data['edit_hna_besar'],
            'harga_hna_ppn' => $data['edit_hna_ppn_besar'],
            'harga' => $data['edit_jual_sat'],
            'harga_sat_ppn' => $data['edit_jual_sat_hna_ppn'],
            'harga_jual' => $data['harga_edit_final'],
            'is_margin' => isset($data['harga_edit_final_checkbox']) ? '1' : '0',
            'last_updated_by' => $this->session->userdata('user_id_pc'),
            'last_updated_date' => date('Y-m-d H:i:s')
        ];

        $this->db->where('obat_id', $data['obat_id']);
        return $this->db->update('pc01_frm_obat_ms', $updateData);
    }

    public function updateObat($data)
    {
        $lokasi_id = $data['lokasi_id'];
        $obat_id   = $data['obat_id'];

        $isMargin = isset($data['harga_final_checkbox']) ? true : false;

        $obatFornas   = isset($data['obat_fornas']) ? 'Y' : 'N';
        $obatHialert  = isset($data['obat_hialert']) ? 'Y' : 'N';
        $isProduksi   = isset($data['obat_produksi']) ? 'Y' : 'N';
        $isPembungkus = isset($data['is_pembungkus']) ? 'Y' : 'N';

        $jenis = isset($data['alkes']) ? 'A' : 'O';
        $mandiri = isset($data['obat_bpjs']) ? '1' : '0';
        $tampil = isset($data['semuaykn_obat']) ? '0' : '1';

        $updateObat = [
            'nama'              => $data['nama_obat'],
            'generik_id'        => !empty($data['generik_obat']) ? $data['generik_obat'] : null,
            'gol_obat'          => !empty($data['gol_obat']) ? $data['gol_obat'] : null,
            'sediaan_id'        => !empty($data['sediaan_obat']) ? $data['sediaan_obat'] : null,
            'pabrik_id'         => !empty($data['pabrik']) ? $data['pabrik'] : null,
            'route_id'          => !empty($data['route']) ? $data['route'] : null,
            'satuan_kecil_id'   => !empty($data['satuan_jual']) ? $data['satuan_jual'] : null,
            'satuan_besar_id'   => !empty($data['satuan_beli']) ? $data['satuan_beli'] : null,
            'konversi_satuan'   => $data['kon_satuan'],
            'harga_hna'         => $data['hna_besar'],
            'harga_hna_ppn'     => $data['hna_ppn_besar'],
            'harga'             => $data['jual_sat'],
            'harga_sat_ppn'     => $data['jual_sat_hna_ppn'],
            'harga_jual'        => $data['harga_final'],
            'is_margin'         => $isMargin,
            'obat_fornas'       => $obatFornas,
            'obat_hialert'      => $obatHialert,
            'is_produksi'       => $isProduksi,
            'is_pembungkus'     => $isPembungkus,
            'jenis'             => $jenis,
            'mandiri'           => $mandiri,
            'tampil'            => $tampil,
            'ecatalogue'        => !empty($data['ecatalogue']) ? $data['ecatalogue'] : null,
            'batasan_fornas'    => !empty($data['batasan_fornas']) ? $data['batasan_fornas'] : null,
            'no_registrasi'     => !empty($data['nomor_registrasi']) ? $data['nomor_registrasi'] : null,
            'last_updated_by'   => $data['last_updated_by'],
            'last_updated_date' => date('Y-m-d')
        ];

        $updateStok = [
            'nama_obat'         => $data['nama_obat'],
            'satuan_jl'         => !empty($data['satuan_jual']) ? $data['satuan_jual'] : null,
            'satuan_bl'         => !empty($data['satuan_beli']) ? $data['satuan_beli'] : null,
            'isi_bl'            => $data['kon_satuan'],
            'hna'               => $data['hna_besar'],
            'hna_ppn'           => $data['hna_ppn_besar'],
            'hna_satuan'        => $data['jual_sat'],
            'generik_id'        => !empty($data['generik_obat']) ? $data['generik_obat'] : null,
            'pabrik_id'         => !empty($data['pabrik']) ? $data['pabrik'] : null,
            'golobat_id'        => !empty($data['gol_obat']) ? $data['gol_obat'] : null,
            'last_updated_by'   => $data['last_updated_by'],
            'last_updated_date' => date('Y-m-d H:i:s')
        ];

        $this->db->trans_begin();

        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('obat_id', $obat_id)
            ->update('pcare_manager.pc01_frm_obat_ms', $updateObat);

        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('obat_id', $obat_id)
            ->update('pcare_manager.pc01_frm_gudang_stok', $updateStok);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    public function getDepoObat()
    {
        $this->db->select('gudang_id, keterangan');
        $this->db->from('pc01_frm_gudang_ms');
        return $this->db->get()->result();
    }

    public function getAllStok($depo_obat = null)
    {
        $sql = "SELECT 
                a.obat_id, 
                c.nama as nama_obat, 
                a.salwal, 
                a.mutbl, 
                a.mutjl, 
                a.retbl,
                a.retjl, 
                a.adjust,
                -- (a.salwal + a.mutbl - a.mutjl + a.retbl - a.retjl + a.adjust) AS stok_saat_ini,
                (a.salwal + a.mutbl - a.mutjl - a.retbl + a.retjl + a.adjust) AS stok_komputer,
                a.supplier_id,
                a.gudang_id,
                b.keterangan as depo  
            FROM pc01_frm_gudang_stok a   
            JOIN pc01_frm_gudang_ms b ON a.gudang_id = b.gudang_id AND b.aktif = '1'
            JOIN pc01_frm_obat_ms c ON a.obat_id = c.obat_id AND c.aktif = '1'";

        // Parameter untuk binding
        $params = [];
        if (!empty($depo_obat) && strtolower($depo_obat) !== 'semua') {
            $sql .= " AND LOWER(a.gudang_id) = LOWER(?)";
            $params[] = $depo_obat;
        }

        $query = $this->db->query($sql, $params);

        return $query->result();
    }

    public function getStockByObatGudang($obat_id, $gudang_id)
    {
        $this->db->where('obat_id', $obat_id);
        $this->db->where('gudang_id', $gudang_id);
        return $this->db->get('pc01_frm_gudang_stok')->row();
    }

    public function updateAdjustment($obat_id, $gudang_id, $adjustment_value)
    {
        $this->db->where('obat_id', $obat_id);
        $this->db->where('gudang_id', $gudang_id);
        return $this->db->update('pc01_frm_gudang_stok', ['adjust' => $adjustment_value]);
    }

    public function getAllTindakan($lokasi_id, $gol_tindakan = null, $search = null)
    {
        $sql = "SELECT a.layan_id, a.nama_layan1,a.nama_layan2 ,a.kategori_id,b.ms_skema_id,c.harga   from pc01_keu_layan_ms a
                join pc01_keu_harga_ms b on a.lokasi_id =b.lokasi_id 
                join pc01_keu_harga_dt c on a.layan_id =c.layan_id 
                where a.lokasi_id ='{$lokasi_id}'
                and a.lokasi_id =c.lokasi_id 
                and c.kelas_id ='6'
                and a.aktif ='1'";

        // Parameter untuk binding
        $params = [];

        // Filter gol_obat jika bukan null dan bukan "SEMUA"
        if (!empty($gol_tindakan) && strtolower($gol_tindakan) !== 'semua') {
            $sql .= " AND LOWER(a.nama_layan2) = LOWER(?)";
            $params[] = $gol_tindakan;
        }

        // Filter pencarian jika ada input search
        if (!empty($search)) {
            $sql .= " AND (LOWER(a.nama_layan1) LIKE ? 
                        OR LOWER(a.layan_id) LIKE ?)";
            $searchTerm = '%' . strtolower($search) . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        $sql .= "ORDER BY a.nama_layan1 ASC";

        $query = $this->db->query($sql, $params);

        return $query->result();
    }

    public function getSummaryManajemenObat($lokasi_id)
    {
        $sql = "SELECT
                COUNT(*) AS total_item,
                SUM(CASE WHEN status_stok = 'MINUS' THEN 1 ELSE 0 END) AS total_minus,
                SUM(CASE WHEN status_stok = 'HABIS' THEN 1 ELSE 0 END) AS total_habis,
                SUM(CASE WHEN status_stok = 'MENIPIS' THEN 1 ELSE 0 END) AS total_menipis,
                SUM(CASE WHEN status_stok = 'AMAN' THEN 1 ELSE 0 END) AS total_aman
            FROM pcare_manager.vw_frm_manajemen_obat
            WHERE lokasi_id = ?
        ";

        return $this->db->query($sql, [$lokasi_id])->row();
    }

    public function getManajemenObat($lokasi_id, $filter = [])
    {
        $sql = "SELECT *
                FROM pcare_manager.vw_frm_manajemen_obat
                WHERE lokasi_id = ?
            ";

        $params = [$lokasi_id];

        if (!empty($filter['depo_obat']) && strtolower($filter['depo_obat']) !== 'semua') {
            $sql .= " AND gudang_id = ? ";
            $params[] = $filter['depo_obat'];
        }

        if (!empty($filter['gol_obat']) && strtolower($filter['gol_obat']) !== 'semua') {
            $sql .= " AND gol_obat = ? ";
            $params[] = $filter['gol_obat'];
        }

        if (!empty($filter['status_stok']) && strtolower($filter['status_stok']) !== 'semua') {
            $sql .= " AND status_stok = ? ";
            $params[] = strtoupper($filter['status_stok']);
        }

        if (!empty($filter['search'])) {
            $sql .= "
            AND (
                LOWER(obat_id) LIKE ?
                OR LOWER(nama_obat) LIKE ?
                OR LOWER(COALESCE(nama_gudang, '')) LIKE ?
                OR LOWER(COALESCE(nama_golongan, '')) LIKE ?
                OR LOWER(COALESCE(satuan_jual, '')) LIKE ?
            )
        ";

            $search = '%' . strtolower($filter['search']) . '%';

            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= "ORDER BY
                        CASE status_stok
                            WHEN 'MINUS' THEN 1
                            WHEN 'HABIS' THEN 2
                            WHEN 'MENIPIS' THEN 3
                            ELSE 4
                        END,
                        nama_obat ASC
                ";

        return $this->db->query($sql, $params)->result();
    }

    public function adjustStokFisik($lokasi_id, $obat_id, $gudang_id, $stok_fisik, $user_id)
    {
        $this->db->trans_begin();

        $this->ensureStockRow($lokasi_id, $obat_id, $gudang_id, $user_id);

        $sql = "SELECT *
        FROM pcare_manager.fn_frm_adjust_stok_fisik(?, ?, ?, ?, ?)
        ";

        $result = $this->db->query($sql, [
            $lokasi_id,
            $obat_id,
            $gudang_id,
            $stok_fisik,
            $user_id
        ])->row();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            throw new Exception('Gagal melakukan adjustment stok.');
        }

        $this->db->trans_commit();

        return $result;
    }

    private function ensureStockRow($lokasi_id, $obat_id, $gudang_id, $user_id)
    {
        $existing = $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('obat_id', $obat_id)
            ->where('gudang_id', $gudang_id)
            ->where('aktif', '1')
            ->get('pcare_manager.pc01_frm_gudang_stok')
            ->row();

        if ($existing) {
            return true;
        }

        $obat = $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('obat_id', $obat_id)
            ->where('aktif', '1')
            ->get('pcare_manager.pc01_frm_obat_ms')
            ->row();

        if (!$obat) {
            throw new Exception('Master obat tidak ditemukan.');
        }

        $insert = [
            'lokasi_id'    => $lokasi_id,
            'obat_id'      => $obat_id,
            'nama_obat'    => $obat->nama,
            'satuan_jl'    => $obat->satuan_kecil_id,
            'satuan_bl'    => $obat->satuan_besar_id,
            'isi_bl'       => $obat->konversi_satuan,
            'salwal'       => 0,
            'mutbl'        => 0,
            'mutjl'        => 0,
            'retbl'        => 0,
            'retjl'        => 0,
            'promsk'       => 0,
            'proklr'       => 0,
            'adjust'       => 0,
            'hna'          => $obat->harga_hna,
            'hna_ppn'      => $obat->harga_hna_ppn,
            'hna_satuan'   => $obat->harga,
            'generik_id'   => $obat->generik_id,
            'pabrik_id'    => $obat->pabrik_id,
            'golobat_id'   => $obat->gol_obat,
            'gudang_id'    => $gudang_id,
            'aktif'        => '1',
            'created_date' => date('Y-m-d'),
            'created_by'   => $user_id,
            'periode'      => date('Ym'),
            'is_resep'     => 'Y',
            'is_anfrah'    => 'N',
            'is_penuh'     => 'Y'
        ];

        return $this->db->insert('pcare_manager.pc01_frm_gudang_stok', $insert);
    }
}
