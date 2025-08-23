<x-filament-panels::page>

    <!-- Contenido de la página -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- @livewire('Habitaciones') --}}

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach ($habitaciones as $habitacion)
            <div {{-- Colores --}}
                class="habitacion-card p-6 text-white flex flex-col rounded-xl shadow-lg min-h-[320px] relative overflow-hidden
                {{ $habitacion->estado === 'Disponible'
                    ? 'bg-gradient-to-br from-green-800 to-green-900 border-green-800 dark:from-green-700 dark:to-green-800'
                    : ($habitacion->estado === 'Ocupada'
                        ? 'bg-gradient-to-br from-gray-700 to-gray-800 border-gray-700 dark:from-gray-700 dark:to-gray-800'
                        : ($habitacion->estado === 'Limpiar'
                            ? 'bg-gradient-to-br from-red-700 to-red-800 border-yellow-600 dark:from-orange-800 dark:to-red-900'
                            : ($habitacion->estado === 'Mantenimiento'
                                ? 'bg-gradient-to-br from-blue-800 to-blue-900 border-blue-800 dark:from-blue-700 dark:to-blue-800'
                                : ($habitacion->estado === 'Deshabilitada'
                                    ? 'bg-gradient-to-br from-[#0c0e27] to-[#1a1d47] border-gray-900 dark:from-[#0c0e27] dark:to-[#1a1d47]'
                                    : 'bg-gradient-to-br from-gray-700 to-gray-800 border-gray-900 dark:from-gray-800 dark:to-gray-900')))) }}"
                style="backdrop-filter: blur(10px);"
            >

                <!-- Indicador de estado superior -->
                <div class="absolute top-0 left-0 right-0 h-1 rounded-t-xl
                {{ $habitacion->estado === 'Disponible'
                    ? 'bg-green-400'
                    : ($habitacion->estado === 'Ocupada'
                        ? 'bg-gray-400'
                        : ($habitacion->estado === 'Limpiar'
                            ? 'bg-yellow-400'
                            : ($habitacion->estado === 'Mantenimiento'
                                ? 'bg-blue-400'
                                : 'bg-red-400'))) }}">
                </div>

                <div class="flex items-center justify-between gap-2 mb-2">
                    <!-- Iconos segun estado de habitación -->
                    @switch($habitacion->estado)
                        @case('Ocupada')
                            <!-- Icono de Ocupado -->
                            <svg class="icon-bounce ml-2 text-white w-11 h-11 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 640 512" fill="currentColor">
                                <path
                                    d="M32 32c17.7 0 32 14.3 32 32v256h224V160c0-17.7 14.3-32 32-32h224c53 0 96 43 96 96v224c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32H256v32l-32 0H64v32c0 17.7-14.3 32-32 32s-32-14.3-32-32V64C0 46.3 14.3 32 32 32zm144 96a80 80 0 1 1 0 160 80 80 0 1 1 0-160z" />
                            </svg>
                        @break

                        @case('Mantenimiento')
                            <!-- Icono de Mantenimiento -->
                            <svg class="icon-bounce w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                            </svg>
                        @break

                        @case('Disponible')
                            <svg class="icon-bounce w-10 h-10 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M2.535 11A3.981 3.981 0 0 0 2 13v4a1 1 0 0 0 1 1h2v1a1 1 0 1 0 2 0v-1h10v1a1 1 0 1 0 2 0v-1h2a1 1 0 0 0 1-1v-4c0-.729-.195-1.412-.535-2H2.535ZM20 9V8a4 4 0 0 0-4-4h-3v5h7Zm-9-5H8a4 4 0 0 0-4 4v1h7V4Z" />
                            </svg>
                        @break

                        @case('Deshabilitada')
                            <!-- Icono de Inhabilitada -->
                            <svg class="icon-bounce w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        @break

                        @case('Limpiar')
                            <!-- Icono de Limpieza -->
                            <svg class="icon-bounce w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                                class="bi bi-stars" viewBox="0 0 16 16">
                                <path
                                    d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.73 1.73 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.73 1.73 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.73 1.73 0 0 0 3.407 2.31zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z" />
                            </svg>
                        @break
                    @endswitch
                    <h2 class="text-4xl font-extrabold tracking-wide">
                        <span class="mr-2">{{ $habitacion->numero }}</span>


                    </h2>
                    <!-- Información de estado de habitación -->
                    <span
                        class="px-2 py-1 rounded-full text-sm font-semibold
                        {{ $habitacion->estado === 'Disponible'
                            ? 'bg-green-900'
                            : ($habitacion->estado === 'Ocupada'
                                ? 'bg-gray-800'
                                : ($habitacion->estado === 'Limpiar'
                                    ? 'bg-red-900'
                                    : ($habitacion->estado === 'Mantenimiento'
                                        ? 'bg-blue-900'
                                        : 'bg-[#000000]'))) }}">
                        {{ $habitacion->estado }}
                    </span>
                </div>
                <!-- Título dinámico -->
                <div class="mt-4 mb-3">
                    <h5 class="text-xl font-bold text-white">
                        {{ $habitacion->tipo?->name ?? 'Tipo no definido' }}
                    </h5>
                    @if($habitacion->tipo?->capacidad)
                        <div class="flex items-center mt-1 text-sm text-gray-200">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            Capacidad: {{ $habitacion->tipo->capacidad }} persona{{ $habitacion->tipo->capacidad > 1 ? 's' : '' }}
                        </div>
                    @endif
                </div>

                <!-- Descripción -->
                @if($habitacion->descripcion)
                    <p class="mb-3 text-sm text-gray-200 line-clamp-2">
                        {{ $habitacion->descripcion }}
                    </p>
                @endif

                <!-- Información de precios -->
                <div class="mt-auto">
                    @if($habitacion->precio_final || $habitacion->tipo?->precio_final)
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-300">Precio:</span>
                            <span class="text-lg font-bold text-white">
                                ${{ number_format($habitacion->precio_final ?? $habitacion->tipo->precio_final, 2) }}
                            </span>
                        </div>
                    @endif

                    <!-- Ubicación -->
                    @if($habitacion->ubicacion)
                        <div class="flex items-center mb-2 text-sm text-gray-200">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $habitacion->ubicacion }}
                        </div>
                    @endif

                    <!-- Última limpieza -->
                    @if($habitacion->ultima_limpieza)
                        <div class="flex items-center mb-2 text-xs text-gray-300">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            Última limpieza: {{ $habitacion->ultima_limpieza->format('d/m/Y') }}
                        </div>
                    @endif

                    <!-- Características principales -->
                    @if($habitacion->tipo && $habitacion->tipo->caracteristicas->count() > 0)
                        <div class="mt-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($habitacion->tipo->caracteristicas->take(3) as $caracteristica)
                                    <span class="px-2 py-1 text-xs text-white bg-white rounded-full bg-opacity-20">
                                        {{ $caracteristica->name }}
                                    </span>
                                @endforeach
                                @if($habitacion->tipo->caracteristicas->count() > 3)
                                    <span class="px-2 py-1 text-xs text-gray-200 bg-white rounded-full bg-opacity-10">
                                        +{{ $habitacion->tipo->caracteristicas->count() - 3 }} más
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Notas adicionales -->
                    @if($habitacion->notas)
                        <div class="p-2 mt-2 text-xs text-gray-200 bg-black rounded bg-opacity-20">
                            <strong>Nota:</strong> {{ Str::limit($habitacion->notas, 60) }}
                        </div>
                    @endif

                    <!-- Botón de acción -->
                    <div class="pt-3 mt-4 border-t border-white border-opacity-20">
                        <button class="w-full px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-white rounded-lg bg-opacity-20 hover:bg-opacity-30">
                            @switch($habitacion->estado)
                                @case('Disponible')
                                    Ver Detalles
                                @break
                                @case('Ocupada')
                                    Gestionar Reserva
                                @break
                                @case('Limpiar')
                                    Marcar como Limpia
                                @break
                                @case('Mantenimiento')
                                    Ver Estado
                                @break
                                @default
                                    Ver Información
                            @endswitch
                        </button>
                    </div>
                </div>


            </div>
        @endforeach

    </div>



</x-filament-panels::page>
