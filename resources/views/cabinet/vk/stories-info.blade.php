@extends('cabinet.layouts.master')

@section('title', 'Информация об истории "'.$stories->title.'"')
<script>
    if({{ $user->Subscribe ? ($user->Subscribe->sub_status === 1 && unserialize($user->Subscribe->sub_config)['vk']['stories']['type1'] !== 1) : true }}){
      window.location.href = "{{ route('cabinet') }}";
    }
  </script>
@section('cabinet-content')
    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h2 class="pageTitle">{{ $stories->title}}</h2>
                    <p class="pageText _mt12">Вся статистика истории собирается за 5 минут до обновления. Следите за статусом. Если при статусе "В обработке" статистика нулевая, то нужно подождать когда будет запущена следующая история</p>
                    <div class="storyWrapper">
                        @include('load.stories-info-load')
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>
@endsection
