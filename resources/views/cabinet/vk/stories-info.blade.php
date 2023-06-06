@extends('cabinet.layouts.master')

@section('title', 'Информация об истории "'.$stories->title.'"')

@section('cabinet-content')
    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h2 class="pageTitle">История "{{ $stories->title}}"</h2>
                    <div class="streamStory">
                        <div class="streamStoryItem">
                            <p class="streamStoryList"><span>Время:</span><span>24.12.22 в 18:14</span></p>
                            <p class="streamStoryList"><span>Статус:</span><span class="_loading">В обработке</span></p>
                            <p class="streamStoryList _link"><span>Ссылка:</span><span>http://f-automation/cabinet/stories.html</span></p>
                            <p class="streamStoryList"><span>Тест ссылки:</span><span>Подробнее</span></p>
                            <p class="streamStoryList"><span>Просмотров:</span>
                                <span class="storyIcon">
                                    0
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4663 7.39768C12.3537 9.30146 9.85605 10.848 7.00005 10.848C4.14405 10.848 1.64637 9.30146 0.533805 7.39768C0.405398 7.17827 0.405398 6.82171 0.533805 6.60231C1.64637 4.69852 4.14405 3.15196 7.00005 3.15196C9.85605 3.15196 12.3537 4.69852 13.4663 6.60231C13.5947 6.82171 13.5947 7.17827 13.4663 7.39768ZM4.20159 6.99999C4.20159 5.45562 5.45568 4.20152 7.00005 4.20152C8.54443 4.20152 9.79852 5.45562 9.79852 6.99999C9.79852 8.54437 8.54443 9.79846 7.00005 9.79846C5.45568 9.79846 4.20159 8.54437 4.20159 6.99999Z" fill="#FF5437"/>
                                        <path d="M4.74707 7C4.74707 5.7564 5.7566 4.74687 7.00019 4.74687C8.24379 4.74687 9.25332 5.7564 9.25332 7C9.25332 8.24359 8.24379 9.25312 7.00019 9.25312C5.7566 9.25312 4.74707 8.24359 4.74707 7Z" fill="#FF5437"/>
                                    </svg>
                                </span>
                            </p>
                        </div>
                        <div class="streamStoryItem">
                            <p class="streamStoryList"><span>Время:</span><span>24.12.22 в 12:14</span></p>
                            <p class="streamStoryList"><span>Статус:</span><span class="_complete">Завершена</span></p>
                            <p class="streamStoryList _link"><span>Ссылка:</span><span>http://f-automation/cabinet/stories.html</span></p>
                            <p class="streamStoryList"><span>Тест ссылки:</span><span>Подробнее</span></p>
                            <p class="streamStoryList"><span>Просмотров:</span>
                                <span class="storyIcon">
                                    0
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4663 7.39768C12.3537 9.30146 9.85605 10.848 7.00005 10.848C4.14405 10.848 1.64637 9.30146 0.533805 7.39768C0.405398 7.17827 0.405398 6.82171 0.533805 6.60231C1.64637 4.69852 4.14405 3.15196 7.00005 3.15196C9.85605 3.15196 12.3537 4.69852 13.4663 6.60231C13.5947 6.82171 13.5947 7.17827 13.4663 7.39768ZM4.20159 6.99999C4.20159 5.45562 5.45568 4.20152 7.00005 4.20152C8.54443 4.20152 9.79852 5.45562 9.79852 6.99999C9.79852 8.54437 8.54443 9.79846 7.00005 9.79846C5.45568 9.79846 4.20159 8.54437 4.20159 6.99999Z" fill="#FF5437"/>
                                        <path d="M4.74707 7C4.74707 5.7564 5.7566 4.74687 7.00019 4.74687C8.24379 4.74687 9.25332 5.7564 9.25332 7C9.25332 8.24359 8.24379 9.25312 7.00019 9.25312C5.7566 9.25312 4.74707 8.24359 4.74707 7Z" fill="#FF5437"/>
                                    </svg>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>
@endsection
