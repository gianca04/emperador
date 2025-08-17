# Comandos útiles de Laravel

## Migraciones
### Crear un modelo con su respectiva migración
```bash
php artisan make:model NombreDelModelo -m
```
Ejemplo:
```bash
php artisan make:model Area -m
```

### Crear una migración para añadir columnas a una tabla existente
```bash
php artisan make:migration add_nombre_columna_to_nombre_tabla --table=nombre_tabla
```
Ejemplos:
```bash
php artisan make:migration add_accepted_quote_to_projects_table
php artisan make:migration add_end_break_to_timesheets_table
php artisan make:migration add_tdr_to_quotes_table --table=quotes
php artisan make:migration add_client_id_to_quotes_table --table=quotes
php artisan make:migration add_comment_to_quotes_table --table=quotes
```

### Crear una migración para eliminar una columna de una tabla
```bash
php artisan make:migration remove_column_from_table --table=nombre_tabla
```

### Crear una migración para renombrar una columna
```bash
php artisan make:migration rename_column_in_table --table=nombre_tabla
```

### Ejecutar todas las migraciones pendientes
```bash
php artisan migrate
```

### Revertir todas las migraciones
```bash
php artisan migrate:reset
```

### Revertir la última migración ejecutada
```bash
php artisan migrate:rollback --step=1
```

## Modelos
### Crear un modelo con su respectiva migración y un controlador
```bash
php artisan make:model NombreDelModelo -mcr
```

## Controladores
### Crear un controlador básico
```bash
php artisan make:controller NombreDelControlador
```

### Crear un controlador con recursos
```bash
php artisan make:controller NombreDelControlador --resource
```

## Filament
### Crear un recurso de Filament
```bash
php artisan make:filament-resource NombreDelRecurso
```

### Crear una página de Filament
```bash
php artisan make:filament-page NombreDeLaPagina
```

### Crear un formulario de Filament
```bash
php artisan make:form-layout nombre
```

### Crear un componente de tabla de Filament
```bash
php artisan make:filament-table NombreDeLaTabla
```

## Otros comandos útiles
### Limpiar caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Crear un seeder
```bash
php artisan make:seeder NombreDelSeeder
```

### Ejecutar seeders
```bash
php artisan db:seed
```

### Listar rutas
```bash
php artisan route:list
```

-------------Seeders y Población de Base de Datos
# Ejecutar el seeder de la clase 'ClientSeeder' para poblar la base de datos
php artisan db:seed --class=ClientSeeder


# Ejecutar todos los seeders registrados
php artisan db:seed

# Limpiar la base de datos y volver a ejecutar los seeders
php artisan migrate:refresh --seed

# Crear un Seeder
php artisan make:seeder NombreDelSeeder
php artisan make:seeder SeccionSeeder
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Insertar 10 usuarios ficticios en la tabla 'users'
        foreach (range(1, 10) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),  // Puedes ajustar la contraseña si es necesario
            ]);
        }
    }
}

 


Cotizacion
- fecha_registro
- id_subcliente
- id_emplado
- nombre
- descripcion
- TDR (documentos)
- id visita tecnica
- responsable (ellos)

visita_tecnica
- id_propuesta_cotizacion
- fecha
- documento
- id_empleado

primero se dice si se va a cotizar para hacer la visita

SIN AGIGNAR (NO TIENE NINGUN EMPLEADO ASIGNADO)
EN PROCESO (FUE RECIBIDA POR EL EMPLEDO ESTA SIENDO REALIZADA)
EN REVISIÓN (LA COTIZACIÓN ESTA HECHA Y SE ESPERA SER REVISADA POR EL GERENTE)
APROBADA (FUE APROBADA POR EL GERENTE)
DENEGADA (FUE DENEGADA POR EL GERENTE)

ENVIADA (FUE ENVIADA AL CLIENTE)
RECHAZADA (FUE RECHAZADA POR EL CLIENTE)
ACEPTADA (FUE ACEPTADA POR EL CLEINTE LISTA PARA CONTUAR COMO PROYECTO)


Sin contexto:
php artisan make:filament-relation-manager CategoryResource posts title

php artisan make:filament-relation-manager ProjectResource timesheets employee_id

timesheets


php artisan make:filament-relation-manager TimesheetResource timesheets user.full_name --associate

SUB AREAS
- NEUMATICOS
- MATERIALES GENERALES:
	AREAS:
		- BIOSEGURIDAD
			
		- FORMATOS
			HOJAS EN FISICO
		- MECANICA
		- ELECTRICIDAD
			- STOCK MINIMO
			
		- LUBRICANTES
		- PINTURAS
		- SOLDADURA		
- REPUESTOS DE VOLVO	
- IDENFICIADOR UNICO


SALIDA
REGISTRAR SALIDAS:
	FECHA, UNIDADES, MATERIALES, A DONDE IRAN, QUIEN FUE EL QUE ASIGNO ESO
	
		ARUS-782


REGISTRAR ENTRADAS:
	FECHA, QUIEN FUE EL QUE INGRESO EL STOCK, FACTURAS O BOLETAS O GUIAS, CODIGO DEL PRODUCTO INGRESO, STOCK

REPORTE MENSUAL DE GASTOS


los asistentes no generan reportes, no editan, ni remomueven.



HITORIAL DE PRESTAMO SOLO PARA HERRAMIENTAS



AGREGAR IMAGEN A LARAVEL

public \ resources \ css \ custom-login.css

body {
    background: url('/public/images/fondo.png') center / cover,
                linear-gradient(0deg, rgba(var(--primary-500), 1) 100%);
}


vite.config.js:

"resources/css/custom-login.css",

AppServiceProvider:


FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn (): string => Blade::render('@vite(\'resources/css/custom-login.css\')'),
        );


libreris
