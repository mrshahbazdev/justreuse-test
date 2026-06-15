<div x-data="{ show: @entangle('showFollowingModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen">
        <!-- Background overlay -->
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div x-show="show" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
            <div class="bg-white px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Following</h3>
                <div class="max-h-96 overflow-y-auto">
                    @if(isset($following) && count($following) > 0)
                        @foreach($following as $follow)
                            <a href="{{ url('seller-profile/' . $follow->id) }}" class="flex items-center py-3 border-b hover:bg-gray-50">
                                <img class="w-12 h-12 rounded-full mr-4" src="{{ $follow->profile_photo_url }}" alt="{{ $follow->name }}">
                                <span class="font-medium text-gray-800">{{ $follow->name }}</span>
                            </a>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-4">This user isn't following anyone yet.</p>
                    @endif
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

