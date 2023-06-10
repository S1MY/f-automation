@extends('cabinet.layouts.master')

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
                            <input type="text" name="name" class="formInput" placeholder="Название истории">
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
                        <div class="tabPageInput _full" data-name="file">
                            <div class="fileInput">
                                <div class="progressbar"></div>
                                <div class="myFile">
                                    <p class="fileName">Файл не выбран</p>
                                    <div class="myFileBtn">
                                        <span>Обзор...</span>
                                        <input type="file" name="file" id="file" class="hidden" accept="image/jpeg, video/mp4, video/avi, image/png">
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
                            </div>
                        </div>
                        <input type="hidden" name="duration" value="">
                        <button class="formBtn _full _disabled">Создать историю</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @include('cabinet.layouts.footer')
    </div>
</div>
<script src="/assets/js/cabinet/posts.js"></script>
@endsection
