
    @foreach ($allOffers as $offer)
        <div class="offerItem">
            <a href="{{$offer->account_link}}" target="_blank" class="accountInfo">
                <div class="accountAvatar" style="background: url({{$offer->account_avatar}}), #FFFFFF;"></div>
                <p class="accountName">{{ $offer->user_login }}</p>
            </a>
            <p class="offerText">{{ $offer->offer }}</p>
            <p class="subCheckPrice">Набрано<span><span class="nowVotes">{{ is_null($offer->votes) ? 0 : count(unserialize($offer->votes)) }}</span>/{{ $offer->need }}</span>голосов</p>
            <a href="#" class="offerBtn" data-offer="{{ $offer->id }}" data-action="{{ route('getVotes') }}">Проголосовать</a>
        </div>
    @endforeach


{{ $allOffers->links('layouts.paginate', ['currentPage' => $currentPage]) }}
