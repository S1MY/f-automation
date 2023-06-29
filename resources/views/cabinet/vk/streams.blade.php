@extends('cabinet.layouts.master')

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
@section('title', 'Трансляции Вконтакте')

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
                    <p class="pageText _mt12">Создание трансляции может занять до 25 минут. Подробнее в <a href="#" class="instructorLink" data-name="Как запустить трансляцию правильно" data-popup="streamStart">Инструкции</a></p>
                    <p class="editText">Внимание! Вы редактируете существующий стрим. Если хотите создать новый, то обновите страницу или нажмите <a href="#" class="editStop">сюда</a></p>
                    @if(count(Auth::user()->VkAccount) > 0)
                    <div class="tabPageItem">
                        <form class="tabPageForm" action="{{ route('streamCreate') }}">
                            <input type="hidden" name="accType" value="user">
                            <div class="tabPageInput" data-name="name">
                                <input type="text" name="name" class="formInput" placeholder="Название трансляции">
                            </div>
                        <div class="tabPageInput tabPageSelect" data-name="account">
                            <div class="formSelect">
                                @foreach (Auth::user()->VkAccount as $key => $vkAccount)
                                @if ($key === 0)
                                    <input type="hidden" name="accountId" value="{{$vkAccount->id}}">
                                    <div class="selectHeader">
                                        <p>Аккаунт: {{$vkAccount->name}}</p>
                                        <svg class="selectArrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188268 4.17936C0.128661 4.2348 0.0813067 4.30098 0.0489791 4.37401C0.0166514 4.44704 0 4.52546 0 4.60468C0 4.6839 0.0166514 4.76232 0.0489791 4.83536C0.0813067 4.90839 0.128661 4.97456 0.188268 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.3517 11.9333 8.48042 11.8142L15.8103 5.03131C15.8703 4.97583 15.9181 4.90949 15.9506 4.83622C15.9832 4.76294 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37314C15.9181 4.29987 15.8703 4.23353 15.8103 4.17805C15.6872 4.0639 15.522 4 15.3499 4C15.1778 4 15.0125 4.0639 14.8894 4.17805V4.17936Z" fill="white"/>
                                        </svg>
                                    </div>
                                @endif
                                @endforeach
                                <div class="selectFlow">
                                    @php 
                                        $groupPosts = unserialize($user->Subscribe->sub_config)['vk']['posts']['type2'];
                                    @endphp
                                    @foreach (Auth::user()->VkAccount as $key => $vkAccount)
                                    @if ($key === 0)
                                    <p class="selectItem _active" data-type="user" data-id="{{ $vkAccount->id }}"><span class="_name">Аккаунт: {{$vkAccount->name}}</span></p>
                                    @else
                                    <p class="selectItem" data-type="user" data-id="{{ $vkAccount->id }}"><span class="_name">Аккаунт: {{$vkAccount->name}}</span></p>
                                    @endif
                                    @if ($groupPosts === 1)
                                        @foreach ($vkAccount->VkGroup as $VkGroup)
                                        <p class="selectItem" data-type="group" data-id="{{ $VkGroup->id }}"><span class="_name">Группа: {{ $VkGroup->name }}</span></p>
                                        @endforeach
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
                            <div class="tabPageInput" data-name="desc">
                                <textarea name="description" class="formArea" placeholder="Описание трансляции"></textarea>
                            </div>
                            <div class="tabPageInput" data-name="file">
                                <div class="fileInput">
                                    <div class="progressbar"></div>
                                    <div class="myFile">
                                        <p class="fileName">Файл не выбран</p>
                                        <div class="myFileBtn">
                                            <span>Обзор...</span>
                                            <input type="file" name="file[]" id="file" class="hidden" accept="image/jpeg, video/mp4, video/avi, image/png">
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
                            <div class="tabPageInput">
                                <div class="customCheck">
                                    <span>Опубликовать на стене:</span>
                                    <input type="checkbox" class="checkInput" name="public" value="1" checked>
                                    <span class="_check">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.33317 11.3333L13.2498 6.41667C13.4026 6.26389 13.5971 6.1875 13.8332 6.1875C14.0693 6.1875 14.2637 6.26389 14.4165 6.41667C14.5693 6.56944 14.6457 6.76389 14.6457 7C14.6457 7.23611 14.5693 7.43056 14.4165 7.58333L8.9165 13.0833C8.74984 13.25 8.55539 13.3333 8.33317 13.3333C8.11095 13.3333 7.9165 13.25 7.74984 13.0833L5.58317 10.9167C5.43039 10.7639 5.354 10.5694 5.354 10.3333C5.354 10.0972 5.43039 9.90278 5.58317 9.75C5.73595 9.59722 5.93039 9.52083 6.1665 9.52083C6.40261 9.52083 6.59706 9.59722 6.74984 9.75L8.33317 11.3333Z" fill="white"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <input type="hidden" name="duration" value="">
                            <button class="formBtn _disabled">Создать трансляцию</button>
                        </form>
                        <!-- <form class="tabPageForm" data-action="/stream/edit">
                            <input type="hidden" name="streamId" value="1">
                            <div class="tabPageInput" data-name="name">
                                <input type="text" name="name" class="formInput" value="Название трансляции">
                            </div>
                            <div class="tabPageInput" data-name="account">
                                <div class="formSelect">
                                    <input type="hidden" name="accountId" value="15">
                                    <div class="selectHeader">
                                        <p>Аккаунт: Фамилия Имя</p>
                                        <svg class="selectArrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188268 4.17936C0.128661 4.2348 0.0813067 4.30098 0.0489791 4.37401C0.0166514 4.44704 0 4.52546 0 4.60468C0 4.6839 0.0166514 4.76232 0.0489791 4.83536C0.0813067 4.90839 0.128661 4.97456 0.188268 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.3517 11.9333 8.48042 11.8142L15.8103 5.03131C15.8703 4.97583 15.9181 4.90949 15.9506 4.83622C15.9832 4.76294 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37314C15.9181 4.29987 15.8703 4.23353 15.8103 4.17805C15.6872 4.0639 15.522 4 15.3499 4C15.1778 4 15.0125 4.0639 14.8894 4.17805V4.17936Z" fill="white"/>
                                        </svg>
                                    </div>
                                    <div class="selectFlow">
                                        <p class="selectItem _active" data-id="15"><span class="_name">Аккаунт: Фамилия Имя</span></p>
                                        <p class="selectItem" data-id="23"><span class="_name">Аккаунт: Фамилия Имя</span></p>
                                        <p class="selectItem" data-id="31"><span class="_name">Аккаунт: Фамилия Имя</p>
                                        <p class="selectItem" data-id="78"><span class="_name">Группа: Название</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="tabPageInput _full" data-name="desc">
                                <textarea name="description" class="formArea">Описание трансляции</textarea>
                            </div>
                            <div class="streamTypes _all">
                                <input type="hidden" name="type" value="interval">
                                <p class="streamType" data-type="interval">Интервал</p>
                                <p class="streamType" data-type="date">Дата</p>
                                <p class="streamType _active" data-type="daily">Ежедневно</p>
                                <p class="streamType" data-type="range">Диапазон</p>
                            </div>
                            <div class="tabPageInput" data-name="tabs">
                                <div class="streamTabs">
                                    <div class="formSelect _type" data-type="interval">
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
                                    <div class="formSelect _type _active" data-type="daily">
                                        <input type="hidden" name="daily" value="5">
                                        <div class="selectHeader">
                                            <p>Ежедневно в 05:00</p>
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
                                            <p class="selectItem _active" data-id="5"><span class="_name">Ежедневно в 05:00</span><span class="_r">МСК</span></p>
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
                            <div class="tabPageInput">
                                <div class="customCheck">
                                    <span>Опубликовать на стене:</span>
                                    <input type="checkbox" class="checkInput" name="public" value="1" checked>
                                    <span class="_check">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.33317 11.3333L13.2498 6.41667C13.4026 6.26389 13.5971 6.1875 13.8332 6.1875C14.0693 6.1875 14.2637 6.26389 14.4165 6.41667C14.5693 6.56944 14.6457 6.76389 14.6457 7C14.6457 7.23611 14.5693 7.43056 14.4165 7.58333L8.9165 13.0833C8.74984 13.25 8.55539 13.3333 8.33317 13.3333C8.11095 13.3333 7.9165 13.25 7.74984 13.0833L5.58317 10.9167C5.43039 10.7639 5.354 10.5694 5.354 10.3333C5.354 10.0972 5.43039 9.90278 5.58317 9.75C5.73595 9.59722 5.93039 9.52083 6.1665 9.52083C6.40261 9.52083 6.59706 9.59722 6.74984 9.75L8.33317 11.3333Z" fill="white"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <button class="formBtn _disabled">Создать трансляцию</button>
                        </form> -->
                    </div>
                    @endif
                </div>
                <div class="pageItem">
                    <h2 class="pageTitle">Ваши трансляции</h2>
                    <p class="pageText _mt12">Все трансляции, которые не активны больше 7 дней автоматически удаляются из базы данных без возможности их восстановления</p>
                    <div class="pageTabs" data-name="classic">
                        <p class="pageTab _active" data-tab="all">Все трансляции</p>
                        <p class="pageTab" data-tab="12">Фамилия Имя</p>
                        <p class="pageTab" data-tab="35">Название группы</p>
                    </div>
                    <div class="tabItem _active" data-tab="all">
                        <div class="rowFlex">
                            <div class="cardItem">
                                <div class="videoWrapper">
                                    <video controls class="videoGuid" data-index="0" preload="metadata">
                                    <source src="/files/test/streams/321.mp4" type="video/mp4"/>
                                    </video>
                                    <div class="videoContoller">
                                    <p class="togglePlay">
                                        <svg class="play" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.64612 11.8332C2.33007 12.0429 2.00991 12.0549 1.68565 11.869C1.36201 11.6838 1.2002 11.3975 1.2002 11.0103V0.99001C1.2002 0.602754 1.36201 0.316185 1.68565 0.130302C2.00991 -0.0549357 2.33007 -0.0426725 2.64612 0.167091L10.3735 5.17722C10.658 5.37084 10.8002 5.64515 10.8002 6.00014C10.8002 6.35512 10.658 6.62943 10.3735 6.82306L2.64612 11.8332Z" fill="white"/>
                                        </svg>
                                        <svg class="stop" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 12C1.45 12 0.979333 11.8043 0.588 11.413C0.196 11.021 0 10.55 0 10V2C0 1.45 0.196 0.979333 0.588 0.588C0.979333 0.196 1.45 0 2 0H10C10.55 0 11.021 0.196 11.413 0.588C11.8043 0.979333 12 1.45 12 2V10C12 10.55 11.8043 11.021 11.413 11.413C11.021 11.8043 10.55 12 10 12H2Z" fill="white"/>
                                        </svg>
                                    </p>
                                    <div class="videoTime"><span class="elapsed">00:00</span>&nbsp;|&nbsp;<span class="duration">00:00</span></div>
                                    <div class="videoProgress">
                                        <input class="seek" value="0" min="0" max="0" type="range" step="0.1">
                                    </div>
                                    <div class="volumeContol">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.2279 3.58652C11.1224 3.44871 10.9693 3.36076 10.8023 3.34203C10.6353 3.32329 10.4681 3.37529 10.3375 3.48659C10.2069 3.5979 10.1235 3.75939 10.1057 3.93553C10.088 4.11168 10.1373 4.28805 10.2428 4.42586C10.5618 4.88344 10.7339 5.4366 10.7339 6.00463C10.7339 6.57265 10.5618 7.12581 10.2428 7.58339C10.1675 7.68129 10.1201 7.79971 10.1061 7.9249C10.092 8.05009 10.112 8.1769 10.1636 8.29061C10.2151 8.40432 10.2963 8.50026 10.3975 8.56727C10.4987 8.63429 10.6158 8.66964 10.7353 8.66921C10.8297 8.66955 10.9229 8.64758 11.0081 8.60493C11.0934 8.56228 11.1685 8.50002 11.2279 8.42274C11.7288 7.72733 12 6.878 12 6.00463C12 5.13125 11.7288 4.28192 11.2279 3.58652ZM8.50618 0.0892485C8.41018 0.030782 8.30129 1.90735e-06 8.19044 1.90735e-06C8.07959 1.90735e-06 7.97069 0.030782 7.87469 0.0892485L3.78895 3.0536H0.631491C0.464009 3.0536 0.303387 3.12378 0.184959 3.24871C0.0665319 3.37363 0 3.54307 0 3.71974V8.28951C0 8.46618 0.0665319 8.63562 0.184959 8.76054C0.303387 8.88547 0.464009 8.95565 0.631491 8.95565H3.78895L7.8368 11.8867C7.94769 11.9624 8.07732 12.0018 8.20938 11.9999C8.37686 11.9999 8.53749 11.9298 8.65591 11.8048C8.77434 11.6799 8.84087 11.5105 8.84087 11.3338V0.675457C8.84057 0.554949 8.80928 0.436784 8.75034 0.333552C8.6914 0.23032 8.60702 0.145887 8.50618 0.0892485Z" fill="#202020"/>
                                            </svg>
                                        <input class="volume" value="1" min="0" max="1" type="range" step="0.01">
                                    </div>
                                    <p class="toggleScreen">
                                        <svg class="open" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.3708 7.629C4.30048 7.5587 4.20512 7.51921 4.10568 7.51921C4.00624 7.51921 3.91088 7.5587 3.84055 7.629L0.768555 10.701V8.625C0.768555 8.52554 0.729046 8.43016 0.65872 8.35983C0.588394 8.28951 0.493011 8.25 0.393555 8.25C0.294098 8.25 0.198716 8.28951 0.12839 8.35983C0.0580635 8.43016 0.0185547 8.52554 0.0185547 8.625V11.6062C0.0185547 11.7057 0.0580635 11.8011 0.12839 11.8714C0.198716 11.9417 0.294098 11.9812 0.393555 11.9812H3.3748C3.47426 11.9812 3.56964 11.9417 3.63997 11.8714C3.7103 11.8011 3.7498 11.7057 3.7498 11.6062C3.7498 11.5068 3.7103 11.4114 3.63997 11.3411C3.56964 11.2708 3.47426 11.2312 3.3748 11.2312H1.2988L4.3708 8.15925C4.44111 8.08893 4.4806 7.99356 4.4806 7.89412C4.4806 7.79469 4.44111 7.69932 4.3708 7.629ZM7.62881 7.629C7.69913 7.5587 7.79449 7.51921 7.89393 7.51921C7.99337 7.51921 8.08873 7.5587 8.15905 7.629L11.2311 10.701V8.625C11.2311 8.52554 11.2706 8.43016 11.3409 8.35983C11.4112 8.28951 11.5066 8.25 11.6061 8.25C11.7055 8.25 11.8009 8.28951 11.8712 8.35983C11.9415 8.43016 11.9811 8.52554 11.9811 8.625V11.6062C11.9811 11.7057 11.9415 11.8011 11.8712 11.8714C11.8009 11.9417 11.7055 11.9812 11.6061 11.9812H8.62481C8.52535 11.9812 8.42997 11.9417 8.35964 11.8714C8.28931 11.8011 8.24981 11.7057 8.24981 11.6062C8.24981 11.5068 8.28931 11.4114 8.35964 11.3411C8.42997 11.2708 8.52535 11.2312 8.62481 11.2312H10.7008L7.62881 8.15925C7.5585 8.08893 7.51901 7.99356 7.51901 7.89412C7.51901 7.79469 7.5585 7.69932 7.62881 7.629ZM7.62881 4.371C7.69913 4.4413 7.79449 4.48079 7.89393 4.48079C7.99337 4.48079 8.08873 4.4413 8.15905 4.371L11.2311 1.299V3.375C11.2311 3.47446 11.2706 3.56984 11.3409 3.64016C11.4112 3.71049 11.5066 3.75 11.6061 3.75C11.7055 3.75 11.8009 3.71049 11.8712 3.64016C11.9415 3.56984 11.9811 3.47446 11.9811 3.375V0.393749C11.9811 0.294293 11.9415 0.19891 11.8712 0.128584C11.8009 0.058258 11.7055 0.0187492 11.6061 0.0187492H8.62481C8.52535 0.0187492 8.42997 0.058258 8.35964 0.128584C8.28931 0.19891 8.24981 0.294293 8.24981 0.393749C8.24981 0.493205 8.28931 0.588588 8.35964 0.658914C8.42997 0.72924 8.52535 0.768749 8.62481 0.768749H10.7008L7.62881 3.84075C7.5585 3.91107 7.51901 4.00644 7.51901 4.10587C7.51901 4.20531 7.5585 4.30068 7.62881 4.371ZM4.3708 4.371C4.30048 4.4413 4.20512 4.48079 4.10568 4.48079C4.00624 4.48079 3.91088 4.4413 3.84055 4.371L0.768555 1.299V3.375C0.768555 3.47446 0.729046 3.56984 0.65872 3.64016C0.588394 3.71049 0.493011 3.75 0.393555 3.75C0.294098 3.75 0.198716 3.71049 0.12839 3.64016C0.0580635 3.56984 0.0185547 3.47446 0.0185547 3.375V0.393749C0.0185547 0.294293 0.0580635 0.19891 0.12839 0.128584C0.198716 0.058258 0.294098 0.0187492 0.393555 0.0187492H3.3748C3.47426 0.0187492 3.56964 0.058258 3.63997 0.128584C3.7103 0.19891 3.7498 0.294293 3.7498 0.393749C3.7498 0.493205 3.7103 0.588588 3.63997 0.658914C3.56964 0.72924 3.47426 0.768749 3.3748 0.768749H1.2988L4.3708 3.84075C4.44111 3.91107 4.4806 4.00644 4.4806 4.10587C4.4806 4.20531 4.44111 4.30068 4.3708 4.371Z" fill="#202020"/>
                                        </svg>
                                        <svg class="close" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.857143 9.42857H2.57143V11.1429C2.57143 11.6143 2.95714 12 3.42857 12C3.9 12 4.28571 11.6143 4.28571 11.1429V8.57143C4.28571 8.1 3.9 7.71429 3.42857 7.71429H0.857143C0.385714 7.71429 0 8.1 0 8.57143C0 9.04286 0.385714 9.42857 0.857143 9.42857ZM2.57143 2.57143H0.857143C0.385714 2.57143 0 2.95714 0 3.42857C0 3.9 0.385714 4.28571 0.857143 4.28571H3.42857C3.9 4.28571 4.28571 3.9 4.28571 3.42857V0.857143C4.28571 0.385714 3.9 0 3.42857 0C2.95714 0 2.57143 0.385714 2.57143 0.857143V2.57143ZM8.57143 12C9.04286 12 9.42857 11.6143 9.42857 11.1429V9.42857H11.1429C11.6143 9.42857 12 9.04286 12 8.57143C12 8.1 11.6143 7.71429 11.1429 7.71429H8.57143C8.1 7.71429 7.71429 8.1 7.71429 8.57143V11.1429C7.71429 11.6143 8.1 12 8.57143 12ZM9.42857 2.57143V0.857143C9.42857 0.385714 9.04286 0 8.57143 0C8.1 0 7.71429 0.385714 7.71429 0.857143V3.42857C7.71429 3.9 8.1 4.28571 8.57143 4.28571H11.1429C11.6143 4.28571 12 3.9 12 3.42857C12 2.95714 11.6143 2.57143 11.1429 2.57143H9.42857Z" fill="black"/>
                                            </svg>
                                    </p>
                                    </div>
                                    <div class="videoStarter">
                                        <svg class="starterPlay" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="64" rx="12" fill="#202020"/>
                                            <path d="M23.056 47.5551C22.2132 48.1145 21.3594 48.1464 20.4947 47.6507C19.6317 47.1567 19.2002 46.3934 19.2002 45.3607V18.64C19.2002 17.6073 19.6317 16.8432 20.4947 16.3475C21.3594 15.8535 22.2132 15.8862 23.056 16.4456L43.6624 29.8059C44.4209 30.3223 44.8002 31.0537 44.8002 32.0004C44.8002 32.947 44.4209 33.6785 43.6624 34.1948L23.056 47.5551Z" fill="white"/>
                                        </svg>
                                        <svg class="starterStop" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="64" rx="12" fill="#202020"/>
                                            <path d="M21.3333 48C19.8667 48 18.6116 47.4781 17.568 46.4347C16.5227 45.3893 16 44.1333 16 42.6667V21.3333C16 19.8667 16.5227 18.6116 17.568 17.568C18.6116 16.5227 19.8667 16 21.3333 16H42.6667C44.1333 16 45.3893 16.5227 46.4347 17.568C47.4781 18.6116 48 19.8667 48 21.3333V42.6667C48 44.1333 47.4781 45.3893 46.4347 46.4347C45.3893 47.4781 44.1333 48 42.6667 48H21.3333Z" fill="white"/>
                                            <rect width="32" height="32" fill="white" transform="translate(16 16)"/>
                                        </svg>
                                    </div>
                                    <div class="videoShadow"></div>
                                </div>
                                <div class="cardInformation">
                                    <div class="cardInformationTop">
                                        <h3 class="cardName">Название трансляции</h3>
                                        <p class="streamDesc">Описание трансляции, типо самый крутой и так далее, а по факту хуйня</p>
                                        <p class="cardLine">Владелец: <span>Фамилия Имя</span></p>
                                        <p class="cardLine">Автозапуск: <span>Активен</span></p>
                                        <p class="cardLine _nextStart">Следующий стрим: <span>20.11.2023 в 21:49 (МСК)</span></p>
                                    </div>
                                    <div class="cardControls">
                                        <a href="#" class="cardButton _autostart" data-stream="1" title="Выключить автозапуск">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.33333 22C4.41667 22 3.63222 21.6738 2.98 21.0217C2.32667 20.3683 2 19.5833 2 18.6667V5.33333C2 4.41667 2.32667 3.63222 2.98 2.98C3.63222 2.32667 4.41667 2 5.33333 2H18.6667C19.5833 2 20.3683 2.32667 21.0217 2.98C21.6738 3.63222 22 4.41667 22 5.33333V18.6667C22 19.5833 21.6738 20.3683 21.0217 21.0217C20.3683 21.6738 19.5833 22 18.6667 22H5.33333Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="cardButton _edit" data-stream="1" title="Изменить">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 22H3C2.73478 22 2.48043 21.8946 2.29289 21.7071C2.10536 21.5195 2 21.2652 2 21V3C2 2.73478 2.10536 2.48043 2.29289 2.29289C2.48043 2.10536 2.73478 2 3 2H15C15.2652 2 15.5195 2.10536 15.7071 2.29289C15.8946 2.48043 16 2.73478 16 3C16 3.26521 15.8946 3.51957 15.7071 3.7071C15.5195 3.89464 15.2652 4 15 4H4V20H20V8.99999C20 8.73477 20.1053 8.48042 20.2929 8.29288C20.4804 8.10535 20.7348 7.99999 21 7.99999C21.2652 7.99999 21.5195 8.10535 21.7071 8.29288C21.8946 8.48042 22 8.73477 22 8.99999V21C22 21.2652 21.8946 21.5195 21.7071 21.7071C21.5195 21.8946 21.2652 22 21 22Z" fill="white"/>
                                                <path d="M11.9851 14.0109C11.7379 14.0103 11.4997 13.9179 11.3168 13.7516C11.1214 13.5742 11.0044 13.3265 10.9913 13.063C10.9782 12.7994 11.0702 12.5414 11.247 12.3455L20.2247 2.37283C20.3098 2.26685 20.4157 2.17938 20.5359 2.1158C20.6561 2.05222 20.788 2.01388 20.9235 2.00313C21.0591 1.99237 21.1954 2.00943 21.3241 2.05326C21.4528 2.09709 21.5712 2.16676 21.672 2.25799C21.7728 2.34923 21.8538 2.4601 21.9102 2.5838C21.9666 2.7075 21.9971 2.84141 21.9998 2.97731C22.0025 3.11322 21.9774 3.24824 21.926 3.3741C21.8746 3.49995 21.7981 3.61398 21.701 3.70917L12.7233 13.6818C12.63 13.7851 12.5162 13.8678 12.3891 13.9245C12.2619 13.9811 12.1243 14.0106 11.9851 14.0109ZM16.9728 18H6.99752C6.73296 18 6.47924 17.8949 6.29217 17.7079C6.1051 17.5209 6 17.2672 6 17.0027C6 16.7382 6.1051 16.4846 6.29217 16.2975C6.47924 16.1105 6.73296 16.0054 6.99752 16.0054H16.9728C17.2373 16.0054 17.491 16.1105 17.6781 16.2975C17.8652 16.4846 17.9703 16.7382 17.9703 17.0027C17.9703 17.2672 17.8652 17.5209 17.6781 17.7079C17.491 17.8949 17.2373 18 16.9728 18Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="cardButton _remove" data-stream="1" title="Удалить">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.1416 2.80002V5.20006H15.8559V2.80002C15.8559 2.58784 15.7656 2.38435 15.6048 2.23432C15.4441 2.08429 15.2261 2 14.9987 2H8.99874C8.77142 2 8.5534 2.08429 8.39265 2.23432C8.23191 2.38435 8.1416 2.58784 8.1416 2.80002Z" fill="white"/>
                                                <path d="M3 4.39963V5.19964H21V4.39963C21 4.18745 20.9097 3.98396 20.7489 3.83393C20.5882 3.6839 20.3702 3.59961 20.1429 3.59961H3.85714C3.62981 3.59961 3.4118 3.6839 3.25105 3.83393C3.09031 3.98396 3 4.18745 3 4.39963ZM4.71429 6.79967L6.26743 20.5671C6.3117 20.9604 6.5104 21.3244 6.82521 21.589C7.14003 21.8535 7.54865 22 7.97229 22H16.0286C16.4522 22 16.8608 21.8535 17.1756 21.589C17.4905 21.3244 17.6892 20.9604 17.7334 20.5671L19.2857 6.79967H4.71429ZM8.63743 20.3975C8.52518 20.4057 8.41231 20.3932 8.30526 20.3606C8.19822 20.328 8.09911 20.2761 8.0136 20.2077C7.92809 20.1394 7.85787 20.0559 7.80695 19.9622C7.75603 19.8685 7.72542 19.7663 7.71686 19.6615L6.85971 9.26132C6.84222 9.04978 6.91547 8.84042 7.06335 8.67929C7.21124 8.51815 7.42164 8.41845 7.64829 8.4021C7.76076 8.39282 7.87409 8.4046 7.98162 8.43675C8.08915 8.46891 8.18872 8.52079 8.27447 8.58935C8.36023 8.65791 8.43044 8.74177 8.48099 8.83601C8.53155 8.93024 8.56142 9.03296 8.56886 9.13812L9.426 19.5383C9.4435 19.7499 9.37025 19.9592 9.22236 20.1204C9.07448 20.2815 8.86407 20.3812 8.63743 20.3975ZM12.8571 19.5999C12.8571 19.8121 12.7668 20.0156 12.6061 20.1656C12.4453 20.3157 12.2273 20.3999 12 20.3999C11.7727 20.3999 11.5547 20.3157 11.3939 20.1656C11.2332 20.0156 11.1429 19.8121 11.1429 19.5999V9.19972C11.1429 8.98754 11.2332 8.78406 11.3939 8.63402C11.5547 8.48399 11.7727 8.3997 12 8.3997C12.2273 8.3997 12.4453 8.48399 12.6061 8.63402C12.7668 8.78406 12.8571 8.98754 12.8571 9.19972V19.5999ZM16.2831 19.6615C16.2745 19.7663 16.2438 19.8684 16.1928 19.9621C16.1419 20.0557 16.0717 20.1391 15.9862 20.2075C15.9007 20.2758 15.8016 20.3278 15.6946 20.3604C15.5876 20.393 15.4748 20.4056 15.3626 20.3975C15.2503 20.3895 15.1409 20.3608 15.0406 20.3133C14.9402 20.2657 14.8509 20.2002 14.7776 20.1204C14.7044 20.0406 14.6487 19.9481 14.6138 19.8482C14.5789 19.7484 14.5653 19.6431 14.574 19.5383L15.4311 9.13812C15.4381 9.03281 15.4677 8.92987 15.5181 8.83542C15.5685 8.74098 15.6387 8.65694 15.7246 8.58829C15.8104 8.51965 15.9102 8.4678 16.0179 8.43582C16.1256 8.40383 16.2391 8.39237 16.3517 8.4021C16.5784 8.41845 16.7888 8.51815 16.9366 8.67929C17.0845 8.84042 17.1578 9.04978 17.1403 9.26132L16.2831 19.6615Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('streamInfo') }}" class="cardButton _stats" data-stream="1" title="Подробная информация">Подобная информация</a>
                                    </div>
                                </div>
                            </div>
                            <div class="cardItem">
                                <div class="videoWrapper">
                                    <video controls class="videoGuid" data-index="0" preload="metadata">
                                    <source src="/files/test/streams/321.mp4" type="video/mp4"/>
                                    </video>
                                    <div class="videoContoller">
                                    <p class="togglePlay">
                                        <svg class="play" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.64612 11.8332C2.33007 12.0429 2.00991 12.0549 1.68565 11.869C1.36201 11.6838 1.2002 11.3975 1.2002 11.0103V0.99001C1.2002 0.602754 1.36201 0.316185 1.68565 0.130302C2.00991 -0.0549357 2.33007 -0.0426725 2.64612 0.167091L10.3735 5.17722C10.658 5.37084 10.8002 5.64515 10.8002 6.00014C10.8002 6.35512 10.658 6.62943 10.3735 6.82306L2.64612 11.8332Z" fill="white"/>
                                        </svg>
                                        <svg class="stop" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 12C1.45 12 0.979333 11.8043 0.588 11.413C0.196 11.021 0 10.55 0 10V2C0 1.45 0.196 0.979333 0.588 0.588C0.979333 0.196 1.45 0 2 0H10C10.55 0 11.021 0.196 11.413 0.588C11.8043 0.979333 12 1.45 12 2V10C12 10.55 11.8043 11.021 11.413 11.413C11.021 11.8043 10.55 12 10 12H2Z" fill="white"/>
                                        </svg>
                                    </p>
                                    <div class="videoTime"><span class="elapsed">00:00</span>&nbsp;|&nbsp;<span class="duration">00:00</span></div>
                                    <div class="videoProgress">
                                        <input class="seek" value="0" min="0" max="0" type="range" step="0.1">
                                    </div>
                                    <div class="volumeContol">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.2279 3.58652C11.1224 3.44871 10.9693 3.36076 10.8023 3.34203C10.6353 3.32329 10.4681 3.37529 10.3375 3.48659C10.2069 3.5979 10.1235 3.75939 10.1057 3.93553C10.088 4.11168 10.1373 4.28805 10.2428 4.42586C10.5618 4.88344 10.7339 5.4366 10.7339 6.00463C10.7339 6.57265 10.5618 7.12581 10.2428 7.58339C10.1675 7.68129 10.1201 7.79971 10.1061 7.9249C10.092 8.05009 10.112 8.1769 10.1636 8.29061C10.2151 8.40432 10.2963 8.50026 10.3975 8.56727C10.4987 8.63429 10.6158 8.66964 10.7353 8.66921C10.8297 8.66955 10.9229 8.64758 11.0081 8.60493C11.0934 8.56228 11.1685 8.50002 11.2279 8.42274C11.7288 7.72733 12 6.878 12 6.00463C12 5.13125 11.7288 4.28192 11.2279 3.58652ZM8.50618 0.0892485C8.41018 0.030782 8.30129 1.90735e-06 8.19044 1.90735e-06C8.07959 1.90735e-06 7.97069 0.030782 7.87469 0.0892485L3.78895 3.0536H0.631491C0.464009 3.0536 0.303387 3.12378 0.184959 3.24871C0.0665319 3.37363 0 3.54307 0 3.71974V8.28951C0 8.46618 0.0665319 8.63562 0.184959 8.76054C0.303387 8.88547 0.464009 8.95565 0.631491 8.95565H3.78895L7.8368 11.8867C7.94769 11.9624 8.07732 12.0018 8.20938 11.9999C8.37686 11.9999 8.53749 11.9298 8.65591 11.8048C8.77434 11.6799 8.84087 11.5105 8.84087 11.3338V0.675457C8.84057 0.554949 8.80928 0.436784 8.75034 0.333552C8.6914 0.23032 8.60702 0.145887 8.50618 0.0892485Z" fill="#202020"/>
                                            </svg>
                                        <input class="volume" value="1" min="0" max="1" type="range" step="0.01">
                                    </div>
                                    <p class="toggleScreen">
                                        <svg class="open" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.3708 7.629C4.30048 7.5587 4.20512 7.51921 4.10568 7.51921C4.00624 7.51921 3.91088 7.5587 3.84055 7.629L0.768555 10.701V8.625C0.768555 8.52554 0.729046 8.43016 0.65872 8.35983C0.588394 8.28951 0.493011 8.25 0.393555 8.25C0.294098 8.25 0.198716 8.28951 0.12839 8.35983C0.0580635 8.43016 0.0185547 8.52554 0.0185547 8.625V11.6062C0.0185547 11.7057 0.0580635 11.8011 0.12839 11.8714C0.198716 11.9417 0.294098 11.9812 0.393555 11.9812H3.3748C3.47426 11.9812 3.56964 11.9417 3.63997 11.8714C3.7103 11.8011 3.7498 11.7057 3.7498 11.6062C3.7498 11.5068 3.7103 11.4114 3.63997 11.3411C3.56964 11.2708 3.47426 11.2312 3.3748 11.2312H1.2988L4.3708 8.15925C4.44111 8.08893 4.4806 7.99356 4.4806 7.89412C4.4806 7.79469 4.44111 7.69932 4.3708 7.629ZM7.62881 7.629C7.69913 7.5587 7.79449 7.51921 7.89393 7.51921C7.99337 7.51921 8.08873 7.5587 8.15905 7.629L11.2311 10.701V8.625C11.2311 8.52554 11.2706 8.43016 11.3409 8.35983C11.4112 8.28951 11.5066 8.25 11.6061 8.25C11.7055 8.25 11.8009 8.28951 11.8712 8.35983C11.9415 8.43016 11.9811 8.52554 11.9811 8.625V11.6062C11.9811 11.7057 11.9415 11.8011 11.8712 11.8714C11.8009 11.9417 11.7055 11.9812 11.6061 11.9812H8.62481C8.52535 11.9812 8.42997 11.9417 8.35964 11.8714C8.28931 11.8011 8.24981 11.7057 8.24981 11.6062C8.24981 11.5068 8.28931 11.4114 8.35964 11.3411C8.42997 11.2708 8.52535 11.2312 8.62481 11.2312H10.7008L7.62881 8.15925C7.5585 8.08893 7.51901 7.99356 7.51901 7.89412C7.51901 7.79469 7.5585 7.69932 7.62881 7.629ZM7.62881 4.371C7.69913 4.4413 7.79449 4.48079 7.89393 4.48079C7.99337 4.48079 8.08873 4.4413 8.15905 4.371L11.2311 1.299V3.375C11.2311 3.47446 11.2706 3.56984 11.3409 3.64016C11.4112 3.71049 11.5066 3.75 11.6061 3.75C11.7055 3.75 11.8009 3.71049 11.8712 3.64016C11.9415 3.56984 11.9811 3.47446 11.9811 3.375V0.393749C11.9811 0.294293 11.9415 0.19891 11.8712 0.128584C11.8009 0.058258 11.7055 0.0187492 11.6061 0.0187492H8.62481C8.52535 0.0187492 8.42997 0.058258 8.35964 0.128584C8.28931 0.19891 8.24981 0.294293 8.24981 0.393749C8.24981 0.493205 8.28931 0.588588 8.35964 0.658914C8.42997 0.72924 8.52535 0.768749 8.62481 0.768749H10.7008L7.62881 3.84075C7.5585 3.91107 7.51901 4.00644 7.51901 4.10587C7.51901 4.20531 7.5585 4.30068 7.62881 4.371ZM4.3708 4.371C4.30048 4.4413 4.20512 4.48079 4.10568 4.48079C4.00624 4.48079 3.91088 4.4413 3.84055 4.371L0.768555 1.299V3.375C0.768555 3.47446 0.729046 3.56984 0.65872 3.64016C0.588394 3.71049 0.493011 3.75 0.393555 3.75C0.294098 3.75 0.198716 3.71049 0.12839 3.64016C0.0580635 3.56984 0.0185547 3.47446 0.0185547 3.375V0.393749C0.0185547 0.294293 0.0580635 0.19891 0.12839 0.128584C0.198716 0.058258 0.294098 0.0187492 0.393555 0.0187492H3.3748C3.47426 0.0187492 3.56964 0.058258 3.63997 0.128584C3.7103 0.19891 3.7498 0.294293 3.7498 0.393749C3.7498 0.493205 3.7103 0.588588 3.63997 0.658914C3.56964 0.72924 3.47426 0.768749 3.3748 0.768749H1.2988L4.3708 3.84075C4.44111 3.91107 4.4806 4.00644 4.4806 4.10587C4.4806 4.20531 4.44111 4.30068 4.3708 4.371Z" fill="#202020"/>
                                        </svg>
                                        <svg class="close" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.857143 9.42857H2.57143V11.1429C2.57143 11.6143 2.95714 12 3.42857 12C3.9 12 4.28571 11.6143 4.28571 11.1429V8.57143C4.28571 8.1 3.9 7.71429 3.42857 7.71429H0.857143C0.385714 7.71429 0 8.1 0 8.57143C0 9.04286 0.385714 9.42857 0.857143 9.42857ZM2.57143 2.57143H0.857143C0.385714 2.57143 0 2.95714 0 3.42857C0 3.9 0.385714 4.28571 0.857143 4.28571H3.42857C3.9 4.28571 4.28571 3.9 4.28571 3.42857V0.857143C4.28571 0.385714 3.9 0 3.42857 0C2.95714 0 2.57143 0.385714 2.57143 0.857143V2.57143ZM8.57143 12C9.04286 12 9.42857 11.6143 9.42857 11.1429V9.42857H11.1429C11.6143 9.42857 12 9.04286 12 8.57143C12 8.1 11.6143 7.71429 11.1429 7.71429H8.57143C8.1 7.71429 7.71429 8.1 7.71429 8.57143V11.1429C7.71429 11.6143 8.1 12 8.57143 12ZM9.42857 2.57143V0.857143C9.42857 0.385714 9.04286 0 8.57143 0C8.1 0 7.71429 0.385714 7.71429 0.857143V3.42857C7.71429 3.9 8.1 4.28571 8.57143 4.28571H11.1429C11.6143 4.28571 12 3.9 12 3.42857C12 2.95714 11.6143 2.57143 11.1429 2.57143H9.42857Z" fill="black"/>
                                            </svg>
                                    </p>
                                    </div>
                                    <div class="videoStarter">
                                        <svg class="starterPlay" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="64" rx="12" fill="#202020"/>
                                            <path d="M23.056 47.5551C22.2132 48.1145 21.3594 48.1464 20.4947 47.6507C19.6317 47.1567 19.2002 46.3934 19.2002 45.3607V18.64C19.2002 17.6073 19.6317 16.8432 20.4947 16.3475C21.3594 15.8535 22.2132 15.8862 23.056 16.4456L43.6624 29.8059C44.4209 30.3223 44.8002 31.0537 44.8002 32.0004C44.8002 32.947 44.4209 33.6785 43.6624 34.1948L23.056 47.5551Z" fill="white"/>
                                        </svg>
                                        <svg class="starterStop" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="64" rx="12" fill="#202020"/>
                                            <path d="M21.3333 48C19.8667 48 18.6116 47.4781 17.568 46.4347C16.5227 45.3893 16 44.1333 16 42.6667V21.3333C16 19.8667 16.5227 18.6116 17.568 17.568C18.6116 16.5227 19.8667 16 21.3333 16H42.6667C44.1333 16 45.3893 16.5227 46.4347 17.568C47.4781 18.6116 48 19.8667 48 21.3333V42.6667C48 44.1333 47.4781 45.3893 46.4347 46.4347C45.3893 47.4781 44.1333 48 42.6667 48H21.3333Z" fill="white"/>
                                            <rect width="32" height="32" fill="white" transform="translate(16 16)"/>
                                        </svg>
                                    </div>
                                    <div class="videoShadow"></div>
                                </div>
                                <div class="cardInformation">
                                    <div class="cardInformationTop">
                                        <h3 class="cardName">Название трансляции</h3>
                                        <p class="streamDesc">Описание трансляции, типо самый крутой и так далее, а по факту хуйня</p>
                                        <p class="cardLine">Владелец: <span>Фамилия Имя</span></p>
                                        <p class="cardLine">Автозапуск: <span>Активен</span></p>
                                        <p class="cardLine _nextStart">Следующий стрим: <span>20.11.2023 в 21:49 (МСК)</span></p>
                                    </div>
                                    <div class="cardControls">
                                        <a href="#" class="cardButton _autostart" data-stream="1" title="Выключить автозапуск">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.33333 22C4.41667 22 3.63222 21.6738 2.98 21.0217C2.32667 20.3683 2 19.5833 2 18.6667V5.33333C2 4.41667 2.32667 3.63222 2.98 2.98C3.63222 2.32667 4.41667 2 5.33333 2H18.6667C19.5833 2 20.3683 2.32667 21.0217 2.98C21.6738 3.63222 22 4.41667 22 5.33333V18.6667C22 19.5833 21.6738 20.3683 21.0217 21.0217C20.3683 21.6738 19.5833 22 18.6667 22H5.33333Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="cardButton _edit" data-stream="1" title="Изменить">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 22H3C2.73478 22 2.48043 21.8946 2.29289 21.7071C2.10536 21.5195 2 21.2652 2 21V3C2 2.73478 2.10536 2.48043 2.29289 2.29289C2.48043 2.10536 2.73478 2 3 2H15C15.2652 2 15.5195 2.10536 15.7071 2.29289C15.8946 2.48043 16 2.73478 16 3C16 3.26521 15.8946 3.51957 15.7071 3.7071C15.5195 3.89464 15.2652 4 15 4H4V20H20V8.99999C20 8.73477 20.1053 8.48042 20.2929 8.29288C20.4804 8.10535 20.7348 7.99999 21 7.99999C21.2652 7.99999 21.5195 8.10535 21.7071 8.29288C21.8946 8.48042 22 8.73477 22 8.99999V21C22 21.2652 21.8946 21.5195 21.7071 21.7071C21.5195 21.8946 21.2652 22 21 22Z" fill="white"/>
                                                <path d="M11.9851 14.0109C11.7379 14.0103 11.4997 13.9179 11.3168 13.7516C11.1214 13.5742 11.0044 13.3265 10.9913 13.063C10.9782 12.7994 11.0702 12.5414 11.247 12.3455L20.2247 2.37283C20.3098 2.26685 20.4157 2.17938 20.5359 2.1158C20.6561 2.05222 20.788 2.01388 20.9235 2.00313C21.0591 1.99237 21.1954 2.00943 21.3241 2.05326C21.4528 2.09709 21.5712 2.16676 21.672 2.25799C21.7728 2.34923 21.8538 2.4601 21.9102 2.5838C21.9666 2.7075 21.9971 2.84141 21.9998 2.97731C22.0025 3.11322 21.9774 3.24824 21.926 3.3741C21.8746 3.49995 21.7981 3.61398 21.701 3.70917L12.7233 13.6818C12.63 13.7851 12.5162 13.8678 12.3891 13.9245C12.2619 13.9811 12.1243 14.0106 11.9851 14.0109ZM16.9728 18H6.99752C6.73296 18 6.47924 17.8949 6.29217 17.7079C6.1051 17.5209 6 17.2672 6 17.0027C6 16.7382 6.1051 16.4846 6.29217 16.2975C6.47924 16.1105 6.73296 16.0054 6.99752 16.0054H16.9728C17.2373 16.0054 17.491 16.1105 17.6781 16.2975C17.8652 16.4846 17.9703 16.7382 17.9703 17.0027C17.9703 17.2672 17.8652 17.5209 17.6781 17.7079C17.491 17.8949 17.2373 18 16.9728 18Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="cardButton _remove" data-stream="1" title="Удалить">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.1416 2.80002V5.20006H15.8559V2.80002C15.8559 2.58784 15.7656 2.38435 15.6048 2.23432C15.4441 2.08429 15.2261 2 14.9987 2H8.99874C8.77142 2 8.5534 2.08429 8.39265 2.23432C8.23191 2.38435 8.1416 2.58784 8.1416 2.80002Z" fill="white"/>
                                                <path d="M3 4.39963V5.19964H21V4.39963C21 4.18745 20.9097 3.98396 20.7489 3.83393C20.5882 3.6839 20.3702 3.59961 20.1429 3.59961H3.85714C3.62981 3.59961 3.4118 3.6839 3.25105 3.83393C3.09031 3.98396 3 4.18745 3 4.39963ZM4.71429 6.79967L6.26743 20.5671C6.3117 20.9604 6.5104 21.3244 6.82521 21.589C7.14003 21.8535 7.54865 22 7.97229 22H16.0286C16.4522 22 16.8608 21.8535 17.1756 21.589C17.4905 21.3244 17.6892 20.9604 17.7334 20.5671L19.2857 6.79967H4.71429ZM8.63743 20.3975C8.52518 20.4057 8.41231 20.3932 8.30526 20.3606C8.19822 20.328 8.09911 20.2761 8.0136 20.2077C7.92809 20.1394 7.85787 20.0559 7.80695 19.9622C7.75603 19.8685 7.72542 19.7663 7.71686 19.6615L6.85971 9.26132C6.84222 9.04978 6.91547 8.84042 7.06335 8.67929C7.21124 8.51815 7.42164 8.41845 7.64829 8.4021C7.76076 8.39282 7.87409 8.4046 7.98162 8.43675C8.08915 8.46891 8.18872 8.52079 8.27447 8.58935C8.36023 8.65791 8.43044 8.74177 8.48099 8.83601C8.53155 8.93024 8.56142 9.03296 8.56886 9.13812L9.426 19.5383C9.4435 19.7499 9.37025 19.9592 9.22236 20.1204C9.07448 20.2815 8.86407 20.3812 8.63743 20.3975ZM12.8571 19.5999C12.8571 19.8121 12.7668 20.0156 12.6061 20.1656C12.4453 20.3157 12.2273 20.3999 12 20.3999C11.7727 20.3999 11.5547 20.3157 11.3939 20.1656C11.2332 20.0156 11.1429 19.8121 11.1429 19.5999V9.19972C11.1429 8.98754 11.2332 8.78406 11.3939 8.63402C11.5547 8.48399 11.7727 8.3997 12 8.3997C12.2273 8.3997 12.4453 8.48399 12.6061 8.63402C12.7668 8.78406 12.8571 8.98754 12.8571 9.19972V19.5999ZM16.2831 19.6615C16.2745 19.7663 16.2438 19.8684 16.1928 19.9621C16.1419 20.0557 16.0717 20.1391 15.9862 20.2075C15.9007 20.2758 15.8016 20.3278 15.6946 20.3604C15.5876 20.393 15.4748 20.4056 15.3626 20.3975C15.2503 20.3895 15.1409 20.3608 15.0406 20.3133C14.9402 20.2657 14.8509 20.2002 14.7776 20.1204C14.7044 20.0406 14.6487 19.9481 14.6138 19.8482C14.5789 19.7484 14.5653 19.6431 14.574 19.5383L15.4311 9.13812C15.4381 9.03281 15.4677 8.92987 15.5181 8.83542C15.5685 8.74098 15.6387 8.65694 15.7246 8.58829C15.8104 8.51965 15.9102 8.4678 16.0179 8.43582C16.1256 8.40383 16.2391 8.39237 16.3517 8.4021C16.5784 8.41845 16.7888 8.51815 16.9366 8.67929C17.0845 8.84042 17.1578 9.04978 17.1403 9.26132L16.2831 19.6615Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="/cabinet/stream.html" class="cardButton _stats" data-stream="1" title="Подробная информация">Подобная информация</a>
                                    </div>
                                </div>
                            </div>
                            <div class="cardItem">
                                <div class="videoWrapper">
                                    <video controls class="videoGuid" data-index="0" preload="metadata">
                                    <source src="/files/test/streams/321.mp4" type="video/mp4"/>
                                    </video>
                                    <div class="videoContoller">
                                    <p class="togglePlay">
                                        <svg class="play" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.64612 11.8332C2.33007 12.0429 2.00991 12.0549 1.68565 11.869C1.36201 11.6838 1.2002 11.3975 1.2002 11.0103V0.99001C1.2002 0.602754 1.36201 0.316185 1.68565 0.130302C2.00991 -0.0549357 2.33007 -0.0426725 2.64612 0.167091L10.3735 5.17722C10.658 5.37084 10.8002 5.64515 10.8002 6.00014C10.8002 6.35512 10.658 6.62943 10.3735 6.82306L2.64612 11.8332Z" fill="white"/>
                                        </svg>
                                        <svg class="stop" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 12C1.45 12 0.979333 11.8043 0.588 11.413C0.196 11.021 0 10.55 0 10V2C0 1.45 0.196 0.979333 0.588 0.588C0.979333 0.196 1.45 0 2 0H10C10.55 0 11.021 0.196 11.413 0.588C11.8043 0.979333 12 1.45 12 2V10C12 10.55 11.8043 11.021 11.413 11.413C11.021 11.8043 10.55 12 10 12H2Z" fill="white"/>
                                        </svg>
                                    </p>
                                    <div class="videoTime"><span class="elapsed">00:00</span>&nbsp;|&nbsp;<span class="duration">00:00</span></div>
                                    <div class="videoProgress">
                                        <input class="seek" value="0" min="0" max="0" type="range" step="0.1">
                                    </div>
                                    <div class="volumeContol">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.2279 3.58652C11.1224 3.44871 10.9693 3.36076 10.8023 3.34203C10.6353 3.32329 10.4681 3.37529 10.3375 3.48659C10.2069 3.5979 10.1235 3.75939 10.1057 3.93553C10.088 4.11168 10.1373 4.28805 10.2428 4.42586C10.5618 4.88344 10.7339 5.4366 10.7339 6.00463C10.7339 6.57265 10.5618 7.12581 10.2428 7.58339C10.1675 7.68129 10.1201 7.79971 10.1061 7.9249C10.092 8.05009 10.112 8.1769 10.1636 8.29061C10.2151 8.40432 10.2963 8.50026 10.3975 8.56727C10.4987 8.63429 10.6158 8.66964 10.7353 8.66921C10.8297 8.66955 10.9229 8.64758 11.0081 8.60493C11.0934 8.56228 11.1685 8.50002 11.2279 8.42274C11.7288 7.72733 12 6.878 12 6.00463C12 5.13125 11.7288 4.28192 11.2279 3.58652ZM8.50618 0.0892485C8.41018 0.030782 8.30129 1.90735e-06 8.19044 1.90735e-06C8.07959 1.90735e-06 7.97069 0.030782 7.87469 0.0892485L3.78895 3.0536H0.631491C0.464009 3.0536 0.303387 3.12378 0.184959 3.24871C0.0665319 3.37363 0 3.54307 0 3.71974V8.28951C0 8.46618 0.0665319 8.63562 0.184959 8.76054C0.303387 8.88547 0.464009 8.95565 0.631491 8.95565H3.78895L7.8368 11.8867C7.94769 11.9624 8.07732 12.0018 8.20938 11.9999C8.37686 11.9999 8.53749 11.9298 8.65591 11.8048C8.77434 11.6799 8.84087 11.5105 8.84087 11.3338V0.675457C8.84057 0.554949 8.80928 0.436784 8.75034 0.333552C8.6914 0.23032 8.60702 0.145887 8.50618 0.0892485Z" fill="#202020"/>
                                            </svg>
                                        <input class="volume" value="1" min="0" max="1" type="range" step="0.01">
                                    </div>
                                    <p class="toggleScreen">
                                        <svg class="open" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.3708 7.629C4.30048 7.5587 4.20512 7.51921 4.10568 7.51921C4.00624 7.51921 3.91088 7.5587 3.84055 7.629L0.768555 10.701V8.625C0.768555 8.52554 0.729046 8.43016 0.65872 8.35983C0.588394 8.28951 0.493011 8.25 0.393555 8.25C0.294098 8.25 0.198716 8.28951 0.12839 8.35983C0.0580635 8.43016 0.0185547 8.52554 0.0185547 8.625V11.6062C0.0185547 11.7057 0.0580635 11.8011 0.12839 11.8714C0.198716 11.9417 0.294098 11.9812 0.393555 11.9812H3.3748C3.47426 11.9812 3.56964 11.9417 3.63997 11.8714C3.7103 11.8011 3.7498 11.7057 3.7498 11.6062C3.7498 11.5068 3.7103 11.4114 3.63997 11.3411C3.56964 11.2708 3.47426 11.2312 3.3748 11.2312H1.2988L4.3708 8.15925C4.44111 8.08893 4.4806 7.99356 4.4806 7.89412C4.4806 7.79469 4.44111 7.69932 4.3708 7.629ZM7.62881 7.629C7.69913 7.5587 7.79449 7.51921 7.89393 7.51921C7.99337 7.51921 8.08873 7.5587 8.15905 7.629L11.2311 10.701V8.625C11.2311 8.52554 11.2706 8.43016 11.3409 8.35983C11.4112 8.28951 11.5066 8.25 11.6061 8.25C11.7055 8.25 11.8009 8.28951 11.8712 8.35983C11.9415 8.43016 11.9811 8.52554 11.9811 8.625V11.6062C11.9811 11.7057 11.9415 11.8011 11.8712 11.8714C11.8009 11.9417 11.7055 11.9812 11.6061 11.9812H8.62481C8.52535 11.9812 8.42997 11.9417 8.35964 11.8714C8.28931 11.8011 8.24981 11.7057 8.24981 11.6062C8.24981 11.5068 8.28931 11.4114 8.35964 11.3411C8.42997 11.2708 8.52535 11.2312 8.62481 11.2312H10.7008L7.62881 8.15925C7.5585 8.08893 7.51901 7.99356 7.51901 7.89412C7.51901 7.79469 7.5585 7.69932 7.62881 7.629ZM7.62881 4.371C7.69913 4.4413 7.79449 4.48079 7.89393 4.48079C7.99337 4.48079 8.08873 4.4413 8.15905 4.371L11.2311 1.299V3.375C11.2311 3.47446 11.2706 3.56984 11.3409 3.64016C11.4112 3.71049 11.5066 3.75 11.6061 3.75C11.7055 3.75 11.8009 3.71049 11.8712 3.64016C11.9415 3.56984 11.9811 3.47446 11.9811 3.375V0.393749C11.9811 0.294293 11.9415 0.19891 11.8712 0.128584C11.8009 0.058258 11.7055 0.0187492 11.6061 0.0187492H8.62481C8.52535 0.0187492 8.42997 0.058258 8.35964 0.128584C8.28931 0.19891 8.24981 0.294293 8.24981 0.393749C8.24981 0.493205 8.28931 0.588588 8.35964 0.658914C8.42997 0.72924 8.52535 0.768749 8.62481 0.768749H10.7008L7.62881 3.84075C7.5585 3.91107 7.51901 4.00644 7.51901 4.10587C7.51901 4.20531 7.5585 4.30068 7.62881 4.371ZM4.3708 4.371C4.30048 4.4413 4.20512 4.48079 4.10568 4.48079C4.00624 4.48079 3.91088 4.4413 3.84055 4.371L0.768555 1.299V3.375C0.768555 3.47446 0.729046 3.56984 0.65872 3.64016C0.588394 3.71049 0.493011 3.75 0.393555 3.75C0.294098 3.75 0.198716 3.71049 0.12839 3.64016C0.0580635 3.56984 0.0185547 3.47446 0.0185547 3.375V0.393749C0.0185547 0.294293 0.0580635 0.19891 0.12839 0.128584C0.198716 0.058258 0.294098 0.0187492 0.393555 0.0187492H3.3748C3.47426 0.0187492 3.56964 0.058258 3.63997 0.128584C3.7103 0.19891 3.7498 0.294293 3.7498 0.393749C3.7498 0.493205 3.7103 0.588588 3.63997 0.658914C3.56964 0.72924 3.47426 0.768749 3.3748 0.768749H1.2988L4.3708 3.84075C4.44111 3.91107 4.4806 4.00644 4.4806 4.10587C4.4806 4.20531 4.44111 4.30068 4.3708 4.371Z" fill="#202020"/>
                                        </svg>
                                        <svg class="close" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.857143 9.42857H2.57143V11.1429C2.57143 11.6143 2.95714 12 3.42857 12C3.9 12 4.28571 11.6143 4.28571 11.1429V8.57143C4.28571 8.1 3.9 7.71429 3.42857 7.71429H0.857143C0.385714 7.71429 0 8.1 0 8.57143C0 9.04286 0.385714 9.42857 0.857143 9.42857ZM2.57143 2.57143H0.857143C0.385714 2.57143 0 2.95714 0 3.42857C0 3.9 0.385714 4.28571 0.857143 4.28571H3.42857C3.9 4.28571 4.28571 3.9 4.28571 3.42857V0.857143C4.28571 0.385714 3.9 0 3.42857 0C2.95714 0 2.57143 0.385714 2.57143 0.857143V2.57143ZM8.57143 12C9.04286 12 9.42857 11.6143 9.42857 11.1429V9.42857H11.1429C11.6143 9.42857 12 9.04286 12 8.57143C12 8.1 11.6143 7.71429 11.1429 7.71429H8.57143C8.1 7.71429 7.71429 8.1 7.71429 8.57143V11.1429C7.71429 11.6143 8.1 12 8.57143 12ZM9.42857 2.57143V0.857143C9.42857 0.385714 9.04286 0 8.57143 0C8.1 0 7.71429 0.385714 7.71429 0.857143V3.42857C7.71429 3.9 8.1 4.28571 8.57143 4.28571H11.1429C11.6143 4.28571 12 3.9 12 3.42857C12 2.95714 11.6143 2.57143 11.1429 2.57143H9.42857Z" fill="black"/>
                                            </svg>
                                    </p>
                                    </div>
                                    <div class="videoStarter">
                                        <svg class="starterPlay" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="64" rx="12" fill="#202020"/>
                                            <path d="M23.056 47.5551C22.2132 48.1145 21.3594 48.1464 20.4947 47.6507C19.6317 47.1567 19.2002 46.3934 19.2002 45.3607V18.64C19.2002 17.6073 19.6317 16.8432 20.4947 16.3475C21.3594 15.8535 22.2132 15.8862 23.056 16.4456L43.6624 29.8059C44.4209 30.3223 44.8002 31.0537 44.8002 32.0004C44.8002 32.947 44.4209 33.6785 43.6624 34.1948L23.056 47.5551Z" fill="white"/>
                                        </svg>
                                        <svg class="starterStop" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="64" rx="12" fill="#202020"/>
                                            <path d="M21.3333 48C19.8667 48 18.6116 47.4781 17.568 46.4347C16.5227 45.3893 16 44.1333 16 42.6667V21.3333C16 19.8667 16.5227 18.6116 17.568 17.568C18.6116 16.5227 19.8667 16 21.3333 16H42.6667C44.1333 16 45.3893 16.5227 46.4347 17.568C47.4781 18.6116 48 19.8667 48 21.3333V42.6667C48 44.1333 47.4781 45.3893 46.4347 46.4347C45.3893 47.4781 44.1333 48 42.6667 48H21.3333Z" fill="white"/>
                                            <rect width="32" height="32" fill="white" transform="translate(16 16)"/>
                                        </svg>
                                    </div>
                                    <div class="videoShadow"></div>
                                </div>
                                <div class="cardInformation">
                                    <div class="cardInformationTop">
                                        <h3 class="cardName">Название трансляции</h3>
                                        <p class="streamDesc">Описание трансляции, типо самый крутой и так далее, а по факту хуйня</p>
                                        <p class="cardLine">Владелец: <span>Фамилия Имя</span></p>
                                        <p class="cardLine">Автозапуск: <span>Активен</span></p>
                                        <p class="cardLine _nextStart">Следующий стрим: <span>20.11.2023 в 21:49 (МСК)</span></p>
                                    </div>
                                    <div class="cardControls">
                                        <a href="#" class="cardButton _autostart" data-stream="1" title="Выключить автозапуск">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.33333 22C4.41667 22 3.63222 21.6738 2.98 21.0217C2.32667 20.3683 2 19.5833 2 18.6667V5.33333C2 4.41667 2.32667 3.63222 2.98 2.98C3.63222 2.32667 4.41667 2 5.33333 2H18.6667C19.5833 2 20.3683 2.32667 21.0217 2.98C21.6738 3.63222 22 4.41667 22 5.33333V18.6667C22 19.5833 21.6738 20.3683 21.0217 21.0217C20.3683 21.6738 19.5833 22 18.6667 22H5.33333Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="cardButton _edit" data-stream="1" title="Изменить">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 22H3C2.73478 22 2.48043 21.8946 2.29289 21.7071C2.10536 21.5195 2 21.2652 2 21V3C2 2.73478 2.10536 2.48043 2.29289 2.29289C2.48043 2.10536 2.73478 2 3 2H15C15.2652 2 15.5195 2.10536 15.7071 2.29289C15.8946 2.48043 16 2.73478 16 3C16 3.26521 15.8946 3.51957 15.7071 3.7071C15.5195 3.89464 15.2652 4 15 4H4V20H20V8.99999C20 8.73477 20.1053 8.48042 20.2929 8.29288C20.4804 8.10535 20.7348 7.99999 21 7.99999C21.2652 7.99999 21.5195 8.10535 21.7071 8.29288C21.8946 8.48042 22 8.73477 22 8.99999V21C22 21.2652 21.8946 21.5195 21.7071 21.7071C21.5195 21.8946 21.2652 22 21 22Z" fill="white"/>
                                                <path d="M11.9851 14.0109C11.7379 14.0103 11.4997 13.9179 11.3168 13.7516C11.1214 13.5742 11.0044 13.3265 10.9913 13.063C10.9782 12.7994 11.0702 12.5414 11.247 12.3455L20.2247 2.37283C20.3098 2.26685 20.4157 2.17938 20.5359 2.1158C20.6561 2.05222 20.788 2.01388 20.9235 2.00313C21.0591 1.99237 21.1954 2.00943 21.3241 2.05326C21.4528 2.09709 21.5712 2.16676 21.672 2.25799C21.7728 2.34923 21.8538 2.4601 21.9102 2.5838C21.9666 2.7075 21.9971 2.84141 21.9998 2.97731C22.0025 3.11322 21.9774 3.24824 21.926 3.3741C21.8746 3.49995 21.7981 3.61398 21.701 3.70917L12.7233 13.6818C12.63 13.7851 12.5162 13.8678 12.3891 13.9245C12.2619 13.9811 12.1243 14.0106 11.9851 14.0109ZM16.9728 18H6.99752C6.73296 18 6.47924 17.8949 6.29217 17.7079C6.1051 17.5209 6 17.2672 6 17.0027C6 16.7382 6.1051 16.4846 6.29217 16.2975C6.47924 16.1105 6.73296 16.0054 6.99752 16.0054H16.9728C17.2373 16.0054 17.491 16.1105 17.6781 16.2975C17.8652 16.4846 17.9703 16.7382 17.9703 17.0027C17.9703 17.2672 17.8652 17.5209 17.6781 17.7079C17.491 17.8949 17.2373 18 16.9728 18Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="cardButton _remove" data-stream="1" title="Удалить">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.1416 2.80002V5.20006H15.8559V2.80002C15.8559 2.58784 15.7656 2.38435 15.6048 2.23432C15.4441 2.08429 15.2261 2 14.9987 2H8.99874C8.77142 2 8.5534 2.08429 8.39265 2.23432C8.23191 2.38435 8.1416 2.58784 8.1416 2.80002Z" fill="white"/>
                                                <path d="M3 4.39963V5.19964H21V4.39963C21 4.18745 20.9097 3.98396 20.7489 3.83393C20.5882 3.6839 20.3702 3.59961 20.1429 3.59961H3.85714C3.62981 3.59961 3.4118 3.6839 3.25105 3.83393C3.09031 3.98396 3 4.18745 3 4.39963ZM4.71429 6.79967L6.26743 20.5671C6.3117 20.9604 6.5104 21.3244 6.82521 21.589C7.14003 21.8535 7.54865 22 7.97229 22H16.0286C16.4522 22 16.8608 21.8535 17.1756 21.589C17.4905 21.3244 17.6892 20.9604 17.7334 20.5671L19.2857 6.79967H4.71429ZM8.63743 20.3975C8.52518 20.4057 8.41231 20.3932 8.30526 20.3606C8.19822 20.328 8.09911 20.2761 8.0136 20.2077C7.92809 20.1394 7.85787 20.0559 7.80695 19.9622C7.75603 19.8685 7.72542 19.7663 7.71686 19.6615L6.85971 9.26132C6.84222 9.04978 6.91547 8.84042 7.06335 8.67929C7.21124 8.51815 7.42164 8.41845 7.64829 8.4021C7.76076 8.39282 7.87409 8.4046 7.98162 8.43675C8.08915 8.46891 8.18872 8.52079 8.27447 8.58935C8.36023 8.65791 8.43044 8.74177 8.48099 8.83601C8.53155 8.93024 8.56142 9.03296 8.56886 9.13812L9.426 19.5383C9.4435 19.7499 9.37025 19.9592 9.22236 20.1204C9.07448 20.2815 8.86407 20.3812 8.63743 20.3975ZM12.8571 19.5999C12.8571 19.8121 12.7668 20.0156 12.6061 20.1656C12.4453 20.3157 12.2273 20.3999 12 20.3999C11.7727 20.3999 11.5547 20.3157 11.3939 20.1656C11.2332 20.0156 11.1429 19.8121 11.1429 19.5999V9.19972C11.1429 8.98754 11.2332 8.78406 11.3939 8.63402C11.5547 8.48399 11.7727 8.3997 12 8.3997C12.2273 8.3997 12.4453 8.48399 12.6061 8.63402C12.7668 8.78406 12.8571 8.98754 12.8571 9.19972V19.5999ZM16.2831 19.6615C16.2745 19.7663 16.2438 19.8684 16.1928 19.9621C16.1419 20.0557 16.0717 20.1391 15.9862 20.2075C15.9007 20.2758 15.8016 20.3278 15.6946 20.3604C15.5876 20.393 15.4748 20.4056 15.3626 20.3975C15.2503 20.3895 15.1409 20.3608 15.0406 20.3133C14.9402 20.2657 14.8509 20.2002 14.7776 20.1204C14.7044 20.0406 14.6487 19.9481 14.6138 19.8482C14.5789 19.7484 14.5653 19.6431 14.574 19.5383L15.4311 9.13812C15.4381 9.03281 15.4677 8.92987 15.5181 8.83542C15.5685 8.74098 15.6387 8.65694 15.7246 8.58829C15.8104 8.51965 15.9102 8.4678 16.0179 8.43582C16.1256 8.40383 16.2391 8.39237 16.3517 8.4021C16.5784 8.41845 16.7888 8.51815 16.9366 8.67929C17.0845 8.84042 17.1578 9.04978 17.1403 9.26132L16.2831 19.6615Z" fill="white"/>
                                            </svg>
                                        </a>
                                        <a href="/cabinet/stream.html" class="cardButton _stats" data-stream="1" title="Подробная информация">Подобная информация</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pagePagination">
                            <a href="#" class="paginationItem _disabled">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.8206 14.8894L5.44472 7.99929L11.8206 1.10916C11.9348 0.986054 11.9987 0.820797 11.9987 0.648713C11.9987 0.476628 11.9348 0.311372 11.8206 0.188268C11.7652 0.128661 11.699 0.0813067 11.626 0.048979C11.553 0.0166514 11.4745 -2.29687e-08 11.3953 -2.64315e-08C11.3161 -2.98942e-08 11.2377 0.0166514 11.1646 0.048979C11.0916 0.0813067 11.0254 0.128661 10.97 0.188267L4.18577 7.51817C4.06666 7.64689 4 7.81954 4 7.99929C4 8.17905 4.06666 8.35169 4.18577 8.48042L10.9687 15.8103C11.0242 15.8703 11.0905 15.9181 11.1638 15.9506C11.2371 15.9832 11.3158 16 11.3953 16C11.4749 16 11.5536 15.9832 11.6269 15.9506C11.7001 15.9181 11.7665 15.8703 11.8219 15.8103C11.9361 15.6872 12 15.522 12 15.3499C12 15.1778 11.9361 15.0125 11.8219 14.8894L11.8206 14.8894Z" fill="white"/>
                                </svg>
                            </a>
                            <a href="#" class="paginationItem _active">1</a>
                            <a href="#" class="paginationItem">2</a>
                            <a href="#" class="paginationItem">3</a>
                            <a href="#" class="paginationItem">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.17936 1.11057L10.5553 8.00071L4.17936 14.8908C4.06521 15.0139 4.00131 15.1792 4.00131 15.3513C4.00131 15.5234 4.06521 15.6886 4.17936 15.8117C4.2348 15.8713 4.30098 15.9187 4.37401 15.951C4.44704 15.9833 4.52546 16 4.60468 16C4.6839 16 4.76232 15.9833 4.83536 15.951C4.90839 15.9187 4.97456 15.8713 5.03 15.8117L11.8142 8.48183C11.9333 8.35311 12 8.18046 12 8.00071C12 7.82095 11.9333 7.64831 11.8142 7.51958L5.03131 0.189678C4.97583 0.12965 4.90949 0.0819397 4.83622 0.0493612C4.76294 0.0167837 4.68422 9.61834e-07 4.60468 9.60885e-07C4.52515 9.59937e-07 4.44642 0.0167837 4.37315 0.0493612C4.29987 0.0819397 4.23353 0.12965 4.17805 0.189678C4.0639 0.312782 4 0.478041 4 0.650125C4 0.822208 4.0639 0.987465 4.17805 1.11057L4.17936 1.11057Z" fill="white"/>
                                    </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

    <script src="/assets/js/cabinet/datapicker.js"></script>
    <script src="/assets/js/cabinet/streams.js"></script>
@endsection
