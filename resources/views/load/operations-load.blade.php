@php
    use Carbon\Carbon;
    use App\Models\Operation;
    use App\Models\VkAccount;
    use App\Models\User;
@endphp
    <div class="tableLine">
        <p class="tableItem">Пользователь</p>
        <p class="tableItem">Дата операции</p>
        <p class="tableItem">Сумма операции</p>
        <p class="tableItem">Всего пополнено</p>
    </div>
    @foreach ($operations as $operation)
    @php
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $operation->created_at, 'Europe/Moscow');
        $date = $date->format('d.m.Y в h:i:s');
        $user = User::where('id', $operation->uid)->first();
        $vkAccount = VkAccount::where('uid', $operation->uid)->first();
        $allMoney = Operation::where('uid', $operation->uid)->sum('amount');
    @endphp
        <div class="tableLine">
            <a href="{{ $vkAccount->link }}" target="_blank" class="tableItem" aria-label="Пользователь">{{ $user->login }}</a>
            <p class="tableItem" aria-label="Дата операции">{{ $date }}</p>
            <p class="tableItem" aria-label="Сумма операции"><span class="_sum">{{ $operation->amount }}₽</span></p>
            <p class="tableItem" aria-label="Всего пополнено">{{ $allMoney }}₽</p>
        </div>
    @endforeach

{{ $operations->links('layouts.paginate', ['currentPage' => $currentPage]) }}