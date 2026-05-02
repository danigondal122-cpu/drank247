@extends('layouts.user')

@push('extraStyle')
	<style>
		.card {
			border-radius: .25rem;
		}
	</style>
@endpush

@section('content')
	<x-user.content>
		<div class="row">
			<div class="col-md-8 mx-auto">
				<div class="card">
					<div class="card-body">
						{!! $content !!}
					</div>
				</div>
			</div>
		</div>
	</x-user.content>
@endsection