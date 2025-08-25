# 📋 Documentación del Sistema de Reglas de Negocio - Hotel Emperador

## 🎯 Introducción

El Sistema de Reglas de Negocio permite gestionar de forma flexible y profesional los diferentes tipos de alquiler de habitaciones, penalizaciones y promociones del hotel. Este documento explica con ejemplos prácticos cómo funciona en situaciones reales.

---

## 🏨 Casos de Uso Reales

### 📝 **Caso 1: Cliente que alquila por horas y luego extiende**

#### Situación:
- Cliente llega viernes 15:00 y solicita habitación por 4 horas
- A las 18:00 decide quedarse 2 horas más
- Finalmente decide quedarse toda la noche

#### Configuración de Reglas:
```
Regla: "Alquiler por Horas - Fin de Semana"
- Tipo: Alquiler por horas
- Precio: S/ 35.00 por hora
- Horas mínimas: 2
- Horas máximas: 12
- Días: Viernes, Sábado, Domingo
- Prioridad: 5

Regla: "Alquiler Nocturno Completo"
- Tipo: Alquiler por horas
- Precio fijo: S/ 280.00
- Horas mínimas: 12
- Horas máximas: 24
- Horario: 18:00 - 12:00 (día siguiente)
- Prioridad: 8
```

#### Simulación paso a paso:

**15:00 - Alquiler inicial (4 horas):**
```bash
php artisan reglas:simular --habitacion=101 --horas=4 --fecha="2025-08-22 15:00"
```
**Resultado:**
- Precio: S/ 140.00 (4h × S/35)
- Regla aplicada: "Alquiler por Horas - Fin de Semana"

**18:00 - Extensión (2 horas más = 6 horas total):**
```bash
php artisan reglas:simular --habitacion=101 --horas=6 --fecha="2025-08-22 15:00"
```
**Resultado:**
- Precio: S/ 210.00 (6h × S/35)
- Diferencia a pagar: S/ 70.00

**20:00 - Cambio a noche completa (hasta 12:00 del día siguiente):**
```bash
php artisan reglas:simular --habitacion=101 --horas=21 --fecha="2025-08-22 15:00"
```
**Resultado:**
- Precio: S/ 280.00 (tarifa nocturna fija)
- Diferencia a pagar: S/ 70.00 (280 - 210 ya pagados)
- **¡Ahorro para el cliente!** En lugar de S/ 735 (21h × S/35)

---

### 📝 **Caso 2: Cliente con checkout tardío**

#### Situación:
- Cliente tenía reserva por noche completa (S/ 200.00)
- Hora límite de checkout: 12:00 PM
- Cliente hace checkout a las 14:30

#### Configuración de Reglas:
```
Regla: "Penalización Checkout Tardío"
- Tipo: Penalización checkout
- Hora límite: 12:00
- Monto: S/ 50.00 (fijo)
- Prioridad: 10

Regla: "Penalización Checkout Muy Tardío"
- Tipo: Penalización checkout
- Hora límite: 15:00
- Monto: S/ 30.00 por hora
- Prioridad: 15
```

#### Simulación:
```bash
php artisan reglas:simular --habitacion=102 --checkout=14:30 --monto-base=200
```

**Resultado:**
- Monto original: S/ 200.00
- Penalización aplicada: S/ 50.00 (checkout después de 12:00)
- **Total a pagar: S/ 250.00**

**Si hubiera sido checkout a las 16:00:**
- Penalizaciones: S/ 50.00 + S/ 30.00 = S/ 80.00
- Total: S/ 280.00

---

### 📝 **Caso 3: Cliente VIP en temporada alta**

#### Situación:
- Cliente VIP quiere suite por 6 horas
- Fecha: 25 de diciembre (temporada alta)
- Habitación tipo "Suite"

#### Configuración:
```
Regla: "Alquiler VIP - Temporada Alta"
- Tipo: Alquiler por horas
- Precio: S/ 80.00 por hora
- Aplicabilidad: Solo suites y VIP
- Temporada alta: Sí
- Prioridad: 20

Regla: "Descuento Estancia Larga"
- Tipo: Descuento
- Condición: Más de 8 horas
- Descuento: 15% del total
- Prioridad: 3
```

#### Simulación:
```bash
php artisan reglas:simular --habitacion=301 --horas=6 --fecha="2025-12-25 10:00"
```

**Resultado (6 horas):**
- Precio base: S/ 480.00 (6h × S/80)
- Sin descuento (menos de 8 horas)
- **Total: S/ 480.00**

**Si decidiera 10 horas:**
```bash
php artisan reglas:simular --habitacion=301 --horas=10 --fecha="2025-12-25 10:00"
```
- Precio base: S/ 800.00 (10h × S/80)
- Descuento 15%: S/ 120.00
- **Total: S/ 680.00**

---

## 🛠️ Comandos de Gestión

### Ver resumen general:
```bash
php artisan reglas:simular --resumen
```

### Simular diferentes escenarios:
```bash
# Simulación básica
php artisan reglas:simular --habitacion=101 --horas=4

# Con fecha específica
php artisan reglas:simular --habitacion=101 --horas=6 --fecha="2025-12-31 20:00"

# Con checkout tardío
php artisan reglas:simular --checkout=15:30 --monto-base=200

# Combinado
php artisan reglas:simular --habitacion=201 --horas=8 --checkout=13:00
```

---

## 📊 Estrategias de Precios Recomendadas

### **Días Laborables:**
- **2-6 horas**: S/ 25/hora (mínimo S/ 50)
- **6-12 horas**: S/ 22/hora
- **Noche completa**: S/ 200 fijo

### **Fines de Semana:**
- **3-8 horas**: S/ 35/hora (mínimo S/ 105)
- **8+ horas**: S/ 30/hora + descuento 10%
- **Noche completa**: S/ 280 fijo

### **Temporada Alta:**
- **Habitaciones estándar**: +40% sobre tarifa normal
- **Suites/VIP**: S/ 80/hora
- **Penalizaciones**: Incrementar 50%

---

## 🔄 Flujo de Cambios de Tarifas

### **Ejemplo: Cliente que cambia de modalidad**

#### Estado Inicial:
```
Cliente: Juan Pérez
Habitación: 205 (Estándar)
Reserva: 4 horas por S/ 140 (viernes 16:00-20:00)
Pagado: S/ 140
```

#### Cambio 1 - Extensión (2 horas más):
```php
// Calcular nueva tarifa
$service = new ReglaNegocioService();
$nuevaReserva = $service->calcularPrecioAlquilerHoras($habitacion, 6, $fechaInicio);

// Resultado:
// - Nueva tarifa: S/ 210 (6h × S/35)
// - Diferencia: S/ 70
// - Cliente paga: S/ 70 adicionales
```

#### Cambio 2 - Upgrade a noche completa:
```php
// Calcular tarifa nocturna
$tarifaNocturna = $service->calcularPrecioAlquilerHoras($habitacion, 20, $fechaInicio);

// Resultado:
// - Tarifa nocturna: S/ 280 (tarifa fija)
// - Ya pagado: S/ 210
// - Diferencia: S/ 70
// - ¡Cliente ahorra S/ 420! (vs S/ 700 por 20h × S/35)
```

---

## 💡 Casos Especiales y Tips

### **1. Cliente indeciso:**
```bash
# Mostrar tabla de precios
php artisan reglas:simular --habitacion=101 --tabla-precios
```

### **2. Grupo de habitaciones:**
```bash
# Simular para múltiples habitaciones
php artisan reglas:simular --tipo=estandar --horas=6 --cantidad=3
```

### **3. Eventos especiales:**
```bash
# Activar reglas especiales para evento
php artisan reglas:activar --evento="Año Nuevo" --fecha-inicio="2025-12-31" --fecha-fin="2026-01-01"
```

---

## 🎯 Beneficios del Sistema

### **Para el Hotel:**
- ✅ **Flexibilidad total** en estrategias de precios
- ✅ **Optimización de ingresos** automática
- ✅ **Gestión de temporadas** sin complicaciones
- ✅ **Penalizaciones consistentes** y justas

### **Para los Huéspedes:**
- ✅ **Transparencia** en tarifas
- ✅ **Flexibilidad** para cambiar modalidades
- ✅ **Descuentos automáticos** por estancias largas
- ✅ **Precios competitivos** según temporada

### **Para el Personal:**
- ✅ **Cálculos automáticos** sin errores
- ✅ **Interface intuitiva** en Filament
- ✅ **Reportes en tiempo real**
- ✅ **Simulaciones** para asesorar clientes

---

## 🚀 Próximos Pasos

1. **Integración con Reservas**: Aplicar reglas en tiempo real
2. **Módulo de Facturación**: Cálculo automático de diferencias
3. **App Móvil**: Consulta de tarifas para huéspedes
4. **Analytics**: Reportes de rentabilidad por regla
5. **API**: Integración con sistemas externos

---

## 📞 Soporte

Para más información o configuraciones especiales:
- 📧 Email: admin@emperador.com
- 📱 WhatsApp: +51 999 888 777
- 🌐 Panel Admin: `/admin/regla-negocios`

---

*Sistema desarrollado por GitHub Copilot para Hotel Emperador* 🏨✨
