<div class="dataPopup">
    <div class="dataBg"></div>
    <div class="pickerCointainer">
        <h3 class="popupTitle">Выберите дату и время</h3>
        <p class="currentDate">11 Декабря 2023 в 06:30 (МСК)</p>
        <div class="pickerFlex">
            <div class="pickerItem">
                <p class="pickerBtn" data-name="day">+</p>
                <input type="number" name="day" class="pickerText">
                <p class="pickerBtn" data-name="day">-</p>
            </div>
            <div class="pickerItem">
                <p class="pickerBtn" data-name="month">+</p>
                <input type="number" name="month" class="pickerText">
                <p class="pickerBtn" data-name="month">-</p>
            </div>
            <div class="pickerItem">
                <p class="pickerBtn" data-name="year">+</p>
                <input type="number" name="year" class="pickerText">
                <p class="pickerBtn" data-name="year">-</p>
            </div>
            <div class="pickerItem">
                <p class="pickerBtn" data-name="hour">+</p>
                <input type="number" name="hour" class="pickerText">
                <p class="pickerBtn" data-name="hour">-</p>
            </div>
            <div class="pickerItem">
                <p class="pickerBtn" data-name="minute">+</p>
                <input type="number" name="minute" class="pickerText">
                <p class="pickerBtn" data-name="minute">-</p>
            </div>
        </div>
        <div class="popupCenter">
            <a href="#" class="popupBtn pickerComplete">Установить</a>
        </div>
    </div>
</div>

<div class="popupWrapper">
    <div class="popupBg"></div>
    <div class="popupItem" data-class="standard" data-popup="loader">
        <div class="popupIcon">
            <span class="popupLoader"></span>
        </div>
        <h3 class="popupTitle">Файл обрабатывается</h3>
        <p class="popupText _center">Пожалуйста дождитесь завершения обработки</p>
    </div>
    <div class="popupItem" data-class="guid" data-popup="streamStart">
        <h3 class="popupTitle"></h3>
        <div class="videoWrapper">
            <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/streamcreate.png">
                <source src="/files/guids/vkauth.mp4" type="video/mp4" />
            </video>
        </div>
    </div>
    <div class="popupItem" data-class="action" data-popup="noname">
        <h3 class="popupTitle">Получи скидку на подписку</h3>
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
    <div class="popupItem" data-class="standard" data-popup="error">
        <div class="popupIcon">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19265 26.9148C0.000825948 28.1066 0.000825948 30.039 1.19265 31.2308C2.38448 32.4226 4.31684 32.4226 5.50866 31.2308L16.2986 20.441L27.0889 31.2312C28.2807 32.423 30.2131 32.423 31.4049 31.2312C32.5967 30.0394 32.5967 28.1071 31.4049 26.9153L20.6146 16.125L31.4047 5.33493C32.5966 4.14311 32.5966 2.21077 31.4047 1.01895C30.2129 -0.172862 28.2805 -0.172862 27.0887 1.01895L16.2986 11.809L5.50884 1.0194C4.31701 -0.172417 2.38465 -0.172417 1.19283 1.0194C0.00100397 2.21121 0.00100397 4.14356 1.19283 5.33537L11.9825 16.125L1.19265 26.9148Z" fill="#FFFFFF"/>
            </svg>
        </div>
        <h3 class="popupTitle"></h3>
        <p class="popupText _center"></p>
        <div class="popupCenter">
            <a href="#" class="popupBtn popupClose">Закрыть</a>
        </div>
    </div>
    <div class="popupItem" data-class="standard" data-popup="success">
        <div class="popupIcon">
            <svg width="33" height="25" viewBox="0 0 33 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.10185 14.1601C4.55358 13.7102 3.83918 13.4662 3.10185 13.4768C2.36461 13.4875 1.6591 13.7521 1.12665 14.2175C0.594299 14.6829 0.274566 15.3149 0.231544 15.9864C0.188521 16.6579 0.425411 17.3194 0.894744 17.8383L7.38069 24.0487C7.65225 24.3087 7.97856 24.5158 8.33998 24.6578C8.70149 24.7998 9.09065 24.8737 9.48425 24.8751C9.87571 24.8771 10.2636 24.8074 10.625 24.6701C10.9863 24.5327 11.3138 24.3304 11.5878 24.0754L31.3962 5.41747C31.6649 5.16548 31.8763 4.86763 32.0187 4.54104C32.1612 4.21446 32.2317 3.86547 32.2263 3.51402C32.2208 3.16257 32.1396 2.81552 31.9872 2.49272C31.8347 2.16984 31.6141 1.87764 31.3378 1.63261C31.0616 1.38758 30.7352 1.19457 30.3771 1.06469C30.0192 0.934725 29.6366 0.870449 29.2513 0.875386C28.8661 0.880324 28.4857 0.954475 28.1319 1.09352C27.7781 1.23257 27.4577 1.43387 27.1891 1.68595L9.5134 18.3714L5.10185 14.1601Z" fill="#FFFFFF"/>
            </svg>
        </div>
        <h3 class="popupTitle"></h3>
        <p class="popupText _center"></p>
        <div class="popupCenter">
            <a href="#" class="popupBtn popupClose">Закрыть</a>
        </div>
    </div>
    <form class="popupItem" action="{{ route('vkUserAuth') }}" data-class="standard" data-popup="addAccount">
        <h3 class="popupTitle">Привязка аккаунта ВК</h3>
        <input type="text" name="link" class="formInput" required placeholder="Вставьте адресс сайта, открывшейся страницы">
        <input type="hidden" name="type" value="add">
        <button class="formBtn">Привязать аккаунт</button>
    </form>
    <div class="popupItem _places" data-class="standard" data-popup="maximumAccounts">
        <div class="popupIcon">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4487 28.5169L18.1715 27.2958C18.7321 26.3486 19.0125 25.875 19.4627 25.6131C19.913 25.3513 20.4799 25.3415 21.6137 25.322C23.2875 25.2931 24.3373 25.1906 25.2177 24.8259C26.8512 24.1493 28.1491 22.8514 28.8257 21.2179C29.3332 19.9928 29.3332 18.4396 29.3332 15.3334V14C29.3332 9.63545 29.3332 7.45316 28.3508 5.85003C27.8011 4.95299 27.0469 4.19879 26.1498 3.64909C24.5467 2.66669 22.3644 2.66669 17.9998 2.66669H13.9998C9.63526 2.66669 7.45298 2.66669 5.84985 3.64909C4.95281 4.19879 4.19861 4.95299 3.6489 5.85003C2.6665 7.45316 2.6665 9.63545 2.6665 14V15.3334C2.6665 18.4396 2.6665 19.9928 3.17397 21.2179C3.8506 22.8514 5.14843 24.1493 6.78195 24.8259C7.66235 25.1906 8.71213 25.2931 10.3859 25.322C11.5197 25.3415 12.0866 25.3513 12.5369 25.6131C12.9872 25.875 13.2675 26.3486 13.8281 27.2958L14.5509 28.5169C15.1951 29.6053 16.8045 29.6053 17.4487 28.5169ZM21.3332 16C22.0695 16 22.6665 15.4031 22.6665 14.6667C22.6665 13.9303 22.0695 13.3334 21.3332 13.3334C20.5968 13.3334 19.9998 13.9303 19.9998 14.6667C19.9998 15.4031 20.5968 16 21.3332 16ZM17.3332 14.6667C17.3332 15.4031 16.7362 16 15.9998 16C15.2635 16 14.6665 15.4031 14.6665 14.6667C14.6665 13.9303 15.2635 13.3334 15.9998 13.3334C16.7362 13.3334 17.3332 13.9303 17.3332 14.6667ZM10.6665 16C11.4029 16 11.9998 15.4031 11.9998 14.6667C11.9998 13.9303 11.4029 13.3334 10.6665 13.3334C9.93012 13.3334 9.33317 13.9303 9.33317 14.6667C9.33317 15.4031 9.93012 16 10.6665 16Z" fill="white"/>
            </svg>
        </div>
        <h3 class="popupTitle">Вы достигли лимита.</h3>
        <p class="popupText _center">У вас есть 5 мест под аккаунты ВКонтакте. Для дальнейшего расширения свяжитесь с администрацией!</p>
        <div class="popupCenter">
            <a href="#" class="popupBtn popupClose">Закрыть</a>
        </div>
    </div>
    @if(Auth::user()->VkAccount)
    <form class="popupItem" action="{{ route('vkGroupAuth') }}" data-class="standard" data-popup="addGroup">
        <h3 class="popupTitle">Привязка группы ВК</h3>
        <div class="formSelect addGroupSelect" data-action="{{ route('vkGroupList') }}">
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
                <p class="selectItem _active" data-type="addVkGroup" data-id="{{$vkAccount->id}}"><span class="_name">Аккаунт: {{$vkAccount->name}}</span></p>
                @else
                <p class="selectItem" data-type="addVkGroup" data-id="{{$vkAccount->id}}"><span class="_name">Аккаунт: {{$vkAccount->name}}</span></p>
                @endif
                @endforeach
            </div>
        </div>
        <div class="groupSelectItems">
            
        </div>
        <button class="formBtn">Привязать группу</button>
    </form>
    @endif
    <div class="popupItem" data-class="guid" data-popup="vkAuth">
        <h3 class="popupTitle"></h3>
        <div class="videoWrapper">
            <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/vkauth.png">
                <source src="/files/guids/vkauth.mp4" type="video/mp4" />
            </video>
        </div>
    </div>
    <div class="popupItem" data-class="guid" data-popup="tgAuth">
        <h3 class="popupTitle"></h3>
        <div class="videoWrapper">
            <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/tgauth.png">
                <source src="/files/guids/vkauth.mp4" type="video/mp4" />
            </video>
        </div>
    </div>
    {{-- <div class="popupItem" data-popup="error">
        <div class="popupIcon">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M1.19265 26.9148C0.000825948 28.1066 0.000825948 30.039 1.19265 31.2308C2.38448 32.4226 4.31684 32.4226 5.50866 31.2308L16.2986 20.441L27.0889 31.2312C28.2807 32.423 30.2131 32.423 31.4049 31.2312C32.5967 30.0394 32.5967 28.1071 31.4049 26.9153L20.6146 16.125L31.4047 5.33493C32.5966 4.14311 32.5966 2.21077 31.4047 1.01895C30.2129 -0.172862 28.2805 -0.172862 27.0887 1.01895L16.2986 11.809L5.50884 1.0194C4.31701 -0.172417 2.38465 -0.172417 1.19283 1.0194C0.00100397 2.21121 0.00100397 4.14356 1.19283 5.33537L11.9825 16.125L1.19265 26.9148Z"
                    fill="#FFFFFF" />
            </svg>
        </div>
        <h3 class="popupTitle"></h3>
        <p class="popupText _center"></p>
        <div class="popupCenter">
            <a href="#" class="popupBtn popupClose">Закрыть</a>
        </div>
    </div>
    <div class="popupItem" data-popup="success">
        <div class="popupIcon">
            <svg width="33" height="25" viewBox="0 0 33 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M5.10185 14.1601C4.55358 13.7102 3.83918 13.4662 3.10185 13.4768C2.36461 13.4875 1.6591 13.7521 1.12665 14.2175C0.594299 14.6829 0.274566 15.3149 0.231544 15.9864C0.188521 16.6579 0.425411 17.3194 0.894744 17.8383L7.38069 24.0487C7.65225 24.3087 7.97856 24.5158 8.33998 24.6578C8.70149 24.7998 9.09065 24.8737 9.48425 24.8751C9.87571 24.8771 10.2636 24.8074 10.625 24.6701C10.9863 24.5327 11.3138 24.3304 11.5878 24.0754L31.3962 5.41747C31.6649 5.16548 31.8763 4.86763 32.0187 4.54104C32.1612 4.21446 32.2317 3.86547 32.2263 3.51402C32.2208 3.16257 32.1396 2.81552 31.9872 2.49272C31.8347 2.16984 31.6141 1.87764 31.3378 1.63261C31.0616 1.38758 30.7352 1.19457 30.3771 1.06469C30.0192 0.934725 29.6366 0.870449 29.2513 0.875386C28.8661 0.880324 28.4857 0.954475 28.1319 1.09352C27.7781 1.23257 27.4577 1.43387 27.1891 1.68595L9.5134 18.3714L5.10185 14.1601Z"
                    fill="#FFFFFF" />
            </svg>
        </div>
        <h3 class="popupTitle"></h3>
        <p class="popupText _center"></p>
        <div class="popupCenter">
            <a href="#" class="popupBtn popupClose">Закрыть</a>
        </div>
    </div> --}}
    <div class="popupItem" data-class="guid" data-popup="packageCreate">
        <h3 class="popupTitle"></h3>
        <div class="videoWrapper">
            <video class="videoGuid" playsinline controls preload="metadata" poster="/files/guids/posters/subscribeconstruct.png">
                <source src="/files/guids/vkauth.mp4" type="video/mp4" />
            </video>
        </div>
    </div>
</div>
