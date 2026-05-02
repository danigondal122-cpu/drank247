<div class="row d-flex align-items-stretch">
    @foreach ($addresses as $address)
        <div class="col-12 col-sm-6 col-md-6 align-items-stretch">
            <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0"></div>
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-12">
                            <!-- <p class="text-muted text-sm">534, Klarendalseweg, Arnhem, Gelderland</p>-->
                            <ul class="m-2  fa-ul text-muted">
                                <li class="small"><span class="fa-li"></span>{{ $address->address }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        @if ($address->default != '1')
                            <a href="javascript:;" class="btn btn-sm btn-primary"
                                onclick="updateDefaultAddress('{{ $address->id }}','{{ $address->address }}','{{ $address->address_postcode }}')">
                                SELECT
                            </a>
                        @else
                            <button type="button" disabled class="btn btn-sm btn-secondary">
                                SELECTED
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
