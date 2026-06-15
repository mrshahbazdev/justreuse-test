<!-- Followers List Modal -->
<div x-data="{ show: @entangle('showFollowersModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>
        
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Followers</h3>
                    <button @click="show = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="max-h-80 overflow-y-auto -mx-2 px-2">
                    @forelse($followers as $follower)
                    <a href="{{ route('seller.profile', $follower->id) }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition">
                        <img class="w-11 h-11 rounded-full object-cover border-2 border-gray-100" src="{{ $follower->profile_photo_path ? Storage::url($follower->profile_photo_path) : asset('storage/profile-avatar.jpg') }}" alt="{{ $follower->name }}">
                        <span class="ml-3 font-medium text-gray-800">{{ $follower->name }}</span>
                    </a>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">No followers yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
