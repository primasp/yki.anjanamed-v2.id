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
        $allowedPenunjang = "'" . implode("','", $this->allowedPenunjangDokterIds()) . "'";

        $query = "SELECT a.*
                    FROM (
                        SELECT 
                            a.lokasi_id,
                            a.episode_id,
                            a.pasien_id,
                            a.layan_id,
                            a.nama_layan,
                            a.qty,
                            a.qty * b.harga AS total_harga,
                            a.urut,
                            0 AS priority
                        FROM pc01_co_alkes_dt a
                        JOIN pc01_keu_harga_dt b ON a.layan_id = b.layan_id
                        WHERE a.show_item = '1'
                          AND b.kelas_id = '6'
                          AND b.aktif = '1'
                          AND a.lokasi_id = '{$data['LOKASI_ID']}'
                          AND a.episode_id = '{$data['EPISODE_ID']}'
                          AND a.pasien_id = '{$data['PASIEN_ID']}'

                        UNION

                        SELECT
                            x.lokasi_id,
                            x.episode_id,
                            x.pasien_id,
                            x.layan_id,
                            '[Penunjang] ' || l.nama_layan1 AS nama_layan,
                            x.qty,
                            x.harga_total AS total_harga,
                            0 AS urut,
                            1 AS priority
                        FROM pc01_keu_transctr_it x
                        JOIN pc01_keu_layan_ms l ON l.layan_id = x.layan_id
                        WHERE x.aktif = '1'
                          AND x.layan_id IN ({$allowedPenunjang})
                          AND x.lokasi_id = '{$data['LOKASI_ID']}'
                          AND x.episode_id = '{$data['EPISODE_ID']}'
                          AND x.pasien_id = '{$data['PASIEN_ID']}'

                        UNION

                        SELECT 
                            a.lokasi_id,
                            a.episode_id,
                            a.pasien_id,
                            a.obat_id AS layan_id,
                            a.nama_obat AS nama_layan,
                            a.qty,
                            a.qty * c.harga_jual AS total_harga,
                            a.urut,
                            2 AS priority
                        FROM pc01_co_resep_dt a
                        JOIN pc01_worklist_frm b 
                          ON a.lokasi_id = b.lokasi_id 
                         AND a.episode_id = b.episode_id
                         AND a.pasien_id = b.pasien_id
                         AND a.trans_co = b.trans_co
                        JOIN pc01_frm_obat_ms c 
                          ON a.lokasi_id = c.lokasi_id 
                         AND a.obat_id = c.obat_id
                        WHERE a.show_item <> '0'
                          AND a.lokasi_id = '{$data['LOKASI_ID']}'
                          AND a.episode_id = '{$data['EPISODE_ID']}'
                          AND a.pasien_id = '{$data['PASIEN_ID']}'
                    ) a
                    ORDER BY a.priority, a.urut, a.nama_layan
                ";

        return $this->db->query($query)->result();
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
        /*
         * Alur Baru YKI:
         * Menu Tindakan hanya menampilkan tindakan umum.
         * Lab/Radiologi dipisahkan ke panel Penunjang Dokter, sehingga seluruh layanan Lab/Rad dikeluarkan dari pencarian ini.
         */
        $excludedPenunjang = "'" . implode("','", $this->allowedPenunjangDokterIds()) . "'";

        $query = "SELECT a.layan_id, a.nama_layan1, a.kategori_id, b.harga
                    FROM pc01_keu_layan_ms a
                    JOIN pc01_keu_harga_dt b ON a.layan_id = b.layan_id
                    WHERE a.aktif = '1'
                      AND b.kelas_id = '6'
                      AND b.aktif = '1'
                      AND a.kategori_id = 'JKL-UMU'
                      AND COALESCE(a.kategori_id, '') NOT IN ('JKL-LAB', 'JKL-RAD', 'JKL-RA')
                      AND a.layan_id NOT IN ({$excludedPenunjang}, 'TDK000000000005','TDK000000000004','TDK000000000009')
                      AND UPPER(a.nama_layan1) LIKE UPPER('%{$filtercari}%')
                    ORDER BY a.nama_layan1
                ";

        return $this->db->query($query)->result();
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

    private function getEpisodeBillingContext($lokasi_id, $episode_id, $pasien_id)
    {
        $sql = "SELECT
                    e.lokasi_id,
                    e.episode_id,
                    e.pasien_id,
                    COALESCE(NULLIF(e.rekanan_id, ''), 'UMUM') AS rekanan_id,
                    e.poli_id,
                    e.dokter_id,
                    e.tgl_masuk,
                    COALESCE(
                        (
                            SELECT h.kelas_id
                            FROM pc01_keu_transaksi_hd h
                            WHERE h.lokasi_id = e.lokasi_id
                              AND h.episode_id = e.episode_id
                              AND h.pasien_id = e.pasien_id
                              AND COALESCE(h.aktif, '1') = '1'
                              AND COALESCE(h.kelas_id, '') <> ''
                            ORDER BY h.created_date DESC NULLS LAST, h.tgl_transaksi DESC NULLS LAST
                            LIMIT 1
                        ),
                        '6'
                    ) AS kelas_id
                FROM pc01_keu_episode e
                WHERE e.lokasi_id = ?
                  AND e.episode_id = ?
                  AND e.pasien_id = ?
                  AND e.aktif = '1'
                LIMIT 1";

        return $this->db->query($sql, [$lokasi_id, $episode_id, $pasien_id])->row();
    }

    private function getLayananTindakanById($layanId, $kelas_id = '6')
    {
        $sql = "SELECT
                    l.layan_id,
                    l.nama_layan1,
                    l.kategori_id,
                    COALESCE(
                        (
                            SELECT h.harga
                            FROM pc01_keu_harga_dt h
                            WHERE h.layan_id = l.layan_id
                              AND h.aktif = '1'
                              AND h.kelas_id = ?
                            ORDER BY h.harga DESC
                            LIMIT 1
                        ),
                        0
                    ) AS harga
                FROM pc01_keu_layan_ms l
                WHERE l.aktif = '1'
                  AND l.layan_id = ?
                  AND COALESCE(l.kategori_id, '') = 'JKL-UMU'
                  AND l.layan_id NOT IN ('TDK000000000005','TDK000000000004','TDK000000000009')
                LIMIT 1";

        return $this->db->query($sql, [$kelas_id, $layanId])->row();
    }

    private function hapusBillingTindakanDokterBelumBayar($data)
    {
        $lokasi_id  = $data['LOKASI_ID'];
        $episode_id = $data['EPISODE_ID'];
        $pasien_id  = $data['PASIEN_ID'];
        $updated_by = $data['CREATED_BY'] ?? null;
        $updated_at = date('Y-m-d H:i:s');

        /*
         * Nonaktifkan detail tindakan umum lama yang header 003-nya belum dibayar.
         * Header/detail yang sudah memiliki bayar_id tidak disentuh agar histori pembayaran aman.
         */
        $sqlDetail = "UPDATE pc01_keu_transctr_it d
            SET aktif = '0',
                last_updated_by = ?,
                last_updated_date = ?
            WHERE d.lokasi_id = ?
              AND d.episode_id = ?
              AND d.pasien_id = ?
              AND COALESCE(d.aktif, '1') = '1'
              AND EXISTS (
                    SELECT 1
                    FROM pc01_keu_transaksi_hd h
                    WHERE h.lokasi_id = d.lokasi_id
                      AND h.episode_id = d.episode_id
                      AND h.pasien_id = d.pasien_id
                      AND h.trans_id = d.trans_id
                      AND h.jenis_tr = '003'
                      AND COALESCE(h.aktif, '1') = '1'
                      AND h.bayar_id IS NULL
                      AND h.tgl_lunas IS NULL
              )";

        $this->db->query($sqlDetail, [$updated_by, $updated_at, $lokasi_id, $episode_id, $pasien_id]);

        /*
         * Nonaktifkan header tindakan 003 lama yang belum dibayar agar saat dokter menyimpan ulang
         * tidak terjadi tagihan dobel di kasir.
         */
        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('jenis_tr', '003')
            ->where('bayar_id IS NULL', null, false)
            ->where('tgl_lunas IS NULL', null, false)
            ->where('aktif', '1')
            ->update('pc01_keu_transaksi_hd', [
                'aktif' => '0',
                'last_updated_by' => $updated_by,
                'last_updated_date' => $updated_at
            ]);

        return true;
    }

    public function sinkronTindakanDokterKeBilling($data, $tindakanArray)
    {
        $this->db->trans_begin();

        try {
            $lokasi_id  = $data['LOKASI_ID'] ?? '';
            $episode_id = $data['EPISODE_ID'] ?? '';
            $pasien_id  = $data['PASIEN_ID'] ?? '';

            if ($lokasi_id === '' || $episode_id === '' || $pasien_id === '') {
                throw new Exception('Data episode/pasien tindakan tidak lengkap.');
            }

            $context = $this->getEpisodeBillingContext($lokasi_id, $episode_id, $pasien_id);
            if (empty($context)) {
                throw new Exception('Episode pasien tidak ditemukan untuk sinkron tindakan.');
            }

            $rekanan_id = strtoupper(trim((string)($data['REKANAN_ID'] ?? $context->rekanan_id ?? 'UMUM')));
            if ($rekanan_id === '') {
                $rekanan_id = 'UMUM';
            }

            $poli_id    = !empty($data['POLI_ID']) ? $data['POLI_ID'] : $context->poli_id;
            $dokter_id  = !empty($data['DOKTER_ID']) ? $data['DOKTER_ID'] : $context->dokter_id;
            $kelas_id   = !empty($data['KELAS_ID']) ? $data['KELAS_ID'] : ($context->kelas_id ?: '6');
            $created_by = $data['CREATED_BY'] ?? null;
            $tanggal    = !empty($data['TANGGAL']) ? $data['TANGGAL'] : date('Y-m-d');
            $trans_co   = $data['TRANS_CO'] ?? null;

            $isUmum = ($rekanan_id === 'UMUM');

            /*
             * Hapus/supersede transaksi tindakan 003 lama yang belum dibayar.
             * Jika tindakan sudah dibayar, tidak akan tersentuh.
             */
            $this->hapusBillingTindakanDokterBelumBayar($data);

            $validItems = [];
            foreach ((array)$tindakanArray as $it) {
                $layanId = $it['layanid'] ?? ($it['layan_id'] ?? '');
                if ($layanId === '') {
                    continue;
                }

                $qty = max(1, (int)($it['qty'] ?? 1));

                $layanan = $this->getLayananTindakanById($layanId, $kelas_id);
                if (empty($layanan)) {
                    continue;
                }

                $validItems[$layanId] = [
                    'layan_id'     => $layanId,
                    'nama_layan'   => $layanan->nama_layan1,
                    'kategori_id'  => $layanan->kategori_id,
                    'qty'          => $qty,
                    'harga_satuan' => (float)$layanan->harga,
                ];
            }

            if (empty($validItems)) {
                if ($this->db->trans_status() === false) {
                    throw new Exception('Gagal menghapus transaksi tindakan lama.');
                }

                $this->db->trans_commit();

                return [
                    'success' => true,
                    'message' => 'Tidak ada tindakan umum aktif untuk ditagihkan.',
                    'items' => []
                ];
            }

            $jenis_tr = '003';
            $trans_id_tindakan = $this->generateTransIdByJenisTr($jenis_tr);
            $total_harga = 0;
            $resultItems = [];

            foreach ($validItems as $item) {
                $qty = (float)$item['qty'];
                $harga = (float)$item['harga_satuan'];
                $hargaTotal = $qty * $harga;
                $total_harga += $hargaTotal;

                $this->db->insert('pc01_keu_transctr_it', [
                    'lokasi_id'      => $lokasi_id,
                    'episode_id'     => $episode_id,
                    'trans_id'       => $trans_id_tindakan,
                    'pasien_id'      => $pasien_id,
                    'layan_id'       => $item['layan_id'],
                    'dokter_id'      => $dokter_id,
                    'qty'            => $qty,
                    'harga_satuan'   => $harga,
                    'disk_pct'       => 0,
                    'disk_tot'       => 0,
                    'harga_total'    => $hargaTotal,
                    'total_rekanan'  => $isUmum ? 0 : $hargaTotal,
                    'total_pribadi'  => $isUmum ? $hargaTotal : 0,
                    'aktif'          => '1',
                    'created_by'     => $created_by,
                    'created_date'   => date('Y-m-d H:i:s'),
                    'tgl_transaksi'  => $tanggal,
                    'trans_co'       => $trans_co,
                ]);

                $resultItems[] = [
                    'layan_id' => $item['layan_id'],
                    'nama'     => $item['nama_layan'],
                    'qty'      => $qty,
                    'harga'    => $hargaTotal,
                    'trans_id' => $trans_id_tindakan,
                    'jenis_tr' => $jenis_tr
                ];
            }

            if ($total_harga > 0) {
                $this->insertHeaderOrderDokterUmum(
                    $lokasi_id,
                    $episode_id,
                    $pasien_id,
                    $trans_id_tindakan,
                    $jenis_tr,
                    $kelas_id,
                    $poli_id,
                    $dokter_id,
                    $total_harga,
                    $created_by,
                    $rekanan_id
                );
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Gagal menyimpan transaksi tindakan 003.');
            }

            $this->db->trans_commit();

            return [
                'success' => true,
                'message' => 'Tindakan dokter berhasil masuk billing 003.',
                'trans_id' => $trans_id_tindakan,
                'jenis_tr' => $jenis_tr,
                'total_harga' => $total_harga,
                'items' => $resultItems
            ];
        } catch (Exception $e) {
            $this->db->trans_rollback();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'items' => []
            ];
        }
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


    private function allowedPenunjangDokterIds()
    {
        return [
            'TDK000000000003', // Lab - Papsmear
            'TDK000000000001', // Rad - Mammografi
            'TDK000000000006', // Rad - USG Abdomen Atas
            'TDK000000000007', // Rad - USG Kandungan
            'TDK000000000002', // Rad - USG Payudara
            'TDK000000000008', // Rad - USG Thyroid
        ];
    }

    private function allowedPenunjangRadIds()
    {
        return [
            'TDK000000000001',
            'TDK000000000006',
            'TDK000000000007',
            'TDK000000000002',
            'TDK000000000008',
        ];
    }

    public function getMasterPenunjangDokter()
    {
        $ids = $this->allowedPenunjangDokterIds();

        $sql = "SELECT 
                    l.layan_id,
                    l.nama_layan1,
                    l.kategori_id,
                    h.harga,
                    CASE 
                        WHEN l.layan_id = 'TDK000000000003' THEN 'LAB'
                        ELSE 'RAD'
                    END AS kategori_kelompok,
                    CASE l.layan_id
                        WHEN 'TDK000000000003' THEN 1
                        WHEN 'TDK000000000001' THEN 2
                        WHEN 'TDK000000000002' THEN 3
                        WHEN 'TDK000000000006' THEN 4
                        WHEN 'TDK000000000007' THEN 5
                        WHEN 'TDK000000000008' THEN 6
                        ELSE 99
                    END AS urut_tampil
                FROM pc01_keu_layan_ms l
                JOIN pc01_keu_harga_dt h ON h.layan_id = l.layan_id
                WHERE l.aktif = '1'
                  AND h.aktif = '1'
                  AND h.kelas_id = '6'
                  AND l.layan_id IN ?
                ORDER BY urut_tampil, l.nama_layan1";

        /*
         * CodeIgniter tidak selalu expand array binding untuk IN (?) di semua versi.
         * Karena daftar ID dikontrol dari sistem, query dibuat dengan escape manual.
         */
        $escaped = array_map([$this->db, 'escape'], $ids);
        $sql = str_replace('IN ?', 'IN (' . implode(',', $escaped) . ')', $sql);

        return $this->db->query($sql)->result();
    }

    public function getOrderPenunjangDokter($data)
    {
        $ids = array_map([$this->db, 'escape'], $this->allowedPenunjangDokterIds());
        $in  = implode(',', $ids);

        $sql = "SELECT 
                    x.layan_id,
                    l.nama_layan1,
                    l.kategori_id,
                    x.qty,
                    x.harga_satuan,
                    x.harga_total,
                    CASE 
                        WHEN x.layan_id = 'TDK000000000003' THEN 'LAB'
                        ELSE 'RAD'
                    END AS kategori_kelompok
                FROM pc01_keu_transctr_it x
                JOIN pc01_keu_layan_ms l 
                  ON l.layan_id = x.layan_id
                 AND COALESCE(l.aktif, '1') = '1'
                WHERE COALESCE(x.aktif, '1') = '1'
                  AND x.lokasi_id = ?
                  AND x.episode_id = ?
                  AND x.pasien_id = ?
                  AND x.layan_id IN ({$in})
                ORDER BY l.nama_layan1";

        return $this->db->query($sql, [
            $data['LOKASI_ID'],
            $data['EPISODE_ID'],
            $data['PASIEN_ID']
        ])->result();
    }

    public function hapusOrderPenunjangDokter($data)
    {
        $ids = array_map([$this->db, 'escape'], $this->allowedPenunjangDokterIds());
        $in  = implode(',', $ids);

        $lokasi_id   = $data['LOKASI_ID'];
        $episode_id  = $data['EPISODE_ID'];
        $pasien_id   = $data['PASIEN_ID'];
        $updated_by  = $data['CREATED_BY'] ?? null;
        $updated_at  = date('Y-m-d H:i:s');

        /*
         * Nonaktifkan detail order penunjang dokter lama yang belum dibayar.
         * Detail yang sudah memiliki header transaksi ber-bayar_id tidak disentuh.
         */
        $sqlDetail = "UPDATE pc01_keu_transctr_it d
            SET aktif = '0',
                last_updated_by = ?,
                last_updated_date = ?
            WHERE d.lokasi_id = ?
              AND d.episode_id = ?
              AND d.pasien_id = ?
              AND d.layan_id IN ({$in})
              AND COALESCE(d.aktif, '1') = '1'
              AND NOT EXISTS (
                    SELECT 1
                    FROM pc01_keu_transaksi_hd h_paid
                    WHERE h_paid.lokasi_id = d.lokasi_id
                      AND h_paid.episode_id = d.episode_id
                      AND h_paid.pasien_id = d.pasien_id
                      AND h_paid.trans_id = d.trans_id
                      AND COALESCE(h_paid.aktif, '1') = '1'
                      AND h_paid.bayar_id IS NOT NULL
              )";
        $this->db->query($sqlDetail, [$updated_by, $updated_at, $lokasi_id, $episode_id, $pasien_id]);

        /*
         * Nonaktifkan header LAB/RAD lama yang belum dibayar agar tidak dobel di Kasir.
         */
        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where_in('jenis_tr', ['004', '005'])
            ->where('bayar_id IS NULL', null, false)
            ->where('tgl_lunas IS NULL', null, false)
            ->where('aktif', '1')
            ->update('pc01_keu_transaksi_hd', [
                'aktif' => '0',
                'last_updated_by' => $updated_by,
                'last_updated_date' => $updated_at
            ]);

        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        $this->db->where_in('test_id', $this->allowedPenunjangDokterIds());
        $this->db->update('pc01_co_lab_dt', [
            'show_item' => '0'
        ]);

        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        $this->db->where_in('test_id', $this->allowedPenunjangDokterIds());
        $this->db->update('pc01_co_rad_dt', [
            'show_item' => '0'
        ]);

        return true;
    }

    private function generateTransIdByJenisTr($jenis_tr)
    {
        /*
         * Mapping generate TRANS_ID:
         * 001 = Pendaftaran, prefix 1
         * 003 = Tindakan,    prefix 3
         * 004 = Lab,         prefix 4
         * 005 = Radiologi,   prefix 5
         * 006 = Obat,        prefix 6
         */
        $prefix = [
            '001' => '1',
            '003' => '3',
            '004' => '4',
            '005' => '5',
            '006' => '6',
        ];

        $kode = $prefix[$jenis_tr] ?? '3';

        $row = $this->db->query("SELECT buat_id_transaksi_id(?) AS id", [$kode])->row();

        if (!empty($row) && !empty($row->id)) {
            return $row->id;
        }

        return $kode . date('ymdHis') . mt_rand(10, 99);
    }

    private function insertHeaderOrderDokterUmum(
        $lokasi_id,
        $episode_id,
        $pasien_id,
        $trans_id,
        $jenis_tr,
        $kelas_id,
        $poli_id,
        $dokter_id,
        $total_harga,
        $created_by,
        $rekanan_id = 'UMUM'
    ) {
        $rekanan_id = strtoupper(trim((string)$rekanan_id));
        if ($rekanan_id === '') {
            $rekanan_id = 'UMUM';
        }

        $isUmum = ($rekanan_id === 'UMUM');

        $data_hd = [
            'lokasi_id'         => $lokasi_id,
            'episode_id'        => $episode_id,
            'trans_id'          => $trans_id,
            'pasien_id'         => $pasien_id,
            'shift_id'          => null,
            'bayar_id'          => null,
            'tgl_lunas'         => null,
            'jenis_tr'          => $jenis_tr,
            'kelas_id'          => $kelas_id,
            'poli_id'           => $poli_id,
            'dokter_id'         => $dokter_id,
            'rekanan_id'        => $rekanan_id,
            'perjanjian_yn'     => 'T',
            'status_tr'         => '00',
            'tgl_transaksi'     => date('Y-m-d H:i:s'),
            'total_pembulatan'  => 0,
            'total_sub'         => $total_harga,
            'total_diskpct'     => 0,
            'total_diskon'      => 0,
            'total_harga'       => $total_harga,
            'total_rekanan'     => $isUmum ? 0 : $total_harga,
            'total_pribadi'     => $isUmum ? $total_harga : 0,
            'bayar_rekanan'     => 0,
            'bayar_pribadi'     => 0,
            'aktif'             => '1',
            'created_by'        => $created_by,
            'created_date'      => date('Y-m-d H:i:s'),
            'total_tanggung'    => $isUmum ? 0 : $total_harga,
            'sdh_rehitung'      => 'T'
        ];

        return $this->db->insert('pc01_keu_transaksi_hd', $data_hd);
    }

    public function simpanOrderPenunjangDokter($data, $items)
    {
        $allowed = $this->allowedPenunjangDokterIds();
        $radIds  = $this->allowedPenunjangRadIds();

        $validItems = [];
        foreach ((array)$items as $it) {
            $layanId = $it['layan_id'] ?? '';
            if (!in_array($layanId, $allowed, true)) {
                continue;
            }

            $validItems[$layanId] = [
                'layan_id' => $layanId,
                'qty'      => max(1, (int)($it['qty'] ?? 1)),
                'jenis'    => ($layanId === 'TDK000000000003') ? 'LAB' : 'RAD',
            ];
        }

        $this->db->trans_begin();

        try {
            $lokasi_id  = $data['LOKASI_ID'];
            $episode_id = $data['EPISODE_ID'];
            $pasien_id  = $data['PASIEN_ID'];
            $poli_id    = $data['POLI_ID'];
            $dokter_id  = $data['DOKTER_ID'];
            $kelas_id   = $data['KELAS_ID'] ?? '6';
            $created_by = $data['CREATED_BY'];
            $tanggal    = $data['TANGGAL'] ?? date('Y-m-d');
            $trans_co   = $data['TRANS_CO'] ?? null;
            $rekanan_id = strtoupper(trim((string)($data['REKANAN_ID'] ?? 'UMUM')));
            if ($rekanan_id === '') {
                $rekanan_id = 'UMUM';
            }
            $isUmum = ($rekanan_id === 'UMUM');

            /* Hapus order penunjang dokter lama yang belum dibayar supaya tidak dobel di kasir. */
            $this->hapusOrderPenunjangDokter($data);

            if (empty($validItems)) {
                if ($this->db->trans_status() === false) {
                    throw new Exception('Gagal mengosongkan order penunjang dokter.');
                }

                $this->db->trans_commit();
                return [
                    'success' => true,
                    'message' => 'Order penunjang dikosongkan.',
                    'items'   => []
                ];
            }

            $transIdByJenis = [];
            $totalByJenis   = [];
            $resultItems    = [];

            foreach ($validItems as $item) {
                $layanan = $this->getLayananPenunjangById($item['layan_id']);
                if (empty($layanan)) {
                    continue;
                }

                $kategoriId = strtoupper(trim((string)$layanan->kategori_id));
                $jenis_tr   = ($kategoriId === 'JKL-LAB') ? '004' : '005';

                if (empty($transIdByJenis[$jenis_tr])) {
                    $transIdByJenis[$jenis_tr] = $this->generateTransIdByJenisTr($jenis_tr);
                    $totalByJenis[$jenis_tr] = 0;
                }

                $trans_id   = $transIdByJenis[$jenis_tr];
                $qty        = $item['qty'];
                $harga      = (float)$layanan->harga;
                $hargaTotal = $harga * $qty;

                $detail = [
                    'lokasi_id'      => $lokasi_id,
                    'episode_id'     => $episode_id,
                    'trans_id'       => $trans_id,
                    'pasien_id'      => $pasien_id,
                    'layan_id'       => $item['layan_id'],
                    'dokter_id'      => $dokter_id,
                    'qty'            => $qty,
                    'harga_satuan'   => $harga,
                    'disk_pct'       => 0,
                    'disk_tot'       => 0,
                    'harga_total'    => $hargaTotal,
                    'total_rekanan'  => $isUmum ? 0 : $hargaTotal,
                    'total_pribadi'  => $isUmum ? $hargaTotal : 0,
                    'aktif'          => '1',
                    'created_by'     => $created_by,
                    'created_date'   => date('Y-m-d H:i:s'),
                    'tgl_transaksi'  => $tanggal,
                    'trans_co'       => $trans_co,
                ];

                $this->db->insert('pc01_keu_transctr_it', $detail);

                if ($jenis_tr === '004') {
                    $this->db->insert('pc01_co_lab_dt', [
                        'trans_co'       => $trans_co,
                        'trans_id'       => $trans_id,
                        'pasien_id'      => $pasien_id,
                        'tanggal'        => $tanggal,
                        'test_id'        => $item['layan_id'],
                        'show_item'      => 1,
                        'created_date'   => date('Y-m-d H:i:s'),
                        'created_by'     => $created_by,
                        'episode_id'     => $episode_id,
                        'trans_bayar_id' => null,
                        'cito'           => 'N',
                        'dikerjakan'     => 'T',
                        'iscover'        => $isUmum ? 'T' : 'Y',
                        'nilcover'       => $isUmum ? 0 : $hargaTotal
                    ]);
                } else {
                    $this->db->insert('pc01_co_rad_dt', [
                        'trans_co'       => $trans_co,
                        'trans_id'       => $trans_id,
                        'pasien_id'      => $pasien_id,
                        'tanggal'        => $tanggal,
                        'test_id'        => $item['layan_id'],
                        'kanan'          => 'N',
                        'kiri'           => 'N',
                        'show_item'      => 1,
                        'created_date'   => date('Y-m-d H:i:s'),
                        'created_by'     => $created_by,
                        'episode_id'     => $episode_id,
                        'trans_bayar_id' => null,
                        'iscover'        => $isUmum ? 'T' : 'Y',
                        'nilcover'       => $isUmum ? 0 : $hargaTotal
                    ]);
                }

                $totalByJenis[$jenis_tr] += $hargaTotal;

                $resultItems[] = [
                    'layan_id' => $item['layan_id'],
                    'nama'     => $layanan->nama_layan1,
                    'jenis'    => $jenis_tr === '004' ? 'LAB' : 'RAD',
                    'qty'      => $qty,
                    'harga'    => $hargaTotal,
                    'trans_id' => $trans_id,
                    'jenis_tr' => $jenis_tr
                ];
            }

            foreach ($totalByJenis as $jenis_tr => $total) {
                if ($total <= 0 || empty($transIdByJenis[$jenis_tr])) {
                    continue;
                }

                /*
                 * Semua order dokter dibuatkan header transaksi.
                 * UMUM: akan muncul di Kasir karena rekanan_id UMUM dan bayar_id NULL.
                 * PROGRAM: tidak muncul di Kasir Umum karena rekanan_id bukan UMUM.
                 */
                $this->insertHeaderOrderDokterUmum(
                    $lokasi_id,
                    $episode_id,
                    $pasien_id,
                    $transIdByJenis[$jenis_tr],
                    $jenis_tr,
                    $kelas_id,
                    $poli_id,
                    $dokter_id,
                    $total,
                    $created_by,
                    $rekanan_id
                );
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Transaksi database gagal saat menyimpan order penunjang dokter.');
            }

            $this->db->trans_commit();

            return [
                'success' => true,
                'message' => 'Order penunjang dokter berhasil disimpan.',
                'items'   => $resultItems
            ];
        } catch (Exception $e) {
            $this->db->trans_rollback();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'items'   => []
            ];
        }
    }

    private function getLayananPenunjangById($layanId)
    {
        $sql = "SELECT 
                    l.layan_id,
                    l.nama_layan1,
                    l.kategori_id,
                    COALESCE(h.harga, 0) AS harga
                FROM pc01_keu_layan_ms l
                LEFT JOIN pc01_keu_harga_dt h
                  ON h.layan_id = l.layan_id
                 AND h.kelas_id = '6'
                 AND h.aktif = '1'
                WHERE l.aktif = '1'
                  AND l.layan_id = ?
                LIMIT 1";

        return $this->db->query($sql, [$layanId])->row();
    }
}
