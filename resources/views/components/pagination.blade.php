{{-- resources/views/components/pagination.blade.php --}}
@if(method_exists($paginator, 'links') && $paginator->hasPages())
<div class="flex justify-center items-center my-12 mt-5" x-data="paginationHandler()">
    <div class="flex items-center space-x-2">
        <!-- Previous Button -->
        @if($paginator->previousPageUrl())
            <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" 
               class="h-10 w-10 flex justify-center items-center rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 transition duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
        @else
            <div class="h-10 w-10 flex justify-center items-center rounded-full bg-gray-100 text-gray-400 cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </div>
        @endif

        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            
            // Logic untuk menampilkan pagination
            $showPages = [];
            
            if ($lastPage <= 7) {
                // Jika total halaman <= 7, tampilkan semua
                for ($i = 1; $i <= $lastPage; $i++) {
                    $showPages[] = $i;
                }
            } else {
                // Jika total halaman > 7
                if ($currentPage <= 4) {
                    // Di awal: 1, 2, 3, 4, 5, ..., last
                    for ($i = 1; $i <= 5; $i++) {
                        $showPages[] = $i;
                    }
                    $showPages[] = '...';
                    $showPages[] = $lastPage;
                } elseif ($currentPage >= $lastPage - 3) {
                    // Di akhir: 1, ..., last-4, last-3, last-2, last-1, last
                    $showPages[] = 1;
                    $showPages[] = '...';
                    for ($i = $lastPage - 4; $i <= $lastPage; $i++) {
                        $showPages[] = $i;
                    }
                } else {
                    // Di tengah: 1, ..., current-1, current, current+1, ..., last
                    $showPages[] = 1;
                    $showPages[] = '...';
                    for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++) {
                        $showPages[] = $i;
                    }
                    $showPages[] = '...';
                    $showPages[] = $lastPage;
                }
            }
        @endphp

        <!-- Page Numbers -->
        @foreach($showPages as $page)
            @if($page === '...')
                <span class="h-10 w-10 flex justify-center items-center text-gray-500 font-medium">...</span>
            @else
                <a href="{{ $paginator->appends(request()->query())->url($page) }}" 
                   class="h-10 w-10 flex justify-center items-center rounded-full font-medium transition duration-150 
                          {{ $currentPage == $page ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        <!-- Next Button -->
        @if($paginator->nextPageUrl())
            <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" 
               class="h-10 w-10 flex justify-center items-center rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 transition duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        @else
            <div class="h-10 w-10 flex justify-center items-center rounded-full bg-gray-100 text-gray-400 cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript untuk pagination handler -->
<script>
    function paginationHandler() {
        return {
            // Fungsi tambahan untuk pagination jika diperlukan
            goToPage(page) {
                if (page && page !== '...') {
                    const baseUrl = '{{ $paginator->url('') }}';
                    const queryString = '{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}';
                    window.location.href = `${baseUrl}/${page}${queryString}`;
                }
            }
        }
    }
</script>
@endif