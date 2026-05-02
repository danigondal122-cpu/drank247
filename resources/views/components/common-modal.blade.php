@props([
	'view' => null,
])

<div id="commonModal" class="modal fade" role="dialog">
	<div @class([
		'modal-dialog',
		'modal-lg' => $view == 'user' && !$attributes->has('dialogClass'),
		$attributes->get('dialogClass')
	])>
		<div class="modal-content" id="commonModalHtml" @style([
			'border-radius: 0.8rem; box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .5)' => $view == 'user'
		])>
			{{ $slot }}
		</div>
	</div>
</div>