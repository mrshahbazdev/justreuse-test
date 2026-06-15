<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Manage Ad Zones</h1>
    <button wire:click="create()" class="bg-green-600 text-white px-4 py-2 rounded-lg mb-4">Create New Ad Zone</button>

    @if($isOpen)
        @include('livewire.admin.ad-zone-modal')
    @endif

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4">{{ session('message') }}</div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Name</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Location</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Price/Day</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zones as $zone)
                <tr>
                    <td class="px-5 py-5 border-b text-sm">{{ $zone->name }}</td>
                    <td class="px-5 py-5 border-b text-sm">{{ $zone->page_location }}</td>
                    <td class="px-5 py-5 border-b text-sm">${{ number_format($zone->price_per_day, 2) }}</td>
                    <td class="px-5 py-5 border-b text-sm">
                        <span class="{{ $zone->is_active ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} py-1 px-3 rounded-full text-xs">
                            {{ $zone->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b text-sm">
                        <button wire:click="edit('{{ $zone->id }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button wire:click="delete('{{ $zone->id }}')" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
