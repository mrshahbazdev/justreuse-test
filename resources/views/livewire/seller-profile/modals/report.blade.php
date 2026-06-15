<!-- Report User Modal -->
<div x-data="{ show: @entangle('showReportModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>

        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            @if($reportStatus === 'success')
                <div class="p-6 text-center">
                    <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Report Submitted</h3>
                    <p class="text-gray-500 text-sm mb-4">Thank you. We'll review this report shortly.</p>
                    <button @click="show = false" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">Close</button>
                </div>
            @elseif($reportStatus === 'already_reported')
                <div class="p-6 text-center">
                    <div class="w-14 h-14 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Already Reported</h3>
                    <p class="text-gray-500 text-sm mb-4">You've already reported this user. We're looking into it.</p>
                    <button @click="show = false" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">Close</button>
                </div>
            @else
                <form wire:submit.prevent="submitReport">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Report User</h3>
                            <button @click="show = false" type="button" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for reporting</label>
                                @foreach($report_types as $type)
                                    <label class="flex items-center p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer mb-2 transition">
                                        <input wire:model="reportType" type="radio" value="{{ $type->id }}" class="form-radio h-4 w-4 text-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $type->name }}</span>
                                    </label>
                                @endforeach
                                @error('reportType') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Additional details</label>
                                <textarea wire:model="reportComment" rows="3" maxlength="500" placeholder="Describe the issue..." class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none resize-none transition"></textarea>
                                @error('reportComment') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-6 flex gap-3">
                        <button @click="show = false" type="button" class="flex-1 py-2.5 px-4 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 py-2.5 px-4 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition text-sm">
                            <span wire:loading.remove wire:target="submitReport">Submit Report</span>
                            <span wire:loading wire:target="submitReport"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
