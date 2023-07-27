@extends('cabinet.layouts.master')

@section('title', 'АдминОчка')

@section('cabinet-content')

    <link rel="stylesheet" href="/assets/css/admin.css">

    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <div class="pageTabs" data-name="classic">
                        <p class="pageTab _active" data-tab="statistics">Статистика</p>
                        <p class="pageTab" data-tab="finance">Доходы</p>
                        <p class="pageTab" data-tab="offers">Предложения ({{ $offers->count() }})</p>
                        <p class="pageTab" data-tab="promocodes">Промокоды</p>
                    </div>
                    <div class="tabItem _active" data-tab="statistics">
                        <div class="adminFlex">
                            <div class="adminItem">
                                <p class="adminStat">Регистраций за 24 часа: <span>{{ $todayUser }}</span></p>
                                <p class="adminStat">Число пользователей (Без почты): <span>{{ $countUser }} ({{ $countUserNotEmail }})</span></p>
                                <p class="adminStat">Активных подписок: <span>{{ $activeSubs }}</span></p>
                                <p class="adminStat">Аккаунтов ВКонтакте: <span>{{ $vkAccounts }}</span></p>
                                <p class="adminStat">Групп ВКонтакте: <span>{{ $vkGroups }}</span></p>
                                <p class="adminStat">Аккаунтов Телеграм: <span>{{ $tgAccounts }}</span></p>
                                <p class="adminStat">Каналов Телеграм: <span>{{ $tgChannels }}</span></p>
                            </div>
                            <div class="adminItem">
                                <p class="adminStat">Трансляций ВКонтакте: <span>{{ $vkStreams }}</span></p>
                                <p class="adminStat">Активных трансляций: <span>{{ $vkStreamsActive }}</span></p>
                                <p class="adminStat">Трансляций в эфире: <span>{{ $vkStreamsLive }}</span></p>
                                <p class="adminStat">Трансляций Телеграм: <span>{{ $tgStreams }}</span></p>
                                <p class="adminStat">Активных трансляций: <span>{{ $tgStreamsActive }}</span></p>
                                <p class="adminStat">Трансляций в эфире: <span>{{ $tgStreamsLive }}</span></p>
                            </div>
                            <div class="adminItem">
                                <p class="adminStat">Историй ВКонтакте: <span>{{ $vkStories }}</span></p>
                                <p class="adminStat">Активных историй: <span>{{ $vkStoriesActive }}</span></p>
                                <p class="adminStat">Постов ВКонтакте: <span>{{ $vkPost }}</span></p>
                                <p class="adminStat">Активных постов: <span>{{ $vkPostActive }}</span></p>
                                <p class="adminStat">Постов Телеграм: <span>{{ $tgPost }}</span></p>
                                <p class="adminStat">Активных постов: <span>{{ $tgPostActive }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="finance">
                        <div class="adminFlex">
                            <div class="financeColumn">
                                <p class="adminFinance">
                                    Текущий месяц:
                                    <span>{{ $currentMonthMoney }}₽</span>
                                </p>
                                <p class="adminFinance">
                                    За 24 часа:
                                    <span>{{ $todayMoney }}₽</span>
                                </p>
                            </div>
                            <div class="financeColumn">
                                <p class="adminFinance">
                                    Прошлый месяц:
                                    <span>{{ $prevMonthMoney }}₽</span>
                                </p>
                                <p class="adminFinance _last">
                                    Доход за всё время:
                                    <span>{{ $allTimeMoney }}₽</span>
                                </p>
                            </div>
                        </div>
                        <div class="tableWrapper" data-name="finance">
                            
                        </div>
                    </div>
                    <div class="tabItem" data-tab="offers">
                        <div class="adminFlex">
                            @foreach ($offers as $offer)
                                <form class="offerItem" action="{{ route('offerUpdate') }}" id="offer-id-{{ $offer->id }}">
                                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                    <a href="#" class="accountInfo">
                                        <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
                                        <p class="accountName">{{ $offer->login }}</p>
                                    </a>
                                    <p class="offerText">{{ $offer->offer }}</p>
                                    @if ($offer->for_dev)
                                        <p class="subCheckPrice">Лично разрабам</p>
                                    @endif
                                    <input type="number" name="votes" class="formInput" placeholder="Скока голосов нада" required>
                                    <div class="offerBtns">
                                        <label for="goodOffer{{ $offer->id }}">
                                            <input type="radio" name="offer_status" value="1" id="goodOffer{{ $offer->id }}">
                                            <span class="offerBtn _yes">Заебись</span>
                                        </label>
                                        <label for="badOffer{{ $offer->id }}">
                                            <input type="radio" name="offer_status" value="3" id="badOffer{{ $offer->id }}">
                                            <span class="offerBtn _no">Хуета</span>
                                        </label>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                    <div class="tabItem" data-tab="promocodes">
                        <form class="promoForm" data-action="{{ route('setPromo') }}">
                            <div class="tabPageInput" data-name="promo">
                                <input type="text" name="promo" class="formInput" placeholder="Промокодус" required>
                            </div>
                            <div class="tabPageInput" data-name="count">
                                <input type="number" name="count" class="formInput" placeholder="Число использований" required>
                            </div>
                            <div class="tabPageInput" data-name="amount">
                                <input type="number" name="amount" class="formInput" placeholder="Сумма скидки" required>
                            </div>
                            <button class="formBtn">Создать промокод</button>
                        </form>
                        <div class="tableWrapper" data-name="promocodes">
                            @if ($promocodes->count() > 0)
                                @include('load.promocodes-load')
                            @else
                                <div class="pageTable">
                                    <div class="tableLine" style="justify-content:center">
                                        <p>Ещё нет активных промокодов</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

    <script src="/assets/js/admin/admin.js"></script>

@endsection
