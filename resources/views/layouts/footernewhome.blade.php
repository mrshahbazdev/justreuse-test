<footer class="border-t border-slate-200 bg-white">
    @php
        $footerPages = \App\Models\TblStaticpage::select('title', 'slug')->get();
    @endphp
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">

        {{-- Links Row --}}
        <div class="flex items-center justify-center gap-x-4 gap-y-2 flex-wrap text-sm mb-6">
            <a href="{{ URL::to('/contact-us') }}" class="font-medium text-slate-600 hover:text-orange-500 transition-colors">{{ __('messages.contact us') }}</a>
            @foreach($footerPages as $page)
                <span class="text-slate-300">·</span>
                <a href="{{ URL::to('/pages/' . $page->slug) }}" class="font-medium text-slate-600 hover:text-orange-500 transition-colors">{{ $page->title }}</a>
            @endforeach
        </div>

        {{-- Divider --}}
        <div class="border-t border-slate-100 pt-5"></div>

        {{-- Bottom Row: Logo + Copyright --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ URL::to('/') }}">
                <img src="{{ asset('storage/'.$settings['logo']) }}" alt="Site Logo" class="h-8 w-auto">
            </a>
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} JustReused. All Rights Reserved.</p>
        </div>

    </div>
</footer>
