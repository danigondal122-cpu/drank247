<div class="modal-header">
	<h4 class="modal-title"><b>{{ __('messages.productdetail') }}</b></h4>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
	<div class="col-12">
		<x-user.product-card
			class="elevation-1 my-2"
			addIcon="big"
			:product="$product"
			:detail="true"
		/>
	</div>
</div>