<div class="fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="store">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $template_id ? 'Edit' : 'Create' }} Ad Template</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Ad Zone</label>
                            <select wire:model="ad_zone_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select a Zone</option>
                                @foreach($adZones as $zone)
                                    @php
                                        // === YEH MUKAMMAL FIX HAI: Data ko safe tareeqe se access karein ===
                                        $specs = is_array($zone->specifications) ? $zone->specifications : json_decode($zone->specifications, true);
                                        $width = $specs['width'] ?? 'N/A';
                                        $height = $specs['height'] ?? 'N/A';
                                    @endphp
                                    <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $width }}x{{ $height }})</option>
                                @endforeach
                            </select>
                            @error('ad_zone_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Template Name</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">HTML Content</label>
                            <textarea wire:model="html_content" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm font-mono text-sm" placeholder="Use placeholders like __IMAGE_URL__, __HEADLINE__, __LINK__"></textarea>
                            @error('html_content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="btn-primary">Save</button>
                    <button wire:click="closeModal()" type="button" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

