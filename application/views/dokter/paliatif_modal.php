<!-- paliatif_modal.php -->

<style>
    .exam-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 10px;
        align-items: center;
        margin-bottom: 8px;
    }

    .exam-row label {
        margin-bottom: 0;
    }

    .draw-box {
        border: 1px solid #adb5bd;
        border-radius: 4px;
        background: #fff;
    }

    .canvas-wrap {
        text-align: center;
    }

    .canvas-wrap small {
        display: block;
        margin-top: 4px;
        color: #6c757d;
    }
</style>


<form id="formPaliatif">
    <div class="modal fade" id="modalPaliatif" tabindex="-1" role="dialog" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="height: 800px;">
                <div class="modal-header">
                    <h5 class="modal-title">Asesmen Paliatif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="overflow-y: auto;">

                    <!-- hidden akan diisi JS saat klik pasien -->
                    <input type="hidden" name="episode_id">
                    <input type="hidden" name="pasien_id">

                    <!-- ================= DATA PASIEN ================= -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0 fw-bold">Hospice Home - Palliative Care (HHPC)</h5>
                                    <div class="small text-muted">Yayasan Kanker Indonesia Provinsi DKI Jakarta</div>
                                </div>
                                <span class="badge bg-primary">ASESMEN PALIATIF</span>
                            </div>

                            <div class="border rounded-3 p-3 mb-3">
                                <div class="fw-bold mb-2 small text-uppercase">DATA PASIEN</div>

                                <div class="row g-3 small">
                                    <div class="col-md-6">
                                        <label class="form-label">Oleh Tim / Relawan HHPC</label>
                                        <input type="text" name="petugas_hhpc" class="form-control form-control-sm" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Hari / Tanggal</label>
                                        <input type="date" name="tgl_kunjungan" class="form-control form-control-sm" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Nomor HHPC – YKI DKI Jakarta</label>
                                        <input type="text" name="no_hhpc" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Nama Pasien</label>
                                        <input type="text" name="nama_pasien" class="form-control form-control-sm" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Umur (tahun)</label>
                                        <input type="number" name="umur" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Agama</label>
                                        <input type="text" name="agama" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label d-block">Jenis Kelamin</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin" value="L">
                                            <label class="form-check-label">Laki-laki</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin" value="P">
                                            <label class="form-check-label">Perempuan</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Suku Bangsa</label>
                                        <input type="text" name="suku_bangsa" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Bahasa Sehari-hari</label>
                                        <input type="text" name="bahasa" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="alamat" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" name="telp" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Fax</label>
                                        <input type="text" name="fax" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">HP</label>
                                        <input type="text" name="hp" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- ============ MASALAH YANG DIHADAPI ============ -->
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="fw-bold mb-2 small text-uppercase">MASALAH YANG DIHADAPI (PALIATIF)</div>
                                <textarea name="masalah_1" class="form-control form-control-sm mb-2" rows="2" placeholder="1."></textarea>
                                <textarea name="masalah_2" class="form-control form-control-sm mb-2" rows="2" placeholder="2."></textarea>
                                <textarea name="masalah_3" class="form-control form-control-sm" rows="2" placeholder="3."></textarea>
                            </div>

                            <!-- ============ INFO PERAWATAN & KELUARGA ============ -->
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="fw-bold mb-2 small text-uppercase">INFORMASI PERAWATAN & KELUARGA</div>

                                <div class="mb-2">
                                    <label class="form-label">Yang Merawat di Rumah (Care Giver)</label>
                                    <input type="text" name="care_giver" class="form-control form-control-sm">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label d-block">Apakah Pasien Mengetahui Tentang Penyakitnya?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pasien_tahu" value="YA">
                                        <label class="form-check-label">YA</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pasien_tahu" value="TIDAK">
                                        <label class="form-check-label">TIDAK</label>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Keluarga yang Menderita Penyakit yang Sama</label>
                                    <input type="text" name="keluarga_sakit_sama" class="form-control form-control-sm">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Dukungan yang Diperlukan</label>
                                    <textarea name="dukungan" class="form-control form-control-sm" rows="2"></textarea>
                                </div>

                                <div>
                                    <label class="form-label">Staff Perawat HHPC – YKI Prov. DKI Jakarta</label>
                                    <input type="text" name="staff_perawat" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- ============ PHYSICAL SYMPTOMS ============ -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="fw-bold mb-3 text-uppercase small">
                                        Physical Symptoms
                                    </div>

                                    <!-- NYERI -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nyeri</label>
                                        <textarea name="nyeri" class="form-control form-control-sm" rows="2" placeholder="Lokasi, skala, durasi, faktor pencetus"></textarea>
                                    </div>

                                    <div class="row g-3 small">
                                        <!-- KOLOM KIRI -->
                                        <div class="col-md-6">
                                            <?php
                                            $leftSymptoms = [
                                                'anorexia'              => 'Anorexia',
                                                'nausea'                => 'Nausea',
                                                'vomiting'              => 'Vomiting',
                                                'penurunan_bb'          => 'Penurunan BB',
                                                'haematemesis'          => 'Haematemesis',
                                                'sariawan'              => 'Sariawan',
                                                'mulut_kering'          => 'Mulut Kering',
                                                'difficulty_swallowing' => 'Difficulty in Swallowing',
                                                'konstipasi'            => 'Konstipasi',
                                                'obstipasi'             => 'Obstipasi',
                                                'diarrhoea'             => 'Diarrhoea',
                                                'faecal_incontinence'   => 'Faecal Incontinence',
                                                'perdarahan'            => 'Perdarahan',
                                                'abdomen_distended'     => 'Abdomen Distended',
                                                'other_symptoms'        => 'Other Symptoms'
                                            ];
                                            foreach ($leftSymptoms as $name => $label) :
                                                $id = "symptom_" . $name;
                                            ?>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="form-check me-2">
                                                        <input class="form-check-input symptom-check" type="checkbox" id="<?= $id ?>" data-target="<?= $id ?>_desc" name="symptom[<?= $name ?>]" value="1">
                                                    </div>
                                                    <label for="<?= $id ?>" class="me-2 mb-0" style="min-width:180px;">
                                                        <?= $label ?>
                                                    </label>
                                                    <input type="text" id="<?= $id ?>_desc" name="symptom_desc[<?= $name ?>]" class="form-control form-control-sm" placeholder="Keterangan" disabled>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- KOLOM KANAN -->
                                        <div class="col-md-6">
                                            <?php
                                            $rightSymptoms = [
                                                'sesak_nafas'        => 'Sesak Nafas',
                                                'batuk'              => 'Batuk',
                                                'sputum'             => 'Sputum',
                                                'haemoptysis'        => 'Haemoptysis',
                                                'urinary_problems'   => 'Urinary Problems',
                                                'swelling'           => 'Swelling / Lymphoedema',
                                                'discharge'          => 'Discharge',
                                                'dekubitus'          => 'Dekubitus',
                                                'gangguan_tidur'     => 'Gangguan Tidur',
                                                'kejang'             => 'Kejang',
                                                'abnormal_sensation' => 'Abnormal Sensation',
                                                'weakness'           => 'Weakness',
                                                'paraplegia'         => 'Paraplegia',
                                                'mobility'           => 'Mobility'
                                            ];
                                            foreach ($rightSymptoms as $name => $label) :
                                                $id = "symptom_" . $name;
                                            ?>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="form-check me-2">
                                                        <input class="form-check-input symptom-check" type="checkbox" id="<?= $id ?>" data-target="<?= $id ?>_desc" name="symptom[<?= $name ?>]" value="1">
                                                    </div>
                                                    <label for="<?= $id ?>" class="me-2 mb-0" style="min-width:180px;">
                                                        <?= $label ?>
                                                    </label>
                                                    <input type="text" id="<?= $id ?>_desc" name="symptom_desc[<?= $name ?>]" class="form-control form-control-sm" placeholder="Keterangan" disabled>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ============ MENTAL & EMOTIONAL ============ -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="fw-bold mb-3 text-uppercase small">
                                        Mental & Emotional Assessment
                                    </div>

                                    <div class="row g-3 small mb-3">
                                        <div class="col-12 fw-semibold mb-1">
                                            Status Mental / Emosional
                                        </div>

                                        <?php
                                        $mentalStates = [
                                            'alert'       => 'Alert',
                                            'cooperative' => 'Co-operative',
                                            'calm'        => 'Calm',
                                            'withdrawn'   => 'Withdrawn',
                                            'irritable'   => 'Irritable',
                                            'angry'       => 'Angry',
                                            'anxious'     => 'Anxious',
                                            'confused'    => 'Confused'
                                        ];
                                        foreach ($mentalStates as $key => $label) :
                                            $id = "mental_" . $key;
                                        ?>
                                            <div class="col-md-3 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="<?= $id ?>" name="mental_state[]" value="<?= $key ?>">
                                                    <label class="form-check-label" for="<?= $id ?>">
                                                        <?= $label ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Catatan Mental / Emosional
                                        </label>
                                        <textarea name="mental_note" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan kondisi emosional pasien"></textarea>
                                    </div>

                                    <hr class="my-3">

                                    <div class="row g-3 small">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Diagnosis</label>
                                            <textarea name="diagnosis" class="form-control form-control-sm" rows="2" placeholder="Diagnosis medis utama"></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Prognosis</label>
                                            <textarea name="prognosis" class="form-control form-control-sm" rows="2" placeholder="Prognosis pasien"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ============ EXAMINATION (RESPIRATORY, ABDOMEN, PR/PV) ============ -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="fw-bold mb-3 text-uppercase small">
                                        Examination
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Keadaan Umum</label>
                                        <textarea name="keadaan_umum" class="form-control form-control-sm" rows="2" placeholder="Kondisi umum pasien saat pemeriksaan"></textarea>
                                    </div>

                                    <div class="fw-semibold mb-2">Komunikasi</div>

                                    <div class="exam-row">
                                        <label>Bicara</label>
                                        <input type="text" name="bicara" class="form-control form-control-sm">
                                    </div>

                                    <div class="exam-row">
                                        <label>Pucat</label>
                                        <input type="text" name="pucat" class="form-control form-control-sm">
                                    </div>

                                    <div class="exam-row">
                                        <label>Jaundice</label>
                                        <input type="text" name="jaundice" class="form-control form-control-sm">
                                    </div>

                                    <div class="exam-row">
                                        <label>Cyanosis</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cyanosis" value="Y">
                                                <label class="form-check-label">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cyanosis" value="N">
                                                <label class="form-check-label">Tidak</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="exam-row">
                                        <label>Oedema / Lymphoedema</label>
                                        <input type="text" name="oedema" class="form-control form-control-sm">
                                    </div>

                                    <!-- Respiratory + gambar paru -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="fw-semibold">Respiratory System</label>
                                            <textarea name="respiratory_system" class="form-control form-control-sm" rows="4"></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-center gap-3">
                                                <div class="canvas-wrap">
                                                    <canvas id="lung_left" width="120" height="160" class="draw-box"></canvas>
                                                    <small>Paru Kiri</small>
                                                </div>
                                                <div class="canvas-wrap">
                                                    <canvas id="lung_right" width="120" height="160" class="draw-box"></canvas>
                                                    <small>Paru Kanan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Abdomen + gambar -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="fw-semibold">Abdomen</label>
                                            <textarea name="abdomen" class="form-control form-control-sm" rows="4"></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="canvas-wrap">
                                                <canvas id="abdomen_canvas" width="220" height="320" class="draw-box"></canvas>
                                                <small>Gambar Lokasi Abdomen / Luka</small>
                                            </div>
                                        </div>
                                    </div>





                                    <!-- PR / PV -->
                                    <div class="mt-3">
                                        <div class="fw-semibold mb-2">PR / PV</div>

                                        <div class="exam-row">
                                            <label>PR (Per Rectal)</label>
                                            <textarea name="pr" class="form-control form-control-sm" rows="2" placeholder="Temuan PR"></textarea>
                                        </div>

                                        <div class="exam-row mt-2">
                                            <label>PV (Per Vaginam)</label>
                                            <textarea name="pv" class="form-control form-control-sm" rows="2" placeholder="Temuan PV"></textarea>
                                        </div>
                                    </div>

                                    <!-- ============ FAMILY / GENOGRAM ============ -->
                                    <hr class="my-3">










                                    <!-- ============ FAMILY / KESIMPULAN / RENCANA ============ -->
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="fw-bold mb-3 text-uppercase small">
                                                Riwayat Sosial & Rencana
                                            </div>

                                            <!-- FAMILY + GENOGRAM -->
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Family / Genogram (catatan)</label>
                                                    <textarea name="family_note" class="form-control form-control-sm" rows="3" placeholder="Catatan singkat struktur keluarga / genogram"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="canvas-wrap">
                                                        <canvas id="genogram_canvas" width="220" height="200" class="draw-box"></canvas>
                                                        <small>Gambar Genogram Keluarga</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- KESIMPULAN -->
                                            <div class="mt-3">
                                                <label class="form-label fw-semibold">Kesimpulan keadaan pasien</label>
                                                <textarea name="kesimpulan_pasien" class="form-control form-control-sm" rows="3"></textarea>
                                            </div>

                                            <!-- RENCANA -->
                                            <div class="mt-3">
                                                <label class="form-label fw-semibold">Rencana penanganan selanjutnya</label>
                                                <textarea name="rencana_selanjutnya" class="form-control form-control-sm" rows="3"></textarea>
                                            </div>

                                            <!-- TTD -->
                                            <div class="row g-3 mt-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Tanda tangan (nama)</label>
                                                    <input type="text" name="ttd_nama" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Tanggal</label>
                                                    <input type="date" name="ttd_tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- HIDDEN UNTUK GAMBAR CANVAS (PNG BASE64 TANPA PREFIX) -->
                                    <input type="text" name="lung_left_img" id="lung_left_img">
                                    <input type="text" name="lung_right_img" id="lung_right_img">
                                    <input type="text" name="abdomen_img" id="abdomen_img">
                                    <input type="text" name="genogram_img" id="genogram_img">

















                                </div>
                            </div>

                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div> <!-- modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-simpan-paliatif">Simpan</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</form>