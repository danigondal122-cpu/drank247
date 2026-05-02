<form id="form_Reassignpopup" action="#" method="post">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><b>Reassign</b></h3>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="message_div"></div>
            <div class="row box-body">
                <input type="hidden" name="oid" value="{{ $oid }}" id="oid">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group" id="deliverypersondiv">
                                <label for="first_name">*Franchises</label>
                                <select class="form-control select2" id="franchises" name="franchises">
                                    <option value="">Select Franchises</option>
                                    @foreach ($franchiseslist as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $value->id == $franchisesid ? 'selected' : '' }}>
                                            {{ $value->franchises_name }}</option>
                                    @endforeach
                                </select>
                                <span id="franchises_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="first_name">*Delivery Person</label>
                                <select class="form-control select2" id="deliveryperson" name="deliveryperson">
                                    <option value="">Select Delivery person</option>
                                    @foreach ($deliverypersonlist as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $value->id == $deliverypersonid ? 'selected' : '' }}>
                                            {{ $value->dp_name }}</option>
                                    @endforeach
                                </select>
                                <span id="deliveryperson_error"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal-footer">
            <div class="form-group">
                <div class="col-md-12">
                    <input type="hidden" name="order_id" id="order_id" class="form-control" autocomplete="off"
                        value="{{ $oid }}">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;&nbsp;Save </button>
                    <a href="{{ url('customer_service/order/list') }}" class="btn btn-secondary text-white"> <i
                            class="fa fa-window-close-o"></i>&nbsp;&nbsp;Cancel</a>


                </div>
            </div>
        </div>

    </div>
</form>
{{-- see #form_Reassignpopup script --}}
