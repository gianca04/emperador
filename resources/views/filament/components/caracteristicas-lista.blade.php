<div class="space-y-2">
    @if($caracteristicas->count() > 0)
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            @foreach($caracteristicas as $caracteristica)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $caracteristica->name }}
                        </span>
                    </div>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                        +S/ {{ number_format((float)$caracteristica->precio, 2) }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="p-3 mt-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    Total por características:
                </span>
                <span class="text-sm font-bold text-blue-900 dark:text-blue-100">
                    S/ {{ number_format((float)$total_caracteristicas, 2) }}
                </span>
            </div>
        </div>
    @else
        <div class="py-4 text-center text-gray-500 dark:text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8v2m0 6V9"></path>
            </svg>
            <p class="text-sm">Esta habitación no tiene características adicionales</p>
        </div>
    @endif
</div>
