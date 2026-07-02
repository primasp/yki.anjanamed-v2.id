<div class="table-responsive obat-table-wrapper">
    <table class="table table-sm table-hover align-middle table-modern" id="manajemen-obat-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Obat</th>
                <th>Depo</th>
                <th class="text-end">Stok</th>
                <th>Status</th>
                <th>Satuan</th>
                <th class="text-end">HNA</th>
                <th class="text-end">Harga Jual</th>
                <th>Golongan</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($obat_list as $o) : ?>
                <?php
                $stok = (float) $o->stok_komputer;

                $badge = 'bg-success';
                if ($o->status_stok == 'MINUS') $badge = 'bg-danger';
                if ($o->status_stok == 'HABIS') $badge = 'bg-dark';
                if ($o->status_stok == 'MENIPIS') $badge = 'bg-warning text-dark';
                ?>

                <tr class="<?= $o->status_stok == 'MINUS' ? 'row-stock-minus' : '' ?>">
                    <td class="fw-bold text-primary">
                        <?= html_escape($o->obat_id) ?>
                    </td>

                    <td>
                        <div class="fw-semibold"><?= html_escape($o->nama_obat) ?></div>
                        <div class="text-muted small">
                            Pabrik: <?= html_escape($o->nama_pabrik ?: '-') ?>
                        </div>
                    </td>

                    <td>
                        <span class="badge bg-light text-dark border">
                            <?= html_escape($o->nama_gudang ?: '-') ?>
                        </span>
                    </td>

                    <td class="text-end">
                        <div class="fw-bold <?= $stok < 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($stok, 2, ',', '.') ?>
                        </div>
                        <div class="text-muted small">
                            Min: <?= number_format($o->stok_min ?? 0, 0, ',', '.') ?>
                        </div>
                    </td>

                    <td>
                        <span class="badge <?= $badge ?>">
                            <?= html_escape($o->status_stok) ?>
                        </span>
                    </td>

                    <td>
                        <?= html_escape($o->satuan_jual ?: '-') ?>
                    </td>

                    <td class="text-end">
                        Rp <?= number_format($o->harga_hna ?? 0, 0, ',', '.') ?>
                    </td>

                    <td class="text-end fw-semibold">
                        Rp <?= number_format($o->harga_jual ?? 0, 0, ',', '.') ?>
                    </td>

                    <td>
                        <?= html_escape($o->nama_golongan ?: '-') ?>
                    </td>

                    <td class="text-center">

                        <?php
                        $defaultGudangId = !empty($default_gudang_id) ? $default_gudang_id : 'DEPO00000000APT';

                        $gudangId = !empty($o->gudang_id)
                            ? $o->gudang_id
                            : $defaultGudangId;

                        $gudangNama = !empty($o->nama_gudang)
                            ? $o->nama_gudang
                            : 'DEPO RAWAT JALAN';
                        ?>

                        <!-- <button type="button" class="btn btn-sm btn-warning js-adjust-stok" data-obat-id="<?= html_escape($o->obat_id) ?>" data-obat-nama="<?= html_escape($o->nama_obat) ?>" data-gudang-id="<?= html_escape($o->gudang_id) ?>" data-gudang-nama="<?= html_escape($o->nama_gudang) ?>" data-stok-komputer="<?= html_escape($o->stok_komputer) ?>">
                            Adjustment
                        </button> -->

                        <button type="button" class="btn btn-sm btn-warning js-adjust-stok" data-obat-id="<?= html_escape($o->obat_id) ?>" data-obat-nama="<?= html_escape($o->nama_obat) ?>" data-gudang-id="<?= html_escape($gudangId) ?>" data-gudang-nama="<?= html_escape($gudangNama) ?>" data-stok-komputer="<?= html_escape($o->stok_komputer ?? 0) ?>">
                            Adjustment
                        </button>

                        <button type="button" class="btn btn-sm btn-primary edit-obat" data-id="<?= html_escape($o->obat_id) ?>">
                            Edit
                        </button>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>