<!-- rujuk_modal.php -->

<form id="formRujukan" onsubmit="simpanrujukan(event)">

    <div class="modal fade" id="modal_rujuk" tabindex="-1" role="dialog" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="height: 800px;">
                <div class="modal-header">
                    <h5 class="modal-title">Rujuk Faskes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto;">
                    <div class="card-body p-3">



                        <div class="row  mb-3">
                            <div class="col-md-12">
                                <div class="settings-form mt-0">
                                    <div class="input-block">
                                        <p class="pay-cont">Rujukan Khusus <span class="text-danger">*</span></p>
                                        <label class="custom_radio me-4">
                                            <input type="radio" name="tipeRjkn" id="rjknKhususYa" value="ya" required onchange="toggleFieldsModalRjkn('ya')">
                                            <span class="checkmark"></span> Ya
                                        </label>
                                        <label class="custom_radio">
                                            <input type="radio" name="tipeRjkn" id="rjknKhususTidak" value="tidak" required onchange="toggleFieldsModalRjkn('tidak')">
                                            <span class="checkmark"></span> Tidak
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <fieldset id="RujukanKhususRow" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Tanggal Rencana Rujuk Khusus <span style="color: red">*</span></label>
                                        <!-- <input class="form-control floating datetimepicker" id="tglEstRujuk" name="tglEstRujuk" placeholder="dd/mm/yyyy" type="text" value="" required> -->
                                        <input class="form-control floating" id="tglEstRujukKhss" name="tglEstRujukKhss" type="date">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Pilih Rujukan Khusus<span style="color: red">*</span></label>
                                        <select id="plhRjknKhssApi" name="plhRjknKhssApi" class="form-control select2" onchange="validateSubFieldsRjkn()">
                                            <option class="text-center" value="">-- Pilih Rujukan Khusus --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <fieldset id="RjknKhssSpesialis" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Spesialis Rujukan Khusus<span style="color: red">*</span></label>
                                            <select id="spsRjknKhssGetApi" name="spsRjknKhssGetApi" class="form-control select2">
                                                <option class="text-center" value="">-- Pilih Spesialis --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <label class="focus-label">Pilih Sub Spesialis<span style="color: red">*</span></label>
                                            <select id="subSpRjknKhssGetApi" name="subSpRjknKhssGetApi" class="form-control select2">
                                                <option class="text-center" value="">-- Pilih Sub Spesialis --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Pilih Faskes Rujukan Khusus<span style="color: red">*</span></label>
                                        <select id="plhFasRjknKhssApi" name="plhFasRjknKhssApi" class="form-control select2">
                                            <option class="text-center" value="">-- Pilih Faskes Rujukan Khusus --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="RujukanRow" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Tanggal Rencana Rujuk <span style="color: red">*</span></label>
                                        <!-- <input class="form-control floating datetimepicker" id="tglEstRujuk" name="tglEstRujuk" placeholder="dd/mm/yyyy" type="text" value="" required> -->
                                        <input class="form-control floating" id="tglEstRujuk" name="tglEstRujuk" type="date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Spesialis Rujukan<span style="color: red">*</span></label>
                                        <select id="spsRjknGetApi" name="spsRjknGetApi" class="form-control select2">
                                            <option class="text-center" value="">-- Pilih Spesialis --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Pilih Sub Spesialis<span style="color: red">*</span></label>
                                        <select id="subSpRjknGetApi" name="subSpRjknGetApi" class="form-control select2">
                                            <option class="text-center" value="">-- Pilih Sub Spesialis --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Pilih Sarana<span style="color: red">*</span></label>
                                        <select id="saranaGetApi" name="saranaGetApi" class="form-control select2">
                                            <option class="text-center" value="">-- Pilih Sarana --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-block local-forms">
                                        <label class="focus-label">Pilih Faskes<span style="color: red">*</span></label>
                                        <select id="faskesRjknGetApi" name="faskesRjknGetApi" class="form-control select2">
                                            <option class="text-center" value="">-- Pilih Faskes --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>



                        <!-- <h3 class="card-title">Detil Rujukan</h3> -->


                        <div class="row" id="cttnKhssRow" style="display:none;">
                            <div class="col-md-12">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Catatan Rujukan<span style="color: red">*</span></label>
                                    <textarea name="catRjkn" id="catRjkn" class="form-control" rows="3" placeholder="Tambahkan catatan mengenai rujukan"></textarea>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="idepisode" id="idepisode" class="idepisode">
                        <input type="hidden" name="nokartuprov" id="nokartuprov" class="nokartuprov">


                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="btn-simpan-rujukan" onclick="simpanrujukan(event)">Simpan</button> -->
                    <!-- <button type="button" class="btn btn-primary" id="btn-simpan-rujukan" onclick="simpanrujukan(event)">Simpan</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button> -->

                    <button type="submit" class="btn btn-primary" id="btn-simpan-rujukan">Simpan</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</form>