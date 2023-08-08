@extends('cabinet.layouts.master')

@section('title', 'Инструкции')

@section('cabinet-content')

    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h1 class="pageTitle">Инструкции</h1>
                    <p class="pageText">В данном разделе вы можете найти ответы на все вопросы, которые касаются функционала нашего сервиса.</p>
                    <div class="pageTabs" data-name="classic">
                        <p class="pageTab _active" data-tab="main">Основные</p>
                        <p class="pageTab" data-tab="streams">Трансляции</p>
                        <p class="pageTab" data-tab="stories">Истории</p>
                        <p class="pageTab" data-tab="posts">Посты</p>
                    </div>
                    <div class="tabItem _active" data-tab="main">
                        <div class="tabFlex">
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/vkauth.png">
                                    <source src="/files/guids/vkauth.mp4" type="video/mp4" />
                                </video>
                            </div>
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/vkgroupauth.png">
                                    <source src="/files/guids/vkgroupauth.mp4" type="video/mp4" />
                                </video>
                            </div>
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/tgauth.png">
                                    <source src="/files/guids/tgauth.mp4" type="video/mp4" />
                                </video>
                            </div>
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/subscribeconstruct.png">
                                    <source src="/files/guids/subscribeconstruct.mp4" type="video/mp4" />
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="streams">
                        <div class="tabFlex">
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/streamcreate.png">
                                    <source src="/files/guids/streamcreate.mp4" type="video/mp4" />
                                </video>
                            </div>
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/streamtelegram.png">
                                    <source src="/files/guids/streamtelegram.mp4" type="video/mp4" />
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="posts">
                        <div class="tabFlex">
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/autoposting.png">
                                    <source src="/files/guids/autoposting.mp4" type="video/mp4" />
                                </video>
                            </div>
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/postingtelegram.png">
                                    <source src="/files/guids/postingtelegram.mp4" type="video/mp4" />
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="stories">
                        <div class="tabFlex">
                            <div class="videoWrapper">
                                <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/storiescreate.png">
                                    <source src="/files/guids/storiescreate.mp4" type="video/mp4" />
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

@endsection
