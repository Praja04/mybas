 <!-- Modal blacklist -->
 <div class="modal fade" id="modalBlacklistVisitor" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-lg modal-dialog-centered">
         <div class="modal-content">
             <form id="formBlacklistVisitor">
                 <input type="hidden" id="bl_trnvisitorid" name="trnvisitorid" />

                 <div class="modal-header">
                     <h5 class="modal-title">Blacklist Visitor</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="row g-3">
                         <div class="border rounded p-3 mb-3 bg-light">
                             <div class="row g-2">
                                 <div class="col-md-6">
                                     <strong>Nama:</strong> <span id="bl_info_nama">-</span><br>
                                     <strong>No Identitas:</strong> <span id="bl_info_no_identitas">-</span><br>
                                     <strong>No Kartu:</strong> <span id="bl_info_no_kartu">-</span><br>
                                     <strong>Perusahaan:</strong> <span id="bl_info_perusahaan">-</span><br>
                                     <strong>Waktu Masuk:</strong> <span id="bl_info_waktu_masuk">-</span>
                                 </div>
                                 <div class="col-md-6">
                                     <strong>Tujuan:</strong> <span id="bl_info_tujuan">-</span><br>
                                     <strong>No HP Driver:</strong> <span id="bl_info_nohp">-</span><br>
                                     <strong>Nopol Kendaraan:</strong> <span id="bl_info_nopol">-</span><br>
                                     <strong>Nama Kernet:</strong> <span id="bl_info_kernet">-</span><br>
                                     <strong>Plant:</strong> <span id="bl_info_plant">-</span>
                                 </div>
                             </div>
                         </div>

                         <div class="col-md-6">
                             <div class="mb-2">
                                 <label class="form-label">No Identitas</label>
                                 <input type="text" class="form-control" id="bl_no_identitas" name="no_identitas"
                                     readonly />
                             </div>
                             <div class="mb-2">
                                 <label class="form-label">Nama</label>
                                 <input type="text" class="form-control" id="bl_nama" name="nama" readonly />
                             </div>
                             <div class="mb-2">
                                 <label class="form-label">Tanggal Lahir</label>
                                 <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                     required />
                             </div>
                             <div class="mb-2">
                                 <label class="form-label">Jenis Identitas</label>
                                 <select class="form-select" id="jenis_identitas" name="jenis_identitas" required>
                                     <option value="">Pilih...</option>
                                     <option value="KTP">KTP</option>
                                     <option value="SIM">SIM</option>
                                     <option value="Paspor">Paspor</option>
                                 </select>
                             </div>
                             <div class="mb-2">
                                 <label class="form-label">Alasan Blacklist</label>
                                 <textarea class="form-control" id="alasan_blacklist" name="alasan_blacklist" rows="3" required></textarea>
                             </div>
                             {{-- <div class="mb-2">
                                 <label class="form-label">Diblacklist Oleh</label>
                                 <input type="text" class="form-control" id="diblacklist_oleh"
                                     name="diblacklist_oleh" required />
                             </div> --}}
                         </div>
                         <div class="col-md-6">
                             <label class="form-label">Foto KTP</label>
                             <div id="blKtpFotoContainer" class="border rounded p-2 mb-3">
                                 <img id="blKtpFoto" src="" alt="Foto KTP" class="img-fluid w-100" />
                             </div>
                             <label class="form-label">Foto Selfie</label>
                             <div id="blSelfieContainer" class="d-flex flex-wrap gap-2"></div>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button class="btn btn-danger" type="submit">Blacklist</button>
                     <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
