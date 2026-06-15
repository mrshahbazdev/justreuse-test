@extends('layouts.frontnewhome')
@section('content')
    <div>
        @livewire('insights-component', ['postId' => request()->segment(2)])
    </div>
@endsection
