
@foreach ($allStories as $stories)
    {{$stories->id}}
@endforeach

{{ $allStories->links('layouts.paginate', ['curentPage' => $curentPage]) }}
