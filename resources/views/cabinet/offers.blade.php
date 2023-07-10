@extends('cabinet.layouts.master')

@section('title', 'Предложения')

@section('cabinet-content')

    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h1 class="pageTitle">Предложения</h1>
                    <p class="pageText _mt12">У вас есть КРУТАЯ идея для нашего сервиса? Мы готовы её рассмотреть, провести опрос среди пользователей и реализовать данную идею. В случае её успеха вы получите промокод  на скидку до 50% к оплате подписки 1 месяца.</p>
                    <p class="pageText _mt12">Вы можете отправить предложение только нам, но в таком случае оно будет реализовано с меньшей вероятностью.</p>
                    <p class="pageText _mt12">Гораздо понятней, когда ты видишь, что нужно пользователю. Поэтому мы рассматриваем каждую идею и определяем кол-во нужных голосов, для релизации идеи. Мы хотим делать только нужный контент, а не мешать всё подряд.</p>
                    <form class="tabPageForm" action="{{ route('setOffer') }}">
                        <div class="tabPageInput _full" data-name="offer">
                            <textarea name="offer" class="formArea _h100" placeholder="Ваше предложение"></textarea>
                        </div>
                        <div class="tabPageInput _full" data-name="developer">
                            <div class="customCheck">
                                <span>Только для разработчиков:</span>
                                <input type="checkbox" class="checkInput" name="developer" value="1">
                                <span class="_check">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.33317 11.3333L13.2498 6.41667C13.4026 6.26389 13.5971 6.1875 13.8332 6.1875C14.0693 6.1875 14.2637 6.26389 14.4165 6.41667C14.5693 6.56944 14.6457 6.76389 14.6457 7C14.6457 7.23611 14.5693 7.43056 14.4165 7.58333L8.9165 13.0833C8.74984 13.25 8.55539 13.3333 8.33317 13.3333C8.11095 13.3333 7.9165 13.25 7.74984 13.0833L5.58317 10.9167C5.43039 10.7639 5.354 10.5694 5.354 10.3333C5.354 10.0972 5.43039 9.90278 5.58317 9.75C5.73595 9.59722 5.93039 9.52083 6.1665 9.52083C6.40261 9.52083 6.59706 9.59722 6.74984 9.75L8.33317 11.3333Z" fill="white"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <button class="formBtn _full">Отправить идею</button>
                    </form>
                </div>
                <div class="pageItem">
                    <h2 class="pageTitle">Лента ВАШИХ идей</h2>
                    <div class="pageTabs" data-name="classic">
                        <p class="pageTab _active" data-tab="all">Все предложения</p>
                        <p class="pageTab" data-tab="you">Вы проголосовали</p>
                        <p class="pageTab" data-tab="complete">Принятые идеи</p>
                        <p class="pageTab" data-tab="me">Ваши идеи</p>
                    </div>
                    <div class="tabItem _active" data-tab="all">
                        <div class="offerFlex">
                            @include('load.all-offers-load')
                        </div>
                    </div>
                    <div class="tabItem" data-tab="you">
                        <div class="offerFlex">
                            @include('load.liked-offers-load')
                        </div>
                    </div>
                    <div class="tabItem" data-tab="complete">
                        <div class="offerFlex">
                            <div class="offerItem">
                                @include('load.complete-offers-load')
                            </div>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="me">
                        <div class="offerFlex">
                            @include('load.me-offers-load')
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

    <script src="/assets/js/cabinet/offers.js"></script>
@endsection
