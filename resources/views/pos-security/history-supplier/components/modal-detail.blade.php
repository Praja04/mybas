 <!-- Modal visitor -->
 <div class="modal fade" id="modalVisitorDetail" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-xl modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Detail Visitor</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="row g-3">
                     <div class="col-md-6">
                         <dl class="row">
                             <dt class="col-sm-4">Nama</dt>
                             <dd class="col-sm-8" id="detailNamaVisitor"></dd>

                             <dt class="col-sm-4">No Identitas</dt>
                             <dd class="col-sm-8" id="detailNoIdentitas"></dd>

                             <dt class="col-sm-4">Tanggal Lahir</dt>
                             <dd class="col-sm-8" id="detailTglLahir"></dd>

                             <dt class="col-sm-4">Perusahaan</dt>
                             <dd class="col-sm-8" id="detailPerusahaan"></dd>

                             <dt class="col-sm-4">No Kartu</dt>
                             <dd class="col-sm-8" id="detailNoKartu"></dd>

                             <dt class="col-sm-4">Tujuan</dt>
                             <dd class="col-sm-8" id="detailPurpose"></dd>

                             <dt class="col-sm-4">Nopol</dt>
                             <dd class="col-sm-8" id="detailNopol"></dd>

                             <dt class="col-sm-4">Nama Kernet</dt>
                             <dd class="col-sm-8" id="detailNamaKernet"></dd>

                             <dt class="col-sm-4">No HP Driver</dt>
                             <dd class="col-sm-8" id="detailNoHpDriver"></dd>

                             <dt class="col-sm-4">Waktu Masuk</dt>
                             <dd class="col-sm-8" id="detailWaktuMasuk"></dd>

                             <dt class="col-sm-4">Waktu Keluar</dt>
                             <dd class="col-sm-8" id="detailWaktuKeluar"></dd>
                         </dl>
                     </div>
                     <div class="col-md-6">
                         <div class="mb-3">
                             <label class="form-label">Foto KTP</label>
                             <div id="detailKtpFotoContainer" class="border rounded p-2">
                                 <img id="detailKtpFoto" src="" alt="Foto KTP" class="img-fluid w-100" />
                             </div>
                         </div>
                         <div class="mb-3">
                             <label class="form-label">Foto Diri (Masuk)</label>
                             <div id="detailSelfieContainer" class="d-flex flex-wrap gap-2"></div>
                         </div>
                         <div class="mb-3">
                             <label class="form-label">Foto Diri (Keluar)</label>
                                <img id="detailSelfieOutContainer" src="" alt="Foto Out" class="img-thumbnail d-flex flex-wrap" style="max-width: 120px;" />
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 {{-- <button onclick="triggerReportLostCard()" class="btn btn-warning">Lapor Kartu Hilang</button>
                 <button onclick="triggerBlacklistVisitor()" class="btn btn-danger">Blacklist</button> --}}
                 <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
             </div>
         </div>
     </div>
 </div>
