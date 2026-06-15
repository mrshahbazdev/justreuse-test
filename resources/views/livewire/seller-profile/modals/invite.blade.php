<!-- Invite Friends Modal -->
<div x-data="{ show: @entangle('showInviteModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen">
         <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="show" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
            <form wire:submit.prevent="sendInvite">
                <div class="bg-white px-6 py-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Invite Friends</h3>
                    <p class="text-gray-600 mb-4">Invite your friends to join via email.</p>
                    <div>
                        <label for="invite_emails" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input wire:model="inviteEmails" type="email" id="invite_emails" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="friend@example.com">
                        @error('inviteEmails') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        <span wire:loading wire:target="sendInvite" class="animate-spin mr-2">&#9696;</span> Send Invite
                    </button>
                    <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
