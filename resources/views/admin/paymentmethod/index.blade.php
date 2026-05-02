@extends('admin.layout.layout')
@section('header_content')
    <div class="content-header">
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Methods</h3>
                </div>
                <form method="post" id="updatepaymentmethod" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            @foreach ($paymentmethods as $key => $row)
                                @if ($row->method_name == 'cod')
                                    @php $title = 'Cash On Delivery' @endphp
                                @elseif($row->method_name == 'pin')
                                    @php $title = 'Pin at Door' @endphp
                                @elseif($row->method_name == 'credit_card')
                                    @php $title = 'Credit card' @endphp
                                @elseif($row->method_name == 'paypal')
                                    @php $title = 'Paypal' @endphp
                                @elseif($row->method_name == 'ideal')
                                    @php $title = 'Ideal' @endphp
                                @elseif($row->method_name == 'pin')
                                    @php $title = 'Pin at Door' @endphp
                                @elseif($row->method_name == 'bitpay')
                                    @php $title = 'Bitpay' @endphp
                                @elseif($row->method_name == 'gpay')
                                    @php $title = 'Google Pay' @endphp
                                @elseif($row->method_name == 'bancontact')
                                    @php $title = 'Bancontact' @endphp
                                @elseif($row->method_name == 'giropay')
                                    @php $title = 'Giropay' @endphp
                                @elseif($row->method_name == 'sofort_banking')
                                    @php $title = 'SOFORT Banking' @endphp
                                @elseif($row->method_name == 'trustly')
                                    @php $title = 'Trustly' @endphp
                                @elseif($row->method_name == 'eps_uberweisung')
                                    @php $title = 'EPS Uberweisung' @endphp
                                @elseif($row->method_name == 'przelewy24')
                                    @php $title = 'Przelewy24' @endphp
                                @elseif($row->method_name == 'idin')
                                    @php $title = 'IDIN' @endphp
                                @endif
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="{{ $row->method_name }}"
                                                name="payment_method[]" value="{{ $row->method_name }}"
                                                {{ $row->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="{{ $row->method_name }}">{{ $title }}</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script>
        $('#updatepaymentmethod').on('submit', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this)
            $('#updatepaymentmethod .is-invalid').removeClass('is-invalid');
            $('#updatepaymentmethod .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/paymentmethod/save',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(obj) {
                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).after('<p class="text-danger">' + obj.errors[key] + '</p>');
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#updatepaymentmethod')[0].reset();
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500)
                    }
                },

            })
        })
    </script>
@endsection
