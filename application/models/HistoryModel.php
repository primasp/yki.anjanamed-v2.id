<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HistoryModel extends CI_Model
{

    public function listHistKunjungan($pasien_id)
    {
        $query = "SELECT a.pasien_id,a.episode_id, b.int_pasien_id, b.nama as nama_pasien , a.tgl_masuk,a.poli_id,c.keterangan as nama_poli,a.dokter_id,d.nama as nama_dokter ,a.status_episode from pc01_keu_episode a 
                    left join pc01_gen_pasien_ms b on a.pasien_id=b.pasien_id 
                    left join pc01_med_poli_ms c on a.poli_id =c.poli_id 
                    left join pc01_med_dokter_ms d on a.dokter_id =d.dokter_id 
                    where a.pasien_id ='{$pasien_id}'
                    and a.poli_id =d.def_poli_id 
                    and a.status_episode <> '99'
                    and a.aktif ='1'
                    ORDER BY  a.tgl_masuk DESC";
        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }
}
