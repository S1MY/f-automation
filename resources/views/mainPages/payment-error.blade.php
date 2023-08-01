<script>
    setTimeout(function() {
        window.location.href = "{{ route('cabinet') }}";
    }, 4000);
</script>
@extends('layouts.master-no-header')
@section('content')
    <div class="page _index">
        <div class="indexForm _payment">
            <div class="paymentIcon">
                <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19265 26.9148C0.000825948 28.1066 0.000825948 30.039 1.19265 31.2308C2.38448 32.4226 4.31684 32.4226 5.50866 31.2308L16.2986 20.441L27.0889 31.2312C28.2807 32.423 30.2131 32.423 31.4049 31.2312C32.5967 30.0394 32.5967 28.1071 31.4049 26.9153L20.6146 16.125L31.4047 5.33493C32.5966 4.14311 32.5966 2.21077 31.4047 1.01895C30.2129 -0.172862 28.2805 -0.172862 27.0887 1.01895L16.2986 11.809L5.50884 1.0194C4.31701 -0.172417 2.38465 -0.172417 1.19283 1.0194C0.00100397 2.21121 0.00100397 4.14356 1.19283 5.33537L11.9825 16.125L1.19265 26.9148Z" fill="#FFFFFF"/>
                </svg>
            </div>
            <h1 class="formTitle _payment">Ошибка!</h1>
            <p class="paymentText">При пополнении баланса произошла ошибка!<br> Сейчас вас переместит в кабинет!</p>
        </div>
    </div>
@endsection
