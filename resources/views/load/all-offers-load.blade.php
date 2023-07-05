<div class="offerFlex">

    @foreach ($allOffers as $offer)
        <div class="offerItem">
            <a href="#" class="accountInfo">
                <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
                <p class="accountName">{{ $offer->login }}</p>
            </a>
            <p class="offerText">{{ $offer->offer }}</p>
            <p class="subCheckPrice">Набрано<span><span class="nowVotes">{{ is_null($offer->votes) ? 0 : count(unserialize($offer->votes)) }}</span>/{{ $offer->need }}</span>голосов</p>
            <a href="#" class="offerBtn" data-offer="{{ $offer->id }}" data-action="{{ route('getVotes') }}">Проголосовать</a>
        </div>
    @endforeach

</div>

{{ $allOffers->links('layouts.paginate', ['currentPage' => $currentPage]) }}
