@extends('layouts.user')

{{-- <x-plugins vendors="owlcarousel" /> --}}

@section('content')
	<x-user.content>
		<x-slot:breadcrumbs :title="__('messages.category')">
			<li class="breadcrumb-item active">{{ __('messages.category') }}</li>
		</x-slot>

		<div class="row">
			<div class="col-md-11 mx-auto">
				<div class="row productlistdiv">
					@foreach ($categories as $category)
						<div class="col-md-4 col-lg-3 col-xl-2">
							<div class="card elevation-1 mb-3">
								<a href="{{ 'products/' . str_replace(' ', '_', $category->category_name) }}">
									<div class="p-1 d-flex" style="height: 150px;">
										<img src="{{ $category->image }}" class="card-img-top product_img" alt="...">
									</div>
									<hr class="card-border">  
									<div class="card-body p-2">
										<p class="font-1rem text-ellipsis text-center mb-1" style="color: #d42f7a">
											<b><span title="{{ $category->category_name }}">{{ $category->category_name }}</span></b>
										</p>
									</div>
								</a>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</x-user.content>
@endsection