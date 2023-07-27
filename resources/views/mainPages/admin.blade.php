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
                                    <span>62200₽</span>
                                </p>
                                <p class="adminFinance _last">
                                    Доход за всё время:
                                    <span>{{ $allTimeMoney }}₽</span>
                                </p>
                            </div>
                        </div>
                        <div class="tableWrapper" data-name="finance">
                            <div class="pageTable">
                                <div class="tableLine">
                                    <p class="tableItem">Пользователь</p>
                                    <p class="tableItem">Дата операции</p>
                                    <p class="tableItem">Сумма операции</p>
                                    <p class="tableItem">Всего пополнено</p>
                                </div>
                                <div class="tableLine _type0">
                                    <a href="#" class="tableItem" aria-label="Пользователь">benderGame</a>
                                    <p class="tableItem" aria-label="Дата операции">04.01.23 в 04:42:01</p>
                                    <p class="tableItem" aria-label="Сумма операции"><span class="_sum">150₽</span></p>
                                    <p class="tableItem" aria-label="Всего пополнено">1550₽</p>
                                </div>
                                <div class="tableLine _type0">
                                    <a href="#" class="tableItem" aria-label="Пользователь">benderGame</a>
                                    <p class="tableItem" aria-label="Дата операции">04.01.23 в 04:42:01</p>
                                    <p class="tableItem" aria-label="Сумма операции"><span class="_sum">150₽</span></p>
                                    <p class="tableItem" aria-label="Всего пополнено">1550₽</p>
                                </div>
                                <div class="tableLine _type0">
                                    <a href="#" class="tableItem" aria-label="Пользователь">benderGame</a>
                                    <p class="tableItem" aria-label="Дата операции">04.01.23 в 04:42:01</p>
                                    <p class="tableItem" aria-label="Сумма операции"><span class="_sum">150₽</span></p>
                                    <p class="tableItem" aria-label="Всего пополнено">1550₽</p>
                                </div>
                                <div class="tableLine _type0">
                                    <a href="#" class="tableItem" aria-label="Пользователь">benderGame</a>
                                    <p class="tableItem" aria-label="Дата операции">04.01.23 в 04:42:01</p>
                                    <p class="tableItem" aria-label="Сумма операции"><span class="_sum">150₽</span></p>
                                    <p class="tableItem" aria-label="Всего пополнено">1550₽</p>
                                </div>
                                <div class="tableLine _type1">
                                    <a href="#" class="tableItem" aria-label="Пользователь">benderGame</a>
                                    <p class="tableItem" aria-label="Дата операции">04.01.23 в 04:42:01</p>
                                    <p class="tableItem" aria-label="Сумма операции"><span class="_sum">150₽</span></p>
                                    <p class="tableItem" aria-label="Всего пополнено">1550₽</p>
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
