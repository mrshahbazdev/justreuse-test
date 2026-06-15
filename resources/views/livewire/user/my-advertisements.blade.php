<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Advertisements</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($advertisements as $ad)
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $ad->adZone->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">
                            Runs from {{ \Carbon\Carbon::parse($ad->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($ad->end_date)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-lg font-bold text-green-700">${{ number_format($ad->total_amount, 2) }}</span>
                         <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                            {{ $ad->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $ad->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $ad->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                        ">{{ str_replace('_', ' ', $ad->status) }}</span>
                    </div>
                </div>
                <div class="p-6 flex items-center gap-6">
                    @if(isset($ad->content['image']))
                        <img src="{{ asset('storage/' . $ad->content['image']) }}" class="w-48 h-24 object-contain rounded-md border bg-gray-100">
                    @endif
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $ad->content['headline'] ?? 'No Headline' }}</h3>
                        <a href="{{ $ad->content['link'] ?? '#' }}" target="_blank" class="text-sm text-blue-500 hover:underline truncate">{{ $ad->content['link'] ?? '' }}</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 border-2 border-dashed rounded-lg">
                <p class="text-xl text-gray-600 font-semibold">No Advertisements Found</p>
                <p class="text-gray-500 mt-2">You haven't created any ads yet.</p>
                <a href="{{ route('ads.create') }}" class="mt-6 inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-semibold">Create Your First Ad</a>
            </div>
        @endforelse

        <div class="mt-8">
            {{ $advertisements->links() }}
        </div>
    </div>
</div>
