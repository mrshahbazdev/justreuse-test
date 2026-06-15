@extends('layouts.frontendother') {{-- Ya aapka jo bhi main layout hai --}}

@section('content')
    {{-- Yeh file Livewire component ko slug ke saath load karegi --}}
    @livewire('post-detail-component', ['slug' => $slug])
@endsection

