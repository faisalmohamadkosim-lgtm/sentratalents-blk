@if ($paginator->hasPages())
  @if ($paginator->onFirstPage())
    <span class="page-info" style="opacity:.4">← Prev</span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}">← Prev</a>
  @endif

  <span class="page-info">Hal {{ $paginator->currentPage() }}</span>

  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}">Next →</a>
  @else
    <span class="page-info" style="opacity:.4">Next →</span>
  @endif
@endif
