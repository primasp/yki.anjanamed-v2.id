<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LabModel extends CI_Model
{
    // tabel inti
    private string $t_worklist = 'pcare_manager.pc01_worklist_lab';
    private string $t_dtl      = 'pcare_manager.pc01_co_lab_dt';
    private string $t_counter  = 'pcare_manager.lab_sampel_counter';


    /**
     * OPTIONAL JOIN MASTER
     * Default saya matikan supaya tidak error kalau tabel master Anda berbeda.
     * Kalau tabel master sudah pasti ada, set true + sesuaikan nama tabelnya.
     */

    private bool $enable_join_master = true;
    private string $t_pasien  = 'pcare_manager.pc01_gen_pasien_ms';
    private string $t_poli    = 'pcare_manager.pc01_med_poli_ms';
    private string $t_dokter  = 'pcare_manager.pc01_med_dokter_ms';
    private string $t_rekanan = 'pcare_manager.pc01_keu_rekanan_ms';


    public function countsxxx(): array
    {
        $sql = "SELECT status, count(*)::int as cnt
            from {$this->t_worklist}
            where aktif = '1'
            group by status
        ";
        $rows = $this->db->query($sql)->result_array();

        $out = ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '9' => 0];
        foreach ($rows as $r) {
            $out[(string)$r['status']] = (int)$r['cnt'];
        }
        return $out;
    }



    public function counts(): array
    {
        $latest = $this->latest_pap_result_sql();

        $sql = "
        SELECT
            w.status,
            COUNT(*)::int AS cnt
        FROM {$this->t_worklist} w
        LEFT JOIN ({$latest}) fr
            ON fr.episode_id = w.episode_id
        WHERE w.aktif = '1'
          AND (
                w.status <> '1'
                OR (
                    w.status = '1'
                    AND fr.status_admin = 'F'
                    AND fr.status_dokter = 'A'
                )
          )
        GROUP BY w.status
    ";

        $rows = $this->db->query($sql)->result_array();

        $out = ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '9' => 0];

        foreach ($rows as $r) {
            $out[(string)$r['status']] = (int)$r['cnt'];
        }

        return $out;
    }

    public function list_by_statusxxx(string $status, string $keyword = ''): array
    {
        $status  = (string)$status;
        $keyword = trim((string)$keyword);

        $params = [$status];
        $whereKeyword = "";

        // kalau join master dimatikan, keyword hanya cari di field worklist (aman)
        if ($keyword !== '') {
            $k = '%' . $keyword . '%';


            if ($this->enable_join_master) {

                $whereKeyword = " AND (
                    coalesce(p.nama,'') ilike ?
                    or coalesce(p.pasien_id::text,'') ilike ?
                    or coalesce(w.episode_id,'') ilike ?
                    or coalesce(w.trans_id,'') ilike ?
                    or coalesce(w.sampel_id,'') ilike ?
                ) ";
                array_push($params, $k, $k, $k, $k, $k);
            } else {
                $whereKeyword = " AND (
                    coalesce(w.episode_id,'') ilike ?
                    or coalesce(w.pasien_id,'') ilike ?
                    or coalesce(w.trans_id,'') ilike ?
                    or coalesce(w.trans_co,'') ilike ?
                    or coalesce(w.sampel_id,'') ilike ?
                ) ";
                array_push($params, $k, $k, $k, $k, $k);
            }
        }

        if ($this->enable_join_master) {
            $sql = "SELECT
                w.*,
                p.nama as nama_pasien,
                coalesce(p.int_pasien_id::text, p.pasien_id::text) as no_rm,
                po.keterangan as nama_poli,
                d.nama as nama_dokter,
                r.nama as rekanan_nama

              from {$this->t_worklist} w
              left join {$this->t_pasien}  p  on p.pasien_id = w.pasien_id
              left join {$this->t_poli}    po on po.poli_id = w.poli_id
              left join {$this->t_dokter}  d  on d.dokter_id = w.dokter_id
              left join {$this->t_rekanan} r  on r.rekanan_id = w.rekanan_id

              where w.aktif = '1'
                and w.status = ?
                {$whereKeyword}

              order by w.created_date desc nulls last, w.tanggal desc nulls last
              limit 300
            ";
        } else {
            // versi aman tanpa join (nama_pasien dll tetap ada tapi null)
            $sql = "SELECT
                w.*,
                null::text as nama_pasien,
                null::text as no_rm,
                null::text as nama_poli,
                null::text as nama_dokter,
                null::text as rekanan_nama
              from {$this->t_worklist} w
              where w.aktif = '1'
                and w.status = ?
                {$whereKeyword}
              order by w.created_date desc nulls last, w.tanggal desc nulls last
              limit 300
            ";
        }

        $rows = $this->db->query($sql, $params)->result_array();

        // pakai id virtual jika belum ada PK id
        // foreach ($rows as &$r) {
        //     if (!isset($r['id']) || $r['id'] === null || $r['id'] === '') {
        //         $r['id'] = $this->make_virtual_id($r);
        //     }
        // }

        return $rows;
    }


    public function list_by_status(string $status, string $keyword = ''): array
    {
        $status  = (string)$status;
        $keyword = trim((string)$keyword);

        $latest = $this->latest_pap_result_sql();

        $params = [$status];
        $whereKeyword = '';

        if ($keyword !== '') {
            $k = '%' . $keyword . '%';

            $whereKeyword = "
            AND (
                COALESCE(p.nama,'') ILIKE ?
                OR COALESCE(p.pasien_id::text,'') ILIKE ?
                OR COALESCE(p.int_pasien_id::text,'') ILIKE ?
                OR COALESCE(w.episode_id,'') ILIKE ?
                OR COALESCE(w.trans_id,'') ILIKE ?
                OR COALESCE(w.sampel_id,'') ILIKE ?
                OR COALESCE(w.no_sitologi,'') ILIKE ?
            )
        ";

            array_push($params, $k, $k, $k, $k, $k, $k, $k);
        }

        $whereFinal = '';
        if ($status === '1') {
            $whereFinal = "
            AND fr.status_admin = 'F'
            AND fr.status_dokter = 'A'
        ";
        }

        $sql = "
        SELECT
            w.*,

            -- id virtual untuk kebutuhan JS saja, bukan kolom database
            (
                COALESCE(w.episode_id, '') || '|' ||
                COALESCE(w.pasien_id, '')  || '|' ||
                COALESCE(w.trans_id, '')   || '|' ||
                COALESCE(w.trans_co, '')
            ) AS id,

            p.nama AS nama_pasien,
            COALESCE(p.int_pasien_id::text, p.pasien_id::text) AS no_rm,
            po.keterangan AS nama_poli,
            d.nama AS nama_dokter,
            r.nama AS rekanan_nama,

            fr.result_id,
            fr.status_admin,
            fr.status_dokter,
            fr.dokter_user_id,
            fr.dokter_acc_date

        FROM {$this->t_worklist} w
        LEFT JOIN ({$latest}) fr
            ON fr.episode_id = w.episode_id
        LEFT JOIN {$this->t_pasien}  p  ON p.pasien_id = w.pasien_id
        LEFT JOIN {$this->t_poli}    po ON po.poli_id = w.poli_id
        LEFT JOIN {$this->t_dokter}  d  ON d.dokter_id = w.dokter_id
        LEFT JOIN {$this->t_rekanan} r  ON r.rekanan_id = w.rekanan_id

        WHERE w.aktif = '1'
          AND w.status = ?
          {$whereFinal}
          {$whereKeyword}

        ORDER BY w.created_date DESC NULLS LAST, w.tanggal DESC NULLS LAST
        LIMIT 300
    ";

        return $this->db->query($sql, $params)->result_array();
    }


    private function parse_id($id): array
    {
        $id = trim((string)$id);

        // format normal dari JS:
        // episode_id|pasien_id|trans_id|trans_co
        if (strpos($id, '|') !== false) {
            $parts = explode('|', $id);

            return [
                'type'       => 'composite',
                'episode_id' => $parts[0] ?? null,
                'pasien_id'  => $parts[1] ?? null,
                'trans_id'   => $parts[2] ?? null,
                'trans_co'   => $parts[3] ?? null,
            ];
        }

        // fallback kalau hanya kirim episode_id
        return [
            'type'       => 'episode',
            'episode_id' => $id,
            'pasien_id'  => null,
            'trans_id'   => null,
            'trans_co'   => null,
        ];
    }

    private function make_virtual_id(array $row): string
    {
        return ($row['episode_id'] ?? '') . '|' . ($row['pasien_id'] ?? '') . '|' . ($row['trans_id'] ?? '') . '|' . ($row['trans_co'] ?? '');
    }




    private string $pap_form_code = 'PAP_ANATOMIK';

    private function latest_pap_result_sql(): string
    {
        return "
        SELECT DISTINCT ON (episode_id)
            result_id,
            worklist_id,
            form_code,
            episode_id,
            pasien_id,
            sampel_id,
            status_admin,
            status_dokter,
            admin_user_id,
            dokter_user_id,
            dokter_acc_date,
            created_date,
            last_updated_date
        FROM pcare_manager.pc01_lab_form_result
        WHERE form_code = 'PAP_ANATOMIK'
          AND episode_id IS NOT NULL
        ORDER BY episode_id, result_id DESC
    ";
    }

    private function update_worklist_status_from_rowxxxxx(array $w, string $status, string $username, string $now, bool $selesai = false): void
    {
        $data = [
            'status'            => $status,
            'last_updated_by'   => $username,
            'last_updated_date' => $now,
        ];

        if ($selesai) {
            $data['tgl_selesai']  = date('Y-m-d', strtotime($now));
            $data['user_selesai'] = $username;
        }

        if (!empty($w['id']) && is_numeric($w['id'])) {
            $this->db
                ->where('id', (int)$w['id'])
                ->update($this->t_worklist, $data);
            return;
        }

        $this->db
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) {
            $this->db->where('trans_co', $w['trans_co']);
        }

        $this->db->update($this->t_worklist, $data);
    }


    private function update_worklist_status_from_row(array $w, string $status, string $username, string $now, bool $selesai = false): void
    {
        $data = [
            'status'            => $status,
            'last_updated_by'   => $username,
            'last_updated_date' => $now,
        ];

        if ($selesai) {
            $data['tgl_selesai']  = date('Y-m-d', strtotime($now));
            $data['user_selesai'] = $username;
        }

        $this->db
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) {
            $this->db->where('trans_co', $w['trans_co']);
        }

        $this->db->update($this->t_worklist, $data);
    }



    private function is_pap_final_dokter(?array $r): bool
    {
        return $r
            && strtoupper((string)($r['status_admin'] ?? '')) === 'F'
            && strtoupper((string)($r['status_dokter'] ?? '')) === 'A';
    }









    public function get_one($id): ?array
    {

        // return var_dump($id);
        // die;

        $key = $this->parse_id($id);

        // return var_dump($key);
        // die;

        // $select = "w.*, p.nama as nama_pasien, coalesce(p.int_pasien_id::text, p.pasien_id::text) as no_rm";


        // $select .= ", po.keterangan as nama_poli, d.nama as nama_dokter, r.nama as rekanan_nama";

        $select = "w.*,
            TO_CHAR(w.tgl_no_sitologi, 'DD-MM-YYYY HH24:MI:SS') as tgl_sitologi,
            TO_CHAR(fr.created_date, 'DD-MM-YYYY HH24:MI:SS') as tgl_skrinning_admin,
            TO_CHAR(fr.dokter_acc_date, 'DD-MM-YYYY HH24:MI:SS') as tgl_skrinning_dokter,
            fr.admin_user_id,
            fr.dokter_user_id,
            fr.status_admin,
            fr.status_dokter,
            p.nama as nama_pasien,
            p.nama_pasangan,
            p.alamat1,
            TO_CHAR(p.tgl_lahir, 'DD-MM-YYYY') as tgl_lahir,
            hitung_umur(p.tgl_lahir, current_date) AS umur,
            coalesce(p.int_pasien_id::text, p.pasien_id::text) as no_rm,
            po.keterangan as nama_poli,
            d.nama as nama_dokter,
            r.nama as rekanan_nama";

        $this->db->select($select, false);
        $this->db->from($this->t_worklist . ' w');

        // =========================
        // RESULT FORM TERAKHIR
        // =========================
        $this->db->join(
            "
                (
                    SELECT DISTINCT ON (episode_id)
                        episode_id,
                        created_date,
                        dokter_acc_date,
                        admin_user_id,
                        dokter_user_id,
                        status_admin,
                        status_dokter,
                        result_id
                    FROM pcare_manager.pc01_lab_form_result
                    WHERE form_code = 'PAP_ANATOMIK'
                    ORDER BY episode_id, result_id DESC
                ) fr
            ",
            "fr.episode_id = w.episode_id",
            'left',
            false
        );

        // =========================
        // JOIN MASTER
        // =========================
        $this->db->join($this->t_pasien . ' p', 'p.pasien_id = w.pasien_id', 'left');
        $this->db->join($this->t_poli . ' po', 'po.poli_id = w.poli_id', 'left');
        $this->db->join($this->t_dokter . ' d', 'd.dokter_id = w.dokter_id', 'left');
        $this->db->join($this->t_rekanan . ' r', 'r.rekanan_id = w.rekanan_id', 'left');

        $this->db->where('w.aktif', '1');


        // if ($key['type'] === 'pk') {
        //     $this->db->where('w.id', (int)$key['id']);
        // } else {
        //     $this->db->where('w.episode_id', $key['episode_id']);
        //     $this->db->where('w.pasien_id',  $key['pasien_id']);
        //     $this->db->where('w.trans_id',   $key['trans_id']);
        //     if (!empty($key['trans_co'])) {
        //         $this->db->where('w.trans_co', $key['trans_co']);
        //     }
        // }


        if ($key['type'] === 'composite') {
            $this->db->where('w.episode_id', $key['episode_id']);
            $this->db->where('w.pasien_id',  $key['pasien_id']);
            $this->db->where('w.trans_id',   $key['trans_id']);

            if (!empty($key['trans_co'])) {
                $this->db->where('w.trans_co', $key['trans_co']);
            }
        } else {
            $this->db->where('w.episode_id', $key['episode_id']);
        }



        // $row = $this->db->get()->row_array();
        $query = $this->db->get();

        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;

        $row = $query->row_array();





        if ($row && (!isset($row['id']) || $row['id'] === null || $row['id'] === '')) {
            $row['id'] = $this->make_virtual_id($row);
        }
        return $row ?: null;
    }


    public function get_tests_for_worklist(array $w): array
    {
        $t_layan = 'pcare_manager.pc01_keu_layan_ms';


        $sql = "SELECT
                        a.test_id,
                        a.test_lab_id,
                        a.cito,
                        a.sampel_id,
                        a.dikerjakan,
                        l.nama_layan1 as nama_pemeriksaan
                    from {$this->t_dtl} a
                    left join {$t_layan} l
                        on l.layan_id = a.test_id
                        and l.kategori_id = 'JKL-LAB'
                    where a.episode_id = ?
                    and a.pasien_id  = ?
                    and a.trans_id   = ?
                    and a.trans_co   = ?
                    order by a.test_id
                ";

        return $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co']
        ])->result_array();
    }

    public function kerjakanxxxx($id, string $username, string $no_sitologi): array
    {
        $this->db->trans_begin();

        // lock row worklist agar tidak double-kerjakan
        $key = $this->parse_id($id);

        if ($key['type'] === 'pk') {
            $w = $this->db->query("SELECT * from {$this->t_worklist}
              where id = ? and aktif = '1'
              for update
            ", [$key['id']])->row_array();
        } else {
            $params = [$key['episode_id'], $key['pasien_id'], $key['trans_id']];
            $sql = "SELECT * from {$this->t_worklist}
              where aktif='1'
                and episode_id = ?
                and pasien_id  = ?
                and trans_id   = ?
            ";
            if (!empty($key['trans_co'])) {
                $sql .= " and trans_co = ? ";
                $params[] = $key['trans_co'];
            }
            $sql .= " for update";
            $w = $this->db->query($sql, $params)->row_array();
        }

        if (!$w) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Worklist tidak ditemukan.'];
        }

        // if ((string)$w['status'] !== '0') {
        //     $this->db->trans_rollback();
        //     return ['success' => false, 'message' => 'Status bukan BARU (0). Tidak bisa Kerjakan.'];
        // }


        // ============================
        // VALIDASI STATUS
        // ============================
        if (!in_array((string)$w['status'], ['0', '2'], true)) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Status tidak bisa dikerjakan.'];
        }

        // ============================
        // VALIDASI NO SITOLOGI
        // ============================

        $no_sitologi = trim($no_sitologi);
        if ($no_sitologi === '') {

            $this->db->trans_rollback();

            return [
                'success' => false,
                'message' => 'No Sitologi wajib diisi.'
            ];
        }

        // optional: cek duplikasi
        $cek = $this->db
            ->where('no_sitologi', $no_sitologi)
            ->where('aktif', '1')
            ->count_all_results($this->t_worklist);

        if ($cek > 0) {

            $this->db->trans_rollback();

            return [
                'success' => false,
                'message' => 'No Sitologi sudah digunakan.'
            ];
        }



        // ============================
        // GENERATE SAMPLE ID
        // ============================
        $sampel_id = $this->generate_sampel_id('LAB');

        // return var_dump($sampel_id);
        // die;

        $upd = [
            'status'            => '4',
            'sampel_id'         => $sampel_id,
            'no_sitologi'       => $no_sitologi, // 🔥 TAMBAHAN
            'tgl_barcode'       => date('Y-m-d'),

            // rekam pertama kali dibuat
            'tgl_no_sitologi'   => date('Y-m-d H:i:s'),
            'user_no_sitologi'  => $username,


            'last_updated_by'   => $username,
            'last_updated_date' => date('Y-m-d H:i:s'),
        ];

        // update worklist by PK or composite
        if (isset($w['id']) && $w['id'] !== null && $w['id'] !== '' && ctype_digit((string)$w['id'])) {
            $this->db->where('id', (int)$w['id'])->update($this->t_worklist, $upd);
        } else {
            $this->db->where('episode_id', $w['episode_id'])
                ->where('pasien_id',  $w['pasien_id'])
                ->where('trans_id',   $w['trans_id']);
            if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);
            $this->db->update($this->t_worklist, $upd);
        }

        // update dtl: set sampel_id untuk semua test terkait
        $this->db->set('sampel_id', $sampel_id)
            ->set('last_updated_by', $username)
            ->set('last_updated_date', date('Y-m-d H:i:s'))
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);
        if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);
        $this->db->update($this->t_dtl);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Gagal kerjakan (DB error).'];
        }

        $this->db->trans_commit();

        return [
            'success'     => true,
            'sampel_id'   => $sampel_id,
            'no_sitologi' => $no_sitologi, // optional return
            'tgl_no_sitologi' => date('Y-m-d H:i:s')
        ];


        // return ['success' => true, 'sampel_id' => $sampel_id];
    }


    public function kerjakan($id, string $username, string $no_sitologi): array
    {
        $this->db->trans_begin();

        $key = $this->parse_id($id);

        // ============================
        // AMBIL WORKLIST TANPA w.id
        // ============================
        if ($key['type'] === 'composite') {
            $params = [$key['episode_id'], $key['pasien_id'], $key['trans_id']];

            $sql = "SELECT *
                FROM {$this->t_worklist}
                WHERE aktif = '1'
                  AND episode_id = ?
                  AND pasien_id  = ?
                  AND trans_id   = ?";

            if (!empty($key['trans_co'])) {
                $sql .= " AND trans_co = ?";
                $params[] = $key['trans_co'];
            }

            $sql .= " FOR UPDATE";

            $w = $this->db->query($sql, $params)->row_array();
        } else {
            $w = $this->db->query("
            SELECT *
            FROM {$this->t_worklist}
            WHERE aktif = '1'
              AND episode_id = ?
            ORDER BY created_date DESC NULLS LAST
            LIMIT 1
            FOR UPDATE
        ", [$key['episode_id']])->row_array();
        }

        if (!$w) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Worklist tidak ditemukan.'];
        }

        if (!in_array((string)$w['status'], ['0', '2'], true)) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Status tidak bisa dikerjakan.'];
        }

        $no_sitologi = trim($no_sitologi);

        if ($no_sitologi === '') {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'No Sitologi wajib diisi.'];
        }

        $cek = $this->db
            ->where('no_sitologi', $no_sitologi)
            ->where('aktif', '1')
            ->count_all_results($this->t_worklist);

        if ($cek > 0) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'No Sitologi sudah digunakan.'];
        }

        $now = date('Y-m-d H:i:s');
        $sampel_id = $this->generate_sampel_id('LAB');

        $upd = [
            'status'            => '4',
            'sampel_id'         => $sampel_id,
            'no_sitologi'       => $no_sitologi,
            'tgl_barcode'       => date('Y-m-d'),
            'tgl_no_sitologi'   => $now,
            'user_no_sitologi'  => $username,
            'last_updated_by'   => $username,
            'last_updated_date' => $now,
        ];

        // update worklist tanpa id
        $this->db
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) {
            $this->db->where('trans_co', $w['trans_co']);
        }

        $this->db->update($this->t_worklist, $upd);

        // update detail lab
        $this->db
            ->set('sampel_id', $sampel_id)
            ->set('last_updated_by', $username)
            ->set('last_updated_date', $now)
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) {
            $this->db->where('trans_co', $w['trans_co']);
        }

        $this->db->update($this->t_dtl);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Gagal kerjakan (DB error).'];
        }

        $this->db->trans_commit();

        return [
            'success'          => true,
            'sampel_id'        => $sampel_id,
            'no_sitologi'      => $no_sitologi,
            'tgl_no_sitologi'  => $now,
        ];
    }

    private function generate_sampel_id(string $prefix = 'LAB'): string
    {
        // pastikan counter hari ini ada
        $this->db->query("INSERT into {$this->t_counter} (tgl, last_no)
          values (current_date, 0)
          on conflict (tgl) do nothing
        ");

        // increment atomic
        $row = $this->db->query("UPDATE {$this->t_counter}
          set last_no = last_no + 1
          where tgl = current_date
          returning last_no
        ")->row_array();

        $no  = (int)($row['last_no'] ?? 0);
        $tgl = date('Ymd');
        return sprintf('%s-%s-%04d', $prefix, $tgl, $no);
    }



    public function update_statusxxxx($id, string $status, string $username): array
    {
        $w = $this->get_one($id);
        if (!$w) return ['success' => false, 'message' => 'Worklist tidak ditemukan.'];



        // otomatis isi field terkait status
        if ($status === '2' || $status === '3') { // tunda / batal
            if (isset($w['id']) && $w['id'] !== null && ctype_digit((string)$w['id'])) {
                $this->db->where('id', (int)$w['id'])
                    ->update($this->t_worklist, ['status' => $status]);
            } else {
                $this->db->where('episode_id', $w['episode_id'])
                    ->where('pasien_id',  $w['pasien_id'])
                    ->where('trans_id',   $w['trans_id']);
                if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);

                $this->db->update($this->t_worklist, ['status' => $status]);
            }

            return ['success' => true];
        }

        // if (isset($w['id']) && $w['id'] !== null && ctype_digit((string)$w['id'])) {
        //     $this->db->where('id', (int)$w['id'])->update($this->t_worklist, $data);
        // } else {
        //     $this->db->where('episode_id', $w['episode_id'])
        //         ->where('pasien_id',  $w['pasien_id'])
        //         ->where('trans_id',   $w['trans_id']);
        //     if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);
        //     $this->db->update($this->t_worklist, $data);
        // }

        // =========================
        // DEFAULT: status lain
        // =========================
        $data = [
            'status'            => $status,
            'last_updated_by'   => $username,
            'last_updated_date' => date('Y-m-d H:i:s'),
        ];

        if ($status === '1') { // selesai
            $data['tgl_selesai']  = date('Y-m-d');
            $data['user_selesai'] = $username;
        }

        if (isset($w['id']) && $w['id'] !== null && ctype_digit((string)$w['id'])) {
            $this->db->where('id', (int)$w['id'])->update($this->t_worklist, $data);
        } else {
            $this->db->where('episode_id', $w['episode_id'])
                ->where('pasien_id',  $w['pasien_id'])
                ->where('trans_id',   $w['trans_id']);
            if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);
            $this->db->update($this->t_worklist, $data);
        }

        return ['success' => true];
    }


    public function update_status($id, string $status, string $username): array
    {
        $w = $this->get_one($id);

        if (!$w) {
            return ['success' => false, 'message' => 'Worklist tidak ditemukan.'];
        }

        // Status selesai tidak boleh manual.
        // Selesai hanya dari ACC dokter.
        if ($status === '1') {
            return [
                'success' => false,
                'message' => 'Status selesai hanya bisa dari ACC dokter.',
            ];
        }

        $now = date('Y-m-d H:i:s');

        $data = [
            'status'            => $status,
            'last_updated_by'   => $username,
            'last_updated_date' => $now,
        ];

        $this->db
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) {
            $this->db->where('trans_co', $w['trans_co']);
        }

        $this->db->update($this->t_worklist, $data);

        return ['success' => true];
    }

    public function get_form_with_values($form_code, array $w)
    // public function get_form_with_values($worklist_id, $form_code = 'PAP_LAB')
    {
        if (!$w) {
            return null;
        }

        // -----------------------------
        // 1. IDENTITAS WORKLIST
        // -----------------------------
        $worklist_id = $w['id']        ?? null;   // kalau ada kolom id
        $episode_id  = $w['episode_id'] ?? null;
        $pasien_id   = $w['pasien_id']  ?? null;
        $sampel_id   = $w['sampel_id']  ?? null;


        // -----------------------------
        // 2. HEADER HASIL (TERBARU)
        // -----------------------------
        // Silakan SESUAIKAN nama tabel & kolom kalau beda
        $this->db->from('pcare_manager.pc01_lab_form_result');

        // if ($worklist_id) {
        //     $this->db->where('worklist_id', $worklist_id);
        // }
        if ($episode_id) {
            $this->db->where('episode_id', $episode_id);
        }
        if ($pasien_id) {
            $this->db->where('pasien_id', $pasien_id);
        }
        if ($sampel_id) {
            $this->db->where('sampel_id', $sampel_id);
        }

        $this->db->where('form_code', $form_code);
        $this->db->order_by('result_id', 'DESC');
        $this->db->limit(1);

        $result_hdr = $this->db->get()->row_array();
        $result_id  = $result_hdr['result_id'] ?? null;

        // -----------------------------
        // 3. DETAIL NILAI FIELD
        // -----------------------------
        $values = [];
        if ($result_id) {
            // return var_dump("ADA xxadssdsd");
            // die;
            $detail_rows = $this->db
                ->from('pcare_manager.pc01_lab_form_result_value')
                ->where('result_id', $result_id)
                ->get()
                ->result_array();

            foreach ($detail_rows as $row) {
                $code = $row['field_code'];
                // $values[$code] = $row['value_text'];

                // kolom di tabel: value_text (BUKAN field_value)
                $val  = $row['value_text'];

                // kalau suatu saat ada multi-row per field_code, bisa dijadikan array
                if (isset($values[$code])) {
                    if (!is_array($values[$code])) {
                        $values[$code] = [$values[$code]];
                    }
                    $values[$code][] = $val;
                } else {
                    $values[$code] = $val;
                }
            }
        }


        // -----------------------------
        // 4. MASTER STRUKTUR FORM (GLOBAL MS)
        // -----------------------------

        // Sesuaikan schema & nama tabel master di sini


        $sections_raw = $this->db
            ->from('pc01_lab_form_section')
            ->where('form_code', $form_code)
            ->where('aktif', '1')
            ->order_by('sort_order', 'ASC')
            ->get()
            ->result_array();

        $sections = [];

        foreach ($sections_raw as $sec) {
            $section_code  = $sec['section_code'];
            $section_label = $sec['section_label'];

            // $fields_raw = $this->db
            //     ->from('pc01_lab_form_field')
            //     ->where('form_code', $form_code)
            //     ->where('section_code', $section_code)
            //     ->where('aktif', '1')
            //     ->order_by('urut', 'ASC')
            //     ->get()
            //     ->result_array();
            // return var_dump($section_code);
            // die;

            $fields_raw = $this->db
                ->select('a.*, b.section_code, b.section_label')
                ->from('pc01_lab_form_field a')
                ->join(
                    'pc01_lab_form_section b',
                    'a.section_id = b.section_id',
                    'inner'
                )
                ->where('a.form_code', $form_code)
                ->where('b.section_code', $section_code)
                ->where('a.aktif', '1')
                ->order_by('a.sort_order', 'ASC')
                ->get()
                ->result_array();

            $field_list = [];

            foreach ($fields_raw as $f) {
                // $options = [];

                // if (!empty($f['options_json'])) {
                //     $decoded = json_decode($f['options_json'], true);
                //     if (is_array($decoded)) {
                //         $options = $decoded;
                //     }
                // }


                $options = [];

                if (!empty($f['options_json'])) {

                    $decoded = json_decode($f['options_json'], true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $options = $decoded;
                    }
                }


                $field_code = $f['field_code'];
                $field_list[] = [
                    'field_code'    => $field_code,
                    'field_label'   => $f['field_label'],
                    'field_type'    => strtolower($f['field_type'] ?? 'text'),
                    'options'       => $options,
                    'default_value' => $values[$field_code] ?? $f['default_value'] ?? null,
                ];




                // $field_list[] = [
                //     'field_code'    => $f['field_code'],
                //     'field_label'   => $f['field_label'],
                //     'field_type'    => strtolower($f['field_type'] ?? 'text'),
                //     'options'       => $options,
                //     'default_value' => $f['default_value'] ?? null,
                // ];
            }


            $sections[] = [
                'section_code'  => $section_code,
                'section_label' => $section_label,
                'fields'        => $field_list,
            ];
        }

        // return var_dump($result_hdr);
        // die;

        return [
            'worklist' => $w,
            'sections' => $sections,
            'values'   => $values,
            'result'   => $result_hdr,
        ];
    }

    /**
     * Simpan hasil form PAP_ANATOMIK (header + detail values).
     *
     * @param array  $worklist   Row pc01_worklist_lab (hasil get_one)
     * @param array  $values     Array [field_code => value] dari POST (fields[...])
     * @param string $actor      'ADMIN' atau 'DOKTER'
     * @param bool   $finalize   true jika sekaligus final/ACC (ADMIN finalize ATAU DOKTER ACC)
     * @param string|null $userId user id yg sedang login (user_id_pc)
     *
     * @return array ['success' => bool, 'message' => string, 'result_id' => int|null]
     */

    public function save_form_result_pap($formCode, $worklistId, $values, $mode = 'draft', $username = 'system')
    {
        $formCode   = trim((string)$formCode);
        // $worklistId = $worklistId;
        $rawKey   = (string)$worklistId; // apa pun yang dikirim dari frontend
        $mode       = strtolower((string)$mode) === 'submit' ? 'submit' : 'draft';
        $username   = $username ?: 'system';

        // return var_dump($rawKey);
        // die;

        if (!$formCode || !$rawKey) {
            return [
                'success'   => false,
                'message'   => 'Form code atau worklist_id kosong',
                'result_id' => null,
            ];
        }

        // --- Normalisasi values: buat assoc [field_code => value] ---
        $assocValues = [];

        if (is_array($values) && !isset($values[0]['name'])) {
            $assocValues = $values;
        } else {
            // antisipasi kalau bentuknya serializeArray: [ ['name'=>'fields[kode]','value'=>'x'], ... ]
            foreach ((array)$values as $row) {
                if (!isset($row['name'])) continue;
                $name = $row['name'];
                $val  = isset($row['value']) ? $row['value'] : null;

                // ambil bagian dalam [] kalau ada fields[kode]
                if (preg_match('/\[(.+?)\]/', $name, $m)) {
                    $code = $m[1];
                } else {
                    $code = $name;
                }

                if (isset($assocValues[$code])) {
                    // kalau multi-value (checkbox group), gabung
                    if (!is_array($assocValues[$code])) {
                        $assocValues[$code] = [$assocValues[$code]];
                    }
                    $assocValues[$code][] = $val;
                } else {
                    $assocValues[$code] = $val;
                }
            }
        }

        // --- Ambil worklist utk episode, pasien, sampel ---
        $w = $this->get_one($rawKey);

        // return var_dump($w);
        // die;

        if (!$w) {
            return [
                'success'   => false,
                'message'   => 'Worklist tidak ditemukan',
                'result_id' => null,
            ];
        }


        $episodeId = $w['episode_id'] ?? null;
        $pasienId  = $w['pasien_id']  ?? null;
        $sampelId  = $w['sampel_id']  ?? null;


        // ==========================
        // 3. Tentukan PK integer worklist (worklist_id di tabel hasil)
        // ==========================
        // Ideal: kolom id ada di $w
        // if (!empty($w['id']) && is_numeric($w['id'])) {
        //     $worklistPk = $w['id'];
        // } else {
        //     // fallback: ambil bagian pertama sebelum '|'
        //     $parts = explode('|', $rawKey);
        //     $first = $parts[0] ?? null;
        //     if ($first !== null && is_numeric($first)) {
        //         $worklistPk = $first;
        //     } else {
        //         return [
        //             'success'   => false,
        //             'message'   => 'worklist_id tidak valid (tidak bisa diubah ke integer)',
        //             'result_id' => null,
        //         ];
        //     }
        // }

        $worklistPk = (string)$episodeId;

        // ==========================
        // 4. Master field
        // ==========================
        $fields = $this->db
            ->select('field_id, field_code, field_type')
            ->from('pcare_manager.pc01_lab_form_field')
            ->where('form_code', $formCode)
            ->where('aktif', '1')
            ->order_by('field_id')
            ->get()
            ->result_array();

        if (empty($fields)) {
            return [
                'success'   => false,
                'message'   => 'Master field form belum diset untuk ' . $formCode,
                'result_id' => null,
            ];
        }


        $fieldMap = [];
        foreach ($fields as $f) {
            $fieldMap[$f['field_code']] = [
                'field_id'   => $f['field_id'],
                // normalisasi ke lowercase supaya aman
                'field_type' => strtolower($f['field_type'] ?? 'text'),
            ];
        }

        $this->db->trans_start();
        $now = date('Y-m-d H:i:s');

        // ==========================
        // 5. HEADER: pc01_lab_form_result
        // ==========================
        // $hdr = $this->db
        //     ->from('pcare_manager.pc01_lab_form_result')
        //     ->where('form_code', $formCode)
        //     ->where('worklist_id', $worklistPk)   // <-- sudah integer aman
        //     ->get()
        //     ->row_array();

        $hdr = $this->db
            ->from('pcare_manager.pc01_lab_form_result')
            ->where('form_code', $formCode)
            ->where('episode_id', $episodeId)
            ->get()
            ->row_array();

        $statusAdmin = ($mode === 'submit') ? 'F' : 'D';

        if ($hdr) {

            $resultId = (int)$hdr['result_id'];

            $this->db
                ->where('result_id', $resultId)
                ->update('pcare_manager.pc01_lab_form_result', [
                    'episode_id'        => $episodeId,
                    'pasien_id'         => $pasienId,
                    'sampel_id'         => $sampelId,
                    'status_admin'      => $statusAdmin,
                    'admin_user_id'     => $username,
                    'last_updated_by'   => $username,
                    'last_updated_date' => $now,
                ]);
        } else {
            // $insertHdr = [
            //     'form_code'         => $formCode,
            //     'episode_id'        => $episodeId,
            //     'pasien_id'         => $pasienId,
            //     'sampel_id'         => $sampelId,
            //     'status_admin'      => $statusAdmin,
            //     'status_dokter'     => 'D',
            //     'admin_user_id'     => $username,
            //     'created_by'        => $username,
            //     'created_date'      => $now,
            //     'last_updated_by'   => $username,
            //     'last_updated_date' => $now,
            // ];
            $insertHdr = [
                'form_code'         => $formCode,
                'worklist_id'       => $worklistPk, // isinya episode_id
                'episode_id'        => $episodeId,
                'pasien_id'         => $pasienId,
                'sampel_id'         => $sampelId,
                'status_admin'      => $statusAdmin,
                'status_dokter'     => 'D',
                'admin_user_id'     => $username,
                'created_by'        => $username,
                'created_date'      => $now,
                'last_updated_by'   => $username,
                'last_updated_date' => $now,
            ];

            if ($worklistPk !== null) {
                $insertHdr['worklist_id'] = $worklistPk; // kalau kolomnya int/bigint
            }

            $this->db->insert('pcare_manager.pc01_lab_form_result', $insertHdr);
            $resultId = (int)$this->db->insert_id();
        }

        if (!$resultId) {
            $this->db->trans_complete();
            return [
                'success'   => false,
                'message'   => 'Gagal menyimpan header hasil PAP',
                'result_id' => null,
            ];
        }

        // return var_dump("11111aaa");
        // die;

        // ==========================
        // 6. DETAIL: value per field
        // ==========================
        $this->db
            ->where('result_id', $resultId)
            ->delete('pcare_manager.pc01_lab_form_result_value');


        $batch = [];
        foreach ($fieldMap as $code => $meta) {
            $type = $meta['field_type'];

            $raw = array_key_exists($code, $assocValues) ? $assocValues[$code] : null;
            $val  = null;

            // if ($type === 'checkbox') {

            //     $checked = false;

            //     if (is_array($raw)) {
            //         $checked = count($raw) > 0;
            //     } else {

            //         $v = strtoupper(trim((string)$raw));
            //         $checked = ($v !== '' && $v !== '0' && $v !== 'T' && $v !== 'FALSE');
            //     }

            //     $val = $checked ? 'Y' : 'N';
            // }


            if ($type === 'checkbox') {

                // checkbox multi option
                if (is_array($raw)) {

                    $raw = array_filter($raw);

                    if (empty($raw)) {
                        $val = 'N';
                    } else {
                        $val = implode(',', $raw);
                    }
                }
                // checkbox boolean
                else {

                    $v = strtoupper(trim((string)$raw));

                    $checked = ($v !== '' && $v !== '0' && $v !== 'T' && $v !== 'FALSE');

                    $val = $checked ? 'Y' : 'N';
                }
            } else {
                // field lain
                if (is_array($raw)) {
                    $val = implode(',', $raw);
                } else {
                    $val = ($raw === null || $raw === '') ? null : (string)$raw;
                }

                // kalau kosong total, skip insert supaya tabel tidak penuh null
                if ($val === null || $val === '') {
                    continue;
                }
            }



            // if ($meta['field_type'] === 'checkbox') {
            //     $val = $raw ? 'Y' : 'N';
            // } else {
            //     if (is_array($raw)) {
            //         $val = implode(',', $raw);
            //     } else {
            //         $val = ($raw === null || $raw === '') ? null : (string)$raw;
            //     }
            //     if ($val === null || $val === '') continue;
            // }

            $batch[] = [
                'result_id'        => $resultId,
                'field_id'         => $meta['field_id'],
                'field_code'       => $code,
                'value_text'       => $val,
                'created_date'     => $now,
                'last_updated_date' => $now,
            ];
        }


        // return var_dump($worklistPk);
        // die;


        if (!empty($batch)) {
            $this->db->insert_batch('pcare_manager.pc01_lab_form_result_value', $batch);
        }

        // ==========================
        // 7. Kalau SUBMIT: status worklist = 9 (Menunggu Dokter)
        // ==========================
        // if ($mode === 'submit') {
        //     $this->db
        //         ->where('episode_id', $worklistPk)
        //         ->update('pcare_manager.pc01_worklist_lab', [
        //             'status'           => '9',
        //             'last_updated_by'  => $username,
        //             'last_updated_date' => $now,
        //         ]);
        // }

        // if ($mode === 'submit') {
        //     // Admin final -> masuk antrean dokter
        //     $this->update_worklist_status_from_row($w, '9', $username, $now, false);
        // }

        if ($mode === 'submit') {
            $this->db
                ->where('episode_id', $episodeId)
                ->where('pasien_id', $pasienId)
                ->update('pcare_manager.pc01_worklist_lab', [
                    'status'            => '9',
                    'last_updated_by'   => $username,
                    'last_updated_date' => $now,
                ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return [
                'success'   => false,
                'message'   => 'Transaksi gagal (rollback).',
                'result_id' => null,
            ];
        }

        return [
            'success'   => true,
            'message'   => ($mode === 'submit')
                ? 'Form PAP berhasil disimpan & dikirim ke dokter.'
                : 'Draft form PAP berhasil disimpan.',
            'result_id' => $resultId,
        ];
    }


    public function save_form_result_pap_dokter($formCode, $worklistId, $values, $mode = 'draft', $username = 'system')
    {
        $formCode = trim((string)$formCode);
        $rawKey   = (string)$worklistId;
        $mode     = strtolower((string)$mode) === 'submit' ? 'submit' : 'draft';
        $username = $username ?: 'system';



        if (!$formCode || !$rawKey) {
            return [
                'success'   => false,
                'message'   => 'Form code atau worklist_id kosong',
                'result_id' => null,
            ];
        }

        // 1. Normalisasi values (sama seperti admin)
        $assocValues = [];


        if (is_array($values) && !isset($values[0]['name'])) {
            $assocValues = $values;
        } else {
            foreach ((array)$values as $row) {
                if (!isset($row['name'])) continue;
                $name = $row['name'];
                $val  = isset($row['value']) ? $row['value'] : null;

                if (preg_match('/\[(.+?)\]/', $name, $m)) {
                    $code = $m[1];
                } else {
                    $code = $name;
                }

                if (isset($assocValues[$code])) {
                    if (!is_array($assocValues[$code])) {
                        $assocValues[$code] = [$assocValues[$code]];
                    }
                    $assocValues[$code][] = $val;
                } else {
                    $assocValues[$code] = $val;
                }
            }
        }

        // 2. Ambil worklist
        $w = $this->get_one($rawKey);
        if (!$w) {
            return [
                'success'   => false,
                'message'   => 'Worklist tidak ditemukan',
                'result_id' => null,
            ];
        }

        $episodeId = $w['episode_id'] ?? null;
        $pasienId  = $w['pasien_id']  ?? null;
        $sampelId  = $w['sampel_id']  ?? null;

        // 3. Tentukan PK integer worklist
        // if (!empty($w['id']) && is_numeric($w['id'])) {
        //     // $worklistPk = (int)$w['id'];
        //     $worklistPk = (string)$episodeId;
        // } else {
        //     $parts = explode('|', $rawKey);
        //     $first = $parts[0] ?? null;
        //     if ($first !== null && is_numeric($first)) {
        //         $worklistPk = $first;
        //     } else {
        //         return [
        //             'success'   => false,
        //             'message'   => 'worklist_id tidak valid (tidak bisa diubah ke integer)',
        //             'result_id' => null,
        //         ];
        //     }
        // }
        $worklistPk = (string)$episodeId;


        // 4. Master field
        $fields = $this->db
            ->select('field_id, field_code, field_type')
            ->from('pcare_manager.pc01_lab_form_field')
            ->where('form_code', $formCode)
            ->where('aktif', '1')
            ->order_by('field_id')
            ->get()
            ->result_array();


        if (empty($fields)) {
            return [
                'success'   => false,
                'message'   => 'Master field form belum diset untuk ' . $formCode,
                'result_id' => null,
            ];
        }

        $fieldMap = [];
        foreach ($fields as $f) {
            $fieldMap[$f['field_code']] = $f;
        }

        $this->db->trans_start();
        $now = date('Y-m-d H:i:s');



        // 5. HEADER: pc01_lab_form_result (DOKTER)
        // $hdr = $this->db
        //     ->from('pcare_manager.pc01_lab_form_result')
        //     ->where('form_code', $formCode)
        //     ->where('worklist_id', $worklistPk)
        //     ->get()
        //     ->row_array();

        $hdr = $this->db
            ->from('pcare_manager.pc01_lab_form_result')
            ->where('form_code', $formCode)
            ->where('episode_id', $episodeId)
            ->where('pasien_id', $pasienId)
            ->order_by('result_id', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();


        $statusDokter = ($mode === 'submit') ? 'A' : 'D';  //A=ACC D=Draft


        if ($hdr) {
            $resultId = (int)$hdr['result_id'];

            $updateHdr = [
                'episode_id'        => $episodeId,
                'pasien_id'         => $pasienId,
                'sampel_id'         => $sampelId,
                'status_dokter'     => $statusDokter,
                'dokter_user_id'    => $username,
                'last_updated_by'   => $username,
                'last_updated_date' => $now,
            ];

            if ($mode === 'submit') {
                $updateHdr['dokter_acc_date'] = $now;
            }

            $this->db
                ->where('result_id', $resultId)
                ->update('pcare_manager.pc01_lab_form_result', $updateHdr);
        } else {
            // kasus jarang: dokter isi duluan tanpa admin,
            // pakai status_admin 'F' supaya dianggap final analis.
            $this->db->insert('pcare_manager.pc01_lab_form_result', [
                'form_code'         => $formCode,
                'worklist_id'       => $worklistPk,
                'episode_id'        => $episodeId,
                'pasien_id'         => $pasienId,
                'sampel_id'         => $sampelId,
                'status_admin'      => 'F',
                'status_dokter'     => $statusDokter,
                'dokter_user_id'    => $username,
                'dokter_acc_date'   => ($mode === 'submit') ? $now : null,
                'created_by'        => $username,
                'created_date'      => $now,
                'last_updated_by'   => $username,
                'last_updated_date' => $now,
            ]);

            $resultId = $this->db->insert_id();
            // return var_dump($resultId);
            // die;
        }



        if (!$resultId) {
            $this->db->trans_complete();
            return [
                'success'   => false,
                'message'   => 'Gagal menyimpan header hasil PAP (dokter).',
                'result_id' => null,
            ];
        }

        // 6. DETAIL: hapus lama, insert baru
        $this->db
            ->where('result_id', $resultId)
            ->delete('pcare_manager.pc01_lab_form_result_value');

        $batch = [];

        foreach ($fieldMap as $code => $meta) {
            $type = strtolower($meta['field_type'] ?? 'text');
            $raw  = array_key_exists($code, $assocValues) ? $assocValues[$code] : null;

            if ($type === 'checkbox') {
                // Sama seperti versi admin (Y / tidak ada row)
                $checked = false;
                if (is_array($raw)) {
                    $checked = count($raw) > 0;
                } else {
                    $v = strtoupper(trim((string)$raw));
                    $checked = ($v !== '' && $v !== '0' && $v !== 'N' && $v !== 'FALSE');
                }
                if (!$checked) continue;
                $val = 'Y';
            } else {
                if (is_array($raw)) {
                    $val = implode(',', $raw);
                } else {
                    $val = ($raw === null || $raw === '') ? null : (string)$raw;
                }
                if ($val === null || $val === '') continue;
            }

            $batch[] = [
                'result_id'        => $resultId,
                'field_id'         => $meta['field_id'],
                'field_code'       => $code,
                'value_text'       => $val,
                'created_date'     => $now,
                'last_updated_date' => $now,
            ];
        }

        if (!empty($batch)) {
            $this->db->insert_batch('pcare_manager.pc01_lab_form_result_value', $batch);
        }

        // 7. Kalau dokter submit → tandai worklist selesai (status = 1)
        // if ($mode === 'submit') {
        //     $this->db
        //         ->where('episode_id', $worklistPk)
        //         ->update('pcare_manager.pc01_worklist_lab', [
        //             'status'           => '1',
        //             'last_updated_by'  => $username,
        //             'last_updated_date' => $now,
        //         ]);
        // }

        if ($mode === 'submit') {
            // Dokter ACC -> benar-benar selesai dan siap cetak
            $this->update_worklist_status_from_row($w, '1', $username, $now, true);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return [
                'success'   => false,
                'message'   => 'Transaksi gagal (rollback).',
                'result_id' => null,
            ];
        }

        return [
            'success'   => true,
            'message'   => ($mode === 'submit')
                ? 'Form PAP berhasil di-ACC dokter.'
                : 'Draft form PAP dokter berhasil disimpan.',
            'result_id' => $resultId,
        ];
    }




    public function list_pap_for_dokterxxxx($keyword = '')
    {
        $keyword = trim((string)$keyword);

        $this->db
            ->select('
            w.episode_id,
            w.no_sitologi,
            w.trans_id,
            w.trans_co,
            w.pasien_id,
            w.sampel_id,
            w.tanggal,
            w.status,
            w.tgl_barcode,
            w.cito_yn,
            w.rekanan_id,
            w.poli_id,
            w.dokter_id,

            p.nama as nama_pasien,
            coalesce(p.int_pasien_id::text, p.pasien_id::text) as no_rm,

            r.result_id,
            r.status_admin,
            r.status_dokter,
            r.worklist_id,
            r.dokter_user_id,
            r.dokter_acc_date
        ')
            ->from('pcare_manager.pc01_worklist_lab w')

            // 🔥 JOIN PASIEN
            ->join(
                'pcare_manager.pc01_gen_pasien_ms p',
                "p.pasien_id = w.pasien_id AND p.aktif = '1'",
                'left'
            )

            // JOIN RESULT
            ->join(
                'pcare_manager.pc01_lab_form_result r',
                "r.worklist_id = w.episode_id AND r.form_code = 'PAP_ANATOMIK'",
                'inner'
            )

            ->where('w.aktif', '1')
            ->where_in('w.status', ['9', '1'])
            ->where('r.status_admin', 'F');

        // ============================
        // 🔍 SEARCH
        // ============================
        if ($keyword !== '') {
            $this->db->group_start();
            $this->db->like('w.sampel_id', $keyword);
            $this->db->or_like('w.no_sitologi', $keyword); // 🔥 TAMBAHAN
            $this->db->or_like('w.episode_id', $keyword);
            $this->db->or_like('w.pasien_id', $keyword);
            $this->db->or_like('p.nama', $keyword); // 🔥 SEARCH NAMA
            $this->db->or_like('p.int_pasien_id', $keyword); // 🔥 SEARCH RM
            $this->db->group_end();
        }

        // ============================
        // ORDER
        // ============================
        $this->db->order_by('w.tanggal', 'DESC');
        $this->db->order_by('w.episode_id', 'DESC');

        $rows = $this->db->get()->result_array();

        // ============================
        // OPTIONAL: IDENTITY
        // ============================
        foreach ($rows as &$r) {
            if (empty($r['id'])) continue;
            $r['identity'] = (string)$r['id'];
        }

        return $rows;
    }




    public function list_pap_for_dokter($keyword = '', $tab = 'waiting')
    {
        $keyword = trim((string)$keyword);
        $tab = strtolower((string)$tab);

        $latest = $this->latest_pap_result_sql();

        $this->db
            ->select("
            (
                COALESCE(w.episode_id, '') || '|' ||
                COALESCE(w.pasien_id, '')  || '|' ||
                COALESCE(w.trans_id, '')   || '|' ||
                COALESCE(w.trans_co, '')
            ) AS id,

            w.episode_id,
            w.no_sitologi,
            w.trans_id,
            w.trans_co,
            w.pasien_id,
            w.sampel_id,
            w.tanggal,
            w.created_date,
            w.status,
            w.tgl_barcode,
            w.cito_yn,
            w.rekanan_id,
            w.poli_id,
            w.dokter_id,

            p.nama AS nama_pasien,
            COALESCE(p.int_pasien_id::text, p.pasien_id::text) AS no_rm,

            r.result_id,
            r.status_admin,
            r.status_dokter,
            r.worklist_id,
            r.dokter_user_id,
            r.dokter_acc_date
        ", false)
            ->from($this->t_worklist . ' w')
            ->join(
                $this->t_pasien . " p",
                "p.pasien_id = w.pasien_id AND p.aktif = '1'",
                'left'
            )
            ->join(
                "({$latest}) r",
                "r.episode_id = w.episode_id",
                'inner',
                false
            )
            ->where('w.aktif', '1')
            ->where('r.status_admin', 'F');

        if ($tab === 'final') {
            $this->db->where('w.status', '1');
            $this->db->where('r.status_dokter', 'A');
        } else {
            $this->db->where('w.status', '9');
            $this->db->group_start();
            $this->db->where('r.status_dokter IS NULL', null, false);
            $this->db->or_where('r.status_dokter <>', 'A');
            $this->db->group_end();
        }

        if ($keyword !== '') {
            $this->db->group_start();
            $this->db->like('w.sampel_id', $keyword);
            $this->db->or_like('w.no_sitologi', $keyword);
            $this->db->or_like('w.episode_id', $keyword);
            $this->db->or_like('w.pasien_id', $keyword);
            $this->db->or_like('p.nama', $keyword);
            $this->db->or_like('p.int_pasien_id', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('w.tanggal', 'DESC');
        $this->db->order_by('w.created_date', 'DESC');

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['identity'] = $r['id'];
        }

        return $rows;
    }



    public function get_pap_print_payload($formCode, $worklistId): array
    {
        $w = $this->get_one($worklistId);

        if (!$w) {
            return [
                'success' => false,
                'message' => 'Worklist tidak ditemukan.',
            ];
        }

        $form = $this->get_form_with_values($formCode, $w);
        $result = $form['result'] ?? null;

        if (!$this->is_pap_final_dokter($result)) {
            return [
                'success' => false,
                'message' => 'Hasil belum final dokter. PDF hanya bisa dicetak jika status_admin = F dan status_dokter = A.',
            ];
        }

        return [
            'success'  => true,
            'worklist' => $form['worklist'],
            'sections' => $form['sections'],
            'values'   => $form['values'],
            'result'   => $result,
        ];
    }
}
