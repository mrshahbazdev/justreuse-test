
@extends('layouts.frontnewhome') {{-- Ya aapka jo bhi layout hai --}}

@section('content')
    
        @livewire('post-component-edit', ['postId' => request()->query('id')])
    
@endsection
