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
                    <p class="pageText _mt12">Информация о постах обновляется раз в час (9:00, 10:00, 11:00), либо перед удалением если это вариант автопостинга</p>
                    <div class="streamStory">
                        @include('load.posts-info-load')
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>
@endsection
