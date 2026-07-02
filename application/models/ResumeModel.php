<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ResumeModel extends CI_Model
{
    public function getKunjPasienPenunjang($tanggal_mulai, $tanggal_selesai)
    {
        $sql = "SELECT 
                        A.pasien_id,
                        A.tgl_masuk,
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
                    AND B.lokasi_id  = '001'
                    JOIN pc01_gen_pasien_ms C 
                    ON A.pasien_id = C.pasien_id
                    JOIN (
                        SELECT pasien_id, COUNT(*) AS kunjungan_rs
                        FROM pc01_med_prwt_tr
                        WHERE aktif = '1' 
                        AND lokasi_id = '001'
                        GROUP BY pasien_id
                    ) K ON A.pasien_id = K.pasien_id
                    LEFT JOIN (
                        SELECT pasien_id, poli_id, COUNT(*) AS kunjungan_poli
                        FROM pc01_med_prwt_tr
                        WHERE aktif = '1' 
                        AND lokasi_id = '001'
                        GROUP BY pasien_id, poli_id
                    ) KP ON A.pasien_id = KP.pasien_id 
                    AND A.poli_id = KP.poli_id
                    WHERE A.lokasi_id = '001'
                    AND A.status_episode IN ('00')
                    AND A.kelas_id = '6'
                    AND A.jenis_episode = 'O'
                    AND A.poli_id = 'APS'
                    AND A.tgl_masuk BETWEEN ? AND ?
                    GROUP BY 
                        A.pasien_id,
                        C.int_pasien_id,
                        C.nama,
                        B.no_urut_dr,
                        C.tgl_lahir,
                        KP.kunjungan_poli,
                        A.episode_id,
                        A.rekanan_id,
                        A.poli_id,
                        A.dokter_id,
                        K.kunjungan_rs,
                        A.created_date,
                        A.tgl_masuk
                    ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC
                ";

        return $this->db->query($sql, [$tanggal_mulai, $tanggal_selesai])->result_array();
    }

    public function getKunjPasienPoli($jenis, $poliId, $dokterId, $tgl_mulai, $tgl_selesai)
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
            JOIN pc01_gen_pasien_ms C 
            ON A.pasien_id = C.pasien_id
            JOIN pc01_co_registrasi_ms D 
            ON A.episode_id = D.episode_id
            JOIN pc01_med_poli_ms E 
            ON A.poli_id = E.poli_id
            JOIN pc01_med_dokter_ms F 
            ON A.dokter_id = F.dokter_id
            JOIN pc01_med_poli_dokter G 
            ON A.dokter_id = G.dokter_id
            AND A.poli_id   = G.poli_id
            JOIN pc01_med_poli_ruang H 
            ON G.id_ruang = H.id_ruang
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
                WHERE aktif = '1'
                AND lokasi_id = ?
                GROUP BY pasien_id, poli_id
            ) KP ON A.pasien_id = KP.pasien_id 
            AND A.poli_id = KP.poli_id
            WHERE A.lokasi_id      = ?
            AND A.status_episode   = '00'
            AND A.kelas_id         = '6'
            AND A.jenis_episode    = 'O'
            AND D.status           = '0'
            -- filter tanggal
            AND A.tgl_masuk BETWEEN ? AND ?
            -- optional filter poli
            AND (? = '' OR A.poli_id = ?)
            -- optional filter dokter
            AND (? = '' OR A.dokter_id = ?)
            GROUP BY 
                A.pasien_id,
                C.int_pasien_id,
                C.nama,
                B.no_urut_dr,
                C.tgl_lahir,
                A.episode_id,
                A.rekanan_id,
                A.poli_id,
                E.keterangan,
                KP.kunjungan_poli,
                A.dokter_id,
                F.nama,
                K.kunjungan_rs,
                H.nama_ruang,
                A.created_date
            ORDER BY A.poli_id, A.dokter_id, B.no_urut_dr ASC
            ";

        return $this->db->query($sql, [
            '001', // B.lokasi_id
            '001', // subquery kunjungan_rs
            '001', // subquery kunjungan_poli
            '001', // A.lokasi_id
            $tgl_mulai,
            $tgl_selesai,
            $poliId,
            $poliId,
            $dokterId,
            $dokterId
        ])->result_array();
    }

    public function getKunjPasienAll($tgl_mulai, $tgl_selesai)
    {
        $sql = "SELECT *
                    FROM (
                    -- ======================
                    -- PENUNJANG
                    -- ======================
                    SELECT 
                        A.pasien_id,
                        C.int_pasien_id,
                        C.nama AS nama_pas,
                        B.no_urut_dr,
                        C.tgl_lahir,
                        A.episode_id,
                        A.rekanan_id,
                        A.poli_id,
                        'Penunjang' AS nama_poli,
                        A.dokter_id,
                        '-' AS nama_dr,
                        K.kunjungan_rs,
                        COALESCE(KP.kunjungan_poli,0) AS kunjungan_poli,
                        '-' AS nama_ruang,
                        TO_CHAR(A.created_date,'HH24:MI') AS jam_daftar
                    FROM pc01_keu_episode A
                    JOIN pc01_med_prwt_tr B
                    ON A.episode_id = B.episode_id
                    AND A.pasien_id = B.pasien_id
                    AND B.aktif = '1'
                    AND B.done_status = '01'
                    AND B.lokasi_id = '001'
                    JOIN pc01_gen_pasien_ms C
                    ON A.pasien_id = C.pasien_id
                    JOIN (
                        SELECT pasien_id, COUNT(*) kunjungan_rs
                        FROM pc01_med_prwt_tr
                        WHERE aktif='1'
                        AND lokasi_id='001'
                        GROUP BY pasien_id
                    ) K ON A.pasien_id = K.pasien_id
                    LEFT JOIN (
                        SELECT pasien_id,poli_id,COUNT(*) kunjungan_poli
                        FROM pc01_med_prwt_tr
                        WHERE aktif='1'
                        AND lokasi_id='001'
                        GROUP BY pasien_id,poli_id
                    ) KP ON A.pasien_id = KP.pasien_id
                    AND A.poli_id = KP.poli_id
                    WHERE A.lokasi_id='001'
                    AND A.status_episode='00'
                    AND A.kelas_id='6'
                    AND A.jenis_episode='O'
                    AND A.poli_id='APS'
                    AND A.tgl_masuk BETWEEN ? AND ?
                    UNION ALL
                    -- ======================
                    -- POLIKLINIK
                    -- ======================
                    SELECT 
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
                        COALESCE(KP.kunjungan_poli,0) AS kunjungan_poli,
                        H.nama_ruang,
                        TO_CHAR(A.created_date,'HH24:MI') AS jam_daftar
                    FROM pc01_keu_episode A
                    JOIN pc01_med_prwt_tr B
                    ON A.episode_id = B.episode_id
                    AND A.pasien_id = B.pasien_id
                    AND B.aktif='1'
                    AND B.done_status='00'
                    AND B.lokasi_id='001'
                    JOIN pc01_gen_pasien_ms C ON A.pasien_id=C.pasien_id
                    JOIN pc01_co_registrasi_ms D ON A.episode_id=D.episode_id
                    JOIN pc01_med_poli_ms E ON A.poli_id=E.poli_id
                    JOIN pc01_med_dokter_ms F ON A.dokter_id=F.dokter_id
                    JOIN pc01_med_poli_dokter G ON A.dokter_id=G.dokter_id AND A.poli_id=G.poli_id
                    JOIN pc01_med_poli_ruang H ON G.id_ruang=H.id_ruang
                    JOIN (
                        SELECT pasien_id,COUNT(*) kunjungan_rs
                        FROM pc01_med_prwt_tr
                        WHERE aktif='1'
                        AND lokasi_id='001'
                        GROUP BY pasien_id
                    ) K ON A.pasien_id=K.pasien_id
                    LEFT JOIN (
                        SELECT pasien_id,poli_id,COUNT(*) kunjungan_poli
                        FROM pc01_med_prwt_tr
                        WHERE aktif='1'
                        AND lokasi_id='001'
                        GROUP BY pasien_id,poli_id
                    ) KP ON A.pasien_id=KP.pasien_id
                    AND A.poli_id=KP.poli_id
                    WHERE A.lokasi_id='001'
                    AND A.status_episode='00'
                    AND A.kelas_id='6'
                    AND A.jenis_episode='O'
                    AND D.status='0'
                    AND A.tgl_masuk BETWEEN ? AND ?
                    ) X
                    ORDER BY poli_id,dokter_id,no_urut_dr";

        return $this->db->query($sql, [
            $tgl_mulai,
            $tgl_selesai,
            $tgl_mulai,
            $tgl_selesai
        ])->result_array();
    }
}
