@php
    use Carbon\Carbon;
@endphp

@foreach ($streamComments as $comment)
@php
    $commentDate = Carbon::createFromFormat('Y-m-d H:i:s', $comment->time, 'Europe/Moscow');
    $commentSend = $commentDate->format('d.m.Y в H:i');
@endphp
<div class="storyWrapperItem">
    <p class="storyWrapperList"><span>Время:</span><span>{{ $commentSend }}</span></p>
    <div class="lastComment">
        <a href="{{ $comment->link }}" class="accountInfo" target="_blank">
            <div class="accountAvatar" style="background: url({{$comment->avatar}}), #FFFFFF;"></div>
            <p class="accountName">{{$comment->name}}</p>
        </a>
        <p class="storyWrapperComment">{{$comment->comment}}</p>
    </div>
</div>
@endforeach
{{ $streamComments->links('layouts.paginate', ['currentPage' => $currentPage]) }}
