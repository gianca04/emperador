# 🎯 GUÍA RÁPIDA: Sistema de Reglas de Negocio para Hotel Emperador

## 📋 Comandos Disponibles

### 1. Resumen General
```bash
php artisan reglas:simular --resumen
```
Muestra todas las reglas activas y estadísticas del sistema.

### 2. Ejemplo Real Interactivo
```bash
php artisan reglas:simular --ejemplo-real
```
Ejecuta el caso del cliente Juan Pérez que cambia de modalidad de alquiler.

### 3. Simulación Personalizada
```bash
php artisan reglas:simular --horas=6 --fecha="2025-08-25 15:00"
```
Simula precios para una cantidad específica de horas en una fecha determinada.

### 4. Simulación con Penalizaciones
```bash
php artisan reglas:simular --horas=8 --checkout="15:30" --monto-base=400
```
Simula tanto el alquiler como las penalizaciones por checkout tardío.

## 🏨 Casos de Uso Reales

### Caso 1: Cliente de Paso Rápido
**Escenario**: Cliente necesita habitación por 3 horas un martes normal.
```bash
php artisan reglas:simular --horas=3 --fecha="2025-08-26 14:00"
```
**Resultado esperado**: Aplica regla estándar, precio económico por hora.

### Caso 2: Pareja Romántica - Fin de Semana
**Escenario**: Pareja quiere habitación 6 horas un sábado por la noche.
```bash
php artisan reglas:simular --horas=6 --fecha="2025-08-30 20:00"
```
**Resultado esperado**: Aplica regla nocturna + fin de semana, precio premium.

### Caso 3: Ejecutivo con Vuelo Retrasado
**Escenario**: Ejecutivo necesita habitación hasta el día siguiente (20 horas).
```bash
php artisan reglas:simular --horas=20 --fecha="2025-08-27 16:00"
```
**Resultado esperado**: Aplica regla de estancia larga, precio optimizado.

### Caso 4: Cliente con Checkout Muy Tardío
**Escenario**: Cliente hace checkout a las 16:00 (4 horas tarde).
```bash
php artisan reglas:simular --horas=8 --checkout="16:00" --monto-base=500
```
**Resultado esperado**: Penalización por checkout tardío + costo del alquiler.

## 🎭 Historia del Cliente Juan Pérez

El comando `--ejemplo-real` simula esta historia completa:

1. **15:00**: Juan llega y reserva 4 horas (hasta 19:00)
   - Paga: S/ 100 (S/ 25/hora)

2. **18:00**: Decide extender 2 horas más (hasta 21:00)
   - Paga adicional: S/ 50
   - Total acumulado: S/ 150

3. **20:00**: Decide quedarse toda la noche (21 horas total)
   - Paga adicional: S/ 480 (se aplica regla de estancia larga)
   - Total final: S/ 630

**Beneficio**: Ahorra S/ 105 comparado con tarifa normal.

## 💡 Lógica de Negocio

### Prioridad de Reglas
Las reglas se aplican por prioridad (mayor número = mayor prioridad):

1. **Prioridad 20**: Alquiler VIP - Temporada Alta
2. **Prioridad 15**: Alquiler por Horas - Estancia Larga (18-24h)
3. **Prioridad 10**: Penalización Checkout Tardío
4. **Prioridad 7**: Alquiler por Horas - Nocturno (18:00-08:00)
5. **Prioridad 5**: Alquiler por Horas - Fin de Semana
6. **Prioridad 3**: Descuento Estancia Larga
7. **Prioridad 1**: Alquiler por Horas - Estándar

### Horarios Especiales
- **Nocturno**: 18:00 - 08:00 (S/ 40/hora)
- **Fin de Semana**: Sábado y Domingo (S/ 35/hora)
- **Estándar**: Lunes a Viernes día (S/ 25/hora)
- **Estancia Larga**: 18+ horas (S/ 30/hora)

### Penalizaciones de Checkout
- **Límite normal**: 12:00 PM
- **Penalización tardía**: S/ 50 fijo (12:01-15:00)
- **Penalización muy tardía**: 20% del monto (después 15:00)

## 🔧 Personalización

Para crear nuevas reglas, usa el panel de Filament:
- Ve a Admin Panel → Reglas de Negocio
- Crea nueva regla con condiciones específicas
- Las reglas se aplican automáticamente según prioridad

## 📊 Monitoreo

Usa `--resumen` regularmente para:
- Verificar reglas activas
- Analizar cobertura por tipo de habitación
- Validar configuración de prioridades

---

*Este sistema permite máxima flexibilidad para el cliente mientras optimiza ingresos del hotel.*
