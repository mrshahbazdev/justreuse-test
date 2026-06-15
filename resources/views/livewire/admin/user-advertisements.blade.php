<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">User Advertisements</h1>
        <select wire:model="filterStatus" class="border-gray-300 rounded-md">
            <option value="pending_approval">Pending Approval</option>
            <option value="approved">Approved (Live)</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
     @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">User</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Ad Zone</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Content</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Duration & Payment</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advertisements as $ad)
                <tr>
                    <td class="px-5 py-5 border-b text-sm">{{ $ad->user->name ?? 'N/A' }}</td>
                    <td class="px-5 py-5 border-b text-sm">{{ $ad->adZone->name ?? 'N/A' }}</td>
                    <td class="px-5 py-5 border-b text-sm">
                        @if(isset($ad->content['image']))
                            <a href="{{ asset('storage/' . $ad->content['image']) }}" target="_blank">
                                <img src="{{ asset('storage/' . $ad->content['image']) }}" class="w-24 h-12 object-contain">
                            </a>
                        @endif
                        <p class="font-semibold mt-1">{{ $ad->content['headline'] ?? 'No Headline' }}</p>
                    </td>
                    <td class="px-5 py-5 border-b text-sm">
                        <p>{{ \Carbon\Carbon::parse($ad->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($ad->end_date)->format('d M, Y') }}</p>
                        <p class="font-bold">${{ number_format($ad->total_amount, 2) }}</p>
                        <span class="capitalize py-1 px-3 rounded-full text-xs {{ $ad->payment_status == 'paid' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
                            {{ $ad->payment_status }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b text-sm">
                        @if($filterStatus === 'pending_approval')
                            <button wire:click="approveAd('{{ $ad->id }}')" class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Approve</button>
                            <button wire:click="rejectAd('{{ $ad->id }}')" class="ml-2 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Reject</button>
                        @else
                            <span class="text-gray-500">No actions</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">No advertisements found with status '{{ $filterStatus }}'.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $advertisements->links() }}
        </div>
    </div>
</div>
