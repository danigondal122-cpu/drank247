<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title"><b>Franchises</b></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">
        <div id="message_div"></div>
        <div class="row box-body">
            <div class="table-responsive">
                <table id="table" class="table table-bordered table-hover" style="border-radius:10px;">
                    <thead style="background-color:#f2f2f2;">
                        <tr>
                            <th>No.</th>
                            <th>Franchises</th>
                            <th>Pool</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($list as $index => $value)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $value->franchise->franchises_name }}</td>
                                <td>{{ $value->poolareas }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No Record Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer">
        <div class="form-group text-right">
            {{-- Uncomment if buttons are needed --}}
            {{-- <button type="submit" class="btn btn-default btn-save btn-flat">Save</button> --}}
            {{-- <a href="{{ url('admin/country/view/'.$cid) }}" class="btn btn-default btn-cancel btn-flat">Cancel</a> --}}
        </div>
    </div>
</div>
