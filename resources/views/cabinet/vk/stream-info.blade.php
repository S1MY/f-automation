@extends('cabinet.layouts.master')

@section('title', 'Информация о стриме "{{$stream->title}}"')

@if($user->Subscribe)
@if($user->Subscribe->sub_status === 1 && unserialize($user->Subscribe->sub_config)["vk"]["streams"]["type1"] !== 1)
    <script>
        window.location.href = "{{ route('cabinet') }}";
    </script>
@endif
@else
<script>
    window.location.href = "{{ route('cabinet') }}";
</script>
@endif
@if ($streamSettings->type === 'date')
<script>
    window.location.href = "{{ route('cabinet') }}";
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
                    <h2 class="pageTitle">{{ $stream->title}}</h2>
                    <p class="pageText _mt12">Вся статистика трансляции собирается за 5 минут до обновления. Следите за статусом. Если при статусе "В обработке" статистика нулевая, то нужно подождать когда будет запущен следующий стрим</p>
                    <div class="storyWrapper">
                        @include('load.stream-info-load')
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>
@endsection
