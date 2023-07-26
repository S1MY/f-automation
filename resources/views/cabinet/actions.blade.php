
@extends('cabinet.layouts.master')

@section('title', 'Акции')

@section('cabinet-content')

    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h1 class="pageTitle">F-Акции</h1>
                    <p class="pageText">Участвуйте в акциях и получайте скидки. Ваше участие в акциях выгодно всем.</p>
                    <p class="pageText _mt12">Мы получаем дополнительную рекламу и прирост аудитории. Вы получаете скидку на дальнейшее приобретение/продление пакета.</p>
                    <p class="pageText _mt12">Полученные промокоды можно передавать третьим лицам. Никаких ограничений нет.</p>
                    <div class="tabItem _active">
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Тестовый период</span><span class="actionDate">на 4 дня</span></h2>
                            <p class="actionText">Вы получаете возможность протестировать функционал нашего сервиса</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Аккаунт который не участвовал в акции</p>
                            <p class="actionList">Тестовый период действует 1 раз</p>
                            <p class="actionText">Промо-сторис автоматически запустится в работув 00:00 по МСК</p>
                            @dd($testPeriod)
                            @if ($user->TestPeriod['users_join'] !== NULL)
                                @if (!in_array($user->id, unserialize($user->TestPeriod['users_join'])['users']))
                                    <p data-action="{{route('testPeriod')}}" class="actionButton">Протестировать</p>
                                @else
                                    <p class="actionComplete">Вы уже тестировали</p>
                                @endif
                            @else
                                <p data-action="{{route('testPeriod')}}" class="actionButton">Протестировать</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

@endsection
