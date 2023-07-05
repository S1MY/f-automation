@if ($paginator->hasPages())
    <div class="pagePagination">
        @if ($paginator->currentPage() > 1)
            <a href="{{ $paginator->url(1) }}" class="paginationItem arrow" data-action="{{ route('paginate') }}">
                &lt;
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="paginationItem disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="paginationItem active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="paginationItem" data-action="{{ route('paginate') }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->currentPage() < $paginator->lastPage())
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="paginationItem arrow" data-action="{{ route('paginate') }}">
                &gt;
            </a>
        @endif
    </div>
@endif