@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de Paginación" class="flex items-center justify-between w-full">
        
        <!-- Versión Móvil -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-gray-600 bg-gray-950 border border-gray-800 rounded-xl cursor-not-allowed">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-gray-300 bg-gray-900 border border-gray-800 rounded-xl hover:bg-gray-800 transition">
                    Anterior
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-gray-300 bg-gray-900 border border-gray-800 rounded-xl hover:bg-gray-800 transition">
                    Siguiente
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-gray-600 bg-gray-950 border border-gray-800 rounded-xl cursor-not-allowed">
                    Siguiente
                </span>
            @endif
        </div>

        <!-- Versión ESCRITORIO (Traducida al Español) -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-gray-400">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-black text-white">{{ $paginator->firstItem() }}</span>
                        a
                        <span class="font-black text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    de
                    <span class="font-black text-white">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-xl rounded-xl overflow-hidden border border-gray-800 bg-gray-950">
                    
                    {{-- Botón Anterior (<) --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Anterior">
                            <span class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-gray-600 bg-gray-950 cursor-not-allowed" aria-hidden="true">&lsaquo;</span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-gray-300 bg-gray-900 hover:bg-gray-800 hover:text-white transition" aria-label="Anterior">&lsaquo;</a>
                    @endif

                    {{-- Números de Página --}}
                    @foreach ($elements as $element)
                        {{-- Separador "..." --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-gray-500 bg-gray-950 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array de Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-black text-indigo-400 bg-indigo-600/20 border-x border-indigo-500/30 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3.5 py-2 text-xs font-bold text-gray-400 bg-gray-900 hover:bg-gray-800 hover:text-white transition border-x border-gray-800/60" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Botón Siguiente (>) --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-gray-300 bg-gray-900 hover:bg-gray-800 hover:text-white transition" aria-label="Siguiente">&rsaquo;</a>
                    @else
                        <span aria-disabled="true" aria-label="Siguiente">
                            <span class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-gray-600 bg-gray-950 cursor-not-allowed" aria-hidden="true">&rsaquo;</span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif