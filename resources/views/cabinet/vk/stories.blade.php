@extends('cabinet.layouts.master')

@section('title', 'Истории Вконтакте')

@section('cabinet-content')
    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem _tabOpen" data-storage="tab">
                    <h1 class="pageTitle _tab">
                        Создание истории
                        <div class="titleIcon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188267 4.17936C0.128661 4.2348 0.0813067 4.30098 0.048979 4.37401C0.0166513 4.44704 -9.18749e-08 4.52546 -1.05726e-07 4.60468C-1.19577e-07 4.6839 0.0166513 4.76232 0.0489789 4.83536C0.0813066 4.90839 0.128661 4.97456 0.188267 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.35169 11.9333 8.48042 11.8142L15.8103 5.03132C15.8703 4.97583 15.9181 4.9095 15.9506 4.83622C15.9832 4.76295 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37315C15.9181 4.29987 15.8703 4.23354 15.8103 4.17805C15.6872 4.06391 15.522 4 15.3499 4C15.1778 4 15.0125 4.06391 14.8894 4.17805L14.8894 4.17936Z" fill="white"/>
                            </svg>
                        </div>
                    </h1>
                    <p class="pageText _mt12">Созданная история обновляется ежедневно во время своего создания. Подробнее в <a href="#" class="instructorLink" data-name="Как запустить трансляцию правильно" data-popup="streamStart">Инструкции</a></p>
                    <div class="tabPageItem">
                        <form class="tabPageForm" action="{{ route('storiesCreate') }}" enctype='multipart/form-data'>
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
                            <div class="tabPageGroup">
                                <div class="tabPageInput" data-name="linkUrl">
                                    <input type="text" name="linkUrl" class="formInput" placeholder="Адрес ссылки">
                                </div>
                                <div class="tabPageInput tabPageSelect" data-name="linkText">
                                    <div class="formSelect">
                                        <input type="hidden" name="linkText" value="learn_more">
                                        <div class="selectHeader">
                                            <p>Текст ссылки</p>
                                            <svg class="selectArrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.8894 4.17936L7.99929 10.5553L1.10916 4.17936C0.986054 4.06521 0.820797 4.00131 0.648713 4.00131C0.476628 4.00131 0.311372 4.06521 0.188268 4.17936C0.128661 4.2348 0.0813067 4.30098 0.0489791 4.37401C0.0166514 4.44704 0 4.52546 0 4.60468C0 4.6839 0.0166514 4.76232 0.0489791 4.83536C0.0813067 4.90839 0.128661 4.97456 0.188268 5.03L7.51817 11.8142C7.64689 11.9333 7.81954 12 7.99929 12C8.17905 12 8.3517 11.9333 8.48042 11.8142L15.8103 5.03131C15.8703 4.97583 15.9181 4.90949 15.9506 4.83622C15.9832 4.76294 16 4.68422 16 4.60468C16 4.52515 15.9832 4.44642 15.9506 4.37314C15.9181 4.29987 15.8703 4.23353 15.8103 4.17805C15.6872 4.0639 15.522 4 15.3499 4C15.1778 4 15.0125 4.0639 14.8894 4.17805V4.17936Z" fill="white"/>
                                            </svg>
                                        </div>
                                        <div class="selectFlow">
                                            <p class="selectItem" data-id="learn_more"><span class="_name">Подробнее</span></p>
                                            <p class="selectItem" data-id="to_store"><span class="_name">В магазин</span></p>
                                            <p class="selectItem" data-id="more"><span class="_name">Ещё</span></p>
                                            <p class="selectItem" data-id="vote"><span class="_name">Голосовать</span></p>
                                            <p class="selectItem" data-id="book"><span class="_name">Забронировать</span></p>
                                            <p class="selectItem" data-id="order"><span class="_name">Заказать</span></p>
                                            <p class="selectItem" data-id="enroll"><span class="_name">Записаться</span></p>
                                            <p class="selectItem" data-id="fill"><span class="_name">Заполнить</span></p>
                                            <p class="selectItem" data-id="signup"><span class="_name">Зарегистрироваться</span></p>
                                            <p class="selectItem" data-id="buy"><span class="_name">Купить</span></p>
                                            <p class="selectItem" data-id="ticket"><span class="_name">Купить билет</span></p>
                                            <p class="selectItem" data-id="write"><span class="_name">Написать</span></p>
                                            <p class="selectItem" data-id="open"><span class="_name">Открыть</span></p>
                                            <p class="selectItem" data-id="view"><span class="_name">Посмотреть</span></p>
                                            <p class="selectItem" data-id="go_to"><span class="_name">Перейти</span></p>
                                            <p class="selectItem" data-id="contact"><span class="_name">Связаться</span></p>
                                            <p class="selectItem" data-id="watch"><span class="_name">Смотреть</span></p>
                                            <p class="selectItem" data-id="play"><span class="_name">Слушать</span></p>
                                            <p class="selectItem" data-id="install"><span class="_name">Установить</span></p>
                                            <p class="selectItem" data-id="read"><span class="_name">Читать</span></p>
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
                </div>
                <div class="pageItem">
                    <h2 class="pageTitle">Ваши истории</h2>
                    <p class="pageText _mt12">Все истории, которые не активны больше 7 дней автоматически удаляются из базы данных без возможности их восстановления</p>
                    <div class="pageTabs" data-name="classic">
                        <p class="pageTab _active" data-tab="all">Все истории</p>
                        @if(Auth::user()->VkAccount)
                            @foreach (Auth::user()->VkAccount as $vkAccount)
                                    <p class="pageTab" data-tab="{{$vkAccount->id}}" data-accountId="{{$vkAccount->id}}" data-action="{{route('paginate')}}" data-type="account-stories">{{$vkAccount->name}}</p>
                                @foreach ($vkAccount->VkGroup as $VkGroup)
                                    <p class="pageTab" data-tab="{{$VkGroup->group_id}}" data-groupId="{{$VkGroup->group_id}}" data-action="{{route('paginate')}}" data-type="group-stories">{{$VkGroup->name}}</p>
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                    <div class="tabItem _active" data-tab="all">
                        @if ( $allStories->count() > 0 )
                            <div class="storiesAllWrapper">
                                @include('load.all-stories-load')
                            </div>
                        @else
                        <div class="cardEmpty">
                            <svg width="70" height="60" viewBox="0 0 70 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_723_32)">
                                <path d="M35 59.9475C54.3226 59.9475 69.9868 55.1723 69.9868 49.282C69.9868 43.3916 54.3226 38.6165 35 38.6165C15.6773 38.6165 0.0131836 43.3916 0.0131836 49.282C0.0131836 55.1723 15.6773 59.9475 35 59.9475Z" fill="white"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M55.4176 11.7687C52.7499 15.2889 49.9797 18.809 47.2608 22.3817C49.5693 24.2731 51.8265 26.1645 54.0838 28.1085C57.3157 30.9982 60.8554 33.6251 62.9074 37.6181C64.2412 40.1926 62.7535 43.0297 60.4963 43.8703C60.0859 44.5008 59.6242 45.1313 59.2138 45.7617C57.88 47.6006 56.6488 49.4395 54.289 49.9123C54.2377 49.9123 54.2377 49.9649 54.1864 49.9649C49.6719 52.5919 43.4646 45.3414 40.4892 42.5568C38.8476 41.0857 37.2573 39.6672 35.667 38.1961C32.0759 43.5025 28.7414 48.9666 25.9199 54.746C25.253 56.112 23.9705 56.9526 22.5854 57.0577C19.3022 58.161 15.9163 56.112 13.2487 54.2206C10.6837 52.4342 8.22131 50.4377 6.4258 47.7582C4.16858 46.2871 3.45038 42.9246 5.50239 40.6654C5.96409 40.14 6.4771 39.5621 6.9388 39.0367C7.0414 38.9841 7.0927 38.9316 7.144 38.8791C11.5045 34.2031 16.0189 29.6847 20.5847 25.2714C16.4806 22.0139 12.3766 18.6514 8.73431 14.816C6.6823 12.7144 7.5031 9.40447 9.65771 8.09098C12.4792 4.93862 16.2241 2.94212 19.7639 0.735465C22.2776 -0.788178 25.2017 0.472768 26.2277 2.88958C27.7667 4.30814 28.9979 5.93687 30.3317 7.51305C30.5369 7.67067 30.6908 7.77575 30.8447 7.93336L34.1279 9.7197L33.4097 10.6654C33.8201 11.0332 34.2818 11.401 34.6922 11.8213C37.719 8.87907 40.7457 5.88433 43.6698 2.83704C44.4393 0.682925 47.0043 -0.735639 49.3641 0.367689C50.8005 1.10324 52.2369 1.78625 53.6734 2.57434C56.0845 2.7845 58.1878 5.14877 57.7774 7.77575C57.7261 9.40447 56.8027 10.9806 55.4176 11.7687Z" fill="#202020"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M59.9833 39.1942C58.2391 35.7791 54.7507 33.1522 51.9291 30.6303C48.9024 28.0033 45.7731 25.4289 42.6951 22.907C46.2348 18.2835 49.8771 13.6075 53.4168 8.93151C54.0838 8.93151 54.5968 8.14342 54.4942 7.4604C54.802 6.56723 53.7247 5.62152 52.9038 5.9893C51.3135 5.04359 49.5693 4.20296 47.979 3.41487C47.3121 3.09963 46.5426 3.78265 46.7991 4.41312C42.9003 8.45865 38.8476 12.3991 34.7948 16.3396C33.1019 14.8685 31.409 13.3448 29.7674 11.7686C29.7161 11.4534 29.6135 11.1907 29.357 10.9805C29.2544 10.8755 29.1518 10.8229 29.1005 10.7704C29.1005 10.7704 29.0492 10.7704 29.0492 10.7178C28.7414 10.5077 28.4336 10.245 28.1258 10.0348C26.5868 8.19596 25.0991 6.19946 23.3549 4.78089C23.3036 3.88772 22.4315 3.04709 21.4568 3.62503C18.2762 5.62152 14.326 7.67056 11.8636 10.6128C11.8123 10.7178 11.761 10.8229 11.7097 10.928C11.0428 10.928 10.5298 11.9263 11.0428 12.5042C15.5059 17.1277 20.636 21.0681 25.6121 25.0611C20.0717 30.3676 14.5825 35.7266 9.40121 41.3483C9.34991 41.3483 9.34991 41.4009 9.29861 41.4009C8.83691 41.9263 8.3752 42.3991 7.9135 42.9245C7.0927 43.8177 7.7596 44.9735 8.6317 45.1312C10.222 47.7581 12.6331 49.7546 15.0955 51.4884C16.7884 52.6443 19.8665 54.7984 21.9698 53.7476C22.3802 53.8002 22.7906 53.6951 22.9958 53.2748C26.4329 46.1819 30.6395 39.562 35.1026 33.2047C37.6164 35.4639 40.1301 37.7756 42.6438 40.0348C43.362 40.6653 51.57 49.5445 52.9551 46.6023C53.1603 46.6548 53.3655 46.6548 53.5707 46.6548C54.8533 46.3921 55.8793 44.7634 56.5462 43.7651C57.2644 42.7669 58.0339 41.6636 58.6495 40.6128C59.5216 41.1907 60.445 39.9823 59.9833 39.1942Z" fill="white"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_723_32">
                                <rect width="70" height="60" fill="white"/>
                                </clipPath>
                                </defs>
                                </svg>
                            <p class="cardEmptyText">У вас нет историй</p>
                        </div>
                        @endif
                    </div>
                    @if(Auth::user()->VkAccount)
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

    <script src="/assets/js/cabinet/stories.js"></script>
@endsection
