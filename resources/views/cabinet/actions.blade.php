
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
                    <div class="pageTabs">
                        <p class="pageTab _active" data-tab="new">Все акции</p>
                        <p class="pageTab" data-tab="you">Ваши акции</p>
                    </div>
                    <div class="tabItem _active" data-tab="new">
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-ролик</span><span class="actionDate">до 16.12.2023</span></h2>
                            <p class="actionText">С 16.11 до 16.12 включительно получи промокод номиналом 100₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку</p>
                            <p class="actionList">Стримить наш промо-ролик в течении 3 дней 6 раз</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-ролик начнёт работу автоматически и будет запускаться каждые 6 часов по 2 раза в день.</p>
                            <p class="actionText">Первый запуск в 10:00 по МСК, второй запуск в 16:00 по МСК.</p>
                            <p class="actionText">Нажимая кнопку <span class="_number">"Участвовать в акции"</span> вы принимаете все условия акции и автоматически попадаете в очередь на запуск промо-трансляции.</p>
                            <p class="actionComment">Во время активного участия в акции вы не сможете запускать другие трансляции.</p>
                            <a href="#" class="actionButton">Участвовать в акции</a>
                        </div>
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.12.2023</span></h2>
                            <p class="actionText">С 16.11 до 16.12 включительно получи промокод номиналом 35₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис выложена <span class="_number">1</span> из <span class="_number">3</span> дней.</p>
                            <p class="actionCurrent">Вы уже участвуете</p>
                        </div>
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.10.2023</span></h2>
                            <p class="actionText">С 16.09 до 16.10 включительно получи промокод номиналом 35₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис автоматически запустится в работув 00:00 по МСК</p>
                            <p class="actionComplete">Акция закончилась</p>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="you">
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.12.2023</span></h2>
                            <p class="actionText">С 16.11 до 16.12 включительно получи промокод номиналом 35₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис выложена <span class="_number">1</span> из <span class="_number">3</span> дней.</p>
                            <p class="actionCurrent">Вы уже участвуете</p>
                        </div>
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.11.2023</span></h2>
                            <p class="actionText">С 16.10 до 16.11 включительно получи промокод номиналом 30₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис выложена <span class="_number">3</span> из <span class="_number">3</span> дней.</p>
                            <p class="actionListName">Ваш промокод</p>
                            <p class="actionList">Действителен до 27.11.2023</p>
                            <div class="partnerItemChild _promocode">
                                <p class="partnerItemLink _link">PS10YES11</p>
                                <a href="#" class="partnerItemBtn">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 18C8.45 18 7.97933 17.8043 7.588 17.413C7.196 17.021 7 16.55 7 16V4C7 3.45 7.196 2.979 7.588 2.587C7.97933 2.19567 8.45 2 9 2H18C18.55 2 19.021 2.19567 19.413 2.587C19.8043 2.979 20 3.45 20 4V16C20 16.55 19.8043 17.021 19.413 17.413C19.021 17.8043 18.55 18 18 18H9ZM5 22C4.45 22 3.979 21.8043 3.587 21.413C3.19567 21.021 3 20.55 3 20V7C3 6.71667 3.096 6.479 3.288 6.287C3.47933 6.09567 3.71667 6 4 6C4.28333 6 4.521 6.09567 4.713 6.287C4.90433 6.479 5 6.71667 5 7V20H15C15.2833 20 15.521 20.096 15.713 20.288C15.9043 20.4793 16 20.7167 16 21C16 21.2833 15.9043 21.5207 15.713 21.712C15.521 21.904 15.2833 22 15 22H5Z" fill="white"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

@endsection
