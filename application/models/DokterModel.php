<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DokterModel extends CI_Model
{

    function getPoliDokter($dokter_id)
    {
        $sql = "SELECT DISTINCT b.poli_id, c.keterangan AS nama_poli
        FROM pc01_med_dokter_ms a
        JOIN pc01_med_prwt_tr b ON a.dokter_id = b.dokter_id 
        JOIN pc01_med_poli_ms c ON b.poli_id = c.poli_id
        WHERE a.dokter_id = ?
          AND a.aktif = '1'
          AND b.aktif = '1'
        ORDER BY c.keterangan
    ";
        return $this->db->query($sql, [$dokter_id])->result();
    }

    // function listpasien($dokterid, $poli_id = null)
    function listpasien($dokterid, $poli_id = null, $tanggal = null)
    {

        // return var_dump($tanggal);
        // die;
        $filter_poli = "";
        $filter_tanggal = "";

        if ($poli_id !== null && $poli_id !== "") {
            $filter_poli = " AND a.poli_id = '{$poli_id}' ";
        }

        if ($tanggal != null && $tanggal != "") {
            $filter_tanggal = " AND a.tgl_masuk::DATE = '{$tanggal}' ";
        } else {
            $filter_tanggal = " AND a.tgl_masuk::DATE = CURRENT_DATE ";
        }

        $query = "SELECT 
        a.lokasi_id, a.episode_id, a.pasien_id, a.rekanan_id,
        c.int_pasien_id, c.nama,
        TO_CHAR(c.tgl_lahir, 'DD.MM.YYYY') AS tgl_lahir,
        b.no_urut_dr, d.urut, b.poli_id,
        e.keterangan AS nama_poli,
        CASE WHEN c.sex_id = 'L' THEN 'LAKI - LAKI'
             WHEN c.sex_id = 'P' THEN 'PEREMPUAN' END AS jenis_kelamin,
        CASE 
            WHEN EXISTS (SELECT 1 FROM pc01_worklist_frm w
                         WHERE w.aktif='1' AND w.status='9'
                         AND w.lokasi_id=a.lokasi_id
                         AND w.episode_id=a.episode_id
                         AND w.pasien_id=a.pasien_id)
                THEN '3'
            WHEN EXISTS (SELECT 1 FROM pcare_manager.pc01_co_selesai_periksa e 
                         WHERE e.episode_id = a.episode_id)
                THEN '1'
            ELSE '0'
        END AS status_periksa
    FROM pc01_keu_episode a
    INNER JOIN pc01_med_prwt_tr b 
        ON a.lokasi_id=b.lokasi_id 
        AND a.episode_id=b.episode_id 
        AND a.pasien_id=b.pasien_id
    INNER JOIN pc01_gen_pasien_ms c 
        ON a.lokasi_id=c.lokasi_id 
        AND a.pasien_id=c.pasien_id
    INNER JOIN pc01_co_registrasi_online_hd d 
        ON a.episode_id=d.episode_id
    INNER JOIN pc01_med_poli_ms e 
        ON a.poli_id=e.poli_id
    WHERE a.aktif='1'
      AND b.aktif='1'
      AND a.status_episode <> '99'
      AND b.done_status='01'
      AND a.dokter_id='{$dokterid}'
      {$filter_poli}
      {$filter_tanggal}
    ORDER BY d.urut";

        $queryxx = "SELECT 
                a.lokasi_id, a.episode_id, a.pasien_id, a.rekanan_id,
                c.int_pasien_id, c.nama,
                TO_CHAR(c.tgl_lahir, 'DD.MM.YYYY') AS tgl_lahir,
                b.no_urut_dr, d.urut, b.poli_id,
                e.keterangan AS nama_poli,
                CASE WHEN c.sex_id = 'L' THEN 'LAKI - LAKI'
                     WHEN c.sex_id = 'P' THEN 'PEREMPUAN' END AS jenis_kelamin,
                CASE 
                    WHEN EXISTS (SELECT 1 FROM pc01_worklist_frm w
                                 WHERE w.aktif='1' AND w.status='9'
                                 AND w.lokasi_id=a.lokasi_id
                                 AND w.episode_id=a.episode_id
                                 AND w.pasien_id=a.pasien_id)
                        THEN '3'
                    WHEN EXISTS (SELECT 1 FROM pcare_manager.pc01_co_selesai_periksa e 
                                 WHERE e.episode_id = a.episode_id)
                        THEN '1'
                    ELSE '0'
                END AS status_periksa
            FROM pc01_keu_episode a
            INNER JOIN pc01_med_prwt_tr b 
                ON a.lokasi_id=b.lokasi_id 
                AND a.episode_id=b.episode_id 
                AND a.pasien_id=b.pasien_id
            INNER JOIN pc01_gen_pasien_ms c 
                ON a.lokasi_id=c.lokasi_id 
                AND a.pasien_id=c.pasien_id
            INNER JOIN pc01_co_registrasi_online_hd d 
                ON a.episode_id=d.episode_id
            INNER JOIN pc01_med_poli_ms e 
                ON a.poli_id=e.poli_id
            WHERE a.aktif='1'
              AND b.aktif='1'
              AND a.status_episode <> '99'
              AND b.done_status='01'
              AND a.tgl_masuk::DATE = CURRENT_DATE
              AND a.dokter_id='{$dokterid}'
              {$filter_poli}
            ORDER BY d.urut";

        return $this->db->query($query)->result();
    }
    function listpasienx($dokterid)
    {
        $query = "SELECT a.lokasi_id,
                        a.episode_id,
                        a.pasien_id,
                        a.rekanan_id, 
                        c.int_pasien_id,
                        c.nama,
                        TO_CHAR(c.tgl_lahir, 'DD.MM.YYYY') AS tgl_lahir,
                        b.no_urut_dr,
                        d.urut,
                        b.poli_id,
                        e.keterangan as nama_poli,
                        CASE 
                            WHEN c.sex_id = 'L' THEN 'LAKI - LAKI'
                            WHEN c.sex_id = 'P' THEN 'PEREMPUAN'
                        END AS jenis_kelamin,
                        CASE 
                            WHEN EXISTS (
                                SELECT 1 
                                FROM pc01_worklist_frm w
                                WHERE w.aktif = '1'
                                    AND w.status = '9'
                                    AND w.lokasi_id = a.lokasi_id
                                    AND w.episode_id = a.episode_id
                                    AND w.pasien_id = a.pasien_id
                            ) THEN '3'  
                            WHEN EXISTS (
                                SELECT 1 
                                FROM pcare_manager.pc01_co_selesai_periksa e 
                                WHERE e.episode_id = a.episode_id
                            ) THEN '1' 
                            ELSE '0'  
                        END AS status_periksa
                    FROM pc01_keu_episode a
                        INNER JOIN pc01_med_prwt_tr b 
                            ON a.lokasi_id = b.lokasi_id 
                            AND a.episode_id = b.episode_id 
                            AND a.pasien_id = b.pasien_id
                        INNER JOIN pc01_gen_pasien_ms c 
                            ON a.lokasi_id = c.lokasi_id 
                            AND a.pasien_id = c.pasien_id
                        INNER JOIN pc01_co_registrasi_online_hd d 
                            ON a.episode_id = d.episode_id
                        INNER JOIN pc01_med_poli_ms e 
                            ON a.poli_id = e.poli_id
                    WHERE a.aktif = '1'
                    AND b.aktif = '1'
                    AND a.status_episode <> '99'
                    AND b.done_status = '01'
                    AND a.tgl_masuk::DATE = CURRENT_DATE
                    AND a.dokter_id = '{$dokterid}'
                    ORDER BY d.urut";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();


        return $recordset;
    }

    public function getDtlKrmKunjungan($episodeId)
    {
        $sql = "SELECT 
            a.tgl_masuk,
            a.episode_id, 
            b.int_pasien_id, 
            a.pasien_id, 
            a.rekanan_id,
            a.tgl_masuk, 
            e.keluhan_pasien AS Keluhan,
            e.tv_tekanan_darah AS Sistole, 
            e.tv_tekanan_darah2 AS Diastole, 
            e.ant_bb AS Berat_Badan, 
            e.ant_tb AS Tinggi_Badan, 
            e.tv_frek_nafas AS Respiratory_Rate, 
            e.tv_heart_rate AS Heart_Rate,
            e.ant_ling_perut AS Ling_Perut,
            e.tv_suhu, 
            b.no_kartuprov, 
            b.nama, 
            b.no_identitas, 
            b.tgl_lahir, 
            c.poli_id, 
            c.poli_bpjsid ,
            c.keterangan AS poli_nama, 
            a.dokter_id, 
            d.prefix ,
            d.nama AS nama_dokter,
            f.s as anamnesa
        FROM 
            pc01_keu_episode a
        join  
            pc01_gen_pasien_ms b ON a.pasien_id = b.pasien_id
        join 
            pc01_med_poli_ms c ON a.poli_id = c.poli_id
        join 
            pc01_med_dokter_ms d ON a.dokter_id = d.dokter_id AND d.aktif = '1'
        join  
            pc01_med_anamawal e on a.episode_id =e.episode_id 
        join 
            pc01_co_diagnosa_dt f on a.episode_id =f.episode_id and f.show_item ='1'
        WHERE 
            a.episode_id = ?
        AND 
            a.aktif = '1'";

        // Gunakan binding untuk menggantikan parameter
        $query = $this->db->query($sql, array($episodeId));

        // Kembalikan hasil sebagai array
        return $query->row_array();
    }

    function loaddatapasien($episodeid, $pasienid)
    {

        $query = "SELECT DISTINCT
                a.lokasi_id, 
                a.episode_id, 
                b.trans_id, 
                a.pasien_id, 
                dt.trans_co, 
                -- q.keluhan_pasien, 
                q.keluhan_utama, 
                -- q.ant_imt ,
                c.int_pasien_id, 
                c.nama, 
                c.no_kartuprov, 
                to_char(c.tgl_lahir, 'DD.MM.YYYY') tgl_lahir, 
                a.rekanan_id, 
                d.nama rekanan, 
                a.poli_id, 
                p.poli_bpjsid, 
                a.dokter_id, 
                r.prefix, 
                a.tgl_masuk,
                CASE 
                    WHEN c.sex_id = 'L' THEN 'LAKI - LAKI'
                    WHEN c.sex_id = 'P' THEN 'PEREMPUAN'
                END jenis_kelamin,
                CASE 
                    WHEN dt.trans_co <> '' THEN 'Y'
                END ada_soap,
                (SELECT COUNT(k.episode_id) 
                    FROM pc01_keu_episode k 
                    WHERE k.lokasi_id = a.lokasi_id 
                    AND k.pasien_id = a.pasien_id 
                    AND k.aktif = '1' 
                    AND k.status_episode <> '99' 
                    AND k.poli_id = a.poli_id) kunj_ke,
                dg.episode_id episode_diagnosa, 
                dg.icd10, 
                dg.diagnosa,
                dt.episode_id episode_soap, 
                dt.trans_soap, 
                dt.s, 
                dt.o, 
                dt.a, 
                dt.p,
                al.alergi_id_mknn, 
                al.alergi_id_udr, 
                al.alergi_id_obat, 
                -- q.tv_tekanan_darah, 
                q.fisik_td,
                -- q.tv_tekanan_darah2, 
                -- q.ant_bb, 
                q.fisik_bb,
                -- q.ant_tb, 
                q.fisik_tb,
                -- q.tv_frek_nafas,  
                q.fisik_rr,
                -- q.tv_heart_rate,  
                q.fisik_nadi,
                -- q.ant_ling_perut, 
                q.fisik_lila, 
                -- q.tv_suhu, 
                q.fisik_suhu,
                -- q.created_by, 
                q.perawat_nama,
                z.kondisi_pulang,            -- Data dari pc01_keu_bpjs_pcare
                z.tingkat_sadar,               -- Data dari pc01_keu_bpjs_pcare
                z.prognosis             -- Data dari pc01_keu_bpjs_pcare
            FROM 
                pc01_keu_episode a
            LEFT JOIN pc01_co_diagnosa_ms dg 
                ON dg.lokasi_id = a.lokasi_id 
                AND dg.episode_id = a.episode_id 
                AND dg.pasien_id = a.pasien_id 
                AND dg.show_item = '1'
            LEFT JOIN pc01_co_diagnosa_dt dt 
                ON dt.lokasi_id = a.lokasi_id 
                AND dt.episode_id = a.episode_id 
                AND dt.pasien_id = a.pasien_id 
                AND dt.show_item = '1' 
                AND dt.flag_hapus = '1'
            LEFT JOIN pc01_co_alergi_bpjs_dt al 
                ON al.lokasi_id = a.lokasi_id 
                AND al.pasien_id = a.pasien_id 
                AND al.aktif = '1'
            LEFT JOIN pc01_med_poli_ms p
                ON a.poli_id = p.poli_id 
                AND p.aktif = '1'
            LEFT JOIN pc01_med_dokter_ms r 
                ON a.dokter_id = r.dokter_id 
                AND r.aktif = '1'
            LEFT JOIN pc01_med_ases_kaji_awal q
                ON a.episode_id = q.episode_id  
                AND q.aktif = '1'
            LEFT JOIN pc01_resume_medis z 
                ON z.episode_id = a.episode_id 
                AND z.aktif = '1', 
                pc01_med_prwt_tr b, 
                pc01_gen_pasien_ms c, 
                pc01_keu_rekanan_ms d
            WHERE 
                a.lokasi_id = b.lokasi_id
                AND a.episode_id = b.episode_id
                AND a.pasien_id = b.pasien_id
                AND a.lokasi_id = c.lokasi_id
                AND a.pasien_id = c.pasien_id
                AND a.aktif = '1'
                AND a.lokasi_id = d.lokasi_id
                AND a.rekanan_id = d.rekanan_id
                AND a.episode_id = '{$episodeid}'    
                AND a.pasien_id = '{$pasienid}'";

        $recordset = $this->db->query($query);
        $recordset = $recordset->row();

        return $recordset;
    }

    public function getDataDisplay($episId, $pasId)
    {
        $sql = "SELECT 
            a.episode_id,
            a.pasien_id,
            c.nama AS nama_pasien,
            a.urut,
            a.poli_id,
            b.keterangan AS nama_poli,
            a.dokter_id
        FROM pc01_co_registrasi_online_hd a
        JOIN pc01_med_poli_ms b ON a.poli_id = b.poli_id AND b.aktif = '1'
        JOIN pc01_gen_pasien_ms c ON a.pasien_id = c.pasien_id AND c.aktif = '1'
        WHERE a.episode_id = ? 
          AND a.pasien_id = ? 
          AND a.aktif = '1' 
          AND a.hadir = 'Y' 
          AND a.tgl_hadir IS NOT NULL
        LIMIT 1
        ";

        $query = $this->db->query($sql, [$episId, $pasId]);

        return $query->row_array(); // atau ->row() jika ingin object
    }

    function insertDisplay($EID, $no, $idPoli, $poli, $ruang, $pasien, $idDokter, $idPasien, $deviceId)
    {
        // Cek apakah IP aktif dalam sistem display
        $sysdisplay = $this->db->query("SELECT COUNT(ip_pc) ada FROM pc01_co_sys_display WHERE ip_pc  = ? AND aktif  = '1'", [$deviceId])->row_array();


        if ($sysdisplay['ada'] > 0) {
            $this->db->where('dokter_id', $idDokter);
            $this->db->where('poli_id', $idPoli);
            $this->db->where('status', '1');
            $this->db->where("DATE(created_date) = CURRENT_DATE", null, false); // raw SQL
            $update = $this->db->update('pc01_co_display_poli', ['status' => '0']);



            if ($update) {

                // Insert data baru
                $insertData = [
                    'pasien_id'   => $idPasien,
                    'episode_id'  => $EID,
                    'dokter_id'   => $idDokter,
                    'no_urut'     => $no,
                    'poli_id'     => $idPoli,
                    'nama_poli'   => $poli,
                    'ruang_poli'  => $ruang,
                    'pasien'      => $pasien,
                    'ip'          => $deviceId
                ];

                $this->db->insert('pc01_co_display_poli', $insertData);
                return true;
            }
        }
        return false;
    }

    function loadsoaplama($pasienid, $poliid)
    {
        $query = "SELECT a.episode_id, a.pasien_id, b.trans_soap, b.s, b.o, b.a, b.p
                    from pc01_keu_episode a, pc01_co_diagnosa_dt b
                    where a.aktif = '1'
                    and a.status_episode <> '99'
                    and a.lokasi_id = b.lokasi_id 
                    and a.episode_id = b.episode_id 
                    and a.pasien_id = b.pasien_id
                    and b.show_item = '1'
                    and b.created_date = (select max(d.created_date) from pc01_co_diagnosa_dt d where d.lokasi_id = b.lokasi_id
                                            and d.pasien_id = b.pasien_id and d.poli_id = b.poli_id and d.show_item = '1')  
                    and a.pasien_id = '{$pasienid}'
                    and a.poli_id = '{$poliid}'
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->row();

        return $recordset;
    }

    public function getAlergiFromDB($jenis)
    {
        $query = "SELECT global_id AS kdAlergi, keterangan AS nmAlergi
              FROM pc01_gen_global_ms 
              WHERE jenis_id = ? AND aktif = '1'";

        return $this->db->query($query, [$jenis])->result_array();
    }

    public function getPrognosaFromDB()
    {
        $query = "SELECT global_id AS kdPrognosa, keterangan AS nmPrognosa
              FROM pc01_gen_global_ms 
              WHERE jenis_id = 'PROGNO' AND aktif = '1'";

        return $this->db->query($query)->result_array();
    }

    public function getStatPlgApi()  //Dari DB
    {

        $data = $this->dm->getStatPlgFromDB();

        // Set header untuk memastikan respons dalam format JSON
        header('Content-Type: application/json');

        if (!empty($data)) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data Status Pulang tidak ditemukan di database"
            ]);
        }
    }

    public function getStatPlgFromDB()
    {
        $query = "SELECT global_id AS kdStatusPulang, keterangan AS nmStatusPulang
              FROM pc01_gen_global_ms 
              WHERE jenis_id = 'KONPLG' AND aktif = '1'";

        return $this->db->query($query)->result_array();
    }

    public function getSadarFromDB()
    {
        $query = "SELECT global_id AS kdSadar, keterangan AS nmSadar
              FROM pc01_gen_global_ms 
              WHERE jenis_id = 'KAJI_SADAR' AND aktif = '1'";

        return $this->db->query($query)->result_array();
    }

    function ceksudahmulai($data)
    {

        $query = "SELECT lokasi_id, episode_id, pasien_id
                    from pc01_co_mulai_periksa
                    where show_item = '1'
                    and lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'                
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function cekstatusresep($data)
    {
        $query = "SELECT a.lokasi_id, a.episode_id, a.pasien_id, a.trans_co, a.status
                    from pc01_worklist_frm a
                    where a.aktif = '1'
                    and a.status = '9'
                    and a.lokasi_id = '{$data['LOKASI_ID']}'
                    and a.episode_id = '{$data['EPISODE_ID']}'    
                    and a.pasien_id = '{$data['PASIEN_ID']}'
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->row();

        return $recordset;
    }

    function loadicd10utama($data)
    {
        // $query = "SELECT DISTINCT 
        //                 b.kode_icd, 
        //                 a.diagnosa, 
        //                 a.icd10, 
        //                 c.diag_non_sp
        //             FROM 
        //                 pc01_co_diagnosa_ms a
        //             JOIN 
        //                 pc01_med_icd10_ms b ON a.icd10 = b.kode_icd
        //             LEFT JOIN 
        //                 pc01_keu_bpjs_pcare c ON a.episode_id = c.episode_id
        //             WHERE 
        //                 a.show_item = '1'
        //                 AND a.lokasi_id = '{$data['LOKASI_ID']}'
        //                 AND a.episode_id = '{$data['EPISODE_ID']}'
        //                 AND a.pasien_id = '{$data['PASIEN_ID']}'";

        $query = "SELECT DISTINCT 
                        b.kode_icd, 
                        a.diagnosa, 
                        a.icd10 ,
                        c.diag_non_sp
                    FROM 
                        pc01_co_diagnosa_ms a
                    JOIN 
                        pc01_med_icd10_ms b ON a.icd10 = b.kode 
                    LEFT JOIN 
                        pc01_keu_bpjs_pcare c ON a.episode_id = c.episode_id
                    WHERE 
                        a.show_item = '1'
                        AND a.lokasi_id = '{$data['LOKASI_ID']}'
                        AND a.episode_id = '{$data['EPISODE_ID']}'
                        AND a.pasien_id = '{$data['PASIEN_ID']}'";

        $recordset = $this->db->query($query);
        $recordset = $recordset->row();
        return $recordset;
    }

    function loadicd10sek($data)
    {
        $query = "SELECT a.episode_id, a.pasien_id, a.kode, a.kode_icd, a.diagnosa, a.trans_diag, a.created_date
                    from pc01_co_diag_dt a
                    where a.show_item = '1'
                    and a.lokasi_id = '{$data['LOKASI_ID']}' 
                    and episode_id = '{$data['EPISODE_ID']}' 
                    and pasien_id = '{$data['PASIEN_ID']}'               
                    order by a.created_date
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function loadicd10($filtercari)
    {
        $query = "SELECT a.kode, a.kode_icd, a.nm_diag1
                    from pc01_med_icd10_ms a
                    where a.show_item = '1'
                    and ( upper(a.kode_icd) like upper('%{$filtercari}%') or  upper(a.nm_diag1) like upper('%{$filtercari}%') )                     
                    order by a.kode_icd
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    public function cekICD10($kode_icd)
    {
        $this->db->select('kode_icd');
        $this->db->from('pc01_med_icd10_ms');
        $this->db->where('kode_icd', $kode_icd);
        $query = $this->db->get();

        return $query->row_array(); // Mengembalikan data jika ditemukan
    }

    public function insertICD10($data)
    {
        return $this->db->insert('pc01_med_icd10_ms', $data);
    }

    function simpanicd10sek($data)
    {
        $query = "INSERT into pc01_co_diag_dt
                    (lokasi_id, episode_id, trans_id, pasien_id, trans_co, trans_diag, dokter_id, kode, kode_icd, diagnosa, show_item, created_date, created_by)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$data['TRANS_CO']}', '{$data['TRANS_DIAG']}', '{$data['DOKTER_ID']}', '{$data['KODE']}', '{$data['KODE_ICD']}', '{$data['DIAGNOSA']}', '1', (select current_timestamp), '{$data['CREATED_BY']}')
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function hapusicd10sek($data)
    {
        $query = "UPDATE pc01_co_diag_dt set show_item = '0'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                    and trans_diag = '{$data['TRANS_DIAG']}'
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function loadicd9($data)
    {
        $query = "SELECT a.episode_id, a.pasien_id, a.kode, a.kode_icd, a.tindakan, a.trans_tin, a.created_date
                    from pc01_co_tindak_dt a
                    where a.show_item = '1'
                    and a.lokasi_id = '{$data['LOKASI_ID']}' 
                    and episode_id = '{$data['EPISODE_ID']}' 
                    and pasien_id = '{$data['PASIEN_ID']}'               
                    order by a.created_date
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function loadmastericd9($filtercari)
    {
        $query = "SELECT a.kode, a.kode_icd, a.long_description
                    from pc01_med_icd9_ms a
                    where a.show_item = '1'
                    and ( upper(a.kode_icd) like upper('%{$filtercari}%') or  upper(a.long_description) like upper('%{$filtercari}%') )                     
                    order by a.kode_icd
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function simpanicd9($data)
    {
        $query = "INSERT into pc01_co_tindak_dt
                    (lokasi_id, episode_id, trans_id, pasien_id, trans_co, trans_tin, dokter_id, kode, kode_icd, tindakan, show_item, created_date, created_by)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$data['TRANS_CO']}', '{$data['TRANS_TIN']}', '{$data['DOKTER_ID']}', '{$data['KODE']}', '{$data['KODE_ICD']}', '{$data['TINDAKAN']}', '1', (select current_timestamp), '{$data['CREATED_BY']}')
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function hapusicd9($data)
    {
        $query =
            "
                    update pc01_co_tindak_dt set show_item = '0'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                    and trans_tin = '{$data['TRANS_TIN']}'
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function loaditemharga($data)
    {
        $query = "SELECT a.*
                    from(
                        select a.lokasi_id, a.episode_id, a.pasien_id, a.layan_id, a.nama_layan, a.qty, a.qty*b.harga total_harga, a.urut, 0 priority
                        from pc01_co_alkes_dt a, pc01_keu_harga_dt b
                        where a.show_item = '1'
                        and a.layan_id = b.layan_id
                        and b.kelas_id = '6'
                        and a.lokasi_id = '{$data['LOKASI_ID']}'
                        and a.episode_id = '{$data['EPISODE_ID']}'
                        and a.pasien_id = '{$data['PASIEN_ID']}'
                        union
                        select a.lokasi_id, a.episode_id, a.pasien_id, a.obat_id layan_id, a.nama_obat nama_layan, a.qty, a.qty*c.harga_jual total_harga, a.urut, 1 priority
                        from pc01_co_resep_dt a, pc01_worklist_frm b, pc01_frm_obat_ms c
                        where a.show_item <> '0'
                        and a.lokasi_id = b.lokasi_id 
                        and a.episode_id = b.episode_id
                        and a.pasien_id = b.pasien_id
                        and a.trans_co = b.trans_co
                        and a.lokasi_id = c.lokasi_id 
                        and a.obat_id = c.obat_id
                        and a.lokasi_id = '{$data['LOKASI_ID']}'
                        and a.episode_id = '{$data['EPISODE_ID']}'
                        and a.pasien_id = '{$data['PASIEN_ID']}'
                    )a
                    order by a.priority, a.urut
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function mulaiperiksa($data)
    {


        $querymulai = "INSERT into pc01_co_mulai_periksa
                    (lokasi_id, episode_id, trans_id, pasien_id, dokter_id, poli_id, created_date, created_by, show_item)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$data['DOKTER_ID']}', '{$data['POLI_ID']}', (select current_timestamp), '{$data['CREATED_BY']}', '1' )
                ";
        $recordquerymulai = $this->db->query($querymulai);

        return $recordquerymulai;
    }

    function gettransco()
    {
        $query = "SELECT get_trans_co() trans_co";

        $recordset = $this->db->query($query)->row();

        return $recordset;
    }


    function gettransinput()
    {
        $query = "SELECT get_trans_input() trans_input";

        $recordset = $this->db->query($query)->row();

        return $recordset;
    }

    function simpansoap($data)
    {
        $queryhapus = "UPDATE pc01_co_diagnosa_dt set show_item = '0'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                    and trans_co = '{$data['TRANS_CO']}'
                    and show_item = '1'
                ";
        $recordsethapus = $this->db->query($queryhapus);

        $query = "INSERT into pc01_co_diagnosa_dt
                    (lokasi_id, episode_id, trans_id, pasien_id, trans_co, trans_soap, tanggal, poli_id, dokter_id, s, o, a, p, soap_ke, show_item, created_date, created_by, flag_hapus)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$data['TRANS_CO']}', '{$data['TRANS_INPUT']}', '{$data['TANGGAL']}', '{$data['POLI_ID']}', '{$data['DOKTER_ID']}', '{$data['S']}', '{$data['O']}', '{$data['A']}', '{$data['P']}' 
                    ,   (select count(episode_id) + 1
                        from pc01_co_diagnosa_dt 
                        where episode_id = '{$data['EPISODE_ID']}' 
                        and pasien_id = '{$data['PASIEN_ID']}' 
                        and trans_co = '{$data['TRANS_CO']}') 
                    , '1', (select current_timestamp),  '{$data['CREATED_BY']}', '1'  
                    )
                ";
        $recordset = $this->db->query($query);

        return $recordsethapus && $recordset;
    }

    function loadtindakanmodal($data)
    {
        $query = "SELECT a.layan_id, a.nama_layan, a.qty, b.harga, a.trans_alkes
                    from pc01_co_alkes_dt a, pc01_keu_harga_dt b
                    where a.show_item = '1'
                    and a.layan_id = b.layan_id
                    and b.kelas_id = '6'
                    and b.aktif = '1'
                    and a.lokasi_id = '{$data['LOKASI_ID']}' 
                    and a.episode_id = '{$data['EPISODE_ID']}' 
                    and a.pasien_id = '{$data['PASIEN_ID']}'                  
                    order by a.nama_layan
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }


    function loadmastertindakan($filtercari)
    {
        $query = "SELECT a.layan_id, a.nama_layan1, a.kategori_id, b.harga
                    from pc01_keu_layan_ms a, pc01_keu_harga_dt b
                    where a.aktif = '1'
                    and a.layan_id = b.layan_id
                    and b.kelas_id = '6'
                    and b.aktif = '1'
                    and a.kategori_id ='JKL-UMU'
                    and upper(a.nama_layan1) like upper('%{$filtercari}%')  and a.layan_id not IN('TDK000000000005','TDK000000000004','TDK000000000009')                  
                    order by a.nama_layan1
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }


    function hapustindakan($data, $vartransalkes)
    {
        $query = "UPDATE pc01_co_alkes_dt set show_item = '0'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                    and trans_alkes not in ({$vartransalkes})
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function hapustindakanall($data)
    {
        $query = "UPDATE pc01_co_alkes_dt set show_item = '0'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function simpantindakan($data, $tindakanData)
    {
        $query = "INSERT INTO pc01_co_alkes_dt(
                    lokasi_id,
                    episode_id, 
                    pasien_id,
                    trans_co, 
                    trans_id, 
                    trans_alkes,
                    tanggal,
                    layan_id, 
                    nama_layan, 
                    qty,
                    urut,
                    dokter_id,
                    show_item,
                    created_date,
                    created_by
                )
                VALUES
                (
                    '{$data['LOKASI_ID']}',  
                    '{$data['EPISODE_ID']}', 
                    '{$data['PASIEN_ID']}',
                    '{$data['TRANS_CO']}',                      
                    '{$data['TRANS_ID']}', 
                    '{$data['TRANS_ALKES']}',
                    '{$data['TANGGAL']}', 
                    '{$tindakanData['LAYAN_ID']}', 
                    '{$tindakanData['NAMA_LAYAN']}', 
                    '{$tindakanData['QTY']}',
                    1, 
                    '{$data['DOKTER_ID']}',
                    '1', 
                    (select current_timestamp),
                    '{$data['CREATED_BY']}'
                    )
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }


    function loadinputobat($data)
    {
        $query = "SELECT a.lokasi_id, a.episode_id, a.pasien_id, a.trans_co, b.obat_id, b.nama_obat, b.qty, hitung_stok_obat('DEPO00000000APT', b.obat_id) stok, b.satuan_id
                    , (select c.keterangan from pc01_frm_satuan_ms c where c.satuan_id = b.satuan_id) satuan
                    , b.free_dosis, b.signa_id, b.signa_nama, b.signa_dokter, b.catatan, b.urut, b.type, b.header
                    from pc01_worklist_frm a, pc01_co_resep_dt b
                    where a.lokasi_id = b.lokasi_id
                    and a.episode_id = b.episode_id
                    and a.pasien_id = b.pasien_id
                    and a.aktif = '1' and b.show_item in ('1','2')                    
                    and a.lokasi_id = '{$data['LOKASI_ID']}' 
                    and a.episode_id = '{$data['EPISODE_ID']}' 
                    and a.pasien_id = '{$data['PASIEN_ID']}' 
                    order by b.urut, b.type 
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }


    function loadmasterobat($filtercari)
    {
        $query = "SELECT a.lokasi_id, a.obat_id, a.nama nama_obat, hitung_stok_obat('DEPO00000000APT', a.obat_id) stok, a.harga_sat_ppn harga, a.harga_jual, b.satuan_id, b.keterangan satuan
                    from pc01_frm_obat_ms a, pc01_frm_satuan_ms b
                    where is_pembungkus = 'N'
                    and upper(a.nama) like upper('%{$filtercari}%')
                    and a.satuan_kecil_id = b.satuan_id
                    order by a.nama
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function carisigna($filtercari)
    {
        $query = "SELECT '' lokasi_id, '' signa_id, '' signa, 0 urut
                    union
                    select a.lokasi_id, a.signa_id, a.signa, 1 urut
                    from pc01_frm_signa_ms a
                    where a.aktif = '1'
                    and upper(a.signa) like upper('%{$filtercari}%')
                    order by urut, signa
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function caribungkus($filtercari)
    {
        $query = "SELECT '' lokasi_id, '' obat_id, '' nama_obat, 0 stok, '' satuan_id, '' keterangan, 0 urut
                    union
                    select a.lokasi_id, a.obat_id, a.nama nama_obat, hitung_stok_obat('DEPO00000000APT', a.obat_id) stok, b.satuan_id, b.keterangan, 1 urut
                    from pc01_frm_obat_ms a, pc01_frm_satuan_ms b
                    where is_pembungkus = 'Y'
                    and upper(a.nama) like upper('%{$filtercari}%')
                    and a.satuan_kecil_id = b.satuan_id
                    order by urut, nama_obat";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function hapusreseplama($data)
    {
        $queryhd = "UPDATE pc01_worklist_frm set aktif = '0'
                    where lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'                    
                ";
        $recordsethd = $this->db->query($queryhd);

        $queryit =
            "
                    update pc01_co_resep_dt set show_item = '0'
                    where lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'                    
                ";
        $recordsetit = $this->db->query($queryit);

        return $recordsethd && $recordsetit;
    }

    function simpanresepit($data, $obatData, $transco)
    {
        $query = "INSERT INTO pc01_co_resep_dt(
                        lokasi_id, episode_id, trans_id, pasien_id, tanggal, trans_co, obat_id, nama_obat, satuan_id, qty, 
                        signa_id, signa_nama, signa_dokter, urut, catatan, type, header, nama_racikan, free_dosis, status_ver,
                        jenis_resep, show_item, created_date, created_by
                    )
                    values(
                        '{$data['LOKASI_ID']}',
                        '{$data['EPISODE_ID']}', 
                        '{$data['TRANS_ID']}', 
                        '{$data['PASIEN_ID']}', 
                        '{$data['TANGGAL']}', 
                        '{$transco}',  
                        '{$obatData['OBAT_ID']}', 
                        '{$obatData['NAMA_OBAT']}', 
                        '{$obatData['SATUAN_ID']}', 
                        '{$obatData['QTY']}', 
                        '{$obatData['SIGNA_ID']}', 
                        '{$obatData['SIGNA_NAMA']}',
                        '{$obatData['SIGNA_DOKTER']}',
                        '{$obatData['URUT']}',
                        '{$obatData['CATATAN']}',
                        '{$obatData['TIPE_OBAT']}',
                        '{$obatData['HEADER']}',
                        '{$obatData['NAMA_RACIKAN']}',
                        '{$obatData['FREEDOSIS']}',
                        '0',
                        '1',
                        '2',
                        (select current_timestamp),
                            '{$data['CREATED_BY']}'
                        )
                    ";
        $recordset = $this->db->query($query);

        return $recordset;
    }

    function simpanresephd($data, $transco)
    {
        $query = "INSERT into pc01_worklist_frm
                    (lokasi_id, episode_id, trans_id, pasien_id, trans_co, tanggal, rekanan_id, poli_id, dokter_id, status, created_by, created_date, frm_ke)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$transco}', '{$data['TANGGAL']}', '{$data['REKANAN_ID']}', '{$data['POLI_ID']}', '{$data['DOKTER_ID']}', '9', '{$data['CREATED_BY']}', (select current_timestamp), 1)
                ";
        $recordset = $this->db->query($query);

        return $recordset;
    }


    public function getResepDr($episode_id)
    {
        $this->db->select('nama_obat, free_dosis, qty, catatan');
        $this->db->from('pc01_co_resep_dt');
        $this->db->where('episode_id', $episode_id);
        $this->db->where_in('show_item', ['1', '2']);
        $this->db->where('qty >', 0);
        $query = $this->db->get();

        return $query->result_array();
    }

    function gettindakan($lokasi_id, $episode_id, $pasien_id)
    {
        $query = "SELECT a.layan_id, a.nama_layan, a.qty, b.harga, a.trans_alkes
                    from pc01_co_alkes_dt a, pc01_keu_harga_dt b
                    where a.show_item = '1'
                    and a.layan_id = b.layan_id
                    and b.kelas_id = '6'
                    and b.aktif = '1'
                    and a.lokasi_id = '{$lokasi_id}' 
                    and a.episode_id = '{$episode_id}' 
                    and a.pasien_id = '{$pasien_id}'                  
                    order by a.nama_layan
                ";

        $recordset = $this->db->query($query);
        return $recordset->result_array();
    }

    public function cekResume($episodeId)
    {
        $this->db->select('*');
        $this->db->from('pc01_resume_medis');
        $this->db->where('episode_id', $episodeId);
        $this->db->where('aktif', '1');
        $query = $this->db->get();

        return $query->row(); // Return baris pertama jika data ditemukan
    }

    public function insertResume($data)
    {
        $this->db->insert('pc01_resume_medis', $data);
        return $this->db->affected_rows() > 0;
    }

    public function updateResume($episodeId, $kdStatPlg = null, $kdSadar = null, $kdPrognosa = null)
    {
        // $this->db->set('nomor_kunjungan', $noKunjungan);

        // Jika parameter tambahan tidak null, tambahkan ke query
        if (!is_null($kdStatPlg)) {
            $this->db->set('kondisi_pulang', $kdStatPlg);
        }

        if (!is_null($kdSadar)) {
            $this->db->set('tingkat_sadar', $kdSadar);
        }

        if (!is_null($kdPrognosa)) {
            $this->db->set('prognosis', $kdPrognosa);
        }

        $this->db->where('episode_id', $episodeId);
        $this->db->where('aktif', '1');

        return $this->db->update('pc01_resume_medis');
    }


    function kirimalergi($data)
    {
        $this->db->where('pasien_id', $data['PASIEN_ID']);
        $this->db->where('aktif', '1');
        $query = $this->db->get('pc01_co_alergi_bpjs_dt');

        if ($query->num_rows() > 0) {
            $this->db->where('pasien_id', $data['PASIEN_ID']);
            $this->db->where('aktif', '1');

            return $this->db->update('pc01_co_alergi_bpjs_dt', [
                'alergi_id_mknn' => $data['ALERGI_MKNN_KD'],
                'alergi_teks_mknn' => $data['ALERGI_MKNN_NM'],
                'alergi_id_udr' => $data['ALERGI_UDARA_KD'],
                'alergi_teks_udr' => $data['ALERGI_UDARA_NM'],
                'alergi_id_obat' => $data['ALERGI_OBAT_KD'],
                'alergi_teks_obat' => $data['ALERGI_OBAT_NM'],
                'created_date' => date('Y-m-d H:i:s'), // Tambahkan timestamp jika diperlukan $data['CREATED_BY'] 
                'created_by' => $data['CREATED_BY']
            ]);
        } else {
            return $this->db->insert('pc01_co_alergi_bpjs_dt', [
                'pasien_id' => $data['PASIEN_ID'],
                'alergi_id_mknn' => $data['ALERGI_MKNN_KD'],
                'alergi_teks_mknn' => $data['ALERGI_MKNN_NM'],
                'alergi_id_udr' => $data['ALERGI_UDARA_KD'],
                'alergi_teks_udr' => $data['ALERGI_UDARA_NM'],
                'alergi_id_obat' => $data['ALERGI_OBAT_KD'],
                'alergi_teks_obat' => $data['ALERGI_OBAT_NM'],
                'created_date' => date('Y-m-d H:i:s'), // Tambahkan timestamp jika diperlukan
                'created_by' => $data['CREATED_BY']
            ]);
        }
    }

    function selesaiperiksa($data)
    {
        $queryhpsdiag =
            "
                    update pc01_co_diagnosa_ms set show_item = '0'
                    where lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                ";
        $recordhpsdiag = $this->db->query($queryhpsdiag);

        $querydiag =
            "
                    insert into pc01_co_diagnosa_ms
                    (lokasi_id, episode_id, pasien_id, trans_co, trans_id, tanggal, poli_id, dokter_id, rekanan_id, done_status, icd10, diagnosa, show_item, created_date, created_by)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['PASIEN_ID']}', '{$data['TRANS_CO']}', '{$data['TRANS_ID']}',
                     '{$data['TANGGAL']}', '{$data['POLI_ID']}', '{$data['DOKTER_ID']}', '{$data['REKANAN_ID']}', '01', '{$data['ICD10']}', '{$data['DIAGNOSA']}',
                      '1', (select current_timestamp), '{$data['CREATED_BY']}')
                ";
        $recorddiag = $this->db->query($querydiag);



        // Pengecekan ICD10 di tabel pc01_med_icd10_ms
        $queryCheckICD = "SELECT COUNT(*) AS total 
        FROM pc01_med_icd10_ms 
        WHERE kode_icd = '{$data['ICD10']}' and show_item='1'";
        $resultCheckICD = $this->db->query($queryCheckICD)->row();


        if ($resultCheckICD->total == 0) {


            // Jika ICD10 tidak ditemukan, tambahkan ke tabel pc01_med_icd10_ms
            $queryInsertICD = "INSERT INTO pc01_med_icd10_ms (lokasi_id,kode_icd, nm_diag1,nm_diag2, show_item, created_date, created_by)
                VALUES ('{$data['LOKASI_ID']}','{$data['ICD10']}', '{$data['DIAGNOSA']}', '{$data['DIAGNOSA']}', '1', CURRENT_TIMESTAMP, '{$data['CREATED_BY']}')
            ";
            $this->db->query($queryInsertICD);
        }

        // return var_dump($data);
        // die;


        // Insert: Tambahkan data baru ke pc01_co_diagnosa_ms
        $querydiag = "INSERT INTO pc01_co_diagnosa_ms
                        (lokasi_id, episode_id, pasien_id, trans_co, trans_id, tanggal, poli_id, dokter_id, rekanan_id, done_status, icd10, diagnosa, show_item, created_date, created_by)
                        VALUES
                        ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['PASIEN_ID']}', '{$data['TRANS_CO']}', '{$data['TRANS_ID']}',
                        '{$data['TANGGAL']}', '{$data['POLI_ID']}', '{$data['DOKTER_ID']}', '{$data['REKANAN_ID']}', '01', '{$data['ICD10']}', '{$data['DIAGNOSA']}',
                        '1', CURRENT_TIMESTAMP, '{$data['CREATED_BY']}')
                        ";
        $recorddiag = $this->db->query($querydiag);

        $queryhpsselesai = "UPDATE pc01_co_selesai_periksa set show_item = '0'
                    where lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                ";
        $recordhpsselesai = $this->db->query($queryhpsselesai);

        $queryselesai = "INSERT INTO pc01_co_selesai_periksa
                    (lokasi_id, episode_id, trans_id, pasien_id, dokter_id, poli_id, created_date, created_by, show_item)
                    values
                    ('{$data['LOKASI_ID']}', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$data['DOKTER_ID']}', '{$data['POLI_ID']}', (select current_timestamp), '{$data['CREATED_BY']}', '1' )
                ";
        $recordselesai = $this->db->query($queryselesai);



        $queryprwt = "UPDATE pc01_med_prwt_tr set status = '1'
                    where lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                ";
        $recordprwt = $this->db->query($queryprwt);

        return $recordhpsdiag && $recorddiag && $recordselesai && $recordhpsselesai && $recordprwt;
    }


    function kirimresep($data)
    {
        $querywl = "UPDATE pc01_worklist_frm set status = '0'
                    where aktif = '1'
                    and lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                ";
        $recordwl = $this->db->query($querywl);

        $queryresep = "UPDATE pc01_co_resep_dt set show_item = '1'
                    where show_item = '2'
                    and lokasi_id = '{$data['LOKASI_ID']}'
                    and episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                ";
        $recordresep = $this->db->query($queryresep);

        return $recordwl && $recordresep;
    }
}
