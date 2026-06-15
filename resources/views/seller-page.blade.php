<x-site-layout>
    {{-- Page Heading (Optional) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $seller->name }}'s Profile
        </h2>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                {{-- Livewire component ko yahan load karein aur seller variable pass karein --}}
                <livewire:seller-profile :seller="$seller" />
            </div>
        </div>
    </div>
</x-site-layout>