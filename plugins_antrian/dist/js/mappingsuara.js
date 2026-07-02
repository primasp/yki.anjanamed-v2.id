
//datamapping();

function datamapping() {
    $.ajax({
        url: url + 'index.php/Mappingsuara/datamapping',
        type: 'post',
        dataType: 'json',
        //cache: false,
        success: function (res) {
            var tr = "";
            res.map(function (d) {
                tr += `
                     <tr class="bg-light" style="position: sticky;top:0;background:#ffffff;z-index:1001;">
                        <td><strong>`+ d.NAMAPOLI + `</strong></td>
                        <td>`+ d.POLIID + `</td>
                        <td></td>
                        <td></td>
                        <td>`+ d.FILEPOLI + `</td>
                        <td>
                            <form action="`+ url + `index.php/Mappingsuara/uploadsuarapoli" class="dropzone" style="min-height: unset;margin:0;padding:10px;" id="myDropzone">
                                <input value="`+ d.POLIID + `" name="idpoli" readonly hidden />
                                <div class="dz-message" style="margin: 0;">Drop files</div>
                            </form>
                        </td>
                    </tr>
                `;
                d.LISTDOKTER.map(function (e) {
                    tr += `
                        <tr>
                           <td style="padding-left: 50px;">- `+ e.NAMADOKTER + `</td>
                           <td>`+ e.DOKTERID + `</td>
                           <td>`+ (e.IPTV ? e.IPTV : '-') + `</td>
                           <td>`+ (e.IPLOGIN ? e.IPLOGIN : '-') + `</td>
                           <td>`+ e.FILEDOKTER + `</td>
                           <td>
                               <form action="`+ url + `index.php/Mappingsuara/uploadsuarapoli" class="dropzone" style="min-height: unset;margin:0;padding:10px;" id="myDropzone">
                                   <input value="`+ e.DOKTERID + `" name="idpoli" readonly hidden />
                                   <div class="dz-message" style="margin: 0;">Drop files</div>
                               </form>
                           </td>
                       </tr>
                   `;

                })
            })
            $("#data-mapping tbody").html(tr).ready(function () {
                $('.dropzone').dropzone({
                    maxFilesize: 2, // Max file size in MB
                    acceptedFiles: '.mp3', // Accept only images
                    init: function () {
                        /* this.on("sending", function(file, xhr, formData) {
                            // Append additional data here
                            formData.append("ID", "12345");
                            formData.append("extra_data", "Some additional info");
                        }); */
                        this.on("success", function (file, response) {
                            datamapping();
                            /*  console.log(response);
                             console.log("File uploaded successfully"); */
                        });
                        this.on("error", function (file, errorMessage) {
                            console.log("Error: " + errorMessage);
                        });
                    }
                })
            });

        }, error: function (res) {
            console.log('update error');
        }
    });
}
