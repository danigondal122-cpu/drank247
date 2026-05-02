
  
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title"><b> Promo Code</b></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body" style="overflow-y: scroll;max-height:350px;">
            <div id="message_div"></div>
            <div class="row box-body">
                <div class="table-responsive">
                <table id="table" class="table table-bordered table-hover" style="border-radius:10px;" width="100%">
                    <tr style="background-color:#f2f2f2;">
                        <td><b>No.</b></td>
                        <td><b>Order No.</b></td>
                        <td><b>Customer Name</b></td>
                        <td ><b>Discount</b></td>
                        <td ><b>Order Date</b></td>
                    </tr>
                    <?php $i=1 ?>
                    @if($count>0)
                    @foreach($list as $value)
                     <tr>
                        <td>{{$i}}</td>
                        <td>{{$value->order_id}}</td>
                        <td>{{$value->customer_name}}</td>
                        <td>€ {{$value->order_discount}}</td>
                        <td>{{$value->orderdate}}</td>
                    </tr>       
                    <?php $i++ ?>
  
                   @endforeach
                   @else 
                   <tr>
                        <td colspan="5">No Record Found</td>
                        
                   </tr>     
                  @endif
                </table>
            </div>
            </div> 
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



