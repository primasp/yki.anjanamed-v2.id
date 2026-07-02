<?php
$e = $e ?? function ($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
};
?>

<div class="page">

    <!-- <table class="header-table" cellpadding="1" cellspacing="0" width="100%">
        <tr>
            <td width="13%" class="logo-cell">
                <?php if (!empty($logoPath)) : ?>
                    <img src="<?= $logoPath ?>" width="58">
                <?php endif; ?>
            </td>

            <td width="57%" class="title-cell">
                <div class="brand-title">YAYASAN KANKER INDONESIA</div>
                <div class="brand-title">PROVINSI DKI JAKARTA</div>
            </td>

            <td width="30%" align="right">
                <table cellpadding="1" cellspacing="0" width="100%">
                    <tr>
                        <td class="date-box">Tanggal diterima</td>
                    </tr>
                    <tr>
                        <td class="date-box"><b><?= $e($tglDiterima) ?></b></td>
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
    </table> -->


    <table class="header-main" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <!-- LOGO -->
            <td width="16%" class="header-logo" cellpadding="4">
                <?php if (!empty($logoPath)) : ?>
                    <img src="<?= $logoPath ?>" width="58">
                <?php endif; ?>
            </td>

            <!-- JUDUL -->
            <td width="64%" class="header-title">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td class="brand-title">YAYASAN KANKER INDONESIA</td>
                    </tr>
                    <tr>
                        <td class="brand-title">PROVINSI DKI JAKARTA</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="header-address">Jl. Baru Sunter Permai Raya No. 2 Telp. 021-6509144 Fax 021-6507748 | Call Centre 021-6507746
                        </td>
                    </tr>
                </table>
            </td>

            <!-- TANGGAL DITERIMA -->
            <td width="20%" class="header-date">
                <table class="date-table" cellpadding="4" cellspacing="0" width="100%">
                    <tr>
                        <td class="date-box-title">Tanggal diterima</td>
                    </tr>
                    <tr>
                        <td class="date-box-value">
                            <b><?= $e($tglDiterima) ?></b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- <tr>
            <td colspan="3" class="header-address">
                Jl. Baru Sunter Permai Raya No. 2 Telp. 021-6509144 Fax 021-6507748 | Call Centre 021-6507746
            </td>
        </tr> -->
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
            <td height="20"></td>
        </tr>
    </table>

    <!-- IDENTITAS -->
    <table class="identity-wrap" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="72%" valign="top">
                <table class="identity-table" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td class="meta-label">NAMA PASIEN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value"><b><?= $e($namaPasien) ?></b></td>
                    </tr>
                    <tr>
                        <td class="meta-label">UMUR</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value"><?= $e($umur) ?></td>
                    </tr>
                    <tr>
                        <td class="meta-label">NAMA SUAMI</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value"><?= $e($namaSuami) ?></td>
                    </tr>
                    <tr>
                        <td class="meta-label">ALAMAT</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value"><?= $e($alamat) ?></td>
                    </tr>
                    <tr>
                        <td class="meta-label">DOKTER PENGIRIM</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value"><?= $e($dokterPengirim) ?></td>
                    </tr>
                    <tr>
                        <td class="meta-label">ALAMAT</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value"><?= $e($alamatPengirim) ?></td>
                    </tr>
                </table>
            </td>

            <td width="28%" valign="top" align="right">
                <table cellpadding="1" cellspacing="0" width="82%">
                    <tr>
                        <td class="sitologi-box">No. Sitologi :</td>
                    </tr>
                    <tr>
                        <td class="sitologi-box"><b><?= $e($noSitologi) ?></b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- JARAK KE BAWAH -->
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td height="30"></td>
        </tr>
    </table>

    <!-- HASIL -->
    <!-- <table class="result-table" cellpadding="0" cellspacing="0" width="100%">
        <?php foreach ($hasilRows as $row) : ?>
            <?php
            $answerHtml = '&nbsp;';

            if (!empty($row['answers'])) {
                $cleanAnswers = [];

                foreach ($row['answers'] as $ans) {
                    $ans = trim(preg_replace('/\s+/', ' ', (string)$ans));
                    if ($ans !== '') {
                        $cleanAnswers[] = $e($ans);
                    }
                }

                if (!empty($cleanAnswers)) {
                    $answerHtml = implode('<br />', $cleanAnswers);
                }
            }
            ?>
            <tr>
                <td class="result-label" width="30%"><?= $e($row['section']) ?></td>
                <td class="result-colon" width="3%">:</td>
                <td class="result-answer" width="67%"><?= $answerHtml ?></td>
            </tr>
        <?php endforeach; ?>
    </table> -->

    <table class="result-table" cellpadding="0" cellspacing="0" width="100%">
        <?php foreach ($hasilRows as $idx => $row) : ?>
            <?php
            $answerHtml = '&nbsp;';

            if (!empty($row['answers'])) {
                $cleanAnswers = [];

                foreach ($row['answers'] as $ans) {
                    $ans = trim(preg_replace('/\s+/', ' ', (string)$ans));

                    if ($ans !== '') {
                        $cleanAnswers[] = $e($ans);
                    }
                }

                if (!empty($cleanAnswers)) {
                    $answerHtml = implode('<br />', $cleanAnswers);
                }
            }
            ?>

            <tr>
                <td class="result-label" width="30%"><?= $e($row['section']) ?></td>
                <td class="result-colon" width="3%">:</td>
                <td class="result-answer" width="67%" style="padding-left:0; text-align:left;"><?= $answerHtml ?></td>
            </tr>

            <?php if ($idx < count($hasilRows) - 1) : ?>
                <tr class="result-gap">
                    <td colspan="3" height="11"></td>
                </tr>
            <?php endif; ?>

        <?php endforeach; ?>
    </table>

    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td height="48"></td>
        </tr>
    </table>

    <!-- FOOTER -->
    <table class="footer-table" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="42%" valign="top">
                <div class="note">
                    <!-- Dicetak otomatis dari sistem setelah final dokter.<br>
                    Status: Admin Final / Dokter ACC -->
                </div>
            </td>

            <td width="58%" valign="top">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="35%"></td>
                        <td width="45%" align="right">
                            <table cellpadding="5" cellspacing="0" width="160">
                                <tr>
                                    <td class="sign-box" valign="middle" align="center">
                                        Tanggal Periksa<br>
                                        <b><?= $e($tglPeriksa) ?></b>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" height="52"></td>
                    </tr>

                    <tr>
                        <td width="35%"></td>
                        <td width="45%" align="right">
                            <table cellpadding="0" cellspacing="0" width="150">
                                <tr>
                                    <td class="doctor-name">
                                        <?= $e($dokterLabNama) ?>
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