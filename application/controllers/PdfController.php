<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Endroid\QrCode\Builder\Builder;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\Writer\PngWriter;

class PdfController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');

            // $this->load->library('Tcpdf_library');
        }
    }


    public function rajalStruk($episode_id, $no_antrian, $tgl_berobat)
    {
        // $cek = $this->db->query("SELECT poli_id FROM pc01_keu_episode WHERE episode_id = ?", [$episode_id])->row();

        $cek = $this->db->query("SELECT episode_id, poli_id, rekanan_id, pasien_id, tgl_masuk
                                FROM pc01_keu_episode
                                WHERE episode_id = ?
                            ", [$episode_id])->row();


        if (!$cek) {
            echo "Data tidak ditemukan (Episode tidak valid).";
            return;
        }

        $poli_id = $cek->poli_id;

        // =========================================
        // 1. Ambil data umum pasien
        // =========================================

        $pasien = $this->db->query("SELECT pasien_id, nama, int_pasien_id
                                    FROM pc01_gen_pasien_ms
                                    WHERE pasien_id = ?
                                ", [$cek->pasien_id])->row();

        if (!$pasien) {
            echo "Data pasien tidak ditemukan.";
            return;
        }

        // =========================================
        // 2. Ambil header transaksi
        // =========================================
        $trans_hd = $this->db->query("SELECT *
                                        FROM pc01_keu_transaksi_hd
                                        WHERE episode_id = ?
                                        ORDER BY created_date DESC
                                        LIMIT 1
                                    ", [$episode_id])->row();


        // =========================================
        // 3. Ambil detail transaksi
        // =========================================
        $detail_trans = $this->db->query("SELECT 
                                                a.layan_id,
                                                b.nama_layan1,
                                                a.qty,
                                                COALESCE(a.harga_satuan,0) AS harga_satuan,
                                                COALESCE(a.disk_pct,0) AS disk_pct,
                                                COALESCE(a.disk_tot,0) AS disk_tot,
                                                COALESCE(a.harga_total,0) AS harga_total
                                            FROM pc01_keu_transctr_it a
                                            JOIN pc01_keu_layan_ms b ON a.layan_id = b.layan_id
                                            WHERE a.episode_id = ?
                                            AND a.aktif = '1'
                                            ORDER BY a.created_date ASC
                                        ", [$episode_id])->result();


        // =========================================
        // 4. Ambil data pembayaran
        // =========================================
        $bayar = $this->db->query("SELECT *
                                        FROM pc01_keu_episode_byr
                                        WHERE episode_id = ?
                                        ORDER BY created_date DESC
                                        LIMIT 1
                                    ", [$episode_id])->row();


        // =========================================
        // 5. CASE POLIKLINIK
        // =========================================
        $nama_poli = '';
        $nama_dokter = '';
        $jam_mulai = '-';
        $jam_selesai = '-';
        $antrian_text = $no_antrian ?: '-';
        $section_layanan = '';


        if ($poli_id != 'APS') {
            $result = $this->db->query("SELECT
                                        a.tanggal,
                                        a.antrian,
                                        a.jam_mulai,
                                        a.jam_selesai,
                                        d.nama AS nama_dokter,
                                        e.keterangan AS nama_poli
                                    FROM pc01_jadwal_antrian_temp a
                                    JOIN pc01_keu_episode c ON c.episode_id = a.episode_id
                                    JOIN pc01_med_dokter_ms d ON d.dokter_id = c.dokter_id
                                    JOIN pc01_med_poli_ms e ON e.poli_id = c.poli_id
                                    WHERE a.episode_id = ?
                                    ORDER BY a.created_date DESC
                                    LIMIT 1
                                ", [$episode_id])->row();



            if ($result) {
                $nama_poli   = $result->nama_poli ?? '-';
                $nama_dokter = $result->nama_dokter ?? '-';
                $jam_mulai   = $result->jam_mulai ?? '-';
                $jam_selesai = $result->jam_selesai ?? '-';
                $antrian_text = $result->antrian ?? $no_antrian;
            }
            $section_layanan = '
            <div style="font-size:13px; font-weight:bold; margin-top:3px;">' . $nama_poli . '</div>
            <div style="font-size:10px; margin-top:2px;">Dokter: <b>' . $nama_dokter . '</b></div>
            <div style="font-size:10px; margin-top:2px;"><b>-- YKI DKI JAKARTA --</b></div>
        ';
        } else {
            // =========================================
            // 6. CASE PENUNJANG APS
            // =========================================
            $sql_pem = "SELECT b.nama_layan1, b.kategori_id
                        FROM pc01_keu_transctr_it a
                        JOIN pc01_keu_layan_ms b ON a.layan_id = b.layan_id
                        WHERE a.episode_id = ?
                        AND a.aktif = '1'
                        AND b.kategori_id IN ('JKL-RAD','JKL-LAB')
                        ORDER BY a.created_date ASC
        ";

            $pemeriksaan = $this->db->query($sql_pem, [$episode_id])->result();

            $nama_pemeriksaan = "";
            if (!empty($pemeriksaan)) {
                foreach ($pemeriksaan as $p) {
                    $nama_pemeriksaan .= '&bull; ' . $p->nama_layan1 . '<br>';
                }
            } else {
                $nama_pemeriksaan = 'Pemeriksaan Penunjang';
            }

            $section_layanan = '
            <div style="font-size:12px; font-weight:bold; margin-top:3px;">Unit Penunjang</div>
            <div style="font-size:10px; margin-top:2px;">' . $nama_pemeriksaan . '</div>
            <div style="font-size:10px; margin-top:2px;"><b>-- YKI DKI JAKARTA --</b></div>
        ';
        }


        // =========================================
        // 7. Detail transaksi HTML
        // =========================================
        $detail_html = '';
        if (!empty($detail_trans)) {
            foreach ($detail_trans as $d) {
                $detail_html .= '
                <tr>
                    <td style="width:44%; text-align:left;">' . $d->nama_layan1 . '</td>
                    <td style="width:8%; text-align:center;">' . (int)$d->qty . '</td>
                    <td style="width:20%; text-align:right;">' . number_format($d->harga_satuan, 0, ',', '.') . '</td>
                    <td style="width:28%; text-align:right;">' . number_format($d->harga_total, 0, ',', '.') . '</td>
                </tr>
            ';
            }
        } else {
            $detail_html .= '
            <tr>
                <td colspan="4" style="text-align:center;">Tidak ada detail transaksi</td>
            </tr>
        ';
        }

        // =========================================
        // 8. Ringkasan pembayaran
        // =========================================
        $rekanan_id      = $cek->rekanan_id ?? '-';
        $is_umum         = strtoupper($rekanan_id) === 'UMUM';

        $subtotal        = $trans_hd->total_sub ?? 0;
        $diskon_persen   = $trans_hd->total_diskpct ?? 0;
        $diskon_nominal  = $trans_hd->total_diskon ?? 0;
        $total_harga     = $trans_hd->total_harga ?? 0;

        $bayar_pribadi   = $bayar->bayar_pribadi ?? 0;
        $bayar_rekanan   = $bayar->bayar_rekanan ?? 0;
        $total_kembali   = $bayar->total_kembali ?? 0;

        // metode bayar dari controller sebelumnya tidak tersimpan eksplisit di tabel
        // jadi sementara ditampilkan berdasarkan logika sederhana
        $metode_bayar = 'NON TUNAI';
        if ($is_umum) {
            if ((float)$total_kembali > 0) {
                $metode_bayar = 'CASH';
            } elseif ((float)$bayar_pribadi == 0 && (float)$total_harga > 0) {
                $metode_bayar = 'TUNDA';
            }
        } else {
            $metode_bayar = 'PENJAMIN';
        }

        if ($is_umum) {
            $payment_block = '
            <table border="0" cellspacing="0" cellpadding="2" style="width:100%; font-size:8px; border-top:1px solid #000; margin-top:6px;">
                <tr>
                    <td style="width:55%; text-align:left;">Subtotal</td>
                    <td style="width:45%; text-align:right;">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td style="text-align:left;">Diskon (' . rtrim(rtrim(number_format($diskon_persen, 2, '.', ''), '0'), '.') . '%)</td>
                    <td style="text-align:right;">Rp ' . number_format($diskon_nominal, 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td style="text-align:left;"><b>Total Bayar</b></td>
                    <td style="text-align:right;"><b>Rp ' . number_format($total_harga, 0, ',', '.') . '</b></td>
                </tr>
                <tr>
                    <td style="text-align:left;">Metode Bayar</td>
                    <td style="text-align:right;">' . $metode_bayar . '</td>
                </tr>
                <tr>
                    <td style="text-align:left;">Uang Dibayar</td>
                    <td style="text-align:right;">Rp ' . number_format($bayar_pribadi, 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td style="text-align:left;">Kembalian</td>
                    <td style="text-align:right;">Rp ' . number_format($total_kembali, 0, ',', '.') . '</td>
                </tr>
            </table>
        ';
        } else {
            $payment_block = '
            <table border="0" cellspacing="0" cellpadding="2" style="width:100%; font-size:8px; border-top:1px solid #000; margin-top:6px;">
                <tr>
                    <td style="width:55%; text-align:left;">Pembiayaan</td>
                    <td style="width:45%; text-align:right;"><b>' . $rekanan_id . '</b></td>
                </tr>
                <tr>
                    <td style="text-align:left;">Subtotal</td>
                    <td style="text-align:right;">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td style="text-align:left;">Diskon</td>
                    <td style="text-align:right;">Rp ' . number_format($diskon_nominal, 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td style="text-align:left;"><b>Total Dijamin</b></td>
                    <td style="text-align:right;"><b>Rp ' . number_format($total_harga, 0, ',', '.') . '</b></td>
                </tr>
            </table>
        ';
        }

        // =========================================
        // 9. QR Code
        // =========================================
        $this->load->library('Tcpdf_library');

        // $qrResult = Builder::create()
        //     ->data($episode_id)
        //     ->size(100)
        //     ->margin(0)
        //     ->build();

        $qrPath = FCPATH . 'assets/temp/qr_code_' . $episode_id . '.png';
        // $qrResult->saveToFile($qrPath);

        ob_start();

        $pdf = new Tcpdf_library();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('YKI DKI Jakarta');
        $pdf->SetTitle('Bukti Pendaftaran Layanan');
        $pdf->SetSubject('Bukti Pendaftaran Layanan');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage('P', [220, 80]);
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);
        $pdf->SetFont('helvetica', '', 8);


        $html = '
    <div style="text-align:center;">
        <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
            <tr>
                <td style="text-align:center;">
                    <img src="' . FCPATH . 'assets/img/yki_logo.png" width="70" height="45" />
                </td>
            </tr>
            <tr>
                <td style="font-size:11px; font-weight:bold; text-align:center;">BUKTI PENDAFTARAN LAYANAN</td>
            </tr>
            <tr>
                <td style="font-size:8px; text-align:center;">' . date('d-m-Y H:i') . '</td>
            </tr>
        </table>

        <table border="0" cellspacing="0" cellpadding="2" style="width:100%; font-size:8px; margin-top:6px;">
            <tr>
                <td style="width:30%; text-align:left;">No. Episode</td>
                <td style="width:5%;">:</td>
                <td style="width:65%; text-align:left;"><b>' . $episode_id . '</b></td>
            </tr>
            <tr>
                <td style="text-align:left;">Nama Pasien</td>
                <td>:</td>
                <td style="text-align:left;"><b>' . $pasien->nama . '</b></td>
            </tr>
            <tr>
                <td style="text-align:left;">No. RM</td>
                <td>:</td>
                <td style="text-align:left;"><b>' . $pasien->int_pasien_id . '</b></td>
            </tr>
            <tr>
                <td style="text-align:left;">Pembiayaan</td>
                <td>:</td>
                <td style="text-align:left;"><b>' . $rekanan_id . '</b></td>
            </tr>
            <tr>
                <td style="text-align:left;">Tanggal Layanan</td>
                <td>:</td>
                <td style="text-align:left;"><b>' . date('d-m-Y', strtotime($cek->tgl_masuk)) . '</b></td>
            </tr>
        </table>

        <div style="margin-top:6px; padding:5px; border:1px solid #000; text-align:center;">
            <div style="font-size:9px;">Nomor Antrian</div>
            <div style="font-size:22px; font-weight:bold;">' . $antrian_text . '</div>
            <div style="font-size:9px;">' . $jam_mulai . ' - ' . $jam_selesai . '</div>
        </div>

        <div style="margin-top:6px;">' . $section_layanan . '</div>

        <table border="0" cellspacing="0" cellpadding="2" style="width:100%; font-size:7.5px; margin-top:8px; border-top:1px solid #000; border-bottom:1px solid #000;">
            <tr style="font-weight:bold;">
                <td style="width:44%; text-align:left;">Layanan</td>
                <td style="width:8%; text-align:center;">Qty</td>
                <td style="width:20%; text-align:right;">Harga</td>
                <td style="width:28%; text-align:right;">Subtotal</td>
            </tr>
            ' . $detail_html . '
        </table>

        ' . $payment_block . '

        <div style="margin-top:8px; text-align:center;">
            <img src="' . $qrPath . '" width="52" height="52" />
            <div style="font-size:7px; margin-top:4px;">Scan / tunjukkan kode ini ke petugas</div>
        </div>

        <div style="font-size:7px; margin-top:8px; text-align:center;">
            Harap hadir 30 menit sebelum pelayanan dimulai.<br>
            Simpan bukti ini dan tunjukkan kepada petugas.
        </div>
    </div>';

        $pdf->writeHTML($html, true, false, true, false, '');

        if (file_exists($qrPath)) {
            unlink($qrPath);
        }

        ob_end_clean();
        $pdf->Output('bukti_pendaftaran.pdf', 'I');
    }

    public function cetakPasien($pasien_id)
    {
        // Ambil data pasien
        $this->load->model('PasienModel', 'pm');
        $pasien = $this->pm->getDetilPasienById($pasien_id);

        if (!$pasien) {
            show_error("Data pasien tidak ditemukan.");
            return;
        }

        // --- LOAD TCPDF ---
        $this->load->library('Tcpdf_library');
        $pdf = new Tcpdf_library();

        // Set halaman A4 Portrait
        $pdf->AddPage('P', 'A4');

        // Hilangkan header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Margin
        $pdf->SetMargins(10, 10, 10);

        // Font
        $pdf->SetFont('helvetica', '', 10);

        // ---------------------
        // OPTIONAL: QR CODE
        // ---------------------
        $qrResult = Builder::create()
            ->data($pasien_id)
            ->size(120)
            ->margin(0)
            ->build();

        $qrPath = FCPATH . 'assets/temp/qr_detail_pasien.png';
        $qrResult->saveToFile($qrPath);

        // ---------------------
        // HTML TEMPLATE CETAK
        // ---------------------

        $html = '<div style="text-align:center;"><br>
            <img src="' . FCPATH . 'assets/img/yki_logo.png" width="100">
            <h2 style="margin:5px 0;">Detail Pasien</h2>

        </div>

        <table border="0" cellspacing="0" cellpadding="6" width="100%">

            <tr>
                <td width="30%"><b>Nama Pasien</b></td>
                <td width="70%">: ' . $pasien->nama . '</td>
            </tr>

            <tr>
                <td><b>No. Rekam Medis</b></td>
                <td>: ' . $pasien->int_pasien_id . '</td>
            </tr>

            <tr>
                <td><b>No. Identitas (KTP)</b></td>
                <td>: ' . $pasien->no_identitas . '</td>
            </tr>

            <tr>
                <td><b>Tempat, Tanggal Lahir</b></td>
                <td>: ' . $pasien->tempat_lahir_txt . ', ' . date('d M Y', strtotime($pasien->tgl_lahir)) . '</td>
            </tr>

            <tr>
                <td><b>Jenis Kelamin</b></td>
                <td>: ' . ($pasien->sex_id == "L" ? "Laki-Laki" : "Perempuan") . '</td>
            </tr>

            <tr>
                <td><b>No. HP</b></td>
                <td>: ' . $pasien->no_selular . '</td>
            </tr>

            <tr><td colspan="2"><hr></td></tr>

            <tr>
                <td><b>Alamat KTP</b></td>
                <td>: ' . $pasien->alamat1 . ', RT ' . $pasien->rt . '/RW ' . $pasien->rw . ',
                    ' . $pasien->kelurahan_txt . ', ' . $pasien->kecamatan_txt . ',
                    ' . $pasien->kabupaten_txt . ', ' . $pasien->propinsi_txt . '</td>
            </tr>

            <tr><td colspan="2"><hr></td></tr>

            <tr>
                <td><b>Penjamin</b></td>
                <td>: ' . $pasien->rekanan_txt . '</td>
            </tr>';

        if (!empty($pasien->no_kartuprov)) {
            $html .= '
            <tr>
                <td><b>No BPJS</b></td>
                <td>: ' . $pasien->no_kartuprov . '</td>
            </tr>';
        }

        $html .= '
            <tr><td colspan="2"><hr></td></tr>

            <tr>
                <td><b>PJP</b></td>
                <td>: ' . $pasien->pjp_nama . '</td>
            </tr>

            <tr>
                <td><b>No HP PJP</b></td>
                <td>: ' . $pasien->pjp_hp . '</td>
            </tr>

            <tr>
                <td><b>Hubungan</b></td>
                <td>: ' . $pasien->pjp_hubungan_txt . '</td>
            </tr>
        </table>

        <br><br>

        <div style="text-align:center;">
            <img src="' . $qrPath . '" width="100"><br>
            <small>QR Detail Pasien</small>
        </div>
        ';

        // Tulis HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Hapus QR sementara
        unlink($qrPath);

        // Output PDF langsung ke browser
        $pdf->Output('Detail_Pasien_' . $pasien->nama . '(' . $pasien->int_pasien_id . ').pdf', 'I');
    }


    public function radResultxxxx($episode_id, $pasien_id, $trans_id, $trans_co = null)
    {
        $this->load->model('RadDoctorModel', 'rdm');

        $ref = [
            'episode_id' => $episode_id,
            'pasien_id'  => $pasien_id,
            'trans_id'   => $trans_id,
            'trans_co'   => $trans_co
        ];

        $data = $this->rdm->get_full_result($ref);

        if (!$data) {
            show_error('Data hasil radiologi tidak ditemukan');
            return;
        }

        $result   = $data['result'];
        $versions = $data['versions'];
        $files    = $data['files'];

        // 🔥 LOAD TCPDF
        $this->load->library('Tcpdf_library');



        $pdf = new Tcpdf_library();

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // 🔥 margin sesuai permintaan
        $pdf->SetMargins(15, 35, 15); // kiri, atas (3.5cm), kanan
        $pdf->SetAutoPageBreak(true, 20);


        // $pdf->AddPage();
        $pdf->AddPage('P', 'A4');





        $pdf->SetFont('helvetica', '', 10);

        // ================= HTML =================

        $hasil = trim($result['hasil_bacaan'] ?? '');

        // 🔥 hapus karakter aneh
        $hasil = str_replace(["\r", "\t"], '', $hasil);

        // // 🔥 hapus tanda petik & koma berlebih
        $hasil = preg_replace("/[',]+/", '', $hasil);

        // // 🔥 rapikan line break
        // $hasil = preg_replace("/\n+/", "\n", $hasil);



        $html = '
                <style>
                    .title { font-size:13px; font-weight:bold; }
                    .label { width:90px; font-weight:bold; }
                    .colon { width:10px; }
                    .section-title {
                        font-weight:bold;
                        margin-top:10px;
                        margin-bottom:5px;
                        border-bottom:1px solid #ccc;
                        padding-bottom:3px;
                    }
                    .content-box {
                        border:1px solid #e0e0e0;
                        padding:8px;
                        border-radius:4px;
                        background:#fafafa;
                    }
                </style>

                <div style="font-family:helvetica; font-size:11px; line-height:1.6;">

                    <div style="text-align:right; font-size:10px;">
                        Jakarta, ' . date('d F Y') . '
                    </div>

                    <br>

                    <div>Teman sejawat yang terhormat,</div>
                    <div>Kami laporkan hasil pemeriksaan :</div>

                    <br>

                    <table cellpadding="3">
                        <tr>
                            <td class="label">Nama</td>
                            <td class="colon">:</td>
                            <td><b>' . $result['nama_pasien'] . '</b></td>
                        </tr>
                        <tr>
                            <td class="label">Umur</td>
                            <td>:</td>
                            <td>' . $result['umur_tahun'] . ' Tahun</td>
                        </tr>
                    </table>

                    <br>

                    <div class="section-title">
                        ' . strtoupper($result['nama_pemeriksaan'] ?? 'PEMERIKSAAN RADIOLOGI') . '
                    </div>

                    <div class="content-box" style="text-align:justify;">
                        ' . nl2br('<br>' . $hasil) . '
                    </div>

                    <br>

                    
                    <br><br>

                    <div style="text-align:right;">
                        <div>Salam sejawat,</div>

                        <br>

                        <div style="font-weight:bold;">
                            ' . $result['nama_dokter_radiologi'] . '
                        </div>
                        <div style="font-size:10px;">Sp.Rad</div>
                    </div>

                    <br><br>

                    <div class="section-title">Riwayat Versi</div>
                ';




        foreach ($versions as $v) {
            $html .= '
        <div style="margin-bottom:6px; padding:6px; border:1px dashed #ccc;">
        <b>Versi ' . ($v['file_version'] ?? '-') . '</b> 
        <span style="color:' . ($v['status_lap'] == 'F' ? 'green' : 'orange') . ';">
            (' . ($v['status_lap'] == 'F' ? 'Final' : 'Draft') . ')
        </span>
        <br>
        <span style="font-size:10px;">' . $v['created_date'] . '</span>
        </div>
        ';
        }

        $html .= '<hr><h4>File Lampiran</h4>';



        foreach ($files as $f) {

            $filePath = FCPATH . $f['file_path'];
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png']) && file_exists($filePath)) {

                $html .= '
        <div style="margin-top:12px; text-align:center; border:1px solid #ddd; padding:8px; border-radius:5px;">
            <img src="' . $filePath . '" style="max-width:480px; border-radius:4px;">
            <br>
            <span style="font-size:9px; color:#555;">' . $f['file_name'] . '</span>
        </div>
        ';
            } else {
                $html .= '
        <div style="margin-top:5px;">
            • ' . $f['file_name'] . '
        </div>
        ';
            }
        }

        // $html .= '<br><div class="section-title">File Lampiran</div>';


        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('hasil_radiologi.pdf', 'I');
    }

    public function radResult($episode_id = null, $pasien_id = null, $trans_id = null, $trans_co = null)
    {
        $this->load->model('RadDoctorModel', 'rdm');

        // Bisa dari URL segment atau query string
        $episode_id = $episode_id ?: $this->input->get('episode_id', true);
        $pasien_id  = $pasien_id  ?: $this->input->get('pasien_id', true);
        $trans_id   = $trans_id   ?: $this->input->get('trans_id', true);
        $trans_co   = $trans_co;
        if ($trans_co === null) {
            $trans_co = $this->input->get('trans_co', true);
        }

        $rad_ke = $this->input->get('rad_ke', true);

        $paper = strtoupper(trim((string)$this->input->get('paper', true)));
        if (!in_array($paper, ['A4', 'F4'], true)) {
            $paper = 'A4';
        }

        if (!$episode_id || !$pasien_id || !$trans_id) {
            show_error('Parameter hasil radiologi tidak lengkap');
            return;
        }

        $ref = [
            'episode_id' => $episode_id,
            'pasien_id'  => $pasien_id,
            'trans_id'   => $trans_id,
            'trans_co'   => $trans_co,
            'rad_ke'     => $rad_ke,
        ];

        $data = $this->rdm->get_full_result($ref);

        if (!$data || empty($data['result'])) {
            show_error('Data hasil radiologi tidak ditemukan');
            return;
        }

        $result = $data['result'];

        $this->load->library('Tcpdf_library');

        $pdf = new Tcpdf_library();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('YKI DKI Jakarta');
        $pdf->SetTitle('Hasil Radiologi');

        // F4 Indonesia umum: 210 x 330 mm
        $paperSize = ($paper === 'F4') ? [210, 330] : 'A4';

        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(true, 22);
        $pdf->AddPage('P', $paperSize);
        $pdf->SetFont('helvetica', '', 10);

        $e = function ($v) {
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        };

        $hasil = trim((string)($result['hasil_bacaan'] ?? ''));
        $hasil = str_replace(["\r", "\t"], ['', ''], $hasil);
        $hasil = preg_replace("/\n{3,}/", "\n\n", $hasil);

        $namaPasien = $e($result['nama_pasien'] ?? '-');
        $umur       = $e($result['umur_tahun'] ?? '-');
        $noRm       = $e($result['no_rm'] ?? '-');
        $jk         = $e($result['jenis_kelamin'] ?? '-');
        $pemeriksaan = strtoupper($e($result['nama_pemeriksaan'] ?? 'PEMERIKSAAN RADIOLOGI'));

        $namaDokter = trim((string)($result['nama_dokter_radiologi'] ?? ''));
        if ($namaDokter === '') {
            $namaDokter = 'Dokter Radiologi';
        }

        $html = '
    <style>
        .page {
            font-family: helvetica;
            font-size: 10.5px;
            line-height: 1.55;
            color: #111827;
        }

        .date {
            text-align: right;
            font-size: 10px;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .identity td {
            font-size: 10.5px;
            padding: 2px 4px;
        }

        .label {
            width: 85px;
            font-weight: bold;
        }

        .colon {
            width: 8px;
        }

        .result-box {
            border: 1px solid #d1d5db;
            padding: 10px;
            background-color: #ffffff;
            text-align: justify;
        }
    </style>

    <div class="page">
        <div class="date">Jakarta, ' . date('d F Y') . '</div>

        <br>

        <div>Teman sejawat yang terhormat,</div>
        <div>Kami laporkan hasil pemeriksaan:</div>

        <br>

        <table class="identity" cellpadding="2">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td><b>' . $namaPasien . '</b></td>
            </tr>
            <tr>
                <td class="label">No. RM</td>
                <td class="colon">:</td>
                <td>' . $noRm . '</td>
            </tr>
            <tr>
                <td class="label">Umur</td>
                <td class="colon">:</td>
                <td>' . $umur . ' Tahun</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td>' . $jk . '</td>
            </tr>
        </table>

        <br>

        <div class="title">' . $pemeriksaan . '</div>

        <div class="result-box">
            ' . nl2br($e($hasil)) . '
        </div>
    </div>
    ';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Pastikan area tanda tangan tidak terpotong.
        // Jika sisa halaman kurang dari ±55 mm, pindah halaman baru.
        if ($pdf->GetY() > ($pdf->getPageHeight() - 70)) {
            $pdf->AddPage('P', $paperSize);
        }

        $pdf->Ln(10);

        $signatureHtml = '
    <table cellpadding="2" cellspacing="0" width="100%">
        <tr>
            <td width="60%"></td>
            <td width="40%" align="center">
                Salam sejawat,<br><br><br><br>
                <b>' . $e($namaDokter) . '</b><br>
                <span style="font-size:10px;">Dokter Spesialis Radiologi</span>
            </td>
        </tr>
    </table>
    ';

        $pdf->writeHTML($signatureHtml, true, false, true, false, '');

        $filename = 'hasil_radiologi_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $episode_id) . '_' . $paper . '.pdf';

        $pdf->Output($filename, 'I');
    }









    public function papResultxxxxxx()
    {
        $this->load->model('LabModel', 'lm');

        $id = trim((string)$this->input->get('id', true));

        if ($id === '') {
            show_error('Parameter hasil PAP tidak lengkap.');
            return;
        }

        $w = $this->lm->get_one($id);

        if (!$w) {
            show_error('Data worklist PAP tidak ditemukan.');
            return;
        }

        $formCode = 'PAP_ANATOMIK';
        $form = $this->lm->get_form_with_values($formCode, $w);

        $worklist = $form['worklist'] ?? $w;
        $sections = $form['sections'] ?? [];
        $values   = $form['values'] ?? [];
        $result   = $form['result'] ?? [];

        $statusAdmin  = strtoupper((string)($result['status_admin'] ?? $worklist['status_admin'] ?? ''));
        $statusDokter = strtoupper((string)($result['status_dokter'] ?? $worklist['status_dokter'] ?? ''));

        if ($statusAdmin !== 'F' || $statusDokter !== 'A') {
            show_error('Hasil belum final dokter. PDF hanya bisa dicetak jika status_admin = F dan status_dokter = A.');
            return;
        }

        $e = function ($v) {
            return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
        };

        $getVal = function ($code) use ($values) {
            $v = $values[$code] ?? '';

            if (is_array($v)) {
                $v = array_filter($v, function ($x) {
                    return trim((string)$x) !== '';
                });

                return trim(implode(', ', $v));
            }

            return trim((string)$v);
        };

        $isChecked = function ($code) use ($values) {
            $v = $values[$code] ?? '';

            if (is_array($v)) {
                return count(array_filter($v, function ($x) {
                    return trim((string)$x) !== '';
                })) > 0;
            }

            $v = strtoupper(trim((string)$v));

            if ($v === '' || $v === 'N' || $v === 'T' || $v === '0' || $v === 'FALSE') {
                return false;
            }

            return true;
        };

        $fmtDate = function ($v) {
            if (!$v) return '';
            $t = strtotime((string)$v);
            return $t ? date('d/m/Y', $t) : (string)$v;
        };

        // $namaPasien = $e($worklist['nama_pasien'] ?? '-');
        // $umur       = $e($worklist['umur'] ?? '');
        // $noRm       = $e($worklist['no_rm'] ?? $worklist['pasien_id'] ?? '-');
        // $noSitologi = $e($worklist['no_sitologi'] ?? '-');


        $namaPasienRaw = trim((string)($worklist['nama_pasien'] ?? '-'));
        $noRmRaw       = trim((string)($worklist['no_rm'] ?? $worklist['pasien_id'] ?? ''));

        $namaPasien = $e(
            $namaPasienRaw . ($noRmRaw !== '' ? ' (' . $noRmRaw . ')' : '')
        );

        $umur       = $e($worklist['umur'] ?? '');
        $noSitologi = $e($worklist['no_sitologi'] ?? '-');





        $namaSuami  = $e($worklist['nama_pasangan'] ?? '');
        $alamat     = $e($worklist['alamat1'] ?? '');
        $dokter     = $e($worklist['nama_dokter'] ?? '');
        $asal       = $e($worklist['nama_poli'] ?? '');

        $tglDiterima = $fmtDate($result['created_date'] ?? $worklist['created_date'] ?? '');
        $tglPeriksa  = $fmtDate(
            $getVal('tgl_periksa')
                ?: ($result['dokter_acc_date'] ?? $worklist['tgl_skrinning_dokter'] ?? date('Y-m-d'))
        );

        $dokterLabNama = trim((string)($result['dokter_user_id']
            ?? $worklist['dokter_user_id']
            ?? ''
        ));

        if ($dokterLabNama !== '') {
            $u = $this->db->query("
                SELECT nama
                FROM pcare_manager.pc01_gen_user_data
                WHERE user_id = ?
                LIMIT 1
            ", [$dokterLabNama])->row_array();

            if (!empty($u['nama'])) {
                $dokterLabNama = $u['nama'];
            }
        }

        if ($dokterLabNama === '') {
            $dokterLabNama = 'Dokter Pemeriksa';
        }

        $dokterLabNama = $e($dokterLabNama);



        $logoPath = FCPATH . 'assets/img/yki_logo.png';
        $logoHtml = file_exists($logoPath)
            ? '<img src="' . $logoPath . '" width="58" />'
            : '';

        // ============================
        // BUILD ISIAN HASIL - COMPACT & RAPI
        // ============================
        $hasilRows = [];

        $skipFieldCodes = [
            'tgl_periksa',
            'tgl_diterima',
            'tanggal_periksa',
            'tanggal_diterima',
        ];

        foreach ($sections as $sec) {
            $sectionLabel = trim((string)($sec['section_label'] ?? $sec['section_code'] ?? '-'));
            $sectionKey = strtolower(trim($sectionLabel));

            if (in_array($sectionKey, ['dokter pengirim', 'dokter_pengirim'], true)) {
                continue;
            }



            $fields = $sec['fields'] ?? [];

            $answers = [];

            foreach ($fields as $f) {
                $code  = $f['field_code'] ?? '';
                $label = trim((string)($f['field_label'] ?? $code));
                $type  = strtolower((string)($f['field_type'] ?? 'text'));

                if ($code === '') {
                    continue;
                }

                if (in_array(strtolower($code), $skipFieldCodes, true)) {
                    continue;
                }

                if ($type === 'checkbox') {
                    if ($isChecked($code)) {
                        $answers[] = $label;
                    }
                } else {
                    $val = $getVal($code);

                    if ($val !== '' && strtoupper($val) !== 'N' && strtoupper($val) !== 'T') {
                        $answers[] = $val;
                    }
                }
            }

            if (!empty($answers)) {
                $hasilRows[] = [
                    'section' => $sectionLabel,
                    'answers' => $answers,
                ];
            }
        }


        $orderMap = [
            'kelayakan spesimen'       => 1,
            'interpretasi / kategori'  => 2,
            'interpretasi/kategori'    => 2,
            'rincian'                  => 3,
            'temuan organisme'         => 4,
            'anjuran'                  => 5,
        ];


        usort($hasilRows, function ($a, $b) use ($orderMap) {
            $ka = strtolower(trim((string)$a['section']));
            $kb = strtolower(trim((string)$b['section']));

            $oa = $orderMap[$ka] ?? 99;
            $ob = $orderMap[$kb] ?? 99;

            if ($oa === $ob) {
                return strcmp($ka, $kb);
            }

            return $oa <=> $ob;
        });



        $hasilHtml = '';

        if (!empty($hasilRows)) {
            $hasilHtml .= '<table class="result-table" cellpadding="0" cellspacing="0" width="100%">';

            foreach ($hasilRows as $row) {
                $hasilHtml .= '
        <tr>
        <td class="result-label" width="28%">' . $e($row['section']) . '</td>
        <td class="result-colon" width="3%">:</td>
        <td class="result-answer" width="69%">
            ';

                foreach ($row['answers'] as $ans) {
                    // $hasilHtml .= '<div class="answer-item">- ' . $e($ans) . '</div>';
                    $hasilHtml .= '<div class="answer-item">' . $e($ans) . '</div>';
                }

                $hasilHtml .= '
                    </td>
                </tr>
            ';
            }

            $hasilHtml .= '</table>';
        } else {
            $hasilHtml = '
            <div class="empty-result">
                Belum ada isian hasil yang dapat ditampilkan.
            </div>
        ';
        }

        $html = '
        <style>
            .page {
            font-family: helvetica;
            font-size: 8.4px;
            color: #111;
            line-height: 1.15;
        }

        .header-table {
            width: 100%;
            padding-bottom: 2px;
        }

        .logo-cell {
            text-align: left;
            vertical-align: middle;
            padding-left: 4px;
        }

        .title-cell {
            text-align: left;
            vertical-align: middle;
            padding-left: 4px;
        }

        .brand-title {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.2px;
            line-height: 1.15;
            text-align: left;
        }

        .addr-row {
            text-align: center;
            padding-top: 2px;
        }

        .addr {
            font-size: 7.4px;
            margin-top: 2px;
            margin-bottom: 2px;
            line-height: 1.1;
            text-align: center;
        }

            .date-box {
                border: 0.8px solid #222;
                font-size: 7.8px;
                text-align: center;
                height: 18px;
                padding: 2px;
                line-height: 1.1;
            }

        .main-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            border-top: 0.8px solid #111;
            border-bottom: 0.8px solid #111;
            padding-top: 4px;
            padding-bottom: 4px;
            line-height: 1.2;
        }
            .meta {
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .meta td {
            font-size: 8.4px;
            padding: 2px 2px;
            vertical-align: top;
            line-height: 1.2;
        }

        .meta-label {
            width: 92px;
            font-weight: bold;
        }

        .meta-colon {
            width: 6px;
            text-align: center;
        }

        .meta-value {
            border-bottom: none;
        }

        .sitologi-box {
            border: 0.8px solid #222;
            text-align: center;
            font-size: 7.8px;
            padding: 3px;
            line-height: 1.1;
        }

        .result-table {
            margin-top: 0;
        }

        .result-table td {
            font-size: 8.5px;
            vertical-align: top;
            line-height: 1.32;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .result-label {
            font-weight: bold;
            padding-right: 4px;
        }

        .result-colon {
            text-align: center;
        }

        .result-answer {
            padding-left: 4px;
        }

        .answer-item {
            margin: 0 0 2px 0;
            padding: 0;
        }

        .footer-table {
            margin-top: 18px;
        }

        .footer-table td {
            font-size: 7.6px;
            vertical-align: top;
            line-height: 1.12;
        }

        .sign-box {
            border: 0.8px solid #333;
            width: 120px;
            height: 28px;
            text-align: center;
            padding-top: 4px;
            font-size: 7.8px;
            line-height: 1.1;
        }

        .doctor-name {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            border-bottom: 0.5px dotted #333;
            padding-top: 18px;
        }

        .note {
            font-size: 7.2px;
            color: #555;
            line-height: 1.1;
        }
            
            </style>

            <div class="page">

        <table class="header-table" cellpadding="1" cellspacing="0" width="100%">
            <tr>
                <td width="14%" class="logo-cell">
                    ' . $logoHtml . '
                </td>
                <td width="56%" class="title-cell">
                    <div class="brand-title">YAYASAN KANKER INDONESIA</div>
                    <div class="brand-title">PROVINSI DKI JAKARTA</div>
                </td>
                <td width="30%" align="right">
                    <table cellpadding="1" cellspacing="0" width="100%">
                        <tr>
                            <td class="date-box">Tanggal diterima</td>
                        </tr>
                        <tr>
                            <td class="date-box"><b>' . $e($tglDiterima) . '</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="addr-row">
                    <div class="addr">
                        Jl. Baru Sunter Permai Raya No. 2 Telp. 021-6509144 Fax 021-6507748 | Call Centre 021-6507746
                    </div>
                </td>
            </tr>
        </table>

            <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td height="8"></td>
            </tr>
        </table>

            <div class="main-title">
                HASIL PEMERIKSAAN PAP TEST LABORATORIUM PATOLOGI ANATOMIK
            </div>

            <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td height="17"></td>
            </tr>
        </table>

        <table class="meta" cellpadding="1" cellspacing="0" width="100%">
            <tr>
                <td width="72%" valign="top">
                    <table cellpadding="1" cellspacing="0" width="100%">
                        <tr>
                            <td class="meta-label">NAMA PASIEN</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value"><b>' . $namaPasien . '</b></td>
                        </tr>
                        <tr>
                            <td class="meta-label">UMUR</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value">' . $umur . ' Tahun</td>
                        </tr>
                        <tr>
                            <td class="meta-label">NAMA SUAMI</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value">' . $namaSuami . '</td>
                        </tr>
                        <tr>
                            <td class="meta-label">ALAMAT</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value">' . $alamat . '</td>
                        </tr>
                        <tr>
                            <td class="meta-label">DOKTER PENGIRIM</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value">' . $dokter . '</td>
                        </tr>
                        <tr>
                            <td class="meta-label">ALAMAT</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value">' . $asal . '</td>
                        </tr>
                    </table>
                </td>

                <td width="28%" valign="top" align="right">
                    <table cellpadding="1" cellspacing="0" width="80%">
                        <tr>
                            <td class="sitologi-box">No. Sitologi :</td>
                        </tr>
                        <tr>
                            <td class="sitologi-box"><b>' . $noSitologi . '</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td height="34"></td>
            </tr>
        </table>

        ' . $hasilHtml . '

        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td height="24"></td>
            </tr>
        </table>

        ' . $hasilHtml . '

        <table class="footer-table" cellpadding="1" cellspacing="0" width="100%">
            <tr>
                <td width="55%">
                    <div class="note">
                        Dicetak otomatis dari sistem setelah final dokter.<br>
                        Status: Admin Final / Dokter ACC
                    </div>
                </td>

                <td width="45%" align="right">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="right">
                                <table cellpadding="0" cellspacing="0" width="120">
                                    <tr>
                                        <td class="sign-box">
                                            Tanggal Periksa<br>
                                            <b>' . $e($tglPeriksa) . '</b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td height="30"></td>
                        </tr>

                        <tr>
                            <td align="right">
                                <table cellpadding="0" cellspacing="0" width="150">
                                    <tr>
                                        <td class="doctor-name">
                                            ' . $dokterLabNama . '
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        </div>
            ';

        $this->load->library('Tcpdf_library');

        $pdf = new Tcpdf_library();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('YKI DKI Jakarta');
        $pdf->SetTitle('Hasil PAP Test');

        // 6.5 inch x 8.5 inch = 165.1 mm x 215.9 mm
        $paperSize = [165.1, 215.9];

        $pdf->SetMargins(4.5, 4.5, 4.5);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage('P', $paperSize);

        $pdf->SetFont('helvetica', '', 8.4);
        $pdf->setCellHeightRatio(1.05);

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'hasil_pap_' . preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '_',
            (string)($worklist['no_sitologi'] ?? $worklist['episode_id'] ?? 'print')
        ) . '.pdf';

        $pdf->Output($filename, 'I');
    }


    public function papResult()
    {
        $this->load->model('LabModel', 'lm');

        $id = trim((string)$this->input->get('id', true));

        if ($id === '') {
            show_error('Parameter hasil PAP tidak lengkap.');
            return;
        }

        $w = $this->lm->get_one($id);

        if (!$w) {
            show_error('Data worklist PAP tidak ditemukan.');
            return;
        }

        $formCode = 'PAP_ANATOMIK';
        $form = $this->lm->get_form_with_values($formCode, $w);

        $worklist = $form['worklist'] ?? $w;
        $sections = $form['sections'] ?? [];
        $values   = $form['values'] ?? [];
        $result   = $form['result'] ?? [];

        $statusAdmin  = strtoupper((string)($result['status_admin'] ?? $worklist['status_admin'] ?? ''));
        $statusDokter = strtoupper((string)($result['status_dokter'] ?? $worklist['status_dokter'] ?? ''));

        if ($statusAdmin !== 'F' || $statusDokter !== 'A') {
            show_error('Hasil belum final dokter. PDF hanya bisa dicetak jika status_admin = F dan status_dokter = A.');
            return;
        }

        $e = function ($v) {
            return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
        };

        $getVal = function ($code) use ($values) {
            $v = $values[$code] ?? '';

            if (is_array($v)) {
                $v = array_filter($v, function ($x) {
                    return trim((string)$x) !== '';
                });

                return trim(implode(', ', $v));
            }

            return trim((string)$v);
        };

        $isChecked = function ($code) use ($values) {
            $v = $values[$code] ?? '';

            if (is_array($v)) {
                return count(array_filter($v, function ($x) {
                    return trim((string)$x) !== '';
                })) > 0;
            }

            $v = strtoupper(trim((string)$v));

            return !in_array($v, ['', 'N', 'T', '0', 'FALSE'], true);
        };

        $fmtDate = function ($v) {
            if (!$v) return '';
            $t = strtotime((string)$v);
            return $t ? date('d/m/Y', $t) : (string)$v;
        };

        $namaPasienRaw = trim((string)($worklist['nama_pasien'] ?? '-'));
        $noRmRaw       = trim((string)($worklist['no_rm'] ?? $worklist['pasien_id'] ?? ''));

        $namaPasien = $namaPasienRaw . ($noRmRaw !== '' ? ' (' . $noRmRaw . ')' : '');

        $tglDiterima = $fmtDate($result['created_date'] ?? $worklist['created_date'] ?? '');
        $tglPeriksa  = $fmtDate(
            $getVal('tgl_periksa')
                ?: ($result['dokter_acc_date'] ?? $worklist['tgl_skrinning_dokter'] ?? date('Y-m-d'))
        );

        // Ambil nama dokter lab
        $dokterLabNama = trim((string)($result['dokter_user_id']
            ?? $worklist['dokter_user_id']
            ?? ''
        ));

        if ($dokterLabNama !== '') {
            $u = $this->db->query("
            SELECT nama
            FROM pcare_manager.pc01_gen_user_data
            WHERE user_id = ?
            LIMIT 1
        ", [$dokterLabNama])->row_array();

            if (!empty($u['nama'])) {
                $dokterLabNama = $u['nama'];
            }
        }

        if ($dokterLabNama === '') {
            $dokterLabNama = 'Dokter Pemeriksa';
        }

        // Susun hasil berdasarkan urutan tetap
        $hasilMap = [
            'kelayakan spesimen'      => [],
            'interpretasi / kategori' => [],
            'interpretasi/kategori'   => [],
            'rincian'                 => [],
            'temuan organisme'        => [],
            'anjuran'                 => [],
        ];

        $skipFieldCodes = [
            'tgl_periksa',
            'tgl_diterima',
            'tanggal_periksa',
            'tanggal_diterima',
        ];

        foreach ($sections as $sec) {
            $sectionLabel = trim((string)($sec['section_label'] ?? $sec['section_code'] ?? '-'));
            $sectionKey   = strtolower(trim($sectionLabel));

            if (in_array($sectionKey, ['dokter pengirim', 'dokter_pengirim'], true)) {
                continue;
            }

            $answers = [];

            foreach (($sec['fields'] ?? []) as $f) {
                $code  = $f['field_code'] ?? '';
                $label = trim((string)($f['field_label'] ?? $code));
                $type  = strtolower((string)($f['field_type'] ?? 'text'));

                if ($code === '' || in_array(strtolower($code), $skipFieldCodes, true)) {
                    continue;
                }

                if ($type === 'checkbox') {
                    if ($isChecked($code)) {
                        $answers[] = $label;
                    }
                } else {
                    $val = $getVal($code);

                    if ($val !== '' && strtoupper($val) !== 'N' && strtoupper($val) !== 'T') {
                        $answers[] = $val;
                    }
                }
            }

            if (!empty($answers)) {
                $hasilMap[$sectionKey] = $answers;
            }
        }

        $hasilRows = [
            [
                'section' => 'Kelayakan Spesimen',
                'answers' => $hasilMap['kelayakan spesimen'] ?? [],
            ],
            [
                'section' => 'Interpretasi / Kategori',
                'answers' => !empty($hasilMap['interpretasi / kategori'])
                    ? $hasilMap['interpretasi / kategori']
                    : ($hasilMap['interpretasi/kategori'] ?? []),
            ],
            [
                'section' => 'Rincian',
                'answers' => $hasilMap['rincian'] ?? [],
            ],
            [
                'section' => 'Temuan Organisme',
                'answers' => $hasilMap['temuan organisme'] ?? [],
            ],
            [
                'section' => 'Anjuran',
                'answers' => $hasilMap['anjuran'] ?? [],
            ],
        ];

        $logoPath = FCPATH . 'assets/img/yki_logo.png';

        $data = [
            'logoPath'       => file_exists($logoPath) ? $logoPath : '',
            'tglDiterima'    => $tglDiterima,
            'tglPeriksa'     => $tglPeriksa,
            'noSitologi'     => $worklist['no_sitologi'] ?? '-',
            'namaPasien'     => $namaPasien,
            'umur'           => $worklist['umur'] ?? '',
            'namaSuami'      => $worklist['nama_pasangan'] ?? '',
            'alamat'         => $worklist['alamat1'] ?? '',
            'dokterPengirim' => $worklist['nama_dokter'] ?? '',
            'alamatPengirim' => $worklist['nama_poli'] ?? '',
            'dokterLabNama'  => $dokterLabNama,
            'hasilRows'      => $hasilRows,
            'e'              => $e,
        ];

        $cssPath = APPPATH . 'views/laboratorium/pap_print_pdf.css';
        $css = file_exists($cssPath) ? file_get_contents($cssPath) : '';

        $html = '<style type="text/css">' . $css . '</style>';
        $html .= $this->load->view('laboratorium/pap_print_pdf', $data, true);

        $this->load->library('Tcpdf_library');

        $pdf = new Tcpdf_library();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('YKI DKI Jakarta');
        $pdf->SetTitle('Hasil PAP Test');

        $paperSize = [165.1, 215.9];

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage('P', $paperSize);

        $pdf->SetFont('helvetica', '', 8.8);
        $pdf->setCellHeightRatio(1.08);

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'hasil_pap_' . preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '_',
            (string)($worklist['no_sitologi'] ?? $worklist['episode_id'] ?? 'print')
        ) . '.pdf';

        $pdf->Output($filename, 'I');
    }
}
