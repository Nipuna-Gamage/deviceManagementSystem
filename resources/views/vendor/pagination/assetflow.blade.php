@if ($paginator->hasPages())
<nav class="assetflow-pagination" aria-label="Pagination">
    <p class="pagination-summary">
        Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong>
        of <strong>{{ $paginator->total() }}</strong> results
    </p>
    <div class="pagination-controls">
        @if ($paginator->onFirstPage())
            <span class="pagination-link pagination-arrow is-disabled" aria-disabled="true" aria-label="Previous page">
                <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path d="m9.5 3.5-4 4 4 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg>
            </span>
        @else
            <a class="pagination-link pagination-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">
                <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path d="m9.5 3.5-4 4 4 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-link pagination-ellipsis" aria-hidden="true">{{ $element }}</span>
            @elseif (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pagination-link" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pagination-link pagination-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">
                <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path d="m6.5 3.5 4 4-4 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg>
            </a>
        @else
            <span class="pagination-link pagination-arrow is-disabled" aria-disabled="true" aria-label="Next page">
                <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path d="m6.5 3.5 4 4-4 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg>
            </span>
        @endif
    </div>
</nav>
@endif
