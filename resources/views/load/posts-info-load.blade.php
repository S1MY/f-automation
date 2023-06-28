@php
    use Carbon\Carbon;
@endphp

@foreach ($postInfo as $post)
@php
    $nowTime2 = Carbon::now('Europe/Moscow')->startOfMinute();
    $updatedAt = $nowTime2->setTime($post->daily, 0, 0);
    echo $nowTime2;
    echo $updatedAt;
    $postDate = Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at, 'Europe/Moscow');
    $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
    $dateView = $postDate->format('H:i d.m.Y');
    $diffCheck = 0;
    if($post->type === 'daily'){
        if (strtotime($nowTime) >= strtotime($postDate->addDay())) {
            $diffCheck = 1;
        }
    }elseif ($post->type === 'interval') {
        if(strtotime($nowTime) >= strtotime($postDate->addMinutes($post->interval))){
            $diffCheck = 1;
        }
    }elseif ($post->type === 'date') {
        $postDate = Carbon::createFromFormat('Y-m-d H:i:s', $post->date, 'Europe/Moscow');
        if(strtotime($nowTime) >= strtotime($postDate)){
            $diffCheck = 1;
        }
    }
@endphp
<div class="streamStoryItem">
    <p class="streamStoryList"><span>Время:</span><span>{{$dateView}}</span></p>
    @if ($diffCheck >= 1)
        <p class="streamStoryList"><span>Статус:</span><span class="_complete">Завершена</span></p>
    @else
        <p class="streamStoryList"><span>Статус:</span><span class="_loading">В обработке</span></p>
    @endif
    <p class="cardLine">Только для друзей: <span>{{($post->friends_only === 1) ? 'Да' : 'Нет'}}</span></p>
    <p class="cardLine">Скрытые комментарии: <span>{{($post->close_comments === 1) ? 'Да' : 'Нет'}}</span></p>
    <p class="cardLine">Закреплять на стене: <span>{{($post->pin_new === 1) ? 'Да' : 'Нет'}}</span></p>
    <p class="cardLine">Удалять старый пост: <span>{{($post->remove_old === 1) ? 'Да' : 'Нет'}}</span></p>
    <p class="streamStoryList"><span>Просмотров:</span>
        <span class="storyIcon">
            {{$post->viewes}}
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4663 7.39768C12.3537 9.30146 9.85605 10.848 7.00005 10.848C4.14405 10.848 1.64637 9.30146 0.533805 7.39768C0.405398 7.17827 0.405398 6.82171 0.533805 6.60231C1.64637 4.69852 4.14405 3.15196 7.00005 3.15196C9.85605 3.15196 12.3537 4.69852 13.4663 6.60231C13.5947 6.82171 13.5947 7.17827 13.4663 7.39768ZM4.20159 6.99999C4.20159 5.45562 5.45568 4.20152 7.00005 4.20152C8.54443 4.20152 9.79852 5.45562 9.79852 6.99999C9.79852 8.54437 8.54443 9.79846 7.00005 9.79846C5.45568 9.79846 4.20159 8.54437 4.20159 6.99999Z" fill="#FF5437"/>
                <path d="M4.74707 7C4.74707 5.7564 5.7566 4.74687 7.00019 4.74687C8.24379 4.74687 9.25332 5.7564 9.25332 7C9.25332 8.24359 8.24379 9.25312 7.00019 9.25312C5.7566 9.25312 4.74707 8.24359 4.74707 7Z" fill="#FF5437"/>
            </svg>
        </span>
    </p>
    <p class="streamStoryList"><span>Лайков:</span>
        <span class="storyIcon">
            {{$post->likes}}
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.19717 13.4419C7.13758 13.4799 7.06944 13.5 7 13.5C6.93056 13.5 6.86242 13.4799 6.80283 13.4419C6.47076 13.2347 -1.34593 8.29845 0.202881 3.73811C0.994148 1.41372 2.35098 0.717237 3.34979 0.540352C4.00852 0.448217 4.67825 0.514332 5.30988 0.733848C5.9415 0.953363 6.51899 1.32071 7 1.80895C7.48101 1.32071 8.0585 0.953363 8.69012 0.733848C9.32174 0.514332 9.99148 0.448217 10.6502 0.540352C11.649 0.717237 13.0059 1.41372 13.7971 3.73811C15.3459 8.29845 7.52924 13.2347 7.19717 13.4419V13.4419Z" fill="#FF5437"/>
            </svg>
        </span>
    </p>
    <p class="streamStoryList"><span>Репостов:</span>
        <span class="storyIcon">
            {{$post->reposts}}
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.8046 4.8142L8.55458 0.147536C8.47073 0.0721965 8.36681 0.0228209 8.25544 0.0054042C8.14407 -0.0120125 8.03004 0.0032788 7.92719 0.0494215C7.82434 0.0955642 7.7371 0.170574 7.67606 0.265342C7.61502 0.360111 7.58281 0.470562 7.58333 0.583286V2.45404C5.831 2.9242 0 5.11287 0 13.4166C0.000213578 13.552 0.0475393 13.6832 0.133861 13.7875C0.220182 13.8919 0.34012 13.9629 0.473105 13.9885C0.606091 14.0141 0.743836 13.9926 0.862718 13.9278C0.9816 13.8629 1.07421 13.7587 1.12467 13.633C2.85425 9.30879 6.22592 8.40637 7.58275 8.21795V9.91662C7.58263 10.0292 7.61511 10.1395 7.67628 10.234C7.73744 10.3286 7.82466 10.4034 7.92742 10.4495C8.03018 10.4956 8.14408 10.5109 8.25537 10.4937C8.36666 10.4765 8.47058 10.4274 8.55458 10.3524L13.8046 5.6857C13.8661 5.63098 13.9153 5.56386 13.949 5.48875C13.9827 5.41365 14.0001 5.33227 14.0001 5.24995C14.0001 5.16764 13.9827 5.08625 13.949 5.01115C13.9153 4.93605 13.8661 4.86893 13.8046 4.8142Z" fill="#FF5437"/>
            </svg>
        </span>
    </p>
    @if ($post->close_comments === 0)
        <p class="streamStoryList"><span>Комментариев:</span>
            <span class="storyIcon">
                {{$post->comments}}
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.993 1.4C13.993 0.6265 13.3735 0 12.6 0H1.4C0.6265 0 0 0.6265 0 1.4V9.8C0 10.5735 0.6265 11.2 1.4 11.2H11.2L14 14L13.993 1.4Z" fill="#FF5437"/>
                </svg>
            </span>
        </p>
    @endif
</div>
@endforeach
{{ $postInfo->links('layouts.paginate', ['curentPage' => $curentPage]) }}
