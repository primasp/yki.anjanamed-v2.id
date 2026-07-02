<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RajalModel extends CI_Model
{
    public function get_pasien($term, $type)
    {
        $this->db->select('nama, tgl_lahir, sex_id, alamat1, no_kartuprov, pasien_id, int_pasien_id');
        $this->db->from('pc01_gen_pasien_ms');

        if ($type === "nama") {
            $this->db->where("LOWER(nama) LIKE", "%" . strtolower($term) . "%");
        } else if ($type === 'id') {
            $this->db->where('pasien_id', $term);
        } else {
            $this->db->where("LOWER(int_pasien_id) LIKE", "%" . strtolower($term) . "%");
        }

        $this->db->where('aktif', '1');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_pasien_v2($term, $type)
    {
        $this->db->select('nama, tgl_lahir, sex_id, alamat1, no_kartuprov, pasien_id, int_pasien_id');

        // $this->db->select('nama, tgl_lahir,sex_id,alamat1,no_kartuprov,pasien_id');
        // $this->db->select(' nama, int_pasien_id, tgl_lahir');
        $this->db->from('pc01_gen_pasien_ms');

        if ($type === "nama") {
            $this->db->where("LOWER(nama) LIKE", "%" . strtolower($term) . "%");
        } else if ($type === 'id') {
            $this->db->where('pasien_id', $term);
        } else {
            $this->db->where("LOWER(int_pasien_id) LIKE", "%" . strtolower($term) . "%");
        }

        // Tambahkan kondisi aktif
        $this->db->where('aktif', '1');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_lab_items()
    {
        $sql = "SELECT 
                a.layan_id, 
                a.nama_layan1 AS nama_pemeriksaan, 
                b.harga
            FROM pcare_manager.pc01_keu_layan_ms a
            JOIN pcare_manager.pc01_keu_harga_dt b 
                ON a.layan_id = b.layan_id 
                AND b.kelas_id = '6'
            WHERE 
                a.kategori_id = 'JKL-LAB'
                AND a.aktif = '1'
            ORDER BY a.nama_layan1 ASC
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_radiologi_items()
    {
        $sql = "SELECT 
                a.layan_id, 
                a.nama_layan1 AS nama_pemeriksaan, 
                b.harga
            FROM pcare_manager.pc01_keu_layan_ms a
            JOIN pcare_manager.pc01_keu_harga_dt b 
                ON a.layan_id = b.layan_id 
                AND b.kelas_id = '6'
            WHERE 
                a.kategori_id = 'JKL-RAD'
                AND a.aktif = '1'
            ORDER BY a.nama_layan1 ASC
        ";
        return $this->db->query($sql)->result_array();
    }


    public function getPoliByPembiayaan($pembiayaan)
    {
        if ($pembiayaan == 'UMUM') {
            // Hanya Klinik Umum & Klinik Vaksin
            $where = "AND A.poli_id IN ('POLI0000000002', 'POLI0000000003')";
        } elseif ($pembiayaan == 'PROGRAM') {
            // Hanya Klinik Paliatif
            $where = "AND A.poli_id IN ('POLI0000000001','POLI0000000002')";
        } else {
            // Default: semua aktif
            $where = "";
        }

        $query = "SELECT A.poli_id, A.keterangan 
                    FROM pc01_med_poli_ms A 
                    WHERE A.aktif = '1' $where
                    ORDER BY A.keterangan ASC
                ";

        return $this->db->query($query)->result();
    }



    // public function getPoli()
    // {
    //     $query = "SELECT * 
    //     FROM pc01_med_poli_ms A 
    //     WHERE A.aktif='1' ";

    //     return $this->db->query($query)->result();
    // }


    public function getDokterbyTgl($poli_id, $hari_id)
    {
        $query = "SELECT 
                    d.dokter_id,
                    d.nama ,
                    p.keterangan AS nama_poli,
                    d.prefix AS kd_dokter_bpjs
                FROM pc01_jadwal_antrian a
                LEFT JOIN pc01_med_dokter_ms d ON d.dokter_id = a.dokter_id 
                    AND d.lokasi_id = '001' 
                    AND d.aktif = '1'
                LEFT JOIN pc01_med_poli_ms p ON p.poli_id = a.poli_id 
                    AND p.lokasi_id = '001' 
                    AND p.aktif = '1'
                WHERE 
                    a.lokasi_id = '001' 
                    AND a.aktif = '1' 
                    AND d.aktif = '1' 
                    AND a.poli_id = '$poli_id'
                    AND a.hari_id = '$hari_id'
                GROUP BY 
                    d.dokter_id, 
                    d.nama, 
                    p.keterangan,
                    d.prefix
                ORDER BY 
                    d.nama ASC";


        return $this->db->query($query)->result();
    }


    public function get_jam_slot($tgl_berobat, $poli_id, $dokter_id, $hari_id)
    {

        $dateFormatted = DateTime::createFromFormat('Y-m-d', $tgl_berobat)->format('d.m.Y');

        $query = "SELECT X.*, 
                jmlpasienperslot - jmlterdaftarperslot sisaantrian, 
                (startantrian + jmlbatal + jmlterdaftar)::TEXT AS nourut,
                CASE
                    WHEN (
                        NOW() > TO_TIMESTAMP('$dateFormatted ' || SUBSTRING(X.jam_selesai, 1, 2), 'DD.MM.YYYY HH24')
                        OR jmlpasienperslot <= jmlterdaftarperslot
                    )
                    THEN 'T'
                    ELSE 'Y'
                END AS flag, 
                CASE
                    WHEN jmlpasienperslot - jmlterdaftarperslot = 0 THEN 'NULL'
                    ELSE X.kode_antrian || '-' || (startantrian + jmlbatal + jmlterdaftar)::TEXT
                END AS antrian,
				CASE 
			    		WHEN (jmlpasienperslot - jmlterdaftarperslot) > 0 THEN 'Tersedia' 
			   			 ELSE 'Penuh' 
				END AS slot_status,
				CAST(jmlbatal + jmlterdaftar + 1 AS VARCHAR) AS getantrian
            FROM (
                SELECT 
                     a.jam_mulai, a.jam_selesai, jml_ots AS jmlpasienperslot, a.antrian_id,a.kode_antrian,terdaftar(a.hari_id, a.jadwal_id, a.poli_id, a.dokter_id, '$dateFormatted', a.antrian_id) jmlterdaftar, terdaftar_perslot(a.poli_id, a.dokter_id, '$dateFormatted', a.hari_id, '2', a.kode_antrian) jmlterdaftarperslot 
                     ,a.hari_id, batal(a.hari_id, a.antrian_id, a.poli_id, a.dokter_id, '$dateFormatted', a.jam_mulai, a.jam_selesai) jmlbatal, 1 startantrian, a.jml_ots finish_antrian
                FROM pc01_jadwal_antrian a
                JOIN pc01_med_dokter_ms b ON b.dokter_id = a.dokter_id AND b.lokasi_id = '001' AND b.aktif = '1'
                JOIN pc01_med_poli_ms c ON c.poli_id = a.poli_id AND c.lokasi_id = '001' AND c.aktif = '1'
                WHERE a.lokasi_id = '001'
                    AND a.aktif = '1'
                    AND a.poli_id = '$poli_id'
					AND a.dokter_id = '$dokter_id'
                    AND a.hari_id = '$hari_id'
                AND a.aktif = '1'
            ) X";


        return $this->db->query($query)->result();
    }

    // public function getTarifPoli()
    // {
    //     $sql = "SELECT a.layan_id, a.nama_layan1, a.kategori_id, b.harga
    //         FROM pc01_keu_layan_ms a
    //         JOIN pc01_keu_harga_dt b 
    //             ON a.layan_id = b.layan_id 
    //            AND b.ms_skema_id = 'SKHRG0000000000'
    //         WHERE a.layan_id IN ('TDK000000000004', 'TDK000000000005')
    //           AND a.aktif = '1'
    //     ";
    //     return $this->db->query($sql)->result_array();
    // }

    public function getTarifPoliByJenis($poli_id)
    {
        $layan_ids = [];

        // Klinik Vaksin
        if ($poli_id === 'POLI0000000003') {
            $layan_ids = ['TDK000000000009', 'TDK000000000005']; // Pemeriksaan Vaksin + Pendaftaran
        }
        // Klinik Umum
        elseif ($poli_id === 'POLI0000000002') {
            $layan_ids = ['TDK000000000004', 'TDK000000000005']; // Pemeriksaan Umum + Pendaftaran
        }
        // default: jika belum diatur
        else {
            $layan_ids = ['TDK000000000005']; // minimal biaya pendaftaran
        }

        $sql = "SELECT a.layan_id, a.nama_layan1, a.kategori_id, b.harga
            FROM pc01_keu_layan_ms a
            JOIN pc01_keu_harga_dt b 
                ON a.layan_id = b.layan_id 
               AND b.ms_skema_id = 'SKHRG0000000000'
            WHERE a.layan_id IN (" . implode(',', array_map(fn ($id) => $this->db->escape($id), $layan_ids)) . ")
              AND a.aktif = '1'";

        return $this->db->query($sql)->result_array();
    }


    public function getTarifPenunjang()
    {
        $sql = "SELECT a.layan_id, a.nama_layan1, a.kategori_id, b.harga
            FROM pc01_keu_layan_ms a
            JOIN pc01_keu_harga_dt b 
                ON a.layan_id = b.layan_id 
               AND b.ms_skema_id = 'SKHRG0000000000'
            WHERE a.layan_id IN ('TDK000000000005')
              AND a.aktif = '1'
        ";
        return $this->db->query($sql)->result_array();
    }

    public function getAntrian($date, $hari_id, $antrian_id, $poli_id, $dokter_id)
    {
        // return var_dump($date);
        // die;
        $dateFormatted = DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y');



        $query = "SELECT X.*, 
                jmlpasienperslot - jmlterdaftarperslot sisa_antrian, 
                (startantrian + jmlbatal + jmlterdaftar)::TEXT AS nourut,
                CASE
                    WHEN (
                        NOW() > TO_TIMESTAMP('$dateFormatted ' || SUBSTRING(X.jam_selesai, 1, 2), 'DD.MM.YYYY HH24')
                        OR jmlpasienperslot <= jmlterdaftarperslot
                    )
                    THEN 'T'
                    ELSE 'Y'
                END AS flag, 
                CASE
                    WHEN jmlpasienperslot - jmlterdaftarperslot = 0 THEN 'NULL'
                    ELSE X.kode_antrian || '-' || (startantrian + jmlbatal + jmlterdaftar)::TEXT
                END AS antrian,
                CAST(jmlbatal + jmlterdaftar + 1 AS VARCHAR) AS getantrian
            FROM (
                SELECT 
                    a.urut, a.jadwal_id,a.hari_id, a.poli_id, a.dokter_id, a.jam_mulai, a.jam_selesai, 
                    jml_ots AS jmlpasienperslot, a.antrian_id,a.kode_antrian,b.nama,c.keterangan,
                    terdaftar(a.hari_id, a.jadwal_id, a.poli_id, a.dokter_id, '$dateFormatted', a.antrian_id) AS jmlterdaftar,
                    terdaftar_perslot(a.poli_id, a.dokter_id, '$dateFormatted', a.hari_id, '2', a.kode_antrian) AS jmlterdaftarperslot,
                    batal(a.hari_id, a.antrian_id, a.poli_id, a.dokter_id, '$dateFormatted', a.jam_mulai, a.jam_selesai) AS jmlbatal, 1 AS startantrian, a.jml_ots AS finish_antrian
                FROM pc01_jadwal_antrian a
                JOIN pc01_med_dokter_ms b ON b.dokter_id = a.dokter_id AND b.lokasi_id = '001' AND b.aktif = '1'
                JOIN pc01_med_poli_ms c ON c.poli_id = a.poli_id AND c.lokasi_id = '001' AND c.aktif = '1'
                WHERE a.lokasi_id = '001'
                    AND a.aktif = '1'
                    AND a.poli_id = '$poli_id'
					AND a.dokter_id = '$dokter_id'
                    AND a.hari_id = '$hari_id'
                AND a.aktif = '1'
            ) X";


        return $this->db->query($query)->row();
    }

    public function buat_episode_id($jenis)
    {
        return $this->db->query("SELECT buat_id_episode_id('$jenis');")->row();
    }

    public function buat_transaksi_id()
    {
        return $this->db->query("SELECT buat_id_transaksi_id('1');")->row();
    }

    public function registPoliPasien($user_id, $request, $antrian, $noUrut, $episode_id, $transaksi_id, $pasien_id, $provider, $poli_bpjs_id)
    {
        $pasien = $this->get_pasien_by_pas_id($pasien_id);
        $datetime = date("Y-m-d H:i:s");

        $this->PC01_JADWAL_ANTRIAN_TEMP($user_id, $request, $antrian, $episode_id, $pasien_id, $datetime, $pasien);
        $this->PC01_CO_REGISTRASI_ONLINE_HD($user_id, $request, $antrian, $episode_id, $pasien_id, $datetime, $pasien, $provider);

        $this->PC01_KEU_EPISODE($user_id, $request, $episode_id, $pasien_id, $provider);
        $this->PC01_KEU_TRANSAKSI_HD($user_id, $request, $antrian, $episode_id, $transaksi_id, $pasien_id, $datetime, $provider);
        $this->PC01_MED_PRWT_TR($user_id, $request, $antrian, $episode_id, $transaksi_id, $pasien_id, $datetime, $provider);

        $this->PC01_CO_REGISTRASI_MS($user_id, $request, $antrian, $episode_id, $transaksi_id, $pasien_id, $datetime, $provider);
        $this->PC01_KEU_EPISODE_BYR($user_id, $request, $episode_id, $pasien_id, $datetime, $provider);

        // return var_dump($pasien);
        // die;
    }

    public function updateTransHeaderPoli($lokasi_id, $episode_id, $trans_id, $data)
    {
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->where('episode_id', $episode_id);
        $this->db->where('trans_id', $trans_id);
        return $this->db->update('pc01_keu_transaksi_hd', $data);
    }

    public function updateEpisodeBayarPoli($lokasi_id, $episode_id, $pasien_id, $data)
    {
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        return $this->db->update('pc01_keu_episode_byr', $data);
    }

    public function getTransHeaderPoli($lokasi_id, $episode_id, $trans_id)
    {
        return $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('trans_id', $trans_id)
            ->get('pc01_keu_transaksi_hd')
            ->row_array();
    }

    public function getEpisodeBayarPoli($lokasi_id, $episode_id, $pasien_id)
    {
        return $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->get('pc01_keu_episode_byr')
            ->row_array();
    }

    public function get_pasien_by_pas_id($pasien_id)
    {
        $query = "SELECT * 
        FROM pc01_gen_pasien_ms 
        WHERE aktif = '1' and pasien_id='$pasien_id'";



        return $this->db->query($query)->row();
    }

    public function PC01_JADWAL_ANTRIAN_TEMP($user_id, $request, $antrian, $episode_id, $pasien_id, $datetime, $pasien)
    {
        $data = [
            'lokasi_id' => '001',
            'pasien_id' => $pasien_id,
            'episode_id' => $episode_id,
            'tanggal' => $request['tgl_berobat'],
            'jam_mulai' => $request['jam_awal'],
            'jam_selesai' => $request['jam_akhir'],
            'antrian_id' => $antrian->antrian_id,
            'hari_id' => $request['hari_id'],
            'jadwal_id' => $antrian->jadwal_id,
            'poli_id' => $request['poli'],
            'dokter_id' => $request['dokter_id'],
            'antrian' => $antrian->antrian,
            'aktif' => '1',
            'created_by' => $user_id,
            'tgl_lahir' => $pasien->tgl_lahir,
            'jenis' => '2',
            'kode_antrian' => $antrian->kode_antrian,
        ];
        // return ($data); //TEST

        $this->db->insert('pc01_jadwal_antrian_temp', $data); //ORI

    }

    public function buat_bayar_id($tipe = '1')
    {
        // Panggil function database sesuai sistem kamu
        // Jika di PostgreSQL → SELECT buat_id_bayar_id('1') AS buat_id_bayar_id
        // Jika di Oracle → SELECT buat_id_bayar_id('1') AS buat_id_bayar_id FROM dual
        $sql = "SELECT buat_id_bayar_id(?) AS buat_id_bayar_id";
        return $this->db->query($sql, [$tipe])->row();
    }

    public function PC01_CO_REGISTRASI_ONLINE_HD($user_id, $request, $antrian, $episode_id, $pasien_id, $datetime, $pasien, $provider)
    {
        $data = [
            'lokasi_id' => '001',
            'booking_id' => 'OTS',
            'pasien_id' => $pasien_id,
            'tgl_masuk' => $datetime,
            'episode_id' => $episode_id,
            'poli_id' => $request['poli'],
            'dokter_id' => $request['dokter_id'],
            'rekanan_id' => $provider,
            'aktif' => '1',
            'created_by' => $user_id,
            'pasien_baru' => '0',
            'valid' => '0',
            'hari_id'  => $antrian->hari_id,
            'jadwal_id' => $antrian->jadwal_id,
            'jam_mulai' => $antrian->jam_mulai,
            'jam_selesai' => $antrian->jam_selesai,
            'urut' => $antrian->antrian,
            'no_selular' => $pasien->no_selular,
            'email' => $pasien->email,
            'no_kartu_bpjs' => $pasien->no_kartuprov,
            'antrian_id' => $antrian->antrian_id,
            'isselesaidr' => 'T',
            'isregist' => 'Y',
            'jml_kuota' => '0',
            'sisa_kuota' => '0',
            'hadir' => 'Y',
            'tgl_hadir' => $datetime,
            'panggil' => 'T',
        ];
        $this->db->insert('pc01_co_registrasi_online_hd', $data);
    }

    public function PC01_KEU_EPISODE($user_id, $request, $episode_id, $pasien_id, $provider)
    {
        $data = [
            'lokasi_id' => '001',
            'episode_id' => $episode_id,
            'pasien_id' => $pasien_id,
            'jenis_episode' => 'O',
            'tgl_masuk' => $request['tgl_berobat'],
            'kelas_id' => '6',
            'poli_id' => $request['poli'],
            'dokter_id' => $request['dokter_id'],
            'rekanan_id' => $provider,
            'status_episode' => '00',
            'status_tr' => '0',
            'boleh_edit' => 'T',
            'print_kw' => '0',
            'print_nota' => '0',
            'tgl_transaksi' => $request['tgl_berobat'],
            'total_pembulatan' => '0',
            'total_sub' => '0',
            'total_diskpct' => '0',
            'total_diskon' => '0',
            'total_harga' => '0',
            'total_rekanan' => '0',
            'total_pribadi' => '0',
            'bayar_rekanan' => '0',
            'bayar_pribadi' => '0',
            'aktif' => '1',
            'created_by' => $user_id,
            'total_tanggung' => '0',
            'bpjs_tanggung' => '0',
            'bpjs_trans' => '0',
        ];

        // return ($data); //TEST

        $this->db->insert('pc01_keu_episode', $data);
    }

    public function PC01_KEU_TRANSAKSI_HD($user_id, $request, $antrian, $episode_id, $transaksi_id, $pasien_id, $datetime, $provider)
    {
        $data = [
            'lokasi_id' => '001',
            'episode_id' => $episode_id,
            'trans_id' => $transaksi_id,
            'pasien_id' => $pasien_id,
            'jenis_tr' => '001',
            'kelas_id' => '6',
            'poli_id' => $request['poli'],
            'dokter_id' => $request['dokter_id'],
            'rekanan_id' => $provider,
            'perjanjian_yn' => 'T',
            'no_urut' => $antrian->urut,
            'no_antrian' => $antrian->antrian,
            'frm_jmlpaten' => '0',
            'frm_jmlracik' => '0',
            'status_tr' => '00',
            'print_kw' => '0',
            'print_nota' => '0',
            'tgl_transaksi' => $datetime,
            'total_pembulatan' => '0',
            'total_sub' => '0',
            'total_diskpct' => '0',
            'total_diskon' => '0',
            'total_harga' => '0',
            'total_rekanan' => '0',
            'total_pribadi' => '0',
            'bayar_rekanan' => '0',
            'bayar_pribadi' => '0',
            'aktif' => '1',
            'created_by' => $user_id,
            'created_date' => $datetime,
            'total_tanggung' => '0',
            'sdh_rehitung' => 'T',
        ];

        $this->db->insert('pc01_keu_transaksi_hd', $data);
    }

    public function PC01_MED_PRWT_TR($user_id, $request, $antrian, $episode_id, $transaksi_id, $pasien_id, $datetime, $provider)
    {
        $data = [
            'lokasi_id' => '001',
            'episode_id' => $episode_id,
            'trans_id' => $transaksi_id,
            'pasien_id' => $pasien_id,
            'tanggal' => $datetime,
            'jadwal_id' => $antrian->jadwal_id,
            'poli_id' => $request['poli'],
            'dokter_id' => $request['dokter_id'],
            'rekanan_id' => $provider,
            'urut_dr' => $antrian->urut,
            'no_antri' => $antrian->getantrian,
            'daftar_user' => $user_id,
            'done_status' => '00',
            'trans_short' => $request['jam_awal'] . ' - ' . $request['jam_akhir'], // gatau mksd nya apa blm jelas
            'status' => '1',
            'aktif' => '1',
            'created_by' => $user_id,
            'created_date' => $datetime,
            'jenis_prwt' => 'L', // gatau mksd nya apa blm jelas
            'no_urut_dr' => $antrian->urut,
        ];
        $this->db->insert('pc01_med_prwt_tr', $data);
    }

    public function PC01_CO_REGISTRASI_MS($user_id, $request, $antrian, $episode_id, $transaksi_id, $pasien_id, $datetime, $provider)
    {


        $data = [
            'trans_id' => $transaksi_id,
            'pasien_id' => $pasien_id,
            'tanggal' => $request['tgl_berobat'],
            'dept_id' => $request['poli'],
            'dokter_id' => $request['dokter_id'],
            'rekanan_id' => $provider,
            'urut_dr' => $antrian->urut,
            'no_antri' => $antrian->getantrian,
            'created_by' => $user_id,
            'done_status' => '01',
            'status' => '0',
            'episode_id' => $episode_id,
        ];

        $this->db->insert('pc01_co_registrasi_ms', $data);
    }

    public function PC01_KEU_EPISODE_BYR($user_id, $request, $episode_id, $pasien_id, $datetime, $provider)
    {

        $data = [
            'lokasi_id' => '001',
            'episode_id' => $episode_id,
            'pasien_id' => $pasien_id,
            'rekanan_id' => $provider,
            'boleh_edit' => 'T',
            'print_kw' => '0',
            'print_nota' => '0',
            'tgl_transaksi' => $datetime,
            'tgl_lunas' => $datetime,
            'total_pembulatan' => '0',
            'total_sub' => '0',
            'total_diskpct' => '0',
            'total_diskon' => '0',
            'total_harga' => '0',
            'total_rekanan' => '0',
            'total_pribadi' => '0',
            'bayar_rekanan' => '0',
            'bayar_pribadi' => '0',
            'total_kembali' => '0',
            'sudah_terima_dari' => '',
            'aktif' => '1',
            'created_by' => $user_id,
            'created_date' => $datetime,
            'jenis_tr' => '1',
            'issplit' => 'T',
        ];
        $this->db->insert('pc01_keu_episode_byr', $data);
    }


    public function generateIdAps()
    {
        $data = [];
        $data['episode_id'] = $this->db->query("SELECT buat_id_episode_id('3') AS id")->row()->id;
        $data['trans_id']   = $this->db->query("SELECT buat_id_transaksi_id('4') AS id")->row()->id;
        $data['bayar_id']   = $this->db->query("SELECT buat_id_bayar_id('4') AS id")->row()->id;
        $data['trans_co']   = $this->db->query("SELECT pcare_manager.buat_id_transco() AS id")->row()->id;
        $data['shift_id']   = 'SHID' . date('ymdHis');
        return $data;
    }


    // public function insertTransDetail($data)
    // {
    //     $this->db->insert('pc01_keu_transctr_it', $data);
    // }

    public function insertTransDetail($data)
    {
        $defaults = [
            'qty' => 0,
            'harga_satuan' => 0,
            'disk_pct' => 0,
            'disk_tot' => 0,
            'harga_total' => 0,
            'total_rekanan' => 0,
            'total_pribadi' => 0,
            'aktif' => '1',
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $data = array_merge($defaults, $data);

        return $this->db->insert('pc01_keu_transctr_it', $data);
    }

    public function getHargaLayan($layan_id)
    {
        $row = $this->db->query("SELECT b.harga 
        FROM pc01_keu_layan_ms a
        JOIN pc01_keu_harga_dt b ON a.layan_id = b.layan_id
        WHERE a.layan_id = ? AND b.ms_skema_id = 'SKHRG0000000000'
    ", [$layan_id])->row();
        return $row ? (int)$row->harga : 0;
    }

    public function getKategoriLayan($layan_id)
    {
        $row = $this->db->query("SELECT kategori_id FROM pc01_keu_layan_ms WHERE layan_id = ?", [$layan_id])->row();
        return $row ? $row->kategori_id : null;
    }

    public function insertEpisodeAps($lokasi_id, $episode_id, $pasien_id, $kelas_id, $poli_id, $dokter_id, $rekanan_id, $status_tr, $user_id)
    {
        $data = [
            'lokasi_id' => $lokasi_id,
            'episode_id'     => $episode_id,
            'pasien_id'      => $pasien_id,
            'jenis_episode'  => 'O',
            // 'tgl_masuk'      => date('d/m/Y'),
            'tgl_masuk'      => date('Y-m-d'),
            'kelas_id'       => $kelas_id,
            'poli_id'        => $poli_id,
            'dokter_id'      => $dokter_id,
            'rekanan_id'     => $rekanan_id,
            'status_episode' => '00',
            'status_tr' => $status_tr,
            // 'tgl_transaksi'  => date('d/m/Y')
            'tgl_transaksi'  => date('Y-m-d'),
            'created_by' => $user_id
        ];
        $this->db->insert('pc01_keu_episode', $data);
    }

    // 🔹 Insert ke tabel med_prwt_tr
    public function insertMedPrwt($lokasi_id, $episode_id, $trans_id, $pasien_id, $poli_id, $dokter_id, $rekanan_id, $user_id)
    {
        $data = [
            'lokasi_id'   => $lokasi_id,
            'episode_id'  => $episode_id,
            'trans_id'    => $trans_id,
            'pasien_id'   => $pasien_id,
            // 'tanggal'     => date('d/m/Y'),
            'tanggal'        => date('Y-m-d'),
            'poli_id'     => $poli_id,
            'dokter_id'   => $dokter_id,
            'rekanan_id'  => $rekanan_id,
            'daftar_user' => $user_id,
            'done_status' => '00',
            'status'      => '0',
            'jenis_prwt'  => 'L'
        ];
        $this->db->insert('pc01_med_prwt_tr', $data);
    }


    public function get_daftar_rj($filters = [])
    {
        /*  a.booking_id,
            a.jam_mulai,
            a.jam_selesai,
            a.hari_id,
            a.antrian_id,
            a.urut,
            a.alasan_reserved,
        */
        $this->db->select("
        a.lokasi_id,
        a.episode_id,
        b.nama,
        b.int_pasien_id,
        b.pasien_id,
        b.tgl_lahir,
        b.no_kartuprov,
        b.sex_id,
        b.alamat1,
        a.rekanan_id,
        TO_CHAR(a.tgl_masuk, 'YYYY-MM-DD') as tgl_masuk,
        a.poli_id,
        c.keterangan AS nama_poli,
        c.poli_bpjsid,
        a.dokter_id,
        d.nama AS nama_dr,
        d.prefix AS id_dr_bpjs,
        a.aktif,
        e.status_episode
    ");

        $this->db->from("pc01_keu_episode a");

        $this->db->join(
            "pc01_gen_pasien_ms b",
            "a.pasien_id = b.pasien_id 
        AND b.aktif = '1' 
        AND a.lokasi_id = b.lokasi_id"
        );

        $this->db->join(
            "pc01_med_poli_ms c",
            "a.poli_id = c.poli_id 
        AND c.aktif = '1'  
        AND a.lokasi_id = c.lokasi_id"
        );

        $this->db->join(
            "pc01_med_dokter_ms d",
            "a.dokter_id = d.dokter_id 
        AND d.aktif = '1'  
        AND a.lokasi_id = d.lokasi_id 
        AND a.poli_id = d.def_poli_id"
        );

        // JOIN episode
        $this->db->join(
            "pc01_keu_episode e",
            "a.episode_id = e.episode_id
        AND a.lokasi_id = e.lokasi_id",
            "left"
        );

        $this->db->where("a.aktif", '1');

        // exclude batal
        $this->db->where_not_in("e.status_episode", ['99', '55']);

        if (!empty($filters['lokasi_id'])) {
            $this->db->where("a.lokasi_id", $filters['lokasi_id']);
        }

        if (!empty($filters['tgl'])) {
            $this->db->where("DATE(a.tgl_masuk)", $filters['tgl']);
        }

        if (!empty($filters['poli'])) {
            $this->db->where("a.poli_id", $filters['poli']);
        }

        if (!empty($filters['dokter'])) {
            $this->db->where("a.dokter_id", $filters['dokter']);
        }

        $this->db->order_by('a.tgl_masuk', 'DESC');

        return $this->db->get()->result_array();
    }

    // public function get_daftar_rjxx($filters = [])
    // {
    //     $this->db->select("
    //     a.lokasi_id,
    //     a.episode_id,
    //     b.nama,
    //     b.int_pasien_id,
    //     b.pasien_id,
    //     b.tgl_lahir,
    //     b.no_kartuprov,
    //     b.sex_id,
    //     b.alamat1,
    //     a.booking_id,
    //     a.rekanan_id,
    //     a.jam_mulai,
    //     a.jam_selesai,
    //     a.hari_id,
    //     a.antrian_id,
    //     TO_CHAR(a.tgl_masuk, 'YYYY-MM-DD') as tgl_masuk,
    //     a.urut,
    //     a.poli_id,
    //     c.keterangan AS nama_poli,
    //     c.poli_bpjsid,
    //     a.dokter_id,
    //     d.nama AS nama_dr,
    //     d.prefix AS id_dr_bpjs,
    //     a.alasan_reserved,
    //     a.aktif
    // ");
    //     $this->db->from("pc01_co_registrasi_online_hd a");
    //     $this->db->join("pc01_gen_pasien_ms b", "a.pasien_id = b.pasien_id AND b.aktif = '1' and a.lokasi_id=b.lokasi_id");
    //     $this->db->join("pc01_med_poli_ms c", "a.poli_id = c.poli_id AND c.aktif = '1'  and a.lokasi_id=c.lokasi_id");
    //     $this->db->join("pc01_med_dokter_ms d", "a.dokter_id = d.dokter_id AND d.aktif = '1'  and a.lokasi_id=d.lokasi_id and a.poli_id  =d.def_poli_id ");
    //     $this->db->where("a.status_episode", 'NOT IN(''99)');
    //     // $this->db->where("a.lokasi_id", $filters['lokasi_id']);
    //     // $this->db->where("a.isregist", 'T');
    //     // $this->db->where("a.tgl_hadir IS NULL");
    //     // $this->db->where("a.aktif", '1');

    //     $this->db->order_by('a.tgl_masuk', 'DESC');   // <= tambah ini

    //     // return var_dump("1111");
    //     // die;
    //     // echo $this->db->last_query();
    //     // die;
    //     // Jika episode_id dan pasien_id diberikan, tampilkan hanya satu baris
    //     if (!empty($filters['episode_id']) && !empty($filters['pasien_id'])) {
    //         $this->db->where("a.episode_id", $filters['episode_id']);
    //         $this->db->where("a.pasien_id", $filters['pasien_id']);
    //         return $this->db->get()->row_array();
    //     }



    //     // Filter umum untuk list
    //     if (!empty($filters['poli'])) {
    //         $this->db->where("a.poli_id", $filters['poli']);
    //     }



    //     if (!empty($filters['dokter'])) {
    //         $this->db->where("a.dokter_id", $filters['dokter']);
    //     }



    //     if (!empty($filters['tgl'])) {
    //         $tglFormatted = date('d.m.Y', strtotime($filters['tgl']));


    //         // return var_dump($tglFormatted);
    //         // die;
    //         $this->db->where("TO_CHAR(a.tgl_masuk, 'DD.MM.YYYY') =", $tglFormatted);
    //     }


    //     // print_r()
    //     // echo $this->db->last_query();
    //     // die;

    //     return $this->db->get()->result_array();
    // }


    public function batalBooking($episode_id, $lokasi_id)
    {
        // =========================
        // UPDATE EPISODE
        // =========================
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->where('episode_id', $episode_id);
        $this->db->where('status_episode', '00');
        $this->db->where('aktif', '1');

        $this->db->update('pc01_keu_episode', [
            'status_episode' => '99',
            // 'modified_date'  => date('Y-m-d H:i:s'),
            // 'modified_by'    => $this->session->userdata('user_id_pc')
        ]);

        // Jika tidak ada row terupdate
        if ($this->db->affected_rows() <= 0) {
            return false;
        }

        // =========================
        // UPDATE REGISTRASI ONLINE
        // =========================
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->where('episode_id', $episode_id);
        $this->db->where('aktif', '1');

        $this->db->update('pc01_co_registrasi_online_hd', [
            'aktif'         => '0',
            'alasan_reserved' => 'DIBATALKAN',
            // 'modified_date' => date('Y-m-d H:i:s'),
            // 'modified_by'   => $this->session->userdata('user_id_pc')
        ]);

        return true;
    }

    public function insertEpisodeBayar($data)
    {
        $this->db->insert('pc01_keu_episode_byr', $data);
    }


    // 🔹 Insert header transaksi (HD)
    // public function insertTransHeader($data)
    // {
    //     $this->db->insert('pc01_keu_transaksi_hd', $data);
    // }
    public function insertTransHeader($data)
    {
        $defaults = [
            'total_sub' => 0,
            'total_diskpct' => 0,
            'total_diskon' => 0,
            'total_harga' => 0,
            'total_rekanan' => 0,
            'total_pribadi' => 0,
            'aktif' => '1',
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $data = array_merge($defaults, $data);

        return $this->db->insert('pc01_keu_transaksi_hd', $data);
    }

    // 🔹 Insert data Lab
    public function insertLab($data)
    {
        $this->db->insert('pc01_co_lab_dt', $data);
    }


    // 🔹 Insert data Radiologi
    public function insertRad($data)
    {
        $this->db->insert('pc01_co_rad_dt', $data);
    }

    public function getTarifPoli($poli_id)
    {
        $lokasi_id = '001';
        $kelas_id  = '6';

        if ($poli_id === 'POLI0000000003') {
            $layanan = ['TDK000000000005', 'TDK000000000009'];
        } else {
            $layanan = ['TDK000000000005', 'TDK000000000004'];
        }

        $sql = "
        SELECT
            l.layan_id,
            l.nama_layan1,
            l.kategori_id,
            h.harga
        FROM pcare_manager.pc01_keu_layan_ms l
        JOIN pcare_manager.pc01_keu_harga_dt h
            ON h.lokasi_id = l.lokasi_id
           AND h.layan_id  = l.layan_id
           AND h.kelas_id  = ?
        WHERE l.lokasi_id = ?
          AND l.layan_id IN (?, ?)
        ORDER BY l.layan_id
    ";

        return $this->db->query($sql, [
            $kelas_id,
            $lokasi_id,
            $layanan[0],
            $layanan[1]
        ])->result_array();
    }



    public function get_dt_by_episode($episodeId)
    {
        // $queryx = "SELECT a.episode_id,b.nama as nama_pasien, b.no_identitas ,a.poli_id, d.keterangan as nama_poli,d.location_id_satu_sehat, a.dokter_id, c.nama as nama_dr, c.satusehat_id as dokter_id_satu_sehat  from pc01_keu_episode a
        //             join pc01_gen_pasien_ms b on a.pasien_id =b.pasien_id and b.aktif ='1'
        //             join pc01_med_dokter_ms c on a.dokter_id =c.dokter_id and c.aktif ='1'
        //             join pc01_med_poli_ms d  on a.poli_id =d.poli_id and d.aktif ='1'
        //            where a.episode_id ='$episodeId' and a.aktif ='1'";

        $query = "SELECT f.int_pasien_id as no_mr,f.no_identitas, a.episode_id,f.nama , a.poli_id,d.keterangan as nama_poli ,a.dokter_id,c.nama as nama_dr, b.ihs_id, e.satusehat_id     from pc01_keu_episode a         
                    left join pc01_gen_user_data b on a.dokter_id =b.dokter_id 
                    left join pc01_med_dokter_ms c on a.dokter_id =c.dokter_id and c.aktif ='1'
                    left join pc01_med_poli_ms d  on a.poli_id =d.poli_id and d.aktif ='1'
                    left join pc01_satusehat_location e on a.poli_id =e.value 
                    join pc01_gen_pasien_ms f on a.pasien_id =f.pasien_id and b.aktif ='1'
                    where a.episode_id ='$episodeId' and b.ihs_id is not null and a.aktif ='1'      
            ";








        return $this->db->query($query)->row();
    }


    public function get_encounter_uuid_by_local($encounter_id_local)
    {
        $q = $this->db->query("SELECT resource_id_satusehat
                FROM log_satusehat_fhir
                WHERE resource_id_local = ?
                AND resource_id_satusehat IS NOT NULL
                AND resource_id_satusehat <> ''
                ORDER BY created_at DESC
                LIMIT 1
            ", [$encounter_id_local]);

        $row = $q->row();
        return $row ? $row->resource_id_satusehat : null;
    }


    public function get_uuid_from_log($resource_type, $resource_id_local)
    {
        $q = $this->db->query("SELECT resource_id_satusehat
        FROM pcare_manager.log_satusehat_fhir
        WHERE resource_type = ?
          AND resource_id_local = ?
          AND resource_id_satusehat IS NOT NULL
          AND resource_id_satusehat <> ''
          AND status = 'success'
        ORDER BY created_at DESC
        LIMIT 1
    ", [$resource_type, $resource_id_local]);

        $row = $q->row();
        return $row ? $row->resource_id_satusehat : null;
    }

    public function simpan_log_encounter($data)
    {
        $this->db->insert('log_satusehat_fhir', [
            'resource_type' => 'Encounter',
            'resource_id_satusehat' => $data['encounter_id_satusehat'],
            'resource_id_local' => $data['encounter_id_local'],
            'response_json' => $data['response_json'],
            'status' => $data['status']
        ]);
    }

    public function insert_log_satusehat_fhir($data)
    {
        return $this->db->insert('pcare_manager.log_satusehat_fhir', $data);
    }
}
