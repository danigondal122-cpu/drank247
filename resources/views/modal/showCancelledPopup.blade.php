<form id="form_cancelledpopup" action="#" method="post">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><b> Reason of rejection</b></h3>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="message_div"></div>
            <div class="row box-body">
                <input type="hidden" name="oid" value="{{ $oid }}" id="oid">

                <div class="col-sm-6">
                    <!-- radio -->
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelledreason" value="Fake Order"
                                checked="" onchange="showOtherinput();">
                            <label class="form-check-label"> Fake Order</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelledreason"
                                value="Requested by Customer" onchange="showOtherinput();">
                            <label class="form-check-label"> Requested by Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelledreason" value="Payment Failed"
                                onchange="showOtherinput();">
                            <label class="form-check-label"> Payment Failed</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelledreason" value="Fraud"
                                onchange="showOtherinput();">
                            <label class="form-check-label"> Fraud</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="othercheckbox" name="cancelledreason"
                                value="other" onchange="showOtherinput();">
                            <label class="form-check-label"> Others</label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12" id="otherinput" style="display:none;">
                    <label>Other Reason</label>
                    <input type="text" name="other" id="other" class="form-control" autocomplete="off"
                        value="">
                    <span class="text-danger" id="other_error"></span>
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

    </div>
</form>
{{-- see #form_cancelledpopup script --}}
