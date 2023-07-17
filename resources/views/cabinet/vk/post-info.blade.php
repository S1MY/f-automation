@extends('cabinet.layouts.master')

@section('title', 'Информация о посте "{{$post->title}}"')

@if($user->Subscribe)
@if($user->Subscribe->sub_status === 1 && unserialize($user->Subscribe->sub_config)["vk"]["posts"]["type1"] !== 1)
    <script>
        window.location.href = "{{ route('cabinet') }}";
    </script>
@endif
@if ($user->Subscribe->sub_status === 0)
<script>
    window.location.href = "{{ route('cabinet') }}";
</script>
@endif
@else
<script>
    window.location.href = "{{ route('cabinet') }}";
</script>
@endif
@if ($postSettings->type === 'date')
<script>
    window.location.href = "{{ route('posts') }}";
</script>
@endif
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
                    <div class="storyWrapper">
                        @include('load.posts-info-load')
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>
@endsection
