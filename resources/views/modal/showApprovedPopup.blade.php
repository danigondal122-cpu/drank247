<form id="form_approved" action="#" method="post">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><b>Order Approval</b></h3>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="message_div"></div>
            <div class="row box-body">
                <input type="hidden" name="oid" value="{{ $oid }}" id="oid">
                <input type="hidden" name="fid" value="{{ $franchisesid }}" id="fid">
                <input type="hidden" name="did" value="{{ $deliverypersonid }}" id="did">

                <div class="col-sm-12">
                    <b>System has found franchise and delivery person matched with customer pool area, so would you like
                        to assign the order</b>
                </div>
                <div class="col-sm-1">
                    <div class="form-group" style="margin-top:10px;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck1" name="customCheck1"
                                value="1" {{ $checked }} {{ $disabled }}>
                            <label class="custom-control-label" for="customCheck1">Custom checkbox</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-11">
                    <div class="form-group" style="margin-top:10px;">
                        <table id="table" class="table table-bordered table-hover">
                            <tr style="background-color:#f2f2f2;">
                                <td>Franchises</td>
                                <td>Delivery Person</td>
                            </tr>
                            <tr>
                                <td>{{ $franchises_name }}</td>
                                <td>{{ $dp_name }}</td>

                            </tr>

                        </table>

                    </div>

                </div>
                <div class="col-sm-12">
                    <div class="form-group" style="margin-top:10px;">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group" id="deliverypersondiv">
                                    <label for="first_name">*Franchises</label>
                                    <select class="form-control select2" id="franchises" name="franchises">
                                        <option value="">Select Franchises</option>
                                        @foreach ($franchiseslist as $value)
                                            <option value="{{ $value->id }}">{{ $value->franchises_name }}
                                            </option>
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
                                                @selected(empty($row) == false && $value->id == $row->delivery_person_id)>
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

        </div>
        <div class="modal-footer">
            <div class="form-group">
                <div class="col-md-12">
                    <input type="hidden" name="order_id" id="order_id" class="form-control" autocomplete="off"
                        value="{{ $oid }}">

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;&nbsp;Save </button>
                    <a href="{{ url('customer_service/order/view/' . $oid) }}" class="btn btn-secondary text-white"> <i
                            class="fa fa-window-close"></i>&nbsp;&nbsp;Cancel</a>

                </div>
            </div>
        </div>
</form>
{{-- see #form_approved script --}}
