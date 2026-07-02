<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RadModel extends CI_Model
{

    private string $t_worklist = 'pcare_manager.pc01_worklist_rad';
    private string $t_orders   = 'pcare_manager.pc01_co_rad_dt'; // << SESUAIKAN jika nama detail radiologi Anda berbeda
    private string $t_files    = 'pcare_manager.pc01_rad_result_files';

    private bool $enable_join_master = true;
    private string $t_pasien  = 'pcare_manager.pc01_gen_pasien_ms';
    private string $t_poli    = 'pcare_manager.pc01_med_poli_ms';
    private string $t_dokter  = 'pcare_manager.pc01_med_dokter_ms';
    private string $t_rekanan = 'pcare_manager.pc01_keu_rekanan_ms';


    public function counts(): array
    {
        $sql = "SELECT status, count(*)::int as cnt
                FROM {$this->t_worklist}
                WHERE aktif='1'
                GROUP BY status";
        $rows = $this->db->query($sql)->result_array();

        $out = ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '9' => 0];
        foreach ($rows as $r) $out[(string)$r['status']] = (int)$r['cnt'];
        return $out;
    }


    public function list_by_status(string $status, string $keyword = '', $tanggal = null, bool $semuaTanggal = false): array
    {
        $status  = (string)$status;

        $keyword = trim((string)$keyword);

        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }



        // WHERE tanggal dinamis
        $whereTanggal = '';
        $params = [$status];


        if (!$semuaTanggal) {
            $whereTanggal = " AND w.tanggal = ? ";
            $params[] = $tanggal;
        }

        // return var_dump($whereTanggal);
        // die;

        // $params = [$status, $tanggal];
        $whereKeyword = "";

        if ($keyword !== '') {
            $k = '%' . $keyword . '%';

            if ($this->enable_join_master) {

                $whereKeyword = " AND (
                    COALESCE(p.nama,'') ILIKE ?
                    OR COALESCE(p.pasien_id::text,'') ILIKE ?
                    OR COALESCE(w.episode_id,'') ILIKE ?
                    OR COALESCE(w.trans_id,'') ILIKE ?
                        OR COALESCE(w.trans_co,'') ILIKE ?
                    OR COALESCE(w.catatan_rad,'') ILIKE ?
                )";
                array_push($params, $k, $k, $k, $k, $k, $k);
            } else {
                $whereKeyword = " AND (
                    COALESCE(w.episode_id,'') ILIKE ?
                    OR COALESCE(w.pasien_id,'') ILIKE ?
                    OR COALESCE(w.trans_id,'') ILIKE ?
                    OR COALESCE(w.trans_co,'') ILIKE ?
                    OR COALESCE(w.catatan_rad,'') ILIKE ?
                )";
                array_push($params, $k, $k, $k, $k, $k);
            }
        }
        // return var_dump($whereKeyword);
        // die;
        if ($this->enable_join_master) {
            $sql = "SELECT
                      w.*,
                      p.nama AS nama_pasien,
                      COALESCE(p.int_pasien_id::text, p.pasien_id::text) AS no_rm,
                      po.keterangan AS nama_poli,
                      d.nama AS nama_dokter,
                      r.nama AS rekanan_nama,
                      (SELECT count(*)::int FROM {$this->t_files} f
                        WHERE f.is_active='1'
                          AND f.episode_id=w.episode_id AND f.pasien_id=w.pasien_id AND f.trans_id=w.trans_id
                          AND ( (w.trans_co IS NULL AND f.trans_co IS NULL) OR (f.trans_co=w.trans_co) )
                      ) AS file_count
                    FROM {$this->t_worklist} w
                    LEFT JOIN {$this->t_pasien}  p  ON p.pasien_id = w.pasien_id
                    LEFT JOIN {$this->t_poli}    po ON po.poli_id  = w.poli_id
                    LEFT JOIN {$this->t_dokter}  d  ON d.dokter_id = w.dokter_id
                    LEFT JOIN {$this->t_rekanan} r  ON r.rekanan_id= w.rekanan_id
                    WHERE w.aktif='1' AND w.status=? 
                    {$whereTanggal}
                    {$whereKeyword}
                    ORDER BY w.created_date DESC NULLS LAST, w.tanggal DESC NULLS LAST
                    LIMIT 300";
        } else {
            $sql = "SELECT w.*,
                      NULL::text AS nama_pasien,
                      NULL::text AS no_rm,
                      NULL::text AS nama_poli,
                      NULL::text AS nama_dokter,
                      NULL::text AS rekanan_nama,
                      0::int AS file_count
                    FROM {$this->t_worklist} w
                    WHERE w.aktif='1' AND w.status=? 
                    {$whereTanggal}
                    {$whereKeyword}
                    ORDER BY w.created_date DESC NULLS LAST, w.tanggal DESC NULLS LAST
                    LIMIT 300";
        }

        return $this->db->query($sql, $params)->result_array();
    }

    private function parse_id($id): array
    {
        if (ctype_digit((string)$id)) return ['type' => 'pk', 'id' => (int)$id];
        $parts = explode('|', (string)$id);
        return [
            'type' => 'composite',
            'episode_id' => $parts[0] ?? null,
            'pasien_id' => $parts[1] ?? null,
            'trans_id' => $parts[2] ?? null,
            'trans_co' => $parts[3] ?? null,
        ];
    }

    private function make_virtual_id(array $row): string
    {
        return ($row['episode_id'] ?? '') . '|' . ($row['pasien_id'] ?? '') . '|' . ($row['trans_id'] ?? '') . '|' . ($row['trans_co'] ?? '');
    }

    public function get_one($id): ?array
    {
        $key = $this->parse_id($id);

        $select = "w.*,
        p.nama as nama_pasien,
        coalesce(p.int_pasien_id::text, p.pasien_id::text) as no_rm,
        po.keterangan as nama_poli,
        d.nama as nama_dokter,
        r.nama as rekanan_nama";

        $this->db->select($select, false);
        $this->db->from($this->t_worklist . ' w');
        $this->db->join($this->t_pasien . ' p', 'p.pasien_id = w.pasien_id', 'left');
        $this->db->join($this->t_poli . ' po', 'po.poli_id = w.poli_id', 'left');
        $this->db->join($this->t_dokter . ' d', 'd.dokter_id = w.dokter_id', 'left');
        $this->db->join($this->t_rekanan . ' r', 'r.rekanan_id = w.rekanan_id', 'left');

        $this->db->where('w.aktif', '1');

        // ====== PK mode (kalau memang tabel ada kolom id) ======
        // IMPORTANT: jangan pakai get_where($this->t_worklist, ...) karena itu mengabaikan join
        if ($key['type'] === 'pk') {
            $this->db->where('w.id', (int)$key['id']);
            $row = $this->db->get()->row_array();
            return $row ?: null;
        }

        // ====== Composite key mode ======
        $this->db->where('w.episode_id', $key['episode_id']);
        $this->db->where('w.pasien_id',  $key['pasien_id']);
        $this->db->where('w.trans_id',   $key['trans_id']);

        // trans_co opsional
        if (!empty($key['trans_co'])) {
            $this->db->where('w.trans_co', $key['trans_co']);
        }

        $row = $this->db->get()->row_array();

        if ($row && (empty($row['id']))) {
            $row['id'] = $this->make_virtual_id($row);
        }
        return $row ?: null;
    }


    // ========= DETAIL ORDER RADIOLOGI =========
    // Anda WAJIB sesuaikan tabel detail radiologi (pc01_co_rad_dt) & kolomnya.
    public function get_orders_for_worklist(array $w): array
    {
        // contoh join layan master radiologi
        $t_layan = 'pcare_manager.pc01_keu_layan_ms';

        // SESUAIKAN: jika detail radiologi Anda beda dari ini
        $sql = "SELECT
                  a.test_id,
                  l.nama_layan1 AS nama_pemeriksaan,
                  a.cito,
                  a.catatan
                FROM {$this->t_orders} a
                LEFT JOIN {$t_layan} l
                  ON l.layan_id = a.test_id
                 AND l.kategori_id = 'JKL-RAD'
                WHERE a.episode_id=? AND a.pasien_id=? AND a.trans_id=? AND a.trans_co=?
                ORDER BY a.test_id";
        return $this->db->query($sql, [
            $w['episode_id'], $w['pasien_id'], $w['trans_id'], $w['trans_co']
        ])->result_array();
    }


    public function get_result_files(array $w): array
    {
        $sql = "SELECT id, file_name, file_path,catatan, mime_type, file_size, uploaded_by, uploaded_at
                FROM {$this->t_files}
                WHERE is_active='1'
                  AND episode_id=? AND pasien_id=? AND trans_id=?
                  AND ( (trans_co IS NULL AND ? IS NULL) OR (trans_co=?) )
                ORDER BY uploaded_at DESC";
        $tc = $w['trans_co'] ?? null;
        return $this->db->query($sql, [$w['episode_id'], $w['pasien_id'], $w['trans_id'], $tc, $tc])->result_array();
    }

    public function update_note($fileId, $catatan, $user)
    {
        $this->db->where('id', $fileId);
        $this->db->update($this->t_files, [
            'catatan' => $catatan,
            'last_updated_by' => $user,
            'last_updated_date' => date('Y-m-d H:i:s')
        ]);

        if ($this->db->affected_rows() > 0) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Gagal update catatan'
        ];
    }

    public function update_status($id, string $status, string $username): array
    {
        $w = $this->get_one($id);
        if (!$w) return ['success' => false, 'message' => 'Worklist tidak ditemukan.'];

        // Rule minimal:
        // - Admin boleh set 2 (tunda) / 3 (batal) dari status manapun (opsional Anda perketat)
        // - Selesai (1) isi tgl_selesai, user_selesai
        // - Dikerjakan (4) isi last_updated...

        $data = [
            'status' => $status,
            'last_updated_by' => $username,
            'last_updated_date' => date('Y-m-d H:i:s'),
        ];

        if ($status === '1') {
            $data['tgl_selesai'] = date('Y-m-d H:i:s');
            $data['user_selesai'] = $username;
        }

        $this->db->where('episode_id', $w['episode_id'])
            ->where('pasien_id', $w['pasien_id'])
            ->where('trans_id', $w['trans_id']);
        if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);

        $this->db->update($this->t_worklist, $data);

        return ['success' => true];
    }

    // ========= FILES =========
    // public function save_result_file(array $w, array $file, string $username): void
    public function save_result_file(array $w, array $file, string $username, ?string $catatanRevisi = null): array
    {

        $this->db->trans_begin();
        // 1) Cek apakah sudah di-lock (kalau sudah final tidak boleh di-edit admin)
        $locked = $this->db->select('status')
            ->from($this->t_worklist)
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id'])
            ->where(
                "COALESCE(trans_co, '') = " . $this->db->escape($w['trans_co'] ?? ''),
                null,
                false
            )
            ->get()
            ->row_array();


        if ($locked && (string)$locked['status'] === '1') {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Pemeriksaan sudah SELESAI, tidak bisa upload baru.'];
        }


        // 2) Hitung versi terakhir
        $rowVersi = $this->db->select('coalesce(max(versi),0) as v')
            ->from($this->t_files)
            ->where([
                'episode_id' => $w['episode_id'],
                'pasien_id'  => $w['pasien_id'],
                'trans_id'   => $w['trans_id'],
                'trans_co'   => $w['trans_co'] ?? null,
                'rad_ke'     => $w['rad_ke'] ?? null,
            ])->get()->row_array();

        $versi = ((int)($rowVersi['v'] ?? 0)) + 1;


        // 3) Tandai versi lama sebagai bukan latest
        $this->db->set('is_latest', '0')
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);


        if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);
        if (!empty($w['rad_ke']))   $this->db->where('rad_ke',   $w['rad_ke']);

        $this->db->update($this->t_files);

        // 4) Insert file baru (versi baru)
        $this->db->insert($this->t_files, [
            'lokasi_id'       => $w['lokasi_id'] ?? null,
            'episode_id'  => $w['episode_id'],
            'pasien_id'   => $w['pasien_id'],
            'trans_id'    => $w['trans_id'],
            'trans_co'    => $w['trans_co'] ?? null,
            'rad_ke'      => $w['rad_ke'] ?? null,
            'file_name'   => $file['file_name'],
            'file_path'   => $file['file_path'],
            'mime_type'   => $file['mime_type'] ?? null,
            'file_size'   => $file['file_size'] ?? null,
            // 🔥 CATATAN ADMIN (BARU)
            'catatan'         => $file['catatan'] ?? null,
            // 🔥 CATATAN DOKTER (REVISI)
            'catatan_revisi'  => $catatanRevisi,

            'uploaded_by' => $username,
            'uploaded_at'     => date('Y-m-d H:i:s'),

            'versi'           => $versi,
            'is_latest'       => '1',
            'is_locked'       => '0',

            'last_updated_by' => $username,
            'last_updated_date' => date('Y-m-d H:i:s'),
        ]);

        // 5) Hitung total file aktif
        $cntRow = $this->db->select('count(*) as cnt')
            ->from($this->t_files)
            ->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) $cntRow = $cntRow->where('trans_co', $w['trans_co']);
        if (!empty($w['rad_ke']))   $cntRow = $cntRow->where('rad_ke',   $w['rad_ke']);

        $cnt = (int)($cntRow->get()->row()->cnt ?? 0);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            // return ['success' => false, 'message' => 'Gagal upload hasil radiologi.'];

            return [
                'success' => false,
                'message' => 'Gagal upload hasil radiologi.'
            ];
        }

        $this->db->trans_commit();
        // return ['success' => true, 'file_count' => $cnt];
        return [
            'success'    => true,
            'file_count' => $cnt,
            'versi'      => $versi
        ];
    }

    public function soft_delete_result_filexxx(int $fileId, string $username): array
    {
        $this->db->where('id', $fileId)->update($this->t_files, [
            'is_active' => '0',
        ]);
        return ['success' => true];
    }


    public function soft_delete_result_file(string $fileId, string $username): array
    {
        $fileId   = trim($fileId);
        $username = trim($username);

        if ($fileId === '') {
            return [
                'success' => false,
                'message' => 'ID file kosong'
            ];
        }

        if ($username === '') {
            $username = 'system';
        }

        $this->db->where('id', $fileId);
        $this->db->where('is_active', '1');
        $ok = $this->db->update($this->t_files, [
            'is_active'         => '0',
            'last_updated_by'   => $username,
            'last_updated_date' => date('Y-m-d H:i:s')
        ]);

        if (!$ok) {
            return [
                'success' => false,
                'message' => $this->db->error()['message'] ?? 'Gagal update data'
            ];
        }

        if ($this->db->affected_rows() < 1) {
            return [
                'success' => false,
                'message' => 'Data file tidak ditemukan atau sudah nonaktif'
            ];
        }

        return [
            'success' => true,
            'message' => 'File berhasil di-nonaktifkan'
        ];
    }

    public function update_status_to_wait_doctor(array $w, string $username): void
    {
        $upd = [
            'status'            => '9', // menunggu dokter
            'tgl_kirim'         => date('Y-m-d H:i:s'),
            'user_kirim'        => $username,
            'last_updated_by'   => $username,
            'last_updated_date' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('episode_id', $w['episode_id'])
            ->where('pasien_id',  $w['pasien_id'])
            ->where('trans_id',   $w['trans_id']);

        if (!empty($w['trans_co'])) $this->db->where('trans_co', $w['trans_co']);
        if (!empty($w['rad_ke']))   $this->db->where('rad_ke',   $w['rad_ke']);

        $this->db->update($this->t_worklist, $upd);
    }
}
