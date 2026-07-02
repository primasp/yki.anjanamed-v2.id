<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminModel extends CI_Model
{
    // Fungsi untuk insert banyak data sekaligus (batch insert)
    // public function insert_batch($data)
    // {


    //     foreach ($data as $row) {
    //         $this->db->insert('pc01_keu_layan_ms', $row);
    //         echo $this->db->last_query() . "<br>"; // Tampilkan setiap query
    //     }
    //     die();
    // }
    public function insert_batch_obat($data)
    {
        if (empty($data)) {
            log_message('error', 'Insert Batch Gagal: Data kosong');
            // return false;
            return ['status' => 'error', 'message' => 'Data kosong, tidak ada yang diproses.'];
        }

        // Ambil semua nama_layan1 yang sudah ada di database
        $existing_names = $this->db->select('nama')
            ->from('pc01_frm_obat_ms')
            ->get()
            ->result_array();

        $existing_names_array = array_column($existing_names, 'nama');

        $filtered_data = [];
        $harga_data = [];
        $duplicate_data = [];
        foreach ($data as $row) {
            if (in_array($row['nama'], $existing_names_array)) {
                $duplicate_data[] = $row['nama']; // Simpan nama layanan yang duplikat
            } else {
                $filtered_data[] = [
                    'nama' => $row['nama'],
                    'satuan_kecil_id' => $row['satuan_kecil_id'],

                    'konversi_satuan' => $row['konversi_satuan'],
                    'harga_hna' => $row['harga_hna'],
                    'harga_hna_ppn' => $row['harga_hna_ppn'],
                    'harga' => $row['harga'],
                    'harga_sat_ppn' => $row['harga_sat_ppn'],




                    'harga_jual' => $row['harga_jual']
                ];

                $harga_data[$row['nama']] = $row['harga_jual'] ?? 0; // Simpan harga_jual berdasarkan nama
            }
        }

        if (empty($filtered_data)) {
            log_message('error', 'Semua data sudah ada, tidak ada yang disimpan.');
            // return false;
            return ['status' => 'error', 'message' => 'Semua data sudah pernah diinput.', 'duplicates' => $duplicate_data];
        }

        // Mulai transaksi
        $this->db->trans_start();

        // Insert data ke pc01_keu_layan_ms
        $insert_query  = $this->db->insert_batch('pc01_frm_obat_ms', $filtered_data);


        if (!$insert_query) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Gagal menyimpan data layanan.', 'duplicates' => $duplicate_data];
        }

        $this->db->trans_complete();

        return [
            'status' => 'success',
            'message' => 'Data berhasil disimpan.',
            'duplicates' => $duplicate_data
            // 'inserted_layan_ids' => array_column($layan_ids, 'layan_id')
        ];
    }


    public function insert_batch_tindakan($data)
    {


        // Cek apakah data tidak kosong
        if (empty($data)) {
            log_message('error', 'Insert Batch Gagal: Data kosong');
            // return false;
            return ['status' => 'error', 'message' => 'Data kosong, tidak ada yang diproses.'];
        }



        // Ambil semua nama_layan1 yang sudah ada di database
        $existing_names = $this->db->select('nama_layan1')
            ->from('pc01_keu_layan_ms')
            ->get()
            ->result_array();


        // Konversi ke array untuk pengecekan cepat
        $existing_names_array = array_column($existing_names, 'nama_layan1');

        // Filter data untuk hanya memasukkan yang belum ada
        $filtered_data = [];
        $harga_data = [];
        $duplicate_data = [];



        foreach ($data as $row) {
            if (in_array($row['nama_layan1'], $existing_names_array)) {
                $duplicate_data[] = $row['nama_layan1']; // Simpan nama layanan yang duplikat
            } else {
                // $filtered_data[] = $row;
                $filtered_data[] = [
                    'nama_layan1' => $row['nama_layan1'],
                    'nama_layan2' => $row['nama_layan2'],
                    'kategori_id' => $row['kategori_id']
                ];
                // $harga_data[] = [
                //     'harga' => $row['harga'] ?? 0, // Ambil harga dari upload Excel

                // ];

                $harga_data[$row['nama_layan1']] = $row['harga'] ?? 0; // Simpan harga berdasarkan nama_layan1
            }
        }



        // Jika tidak ada data yang tersisa setelah filter, hentikan proses
        if (empty($filtered_data)) {
            log_message('error', 'Semua data sudah ada, tidak ada yang disimpan.');
            // return false;
            return ['status' => 'error', 'message' => 'Semua data sudah pernah diinput.', 'duplicates' => $duplicate_data];
        }




        // Mulai transaksi
        $this->db->trans_start();

        // Menjalankan insert_batch

        // Insert data ke pc01_keu_layan_ms
        $insert_query  = $this->db->insert_batch('pc01_keu_layan_ms', $filtered_data);

        // Ambil ID layanan yang baru dimasukkan
        // $layan_ids = $this->db->query("SELECT layan_id FROM pc01_keu_layan_ms ORDER BY layan_id DESC LIMIT " . count($filtered_data))->result_array();


        if (!$insert_query) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Gagal menyimpan data layanan.', 'duplicates' => $duplicate_data];
        }

        // Ambil ID layanan yang baru dimasukkan
        // $layan_ids = $this->db->query("SELECT layan_id FROM pc01_keu_layan_ms ORDER BY layan_id DESC LIMIT " . count($filtered_data))->result_array();
        $layan_ids = $this->db->query(" SELECT layan_id, nama_layan1 FROM pc01_keu_layan_ms 
        WHERE nama_layan1 IN ('" . implode("','", array_keys($harga_data)) . "')")->result_array();

        if (!$layan_ids) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Gagal mengambil layan_id.', 'duplicates' => $duplicate_data];
        }


        // Gabungkan ID layanan dengan harga
        $harga_insert_data = [];
        foreach ($layan_ids as $layan) {
            $harga_insert_data[] = [
                'layan_id' => $layan['layan_id'],
                'harga'    => $harga_data[$layan['nama_layan1']]
            ];
        }

        // return var_dump($harga_insert_data);
        // die;

        // Insert harga ke pc01_keu_harga_dt
        $this->db->insert_batch('pc01_keu_harga_dt', $harga_insert_data);


        $this->db->trans_complete();


        return [
            'status' => 'success',
            'message' => 'Data berhasil disimpan.',
            'duplicates' => $duplicate_data,
            'inserted_layan_ids' => array_column($layan_ids, 'layan_id')
        ];
    }
}
