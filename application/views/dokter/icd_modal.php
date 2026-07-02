<!-- icd_modal.php -->


<!-- Modal Cari ICD-->
<div class="modal fade" id="modal_icd10" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="height: 800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ICD 10</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <label class="col-sm-2 col-form-label">Cari</label>
                            <input class="form-control form-control-sm" id="input_pencarian10" type="text" name="input_pencarian10">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-sm small table-hover table-head-fixed text-nowrap">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 10%">Kode ICD 10</th>
                                        <th>Diagnosa</th>

                                    </tr>
                                </thead>
                                <tbody id="listicd10utama"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="modal_icd10_sek" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ICD 10 Sekunder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <label class="col-sm-2 col-form-label">Cari </label>
                            <input class="form-control form-control-sm" id="input_pencarian10_sek" type="text" name="input_pencarian10_sek">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-sm small table-hover table-head-fixed text-nowrap">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 10%">Kode ICD 10</th>
                                        <th>Diagnosa</th>
                                    </tr>
                                </thead>
                                <tbody id="listicd10sekunder"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modal_icd9" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ICD 9</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <label class="col-sm-2 col-form-label">Cari </label>
                            <input class="form-control form-control-sm" id="input_pencarian9" type="text" name="pencarian9">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-sm small table-hover table-head-fixed text-nowrap">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 10%">Kode ICD 9</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody id="listmastericd9"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>