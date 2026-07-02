<h3>HASIL RADIOLOGI</h3>

<p><b>Nama:</b> <?= $nama ?></p>
<p><b>RM:</b> <?= $no_rm ?></p>

<hr>

<h4>Hasil Bacaan</h4>
<pre><?= $hasil_bacaan ?></pre>

<hr>

<h4>File</h4>
<?php foreach ($files as $f) : ?>
    <div>
        <?= $f->file_name ?>
        <?php if ($f->catatan) : ?>
            <br><small>Catatan: <?= $f->catatan ?></small>
        <?php endif; ?>
    </div>
<?php endforeach; ?>