<div>
    <!-- Panel de Búsqueda y Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Filtros de Habitaciones
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Filtro por Número -->
            <div>
                <label for="filtroNumero" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Número de Habitación
                </label>
                <input
                    type="text"
                    id="filtroNumero"
                    wire:model.live="filtroNumero"
                    placeholder="Ej: 101, 201..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
            </div>

            <!-- Filtro por Estado -->
            <div>
                <label for="filtroEstado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Estado
                </label>
                <select
                    id="filtroEstado"
                    wire:model.live="filtroEstado"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="">Todos los estados</option>
                    <option value="Disponible">Disponible</option>
                    <option value="Ocupada">Ocupada</option>
                    <option value="Limpiar">Por limpiar</option>
                    <option value="Deshabilitada">Deshabilitada</option>
                    <option value="Mantenimiento">En Mantenimiento</option>
                </select>
            </div>

            <!-- Filtro por Ubicación -->
            <div>
                <label for="filtroUbicacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Ubicación
                </label>
                <select
                    id="filtroUbicacion"
                    wire:model.live="filtroUbicacion"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="">Todas las ubicaciones</option>
                    <option value="Segundo Piso">Segundo Piso</option>
                    <option value="Tercer Piso">Tercer Piso</option>
                    <option value="Cuarto Piso">Cuarto Piso</option>
                    <option value="Quinto Piso">Quinto Piso</option>
                </select>
            </div>

            <!-- Toggle Solo Disponibles -->
            <div class="flex items-end">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model.live="mostrarSoloDisponibles"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Solo disponibles</span>
                </label>
            </div>
        </div>

        <!-- Botones de filtro rápido -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button
                wire:click="filtrarPorEstado('Disponible')"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-800 dark:text-green-100"
            >
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                Disponibles
            </button>
            <button
                wire:click="filtrarPorEstado('Ocupada')"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-800 dark:text-red-100"
            >
                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                Ocupadas
            </button>
            <button
                wire:click="filtrarPorEstado('Limpiar')"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-100"
            >
                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                Por limpiar
            </button>
            <button
                wire:click="filtrarPorEstado('Mantenimiento')"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-100"
            >
                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                Mantenimiento
            </button>
            <button
                wire:click="filtrarPorEstado('Deshabilitada')"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-100"
            >
                <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                Deshabilitadas
            </button>
            <button
                wire:click="limpiarFiltros"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-100"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Limpiar filtros
            </button>
        </div>

        <!-- Contador de resultados -->
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Mostrando {{ count($habitaciones) }} habitación(es)
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

        @foreach ($habitaciones as $habitacion)
            <div
                class="p-6 text-white flex flex-col rounded-xl shadow-lg transition-transform duration-300 hover:scale-105 hover:shadow-xl
                {{ $habitacion->estado === 'Disponible'
                    ? 'bg-green-700 border-green-900 dark:bg-green-800 dark:border-green-950'
                    : ($habitacion->estado === 'Ocupada'
                        ? 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700'
                        : ($habitacion->estado === 'Por limpiar'
                            ? 'bg-red-700 border-red-900 dark:bg-red-800 dark:border-red-950'
                            : ($habitacion->estado === 'En Mantenimiento'
                                ? 'bg-blue-700 border-blue-900 dark:bg-blue-800 dark:border-blue-950'
                                : 'bg-gray-700 border-gray-900 dark:bg-gray-800 dark:border-gray-950'))) }}">

                <div class="flex justify-between gap-2 items-center">
                    <h2 class="text-4xl inline-flex items-center font-extrabold tracking-wide">
                        <span class="mr-2">{{ $habitacion->numero }}</span> <!-- Aquí va la clase correcta -->
                        @switch($habitacion->estado)
                            @case('Ocupada')
                                <!-- Icono de Ocupado -->
                                <svg class="w-11 h-11 text-gray-800 dark:text-white ml-2" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512" fill="currentColor">
                                    <path
                                        d="M32 32c17.7 0 32 14.3 32 32v256h224V160c0-17.7 14.3-32 32-32h224c53 0 96 43 96 96v224c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32H256v32l-32 0H64v32c0 17.7-14.3 32-32 32s-32-14.3-32-32V64C0 46.3 14.3 32 32 32zm144 96a80 80 0 1 1 0 160 80 80 0 1 1 0-160z" />
                                </svg>
                            @break

                            @case('En Mantenimiento')
                                <!-- Icono de Mantenimiento -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>
                            @break

                            @case('Disponible')
                                <svg class="w-10 h-10 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M2.535 11A3.981 3.981 0 0 0 2 13v4a1 1 0 0 0 1 1h2v1a1 1 0 1 0 2 0v-1h10v1a1 1 0 1 0 2 0v-1h2a1 1 0 0 0 1-1v-4c0-.729-.195-1.412-.535-2H2.535ZM20 9V8a4 4 0 0 0-4-4h-3v5h7Zm-9-5H8a4 4 0 0 0-4 4v1h7V4Z" />
                                </svg>
                            @break

                            @case('Deshabilitada')
                                <!-- Icono de Inhabilitada -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            @break

                            @case('Por limpiar')
                                <!-- Icono de Limpieza -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                                    class="bi bi-stars" viewBox="0 0 16 16">
                                    <path
                                        d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.73 1.73 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.73 1.73 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.73 1.73 0 0 0 3.407 2.31zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z" />
                                </svg>
                            @break
                        @endswitch
                    </h2>

                    <span
                        class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $habitacion->estado === 'Disponible'
                            ? 'bg-green-900'
                            : ($habitacion->estado === 'Ocupada'
                                ? 'bg-red-900'
                                : ($habitacion->estado === 'Limpiar'
                                    ? 'bg-red-900'
                                    : ($habitacion->estado === 'En Mantenimiento'
                                        ? 'bg-blue-900'
                                        : ''))) }}">
                        {{ $habitacion->estado }}
                    </span>

                </div>


                <!-- Información de la habitación -->
                <div class="flex-1">
                    <div class="mb-2">
                        <span class="text-sm font-medium {{ $habitacion->estado === 'Ocupada' ? 'text-gray-600' : 'text-white' }}">
                            Tipo: {{ $habitacion->tipo?->name ?? 'No definido' }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <span class="text-sm {{ $habitacion->estado === 'Ocupada' ? 'text-gray-600' : 'text-white' }}">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Capacidad: {{ $habitacion->tipo?->capacidad ?? 1 }} persona(s)
                        </span>
                    </div>

                    @if($habitacion->ubicacion)
                    <div class="mb-2">
                        <span class="text-sm {{ $habitacion->estado === 'Ocupada' ? 'text-gray-600' : 'text-white' }}">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $habitacion->ubicacion }}
                        </span>
                    </div>
                    @endif

                    @if($habitacion->precio_final > 0)
                    <div class="mb-3">
                        <span class="text-lg font-bold {{ $habitacion->estado === 'Ocupada' ? 'text-gray-800' : 'text-white' }}">
                            S/ {{ number_format($habitacion->precio_final, 2) }}
                        </span>
                    </div>
                    @endif

                    @if($habitacion->ultima_limpieza)
                    <div class="text-xs {{ $habitacion->estado === 'Ocupada' ? 'text-gray-500' : 'text-gray-200' }}">
                        Última limpieza: {{ $habitacion->ultima_limpieza->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>

                <!-- Acciones -->
                <div class="flex justify-center mt-4">
                    @if($habitacion->estado === 'Disponible')
                        <button class="px-4 py-2 bg-white text-green-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium">
                            Asignar
                        </button>
                    @elseif($habitacion->estado === 'Ocupada')
                        <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 font-medium">
                            Ver detalles
                        </button>
                    @elseif($habitacion->estado === 'Limpiar')
                        <button class="px-4 py-2 bg-white text-red-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium">
                            Marcar limpia
                        </button>
                    @else
                        <button class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium">
                            Gestionar
                        </button>
                    @endif
                </div>

            </div>
        @endforeach

    </div>

    <div>
        {{ $this->table }}
    </div>



<div data-dial-init class="fixed bottom-6 end-24 group">
    <div id="speed-dial-menu-text-inside-button" class="flex flex-col items-center hidden mb-4 space-y-2">
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-full border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                <path d="M14.419 10.581a3.564 3.564 0 0 0-2.574 1.1l-4.756-2.49a3.54 3.54 0 0 0 .072-.71 3.55 3.55 0 0 0-.043-.428L11.67 6.1a3.56 3.56 0 1 0-.831-2.265c.006.143.02.286.043.428L6.33 6.218a3.573 3.573 0 1 0-.175 4.743l4.756 2.491a3.58 3.58 0 1 0 3.508-2.871Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Share</span>
        </button>
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-full border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 20h10a1 1 0 0 0 1-1v-5H4v5a1 1 0 0 0 1 1Z"/>
                <path d="M18 7H2a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2v-3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-1-2V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v3h14Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Print</span>
        </button>
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-full border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Save</span>
        </button>
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-full border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                <path d="M5 9V4.13a2.96 2.96 0 0 0-1.293.749L.879 7.707A2.96 2.96 0 0 0 .13 9H5Zm11.066-9H9.829a2.98 2.98 0 0 0-2.122.879L7 1.584A.987.987 0 0 0 6.766 2h4.3A3.972 3.972 0 0 1 15 6v10h1.066A1.97 1.97 0 0 0 18 14V2a1.97 1.97 0 0 0-1.934-2Z"/>
                <path d="M11.066 4H7v5a2 2 0 0 1-2 2H0v7a1.969 1.969 0 0 0 1.933 2h9.133A1.97 1.97 0 0 0 13 18V6a1.97 1.97 0 0 0-1.934-2Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Copy</span>
        </button>
    </div>
    <button type="button" data-dial-toggle="speed-dial-menu-text-inside-button" aria-controls="speed-dial-menu-text-inside-button" aria-expanded="false" class="flex items-center justify-center text-white bg-blue-700 rounded-full w-14 h-14 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800">
        <svg class="w-5 h-5 transition-transform group-hover:rotate-45" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
        </svg>
        <span class="sr-only">Open actions menu</span>
    </button>
</div>

<div data-dial-init class="fixed end-6 bottom-6 group">
    <div id="speed-dial-menu-text-inside-button-square" class="flex flex-col items-center hidden mb-4 space-y-2">
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-lg border border-gray-200 hover:text-gray-900 dark:border-gray-600 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                <path d="M14.419 10.581a3.564 3.564 0 0 0-2.574 1.1l-4.756-2.49a3.54 3.54 0 0 0 .072-.71 3.55 3.55 0 0 0-.043-.428L11.67 6.1a3.56 3.56 0 1 0-.831-2.265c.006.143.02.286.043.428L6.33 6.218a3.573 3.573 0 1 0-.175 4.743l4.756 2.491a3.58 3.58 0 1 0 3.508-2.871Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Share</span>
        </button>
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-lg border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 20h10a1 1 0 0 0 1-1v-5H4v5a1 1 0 0 0 1 1Z"/>
                <path d="M18 7H2a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2v-3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-1-2V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v3h14Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Print</span>
        </button>
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-lg border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Save</span>
        </button>
        <button type="button" class="w-[56px] h-[56px] text-gray-500 bg-white rounded-lg border border-gray-200 dark:border-gray-600 hover:text-gray-900 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
            <svg class="w-4 h-4 mx-auto mb-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                <path d="M5 9V4.13a2.96 2.96 0 0 0-1.293.749L.879 7.707A2.96 2.96 0 0 0 .13 9H5Zm11.066-9H9.829a2.98 2.98 0 0 0-2.122.879L7 1.584A.987.987 0 0 0 6.766 2h4.3A3.972 3.972 0 0 1 15 6v10h1.066A1.97 1.97 0 0 0 18 14V2a1.97 1.97 0 0 0-1.934-2Z"/>
                <path d="M11.066 4H7v5a2 2 0 0 1-2 2H0v7a1.969 1.969 0 0 0 1.933 2h9.133A1.97 1.97 0 0 0 13 18V6a1.97 1.97 0 0 0-1.934-2Z"/>
            </svg>
            <span class="block mb-px text-xs font-medium">Copy</span>
        </button>
    </div>
    <button type="button" data-dial-toggle="speed-dial-menu-text-inside-button-square" aria-controls="speed-dial-menu-text-inside-button-square" aria-expanded="false" class="flex items-center justify-center text-white bg-blue-700 rounded-lg w-14 h-14 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800">
        <svg class="w-5 h-5 transition-transform group-hover:rotate-45" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
        </svg>
        <span class="sr-only">Open actions menu</span>
    </button>
</div>

</div>
