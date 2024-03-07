<li>
    <a href="#embebed-{{ $i }}"
        class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">
        <span class="inline-flex justify-center items-center ml-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                </path>
            </svg>
        </span>
        <span class="ml-2 text-sm tracking-wide truncate">{{ $tema->contenido[$i]['data']['titulo'] ?? 'Sección ' . $i + 1 }}</span>
        <span class="px-2 py-0.5 ml-auto text-xs font-medium tracking-wide text-indigo-500 bg-indigo-50 rounded-full">
            <progress id='embebed-{{ $i }}-p' class="progress w-8" value="{{ $completada ? '100' : '0' }}" max="100"></progress>
        </span>
    </a>
</li>