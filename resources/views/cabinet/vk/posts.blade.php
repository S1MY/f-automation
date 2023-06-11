@extends('cabinet.layouts.master')
<link rel="stylesheet" href="/assets/css/slick.css">
@section('title', 'Посты Вконтакте')

@section('cabinet-content')
<div class="pageFlex">
    <div class="sidebar">
        @include('cabinet.layouts.sidebar')
    </div>
    <div class="pageWrapper">
        <div class="pageItems">
            <div class="pageItem _tabOpen" data-storage="tab">
                <h1 class="pageTitle _tab">
                    Создание поста
                    <div class="titleIcon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188267 4.17936C0.128661 4.2348 0.0813067 4.30098 0.048979 4.37401C0.0166513 4.44704 -9.18749e-08 4.52546 -1.05726e-07 4.60468C-1.19577e-07 4.6839 0.0166513 4.76232 0.0489789 4.83536C0.0813066 4.90839 0.128661 4.97456 0.188267 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.35169 11.9333 8.48042 11.8142L15.8103 5.03132C15.8703 4.97583 15.9181 4.9095 15.9506 4.83622C15.9832 4.76295 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37315C15.9181 4.29987 15.8703 4.23354 15.8103 4.17805C15.6872 4.06391 15.522 4 15.3499 4C15.1778 4 15.0125 4.06391 14.8894 4.17805L14.8894 4.17936Z" fill="white"/>
                        </svg>
                    </div>
                </h1>
                <p class="pageText _mt12">Созданный пост что-то делает</p>
                @if(count(Auth::user()->VkAccount) > 0)
                <div class="tabPageItem">
                    <form class="tabPageForm" action="{{ route('postCreate') }}" enctype='multipart/form-data'>
                        <input type="hidden" name="accType" value="user">
                        <div class="tabPageInput" data-name="name">
                            <input type="text" name="name" class="formInput" placeholder="Название поста">
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
                                    @foreach (Auth::user()->VkAccount as $key => $vkAccount)
                                    @if ($key === 0)
                                    <p class="selectItem _active" data-type="user" data-id="{{ $vkAccount->id }}"><span class="_name">Аккаунт: {{$vkAccount->name}}</span></p>
                                    @else
                                    <p class="selectItem" data-type="user" data-id="{{ $vkAccount->id }}"><span class="_name">Аккаунт: {{$vkAccount->name}}</span></p>
                                    @endif
                                    @foreach ($vkAccount->VkGroup as $VkGroup)
                                    <p class="selectItem" data-type="group" data-id="{{ $VkGroup->id }}"><span class="_name">Группа: {{ $VkGroup->name }}</span></p>
                                    @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="tabPageInput _full" data-name="message">
                            <textarea name="message" class="formInput" placeholder="Сообщение вашего поста"></textarea>
                        </div>
                        <div class="tabPageInput">
                            <div class="customCheck">
                                <span>Закрыть комментарии:</span>
                                <input type="checkbox" class="checkInput" name="closeComments" value="1">
                                <span class="_check">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.33317 11.3333L13.2498 6.41667C13.4026 6.26389 13.5971 6.1875 13.8332 6.1875C14.0693 6.1875 14.2637 6.26389 14.4165 6.41667C14.5693 6.56944 14.6457 6.76389 14.6457 7C14.6457 7.23611 14.5693 7.43056 14.4165 7.58333L8.9165 13.0833C8.74984 13.25 8.55539 13.3333 8.33317 13.3333C8.11095 13.3333 7.9165 13.25 7.74984 13.0833L5.58317 10.9167C5.43039 10.7639 5.354 10.5694 5.354 10.3333C5.354 10.0972 5.43039 9.90278 5.58317 9.75C5.73595 9.59722 5.93039 9.52083 6.1665 9.52083C6.40261 9.52083 6.59706 9.59722 6.74984 9.75L8.33317 11.3333Z" fill="white"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="tabPageInput">
                            <div class="customCheck">
                                <span>Только для друзей:</span>
                                <input type="checkbox" class="checkInput" name="onlyFriends" value="1">
                                <span class="_check">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.33317 11.3333L13.2498 6.41667C13.4026 6.26389 13.5971 6.1875 13.8332 6.1875C14.0693 6.1875 14.2637 6.26389 14.4165 6.41667C14.5693 6.56944 14.6457 6.76389 14.6457 7C14.6457 7.23611 14.5693 7.43056 14.4165 7.58333L8.9165 13.0833C8.74984 13.25 8.55539 13.3333 8.33317 13.3333C8.11095 13.3333 7.9165 13.25 7.74984 13.0833L5.58317 10.9167C5.43039 10.7639 5.354 10.5694 5.354 10.3333C5.354 10.0972 5.43039 9.90278 5.58317 9.75C5.73595 9.59722 5.93039 9.52083 6.1665 9.52083C6.40261 9.52083 6.59706 9.59722 6.74984 9.75L8.33317 11.3333Z" fill="white"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="tabPageInput _full" data-name="file">
                            <div class="fileInput">
                                <div class="progressbar"></div>
                                <div class="myFile">
                                    <p class="fileName">Файл не выбран</p>
                                    <div class="myFileBtn">
                                        <span>Обзор...</span>
                                        <input type="file" name="file[]" id="file" class="hidden" accept="image/jpeg, video/mp4, video/avi, image/png" multiple>
                                    </div>
                                </div>
                                <p class="fileText">
                                    <span>Статус загрузки:</span>
                                    <span class="_status">Файл не обнаружен</span>
                                </p>
                                <p class="fileText">
                                    <span>Размер файлов:</span>
                                    <span class="_size">0 mb</span>
                                </p>
                                <p class="fileText">
                                    <span>Числов файлов:</span>
                                    <span class="_filecounts">0 / 5</span>
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="duration" value="">
                        <button class="formBtn _full _disabled">Создать пост</button>
                    </form>
                </div>
                @endif
            </div>
            <div class="pageItem">
                <h2 class="pageTitle">Ваши посты</h2>
                <p class="pageText _mt12">Все посты, которые не активны больше 7 дней автоматически удаляются из базы данных без возможности их восстановления</p>
                <div class="pageTabs" data-name="classic">
                    <p class="pageTab _active" data-tab="all">Все посты</p>
                    @if(count(Auth::user()->VkAccount) > 0)
                        @foreach (Auth::user()->VkAccount as $vkAccount)
                                <p class="pageTab" data-tab="{{$vkAccount->id}}" data-accountId="{{$vkAccount->id}}" data-action="{{route('paginate')}}" data-type="account-posts">{{$vkAccount->name}}</p>
                            @foreach ($vkAccount->VkGroup as $VkGroup)
                                <p class="pageTab" data-tab="{{$VkGroup->group_id}}" data-groupId="{{$VkGroup->group_id}}" data-action="{{route('paginate')}}" data-type="group-posts">{{$VkGroup->name}}</p>
                            @endforeach
                        @endforeach
                    @endif
                </div>
                <div class="tabItem _active" data-tab="all">
                    <div class="postsAllWrapper">
                        @include('load.all-posts-load')
                    </div>
                </div>
                @if(count(Auth::user()->VkAccount) > 0)
                    @foreach (Auth::user()->VkAccount as $vkAccount)
                        <div class="tabItem" data-tab="{{$vkAccount->id}}" data-type="account">
                            <div class="rowFlex">
                            </div>
                        </div>
                        @foreach ($vkAccount->VkGroup as $VkGroup)
                            <div class="tabItem" data-tab="{{$VkGroup->group_id}}" data-type="group">
                                <div class="rowFlex">
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
        @include('cabinet.layouts.footer')
    </div>
</div>
<script src="/assets/js/slick.min.js"></script>
<script src="/assets/js/cabinet/posts.js"></script>
@endsection
