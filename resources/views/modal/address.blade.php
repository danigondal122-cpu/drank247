<div class="row d-flex align-items-stretch">
	@foreach ($addresses as $address)
		<div class="col-12 col-sm-6 col-md-4 col-xs-12 col-lg-4 d-flex align-items-stretch">
			<div class="col-12 card">
				<div class="card-body pt-0" >
					<ul class="m-2 fa-ul text-muted">
						<li class="small"><span class="fa-li"></span>{{ $address->address }}</li>
					</ul>
				</div>
				<div class="card-footer px-0">
					<div class="text-center">
						@if (!$address->default)
							<a href="javascript:;" class="btn btn-sm btn-primary" style="font-size: 11px !important;" onclick="setDefaultAddress('{{ $address->id }}')">
								SET DEFAULT
							</a>
						@endif
						<a href="javascript:;" class="btn btn-sm btn-info" onclick="editAddress('{{ $address->id }}')">
							<i class="fas fa-edit"></i>
						</a>
						<a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteAddress('{{ $address->id }}')">
							<i class="fas fa-trash"></i>
						</a>
					</div>
				</div>
			</div>
		</div> 
	@endforeach
</div>