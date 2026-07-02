<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AsessmentModel extends CI_Model
{

    public function getDetilPemeriksaanByEpisode($episode_id)
    {
        $sql = "SELECT b.layan_id, c.nama_layan1, c.kategori_id
        FROM pc01_keu_transaksi_hd a
        JOIN pc01_keu_transctr_it b ON a.episode_id = b.episode_id
        JOIN pc01_keu_layan_ms c ON b.layan_id = c.layan_id
        WHERE a.episode_id = ?
          AND b.layan_id NOT IN ('TDK000000000005')
          AND a.poli_id = 'APS'
        ORDER BY c.nama_layan1 ASC
    ";
        return $this->db->query($sql, [$episode_id])->result();
    }

    public function getDetilLayan($episode_id)
    {
        $sql = "SELECT *
        FROM pc01_keu_episode a
        --JOIN pc01_keu_transctr_it b ON a.episode_id = b.episode_id
        --JOIN pc01_keu_layan_ms c ON b.layan_id = c.layan_id
        WHERE a.episode_id = ?
         -- AND b.layan_id NOT IN ('TDK000000000005')
          --AND a.poli_id = 'APS'
        --ORDER BY c.nama_layan1 ASC
    ";
        return $this->db->query($sql, [$episode_id])->row();
    }



    public function getGlobalByJenis($jenis_id)
    {
        return $this->db
            ->select('global_id, keterangan')
            ->from('pc01_gen_global_ms')
            ->where(['jenis_id' => $jenis_id, 'aktif' => '1'])
            ->order_by('urut', 'ASC')
            ->order_by('global_id', 'ASC')
            ->get()
            ->result();
    }



    public function getPasienRingkas($pasien_id)
    {
        return $this->db->query("SELECT
                                pasien_id,
                                int_pasien_id AS no_rm,
                                nama,
                                no_selular,
                                no_identitas,
                                alamat1,
                                tempat_lahir_txt,
                                TO_CHAR(tgl_lahir,'DD-MM-YYYY') AS tgl_lahir,
                                agama_id,
                                nama_pasangan,
                                pas_goldar_id,
                                sex_id,
                                marital_status_id,
                                pendidikan_id,
                                --date_part('year', age(current_date, tgl_lahir))::int AS umur
                                hitung_umur(
                                    TO_CHAR(tgl_lahir, 'YYYY-MM-DD'),
                                    TO_CHAR(current_date, 'YYYY-MM-DD')
                                ) AS umur,
                                b.keterangan as kecamatan,
                                c.keterangan as kabupaten,
                                d.keterangan as provinsi
                                FROM pc01_gen_pasien_ms a
                                left join pc01_gen_kkk_ms b on a.kecamatan_id =b.kkk_id
                                left join pc01_gen_kkk_ms c on a.kabupaten_id  =c.kkk_id 
                                left join pc01_gen_kkk_ms d on a.propinsi_id  =d.kkk_id
                                WHERE pasien_id = ?
    ", [$pasien_id])->row();
    }

    public function cekAsesmenPerLayanan($episode_id, $pasien_id, $kategori)
    {
        if ($kategori == 'JKL-RAD') {
            $this->db->where('episode_id', $episode_id);
            $this->db->where('pasien_id', $pasien_id);
            return $this->db->count_all_results('pcare_manager.pc01_med_ases_radiologi') > 0;
        }

        if ($kategori == 'JKL-LAB') {
            $this->db->where('episode_id', $episode_id);
            $this->db->where('pasien_id', $pasien_id);
            return $this->db->count_all_results('pcare_manager.pc01_med_ases_laboratorium') > 0;
        }

        return false;
    }



    public function getStatusKawin()
    {
        // merupakan status kawin ya 
        $query = "SELECT global_id,keterangan
        FROM pc01_gen_global_ms
        WHERE aktif = '1'
        AND jenis_id = 'SKWN'
        AND lokasi_id = '001';
        ";

        $data = $this->db->query($query)->result_array();
        return $data;
    }


    public function getBelumAnamnesaPoli($lokasi_id = '001', $tanggal = null)
    {
        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }

        $sql = "SELECT 
            A.pasien_id,
            C.int_pasien_id,
            C.nama AS nama_pas,
            B.no_urut_dr,
            C.tgl_lahir,
            A.episode_id,
            A.rekanan_id,
            A.poli_id,
            E.keterangan AS nama_poli,
            A.dokter_id,
            F.nama AS nama_dr,
            K.kunjungan_rs,
              COALESCE(KP.kunjungan_poli, 0) AS kunjungan_poli,
            H.nama_ruang,
            TO_CHAR(A.created_date, 'HH24:MI') AS jam_daftar
        FROM pc01_keu_episode A
        JOIN pc01_med_prwt_tr B
            ON A.episode_id = B.episode_id
           AND A.pasien_id  = B.pasien_id
           AND B.aktif      = '1'
           AND B.done_status= '00'
           AND B.lokasi_id  = ?
        JOIN pc01_gen_pasien_ms     C ON A.pasien_id  = C.pasien_id
        JOIN pc01_co_registrasi_ms  D ON A.episode_id = D.episode_id
        JOIN pc01_med_poli_ms       E ON A.poli_id    = E.poli_id
        JOIN pc01_med_dokter_ms     F ON A.dokter_id  = F.dokter_id
        JOIN pc01_med_poli_dokter   G ON A.dokter_id  = G.dokter_id
                                      AND A.poli_id    = G.poli_id
        JOIN pc01_med_poli_ruang    H ON G.id_ruang   = H.id_ruang
        JOIN (
            SELECT pasien_id, COUNT(*) AS kunjungan_rs
            FROM pc01_med_prwt_tr
            WHERE aktif = '1'
              AND lokasi_id = ?
            GROUP BY pasien_id
        ) K ON A.pasien_id = K.pasien_id
         LEFT JOIN (
                    SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = ?
                    GROUP BY pasien_id, poli_id
                ) KP ON A.pasien_id = KP.pasien_id AND A.poli_id = KP.poli_id
        WHERE A.lokasi_id      = ?
          AND A.status_episode IN ('00')
          AND A.kelas_id       = '6'
          AND A.jenis_episode  = 'O'
          AND D.status         = '0'
           AND A.tgl_masuk      = ?
        GROUP BY 
            A.pasien_id, C.int_pasien_id, C.nama, B.no_urut_dr, C.tgl_lahir,
            A.episode_id, A.rekanan_id, A.poli_id, E.keterangan,KP.kunjungan_poli,
            A.dokter_id, F.nama, K.kunjungan_rs, H.nama_ruang, A.created_date
        ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC
        ";





        return $this->db->query($sql, [$lokasi_id, $lokasi_id, $lokasi_id, $lokasi_id, $tanggal])->result();
    }


    public function getSudahAnamnesaPoli($lokasi_id = '001', $tanggal = null)
    {
        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }

        $sql = "SELECT 
            A.pasien_id,
            C.int_pasien_id,
            C.nama AS nama_pas,
            B.no_urut_dr,
            C.tgl_lahir,
            A.episode_id,
            A.rekanan_id,
            A.poli_id,
            E.keterangan AS nama_poli,
            A.dokter_id,
            F.nama AS nama_dr,
            K.kunjungan_rs,
              COALESCE(KP.kunjungan_poli, 0) AS kunjungan_poli,
            H.nama_ruang,
            TO_CHAR(A.created_date, 'HH24:MI') AS jam_daftar
        FROM pc01_keu_episode A
        JOIN pc01_med_prwt_tr B
            ON A.episode_id = B.episode_id
           AND A.pasien_id  = B.pasien_id
           AND B.aktif      = '1'
           AND B.done_status= '01'
           AND B.lokasi_id  = ?
        JOIN pc01_gen_pasien_ms     C ON A.pasien_id  = C.pasien_id
        JOIN pc01_co_registrasi_ms  D ON A.episode_id = D.episode_id
        JOIN pc01_med_poli_ms       E ON A.poli_id    = E.poli_id
        JOIN pc01_med_dokter_ms     F ON A.dokter_id  = F.dokter_id
        JOIN pc01_med_poli_dokter   G ON A.dokter_id  = G.dokter_id
                                      AND A.poli_id    = G.poli_id
        JOIN pc01_med_poli_ruang    H ON G.id_ruang   = H.id_ruang
        JOIN (
            SELECT pasien_id, COUNT(*) AS kunjungan_rs
            FROM pc01_med_prwt_tr
            WHERE aktif = '1'
              AND lokasi_id = ?
            GROUP BY pasien_id
        ) K ON A.pasien_id = K.pasien_id
         LEFT JOIN (
                    SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = ?
                    GROUP BY pasien_id, poli_id
                ) KP ON A.pasien_id = KP.pasien_id AND A.poli_id = KP.poli_id
        WHERE A.lokasi_id      = ?
          AND A.status_episode IN ('00')
          AND A.kelas_id       = '6'
          AND A.jenis_episode  = 'O'
          AND D.status         = '0'
           AND A.tgl_masuk      = ?
        GROUP BY 
            A.pasien_id, C.int_pasien_id, C.nama, B.no_urut_dr, C.tgl_lahir,
            A.episode_id, A.rekanan_id, A.poli_id, E.keterangan,KP.kunjungan_poli,
            A.dokter_id, F.nama, K.kunjungan_rs, H.nama_ruang, A.created_date
        ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC
        ";





        return $this->db->query($sql, [$lokasi_id, $lokasi_id, $lokasi_id, $lokasi_id, $tanggal])->result();
    }


    public function getBelumAnamnesaxxx($lokasi_id = '001')
    {
        $sql = "SELECT 
                    A.pasien_id,
                    C.int_pasien_id,
                    C.nama AS nama_pas,
                    B.no_urut_dr,
                    C.tgl_lahir,
                    A.episode_id,
                    A.rekanan_id,
                    A.poli_id,
                    CASE 
                        WHEN A.poli_id = 'APS' THEN 'Penunjang'
                        ELSE E.keterangan 
                    END AS nama_poli,
                    A.dokter_id,
                    CASE 
                        WHEN A.poli_id = 'APS' THEN '-' 
                        ELSE F.nama 
                    END AS nama_dr,
                    K.kunjungan_rs,
                    TO_CHAR(A.created_date, 'HH24:MI') AS jam_daftar
                FROM pc01_keu_episode A
                JOIN pc01_med_prwt_tr B
                ON A.episode_id = B.episode_id
                AND A.pasien_id  = B.pasien_id
                AND B.aktif      = '1'
                AND B.done_status= '00'
                AND B.lokasi_id  = ?
                JOIN pc01_gen_pasien_ms     C ON A.pasien_id = C.pasien_id
                LEFT JOIN pc01_med_poli_ms  E ON A.poli_id   = E.poli_id
                LEFT JOIN pc01_med_dokter_ms F ON A.dokter_id = F.dokter_id
                JOIN (
                    SELECT pasien_id, COUNT(*) AS kunjungan_rs
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = ?
                    GROUP BY pasien_id
                ) K ON A.pasien_id = K.pasien_id
                WHERE A.lokasi_id      = ?
                AND A.status_episode IN ('00')
                AND A.kelas_id       = '6'
                AND A.jenis_episode  = 'O'
                AND A.tgl_masuk      = CURRENT_DATE
                GROUP BY 
                    A.pasien_id, C.int_pasien_id, C.nama, B.no_urut_dr, C.tgl_lahir,
                    A.episode_id, A.rekanan_id, A.poli_id, A.dokter_id, 
                    E.keterangan, F.nama, K.kunjungan_rs, A.created_date
                ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC;
            ";
        return $this->db->query($sql, [$lokasi_id, $lokasi_id, $lokasi_id])->result();
    }


    // public function getBelumAnamnesa($lokasi_id = '001')
    public function getBelumAnamnesa($tanggal = null, $lokasi_id = '001')
    {
        // kalau tidak dikirim, pakai hari ini
        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }

        $sql = "SELECT 
                    A.pasien_id,
                    C.int_pasien_id,
                    C.nama AS nama_pas,
                    B.no_urut_dr,
                    C.tgl_lahir,
                    A.episode_id,
                    A.rekanan_id,
                    K.kunjungan_rs,
                    A.poli_id,
                    CASE 
                        WHEN A.poli_id = 'APS' THEN 'Penunjang'
                        ELSE E.keterangan 
                    END AS nama_poli,
                    A.dokter_id,
                    CASE 
                        WHEN A.poli_id = 'APS' THEN '-' 
                        ELSE F.nama 
                    END AS nama_dr,
                    K.kunjungan_rs,
                    COALESCE(KP.kunjungan_poli, 0) AS kunjungan_poli,
                    TO_CHAR(A.created_date, 'HH24:MI') AS jam_daftar
                FROM pc01_keu_episode A
                JOIN pc01_med_prwt_tr B
                ON A.episode_id = B.episode_id
                AND A.pasien_id  = B.pasien_id
                AND B.aktif      = '1'
                AND B.done_status= '00'
                AND B.lokasi_id  = '001'
                JOIN pc01_gen_pasien_ms     C ON A.pasien_id = C.pasien_id
                LEFT JOIN pc01_med_poli_ms  E ON A.poli_id   = E.poli_id
                LEFT JOIN pc01_med_dokter_ms F ON A.dokter_id = F.dokter_id
                JOIN (
            SELECT pasien_id, COUNT(*) AS kunjungan_rs
            FROM pc01_med_prwt_tr
            WHERE aktif = '1'
              AND lokasi_id = ?
            GROUP BY pasien_id
        ) K ON A.pasien_id = K.pasien_id
                LEFT JOIN (
                    SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = ?
                    GROUP BY pasien_id, poli_id
                ) KP ON A.pasien_id = KP.pasien_id AND A.poli_id = KP.poli_id
                WHERE 
                    A.lokasi_id      = ?
                    AND A.status_episode IN ('00')
                    AND A.kelas_id       = '6'
                    AND A.jenis_episode  = 'O'
                    AND A.tgl_masuk      = ?
                GROUP BY 
                    A.pasien_id, 
                    C.int_pasien_id, 
                    C.nama, 
                    B.no_urut_dr, 
                    C.tgl_lahir,
                    A.episode_id, 
                    A.rekanan_id, 
                    A.poli_id, 
                    A.dokter_id, 
                    E.keterangan, 
                    F.nama, 
                    K.kunjungan_rs, 
                    KP.kunjungan_poli,
                    A.created_date
                ORDER BY 
                    A.poli_id, 
                    A.dokter_id, 
                    B.no_urut_dr ASC";
        return $this->db->query($sql, [
            $lokasi_id, $lokasi_id, $lokasi_id,
            $tanggal,
        ])->result();
    }

    // === Pasien Sudah Anamnesa (done_status = 01)
    public function getSudahAnamnesa($tanggal = null, $lokasi_id = '001')
    {

        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }

        $sql = "SELECT 
                    A.pasien_id,
                    C.int_pasien_id,
                    C.nama AS nama_pas,
                    B.no_urut_dr,
                    C.tgl_lahir,
                    A.episode_id,
                    A.rekanan_id,
                    K.kunjungan_rs,
                    A.poli_id,
                    CASE 
                        WHEN A.poli_id = 'APS' THEN 'Penunjang'
                        ELSE E.keterangan 
                    END AS nama_poli,
                    A.dokter_id,
                    CASE 
                        WHEN A.poli_id = 'APS' THEN '-' 
                        ELSE F.nama 
                    END AS nama_dr,
                    K.kunjungan_rs,
                    COALESCE(KP.kunjungan_poli, 0) AS kunjungan_poli,
                    TO_CHAR(A.created_date, 'HH24:MI') AS jam_daftar
                FROM pc01_keu_episode A
                JOIN pc01_med_prwt_tr B
                ON A.episode_id = B.episode_id
                AND A.pasien_id  = B.pasien_id
                AND B.aktif      = '1'
                AND B.done_status= '01'
                AND B.lokasi_id  = '001'
                JOIN pc01_gen_pasien_ms     C ON A.pasien_id = C.pasien_id
                LEFT JOIN pc01_med_poli_ms  E ON A.poli_id   = E.poli_id
                LEFT JOIN pc01_med_dokter_ms F ON A.dokter_id = F.dokter_id
                JOIN (
            SELECT pasien_id, COUNT(*) AS kunjungan_rs
            FROM pc01_med_prwt_tr
            WHERE aktif = '1'
              AND lokasi_id = ?
            GROUP BY pasien_id
        ) K ON A.pasien_id = K.pasien_id
                LEFT JOIN (
                    SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = ?
                    GROUP BY pasien_id, poli_id
                ) KP ON A.pasien_id = KP.pasien_id AND A.poli_id = KP.poli_id
                WHERE 
                    A.lokasi_id      = ?
                    AND A.status_episode IN ('00')
                    AND A.kelas_id       = '6'
                    AND A.jenis_episode  = 'O'
                    AND A.tgl_masuk      = ?
                GROUP BY 
                    A.pasien_id, 
                    C.int_pasien_id, 
                    C.nama, 
                    B.no_urut_dr, 
                    C.tgl_lahir,
                    A.episode_id, 
                    A.rekanan_id, 
                    A.poli_id, 
                    A.dokter_id, 
                    E.keterangan, 
                    F.nama, 
                    K.kunjungan_rs, 
                    KP.kunjungan_poli,
                    A.created_date
                ORDER BY 
                    A.poli_id, 
                    A.dokter_id, 
                    B.no_urut_dr ASC";
        return $this->db->query($sql, [$lokasi_id, $lokasi_id, $lokasi_id, $tanggal])->result();
    }

    public function getPoliklinik($lokasi_id = '001')
    {
        return $this->db->query("
        SELECT poli_id, keterangan 
        FROM pc01_med_poli_ms 
        WHERE lokasi_id = ? 
          AND aktif = '1'
          AND poli_id not in('APS')
        ORDER BY keterangan
    ", [$lokasi_id])->result();
    }

    public function getDokterByPoli($poli_id)
    {
        return $this->db->query("SELECT 
            A.poli_id,
            A.poli_bpjsid,
            B.dokter_id,
            B.nama
        FROM pc01_med_poli_ms A
        JOIN pc01_med_dokter_ms B 
          ON A.poli_id = B.def_poli_id
        WHERE A.aktif = '1'
          AND A.poli_id = ?
          AND B.prefix IS NOT NULL
        ORDER BY B.nama
    ", [$poli_id])->result();
    }


    public function getBelumAnamnesaFiltered($jenis, $poli_id = '', $dokter_id = '', $tanggal = null)
    {

        $base = $this->getBelumAnamnesaPoli('001', $tanggal); // panggil query utama
        // $base = $this->getBelumAnamnesa($tanggal); // panggil query utama


        // return var_dump($base);
        // die;

        return $this->applyFilters($base, $jenis, $poli_id, $dokter_id);
    }

    // public function getSudahAnamnesaFiltered($jenis, $poli_id = '', $dokter_id = '')
    public function getSudahAnamnesaFiltered($jenis, $poli_id = '', $dokter_id = '', $tanggal = null)
    {
        // $base = $this->getSudahAnamnesa();
        // $base = $this->getSudahAnamnesa('001', $tanggal); // panggil query utama
        $base = $this->getSudahAnamnesaPoli('001', $tanggal); // panggil query utama

        return $this->applyFilters($base, $jenis, $poli_id, $dokter_id);
    }

    private function applyFilters($data, $jenis, $poli_id, $dokter_id)
    {
        return array_filter($data, function ($r) use ($jenis, $poli_id, $dokter_id) {
            if ($jenis === 'POLIKLINIK' && $poli_id && $r->poli_id !== $poli_id) return false;
            if ($dokter_id && $r->dokter_id !== $dokter_id) return false;
            return true;
        });
    }


    public function getBelumAnamnesaPenunjang($tanggal = null, $lokasi_id = '001')
    {
        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }


        $sql = "SELECT 
                    A.pasien_id,
                    C.int_pasien_id,
                    C.nama AS nama_pas,
                    B.no_urut_dr,
                    C.tgl_lahir,
                    A.episode_id,
                    A.rekanan_id,
                    A.poli_id,
                    'Penunjang'::text AS nama_poli,
                        '-'::text AS nama_dr,
                    A.dokter_id,
                    K.kunjungan_rs,
                     COALESCE(KP.kunjungan_poli, 0) AS kunjungan_poli,
                    to_char(A.created_date, 'HH24:MI') AS jam_daftar
                FROM pc01_keu_episode A
                JOIN pc01_med_prwt_tr B
                ON A.episode_id = B.episode_id
                AND A.pasien_id  = B.pasien_id
                AND B.aktif      = '1'
                AND B.done_status= '00'
                AND B.lokasi_id  = '$lokasi_id'
                JOIN pc01_gen_pasien_ms C 
                ON A.pasien_id = C.pasien_id
                JOIN (
                    SELECT pasien_id, COUNT(*) AS kunjungan_rs
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = '$lokasi_id'
                    GROUP BY pasien_id
                ) K ON A.pasien_id = K.pasien_id
                LEFT JOIN (
                    SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = '$lokasi_id'
                    GROUP BY pasien_id, poli_id
                ) KP ON A.pasien_id = KP.pasien_id AND A.poli_id = KP.poli_id
                WHERE A.lokasi_id      = '$lokasi_id'
                AND A.status_episode IN ('00')
                AND A.kelas_id       = '6'
                AND A.jenis_episode  = 'O'
                AND A.poli_id        = 'APS'
                AND A.tgl_masuk      = '$tanggal'
                GROUP BY 
                    A.pasien_id, C.int_pasien_id, C.nama, B.no_urut_dr, C.tgl_lahir,KP.kunjungan_poli,
                    A.episode_id, A.rekanan_id, A.poli_id, 
                    A.dokter_id, K.kunjungan_rs, A.created_date
                ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC
            ";
        return $this->db->query($sql)->result();
    }

    public function getSudahAnamnesaPenunjang($tanggal = null, $lokasi_id = '001')
    {
        $sql = "SELECT 
                A.pasien_id,
                C.int_pasien_id,
                C.nama AS nama_pas,
                B.no_urut_dr,
                C.tgl_lahir,
                A.episode_id,
                A.rekanan_id,
                A.poli_id,
                'Penunjang'::text AS nama_poli,
                '-'::text AS nama_dr,
                A.dokter_id,
                K.kunjungan_rs,
                 COALESCE(KP.kunjungan_poli, 0) AS kunjungan_poli,
                to_char(A.created_date, 'HH24:MI') AS jam_daftar
            FROM pc01_keu_episode A
            JOIN pc01_med_prwt_tr B
            ON A.episode_id = B.episode_id
            AND A.pasien_id  = B.pasien_id
            AND B.aktif      = '1'
            AND B.done_status= '01'
            AND B.lokasi_id  = '$lokasi_id'
            JOIN pc01_gen_pasien_ms C 
            ON A.pasien_id = C.pasien_id
            JOIN (
                SELECT pasien_id, COUNT(*) AS kunjungan_rs
                FROM pc01_med_prwt_tr
                WHERE aktif = '1' AND lokasi_id = '$lokasi_id'
                GROUP BY pasien_id
            ) K ON A.pasien_id = K.pasien_id
            LEFT JOIN (
                    SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                    FROM pc01_med_prwt_tr
                    WHERE aktif = '1' AND lokasi_id = '$lokasi_id'
                    GROUP BY pasien_id, poli_id
                ) KP ON A.pasien_id = KP.pasien_id AND A.poli_id = KP.poli_id
            WHERE A.lokasi_id      = '$lokasi_id'
            AND A.status_episode IN ('00')
            AND A.kelas_id       = '6'
            AND A.jenis_episode  = 'O'
            AND A.poli_id        = 'APS'
            AND A.tgl_masuk      = '$tanggal'
            GROUP BY 
                A.pasien_id, C.int_pasien_id, C.nama, B.no_urut_dr, C.tgl_lahir,KP.kunjungan_poli,
                A.episode_id, A.rekanan_id, A.poli_id, 
                A.dokter_id, K.kunjungan_rs, A.created_date
            ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC
            ";
        return $this->db->query($sql)->result();
    }

    public function insertPengkajianAwal($data)
    {
        return $this->db->insert('pcare_manager.pc01_med_ases_kaji_awal', $data);
    }

    public function getAssessLab($episode_id)
    {
        return $this->db->get_where('pcare_manager.pc01_med_ases_laboratorium', [
            'episode_id' => $episode_id, 'aktif' => '1'
        ])->row_array();
    }

    public function getAssessRad($episode_id)
    {
        return $this->db->get_where('pcare_manager.pc01_med_ases_radiologi', [
            'episode_id' => $episode_id, 'aktif' => '1'
        ])->row_array();
    }

    public function getKajiAwal($episode_id)
    {
        return $this->db->get_where('pcare_manager.pc01_med_ases_kaji_awal', [
            'episode_id' => $episode_id
        ])->row_array();
    }

    public function updatePengkajianAwal($episode_id, $pasien_id, $data)
    {
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        return $this->db->update('pc01_med_ases_kaji_awal', $data);
    }



    // Update done_status
    public function updateDoneStatus($episode_id, $pasien_id)
    {
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        $this->db->where('aktif', '1');
        return $this->db->update('pcare_manager.pc01_med_prwt_tr', [
            'done_status' => '01'
        ]);
    }

    // Insert worklist
    public function insertWorklistLab($data)
    {
        return $this->db->insert('pcare_manager.pc01_worklist_lab', $data);
    }


    // Insert worklist
    public function insertWorklistRad($data)
    {
        return $this->db->insert('pcare_manager.pc01_worklist_rad', $data);
    }



    public function nonaktifkan_paliatifxxxxx($episode_id, $pasien_id)
    {
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        $this->db->where('aktif', '1');
        return $this->db->update('pcare_manager.pc01_med_ases_paliatif', ['aktif' => '0']);
    }

    public function nonaktifkan_paliatif($episode_id, $pasien_id)
    {
        return $this->db
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('aktif', '1')
            ->update('pcare_manager.pc01_med_ases_paliatif', [
                'aktif'      => '0',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('user_id_pc')
            ]);
    }


    public function insert_paliatif($data)
    {
        return $this->db->insert('pcare_manager.pc01_med_ases_paliatif', $data);
    }

    public function get_paliatifxxx($episode_id, $pasien_id)
    {
        return $this->db
            ->from('pcare_manager.pc01_med_ases_paliatif')
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('aktif', '1')
            ->get()
            ->row();
    }

    public function get_paliatif($episode_id, $pasien_id)
    {
        return $this->db
            ->from('pcare_manager.pc01_med_ases_paliatif')
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('aktif', '1')
            ->order_by('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();
    }
}
