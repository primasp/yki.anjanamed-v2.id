<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PasienModel extends CI_Model
{

    public function getDetilPasienById($pasien_id)
    {
        $sql = "SELECT a.*, b.keterangan as propinsi_txt,c.keterangan as kecamatan_txt,d.keterangan as kabupaten_txt,e.keterangan as kelurahan_txt, f.nama as rekanan_txt,g.keterangan as pjp_hubungan_txt from pc01_gen_pasien_ms a 
                left join pc01_gen_kkk_ms  b on a.propinsi_id=b.kkk_id 
                left join pc01_gen_kkk_ms  c on a.kecamatan_id=c.kkk_id 
                left join pc01_gen_kkk_ms  d on a.kabupaten_id=d.kkk_id 
                left join pc01_gen_kkk_ms  e on a.kelurahan_id=e.kkk_id 
                left join pc01_keu_rekanan_ms f  on a.akhir_rekanan_id =f.rekanan_id  
                left join pc01_gen_global_ms g on a.pjp_hubungan_id =g.global_id and jenis_id='JHUB' 
                where a.pasien_id =? AND a.aktif='1'";

        return $this->db->query($sql, [$pasien_id])->row();
    }

    public function get_pasien_datatable($lokasi_id, $start, $length, $keyword = "")
    {
        // $this->db->select("
        //         p.pasien_id,
        //         p.int_pasien_id,
        //         pcare_manager.format_nama_pasien(
        //             p.nama,
        //             p.tgl_lahir,
        //             p.sex_id,
        //             p.marital_status_id
        //         ) AS nama,
        //         p.tgl_lahir,
        //         p.no_identitas,
        //         p.no_kartuprov
        //     ");

        $this->db->select("p.pasien_id,
                            p.int_pasien_id,
                            pcare_manager.format_nama_pasien(
                                p.nama,
                                p.tgl_lahir,
                                p.sex_id,
                                p.marital_status_id
                            ) AS nama,
                            p.tgl_lahir,
                            p.no_identitas,
                            p.no_kartuprov,
                            /* jumlah kunjungan */
                            /* (
                                SELECT COUNT(a.episode_id)
                                FROM pcare_manager.pc01_keu_episode a
                                WHERE a.pasien_id = p.pasien_id
                                AND a.status_episode <> '99'
                                AND a.lokasi_id = '" . $lokasi_id . "'
                                AND a.aktif = '1'
                            ) AS kunjungan_ke,
                            */
                            /* last visit */
                            last_ep.tgl_masuk AS tgl_masuk_terakhir,
                            last_ep.poli_id AS poli_terakhir,
                            c.keterangan AS nama_poli_terakhir", FALSE);


        $this->db->from('pcare_manager.pc01_gen_pasien_ms p');
        /* LATERAL JOIN (WAJIB FALSE agar tidak di-escape CI) */
        $this->db->join("
                        LATERAL (
                            SELECT to_char(a.tgl_masuk,'DD-MM-YYYY')tgl_masuk, a.poli_id
                            FROM pcare_manager.pc01_keu_episode a
                            WHERE a.pasien_id = p.pasien_id
                            AND a.status_episode <> '99'
                            AND a.lokasi_id = '" . $lokasi_id . "'
                            AND a.aktif = '1'
                            ORDER BY a.tgl_masuk DESC
                            LIMIT 1
                        ) last_ep", "1=1", "LEFT", FALSE);
        //  Di CodeIgniter, untuk LATERAL: jangan pakai parameter TRUE biasa harus pakai string "1=1"

        /* JOIN POLI */
        $this->db->join('pcare_manager.pc01_med_poli_ms c', "
                            c.poli_id = last_ep.poli_id
                            AND c.lokasi_id = '" . $lokasi_id . "'
                            AND c.aktif = '1'
                        ", "LEFT", FALSE);

        /* FILTER */
        $this->db->where('p.aktif', '1');
        $this->db->where('p.lokasi_id', $lokasi_id);


        /* SEARCH */
        if ($keyword !== "") {
            $this->db->group_start();
            $this->db->like('LOWER(p.nama)', strtolower($keyword));
            $this->db->or_like('p.no_identitas', $keyword);
            $this->db->or_like('p.no_kartuprov', $keyword);
            $this->db->group_end();
        }

        /* ORDER & LIMIT */
        $this->db->order_by('p.nama', 'ASC');
        $this->db->limit($length, $start);

        return $this->db->get()->result();
    }

    public function count_all_pasien($lokasi_id)

    {
        $this->db->from('pc01_gen_pasien_ms');
        $this->db->where('aktif', '1');
        $this->db->where('lokasi_id', $lokasi_id);
        return $this->db->count_all_results();
    }

    public function count_filtered_pasien($lokasi_id, $keyword = "")
    {
        $this->db->from('pc01_gen_pasien_ms');
        $this->db->where('aktif', '1');
        $this->db->where('lokasi_id', $lokasi_id);

        if ($keyword !== "") {
            $this->db->group_start();
            $this->db->like('LOWER(nama)', strtolower($keyword));
            // $this->db->or_like('no_identitas', $keyword);
            $this->db->or_like('no_identitas', $keyword);
            $this->db->or_like('bpjs_no', $keyword);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }



    public function getPasienById($pasien_id)
    {
        return $this->db->get_where('pc01_gen_pasien_ms', ['pasien_id' => $pasien_id])->row();
    }

    public function getPasienID()
    {
        $query = "SELECT buat_id_pasien_master_id() AS buat_id_pasien_master_id";
        $result = $this->db->query($query)->row();

        if (isset($result->buat_id_pasien_master_id)) {
            return $result->buat_id_pasien_master_id;
        } else {
            echo '<script>console.error("Query did not return the expected result.");</script>';
            return null;
        }
    }

    public function getIntPasienID()
    {
        $query = "SELECT buat_id_pasien_internal_id() AS buat_id_pasien_internal_id";
        $result = $this->db->query($query)->row();

        if (isset($result->buat_id_pasien_internal_id)) {
            return $result->buat_id_pasien_internal_id;
        } else {
            echo '<script>console.error("Query did not return the expected result.");</script>';
            return null;
        }
    }
    public function updatePasien($id, $data)
    {
        $this->db->where('pasien_id', $id);
        return $this->db->update('pc01_gen_pasien_ms', $data);
    }

    public function insertPasien($data)
    {
        $tgl_lahir = $data['tgl_lahir'];
        $tgl_lahir = DateTime::createFromFormat('d/m/Y', $tgl_lahir)->format('Y-m-d');

        $insert = [
            'lokasi_id' => '001',
            'pasien_id' => $this->getIntPasienID(),

            'int_pasien_id' => $this->getPasienID(),
            'jenis_pas' => 'l',
            'nama' => $data['nama'],
            'nama_akhir' => $data['nama'],
            'no_kk' => $data['no_kakel'],
            'no_identitas' => $data['nik'],
            'vip' => 'T',
            'tempat_lahir_txt' => $data['tempat_lahir'],
            'tgl_lahir' => $tgl_lahir,
            'sex_id' => $data['sex_id'],
            'nama_pasangan' => $data['nama_pasangan'],
            'email' => $data['email'],
            'nama_ibukandung' => $data['nama_ibukandung'],
            'marital_status_id' => $data['marital_status_id'],
            'pas_goldar_id' => $data['pas_goldar_id'],
            'no_selular' => $data['no_selular'],
            'mr_lama' => $data['rm_lama'],
            'propinsi_id' => $data['propinsi_id'],
            'kabupaten_id' => $data['kabupaten_id'],
            'kecamatan_id' => $data['kecamatan_id'],
            'kelurahan_id' => $data['kelurahan_id'],
            'alamat1' => $data['alamat'],
            'rt' => $data['rt'],
            'rw' => $data['rw'],
            'kode_pos' => $data['kodepos'],
            'dms_propinsi' => $data['dms_propinsi_id'],
            'dms_kota' => $data['dms_kabupaten_id'],
            'dms_kecamatan' => $data['dms_kecamatan_id'],
            'dms_kelurahan' => $data['dms_kelurahan_id'],
            'dms_alamat' => $data['dms_alamat'],
            'dms_rt' => $data['dms_rt'],
            'dms_rw' => $data['dms_rw'],
            'dms_kodepos' => $data['dms_kodepos'],
            'akhir_rekanan_id' => $data['rekanan_id'],
            'bpjs_no' => $data['bpjs_no'] ?? '',
            'pekerjaan_id' => $data['pekerjaan_id'],
            'pendidikan_id' => $data['pendidikan_id'],
            'agama_id' => $data['agama_id'],
            'ethnic_id' => $data['ethnic_id'],
            'pjp_nama' => $data['pjp_nama'],
            'pjp_hp' => $data['pjp_hp'],
            'pjp_hubungan_id' => $data['pjp_hubungan_id'],
            'no_kartuprov' => $data['bpjs_no'] ?? '',
            'bahasa' => 'INDONESIA',
            'aktif' => 1,

            'created_by' => $this->session->userdata('user_id_pc'),
            'created_date' => date('Y-m-d H:i:s'),
            'geriatri' => 'N',
            'wna' => 'N',
            'disabilitas' => 'N',

        ];




        $this->db->insert('pc01_gen_pasien_ms', $insert);
    }


    public function cekNikExist($nik)
    {
        $this->db->where('no_identitas', $nik);
        $this->db->where('aktif', '1');
        $query = $this->db->get('pc01_gen_pasien_ms');
        return $query->num_rows() > 0; // TRUE jika NIK aktif sudah ada
    }


    // public function get_all_pasien()
    // {
    //     $this->db->from('pc01_gen_pasien_ms');
    //     $this->db->where('aktif', '1');
    //     return $this->db->get()->result();
    // }

    public function get_all_pasienxx()
    {
        $this->db->select("
        p.pasien_id,
        p.int_pasien_id,
        p.nama || ' ' || COALESCE(s.suffix,'') AS nama,
        p.tgl_lahir,
        p.no_identitas,
        p.no_kartuprov,
        p.akhir_rekanan_id
    ");

        $this->db->from('pc01_gen_pasien_ms p');

        $this->db->join(
            'pcare_manager.pc01_gen_pasien_suffix s',
            "s.jenkel_id = p.sex_id
        AND EXTRACT(YEAR FROM AGE(p.tgl_lahir)) BETWEEN s.usia_min AND s.usia_max
        AND s.status_nikah = p.marital_status_id",
            'left'
        );

        $this->db->where('p.aktif', '1');

        return $this->db->get()->result();
    }


    public function get_all_pasien()
    {
        $this->db->select("
        p.pasien_id,
        p.int_pasien_id,
        p.nama || COALESCE(' ' || s.suffix,'') ||
        ' (' || EXTRACT(YEAR FROM AGE(p.tgl_lahir)) || ' Th)' AS nama,
        p.tgl_lahir,
        p.no_identitas,
        p.no_kartuprov,
        p.akhir_rekanan_id
    ");

        $this->db->from('pc01_gen_pasien_ms p');

        $this->db->join(
            'pcare_manager.pc01_gen_pasien_suffix s',
            "s.jenkel_id = p.sex_id
        AND EXTRACT(YEAR FROM AGE(p.tgl_lahir)) BETWEEN s.usia_min AND s.usia_max
        AND s.status_nikah = p.marital_status_id",
            'left'
        );

        $this->db->where('p.aktif', '1');

        return $this->db->get()->result();
    }


    public function cekPasienByRm($nrm)
    {
        return $this->db->get_where('pc01_gen_pasien_ms', [
            'int_pasien_id' => $nrm,
            'aktif' => '1' // Kondisi pasien aktif
        ])->row_array();
    }

    public function cekPasienByNIK($nik)
    {
        return $this->db->get_where('pc01_gen_pasien_ms', [
            'no_identitas' => $nik,
            'aktif' => '1' // Kondisi pasien aktif
        ])->row_array();
    }

    public function cekPasienByNoBPJS($noBpjs)
    {
        return $this->db->get_where('pc01_gen_pasien_ms', [
            'no_kartuprov' => $noBpjs,
            'aktif' => '1'
        ])->row_array();
    }

    public function getGoldar()
    {
        $query = "SELECT global_id,keterangan
        from pc01_gen_global_ms
        WHERE aktif = '1' and jenis_id = 'GOLDAR' and lokasi_id = '001'";

        $data = $this->db->query($query)->result_array();

        return $data;
    }


    public function getKota($prov_id)
    {
        $query = "SELECT kkk_id, keterangan
        FROM pc01_gen_kkk_ms pgkm
        WHERE aktif = '1' AND jenis = '2' and header_id = '$prov_id'
        ORDER BY keterangan asc";

        return $this->db->query($query)->result();
    }

    public function getStatus()
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

    public function getProvinsi()
    {
        $query = "SELECT kkk_id, keterangan
        FROM pc01_gen_kkk_ms pgkm
        WHERE aktif = '1' AND jenis = '1'
        ORDER BY keterangan asc;";

        return $this->db->query($query)->result_array();
    }

    public function getKecamatan($kota_id)
    {
        $query = "SELECT kkk_id, keterangan
        FROM pc01_gen_kkk_ms pgkm
        WHERE aktif = '1' AND jenis = '3' and header_id = '$kota_id'
        ORDER BY keterangan asc";

        return $this->db->query($query)->result();
    }


    public function getKelurahan($kecamatan_id)
    {
        $query = "SELECT kkk_id, keterangan
        FROM pc01_gen_kkk_ms pgkm
        WHERE aktif = '1' AND jenis = '4' and header_id = '$kecamatan_id'
        ORDER BY keterangan asc";

        return $this->db->query($query)->result();
    }
    public function getPenjamin()
    {
        $query = "SELECT rekanan_id, NAMA
        FROM pc01_keu_rekanan_ms
        WHERE aktif = '1'
        AND rekanan_id IN ('BPJS', 'UMUM','PROGRAM')
        ";
        return $this->db->query($query)->result_array();
    }
    public function getHubPJP()
    {
        $query = "SELECT global_id,keterangan
        FROM pc01_gen_global_ms
        WHERE
        aktif = '1'
        AND jenis_id = 'JHUB'
        AND lokasi_id = '001'";

        return $this->db->query($query)->result_array();
    }

    public function getPekerjaan()
    {
        $query = "SELECT global_id,keterangan
        FROM pc01_gen_global_ms
        WHERE
        AKTIF = '1'
        AND jenis_id = 'JPEK'
        AND lokasi_id = '001'";

        return $this->db->query($query)->result_array();
    }

    public function getPendidikan()
    {
        $query = "SELECT global_id,keterangan
        FROM pc01_gen_global_ms
        WHERE
        AKTIF = '1'
        AND jenis_id = 'SPEND'
        AND lokasi_id = '001'
        order by global_id ASC";

        return $this->db->query($query)->result_array();
    }

    public function getAgama()
    {
        $query = "SELECT global_id,keterangan
        FROM pc01_gen_global_ms
        WHERE aktif = '1'
        AND jenis_id = 'SAGM'
        AND lokasi_id = '001'";
        return $this->db->query($query)->result_array();
    }

    public function getSuku()
    {
        $query = "SELECT suneg_id, keterangan
        FROM pc01_gen_suku_ms
        WHERE
        aktif = '1'
        ORDER BY
        keterangan ASC";
        return $this->db->query($query)->result_array();
    }
}
