<div>
    <div>
        {{ $this->table }}
    </div>
    <link
    href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css"
    rel="stylesheet" />

    <script
      src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <h1>Contador</h1>

    <section>
        <h1>{{ $count }}</h1>
        <x-filament::button wire:click="increment">
            +
        </x-filament::button>
        <x-filament::button wire:click="decrement">
            -
        </x-dynamic-component>
    </section>

    

<div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div class="flex justify-end px-4 pt-4">
        <button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1.5" type="button">
            <span class="sr-only">Open dropdown</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
            </svg>
        </button>
        <!-- Dropdown menu -->
        <div id="dropdown" class="z-10 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
            <ul class="py-2" aria-labelledby="dropdownButton">
            <li>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Edit</a>
            </li>
            <li>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Export Data</a>
            </li>
            <li>
                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete</a>
            </li>
            </ul>
        </div>
    </div>
    <div class="flex flex-col items-center pb-10">
        <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="/docs/images/people/profile-picture-3.jpg" alt="Bonnie image"/>
        <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">Bonnie Green</h5>
        <span class="text-sm text-gray-500 dark:text-gray-400">Visual Designer</span>
        <div class="flex mt-4 md:mt-6">
            <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add friend</a>
            <a href="#" class="py-2 px-4 ms-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Message</a>
        </div>
    </div>
</div>

    <!-- component -->
<!-- component -->
<div class="flex justify-center items-center min-h-screen">
    <div class="max-w-[720px] mx-auto">
        <div class="block mb-4 mx-auto border-b border-slate-300 pb-2 max-w-[360px]">
            <a 
                target="_blank" 
                href="https://www.material-tailwind.com/docs/html/card" 
                class="block w-full px-4 py-2 text-center text-slate-700 transition-all"
            >
                More components on <b>Material Tailwind</b>.
            </a>
        </div>

        <!-- Centering wrapper -->
        <div class="relative flex flex-col text-gray-700 bg-white shadow-md bg-clip-border rounded-xl w-96">
            <div class="relative mx-4 mt-4 overflow-hidden text-gray-700 bg-white bg-clip-border rounded-xl h-96">
                <img
                    src="https://images.unsplash.com/photo-1629367494173-c78a56567877?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=927&amp;q=80"
                    alt="card-image" class="object-cover w-full h-full" />
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="block font-sans text-base antialiased font-medium leading-relaxed text-blue-gray-900">
                        Apple AirPods
                    </p>
                    <p class="block font-sans text-base antialiased font-medium leading-relaxed text-blue-gray-900">
                        $95.00
                    </p>
                </div>
                <p class="block font-sans text-sm antialiased font-normal leading-normal text-gray-700 opacity-75">
                    With plenty of talk and listen time, voice-activated Siri access, and an
                    available wireless charging case.
                </p>
            </div>
            <div class="p-6 pt-0">
                <button
                    class="align-middle select-none font-sans font-bold text-center uppercase transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none text-xs py-3 px-6 rounded-lg shadow-gray-900/10 hover:shadow-gray-900/20 focus:opacity-[0.85] active:opacity-[0.85] active:shadow-none block w-full bg-blue-gray-900/10 text-blue-gray-900 shadow-none hover:scale-105 hover:shadow-none focus:scale-105 focus:shadow-none active:scale-100"
                    type="button">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

    <div class="card-body">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
                $habitacionesEnPiso = [
                    (object) [
                        'idHabitacion' => 101,
                        'estado' => 'Libre',
                        'estadoLimpieza' => 'Limpio',
                        'tipo_habitacion' => (object) ['nombre' => 'Suite Deluxe', 'tipoHabitacion' => 'Lujo'],
                        'detalle' => (object) ['ubicacion' => 'Piso 1']
                    ],
                    (object) [
                        'idHabitacion' => 102,
                        'estado' => 'Ocupado',
                        'estadoLimpieza' => 'Sucio',
                        'tipo_habitacion' => (object) ['nombre' => 'Habitación Doble', 'tipoHabitacion' => 'Familiar'],
                        'detalle' => (object) ['ubicacion' => 'Piso 1']
                    ],
                    (object) [
                        'idHabitacion' => 103,
                        'estado' => 'Libre',
                        'estadoLimpieza' => 'Limpio',
                        'tipo_habitacion' => (object) ['nombre' => 'Habitación Individual', 'tipoHabitacion' => 'Económica'],
                        'detalle' => (object) ['ubicacion' => 'Piso 2']
                    ],
                    (object) [
                        'idHabitacion' => 104,
                        'estado' => 'Mantenimiento',
                        'estadoLimpieza' => 'Muy Sucio',
                        'tipo_habitacion' => (object) ['nombre' => 'Suite Presidencial', 'tipoHabitacion' => 'Premium'],
                        'detalle' => (object) ['ubicacion' => 'Piso 3']
                    ],
                ];
            @endphp
            @foreach ($habitacionesEnPiso as $habitacion)
                <div class="mb-4">
                    <div class="rounded-lg shadow-md p-4 
                        @if ($habitacion->estado == 'Libre') bg-green-500 
                        @elseif($habitacion->estado == 'Ocupado') bg-red-500 
                        @else bg-gray-500 @endif">
                        <div class="p-4">
                            <h3 class="text-white text-lg font-bold">Hab. #{{ $habitacion->idHabitacion }}</h3>
                            <p class="text-white">{{ $habitacion->tipo_habitacion->nombre }}</p>
                            <p class="text-white font-semibold">Estado de limpieza:
                                <span class="px-2 py-1 rounded text-white 
                                    {{ $habitacion->estadoLimpieza == 'Limpio' ? 'bg-blue-500' : ($habitacion->estadoLimpieza == 'Sucio' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                    {{ $habitacion->estadoLimpieza }}
                                </span>
                            </p>
                        </div>
                        <div class="p-3 flex justify-center">
                            <i class="fas fa-bed text-white text-2xl"></i>
                        </div>
                        <div class="flex justify-between items-center px-3 py-2 bg-gray-200 rounded-b-lg">
                            <div>
                                <p class="text-gray-700 font-semibold"><strong>Ubicación:</strong> {{ $habitacion->detalle->ubicacion }}</p>
                            </div>
                            <div>
                                <p class="text-gray-700 font-semibold"><strong>Tipo:</strong> {{ $habitacion->tipo_habitacion->tipoHabitacion }}</p>
                            </div>
                        </div>
                        <a href="" class="block text-center py-2 text-white bg-blue-600 rounded-b-lg hover:bg-blue-700">
                            <strong>Ver detalles</strong> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
</div>