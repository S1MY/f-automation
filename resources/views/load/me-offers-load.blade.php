
@foreach ($meOffers as $offer)
<div class="offerItem">
    <a href="{{$offer->account_link}}" target="_blank" class="accountInfo">
        <div class="accountAvatar" style="background: url({{$offer->account_avatar}}), #FFFFFF;"></div>
        <p class="accountName">{{ $offer->user_login }}</p>
    </a>
    <p class="offerText">{{ $offer->offer }}</p>
    <p class="subCheckPrice">Набрано<span>{{ is_null($offer->votes) ? 0 : count(unserialize($offer->votes)) }}/{{ $offer->need }}</span>голосов</p>
    @if ($offer->published == 0)
        <p class="offerYes offerProggres">Предложение на рассмотрение!</p>
    @elseif ($offer->published == 1)
        <p class="offerComplete">Предложение принято!</p>
    @else
        <p class="offerYes">Предложение отклонено!</p>
    @endif
</div>
@endforeach


{{ $meOffers->links('layouts.paginate', ['currentPage' => $currentPage]) }}
