<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KasirUmumModel extends CI_Model
{
    private $schema = '';
    private $fieldCache = [];

    private function t($table)
    {
        return $this->schema . $table;
    }

    public function get_payment_methods()
    {
        return [
            ['id' => 'TUNAI',    'nama' => 'Tunai',    'jbayar_id' => 'TUNAI'],
            ['id' => 'QRIS',     'nama' => 'QRIS',     'jbayar_id' => 'QRIS'],
            ['id' => 'DEBIT',    'nama' => 'Debit',    'jbayar_id' => 'DEBIT'],
            ['id' => 'TRANSFER', 'nama' => 'Transfer', 'jbayar_id' => 'TRANSFER'],
        ];
    }

    private function map_payment_method($method)
    {
        $method = strtoupper(trim((string) $method));
        $map = [
            'CASH'     => ['jbayar_id' => 'TUNAI',    'keterangan' => 'TUNAI'],
            'TUNAI'    => ['jbayar_id' => 'TUNAI',    'keterangan' => 'TUNAI'],
            'QRIS'     => ['jbayar_id' => 'QRIS',     'keterangan' => 'QRIS'],
            'DEBIT'    => ['jbayar_id' => 'DEBIT',    'keterangan' => 'DEBIT'],
            'TRANSFER' => ['jbayar_id' => 'TRANSFER', 'keterangan' => 'TRANSFER'],
        ];

        return isset($map[$method]) ? $map[$method] : ['jbayar_id' => $method, 'keterangan' => $method];
    }

    private function num($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return (float) $value;
        }

        return (float) str_replace(',', '', $value);
    }

    private function table_schema_name($table)
    {
        if (strpos($table, '.') !== false) {
            $parts = explode('.', $table);
            return [$parts[0], $parts[1]];
        }

        $schema = rtrim($this->schema, '.');
        if ($schema === '') {
            $schema = 'pcare_manager';
        }

        return [$schema, $table];
    }

    private function has_field($table, $column)
    {
        list($schema, $tableName) = $this->table_schema_name($table);
        $key = $schema . '.' . $tableName . '.' . $column;

        if (array_key_exists($key, $this->fieldCache)) {
            return $this->fieldCache[$key];
        }

        $sql = "SELECT COUNT(*) AS jml FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?";
        $row = $this->db->query($sql, [$schema, $tableName, $column])->row();
        $exists = !empty($row) && (int) $row->jml > 0;
        $this->fieldCache[$key] = $exists;

        return $exists;
    }

    private function add_if_field_exists(&$data, $table, $column, $value)
    {
        if ($this->has_field($table, $column)) {
            $data[$column] = $value;
        }
    }

    private function make_bayar_id()
    {
        try {
            $row = $this->db->query("SELECT buat_id_bayar_id(?) AS bayar_id", ['1'])->row();
            if (!empty($row) && !empty($row->bayar_id)) {
                return $row->bayar_id;
            }
        } catch (Exception $e) {
            // fallback manual
        }

        return 'BYR' . date('ymdHis') . mt_rand(100, 999);
    }

    private function calculate_age($tgl_lahir)
    {
        if (empty($tgl_lahir)) {
            return '-';
        }

        try {
            $birth = new DateTime($tgl_lahir);
            $today = new DateTime();
            $diff  = $today->diff($birth);
            return $diff->y . ' Thn ' . $diff->m . ' Bln';
        } catch (Exception $e) {
            return '-';
        }
    }

    /**
     * Catatan penting:
     * pc01_keu_transctr_it dan pc01_keu_transfrm_it pada struktur Bapak TIDAK punya bayar_id/rekanan_id.
     * Karena itu status belum bayar harus diambil dari HEADER pc01_keu_transaksi_hd.
     * Pendaftaran yang sudah dibayar di registrasi punya bayar_id pada header, sehingga tidak muncul lagi di kasir.
     */
    private function unpaid_header_condition($alias = 'a')
    {
        return "
        COALESCE({$alias}.aktif, '1') = '1'
        AND {$alias}.bayar_id IS NULL
        AND {$alias}.tgl_lunas IS NULL
        AND UPPER(TRIM(COALESCE({$alias}.rekanan_id, ''))) = 'UMUM'
        AND COALESCE({$alias}.jenis_tr, '') IN ('001','003','004','005','006')
    ";
    }

    private function total_expr($alias)
    {
        return "COALESCE(NULLIF({$alias}.harga_total, 0), COALESCE(NULLIF({$alias}.qty, 0), 1) * COALESCE({$alias}.harga_satuan, 0), 0)";
    }


    private function jenis_tr_label_expr($alias = 'a')
    {
        return "CASE
        WHEN {$alias}.jenis_tr = '001' THEN 'PENDAFTARAN'
        WHEN {$alias}.jenis_tr = '003' THEN 'TINDAKAN'
        WHEN {$alias}.jenis_tr = '004' THEN 'PENUNJANG'
        WHEN {$alias}.jenis_tr = '005' THEN 'PENUNJANG'
        WHEN {$alias}.jenis_tr = '006' THEN 'OBAT'
        ELSE 'LAINNYA'
    END";
    }


    private function jenis_tr_kategori_expr($alias = 'a')
    {
        return "CASE
        WHEN {$alias}.jenis_tr = '001' THEN 'BIAYA PENDAFTARAN'
        WHEN {$alias}.jenis_tr = '003' THEN 'TINDAKAN UMUM'
        WHEN {$alias}.jenis_tr = '004' THEN 'LABORATORIUM'
        WHEN {$alias}.jenis_tr = '005' THEN 'RADIOLOGI'
        WHEN {$alias}.jenis_tr = '006' THEN 'RESEP OBAT'
        ELSE 'LAINNYA'
    END";
    }



    public function get_worklist($lokasi_id, $tanggal_mulai = null, $tanggal_selesai = null, $keyword = '', $kategori = '')
    {
        if (empty($tanggal_mulai)) $tanggal_mulai = date('Y-m-d');
        if (empty($tanggal_selesai)) $tanggal_selesai = $tanggal_mulai;

        $keyword  = trim((string) $keyword);
        $kategori = strtoupper(trim((string) $kategori));

        $whereKeyword = '';
        $whereKategori = '';
        $outerParams = [];

        if ($keyword !== '') {
            $whereKeyword = "
            AND (
                LOWER(p.nama) LIKE LOWER(?)
                OR LOWER(p.int_pasien_id) LIKE LOWER(?)
                OR LOWER(e.episode_id) LIKE LOWER(?)
                OR LOWER(x.trans_id) LIKE LOWER(?)
            )
        ";
            $kw = '%' . $keyword . '%';
            $outerParams[] = $kw;
            $outerParams[] = $kw;
            $outerParams[] = $kw;
            $outerParams[] = $kw;
        }

        if (in_array($kategori, ['PENDAFTARAN', 'PENUNJANG', 'TINDAKAN', 'OBAT'], true)) {
            $whereKategori = " AND x.kelompok = ? ";
            $outerParams[] = $kategori;
        }

        $unpaidHeader = $this->unpaid_header_condition('a');
        $kelompok = $this->jenis_tr_label_expr('a');
        $kategoriExpr = $this->jenis_tr_kategori_expr('a');
        $totalB = $this->total_expr('b');
        $totalC = $this->total_expr('c');

        $sql = "
        WITH unpaid_items AS (
            SELECT
                a.lokasi_id,
                a.episode_id,
                a.pasien_id,
                a.trans_id,
                a.jenis_tr,
                a.rekanan_id,
                {$kelompok} AS kelompok,
                {$kategoriExpr} AS kategori,
                b.layan_id AS ref_id,
                COALESCE(lm.nama_layan1, b.layan_id) AS nama_item,
                COALESCE(lm.kategori_id, '') AS kategori_id,
                COALESCE(NULLIF(b.qty,0),1) AS qty,
                COALESCE(b.harga_satuan,0) AS tarif,
                {$totalB} AS total_item
            FROM {$this->t('pc01_keu_transaksi_hd')} a
            JOIN {$this->t('pc01_keu_transctr_it')} b
                ON a.lokasi_id  = b.lokasi_id
               AND a.episode_id = b.episode_id
               AND a.trans_id   = b.trans_id
               AND a.pasien_id  = b.pasien_id
            LEFT JOIN {$this->t('pc01_keu_layan_ms')} lm
                ON lm.lokasi_id = b.lokasi_id
               AND lm.layan_id  = b.layan_id
            WHERE a.lokasi_id = ?
              AND a.tgl_transaksi::date BETWEEN ? AND ?
              AND {$unpaidHeader}
              AND a.jenis_tr IN ('001','003','004','005')
              AND COALESCE(b.aktif, '1') = '1'
              AND COALESCE(b.layan_id, '') NOT IN ('ADM01','JASADR')
              AND {$totalB} > 0

            UNION ALL

            SELECT
                a.lokasi_id,
                a.episode_id,
                a.pasien_id,
                a.trans_id,
                a.jenis_tr,
                a.rekanan_id,
                'OBAT' AS kelompok,
                'RESEP OBAT' AS kategori,
                c.obat_id AS ref_id,
                COALESCE(ob.nama, c.obat_id) AS nama_item,
                'OBAT' AS kategori_id,
                COALESCE(NULLIF(c.qty,0),1) AS qty,
                COALESCE(c.harga_satuan,0) AS tarif,
                {$totalC} AS total_item
            FROM {$this->t('pc01_keu_transaksi_hd')} a
            JOIN {$this->t('pc01_keu_transfrm_it')} c
                ON a.lokasi_id  = c.lokasi_id
               AND a.episode_id = c.episode_id
               AND a.trans_id   = c.trans_id
               AND a.pasien_id  = c.pasien_id
            LEFT JOIN {$this->t('pc01_frm_obat_ms')} ob
                ON ob.lokasi_id = c.lokasi_id
               AND ob.obat_id   = c.obat_id
            WHERE a.lokasi_id = ?
              AND a.tgl_transaksi::date BETWEEN ? AND ?
              AND {$unpaidHeader}
              AND a.jenis_tr = '006'
              AND COALESCE(c.aktif, '1') = '1'
              AND {$totalC} > 0
        )
        SELECT
            e.lokasi_id,
            e.episode_id,
            e.pasien_id,
            COALESCE(NULLIF(MAX(x.rekanan_id), ''), 'UMUM') AS rekanan_id,
            e.poli_id,
            e.dokter_id,
            e.tgl_masuk,
            p.int_pasien_id AS no_rm,
            p.nama,
            p.no_identitas,
            CASE WHEN p.sex_id = 'L' THEN 'Laki-laki' WHEN p.sex_id = 'P' THEN 'Perempuan' ELSE '-' END AS jk,
            TO_CHAR(p.tgl_lahir, 'YYYY-MM-DD') AS tgl_lahir,
            COALESCE(p.alamat1, '') AS alamat,
            COALESCE(pm.keterangan, '-') AS poli,
            COALESCE(dm.nama, '-') AS dokter,
            'PASIEN UMUM' AS nama_penjamin,
            'UMUM' AS jenis_penjamin,
            'INV-UM-' || TO_CHAR(e.tgl_masuk, 'YYYYMMDD') || '-' || e.episode_id AS invoice_no,
            COUNT(*) AS jumlah_item,
            COALESCE(SUM(x.total_item), 0) AS total_tagihan,
            COALESCE(SUM(CASE WHEN x.kelompok = 'PENDAFTARAN' THEN x.total_item ELSE 0 END), 0) AS total_pendaftaran,
            COALESCE(SUM(CASE WHEN x.kelompok = 'PENUNJANG' THEN x.total_item ELSE 0 END), 0) AS total_penunjang,
            COALESCE(SUM(CASE WHEN x.kelompok = 'TINDAKAN' THEN x.total_item ELSE 0 END), 0) AS total_tindakan,
            COALESCE(SUM(CASE WHEN x.kelompok = 'OBAT' THEN x.total_item ELSE 0 END), 0) AS total_obat,
            STRING_AGG(DISTINCT x.kelompok, ', ' ORDER BY x.kelompok) AS kelompok_tagihan,
            STRING_AGG(DISTINCT x.jenis_tr, ', ' ORDER BY x.jenis_tr) AS jenis_tr_tagihan,
            STRING_AGG(DISTINCT x.trans_id, ', ' ORDER BY x.trans_id) AS trans_id_tagihan
        FROM unpaid_items x
        JOIN {$this->t('pc01_keu_episode')} e
            ON e.lokasi_id  = x.lokasi_id
           AND e.episode_id = x.episode_id
           AND e.pasien_id  = x.pasien_id
        JOIN {$this->t('pc01_gen_pasien_ms')} p
            ON p.lokasi_id = e.lokasi_id
           AND p.pasien_id = e.pasien_id
        LEFT JOIN {$this->t('pc01_med_poli_ms')} pm
            ON pm.lokasi_id = e.lokasi_id
           AND pm.poli_id = e.poli_id
        LEFT JOIN {$this->t('pc01_med_dokter_ms')} dm
            ON dm.lokasi_id = e.lokasi_id
           AND dm.dokter_id = e.dokter_id
           AND dm.aktif = '1'
        WHERE e.lokasi_id = ?
          AND e.aktif = '1'
          AND COALESCE(e.status_episode, '00') <> '99'
          {$whereKeyword}
          {$whereKategori}
        GROUP BY
            e.lokasi_id, e.episode_id, e.pasien_id, e.poli_id, e.dokter_id, e.tgl_masuk,
            p.int_pasien_id, p.nama, p.no_identitas, p.sex_id, p.tgl_lahir, p.alamat1,
            pm.keterangan, dm.nama
        ORDER BY e.tgl_masuk ASC, p.nama ASC
    ";

        $params = array_merge(
            [$lokasi_id, $tanggal_mulai, $tanggal_selesai, $lokasi_id, $tanggal_mulai, $tanggal_selesai, $lokasi_id],
            $outerParams
        );

        $rows = $this->db->query($sql, $params)->result_array();

        foreach ($rows as &$r) {
            $r['tanggal_kunjungan'] = !empty($r['tgl_masuk']) ? date('d-m-Y H:i', strtotime($r['tgl_masuk'])) : '-';
            $r['umur'] = $this->calculate_age($r['tgl_lahir']);
            $r['total_tagihan']     = (float) $r['total_tagihan'];
            $r['total_pendaftaran'] = (float) $r['total_pendaftaran'];
            $r['total_penunjang']   = (float) $r['total_penunjang'];
            $r['total_tindakan']    = (float) $r['total_tindakan'];
            $r['total_obat']        = (float) $r['total_obat'];
        }
        unset($r);

        return $rows;
    }











    public function get_billing_patient($lokasi_id, $episode_id)
    {
        $detail = $this->get_patient_detail($lokasi_id, $episode_id);
        if (empty($detail)) {
            return null;
        }

        $items = $this->get_unpaid_items($lokasi_id, $episode_id);
        if (empty($items)) {
            return null;
        }

        $detail->items = $items;
        $detail->total_tagihan = array_sum(array_column($items, 'harga_total'));
        $detail->total_penunjang = 0;
        $detail->total_tindakan = 0;
        $detail->total_obat = 0;

        foreach ($items as $it) {
            if ($it['source_type'] === 'PENUNJANG') {
                $detail->total_penunjang += (float) $it['harga_total'];
            } elseif ($it['source_type'] === 'TINDAKAN') {
                $detail->total_tindakan += (float) $it['harga_total'];
            } elseif ($it['source_type'] === 'OBAT') {
                $detail->total_obat += (float) $it['harga_total'];
            }
        }

        return $detail;
    }



    private function get_patient_detail($lokasi_id, $episode_id)
    {
        $sql = "
        SELECT
            e.lokasi_id,
            e.episode_id,
            e.pasien_id,
            'UMUM' AS rekanan_id,
            e.poli_id,
            e.dokter_id,
            e.tgl_masuk,
            p.int_pasien_id AS no_rm,
            p.nama,
            p.no_identitas,
            CASE WHEN p.sex_id = 'L' THEN 'Laki-laki' WHEN p.sex_id = 'P' THEN 'Perempuan' ELSE '-' END AS jk,
            TO_CHAR(p.tgl_lahir, 'YYYY-MM-DD') AS tgl_lahir,
            COALESCE(p.alamat1, '') AS alamat,
            COALESCE(pm.keterangan, '-') AS poli,
            COALESCE(dm.nama, '-') AS dokter,
            'PASIEN UMUM' AS nama_penjamin,
            'UMUM' AS jenis_penjamin,
            'INV-UM-' || TO_CHAR(e.tgl_masuk, 'YYYYMMDD') || '-' || e.episode_id AS invoice_no
        FROM {$this->t('pc01_keu_episode')} e
        JOIN {$this->t('pc01_gen_pasien_ms')} p
            ON p.lokasi_id = e.lokasi_id
           AND p.pasien_id = e.pasien_id
        LEFT JOIN {$this->t('pc01_med_poli_ms')} pm
            ON pm.lokasi_id = e.lokasi_id
           AND pm.poli_id = e.poli_id
        LEFT JOIN {$this->t('pc01_med_dokter_ms')} dm
            ON dm.lokasi_id = e.lokasi_id
           AND dm.dokter_id = e.dokter_id
           AND dm.aktif = '1'
        WHERE e.lokasi_id = ?
          AND e.episode_id = ?
          AND e.aktif = '1'
          AND COALESCE(e.status_episode, '00') <> '99'
          AND EXISTS (
                SELECT 1
                FROM {$this->t('pc01_keu_transaksi_hd')} h
                WHERE h.lokasi_id = e.lokasi_id
                  AND h.episode_id = e.episode_id
                  AND h.pasien_id = e.pasien_id
                  AND COALESCE(h.aktif, '1') = '1'
                  AND h.bayar_id IS NULL
                  AND h.tgl_lunas IS NULL
                  AND UPPER(TRIM(COALESCE(h.rekanan_id, ''))) = 'UMUM'
                  AND COALESCE(h.jenis_tr, '') IN ('001','003','004','005','006')
          )
    ";

        $row = $this->db->query($sql, [$lokasi_id, $episode_id])->row();
        if (!empty($row)) {
            $row->tanggal_kunjungan = !empty($row->tgl_masuk) ? date('d-m-Y H:i', strtotime($row->tgl_masuk)) : '-';
            $row->umur = $this->calculate_age($row->tgl_lahir);
        }

        return $row;
    }




    private function get_unpaid_items($lokasi_id, $episode_id)
    {
        $unpaidHeader = $this->unpaid_header_condition('a');
        $kelompok = $this->jenis_tr_label_expr('a');
        $kategoriExpr = $this->jenis_tr_kategori_expr('a');
        $totalB = $this->total_expr('b');
        $totalC = $this->total_expr('c');

        $sqlTindakan = "
        SELECT
            {$kelompok} AS source_type,
            {$kategoriExpr} AS kategori,
            ('TRX|' || a.jenis_tr || '|' || b.trans_id || '|' || b.layan_id) AS source_id,
            a.jenis_tr,
            b.trans_id,
            b.layan_id AS ref_id,
            COALESCE(lm.nama_layan1, b.layan_id) AS nama_item,
            COALESCE(lm.kategori_id, '') AS kategori_id,
            COALESCE(NULLIF(b.qty,0),1) AS qty,
            COALESCE(b.harga_satuan,0) AS tarif,
            COALESCE(b.disk_pct,0) AS disk_pct,
            COALESCE(b.disk_tot,0) AS disk_tot,
            {$totalB} AS harga_total,
            CASE WHEN a.jenis_tr = '001' THEN 0 WHEN a.jenis_tr = '004' THEN 1 WHEN a.jenis_tr = '005' THEN 2 ELSE 3 END AS sort_order
        FROM {$this->t('pc01_keu_transaksi_hd')} a
        JOIN {$this->t('pc01_keu_transctr_it')} b
            ON a.lokasi_id  = b.lokasi_id
           AND a.episode_id = b.episode_id
           AND a.trans_id   = b.trans_id
           AND a.pasien_id  = b.pasien_id
        LEFT JOIN {$this->t('pc01_keu_layan_ms')} lm
            ON lm.lokasi_id = b.lokasi_id
           AND lm.layan_id  = b.layan_id
        WHERE a.lokasi_id = ?
          AND a.episode_id = ?
          AND {$unpaidHeader}
          AND a.jenis_tr IN ('001','003','004','005')
          AND COALESCE(b.aktif, '1') = '1'
          AND COALESCE(b.layan_id, '') NOT IN ('ADM01','JASADR')
          AND {$totalB} > 0
        ORDER BY sort_order, nama_item
    ";
        $tindakan = $this->db->query($sqlTindakan, [$lokasi_id, $episode_id])->result_array();

        $sqlObat = "
        SELECT
            'OBAT' AS source_type,
            'RESEP OBAT' AS kategori,
            ('OBAT|006|' || c.trans_id || '|' || COALESCE(c.no_urutobat,0)::text || '|' || COALESCE(c.obat_id,'')) AS source_id,
            a.jenis_tr,
            c.trans_id,
            c.obat_id AS ref_id,
            COALESCE(ob.nama, c.obat_id) AS nama_item,
            'OBAT' AS kategori_id,
            COALESCE(NULLIF(c.qty,0),1) AS qty,
            COALESCE(c.harga_satuan,0) AS tarif,
            COALESCE(c.disk_pct,0) AS disk_pct,
            COALESCE(c.disk_tot,0) AS disk_tot,
            {$totalC} AS harga_total,
            4 AS sort_order
        FROM {$this->t('pc01_keu_transaksi_hd')} a
        JOIN {$this->t('pc01_keu_transfrm_it')} c
            ON a.lokasi_id  = c.lokasi_id
           AND a.episode_id = c.episode_id
           AND a.trans_id   = c.trans_id
           AND a.pasien_id  = c.pasien_id
        LEFT JOIN {$this->t('pc01_frm_obat_ms')} ob
            ON ob.lokasi_id = c.lokasi_id
           AND ob.obat_id   = c.obat_id
        WHERE a.lokasi_id = ?
          AND a.episode_id = ?
          AND {$unpaidHeader}
          AND a.jenis_tr = '006'
          AND COALESCE(c.aktif, '1') = '1'
          AND {$totalC} > 0
        ORDER BY c.no_urutobat, nama_item
    ";
        $obat = $this->db->query($sqlObat, [$lokasi_id, $episode_id])->result_array();

        $items = array_merge($tindakan, $obat);

        foreach ($items as &$it) {
            $it['qty'] = (float)$it['qty'];
            $it['tarif'] = (float)$it['tarif'];
            $it['disk_pct'] = (float)$it['disk_pct'];
            $it['disk_tot'] = (float)$it['disk_tot'];
            $it['harga_total'] = (float)$it['harga_total'];
            $it['selected'] = true;
        }
        unset($it);

        return $items;
    }



    public function proses_bayar_umum($payload)
    {
        $lokasi_id    = $payload['lokasi_id'] ?? '';
        $episode_id   = $payload['episode_id'] ?? '';
        $pasien_id    = $payload['pasien_id'] ?? '';
        $created_by   = $payload['created_by'] ?? '';
        $itemsPayload = $payload['items'] ?? [];
        $payments     = $payload['payments'] ?? [];
        $dibayar_oleh = trim((string) ($payload['dibayar_oleh'] ?? ''));

        if (empty($lokasi_id) || empty($episode_id) || empty($pasien_id)) {
            return ['success' => false, 'message' => 'Data pasien tidak lengkap.'];
        }
        if (empty($itemsPayload) || !is_array($itemsPayload)) {
            return ['success' => false, 'message' => 'Belum ada item tagihan yang dipilih.'];
        }
        if (empty($payments) || !is_array($payments)) {
            return ['success' => false, 'message' => 'Metode pembayaran belum diisi.'];
        }

        $patient = $this->get_patient_detail($lokasi_id, $episode_id);
        if (empty($patient)) {
            return ['success' => false, 'message' => 'Episode pasien UMUM tidak valid atau tidak ditemukan.'];
        }

        $unpaidItems = $this->get_unpaid_items($lokasi_id, $episode_id);
        if (empty($unpaidItems)) {
            return ['success' => false, 'message' => 'Tidak ada tagihan UMUM yang belum dibayar.'];
        }

        $unpaidMap = [];
        foreach ($unpaidItems as $row) {
            $unpaidMap[$row['source_id']] = $row;
        }

        $selected = [];
        foreach ($itemsPayload as $item) {
            $sourceId = $item['source_id'] ?? '';
            if ($sourceId !== '' && isset($unpaidMap[$sourceId])) {
                $selected[$sourceId] = $unpaidMap[$sourceId];
            }
        }
        $selected = array_values($selected);

        if (empty($selected)) {
            return ['success' => false, 'message' => 'Item yang dipilih sudah dibayar atau tidak valid.'];
        }

        /**
         * Karena bayar_id ada di pc01_keu_transaksi_hd, bukan di tabel detail,
         * pembayaran parsial per baris detail berisiko membuat list/detail tidak sinkron.
         * Untuk tahap ini, kasir UMUM harus membayar seluruh tagihan belum bayar pada episode yang dipilih.
         */
        if (count($selected) !== count($unpaidItems)) {
            return [
                'success' => false,
                'message' => 'Pembayaran harus mencakup seluruh tagihan belum bayar pada episode ini. Silakan pilih semua item tagihan.'
            ];
        }

        $subtotal = 0;
        foreach ($selected as $it) {
            $subtotal += (float) $it['harga_total'];
        }

        $discountType  = strtoupper(trim((string) ($payload['discount_type'] ?? 'NOMINAL')));
        $discountValue = $this->num($payload['discount_value'] ?? 0);

        if ($discountType === 'PERSEN') {
            if ($discountValue < 0) $discountValue = 0;
            if ($discountValue > 100) $discountValue = 100;
            $discountTotal = round($subtotal * $discountValue / 100, 2);
        } else {
            if ($discountValue < 0) $discountValue = 0;
            if ($discountValue > $subtotal) $discountValue = $subtotal;
            $discountTotal = $discountValue;
            $discountType = 'NOMINAL';
        }

        $netTotal = max($subtotal - $discountTotal, 0);

        $totalPaid = 0;
        foreach ($payments as $p) {
            $totalPaid += $this->num($p['amount'] ?? 0);
        }

        if ($totalPaid < $netTotal) {
            return ['success' => false, 'message' => 'Pembayaran kurang dari total tagihan.'];
        }

        $kembali  = $totalPaid - $netTotal;
        $bayar_id = $this->make_bayar_id();

        $this->db->trans_begin();
        try {
            $sumNet = 0;
            foreach ($selected as &$it) {
                $gross = (float) $it['harga_total'];
                $itemDisk = $subtotal > 0 ? round(($gross / $subtotal) * $discountTotal, 2) : 0;
                $itemNet = $gross - $itemDisk;
                $it['_disk_pct_new'] = $discountType === 'PERSEN' ? $discountValue : ($gross > 0 ? round(($itemDisk / $gross) * 100, 4) : 0);
                $it['_disk_tot_new'] = $itemDisk;
                $it['_harga_total_new'] = $itemNet;
                $sumNet += $itemNet;
            }
            unset($it);

            $selisih = round($netTotal - $sumNet, 2);
            if (!empty($selected) && $selisih != 0) {
                $last = count($selected) - 1;
                $selected[$last]['_harga_total_new'] = round($selected[$last]['_harga_total_new'] + $selisih, 2);
                $selected[$last]['_disk_tot_new'] = max(round($selected[$last]['_disk_tot_new'] - $selisih, 2), 0);
            }

            foreach ($selected as $it) {
                if ($it['source_type'] === 'OBAT') {
                    $this->update_obat_item_total($lokasi_id, $episode_id, $pasien_id, $it, $created_by);
                } else {
                    $this->update_tindakan_item_total($lokasi_id, $episode_id, $pasien_id, $it, $created_by);
                }
            }

            $this->mark_unpaid_headers_paid($lokasi_id, $episode_id, $pasien_id, $bayar_id, $created_by);

            $dataByr = [
                'lokasi_id' => $lokasi_id,
                'episode_id' => $episode_id,
                'pasien_id' => $pasien_id,
                'bayar_id' => $bayar_id,
                'shift_id' => null,
                'rekanan_id' => 'UMUM',
                'tgl_transaksi' => date('Y-m-d H:i:s'),
                'tgl_lunas' => date('Y-m-d H:i:s'),
                'total_sub' => $subtotal,
                'total_diskon' => $discountTotal,
                'total_harga' => $netTotal,
                'total_rekanan' => 0,
                'total_pribadi' => $netTotal,
                'bayar_rekanan' => 0,
                'bayar_pribadi' => $totalPaid,
                'total_kembali' => $kembali,
                'aktif' => '1',
                'created_by' => $created_by,
                'created_date' => date('Y-m-d H:i:s'),
                'jenis_tr' => '1',
                'issplit' => count($payments) > 1 ? 'Y' : 'T'
            ];
            $this->add_if_field_exists($dataByr, 'pc01_keu_episode_byr', 'sudah_terima_dari', $dibayar_oleh);
            $this->db->insert($this->t('pc01_keu_episode_byr'), $dataByr);

            foreach ($payments as $p) {
                $amount = $this->num($p['amount'] ?? 0);
                if ($amount <= 0) continue;

                $method = $this->map_payment_method($p['method'] ?? 'TUNAI');
                $this->db->insert($this->t('pc01_keu_trnsbayar'), [
                    'lokasi_id' => $lokasi_id,
                    'episode_id' => $episode_id,
                    'bayar_id' => $bayar_id,
                    'pasien_id' => $pasien_id,
                    'jbayar_id' => $method['jbayar_id'],
                    'keterangan' => $method['keterangan'],
                    'pembayaran' => $amount,
                    'kartu_no' => $p['card_no'] ?? null,
                    'kartu_nama' => $p['card_name'] ?? null,
                    'no_konf' => $p['ref_no'] ?? null,
                    'aktif' => '1',
                    'created_by' => $created_by,
                    'created_date' => date('Y-m-d H:i:s'),
                    'trans_id' => $p['trans_id'] ?? null
                ]);
            }

            $this->db->trans_commit();
            return [
                'success' => true,
                'message' => 'Pembayaran berhasil diproses.',
                'bayar_id' => $bayar_id,
                'episode_id' => $episode_id,
                'total_harga' => $netTotal,
                'total_bayar' => $totalPaid,
                'total_kembali' => $kembali
            ];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function update_tindakan_item_total($lokasi_id, $episode_id, $pasien_id, $item, $created_by)
    {
        $data = [
            'disk_pct' => $item['_disk_pct_new'],
            'disk_tot' => $item['_disk_tot_new'],
            'harga_total' => $item['_harga_total_new'],
            'total_pribadi' => $item['_harga_total_new'],
            'total_rekanan' => 0
        ];
        $this->add_if_field_exists($data, 'pc01_keu_transctr_it', 'last_updated_by', $created_by);
        $this->add_if_field_exists($data, 'pc01_keu_transctr_it', 'last_updated_date', date('Y-m-d H:i:s'));

        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('trans_id', $item['trans_id'])
            ->where('layan_id', $item['ref_id'])
            ->update($this->t('pc01_keu_transctr_it'), $data);
    }

    private function update_obat_item_total($lokasi_id, $episode_id, $pasien_id, $item, $created_by)
    {
        $data = [
            'disk_pct' => $item['_disk_pct_new'],
            'disk_tot' => $item['_disk_tot_new'],
            'harga_total' => $item['_harga_total_new']
        ];
        $this->add_if_field_exists($data, 'pc01_keu_transfrm_it', 'total_pribadi', $item['_harga_total_new']);
        $this->add_if_field_exists($data, 'pc01_keu_transfrm_it', 'total_rekanan', 0);
        $this->add_if_field_exists($data, 'pc01_keu_transfrm_it', 'last_updated_by', $created_by);
        $this->add_if_field_exists($data, 'pc01_keu_transfrm_it', 'last_updated_date', date('Y-m-d H:i:s'));

        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('trans_id', $item['trans_id'])
            ->where('obat_id', $item['ref_id'])
            ->update($this->t('pc01_keu_transfrm_it'), $data);
    }



    private function mark_unpaid_headers_paid($lokasi_id, $episode_id, $pasien_id, $bayar_id, $created_by)
    {
        $data = [
            'bayar_id' => $bayar_id,
            'tgl_lunas' => date('Y-m-d H:i:s'),
            'status_tr' => '55',
            'total_rekanan' => 0,
            'bayar_rekanan' => 0,
            'last_updated_by' => $created_by,
            'last_updated_date' => date('Y-m-d H:i:s')
        ];

        $this->db
            ->where('lokasi_id', $lokasi_id)
            ->where('episode_id', $episode_id)
            ->where('pasien_id', $pasien_id)
            ->where('bayar_id IS NULL', null, false)
            ->where('tgl_lunas IS NULL', null, false)
            ->where("UPPER(TRIM(COALESCE(rekanan_id, ''))) = 'UMUM'", null, false)
            ->where("COALESCE(jenis_tr, '') IN ('001','003','004','005','006')", null, false)
            ->where('aktif', '1')
            ->update($this->t('pc01_keu_transaksi_hd'), $data);
    }

    public function get_kwitansi($lokasi_id, $bayar_id)
    {
        $sql = "
            SELECT
                b.*,
                p.int_pasien_id AS no_rm,
                p.nama AS nama_pasien,
                e.tgl_masuk,
                COALESCE(pm.keterangan, '-') AS poli,
                COALESCE(dm.nama, '-') AS dokter
            FROM {$this->t('pc01_keu_episode_byr')} b
            JOIN {$this->t('pc01_keu_episode')} e
                ON e.lokasi_id = b.lokasi_id
               AND e.episode_id = b.episode_id
               AND e.pasien_id = b.pasien_id
            JOIN {$this->t('pc01_gen_pasien_ms')} p
                ON p.lokasi_id = b.lokasi_id
               AND p.pasien_id = b.pasien_id
            LEFT JOIN {$this->t('pc01_med_poli_ms')} pm
                ON pm.lokasi_id = e.lokasi_id
               AND pm.poli_id = e.poli_id
            LEFT JOIN {$this->t('pc01_med_dokter_ms')} dm
                ON dm.lokasi_id = e.lokasi_id
               AND dm.dokter_id = e.dokter_id
            WHERE b.lokasi_id = ?
              AND b.bayar_id = ?
              AND COALESCE(b.aktif, '1') = '1'
            LIMIT 1
        ";

        $header = $this->db->query($sql, [$lokasi_id, $bayar_id])->row_array();
        if (empty($header)) {
            return null;
        }

        $header['items'] = $this->get_paid_items_by_bayar($lokasi_id, $bayar_id);
        $header['payments'] = $this->get_payment_rows($lokasi_id, $bayar_id);
        return $header;
    }

    private function get_paid_items_by_bayar($lokasi_id, $bayar_id)
    {
        $sqlTindakan = "
            SELECT
                CASE WHEN lm.kategori_id IN ('JKL-LAB','JKL-RAD','JKL-RA') THEN 'PENUNJANG' ELSE 'TINDAKAN' END AS kategori,
                lm.nama_layan1 AS nama_item,
                COALESCE(NULLIF(b.qty, 0), 1) AS qty,
                COALESCE(b.harga_satuan, 0) AS tarif,
                COALESCE(b.harga_total, 0) AS harga_total
            FROM {$this->t('pc01_keu_transaksi_hd')} a
            JOIN {$this->t('pc01_keu_transctr_it')} b
                ON a.lokasi_id  = b.lokasi_id
               AND a.episode_id = b.episode_id
               AND a.trans_id   = b.trans_id
               AND a.pasien_id  = b.pasien_id
            JOIN {$this->t('pc01_keu_layan_ms')} lm
                ON lm.lokasi_id = b.lokasi_id
               AND lm.layan_id = b.layan_id
            WHERE a.lokasi_id = ?
              AND a.bayar_id = ?
              AND COALESCE(a.aktif, '1') = '1'
              AND COALESCE(b.aktif, '1') = '1'
              AND COALESCE(b.layan_id, '') NOT IN ('ADM01','JASADR')
            ORDER BY kategori, nama_item
        ";

        $sqlObat = "
            SELECT
                'OBAT' AS kategori,
                COALESCE(ob.nama, c.obat_id) AS nama_item,
                COALESCE(NULLIF(c.qty, 0), 1) AS qty,
                COALESCE(c.harga_satuan, 0) AS tarif,
                COALESCE(c.harga_total, 0) AS harga_total
            FROM {$this->t('pc01_keu_transaksi_hd')} a
            JOIN {$this->t('pc01_keu_transfrm_it')} c
                ON a.lokasi_id  = c.lokasi_id
               AND a.episode_id = c.episode_id
               AND a.trans_id   = c.trans_id
               AND a.pasien_id  = c.pasien_id
            LEFT JOIN {$this->t('pc01_frm_obat_ms')} ob
                ON ob.lokasi_id = c.lokasi_id
               AND ob.obat_id = c.obat_id
            WHERE a.lokasi_id = ?
              AND a.bayar_id = ?
              AND COALESCE(a.aktif, '1') = '1'
              AND COALESCE(c.aktif, '1') = '1'
            ORDER BY nama_item
        ";

        return array_merge(
            $this->db->query($sqlTindakan, [$lokasi_id, $bayar_id])->result_array(),
            $this->db->query($sqlObat, [$lokasi_id, $bayar_id])->result_array()
        );
    }

    private function get_payment_rows($lokasi_id, $bayar_id)
    {
        $sql = "
            SELECT
                jbayar_id,
                COALESCE(keterangan, jbayar_id) AS metode_bayar,
                COALESCE(pembayaran, 0) AS pembayaran,
                COALESCE(no_konf, '') AS no_konf
            FROM {$this->t('pc01_keu_trnsbayar')}
            WHERE lokasi_id = ?
              AND bayar_id = ?
              AND COALESCE(aktif, '1') = '1'
            ORDER BY created_date ASC
        ";

        return $this->db->query($sql, [$lokasi_id, $bayar_id])->result_array();
    }
}
