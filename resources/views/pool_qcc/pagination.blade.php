@if ($paginator->hasPages())
    <div
        class="pool-qcc-pagination flex flex-col sm:flex-row items-center justify-between gap-3 py-3 px-4 glass-panel rounded-2xl border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
        <!-- Result count info -->
        <div>
            <span>Menampilkan</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span>
            <span>sampai</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span>
            <span>dari</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->total() }}</span>
            <span>total soal</span>
        </div>

        <!-- Pagination Controls -->
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span
                    class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-50 flex items-center space-x-1">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 font-bold transition flex items-center space-x-1 shadow-xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-slate-400 dark:text-slate-600 font-bold">...</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black flex items-center justify-center shadow-md shadow-indigo-600/30">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 font-bold flex items-center justify-center transition shadow-xs">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 font-bold transition flex items-center space-x-1 shadow-xs">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span
                    class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-50 flex items-center space-x-1">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </nav>
    </div>
@endif
