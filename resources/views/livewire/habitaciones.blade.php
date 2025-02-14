<div>
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
                                : ($habitacion->estado === 'Por limpiar'
                                    ? 'bg-red-900'
                                    : ($habitacion->estado === 'En Mantenimiento'
                                        ? 'bg-blue-900'
                                        : ''))) }}">
                        {{ $habitacion->estado }}
                    </span>

                </div>


                <!-- Título -->
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Need a help in
                        Claim?</h5>
                </a>

                <!-- Descripción -->
                <p class="mb-3 font-normal text-gray-500 dark:text-gray-400">
                    Go to this step-by-step guideline process on how to certify for your weekly benefits:
                </p>

                <!-- Enlace -->
                <div class="flex flex-col items-center">
                    <div class="flex mt-4 md:mt-6">
                        <a href="#"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add
                            friend</a>


                        <button data-popover-target="popover-user-profile" type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">User
                            profile</button>

                        <div data-popover id="popover-user-profile" role="tooltip"
                            class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-xs opacity-0 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-600">
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <a href="#">
                                        <img class="w-10 h-10 rounded-full"
                                            src="/docs/images/people/profile-picture-1.jpg" alt="Jese Leos">
                                    </a>
                                    <div>
                                        <button type="button"
                                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Follow</button>
                                    </div>
                                </div>
                                <p class="text-base font-semibold leading-none text-gray-900 dark:text-white">
                                    <a href="#">Jese Leos</a>
                                </p>
                                <p class="mb-3 text-sm font-normal">
                                    <a href="#" class="hover:underline">@jeseleos</a>
                                </p>
                                <p class="mb-4 text-sm">Open-source contributor. Building <a href="#"
                                        class="text-blue-600 dark:text-blue-500 hover:underline">flowbite.com</a>.</p>
                                <ul class="flex text-sm">
                                    <li class="me-2">
                                        <a href="#" class="hover:underline">
                                            <span class="font-semibold text-gray-900 dark:text-white">799</span>
                                            <span>Following</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="hover:underline">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div data-popper-arrow></div>
                        </div>

                    </div>
                </div>

            </div>
        @endforeach

    </div>

    <div>
        {{ $this->table }}
    </div>

</div>
