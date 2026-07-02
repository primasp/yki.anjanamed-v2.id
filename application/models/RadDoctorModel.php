<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RadDoctorModel extends CI_Model
{

    private string $t_worklist   = 'pcare_manager.pc01_worklist_rad';
    private string $t_files      = 'pcare_manager.pc01_rad_result_files';
    private string $t_expertise  = 'pcare_manager.pc01_rad_expertise_log';

    // master
    private string $t_pasien  = 'pcare_manager.pc01_gen_pasien_ms';
    private string $t_poli    = 'pcare_manager.pc01_med_poli_ms';
    private string $t_dokter  = 'pcare_manager.pc01_med_dokter_ms';
    private string $t_rekanan = 'pcare_manager.pc01_keu_rekanan_ms';


    /* ============================
     * Helper: ID parsing
     * episode|pasien|trans|trans_co|rad_ke (rad_ke opsional)
     * ============================ */
    private function parse_id($id): array
    {
        if (ctype_digit((string)$id)) {
            return ['type' => 'pk', 'id' => (int)$id];
        }

        $parts = explode('|', (string)$id);

        return [
            'type'       => 'composite',
            'episode_id' => $parts[0] ?? null,
            'pasien_id'  => $parts[1] ?? null,
            'trans_id'   => $parts[2] ?? null,
            'trans_co'   => $parts[3] ?? null,
            'rad_ke'     => $parts[4] ?? null,
        ];
    }

    private function make_virtual_id(array $row): string
    {
        return implode('|', [
            $row['episode_id'] ?? '',
            $row['pasien_id'] ?? '',
            $row['trans_id'] ?? '',
            $row['trans_co'] ?? '',
            $row['rad_ke'] ?? '',
        ]);
    }

    /* ============================
     * LIST WORKLIST UNTUK DOKTER
     * ============================ */

    public function list_for_doctor(
        string $dokter_rad_id,
        string $status = '',
        string $keyword = ''
    ): array {

        $this->db->select("
        w.*,
        p.nama AS nama_pasien,
        COALESCE(p.int_pasien_id::text, p.pasien_id::text) AS no_rm,
        po.keterangan AS nama_poli,
        d.nama AS nama_dokter_pengirim,
        r.nama AS rekanan_nama,

        (
            SELECT COUNT(*)::int
            FROM {$this->t_files} f
            WHERE f.episode_id = w.episode_id
              AND f.pasien_id  = w.pasien_id
              AND f.trans_id   = w.trans_id
              AND COALESCE(f.trans_co,'') = COALESCE(w.trans_co,'')
              AND COALESCE(f.rad_ke,0)    = COALESCE(w.rad_ke,0)
              AND f.is_active = '1'
        ) AS file_count,

        (
            SELECT COUNT(*)::int
            FROM {$this->t_expertise} e
            WHERE e.episode_id = w.episode_id
              AND e.pasien_id  = w.pasien_id
              AND e.trans_id   = w.trans_id
              AND COALESCE(e.trans_co,'') = COALESCE(w.trans_co,'')
              AND COALESCE(e.rad_ke,0)    = COALESCE(w.rad_ke,0)
        ) AS log_count
        ", false);

        $this->db->from("{$this->t_worklist} w");
        $this->db->join("{$this->t_pasien} p", "p.pasien_id = w.pasien_id", "left");
        $this->db->join("{$this->t_poli} po", "po.poli_id = w.poli_id", "left");
        $this->db->join("{$this->t_dokter} d", "d.dokter_id = w.dokter_id", "left");
        $this->db->join("{$this->t_rekanan} r", "r.rekanan_id = w.rekanan_id", "left");

        $this->db->where('w.aktif', '1');

        // ✅ FILTER DOKTER RAD (AMAN)
        $this->db->group_start()
            ->where('w.dokter_rad_id IS NULL', null, false)
            ->or_where('w.dokter_rad_id', $dokter_rad_id)
            ->group_end();

        // ✅ STATUS
        if ($status !== '') {
            $this->db->where('w.status', $status);
        } else {
            $this->db->where_in('w.status', ['9', '4', '1']);
        }

        // ✅ KEYWORD
        // if ($keyword !== '') {
        //     $this->db->group_start()
        //         ->ilike('p.nama', $keyword)
        //         ->or_ilike("COALESCE(p.int_pasien_id::text, p.pasien_id::text)", $keyword, false)
        //         ->or_ilike('w.episode_id', $keyword)
        //         ->or_ilike('w.trans_id', $keyword)
        //         ->or_ilike('w.kesan_singkat', $keyword)
        //         ->group_end();
        // }

        if ($keyword !== '') {
            $keywordLike = '%' . $this->db->escape_like_str($keyword) . '%';

            $this->db->group_start();
            $this->db->where("p.nama ILIKE " . $this->db->escape($keywordLike), null, false);
            $this->db->or_where("COALESCE(p.int_pasien_id::text, p.pasien_id::text) ILIKE " . $this->db->escape($keywordLike), null, false);
            $this->db->or_where("w.episode_id ILIKE " . $this->db->escape($keywordLike), null, false);
            $this->db->or_where("w.trans_id ILIKE " . $this->db->escape($keywordLike), null, false);
            $this->db->or_where("COALESCE(w.kesan_singkat, '') ILIKE " . $this->db->escape($keywordLike), null, false);
            $this->db->group_end();
        }


        // $this->db->order_by('w.status');
        // $this->db->order_by('w.cito_yn', 'DESC');
        // $this->db->order_by('w.tgl_kirim', 'DESC NULLS LAST', false);
        // $this->db->order_by('w.created_date', 'DESC NULLS LAST', false);


        $this->db->order_by("COALESCE(w.tgl_kirim, w.tanggal, w.created_date)", "DESC NULLS LAST", false);
        $this->db->order_by('w.cito_yn', 'DESC');
        $this->db->order_by('w.created_date', 'DESC NULLS LAST', false);
        $this->db->limit(300);

        return $this->db->get()->result_array();
    }

    public function list_for_doctorxx(
        string $dokter_rad_id,
        string $status,
        string $keyword = ''
    ): array {
        $status  = trim($status);
        $keyword = trim($keyword);

        $params = [$dokter_rad_id];

        $whereStatus = '';
        if ($status !== '') {
            $whereStatus = " AND w.status = ? ";
            $params[]    = $status;
        } else {
            // default: yang relevan untuk dokter
            // menunggu dokter (9), sedang dibaca (4), selesai (1)
            $whereStatus = " AND w.status IN ('9','4','1') ";
        }

        $whereKeyword = '';
        if ($keyword !== '') {
            $k = '%' . $keyword . '%';
            $whereKeyword = " AND (
                COALESCE(p.nama,'') ILIKE ?
                OR COALESCE(p.int_pasien_id::text, p.pasien_id::text) ILIKE ?
                OR COALESCE(w.episode_id,'') ILIKE ?
                OR COALESCE(w.trans_id,'') ILIKE ?
                OR COALESCE(w.kesan_singkat,'') ILIKE ?
            ) ";
            array_push($params, $k, $k, $k, $k, $k);
        }

        $sql = "
            SELECT
                w.*,
                p.nama AS nama_pasien,
                COALESCE(p.int_pasien_id::text, p.pasien_id::text) AS no_rm,
                po.keterangan AS nama_poli,
                d.nama  AS nama_dokter_pengirim,
                r.nama  AS rekanan_nama,
                -- info untuk dokter rad
                (
                  SELECT COUNT(*)::int
                  FROM {$this->t_files} f
                  WHERE f.episode_id = w.episode_id
                    AND f.pasien_id  = w.pasien_id
                    AND f.trans_id   = w.trans_id
                    AND COALESCE(f.trans_co,'') = COALESCE(w.trans_co,'')
                    AND COALESCE(f.rad_ke,0)    = COALESCE(w.rad_ke,0)
                    AND f.is_active = '1'
                ) AS file_count,
                (
                  SELECT COUNT(*)::int
                  FROM {$this->t_expertise} e
                  WHERE e.episode_id = w.episode_id
                    AND e.pasien_id  = w.pasien_id
                    AND e.trans_id   = w.trans_id
                    AND COALESCE(e.trans_co,'') = COALESCE(w.trans_co,'')
                    AND COALESCE(e.rad_ke,0)    = COALESCE(w.rad_ke,0)
                ) AS log_count
            FROM {$this->t_worklist} w
            LEFT JOIN {$this->t_pasien}  p  ON p.pasien_id = w.pasien_id
            LEFT JOIN {$this->t_poli}    po ON po.poli_id = w.poli_id
            LEFT JOIN {$this->t_dokter}  d  ON d.dokter_id = w.dokter_id
            LEFT JOIN {$this->t_rekanan} r  ON r.rekanan_id = w.rekanan_id
            WHERE w.aktif = '1'
              AND (w.dokter_rad_id IS NULL OR w.dokter_rad_id = ?)
              {$whereStatus}
              {$whereKeyword}
            ORDER BY
              w.status,              -- supaya 9→4→1
              w.cito_yn DESC,
              w.tgl_kirim DESC NULLS LAST,
              w.created_date DESC NULLS LAST
            LIMIT 300
        ";

        $rows = $this->db->query($sql, $params)->result_array();

        foreach ($rows as &$r) {
            if (!isset($r['id']) || $r['id'] === null || $r['id'] === '') {
                $r['id'] = $this->make_virtual_id($r);
            }
        }
        return $rows;
    }



    /* ============================
     * GET SATU ROW + MASTER
     * ============================ */
    public function get_one($id): ?array
    {
        $key = $this->parse_id($id);

        $this->db->from($this->t_worklist . ' w');
        $this->db->select("
            w.*,
            p.nama AS nama_pasien,
            COALESCE(p.int_pasien_id::text, p.pasien_id::text) AS no_rm,
            po.keterangan AS nama_poli,
            d.nama  AS nama_dokter_pengirim,
            r.nama  AS rekanan_nama
        ", false);
        $this->db->join($this->t_pasien  . ' p',  'p.pasien_id = w.pasien_id', 'left');
        $this->db->join($this->t_poli    . ' po', 'po.poli_id = w.poli_id',    'left');
        $this->db->join($this->t_dokter  . ' d',  'd.dokter_id = w.dokter_id', 'left');
        $this->db->join($this->t_rekanan . ' r',  'r.rekanan_id = w.rekanan_id', 'left');

        $this->db->where('w.aktif', '1');

        if ($key['type'] === 'pk') {
            $this->db->where('w.id', $key['id']);
        } else {
            $this->db->where('w.episode_id', $key['episode_id']);
            $this->db->where('w.pasien_id',  $key['pasien_id']);
            $this->db->where('w.trans_id',   $key['trans_id']);
            if (!empty($key['trans_co'])) {
                $this->db->where('w.trans_co', $key['trans_co']);
            }
            if (!empty($key['rad_ke'])) {
                $this->db->where('w.rad_ke', $key['rad_ke']);
            }
        }

        $row = $this->db->get()->row_array();
        if ($row && (!isset($row['id']) || $row['id'] === null || $row['id'] === '')) {
            $row['id'] = $this->make_virtual_id($row);
        }
        return $row ?: null;
    }


    /* ============================
     * FILE-FILE HASIL (VIEWER)
     * ============================ */
    public function get_files_for_worklist(array $w): array
    {
        $sql = "
          SELECT
            f.*,
            (CASE
               WHEN f.is_latest = '1' THEN 'Terbaru'
               ELSE 'Lama'
             END) AS label_versi
          FROM {$this->t_files} f
          WHERE f.episode_id = ?
            AND f.pasien_id  = ?
            AND f.trans_id   = ?
            AND COALESCE(f.trans_co,'') = COALESCE(?, '')
            AND COALESCE(f.rad_ke,0) = COALESCE(?,0)
            AND f.is_active = '1'
          ORDER BY f.versi DESC, f.uploaded_at DESC
        ";

        return $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->result_array();
    }


    /* ============================
     * EXPERTISE (LAST & HISTORY)
     * ============================ */
    public function get_last_expertise(array $w): ?array
    {
        // kalau last_expertise_id di worklist terisi, gunakan itu
        if (!empty($w['last_expertise_id'])) {
            return $this->db->get_where($this->t_expertise, ['id' => $w['last_expertise_id']])->row_array() ?: null;
        }

        $sql = "
          SELECT *
          FROM {$this->t_expertise}
          WHERE episode_id = ?
            AND pasien_id  = ?
            AND trans_id   = ?
            AND COALESCE(trans_co,'') = COALESCE(?, '')
            AND COALESCE(rad_ke,0)    = COALESCE(?,0)
          ORDER BY created_date DESC
          LIMIT 1
        ";
        $row = $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->row_array();

        return $row ?: null;
    }



    public function get_expertise_history(array $w): array
    {
        $sql = "
          SELECT *
          FROM {$this->t_expertise}
          WHERE episode_id = ?
            AND pasien_id  = ?
            AND trans_id   = ?
            AND COALESCE(trans_co,'') = COALESCE(?, '')
            AND COALESCE(rad_ke,0)    = COALESCE(?,0)
          ORDER BY file_version DESC, created_date DESC
        ";
        return $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->result_array();
    }

    public function get_latest_expertise_version(array $w): int
    {
        $sql = "
        SELECT MAX(file_version)::int AS versi
        FROM {$this->t_expertise}
        WHERE episode_id = ?
          AND pasien_id  = ?
          AND trans_id   = ?
          AND COALESCE(trans_co,'') = COALESCE(?, '')
          AND COALESCE(rad_ke,0)    = COALESCE(?,0)
    ";

        $row = $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->row_array();

        return (int)($row['versi'] ?? 0);
    }

    public function get_latest_file_versionxx(array $w): int
    {
        $sql = "
        SELECT MAX(file_version)::int AS versi
        FROM {$this->t_expertise}
        WHERE episode_id = ?
          AND pasien_id  = ?
          AND trans_id   = ?
          AND COALESCE(trans_co,'') = COALESCE(?, '')
          AND COALESCE(rad_ke,0)    = COALESCE(?,0)
    ";

        $row = $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->row_array();

        return (int)($row['versi'] ?? 0);
    }

    public function get_latest_file_versionxxx(array $w): ?int
    {
        $sql = "
          SELECT MAX(versi)::int AS versi
          FROM {$this->t_files}
          WHERE episode_id = ?
            AND pasien_id  = ?
            AND trans_id   = ?
            AND COALESCE(trans_co,'') = COALESCE(?, '')
            AND COALESCE(rad_ke,0)    = COALESCE(?,0)
            AND is_active = '1'
        ";
        $row = $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->row_array();
        return $row && $row['versi'] ? (int)$row['versi'] : null;
    }

    /* ============================
     * SIMPAN DRAFT / FINAL LAPORAN
     * ============================ */
    public function save_expertise(
        array $w,
        array $data,
        string $dokter_rad_id,
        bool $is_final
    ): array {
        $this->db->trans_begin();

        // versi file yang dipakai: dari POST (jika ada), kalau tidak pakai latest
        // ==============================
        // 1. Tentukan versi file
        // ==============================
        // $file_version = $data['file_version'] ?? null;

        // 🔥 FIX: selalu ambil latest lalu +1
        // $latest = $this->get_latest_file_version($w);
        $latest = $this->get_latest_expertise_version($w);

        // if ($is_final) {
        //     $file_version = $latest + 1; // hanya final naik
        // } else {
        //     $file_version = $latest ?: 1; // draft tetap di versi terakhir
        // }

        if ($is_final) {
            $file_version = $latest + 1;
        } else {
            $file_version = $latest > 0 ? $latest : 1;
        }


        // $file_version = $latest + 1;
        // $file_version = ((int)$latest) + 1;

        // if (!$file_version) {
        //     $file_version = $this->get_latest_file_version($w);
        // }

        $status_lap = $is_final ? 'F' : 'D';

        $insert = [
            'lokasi_id'     => $w['lokasi_id'],
            'episode_id'    => $w['episode_id'],
            'pasien_id'     => $w['pasien_id'],
            'trans_id'      => $w['trans_id'],
            'trans_co'      => $w['trans_co'],
            'rad_ke'        => $w['rad_ke'],
            'dokter_rad_id' => $dokter_rad_id,
            'status_lap'    => $status_lap,
            // 'temuan'        => $data['temuan'] ?? null,
            // 'kesan'         => $data['kesan'] ?? null,
            // 'saran'         => $data['saran'] ?? null,

            // ✅ FIELD BARU
            'hasil_bacaan'  => $data['hasil_bacaan'] ?? null,

            'file_version'  => $file_version,
            'created_by'    => $dokter_rad_id,
            'created_date'  => date('Y-m-d H:i:s'),
        ];



        // ==============================
        // INSERT + RETURNING id (TANPA insert_id)
        // ==============================



        $sql  = $this->db->insert_string($this->t_expertise, $insert) . ' RETURNING id';
        $row  = $this->db->query($sql)->row_array();
        $exp_id = $row['id'] ?? null;
        // return var_dump($exp_id);
        // die;

        if (!$exp_id) {
            // kalau sampai sini, berarti generate_id / insert gagal
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Gagal membuat ID expertise'];
        }

        // ===== update worklist =====
        $upd = [
            'dokter_rad_id'     => $dokter_rad_id,
            'last_expertise_id' => $exp_id,
            'last_updated_by'   => $dokter_rad_id,
            'last_updated_date' => date('Y-m-d H:i:s'),
        ];

        if (empty($w['tgl_dibaca'])) {
            $upd['tgl_dibaca'] = date('Y-m-d H:i:s');
        }

        if ($is_final) {
            $upd['status']        = '1';
            $upd['tgl_validasi']  = date('Y-m-d H:i:s');
            $upd['tgl_selesai']   = date('Y-m-d H:i:s');
            $upd['user_selesai']  = $dokter_rad_id;



            // $upd['kesan_singkat'] = mb_substr((string)($data['kesan'] ?? ''), 0, 500);

            // ✅ ambil ringkasan dari hasil_bacaan
            $upd['kesan_singkat'] = mb_substr(
                trim((string)($data['hasil_bacaan'] ?? '')),
                0,
                500
            );
        } else {
            if ((string)$w['status'] === '9' || (string)$w['status'] === '0') {
                $upd['status'] = '4'; // "dibaca dokter"
            }
        }

        $this->db->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) {
            $this->db->where("COALESCE(trans_co,'')", $w['trans_co']);
        } else {
            $this->db->where("COALESCE(trans_co,'')", '');
        }

        $this->db->where("COALESCE(rad_ke,0)", (int)($w['rad_ke'] ?? 0));
        $this->db->update($this->t_worklist, $upd);

        // ===== lock file versi yang dipakai (kalau final) =====
        if ($is_final && $file_version) {
            $this->db->where('episode_id', $w['episode_id'])
                ->where('pasien_id',  $w['pasien_id'])
                ->where('trans_id',   $w['trans_id']);

            if (!empty($w['trans_co'])) {
                $this->db->where("COALESCE(trans_co,'')", $w['trans_co']);
            } else {
                $this->db->where("COALESCE(trans_co,'')", '');
            }

            $this->db->where("COALESCE(rad_ke,0)", (int)($w['rad_ke'] ?? 0));
            // $this->db->where('versi', (int)$file_version);
            $this->db->where('is_latest', '1');

            $this->db->update($this->t_files, [
                'is_locked'         => '1',
                'last_updated_by'   => $dokter_rad_id,
                'last_updated_date' => date('Y-m-d H:i:s'),
            ]);
        }


        // ==============================
        // 6. COMMIT / ROLLBACK
        // ==============================
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Gagal simpan expertise (DB error)'];
        }

        $this->db->trans_commit();

        return [
            'success'      => true,
            'id'           => $exp_id,
            'file_version' => $file_version,
        ];
    }


    public function get_full_result($ref)
    {
        $tLog      = 'pcare_manager.pc01_rad_expertise_log';
        $tWorklist = 'pcare_manager.pc01_worklist_rad';
        $tPasien   = 'pcare_manager.pc01_gen_pasien_ms';
        $tEpisode  = 'pcare_manager.pc01_keu_episode';
        $tPoli     = 'pcare_manager.pc01_med_poli_ms';
        $tDokter   = 'pcare_manager.pc01_med_dokter_ms';
        $tRekanan  = 'pcare_manager.pc01_keu_rekanan_ms';
        $tFiles    = 'pcare_manager.pc01_rad_result_files';
        $tUser = 'pcare_manager.pc01_gen_user_data';

        $whereMain = '';
        $bindMain  = [];

        if (is_numeric($ref)) {
            $whereMain = 'l.id = ?';
            $bindMain[] = (int)$ref;
        } elseif (is_array($ref)) {

            $episodeId = $ref['episode_id'] ?? null;
            $pasienId  = $ref['pasien_id'] ?? null;
            $transId   = $ref['trans_id'] ?? null;
            $transCo   = $ref['trans_co'] ?? '';
            $transCo = preg_replace('/[^a-zA-Z0-9]/', '', $transCo);

            if (!$episodeId || !$pasienId || !$transId) {
                return null;
            }

            $whereMain = "
            l.episode_id = ?
            AND l.pasien_id = ?
            AND l.trans_id = ?
            AND TRIM(COALESCE(l.trans_co,'')) = TRIM(COALESCE(?, ''))
        ";

            $bindMain = [$episodeId, $pasienId, $transId, $transCo];
        } else {
            return null;
        }

        // ================= MAIN =================
        $sql = "
        SELECT
            l.*,
            w.status AS worklist_status,
            w.tanggal AS tgl_order,
            w.rekanan_id,
            w.poli_id,
            w.dokter_id AS dokter_pengirim_id,
            w.cito_yn,
            w.ruang_id,
            p.int_pasien_id AS no_rm,
            p.nama AS nama_pasien,
            p.tgl_lahir,
            p.sex_id,
            p.alamat1,
            CASE
                WHEN p.sex_id = 'L' THEN 'Laki-laki'
                WHEN p.sex_id = 'P' THEN 'Perempuan'
                ELSE COALESCE(p.sex_id, '-')
            END AS jenis_kelamin,
            EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.tgl_lahir))::INT AS umur_tahun,
            e.tgl_masuk,
            po.keterangan AS nama_poli,
            dp.nama AS nama_dokter_pengirim,
            COALESCE(
                NULLIF(dr.nama, ''),
                NULLIF(ur.nama, ''),
                NULLIF(uc.nama, ''),
                l.dokter_rad_id,
                'Dokter Radiologi'
            ) AS nama_dokter_radiologi,
            rk.nama AS nama_rekanan
        FROM {$tLog} l
        LEFT JOIN {$tWorklist} w
            ON w.episode_id = l.episode_id
            AND w.pasien_id  = l.pasien_id
            AND w.trans_id   = l.trans_id
            AND TRIM(COALESCE(w.trans_co,'')) = TRIM(COALESCE(l.trans_co,''))
            AND w.aktif = '1'
        LEFT JOIN {$tPasien} p ON p.pasien_id = l.pasien_id
        LEFT JOIN {$tEpisode} e ON e.episode_id = l.episode_id AND e.pasien_id = l.pasien_id
        LEFT JOIN {$tPoli} po ON po.poli_id = w.poli_id
        LEFT JOIN {$tDokter} dp ON dp.dokter_id = w.dokter_id
        LEFT JOIN {$tDokter} dr ON dr.dokter_id = l.dokter_rad_id
        LEFT JOIN {$tUser} ur ON ur.user_id = l.dokter_rad_id
        LEFT JOIN {$tUser} uc ON uc.user_id = l.created_by
        LEFT JOIN {$tRekanan} rk ON rk.rekanan_id = w.rekanan_id
        WHERE {$whereMain}
        ORDER BY
            CASE WHEN l.status_lap = 'F' THEN 0 ELSE 1 END,
            COALESCE(l.file_version, 0) DESC,
            l.id DESC
        LIMIT 1
        ";

        // echo "<pre>";
        // print_r($sql);
        // print_r($bindMain);
        // die;

        $main = $this->db->query($sql, $bindMain)->row_array();

        if (!$main) {
            return null;
        }

        $episodeId = $main['episode_id'];
        $pasienId  = $main['pasien_id'];
        $transId   = $main['trans_id'];
        $transCo   = $main['trans_co'] ?? '';

        // ================= VERSIONS =================
        $sqlVersions = "
        SELECT
            id,
            file_version,
            status_lap,
            dokter_rad_id,
            created_by,
            created_date,
            last_updated_by,
            last_updated_date
        FROM {$tLog}
        WHERE episode_id = ?
          AND pasien_id  = ?
          AND trans_id   = ?
          AND TRIM(COALESCE(trans_co,'')) = TRIM(COALESCE(?, ''))
        ORDER BY COALESCE(file_version, 0) DESC, id DESC
        ";

        $versions = $this->db->query(
            $sqlVersions,
            [$episodeId, $pasienId, $transId, $transCo]
        )->result_array();

        // ================= FILES =================
        $sqlFiles = "
        SELECT
            id,
            file_name,
            file_path,
            mime_type,
            file_size,
            catatan,
            uploaded_by,
            uploaded_at,
            versi,
            is_latest,
            is_locked
        FROM {$tFiles}
        WHERE episode_id = ?
          AND pasien_id  = ?
          AND trans_id   = ?
          AND TRIM(COALESCE(trans_co,'')) = TRIM(COALESCE(?, ''))
          AND COALESCE(is_active, '1') = '1'
        ORDER BY versi DESC, uploaded_at DESC
        ";

        $files = $this->db->query(
            $sqlFiles,
            [$episodeId, $pasienId, $transId, $transCo]
        )->result_array();

        return [
            'result'   => $main,
            'versions' => $versions,
            'files'    => $files,
        ];
    }



    public function get_full_resultx($ref)
    {


        $tLog      = 'pcare_manager.pc01_rad_expertise_log';
        $tWorklist = 'pcare_manager.pc01_worklist_rad';
        $tPasien   = 'pcare_manager.pc01_gen_pasien_ms';
        $tEpisode  = 'pcare_manager.pc01_keu_episode';
        $tPoli     = 'pcare_manager.pc01_med_poli_ms';
        $tDokter   = 'pcare_manager.pc01_med_dokter_ms';
        $tRekanan  = 'pcare_manager.pc01_keu_rekanan_ms';

        // sesuaikan kalau nama tabel file radiologi Anda berbeda
        $tFiles    = 'pcare_manager.pc01_rad_result_files';

        $whereMain = '';
        $bindMain  = [];

        $episodeId = null;
        $pasienId  = null;
        $transId   = null;
        $transCo   = null;



        if (is_numeric($ref)) {
            $whereMain = 'l.id = ?';
            $bindMain[] = (int)$ref;
        } elseif (is_array($ref)) {
            $episodeId = $ref['episode_id'] ?? null;
            $pasienId  = $ref['pasien_id'] ?? null;
            $transId   = $ref['trans_id'] ?? null;
            $transCo   = array_key_exists('trans_co', $ref) ? $ref['trans_co'] : null;



            if (!$episodeId || !$pasienId || !$transId) {
                return null;
            }

            $whereMain = "
            l.episode_id = ?
            AND l.pasien_id = ?
            AND l.trans_id = ?
            AND COALESCE(l.trans_co,'') = COALESCE(?, '')
        ";
            // $bindMain = [$episodeId, $pasienId, $transId, $transCo, $transCo];
            $bindMain = [$episodeId, $pasienId, $transId, $transCo];

            // echo $this->db->last_query();
            // die;
        } else {
            return null;
        }

        $sql = "SELECT
                    l.id,
                    l.lokasi_id,
                    l.episode_id,
                    l.pasien_id,
                    l.trans_id,
                    l.trans_co,
                    l.rad_ke,
                    l.dokter_rad_id,
                    l.status_lap,
                    l.temuan,
                    l.kesan,
                    l.saran,
                    l.file_version,
                    l.created_by,
                    l.created_date,
                    l.last_updated_by,
                    l.last_updated_date,
                    l.hasil_bacaan,
                    w.status AS worklist_status,
                    w.tanggal AS tgl_order,
                    w.rekanan_id,
                    w.poli_id,
                    w.dokter_id AS dokter_pengirim_id,
                    w.cito_yn,
                    w.ruang_id,
                    p.int_pasien_id AS no_rm,
                    p.nama AS nama_pasien,
                    p.tgl_lahir,
                    p.sex_id,
                    p.alamat1,
                    CASE
                        WHEN p.sex_id = 'L' THEN 'Laki-laki'
                        WHEN p.sex_id = 'P' THEN 'Perempuan'
                        ELSE COALESCE(p.sex_id, '-')
                    END AS jenis_kelamin,
                    EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.tgl_lahir))::INT AS umur_tahun,
                    e.tgl_masuk,
                    po.keterangan AS nama_poli,
                    dp.nama AS nama_dokter_pengirim,
                    dr.nama AS nama_dokter_radiologi,
                    rk.nama AS nama_rekanan
                FROM {$tLog} l
                LEFT JOIN {$tWorklist} w
                    ON w.episode_id = l.episode_id
                AND w.pasien_id  = l.pasien_id
                AND w.trans_id   = l.trans_id
                AND (
                        (w.trans_co IS NULL AND l.trans_co IS NULL)
                        OR w.trans_co = l.trans_co
                )
                AND w.aktif = '1'
                LEFT JOIN {$tPasien} p
                    ON p.pasien_id = l.pasien_id
                LEFT JOIN {$tEpisode} e
                    ON e.episode_id = l.episode_id
                AND e.pasien_id  = l.pasien_id
                LEFT JOIN {$tPoli} po
                    ON po.poli_id = w.poli_id
                LEFT JOIN {$tDokter} dp
                    ON dp.dokter_id = w.dokter_id
                LEFT JOIN {$tDokter} dr
                    ON dr.dokter_id = l.dokter_rad_id
                LEFT JOIN {$tRekanan} rk
                    ON rk.rekanan_id = w.rekanan_id
                WHERE {$whereMain}
                ORDER BY
                    CASE WHEN l.status_lap = 'F' THEN 0 ELSE 1 END,
                    COALESCE(l.file_version, 0) DESC,
                    l.id DESC
                LIMIT 1
            ";


        $main = $this->db->query($sql, $bindMain)->row_array();

        return var_dump($main);
        die;

        if (!$main) {
            return null;
        }

        $episodeId = $main['episode_id'];
        $pasienId  = $main['pasien_id'];
        $transId   = $main['trans_id'];
        $transCo   = $main['trans_co'];

        // riwayat versi expertise
        $sqlVersions = "
        SELECT
            id,
            file_version,
            status_lap,
            dokter_rad_id,
            created_by,
            created_date,
            last_updated_by,
            last_updated_date
        FROM {$tLog}
        WHERE episode_id = ?
          AND pasien_id  = ?
          AND trans_id   = ?
          AND (
                (trans_co IS NULL AND ? IS NULL)
                OR trans_co = ?
          )
        ORDER BY COALESCE(file_version, 0) DESC, id DESC
        ";
        $versions = $this->db->query(
            $sqlVersions,
            [$episodeId, $pasienId, $transId, $transCo, $transCo]
        )->result_array();

        // file lampiran radiologi
        $sqlFiles = "
        SELECT
            id,
            file_name,
            file_path,
            mime_type,
            file_size,
            catatan,
            uploaded_by,
            uploaded_at
        FROM {$tFiles}
        WHERE episode_id = ?
          AND pasien_id  = ?
          AND trans_id   = ?
          AND (
                (trans_co IS NULL AND ? IS NULL)
                OR trans_co = ?
          )
          AND COALESCE(is_active, '1') = '1'
        ORDER BY uploaded_at DESC, id DESC
        ";

        $files = [];
        try {
            $files = $this->db->query(
                $sqlFiles,
                [$episodeId, $pasienId, $transId, $transCo, $transCo]
            )->result_array();
        } catch (\Throwable $e) {
            $files = [];
        }

        return [
            'result'   => $main,
            'versions' => $versions,
            'files'    => $files,
        ];
    }

    public function get_full_resultxxx(array $w): array
    {
        $sql = "
        SELECT 
            e.id,
            e.lokasi_id,
            e.episode_id,
            e.pasien_id,
            e.trans_id,
            e.trans_co,
            e.rad_ke,
            e.dokter_rad_id,
            e.status_lap,
            e.hasil_bacaan,
            e.file_version,
            e.created_by,
            e.created_date,

            -- 🔥 info dokter (optional, sesuaikan tabel user)
            u.nama AS nama_dokter,

            -- 🔥 file info (ambil latest)
            f.id AS file_id,
            f.file_name,
            f.file_path,
            f.mime_type,
            f.versi AS file_versi,
            f.is_locked

        FROM pcare_manager.pc01_rad_expertise_log e

        LEFT JOIN pcare_manager.pc01_rad_result_files f
            ON f.episode_id = e.episode_id
            AND f.pasien_id  = e.pasien_id
            AND f.trans_id   = e.trans_id
            AND COALESCE(f.trans_co,'') = COALESCE(e.trans_co,'')
            AND COALESCE(f.rad_ke,0)    = COALESCE(e.rad_ke,0)
            AND f.is_active = '1'
            AND f.is_latest = '1'

        LEFT JOIN pcare_manager.pc01_gen_user_ms u
            ON u.user_id = e.dokter_rad_id

        WHERE 
            e.episode_id = ?
            AND e.pasien_id = ?
            AND e.trans_id = ?
            AND COALESCE(e.trans_co,'') = COALESCE(?, '')
            AND COALESCE(e.rad_ke,0)    = COALESCE(?,0)

        ORDER BY 
            e.file_version DESC,
            e.created_date DESC
    ";

        $rows = $this->db->query($sql, [
            $w['episode_id'],
            $w['pasien_id'],
            $w['trans_id'],
            $w['trans_co'],
            $w['rad_ke'],
        ])->result_array();

        return $rows ?: [];
    }
}
