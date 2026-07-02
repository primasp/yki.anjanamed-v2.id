<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ObatModel extends CI_Model
{
    function listpasien($filtercari, $tanggal = null)
    {

        // return var_dump($tanggal);
        // die;
        // Buat bagian filter tanggal jika ada input tanggal dari user
        $filterTanggal = "";
        if (!empty($tanggal)) {
            $filterTanggal = "AND to_char(b.created_date, 'YYYY-MM-DD') = '{$tanggal}'";
        }

        // Query untuk mencari pasien berdasarkan RM/Nama + filter tanggal jika ada
        $query = "SELECT a.lokasi_id, a.episode_id, a.pasien_id, b.trans_co, 
                        to_char(b.tanggal, 'DD.MM.YYYY') AS tanggal, b.frm_ke, 
                        --to_char(b.created_date, 'DD.MM.YYYY') AS created_date, b.created_by,
                        to_char(b.created_date, 'DD.MM.YYYY HH24:MI:SS') AS created_date, b.created_by,
                        f.int_pasien_id, f.nama, 
                        to_char(f.tgl_lahir, 'DD.MM.YYYY') AS tgl_lahir, 
                        hitung_umur(f.tgl_lahir, current_date) AS umur, 
                        f.sex_id, c.keterangan AS poli, a.tgl_masuk, 
                        e.nama AS rekanan, d.nama AS dokter
                    FROM pc01_keu_episode a
                    LEFT JOIN pc01_med_poli_ms c ON c.lokasi_id = a.lokasi_id AND c.poli_id = a.poli_id
                    LEFT JOIN pc01_med_dokter_ms d ON d.lokasi_id  = a.lokasi_id AND d.dokter_id  = a.dokter_id AND d.aktif = '1'
                    LEFT JOIN pc01_keu_rekanan_ms e ON e.lokasi_id = a.lokasi_id AND e.rekanan_id = a.rekanan_id
                    , pc01_worklist_frm b, pc01_gen_pasien_ms f
                    WHERE a.lokasi_id = b.lokasi_id
                    and c.poli_id=d.def_poli_id
                    AND a.episode_id = b.episode_id 
                    AND a.pasien_id = b.pasien_id
                    AND b.aktif = '1'
                    AND b.status = '0'
                    AND (b.trans_valid_id IS NULL OR b.trans_valid_id = '')
                    AND a.lokasi_id = f.lokasi_id 
                    AND a.pasien_id = f.pasien_id
                    AND (f.int_pasien_id LIKE '%{$filtercari}%' OR f.nama LIKE UPPER('%{$filtercari}%'))
                    {$filterTanggal}  -- Filter tanggal jika ada
                    ORDER BY b.created_date DESC";

        $recordset = $this->db->query($query);
        return $recordset->result();
    }



    function detailresep($episodeid, $pasienid, $transco)
    { //Buat detail resep
        $query = "SELECT a.lokasi_id, a.episode_id, a.trans_id, a.pasien_id, a.trans_co, a.obat_id
                    , case when a.type = '00' then 
                            b.nama
                        when a.type = '01' then 
                            a.nama_racikan
                        when a.type = '02' then 
                            b.nama
                        else
                            b.nama
                        end nama_obat
                    , a.qty, c.int_pasien_id, c.nama
                    , hitung_stok_obat('DEPO00000000APT', a.obat_id) stok, a.type tipe_obat, a.header
                    , case when a.type = '01' then
                            'R'
                        else
                            ' '
                        end ket_tipe
                    , a.satuan_id, (select d.keterangan from pc01_frm_satuan_ms d where d.satuan_id = a.satuan_id) satuan, a.free_dosis
                    , a.signa_id, a.signa_nama, a.signa_dokter, a.catatan, b.harga_jual harga_satuan, b.harga_jual*a.qty total_harga, a.urut
                    from pc01_co_resep_dt a
                    left join pc01_frm_obat_ms b on b.lokasi_id = a.lokasi_id and b.obat_id = a.obat_id
                    , pc01_gen_pasien_ms c
                    where a.lokasi_id = c.lokasi_id
                    and a.pasien_id = c.pasien_id
                    and a.episode_id = '{$episodeid}'
                    and a.pasien_id = '{$pasienid}'
                    and a.trans_co = '{$transco}'
                    and a.status_ver = '0'
                    and a.show_item = '1'
                    order by a.urut
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result(); //"result" kalau hasil lebih dari 1 row
        return $recordset;
    }

    function datapasien($episodeid, $pasienid, $transco)
    { //buat data pasien yang ditarik
        // $query = "SELECT a.lokasi_id, a.episode_id, a.pasien_id, b.trans_id, b.trans_co, b.tanggal, b.frm_ke, b.created_date, b.created_by
        //             , c.int_pasien_id, c.nama, to_char(c.tgl_lahir, 'DD.MM.YYYY') tgl_lahir, hitung_umur(c.tgl_lahir, current_date) umur, c.sex_id, d.keterangan poli, a.tgl_masuk, e.nama rekanan, f.nama dokter
        //             from pc01_keu_episode a
        //             left join pc01_med_poli_ms d on d.lokasi_id = a.lokasi_id and d.poli_id = a.poli_id
        //             left join pc01_keu_rekanan_ms e on e.lokasi_id = a.lokasi_id and e.rekanan_id = a.rekanan_id
        //             left join pc01_med_dokter_ms f on f.lokasi_id = a.lokasi_id and f.dokter_id = a.dokter_id
        //             , pc01_worklist_frm b, pc01_gen_pasien_ms c
        //             where a.lokasi_id = b.lokasi_id
        //             and a.episode_id = b.episode_id 
        //             and a.pasien_id = b.pasien_id
        //             and b.aktif = '1'
        //             and (b.trans_valid_id is null or b.trans_valid_id = '')
        //             and a.lokasi_id = c.lokasi_id 
        //             and a.pasien_id = c.pasien_id
        //             and a.episode_id = '{$episodeid}'
        //             and a.pasien_id = '{$pasienid}'
        //             and b.trans_co = '{$transco}'
        //         ";

        $query = "SELECT DISTINCT a.lokasi_id, a.episode_id, a.pasien_id, b.trans_id, b.trans_co, b.tanggal, b.frm_ke, b.created_date, b.created_by, g.icd10,g.diagnosa 
                    , c.int_pasien_id, c.nama, to_char(c.tgl_lahir, 'DD.MM.YYYY') tgl_lahir, hitung_umur(c.tgl_lahir, current_date) umur, c.sex_id, d.keterangan poli, a.tgl_masuk, e.nama rekanan, f.nama dokter
                    from pc01_keu_episode a
                    left join pc01_med_poli_ms d on d.lokasi_id = a.lokasi_id and d.poli_id = a.poli_id
                    left join pc01_keu_rekanan_ms e on e.lokasi_id = a.lokasi_id and e.rekanan_id = a.rekanan_id
                    left join pc01_med_dokter_ms f on f.lokasi_id = a.lokasi_id and f.dokter_id = a.dokter_id AND f.aktif = '1'
                    left join pc01_co_diagnosa_ms g on g.lokasi_id =a.lokasi_id  and g.episode_id =a.episode_id  
                    , pc01_worklist_frm b, pc01_gen_pasien_ms c
                    where a.lokasi_id = b.lokasi_id
                    and a.episode_id = b.episode_id 
                    and a.pasien_id = b.pasien_id and g.show_item ='1'
                    and b.aktif = '1'
                    and (b.trans_valid_id is null or b.trans_valid_id = '')
                    and a.lokasi_id = c.lokasi_id 
                    and a.pasien_id = c.pasien_id
                    and a.episode_id = '{$episodeid}'
                    and a.pasien_id = '{$pasienid}'
                    and b.trans_co = '{$transco}'";

        $recordset = $this->db->query($query);
        $recordset = $recordset->row(); //"row" jika hasil hanya 1 row
        return $recordset;
    }

    function simpanresepit($data, $obatData, $transvalid, $ip, $gudangid)
    {
        $query = "INSERT INTO pc01_frm_validasi_it(
                    lokasi_id,
                    episode_id, 
                    trans_id,
                    pasien_id, 
                    trans_valid_id, 
                    trans_co, 
                    tanggal,
                    obat_id, 
                    nama_obat, 
                    satuan_id, 
                    qty, 
                    signa_id, 
                    signa_nama, 
                    signa_dokter, 
                    resep_ke,
                    harga,
                    total_harga,
                    urut, 
                    catatan, 
                    type, 
                    header, 
                    nama_racikan, 
                    free_dosis,
                    dosis_obat,
                    ip_komputer
                    )                    
                    
                    values

                    (
                    '001', 
                    '{$data['EPISODE_ID']}', 
                    '{$data['TRANS_ID']}', 
                    '{$data['PASIEN_ID']}', 
                    '{$transvalid}', 
                    '{$data['TRANS_CO']}', 
                    '{$data['TANGGAL']}', 
                    '{$obatData['OBAT_ID']}', 
                    '{$obatData['NAMA_OBAT']}', 
                    '{$obatData['SATUAN_ID']}', 
                    '{$obatData['QTY']}', 
                    '{$obatData['SIGNA_ID']}', 
                    '{$obatData['SIGNA_NAMA']}',
                    '{$obatData['SIGNA_DOKTER']}',
                    '{$data['RESEP_KE']}', 
                    '{$obatData['HARGA']}',
                    '{$obatData['TOTAL_HARGA']}',
                    '{$obatData['URUT']}',
                    '{$obatData['CATATAN']}',
                    '{$obatData['TIPE_OBAT']}',
                    '{$obatData['HEADER']}',
                    '{$obatData['NAMA_RACIKAN']}',
                    '{$obatData['FREEDOSIS']}',
                    '{$obatData['DOSIS']}',
                    '{$ip}'
                    )
                ";
        $recordset = $this->db->query($query);



        $updatestok = "UPDATE pc01_frm_gudang_stok 
                    SET mutjl = mutjl + '{$obatData['QTY']}'
                    where gudang_id = '{$gudangid}'
                    and obat_id = '{$obatData['OBAT_ID']}'
                    and aktif = '1'
                ";
        $recordstok = $this->db->query($updatestok);


        return $recordset && $recordstok;
    }

    function simpanresephd($data, $transvalid, $ip, $gudangid)
    {
        $query = "INSERT into pc01_frm_validasi_hd
                    (lokasi_id, episode_id, trans_id, pasien_id, trans_co, trans_valid_id, tanggal, total_harga, jenis_tr, resep_ke, gudang_obat, ip_komputer)
                    values
                    ('001', '{$data['EPISODE_ID']}', '{$data['TRANS_ID']}', '{$data['PASIEN_ID']}', '{$data['TRANS_CO']}', '{$transvalid}', '{$data['TANGGAL']}', '{$data['TOTAL_HARGA']}', '0', '{$data['RESEP_KE']}', '{$gudangid}', '{$ip}' )
                ";
        $recordset = $this->db->query($query);

        $updateWorklist =
            "
                    update pc01_worklist_frm 
                    set trans_valid_id = '{$transvalid}'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                    and trans_co = '{$data['TRANS_CO']}'
                    and aktif = '1'
                ";
        $recordworklist = $this->db->query($updateWorklist);

        $updateResep =
            "
                    update pc01_co_resep_dt 
                    set status_ver = '1'
                    where episode_id = '{$data['EPISODE_ID']}'
                    and pasien_id = '{$data['PASIEN_ID']}'
                    and trans_co = '{$data['TRANS_CO']}'
                    and show_item = '1'
                ";
        $recordresep = $this->db->query($updateResep);



        return $recordset && $recordworklist && $recordresep;
    }

    function gettransvalid()
    {
        $query =
            "
                    select 'V'||to_char(current_timestamp, 'YYYYMMDDHH24MISS') trans_valid_id
                ";

        $recordset = $this->db->query($query)->row();

        return $recordset;
    }


    function loadhistory($pasienid)
    { //Buat riwayat pemeriksaan
        $query =
            "
                    select a.episode_id, a.pasien_id, to_char(a.tgl_masuk, 'DD.MM.YYYY') tgl_masuk, b.keterangan poli
                    from pc01_keu_episode a, pc01_med_poli_ms b
                    where a.lokasi_id = b.lokasi_id
                    and a.aktif = '1'
                    and a.poli_id = b.poli_id                    
                    and a.pasien_id = '{$pasienid}'
                    order by a.tgl_masuk desc
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function loadalergi($pasienid)
    { //Buat riwayat alergi
        $query =
            "
                    select a.lokasi_id, a.pasien_id, a.alergi_id
                    , case when a.alergi_id = '' then 
                            a.alergi_teks 
                        else
                            b.alergi
                        end alergi
                    from pc01_co_alergi_dt a
                    left join pc01_co_alergi_ms b on b.lokasi_id = a.lokasi_id and b.alergi_id = a.alergi_id
                    and a.pasien_id = '{$pasienid}'
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function cariobatms($filtercari)
    { //Buat pencarian obat
        // $query = "SELECT a.lokasi_id, a.obat_id, a.nama nama_obat, hitung_stok_obat('DEPO00000000APT', a.obat_id) stok, a.harga_sat_ppn harga, b.satuan_id, b.keterangan satuan
        //             from pc01_frm_obat_ms a, pc01_frm_satuan_ms b
        //             where is_pembungkus = 'N'
        //             and upper(a.nama) like upper('%{$filtercari}%')
        //             and a.satuan_kecil_id = b.satuan_id
        //             order by a.nama
        //         ";

        $query = "SELECT a.lokasi_id, a.obat_id, a.nama nama_obat, hitung_stok_obat('DEPO00000000APT', a.obat_id) stok, a.harga_jual harga, b.satuan_id, b.keterangan satuan
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

    function caribungkus($filtercari)
    {
        $query =
            "
                    select '' lokasi_id, '' obat_id, '' nama_obat, 0 stok, '' satuan_id, '' keterangan, 0 urut
                    union
                    select a.lokasi_id, a.obat_id, a.nama nama_obat, hitung_stok_obat('DEPO00000000APT', a.obat_id) stok, b.satuan_id, b.keterangan, 1 urut
                    from pc01_frm_obat_ms a, pc01_frm_satuan_ms b
                    where is_pembungkus = 'Y'
                    and upper(a.nama) like upper('%{$filtercari}%')
                    and a.satuan_kecil_id = b.satuan_id
                    order by urut, nama_obat
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
    }

    function carisigna($filtercari)
    {
        $query =
            "
                    select '' lokasi_id, '' signa_id, '' signa, 0 urut
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
}
