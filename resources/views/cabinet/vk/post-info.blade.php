@extends('cabinet.layouts.master')

@section('title', 'Информация о посте "'.$post->title.'"')

@section('cabinet-content')
    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h2 class="pageTitle">{{ $post->title}}</h2>
                    <p class="pageText _mt12">Вся статистика поста собирается за 5 минут до обновления. Следите за статусом. Если при статусе "В обработке" статистика нулевая, то нужно подождать когда будет запущен следующий пост</p>
                    <div class="streamStory">
                        @include('load.posts-info-load')
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>
@endsection
