<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kwitansi <?= html_escape($kwitansi['bayar_id'] ?? '') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .wrap {
            width: 760px;
            margin: 24px auto;
        }

        .head {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        h2 {
            margin: 0;
            font-size: 20px;
        }

        .muted {
            color: #6b7280;
        }

        .box {
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
        }

        th {
            background: #f9fafb;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }

            .wrap {
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="head">
            <div>
                <h2>KWITANSI PEMBAYARAN</h2>
                <div class="muted">Kasir Umum YKI</div>
            </div>
            <div class="right">
                <strong><?= html_escape($kwitansi['bayar_id'] ?? '-') ?></strong><br>
                <span class="muted"><?= date('d-m-Y H:i', strtotime($kwitansi['tgl_lunas'] ?? date('Y-m-d H:i:s'))) ?></span>
            </div>
        </div>

        <div class="box">
            <table>
                <tr>
                    <td width="140">Nama Pasien</td>
                    <td>: <?= html_escape($kwitansi['nama_pasien'] ?? '-') ?></td>
                    <td width="120">No. RM</td>
                    <td>: <?= html_escape($kwitansi['no_rm'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Episode</td>
                    <td>: <?= html_escape($kwitansi['episode_id'] ?? '-') ?></td>
                    <td>Poli</td>
                    <td>: <?= html_escape($kwitansi['poli'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Dokter</td>
                    <td colspan="3">: <?= html_escape($kwitansi['dokter'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Tarif</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($kwitansi['items'] ?? []) as $it) : ?>
                    <tr>
                        <td><?= html_escape($it['kategori'] ?? '-') ?></td>
                        <td><?= html_escape($it['nama_item'] ?? '-') ?></td>
                        <td class="right"><?= number_format((float)($it['qty'] ?? 0), 0, ',', '.') ?></td>
                        <td class="right">Rp <?= number_format((float)($it['tarif'] ?? 0), 0, ',', '.') ?></td>
                        <td class="right">Rp <?= number_format((float)($it['harga_total'] ?? 0), 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="box" style="margin-top:12px;">
            <table>
                <tr>
                    <td>Total Tagihan</td>
                    <td class="right">Rp <?= number_format((float)($kwitansi['total_harga'] ?? 0), 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Total Dibayar</td>
                    <td class="right">Rp <?= number_format((float)($kwitansi['bayar_pribadi'] ?? 0), 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Kembali</td>
                    <td class="right">Rp <?= number_format((float)($kwitansi['total_kembali'] ?? 0), 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <div class="no-print" style="text-align:center;margin-top:16px;">
            <button onclick="window.print()">Print</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>

</html>