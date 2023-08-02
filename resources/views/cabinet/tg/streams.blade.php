@extends('cabinet.layouts.master')
@if($user->Subscribe)
@if($user->Subscribe->sub_status === 1 && unserialize($user->Subscribe->sub_config)["tg"]["streams"]["type1"] !== 1)
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
@section('title', 'Трансляции Телеграм')

@section('cabinet-content')
<div class="pageFlex">
    <div class="sidebar">
        @include('cabinet.layouts.sidebar')
    </div>
    <div class="pageWrapper">
        <div class="pageItems">
            <div class="pageItem _tabOpen" data-storage="tab">
                <h1 class="pageTitle _tab">
                    Создание трансляции
                    <div class="titleIcon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188267 4.17936C0.128661 4.2348 0.0813067 4.30098 0.048979 4.37401C0.0166513 4.44704 -9.18749e-08 4.52546 -1.05726e-07 4.60468C-1.19577e-07 4.6839 0.0166513 4.76232 0.0489789 4.83536C0.0813066 4.90839 0.128661 4.97456 0.188267 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.35169 11.9333 8.48042 11.8142L15.8103 5.03132C15.8703 4.97583 15.9181 4.9095 15.9506 4.83622C15.9832 4.76295 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37315C15.9181 4.29987 15.8703 4.23354 15.8103 4.17805C15.6872 4.06391 15.522 4 15.3499 4C15.1778 4 15.0125 4.06391 14.8894 4.17805L14.8894 4.17936Z" fill="white"/>
                        </svg>
                    </div>
                </h1>
                
                <p class="pageText _mt12">Создание трансляции может занять до 25 минут. Подробнее на <a href="{{route('guide')}}" class="instructorLink">Странице инструкций</a></p>
                @if(count(Auth::user()->TgChannel) > 0)
                <div class="tabPageItem">
                    <form class="tabPageForm" action="{{ route('tgStreamCreate') }}" enctype='multipart/form-data'>
                    <div class="tabPageInput" data-name="name">
                        <input type="text" name="name" class="formInput" placeholder="Название трансляции">
                    </div>
                    <div class="tabPageInput tabPageSelect" data-name="channel">
                        <div class="formSelect">
                            @foreach (Auth::user()->TgChannel as $key => $tgChannel)
                            @if ($key === 0)
                                <input type="hidden" name="channelId" value="{{$tgChannel->id}}">
                                <div class="selectHeader">
                                    <p>Канал: {{$tgChannel->title}}</p>
                                    <svg class="selectArrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188268 4.17936C0.128661 4.2348 0.0813067 4.30098 0.0489791 4.37401C0.0166514 4.44704 0 4.52546 0 4.60468C0 4.6839 0.0166514 4.76232 0.0489791 4.83536C0.0813067 4.90839 0.128661 4.97456 0.188268 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.3517 11.9333 8.48042 11.8142L15.8103 5.03131C15.8703 4.97583 15.9181 4.90949 15.9506 4.83622C15.9832 4.76294 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37314C15.9181 4.29987 15.8703 4.23353 15.8103 4.17805C15.6872 4.0639 15.522 4 15.3499 4C15.1778 4 15.0125 4.0639 14.8894 4.17805V4.17936Z" fill="white"/>
                                    </svg>
                                </div>
                            @endif
                            @endforeach
                            <div class="selectFlow">
                                @foreach (Auth::user()->TgChannel as $key => $tgChannel)
                                @if ($key === 0)
                                <p class="selectItem _active" data-type="channel" data-id="{{ $tgChannel->id }}"><span class="_name">Канал: {{$tgChannel->title}}</span></p>
                                @else
                                <p class="selectItem" data-type="channel" data-id="{{ $tgChannel->id }}"><span class="_name">Канал: {{$tgChannel->title}}</span></p>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="streamTypes">
                        <input type="hidden" name="type" value="interval">
                        <p class="streamType _active" aria-label="Тип 1:" data-type="interval">Интервал</p>
                        <p class="streamType" aria-label="Тип 2:" data-type="date">Дата</p>
                        <p class="streamType" aria-label="Тип 3:" data-type="daily">Ежедневно</p>
                    </div>
                    <div class="tabPageInput tabPageSelect" data-name="tabs">
                        <div class="streamTabs">
                            <div class="formSelect _type _active" data-type="interval">
                                <input type="hidden" name="interval" value="">
                                <div class="selectHeader">
                                    <p>Интервал повтора</p>
                                    <svg class="selectArrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188268 4.17936C0.128661 4.2348 0.0813067 4.30098 0.0489791 4.37401C0.0166514 4.44704 0 4.52546 0 4.60468C0 4.6839 0.0166514 4.76232 0.0489791 4.83536C0.0813067 4.90839 0.128661 4.97456 0.188268 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.3517 11.9333 8.48042 11.8142L15.8103 5.03131C15.8703 4.97583 15.9181 4.90949 15.9506 4.83622C15.9832 4.76294 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37314C15.9181 4.29987 15.8703 4.23353 15.8103 4.17805C15.6872 4.0639 15.522 4 15.3499 4C15.1778 4 15.0125 4.0639 14.8894 4.17805V4.17936Z" fill="white"/>
                                    </svg>
                                </div>
                                <div class="selectFlow">
                                    <p class="selectItem" data-id="60"><span class="_name">Раз в 1 час</span><span class="_r">Не желательно</span></p>
                                    <p class="selectItem" data-id="120"><span class="_name">Раз в 2 часа</span><span class="_b">Оптимально</span></p>
                                    <p class="selectItem" data-id="240"><span class="_name">Раз в 4 часа</span><span class="_g">То что нужно</span></p>
                                    <p class="selectItem" data-id="360"><span class="_name">Раз в 6 часов</span><span class="_g">То что нужно</span></p>
                                    <p class="selectItem" data-id="480"><span class="_name">Раз в 8 часов</span><span class="_g">То что нужно</span></p>
                                </div>
                            </div>
                            <div class="dataPicker _type" data-type="date">
                                <input type="hidden" name="date" value="">
                                <p class="dataPickerText">
                                    Выберите дату и время
                                </p>
                            </div>
                            <div class="formSelect _type" data-type="daily">
                                <input type="hidden" name="daily" value="">
                                <div class="selectHeader">
                                    <p>Ежедневный интервал</p>
                                    <svg class="selectArrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188268 4.17936C0.128661 4.2348 0.0813067 4.30098 0.0489791 4.37401C0.0166514 4.44704 0 4.52546 0 4.60468C0 4.6839 0.0166514 4.76232 0.0489791 4.83536C0.0813067 4.90839 0.128661 4.97456 0.188268 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.3517 11.9333 8.48042 11.8142L15.8103 5.03131C15.8703 4.97583 15.9181 4.90949 15.9506 4.83622C15.9832 4.76294 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37314C15.9181 4.29987 15.8703 4.23353 15.8103 4.17805C15.6872 4.0639 15.522 4 15.3499 4C15.1778 4 15.0125 4.0639 14.8894 4.17805V4.17936Z" fill="white"/>
                                    </svg>
                                </div>
                                <div class="selectFlow">
                                    <p class="selectItem" data-id="0"><span class="_name">Ежедневно в 00:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="1"><span class="_name">Ежедневно в 01:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="2"><span class="_name">Ежедневно в 02:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="3"><span class="_name">Ежедневно в 03:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="4"><span class="_name">Ежедневно в 04:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="5"><span class="_name">Ежедневно в 05:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="6"><span class="_name">Ежедневно в 06:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="7"><span class="_name">Ежедневно в 07:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="8"><span class="_name">Ежедневно в 08:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="9"><span class="_name">Ежедневно в 09:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="10"><span class="_name">Ежедневно в 10:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="11"><span class="_name">Ежедневно в 11:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="12"><span class="_name">Ежедневно в 12:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="13"><span class="_name">Ежедневно в 13:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="14"><span class="_name">Ежедневно в 14:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="15"><span class="_name">Ежедневно в 15:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="16"><span class="_name">Ежедневно в 16:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="17"><span class="_name">Ежедневно в 17:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="18"><span class="_name">Ежедневно в 18:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="19"><span class="_name">Ежедневно в 19:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="20"><span class="_name">Ежедневно в 20:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="21"><span class="_name">Ежедневно в 21:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="22"><span class="_name">Ежедневно в 22:00</span><span class="_r">МСК</span></p>
                                    <p class="selectItem" data-id="23"><span class="_name">Ежедневно в 23:00</span><span class="_r">МСК</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="tabPageInput _full" data-name="file">
                            <div class="fileInput">
                                <div class="progressbar"></div>
                                <div class="myFile">
                                    <p class="fileName">Файл не выбран</p>
                                    <div class="myFileBtn">
                                        <span>Обзор...</span>
                                        <input type="file" name="file[]" id="file" class="hidden" accept="video/mp4, video/avi">
                                    </div>
                                </div>
                                <p class="fileText">
                                    <span>Статус загрузки:</span>
                                    <span class="_status">Файл не обнаружен</span>
                                </p>
                                <p class="fileText">
                                    <span>Размер файла:</span>
                                    <span class="_size">0 mb</span>
                                </p>
                                <p class="fileText">
                                    <span>Число файлов:</span>
                                    <span class="_filecounts">0 / 1</span>
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="duration" value="">
                        <button class="formBtn _full  _disabled">Создать трансляцию</button>
                    </form>
                </div>
                @endif
            </div>
            <div class="pageItem">
                <h2 class="pageTitle">Ваши трансляции</h2>
                <p class="pageText _mt12">Все трансляции, которые не активны больше 7 дней автоматически удаляются из базы данных без возможности их восстановления</p>
                <div class="pageTabs" data-name="classic">
                    <p class="pageTab _active" data-tab="all">Все стримы</p>
                    @if(count(Auth::user()->TgChannel) > 0)
                        @foreach (Auth::user()->TgChannel as $tgChannel)
                                <p class="pageTab" data-tab="{{$tgChannel->id}}" data-channelId="{{$tgChannel->id}}" data-action="{{route('paginate')}}" data-type="channel-streams">{{$tgChannel->title}}</p>
                        @endforeach
                    @endif
                </div>
                <div class="tabItem _active" data-tab="all">
                    <div class="storyWrapper">
                        @include('load.tg_streams-load')
                    </div>
                </div>
                @if(count(Auth::user()->TgChannel) > 0)
                    @foreach (Auth::user()->tgChannel as $tgChannel)
                        <div class="tabItem" data-tab="{{$tgChannel->id}}" data-type="channel">
                            <div class="rowFlex">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @include('cabinet.layouts.footer')
    </div>
</div>
</div>
</div>
<script src="/assets/js/cabinet/datapicker.js"></script>
<script src="/assets/js/cabinet/streams.js"></script>
@endsection
