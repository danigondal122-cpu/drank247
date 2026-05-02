
  
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title"><b>{{$detail->dp_name}}</b></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Document List:</label></div>
                        </div>
                </div>
            </div>
            @if($detail->bank_pass_no=="" && $detail->bank_pass_front==""  && $detail->bank_pass_back==""  
            && $detail->statement_conduct==""  && $detail->licence_front==""  && $detail->licence_back==""
            && $detail->franchise_contract==""  && $detail->payroll_contract==""  && $detail->extra_option=="" )
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6">Document is not available</div>
                        </div>
                </div>
            </div>
            @endif
            @if($detail->bank_pass_no!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Bank Account No.</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->bank_pass_no)}}">{{$detail->bank_pass_no}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->bank_pass_front!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Bank Passbook Front</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->bank_pass_front)}}">{{$detail->bank_pass_front}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->bank_pass_back!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Bank Passbook Back</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->bank_pass_back)}}">{{$detail->bank_pass_back}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->statement_conduct!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Statement Conduct</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->statement_conduct)}}">{{$detail->statement_conduct}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->licence_front!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Licence Front</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->licence_front)}}">{{$detail->licence_front}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->licence_back!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Licence Back </label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->licence_back)}}">{{$detail->licence_back}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->franchise_contract!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Franchise Contract </label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" target="_blank" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->franchise_contract)}}">{{$detail->franchise_contract}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->payroll_contract!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Payroll Contract</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->payroll_contract)}}">{{$detail->payroll_contract}}</a></div>
                    </div>
                </div>
            </div>
            @endif
            @if($detail->extra_option!="")
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group d-flex">
                        <div class="col-sm-6"><label for="first_name">Extra Option</label></div>
                        <div class="col-sm-6"><a data-fancybox="gallery" href="{{asset('uploads/deliverypersondetail/'.$detail->dp_id.'/'.$detail->extra_option)}}">{{$detail->extra_option}}</a></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
      
         <div class="box-footer">
            <div class="form-group">
                <div class="col-md-12">
                {{-- <button type="submit" class="btn btn-default btn-save btn-flat " style="margin-right:5px;">{{!empty($state)? 'Update':'Save'}}</button> --}}
                {{-- <a href="{{ url('admin/country/view/'.$cid) }}" type="submit" class="btn btn-default btn-cancel btn-flat">Cancel</a> --}}
               
                </div>
            </div>
        </div>

    </div>



