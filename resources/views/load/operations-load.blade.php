
    <div class="tableLine">
        <p class="tableItem">Пользователь</p>
        <p class="tableItem">Дата операции</p>
        <p class="tableItem">Сумма операции</p>
        <p class="tableItem">Всего пополнено</p>
    </div>
    @foreach ($operations as $operation)
        <div class="tableLine">
            <a href="#" class="tableItem" aria-label="Пользователь">benderGame</a>
            <p class="tableItem" aria-label="Дата операции">04.01.23 в 04:42:01</p>
            <p class="tableItem" aria-label="Сумма операции"><span class="_sum">150₽</span></p>
            <p class="tableItem" aria-label="Всего пополнено">1550₽</p>
        </div>
    @endforeach

{{ $operations->links('layouts.paginate', ['currentPage' => $currentPage]) }}