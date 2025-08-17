<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Contenedor Principal -->
    <div class="flex h-screen">
        <div class="flex-1 flex flex-col h-full overflow-auto px-6">
            @livewire('Habitaciones')
        </div>
    </div>
</x-filament-panels::page>
