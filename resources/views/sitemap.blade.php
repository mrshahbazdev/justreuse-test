@extends('layouts.frontend')
@section('content')
<div class="w-full float-left mb-3 sm:mb-6 py-8">
<div class="container mx-auto px-4">
    <h1 class="text-xl md:text-2xl font-bold text-black text-center uppercase">Sitemap</h1>

    @if(!empty($pages))
    <h4 class="text-lg sm:text-xl lg:text-xl font-bold text-black uppercase mt-6 mb-4">Pages</h4>
   <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
       
        @foreach($pages as $page)
        <?php $pageurl = URL::to($page->slug)?>
        <li><a href="{{$pageurl}}">{{$page->title}}</a></li>
        @endforeach
        
    </ul>
    @endif
    @if(!empty($staticpages))
        <h4 class="text-lg sm:text-xl lg:text-xl font-bold text-black uppercase mt-6 mb-4">Staticpages</h4>
    <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
       
        @foreach($staticpages as $page)
        <?php $staticpageurl = URL::to('pages/'.$page->slug)?>
        <li><a href="{{$staticpageurl}}">{{$page->title}}</a></li>
        @endforeach
      
        </ul>
        @endif
        @if(!empty($categories))
        <h4 class="text-lg sm:text-xl lg:text-xl font-bold text-black uppercase mt-6 mb-4">Category</h4>
        <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
        @foreach ($categories as $mainSlug => $mainData)
            <li>
            <?php $catUrl =  URL::to($mainSlug);?>
                <a href="{{$catUrl}}">{{ $mainData['title'] }}</a>
                @if (!empty($mainData['subcategories']))
                <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
                        @foreach ($mainData['subcategories'] as $subData)
                        <?php $subcatUrl =  URL::to($subData['slug']);?>
                            <li><a href="{{$subcatUrl}}">{{ $subData['title'] }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
        </ul>
        @endif

   </div>
</div>
@endsection