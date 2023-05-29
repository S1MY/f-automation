@extends('cabinet.layouts.master')

@section('title', 'Посты Вконтакте')

@section('cabinet-content')
<div class="pageFlex">
    <div class="sidebar">
        @include('cabinet.layouts.sidebar')
    </div>
    <div class="pageWrapper">
        <div class="pageItems">
            Посты тут должны быть да
        </div>
        @include('cabinet.layouts.footer')
    </div>
</div>
@endsection
