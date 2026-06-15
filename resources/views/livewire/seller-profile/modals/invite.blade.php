<!-- Invite Friends Modal -->
<div x-data="{ show: @entangle('showInviteModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>

        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <form wire:submit.prevent="sendInvite">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">Invite Friends</h3>
                        <button @click="show = false" type="button" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Invite your friends to join JustReused via email.</p>
                    <div>
                        <label for="invite_emails" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input wire:model="inviteEmails" type="email" id="invite_emails" placeholder="friend@example.com" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none transition">
                        @error('inviteEmails') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button @click="show = false" type="button" class="flex-1 py-2.5 px-4 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition text-sm">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 py-2.5 px-4 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition text-sm">
                        <span wire:loading.remove wire:target="sendInvite"><i class="fas fa-paper-plane mr-1"></i> Send Invite</span>
                        <span wire:loading wire:target="sendInvite"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
