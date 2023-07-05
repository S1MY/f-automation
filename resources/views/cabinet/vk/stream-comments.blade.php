@extends('cabinet.layouts.master')

@php
    $storytesttitle = 'Название транлсяции';
@endphp

@section('title', 'Комментарии к трансляции "'.$storytesttitle.'"')

@section('cabinet-content')
    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h2 class="pageTitle">Комментарии "Название трансляции"</h2>
                    <p class="pageText _mt12">Комментарии, оставленные пользователями во время трансляции</p>
                    <div class="storyWrapper">
                        <div class="storyWrapperItem">
                            <p class="storyWrapperList"><span>Время:</span><span>24.12.22 в 12:14</span></p>
                            <div class="lastComment">
                                <a href="https://vk.com/feed" class="accountInfo" target="_blank">
                                    <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
                                    <p class="accountName">Фамилия Имя</p>
                                </a>
                                <p class="storyWrapperComment">Спасибо за трансляцию. Очень полезная информация</p>
                            </div>
                        </div>
                        <div class="storyWrapperItem">
                            <p class="storyWrapperList"><span>Время:</span><span>24.12.22 в 12:14</span></p>
                            <div class="lastComment">
                                <a href="https://vk.com/feed" class="accountInfo" target="_blank">
                                    <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
                                    <p class="accountName">Фамилия Имя</p>
                                </a>
                                <p class="storyWrapperComment">Спасибо за трансляцию. Очень полезная информация</p>
                            </div>
                        </div>
                        <div class="storyWrapperItem">
                            <p class="storyWrapperList"><span>Время:</span><span>24.12.22 в 12:14</span></p>
                            <div class="lastComment">
                                <a href="https://vk.com/feed" class="accountInfo" target="_blank">
                                    <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
                                    <p class="accountName">Фамилия Имя</p>
                                </a>
                                <p class="storyWrapperComment">Спасибо за трансляцию. Очень полезная информация</p>
                            </div>
                        </div>
                        <div class="storyWrapperItem">
                            <p class="storyWrapperList"><span>Время:</span><span>24.12.22 в 12:14</span></p>
                            <div class="lastComment">
                                <a href="https://vk.com/feed" class="accountInfo" target="_blank">
                                    <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
                                    <p class="accountName">Фамилия Имя</p>
                                </a>
                                <p class="storyWrapperComment">Спасибо за трансляцию. Очень полезная информация</p>
                            </div>
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
