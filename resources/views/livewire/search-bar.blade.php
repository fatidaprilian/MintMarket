<div class="relative w-full">
    {{-- Search Input Container --}}
    <div class="relative">
        <div class="relative group">
            <input type="text" 
                   wire:model.live.debounce.300ms="query"
                   placeholder="Cari produk, toko, atau kategori..." 
                   class="w-full pl-12 pr-12 py-3 bg-white border border-gray-300 rounded-xl shadow-sm 
                          focus:ring-2 focus:ring-sage-500/20 focus:border-sage-500 
                          transition-all duration-200 ease-in-out
                          hover:border-gray-400 hover:shadow-md
                          placeholder:text-gray-400 text-gray-700"
                   id="search-input"
                   onclick="this.focus()">
            
            {{-- Search Icon (hidden when loading) --}}
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                 wire:loading.remove wire:target="query">
                <svg class="h-5 w-5 text-gray-400 transition-colors duration-200" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            {{-- Loading Spinner (replaces search icon) --}}
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" 
                 wire:loading.flex wire:target="query">
                <svg class="animate-spin h-5 w-5 text-sage-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" 
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            {{-- Clear Button --}}
            @if($query)
                <button 
                    wire:click="clearSearch"
                    onclick="clearSearchFallback()"
                    type="button"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200 z-10 cursor-pointer"
                    wire:loading.remove wire:target="query">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Results Dropdown --}}
    @if($showResults && count($results) > 0)
        <div class="absolute top-full left-0 right-0 mt-3 bg-white border border-gray-200 rounded-xl shadow-xl z-[60] overflow-hidden backdrop-blur-sm">
            
            {{-- Search Results Header --}}
            <div class="px-4 py-3 bg-gray-50/80 backdrop-blur-sm border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-700">Hasil Pencarian</h3>
                    <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">{{ count($results) }} produk</span>
                </div>
            </div>

            {{-- Results List --}}
            <div class="max-h-72 overflow-y-auto">
                @foreach($results as $result)
                    <div wire:click="selectProduct({{ $result['id'] }})"
                         class="px-4 py-3 hover:bg-gray-50 cursor-pointer transition-all duration-150 
                                border-b border-gray-50 last:border-b-0 group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    {{-- Product Icon --}}
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-sage-100 to-sage-200 
                                                rounded-lg flex items-center justify-center group-hover:from-sage-200 group-hover:to-sage-300 
                                                transition-all duration-150 group-hover:scale-105">
                                        <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    
                                    {{-- Product Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-sage-700 transition-colors duration-150">
                                            {{ $result['name'] }}
                                        </p>
                                        <p class="text-sm font-semibold text-sage-600">
                                            Rp {{ number_format($result['price'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Arrow Icon --}}
                            <div class="flex-shrink-0 ml-4">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-sage-500 group-hover:translate-x-1 transition-all duration-150" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- View All Results Footer --}}
            <div class="px-4 py-3 bg-gray-50/80 backdrop-blur-sm border-t border-gray-100">
                <div class="w-full text-center">
                    <button class="text-sm text-sage-600 hover:text-sage-700 font-medium py-1 px-3 rounded-lg hover:bg-sage-50 transition-all duration-150">
                        Lihat semua hasil untuk "{{ $query }}"
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- No Results --}}
    @if($showResults && count($results) === 0 && !empty($query))
        <div class="absolute top-full left-0 right-0 mt-3 bg-white border border-gray-200 rounded-xl shadow-xl z-[60] backdrop-blur-sm">
            <div class="px-4 py-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada hasil ditemukan</h3>
                <p class="text-sm text-gray-500">
                    Coba gunakan kata kunci yang berbeda untuk "{{ $query }}"
                </p>
            </div>
        </div>
    @endif
</div>

{{-- JavaScript untuk functionality --}}
<script>
// Listen untuk event focus dari Livewire
document.addEventListener('livewire:initialized', () => {
    Livewire.on('focusSearch', () => {
        setTimeout(() => {
            const input = document.getElementById('search-input');
            if (input) {
                input.focus();
            }
        }, 100);
    });
});

// Fallback function jika wire:click gagal
function clearSearchFallback() {
    const input = document.getElementById('search-input');
    if (input) {
        input.value = '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
    }
}

// Click outside to close dropdown
document.addEventListener('click', function(event) {
    const searchContainer = document.querySelector('.relative.w-full');
    
    if (searchContainer && !searchContainer.contains(event.target)) {
        @this.call('hideResults');
    }
});

// ESC key to close dropdown
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        @this.call('hideResults');
    }
});
</script>