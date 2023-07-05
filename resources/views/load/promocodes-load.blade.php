<div class="pageTable">
    <div class="tableLine">
        <p class="tableItem">Промокод</p>
        <p class="tableItem">Использований</p>
        <p class="tableItem">Сумма скидки</p>
        <p class="tableItem">Активен</p>
    </div>
    <div id="promocodesLoad">
        @foreach ($promocodes as $promocode)
            <div class="tableLine _type0">
                <a href="#" class="tableItem" aria-label="Промокод">{{ $promocode->code }}</a>
                <p class="tableItem" aria-label="Использований">{{ is_null($promocode->users_active) ? 0 : count($promocode->users_active) }}/{{ $promocode->uses }}</p>
                <p class="tableItem" aria-label="Сумма скидки">{{$promocode->amount}}₽</p>
                <p class="tableItem" aria-label="Активен"><span class="_sum">{{ $promocode->active ? 'Да' : 'Нет' }}</span></p>
            </div>
        @endforeach
    </div>
</div>
{{ $promocodes->links('layouts.paginate', ['currentPage' => $currentPage]) }}
