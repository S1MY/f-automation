@extends('cabinet.layouts.master')

@section('title', 'Настройки')

@section('cabinet-content')

    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h2 class="pageTitle">Изменение пароля</h2>
                    <div class="tabPageItem">
                        <form class="tabPageForm passForm" data-action="/user/passchange">
                            <div class="tabPageInput" data-name="oldPass">
                                <input type="password" name="oldPass" class="formInput" placeholder="Старый пароль">
                            </div>
                            <div class="tabPageInput" data-name="newPass">
                                <input type="password" name="newPass" class="formInput" placeholder="Новый пароль">
                            </div>
                            <button class="formBtn">Сохранить</button>
                        </form>
                    </div>
                </div>
                {{-- <div class="pageItem">
                    <h2 class="pageTitle">Логин</h2>
                    <p class="pageText _mt12">После изменения логина изменится партнёрская ссылка</p>
                    <div class="tabPageItem">
                        <form class="tabPageForm" data-action="/user/loginchange">
                            <div class="tabPageInput" data-name="login">
                                <input type="text" name="login" class="formInput" value="AFSDSADSG">
                            </div>
                            <button class="formBtn">Изменить логин</button>
                        </form>
                    </div>
                </div> --}}
                <div class="pageItem">
                    <h2 class="pageTitle">Рассылка</h2>
                    <p class="pageText _mt12">Будь в курсе новых функций сервиса 24/7</p>
                    <div class="tabPageItem">
                        <form class="tabPageForm" data-action="/user/mailing">
                            <div class="tabPageInput">
                                <div class="customCheck">
                                    <span>Вы хотите получать новости?</span>
                                    <input type="checkbox" class="checkInput" name="mailing" value="1" checked>
                                    <span class="_check">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.33317 11.3333L13.2498 6.41667C13.4026 6.26389 13.5971 6.1875 13.8332 6.1875C14.0693 6.1875 14.2637 6.26389 14.4165 6.41667C14.5693 6.56944 14.6457 6.76389 14.6457 7C14.6457 7.23611 14.5693 7.43056 14.4165 7.58333L8.9165 13.0833C8.74984 13.25 8.55539 13.3333 8.33317 13.3333C8.11095 13.3333 7.9165 13.25 7.74984 13.0833L5.58317 10.9167C5.43039 10.7639 5.354 10.5694 5.354 10.3333C5.354 10.0972 5.43039 9.90278 5.58317 9.75C5.73595 9.59722 5.93039 9.52083 6.1665 9.52083C6.40261 9.52083 6.59706 9.59722 6.74984 9.75L8.33317 11.3333Z" fill="white"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <button class="formBtn">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

@endsection
