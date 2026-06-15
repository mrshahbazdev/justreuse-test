@props(['submit'])

<div {{ $attributes->merge(['class' => 'w-full float-left']) }}>
    <x-jet-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-jet-section-title>

    <div class="w-full max-w-2xl mx-auto my-4">
        <form wire:submit.prevent="{{ $submit }}">
            <div class="shadow overflow-hidden sm:rounded-md lg:shadow-lg border border-gray-200 ">
				<div class="px-4 py-5 bg-white sm:p-8 w-full float-left">
					<div class="w-full float-left">
						<div class="w-full inline-block">
							{{ $form }}
						</div>
					</div>

					@if (isset($actions))
						<div class="w-full float-left text-right">
							{{ $actions }}
						</div>
					@endif
				</div>
            </div>
        </form>
    </div>
</div>
