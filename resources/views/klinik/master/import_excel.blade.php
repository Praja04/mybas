<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
    </div>
    <div class="modal-body">
       
        {{ csrf_field() }}

        <label>Pilih file excel</label>
        <div class="form-group">
            <input type="file" name="file" required="required">
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Import</button>
    </div>
</div>
</form>