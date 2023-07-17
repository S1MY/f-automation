@extends('cabinet.layouts.master')

@php
use Carbon\Carbon;
$storyDate = Carbon::createFromFormat('Y-m-d H:i:s', $streamStory->created_at, 'Europe/Moscow');
$storyTime = $storyDate->format('d.m.Y в H:i');
@endphp
@section('title', 'Комментарии к трансляции "{{$stream->title}} {{$storyTime}}"')

@if($user->Subscribe)
@if($user->Subscribe->sub_status === 1 && unserialize($user->Subscribe->sub_config)["vk"]["streams"]["type1"] !== 1)
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
@if ($streamSettings->type === 'date')
<script>
    window.location.href = "{{ route('streams') }}";
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
                    <h2 class="pageTitle">{{ $stream->title}} {{$storyTime}}</h2>
                    <p class="pageText _mt12">Комментарии, оставленные пользователями во время трансляции</p>
                    <div class="storyWrapper">
                        @include('load.stream-comments-load')
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

    <script src="/assets/js/cabinet/datapicker.js"></script>
    <script src="/assets/js/cabinet/streams.js"></script>
@endsection
