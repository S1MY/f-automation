
@foreach ($likedOffers as $offer)
<div class="offerItem">
    <a href="#" class="accountInfo">
        <div class="accountAvatar" style="background: url(/files/test/avatest.jpg), #FFFFFF;"></div>
        <p class="accountName">{{ $offer->login }}</p>
    </a>
    <p class="offerText">{{ $offer->offer }}</p>
    <p class="subCheckPrice">Набрано<span><span class="nowVotes">{{ is_null($offer->votes) ? 0 : count(unserialize($offer->votes)) }}</span>/{{ $offer->need }}</span>голосов</p>
    <p class="offerYes">Вы уже голосовали!</p>
</div>
@endforeach


{{ $likedOffers->links('layouts.paginate', ['currentPage' => $currentPage]) }}
